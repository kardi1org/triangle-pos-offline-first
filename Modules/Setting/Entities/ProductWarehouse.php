<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    // Nama tabel biasanya jamak atau sesuai konvensi anda
    protected $table = 'product_warehouse';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(\Modules\Product\Entities\Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
