<?php
namespace App\Modules\Users\Models;

use App\Model;

class UserStateHistoric extends Model
{
    protected $table = "users_states_historic";
    protected $fillable = [
        'user_id',
        'state_id',
        'occurred_at'
    ];
}
