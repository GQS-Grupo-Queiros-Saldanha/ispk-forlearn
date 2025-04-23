<?php

namespace App\Modules\Users\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Para adicionar cabeçalhos ao Excel
use DB;
use App\Modules\Users\Enum\ParameterEnum;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use stdClass;
class CandidatesExport implements FromCollection, WithHeadings
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
       
        $model = DB::table('user_candidate as uca')
       
        ->where('uca.year',$this->ano_lectivo)
        ->leftJoin('user_parameters as full_name', function ($join) {
            $join->on('uca.user_id', '=', 'full_name.users_id')
              ->where('full_name.parameters_id', ParameterEnum::NOME);
          })
          ->leftJoin('user_parameters as bi', function ($join) {
            $join->on('uca.user_id', '=', 'bi.users_id')
              ->where('bi.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
          })
                ->leftJoin('user_parameters as sexo', function ($join) {
                        $join->on('uca.user_id', '=', 'sexo.users_id')
                          ->where('sexo.parameters_id', ParameterEnum::SEXO);
                      })
                ->leftJoin('parameter_option_translations as sexo_value', function ($join) {
                        $join->on('sexo_value.parameter_options_id', '=', 'sexo.value');
                    })
                ->leftJoin('user_parameters as nascimento', function ($join) {
                        $join->on('uca.user_id', '=', 'nascimento.users_id')
                          ->where('nascimento.parameters_id', ParameterEnum::DATA_DE_NASCIMENTO);
                      })
                ->join('user_courses as uc','uc.users_id','uca.user_id')
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
                ->leftJoin('user_parameters as medio_school', function ($join) {
                    $join->on('uca.user_id', '=', 'medio_school.users_id')
                      ->where('medio_school.parameters_id', ParameterEnum::NOME_DA_ESCOLA_DO_ENSINO_MEDIO);
                  })
                ->leftJoin('user_parameters as province', function ($join) {
                    $join->on('uca.user_id', '=', 'province.users_id')
                      ->where('province.parameters_id', ParameterEnum::LISTA_DE_PROVINCIAS);
                  })
                ->leftJoin('parameter_option_translations as province_value', function ($join) {
                    $join->on('province_value.parameter_options_id', '=', 'province.value');
                })
                ->leftJoin('user_parameters as municip', function ($join) {
                    $join->on('uca.user_id', '=', 'municip.users_id')
                      ->whereIn('municip.parameters_id', [69,71,151,152,153,155,156,157,159,170,182,204,205,206,218,225,282,224]);
                  })
                 
                ->leftJoin('parameter_option_translations as municip_value', function ($join) {
                    $join->on('municip_value.parameter_options_id', '=', 'municip.value');
                })
                ->leftJoin('user_parameters as medio_course', function ($join) {
                    $join->on('uca.user_id', '=', 'medio_course.users_id')
                      ->whereIn('medio_course.parameters_id', [140,141,143]);
                  })
                ->leftJoin('parameter_option_translations as medio_course_value', function ($join) {
                    $join->on('medio_course_value.parameter_options_id', '=', 'medio_course.value');
                })
              
                ->join('user_classes as ucl','uca.user_id','ucl.user_id')
                ->join('classes as turma','turma.id','ucl.class_id')
                ->leftJoin('user_parameters as special', function ($join) {
                    $join->on('uca.user_id', '=', 'special.users_id')
                      ->where('special.parameters_id', ParameterEnum::NECESSIDADES_ESPECIAIS);
                  })
                  ->leftJoin('user_parameters as medio_type', function ($join) {
                    $join->on('uca.user_id', '=', 'medio_type.users_id')
                      ->where('medio_type.parameters_id',  ParameterEnum::TIPO_ESCOLA_ENSINO_MEDIO);
                  })
                ->leftJoin('parameter_option_translations as medio_type_value', function ($join) {
                    $join->on('medio_type_value.parameter_options_id', '=', 'medio_type.value');
                })
                ->leftJoin('user_parameters as medio_grade', function ($join) {
                    $join->on('uca.user_id', '=', 'medio_grade.users_id')
                      ->where('medio_grade.parameters_id',  ParameterEnum::NOTA_ENSINO_MEDIO);
                  })
                ->select([
                    'uca.user_id as id',
                    'full_name.value as full_name',
                    'nascimento.value as nascimento',
                    'ct.display_name as course',
                    'dt.display_name as department',
                    'sexo_value.display_name as sexo',
                    'medio_school.value as medio_school',
                    'province_value.display_name as province',
                    'municip_value.display_name as municip',
                    'medio_course_value.display_name as medio_course',
                    'bi.value as bi',
                    'turma.code as turma',
                    'special.value as special',
                    'medio_type_value.display_name as medio_type',
                    'medio_grade.value as medio_grade'
                ])
                ->distinct('uca.user_id')
                ->orderBy('c.id')
                ->get()
                ->unique('id');
        
                $rowIndex = 0;
                $new_model = collect();
                
                $model = $model->each(function($candidate,$key)use($rowIndex,$new_model){

                   
                    $grades = DB::table('grades as g')
                ->where('student_id', $candidate->id)
                ->join('disciplines as d', 'g.discipline_id','=','d.id')
                ->where('d.discipline_profiles_id', 8)
                ->select(['g.value as nota','d.percentage as percentagem'])
                ->get();

                if(isset($grades[0]->nota,$grades[1]->nota)){
                    $resultado = round( ( $grades[0]->nota * ($grades[0]->percentagem / 100) ) + ( $grades[1]->nota * ($grades[1]->percentagem / 100) )  );
                }else if(isset($grades[0]->nota) && !isset($grades[1]->nota)){
                    $resultado = $grades[0]->nota;
                }else{
                    $resultado = round ( ( ($grades[0]->nota ?? 0) + ($grades[1]->nota ?? 0) ) / 2);
                }

                $candidate->nota = $resultado;

                $candidate->rowIndex = ++$rowIndex;

                $new_object = new stdClass();

                $new_object->rowIndex = $candidate->rowIndex;
                $new_object->full_name = $candidate->full_name;
                $new_object->bi = $candidate->bi;
                $new_object->sexo = $candidate->sexo;
                $new_object->idade = $candidate->idade = (int) Carbon::now()->year - (int) explode('-',$candidate->nascimento)[0];
                $new_object->nascimento = $candidate->nascimento;
                $new_object->province =  $candidate->province;
               $new_object->municip = $candidate->municip;
               $new_object->origin = 'Angola';
             
               $turno = substr($candidate->turma,-1);
               $new_object->period = ($turno == 'M' || $turno == 'T')? 'Regular' : 'Pós-Laboral';
               $new_object->department = $candidate->department;
               $new_object->course = $candidate->course;
                $new_object->nota = $candidate->nota;
                $new_object->ea = $candidate->nota >= 10 ? 'Sim':'Não';

                $matriculation = false;
                if($candidate->nota >= 10){
                    $matriculation = DB::table('matriculations as mt')
                    ->where('mt.user_id',$candidate->id)
                    ->exists();
                }
                
                $new_object->first_matriculation = $matriculation ? 'Sim':null;
                

                $new_object->special = isset($candidate->special) ? 'Sim' : 'Não';
                $new_object->medio_school = $candidate->medio_school;
                $new_object->medio_type = $candidate->medio_type;
                $new_object->medio_course = $candidate->medio_course;
                $new_object->medio_grade = $candidate->medio_grade;
                $new_object->money = null;
             
                $new_object->nss = null;
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
            'Nome do Curso Inscrito no Ensino Superior',
            'Nota do Exame de Acesso',
            'Admitido',
            'Matriculados pela 1º vez',
            'Necessidade de Educação Especial',
            'Procedência Escolar do Ensino Médio',
            'Natureza da Escola de Proveniência',
            'Nome do Curso do Ensino Médio',
            'Média Final no Ensino Médio',
            'Financiamento dos Estudos no Ensino Médio',
            'Trabalhador'
            
            // Adicione outros campos que você deseja exportar
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50// Largura da coluna 'A' (Nome)
           
        ];
    }
}
