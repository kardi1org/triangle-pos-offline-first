<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class RecipeDetail extends Model
{
    protected $fillable = ['recipe_id', 'product_id', 'quantity', 'unit', 'cost'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
