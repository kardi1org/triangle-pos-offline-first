<?php

namespace App\Livewire\Pos;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;
//use App\Livewire\Pos\StorePosOrderRequest;
//use App\Modules\Order\Http\Requests\StorePosOrderRequest;
//use App\Order\Http\Requests\StorePosOrderRequest;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Illuminate\Http\Request;
use Modules\Order\Http\Controllers\PosController\StorePosOrderRequest;
use App\Modules\Order\Http\Controllers\PosOrderController;
//use App\Http\Controllers\PosOrderController;
//use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;
use App\Modules\Sale\Resource\Views\Prints;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Checkout extends Component
{
    //use HasFactory;

    public $listeners = ['productSelected', 'discountModalRefresh'];
    public $cart_instance;
    public $customers;
    public $global_discount;
    public $global_tax;
    public $shipping;
    public $quantity;
    public $check_quantity;
    public $discount_type;
    public $item_discount;
    public $data;
    public $customer_id;
    public $customer_name;
    public $total_amount;
    public $prevQty;
    public $cash;
    public $payments;
    public $qty1;

    public function mount($cartInstance, $customers, $payments)
    {
        $this->cart_instance = $cartInstance;
        $this->customers = $customers;
        $this->global_discount = 0;
        $this->global_tax = 0;
        $this->shipping = 0.00;
        $this->check_quantity = [];
        $this->quantity = [];
        $this->discount_type = [];
        $this->item_discount = [];
        $this->total_amount = 0;
        $this->cash = 4000;
        $this->payments = $payments;
    }

    public function hydrate()
    {
        $this->total_amount = $this->calculateTotal();
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.pos.checkout', [
            'cart_items' => $cart_items
        ]);
    }

    public function proceed()
    {
        //if ($this->customer_name != null) {
        $this->dispatch('showCheckoutModal');
        //} else {
        //session()->flash('message', 'Please Customer Name !');
        //}
    }

    public function saveOrder()
    {
        if ($this->customer_name != null) {
            //  $this->dispatch('showCheckoutModal');
            //return redirect()->route('app.pos.saveorder');
            //redirect()->route('app.pos.store');
            //redirect()->route('saveorder.store');

            /*   if ($result->save()) {
                    alert()->success('Data Berhasil Disimpan ke Database.','Tersimpan!')->autoclose(4000);
                    return redirect()->route('admin.order');
                } else {
                   alert()->info('Harap Periksa lagi data Formulir anda.','Tidak Tersimpan!')->autoclose(4000);
                }

                alert()->success('Data Successfully Saved to Database','Saved !')->autoclose(4000);
//-------------------------------------------------------------------------//
           } else {
              session()->flash('message', 'Please fill in the Customer Name !'); */
            //-------------------------------------------------------------------------//
            try {
                $cart_items = Cart::instance($this->cart_instance)->content();
                return view('livewire.pos.checkout', ['cart_items' => $cart_items]);

                foreach (Cart::instance('sale')->content() as $cart_item) {
                    $data = [
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'message' => 'Sale Created!'
                    ];
                    return response()->json($data);
                }
                //  return response()->json(['data' => $order]);
                //  return view('sales.index', compact('orders'));
                //  return redirect()->route('sales.index')->with('message', 'Data Successfully Saved to Database!');
                //  return redirect()->route('sales.cetakstruk');
                //   return view('prints.receipt_thermal', compact('orders'));
                //  return view('order::pos.index', compact('orders'));
                //  return redirect()->route('show.showorder')->with('message', 'Data Order Successfully Saved to Database!');
                //   redirect()->route('save.saveorder');

            } catch (ValidationException $err) {
                return response()->json([
                    'status' => 422,
                    'msg' => 'error',
                    'errors' => $err->errors(),
                ], 422);
            }
            //-------------------------------------------------------------------------//
        } else {
            session()->flash('message', 'Please fill in the Customer Name !');
        }
    }

    public function getdatacart()
    {
        if ($this->customer_name != null) {

            try {
                $cart_items = Cart::instance($this->cart_instance)->content();
                return view('livewire.pos.checkout', ['cart_items' => $cart_items]);

                foreach (Cart::instance('sale')->content() as $cart_item) {
                    $data = [
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'message' => 'Sale Created!'
                    ];
                    return response()->json($data);
                }
            } catch (ValidationException $err) {
                return response()->json([
                    'status' => 422,
                    'msg' => 'error',
                    'errors' => $err->errors(),
                ], 422);
            }
        } else {
            session()->flash('message', 'Please fill in the Customer Name !');
        }
    }

    public function calculateTotal()
    {
        return Cart::instance($this->cart_instance)->total() + $this->shipping;
    }

    public function resetCart()
    {
        Cart::instance($this->cart_instance)->destroy();
    }

    public function productSelected($product)
    {
        $cart = Cart::instance($this->cart_instance);

        // Cek apakah produk sudah ada di cart
        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        if ($exists->isNotEmpty()) {
            $rowId = $exists->first()->rowId;
            $cartItem = $cart->get($rowId);

            $newQty = $cartItem->qty + 1;

            if ($newQty > $product['product_quantity']) {
                session()->flash('message', 'Stok tidak cukup untuk ' . $product['product_name']);
                return;
            }

            $cart->update($rowId, $newQty);

            $cart->update($rowId, [
                'options' => [
                    'sub_total'             => $cartItem->price * $newQty,
                    'code'                  => $cartItem->options->code,
                    'stock'                 => $cartItem->options->stock,
                    'unit'                  => $cartItem->options->unit,
                    'product_tax'           => $cartItem->options->product_tax,
                    'unit_price'            => $cartItem->options->unit_price,
                    'product_discount'      => $cartItem->options->product_discount,
                    'product_discount_type' => $cartItem->options->product_discount_type,
                ]
            ]);
        } else {
            $cart->add([
                'id'      => $product['id'],
                'name'    => $product['product_name'],
                'qty'     => 1,
                'price'   => $this->calculate($product)['price'],
                'weight'  => 1,
                'options' => [
                    'product_discount'      => 0.00,
                    'product_discount_type' => 'fixed',
                    'sub_total'             => $this->calculate($product)['sub_total'],
                    'code'                  => $product['product_code'],
                    'stock'                 => $product['product_quantity'],
                    'unit'                  => $product['product_unit'],
                    'product_tax'           => $this->calculate($product)['product_tax'],
                    'unit_price'            => $this->calculate($product)['unit_price'],
                ]
            ]);
        }

        $this->check_quantity[$product['id']] = $product['product_quantity'];
        $this->quantity[$product['id']] = isset($this->quantity[$product['id']])
            ? $this->quantity[$product['id']] + 1
            : 1;
        $this->discount_type[$product['id']] = 'fixed';
        $this->item_discount[$product['id']] = 0;

        $this->total_amount = $this->calculateTotal();
    }


    public function removeItem($row_id)
    {
        Cart::instance($this->cart_instance)->remove($row_id);
    }

    public function updatedGlobalTax()
    {
        Cart::instance($this->cart_instance)->setGlobalTax((int)$this->global_tax);
    }

    public function updatedGlobalDiscount()
    {
        Cart::instance($this->cart_instance)->setGlobalDiscount((int)$this->global_discount);
    }



    public function updateQuantity($row_id, $product_id)
    {
        if ($this->check_quantity[$product_id] < $this->quantity[$product_id]) {
            session()->flash('message', 'The requested quantity is not available in stock.');

            return;
        }

        if ($this->quantity[$product_id] < 1) {
            session()->flash('message', 'The requested quantity must be greater than 0.');

            return;
        }

        Cart::instance($this->cart_instance)->update($row_id, $this->quantity[$product_id]);

        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        Cart::instance($this->cart_instance)->update($row_id, [
            'options' => [
                'sub_total'             => $cart_item->price * $cart_item->qty,
                'code'                  => $cart_item->options->code,
                'stock'                 => $cart_item->options->stock,
                'unit'                  => $cart_item->options->unit,
                'product_tax'           => $cart_item->options->product_tax,
                'unit_price'            => $cart_item->options->unit_price,
                'product_discount'      => $cart_item->options->product_discount,
                'product_discount_type' => $cart_item->options->product_discount_type,
            ]
        ]);
    }

    public function updatedDiscountType($value, $name)
    {
        $this->item_discount[$name] = 0;
    }

    public function discountModalRefresh($product_id, $row_id)
    {
        $this->updateQuantity($row_id, $product_id);
    }

    public function setProductDiscount($row_id, $product_id)
    {
        $cart_item = Cart::instance($this->cart_instance)->get($row_id);

        if ($this->discount_type[$product_id] == 'fixed') {
            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $this->item_discount[$product_id]
                ]);

            $discount_amount = $this->item_discount[$product_id];

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        } elseif ($this->discount_type[$product_id] == 'percentage') {
            $discount_amount = ($cart_item->price + $cart_item->options->product_discount) * ($this->item_discount[$product_id] / 100);

            Cart::instance($this->cart_instance)
                ->update($row_id, [
                    'price' => ($cart_item->price + $cart_item->options->product_discount) - $discount_amount
                ]);

            $this->updateCartOptions($row_id, $product_id, $cart_item, $discount_amount);
        }

        session()->flash('discount_message' . $product_id, 'Discount added to the product!');
    }

    public function calculate($product)
    {
        $price = 0;
        $unit_price = 0;
        $product_tax = 0;
        $sub_total = 0;

        if ($product['product_tax_type'] == 1) {
            $price = $product['product_price'] + ($product['product_price'] * ($product['product_order_tax'] / 100));
            $unit_price = $product['product_price'];
            $product_tax = $product['product_price'] * ($product['product_order_tax'] / 100);
            $sub_total = $product['product_price'] + ($product['product_price'] * ($product['product_order_tax'] / 100));
        } elseif ($product['product_tax_type'] == 2) {
            $price = $product['product_price'];
            $unit_price = $product['product_price'] - ($product['product_price'] * ($product['product_order_tax'] / 100));
            $product_tax = $product['product_price'] * ($product['product_order_tax'] / 100);
            $sub_total = $product['product_price'];
        } else {
            $price = $product['product_price'];
            $unit_price = $product['product_price'];
            $product_tax = 0.00;
            $sub_total = $product['product_price'];
        }

        return ['price' => $price, 'unit_price' => $unit_price, 'product_tax' => $product_tax, 'sub_total' => $sub_total];
    }

    public function updateCartOptions($row_id, $product_id, $cart_item, $discount_amount)
    {
        Cart::instance($this->cart_instance)->update($row_id, ['options' => [
            'sub_total'             => $cart_item->price * $cart_item->qty,
            'code'                  => $cart_item->options->code,
            'stock'                 => $cart_item->options->stock,
            'unit'                 => $cart_item->options->unit,
            'product_tax'           => $cart_item->options->product_tax,
            'unit_price'            => $cart_item->options->unit_price,
            'product_discount'      => $discount_amount,
            'product_discount_type' => $this->discount_type[$product_id],
        ]]);
    }

    public function updateQuantityPlus($row_id, $product_id)
    {
        $cart = Cart::instance($this->cart_instance);

        // Cari rowId yang valid berdasarkan product_id
        $cart_item = $cart->search(fn ($item) => $item->id == $product_id)->first();

        if (!$cart_item) {
            return; // Item tidak ditemukan (mungkin sedang dihapus / race condition)
        }

        $newQty = ($this->quantity[$product_id] ?? $cart_item->qty) + 1;

        // Cek stok
        if ($cart_item->options->stock < $newQty) {
            session()->flash('message', 'Stok tidak mencukupi untuk produk ini.');
            return;
        }

        // Update qty
        $cart->update($cart_item->rowId, $newQty);

        // Update data Livewire
        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();
    }


    public function updateQuantityMin($row_id, $product_id)
    {
        $cart = Cart::instance($this->cart_instance);

        $cart_item = $cart->search(fn ($item) => $item->id == $product_id)->first();

        if (!$cart_item) {
            return;
        }

        $newQty = max(1, ($this->quantity[$product_id] ?? $cart_item->qty) - 1);

        $cart->update($cart_item->rowId, $newQty);

        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();
    }
}
