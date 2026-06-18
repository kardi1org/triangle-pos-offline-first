<?php

namespace Modules\Meja\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Meja extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $fillable = [
    //     'no_meja',
    //     'name',
    //     'qty_pax',
    //     'location',
    //     'shape',
    //     'status',
    // ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // 1. Global Scope: Otomatis menyaring meja hanya untuk outlet yang sedang aktif/dipilih
        static::addGlobalScope('outlet', function (Builder $builder) {
            if (session()->has('selected_outlet_id')) {
                $builder->where('outlet_id', session('selected_outlet_id'));
            }
        });

        // 2. Model Event: Otomatis mengisi kolom outlet_id sebelum data meja disimpan ke database
        static::creating(function ($meja) {
            if (session()->has('selected_outlet_id')) {
                $meja->outlet_id = session('selected_outlet_id');
            }
        });
    }

    // Hubungan relasi ke model Outlet (Opsional, jika dibutuhkan)
    // Hubungan relasi ke model Outlet yang berada di modul User
    public function outlet()
    {
        return $this->belongsTo(\Modules\User\Entities\Outlet::class, 'outlet_id', 'id');
    }
}
