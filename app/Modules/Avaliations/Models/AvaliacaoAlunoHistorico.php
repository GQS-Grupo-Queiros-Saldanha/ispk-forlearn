<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class AvaliacaoAlunoHistorico extends Model {

    protected $fillable = [
        'plano_estudo_avaliacaos_id',
        'avaliacaos_id',
        'user_id',
        'nota_final',
        'created_by',
        'updated_by',
        'deleted_by',
        'class_id'
    ];

    protected $table = 'avaliacao_aluno_historicos';

}
