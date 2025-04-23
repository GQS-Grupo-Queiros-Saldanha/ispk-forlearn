<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class Metrica extends Model
{

    protected $fillable = [
        'nome',
        'percentagem',
        'created_by',
        'updated_by',
        'deleted_by',
        'avaliacaos_id',
        'tipo_metricas_id'
    ];

    public function avaliacao()
    {
        return $this->belongsTo(Avaliacao::class);
    }

    public function tipo_metrica()
    {
        return $this->belongsTo(TipoMetrica::class);
    }
}
