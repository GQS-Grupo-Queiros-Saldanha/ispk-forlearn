<?php

namespace App\Modules\Avaliations\Models;

use App\Modules\GA\Models\StudyPlan;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;

class Avaliacao extends Model
{
    protected $fillable = [
        'nome',
        'lock',
        'tipo_avaliacaos_id',
        'percentage',
        'anoLectivo',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'deleted_by'
    ];
    //
    public function tipo_avaliacoes()
    {
        return $this->belongsTo(TipoAvaliacao::class);
    }

    public function metrica()
    {
        return $this->hasMany(Metrica::class);
    }

    public function plano_estudo_avaliacao()
    {
        return $this->hasMany(StudyPlan::class);
    }
}
