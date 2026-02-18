<?php

namespace Modules\FeatureManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeatureManagement extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'feature_managements';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'feature_group',
        'feature_name',
        'feature_key',
        'package_1',
        'package_2',
        'package_3',
    ];

    // Cast kolom package menjadi boolean agar lebih mudah digunakan di View
    // protected $casts = [
    //     'package_1' => 'boolean',
    //     'package_2' => 'boolean',
    //     'package_3' => 'boolean',
    // ];
}
