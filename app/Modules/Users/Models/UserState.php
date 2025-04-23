<?php

namespace App\Modules\Users\Models;

use App\Model;

class UserState extends Model
{
    protected $table = "users_states";

    protected $fillable = 
    [
        'user_id',
        'state_id',
        'courses_id',
        'created_by',
        'updated_by',
        'occured_at',
        'created_at',
        'updated_at'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}