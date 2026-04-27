<?php

namespace Modules\InventoryMovement\Entities;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(InventoryMovementDetail::class);
    }

    public function fromWarehouse()
    {
        // Sesuaikan dengan letak Model Warehouse Anda
        return $this->belongsTo(\Modules\Setting\Entities\Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(\Modules\Setting\Entities\Warehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
