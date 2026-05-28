<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder; // 🎯 Tambahkan ini untuk Global Scope
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id', 'id');
    }

    /**
     * Manajemen Event Boot Model
     */
    protected static function booted()
    {
        // 1. Global Scope: Otomatis menyaring data pengeluaran hanya untuk outlet yang dipilih
        static::addGlobalScope('outlet', function (Builder $builder) {
            if (session()->has('selected_outlet_id')) {
                $builder->where('outlet_id', session('selected_outlet_id'));
            }
        });

        // 2. Event Creating: Otomatis mengisi data saat penyimpanan baru dilakukan
        static::creating(function ($model) {
            // Generate Reference ID otomatis
            $number = Expense::max('id') + 1;
            $model->reference = make_reference_id('EXP', $number);

            // 🎯 Otomatis isi outlet_id dari session jika ada
            if (session()->has('selected_outlet_id')) {
                $model->outlet_id = session('selected_outlet_id');
            }
        });
    }

    // Mengganti fungsi boot lama ke booted() yang lebih baru di Laravel 10+
    public static function boot()
    {
        parent::boot();
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = ($value * 100);
    }

    public function getAmountAttribute($value)
    {
        return ($value / 100);
    }

    /**
     * Relasi ke Model Outlet (Opsional, jika ingin memanggil $expense->outlet)
     */
    public function outlet()
    {
        return $this->belongsTo(\Modules\User\Entities\Outlet::class, 'outlet_id');
    }
}
