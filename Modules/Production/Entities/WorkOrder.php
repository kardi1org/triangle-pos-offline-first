<?php

namespace Modules\Production\Entities;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $guarded = [];

    public function details()
    {
        // Gunakan full namespace agar tidak terjadi error "Class not found"
        return $this->hasMany(\Modules\Production\Entities\WorkOrderDetail::class, 'work_order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\Modules\Setting\Entities\Warehouse::class);
    }
}
