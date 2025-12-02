<?php

namespace App\Livewire\Pos;

use App\Models\Meja;
use Livewire\Component;
//use App\Livewire\Pos\StorePosOrderRequest;
//use App\Modules\Order\Http\Requests\StorePosOrderRequest;
//use App\Order\Http\Requests\StorePosOrderRequest;
use Mike42\Escpos\Printer;
use Livewire\Attributes\On;
use Illuminate\Http\Request;
use Modules\Sale\Entities\Sale;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\DB;
//use App\Http\Controllers\PosOrderController;
//use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\SaleDetails;
use Modules\Order\Entities\OrderDetails;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Modules\Sale\Resource\Views\Prints;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Modules\Order\Http\Controllers\PosOrderController;
use Modules\Order\Http\Controllers\PosController\StorePosOrderRequest;


class Checkout extends Component
{
    //use HasFactory;

    protected $listeners = [
        'productSelected' => 'productSelected',
        'discountModalRefresh' => 'discountModalRefresh',
        'reloadPendingOrders' => 'loadPendingOrders',
        'updateVariant' => 'updateVariant',
        'refresh-cart' => '$refresh',
    ];

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
    public $tables;
    public $table_id;
    public $order_type = 'dine_in'; // default
    public $alertMessage = null;
    public $alertType = 'warning';
    public $pendingOrders = [];
    public $current_reference = null;
    public $selectedOrder = null;
    public $selectedOrderDetails = [];
    public $selectedOrderSummary = [];
    public $cartItems = [];

    public $item_variant = [];
    public $item_typeOrder = [];


    // ✅ event listener untuk clear alert
    #[On('clear-alert')]
    public function clearAlert()
    {
        $this->reset(['alertMessage', 'alertType']);
    }
    private function refreshCartTaxAndDiscount()
    {
        Cart::instance('sale')->setGlobalTax($this->global_tax ?? 0);
        Cart::instance('sale')->setGlobalDiscount($this->global_discount ?? 0);
    }

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

        $this->tables = Meja::orderBy('no_meja')->get();
        $this->order_type = 'dine_in';

