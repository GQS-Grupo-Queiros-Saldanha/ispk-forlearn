<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMetrica extends Model
{
    protected $fillable = [
        'nome',
        'codigo',
        'abreviatura',
        'descricao',
        'anoLectivo',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
