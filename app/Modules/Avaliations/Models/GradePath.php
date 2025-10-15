<?php

namespace App\Modules\Avaliations\Models;

use Illuminate\Database\Eloquent\Model;

class GradePath extends Model
{
    protected $table = 'new_old_grades';
    protected $fillable = [
        'user_id',
        'discipline_id',
        'lective_year',
        'grade',
        'type',
        'tfc_trabalho',
        'tfc_defesa'
    ];
    protected $guarded = ['tipo_avaliacaos_id'];
}
