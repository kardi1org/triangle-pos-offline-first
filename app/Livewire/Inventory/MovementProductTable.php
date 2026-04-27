<?php

namespace App\Livewire\Inventory;

use Livewire\Component;

class MovementProductTable extends Component
{
    protected $listeners = ['productSelected'];
    public $products = [];

    public function mount($movementProducts = null)
    {
        if ($movementProducts) {
            $this->products = $movementProducts;
        } else {
            $this->products = [];
        }
    }

    public function render()
    {
        return view('livewire.inventory.movement-product-table');
    }

    public function productSelected($product)
    {
        foreach ($this->products as $item) {
            if (($item['id'] ?? null) == $product['id']) {
                return session()->flash('message', 'Product already added!');
            }
        }

        // Pastikan kita menambahkan key 'quantity' agar wire:model punya target
        $product['quantity'] = 1;

        array_push($this->products, $product);
    }

    public function removeProduct($key)
    {
        unset($this->products[$key]);
        $this->products = array_values($this->products); // Reset keys
    }
}
