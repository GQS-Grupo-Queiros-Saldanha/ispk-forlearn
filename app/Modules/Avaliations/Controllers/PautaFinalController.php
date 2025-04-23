<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\AvaliacaoAlunoHistorico;
use App\Modules\Avaliations\Models\Avaliations;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\PlanoEstudoAvaliacao;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use App\Modules\Avaliations\Models\TipoMetrica;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\NotaEstudante;
use Illuminate\Support\Str;
use Carbon\Carbon;
//use Barryvdh\DomPDF\PDF;
use App\Modules\GA\Models\LectiveYear;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

use Throwable;
use Yajra\DataTables\Facades\DataTables;

use PDF;
use App\Model\Institution;

/**
 * Cláudio Fernando - [2022-02-21 10:08:00 - crete, ajax, vida]
 * 
 */


class PautaFinalController extends Controller{
    /**
     * Display a listing of the resource.
     * Controller criadao Por gelson Matias
     * Pauto final 
     * @return \Illuminate\Http\Response
     */
    public function index(){
        try {
            return view("Avaliations::avaliacao-aluno.avaliacao-aluno");
        } 
        catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ajax(){

        try {
            $model = Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
                    ->join('tipo_avaliacaos as ta', 'ta.id', '=', 'avaliacaos.tipo_avaliacaos_id')
                    ->select([
                        'avaliacaos.nome as avaliacao_nome',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        'ta.nome as tipo_avaliacao_nome'
                        //'u0.name as student',
                    ]);

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::avaliacao.datatables.actions')->with('item', $item);
                    })
                    /*->editColumn('created_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->created_at);
                    })
                    ->editColumn('updated_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->updated_at);
                    })
                    ->editColumn('deleted_at', function ($item) {
                        return TimeHelper::time_elapsed_string($item->deleted_at);
                    })*/
                    ->rawColumns(['actions'])
                    ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        try {
             //Pegar o ano lectivo na select
             $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
             $currentData = Carbon::now();
             $lectiveYearSelected = DB::table('lective_years')
                 ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                 ->first();
             $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
             //-----------------------------------------------------------------------//
             $data = [
                        //'courses' => $courses->get(),
                        'lectiveYearSelected'=>$lectiveYearSelected,
                        'lectiveYears'=>$lectiveYears
                     ];

            return view("Avaliations::avaliacao-aluno.create-avaliacao-aluno")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     
    public function store(Request $request){
        try {
            
            
           
            if($request->whoIs=="super"){
                //Esse código faz o cadastro da nota quando for O Coordenador
                $turma=$request->turma;
                DB::transaction(function () use ($request) {
                    $turma_id=$request->turma;
                    $id_disc=explode(",",$request->disciplina);
                    $metrica_id = $request->metrica;
                    $plano_estudo = $request->id_plano_estudo;
                    $discipline_id = $id_disc[2];
                    $avaliacao_id = $request->avaliacao;
                    // $sped = $request->get('course_id');
                    $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->select('plano_estudo_avaliacaos.id')
                    ->where('plano_estudo_avaliacaos.study_plan_editions_id', $plano_estudo)
                    ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    ->first();
                    $data = [
                    'notas'=> $request->notas,
                    'estudantes' => $request->estudantes,
                    'presences' => $request->get('inputCheckBox')
                            ];
    
                    for ($i=0; $i < count($data['notas']); $i++) {
                        $avaliacaoAluno =  AvaliacaoAluno::updateOrCreate(

                          [
                            'plano_estudo_avaliacaos_id' => $spea->id,
                            'metricas_id' => $metrica_id,
                            'users_id' => $data['estudantes'][$i],
                            'id_turma' => $turma_id,
                          ],
                          [
                            'nota' => $data['notas'][$i],
                            'presence' => $data['presences'][$i],
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id
                          ]
                        );
     
                    }
                });
              //Success message
              Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
              return back();

             }
         
             
     else {

            $avaliacao_id = Metrica::where('id',$request->metrica_teacher)
            ->select(['avaliacaos_id as id'])
            ->get();
            // return $request->turma;
         DB::transaction(function () use ($request,$avaliacao_id) {
               $turma_id=$request->turma;
               $id_disc=explode(",",$request->disciplina);
               $metrica_id = $request->metrica_teacher;
               $plano_estudo = $request->id_plano_estudo;
               $discipline_id = $id_disc[2];
            // $sped = $request->get('course_id');
           $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->select('plano_estudo_avaliacaos.id')
            ->where('plano_estudo_avaliacaos.study_plan_editions_id', $plano_estudo)
            ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id[0]->id)
            ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
            ->first();

            $data = [
             'notas'=> $request->notas,
             'estudantes' => $request->estudantes,
             'presences' => $request->get('inputCheckBox')
           ];


         for ($i=0; $i < count($data['notas']); $i++) {
                $avaliacaoAluno = AvaliacaoAluno::updateOrCreate([
                'plano_estudo_avaliacaos_id' => $spea->id,
                'metricas_id' => $metrica_id,
                'users_id' => $data['estudantes'][$i],
                'id_turma' => $turma_id,
                ],
                [
                 'nota' => $data['notas'][$i],
                 'presence' => $data['presences'][$i],
                 'updated_by' => Auth::user()->id,
                 'created_by' => Auth::user()->id
                 ]
                ); 
            }
        });
        // Success message
        Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
        return back();
         }

      } catch (Exception | Throwable $e) {
         Toastr::error($e->getMessage(), __('toastr.error'));
        logError($e);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
     }
  }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id){
        try {
            return view("Avaliations::avaliacao-aluno.show-avaliacao-aluno");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        return $id;
    }
    public function destroy($id){
        try {
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function studyPlanEditionAjax(){
        try {
            $teacher_id = Auth::user()->id;
            //Listar Edições de Plano de Estudo associados a plano_estudo_avaliacaos
            $pea = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'stpeid.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })
            ->leftJoin('study_plans as stp', 'stpeid.study_plans_id', '=', 'stp.id')
            ->leftJoin('courses as crs', 'stp.courses_id', '=', 'crs.id')
            ->leftJoin('disciplines as dcp', 'dcp.courses_id', '=', 'crs.id')
            ->leftJoin('user_disciplines as usdc', 'usdc.disciplines_id', '=', 'dcp.id')
            //->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

            ->select([
                 'plano_estudo_avaliacaos.id as pea_id',
                 'stpeid.id as spea_id',
                 'spet.display_name as spea_nome'
             ])
            //Selecionar só plano de estudo pelo id do Professor
            //RETIRAR
            ->where('usdc.users_id', $teacher_id)
            ->distinct()
            ->get();

            $pea = $pea->unique('spea_id')
                       ->values()
                       ->all();

            return response()->json($pea);
            //json_encode(array('data'=>$pea));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function disciplineAjax($id){
        return $this->getDisciplinesByRole($id);
    }
    public function avaliacaoAjax($id){
        $avaliacaos = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')

                    ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

                    ->select([
                        'avl.id as avl_id',
                        'avl.nome as avl_nome',
                        'dp.code as discipline_code'
                    ])
                    ->where('dp.id', $id)
                   ->distinct()
                   ->get();

        return json_encode(array('data'=>$avaliacaos));
    }
    public function metricaAjax($avaliacao_id, $discipline_id, $course_id){
        $pea = PlanoEstudoAvaliacao::Join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                                    ->select(['study_plan_editions.period_type_id'])
                                    ->where('study_plan_editions.id', $course_id)
                                    ->first();
        $disc = Discipline::whereId($discipline_id)->first();
        // 2: 1º semestre
        // 3: 2º semestre
        $discCode = strval($disc->code);

        //   if ($pea->period_type_id == 2 && Str::contains($discCode, "A")) {
        //    return 1;
        //   } elseif ($pea->period_type_id == 3 && Str::contains($discCode, "A")) {
        //   return 2;
        //   } else {
        //   return 3;
        //   } 

        $metricas = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('metricas as mtrc', 'mtrc.avaliacaos_id', '=', 'avl.id')
                    ->select([
                        'mtrc.id as mtrc_id',
                        'mtrc.avaliacaos_id as mtrc_avaliacaos_id',
                        'mtrc.nome as mtrc_nome'
                    ])
                    //comparar se o period_type for 1 ou 2 semestre
                    //e volt
                     ->when($pea->period_type_id == 2 && Str::contains($discCode, "A"), function ($q) {
                         return $q->where('mtrc.nome', '!=', 'PF2');
                     })
                     ->when($pea->period_type_id == 3 && Str::contains($discCode, "A"), function ($q) {
                         return $q->where('mtrc.nome', '!=', 'PF1');
                     })
                    ->where('mtrc.avaliacaos_id', $avaliacao_id)
                   ->distinct('mtrc.nome')
                   ->get();

         return json_encode(array('data'=>$metricas, 'pea' => $pea));

    }
    public function metricaAjaxCoordenador ($id_avaliacao){
         $metrics = DB::table('metricas')
            ->where('avaliacaos_id',$id_avaliacao)
            ->where('deleted_at',null)
            ->where('deleted_by',null)
            ->where('calendario',0)
            ->get();

        return json_encode(array('metricas'=> $metrics));
    }
    public function studentAjax($id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id,$id_anoLectivo){

        //avaliar se a metrica ja foi concluida, se retornar algo é porque já foi concluida
         $lectiveYearSelected = DB::table('lective_years')
        ->where('id', $id_anoLectivo)
        ->first();

         $consulta_aluno=$this->students_matriculado($id); 
         $consulta_aluno->where('mc.class_id', $class_id)->get();

         $dados=$consulta_aluno->get();
         // return $ener = $consulta_aluno->get();
         $metrics = Metrica::whereAvaliacaosId($avaliacao_id)->get();

         $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->leftJoin('matriculations as mt', 'mt.user_id', '=', 'avaliacao_alunos.users_id')
                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                     $join->on('mt.user_id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                        })   
                     ->select(
                        'avaliacao_alunos.id as aaid',
                        'avaliacao_alunos.nota as aanota',
                        'avaliacao_alunos.users_id as user_id',
                        'mc.class_id as class_id',
                        'u_p.value as user_name',
                        'avaliacao_alunos.presence as presence'
                    )
                    //Aqui não seria o ID do Plano Estudo Avaliacaos?
                    ->where('pea.study_plan_editions_id', $study_plan_id)
                    ->where('avaliacao_alunos.metricas_id', $metrica_id)
                    ->where('pea.disciplines_id', $id)
                    ->where('mc.class_id', $class_id)
                    ->where('avaliacao_alunos.id_turma', $class_id)
                    ->orderBy('user_name', 'ASC')
                    ->get();

          return json_encode(array('metricas'=> $metrics,'students' => $dados, 'grades' => $grades));



    }
    //Metodo que pega todos os estudantes para atribuir OA
    public function studentAjaxOA_new($id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id,$id_anoLectivo,$numero_prova){      
        
        //avaliar se a metrica ja foi concluida, se retornar algo é porque já foi concluida
         $lectiveYearSelected = DB::table('lective_years')
        ->where('id', $id_anoLectivo)
        ->first();
         $consulta_aluno=$this->students_matriculado($id); 
         $consulta_aluno
  
         ->where('mc.class_id', $class_id)->get();
         $dados=$consulta_aluno->get();
          // return $ener = $consulta_aluno->get();

         $grades = DB::table('tmp_oa')
                    ->where('avaliacaos_id',$avaliacao_id)
                    ->where('class_id',$class_id)
                    ->where('oa_number',$numero_prova)
                    ->where('discipline_id',$id)
                    ->get();
            
            
            
          return json_encode(array('students' => $dados, 'grades' => $grades));



    }
    //Pega os estudades matriculados 
    private function students_matriculado($id){
        $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                    ->leftJoin('user_parameters as u_p', function ($join) {

                        $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_n', function ($join) {
                        $join->on('users.id', '=', 'up_n.users_id')
                             ->where('up_n.parameters_id', 19);
                    })
                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')
               

                    
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_n.value as n_student',
                        'mc.class_id as class_id',
                    ])
                  ->where('md.discipline_id', $id)

                  //->where('avl.id', 10) //USAR ID da tabela do servidor
                  //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                  ->orderBy('user_name', 'ASC')
                  ->distinct();
               return $students; 
    }
    public function showStudentGradesAjax($avaliacao_id, $discipline_id, $stdplanedition, $class_id){

      try {
        $studyPlan = StudyPlanEdition::whereId($stdplanedition)->first();
        $dd = StudyPlan::whereId($studyPlan->study_plans_id)->first();
        $groupOfStudyPlanEdition = StudyPlanEdition::whereStudyPlansId($dd->id)->get();


        //todas as edicioes de plano de estudo daquele curso
        $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();

        //ao tratar outras avaliacoes essa variavel causava erros...
        //so preciso dela quando a avaliacao selecionada for MAC == 21
        if($avaliacao_id == 21 || $avaliacao_id == 23){

            $current_pea = PlanoEstudoAvaliacao::where('disciplines_id', $discipline_id)
                                            ->where('avaliacaos_id', 21)
                                            ->firstOrFail();
        }else{
            $current_pea = 1;
        }


        $metricas = Metrica::select('metricas.percentagem', 'metricas.id as metrica_id', 'metricas.nome')
                            ->where('avaliacaos_id', $avaliacao_id)
                            ->get();

        $avaliacao = Avaliacao::whereId($avaliacao_id)->get();


        if ($avaliacao_id == 21 || $avaliacao_id == 23) {
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                     ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')
                   

                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  //Estudantes por turma
                  ->where('mc.class_id', $class_id)
                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 1, function ($q) use ($current_pea) {
                      return
                      $q->where(function ($query) use ($current_pea) {
                          $query->where('avl.id', 21)
                                ->where('aah.plano_estudo_avaliacaos_id', $current_pea->id)
                                ->where('aah.nota_final', '>=', '6.5');
                      });
                  })
                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 0, function ($q) use ($current_pea) {
                      return
                            $q->where(function ($query) use ($current_pea) {
                                $query->where('avl.id', 21)
                                    ->where('aah.plano_estudo_avaliacaos_id', $current_pea->id)
                                      ->whereBetween('aah.nota_final', ['6.5', '13']);
                            });
                  })
                  ->orderBy('u_p.value')
                  ->distinct()
                  ->get();
        }
        else if($avaliacao_id == 22)

        {


            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                            ->leftJoin('courses_translations as ct', function ($join) {
                                $join->on('ct.courses_id', '=', 'crs.id');
                                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('ct.active', '=', DB::raw(true));
                            })
                            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                            ->leftJoin('disciplines_translations as dt', function ($join) {
                                $join->on('dt.discipline_id', '=', 'dp.id');
                                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('dt.active', '=', DB::raw(true));
                            })
                            ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                            ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                            ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                            ->leftJoin('user_parameters as u_p', function ($join) {
                                $join->on('users.id', '=', 'u_p.users_id')
                                    ->where('u_p.parameters_id', 1);
                            })
                            ->leftJoin('user_parameters as up_n', function ($join) {
                                $join->on('users.id', '=', 'up_n.users_id')
                                    ->where('up_n.parameters_id', 19);
                            })
                            ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                            ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')
                            ->leftJoin('percentage_avaliation', 'percentage_avaliation.user_id', '=', 'users.id')
                            // ->leftJoin('article_requests', 'article_requests.user_id','=','users.id')
                            ->select([
                                //'mt.user_id',
                                'md.discipline_id',
                                'users.id as user_id',
                                'u_p.value as user_name',
                                'ct.display_name as course',
                                'dt.display_name as discipline',
                                'up_n.value as n_student',
                                'mc.class_id as class_id',
                                'percentage_avaliation.percentage_mac',
                                'percentage_avaliation.percentage_neen'
                            ])
                        ->where('md.discipline_id', $discipline_id)
                        ->where('mc.class_id', $class_id)
                        ->where('percentage_avaliation.discipline_id', $discipline_id)
                        //where date para trazer so o emolumentos pago durante o ano em questao
                        // ->where('article_requests.discipline_id', $discipline_id)
                        // ->where('article_requests.status',"total")
                        // ->where('article_requests.article_id', 36) //emolumento (exame de recurso)
                        // ->where(\DB::raw('percentage_avaliation.percentage_mac + percentage_avaliation.percentage_neen'))
                        //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                        //tenho que adicionar o where para me trazer so as desse ano lectivo
                        ->orderBy('user_name', 'ASC')
                        ->distinct()
                        ->get();

                        $dd = collect();

                        foreach ($students as $value) {
                            $sum = $value->percentage_mac + $value->percentage_neen;
                            if ($sum < 10) {
                                $dd->push([
                                    'discipline_id' => $value->discipline_id,
                                    'user_id'       => $value->user_id,
                                    'user_name'     => $value->user_name,
                                    'course'        => $value->course,
                                    'discipline'    => $value->discipline,
                                    'n_student'     => $value->n_student,
                                    'class_id'      => $value->class_id,
                                ]);
                            }
                        }
                        $students = $dd;

              //barcelona
        }
        elseif($avaliacao_id == 25){ 
        //exame especial
        //anchora_2
        //caso for exame especial
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_n', function ($join) {
                        $join->on('users.id', '=', 'up_n.users_id')
                             ->where('up_n.parameters_id', 19);
                    })
                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')
                    ->leftJoin('article_requests', 'article_requests.user_id','=','users.id')
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_n.value as n_student',
                        'mc.class_id as class_id'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  ->where('mc.class_id', $class_id)
                  ->where('article_requests.discipline_id', $discipline_id)
                  ->where('article_requests.status',"total")
                  ->where('article_requests.article_id', 32) //emolumento (exame de especial)

                  //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();
        }
        else
        
        {
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                     ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')

                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  //Estudantes por turma
                  ->where('mc.class_id', $class_id)

                  ->orderBy('u_p.value')
                  ->distinct()
                  ->get();
        }


        //Falta um WHERE
        $grades = AvaliacaoAluno::leftJoin('metricas as mtrc', "mtrc.id", "=", "avaliacao_alunos.metricas_id")
                      ->leftJoin('users as usr', 'usr.id', '=', 'avaliacao_alunos.users_id')
                      ->leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                      ->select('avaliacao_alunos.users_id', "mtrc.id", "avaliacao_alunos.metricas_id", 'avaliacao_alunos.nota')
                      //->where('avaliacao_alunos.metricas_id', $subMetrica->id)
                      ->where('pea.disciplines_id', $discipline_id)
                      ->where('pea.avaliacaos_id', $avaliacao_id)
                      ->whereIn('pea.study_plan_editions_id', $groupOfStudyPlanEdition->pluck('id'))
                     // ->where('pea.study_plan_editions_id', $stdplanedition)
                      ->get();


        return json_encode(array('metricas'=> $metricas,'students' => $students, 'grades' => $grades, 'avaliacao' => $avaliacao, 'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam));
          } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        /*
            1 - Avaliar se a avaliacao (ou tipo de avaliacao) é exame
            2 - Avaliar se a disciplina te exame obrigatorio ou nao
            3 - Avaliar se tem notas (MAC):
                - Maior ou igual a 6,5 exame
                - Menor que 6,5 recurso directo
            4 - Levar a mesma logica na hora de concluir a avaliacao
            Como é que pego a nota do MAC de cada estudante?
            R: a avaliacao tem que ser concluida e pegar a nota no historico

        */

    }                                                                                         
    


    //Pegar as avalições do coordenador ... por distinção
    private function avaliacaoes_coordenador($id_disciplina,$anoLectivo){   
     $avaliacaos = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'crs.id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
            })->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select(['avl.id as avl_id','avl.nome as avl_nome','dp.code as discipline_code']) 
            ->where('dp.id', $id_disciplina)
            ->where('c_p.deleted_by', null)
            ->where('c_p.lectiveYear',$anoLectivo)        
            ->distinct('avl_id');
           
