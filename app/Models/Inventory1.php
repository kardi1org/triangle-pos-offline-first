<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $guarded = [];

    // public function InventoryDetails()
    // {
    //     return $this->hasMany(InventoryDetail::class, 'Inventory_id', 'id');
    // }

    // public function InventoryPayments()
    // {
    //     return $this->hasMany(InventoryPayment::class, 'Inventory_id', 'id');
    // }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Inventory::max('id') + 1;
            $model->reference = make_reference_id('PR', $number);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDueAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
}
