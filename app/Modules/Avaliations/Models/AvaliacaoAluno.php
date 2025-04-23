<?php

namespace App\Modules\Avaliations\Models;

use App\Modules\GA\Models\StudyPlan;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Model;

class AvaliacaoAluno extends Model
{
    protected $fillable = [
        'plano_estudo_avaliacaos_id',
        'metricas_id',
        'users_id',
        'nota',
        'presence',
        'id_turma',
        'created_by',
        'updated_by',
        'segunda_chamada',
        //VER SE FICA MESMO
        'avaliacao_estados_id'

    ];
    public function plano_estudo()
    {
        return $this->belongsTo(StudyPlan::class);
    }
    public function metrica()
    {
        return $this->belongsTo(Metrica::class);
    }
    public function aluno()
    {
        return $this->belongsTo(User::class);
    }
}