     return $avaliacaos;
    }










    public function getPEAWithGrades(){

        try {
            $pea = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                        $join->on('spet.study_plan_editions_id', '=', 'stpeid.id');
                        $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('spet.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

                    ->select([
                            'stpeid.id as spea_id',
                            'spet.display_name as spea_nome'
                            ])
                    ->whereExists(function ($q) {
                            $q->select('plano_estudo_avaliacaos.id')
                            // $q->select('plano_estudo_avaliacaos.avaliacaos_id')
                            ->from('plano_estudo_avaliacaos')
                            ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                            // ->whereRaw('avaliacao_aluno_historico.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                            })
                    ->distinct()
                    ->get();

                    return response()->json($pea);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }
    public function getStudentSummaryGrades(){
        try {
            $pea = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                            ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                                $join->on('spet.study_plan_editions_id', '=', 'stpeid.id');
                                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('spet.active', '=', DB::raw(true));
                            })
                            ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

                                    ->select([
                                        'stpeid.id as spea_id',
                                        'spet.display_name as spea_nome'
                                    ])
                                    ->whereExists(function ($q) {
                                        $q->select('plano_estudo_avaliacaos.id')
                                    // $q->select('plano_estudo_avaliacaos.avaliacaos_id')
                                        ->from('plano_estudo_avaliacaos')
                                        ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                                        // ->whereRaw('avaliacao_aluno_historico.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                                    })
                                    ->distinct()
                                    ->get();

            return view("Avaliations::avaliacao-aluno.show-summary-avaliacao-aluno", compact('pea'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function getSummaryGrades($stdp_edition, $discipline_id){
        try {
            $avaliacaos = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                            ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                            ->where('disciplines_id', $discipline_id)
                            //->where('avaliacaos_id', 28)
                            //TODO: FAZER ESSE WHERE FUNCIONAR
                            //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                            ->get();
            //return $avaliacaos;

            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                            ->leftJoin('courses_translations as ct', function ($join) {
                                $join->on('ct.courses_id', '=', 'crs.id');
                                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('ct.active', '=', DB::raw(true));
                            })
                            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                            ->leftJoin('disciplines_translations as dt', function ($join) {
                                $join->on('dt.discipline_id', '=', 'dp.id');
                                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('dt.active', '=', DB::raw(true));
                            })
                            ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                            ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                            ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                            ->leftJoin('user_parameters as u_p', function ($join) {
                                $join->on('users.id', '=', 'u_p.users_id')
                                    ->where('u_p.parameters_id', 1);
                            })
                            ->select([
                                //'mt.user_id',
                                'md.discipline_id',
                                'users.id as user_id',
                                'u_p.value as user_name',
                                'ct.display_name as course',
                                'dt.display_name as discipline',
                                //'avaliacao_alunos.*'
                            ])
                        ->where('md.discipline_id', $discipline_id)
                        ->distinct()
                        ->get();


            $finalGrades = AvaliacaoAlunoHistorico::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id')
                ->leftJoin('users as users', 'users.id', '=', 'avaliacao_aluno_historicos.user_id')
                ->select([
                    'users.id as users_id',
                    'pea.id',
                    'avaliacao_aluno_historicos.avaliacaos_id as avaliacaos_id',
                    'avaliacao_aluno_historicos.nota_final as nota_final'
                    ])
                    //TODO: ADICIONAR UM WHERE COM O PLANO DE ESTUDO EDITION
                ->where('pea.disciplines_id', $discipline_id)
                ->get();

            return json_encode(array('avaliacaos'=> $avaliacaos,'students' => $students, 'finalGrades' => $finalGrades));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function AddOAGrades(){
        try {
            $teacher_id = Auth::user()->id;
        
            //Listar Edições de Plano de Estudo associados a plano_estudo_avaliacaos
            $pea = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'stpeid.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })
            ->leftJoin('study_plans as stp', 'stpeid.study_plans_id', '=', 'stp.id')
            ->leftJoin('courses as crs', 'stp.courses_id', '=', 'crs.id')
            ->leftJoin('disciplines as dcp', 'dcp.courses_id', '=', 'crs.id')
            ->leftJoin('user_disciplines as usdc', 'usdc.disciplines_id', '=', 'dcp.id')

            ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                    ->select([
                        'plano_estudo_avaliacaos.id as pea_id',
                        'stpeid.id as spea_id',
                        'spet.display_name as spea_nome'
                    ])
                    ->whereNotExists(function ($q) {
                        $q->select('plano_estudo_avaliacaos.id')
                        //$q->select('plano_estudo_avaliacaos.avaliacaos_id')
                          ->from('plano_estudo_avaliacaos')
                          ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                        //->whereRaw('avaliacao_aluno_historicos.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                    })
            //Selecionar só plano de estudo pelo id do Professor
            //RETIRAR
            ->where('usdc.users_id', $teacher_id)
            ->distinct()
            ->get();


            return view("Avaliations::avaliacao-aluno.add-oa-grade", compact('pea'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function StoreOAGrades(Request $request){
        //IMPORTANTE APOS A AVALIACAO SE CONCLUIDA LIMPAR A TABELA TMP_OA
        //NAO LIMPAR TODA TABELA LIMPAR SO OS DADOS EM QUESTAO (Estudantes da metrica_ SELECIONADA)
        DB::transaction(function () use ($request) {

            $discipline=explode(",",$request->disciplina);
            //Pegar o id do plano de estudo e avaliações
            $Plano_E_avaliacao = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->where('stpeid.id', $request->id_plano)
            ->where('plano_estudo_avaliacaos.disciplines_id',$discipline[2])
            ->where('plano_estudo_avaliacaos.avaliacaos_id',$request->id_avaliacao)
            ->select(['plano_estudo_avaliacaos.*','stpeid.*'])
            ->first();

            $metrica_id = $request->metrica_teacher;
            $course = $discipline[1]; //curso 
            $discipline_id = $discipline[2];
            $avaliacao_id = $request->id_avaliacao;
            



            $data = [
                     'notas' => $request->notas,
                     'estudantes' => $request->estudantes,
                    ];

        for ($i=0; $i < count($data['notas']); $i++) { 

                DB::table('tmp_oa')->updateOrInsert(
                     [
                        'user_id'       => $data['estudantes'][$i],
                         'oa_number'     => $request->oa_number,
                         'avaliacaos_id' => $avaliacao_id,
                         'discipline_id' => $discipline_id,
                         'courses_id'    => $course,
                         'class_id'      => $request->turma
                     ],
                     [
                         'grade'         => $data['notas'][$i] ?: 0,
                         'metricas_id'   => $metrica_id,
                         'created_at'   => date("Y-m-d H:i:s"),
                         'updated_at'   => date("Y-m-d H:i:s")
                     ]
                );

                /*
                    Depois de salvar fazer uma selecao de todas as notas do estudante[$i]
                    somar todas as notas e dividir pelo count() do resultado.
                    salvar o resultado na tabela de notas e dizer que e a metrica da OA
                */

                $somaOA =  DB::table('tmp_oa')
                                ->where('user_id', $data['estudantes'][$i])
                                ->where('class_id', $request->turma)
                                ->where('discipline_id', $discipline_id)
                                //aqui falta adicionar um where para quando a edicao de plano de estudo mudar (proximo ano)
                                ->sum('grade');

                $totalOA =  DB::table('tmp_oa')
                                ->where('user_id', $data['estudantes'][$i])
                                ->where('class_id', $request->turma)
                                ->where('discipline_id', $discipline_id)
                                //aqui falta adicionar um where para quando a edicao de plano de estudo mudar (proximo ano)
                                ->count();
                $mediaOA = $somaOA / $totalOA;

                $avaliacaoAluno =  AvaliacaoAluno::updateOrCreate(
                    [
                            'plano_estudo_avaliacaos_id' => $Plano_E_avaliacao->id,
                            'metricas_id' => $metrica_id,
                            'users_id' => $data['estudantes'][$i],
                            'id_turma' => $request->turma
                    ], 
                    [
                            'nota' => $mediaOA,
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id
                    ]
                );
            }
        });


        //Success message
        Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
        return back();
    }
    public function studentOAAjax($id, $study_plan_id, $avaliacao_id, $class_id, $oa){
        
        
        $teacher_id = Auth::user()->id;
        $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_n', function ($join) {
                        $join->on('users.id', '=', 'up_n.users_id')
                             ->where('up_n.parameters_id', 19);
                    })
                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')

                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_n.value as n_student'
                    ])
                  ->where('md.discipline_id', $id)
                  ->where('mc.class_id', $class_id)
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();

        $metrics = Metrica::whereAvaliacaosId($avaliacao_id)->get();

        $grades = DB::table('tmp_oa')
                    ->select(
                        'user_id',
                        'grade'
                    )->where('oa_number', $oa)
                    ->where('class_id', $class_id)
                    ->where('discipline_id', $id)
                    ->get();


        return json_encode(array('data'=> $grades,'students' => $students,'metrics'=>$metrics));
    }
    public function avaliacaoAjaxOA($id){
        $avaliacaos = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')

                    ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')

                    ->select([
                        'avl.id as avl_id',
                        'avl.nome as avl_nome',
                        'dp.code as discipline_code'
                    ])
                    ->where('dp.id', $id)
                    ->where('avl.id', 21)
                   ->distinct()
                   ->get();
        return json_encode(array('data'=>$avaliacaos));



    }
    public function metricaAjaxOA($id){
        $metricas = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('metricas as mtrc', 'mtrc.avaliacaos_id', '=', 'avl.id')
                    ->select([
                        'mtrc.id as mtrc_id',
                        'mtrc.avaliacaos_id as mtrc_avaliacaos_id',
                        'mtrc.nome as mtrc_nome'
                    ])
                    ->where('mtrc.avaliacaos_id', $id)
                    ->where('mtrc.nome', "OA")
                    ->orWhere('mtrc.nome', "AO")
                   ->distinct('mtrc.nome')
                   ->get();
        return json_encode(array('data'=>$metricas));
    }
    public function generatePartialPDF($avaliacao_id, $discipline_id, $stdplanedition, $class_id) {
        //anchor
        $discipline = Discipline::with([
                 'currentTranslation',
                 'study_plans_has_disciplines' => function ($q) {
                     $q->with(['discipline_period' => function ($q) {
                         $q->with('currentTranslation');
                     }]);
                 },
                 'course' => function ($q) {
                     $q->with('currentTranslation');
                 }])->where('id', $discipline_id)->firstOrFail();

        $class = Classes::whereId($class_id)->firstOrFail();

        $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();


        $metricas = Metrica::select('metricas.percentagem', 'metricas.id as metrica_id', 'metricas.nome')
                            ->where('avaliacaos_id', $avaliacao_id)
                            ->get();

        $avaliacao = Avaliacao::whereId($avaliacao_id)->get();

        if($avaliacao_id == 21 || $avaliacao_id == 23)
        {
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                     ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')

                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  //Estudantes por turma
                  ->where('mc.class_id', $class_id)
                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 1, function ($q) {
                      return
                      $q->where(function ($query) {
                          $query->where('avl.id', 21)
                                ->where('aah.nota_final', '>=', '6.5');
                      });
                  })
                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 0, function ($q) {
                      return
                            $q->where(function ($query) {
                                $query->where('avl.id', 21)
                                      ->whereBetween('aah.nota_final', ['6.5', '13']);
                            });
                  })
                  ->orderBy('u_p.value')
                  ->distinct()
                  ->get();

        }
        elseif($avaliacao_id == 22){
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                     ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')
                     ->leftJoin('percentage_avaliation', 'percentage_avaliation.user_id', '=', 'users.id')
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'percentage_avaliation.percentage_mac',
                        'percentage_avaliation.percentage_neen'
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  //Estudantes por turma
                  ->where('mc.class_id', $class_id)
                  //tenho que adicionar o where para me trazer so os do ano lectivo
                  ->orderBy('u_p.value')
                  ->distinct()
                  ->get();

                  $dd = collect();

                        foreach ($students as $value) {
                            $sum = $value->percentage_mac + $value->percentage_neen;
                            if ($sum < 10) {
                                $dd->push([
                                    'discipline_id' => $value->discipline_id,
                                    'user_id'       => $value->user_id,
                                    'user_name'     => $value->user_name,
                                    'course'        => $value->course,
                                    'discipline'    => $value->discipline,
                                    'n_student'     => $value->n_student,
                                    'class_id'      => $value->class_id,
                                ]);
                            }
                        }

                        $students = $dd;

        }else{
            $students = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'dp.id')
                    ->leftJoin('matriculations as mt', 'mt.id', '=', 'md.matriculation_id')
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('users as users', 'users.id', '=', 'mt.user_id')
                     ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('users.id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->leftJoin('avaliacao_aluno_historicos as aah', 'aah.user_id', '=', 'users.id')

                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  //Estudantes por turma
                  ->where('mc.class_id', $class_id)
                  ->orderBy('u_p.value')
                  ->distinct()
                  ->get();
        }


        //Falta um WHERE
        $grades = AvaliacaoAluno::leftJoin('metricas as mtrc', "mtrc.id", "=", "avaliacao_alunos.metricas_id")
                      ->leftJoin('users as usr', 'usr.id', '=', 'avaliacao_alunos.users_id')
                      ->leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                      ->select('avaliacao_alunos.users_id', "mtrc.id", "avaliacao_alunos.metricas_id", 'avaliacao_alunos.nota')
                      //->where('avaliacao_alunos.metricas_id', $subMetrica->id)
                      ->where('pea.disciplines_id', $discipline_id)
                      ->where('pea.avaliacaos_id', $avaliacao_id)
                      //->where('pea.study_plan_editions_id', $stdplanedition)
                      ->get();

        $data = [
            'metricas'=> $metricas,
            'students' => $students,
            'grades' => $grades,
            'avaliacao' => $avaliacao,
            'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam,
            'discipline' => $discipline,
            'class' => $class
        ];

        return response()->json($data);
        //return view("Avaliations::avaliacao-aluno.reports.pdf_grade", $data);
    }
    public function studentGrade(){
        
        try {
            $student_id = Auth::user()->id;

            $courses = Course::with(['currentTranslation'])->get();


            $classes = User::join('user_classes', 'user_classes.user_id', '=', 'users.id')
                        ->join('classes', 'classes.id', '=', 'user_classes.class_id')
                        ->where('users.id', $student_id)
                        ->select('user_classes.class_id as id', 'classes.display_name as display_name')
                        ->get()
                        ->map(function ($class) {
                            return ['id' => $class->id, 'display_name' => $class->display_name];
                        });

            $disciplines = User::join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
                ->join('disciplines as dc', 'dc.id', '=', 'matriculation_disciplines.discipline_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'dc.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'dc.id')
                ->where('users.id', $student_id)
                ->select('dc.id as discipline_id', 'dt.display_name as display_name', 'discipline_has_exam.has_mandatory_exam')
                ->get();

            $avaliacaos = AvaliacaoAluno::join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                            ->join('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                            ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                            ->select('avl.id as avaliacaos_id', 'avl.nome as nome')
                            ->whereIn('plano_estudo_avaliacaos.disciplines_id', $disciplines->pluck('discipline_id'))
                            ->distinct()
                            ->get();

            $metricas = Metrica::join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->join('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                    ->whereIn('pea.disciplines_id', $disciplines->pluck('discipline_id'))
                    ->orderBy('metricas.id')
                    ->distinct()
                    ->get();

            $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    // ->where('lective_year') codigo futuro, pegar notas publicadas por ano lectivo
                    ->where('avaliacao_alunos.users_id', $student_id)
                    // ->whereIn('published_metric_grade.discipline_id',$disciplines->pluck('discipline_id'))
                    ->get();

            $finalGrades = AvaliacaoAlunoHistorico::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id')
                ->leftJoin('users as users', 'users.id', '=', 'avaliacao_aluno_historicos.user_id')
                ->select([
                    'users.id as users_id',
                    'pea.id',
                    'avaliacao_aluno_historicos.avaliacaos_id as avaliacaos_id',
                    'avaliacao_aluno_historicos.nota_final as nota_final',
                    'pea.disciplines_id as disciplines_id'
                    ])
                ->where('users.id', $student_id)
                ->get();


            $gradesWithPercentage = DB::table('percentage_avaliation')
                ->where('user_id', $student_id) //depois avaliar para quando selecionarem estudante
                ->select('user_id', DB::raw('percentage_mac + percentage_neen as grade'), 'discipline_id')
                ->get();

            $data = [
                        'courses' => $courses,
                        'classes' => $classes,
                        'disciplines' => $disciplines,
                        'avaliacaos' => $avaliacaos,
                        'metricas' => $metricas,
                        'grades' => $grades,
                        'finalGrades' => $finalGrades,
                        'gradesWithPercentage' => $gradesWithPercentage
                    ];
            return view("Avaliations::avaliacao-aluno.student.grade")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function getStudentsByCourse($course_id){
        /*está a trazer ate professores */
        /* $students = User::join('user_courses','user_courses.users_id','=','users.id')
                 ->where('user_courses.courses_id', $course_id)
                 ->orderBy('name')
                 ->get();*/

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
                ->where('uc.courses_id', $course_id)
                ->select([
                    //'u0.name as student',
                    'u0.id as id',
                    'u_p.value as name',
                    'u0.email as email',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name as course',
                    'up_meca.value as mecanografico'
                ])
                ->orderBy('name')
                ->get();
        return response()->json($students);
    }








   













































   
   















     

    





































    // Metodo que o ano Lectivo quando o fromulario é carregado.
    public function getStudentFinalGrades(){
        try {
             // 1º Pegar o ano lectivo  para p eletor na select
             $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
     
           $currentData = Carbon::now();
           $lectiveYearSelected = DB::table('lective_years')
                 ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                 ->first();
                 $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

           $data=[
                    'lectiveYearSelected'=>$lectiveYearSelected,
                    'lectiveYears'=>$lectiveYears
                ];

                
            return view("Avaliations::avaliacao-aluno.show-final-avaliacao-aluno")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
        public function docent_disciplines(){
        try {
             // 1º Pegar o ano lectivo  para p eletor na select
             $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
     
           $currentData = Carbon::now();
           $lectiveYearSelected = DB::table('lective_years')
                 ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                 ->first();
                 $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

           $data=[
                    'lectiveYearSelected'=>$lectiveYearSelected,
                    'lectiveYears'=>$lectiveYears
                ];

                
            return view("Avaliations::matriculations.disciplines")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
    // metodo que pega o ano lectivo para enviar as disciplina de acordo o ano lectivo.
    
    public function getDocenteDisciplina($id_anoLectivo){
      //Método pega todas as disciplinas associadas aos planos de estudo e avaliaçoes 
        //do respectivos professores, coordenador do curso ou outro cargo
        try {
           $teacher_id = Auth::user()->id;
           $user = User::whereId($teacher_id)->firstOrFail();
           //se o coordenador for o logado na plataforma 
           //Entra neste bloco e trás toda as disciplinas do curso
           if($user->hasAnyRole(['coordenador-curso'])) {
               $course_ids = DB::table('coordinator_course')
               ->where('user_id',$teacher_id)
               ->get();
               $disciplinas_coordenador=$this->disciplinas_coordenador_todas($course_ids,$id_anoLectivo);
               return response()->json(['disciplina'=>$disciplinas_coordenador,'whoIs'=>"coordenador"]);
            } 
            //Quando for Professor pegar as disciplina
            //que ele leciona.
            else if($user->hasAnyRole(['teacher'])){
            //Pegar as disciplinas do professor Logado
            $disciplines=$this->disciplina_teacher_apenas($teacher_id,$id_anoLectivo);   
            return response()->json(['disciplina'=>$disciplines,'whoIs'=>"teacher"]);

            } 

      }catch(Exception | Throwable $e){
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);

        }
           
    }
    //metodo para pegar as disciplinas do cordenandor
    private function disciplinas_coordenador_todas($ids_curso,$id_anoLectivo){
        $getDisciplinesAll = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
               ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
               ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
               ->leftJoin('courses_translations as ct', function ($join) {
                   $join->on('ct.courses_id', '=', 'crs.id');
                   $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                   $join->on('ct.active', '=', DB::raw(true));
               })
               ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
               ->leftJoin('disciplines_translations as dt', function ($join) {
                   $join->on('dt.discipline_id', '=', 'dp.id');
                   $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                   $join->on('dt.active', '=', DB::raw(true));
               })
               ->leftJoin('user_disciplines', 'user_disciplines.disciplines_id', '=', 'dp.id')
               ->select([
                   'crs.id as course_id',
                   'ct.display_name as course_name',
                   'dp.id as discipline_id',
                   'dp.code as code',
                   'dt.display_name as dt_display_name',

               ])
  
               ->whereIn('dp.courses_id',$ids_curso->pluck('courses_id'))
               ->distinct()
               ->orderBy('dp.code')
               ->get();

           return $getDisciplinesAll;

    }
    // metodo para pegar apenas as disciplinas de um professor.
    private function disciplina_teacher_apenas($teacher_id,$id_anoLectivo){
        $getMyDisciplines = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                               ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                               ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                               ->leftJoin('courses_translations as ct', function ($join) {
                                   $join->on('ct.courses_id', '=', 'crs.id');
                                   $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                   $join->on('ct.active', '=', DB::raw(true));
                               })
                               ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                               ->leftJoin('disciplines_translations as dt', function ($join) {
                                   $join->on('dt.discipline_id', '=', 'dp.id');
                                   $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                   $join->on('dt.active', '=', DB::raw(true));
                               })
                               ->leftJoin('user_disciplines', 'user_disciplines.disciplines_id', '=', 'dp.id')
                               ->select([
                                   'crs.id as course_id',
                                   'ct.display_name as course_name',
                                   'dp.id as discipline_id',
                                   'dp.code as code',
                                   'dt.display_name as dt_display_name',
                                   'user_disciplines.users_id as id_teacher'
                               ])
                               // ->where('stpeid.id', $id)
                                ->where('stpeid.lective_years_id',$id_anoLectivo)
                               ->where('user_disciplines.users_id', $teacher_id)
                               //->where('dp.courses_id',$course_id->courses_id)
                               ->distinct()
                               ->get();
                       
                 return  $dados=$getMyDisciplines;
    }
    // Metodo que pega a turma, em que o professor leciona a turma.
    public function getTurmasDisciplina($id_edicao_plain,$anoLectivo){                                                                                                           
        try{  
            
           $id = explode(",", $id_edicao_plain);
    
            $cargo=$id[0]; $id_curso=$id[1]; $id_disciplina=$id[2]; $currentData = Carbon::now(); $teacher_id = Auth::id(); 
            //Pega o ano curricular da disciplina.
            //pega tbm o id_plano_estudo.

            
            //Periodo da disciplina (saber se é anual ou simestral)
            $period_disciplina=DB::table('disciplines')
            ->where('id',$id_disciplina)
            ->get();

             $Simestre = $period_disciplina->map(function($item,$key){
                $periodo=substr($item->code,-3, 1);
                if($periodo=="1"){return "1_simestre";}
                if($periodo=="2"){return "2_simestre";}
                if($periodo=="A"){return "Anual";}
                else{return 0;}
                
            });
            //Fim do perios
    
            $courseYear = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            // ->where('stpeid.id', $id_edicao_plain)
            ->where('plano_estudo_avaliacaos.disciplines_id',$id_disciplina)
            ->where('stpeid.lective_years_id',$anoLectivo)
            ->select(['plano_estudo_avaliacaos.*','stpeid.*'])
            ->get();
            
            //Primeiro if compara se está fazia a associação entre disciplina e plano de estudo e avalização.
            if(!$courseYear->isEmpty()){
                $id_plano_estudo=$courseYear[0]['study_plan_editions_id'];
                if($cargo=="coordenador"){ 
                     //Pega avalicao mac e metrica OA (Sem cumprir a regra do calendário de data) 
                    //  $avaliacao=$this->avaliacaoesOA($id_disciplina,$anoLectivo);
                     //Pega as todas as turmas do coordenador 
                    $turmas=$this->turmas_coordenador($courseYear,$id_plano_estudo,$id_curso,$anoLectivo);
                    return response()->json(['turma'=>$turmas,'whoIs'=>"super",'plano_estudo'=>$id_plano_estudo,'disciplina'=>$id_disciplina,'periodo'=>$Simestre]);
                }
                if($cargo=="teacher"){
                    
                    //Pega toda as turmas do professor onde ele leciona esta disciplina
                    $turmas=$this->turmas_teacher($teacher_id,$courseYear,$id_plano_estudo,$anoLectivo);
                    return response()->json(['turma'=>$turmas,'whoIs'=>'teacher','plano_estudo'=>$id_plano_estudo,'disciplina'=>$id_disciplina, 'periodo'=>$Simestre]);
                }
    
            }else{
                  return response()->json(500);
            }
    
            //Pega todas as avaliaçoes das disciplina selecionada
            //como o objectivo é retornar por época de calendário 
            //A ideia é colocar um calendário nas Mac e criar também calendário de cada item da Mac(PF1, PF2 e OA)
            //No final retornar apenas a pauta do item da avaliaçao selecionada, 
            //EX: seleciona a disciplina, seleciona a turma e no final aparece a pauta daquela época.
            // return response()->json(array('turma'=>$turma, 'metrica'=>$metrica_filtrada, 'pea'=>$pea));
        }
        catch(Exception | Throwable $e){                                                                                                                                                      
            logError($e);                                                                                           
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);        
        }                                                                                                   
                                                                                                            
    }
    // Metodo private pegar aas turmas da coodernadora de acordo o ano lectivo
    private function turmas_coordenador($courseYear,$plano_edition,$id_curso,$anoLectivo){
        $turma =  PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
            ->leftJoin('user_classes','user_classes.class_id', '=', 'classes.id')
            // ->where('user_classes.user_id', $id_teacher)
            ->where('stpeid.id', $plano_edition)
            ->where('classes.year', $courseYear[0]->course_year)
            ->where('classes.courses_id', $id_curso)
            ->where('classes.lective_year_id', $anoLectivo)
            ->select('classes.*')
            //->select('classes.id as id', 'classes.display_name as display_name')
            ->distinct()
            ->get();
     return $turma;
    }
    // metodo private que as turma de acordo a disciplina do professor
    private function turmas_teacher($id_teacher,$courseYear,$plano_edition,$anoLectivo){
            $turma =  PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
            ->leftJoin('user_classes', 'user_classes.class_id', '=', 'classes.id')
            ->where('user_classes.user_id', $id_teacher)
            ->where('stpeid.id', $plano_edition)
            ->where('classes.year', $courseYear[0]->course_year)
            ->where('classes.lective_year_id', $anoLectivo)
            ->select('classes.*')
             // ->select('classes.id as id', 'classes.display_name as display_name')
            ->distinct()
             ->get();
            return $turma;
    }
    // metodo que as metricas de acordo as disciplina
    public function getTurmasDisciplina_metricas($id_plano,$anolectivo){
        try {
            $id = explode(",",$id_plano);
    
            $cargo=$id[0]; $id_curso=$id[1]; $id_disciplina=$id[2]; $currentData = Carbon::now(); $teacher_id = Auth::id(); 

            $avaliacaos = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')
            // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select(['avl.id as avl_id','avl.nome as avl_nome','dp.code as discipline_code','mt.nome as nome_metrica','mt.id as id_metrica','mt.calendario as calendario_mt' ]) 
            ->where('dp.id', $id_disciplina)
            ->where('mt.deleted_by', null)
            ->where('mt.deleted_at', null)
            ->where('stp.courses_id', $id_curso)
            ->where('avl.anoLectivo',$anolectivo)
            ->distinct()
            ->get();

          

            return response()->json(array('data'=>$avaliacaos));
            

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function getAvaliacaoAo_student($id_metrica,$id_turma,$id_plano,$ano_lectivo,$valor_oa){
        
        $id = explode(",",$id_plano);
    
            $cargo=$id[0]; $id_curso=$id[1]; $id_disciplina=$id[2]; $currentData = Carbon::now();  

            $avaliacao_Oa = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')
            ->leftJoin('tmp_oa as oa',function ($join){
                $join->on('oa.metricas_id', '=', 'mt.id');
                $join->on('oa.discipline_id', '=', 'dp.id');
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('oa.user_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('oa.user_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
            })
            
            ->select(['avl.id as avl_id','avl.nome as avl_nome','dp.code as discipline_code','mt.nome as nome_metrica','mt.id as id_metrica','mt.calendario as calendario_mt', 'oa.oa_number as metrica_ao', 'oa.grade as nota_oa', 'oa.id as id_metricasOAs','full_name.value as nome_aluno','up_meca.value as matricula']) 
            ->where('oa.discipline_id', $id_disciplina)
            ->where('oa.metricas_id', $id_metrica)
            ->where('mt.deleted_by', null)
            ->where('mt.deleted_at', null)
            ->where('oa.class_id', $id_turma)
            ->where('oa.oa_number', $valor_oa)
            ->where('stp.courses_id', $id_curso)
            ->where('avl.anoLectivo',$ano_lectivo)
            ->distinct();
            
            // return Datatables::of($avaliacao_Oa)
            //  ->addIndexColumn()
            //  ->toJson();

       return response()->json(array('data'=>$avaliacao_Oa));
    }
    // metodo que gera PDF
    public function generatePDF($id_courso,$id_turma,$id_disciplina,$id_metrica,$anolectivo){ 
        
        // return $id_courso."/".$id_turma."/".$id_disciplina."/".$id_metrica."/".$anolectivo;
        try {
            $discipline = Discipline::with([
                 'currentTranslation',
                 'study_plans_has_disciplines' => function ($q) {
                     $q->with(['discipline_period' => function ($q) {
                         $q->with('currentTranslation');
                     }]);
                 },
                 'course' => function ($q) {
                     $q->with('currentTranslation');
                 }])->where('id', $id_disciplina)->firstOrFail();


          $metrics = Metrica::join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->join('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $id_disciplina)
                    ->where('metricas.id',$id_metrica)
                    ->orderBy('metricas.id')
                    ->distinct()
                    ->get();


      
           $lectiveYear = DB::table('lective_year_translations')
            ->select('lective_year_translations.display_name')
            ->where('lective_year_translations.lective_years_id',$anolectivo)
            ->where('lective_year_translations.active',1)
            ->first();
           

            $turma =  PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
            ->leftJoin('user_classes', 'user_classes.class_id', '=', 'classes.id')
            ->where('classes.id', $id_turma)
            ->where('classes.lective_year_id', $anolectivo)
            ->select('classes.*')
             // ->select('classes.id as id', 'classes.display_name as display_name')
            ->distinct()
             ->get();
          


          $avaliacaos_student= PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dp.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')

                    ->leftJoin('avaliacao_alunos as avl_aluno', function ($join){
                        $join->on('avl_aluno.metricas_id','=','mt.id');
                        $join->on('avl_aluno.plano_estudo_avaliacaos_id','=','plano_estudo_avaliacaos.id');
                    })
                    ->leftJoin('user_parameters as full_name', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                                ->where('up_meca.parameters_id', 19);
                    })
                // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
                ->select([
                    'ct.display_name as nome_courso',
                    'dt.display_name as nome_disciplina',
                    'dp.code as codigo',
                    'full_name.value as nome_aluno',
                    'avl_aluno.nota as nota_aluno',
                    'stpeid.course_year as ano',
                    'up_meca.value as code_aluno'
                ]) 
                ->where('dp.id', $id_disciplina)
                ->where('mt.deleted_by', null)
                ->where('avl_aluno.metricas_id',$id_metrica)
                ->where('avl_aluno.id_turma',$id_turma)
                ->where('stp.courses_id', $id_courso)
                ->where('stpeid.lective_years_id',$anolectivo)
                ->distinct('matriculations.code')
                ->get();



         // $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 10;   

        //Pegar área , regime e
        $regime=substr($avaliacaos_student[0]->codigo,-3, 1);
        $regimeFinal="";
        if($regime=="1" || $regime=="2"){$regimeFinal=$regime.'º'."Simestre";}
        else if($regime=="A"){ $regimeFinal="Anual";} 
   

        //dados da instituição
         $institution = Institution::latest()->first();   
         //Logotipo
         $Logotipo_instituicao="https://".$_SERVER['HTTP_HOST']."/storage/".$institution->logotipo;           

         $data = [
                'logotipo' => $Logotipo_instituicao,
                'turma'=> $turma[0]->display_name??"-",
                'curso'=> $avaliacaos_student[0]->nome_courso??"-",
                'ano_curricular'=> $avaliacaos_student[0]->ano??"-",
                'regimeFinal' => $regimeFinal,
                'lectiveYear'=> $lectiveYear->display_name,
                'avaliacaos_student'=> $avaliacaos_student,
                'discipline' => $discipline,
                'discipline_code' => $avaliacaos_student[0]->codigo.'-'.$avaliacaos_student[0]->nome_disciplina,
                'discipline_name' => $avaliacaos_student[0]->nome_disciplina." - ".$metrics[0]->nome,
                'institution' => $institution,
                'metrics' => $metrics,
                'prova'=>$metrics[0]->nome,
                'documentoCode_documento' => $documentoCode_documento
                
            ];
        // return view("Avaliations::avaliacao-aluno.reports.pdf", $data);

        $pdf = PDF::loadView("Avaliations::avaliacao-aluno.reports.pdf", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            /*$pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);*/
            $pdf->setPaper('a4');

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
            
            return $pdf->stream('Pauta Final'. '.pdf');

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
        
    }



    
    public function generatePDF_Oas($id_curso,$id_turma,$id_disciplina,$id_metrica,$ano_lectivo,$valor_oa){    
        
        try {

            $discipline = Discipline::with([
                'currentTranslation',
                'study_plans_has_disciplines' => function ($q) {
                    $q->with(['discipline_period' => function ($q) {
                        $q->with('currentTranslation');
                    }]);
                },
                'course' => function ($q) {
                    $q->with('currentTranslation');
                }])->where('id', $id_disciplina)->firstOrFail();

             $avaliacao_Oa = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')
            ->leftJoin('tmp_oa as oa',function ($join){
                $join->on('oa.metricas_id', '=', 'mt.id');
                $join->on('oa.discipline_id', '=', 'dp.id');
            })
    
            ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
            ->leftJoin('user_classes', 'user_classes.class_id', '=', 'classes.id')
    
    
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('oa.user_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('oa.user_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
            })
    
            ->select(['ct.display_name as nome_courso',
             'dt.display_name as nome_disciplina', 
             'stpeid.course_year as ano', 
             'mt.nome as nome_metrica', 
             'classes.lective_year_id',
            'crs.code', 'classes.display_name as turma', 'ct.display_name as nome_courso',
            'dt.display_name as nome_disciplina',
            'full_name.value as nome_aluno',
            'oa.grade as nota_aluno',
            'stpeid.course_year as ano',
            'up_meca.value as code_aluno',
            'classes.display_name as turma_dados',]) 

            ->where('oa.discipline_id', $id_disciplina)
            ->where('oa.metricas_id', $id_metrica)
            ->where('mt.deleted_by', null)
            ->where('mt.deleted_at', null)
            ->where('oa.class_id', $id_turma)
            ->where('oa.oa_number', $valor_oa)
            ->where('stp.courses_id', $id_curso)
            ->where('avl.anoLectivo',$ano_lectivo)        
            ->where('classes.id', $id_turma)
            ->where('classes.lective_year_id', $ano_lectivo)
            
            ->distinct()
            ->get();    
    
            
            //DADOS            
            $turma =  PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                ->leftJoin('user_classes', 'user_classes.class_id', '=', 'classes.id')
                ->where('classes.id', $id_turma)
                ->where('classes.lective_year_id', $ano_lectivo)
                ->select('classes.*')
                 // ->select('classes.id as id', 'classes.display_name as display_name')
                ->distinct()
                 ->get();

            $lectiveYear = DB::table('lective_year_translations')
                 ->select('lective_year_translations.display_name')
                 ->where('lective_year_translations.lective_years_id',$ano_lectivo)
                 ->where('lective_year_translations.active',1)
                 ->get();                        

                       

  

            
    
            $data = [
                'avaliacaos_student'=> $avaliacao_Oa,
                'turma'=> $turma,
                'lectiveYear'=> $lectiveYear,
                'discipline' => $discipline,
                'oa' => $valor_oa
                // 'avaliacaos'=> $avaliacaos,
                // 'students' => $students,
                // 'finalGrades' => $finalGrades,
                // 'grades' => $grades,
                // 'example' => $example,
                // 'gradesWithPercentage' => $gradesWithPercentage,
                // 'class' => $class,
                // 'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam
            ];
            //return $data;
    
            $pdf = PDF::loadView("Avaliations::avaliacao-aluno.reports.pdf_oa", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            /*$pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);*/
            $pdf->setPaper('a4');
    
            $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
            $pdf->setOption('footer-html', $footer_html);
            return  $pdf->stream('Pauta Final'. '.pdf');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }               
                
    }
    // metodo que pega os estudantes e as suas notas
    public function getStudentNotas($planno_disciplina,$id_turma,$anolectivo,$id_metrica){
        $id = explode(",",$planno_disciplina);
        $cargo=$id[0]; $id_curso=$id[1]; $id_disciplina=$id[2]; $currentData = Carbon::now(); $teacher_id = Auth::id(); 

        $avaliacaos_student= PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')

            ->leftJoin('avaliacao_alunos as avl_aluno', function ($join){
                $join->on('avl_aluno.metricas_id','=','mt.id');
                $join->on('avl_aluno.plano_estudo_avaliacaos_id','=','plano_estudo_avaliacaos.id');
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                 ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('users as u1', 'u1.id', '=', 'avl_aluno.created_by')
            ->leftJoin('user_parameters as up_teacher', function ($join) {
                $join->on('u1.id', '=', 'up_teacher.users_id')
                ->where('up_teacher.parameters_id', 1);
            })
            ->leftJoin('users as u2', 'u2.id', '=', 'avl_aluno.created_by')
            ->leftJoin('user_parameters as u_teacher', function ($join) {
                $join->on('u2.id', '=', 'u_teacher.users_id')
                ->where('u_teacher.parameters_id', 1);
            })
            
             // ->leftJoin('users as us',function($join){
            //     $join->on();
            // })
            // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select([
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'avl_aluno.created_at as criado_a',
                'avl_aluno.updated_at as actualizado_a',
                'up_meca.value as code_matricula',  
                'up_teacher.value as teacher_create',
                'u_teacher.value as teacher_update'
            ]) 
            ->where('dp.id', $id_disciplina)
            ->where('mt.deleted_by', null)
            ->where('avl_aluno.metricas_id',$id_metrica)
            ->where('avl_aluno.id_turma',$id_turma)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id',$anolectivo)
            ->distinct()
            ->get();
            

             return response()->json(array('data'=>$avaliacaos_student));  
    }


    public function gerentePDF_pautaFinal(){               
        //VARIAVEIS
        $ano_lectivo = 7;
        $id_turma = 161;
        $id_courso = 11;
        $id_disciplina = 161;
        $anoCurso = 2;

       return $cabecalhoDisciplina= PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')

            // ->leftJoin('avaliacao_alunos as avl_aluno', function ($join){
            //     $join->on('avl_aluno.metricas_id','=','mt.id');
            //     $join->on('avl_aluno.plano_estudo_avaliacaos_id','=','plano_estudo_avaliacaos.id');
            // })
            // ->leftJoin('user_parameters as full_name', function ($join) {
            //     $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
            //         ->where('full_name.parameters_id', 1);
            // })
            // ->leftJoin('user_parameters as up_meca', function ($join) {
            //     $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
            //             ->where('up_meca.parameters_id', 19);
            // })
            // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select([
                'mt.nome as nome_mterica',
                'ct.display_name as nome_courso'
                
                ]) 
            ->where('dp.id', $id_disciplina)
            // ->where('mt.deleted_by', null)
            // ->where('avl_aluno.metricas_id',$id_metrica)
            // ->where('avl_aluno.id_turma',$id_turma)
            // ->where('stp.courses_id', $id_courso)
            ->where('stpeid.lective_years_id',$ano_lectivo)
            ->distinct('mt.id')
        ->get();

        $avaliacaos_student= PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'crs.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('metricas as mt','mt.avaliacaos_id','=','avl.id')

            ->leftJoin('avaliacao_alunos as avl_aluno', function ($join){
                $join->on('avl_aluno.metricas_id','=','mt.id');
                $join->on('avl_aluno.plano_estudo_avaliacaos_id','=','plano_estudo_avaliacaos.id');
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
            })
            // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select([
                'mt.nome as nome_mterica',
                'ct.display_name as nome_courso',
                'dt.display_name as nome_disciplina',
                'full_name.value as nome_aluno',
                'avl_aluno.nota as nota_aluno',
                'stpeid.course_year as ano',
                'up_meca.value as code_aluno'
                ]) 
            ->where('dp.id', $id_disciplina)
            ->where('mt.deleted_by', null)
            // ->where('avl_aluno.metricas_id',$id_metrica)
            ->where('avl_aluno.id_turma',$id_turma)
            ->where('stp.courses_id', $id_courso)
            ->where('stpeid.lective_years_id',$ano_lectivo)
            ->distinct('matriculations.code')
        ->get();
                

        
        $data = [
            'avaliacaos_student'=> $avaliacaos_student,
        ];
        
        $pdf = PDF::loadView("Avaliations::avaliacao-aluno.reports.pdf-pautaFinal", $data);
        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '13mm');
        $pdf->setOption('margin-right', '2mm');
        /*$pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);*/
        $pdf->setPaper('a4','landscape');

        $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
        $pdf->setOption('footer-html', $footer_html);
        return  $pdf->stream('Pauta Final'. '.pdf');
    }

















   
    
}