        foreach (Cart::instance('sale')->content() as $item) {
            $this->cartItems[$item->rowId] = [
                'order_type' => $item->options->order_type ??
                    'dine_in',
                'variants' => $item->options->variants ?? // [cite: 16] <- PASTIKAN KUNCI INI SUDAH 'variants'
                    [],
            ];
        }
    }

    // #[On('updateVariant')]
    // public function updateVariant($payload = null)
    // {
    //     if (!$payload) return;

    //     $productId = $payload['productId'] ?? null;
    //     $variants  = $payload['variants'] ?? [];

    //     // Simpan juga ke session (kalau Anda butuh restore)
    //     session()->put("variant_session.$productId", $variants);

    //     // Update cart
    //     $cart = Cart::instance('sale');
    //     $cartItem = $cart->search(fn ($item) => $item->id == $productId)->first();

    //     if ($cartItem) {

    //         // Ambil semua options existing
    //         $options = $cartItem->options->toArray();

    //         // ⬅️ Pastikan pakai KUNCI YANG BENAR: `variant_detail`
    //         $options['variants'] = $variants;

    //         // Update cart
    //         $cart->update($cartItem->rowId, [
    //             'options' => $options
    //         ]);
    //     }
    // }

    #[On('updateVariant')]
    public function updateVariant($productId, $variants = [])
    {
        // Logging untuk debugging
        logger('🔥 PRODUCT ID', [$productId]);
        logger('🔥 VARIANTS', [$variants]);

        if (!$productId) {
            logger('❌ productId kosong, update dibatalkan');
            return;
        }

        // Simpan sementara ke session (optional)
        session()->put("variant_session.$productId", $variants);

        // Ambil cart instance
        $cart = Cart::instance('sale');

        // Cari item berdasarkan id product
        $cartItem = $cart->search(fn ($item) => $item->id == $productId)->first();

        logger('🛒 CART ITEM', [$cartItem ? $cartItem->toArray() : 'NOT FOUND']);

        if (!$cartItem) {
            logger('❌ CART ITEM NOT FOUND UNTUK PRODUCT ID:', [$productId]);
            return;
        }

        // Ambil options lama
        $options = $cartItem->options->toArray();

        logger('📦 OPTIONS SEBELUM UPDATE', [$options]);
        logger('📦 VARIANTS BARU', [$variants]);

        // Tambahkan variants ke options
        $options['variants'] = $variants;

        // Update cart item
        $cart->update($cartItem->rowId, [
            'qty'     => $cartItem->qty,
            'options' => $options,
        ]);

        // Ambil ulang untuk memastikan data masuk
        $after = $cart->get($cartItem->rowId)->options ?? [];

        logger('📦 OPTIONS SETELAH UPDATE', [$after]);
    }

    public function hydrate()
    {
        $this->total_amount = $this->calculateTotal();
    }

    public function render()
    {
        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.pos.checkout', [
            'cart_items' => $cart_items,
            'tables' => $this->tables, // ✅ tambahkan ini
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
        $total = (float) str_replace(',', '', Cart::instance($this->cart_instance)->total());
        $shipping = (float) $this->shipping;

        return $total + $shipping;
    }


    public function resetCart()
    {
        session()->forget('variant_session');
        Cart::instance($this->cart_instance)->destroy();

        $this->reset([
            'quantity',
            'check_quantity',
            'discount_type',
            'item_discount',
            'total_amount',

            'customer_name',
            'order_type',
            'table_id',
            'check_quantity',
            'current_reference'
        ]);

        // reset default
        $this->global_discount = 0;
        $this->global_tax = 0;
        $this->shipping = 0.00;

        // 🔹 dispatch event untuk reset JS modal variant
        $this->dispatch('variant-modal-reset-all');

        $this->dispatch('$refresh');
    }


    public function productSelected($product)
    {
        $cart = Cart::instance($this->cart_instance);
        // Cek apakah produk sudah ada di cart
        $exists = $cart->search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id == $product['id'];
        });

        //
        if ($exists->isNotEmpty()) {
            $rowId = $exists->first()->rowId;
            $cartItem = $cart->get($rowId);
            $newQty = $cartItem->qty + 1;

            if ($newQty > $product['product_quantity']) {
                session()->flash('message', 'Stok tidak cukup untuk ' . $product['product_name']);
                return;
            }

            // 1. Update Qty saja (ini tidak menghapus options)
            $cart->update($rowId, $newQty);

            // 2. Ambil item yang sudah di-update QTY-nya
            $updatedItem = $cart->get($rowId);

            // 🔥 FIX: Ambil semua opsi lama, termasuk 'variants'
            $options = $updatedItem->options->toArray();

            // 3. Perbarui sub_total (dan opsi lain jika perlu)
            $options['sub_total'] = $updatedItem->price * $updatedItem->qty;

            // 4. Simpan kembali opsi lengkap ke keranjang (mempertahankan variants)
            $cart->update($rowId, [
                'options' => $options
            ]);

            //
            $this->refreshCartTaxAndDiscount();
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
            // 🧩 Tambahkan baris ini
            $this->refreshCartTaxAndDiscount();
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
        $cart = Cart::instance($this->cart_instance);

        $item = $cart->get($row_id);

        if ($item) {
            $productId = $item->id;

            // Reset Livewire & Session state
            $this->resetProductState($productId);

            // Remove item dari cart
            $cart->remove($row_id);
        }

        // Hitung ulang total
        $this->total_amount = $this->calculateTotal();

        // Reset modal variant (UI) — kirim productId supaya JS hapus cache JS juga
        $this->dispatch('variant-modal-reset', ['productId' => $productId ?? null]);

        // Refresh livewire
        $this->dispatch('$refresh');
    }

    private function resetProductState($productId)
    {
        // 1. Hapus session variant
        session()->forget("variant_session.$productId");

        // 2. Hapus quantity Livewire
        if (isset($this->quantity[$productId])) {
            unset($this->quantity[$productId]);
        }

        // 3. Hapus check_quantity (variant per qty)
        if (isset($this->check_quantity[$productId])) {
            unset($this->check_quantity[$productId]);
        }

        // 4. Hapus cartItems yg berhubungan
        foreach ($this->cartItems as $key => $item) {
            if (!empty($item['product_id']) && $item['product_id'] == $productId) {
                unset($this->cartItems[$key]);
            }
        }

        // 5. Jika punya array variant lain, hapus juga (opsional)
        if (property_exists($this, 'variant_by_product') && isset($this->variant_by_product[$productId])) {
            unset($this->variant_by_product[$productId]);
        }

        // Hapus variants dari cartItems jika ada
        foreach ($this->cartItems as $row => $item) {
            if (isset($item['variants'])) {
                unset($this->cartItems[$row]['variants']);
            }
        }
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
        $cart = Cart::instance($this->cart_instance);

        // Ambil cart item awal
        $cart_item = $cart->get($row_id);
        if (!$cart_item) return;

        $newQty = $this->quantity[$product_id] ?? $cart_item->qty;

        // Stock
        $availableStock = $this->check_quantity[$product_id]
            ?? $cart_item->options->stock
            ?? 0;

        // Validasi minimal
        if ($newQty < 1) $newQty = 1;

        // Validasi stok
        if ($newQty > $availableStock) {
            session()->flash('message', 'The requested quantity is not available in stock.');
            $newQty = $cart_item->qty;
        }

        // 1️⃣ Update qty dulu
        $cart->update($row_id, $newQty);

        // 2️⃣ Hapus variant (TAPI jangan pakai row_id lama!)
        $this->removeVariantAfterQtyDecrease($product_id, $newQty);

        // 3️⃣ Normalize variant (TANPA ROW_ID)
        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

        // 4️⃣ Simpan qty ke state
        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();

        // 5️⃣ ***AMBIL CART ITEM TERBARU BERDASARKAN PRODUCT ID***
        $newCartItem = $cart->search(fn ($i) => $i->id == $product_id)->first();
        if (!$newCartItem) return; // antisipasi

        // 6️⃣ Update options menggunakan ROW ID BARU
        $cart->update($newCartItem->rowId, [
            'options' => [
                'sub_total'             => $newCartItem->price * $newCartItem->qty,
                'code'                  => $newCartItem->options->code,
                'stock'                 => $newCartItem->options->stock,
                'unit'                  => $newCartItem->options->unit,
                'product_tax'           => $newCartItem->options->product_tax,
                'unit_price'            => $newCartItem->options->unit_price,
                'product_discount'      => $newCartItem->options->product_discount,
                'product_discount_type' => $newCartItem->options->product_discount_type,
                'variants'              => $newCartItem->options->variants ?? [],
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
        // if ($cart_item->options->stock < $newQty) {
        //     session()->flash('message', 'Stok tidak mencukupi untuk produk ini.');
        //     return;
        // }

        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

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

        // Qty baru → minimal 1
        $newQty = max(1, ($this->quantity[$product_id] ?? $cart_item->qty) - 1);

        // Update qty di cart
        $cart->update($cart_item->rowId, $newQty);

        // 🟦 HAPUS VARIANT OTOMATIS: buang semua variant yang index > qty baru
        $this->removeVariantAfterQtyDecrease($product_id, $newQty);

        // Normalisasi variant Anda yang lama
        $this->normalizeVariantsAfterQtyChange($product_id, $newQty);

        $this->quantity[$product_id] = $newQty;
        $this->total_amount = $this->calculateTotal();
    }

    public function removeVariantAfterQtyDecrease($productId, $newQty)
    {
        // === SESSION ===
        $sessionVariants = session()->get("variant_session.$productId", []);

        if (count($sessionVariants) > $newQty) {
            $sessionVariants = array_slice($sessionVariants, 0, $newQty);
            session()->put("variant_session.$productId", $sessionVariants);
        }

        // === CART ===
        $cart = Cart::instance('sale');

        // SELALU DAPATKAN CART ITEM TERBARU BERDASARKAN PRODUCT ID
        $cartItem = $cart->search(fn ($item) => $item->id == $productId)->first();

        if (!$cartItem) return;

        $options = $cartItem->options->toArray();

        if (isset($options['variants']) && count($options['variants']) > $newQty) {
            $options['variants'] = array_slice($options['variants'], 0, $newQty);
        }

        // 🟦 GUNAKAN ROWID TERBARU DARI $cartItem — BUKAN ROWID LAMA DARI updateQuantity()
        $cart->update($cartItem->rowId, [
            'qty'     => $newQty,
            'options' => $options,
        ]);
    }


    public function saveOrderPending()
    {
        if (Cart::instance('sale')->count() == 0) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Keranjang masih kosong!';
            $this->dispatch('auto-hide-alert');
            return;
        }

        // Pastikan angka bertipe numerik
        $total = (float) str_replace(',', '', Cart::instance($this->cart_instance)->total());
        $shipping = (float) ($this->shipping ?? 0);
        $tax = (float) str_replace(',', '', Cart::instance($this->cart_instance)->tax());
        $discount = (float) str_replace(',', '', Cart::instance($this->cart_instance)->discount());

        // ✅ Cek apakah ini update atau create baru
        $sale = null;

        if (!empty($this->current_reference)) {
            $sale = Sale::where('reference', $this->current_reference)->first();

            if ($sale) {
                // Hapus detail lama
                SaleDetails::where('sale_id', $sale->id)->delete();

                // Update summary
                $sale->update([
                    'customer_name' => $this->customer_name ?? 'Guest',
                    'order_type' => $this->order_type,
                    'table_id' => $this->table_id ?? null,
                    'total_amount' => ($total + $shipping) * 100,
                    'tax_percentage' => (float) ($this->global_tax ?? 0),
                    'discount_percentage' => (float) ($this->global_discount ?? 0),
                    'shipping_amount' => $shipping * 100,
                    'tax_amount' => $tax * 100,
                    'discount_amount' => $discount * 100,
                    'status' => 'Pending',
                    'payment_status' => 'Unpaid',
                ]);
            }
        }

        // ✅ Kalau tidak ada reference, buat baru
        if (!$sale) {
            $reference = $this->generateSalesNumber();

            $sale = Sale::create([
                'date' => now()->format('Y-m-d'),
                'reference' => $reference,
                'customer_id' => null,
                'customer_name' => $this->customer_name ?? 'Guest',
                'order_type' => $this->order_type,
                'table_id' => $this->table_id ?? null,
                'tax_percentage' => (float) ($this->global_tax ?? 0),
                'discount_percentage' => (float) ($this->global_discount ?? 0),
                'shipping_amount' => $shipping * 100,
                'total_amount' => ($total + $shipping) * 100,
                'status' => 'Pending',
                'payment_status' => 'Unpaid',
                'tax_amount' => $tax * 100,
                'discount_amount' => $discount * 100,
            ]);

            // Simpan reference agar bisa update nanti
            $this->current_reference = $reference;
        }

        // ✅ Simpan detail baru
        foreach (Cart::instance('sale')->content() as $cart_item) {

            logger()->info("VARIANTS DI CART", [
                'product' => $cart_item->name,
                'options' => $cart_item->options,
                'variants' => $cart_item->options->variants ?? null
            ]);

            SaleDetails::create([
                'sale_id' => $sale->id,
                'reference' => $sale->reference,
                'product_id' => $cart_item->id,
                'product_name' => $cart_item->name,
                'product_code' => $cart_item->options->code,
                'quantity' => $cart_item->qty,
                'price' => (float) $cart_item->price * 100,
                'unit_price' => (float) ($cart_item->options->unit_price ?? $cart_item->price) * 100,
                'sub_total' => (float) ($cart_item->options->sub_total ?? $cart_item->subtotal) * 100,
                'product_discount_amount' => (float) ($cart_item->options->product_discount ?? 0) * 100,
                'product_discount_type' => $cart_item->options->product_discount_type ?? 'fixed',
                'product_tax_amount' => (float) ($cart_item->options->product_tax ?? 0),

                // 🔥 TAMBAHAN → SIMPAN VARIANT PER ITEM
                // =======================================
                'variant_detail' => json_encode($cart_item->options->variants ?? []),
            ]);
        }

        // ✅ Bersihkan keranjang dan reset data form
        Cart::instance('sale')->destroy();
        $this->reset(['customer_name', 'order_type', 'table_id', 'current_reference']);
        $this->resetCart();

        // $this->alertType = 'success';
        // $this->alertMessage = 'Order berhasil disimpan dengan status Pending!';
        // $this->dispatch('auto-hide-alert');
    }



    public function generateSalesNumber(): string
    {
        // Mulai transaksi database untuk mencegah race condition
        return DB::transaction(function () {
            $prefix = 'SL/' . date('Ym') . '/';

            // Cari order terakhir untuk bulan dan tahun ini dengan lock
            // lockForUpdate() akan mencegah baris lain membaca record ini sampai transaksi selesai
            $lastSale = Sale::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastSale) {
                // Ambil nomor urut dari nomor order terakhir
                // Contoh: dari "DO/202510/0005", kita ambil "0005"
                $lastNumber = (int) substr($lastSale->reference, -4);
                $newNumber = $lastNumber + 1;
            } else {
                // Ini adalah order pertama di bulan ini
                $newNumber = 1;
            }
            // Format nomor baru dengan padding 4 digit
            $paddedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            return $prefix . $paddedNumber;
        });
    }

    // 🔹 Ambil semua order pending
    public function loadPendingOrders()
    {
        $this->pendingOrders = Sale::where('status', 'Pending')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    }

    // 🔹 Restore isi order pending ke cart
    public function restorePendingOrder($orderId)
    {
        $order = Sale::find($orderId);

        if (!$order) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Order tidak ditemukan.';
            $this->dispatch('auto-hide-alert');
            return;
        }

        // 🧹 Kosongkan cart dulu
        Cart::instance('sale')->destroy();
        // 🧹 Bersihkan semua state internal cart Livewire
        $this->reset([
            'customer_name',
            'order_type',
            'table_id',
            'quantity',
            'check_quantity',
            'discount_type',
            'item_discount',
            'total_amount'
        ]);

        // Kosongkan Livewire property quantity juga
        $this->quantity = [];

        $details = SaleDetails::where('sale_id', $order->id)->get();

        foreach ($details as $item) {

            // Ambil stok terupdate dari database (atau tabel stock product Anda)
            $productStock = Product::find($item->product_id)->product_quantity ?? 0;

            // Set check_quantity agar validasi stok tidak NULL
            $this->check_quantity[$item->product_id] = $productStock;

            // 🔹 Decode variant_detail per item (jika ada)
            $variantsForCart = json_decode($item->variant_detail, true) ?? [];

            $cartItem = Cart::instance('sale')->add([
                'id'      => $item->product_id,
                'name'    => $item->product_name,
                'qty'     => $item->quantity,
                'price'   => $item->unit_price,
                'weight'  => 0,
                'options' => [
                    'code' => $item->product_code,
                    'unit_price' => $item->unit_price,
                    'sub_total' => $item->sub_total,
                    'product_discount' => $item->product_discount_amount,
                    'product_discount_type' => $item->product_discount_type,
                    'product_tax' => $item->product_tax_amount,

                    // 🔥 Load variant & typeOrder
                    'variants' => $variantsForCart,
                ],
            ]);

            // 🔹 Set Livewire variable agar input variant terisi


            // 🔹 Set quantity
            $this->quantity[$item->product_id] = $item->quantity;
        }


        // Simpan reference dan data lain untuk edit
        $this->total_amount = $this->calculateTotal();
        $this->current_reference = $order->reference;
        $this->global_tax = $order->tax_percentage;
        $this->global_discount = $order->discount_percentage;
        $this->shipping = $order->shipping_amount;
        $this->order_type = $order->order_type;
        $this->table_id = $order->table_id;
        $this->customer_name = $order->customer_name;

        $this->refreshCartTaxAndDiscount();

        // ✅ Tutup modal list order
        $this->dispatch('refresh-modal-state');
        $this->dispatch('close-order-detail-modal');
        $this->dispatch('close-pending-orders-modal');


        // $this->alertType = 'info';
        // $this->alertMessage = 'Order berhasil dimuat.';
        // $this->dispatch('auto-hide-alert');
    }
    public function showOrderDetail($orderId)
    {
        $order = Sale::find($orderId);

        if (!$order) {
            $this->alertType = 'warning';
            $this->alertMessage = 'Order tidak ditemukan.';
            $this->dispatch('auto-hide-alert');
            return;
        }

        $this->selectedOrderDetails = SaleDetails::where('sale_id', $order->id)->get();

        $this->selectedOrderSummary = [
            'tax_percentage' => $order->tax_percentage,
            'tax_amount' => $order->tax_amount,
            'discount_percentage' => $order->discount_percentage,
            'discount_amount' => $order->discount_amount,
            'shipping_amount' => $order->shipping_amount,
            'total_amount' => $order->total_amount,
        ];

        // Buka modal detail
        $this->dispatch('show-order-detail-modal');
    }

    private function normalizeVariantsAfterQtyChange($productId, $newQty)
    {
        $variants = session()->get("variant_session.$productId", []);

        if (count($variants) > $newQty) {
            // Potong array variant sesuai qty baru
            $variants = array_slice($variants, 0, $newQty);

            session()->put("variant_session.$productId", $variants);
        }
    }
}
