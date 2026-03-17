<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderKitchenLog extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'reference',
        'product_name',
        'qty',
        'type', // 'new' atau 'void'
        'note',
        'user_id',
        'is_printed',
        'approved_by',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array
     */
    protected $casts = [
        'is_printed' => 'boolean',
        'qty' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke model Sale (Order utama).
     */
    // public function sale()
    // {
    //     return $this->belongsTo(Sale::class, 'sale_id', 'id');
    // }

    /**
     * Relasi ke model User (Siapa yang melakukan perubahan).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope untuk mengambil log yang belum dicetak.
     */
    public function scopeUnprinted($query)
    {
        return $query->where('is_printed', false);
    }

    /**
     * Scope untuk memfilter berdasarkan tipe (new/void).
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Relasi ke User (Admin yang menyetujui)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sale()
    {
        return $this->belongsTo(\Modules\Sale\Entities\Sale::class, 'sale_id', 'id');
    }

    // Relasi ke User yang meng-approve (Supervisor/Admin)
    public function approvedBy()
    {
        // Menghubungkan kolom approved_by ke id di tabel users
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
