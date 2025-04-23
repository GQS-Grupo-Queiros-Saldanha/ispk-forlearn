<?php

namespace App\Modules\Lessons\Models;

use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\Summary;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class Lessons extends Model
{
    protected $table = 'lessons';

    protected $casts = [
        'occured_at' => 'datetime',
    ];

    protected $fillable = [
        'teacher_id',
        'discipline_id',
        'class_id',
        'regime_id',
        'summary_id',
        'occured_at',
        'observations',
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'lesson_attendance', 'lesson_id', 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id', 'id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    public function regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'regime_id', 'id');
    }

    public function summary()
    {
        return $this->belongsTo(Summary::class, 'summary_id', 'id');
    }
}
