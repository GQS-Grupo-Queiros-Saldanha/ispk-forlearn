<?php

namespace App\Modules\Avaliations\Models;

use App\Modules\GA\Models\LectiveYear;
use Illuminate\Database\Eloquent\Model;

class AvaliacaoConfig extends Model
{
    protected $table = "avalicao_config";
    
    protected $fillable = [
        'id',
        'lective_year',
        'strategy',
        'mac_nota_recurso',
        'exame_nota_inicial',
        'exame_nota_final',
        'exame_nota',
        'mac_nota_dispensa',
        'percentagem_mac',
        'percentagem_oral',
        'exame_oral_final'
    ];
    
    public function lectiveYear()
    {
        return $this->belongsTo(LectiveYear::class, 'lective_year');
    }
    
}