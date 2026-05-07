<?php

namespace Modules\InventoryMovement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\InventoryMovement\Entities\InventoryMovement;
use Modules\InventoryMovement\Entities\InventoryMovementDetail;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Warehouse;
use Modules\Setting\Entities\ProductWarehouse;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');

        // Query menggunakan model yang sudah diarahkan ke tabel Inventory_Movements
        $movements = InventoryMovement::with(['fromWarehouse', 'toWarehouse', 'user'])
            ->when($warehouse_id, function ($query) use ($warehouse_id) {
                return $query->where('from_warehouse_id', $warehouse_id)
                    ->orWhere('to_warehouse_id', $warehouse_id);
            })
            ->latest()
            ->paginate(10);

        $warehouses = Warehouse::all();

        return view('inventorymovement::index', compact('movements', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::all();
        return view('inventorymovement::create', compact('warehouses', 'products'));
    }

    public function show($id)
    {
        // Mengambil detail dari Inventory_Movements dan relasi detailnya
        $movement = InventoryMovement::with([
            'fromWarehouse',
            'toWarehouse',
            'user',
            'details.product'
        ])->findOrFail($id);

        return view('inventorymovement::show', compact('movement'));
    }

    public function edit($id)
    {
        $movement = InventoryMovement::with('details.product')->findOrFail($id);
        $warehouses = Warehouse::where('is_active', true)->get();

        // Format data untuk dikirim ke Livewire
        $movementProducts = [];
        foreach ($movement->details as $detail) {
            $movementProducts[] = [
                'id'               => $detail->product->id,
                'product_name'     => $detail->product->product_name,
                'product_code'     => $detail->product->product_code,
                'product_quantity' => $detail->product->product_quantity,
                'product_unit'     => $detail->product->product_unit,
                'quantity'         => $detail->quantity,
            ];
        }

        return view('inventorymovement::edit', compact('movement', 'warehouses', 'movementProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'date'              => 'required|date',
            'product_ids'       => 'required|array|min:1',
            'product_ids.*'     => 'required|exists:products,id',
            'quantities'        => 'required|array|min:1',
            'quantities.*'      => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $movement = new InventoryMovement();
            $movement->reference = $request->reference ?? 'MVT-' . strtoupper(now()->format('Ymd-His'));
            $movement->date = $request->date;
            $movement->from_warehouse_id = $request->from_warehouse_id;
            $movement->to_warehouse_id = $request->to_warehouse_id;
            $movement->user_id = auth()->id();
            $movement->note = $request->note;
            $movement->save();

            foreach ($request->product_ids as $index => $productId) {
                $qty = $request->quantities[$index];

                // 1. Simpan Detail
                $detail = new InventoryMovementDetail();
                $detail->inventory_movement_id = $movement->id;
                $detail->product_id = $productId;
                $detail->quantity = $qty;
                $detail->save();

                // 2. UPDATE STOK: Kurangi di Gudang Asal
                $this->adjustStock($productId, $request->from_warehouse_id, $qty, 'decrement');

                // 3. UPDATE STOK: Tambah di Gudang Tujuan
                $this->adjustStock($productId, $request->to_warehouse_id, $qty, 'increment');
            }

            DB::commit();
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Mutasi stok berhasil disimpan dan stok telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan mutasi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from_warehouse_id' => 'required',
            'to_warehouse_id'   => 'required|different:from_warehouse_id',
            'date'              => 'required|date',
            'product_ids'       => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $movement = InventoryMovement::with('details')->findOrFail($id);

            // --- A. REVERSAL (Kembalikan stok lama sebelum diupdate) ---
            foreach ($movement->details as $oldDetail) {
                // Tambahkan kembali ke gudang asal lama
                $this->adjustStock($oldDetail->product_id, $movement->from_warehouse_id, $oldDetail->quantity, 'increment');
                // Kurangi dari gudang tujuan lama
                $this->adjustStock($oldDetail->product_id, $movement->to_warehouse_id, $oldDetail->quantity, 'decrement');
            }

            // --- B. UPDATE HEADER ---
            $movement->reference = $request->reference;
            $movement->date = $request->date;
            $movement->from_warehouse_id = $request->from_warehouse_id;
            $movement->to_warehouse_id = $request->to_warehouse_id;
            $movement->note = $request->note;
            $movement->user_id = auth()->id();
            $movement->save();

            // --- C. UPDATE DETAIL & STOK BARU ---
            $movement->details()->delete();

            foreach ($request->product_ids as $index => $productId) {
                $qty = $request->quantities[$index];

                InventoryMovementDetail::create([
                    'inventory_movement_id' => $movement->id,
                    'product_id'            => $productId,
                    'quantity'              => $qty,
                ]);

                // Update stok dengan data gudang/qty yang baru
                $this->adjustStock($productId, $request->from_warehouse_id, $qty, 'decrement');
                $this->adjustStock($productId, $request->to_warehouse_id, $qty, 'increment');
            }

            DB::commit();
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Mutasi stok #' . $movement->reference . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $movement = InventoryMovement::with('details')->findOrFail($id);

            // --- REVERSAL (Kembalikan stok sebelum data dihapus) ---
            foreach ($movement->details as $detail) {
                $this->adjustStock($detail->product_id, $movement->from_warehouse_id, $detail->quantity, 'increment');
                $this->adjustStock($detail->product_id, $movement->to_warehouse_id, $detail->quantity, 'decrement');
            }

            $movement->details()->delete();
            $movement->delete();

            DB::commit();
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Data mutasi berhasil dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Helper function untuk menyesuaikan stok di ProductWarehouse
     */
    private function adjustStock($productId, $warehouseId, $qty, $type)
    {
        $stock = ProductWarehouse::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if ($type === 'increment') {
            if ($stock) {
                $stock->increment('qty', $qty);
            } else {
                ProductWarehouse::create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'qty' => $qty
                ]);
            }
        } elseif ($type === 'decrement') {
            if (!$stock || $stock->qty < $qty) {
                $product = Product::find($productId);
                throw new \Exception("Stok produk [" . ($product->product_name ?? $productId) . "] tidak mencukupi di gudang asal!");
            }
            $stock->decrement('qty', $qty);
        }
    }
}
