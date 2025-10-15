<?php

namespace App\Modules\Avaliations\Models;

use App\Modules\GA\Models\StudyPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanoEstudoAvaliacao extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'study_plans_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'avaliacaos_id'
    ];
    //
    public function avaliacao()
    {
        return $this->belongsTo(Avaliacao::class);
    }

    public function study_plan()
    {
        return $this->belongsTo(StudyPlan::class);
    }

}
