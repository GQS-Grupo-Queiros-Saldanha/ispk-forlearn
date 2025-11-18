<?php

namespace App\Modules\Users\util;

use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Parameter;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Models\Matriculation;
use App\Helpers\LanguageHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;

class MatriculationStrategyConfigUtil
{

    private $Strategy_apply;

    public function __construct()
    {
        $this->setInstitutionStrategy();
    }
    private  function setInstitutionStrategy()
    {
        $strategy = DB::table('matriculation_strategy_config as mtsc')
            ->join('users as u1', 'u1.id', '=', 'mtsc.created_by')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u1.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->select([

                'mtsc.id',
                'mtsc.institution',
                'mtsc.status',
                'mtsc.description',
                'mtsc.create_at',
                'mtsc.updated_at',
                'mtsc.code_config_matriculation',
                'u_p.value as created_by'
            ])
            ->where('mtsc.status', 1)
            ->first();

        if ($strategy) {
            $this->Strategy_apply = $strategy->code_config_matriculation;
        }
    }


    public function aproveStatus($students, $lectiveYear){
    
        Log::info('aproveStatus chamado', [
            'tipo_students' => gettype($students),
            'eh_array' => is_array($students),
            'primeiro_elemento' => isset($students[0]) ? get_class($students[0]) : 'não definido',
            'lectiveYear' => $lectiveYear
        ]);

        if (empty($students)) {
            Log::warning('Nenhum estudante válido passado para aproveStatus');
            return ['error' => 'Nenhum estudante'];
        }

        if (!isset($this->Strategy_apply)) {
            Log::error('Strategy_apply não está definido na classe');
            return ['error' => 'Estratégia não definida'];
        }

        Log::info('Estratégia de matrícula ativa', ['Strategy_apply' => $this->Strategy_apply]);

        switch ($this->Strategy_apply) {
            case "inspunyl":
                Log::info("Chamando inspunyl()");
                return $this->inspunyl($students, $lectiveYear);
            case "ispk":
                Log::info("Chamando ispk()");
                return $this->ispk($students, $lectiveYear);
            default:
                Log::error("Estratégia inválida", ['Strategy_apply' => $this->Strategy_apply]);
                return "sem dados activo na estratégia";
        }

    }



    private function inspunyl($studant, $lectiveYear)
    {

        $rules_matriculation = DB::table('matriculation_aprove_roles_config as aprove')
            ->select(['aprove.*'])
            ->where('aprove.id_lective', $lectiveYear)
            ->get();


        //Pegar dados do TRANSFERIDO 
        $studentInfo = User::where('users.id', $studentId)
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['courses.id as course_id'])
            ->first();


        //Turma do estundante
        $classes = Classes::whereCoursesId($studentInfo->course_id)
            ->where('lective_year_id', $lectiveYear)
            ->get();


        //trazer todas as disciplinas (do estudante)  armazenadas no historico
        $disciplinesInOldGrades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
            ->get();


        $matriculation = Matriculation::whereUserId($studentId)
            ->orderBy('created_at', 'desc')
            ->first();


