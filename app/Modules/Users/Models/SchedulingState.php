<?php

namespace App\Modules\Users\Models;

use App\Model;

class SchedulingState extends Model
{
    protected $table = "scheduling_states";

    protected $fillable = 
    [
        'task',
        'first_date',
        'first_month',
        'second_date',
        'second_month',
        'past_day'
    ];
}