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
class MatriculationExport implements FromCollection, WithHeadings
{
    private $ano_lectivo;

    function  __construct($ano_lectivo){
        $this->ano_lectivo = $ano_lectivo;
    }
    /**
     * Retorna a coleção de dados que será exportada.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
       
        $Prematricula=EmolumentCodevLective("p_matricula",$this->ano_lectivo)
        ->first()
        ->id_emolumento;

        $model = DB::table('matriculations as mt')
       ->whereNull('mt.deleted_by')
       ->whereNull('mt.deleted_at')
        ->where('mt.lective_year',$this->ano_lectivo)
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('mt.user_id', '=', 'full_name.users_id')
              ->where('full_name.parameters_id', ParameterEnum::NOME);
          })
          ->leftJoin('user_parameters as bi', function ($join) {
            $join->on('mt.user_id', '=', 'bi.users_id')
              ->where('bi.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
          })
                ->leftJoin('user_parameters as sexo', function ($join) {
                        $join->on('mt.user_id', '=', 'sexo.users_id')
                          ->where('sexo.parameters_id', ParameterEnum::SEXO);
                      })
                ->leftJoin('parameter_option_translations as sexo_value', function ($join) {
                        $join->on('sexo_value.parameter_options_id', '=', 'sexo.value');
                    })
                ->leftJoin('user_parameters as nascimento', function ($join) {
                        $join->on('mt.user_id', '=', 'nascimento.users_id')
                          ->where('nascimento.parameters_id', ParameterEnum::DATA_DE_NASCIMENTO);
                      })
                ->join('user_courses as uc','uc.users_id','mt.user_id')
                ->join('courses as c','c.id','uc.courses_id')
                ->whereNull('c.deleted_at')
                ->whereNull('c.deleted_by')
                ->join('courses_translations as ct','c.id','ct.courses_id')
                ->where('ct.active',1)
                ->join('departments as dep','c.departments_id','dep.id')
                ->join('department_translations as dt','dt.departments_id','dep.id')
                ->where('dt.active',1)
                ->whereNull('dep.deleted_at')
                ->whereNull('dep.deleted_by')
               
                ->leftJoin('user_parameters as province', function ($join) {
                    $join->on('mt.user_id', '=', 'province.users_id')
                      ->where('province.parameters_id', ParameterEnum::LISTA_DE_PROVINCIAS);
                  })
                ->leftJoin('parameter_option_translations as province_value', function ($join) {
                    $join->on('province_value.parameter_options_id', '=', 'province.value');
                })
                ->leftJoin('user_parameters as municip', function ($join) {
                    $join->on('mt.user_id', '=', 'municip.users_id')
                      ->whereIn('municip.parameters_id', [69,71,151,152,153,155,156,157,159,170,182,204,205,206,218,225,282,224]);
                  })
                 
                ->leftJoin('parameter_option_translations as municip_value', function ($join) {
                    $join->on('municip_value.parameter_options_id', '=', 'municip.value');
                })
                ->leftJoin('user_parameters as special', function ($join) {
                    $join->on('mt.user_id', '=', 'special.users_id')
                      ->where('special.parameters_id', ParameterEnum::NECESSIDADES_ESPECIAIS);
                  })
                ->join('matriculation_classes as ucl','mt.id','ucl.matriculation_id')
                ->join('classes as turma','turma.id','ucl.class_id')
               
                ->leftJoin('article_requests as ar', function ($join) use ($Prematricula) {
                    $join->on('ar.user_id', '=', 'mt.user_id')
                    ->where('ar.article_id', $Prematricula)
                      ->whereNull('ar.deleted_at')
                      ->whereNull('ar.deleted_by');
                  })
                  ->leftJoin('articles','articles.id', '=', 'ar.article_id')
             
                  ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('articles.anoLectivo', '=', 'lyt.lective_years_id')
                    ->where('lyt.active', 1);
                  })
                ->select([
                    'mt.user_id as id',
                    'full_name.value as full_name',
                    'nascimento.value as nascimento',
                    'ct.display_name as course',
                    'dt.display_name as department',
                    'sexo_value.display_name as sexo',
                    'province_value.display_name as province',
                    'municip_value.display_name as municip',
                    'bi.value as bi',
                    'turma.code as turma',
                    'special.value as special',
                    'mt.course_year as frequencia',
                     'lyt.display_name as pre_mat_year'
                    
                ])
              
                ->get()
                ->unique('id');

                
               
                $rowIndex = 0;
                $new_model = collect();
                $model = $model->each(function($candidate)use($rowIndex,$new_model){


                $new_object = new stdClass();

                $new_object->rowIndex = ++$rowIndex;
                $new_object->full_name = $candidate->full_name;
                $new_object->bi = $candidate->bi;
                $new_object->sexo = $candidate->sexo;
                $new_object->idade = $candidate->idade = (int) Carbon::now()->year - (int) explode('-',$candidate->nascimento)[0];
                $new_object->nascimento = $candidate->nascimento;
                $new_object->province =  $candidate->province;
               $new_object->municip = $candidate->municip;
               $new_object->origin = 'Angola';
             
               $turno = substr($candidate->turma,-1);
               $new_object->period = ($turno == 'M' || $turno == 'T')? 'Regular' : 'Noturno';
               $new_object->department = $candidate->department;
               $new_object->course = $candidate->course;
                $new_object->frequencia = $candidate->frequencia;
                $new_object->situation = null;
                $new_object->pre_mat_year = isset($candidate->pre_mat_yea) ? $candidate->pre_mat_yea : null;
               
                $new_object->special = isset($candidate->special) ? 'Sim' : 'Não';
              
                $new_object->nss = null;
                $new_object->level = 'Licenciatura';
                $new_object->anual = null;
                $new_model->push($new_object);

                });
               
               
            return $new_model;
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
            'Período de Estudo',
            'Unidade Orgânica',
            'Nome do Curso',
            'Ano de Frequência',
            'Situação Acadêmica',
            'Ano Primeiro Matrícula Ensino Superior',
            'Necessidade de Educação Especial',
            'Trabalhador',
            'Nível de Graduação',
            'Aproveitamento Anual'
            
            // Adicione outros campos que você deseja exportar
        ];
    }

  
}
