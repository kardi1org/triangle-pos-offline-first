<?php

namespace Modules\Adjustment\Http\Controllers;

use Modules\Adjustment\DataTables\AdjustmentsDataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Adjustment\Entities\Adjustment;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Warehouse;
use Modules\Setting\Entities\ProductWarehouse; // Import Model ProductWarehouse

class AdjustmentController extends Controller
{
    public function index(AdjustmentsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_adjustments'), 403);
        return $dataTable->render('adjustment::index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('adjustment::create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $request->validate([
            'reference'    => 'required|string|max:255',
            'warehouse_id' => 'required',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
            'product_ids'  => 'required|array',
            'quantities'   => 'required|array',
            'types'        => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $adjustment = Adjustment::create([
                'reference'    => $request->reference,
                'warehouse_id' => $request->warehouse_id,
                'date'         => $request->date,
                'note'         => $request->note
            ]);

            foreach ($request->product_ids as $key => $id) {
                $qty = $request->quantities[$key];
                $type = $request->types[$key];

                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $qty,
                    'type'          => $type
                ]);

                // Update Master Product (Global Stock)
                $product = Product::findOrFail($id);
                if ($type == 'add') {
                    $product->increment('product_quantity', $qty);
                    // Update Warehouse Stock
                    $this->updateWarehouseStock($id, $request->warehouse_id, $qty, 'increment');
                } else {
                    $product->decrement('product_quantity', $qty);
                    // Update Warehouse Stock
                    $this->updateWarehouseStock($id, $request->warehouse_id, $qty, 'decrement');
                }
            }

            DB::commit();
            toast('Adjustment Created!', 'success');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        // Ambil data adjustment secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $adjustment = Adjustment::findOrFail($id);

        return view('adjustment::show', compact('adjustment'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        // Ambil data adjustment secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $adjustment = Adjustment::findOrFail($id);

        $warehouses = Warehouse::where('is_active', true)->get();
        return view('adjustment::edit', compact('adjustment', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        $request->validate([
            'reference'    => 'required|string|max:255',
            'warehouse_id' => 'required',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
            'product_ids'  => 'required|array',
            'quantities'   => 'required|array',
            'types'        => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            // Ambil data adjustment secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
            $adjustment = Adjustment::findOrFail($id);

            // --- 1. REVERSAL STOK LAMA ---
            foreach ($adjustment->adjustedProducts as $oldDetail) {
                $oldProduct = Product::findOrFail($oldDetail->product_id);

                if ($oldDetail->type == 'add') {
                    // Jika dulu ditambah, sekarang kurangi untuk menetralkan
                    $oldProduct->decrement('product_quantity', $oldDetail->quantity);
                    $this->updateWarehouseStock($oldDetail->product_id, $adjustment->warehouse_id, $oldDetail->quantity, 'decrement');
                } else {
                    // Jika dulu dikurangi, sekarang tambah untuk menetralkan
                    $oldProduct->increment('product_quantity', $oldDetail->quantity);
                    $this->updateWarehouseStock($oldDetail->product_id, $adjustment->warehouse_id, $oldDetail->quantity, 'increment');
                }
                $oldDetail->delete();
            }

            // --- 2. UPDATE HEADER ---
            $adjustment->update([
                'reference'    => $request->reference,
                'warehouse_id' => $request->warehouse_id,
                'date'         => $request->date,
                'note'         => $request->note
            ]);

            // --- 3. SIMPAN DETAIL & STOK BARU ---
            foreach ($request->product_ids as $key => $prod_id) {
                $qty = $request->quantities[$key];
                $type = $request->types[$key];

                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $prod_id,
                    'quantity'      => $qty,
                    'type'          => $type
                ]);

                $product = Product::findOrFail($prod_id);
                if ($type == 'add') {
                    $product->increment('product_quantity', $qty);
                    $this->updateWarehouseStock($prod_id, $request->warehouse_id, $qty, 'increment');
                } else {
                    $product->decrement('product_quantity', $qty);
                    $this->updateWarehouseStock($prod_id, $request->warehouse_id, $qty, 'decrement');
                }
            }

            DB::commit();
            toast('Adjustment Updated!', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        try {
            DB::beginTransaction();

            // Ambil data adjustment secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
            $adjustment = Adjustment::findOrFail($id);

            // --- REVERSAL STOK SEBELUM HAPUS ---
            foreach ($adjustment->adjustedProducts as $detail) {
                $product = Product::findOrFail($detail->product_id);
                if ($detail->type == 'add') {
                    $product->decrement('product_quantity', $detail->quantity);
                    $this->updateWarehouseStock($detail->product_id, $adjustment->warehouse_id, $detail->quantity, 'decrement');
                } else {
                    $product->increment('product_quantity', $detail->quantity);
                    $this->updateWarehouseStock($detail->product_id, $adjustment->warehouse_id, $detail->quantity, 'increment');
                }
            }

            $adjustment->delete();

            DB::commit();
            toast('Adjustment Deleted!', 'warning');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mengupdate tabel ProductWarehouse
     */
    private function updateWarehouseStock($product_id, $warehouse_id, $qty, $action)
    {
        $stock = ProductWarehouse::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($action == 'increment') {
            if ($stock) {
                $stock->increment('qty', $qty);
            } else {
                ProductWarehouse::create([
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'qty' => $qty
                ]);
            }
        } else {
            // Jika decrement, pastikan data ada (idealnya data stok gudang harus ada sebelum adjustment 'sub')
            if ($stock) {
                $stock->decrement('qty', $qty);
            } else {
                // Jika data gudang belum ada tapi dilakukan pengurangan stok
                ProductWarehouse::create([
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                    'qty' => -$qty
                ]);
            }
        }
    }
}
