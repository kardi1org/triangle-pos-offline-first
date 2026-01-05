<?php

namespace Modules\Shift\Entities;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'category', 'note', 'transaction_date'];
}
