<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_name',
    ];

    /**
     * Relasi ke produk
     */
    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class);
    }
}
