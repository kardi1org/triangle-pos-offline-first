<?php

namespace Modules\Order\Http\Controllers;

use Modules\Order\DataTables\OrderDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Product;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
//use Modules\Sale\Entities\SalePayment;
use Modules\Order\Http\Requests\StoreOrderRequest;
use Modules\Order\Http\Requests\UpdateOrderRequest;

class OrderController extends Controller
{

    public function index(OrderDataTable $dataTable) {
        abort_if(Gate::denies('access_orders'), 403);

        return $dataTable->render('order::index');
    }


    public function create() {
        abort_if(Gate::denies('create_orders'), 403);

        Cart::instance('order')->destroy();

        return view('order::create');
    }

    public function showData(StoreOrderRequest $request)
    {
        // Misalkan $dataToLog adalah data yang ingin Anda kirim ke view
        // Ini bisa saja $request->all() jika Anda ingin log apa yang baru diterima
        $dataToLog = ['customer_name' => $request->input('customer_name'), 
                      'total_amount' => $request->total_amount];
        return view('order::create', ['serverData' => $dataToLog]);
    }

    public function store(StoreOrderRequest $request) {
        DB::transaction(function () use ($request) {
            $due_amount = $request->total_amount - $request->paid_amount;

            /* if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            } */

            $sale = Order::create([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
              //  'customer_name' => Customer::findOrFail($request->customer_id)->customer_name,
                'customer_name' => $request->input('customer_name'),
             //   'tax_percentage' => $request->tax_percentage,
             //   'discount_percentage' => $request->discount_percentage,
             //   'shipping_amount' => $request->shipping_amount * 100,
                'paid_amount' => $request->paid_amount,  //* 100,
                'total_amount' => $request->total_amount,  //* 100,
             //   'due_amount' => $due_amount * 100,
                'status' => $request->status,
             //   'payment_status' => $payment_status,
             //   'payment_method' => $request->payment_method,
                'note' => $request->note,
              //  'tax_amount' => Cart::instance('sale')->tax() * 100,
              //  'discount_amount' => Cart::instance('sale')->discount() * 100,
            ]);  
           // info($request->input('customer_name'));
           // $out = new \Symfony\Component\Console\Output\ConsoleOutput();
           // $out->writeln($request->input('customer_name'));

            foreach (Cart::instance('order')->content() as $cart_item) {
                OrderDetails::create([
                    'order_id' => $sale->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price, //* 100,
                    'unit_price' => $cart_item->options->unit_price,  //* 100,
                    'sub_total' => $cart_item->options->sub_total,  //* 100,
                 /*    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100, */
                ]);

                //info($request->input('customer_name'));
                /* if ($request->status == 'Shipped' || $request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $cart_item->qty
                    ]);
                } */
            }

            Cart::instance('order')->destroy();

            /* if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => $request->date,
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $request->payment_method
                ]);
            } */
        });

        toast('Order Created!', 'success');

        //return redirect()->route('orders.index');
        return redirect()->route('order.store');
       // return response()->json($request->input('customer_name'));

       /*  $dataToLog = ['customer_name' => $request->input('customer_name'), 
                      'total_amount' => $request->total_amount];
        return view('order::create', ['serverData' => $dataToLog]); */

    }


    public function show(Order $sale) {
        abort_if(Gate::denies('show_orders'), 403);

        $customer = Customer::findOrFail($sale->customer_id);

        return view('order::show', compact('order', 'customer'));
    }

    /* public function showing(Order $sale)
    {
        $order = Order::find($sale);
        return response()->json($order);
    } */

    public function edit(Order $sale) {
        abort_if(Gate::denies('edit_orders'), 403);

        $sale_details = $sale->saleDetails;

        Cart::instance('order')->destroy();

        $cart = Cart::instance('order');

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

        return view('order::edit', compact('order'));
    }


    public function update(UpdateOrderRequest $request, Order $sale) {
        DB::transaction(function () use ($request, $sale) {

            $due_amount = $request->total_amount - $request->paid_amount;

            /* if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            } */

            foreach ($sale->saleDetails as $sale_detail) {
                if ($sale->status == 'Shipped' || $sale->status == 'Completed') {
                    $product = Product::findOrFail($sale_detail->product_id);
                    $product->update([
                        'product_quantity' => $product->product_quantity + $sale_detail->quantity
                    ]);
                }
                $sale_detail->delete();
            }

            $sale->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'customer_id' => $request->customer_id,
             //   'customer_name' => Customer::findOrFail($request->customer_id)->customer_name,
                'customer_name' => $request->input('customer_name'),
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
             //   'shipping_amount' => $request->shipping_amount * 100,
             //   'paid_amount' => $request->paid_amount * 100,
             //   'total_amount' => $request->total_amount * 100,
             //   'due_amount' => $due_amount * 100,
                'status' => $request->status,
             //   'payment_status' => $payment_status,
             //   'payment_method' => $request->payment_method,
                'note' => $request->note,
             //   'tax_amount' => Cart::instance('sale')->tax() * 100,
             //  'discount_amount' => Cart::instance('sale')->discount() * 100,
            ]);

            foreach (Cart::instance('order')->content() as $cart_item) {
                OrderDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price,  //* 100,
                    'unit_price' => $cart_item->options->unit_price,  //* 100,
                    'sub_total' => $cart_item->options->sub_total,   //* 100,
                   /*  'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100, */
                ]);

                /* if ($request->status == 'Shipped' || $request->status == 'Completed') {
                    $product = Product::findOrFail($cart_item->id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $cart_item->qty
                    ]);
                } */
            }

            Cart::instance('order')->destroy();
        });

        toast('Order Updated!', 'info');

        return redirect()->route('orders.index');
    }


    public function destroy(Order $sale) {
        abort_if(Gate::denies('delete_sales'), 403);

        $sale->delete();

        toast('Sale Deleted!', 'warning');

        return redirect()->route('sales.index');
    }
}
