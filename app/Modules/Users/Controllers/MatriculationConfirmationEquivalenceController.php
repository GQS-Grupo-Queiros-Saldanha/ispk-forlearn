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

use Illuminate\Support\Facades\Auth;
class MatriculationConfirmationEquivalenceController extends Controller
{
   
    public function index()
    {
     return "ola";
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

            return view('Users::confirmations-matriculations-equivalence.confirmation')->with($data);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
           
        }
    }




    private function equivalence_Student($studentId,$anoLEctivo){
        
        //Pegar ano lectivo corrente.
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
            /** Confirmar presença na equivalência lista **/
            $transfere_studant = User::query()
            ->whereHas('roles', function($q) {
                $q->where('id', '=', 6);
            })
            ->whereDoesntHave('matriculation')
            ->join('tb_transference_studant as transf', 'users.id', '=', 'transf.user_id')
            ->where('transf.user_id',$studentId)
            ->whereIn('transf.type_transference',[1, 3])
            ->whereBetween('transf.created_at', [$anoLEctivo->start_date, $anoLEctivo->end_date])
            ->where('transf.status_disc',1)
            ->first();
          
            if(!$transfere_studant){
                return 0;
            }

            
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
            ->where('grade', '>=', '10')
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
            //Para ser confirmada porque identifica que falta outras cadeiras para o aluno fazer.
            $curricularPlanDisciplines_dados=$curricularPlanDisciplines;
            $curricularPlanDisciplines=[ 4=>count($curricularPlanDisciplines)>1 &&  ($equivalencia['year'] + 1)==4 &&  $code_curso[0]->code=="CEE" ? $curricularPlanDisciplines[$equivalencia['year'] + 1]:0 ];
            
           
                $curricularPlanDisciplines_dados = collect($curricularPlanDisciplines_dados);
                $curricularPlanDisciplines_dados =   $curricularPlanDisciplines_dados->merge($equivalencia['disciplineReprovado'])
                ->flatten()->unique()->groupBy('years')->sortKeys();
               
    
            //Grabber
            $data = [
                    'curricularPlanDisciplines' => $curricularPlanDisciplines[4]===0?   $curricularPlanDisciplines_dados:$curricularPlanDisciplines,
                    'classes' => $classes,
                    'estado'=> $equivalencia['Estado'],
                    'nextYear' => $equivalencia['year'] + 1,
                    'disciplinesReproved' =>  $equivalencia['disciplineReprovado'],
                    'info'=>$info??"",
                    'probableYear'=> end($years)
                ];

           
        
        return $data;
    }





    private function Imported_Student($studentId, $anoLEctivo)
    {

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





    public function ajaxUserData($studentId)
    {       
     try{

       
       $info="";
       $student=explode(",",$studentId);
       $studentId=$student[0];
       $anoLEctivo= $student[1];


      $currentData = Carbon::now();
      $lectiveYearSelected = DB::table('lective_years')
      ->where("id",$anoLEctivo)
      ->first();

      $lectiveYearSelected = $lectiveYearSelected ?? DB::table('lective_years')
        ->where('lective_years.id', 6)
        ->first();

     //variaveis super importante
      $data =[];
    
       
        
    $data=$this->equivalence_Student($studentId,$lectiveYearSelected);
    if($data!=0){
        $data;
        $view = view("Users::confirmations-matriculations-equivalence.disciplines_equivalencia")->with($data)->render();
        return response()->json(array('html' => $view));
    }
                            

    
       } catch (Exception | Throwable $e) {
         return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        


    }

    public function store(request $request)
    {
       
     
        try {
    
            //  return $request;
                
            //Ano lectivo 
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
            ->where('id',$request->anoLective)
            ->get();

            

       
          $user_student = User::whereId($request->user)->first();
          $id_curso=$user_student->courses()->first()->id;
   
      
            
          
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
          


            DB::beginTransaction();

            $user = User::findOrFail($request->get('user'));
    
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
        
            
        // dd("No fim");
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
                  
                  $emolumento=['confirm_tardia','confirm_tardia'];
                  $emolumentoFolha=['folha_de_prova'];

                
                  $emolumento_confirmacao  = EmolumentCodeV($emolumento[$index],$lectiveYearSelected[0]->id);
                  $emolumento_folha_de_prova = EmolumentCodeV($emolumentoFolha,$lectiveYearSelected[0]->id);
               
               if($emolumento_confirmacao->isEmpty()){

                    Toastr::warning(__('A forLEARN não encontrou um emolumento [Pré-matrícula ou confirmação de matrícula configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->route('matriculations.index');
                }
               if($emolumento_folha_de_prova->isEmpty()){

                    Toastr::warning(__('A forLEARN não encontrou um emolumento [Folha de prova configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->route('matriculations.index');
                }


                $r1       = createAutomaticArticleRequest($user->id, $emolumento_confirmacao[0]->id_emolumento, null, null);
                $reqFolha = createAutomaticArticleRequest($user->id, $emolumento_folha_de_prova[0]->id_emolumento, null, null);

    
                if (!$r1) {
                   throw new Exception('Could not create automatic [Confirmação de matrícula ()] article request payment for student (id: ' . $user->id . ') matriculation');
                }
                
    
                if (!$reqFolha) {
                   throw new Exception('Could not create automatic [Folha de prova ()] article request payment for student (id: ' . $user->id . ') matriculation');
                }
                
    
             
                    // Emissão de Cartão de Estudante 
                     $article_cartao=DB::table('articles')->where('id_code_dev',14)->where('anoLectivo',$lectiveYearSelected[0]->id)->first();
                     $r2 =createAutomaticArticleRequest($user->id, $article_cartao->id, null, null);
                     if (!$r2) {
                        throw new Exception('Could not create automatic [Emissão de Cartão de Estudante (id: 31)] article request payment for student (id: ' . $user->id . ') matriculation');
                     }
                      $articleRequets[$r2]['updatable'] = false;
                    
              

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
    

            //tratar Para as propinas geral sempre os meses do ano corrente
            //Desde outubro a Julho (21 - 22)
            //para o ano vai ser  outubro a Julho (22-23) 
            $First_year=explode ("-",$lectiveYearSelected[0]->start_date);
            $End_year=explode  ("-",$lectiveYearSelected[0]->end_date);
            $anoFirst=$First_year[0]; 
            $anoEnd=$End_year[0]; 


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
             

            $matriculation->articleRequests()->sync($articleRequets);

                      
            

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
            return $e;
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

                return "AVISO: O Emolumento selecionado não faz referência ao curso Ou Não existe nenhum emolumento para ser actualizado ."; 

        }

    // Toastr::success(__('Emolumentos actualizados com sucesso.'), __('toastr.success'));
    // return redirect()->route('formulario_rotina');


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


                $change_studant = User::whereHas('roles', function ($q) {
                    $q->whereIn('id', [6]);
                 })
                 ->whereHas('courses')
                 ->whereDoesntHave('matriculation')
                 ->with(['parameters' => function ($q) {
                   $q->whereIn('code', ['nome', 'n_mecanografico']);
                 }])
                 
                 ->join('tb_change_course_normal as cc' , function($join){
                    $join->on('cc.id_student_user','users.id')
                        ->whereNull('cc.deleted_by')
                        ->whereNull('cc.deleted_at');
                 })
                 ->join('article_requests', function($join){
                    $join->on('article_requests.user_id', '=', 'cc.id_student_user')
                    ->where('article_requests.status','total')
                        ->whereNull('article_requests.deleted_by')
                        ->whereNull('article_requests.deleted_at');   
                 })
                 ->join('articles', function($join){
                    $join->on('articles.id', '=', 'article_requests.article_id')
                    ->where('articles.id_code_dev',21);
                 })
                 ->join('tb_transference_studant as transf' , function($join){
                    $join->on('transf.user_id','cc.id_student_user')
                    ->whereNull('transf.deleted_by')
                        ->whereNull('transf.deleted_at');  
                 })
                 ->where('transf.type_transference',3)
                 ->where('transf.status_disc',1)
                 ->select(['transf.*','users.*'])
                 ->get()
                 ->map(function ($user) {
                     $displayName = $this->formatUserName($user);
                     return ['id' => $user->id, 'display_name' => $displayName];
                 });
             
                 
            

                $usuarios = collect();
                $usuarios = $equivalente_studant;

                if(isset($change_studant))
                $usuarios = $usuarios->concat($change_studant);

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




private function verificarAprovacao($disciplinesReproved,$id_curso){
    //Pegar o curso 
     $curso=DB::table('courses')->whereId($id_curso)->get();
    
    //Processamento de encontrar as anuais e as simestrais.
    //variavel global para analizar as cadeiras.
     $Observacao=[];
    
    $resultado=[];
    $anual=[]; $simestral=[];
    $reprovadas_estado = $disciplinesReproved->map(function($item,$key) use($disciplinesReproved,$simestral,$anual,$resultado){
       for ($i=0; $i <count($disciplinesReproved[$key]) ; $i++) { 
         $periodo=substr($item[$i]['code'],-3, 1);
         if($periodo=="1" || $periodo=="2"){$simestral[]="S";}
         else if($periodo=="A"){ $anual[]="A";}  
     }
         $resultado['Anual']=count($anual);
         $resultado['Simestral']=count($simestral);
         return $resultado;
     });
    //Processamento somatório agrupar as anuais e as simestrais
    $anual_total=0;   
    $simestral_total=0;   
    foreach ($reprovadas_estado as $key=>$item ){
        $anual_total+=$reprovadas_estado[$key]['Anual'];  
        $simestral_total+=$reprovadas_estado[$key]['Simestral'];  
    }

    //Criar as condições finais possiveis.
    if($anual_total+$simestral_total >=5){

        $A_pontos=$anual_total*2;
        return  $Observacao = [
                    'Obs'=>'regra01',
                    'confirmacao'=>1,
                    'qtd_disciplina'=>$anual_total+$simestral_total,
                    'emolumento'=>'P_normais',
                    'estado'=> $curso[0]->code =="RI" && $A_pontos+$simestral_total>=5 && $A_pontos+$simestral_total< 7 ? 'aprovado':'reprovado',
                    'curso'=>$curso[0]->code,
                    'pontos'=>$A_pontos+$simestral_total,
                    'atencao'=>"Se as disciplinas em atraso forem >= 5,gerar as 10 propinas e a confirmação de matrícula"
                ];

     }
   else if($anual_total+$simestral_total <=4 & $anual_total+$simestral_total>0 ){
                
     $anual_pontos=$anual_total*2;

    if($anual_pontos > 2 & $simestral_total > 0 ) {
                
                    
                return $Observacao = [
                        'Obs'=>'regra02',
                        'confirmacao'=>1,
                        'qtd_disciplina'=>$anual_total+$simestral_total,
                        'emolumento'=>'inscricao_frenquencia',
                        'estado'=>$simestral_total+$anual_pontos >=5 ?'com cadeira':'',
                        'curso'=>$curso[0]->code,
                        'pontos'=>$simestral_total+$anual_pontos,
                        'atencao'=>"Se as disciplinas em atraso forem <= 4,gerar emolumentos 'Inscricao por frequencia' e a confirmação de matrícula(Ex: gerar o nº de emolumento por mês ...4 por mês dependendo das disciplinas em atraso) "
                ];

           }
                
     else if($simestral_total+$anual_pontos >4){
                    return  $Observacao = [
                        'Obs'=>'regra02',
                        'confirmacao'=>1,
                        'curso'=>'test',
                        'qtd_disciplina'=>$anual_total+$simestral_total,
                        'emolumento'=>'inscricao_frenquencia',
                        'estado'=>$simestral_total+$anual_pontos >= 5 ?'com cadeira':'',
                        'curso'=>$curso[0]->code,
                        'pontos'=>$simestral_total+$anual_pontos,
                        'atencao'=>"Se as disciplinas em atraso forem <= 4,gerar emolumentos 'Inscricao por frequencia' e a confirmação de matrícula(Ex: gerar o nº de emolumento por mês ...4 por mês dependendo das disciplinas em atraso) "
                    ];
            }

    }

     return $Observacao = [
                'Obs'=>'normal',
                'curso'=>$curso[0]->code,
                'pontos'=>isset($simestral_total)+isset($anual_pontos)??0,
                'estado'=>"FOI"
               ];
}

}


