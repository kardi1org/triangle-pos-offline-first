<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderSummarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature_key',
        'feature_name',
        'formula_description',
        'tax_position',
        'default_value',
        'is_active'
    ];

    // Helper untuk mempermudah pemanggilan di POS
    public static function getValue($key)
    {
        $setting = self::where('feature_key', $key)->where('is_active', true)->first();
        return $setting ? (float) $setting->default_value : 0;
    }
}
