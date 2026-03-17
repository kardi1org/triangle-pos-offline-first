<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = Sale::max('id') + 1;
            $model->reference = make_reference_id('SL', $number);
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

    public function getServiceChargeAttribute($value)
    {
        return $value / 100;
    }
    public function getLainAAttribute($value)
    {
        return $value / 100;
    }
    public function getLainBAttribute($value)
    {
        return $value / 100;
    }

    // Di dalam Model Sale.php

    public function meja()
    {
        // Ubah 'tabel_id' menjadi 'table_id' (sesuai dengan kolom yang digunakan di Livewire)
        return $this->belongsTo(\Modules\Meja\Entities\Meja::class, 'table_id', 'id');
    }

    // Modules/Sale/Entities/Sale.php

    public function kitchenLogs()
    {
        // Pastikan namespace model log kitchen Anda benar.
        // Jika berada di App\Models gunakan:
        return $this->hasMany(\App\Models\OrderKitchenLog::class, 'sale_id', 'id');

        // Atau jika OrderKitchenLog juga berada di dalam Module Sale, sesuaikan namespacenya:
        // return $this->hasMany(\Modules\Sale\Entities\OrderKitchenLog::class, 'sale_id', 'id');
    }

    protected $casts = [
        // ... casting untuk kolom lain ...
        'selected_table_ids' => 'array', // 🎯 SOLUSI UTAMA: Menginstruksikan Laravel untuk mengkonversi array ke JSON saat menyimpan dan sebaliknya.
    ];
}
