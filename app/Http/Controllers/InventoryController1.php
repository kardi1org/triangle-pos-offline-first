<?php

namespace App\Http\Controllers;

use App\DataTables\InventoryDataTable;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
//use Modules\People\Entities\Supplier;
//use Modules\Product\Entities\Product;
use App\Models\Inventory\Inventory;
use App\Models\InventoryDetail\InventoryDetail;
//use Modules\Inventory\Entities\InventoryPayment;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;

class InventoryController extends Controller
{

    public function index(InventoryDataTable $dataTable)
    {
        //abort_if(Gate::denies('access_Inventories'), 403);

        return $dataTable->render('inventory.index');
    }


    public function create()
    {
        abort_if(Gate::denies('create_Inventories'), 403);

        Cart::instance('Inventory')->destroy();

        return view('Inventory.create');
    }


    public function store(StoreInventoryRequest $request)
    {
        DB::transaction(function () use ($request) {
            $due_amount = $request->total_amount - $request->paid_amount;
            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            $Inventory = Inventory::create([
                'date' => $request->date,
                //'tax_percentage' => $request->tax_percentage,
                // 'discount_percentage' => $request->discount_percentage,
                //'shipping_amount' => $request->shipping_amount * 100,
                //'paid_amount' => $request->paid_amount * 100,
                'total_amount' => $request->total_amount * 100,
                'due_amount' => $due_amount * 100,
                'status' => $request->status,
                'payment_status' => $payment_status,
                //'payment_method' => $request->payment_method,
                'note' => $request->note,
                'tax_amount' => Cart::instance('Inventory')->tax() * 100,
                'discount_amount' => Cart::instance('Inventory')->discount() * 100,
            ]);

            foreach (Cart::instance('Inventory')->content() as $cart_item) {
                InventoryDetail::create([
                    'Inventory_id' => $Inventory->id,
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
                    $product->update([
                        'product_quantity' => $product->product_quantity + $cart_item->qty
                    ]);
                }
            }

            Cart::instance('Inventory')->destroy();

            if ($Inventory->paid_amount > 0) {
                InventoryPayment::create([
                    'date' => $request->date,
                    'reference' => 'INV/' . $Inventory->reference,
                    'amount' => $Inventory->paid_amount,
                    'Inventory_id' => $Inventory->id,
                    'payment_method' => $request->payment_method
                ]);
            }
        });

        toast('Inventory Created!', 'success');

        return redirect()->route('Inventories.index');
    }


    public function show(Inventory $Inventory)
    {
        abort_if(Gate::denies('show_Inventories'), 403);

        $supplier = Supplier::findOrFail($Inventory->supplier_id);

        return view('Inventory::show', compact('Inventory', 'supplier'));
    }


    public function edit(Inventory $Inventory)
    {
        abort_if(Gate::denies('edit_Inventories'), 403);

        $Inventory_details = $Inventory->InventoryDetails;

        Cart::instance('Inventory')->destroy();

        $cart = Cart::instance('Inventory');

        foreach ($Inventory_details as $Inventory_detail) {
            $cart->add([
                'id'      => $Inventory_detail->product_id,
                'name'    => $Inventory_detail->product_name,
                'qty'     => $Inventory_detail->quantity,
                'price'   => $Inventory_detail->price,
                'weight'  => 1,
                'options' => [
                    'product_discount' => $Inventory_detail->product_discount_amount,
                    'product_discount_type' => $Inventory_detail->product_discount_type,
                    'sub_total'   => $Inventory_detail->sub_total,
                    'code'        => $Inventory_detail->product_code,
                    'stock'       => Product::findOrFail($Inventory_detail->product_id)->product_quantity,
                    'product_tax' => $Inventory_detail->product_tax_amount,
                    'unit_price'  => $Inventory_detail->unit_price
                ]
            ]);
        }

        return view('Inventory::edit', compact('Inventory'));
    }


    public function update(UpdateInventoryRequest $request, Inventory $Inventory)
    {
        DB::transaction(function () use ($request, $Inventory) {
            $due_amount = $request->total_amount - $request->paid_amount;
            if ($due_amount == $request->total_amount) {
                $payment_status = 'Unpaid';
            } elseif ($due_amount > 0) {
                $payment_status = 'Partial';
            } else {
                $payment_status = 'Paid';
            }

            foreach ($Inventory->InventoryDetails as $Inventory_detail) {
                if ($Inventory->status == 'Completed') {
                    $product = Product::findOrFail($Inventory_detail->product_id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $Inventory_detail->quantity
                    ]);
                }
                $Inventory_detail->delete();
            }

            $Inventory->update([
                'date' => $request->date,
                'reference' => $request->reference,
                'supplier_id' => $request->supplier_id,
                'supplier_name' => Supplier::findOrFail($request->supplier_id)->supplier_name,
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
                'tax_amount' => Cart::instance('Inventory')->tax() * 100,
                'discount_amount' => Cart::instance('Inventory')->discount() * 100,
            ]);

            foreach (Cart::instance('Inventory')->content() as $cart_item) {
                InventoryDetail::create([
                    'Inventory_id' => $Inventory->id,
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
                    $product->update([
                        'product_quantity' => $product->product_quantity + $cart_item->qty
                    ]);
                }
            }

            Cart::instance('Inventory')->destroy();
        });

        toast('Inventory Updated!', 'info');

        return redirect()->route('Inventories.index');
    }


    public function destroy(Inventory $Inventory)
    {
        abort_if(Gate::denies('delete_Inventories'), 403);

        $Inventory->delete();

        toast('Inventory Deleted!', 'warning');

        return redirect()->route('Inventories.index');
    }
}
