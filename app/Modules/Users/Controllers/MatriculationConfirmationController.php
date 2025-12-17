<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleMonthlyCharge;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\util\MatriculationUtil;
use App\Modules\Users\util\MatriculationStrategyConfigUtil;
use App\Modules\Users\Events\PaidStudentCardEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MatriculationConfirmationController extends Controller
{
    //METODO QUE APAGA REGISTE DE MATRICULA MAL FEITA.
    public function apagar($idusurio)
    {
        //  $apagatudo=DB::table('matriculations')
        //     ->where('created_by',7336)
        //     ->get();   
        // foreach ($apagatudo as $item) {
        //     DB::table('matriculation_disciplines')
        //         ->where('matriculation_id', '=',$item->id)
        //         ->delete();
        // }

        // for ($i=0; $i <count($apagatudo) ; $i++) {   
        //    DB::table('matriculations')
        //     ->where('created_by', '=',7336)
        //     ->delete();
        // }

    }
    public function index()
    {
        return "ola Mundo!";
    }

    public function create(LectiveYear $lective_year)
    {
      
        try {
            
               
            $lective_year->translations->where('active',1)->first();
 
            $data = [
                     'action' => 'create',
                     'lective_year'=>$lective_year,
                     'languages' => Language::whereActive(true)->get(),
                     'users' => $this->studentsWithCourseAndMatriculationSelectList()
                  ];
                  
            // return $data['users'];
            return view('Users::confirmations-matriculations.confirmation')->with($data);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            // return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    //metodo privado para pegar
    //carregar todas as disciplinas em atraso do estudante
    //carregar todas as disciplinas do curso
    private function candidato_primeiro($studentInfo){
        //Pegar ano lectivo corrente.
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 12;

        $disciplinesReproved = collect();
        $classes = Classes::whereCoursesId($studentInfo->course_id)
            ->where('lective_year_id',$lectiveYearSelected)
            ->get();

        //Fim Pegar Turma Por ano lectivo.
        $curricularPlanDisciplines = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            //->whereNotIn('')
            //->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))//array_column($allApprovedDisciplines, 'id')
            //->whereNotIn('disciplines.id', )//array_column($allReprovedDisciplines, 'id')
            ->where('study_plans_has_disciplines.years', 1)
            ->get()
            ->groupBy('years');
      
         //Regras do 5 pontos (saber sobre aprovação ou não)         
        $code_curso=DB::table('courses')->select(['code'])->whereId($studentInfo->course_id)->get();
        $estado= $this->verificarAprovacao($disciplinesReproved,$studentInfo->course_id);                           

        $data = [
                'curricularPlanDisciplines' => $curricularPlanDisciplines,
                'classes' => $classes,
                'estado'=>$estado,
                'nextYear' => 1,
                'disciplinesReproved' => $disciplinesReproved
            ];
            return  $data;
    }





    private function equivalence_Student($studentId,$anoLEctivo){
              
            /** Confirmar presença na equivalência lista **/
            $transfere_studant = User::query()
            ->whereHas('roles', function($q) {
                $q->where('id', '=', 6);
            })
            ->whereDoesntHave('matriculation')
            ->join('tb_transference_studant as transf', 'users.id', '=', 'transf.user_id')
            ->where('transf.user_id',$studentId)
            ->where('transf.type_transference',1)
            ->where('transf.type_transference',1)
            ->where('transf.status_disc',1)
            ->first();

            if(!$transfere_studant){
                Log::warning('Estudante não encontrado na lista de equivalência', ['studentId' => $studentId]);
                return 0;
            }

            //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
            $currentData = Carbon::now();

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
            ->where('lective_year_id',$anoLEctivo->id)
            ->get();

            $matriculation = Matriculation::whereUserId($studentId)
            ->orderBy('created_at', 'desc')
            ->first();

            
            //trazer todas as disciplinas (do estudante)  armazenadas no historico
            $disciplinesInOldGrades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
            ->get();

            
            //armazenar as positivas em uma collection e as negativas noutra

            $disciplinesWithPositiveGrade = collect();
            $disciplinesWithNegativeGrade = collect();
            $disciplinesAll = collect();
            
            foreach($disciplinesInOldGrades as $value){
                $disciplinesAll->push($value->discipline_id);

                if ($value->grade <= 9.00) {
                    $disciplinesWithNegativeGrade->push($value->discipline_id);
                } else {
                    $disciplinesWithPositiveGrade->push($value->discipline_id);
                }
            }

            $disciplinesWithPositiveGrade = $disciplinesWithPositiveGrade->toArray();
            $disciplinesWithNegativeGrade =  $disciplinesWithNegativeGrade->toArray();
            
            //return response;

            //return todas as disciplinas do historico excepto as com positivas.

         $anoCurricular = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->whereIn('disciplines.id', $disciplinesAll)
            ->get()
            ->groupBy('years');
            
            $years=[];$qrd_aprovate=[];
            foreach( $anoCurricular as $index=> $value){
               // dd('index',$index, $anoCurricular);
            $years[]=$index;
            }

           
            //Esse array traz as disciplinas que este estudante não tem feita nas equivalências
            //Serve para saber o estado de aprovação
            $disciplinesReproved = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
                                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                                ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                                ->join('disciplines_translations as dt', function ($join) {
                                    $join->on('dt.discipline_id', '=', 'disciplines.id');
                                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                    $join->on('dt.active', '=', DB::raw(true));
                                })
                                ->whereNotIn('disciplines.id', $disciplinesAll)
                                ->whereIn('study_plans_has_disciplines.years',$years)
                                ->get()
                                ->groupBy('years');
                                           
            $disciplinesReprovedYEAR= collect(); 
            $Matriculation_year=0 ; 
            //Regras do 5 pontos (saber sobre aprovação ou não)  e cada ano curricular pego 
            //erro começa apartir daqui
            //return $curricularYear;
            sort($years);
            foreach($years as $curricularYear){
                if(isset($disciplinesReproved[$curricularYear])){

                    $disciplinesReprovedYEAR[$curricularYear]=collect($disciplinesReproved[$curricularYear]);
                    $qrd_aprovate[$curricularYear] =  $this->verificarAprovacao($disciplinesReprovedYEAR,$studentInfo->course_id); 
                   
                    

                    $estadoT= $qrd_aprovate[$curricularYear]['estado'];
                    $estadoP= $qrd_aprovate[$curricularYear]['pontos'];
                    $estadoC= $qrd_aprovate[$curricularYear]['curso'];
                    if($estadoT=="reprovado"){
                        $Matriculation_year=$curricularYear;
                        break;
                    }
                    else if($estadoT!="reprovado" && $estadoP >4 && $estadoC!="RI"){
                        $Matriculation_year=$curricularYear;
                        break;
                    }
                    else{ continue;}
                }
            }
       //sed
       $aprovedSed = isset($qrd_aprovate[$Matriculation_year])
                ? $qrd_aprovate[$Matriculation_year]
                : [ 'year' => 0 ,'Estado' => 'reprovado', 'error' => 'yes' ];

       $equivalencia= [
                "Estado"=>$aprovedSed,
                "year"=>$Matriculation_year,
                "disciplineReprovado"=>$disciplinesReprovedYEAR,
            ];
        
        
        $curricularPlanDisciplines = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
                                    ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                                    ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                                    ->join('disciplines_translations as dt', function ($join) {
                                        $join->on('dt.discipline_id', '=', 'disciplines.id');
                                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                        $join->on('dt.active', '=', DB::raw(true));
                                        })
                                //->whereNotIn('')
                                    ->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
                                //Uma mão Lava à outra
                                    ->where('study_plans_has_disciplines.years', '>=', $equivalencia['year'])
                                    ->get()
                                    ->groupBy('years');

            
            $code_curso=DB::table('courses')->select(['code'])->whereId($studentInfo->course_id)->get();
            //Este código de baixo é por causa da espcialidade do curso CEE 4º que não aparece
            //Para ser confirmada porque identifica que falta outras cadeiras para o aluno fazer.^
            $curricularPlanDisciplines_dados=$curricularPlanDisciplines;
            $curricularPlanDisciplines=[ 4=>count($curricularPlanDisciplines)>1 &&  ($equivalencia['year'] + 1)==4 &&  $code_curso[0]->code=="CEE" ? $curricularPlanDisciplines[$equivalencia['year'] + 1]:0 ];
            //
            $data = [
                    'curricularPlanDisciplines' => $curricularPlanDisciplines[4]===0?   $curricularPlanDisciplines_dados:$curricularPlanDisciplines,
                    'classes' => $classes,
                    'estado'=> $equivalencia['Estado'],
                    'nextYear' => $equivalencia['year'] + 1,
                    'disciplinesReproved' =>  $equivalencia['disciplineReprovado'],
                    'info'=>$info??""
                ];
        
        return $data;
    }





    private function Imported_Student($studentId, $anoLEctivo){

        $imported_studant = User::query()
            ->whereHas('roles', function ($q) {
                $q->where('id', '=', 6);
            })
            ->whereDoesntHave('matriculation')
            ->join('Import_data_forlearn as imp', 'users.id', '=', 'imp.id_user')
            ->where('imp.id_user', $studentId)
            ->where('imp.type_user', 1)

            ->first();

        if (!$imported_studant) {
            return 0;
        }

        return [$imported_studant];
    }






    /**
     * params: studentId
     * carregar todas as disciplinas em atraso do estudante
     * carregar todas as disciplinas do curso do estudante
     */
     
     
     
    public function ajaxUserData($studentId){       
     try{

       
       $info="";
       $student=explode(",",$studentId);
       $studentId=$student[0];
       $anoLEctivo= $student[1];

    // Crie uma instância da classe
      $matriculationStrategyConfigUtil = new MatriculationStrategyConfigUtil();

      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
      ->where("id",$anoLEctivo)
    // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
      ->first();

      $lectiveYearSelected = $lectiveYearSelected ?? DB::table('lective_years')
        ->where('lective_years.id', 12)
        ->first();

     //variaveis super importante
      $data =[];
      $status="CE";

        //Candidados deste ano 
      //2021--- e os já matriculados.
      $studentInfo = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [15]);
         })
            ->whereHas('courses')
            //->whereHas('matriculation')
            ->with(['parameters' => function ($q) {
                $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            //->whereNotBetween('age', [1, 16])
            //->whereNotBetween('users.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->whereBetween('users.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->leftJoin('user_candidate as u_cand' ,'u_cand.user_id','users.id')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['uc.courses_id as course_id'])
            ->where('u_cand.user_id',$studentId)
            ->first();
           


        if (empty($studentInfo)) {
            // $studentId=1888; 
           $data=$this->equivalence_Student($studentId,$lectiveYearSelected);
           Log::info("Equivalência", ['data' => $data,'estudanteID' => $studentId,'anoLectivo' => $lectiveYearSelected]);
                            
     
           if($data!=0){
                $view = view("Users::confirmations-matriculations.disciplines_equivalencia")->with($data)->render();
                return response()->json(array('html' => $view));
            }

             // Utilizador importado
            $data = $this->Imported_Student($studentId, $lectiveYearSelected);
            Log::info('Importado', ['data' => var_export($data,true), 'estudanteID' => $studentId,'anoLectivo' => $lectiveYearSelected]);

            if ($data != 0) {
                Log::info("Função chamada para importado", ['data' => var_export($data, true),'estudanteID' => $studentId,'anoLectivo' => $lectiveYearSelected->id]);
                
                if (!is_array($data)) {
                    $data = [$data];
                }
                if (empty($data)) {
                    Log::warning('Data vazio ou inválido antes de chamar aproveStatus', ['data' => $data]);
                    $status = ['error' => 'Nenhum estudante para processar'];
                } else {
                    Log::info('Antes de chamar aproveStatus', [
                        'class' => get_class($matriculationStrategyConfigUtil),
                        'temMetodo' => method_exists($matriculationStrategyConfigUtil, 'aproveStatus') ? 'sim' : 'não',
                        'students_type' => gettype($data),
                        'students_is_array' => is_array($data),
                        'students_primeiro' => isset($data[0]) ? get_class($data[0]) : 'não definido'
                    ]);
                   try {
                        Log::info("Chamada a aproveStatus iniciada");
                        $status = $matriculationStrategyConfigUtil->aproveStatus($data, $lectiveYearSelected->id);
                        Log::info("Chamada a aproveStatus concluída", ['status' => $status]);
                    } catch (\Throwable $e) {
                        Log::error("Erro ao chamar aproveStatus: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString()
                        ]);
                    }

                }

                 Log::info('Status retornado de aproveStatus', ['status' => $status]);
                 $view = view("Users::confirmations-matriculations.disciplines_news_trategy")->with($status)->render();
                 return response()->json(array('html' => $view));
            }

            $studentInfo = User::where('users.id', $studentId)
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->select(['matriculations.course_year as year','courses.id as course_id','courses.code as code'])
                ->firstOrFail();

            //se o estudante for candidato exibir apenas as disciplinas do 1º ano
            //return response()->json($studentInfo);
            $status=0;
            Log::info('studentInfo: ' . json_encode($studentInfo, JSON_PRETTY_PRINT));
            
            if($studentInfo->hasRole('candidado-a-estudante')) {  
                $data = $this->candidato_primeiro($studentInfo);
            }
        }

      if ($status==="CE") {
          
                      
           //Default course_candidate
           $default_course=DB::table('courses_default')
            ->select(['courses_id as course_id'])
            ->where('users_id',$studentId)->first();
              
           if($default_course){
               $data = $this->candidato_primeiro($default_course);  
           }else{
                $data = $this->candidato_primeiro($studentInfo);
           }
           
        }

      else {
    
        /**
         * 1- trazer todas as disciplinas do historico - com notas (para depois pegar so as positivas)
         * 2 - (agrupar por ano)listar todas as disciplinas do plano curricular menos as que ja esto no historico
         * 3 - (agrupar por ano) listar todas as disciplinas do plano curricular apenas com as que ja estao no historico
         * verificar se as disciplinas do curso já têm notas ou não.
         */
    
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->where("id",$anoLEctivo)
                ->first();
                
            $lectiveYearSelected = $lectiveYearSelected->id ?? 12;
            $classes = Classes::whereCoursesId($studentInfo->course_id)
                ->where('lective_year_id',$lectiveYearSelected)
                ->get();

            $matriculation = Matriculation::whereUserId($studentId)
                ->orderBy('created_at', 'desc')
                ->first();


            //trazer todas as disciplinas (do estudante)  armazenadas no historico
            $disciplinesInOldGrades = DB::table('new_old_grades')
                ->where('user_id', $studentId)
                ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
                ->get();

            //armazenar as positivas em uma collection e as negativas noutra

            $disciplinesWithPositiveGrade = collect();
            $disciplinesWithNegativeGrade = collect();


            foreach ($disciplinesInOldGrades as $value){
                if ($value->grade <= 9.00) {
                    $disciplinesWithNegativeGrade->push($value->discipline_id);
                } else {
                    $disciplinesWithPositiveGrade->push($value->discipline_id);
                }
             }


            $disciplinesWithPositiveGrade = $disciplinesWithPositiveGrade->toArray();
            $disciplinesWithNegativeGrade =  $disciplinesWithNegativeGrade->toArray();

            //return response()->json($disciplinesWithNegativeGrade);
            //trazer todas as disciplinas do historico excepto as com positivas.

            $disciplinesReproved = StudyPlan::where('study_plans.courses_id', $studentInfo->course_id)
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                ->join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disciplines.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                //->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))//array_column($allApprovedDisciplines, 'id')
                //->whereNotIn('disciplines.id', $disciplinesWithPositiveGrade)
                ->whereIn('disciplines.id', $disciplinesWithNegativeGrade)
                ->where('study_plans_has_disciplines.years', '<=', $matriculation->course_year)
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
                //->whereNotIn('')
                ->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))//array_column($allApprovedDisciplines, 'id')
                //->whereNotIn('disciplines.id', )//array_column($allReprovedDisciplines, 'id')
                ->where('study_plans_has_disciplines.years', '>=', $matriculation->course_year)
                ->get()
                ->groupBy('years');

              //Regras do 5 pontos (saber sobre aprovação ou não)         
              $code_curso=DB::table('courses')->select(['code'])->whereId($studentInfo->course_id)->get();
              $estado = $this->verificarAprovacao($disciplinesReproved,$studentInfo->course_id); 
              
               //Pegar a regra dos cursos com mudança de forma dinâmica.
              $curso_mudanca_status=DB::table('tb_courses_change')
                ->where('course_id_primary',$studentInfo->course_id)
                ->where('status',1)
                ->where('lective_year_id',$lectiveYearSelected)
                ->first();
            
     
              if($curso_mudanca_status){
                // dd($curso_mudanca_status);
                if($estado['estado']=="FOI"){
                      $info="Atenção : Ao confirmar esta matrícula irá ocorrer a mudança automática de curso. ";
                      $classess = Classes::whereCoursesId($curso_mudanca_status->course_id_secundary)
                      ->where('lective_year_id',$lectiveYearSelected)
                      ->get();
                        //Margem de turmas de dois cursos
                        $classes= $classes->merge($classess);  ;
                      
                       $curricularPlanDisciplines = StudyPlan::where('study_plans.courses_id', $curso_mudanca_status->course_id_secundary)
                        ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                        ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                        ->join('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'disciplines.id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                            })
                        ->whereNotIn('disciplines.id', $disciplinesInOldGrades->pluck('discipline_id'))
                        //Uma mão Lava à outra
                        ->where('study_plans_has_disciplines.years', '>=', $matriculation->course_year)
                        ->get()
                        ->groupBy('years');
                 } 
                //O código acima é quando há mudança de curso  sem cadeira em atraso   
              
              }
             
              //Este código de baixo é por causa da espcialidade do curso CEE 4º que não aparece
              //Para ser confirmada porque identifica que falta outras cadeiras para o aluno fazer.^
              $curricularPlanDisciplines_dados=$curricularPlanDisciplines;
              $curricularPlanDisciplines=[ 4=>count($curricularPlanDisciplines)>1 &&  ($matriculation->course_year + 1)==4 &&  $code_curso[0]->code=="CEE" ? $curricularPlanDisciplines[$matriculation->course_year + 1]:0 ];
             
            //   if($curricularPlanDisciplines[4]!==0){
            //     //   return $curricularPlanDisciplines[$matriculation->course_year+1];
            //       return $curricularPlanDisciplines;
            //   }
          
           $data = [
                    'curricularPlanDisciplines' => $curricularPlanDisciplines[4]===0?$curricularPlanDisciplines_dados:$curricularPlanDisciplines,
                    'classes' => $classes,
                    'estado'=>$estado,
                    'nextYear' => $matriculation->course_year + 1,
                    'disciplinesReproved' => $disciplinesReproved,
                    'info'=>$info
                  ];



         }
         
         
        
        Log::info('ReturnedData: ' . json_encode($data, JSON_PRETTY_PRINT));
        $view = view("Users::confirmations-matriculations.disciplines")->with($data)->render();

        return response()->json(array('html' => $view));
    
     } catch (Exception | Throwable $e) {
         return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        


    }
    
    


  private function verificarAprovacao($disciplinesReproved, $id_curso){
    Log::info("Verificar aprovação - início", ['rows' => $disciplinesReproved, 'curso_id' => $id_curso]);

    $curso = DB::table('courses')->whereId($id_curso)->first();
    $cursoCode = $curso ? $curso->code : null;

    // Totais
    $anual_total = 0;
    $simestral_total = 0;

    // Percorre por ano / grupo vindo da view
    foreach ($disciplinesReproved as $year => $disciplineList) {
        // assegura que é array/coleção
        $list = is_array($disciplineList) ? $disciplineList : $disciplineList->toArray();

        foreach ($list as $disc) {
            $code = isset($disc['code']) ? (string)$disc['code'] : (isset($disc->code) ? (string)$disc->code : '');

            // Extrai caracteres confiáveis: último e terceiro a contar do fim (se existirem)
            $lastChar = $code !== '' ? substr($code, -1) : null;
            $thirdFromEnd = strlen($code) >= 3 ? substr($code, -3, 1) : null;

            // Prioriza deteção em último caractere; fallback para terceiro-from-end; default assume semestral
            if ($lastChar === 'A' || $thirdFromEnd === 'A') {
                $anual_total++;
            } elseif (in_array($lastChar, ['1','2']) || in_array($thirdFromEnd, ['1','2'])) {
                $simestral_total++;
            } else {
                // Se não for possível identificar, escolhe semestral por segurança (ou ajustar conforme regra local)
                $simestral_total++;
                Log::warning('Código de disciplina com padrão inesperado — a assumir semestral', ['code'=>$code, 'disc'=>$disc]);
            }
        }
    }

    $total_reprovadas = $anual_total + $simestral_total;
    $pontos = ($anual_total * 2) + $simestral_total;

    Log::info('Contagem de reprovações', [
        'anual_total' => $anual_total,
        'simestral_total' => $simestral_total,
        'total_reprovadas' => $total_reprovadas,
        'pontos_calculados' => $pontos,
        'curso_code' => $cursoCode
    ]);

    //Regras para cada curos by Ezequiel
    //id do curso
    $id_curso
    $curso = [''=>'']

    
    // Regras
    if ($total_reprovadas >= 5) {
        $estado = ($cursoCode === "RI" && $pontos >= 5 && $pontos < 7) ? 'aprovado' : 'reprovado';
        $observacao = [
            'Obs' => 'regra01',
            'confirmacao' => 1,
            'qtd_disciplina' => $total_reprovadas,
            'emolumento' => 'P_normais',
            'estado' => $estado,
            'curso' => $cursoCode,
            'pontos' => $pontos,
            'atencao' => "Se as disciplinas em atraso forem >= 5, gerar as 10 propinas e a confirmação de matrícula"
        ];
        Log::info('Resultado regra01', $observacao);
        return $observacao;
    }

    if ($total_reprovadas > 0 && $total_reprovadas <= 4) {
        $anual_pontos = $anual_total * 2;
        $soma_pontos = $simestral_total + $anual_pontos;

        // Condições para regra02
        if ($anual_pontos > 2 && $simestral_total > 0 || $soma_pontos > 4) {
            $estado = $soma_pontos >= 5 ? 'com cadeira' : 'reprovado';
            $observacao = [
                'Obs' => 'regra02',
                'confirmacao' => 1,
                'qtd_disciplina' => $total_reprovadas,
                'emolumento' => 'inscricao_frequencia',
                'estado' => $estado,
                'curso' => $cursoCode,
                'pontos' => $soma_pontos,
                'atencao' => "Se as disciplinas em atraso forem <= 4, gerar emolumentos 'Inscrição por frequência' e a confirmação de matrícula"
            ];
            Log::info('Resultado regra02', $observacao);
            return $observacao;
        }
    }

    // Caso sem reprovações ou não encaixa nas regras anteriores
    if ($total_reprovadas === 0) {
        $observacao = [
            'Obs' => 'normal',
            'curso' => $cursoCode,
            'pontos' => 0,
            'estado' => 'aprovado'
        ];
        Log::info('Resultado normal - sem reprovações', $observacao);
        return $observacao;
    } else {
        // Se houver reprovações mas não encaixou nas condições acima, devolve como reprovado e pontos calculados
        $observacao = [
            'Obs' => 'com_cadeiras',
            'curso' => $cursoCode,
            'pontos' => $pontos,
            'estado' => 'reprovado'
        ];
        Log::info('Resultado normal - reprovações não cobertas por regras', $observacao);
        return $observacao;
    }
}


































































    /*private function approvalRules($anoAnterior, $anoNovo, $disciplinasReprovadas, $user_student){
         Log::info("ano anterior: $anoAnterior, ano novo: $anoNovo, disciplinas reprovadas: ", $disciplinasReprovadas, "user_student: ", $user_student->id);
        $registration = DB('matriculation')
            ->where('user_id', $user_student->id)
            ->whereNull('deleted_at')
            ->first();
        if (!$registration) {
            return true;
        }
        $consulta = DB::table('matriculation_aprove_roles_config')
            ->where('currular_year', $anoAnterior)
            ->select('discipline_in_delay')
            ->first();

        // Se não encontrou configuração, retorna falso
        if (!$consulta) {
            return false;
        }

        $limite = (int) $consulta->discipline_in_delay;
        $numeroReprovadas = count($disciplinasReprovadas);

        // Se o número de cadeiras reprovadas é maior que o limite → reprova
        if ($numeroReprovadas > $limite) {
            return false;
        }

        return true;
    }*/



    public function store(MatriculationRequest $request){
       
        try {
    
        //Ano lectivo 
         $currentData = Carbon::now();
         $lectiveYearSelected = DB::table('lective_years')
        ->where('id',$request->anoLective)
         ->get();

    
          $user_student = User::whereId($request->user)->first();
          $id_curso=$user_student->courses()->first()->id;
        
        
          if(MatriculationUtil::verificarCursoBloquedoPorAnosCurriculares($request->anoLective, $id_curso, $request->years)){
                Toastr::Warning(__('A forLEARN detectou que o curso e o/os ano(s) curricular(es) deste estudante encontra-se bloqueiado neste ano lectivo, contacte o apoio à forLEARN.'), __('toastr.warning'));
                return redirect()->route('matriculations.index');
             }
            
          
          if(MatriculationUtil::verificarEdicaoPlanoEstudo($request->anoLective, $id_curso)){
                Toastr::Warning(__('A forLEARN detectou que o curso deste estudante encontra-se sem a edição de plano de estudo deste ano configurado, contacte o apoio à forLEARN.'), __('toastr.warning'));
                return redirect()->route('matriculations.index');
           }


         if($lectiveYearSelected->isEmpty()){
            Toastr::Warning(__('A forLEARN detectou uma selecção de ano lectivo inválido, tente novamente, no caso do erro persistir, contacte o apoio à forLEARN!'), __('toastr.warning'));
            return redirect()->route('matriculations.index');
          }   
 
          //Barreira se o usuário já está matriculado neste ano lectivo
           $ConfirmaMatriculaExiste = DB::table('matriculations')
          ->where('user_id',$request->user)
          ->whereNull('deleted_at')
          ->where('lective_year',$lectiveYearSelected[0]->id)
          ->get();
          
          if(!$ConfirmaMatriculaExiste->isEmpty()){
            // Success message
            Toastr::Warning(__('O sistema detectou que a matrícula deste utilizador já se encontra confirmada neste ano lectivo, consulta o código de confirmação de matrícula '.$ConfirmaMatriculaExiste[0]->code .' para validar esta informação , Obrigado(a) !'), __('toastr.warning'));
            return redirect()->route('matriculations.index');
            
          }

          
          //barrar se não tem emolumento Pago - ano passado
           $ConfirmaPagamento= DB::table('article_requests')
            ->where('user_id',$request->user)
            ->where('status','!=','total')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->get();

          
          if(!$ConfirmaPagamento->isEmpty()){

            Toastr::Warning(__('A forLEARN detectou que este(a) estudante tem pendentes na tesouraria.'), __('toastr.warning'));
            return redirect()->route('matriculations.index');

          }

        //Regra de aprovação de matrícula por número de cadeiras em atraso
        /*if(isset($request->years[1])) {
            $approved = $this->approvalRules($request->years[0], $request->years[1], $request->disciplines[$request->years[0]], $user_student);
        } else {
            $approved = $this->approvalRules($request->years[0], $request->years[0], $request->disciplines[$request->years[0]], $user_student);
        }

        if(!$approved) {
            $anoDestino = $request->years[1] ?? $request->years[0];
            Toastr::warning(
                __('O sistema detetou que a matrícula para o ano ' . $anoDestino .
                ' não pode ter este número de cadeiras em atraso. Consulta a PA para validar esta informação. Obrigado(a)!'),
                __('Aviso')
            );

            return redirect()->route('matriculations.index');
        }*/

          


          // Validação de impedimento de confirmação de estudantes com cursos bloqueiados.
          // $user_student = User::with(['course'])->whereId($request->user)->first();
         

        
          

            DB::beginTransaction();

            $user = User::findOrFail($request->get('user'));
            //Saber o Status do Estudante quanto a Inscricao ao exame de Acesso
            //Se for Pendente, nao efeturar a matricula
            //id do article para o exame::6

              $estadoInscricao = DB::table('article_requests')->where([
                ['user_id','=', $user->id],
                ['article_id','=','79']
            ])->get();

        if ($estadoInscricao->isEmpty()) {
        //  return $estadoInscricao=createAutomaticArticleRequest($user->id, 79, null, null);
         }

   
            //ERRO: CODIGO DA MATRÍCULA REPETIDO (CASO RARO)
            //SOLUC: AVALIAR SE O CODIGO A SER CRIADO JA EXISTE NA BD

            $F_year=explode ("-",$lectiveYearSelected[0]->start_date);
            $ano_codigo=substr($F_year[0], -2); 
            $nextCodeNumber = $ano_codigo.'0001';


            $user_code_matricula = DB::select('CALL proc_max_code_matriculations(?,?)',["CM", $lectiveYearSelected[0]->id]);
            $nextCodeNumber = $user_code_matricula[0]->next_code_confirm;
                
            
     

             
            $nextCode = $nextCodeNumber;   

            $maxSelectedYear = (int)collect($request->get('years'))->max();
            
             //Validar se já existe matricula-----------------------------------------------------------------// 
             $valida=DB::table('matriculations')
            ->where('user_id', $user->id)
            ->where('course_year', $maxSelectedYear)
            ->where('lective_year',$lectiveYearSelected[0]->id)
            ->whereNull('deleted_at')
            ->get();


            $validaCodigo=DB::table('matriculations')
            ->where('code', $nextCode)
            ->where('lective_year',$lectiveYearSelected[0]->id)
            ->whereNull('deleted_at')
            ->get();


            //------------------------------------------------------------------------------------------------//
            if(!$valida->isEmpty() ){
                Toastr::error(__('A forLEARN detectou que o estudante já se encontra matriculado, consulta na lista de matriculados, caso não! contacte o apoio a forLEARN.'), __('toastr.error'));
                return back();
            }
            
    
            $currentNumber = $user->parameters()->where('parameters.id', 19)->first();
            $courseNumericCode = $user->courses()->first()->numeric_code;
            $formatY = Carbon::now()->format('y');
            $formatNext = substr($nextCodeNumber, 2);
            if(strpos($formatNext, $formatY) == 0){
                $tam = strlen($formatY);
                $formatNext = substr($formatNext, $tam);
            }
            $newNumber = $formatY . $courseNumericCode .$formatNext;
          

            
            $matriculation = Matriculation::create([
                'user_id' => $user->id,
                'code' => $nextCode,
                'lective_year'=>$lectiveYearSelected[0]->id,
                'course_year' => $maxSelectedYear,
            ]);
           
            $estadoAntigo = $user->hasRole('candidado-a-estudante') ? 1 : 2;
            // role candidate to student
            if ($user->hasRole('candidado-a-estudante')) {
                //codigo para mudar o documento pessoal do candidato para documento pessoal do utilizador
                 $data=DB::table('user_parameters')
                ->where('users_id',$user->id)
                ->where('parameter_group_id',13)
                ->get()
                ->map(function($item ){
                    return $item->id;
                 });
            if (!$data->isEmpty()) {
                    $data1=DB::table('user_parameters')
                    ->whereIn('id',$data)
                    ->update(['parameter_group_id' => 3]); 
                }
                 //Termina aqui a mudança de lado do documento.
                $user->syncRoles('student');
               
                
                if ($currentNumber) {
                    $currentNumber->pivot->value = $newNumber;
                    $currentNumber->pivot->save();
                }else {
                    $user_n_mecanografico[] = [
                        'parameters_id' => 19,
                        'created_by' => auth()->user()->id ?? 0,
                        'parameter_group_id' => 1,
                        'value' => $newNumber
                    ];

                    $user->parameters()->attach($user_n_mecanografico);
                }
            }
            

            //disciplines
            $userDisciplines = [];
            $yearsWithDisciplines = [];
            $allDisciplinesInCurricularYear = [];
            $allDisciplinesOffCurricularYear = [];

            $disciplineByYear = $request->get('disciplines');

            foreach ($disciplineByYear as $year => $disciplines) {
          
                if (is_array($disciplines) && count($disciplines)) {
                    $yearsWithDisciplines[] = (int)$year;
                    foreach ($disciplines as $d) {
                      $userDisciplines[$d] = ['exam_only' => false];
                        if ((int)$year !== $maxSelectedYear) { 
                            $allDisciplinesOffCurricularYear[$d] = false;
                        } else {
                            $allDisciplinesInCurricularYear[$d] = false;
                        }
                    }
                }
            }
          

         //exam only disciplines
         $examOnlyDisciplinesByYear = $request->get('disciplines_exam_only');
            if (is_array($examOnlyDisciplinesByYear)) {
                foreach ($examOnlyDisciplinesByYear as $year => $disciplines) {
                    if (is_array($disciplines) && count($disciplines)) {
                        foreach ($disciplines as $d) {
                            $userDisciplines[$d]['exam_only'] = true;
                            $allDisciplinesOffCurricularYear[$d] = true;
                        }
                    }
                }
            } 

         //classes
         $userClasses = [];
         $yearsWithClasses = [];
            foreach ($request->get('classes') as $year => $class) {
                if ($class) {
                    $yearsWithClasses[] = $year;
                    $userClasses[] = $class;
                }
            }


          //return $allDisciplinesOffCurricularYear;
          if ($user->hasRole('candidado-a-estudante')) {
                $get_matriculation_class_total = 150;
                $get_class_vacancies = 200;
           }
           
          else
          {
             //Obter o total de matriculas feitas em uma determinada turma
             $get_matriculation_class_total = 150;
             //Obter o total de vagas de uma determinada turma
             $get_class_vacancies = 200;
            }
                 
          
            //Avaliar se o total de matriculas feitas em uma determinada turma + 1 (mais a proxima a ser feita) for menor ou igual ao total de vagas
            if ($get_matriculation_class_total + 1 <= $get_class_vacancies) {
                   
                
                //return "Pode Efetuar Matrícula";
                if ($yearsWithDisciplines !== $yearsWithClasses) {
                    return redirect()->back()->withErrors(['Definição de turmas e/ou disciplinas inválida.'])->withInput();
                }
                if (!empty($userDisciplines)) {
                    $matriculation->disciplines()->sync($userDisciplines);
                }
                if (!empty($userClasses)) {
                    $matriculation->classes()->sync($userClasses);
                }
                
                
                 $articleRequets = [];
                // Confirmação de matrícula ou pré-matrícula(id: (2020 - id:8))
                  $index = $estadoAntigo == 1 ? 0 : 1;
                  
                  $emolumento=['p_matricula','confirm'];
                  $emolumentoFolha=['folha_de_prova'];
                  $emolumentoFolha2=['folha_de_prova2'];

              
                  $emolumento_confirmacao  = EmolumentCodeV($emolumento[$index],$lectiveYearSelected[0]->id); //adicionar emolumento de confirmação da matricula
                  $emolumento_folha_de_prova = EmolumentCodeV($emolumentoFolha,$lectiveYearSelected[0]->id); //adiconar emolumento folha de prova 1 semestre
                  $emolumento_folha_de_prova2 = EmolumentCodeV($emolumentoFolha2,$lectiveYearSelected[0]->id); // adicionar emolumento folha de prova 2 semestre
               
               if($emolumento_confirmacao->isEmpty()){

                    Toastr::warning(__('A forLEARN não encontrou um emolumento [Pré-matrícula ou confirmação de matrícula configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->route('matriculations.index');
                }
               if($emolumento_folha_de_prova->isEmpty()){

                    Toastr::warning(__('A forLEARN não encontrou um emolumento [Folha de prova 1º(Semestre) configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->route('matriculations.index');
                }
                if($emolumento_folha_de_prova2->isEmpty()){

                    Toastr::warning(__('A forLEARN não encontrou um emolumento [Folha de prova 2º(Semestre) configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->route('matriculations.index');
                }


                $r1       = createAutomaticArticleRequest($user->id, $emolumento_confirmacao[0]->id_emolumento, null, null);
                $reqFolha = createAutomaticArticleRequest($user->id, $emolumento_folha_de_prova[0]->id_emolumento, null, null);
                $reqFolha2 = createAutomaticArticleRequest($user->id, $emolumento_folha_de_prova2[0]->id_emolumento, null, null);

    
                if (!$r1) {
                   throw new Exception('Could not create automatic [Confirmação de matrícula ()] article request payment for student (id: ' . $user->id . ') matriculation');
                }
                
    
                if (!$reqFolha) {
                   throw new Exception('Could not create automatic [Folha de prova ()] article request payment for student (id: ' . $user->id . ') matriculation');
                }
                
    
                //Apenas cria o emolumento do cartão se o ano lectivo for o 1 ano
                if($maxSelectedYear==1){
                    // Emissão de Cartão de Estudante 
                     $article_cartao=DB::table('articles')->where('id_code_dev',14)->where('anoLectivo',$lectiveYearSelected[0]->id)->first();
                     $r2 =createAutomaticArticleRequest($user->id, $article_cartao->id, null, null);
                     if (!$r2) {
                        throw new Exception('Could not create automatic [Emissão de Cartão de Estudante (id: 31)] article request payment for student (id: ' . $user->id . ') matriculation');
                     }
                      $articleRequets[$r2]['updatable'] = false;
                      //gerar validade 
          event(new PaidStudentCardEvent($user->id));
                 }

                //Pagamento de Propina
                $articlePropinaId = null;
                $courseID = $user->courses()->first()->id;

               $courseData = Course::where('id', $courseID)
                ->with([
                    'studyPlans' => function ($q) use ($maxSelectedYear) {
                        $q->with([
                            'study_plans_has_disciplines' => function ($q) use ($maxSelectedYear) {
                                $q->where('years', $maxSelectedYear);
                                $q->with('discipline');
                            }
                        ]);
                    },
                ])
                ->first();
                
                
              
                
                $curricularYearAllDisciplinesCount = $courseData ? $courseData->studyPlans->study_plans_has_disciplines->count() : 0;
                $curricularYearSelectedDisciplinesCount = count($allDisciplinesInCurricularYear);

                $currentYearToValidate = $maxSelectedYear;
                
            // return  $courseID;
    
                if ($courseData && $courseID === 25 && $currentYearToValidate > 2) {
                       
                    $specializationCode = null;

                    $courseData = Course::where('id', $courseID)
                    ->with([
                        'studyPlans' => function ($q) use ($maxSelectedYear) {
                            $q->with([
                                'study_plans_has_disciplines' => function ($q) {
                                    $q->with('discipline');
                                }
                            ]);
                        },
                    ])
                    ->first();



                    while ($currentYearToValidate > 2) {
                        if (collect($request->get('years'))->contains((string)$currentYearToValidate)) {
                            $disciplineGlobalCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                            $disciplineSelectedCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                        $specializationCodeForTheYear = null;

                            foreach ($courseData->studyPlans->study_plans_has_disciplines as $spDiscipline) {
                                if ($spDiscipline->years === $currentYearToValidate) {
                                    $code = substr($spDiscipline->discipline->code, 0, 3);
                                    if (isset($disciplineGlobalCount[$code])) {
                                        ++$disciplineGlobalCount[$code];
                                        if (in_array((string)$spDiscipline->discipline->id, $disciplineByYear[$currentYearToValidate], true)) {
                                            ++$disciplineSelectedCount[$code];
                                        }
                                    }
                                }
                            }

                            $courseBranchesWithSelectedDisciplines = array_filter($disciplineSelectedCount, function ($item) {
                                return $item;
                            });
                            $specializationCodeForTheYear = array_key_first($courseBranchesWithSelectedDisciplines);

                            if ($currentYearToValidate === $maxSelectedYear) {
                                $specializationCode = $specializationCodeForTheYear;
                                $curricularYearAllDisciplinesCount = $disciplineGlobalCount[$specializationCode];
                            }

                            $differentSpecializationsBetweenYears = $specializationCodeForTheYear !== $specializationCode;
                            $notOneSpecializationSelected = count($courseBranchesWithSelectedDisciplines) !== 1;

                            if ($notOneSpecializationSelected || $differentSpecializationsBetweenYears) {
                                return redirect()->back()->withErrors(['Disciplinas de especialidades selecionadas de forma inválida.'])->withInput();
                            }
                        }
                
                        --$currentYearToValidate;
                    }
                    $emolumentoEsP=['gee','coa','eco'];
                    switch ($specializationCode) {
                     case 'GEE':
                         
                        $emolumento_Especialidade  = EmolumentCodeV($emolumentoEsP[0],$lectiveYearSelected[0]->id);
                        if($emolumento_Especialidade->isEmpty()){
                            Toastr::warning(__('A forLEARN não encontrou um emolumento da especialidade [ GEE configurado no ano lectivo selecionado].'), __('toastr.warning'));
                             return redirect()->route('matriculations.index');
                         }
         
                        $articlePropinaId = $emolumento_Especialidade[0]->id_emolumento;
                      
                         
                     
                        break;
                    case 'COA':
                        
                        $emolumento_Especialidade  = EmolumentCodeV($emolumentoEsP[1],$lectiveYearSelected[0]->id);
                        if($emolumento_Especialidade->isEmpty()){
                             Toastr::warning(__('A forLEARN não encontrou um emolumento da especialidade [ COA configurado no ano lectivo selecionado].'), __('toastr.warning'));
                             return redirect()->route('matriculations.index');
                         }

                        $articlePropinaId = $emolumento_Especialidade[0]->id_emolumento;
                        
                      
                        break;
                    case 'ECO':
                        $emolumento_Especialidade  = EmolumentCodeV($emolumentoEsP[2],$lectiveYearSelected[0]->id);
                        if($emolumento_Especialidade->isEmpty()){
                              Toastr::warning(__('A forLEARN não encontrou um emolumento da especialidade [ ECO configurado no ano lectivo selecionado].'), __('toastr.warning'));
                              return redirect()->route('matriculations.index');
                         }

                        $articlePropinaId = $emolumento_Especialidade[0]->id_emolumento;
                      
                        break;
                
                }

        
                        
                }             
       
        $estudante_so_cadeirante = false;
          //tratar Para as propinas geral sempre os meses do ano corrente
            //Desde outubro a Julho (21 - 22)
            //para o ano vai ser  outubro a Julho (22-23) 
            $First_year=explode ("-",$lectiveYearSelected[0]->start_date);
            $End_year=explode  ("-",$lectiveYearSelected[0]->end_date);
            $anoFirst=$First_year[0]; 
            $anoEnd=$End_year[0]; 

           
        if ($curricularYearAllDisciplinesCount === $curricularYearSelectedDisciplinesCount) {
            

            
        if (!$articlePropinaId) {      
            
                    if($request->course_change) {
                        //validar se existe mudança de curso 
                         $courseID = $this->mudarCurso($user->id,0) ; 
                       //Fim da mudança de curso
                      }
            
         $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID,$lectiveYearSelected) {
            $q->where('course_id', $courseID);
            $q->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);
         })->first();
         
         $articlePropinaId = $articlePropina->id;  
       }
    

          
         
             //Propinas de todo ano 
             $r3 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst, 10);
             $r4 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst, 11);
             $r5 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst, 12);
             $r6 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 01);
             $r7 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 02);
             $r8 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 03);
             $r9 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 04);
             $r10 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 05);
             $r11 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 06);
             $r12 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 07);
            //Fim das propinas todos
            
            if (!$r3) {
                throw new Exception('Could not create automatic [Pagamento de Propina (id: ' . $articlePropinaId . ')] article request payment for student (id: ' . $user->id . ') matriculation');
            }
            $articleRequets[$r3]['updatable'] = false;
             } 
            
        else {
   
                // Cadeiras em atrare
                
                  
                        /* Se o estudante for repetente do ano X e tiver cadeiras em um ou mais anos anteriores, deve-se gerar emolumentos das disciplinas
                        em atraso dos anos anteriores 
                        */
                        $First_year=explode ("-",$lectiveYearSelected[0]->start_date);
                        $End_year=explode  ("-",$lectiveYearSelected[0]->end_date);
                        $anoFirst=$First_year[0]; 
                        $anoEnd=$End_year[0]; 
                
                        $allDisciplinesInCurricularYear = collect($allDisciplinesInCurricularYear);
           

                 $disciplinas = Course::where('id', $courseID)
                ->with([
                    'studyPlans' => function ($q) use ($allDisciplinesInCurricularYear) {
                        $q->with([
                            'study_plans_has_disciplines' => function ($q) use ($allDisciplinesInCurricularYear) {
                                $q->whereIn('disciplines_id',  collect($allDisciplinesInCurricularYear)->keys()->toArray());
                                $q->with('discipline');
                            }
                        ]);
                    },
                ])
                ->first();
 
                $first_sem =  $disciplinas->studyPlans->study_plans_has_disciplines->filter(function($item) {
                    return $item->discipline_periods_id == 1;
                        });

                $second_sem =  $disciplinas->studyPlans->study_plans_has_disciplines->filter(function($item) {
                            return $item->discipline_periods_id == 4;
                                });

                // pegar total primeiro semestre

                 $article_Frequencia = DB::table('articles')->where('id_code_dev',5)->where('anoLectivo',$lectiveYearSelected[0]->id)->first();
                 $t_p_sem = $first_sem->count() * $article_Frequencia->base_value;
                 $t_s_sem = $second_sem->count() * $article_Frequencia->base_value;

                                // verificar se é bolseiro ep pegar valor
                                $bolsa = DB::table('scholarship_holder as sh')
                                ->join('scholarship_entity as se', 'se.id', 'sh.scholarship_entity_id')
                                ->join('artcles_rules as ar', 'ar.scholarship_entity_id', 'se.id')
                                ->where('sh.user_id', $user->id)
                                ->where('se.type', 'PROTOCOLO')
                                ->whereNotNull('ar.scholarship_entity_id')
                                ->where('sh.are_scholarship_holder', 1)
                                ->whereNotNull('ar.id_articles')
                                ->whereNull('se.deleted_at')
                                ->whereNull('ar.deleted_at')
                                ->select(['ar.valor'])
                                ->first();

                            if(isset($bolsa)){
                                $prop = $bolsa->valor;
                            }
                            else{

                                foreach ($request->get('classes') as $year => $class) {
                                    if ($year == $maxSelectedYear) {
                                        $classe = $class;
                                    }
                                }

                                $classe = DB::table('classes')->where('id', $classe)->first();

                                // verificar oitros descontos 
                                $desconto = DB::table('artcles_rules as ar')
                                ->whereNull('ar.scholarship_entity_id')
                                ->whereNotNull('ar.id_articles')
                                ->whereNull('ar.deleted_by')
                                ->whereNull('ar.deleted_at')
                                ->join('articles', function($join) use ($lectiveYearSelected){
                                    $join->on('articles.id', 'ar.id_articles')
                                        ->whereBetween('articles.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);
                                })
                                ->join('article_monthly_charges as a', function($join) use ($courseID){
                                    $join->on('a.article_id', 'articles.id')
                                    ->where('course_id', $courseID);
                                })
                                ->where('ar.ano_curricular', $maxSelectedYear)
                                ->where('schedule_type_id', $classe->schedule_type_id)
                                ->select(['ar.valor'])
                                ->first();

                                if(isset($desconto)){
                                    $prop = $desconto->valor;
                                }
                                else{
                                    $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID,$lectiveYearSelected) {
                                        $q->where('course_id', $courseID);
                                        $q->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);
                                     })->first();

                                    $prop =  $articlePropina->base_value;
                                }

                            }
                          
                            if(isset($prop) && $t_p_sem >= $prop){
                               

                                // remove-las do array
                               
                                 
                                $left = $allDisciplinesInCurricularYear->keys()->diff($first_sem->pluck('disciplines_id')->toArray());

                               
                              
                                  
                                $allDisciplinesInCurricularYear = $allDisciplinesInCurricularYear->map(function($item, $key) use ($left){
                                   
                                    foreach($left as $l){

                                       if($key == $l)
                                       return $item;
                                      }
                                   
                                  });

                                  $allDisciplinesInCurricularYear = $allDisciplinesInCurricularYear->filter(function($item){
                                   
                                    return $item === false;
                                  });
                                  
                                 

                                if($request->course_change) {
                                    //validar se existe mudança de curso 
                                     $courseID = $this->mudarCurso($user->id,0) ; 
                                   //Fim da mudança de curso
                                  }
            
                                $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID,$lectiveYearSelected) {
                                    $q->where('course_id', $courseID);
                                    $q->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);
                                }) ->first();
            
                                $articlePropinaId = $articlePropina->id;

                                 //propina primeiro semestre
                                $r3 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst,10);
                                $r4 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst, 11);
                                $r5 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoFirst, 12);
                                $r6 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 01);
                                $r7 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 02);
                            }
                            else if(isset($prop) && $t_p_sem < $prop){
                              
                                $estudante_so_cadeirante = true;
                            }
                            if(isset($prop) && $t_s_sem >= $prop){
                                // remove-las do array
                              
                                 
                                $left = $allDisciplinesInCurricularYear->keys()->diff($second_sem->pluck('disciplines_id')->toArray());

                                
                               
                                  
                                  $allDisciplinesInCurricularYear = $allDisciplinesInCurricularYear->map(function($item, $key) use ($left){
                                   
                                    foreach($left as $l){

                                       if($key == $l)
                                       return $item;
                                      }
                                   
                                  });

                                  $allDisciplinesInCurricularYear = $allDisciplinesInCurricularYear->filter(function($item){
                                   
                                    return $item === false;
                                  });

                                 
                             
                                //propina primeiro semestre

                                if($request->course_change) {
                                    //validar se existe mudança de curso 
                                     $courseID = $this->mudarCurso($user->id,0) ; 
                                   //Fim da mudança de curso
                                  }
            
                                $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID,$lectiveYearSelected) {
                                    $q->where('course_id', $courseID);
                                    $q->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);
                                }) ->first();
            
                                $articlePropinaId = $articlePropina->id;

                                $r8 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 03);
                                $r9 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 04);
                                $r10 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 05);
                                $r11 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 06);
                                $r12 = createAutomaticArticleRequest($user->id, $articlePropinaId, $anoEnd, 07);
                            }
                            else if(isset($prop) && $t_s_sem < $prop){
                                $estudante_so_cadeirante = true;
                            }

                            $allDisciplinesInCurricularYear = $allDisciplinesInCurricularYear->toArray();
                            $allDisciplinesOffCurricularYear = array_replace($allDisciplinesOffCurricularYear,$allDisciplinesInCurricularYear);


                        }
                       
 
            if(count($allDisciplinesOffCurricularYear) > 0){
                foreach ($allDisciplinesOffCurricularYear as $offDiscipline => $examOnly) {
                   
                    if ($examOnly) {
                       //Inscrição Por Exame Cadeira Em Atraso  com o ano lectivo
                        $article_Exame=DB::table('articles')->where('id_code_dev',4)->where('anoLectivo',$lectiveYearSelected[0]->id)->first();
                        $r4 =createAutomaticArticleRequestExame($user->id, $article_Exame->id,null,null, $offDiscipline);
                   
                        $group = [
                            'discipline_id' => $offDiscipline,
                            'article_request_id' => $r4,
                            'user_id' => $user->id
                        ];
                        DisciplineArticle::insert($group);
                        if (!$r4) {
                         throw new Exception('Could not create automatic [Inscrição Por Exame Cadeira Em Atraso (id: 41)] article request payment for student (id: ' . $user->id . ') matriculation');
                        }
                        $articleRequets[$r4]['updatable'] = true;
                        }


                else {


                        

                        //Pega as disciplina  
                       
                        $qtd_disciplina= count($allDisciplinesOffCurricularYear);
                        $disciplinaCode=DB::table('disciplines')->whereId($offDiscipline)->get(); 
                        $periodo=substr($disciplinaCode[0]->code,-3, 1);
                        $ano = substr($disciplinaCode[0]->code,-4,1);
                        //Verificar o código da disciplina e ver se é relacoes internacionais.
                        $p=substr($disciplinaCode[0]->code,0,2) =="RI" ? $p=substr($disciplinaCode[0]->code,0,2) : $p=substr($disciplinaCode[0]->code,0,3);
                       
                        $codigoDisc=$p;
                        if($codigoDisc!="RI") { 
                            // Inscrição Por Frequência Cadeira Em Atraso
                            $article_Frequencia=DB::table('articles')->where('id_code_dev',5)->where('anoLectivo',$lectiveYearSelected[0]->id)->first();
                          
                            if(($periodo=="1" && !$estudante_so_cadeirante) || ($periodo=="1" && $estudante_so_cadeirante && $ano != $maxSelectedYear)){
                              
                              $f1 = createAutomaticArticleRequestFrequencia($user->id, $article_Frequencia->id, $anoFirst, 10,$offDiscipline);
                              
                              //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                            $group = [
                                'discipline_id' => $offDiscipline,
                                'article_request_id' => $f1,
                                'user_id' => $user->id
                            ];

                            DisciplineArticle::insert($group);
        
                            if ((isset($f1) && !$f1) || (isset($f2) && !$f2)) {
                                throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                            }
                           $articleRequets[$f1]['updatable'] = true;
                           
                            }
                            if(($periodo=="2" && !$estudante_so_cadeirante) || ($periodo=="2" && $estudante_so_cadeirante && $ano != $maxSelectedYear)){
                                $f2 = createAutomaticArticleRequestFrequencia($user->id, $article_Frequencia->id, $anoEnd, 03, $offDiscipline);
                                
                                //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                            $group = [
                                'discipline_id' => $offDiscipline,
                                'article_request_id' => $f2,
                                'user_id' => $user->id
                            ];

                            DisciplineArticle::insert($group);
        
                            if ((isset($f2) && !$f2)) {
                                throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                            }
                            $articleRequets[$f2]['updatable'] = true;
                            
                            }
           
                             if($periodo=="1" && $estudante_so_cadeirante && $ano == $maxSelectedYear){


                              
                             $primeiro_semestre = [10, 11, 12, 01, 02];
                             
                             for($i = 0; $i < count($primeiro_semestre); $i++){
                                
                              $ano =  strpos(sprintf('%02d', $primeiro_semestre[$i]), '1') === 0 ? $anoFirst : $anoEnd;
                              
                              $f1 = createAutomaticArticleRequestFrequencia($user->id, $article_Frequencia->id, $ano, $primeiro_semestre[$i], $offDiscipline);
                             
                            //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                            $group = [
                                'discipline_id' => $offDiscipline,
                                'article_request_id' => $f1,
                                'user_id' => $user->id
                            ];

                            DisciplineArticle::insert($group);
        
                            if ((isset($f1) && !$f1) || (isset($f2) && !$f2)) {
                                throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                            }
                           $articleRequets[$f1]['updatable'] = true; 
                                 
                                 
                             }
                             
                            }
                            if($periodo=="2" && $estudante_so_cadeirante && $ano == $maxSelectedYear){
                              
                             $segundo_semestre = [03, 04, 05, 06, 07];
                             
                             for($i = 0; $i < count($segundo_semestre); $i++){
                                
                              $ano =  strpos((string)$segundo_semestre[$i], '1') === 0 ? $anoFirst : $anoEnd;  
                              $f2 = createAutomaticArticleRequestFrequencia($user->id, $article_Frequencia->id, $ano, $segundo_semestre[$i], $offDiscipline);
                             
                            //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                            $group = [
                                'discipline_id' => $offDiscipline,
                                'article_request_id' => $f2,
                                'user_id' => $user->id
                            ];

                            DisciplineArticle::insert($group);
        
                            if ((isset($f2) && !$f2)) {
                                throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                            }
                            $articleRequets[$f2]['updatable'] = true; 
                                 
                                 
                             }
                            }
                          
                            
                        }
                    }
                         
           

                }

            }

 
                $matriculation->articleRequests()->sync($articleRequets);

                if($request->course_change) {
                        //validar se existe mudança de curso 
                         $courseID = $this->mudarCurso($user->id,2) ; 
                       //Fim da mudança de curso
                      }
                      
                      
            

                DB::commit();
                
                
                // Success message
                Toastr::success(__('Users::matriculations.store_success_message'), __('toastr.success'));
                return redirect()->route('matriculations.index');

             
            } 
            else {
                //Error message (total de vagas excedidos)
                return "Total de cenas";
                Toastr::error(__('Total de vagas excedidas para esta turma'), __('toastr.error'));
                return redirect()->route('matriculations.index');
             }
          } 
      
        catch (Exception | Throwable $e) {
           
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    







private function mudarCurso($id_user,$flag_article){
    $currentData = Carbon::now();
   //Curso actual do estudante

   $curso_user= DB::table('user_courses')
   ->where('users_id',$id_user)
   ->first();

   //Curso para mudar do actual<->Novo curso 
   $curso_user_for_change= DB::table('tb_courses_change')
   ->where('course_id_primary',$curso_user->courses_id)
   ->where('status',1)
   ->first();


   if($flag_article==0){
   //Retornar apenas o 
   return $curso_user_for_change->course_id_secundary;

   }
   //Muda de curso 
   $mudar_curso = DB::table('user_courses')
   ->where('users_id',$id_user)
   ->update(['courses_id' => $curso_user_for_change->course_id_secundary]);

   //Guardar histótico do curso Passado
   $historic_curso_change = DB::table('tb_user_change_course')->updateOrInsert(
       [
            'user_id' => $id_user
       ],
       [
       'old_course_id' => $curso_user->courses_id,
       'new_course_id' => $curso_user_for_change->course_id_secundary,
       'created_by' => Auth::user()->id,
       'updated_by' => Auth::user()->id,
       'created_at' => $currentData,
       'updated_at' => $currentData 
       ]

       ); 
       
    }
































































//metodo para meter os emolumentos de propinas que faltam 
public function colocar_emolumento($id_user){
  //Propinas de todo ano //
  //return $id_user      //
  $r4 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y'), 11);
  $r5 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y'), 12);
  $r6 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,01);
  $r7 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,02);
  $r8 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,03);
  $r9 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,04);
  $r10 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,05);
  $r11 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,06);
  $r12 = createAutomaticArticleRequest($id_user, 80, Carbon::now()->format('Y')+1,07);
  //Fim das propinas todos


}





    //Formulario de Rotina
    public function formulario_rotina(){
        //aNO LECTIVO
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
    //PEGAR OS CURSOS PARA SELECT//
    $cursos=DB::table('courses as cti')
    ->leftJoin('courses_translations as cta', function ($join) {
                    $join->on('cta.courses_id', '=', 'cti.id');
                    $join->on('cta.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('cta.active', '=', DB::raw(true));
                })
    ->select(['cti.code','cti.id','cta.display_name'])
    ->whereNull('cti.deleted_at')
    ->get();
    //PEGAR OS EMOLUMENTOS PARA PROPINAS ANO RECENTE//
    $anoRecente = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
    ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
    ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
    ->leftJoin('article_translations as at', function ($join) {
        $join->on('at.article_id', '=', 'articles.id');
        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('at.active', '=', DB::raw(true));
    })
    ->select([
        'articles.*',
        'u1.name as created_by',
        'u2.name as updated_by',
        'u3.name as deleted_by',
        'at.display_name',
    ])
    ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
    ->orderBy('at.display_name','asc')
    ->get()  
    ->map(function($item){
        $propina=explode(" -",$item->display_name);
        if ($propina[0]=="Propina") {
        return $item;
        }  else{

        }
    });




    //Ano Passado 
    $lectiveYearSelectedP = DB::table('lective_years')
    ->where('id',6)
    ->first();

    $anoPassado = Article::join('users as u1', 'u1.id', '=', 'articles.created_by')
    ->leftJoin('users as u2', 'u2.id', '=', 'articles.updated_by')
    ->leftJoin('users as u3', 'u3.id', '=', 'articles.deleted_by')
    ->leftJoin('article_translations as at', function ($join) {
        $join->on('at.article_id', '=', 'articles.id');
        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('at.active', '=', DB::raw(true));
    })
    ->select([
        'articles.*',
        'u1.name as created_by',
        'u2.name as updated_by',
        'u3.name as deleted_by',
        'at.display_name',
    ])
    ->whereBetween('articles.created_at', [$lectiveYearSelectedP->start_date, $lectiveYearSelectedP->end_date])
    ->orderBy('at.display_name','asc')
    ->get()
        ->map(function($item){
            $propina=explode(" -",$item->display_name);
            if ($propina[0]=="Propina") {
                return $item;
            }
            else{

            
            }

            });

        $old_emu=collect($anoPassado);
        $New_emu=collect($anoRecente);


        return view("Users::confirmations-matriculations.Rotina_form",compact('cursos','New_emu','old_emu'));
        
    }




    public function actualizar_emulumento(Request $request){

        //Separar o ID do valor
        $id_no_emolumento=explode(",",$request->emulumento_novo);
        $id_emol= $id_no_emolumento[0];
        $valor_novo= $id_no_emolumento[1];

        //Ano lectivo
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();

        //Pegars os matriculados no respetivo curso no primeiro aano no 
        //ano Lective 21/22
        $matriculado_user=DB::table('users as u_m')
            ->join('matriculations as mt','mt.user_id','u_m.id')
            ->join('user_courses as cm','cm.users_id','u_m.id')
            ->leftJoin('courses_translations as cta', function ($join) {
                $join->on('cta.courses_id', '=', 'cm.courses_id');
                $join->on('cta.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('cta.active', '=', DB::raw(true));
            })
            ->select(['u_m.id','u_m.name','mt.code','mt.course_year','cta.display_name'])
            ->whereNull('mt.deleted_at')
        
            ->where('mt.course_year',1)
            ->where('cm.courses_id',$request->id_curso)
            ->whereBetween('mt.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            ->get();

        //Pegar todos ID dos matriculados na consulta anterior 
        //e passar no loop .  
        $id_matriculado=[];
        foreach($matriculado_user as $item){
        $id_matriculado[]=$item->id ;
        }   
        // Pegar os dados das transições dos matriculados apenas referente aos meses:
        $Pegar_dados_transacao=DB::table('article_requests as ART')
            ->join('transaction_article_requests as TRS','TRS.article_request_id','ART.id')
            ->join('users as u_t','u_t.id','ART.user_id')
            ->select(['u_t.name','ART.*', 'TRS.transaction_id as id_transicao','TRS.value','TRS.article_request_id as id_article_request'])
            ->whereIn('ART.user_id' ,$id_matriculado)
            ->where('ART.article_id',$request->emulumento_antigo)
            ->whereNotNull('ART.month')
            ->get();

        if (!$Pegar_dados_transacao->isEmpty()) { 

            foreach($Pegar_dados_transacao as $item){         
            DB::transaction(function  () use($id_emol,$valor_novo,$item,$currentData) { 
                    //Actualizar os emulomentos   
                    $transacao=DB::table('article_requests as ART')
                    ->where('ART.id',$item->id)
                    ->whereNotNull('ART.month')
                    ->update(['base_value' =>$valor_novo ,'article_id'=>$id_emol,'updated_at'=> $currentData]);
                    // Actualizao trazacao article
                    $transacao_article=DB::table('transaction_article_requests as TRC')
                    ->where('TRC.article_request_id', $item->id)
                    ->update(['value' =>$valor_novo]);
            });

            }
                    return "Sucesso ao actualizar os emolumentos"; 

            }

            else{

                    return "AVISO: O Emolumento selecionado não faz referencia ao curso Ou Não existe nenhum emolumento para ser actualizado ."; 

        }

    // Toastr::success(__('Emolumentos actualizados com sucesso.'), __('toastr.success'));
    // return redirect()->route('formulario_rotina');

    }

    public function testeAlunos(){

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $lectiveYearSelected = $lectiveYearSelected ?? DB::table('lective_years')
        ->where('lective_years.id', 11)
        ->first();

        $Id_Matriculados_confirmados = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [6]);
            })
            ->whereHas('courses')
            ->whereHas('matriculation')
            // ->doesntHave('matriculation')
            ->with(['parameters' => function ($q) {
            $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->join('matriculations as u_cand' ,'u_cand.user_id','=','users.id')
            ->whereBetween('u_cand.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            //->whereBetween('users.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            // ->leftJoin("matriculations as mt", 'user.id','=','mt.user_id')
            ->where('u_cand.course_year','!=',1)
            ->select(['u_cand.*','users.*'])
            ->get() 
            ->map(function ($user) {
                return ['id' => $user->id];
            });
        
        //  $Id_Matriculados_confirmados;
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [6]);
        })
            ->whereHas('courses')
            ->whereHas('matriculation')
            // ->doesntHave('matriculation')
            ->with(['parameters' => function ($q) {
            $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->whereNotin('users.id',$Id_Matriculados_confirmados)
            ->select(['users.*'])
            ->get()
            ->map(function ($user) {
                $displayName = $this->formatUserName($user);
                return ['id' => $user->id, 'Ano'=>$user->course_year, 'display_name' => $displayName];
        });

    }









 protected function studentsWithCourseAndMatriculationSelectList()
    {
        
        
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
          ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
          ->first();
          
        $lectiveYearSelected = $lectiveYearSelected ?? DB::table('lective_years')
          ->where('lective_years.id', 9)
          ->first();
           

          
 
        $Id_Matriculados_confirmados = DB::table('matriculations as mat')
         ->join('users','mat.user_id','=','users.id')
         ->select(['mat.id as id_matricula','users.id','users.name'])
         ->whereNull('mat.deleted_at')
         ->where('mat.lective_year', $lectiveYearSelected->id)
         ->where('mat.course_year','!=',1)
         ->get()
         ->map(function ($user) {
             return ['id' => $user->id];
         });
 
  
      
     $users = User::whereHas('roles', function ($q) {
             $q->whereIn('id', [6]);
         })
             ->whereHas('courses')
             ->whereHas('matriculation')
             // ->doesntHave('matriculation')
             ->with(['parameters' => function ($q) {
              $q->whereIn('code', ['nome', 'n_mecanografico']);
             }])
            //  ->whereNotin('users.id',$Id_Matriculados_confirmados)
             ->select(['users.*'])
             ->get()
             ->map(function ($user) {
                 $displayName = $this->formatUserName($user);
                 return ['id' => $user->id, 'display_name' => $displayName];
             });
 
            
        //trazer com notas de exame de acesso.
          $candidates = User::whereHas('roles', function ($q) {
                        $q->whereIn('id', [15]);
                    })
                        // ->whereHas('courses')
                        // //->whereHas('matriculation')
                         ->with(['parameters' => function ($q) {
                          $q->whereIn('code', ['nome', 'n_mecanografico']);
                         }])
                        ->whereBetween('users.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->leftJoin('user_candidate as u_cand' ,'u_cand.user_id','users.id')
                        ->leftJoin('article_requests', 'article_requests.user_id', '=', 'u_cand.user_id')
                        ->join('exame_candidates_status as status_exame_candidate' ,'status_exame_candidate.user_id','users.id')
                        ->where('article_requests.status','total')
                        ->where('status_exame_candidate.status' ,'1')
                        ->select(['u_cand.*','users.*'])
                        ->get()
                        ->map(function ($user) {
                            $displayName = $this->formatUserName($user);
                            return ['id' => $user->id, 'display_name' => $displayName];
                        });
    
          
              //Estudantes por Equivalência

              $equivalente_studant = User::whereHas('roles', function ($q) {
                    $q->whereIn('id', [6]);
                })
                    ->whereHas('courses')
                    ->whereDoesntHave('matriculation')
                    ->with(['parameters' => function ($q) {
                      $q->whereIn('code', ['nome', 'n_mecanografico']);
                    }])
                    
                    ->join('tb_transference_studant as transf' ,'transf.user_id','users.id')
                    ->leftJoin('article_requests', 'article_requests.user_id', '=', 'transf.user_id')
                    ->where('article_requests.status','total')
                    
                    ->where('transf.type_transference',1)
                    ->where('transf.status_disc',1)

                    ->select(['transf.*','users.*'])
                    ->get()
                    ->map(function ($user) {
                        $displayName = $this->formatUserName($user);
                        return ['id' => $user->id, 'display_name' => $displayName];
                    });
                    
                    //Importados estudantes
                  $StudentImported = User::whereHas('roles', function ($q) {
                        $q->whereIn('id', [6]);
                    })
            
                        ->whereHas('courses')
                        ->whereDoesntHave('matriculation')
                        ->with(['parameters' => function ($q) {
                            $q->whereIn('code', ['nome', 'n_mecanografico']);
                        }])
            
                        ->join('Import_data_forlearn as imp', 'imp.id_user', 'users.id')
                        ->join('new_old_grades as percurso', 'percurso.user_id', 'users.id')
                        ->where('imp.type_user', 1)
            
                        ->select(['imp.*', 'users.*'])
                        ->distinct()
                        ->get()
                        ->map(function ($user) {
                            $displayName = $this->formatUserName($user);
                            return ['id' => $user->id, 'display_name' => $displayName];
                        });

                        $usuarios = collect();

                        if (!$users->isEmpty()) {
                            $usuarios = $usuarios->concat($users);
                        }
                        
                        if (!$candidates->isEmpty()) {
                            $usuarios = $usuarios->concat($candidates);
                        }

                        if (!$StudentImported->isEmpty()) {
                            $usuarios = $usuarios->concat($StudentImported);
                        }
                        
            
            
        
          


        return $usuarios->sortBy(function ($item) {
            return strtr(
                utf8_decode($item['display_name']),
                utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
            );
        });
        
        
          
        
    }


    protected function formatUserName($user)
    {   

        $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
        $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
        $fullNameParameter->pivot->value : $user->name;
        //Pegar o número de candidado ao estudante CE 
        
        $outher_number= $user->code ? $user->code: "000";

        $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
        $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
        $studentNumberParameter->pivot->value :$outher_number ;
        //  
        return "$fullName #$studentNumber ($user->email)";
    }               





    private function changeState($type, $userId, $maxSelectedYear, $courseID)
    {
        if ($type == "STORE") {
            //Avaliar apenas se ele esta a ser matriculado no ultimo ano do curso - se estiver a ser matriculado, mudar o estado para finalista
            //$yearsMatriculated = Matriculation::whereUserId($userId)->get();
            $maxCourseYear = Course::whereId($courseID)->firstOrFail();

            //se o ano em que ele esta matriculado for igual ao maior do curso. (ex: se estiver no 5 ano e o curso tem 5 anos.)
            if ($maxCourseYear->duration_value == $maxSelectedYear) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 19, 'courses_id' => null] //19 => Finalista
                );
                UserStateHistoric::create([
                                    'user_id' => $userId,
                                    'state_id' => 19
                                ]);
            } 
        } elseif ($type == "CHANGE") {
            $user_state = UserState::whereUserId($userId)->first();

            if (!$user_state == null && $user_state->state_id == 9) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 7]
                );
                UserStateHistoric::create([
                        'user_id' => $userId,
                        'state_id' => 7
                    ]);
            }
        }
    }
}
