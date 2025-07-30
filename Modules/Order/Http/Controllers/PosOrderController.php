<?php

namespace Modules\Order\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
//use Modules\Sale\Entities\SalePayment;
use Modules\Order\Http\Requests\StorePosOrderRequest;

class PosOrderController extends Controller
{

    public function index() {
        Cart::instance('order')->destroy();

      //  $customers = Customer::all();
        $orders = Order::all();  // Add by Chris
      //  $product_categories = Category::all();

        //return view('order::pos.index', compact('product_categories', 'customers'));
        return view('order::pos.index', compact('orders'));
    }


    //public function store(StorePosOrderRequest $request) {
    public function store(Request $request) {
        DB::transaction(function () use ($request) {
           // $due_amount = $request->total_amount - $request->paid_amount;
            $customer_id = '.';  

            /* if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid'; 
            } */

            /* $customer = Customer::firstOrCreate(
                ['customer_name' => $request->customer_name]
            ); */

            $order = Order::create([
                'date' => now()->format('Y-m-d'),
                'reference' => 'POD',
             //   'customer_id' => $request->customer_id, 
             //   'customer_name' => Customer::findOrFail($request->customer_id)->customer_name,
                'customer_name' => $request->input('customer_name'),
             //   'customer_name' => $request->customer_name,
             //   'tax_percentage' => $request->tax_percentage,
             //   'discount_percentage' => $request->discount_percentage,
             //   'shipping_amount' => $request->shipping_amount * 100,
             //   'paid_amount' => $request->paid_amount, //* 100,
                'total_amount' => $request->total_amount, //* 100,
             //   'due_amount' => $due_amount, //* 100,
              //  'status' => 'Completed',
              //  'payment_status' => $payment_status,
              //  'payment_method' => $request->payment_method,
              //  'note' => $request->note,
             //   'tax_amount' => Cart::instance('sale')->tax() * 100,
             //   'discount_amount' => Cart::instance('sale')->discount() * 100,
            ]);

            /* return response()->json([
                'status' =>  $order,
                'data'   => $order,
                'message' => $order ? 'Order Created!' : 'Error Creating Order'
            ]);  */   

            foreach (Cart::instance('order')->content() as $cart_item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $cart_item->id,
                    'product_name' => $cart_item->name,
                    'product_code' => $cart_item->options->code,
                   // 'product_code' => Cart::instance($this->cart_instance)->content(),
                    'quantity' => $cart_item->qty,
                    'price' => $cart_item->price,  //* 100,
                    'unit_price' => $cart_item->options->unit_price, // * 100,
                    'sub_total' => $cart_item->options->sub_total, // * 100,
                /*    'product_discount_amount' => $cart_item->options->product_discount * 100,
                    'product_discount_type' => $cart_item->options->product_discount_type,
                    'product_tax_amount' => $cart_item->options->product_tax * 100, */
                ]);

                /* $product = Product::findOrFail($cart_item->id);
                $product->update([
                    'product_quantity' => $product->product_quantity - $cart_item->qty
                ]); */
            }

            Cart::instance('order')->destroy();

            /* if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'date' => now()->format('Y-m-d'),
                    'reference' => 'INV/'.$sale->reference,
                    'amount' => $sale->paid_amount,
                    'sale_id' => $sale->id,
                    'payment_method' => $request->payment_method
                ]);
            } */
        });

        toast('POS Order Created!', 'success');

       // return redirect()->route('orders.index');
        return redirect()->route('orders.store'); 
    }

//    public function saveorder(StorePosOrderRequest $request) {
    public function saveorder(Request $request) {    
//-------------------------------------------------------------------------//
        DB::transaction(function () use ($request) {
            $order = Order::create([
               'date' => now()->format('Y-m-d'), // $request->date,
               'reference' => 'POD',
               'customer_id' => $request->customer_id,
               'customer_name' => $request->input('customer_name'), 
                      /*   'tax_percentage' => $request->tax_percentage,
                        'discount_percentage' => $request->discount_percentage,
                        'paid_amount' => $request->total_amount,   */
               'total_amount' => $request->total_amount, 
                     //   'status' => $request->status,
               'note' => $request->note,
            ]); 

            foreach (Cart::instance('order')->content() as $cart_item) {
                OrderDetails::create([
                   'order_id' => $order->id,
                   'product_id' => $cart_item->id,
                   'product_name' => $cart_item->name,
                   'product_code' => $cart_item->options->code, 
                //  'product_code' => Cart::instance($this->cart_instance)->content(),
                   'quantity' => $cart_item->qty,
                   'price' => $cart_item->price, //* 100,
                   'unit_price' => $cart_item->options->unit_price, //* 100,
                   'sub_total' => $cart_item->options->sub_total, //* 100,
                ]);

            }
           // Cart::instance('order')->destroy();
        });

        alert()->success('Data Successfully Saved to Database','Saved !')->autoclose(4000);

        /* return response()->json([
                    'status' =>  $request,
                    'data'   => $request,
                    'message' => $request ? 'Order Created!' : 'Error Creating Order'
        ]);  */   
       //return redirect()->route('orders.saveorder');
       //return redirect()->route('app.pos.saveorder'); 
        return redirect()->route('save.saveorder');
        /* $order = [];
        return response()->json(['data' => $order]); */
      }

    public function update(Request $request, Order $sale) {
        DB::transaction(function () use ($request, $sale) {

            /*$due_amount = $request->total_amount - $request->paid_amount;

             if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            } */

            /* foreach ($sale->saleDetails as $sale_detail) {
                if ($sale->status == 'Shipped' || $sale->status == 'Completed') {
                    $product = Product::findOrFail($sale_detail->product_id);
                    $product->update([
                        'product_quantity' => $product->product_quantity + $sale_detail->quantity
                    ]);
                }
                $sale_detail->delete();
            } */

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
}

                /* if ($result->save()) {
                    alert()->success('Data Berhasil Disimpan ke Database.','Tersimpan!')->autoclose(4000);
                    return redirect()->route('admin.order');
                } else {
                   alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
                } */
