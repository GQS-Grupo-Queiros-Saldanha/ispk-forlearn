<?php

namespace App\Modules\Users\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class State extends Model
{
    protected $table = "states";

    protected $fillable = 
    [
        'initials',
        'name',
        'states_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function type()
    {
        return $this->BelongsTo(StateType::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(UserState::class);
    }
}