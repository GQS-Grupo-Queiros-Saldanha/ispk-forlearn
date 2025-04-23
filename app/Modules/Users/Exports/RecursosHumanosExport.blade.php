<?php

namespace App\Modules\Users\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Para adicionar cabeçalhos ao Excel
use DB;
use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Enum\CodevEnum;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use stdClass;
use App\Modules\Users\Models\User;
class RecursosHumanosExport implements FromCollection, WithHeadings
{

    /**
     * Retorna a coleção de dados que será exportada.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
       try{

      
        $model = User::whereDoesntHave('roles', function($q) {
            $q->whereIn('id', [15, 6]); 
        })
        ->whereNotIn('users.id',[23,24])
        ->with(['disciplines' => function($q) {
                $q->with('currentTranslation');
            }])
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
              ->where('full_name.parameters_id', ParameterEnum::NOME);
          })
          ->leftJoin('user_parameters as bi', function ($join) {
            $join->on('users.id', '=', 'bi.users_id')
              ->where('bi.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
          })
                ->leftJoin('user_parameters as sexo', function ($join) {
                        $join->on('users.id', '=', 'sexo.users_id')
                          ->where('sexo.parameters_id', ParameterEnum::SEXO);
                      })
                ->leftJoin('parameter_option_translations as sexo_value', function ($join) {
                        $join->on('sexo_value.parameter_options_id', '=', 'sexo.value');
                    })
                ->leftJoin('user_parameters as nascimento', function ($join) {
                        $join->on('users.id', '=', 'nascimento.users_id')
                          ->where('nascimento.parameters_id', ParameterEnum::DATA_DE_NASCIMENTO);
                      })
                ->join('user_courses as uc','uc.users_id','users.id')
                ->join('courses as c','c.id','uc.courses_id')
                ->whereNull('c.deleted_at')
                ->whereNull('c.deleted_by')
                ->join('departments as dep','c.departments_id','dep.id')
                ->join('department_translations as dt','dt.departments_id','dep.id')
                ->where('dt.active',1)
                ->whereNull('dep.deleted_at')
                ->whereNull('dep.deleted_by')
               
                ->leftJoin('user_parameters as province', function ($join) {
                    $join->on('users.id', '=', 'province.users_id')
                      ->where('province.parameters_id', ParameterEnum::LISTA_DE_PROVINCIAS);
                  })
                ->leftJoin('parameter_option_translations as province_value', function ($join) {
                    $join->on('province_value.parameter_options_id', '=', 'province.value');
                })
                ->leftJoin('user_parameters as municip', function ($join) {
                    $join->on('users.id', '=', 'municip.users_id')
                      ->whereIn('municip.parameters_id', [69,71,151,152,153,155,156,157,159,170,182,204,205,206,218,225,282,224]);
                  })
                 
                ->leftJoin('parameter_option_translations as municip_value', function ($join) {
                    $join->on('municip_value.parameter_options_id', '=', 'municip.value');
                })
                ->leftJoin('user_parameters as grau', function ($join) {
                    $join->on('users.id', '=', 'grau.users_id')
                      ->where('grau.parameters_id', ParameterEnum::GRAU_ACADEMICO);
                  })
                ->leftJoin('grau_academico as ga','ga.id','grau.value')
                ->leftJoin('user_parameters as cargo', function ($join) {
                    $join->on('users.id', '=', 'cargo.users_id')
                      ->where('cargo.parameters_id', ParameterEnum::CARGO_PRINCIPAL);
                  })
                ->leftJoin('role_translations as rt','rt.role_id','cargo.value')
                ->leftJoin('user_parameters as cp', function ($join) {
                    $join->on('users.id', '=', 'cp.users_id')
                      ->where('cp.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL);
                  })
                ->leftJoin('categoria_profissional as prof','prof.id','cp.value')
                ->leftJoin('user_parameters as curso', function ($join) {
                    $join->on('users.id', '=', 'curso.users_id')
                      ->where('cp.parameters_id', ParameterEnum::LICENCIATURA_NO_CURSO);
                  })
                 
                ->select([
                    'users.id as id',
                    'full_name.value as full_name',
                    'nascimento.value as nascimento',
                    'dt.display_name as department',
                    'sexo_value.display_name as sexo',
                    'province_value.display_name as province',
                    'municip_value.display_name as municip',
                    'bi.value as bi',
                    'ga.nome as ga',
                    'prof.nome as cp',
                    'rt.display_name as cargo',
                    'curso.value as course'
                    
                ])
              
                ->get()
                ->unique('id');
               
                $rowIndex = 0;
                $new_model = collect();
                
                $model->each(function($candidate) use (&$rowIndex, $new_model) {
                    $new_object = new stdClass();
                
                    // Preencher o objeto com as propriedades
                    $new_object->rowIndex = ++$rowIndex;
                    $new_object->full_name = $candidate->full_name;
                    $new_object->bi = $candidate->bi;
                    $new_object->sexo = $candidate->sexo;
                    $new_object->idade = (int) Carbon::now()->year - (int) explode('-', $candidate->nascimento)[0];
                    $new_object->nascimento = $candidate->nascimento;
                    $new_object->province = $candidate->province;
                    $new_object->municip = $candidate->municip;
                    $new_object->origin = null;
                    $new_object->department = $candidate->department;
                    $new_object->contract = null;
                    $new_object->regime = null;
                    $new_object->cargo = $candidate->cargo;    
                    $new_object->cp = $candidate->cp;  
                    $new_object->ga = $candidate->ga;
                    $new_object->course = $candidate->course;
                
                    
                
           
                    if (is_null($candidate->disciplines) || $candidate->hasRole('coordenador-curso')) {
                        $disciplines = null;
                        $hours = null;
                    }
                    else{
                        $disciplines = $candidate->disciplines;
                        
                        $hours = $this->getTotalHours($disciplines->pluck('id')->toArray());
                   
                       $disciplines = $disciplines
                        ->map(function($item) {
                            return $item->currentTranslation->display_name;
                        })
                        ->implode(", ");
                    }
                    
                    
                
                    // Adicionar disciplinas e horas ao objeto
                    $new_object->disciplines = $disciplines;
                    $new_object->pub = null;
                    $new_object->qti = null;
                    $new_object->qto = null;
                    $new_object->hours = $hours;
                
                    // Adicionar o objeto à coleção
                    $new_model->push($new_object);
                });
               
               
            return $new_model;

       }
       catch(Exception $e){
        return $e;
       }
    }


/**
 * Função para obter a soma das horas das disciplinas
 */
private function getTotalHours($disciplines)
{
    return DB::table('study_plans_has_disciplines as spd')
        ->whereIn('disciplines_id', $disciplines)
        ->join('sp_has_discipline_regimes as sp', 'spd.id', '=', 'sp.sp_has_disciplines_id')
        ->sum('sp.hours');
}

    /**
     * Definir os cabeçalhos do arquivo exportado.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Número do Ordem',
            'Nome Completo',
            'Bilhete de Identidade',
            'Sexo',
            'Idade',
            'Data de Nascimento',
            'Província Residência',
            'Município de residência',
            'País de Origem',
            'Unidade Orgânica',
            'Tipo de Contrato Laboral',
            'Regime de Trabalho',
            'Cargo',
            'Carreira Docente Universitária -Investigação Cientifica',
            'Grau Acadêmico',
            'Curso de Formação',
            'Disciplinas que Lecciona',
            'Quantidade de Trabalho de Investigação',
            'Quantidade de Teses Orientadas',
            'Carga Horária'
  
        ];
    }

    

  
}
