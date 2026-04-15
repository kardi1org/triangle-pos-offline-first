<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class CashTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['id','date','reference','receive_type_transferfrom','amount_transferfrom',
    'amount_change','receive_type_transferto','amount_transferto','details','trans_type'];
    protected $table = 'cash_transfers';
    //protected $guarded = [];

}
