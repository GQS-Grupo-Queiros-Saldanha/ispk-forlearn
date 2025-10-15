<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Modules\Avaliations\Models\GradePath;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\TranferredStudent;
use App\Modules\Users\Models\TransferredStudent;
use App\Modules\Users\Models\UserState;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Controllers\MatriculationDisciplineListController;
use App\Modules\Avaliations\Models\Metrica;

class ScheduleExamController extends Controller
{
    public function index()
    {
        
            $exams = collect([
                ['id' => 0, 'display_name' => ""],
                ['id' => 1, 'display_name' => "Exame de recurso"],
                ['id' => 2, 'display_name' => "Exame especial"],
                ['id' => 3, 'display_name' => "Prova Parcelar (2ª Chamada)"],
                ['id' => 4, 'display_name' => "Revisão de prova"],
                ['id' => 5, 'display_name' => "Melhoria de nota"],
                ['id' => 6, 'display_name' => "Exame Extraordinário"]
            ]);
      

   $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first(); 

        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $courses = Course::with(['currentTranslation'])->get();

        $data = ['exams' => $exams,
                  'lectiveYearSelected'=>$lectiveYearSelected,
                  'lectiveYears'=>$lectiveYears
                ];

        return view("Avaliations::schedule-exam.schedule-exam")->with($data);
    }

    public function listCourses()
    {
        $courses = Course::with(['currentTranslation'])->get();
        return response()->json($courses);
    }



