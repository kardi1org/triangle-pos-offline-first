<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    // PAKSA ke koneksi pusat (db_pos utama)
    protected $connection = 'mysql';

    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'valid_date',
        'tenant_database', 'tenant_host', 'tenant_port',
        'tenant_username', 'tenant_password', 'codepaket', 'level'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['media'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackUrl('https://www.gravatar.com/avatar/' . md5($this->email));
    }

    public function scopeIsActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    /**
     * Relasi ke Outlets
     * Menggunakan mysql.outlet_user agar tidak mencari di DB tenant
     */
    public function outlets()
    {
        return $this->belongsToMany(
            \Modules\User\Entities\Outlet::class,
            'outlet_user',
            'user_id',
            'outlet_id'
        );
    }

    /**
     * Helper untuk cek akses outlet
     */
    public function hasAccessToOutlet($outletId)
    {
        return $this->outlets()->where('mysql.outlets.id', $outletId)->exists();
    }

    public function sales()
    {
        // Mengarahkan ke namespace module yang benar
        return $this->hasMany(\Modules\Sale\Entities\Sale::class, 'user_id');
    }
}
