<?php

namespace Modules\Meja\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meja extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_meja',
        'name',
        'qty_pax',
        'location',
        'shape',
        'status',
    ];
}
