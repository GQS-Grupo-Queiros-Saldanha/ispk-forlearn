<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CalendarioProvaHorario extends Model
{
    protected $fillable = [
        'id',
        'calendario_prova_id',
        'turma_id',
        'periodo',
        'disciplina_id',
        'data_prova_marcada',
        'hora_comeco',
        'hora_termino',
        'created_at',
        'updated_at',
        'deleted_at',        
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    
    protected $table = "calendario_horario";

    public function calendario_horario_juris()
    {
        return $this->hasMany(CalendarioProvaHorarioJuris::class,"calendario_horario_id","id");
    }
    
}