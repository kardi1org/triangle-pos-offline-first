<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class SaleDetails extends Model
{
    use HasFactory;

    // protected $guarded = [];
    protected $fillable = ['id', 'sale_id', 'reference', 'product_id', 'product_name', 'product_code', 'quantity', 'price', 'unit_price', 'sub_total', 'variant_detail', 'recipe_snapshot'];
    protected $table = 'sale_details';
    protected $with = ['product'];

    // 🎯 CASTING OTOMATIS KE ARRAY PHP
    protected $casts = [
        'recipe_snapshot' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getUnitPriceAttribute($value)
    {
        return $value / 100;
    }

    public function getSubTotalAttribute($value)
    {
        return $value / 100;
    }

    public function getProductDiscountAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getProductTaxAmountAttribute($value)
    {
        return $value / 100;
    }
}
