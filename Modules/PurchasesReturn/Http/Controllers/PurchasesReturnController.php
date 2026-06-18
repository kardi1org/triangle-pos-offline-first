<?php

namespace Modules\PurchasesReturn\Http\Controllers;

use Modules\PurchasesReturn\DataTables\PurchaseReturnsDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\People\Entities\Supplier;
use Modules\Product\Entities\Product;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturnDetail;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\PurchasesReturn\Http\Requests\StorePurchaseReturnRequest;
use Modules\PurchasesReturn\Http\Requests\UpdatePurchaseReturnRequest;
use Modules\Setting\Entities\ProductWarehouse; // Import Model ProductWarehouse

class PurchasesReturnController extends Controller
{
    public function index(PurchaseReturnsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_purchase_returns'), 403);
        return $dataTable->render('purchasesreturn::index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_purchase_returns'), 403);
        Cart::instance('purchase_return')->destroy();
        return view('purchasesreturn::create');
    }

    public function store(StorePurchaseReturnRequest $request)
    {
        try {
            DB::beginTransaction();

            $due_amount = $request->total_amount - $request->paid_amount;
            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase_return = PurchaseReturn::create([
                'date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => Supplier::findOrFail($request->supplier_id)->supplier_name,
                'warehouse_id' => $request->warehouse_id,
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount * 100,
                'paid_amount' => $request->paid_amount * 100,
                'total_amount' => $request->total_amount * 100,
                'due_amount' => $due_amount * 100,
                'status' => $request->status,
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'tax_amount' => Cart::instance('purchase_return')->tax() * 100,
                'discount_amount' => Cart::instance('purchase_return')->discount() * 100,
            ]);

            foreach (Cart::instance('purchase_return')->content() as $cart_item) {
                PurchaseReturnDetail::create([
                    'purchase_return_id' => $purchase_return->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price * 100,
                    'unit_price' => $cart_item->options->unit_price * 100,
                    'sub_total' => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100,
                ]);

                // Update Stok (Kurangi stok karena barang dikembalikan ke Supplier)
                if ($request->status == 'Shipped' || $request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->decrement('product_quantity', $cart_item->qty);

                    $this->updateWarehouseStock($cart_item->id, $request->warehouse_id, $cart_item->qty, 'decrement');
                }
            }

            Cart::instance('purchase_return')->destroy();

            if ($purchase_return->paid_amount > 0) {
                PurchaseReturnPayment::create([
                    'date'               => $request->date,
                    'reference'          => 'INV/' . $purchase_return->reference,
                    'amount'             => $purchase_return->paid_amount,
                    'purchase_return_id' => $purchase_return->id,
                    'payment_method'     => $request->payment_method
                ]);
            }

            DB::commit();
            toast('Purchase Return Created!', 'success');
            return redirect()->route('purchase-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(UpdatePurchaseReturnRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Tangkap model secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
            $purchase_return = PurchaseReturn::findOrFail($id);

            // 1. REVERSAL STOK LAMA (Kembalikan stok yang dulu sempat dikurangi)
            if ($purchase_return->status == 'Shipped' || $purchase_return->status == 'Completed') {
                foreach ($purchase_return->purchaseReturnDetails as $purchase_return_detail) {
                    $product = Product::findOrFail($purchase_return_detail->product_id);
                    $product->increment('product_quantity', $purchase_return_detail->quantity);

                    $this->updateWarehouseStock($purchase_return_detail->product_id, $purchase_return->warehouse_id, $purchase_return_detail->quantity, 'increment');
                }
            }

            $purchase_return->purchaseReturnDetails()->delete();

            // 2. UPDATE HEADER
            $due_amount = $request->total_amount - $request->paid_amount;
            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase_return->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => Supplier::findOrFail($request->supplier_id)->supplier_name,
                'warehouse_id' => $request->warehouse_id,
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount * 100,
                'paid_amount' => $request->paid_amount * 100,
                'total_amount' => $request->total_amount * 100,
                'due_amount' => $due_amount * 100,
                'status' => $request->status,
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'tax_amount' => Cart::instance('purchase_return')->tax() * 100,
                'discount_amount' => Cart::instance('purchase_return')->discount() * 100,
            ]);

            // 3. SIMPAN DETAIL BARU & KURANGI STOK LAGI
            foreach (Cart::instance('purchase_return')->content() as $cart_item) {
                PurchaseReturnDetail::create([
                    'purchase_return_id' => $purchase_return->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price * 100,
                    'unit_price' => $cart_item->options->unit_price * 100,
                    'sub_total' => $cart_item->options->sub_total * 100,
                    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100,
                ]);

                if ($request->status == 'Shipped' || $request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->decrement('product_quantity', $cart_item->qty);

                    $this->updateWarehouseStock($cart_item->id, $request->warehouse_id, $cart_item->qty, 'decrement');
                }
            }

            Cart::instance('purchase_return')->destroy();
            DB::commit();
            toast('Purchase Return Updated!', 'info');
            return redirect()->route('purchase-returns.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('delete_purchase_returns'), 403);

        try {
            DB::beginTransaction();

            // Tangkap model secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
            $purchase_return = PurchaseReturn::findOrFail($id);

            // REVERSAL STOK SEBELUM HAPUS
            if ($purchase_return->status == 'Shipped' || $purchase_return->status == 'Completed') {
                foreach ($purchase_return->purchaseReturnDetails as $purchase_return_detail) {
                    $product = Product::findOrFail($purchase_return_detail->product_id);
                    $product->increment('product_quantity', $purchase_return_detail->quantity);

                    $this->updateWarehouseStock($purchase_return_detail->product_id, $purchase_return->warehouse_id, $purchase_return_detail->quantity, 'increment');
                }
            }

            $purchase_return->delete();
            DB::commit();
            toast('Purchase Return Deleted!', 'warning');
            return redirect()->route('purchase-returns.index');
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
        } else { // decrement
            if ($stock) {
                $stock->decrement('qty', $qty);
            }
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('show_purchase_returns'), 403);

        // Tangkap model secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $purchase_return = PurchaseReturn::findOrFail($id);

        $supplier = Supplier::findOrFail($purchase_return->supplier_id);
        return view('purchasesreturn::show', compact('purchase_return', 'supplier'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('edit_purchase_returns'), 403);

        // Tangkap model secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $purchase_return = PurchaseReturn::findOrFail($id);

        $purchase_return_details = $purchase_return->purchaseReturnDetails;
        Cart::instance('purchase_return')->destroy();
        $cart = Cart::instance('purchase_return');

        foreach ($purchase_return_details as $purchase_return_detail) {
            $cart->add([
                'id'      => $purchase_return_detail->product_id,
                'name'    => $purchase_return_detail->product_name,
                'qty'     => $purchase_return_detail->quantity,
                'price'   => $purchase_return_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $purchase_return_detail->product_discount_amount,
                    'product_discount_type' => $purchase_return_detail->product_discount_type,
                    'sub_total'   => $purchase_return_detail->sub_total,
                    'code'        => $purchase_return_detail->product_code,
                    'stock'       => Product::findOrFail($purchase_return_detail->product_id)->product_quantity,
                    'product_tax' => $purchase_return_detail->product_tax_amount,
                    'unit_price'  => $purchase_return_detail->unit_price
                ]
            ]);
        }
        return view('purchasesreturn::edit', compact('purchase_return'));
    }
}
