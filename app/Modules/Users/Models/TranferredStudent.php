<?php

namespace App\Modules\Users\Models;

use App\Model;

class TranferredStudent extends Model
{
    protected $table = "transferred_students";

    protected $fillable = 
    [
        'user_id',
        'home_institution'
    ];
}