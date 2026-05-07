<?php

namespace Modules\Production\Entities;

use Illuminate\Database\Eloquent\Model;

class WorkOrderDetail extends Model
{
    protected $guarded = [];

    // Relasi ke produk (Bahan Baku)
    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class, 'product_id', 'id');
    }

    // Relasi balik ke header Work Order
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
