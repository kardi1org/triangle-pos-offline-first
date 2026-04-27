<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'outlet_id',
        'is_active'
    ];

    /**
     * Relasi ke Mutasi (Sebagai Gudang Asal)
     */
    public function movementsOut()
    {
        return $this->hasMany(\Modules\InventoryMovement\Entities\InventoryMovement::class, 'from_warehouse_id');
    }

    /**
     * Relasi ke Mutasi (Sebagai Gudang Tujuan)
     */
    public function movementsIn()
    {
        return $this->hasMany(\Modules\InventoryMovement\Entities\InventoryMovement::class, 'to_warehouse_id');
    }

    protected static function newFactory()
    {
        return \Modules\Setting\Database\factories\WarehouseFactory::new();
    }
}
