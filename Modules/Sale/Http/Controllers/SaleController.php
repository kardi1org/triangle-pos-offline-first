<?php

namespace Modules\Sale\Http\Controllers;

use Modules\Sale\DataTables\SalesDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StoreSaleRequest;
use Modules\Sale\Http\Requests\UpdateSaleRequest;

class SaleController extends Controller
{

    public function index(SalesDataTable $dataTable)
    {
        abort_if(Gate::denies('access_sales'), 403);

        return $dataTable->render('sale::index');
    }


    public function create()
    {
        abort_if(Gate::denies('create_sales'), 403);

        Cart::instance('sale')->destroy();

        return view('sale::create');
    }


    public function store(StoreSaleRequest $request)
    {
        // Mengambil warehouse_id berdasarkan session outlet atau outlet pertama user
        $userOutletId = session('selected_outlet_id') ?? auth()->user()->outlets()->first()?->id;
        $warehouse = \Modules\Setting\Entities\Warehouse::where('outlet_id', $userOutletId)
            ->where('is_active', 1)
            ->first();

        if (!$warehouse) {
            toast('No active warehouse found for this outlet!', 'error');
            return redirect()->back();
        }

        $warehouse_id = $warehouse->id;

        DB::transaction(function () use ($request, $warehouse_id) {
            $due_amount = $request->total_amount - $request->paid_amount;

            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $sale = Sale::create([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
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
                'tax_amount' => Cart::instance('sale')->tax() * 100,
                'discount_amount' => Cart::instance('sale')->discount() * 100,
                'warehouse_id' => $warehouse_id,
            ]);

            foreach (Cart::instance('sale')->content() as $cart_item) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'reference'  => $sale->reference,
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

                // --- LOGIKA POTONG STOK ---
                if ($request->status == 'Shipped' || $request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);

                    if ($product->is_recipe == 'Y') {
                        $recipeHeader = \Modules\Setting\Entities\Recipe::where('product_id', $product->id)->first();
                        if ($recipeHeader) {
                            foreach ($recipeHeader->details as $detail) {
                                $qty_to_reduce = (float)$detail->quantity * $cart_item->qty;
                                $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $detail->product_id)
                                    ->where('warehouse_id', $warehouse_id)->first();
                                if ($pw) $pw->decrement('qty', $qty_to_reduce);
                            }
                        }
                    } else {
                        $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $product->id)
                            ->where('warehouse_id', $warehouse_id)->first();
                        if ($pw) $pw->decrement('qty', $cart_item->qty);
                    }
                }
            }

            Cart::instance('sale')->destroy();

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => $request->date,
                    'reference' => 'INV/' . $sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $request->payment_method,
                    'debitcard' => $request->txtdebitcard,
                    'creditcard' => $request->txtcreditcard,
                    'gopay' => $request->txtgopay,
                    'grabpay' => $request->txtgrabpay,
                    'ovopay' => $request->txtovo,
                    'shopeepay' => $request->txtshopeepay,
                    'danapay' => $request->txtdana,
                    'kredivopay' => $request->txtkredivo,
                    'qrispay' => $request->txtqris
                ]);
            }
        });

        toast('Sale Created!', 'success');
        return redirect()->route('sales.index');
    }

    // 🎯 PERBAIKAN: Menggunakan ID biasa untuk bypass 404 Model Binding
    public function update(UpdateSaleRequest $request, $sale_id)
    {
        $sale = Sale::findOrFail($sale_id);

        // Menggunakan warehouse_id yang tersimpan di data sale lama
        $warehouse_id = $sale->warehouse_id;

        DB::transaction(function () use ($request, $sale, $warehouse_id) {
            $due_amount = $request->total_amount - $request->paid_amount;

            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            // --- 1. KEMBALIKAN STOK LAMA ---
            foreach ($sale->saleDetails as $sale_detail) {
                if ($sale->status == 'Shipped' || $sale->status == 'Completed') {
                    $oldProduct = Product::find($sale_detail->product_id);
                    if ($oldProduct) {
                        if ($oldProduct->is_recipe == 'Y') {
                            $recipeHeader = \Modules\Setting\Entities\Recipe::where('product_id', $oldProduct->id)->first();
                            if ($recipeHeader) {
                                foreach ($recipeHeader->details as $detail) {
                                    $qty_to_restore = (float)$detail->quantity * $sale_detail->quantity;
                                    $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $detail->product_id)
                                        ->where('warehouse_id', $warehouse_id)->first();
                                    if ($pw) $pw->increment('qty', $qty_to_restore);
                                }
                            }
                        } else {
                            $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $oldProduct->id)
                                ->where('warehouse_id', $warehouse_id)->first();
                            if ($pw) $pw->increment('qty', $sale_detail->quantity);
                        }
                    }
                }
                $sale_detail->delete();
            }

            // --- 2. UPDATE DATA SALE ---
            $sale->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_id,
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
                'tax_amount' => Cart::instance('sale')->tax() * 100,
                'discount_amount' => Cart::instance('sale')->discount() * 100,
            ]);

            // Contoh cara update pembayaran setelah $sale->update()
            $payment = \Modules\Sale\Entities\SalePayment::where('sale_id', $sale->id)->first();
            if ($payment) {
                $payment->update([
                    'amount'     => $sale->paid_amount,
                    'debitcard'  => $request->txtdebitcard,
                    'creditcard' => $request->txtcreditcard,
                    'gopay'      => $request->txtgopay,
                    'grabpay'    => $request->txtgrabpay,
                    'ovopay'     => $request->txtovo,
                    'shopeepay'  => $request->txtshopeepay,
                    'danapay'    => $request->txtdana,
                    'kredivopay' => $request->txtkredivo,
                    'qrispay'    => $request->txtqris,
                ]);
            }

            // --- 3. SIMPAN DETAIL BARU & POTONG STOK BARU ---
            foreach (Cart::instance('sale')->content() as $cart_item) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'reference' => $request->reference,
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
                    if ($product->is_recipe == 'Y') {
                        $recipeHeader = \Modules\Setting\Entities\Recipe::where('product_id', $product->id)->first();
                        if ($recipeHeader) {
                            foreach ($recipeHeader->details as $detail) {
                                $qty_to_reduce = (float)$detail->quantity * $cart_item->qty;
                                $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $detail->product_id)
                                    ->where('warehouse_id', $warehouse_id)->first();
                                if ($pw) $pw->decrement('qty', $qty_to_reduce);
                            }
                        }
                    } else {
                        $pw = \Modules\Setting\Entities\ProductWarehouse::where('product_id', $product->id)
                            ->where('warehouse_id', $warehouse_id)->first();
                        if ($pw) $pw->decrement('qty', $cart_item->qty);
                    }
                }
            }

            Cart::instance('sale')->destroy();
        });

        toast('Sale Updated!', 'info');
        return redirect()->route('sales.index');
    }

    // 🎯 PERBAIKAN: Menggunakan ID biasa untuk bypass 404 Model Binding
    public function show($sale_id)
    {
        abort_if(Gate::denies('show_sales'), 403);

        $sale = Sale::findOrFail($sale_id);

        return view('sale::show', compact('sale'));
    }


    // 🎯 PERBAIKAN: Menggunakan ID biasa untuk bypass 404 Model Binding
    public function edit($sale_id)
    {
        abort_if(Gate::denies('edit_sales'), 403);

        $sale = Sale::findOrFail($sale_id);
        $sale_details = $sale->saleDetails;

        Cart::instance('sale')->destroy();

        $cart = Cart::instance('sale');

        foreach ($sale_details as $sale_detail) {
            $cart->add([
                'id'      => $sale_detail->product_id,
                'name'    => $sale_detail->product_name,
                'qty'     => $sale_detail->quantity,
                'price'   => $sale_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $sale_detail->product_discount_amount,
                    'product_discount_type' => $sale_detail->product_discount_type,
                    'sub_total'   => $sale_detail->sub_total,
                    'code'        => $sale_detail->product_code,
                    'stock'       => Product::findOrFail($sale_detail->product_id)->product_quantity,
                    'product_tax' => $sale_detail->product_tax_amount,
                    'unit_price'  => $sale_detail->unit_price
                ]
            ]);
        }

        return view('sale::edit', compact('sale'));
    }


    // 🎯 PERBAIKAN: Menggunakan ID biasa untuk bypass 404 Model Binding
    public function destroy($sale_id)
    {
        abort_if(Gate::denies('delete_sales'), 403);

        $sale = Sale::findOrFail($sale_id);
        $sale->delete();

        toast('Sale Deleted!', 'warning');

        return redirect()->route('sales.index');
    }
}
