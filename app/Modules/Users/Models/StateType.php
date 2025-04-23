<?php
namespace App\Modules\Users\Models;

use App\Model;

class StateType extends Model
{
    protected $table = "states_type";

    protected $fillable = 
    [
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}