<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAvaliacao extends Model
{
    protected $fillable = [
        'nome',
        'codigo',
        'abreviatura',
        'anoLectivo',
        'descricao',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    public function avaliacao()
    {
        return $this->hasMany(Avaliacao::class);
    }
}
