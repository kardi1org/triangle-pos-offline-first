<?php

namespace Modules\MethodePay\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class MethodePay extends Model
{
    use HasFactory;

    protected $fillable = ['id','code','methode_name'];
    protected $table = 'methode_payment';
    protected $guarded = [];

    
}
