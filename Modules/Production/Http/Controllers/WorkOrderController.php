<?php

namespace Modules\Production\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Production\Entities\WorkOrder;
use Modules\Production\Entities\WorkOrderDetail;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\ProductWarehouse;

class WorkOrderController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'warehouse_id' => 'required',
            'quantity' => 'required|numeric|min:0.01',
            'ingredient_id' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $wo = WorkOrder::create([
                'reference' => 'WO-' . now()->format('YmdHis'),
                'date' => $request->date ?? now(),
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'note' => $request->note,
            ]);

            foreach ($request->ingredient_id as $key => $ing_id) {
                $qtyNeeded = $request->ing_qty[$key];

                $wo->details()->create([
                    'product_id' => $ing_id,
                    'quantity' => $qtyNeeded,
                    'unit' => $request->ing_unit[$key],
                ]);

                // POTONG STOK BAHAN
                $this->updateStock($ing_id, $request->warehouse_id, $qtyNeeded, 'decrement');
            }

            // TAMBAH STOK BARANG JADI
            $this->updateStock($request->product_id, $request->warehouse_id, $request->quantity, 'increment');

            DB::commit();
            return redirect()->route('work-orders.index')->with('success', 'Work Order Berhasil Disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();
            $wo = WorkOrder::with('details')->findOrFail($id);

            // 1. KEMBALIKAN STOK LAMA (Reversal)
            // Kurangi stok produk jadi yang pernah ditambah
            $this->updateStock($wo->product_id, $wo->warehouse_id, $wo->quantity, 'decrement');

            // Tambah kembali stok bahan yang pernah dipotong
            foreach ($wo->details as $detail) {
                $this->updateStock($detail->product_id, $wo->warehouse_id, $detail->quantity, 'increment');
            }

            // 2. UPDATE DATA HEADER
            $wo->update([
                'quantity' => $request->quantity,
                'note' => $request->note,
            ]);

            // 3. POTONG STOK BARU (Berdasarkan input baru)
            foreach ($request->ingredient_id as $key => $ing_id) {
                $qtyNeeded = $request->ing_qty[$key];

                // Update detail quantity
                WorkOrderDetail::where('work_order_id', $id)
                    ->where('product_id', $ing_id)
                    ->update(['quantity' => $qtyNeeded]);

                $this->updateStock($ing_id, $wo->warehouse_id, $qtyNeeded, 'decrement');
            }

            // 4. TAMBAH STOK PRODUK JADI BARU
            $this->updateStock($wo->product_id, $wo->warehouse_id, $request->quantity, 'increment');

            DB::commit();
            return redirect()->route('work-orders.index')->with('success', 'Work Order Berhasil Diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $wo = WorkOrder::with('details')->findOrFail($id);

            // KEMBALIKAN STOK SEBELUM HAPUS
            $this->updateStock($wo->product_id, $wo->warehouse_id, $wo->quantity, 'decrement');
            foreach ($wo->details as $detail) {
                $this->updateStock($detail->product_id, $wo->warehouse_id, $detail->quantity, 'increment');
            }

            $wo->delete(); // Detail otomatis terhapus jika di db set cascade, jika tidak hapus manual

            DB::commit();
            return back()->with('success', 'Work Order dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Helper Function agar kode tidak berulang (Reusable)
    private function updateStock($product_id, $warehouse_id, $qty, $type)
    {
        $stock = ProductWarehouse::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($type == 'decrement') {
            if (!$stock || $stock->qty < $qty) {
                $p = Product::find($product_id);
                throw new \Exception("Stok [" . ($p->product_name ?? $product_id) . "] tidak cukup!");
            }
            $stock->decrement('qty', $qty);
        } else {
            if ($stock) {
                $stock->increment('qty', $qty);
            } else {
                ProductWarehouse::create([
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'qty' => $qty
                ]);
            }
        }
    }


    // AJAX: Ambil Resep Produk (BOM)
    public function getRecipe($id)
    {
        // Ambil resep (Recipe) dari produk yang dipilih
        $recipe = \Modules\Setting\Entities\Recipe::with('details.product')->where('product_id', $id)->first();

        if (!$recipe) return response()->json(['status' => 'error', 'message' => 'Produk ini tidak punya resep!']);

        return response()->json([
            'status' => 'success',
            'unit' => $recipe->unit,
            'details' => $recipe->details
        ]);
    }

    public function index()
    {
        $workOrders = WorkOrder::with(['product', 'warehouse'])->latest()->paginate(10);
        return view('production::work-orders.index', compact('workOrders'));
    }

    public function edit($id)
    {
        $workOrder = WorkOrder::with('details.product', 'product', 'warehouse')->findOrFail($id);
        return view('production::work-orders.edit', compact('workOrder'));
    }


    public function create()
    {
        // Ambil data produk yang punya resep (BOM)
        $products = \Modules\Product\Entities\Product::all();

        // Ambil data warehouse
        $warehouses = \Modules\Setting\Entities\Warehouse::all();

        return view('production::work-orders.create', compact('products', 'warehouses'));
    }
}