    public function getStudentsWhereHas($exam, $course_id,$lective_year)
    {
     
       

        $students =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                })
                // Adiciona a condição 
                ->where('matriculations.lective_year', $lective_year)
                ->when($exam == 6, function ($query) {
                    return $query->join('courses', function($join){
                        $join->on('courses.id','=','uc.courses_id',);
                        $join->on('courses.duration_value', '=', 'matriculations.course_year');
                    });
                })
                ->where('uc.courses_id', $course_id)
                ->select([
                    //'u0.name as student',
                    'u0.id as user_id',
                    'u_p.value as name',
                    'u0.email as email',
                    'ct.display_name as course',
                    'up_meca.value as student_number'
                ])
                ->whereNull('matriculations.deleted_at')
                ->orderBy('name')
                ->distinct('id')
                ->get();

                    

                return response()->json($students);
    }




    public function getExamInfoBy($exam, $studentId,$lectiveYear)
    {
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->where('id',$lectiveYear)
        ->first();
        
        $verify_matriculation=$this->verifyMatriculation($studentId, $lectiveYearSelected);
       
        if ($exam == 1) {
           
            //trazer so as disciplinas por pagar o emolumento
            //TODO: avalair se ja tinha pago esse emolumento para nao aparecer na lista

        
            
            $Dados_disciplina=collect($verify_matriculation)->map(function ($item,$key)
            {
              return $item->id_disciplina;
            });

            if(!$verify_matriculation->isEmpty()){
                 //
                $id_disc_present_year=$Dados_disciplina;
              
                //Esse código é para trazer as já marcadas e não retornar mais.
                 $exameMarcado=DB::table('tb_recurso_student')
                 ->where('matriculation_id',$verify_matriculation[0]->id_matricula)
                 ->where('estado_exame',1)
                 ->whereIn('discipline_id',$id_disc_present_year)
                 ->get()->map(function ($item,$key)
                  {
                     return $item->discipline_id;
                  });
                 
                 
                 //Pegar todas negativas na transição    
                 $recurso=DB::table('new_old_grades as Nota_transation')
                        ->join('disciplines', 'disciplines.id','=','Nota_transation.discipline_id')
                        ->leftJoin('disciplines_translations as dcp', function ($join) {
                            $join->on('dcp.discipline_id', '=', 'disciplines.id');
                            $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dcp.active', '=', DB::raw(true));
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                                $join->on('Nota_transation.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                                })
                        ->select([
                                    'u_p.value as name',
                                    'Nota_transation.grade as grade',
                                    'disciplines.code as discipline_code',
                                    'dcp.display_name as discipline_name',
                                    'disciplines.id as discipline_id',
                        ])
                        ->where('Nota_transation.user_id', $studentId)
                        ->whereIn('Nota_transation.discipline_id',$id_disc_present_year)
                        ->get()
                         ->filter(function($item){
                             return $item->grade  < 10;
                         });
                         
                         if(count($recurso)>0){
                             
                            $removeDuplicates = $recurso->unique('discipline_id');
                            $removeDuplicates->values()->all();
                            $students = $removeDuplicates;

                        }
                        else {
                            return  $students=501;
                            //não foi detado nenhum recurso para este(a) estudante na presente matrícula  
                        }


            }
            
            else{
                return  $students=502; 
                //Não foi encontrada nenhuma matrícula no ano lectivo corrente para este aluno
                
            }
        
        
        

        }
        // segunda chamada pp
        else if($exam == 3 || $exam == 4){

            $verify_matriculation=$this->verifyMatriculation($studentId, $lectiveYearSelected);

            if(!$verify_matriculation->isEmpty()){

                //Pegar todas notas no Percurso acadêmico com o curso e plano de estudo    
          $Exame_Disciplina=DB::table('matriculations as mt')
          ->where('mt.user_id', $studentId)
          ->whereBetween('mt.created_at',[$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
          ->whereNull('mt.deleted_at')
          ->whereNull('mt.deleted_by')
          ->join('matriculation_disciplines as md','md.matriculation_id','=', 'mt.id')
          ->join('disciplines', 'disciplines.id','=','md.discipline_id')
          ->where('md.exam_only', 0)
          ->leftJoin('disciplines_translations as dcp', function ($join) {
              $join->on('dcp.discipline_id', '=', 'disciplines.id');
              $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('dcp.active', '=', DB::raw(true));
             })
          ->leftJoin('study_plans_has_disciplines as stpd','stpd.disciplines_id','disciplines.id')
          ->join('study_plans', 'study_plans.id','=','stpd.study_plans_id')

          ->leftJoin('user_parameters as u_p', function ($join) {
                  $join->on('mt.user_id', '=', 'u_p.users_id')
                  ->where('u_p.parameters_id', 1);
                  })
         ->select([
                      'u_p.value as name',
                      'disciplines.code as discipline_code',
                      'dcp.display_name as discipline_name',
                      'disciplines.id as discipline_id',
                      'stpd.years as ano_Academico',
                      'stpd.study_plans_id as Plano_estudo',
                      'study_plans.courses_id as id_curso'
            ])
          ->get();
          
             //agrupar por ano curricular a id do curso servindo como chave
             $Disciplina_Grupo_Curso_year = collect($Exame_Disciplina)->groupBy(function ($item, $key) {
                      return $item->ano_Academico."_".$item->id_curso;
              });
              
              //Agrupar as turmas com as chaves iguais as do agrupamento da disciplina
              $Turma=[];
              foreach($Disciplina_Grupo_Curso_year as $key=>$value){
                     //Partindo a chve para ter os dados das turmas por ano
                      $Partir_chave=explode("_",$key);
                      $year=$Partir_chave[0];$curso_id=$Partir_chave[1];

                      $Classe=DB::table('classes')
                      ->join('matriculation_classes as mc','mc.class_id', '=', 'classes.id')
                      ->join('matriculations as mt','mc.matriculation_id', '=', 'mt.id')
                      ->where('mt.user_id', $studentId)
                      ->where('courses_id',$curso_id)  
                      ->where('year',$year)
                      ->whereNull('classes.deleted_by')
                      ->whereNull('classes.deleted_at')
                      ->select(['classes.*'])
                      ->where('lective_year_id',$lectiveYearSelected->id)
                      ->get(); 
                      
                      


                      $Turma[$key]=[
                         $Classe
                      ];


                      if(isset($Classe)){
                         $Turma[$key]=[
                             $Classe
                          ];
                      }else{
                         $Turma[$key]=[
                            'undefined'
                          ];
                      }

                 
              }
           //  return ["Turma"=>$Turma, "Disciplina"=>$Disciplina_Grupo_Curso_year,"ExameExpecial"=>0];

          
           //aviso a marcação de exame encontra-se indesponível, porfavor contacte o apoio a forLEARN para liberar esta  funcionalidade.
          return response()->json(["Turma"=>$Turma, "Disciplina"=>$Disciplina_Grupo_Curso_year,"ExameExpecial"=>0]);

            }
            else{
                return  $students=502; 
                //Não foi encontrada nenhuma matrícula no ano lectivo corrente para este aluno
                
            }

        }
        //Melhoria de Nota
        else if ($exam == 5 || $exam == 6) {
           
            //trazer so as disciplinas por pagar o emolumento
            //TODO: avalair se ja tinha pago esse emolumento para nao aparecer na lista

        
            
         $Exame_Disciplina=DB::table('new_old_grades as Nota_transation')
         ->when($exam == 6,function($sql)use($verify_matriculation){
            $md = DB::table('matriculation_disciplines')
                        ->leftJoin('disciplines','disciplines.id','matriculation_disciplines.discipline_id')
                        ->where('matriculation_disciplines.matriculation_id',$verify_matriculation[0]->id_matricula)
                        ->select([
                            'matriculation_disciplines.discipline_id',
                            'disciplines.code'
                        ])
                        ->get();

            $md = $md->filter(function($item)use($verify_matriculation){
               
                return substr($item->code,2,1) != $verify_matriculation[0]->course_year;
            });

            
         
            $d = $md->isEmpty() ? [] : $md->pluck('discipline_id')->toArray();
           
            return $sql->whereIn('Nota_transation.discipline_id',$d)
                        ->where('Nota_transation.grade','<',10);
            })
         ->join('disciplines', 'disciplines.id','=','Nota_transation.discipline_id')
         ->leftJoin('disciplines_translations as dcp', function ($join) {
             $join->on('dcp.discipline_id', '=', 'disciplines.id');
             $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
             $join->on('dcp.active', '=', DB::raw(true));
            })
         ->leftJoin('study_plans_has_disciplines as stpd','stpd.disciplines_id','disciplines.id')
         ->join('study_plans', 'study_plans.id','=','stpd.study_plans_id')

         ->leftJoin('user_parameters as u_p', function ($join) {
                 $join->on('Nota_transation.user_id', '=', 'u_p.users_id')
                 ->where('u_p.parameters_id', 1);
                 })
        ->select([
                     'u_p.value as name',
                     'Nota_transation.grade as grade',
                     'disciplines.code as discipline_code',
                     'dcp.display_name as discipline_name',
                     'disciplines.id as discipline_id',
                     'stpd.years as ano_Academico',
                     'stpd.study_plans_id as Plano_estudo',
                     'study_plans.courses_id as id_curso'
           ])
         ->where('Nota_transation.user_id', $studentId)
         ->distinct('Nota_transation.discipline_id')
         ->get();

            if(!$Exame_Disciplina->isEmpty()){
                
           
                 $removeDuplicates = $Exame_Disciplina->unique('discipline_id');
                            $removeDuplicates->values()->all();
                            $students = $removeDuplicates;

            }
            
            else{
                return  $students=506; 
                //Não foi encontrada nenhuma matrícula no ano lectivo corrente para este aluno
                
            }
        
        
        }
        //Quando é exame especial entra no else de baixo
        else {




          //Pegar todas notas no Percurso acadêmico com o curso e plano de estudo    
          $Exame_Disciplina=DB::table('new_old_grades as Nota_transation')
                ->join('matriculation_disciplines as md', function($join)use($verify_matriculation){

                    $mesAtual = date('m'); 
                    $epocaFinalistas = [2, 3, 4];
                    $isEpocaExameFinalistas = in_array($mesAtual, $epocaFinalistas);

                    $join->on('Nota_transation.discipline_id', '=', 'md.discipline_id')
                    ->where('md.matriculation_id',$verify_matriculation[0]->id_matricula)
                    ->when($isEpocaExameFinalistas,function($sql)use($verify_matriculation){
                        $md = DB::table('matriculation_disciplines')
                                    ->join('disciplines','disciplines.id','matriculation_disciplines.discipline_id')
                                    ->where('matriculation_disciplines.matriculation_id',$verify_matriculation[0]->id_matricula)
                                    ->select([
                                        'matriculation_disciplines.discipline_id',
                                        'disciplines.code'
                                    ])
                                    ->get();
                                      
                        $md = $md->filter(function($item)use($verify_matriculation){
                           
                            return substr($item->code,2,1) == $verify_matriculation[0]->course_year;
                        });
                     
                        $d = $md->isEmpty() ? [] : $md->pluck('discipline_id')->toArray();
                       
                        return $sql->whereIn('Nota_transation.discipline_id',$d);
                        });

                })
                
                ->join('disciplines', 'disciplines.id','=','Nota_transation.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                     $join->on('dcp.discipline_id', '=', 'disciplines.id');
                     $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                     $join->on('dcp.active', '=', DB::raw(true));
                    }) 
                 ->leftJoin('study_plans_has_disciplines as stpd','stpd.disciplines_id','disciplines.id')
                 ->join('study_plans', 'study_plans.id','=','stpd.study_plans_id')

                 ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('Nota_transation.user_id', '=', 'u_p.users_id')
                         ->where('u_p.parameters_id', 1);
                         })
                ->select([
                             'u_p.value as name',
                             'Nota_transation.grade as grade',
                             'disciplines.code as discipline_code',
                             'dcp.display_name as discipline_name',
                             'disciplines.id as discipline_id',
                             'stpd.years as ano_Academico',
                             'stpd.study_plans_id as Plano_estudo',
                             'study_plans.courses_id as id_curso'
                   ])
                   ->where('Nota_transation.grade','<',10)
                 ->where('Nota_transation.user_id', $studentId)
                 ->distinct('Nota_transation.discipline_id')
                 ->get();
                 
                    //agrupar por ano curricular a id do curso servindo como chave
                    $Disciplina_Grupo_Curso_year = collect($Exame_Disciplina)->groupBy(function ($item, $key) {
                             return $item->ano_Academico."_".$item->id_curso;
                     });
                     
                     //Agrupar as turmas com as chaves iguais as do agrupamento da disciplina
                     $Turma=[];
                     foreach($Disciplina_Grupo_Curso_year as $key=>$value){
                            //Partindo a chve para ter os dados das turmas por ano
                             $Partir_chave=explode("_",$key);
                             $year=$Partir_chave[0];$curso_id=$Partir_chave[1];

                             $Classe=DB::table('classes')
                             ->join('matriculation_classes as mc','mc.class_id', '=', 'classes.id')
                             ->join('matriculations as mt','mc.matriculation_id', '=', 'mt.id')
                             ->where('mt.user_id', $studentId)
                             ->where('courses_id',$curso_id)  
                             ->where('year',$year)
                             ->whereNull('classes.deleted_by')
                             ->whereNull('classes.deleted_at')
                             ->where('lective_year_id',$lectiveYearSelected->id)
                             ->get();    
                             $Turma[$key]=[
                                $Classe
                             ];


                             if(isset($Classe)){
                                $Turma[$key]=[
                                    $Classe
                                 ];
                             }else{
                                $Turma[$key]=[
                                   'undefined'
                                 ];
                             }

                        
                     }
                  //  return ["Turma"=>$Turma, "Disciplina"=>$Disciplina_Grupo_Curso_year,"ExameExpecial"=>0];

                 
                  //aviso a marcação de exame encontra-se indesponível, porfavor contacte o apoio a forLEARN para liberar esta  funcionalidade.
                 return response()->json(["Turma"=>$Turma, "Disciplina"=>$Disciplina_Grupo_Curso_year,"ExameExpecial"=>0]);
                 $students = 505;
                 
       
                 }


        return response()->json($students);
    }

    public function verifyMatriculation($studentId,$lectiveYearSelected)
{
    return DB::table('matriculations as mt')
            ->join('users', 'mt.user_id','=','users.id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join){
                $join->on('matricula_disci.matriculation_id','=','mt.id');
            }) 
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'matricula_disci.discipline_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['mt.id as id_matricula','dt.display_name as disciplina_nome',
            'users.id as id_usuario','dp.id as id_disciplina',
            'dp.code as codigo_disciplina','mt.course_year as course_year'])
            ->where('mt.lective_year', $lectiveYearSelected->id)
            ->where('users.id',$studentId)
            ->whereNull('mt.deleted_at')
            ->distinct()
            ->get();
}
    public function store(Request $request)
    {
        try{ 
        // return $request;
        
        //Usando codeDEV -- exame_recurso and exame_especial
        $request->get('exam') == 1 ? $article_id = "exame_recurso" : $article_id ="exame_especial";
        
        //Retorno do emolumento de recurso ou de exame especial(extraordinário)
        $currentData = Carbon::now();
        
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->where('id',$request->anoLectivo)
        ->first();
        
        
        //Pegar emolumentos com base no código
         $article =  Article::join('code_developer as codeDev','codeDev.id','=','articles.id_code_dev')
        ->select(['articles.*'])
        ->where('codeDev.code',$article_id)
        ->whereNull('articles.deleted_by')
        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->first();


         $data = [
                   'disciplines' => $request->get('disciplines')
                ];
              
        $id_matricula = $this->matriculaID($request->get('students'), $lectiveYearSelected);  
        
       
        if($request->exam==2){
            
           //Comecar o exame especial roles
           return $this->ExameEspecial($request,$article, $id_matricula,$currentData);

        }
        
        if($request->exam==3){
            
            //Comecar o exame especial roles
            return $this->prova_parcelar($request,$article, $id_matricula,$currentData);
 
         } 

         if($request->exam==4){
            
            //Comecar o exame especial roles
            return $this->revisao_prova($request,$article, $id_matricula,$currentData);
 
         }
         if($request->exam==5){ 
            return $this->melhoria_nota($request,"melhoria_nota", $id_matricula,$currentData);
         }
         if($request->exam==6){
            return $this->melhoria_nota($request,"exame_extraordinario", $id_matricula,$currentData);
         }

        if($id_matricula==0){

            Toastr::warning(__('Não foi detetado nehuma matrícula neste ano lectivo para o estudante selecionado, impossibilitando assim a marcação de exame de recurso, tente novamente, caso contrário, consulta o apoio a forLEARN.'), __('toastr.warning'));
            return back();

        }
        
        


        DB::beginTransaction();
            for ($i=0; $i < count($data['disciplines']); $i++) {

                   //Código que verifica marcação já feita
                    $verify=DB::table('tb_recurso_student as tb_r')
                    ->join('disciplines', 'disciplines.id','=','tb_r.discipline_id')
                    ->join('matriculations', 'matriculations.id','=','tb_r.matriculation_id')
                    ->leftJoin('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->select(['dcp.display_name as disciplina_nome','tb_r.matriculation_id as matricula'])
                    ->where('tb_r.id_lectiveYear', $lectiveYearSelected->id)
                    ->where('tb_r.matriculation_id', $id_matricula)
                    ->where('tb_r.discipline_id', $data['disciplines'][$i])
                    ->whereNull('matriculations.deleted_at')
                    ->join('article_requests as ar','ar.user_id','matriculations.user_id')
                    ->where('ar.article_id',$article->id)
                    ->where('ar.discipline_id',$data['disciplines'][$i])
                    ->whereNull('ar.deleted_at')
                    ->first();
                  
                    if($verify!=null) {  
                       
                        $disciplina_name=$verify->disciplina_nome;
                        Toastr::warning(__("Foi detetado uma marcação de exame de recurso já existente neste ano lectivo para o estudante selecionado na disciplina << ".$disciplina_name." >>, impossibilitando assim a marcação de exame de recurso, verifique na tesouraria há existência do emolumento marcação de exame, caso contrário,for um erro; Consulta o apoio a forLEARN."), __('toastr.warning'));
                        return back();
                    }
               
                // Na hora de ir buscar os que pagaram recurso avaliar pelo mes.
                // Pesquisar pelo intervalo de mes.
                // Buscar por data comparando ao ano lectivo.
                $articleRequest = new ArticleRequest([
                    'user_id' =>    $request->get('students'),
                    'article_id' => $article->id,
                    'year'  => null,
                    'month' => null,
                    'base_value' => $article->base_value,
                    'discipline_id' => $data['disciplines'][$i] ?: ""
                ]);

                $articleRequest->save();

                // create debit with article base value
                $transaction = Transaction::create([
                    'type' => 'debit',
                    'value' => $articleRequest->base_value,
                    'notes' => 'Débito inicial do valor base'
                ]);

                $transaction->article_request()
                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);

                //Actualizar  na nova tabela de Recurso_marcação_para_mostrar os aptos
                $marcarRecurso=DB::table('tb_recurso_student')->updateOrInsert(
                        [
                            'id_lectiveYear' => $lectiveYearSelected->id,                           
                            'matriculation_id' => $id_matricula,
                            'discipline_id' => $data['disciplines'][$i],
                        ]
                        ,      
                        [
                            'estado_exame' => 1,
                            'descricao' => "marcado",
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id,
                            'created_at' => $currentData,
                            'updated_at' => $currentData,
                         ]

                        );


            DB::commit();

            $exameR=$request->get('exam') == 1;

            if ($articleRequest && $exameR==1) {
                //Operaçao realizada com sucesso.
                Toastr::success(__("A sua marcação de EXAME (s) RECURSO foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
                        return redirect()->route('schedule_exam.index');

             }else if ($articleRequest && $exameR==2) {
                Toastr::success(__("A sua marcação de EXAME (s) ESPECIAL foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
                        return redirect()->route('schedule_exam.index');
              }

            } 
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }

    



    private function matriculaID($id_estudante,$lectiveYearSelected){

        $verify_matriculation=DB::table('matriculations as mt')
        ->join('users', 'mt.user_id','=','users.id')
        ->select(['mt.id as id','users.name as name'])
        ->where('mt.lective_year', $lectiveYearSelected->id)
        ->where('users.id',$id_estudante)
        ->whereNull('mt.deleted_at')
        ->distinct()
        ->first();

        return $verify_matriculation!=null?$verify_matriculation->id:0;
    }
    
    

    private function ExameEspecial($request,$molumentoExame,$id_matriculation,$currentData){

        $turma=$request->turma;
        $disciplines=$request->disciplines;
        $id_user=$request->students;
        $id_courses=$request->courses;
        $id_LectiveYear=$request->anoLectivo;
        
        $message=false;
        
        DB::beginTransaction();
           //validar turma selecionada
           if(count($turma)>0){

            //loop de disciplina
            foreach($turma as $key=>$value){
                $dados=explode(",",$value);
                $id_turma=$dados[1];
                
                foreach($disciplines as $chave=> $valor){
                  $dados_disciplina=explode(",",$valor);
                  $id_disciplina=$dados_disciplina[1];
                    //Comparar as disciplnas na sua turma 
                    if($dados_disciplina[0]== $dados[0]){

                         //Código que verifica marcação especial já feita
                         $verify=DB::table('tb_exame_studant as tb_r')
                         ->where('tb_r.id_lectiveYear', $id_LectiveYear)
                         ->where('tb_r.id_user', $id_user)
                         ->where('tb_r.id_discipline', $id_disciplina)
                         ->join('article_requests as ar','tb_r.id_discipline','ar.discipline_id')
                         ->where('ar.user_id', $id_user)
                         ->where('ar.article_id', $molumentoExame->id)
                         ->join('disciplines', 'disciplines.id','=','tb_r.id_discipline')
                         ->leftJoin('disciplines_translations as dcp', function ($join) {
                             $join->on('dcp.discipline_id', '=', 'disciplines.id');
                             $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                             $join->on('dcp.active', '=', DB::raw(true));
                         })
                         ->select(['dcp.display_name as disciplina_nome'])
                        
                         ->first();
     
                         if($verify!=null) {  
     
                             $disciplina_name=$verify->disciplina_nome;
                             Toastr::warning(__("Foi detetado uma marcação de exame especial já existente no ano lectivo selecionado para o estudante selecionado na disciplina << ".$disciplina_name." >>, impossibilitando assim a marcação de exame especial, verifique na tesouraria há existência do emolumento marcação de exame, caso contrário,for um erro; Consulta o apoio a forLEARN."), __('toastr.warning'));
                             return back();
                         }

                            //Creador de emolumento
                            createAutomaticArticleRequestExame($id_user, $molumentoExame->id, null, null,$id_disciplina);
                
                            //Actualizar  na nova tabela de Recurso_marcação_para_mostrar os aptos
                            $marcarExame=DB::table('tb_exame_studant')->updateOrInsert(
                                [
                                'id_lectiveYear' => $id_LectiveYear,                           
                                'id_user' => $id_user,
                                'id_discipline' => $id_disciplina,
                                ]
                                ,      
                                [
                                'status' => 1,
                                'id_class' => $id_turma,
                                'description' => "marcado especial",
                                'updated_by' => Auth::user()->id,
                                'created_by' => Auth::user()->id,
                                'created_at' => $currentData,
                                'updated_at' => $currentData,
                            ]

                            );

                            $message=true;
                    }

                }
                //Acima fecha o loop
             }

            }

            DB::commit();
    
            if($message){
            //Operaçao realizada com sucesso.
            Toastr::success(__("A sua marcação de EXAME (s) ESPECIAL  foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
            return redirect()->route('schedule_exam.index');

            }

      
     }

     public function prova_parcelar($request,$molumentoExame,$id_matriculation,$currentData){


        $turma=$request->turma;
        $disciplines=$request->disciplines;
        $id_user=$request->students;
        $id_courses=$request->courses;
        $id_LectiveYear=$request->anoLectivo;
        
        $message=false;

        $lectiveYearSelected = DB::table('lective_years')
        // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->where('id',$request->anoLectivo)
        ->first();

        $matriculaID = $this->matriculaID($id_user,$lectiveYearSelected);

        
        
        DB::beginTransaction();
           //validar turma selecionada
           if(count($turma)>0){

            //loop de disciplina
            foreach($turma as $key=>$value){
                $dados=explode(",",$value);
                $id_turma=$dados[1];
                
                foreach($disciplines as $chave=> $valor){
                  $dados_disciplina=explode(",",$valor);
                  $id_disciplina=$dados_disciplina[1];
                    //Comparar as disciplnas na sua turma 
                    if($dados_disciplina[0]== $dados[0]){

                        $metric = DB::table('metricas')
                        ->where('id', $request->metric)
                         ->select('metricas.*')
                         ->first();

                        if ($metric != null) {
                             $metric_id = $request->metric;
                        }
                        else{
                         
                            Toastr::error(__(' Não foi possivel criar o emolumento de  SEGUNDA CHAMADA DE PROVA PARCELAR, por favor tente novamente'), __('toastr.error'));
                            return redirect()->back();
                        }
                        
                          //Márcia
                          $pauta=DB::table('lancar_pauta')
                          ->where(['id_turma'=>$id_turma,
                          'id_ano_lectivo'=>$id_LectiveYear,
                          'id_disciplina' => $id_disciplina,
                          'pauta_tipo'=>$metric->code_dev,
                          'segunda_chamada' => null])
                          ->orderBy('version', 'DESC')
                          ->first();


                          if(isset($pauta) && !(($pauta->estado == 1 ) || ($pauta->estado == 0 && $pauta->active == 1))){
                                continue;
                          }


                        $codev = "prova_parcelar";

                        //Emolumento com base no ano lectivo
                        $emolumento = EmolumentCodevLective($codev, $id_LectiveYear);
            
                        if ($emolumento->isEmpty()) {
                            Toastr::warning(__('A forLEARN não encontrou um emolumento de SEGUNDA CHAMADA DE PROVA PARCELAR configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                            return redirect()->back();
                        }
                      
                        $article_id = $emolumento[0]->id_emolumento;
                        
                         //Código que verifica marcação especial já feita
                         $verify=DB::table('tb_segunda_chamada_prova_parcelar as tb_r')
                         ->where('tb_r.lectiveYear_id', $id_LectiveYear)
                         ->where('tb_r.matriculation_id', $matriculaID )
                         ->where('tb_r.discipline_id', $id_disciplina)
                         ->where('tb_r.metric_id', $metric_id)
                         ->join('matriculations as mt','mt.id','tb_r.matriculation_id')
                         ->where('mt.lective_year',$id_LectiveYear)
                         ->whereNull('mt.deleted_by')
                         ->whereNull('mt.deleted_at')
                         ->join('users','mt.user_id','users.id')
                         ->join('article_requests as ar','ar.user_id','users.id')
                         ->where('ar.article_id',$article_id)
                         ->where('ar.discipline_id',$id_disciplina)
                         ->where('ar.metric_id',$metric_id)
                         ->whereNull('ar.deleted_at')
                         ->whereNull('ar.deleted_by')
                         ->join('disciplines', 'disciplines.id','=','tb_r.discipline_id')
                         ->leftJoin('disciplines_translations as dcp', function ($join) {
                             $join->on('dcp.discipline_id', '=', 'disciplines.id');
                             $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                             $join->on('dcp.active', '=', DB::raw(true));
                         })
                        
                         ->select(['dcp.display_name as disciplina_nome'])
                         ->first();

                       
     
                         if($verify!=null) {  
     
                             $disciplina_name=$verify->disciplina_nome;
                             Toastr::warning(__("Foi detetado uma marcação de segunda chamada para prova parcelar já existente no ano lectivo selecionado para o estudante selecionado na disciplina << ".$disciplina_name." >>, impossibilitando assim a marcação de exame especial, verifique na tesouraria há existência do emolumento marcação de exame, caso contrário,for um erro; Consulta o apoio a forLEARN."), __('toastr.warning'));
                             return back();
                         }

                        

                              //codev dos emolumentos
            
            $currentData = Carbon::now();

                       
                        $article_request_id = createAutomaticArticleRequestExame($id_user, $article_id, null, null,$id_disciplina,$metric_id);
                        
                        if (!$article_request_id) {
                            
                            Toastr::error(__(' Não foi possivel criar o emolumento de  SEGUNDA CHAMADA DE PROVA PARCELAR, por favor tente novamente'), __('toastr.error'));
                            return redirect()->back();
                        }

                       

                            //Actualizar  na nova tabela de Recurso_marcação_para_mostrar os aptos
                            $marcarExame=DB::table('tb_segunda_chamada_prova_parcelar')->updateOrInsert(
                                [
                                'lectiveYear_id' => $id_LectiveYear,                           
                                'matriculation_id' => $matriculaID,
                                'discipline_id' => $id_disciplina,
                                'metric_id' => $metric_id,
                                'id_class' => $id_turma
                                ]
                                ,      
                                [
                                'updated_by' => Auth::user()->id,
                                'created_by' => Auth::user()->id,
                                'created_at' => $currentData,
                                'updated_at' => $currentData,
                            ]

                            );

                       
                            
                            DB::table('requerimento')->insert(
                                [
                                    'article_id' => $article_request_id,
                                    "user_id" => $id_user,
                                    'year' => $id_LectiveYear
                                ]
                            );

                            $message=true;
                    }

                }
                //Acima fecha o loop
             }

            }

            DB::commit();
    
            if($message){
            //Operaçao realizada com sucesso.
            Toastr::success(__("A sua marcação de SEGUNDA CHAMADA DE PROVA PARCELAR foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
            return redirect()->route('schedule_exam.index');

            }



     }

     public function getMetricasSegundaChamada($lective_year){

        $data = Metrica::whereIn('metricas.code_dev', ['PF1', 'PF2','Neen']) 
        ->join('avaliacaos', function($join) use ($lective_year) {
            $join->on('avaliacaos.id', '=', 'metricas.avaliacaos_id')
                 ->where('avaliacaos.anoLectivo', '=', $lective_year)
                 ->whereNull('avaliacaos.deleted_at')
                 ->whereNull('avaliacaos.deleted_by');
        })
        ->select('metricas.*')
        ->get();
    
            return json_encode($data);
     }
    

     public function revisao_prova($request,$molumentoExame,$id_matriculation,$currentData){


        $turma=$request->turma;
        $disciplines=$request->disciplines;
        $id_user=$request->students;
        $id_courses=$request->courses;
        $id_LectiveYear=$request->anoLectivo;
        
        $message=false;

        $lectiveYearSelected = DB::table('lective_years')
        // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->where('id',$request->anoLectivo)
        ->first();

        $matriculaID = $this->matriculaID($id_user,$lectiveYearSelected);
        
        DB::beginTransaction();
           //validar turma selecionada
           if(count($turma)>0){

            //loop de disciplina
            foreach($turma as $key=>$value){
                $dados=explode(",",$value);
                $id_turma=$dados[1];
                
                foreach($disciplines as $chave=> $valor){
                  $dados_disciplina=explode(",",$valor);
                  $id_disciplina=$dados_disciplina[1];
                    //Comparar as disciplnas na sua turma 
                    if($dados_disciplina[0]== $dados[0]){

                         //Código que verifica marcação especial já feita
                         $verify=DB::table('tb_revisao_prova as tb_r')
                         ->join('disciplines', 'disciplines.id','=','tb_r.discipline_id')
                         ->leftJoin('disciplines_translations as dcp', function ($join) {
                             $join->on('dcp.discipline_id', '=', 'disciplines.id');
                             $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                             $join->on('dcp.active', '=', DB::raw(true));
                         })
                         ->select(['dcp.display_name as disciplina_nome'])
                         ->where('tb_r.lectiveYear_id', $id_LectiveYear)
                         ->where('tb_r.matriculation_id', $matriculaID )
                         ->where('tb_r.discipline_id', $id_disciplina)
                         ->first();
     
                         if($verify!=null) {  
     
                             $disciplina_name=$verify->disciplina_nome;
                             Toastr::warning(__("Foi detetado uma marcação de revisão de prova já existente no ano lectivo selecionado para o estudante selecionado na disciplina << ".$disciplina_name." >>, impossibilitando assim a marcação de exame especial, verifique na tesouraria há existência do emolumento marcação de exame, caso contrário,for um erro; Consulta o apoio a forLEARN."), __('toastr.warning'));
                             return back();
                         }

                              //codev dos emolumentos
              $codev = "revisao_prova";

              //Emolumento com base no ano lectivo
              $emolumento = EmolumentCodevLective($codev, $id_LectiveYear);
  
              if ($emolumento->isEmpty()) {
                  Toastr::warning(__('A forLEARN não encontrou um emolumento de REVISÃO DE PROVA configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                  return redirect()->back();
              }
            
            $article_id = $emolumento[0]->id_emolumento;



            $article_request_id = createAutomaticArticleRequest($id_user, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de REVISÃO DE PROVA, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }

                            //Actualizar  na nova tabela de Recurso_marcação_para_mostrar os aptos
                            $marcarExame=DB::table('tb_revisao_prova')->updateOrInsert(
                                [
                                'lectiveYear_id' => $id_LectiveYear,                           
                                'matriculation_id' => $matriculaID,
                                'discipline_id' => $id_disciplina,
                                ]
                                ,      
                                [
                                'id_class' => $id_turma,
                                'updated_by' => Auth::user()->id,
                                'created_by' => Auth::user()->id,
                                'created_at' => $currentData,
                                'updated_at' => $currentData,
                            ]

                            );

                            $message=true;
                    }

                }
                //Acima fecha o loop
             }

            }

            DB::commit();
    
            if($message){
            //Operaçao realizada com sucesso.
            Toastr::success(__("A sua marcação de REVISÃO DE PROVA foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
            return redirect()->route('schedule_exam.index');

            }



     }

     public function melhoria_nota($request,$codev,$id_matriculation,$currentData){
        
        $message=false;

        $data = [
            'disciplines' => $request->get('disciplines')
         ];

        $lectiveYearSelected = DB::table('lective_years')
        // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->where('id',$request->anoLectivo)
        ->first();

        $article =  Article::join('code_developer as codeDev','codeDev.id','=','articles.id_code_dev')
        ->select(['articles.*'])
        ->where('codeDev.code',$codev)
        ->whereNull('articles.deleted_by')
        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->first();

        
        $classes = DB::table('matriculation_classes')
        ->where('matriculation_classes.matriculation_id',$id_matriculation)
        ->join('classes','classes.id','matriculation_classes.class_id')
        ->select(['classes.*'])
        ->get();
       
        DB::beginTransaction();
            for ($i=0; $i < count($data['disciplines']); $i++) {

                   //Código que verifica marcação já feita
                   $finalista = $codev === "exame_extraordinario" ? 1 : 0;

                   $verify = DB::table('tb_exame_melhoria_nota as tb_r')
                    ->join('article_requests as art','art.user_id','tb_r.id_user')
                    ->where('art.article_id',$article->id)
                    ->whereNull('art.deleted_at')
                    ->whereNull('art.deleted_by')
                    ->join('disciplines', 'disciplines.id','=','tb_r.id_discipline')
                    ->leftJoin('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->select(['dcp.display_name as disciplina_nome'])
                    ->where('tb_r.id_lectiveYear', $lectiveYearSelected->id)
                    ->where('tb_r.id_user', $request->get('students'))
                    ->where('tb_r.id_discipline', $data['disciplines'][$i])
                    ->where('tb_r.finalist',$finalista)
                    ->first();

                    $text = $codev === "exame_extraordinario" ? "EXAME EXTRAORDINÁRIO" : "MELHORIA DE NOTA";

                    if($verify!=null) {  
                       
                        $disciplina_name=$verify->disciplina_nome;

                        Toastr::warning(__("Foi detetado uma marcação de ".$text." já existente neste ano lectivo para o estudante selecionado na disciplina << ".$disciplina_name." >>, impossibilitando assim a marcação, verifique na tesouraria há existência do emolumento marcação de exame, caso contrário,for um erro; Consulta o apoio a forLEARN."), __('toastr.warning'));
                        return back();
                    }

                    

                    $marcarExame=DB::table('tb_exame_melhoria_nota')->updateOrInsert(
                        [
                        'id_lectiveYear' => $lectiveYearSelected->id,                           
                        'id_user' => $request->get('students'),
                        'id_discipline' => $data['disciplines'][$i],
                        'finalist' => $finalista
                        ]
                        ,      
                        [
                        'status' => 1,
                        'description' => "marcado",
                        'updated_by' => Auth::user()->id,
                        'created_by' => Auth::user()->id,
                        'created_at' => $currentData,
                        'updated_at' => $currentData,
                        ]);
               
                // Na hora de ir buscar os que pagaram recurso avaliar pelo mes.
                // Pesquisar pelo intervalo de mes.
                // Buscar por data comparando ao ano lectivo.
                $articleRequest = new ArticleRequest([
                    'user_id' =>    $request->get('students'),
                    'article_id' => $article->id,
                    'year'  => null,
                    'month' => null,
                    'base_value' => $article->base_value,
                    'discipline_id' => $data['disciplines'][$i] ?: ""
                ]);

                $articleRequest->save();

                // create debit with article base value
                $transaction = Transaction::create([
                    'type' => 'debit',
                    'value' => $articleRequest->base_value,
                    'notes' => 'Débito inicial do valor base'
                ]);

                $transaction->article_request()
                ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);

                DB::table('requerimento')->insert(
                    [
                        'article_id' => $articleRequest->id,
                        "user_id" => $request->get('students'),
                        'year' => $request->anoLectivo
                    ]
                );
                
     }
     DB::commit();

     //Operaçao realizada com sucesso.
     Toastr::success(__("A sua marcação de ".$text." foi solicitada com sucesso. Dirija-se à TESOURARIA para liquidar os respectivos emolumentos. Obrigado."), "OPERAÇÃO REALIZADA COM SUCESSO:");
      return back();

     



}

}