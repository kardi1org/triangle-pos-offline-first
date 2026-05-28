<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\SaleDetails;
use Modules\Order\Entities\OrderDetails;
use Gloudemans\Shoppingcart\Facades\Cart;

class SearchProduct extends Component
{
    public $query;
    public $sql;
    public $search_results;
    public $search_barcodes;
    public $how_many;
    public $product = '';
    public $barcode = '';
    public $cart_instance;
    public $quantity = [];
    public string $message = '';
    public $labels = [];
    public $error = '';
    public $cartItem;

    protected $rules = [
        'barcode' => 'required|min:3'
    ];

    public function mount($cartInstance = null)
    {
        $this->query = '';
        $this->sql = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
        $this->search_barcodes = Collection::empty(); //add by Chris
        $this->product = '';
        $this->barcode = '';
        $this->quantity = [];
        $this->cart_instance = $cartInstance;
    }

    public function render()
    {
        return view('livewire.search-product');
    }

    public function updatedQuery()
    {
        $this->search_results = Product::when($this->cart_instance === 'sale', function ($query) {
            // 🎯 Hanya kunci ke FG jika ini instance penjualan (sale)
            return $query->where('product_type', 'FG');
        })
            ->where(function ($query) {
                $query->where('product_name', 'like', '%' . $this->query . '%')
                    ->orWhere('product_code', 'like', '%' . $this->query . '%');
            })
            ->take($this->how_many)
            ->get();
    }

    public function loadMore()
    {
        $this->how_many += 5;
        $this->updatedQuery();
    }

    public function resetQuery()
    {
        $this->query = '';
        $this->sql = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
        $this->search_barcodes = Collection::empty();  //add by Chris
        $this->barcode = '';
    }

    public function selectProduct($product)
    {
        $this->dispatch('productSelected', $product);
    }


    public function addToCartZ()
    {

        if (empty($this->query)) {
            return;
        }

        $this->error = '';
        $quantity = 1;
        $product = Product::where('barcode', $this->query)->first();

        if (!$product) {
            $this->error = 'The ' . $this->query . ' ' . ' - Product which does not exist!';
            $this->query = '';
            return;
        }

        $cartItem = Cart::add(
            [
                'id' => $product->id,
                'name' => $product->product_name,
                'qty' => 1,
                'price' => $product->product_price,
                'weight'  => 1
            ]
        );

        $this->cartItem->save();

        $this->query = '';

        $this->dispatch('cartUpdated');
    }

    public function closeErrorModal()
    {
        $this->error = '';
    }


    public function searchProduct()
    {
        $this->validate();
        // Cari produk berdasarkan barcode (sesuaikan dengan model Anda)
        $this->barcode = Product::where('barcode', $this->query)->first();

        if ($this->barcode) {
            $this->addToCart();
        } else {
            session()->flash('Error', 'Barcode not found');
        }
    }

    public function addToCart()
    {
        $product = Product::where('barcode', $this->query)->first();

        if (!$this->product) {
            session()->flash('Error', 'Barcode not found');
            return;
        }

        $this->product = $product;
        Cart::add([
            'id' => $this->product->id,
            'name' => $this->product->product_name,
            'qty' => 1,
            'price' => $this->product->product_price,
            'weight'  => 1,
            'options' => [
                'product_discount'      => 0.00,
                'product_discount_type' => 'fixed',
                'sub_total'             => $this->calculate($product)['sub_total'],
                'code'                  => $product['product_code'],
                'stock'                 => $product['product_quantity'],
                'unit'                  => $product['product_unit'],
                'product_tax'           => $this->calculate($product)['product_tax'],
                'unit_price'            => $this->calculate($product)['unit_price']
            ]
        ]);
        // Reset input setelah berhasil
        $this->reset(['barcode', 'product']);
        $this->dispatch('cartUpdated'); // Memberi tahu komponen lain bahwa cart diupdate

    }

    public function searchPost()
    {
        //  $this->search_barcodes = Product::where('barcode', 'like', '%' . $this->query . '%')->get();
        //  $this->search_barcodes = Product::where('barcode', '=', $this->query)->get();
        //  $this->search_barcodes = Product::where('barcode', '=', $barcode)->get();
        //            ->take($this->how_many)->get();
        //   dd('OK');
    }



    public function addToCartX()
    {

        $cart_items = Cart::instance($this->cart_instance)->content() ?? [];
        $product = Product::where('barcode', $this->sql)->first();
        if (!$product) {
            $this->dispatch('product-not-found');
            return;
        }
        $cart_items->add([
            'id' => $product['barcode'],
            'name' => $product['product_name'],
            'qty' => 1,
            'price' => $product['product_price'],
            'weight'  => 1,
            'options' => [
                'sub_total'  => $product['product_price'],
                'code'       => $product['product_code'],
                'unit'       => $product['product_unit'],
                'unit_price' => $product['product_price']
            ]
        ]);
    }

    public function searchBarcode()
    {
        // 1. Reset state sebelumnya
        $this->product = null;
        $this->message = '';
        // 2. Validasi sederhana, pastikan barcode tidak kosong
        if (empty($this->barcode)) {
            $this->message = 'Silakan scan atau ketik barcode terlebih dahulu.';
            return;
        }
        // 3. Lakukan pencarian di database
        $foundProduct = Product::where('barcode', $this->barcode)->first();
        // 4. Proses hasil pencarian
        if ($foundProduct) {
            $this->product = $foundProduct;
        } else {
            $this->message = 'Produk dengan barcode "' . htmlspecialchars($this->barcode) . '" tidak ditemukan.';
        }
        // 5. Kosongkan input barcode agar siap untuk scan berikutnya
        $this->barcode = '';
    }

    public function selectFirstResult()
    {
        if ($this->search_barcodes->isNotEmpty()) {
            $firstProduct = $this->search_barcodes->first();
            $this->selectProduct($firstProduct);
            $this->resetQuery();
            return;
        }

        if ($this->search_results->isNotEmpty()) {
            $firstProduct = $this->search_results->first();
            $this->selectProduct($firstProduct);
            $this->resetQuery();
            return;
        }

        session()->flash('Error', 'No product found for the current query.');
    }
}
