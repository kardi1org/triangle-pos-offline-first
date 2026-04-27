<?php

namespace Modules\InventoryMovement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\InventoryMovement\Entities\InventoryMovement;
use Modules\InventoryMovement\Entities\InventoryMovementDetail;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Warehouse;

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

    public function store(Request $request)
    {
        // 1. Perbarui validasi agar sesuai dengan struktur array terpisah
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

            // 2. Simpan Header
            $movement = new InventoryMovement();
            $movement->reference = $request->reference ?? 'MVT-' . strtoupper(now()->format('Ymd-His'));
            $movement->date = $request->date;
            $movement->from_warehouse_id = $request->from_warehouse_id;
            $movement->to_warehouse_id = $request->to_warehouse_id;
            $movement->user_id = auth()->id();
            $movement->note = $request->note;
            $movement->save();

            // 3. Simpan Detail dengan looping berdasarkan index array
            foreach ($request->product_ids as $index => $productId) {
                $detail = new InventoryMovementDetail();
                $detail->inventory_movement_id = $movement->id;
                $detail->product_id = $productId;
                $detail->quantity = $request->quantities[$index]; // Ambil kuantitas berdasarkan index yang sama
                $detail->save();
            }

            DB::commit();
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Mutasi stok berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Return error ke session untuk ditampilkan di view
            return back()->with('error', 'Gagal menyimpan mutasi: ' . $e->getMessage());
        }
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

    public function update(Request $request, $id)
    {
        // Validasi tetap diperlukan
        $request->validate([
            'from_warehouse_id' => 'required',
            'to_warehouse_id'   => 'required|different:from_warehouse_id',
            'date'              => 'required|date',
            'product_ids'       => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $movement = InventoryMovement::findOrFail($id);

            // Gunakan mapping manual untuk memastikan data masuk ke kolom yang tepat
            $movement->reference = $request->reference;
            $movement->date = $request->date;
            $movement->from_warehouse_id = $request->from_warehouse_id;
            $movement->to_warehouse_id = $request->to_warehouse_id;
            // $movement->status = $request->status;
            $movement->note = $request->note;
            $movement->user_id = auth()->id(); // Update siapa yang terakhir mengedit

            $movement->save(); // Simpan header

            // Hapus detail lama
            $movement->details()->delete();

            // Simpan detail baru
            if ($request->has('product_ids')) {
                foreach ($request->product_ids as $index => $productId) {
                    InventoryMovementDetail::create([
                        'inventory_movement_id' => $movement->id,
                        'product_id'            => $productId,
                        'quantity'              => $request->quantities[$index],
                    ]);
                }
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

            $movement = InventoryMovement::findOrFail($id);

            // Hapus detail terlebih dahulu karena ada foreign key
            $movement->details()->delete();

            // Hapus header
            $movement->delete();

            DB::commit();
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Data mutasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
