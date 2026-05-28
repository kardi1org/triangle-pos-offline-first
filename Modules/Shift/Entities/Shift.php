<?php

namespace Modules\Shift\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Shift extends Model
{
    protected $fillable = [
        'user_id', 'open_time', 'close_time', 'starting_cash',
        'ending_cash', 'expected_ending_cash', 'note', 'status', 'terminal_id',
        'session_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