        $anoCurricular = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('new_old_grades', 'new_old_grades.discipline_id', '=', 'disciplines.id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['disciplines.id', 'new_old_grades.grade', 'dt.display_name', 'study_plans_has_disciplines.years'])
            ->whereIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
            ->where('new_old_grades.user_id', $studentId)
            ->get()
            ->groupBy('years');


        $curricularPlanDisciplines = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
            ->get()
            ->groupBy('years');



        $aproveStatus = collect();
        $aproveDirect = collect();
        $countBadGrade = collect();

        foreach ($rules_matriculation as $regras) {
            foreach ($anoCurricular as $year => $disciplines) {
                if ($regras->currular_year == $year) {
                    foreach ($disciplines as $discipline) {
                        if ($discipline->grade < 10) {
                            if (!isset($countBadGrade[$year])) {
                                $countBadGrade[$year] = collect();
                            }
                            $countBadGrade[$year]->push($discipline->id);

                            if ($countBadGrade[$year]->count() > $regras->discipline_in_delay) {
                                $aproveStatus[$year] = 'Reprovado';
                            } else {
                                $aproveStatus[$year] = 'Aprovado';
                            }
                        } else if ($discipline->grade > 9) {
                            $aproveDirect[$year] = $discipline->id;
                        }
                    }
                }
            }
        }

       
        //Generation 
        $Result =  $this->getDisciplinesWithIds($studentInfo, $aproveStatus, $countBadGrade);
        //Generation

     
        $curricularPlanDisciplines_dados = $curricularPlanDisciplines;

        $data = [
            'curricularPlanDisciplines' =>  $curricularPlanDisciplines_dados,
            'classes' => $classes,
            'estado' => $aproveStatus,
            'disciplinesReproved' => $Result['APROVADO_ATRASO'],
            'DADOS_DISCIPLINA' => $Result,
            'disciplinesInOldGrades' => $disciplinesInOldGrades,
            'info' => $info ?? ""
        ];

        return $data;
    }


   private function ispk($studant, $lectiveYear){
        // Verificar se o estudante é um modelo Eloquent válido
        $studentId = null;
        if (is_object($lectiveYear) && property_exists($lectiveYear, 'id')) {
            $lectiveYear = $lectiveYear->id;
        }


        if (is_array($studant) && isset($studant[0])) {
            $s = $studant[0];

            if (is_object($s)) {
                // Caso 1: é um modelo Eloquent (User)
                if (property_exists($s, 'id_user') && !empty($s->id_user)) {
                    $studentId = $s->id_user;
                }
                // Caso 2: pode ser um modelo normal com 'id'
                elseif (property_exists($s, 'id')) {
                    $studentId = $s->id;
                }
            }
        }

        // Verificação de segurança
        if (empty($studentId)) {
            Log::error('ID do estudante não encontrado ou inválido.', ['studant' => $studant]);
            return ['error' => 'Estudante inválido'];
        }

        Log::info('Iniciando função ispk para estudante ID: ' . $studentId);

        // Daqui em diante, substitui todas as ocorrências de
        // $studentId por $studentId
        $rules_matriculation = DB::table('matriculation_aprove_roles_config as aprove')
            ->select(['aprove.*'])
            ->where('aprove.id_lective', $lectiveYear)
            ->get();


        //Pegar dados do TRANSFERIDO 
        $studentInfo = User::where('users.id', $studentId)
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['courses.id as course_id'])
            ->first();

        if (!$studentInfo) {
            Log::error('studentInfo não encontrado para estudante ID: ' . $studentId);
            return [
                'error' => 'studentInfo não encontrado',
                'student_id' => $studentId
            ];
        }

        //Turma do estundante
        $classes = Classes::whereCoursesId($studentInfo->course_id)
            ->where('lective_year_id', $lectiveYear)
            ->get();


        //trazer todas as disciplinas (do estudante)  armazenadas no historico
        $disciplinesInOldGrades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
            ->get();


        $matriculation = Matriculation::whereUserId($studentId)
            ->orderBy('created_at', 'desc')
            ->first();


