<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class OrderDetails extends Model
{
    use HasFactory;

  //  protected $guarded = [];
    protected $fillable = ['id', 'order_id','reference','product_id','product_name','product_code','quantity','price','unit_price','sub_total'];
    protected $table = 'order_details';
    protected $with = ['product'];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /* public function order()
    {
        return $this->belongsTo('App\Modules\Order\Entities\Order', 'order_id', 'id');
    } */

    /* public function getPriceAttribute($value) {
        return $value / 100;
    }

    public function getUnitPriceAttribute($value) {
        return $value / 100;
    }

    public function getSubTotalAttribute($value) {
        return $value / 100;
    }

    public function getProductDiscountAmountAttribute($value) {
        return $value / 100;
    }

    public function getProductTaxAmountAttribute($value) {
        return $value / 100;
    } */
}
