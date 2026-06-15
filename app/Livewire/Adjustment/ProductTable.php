<?php

namespace App\Livewire\Adjustment;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Product\Entities\Product;

class ProductTable extends Component
{
    protected $listeners = ['productSelected'];

    public $products;
    public $hasAdjustments;

    public function mount($adjustedProducts = null)
    {
        $this->products = [];

        if ($adjustedProducts) {
            $this->hasAdjustments = true;
            $this->products = $adjustedProducts;
        } else {
            $this->hasAdjustments = false;
        }
    }

    public function render()
    {
        return view('livewire.adjustment.product-table');
    }

    public function productSelected($product)
    {
        // 🎯 PERBAIKAN: Cek duplikasi disederhanakan agar akurat membaca ID produk
        $productId = $product['id'] ?? null;

        foreach ($this->products as $currentProduct) {
            $currentId = $currentProduct['product_id'] ?? $currentProduct['product']['id'] ?? $currentProduct['id'] ?? null;
            if ($currentId == $productId) {
                return session()->flash('message', 'Already exists in the product list!');
            }
        }

        // 🎯 PERBAIKAN: Bungkus data produk baru agar strukturnya konsisten dengan halaman edit
        array_push($this->products, [
            'product_id'        => $product['id'],
            'quantity'          => 1,
            'type'              => 'add',
            'product_name'      => $product['product_name'],
            'product_code'      => $product['product_code'],
            'product_quantity'  => $product['product_quantity'],
            'product_unit'      => $product['product_unit'] ?? '',
            // Sediakan fallback array product kosong agar blade tidak memicu 'Undefined array key'
            'product'           => $product
        ]);
    }

    public function removeProduct($key)
    {
        unset($this->products[$key]);
        // Reset index array agar urutan key + 1 di blade tidak melompat setelah dihapus
        $this->products = array_values($this->products);
    }
}
