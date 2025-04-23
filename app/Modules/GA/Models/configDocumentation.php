<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

class ConfigDocumentation extends Model
{
    
    
    protected $table = 'config_documentation';
    
    protected $fillable = [
        'user_id',
        'document_type',
        'cabecalho',
        'titulo_position',
        'tamanho_fonte',
        'marca_agua',
        'rodape',
        'file'
     
    ];

   
  
}
