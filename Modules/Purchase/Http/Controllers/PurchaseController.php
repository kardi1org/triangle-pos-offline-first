<?php

namespace Modules\Purchase\Http\Controllers;

use Modules\Purchase\DataTables\PurchaseDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\People\Entities\Supplier;
use Modules\Product\Entities\Product;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchaseDetail;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\Purchase\Http\Requests\StorePurchaseRequest;
use Modules\Purchase\Http\Requests\UpdatePurchaseRequest;
use Modules\Setting\Entities\ProductWarehouse; // Import Model ProductWarehouse

class PurchaseController extends Controller
{
    public function index(PurchaseDataTable $dataTable)
    {
        abort_if(Gate::denies('access_purchases'), 403);
        return $dataTable->render('purchase::index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_purchases'), 403);
        Cart::instance('purchase')->destroy();
        return view('purchase::create');
    }

    public function store(StorePurchaseRequest $request)
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

            $purchase = Purchase::create([
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
                'tax_amount' => Cart::instance('purchase')->tax() * 100,
                'discount_amount' => Cart::instance('purchase')->discount() * 100,
            ]);

            foreach (Cart::instance('purchase')->content() as $cart_item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
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

                // Update Stok jika status Completed
                if ($request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->increment('product_quantity', $cart_item->qty);

                    $this->updateWarehouseStock($cart_item->id, $request->warehouse_id, $cart_item->qty, 'increment');
                }
            }

            Cart::instance('purchase')->destroy();

            if ($purchase->paid_amount > 0) {
                PurchasePayment::create([
                    'date' => $request->date,
                    'reference' => 'INV/' . $purchase->reference,
                    'amount' => $purchase->paid_amount,
                    'purchase_id' => $purchase->id,
                    'payment_method' => $request->payment_method
                ]);
            }

            DB::commit();
            toast('Purchase Created!', 'success');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // 1. REVERSAL STOK LAMA (Hanya jika status sebelumnya Completed)
            if ($purchase->status == 'Completed') {
                foreach ($purchase->purchaseDetails as $purchase_detail) {
                    $product = Product::findOrFail($purchase_detail->product_id);
                    $product->decrement('product_quantity', $purchase_detail->quantity);

                    $this->updateWarehouseStock($purchase_detail->product_id, $purchase->warehouse_id, $purchase_detail->quantity, 'decrement');
                }
            }

            // Hapus detail lama
            $purchase->purchaseDetails()->delete();

            // 2. UPDATE HEADER
            $due_amount = $request->total_amount - $request->paid_amount;
            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $purchase->update([
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
                'tax_amount' => Cart::instance('purchase')->tax() * 100,
                'discount_amount' => Cart::instance('purchase')->discount() * 100,
            ]);

            // 3. SIMPAN DETAIL BARU & UPDATE STOK BARU
            foreach (Cart::instance('purchase')->content() as $cart_item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
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

                if ($request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->increment('product_quantity', $cart_item->qty);

                    $this->updateWarehouseStock($cart_item->id, $request->warehouse_id, $cart_item->qty, 'increment');
                }
            }

            Cart::instance('purchase')->destroy();
            DB::commit();
            toast('Purchase Updated!', 'info');
            return redirect()->route('purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        abort_if(Gate::denies('delete_purchases'), 403);

        try {
            DB::beginTransaction();

            // REVERSAL STOK SEBELUM HAPUS (Jika status Completed)
            if ($purchase->status == 'Completed') {
                foreach ($purchase->purchaseDetails as $purchase_detail) {
                    $product = Product::findOrFail($purchase_detail->product_id);
                    $product->decrement('product_quantity', $purchase_detail->quantity);

                    $this->updateWarehouseStock($purchase_detail->product_id, $purchase->warehouse_id, $purchase_detail->quantity, 'decrement');
                }
            }

            $purchase->delete();
            DB::commit();
            toast('Purchase Deleted!', 'warning');
            return redirect()->route('purchases.index');
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
            if ($stock) {
                $stock->decrement('qty', $qty);
            }
        }
    }

    public function show(Purchase $purchase)
    {
        abort_if(Gate::denies('show_purchases'), 403);
        $supplier = Supplier::findOrFail($purchase->supplier_id);
        return view('purchase::show', compact('purchase', 'supplier'));
    }

    public function edit(Purchase $purchase)
    {
        abort_if(Gate::denies('edit_purchases'), 403);
        $purchase_details = $purchase->purchaseDetails;
        Cart::instance('purchase')->destroy();
        $cart = Cart::instance('purchase');

        foreach ($purchase_details as $purchase_detail) {
            $cart->add([
                'id'      => $purchase_detail->product_id,
                'name'    => $purchase_detail->product_name,
                'qty'     => $purchase_detail->quantity,
                'price'   => $purchase_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $purchase_detail->product_discount_amount,
                    'product_discount_type' => $purchase_detail->product_discount_type,
                    'sub_total'   => $purchase_detail->sub_total,
                    'code'        => $purchase_detail->product_code,
                    'stock'       => Product::findOrFail($purchase_detail->product_id)->product_quantity,
                    'product_tax' => $purchase_detail->product_tax_amount,
                    'unit_price'  => $purchase_detail->unit_price
                ]
            ]);
        }
        return view('purchase::edit', compact('purchase'));
    }
}
