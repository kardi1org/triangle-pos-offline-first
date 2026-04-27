<?php

namespace Modules\Adjustment\Http\Controllers;

use Modules\Adjustment\DataTables\AdjustmentsDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Adjustment\Entities\Adjustment;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Warehouse; // Tambahkan import model Warehouse

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

        // Ambil data gudang aktif untuk dropdown
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('adjustment::create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $request->validate([
            'reference'    => 'required|string|max:255',
            'warehouse_id' => 'required', // Validasi warehouse_id
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
            'product_ids'  => 'required',
            'quantities'   => 'required',
            'types'        => 'required'
        ]);

        DB::transaction(function () use ($request) {
            $adjustment = Adjustment::create([
                'reference'    => $request->reference,
                'warehouse_id' => $request->warehouse_id, // Simpan warehouse_id
                'date'         => $request->date,
                'note'         => $request->note
            ]);

            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $request->quantities[$key],
                    'type'          => $request->types[$key]
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key]
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key]
                    ]);
                }
            }
        });

        toast('Adjustment Created!', 'success');

        return redirect()->route('adjustments.index');
    }

    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        return view('adjustment::show', compact('adjustment'));
    }

    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        // Ambil data gudang aktif untuk dropdown di halaman edit
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('adjustment::edit', compact('adjustment', 'warehouses'));
    }

    public function update(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        $request->validate([
            'reference'    => 'required|string|max:255',
            'warehouse_id' => 'required', // Validasi warehouse_id
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
            'product_ids'  => 'required',
            'quantities'   => 'required',
            'types'        => 'required'
        ]);

        DB::transaction(function () use ($request, $adjustment) {
            // Kembalikan stok lama sebelum menghapus AdjustedProduct lama
            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $product = Product::findOrFail($adjustedProduct->product->id);

                if ($adjustedProduct->type == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $adjustedProduct->quantity
                    ]);
                } elseif ($adjustedProduct->type == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $adjustedProduct->quantity
                    ]);
                }

                $adjustedProduct->delete();
            }

            // Update Header Adjustment termasuk warehouse_id
            $adjustment->update([
                'reference'    => $request->reference,
                'warehouse_id' => $request->warehouse_id, // Update warehouse_id
                'date'         => $request->date,
                'note'         => $request->note
            ]);

            // Simpan detail baru dan update stok baru
            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $request->quantities[$key],
                    'type'          => $request->types[$key]
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key]
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key]
                    ]);
                }
            }
        });

        toast('Adjustment Updated!', 'info');

        return redirect()->route('adjustments.index');
    }

    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        // Sebelum hapus adjustment, kembalikan stok
        foreach ($adjustment->adjustedProducts as $adjustedProduct) {
            $product = Product::findOrFail($adjustedProduct->product_id);
            if ($adjustedProduct->type == 'add') {
                $product->update(['product_quantity' => $product->product_quantity - $adjustedProduct->quantity]);
            } else {
                $product->update(['product_quantity' => $product->product_quantity + $adjustedProduct->quantity]);
            }
        }

        $adjustment->delete();

        toast('Adjustment Deleted!', 'warning');

        return redirect()->route('adjustments.index');
    }
}
