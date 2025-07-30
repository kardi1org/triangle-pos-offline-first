<?php

namespace Modules\Budget\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;
    /**
     * fillable
     *
     * @var array
     */
    // protected $fillable = [
    //     'amount',
    //     'date',
    //     'details',
    // ];
    protected $table = 'budgets';
    protected $guarded = [];
}
