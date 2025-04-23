<?php

namespace App\Modules\Users\Models;


use Illuminate\Database\Eloquent\Model;

class TeacherClass extends Model
{
    protected $table = "teacher_classes";
    protected $fillable = [
        'user_id', 'class_id', 'lective_year'
    ];
}
