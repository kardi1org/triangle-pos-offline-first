<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class Recipe extends Model
{
    protected $fillable = ['product_id', 'quantity', 'unit'];

    // Relasi ke Produk Hasil
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // Relasi ke Detail Bahan Baku
    public function details()
    {
        return $this->hasMany(RecipeDetail::class);
    }
}
