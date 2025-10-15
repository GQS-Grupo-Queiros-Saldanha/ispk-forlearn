<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioProvaHorarioJuris extends Model
{
    protected $fillable = [
        'id',
        'calendario_horario_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',        
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    
    protected $table = "calendario_horario_juris";

    public function calendario_horario(){
        return $this->belongsTo(CalendarioProvaHorario::class,"calendario_horario_id","id");
    }
    
}