<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $connection = 'tenant';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
