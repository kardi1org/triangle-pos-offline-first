<?php

namespace Modules\InventoryMovement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMovementDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel jika tidak mengikuti konvensi jamak (plural).
     * Biasanya nwidart mengikuti konvensi inventory_movement_details.
     */
    protected $table = 'inventory_movement_details';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'inventory_movement_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relasi balik ke header Mutasi (InventoryMovement).
     */
    public function inventoryMovement()
    {
        return $this->belongsTo(InventoryMovement::class, 'inventory_movement_id');
    }

    /**
     * Relasi ke produk yang dimutasi.
     * Pastikan namespace Product sesuai dengan struktur module Anda.
     */
    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class, 'product_id');
    }

    /**
     * Helper untuk menghitung nilai stok (Opsional).
     * Jika Anda menyimpan harga beli/pokok saat mutasi dilakukan.
     */
    public function getSubTotalAttribute()
    {
        return $this->quantity * ($this->product->product_cost ?? 0);
    }
}
