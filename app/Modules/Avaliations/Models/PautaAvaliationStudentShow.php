<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

// use App\Modules\GA\Models\StudyPlan;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PautaAvaliationStudentShow extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'quantidade_mes',
        'quatidade_day',
        'lective_year_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
    ];
    //
    // public function avaliacao()
    // {
    //     return $this->belongsTo(Avaliacao::class);
    // }

    // public function study_plan()
    // {
    //     return $this->belongsTo(StudyPlan::class);
    // }

}