<?php

namespace Modules\ServiceCharge\Entities;

use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = ['name', 'percentage', 'calculation_type', 'is_active'];

    // Konstanta agar tidak typo saat memanggil logika di Controller
    const TYPE_GROSS = 1;
    const TYPE_NETTO = 2;

    public static function getTypes()
    {
        return [
            self::TYPE_GROSS => 'Total Makanan & Minuman (Sebelum Diskon)',
            self::TYPE_NETTO => 'Total Makanan & Minuman (Setelah Diskon)',
        ];
    }
}