        $anoCurricular = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('new_old_grades', 'new_old_grades.discipline_id', '=', 'disciplines.id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['disciplines.id','disciplines.mandatory_discipline', 'new_old_grades.grade', 'dt.display_name', 'study_plans_has_disciplines.years'])
            ->whereIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
            ->where('new_old_grades.user_id', $studentId)
            ->get()
            ->groupBy('years');


        $curricularPlanDisciplines = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
            ->get()
            ->groupBy('years');



        $aproveStatus = collect();
        $aproveDirect = collect();
        $countBadGrade = collect();
        $counterDiscipline = collect();

        $containsReproved = false;
        $reproved_year = null;

        foreach ($rules_matriculation as $regras) {
            foreach ($anoCurricular as $year => $disciplines) {
                if ($regras->currular_year == $year) {
                    foreach ($disciplines as $discipline) {
                        if ($discipline->grade < 10) {
                            if (!isset($countBadGrade[$year])) {
                                $countBadGrade[$year] = collect();
                            }
                            $countBadGrade[$year]->push($discipline->id);
                            $counterDiscipline->push($discipline->id);
                                 
                            
                            if ($countBadGrade[$year]->count() > $regras->discipline_in_delay
                                 || $discipline->mandatory_discipline !== null 
                                 || $counterDiscipline->count() > 2 && $year > 2
                                )
                                
                            {   

                                $aproveStatus[$year] = 'Reprovado';
                                $containsReproved = true;
                                $reproved_year = $year;
                            } else {
                                $aproveStatus[$year] = 'Aprovado';
                            }
                        } else if ($discipline->grade > 9) {
                            $aproveDirect[$year] = $discipline->id;
                        }
                    }
                }
            }
        }
        if($containsReproved){
            $aproveStatus[$reproved_year] = 'Reprovado';
        }
       
        //Generation 
        $Result =  $this->getDisciplinesWithIds($studentInfo, $aproveStatus, $countBadGrade);
        // return ["APROVADO_DIRECT"=>$aproveDirect,"APROVE_YEAR_STATUS"=>$aproveStatus,"REPROVE_YEAR_DISCIPLINE"=> $countBadGrade];

     
        $curricularPlanDisciplines_dados = $curricularPlanDisciplines;

        $data = [

                    'curricularPlanDisciplines' =>  $curricularPlanDisciplines_dados,
                    'classes' => $classes,
                    'estado' => $aproveStatus,
                    'disciplinesReproved' => $Result['APROVADO_ATRASO'],
                    'DADOS_DISCIPLINA' => $Result,
                    'disciplinesInOldGrades' => $disciplinesInOldGrades,
                    'rules_matriculation' => $rules_matriculation,
                    'info' => $info ?? ""

                ];
            /*Log::info('Resultado da função ispk:', [
                'estado' => $aproveStatus,
                'containsReproved' => $containsReproved,
                'reproved_year' => $reproved_year,
                'countBadGrade' => $countBadGrade,
                'DADOS_DISCIPLINA' => $Result,
                'curricularPlanDisciplines' => $curricularPlanDisciplines_dados,
            ]);*/

        return $data;
    }





private function getDisciplinesWithIds($studentInfo, $currricular_year_status, $reprove_disciplines)
  
{

   
        $reprovado_Discipline_displey = [];
        $aprove_Discipline_displey = [];
        $foundReprovado = false;
        
        if($currricular_year_status->contains('Reprovado')){

            foreach ($reprove_disciplines as $Year_Curricular => $disciplines) {
                $reprovado_Discipline_displey[$Year_Curricular] = $this->getDisciplines($studentInfo->course_id, $disciplines);

            }
        }
        else{
            foreach ($currricular_year_status as $year => $estado) {
                if ($foundReprovado) {
                break;
                }
    
                foreach ($reprove_disciplines as $Year_Curricular => $disciplines) {
                if ($year == $Year_Curricular) {
                    if ($estado == "Aprovado") {
                        $aprove_Discipline_displey[$year] = $this->getDisciplines($studentInfo->course_id, $disciplines);
                    } elseif ($estado == "Reprovado") {
                        $reprovado_Discipline_displey[$year] = $this->getDisciplines($studentInfo->course_id, $disciplines);
                        $foundReprovado = true;
                        break;
                    }
                }
                }
            }

    }
    
        return [
            "APROVADO_ATRASO" => $aprove_Discipline_displey,
            "REPROVADO_ATRASO" => $reprovado_Discipline_displey
        ];
}

private function getDisciplines($course_id, $disciplines)
{
        return StudyPlan::where('study_plans.courses_id', $course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->whereIn('disciplines.id', $disciplines)
            ->get();
            // ->groupBy('years');
}
    
}
