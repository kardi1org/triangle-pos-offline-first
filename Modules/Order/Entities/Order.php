<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Support\Facades\DB;
//use Exception;

class Order extends Model
{
    use HasFactory;

//    protected $guarded = [];
    protected $fillable = ['date','reference','customer_name','total_amount','paid_amount'];
    protected $table = 'orders';

    public function orderDetails() {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    /* public function orderPayments() {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    } */

    /* public static function boot() {
        parent::boot(); 

        static::creating(function ($model) {
         try {    
          //  $number = Order::max('id') + 1;
            $number = Order::max('reference') + 1;
          //  $number = $this->generateOrderNumber();
            $model->reference = make_reference_id('', $number);
          //  $model->reference = Order::generateOrderNumber();
         } catch (Exception $err) {
             return "Gagal membentuk No. Order : " . $err->getMessage(). "\n";
         }
      });
    } */

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }

    /* public function getShippingAmountAttribute($value) {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value) {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value) {
        return $value / 100;
    }

    public function getDueAmountAttribute($value) {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value) {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value) {
        return $value / 100;
    } */
}
