<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;

    /**
     * Memaksa model ini menggunakan koneksi pusat (mysql)
     * Pastikan di config/database.php, koneksi 'mysql' mengarah ke db_pos utama.
     */
    protected $connection = 'mysql';

    /**
     * Nama tabel di database pusat
     */
    protected $table = 'outlets';

    protected $fillable = ['name', 'email', 'address'];

    /**
     * Relasi ke User (Many-to-Many)
     * Kita paksa tabel pivot 'outlet_user' dibaca dari koneksi pusat (mysql).
     */
    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'mysql.outlet_user', // Gunakan prefix 'mysql.' agar tidak mencari di DB Tenant
            'outlet_id',
            'user_id'
        );
    }
}
