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
use Illuminate\Support\Facades\Schema;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

use PDF;

class AvaliacaoAlunoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            

            return view("Avaliations::avaliacao-aluno.avaliacao-aluno");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    public function ajax()
    {
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
                    ])
                    ->where('avaliacaos.anoLectivo',7)
                    ->where('ta.anoLectivo',7)
                    ;

            return DataTables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('Avaliations::avaliacao.datatables.actions')->with('item', $item);
                    })
                /*  ->editColumn('created_at', function ($item) {
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
    public function create(Request $request)
    {
        try {
           
            $segunda_chamada = (boolean)$request->query('segunda_chamada',null);
          
             //Pegar o ano lectivo na select
             $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
             $currentData = Carbon::now();
             $lectiveYearSelected = DB::table('lective_years')
             ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
             ->first();
             $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
             //-----------------------------------------------------------------------
        
             $data = [
                // 'courses' => $courses->get(),
                'lectiveYearSelected'=>$lectiveYearSelected,
                'lectiveYears'=>$lectiveYears,
                'segunda_chamada'=> $segunda_chamada
            ];

           
           
                return view("Avaliations::avaliacao-aluno.create-avaliacao-aluno-new")->with($data);
         

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
    public function store(Request $request)
    {
        //return $request;
        try {
            DB::transaction(function () use ($request) {
                $metrica_id = $request->get('metrica_id');
                $discipline_id = $request->get('discipline_id');
                $avaliacao_id = $request->get('avaliacao_id');

                $sped = $request->get('course_id');

                $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                ->select('plano_estudo_avaliacaos.id')
                ->where('plano_estudo_avaliacaos.study_plan_editions_id', $sped)
                ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                ->first();

                $data = [
                'notas'=> $request->get('notas'),
                'estudantes' => $request->get('estudantes'),
                'presences' => $request->get('inputCheckBox')
            ];

                for ($i=0; $i < count($data['notas']); $i++) {
                    $avaliacaoAluno =  AvaliacaoAluno::updateOrCreate(
                        [
                        'plano_estudo_avaliacaos_id' => $spea->id,
                        'metricas_id' => $metrica_id,
                        'users_id' => $data['estudantes'][$i],
                    ],
                        [
                        'nota' => $data['notas'][$i],
                        'presence' => $data['presences'][$i],
                        'updated_by' => Auth::user()->id,
                        'created_by' => Auth::user()->id
                        ]
                    );
                    //( Avaliar se for nota negativa (fim de curso) )
                    //avaliar se a disciplina e final de curso
                    if ($discipline_id == 181 ||  //disciplinas de fim de curso de todos os cursos
                        $discipline_id == 551 ||
                        $discipline_id == 237 ||
                        $discipline_id == 637 ||
                        $discipline_id == 299 ||
                        $discipline_id == 527 ||
                        $discipline_id == 129 ||
                        $discipline_id == 395 ||
                        $discipline_id == 468 ||
                        $discipline_id == 619 ||
                        $discipline_id == 430 ||
                        $discipline_id == 229 ||
                        $discipline_id == 72  ||
                        $discipline_id == 565 ||
                        $discipline_id == 315) {
                        // avaliar se pagou pelo emolumento
                        $user = User::findOrFail($data['estudantes'][$i]);
                        $course_id = $user->courses()->first()->id;
                        $emolumentos = [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61];

                        foreach ($emolumentos as  $value) {
                            $state = ArticleRequest::whereArticleId($value)
                                                        ->whereUserId($data['estudantes'][$i])
                                                        ->first();

                            if (!$state == null && $state->status == "total") {
                                UserState::updateOrCreate(
                                    ['user_id' => $data['estudantes'][$i]],
                                    ['state_id' => 6, 'courses_id' => $course_id] //6 => Concluido
                                );
                                UserStateHistoric::create([
                                        'user_id' => $data['estudantes'][$i],
                                        'state_id' => 6
                                    ]);
                            }
                        }
                    }
                }
            });


            // Success message
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return back();
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


    public function show($id)
    {
        try {
            return view("Avaliations::avaliacao-aluno.show-avaliacao-aluno");
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


























     
    public function update(Request $request, $id)
    {
        return $id;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */




















































    public function destroy($id)
    {
        try {
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




























































    public function studyPlanEditionAjax()
    {

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












































    public function disciplineAjax($id)
    {
        return $this->getDisciplinesByRole($id);
    }

   



















































    public function avaliacaoAjax($id)
    {
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
                    /*->whereNotExists(function ($q) {
                        //$q->select('plano_estudo_avaliacaos.id')
                        $q->select('plano_estudo_avaliacaos.avaliacaos_id')
                          ->from('plano_estudo_avaliacaos')
                          //->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                          ->whereRaw('avaliacao_aluno_historicos.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                    })*/
                    ->where('dp.id', $id)
                   ->distinct()
                   ->get();
        return json_encode(array('data'=>$avaliacaos));
    }





















































































    public function metricaAjax($avaliacao_id, $discipline_id, $course_id)
    
    {


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






































    //Este méodo pega todas as 
    //avaliações dos alunos cujo 
    



    public function studentAjax($id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id)
    {
        
        //avaliar se a metrica ja foi concluida, se retornar algo é porque já foi concluida
        $metricArePlublished = DB::table('published_metric_grade')
                                    ->where('study_plan_edition_id', $study_plan_id)
                                    ->where('discipline_id', $id)
                                    ->where('avaliation_id', $avaliacao_id)
                                    ->where('class_id', $class_id)
                                    ->where('metric_id', $metrica_id)
                                    ->where('lecive_year', date('Y'))
                                    ->get();

        $metricArePlublished = $metricArePlublished->isEmpty() ? false : true;


        $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $id)
                                                ->firstOrFail();
        //ao tratar outras avaliacoes essa variavel causava erros...
        //so preciso dela quando a avaliacao selecionada for MAC == 21
        if($avaliacao_id == 21 || $avaliacao_id == 23){

            $current_pea = PlanoEstudoAvaliacao::where('disciplines_id', $id)
                                                ->where('avaliacaos_id', 21)
                                                ->firstOrFail();
        }else{
                $current_pea = 1;
        }
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
                        'mc.class_id as class_id'
                    ])
                  ->where('md.discipline_id', $id)
                  ->where('mc.class_id', $class_id)
                  //->where('avl.id', 10) //USAR ID da tabela do servidor
                    // 0 - EXAME NAO OBRIGATORIO
                    // 1 - EXAME OBRIGATORIO

                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 1, function ($q) use ($id, $current_pea, $class_id) {
                      return
                      $q->where(function ($query) use ($id, $current_pea, $class_id) {
                          $query->where('avl.id', 21)
                                ->where('aah.plano_estudo_avaliacaos_id', $current_pea->id)
                                //->where('aah.class_id', $class_id)
                                ->where('aah.nota_final', '>=', '6.5');
                          //->where('users.id',5813);
                      });
                  })
                  ->when($avaliacao_id == 13, function ($q) {
                      return 1;
                  })
                  ->when($avaliacao_id == 23 && $disciplineHasMandatoryExam->exam == 0, function ($q) use ($current_pea, $class_id) {
                      return
                            $q->where(function ($query) use ($current_pea, $class_id) {
                                $query->where('avl.id', 21)
                                      ->where('aah.plano_estudo_avaliacaos_id', $current_pea->id)
                                      //->where('aah.class_id', $class_id)
                                      ->whereBetween('aah.nota_final', ['6.5', '13']); /*importante mudar de 10,13 para 6.5, 13*/
                            });
                  })
                  //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();
        }elseif($avaliacao_id == 22){ //exame de recurso
                //anchora_1
                //caso for uma avaliacao de recurso
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
                        ->where('md.discipline_id', $id)
                        ->where('mc.class_id', $class_id)
                        ->where('percentage_avaliation.discipline_id', $id)
                        //where date para trazer so o emolumentos pago durante o ano em questao
                        // ->where('article_requests.discipline_id', $id)
                        // ->where('article_requests.status',"total")
                        // ->where('article_requests.article_id', 36) //emolumento (exame de recurso)
                        //   ->where(\DB::raw('percentage_avaliation.percentage_mac + percentage_avaliation.percentage_neen'))
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

        }elseif($avaliacao_id == 25){ //exame especial
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
                  ->where('md.discipline_id', $id)
                  ->where('mc.class_id', $class_id)
                  ->where('article_requests.discipline_id', $id)
                  ->where('article_requests.status',"total")
                  ->where('article_requests.article_id', 32) //emolumento (exame de especial)

                  //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();
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
                        'mc.class_id as class_id'
                    ])
                  ->where('md.discipline_id', $id)
                  ->where('mc.class_id', $class_id)

                  //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();
        }

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
                    ->orderBy('user_name', 'ASC')
                    ->get();

        return json_encode(array('data'=> $grades,'students' => $students,'metrics'=>$metrics,'metricArePlublished' => $metricArePlublished));

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




































































    public function showStudentGradesAjax($avaliacao_id, $discipline_id, $stdplanedition, $class_id)
    {
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
                        //'avaliacao_alunos.*'
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
        }else if($avaliacao_id == 22)
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


                        //   ->where(\DB::raw('percentage_avaliation.percentage_mac + percentage_avaliation.percentage_neen'))
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
            2 -  Avaliar se a disciplina te exame obrigatorio ou nao
            3 - Avaliar se tem notas (MAC):
                - Maior ou igual a 6,5 exame
                - Menor que 6,5 recurso directo
            4 - Levar a mesma logica na hora de concluir a avaliacao
            Como é que pego a nota do MAC de cada estudante?
            R: a avaliacao tem que ser concluida e pegar a nota no historico

        */

    }










































































    public function getStudentFinalGrades()
    {
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

    public function getDocenteDisciplina()
    {
        $id_usuario=Auth::user()->id;
        
            // $disciplineDocente=DB
        return response()->json(array('data'=>$id_usuario));
    }









































































    public function getPEAWithGrades()
    {

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

    public function getFinalGrades($stdp_edition, $class_id, $discipline_id)
    {
        try {

            //IMPORTANTE: TRAZER SO AS AVALIACOES MAC E EXAME

            //TEMOS QUE SELECIONAR O ANO LECTIVO
            //ADICIONAR UM WHERE PARA SELECIONAR O ANO LECTIVO

            /*
            NA VIEW: AVALIAR NA PAUTA NO MAC SE TIVER NOTA < 6 NAO CALCULAR  COM O EXAME
            */

            $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();


            $avaliacaos = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    // ->where('stpe.id', $stdp_edition)
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();


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
                     ->leftJoin('user_parameters as up_meca', function ($join) {
                         $join->on('users.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_meca.value as student_number'
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  ->where('mc.class_id', $class_id)
                  ->orderBy('user_name', 'ASC')
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
                    //OU WHERE COM A TURMA
                ->where('pea.disciplines_id', $discipline_id)
                ->where('pea.study_plan_editions_id', $stdp_edition)
                ->get();

            $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    // ->where('pea.study_plan_editions_id')
                    ->get();

            $metrics = Metrica::join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->join('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    ->orderBy('metricas.id')
                    ->distinct()
                    ->get();


            $example = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome', 'metricas.id as metrica_id', 'metricas.nome as metrica_nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    // ->where('stpe.id', $stdp_edition)
                    ->whereExists(function ($q) {
                        $q->select('plano_estudo_avaliacaos.id')
                        //$q->select('plano_estudo_avaliacaos.avaliacaos_id')
                          ->from('plano_estudo_avaliacaos')
                          ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                        //->whereRaw('avaliacao_aluno_historicos.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                    })
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    // ->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();

            //AQUI É IMPORTANTE QUE SE UTILIZA UM WHERE PARA TRAZER SO AS NOTAS DAS AVALIACOES NECESSARIAS

            /*$gradeWithPercentage = DB::table('percentage_avaliation')
                                        ->where('avaliation_id', 10) //MAC
                                        ->orWhere('avaliation_id', 12) // EXAME
                                        ->where('discipline_id', $discipline_id)
                                        ->selectRaw('*, SUM(nota) as nota')
                                        ->groupBy('user_id')
                                        ->get();*/

            /*$gradeWithPercentage = User::leftjoin('percentage_avaliation as pa1', 'pa1.user_id', '=', 'users.id')
                                          ->leftJoin('percentage_avaliation as pa2', 'pa2.user_id', '=', 'users.id')
                                          ->where('pa1.avaliation_id', 10)
                                          ->where('pa2.avaliation_id', 12)
                                          ->where('pa1.discipline_id', $discipline_id)
                                          ->where('pa2.discipline_id', $discipline_id)
                                          ->selectRaw('*, SUM(pa1.nota + pa2.nota) as nota')
                                          ->groupBy('users.id')
                                          ->get();*/

            $gradesWithPercentage = DB::table('percentage_avaliation')
                                    ->where('discipline_id', $discipline_id)
                                    ->where('class_id', $class_id)
                                    ->select('user_id', \DB::raw('percentage_mac + percentage_neen as grade'))
                                    ->get();


            return json_encode(array('avaliacaos'=> $avaliacaos,
                                     'students' => $students,
                                     'finalGrades' => $finalGrades,
                                     'grades' => $grades,
                                     'metrics' => $metrics,
                                     'example' => $example,
                                     //'gradeWithPercentage1' => $gradeWithPercentage1,
                                     //'gradeWithPercentage2' => $gradeWithPercentage2,
                                     'gradesWithPercentage' => $gradesWithPercentage,
                                     'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam
                                ));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
public function generatePDF($stdp_edition, $class_id, $discipline_id)
    {
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
                 }])->where('id', $discipline_id)->firstOrFail();

            $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();


            $class = Classes::whereId($class_id)->firstOrFail();

            $avaliacaos = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();

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
                     ->leftJoin('user_parameters as up_meca', function ($join) {
                         $join->on('users.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_meca.value as student_number'
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  ->where('mc.class_id', $class_id)
                  ->orderBy('u_p.value', 'asc')
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
                    //OU WHERE COM A TURMA
                ->where('pea.disciplines_id', $discipline_id)
                ->where('pea.study_plan_editions_id', $stdp_edition)
                ->distinct('users_id')
                ->get();


            $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    ->get();

            $metrics = Metrica::join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->join('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    ->orderBy('metricas.id')
                    ->distinct()
                    ->get();


            $example = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome', 'metricas.id as metrica_id', 'metricas.nome as metrica_nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();

            //AQUI É IMPORTANTE QUE SE UTILIZA UM WHERE PARA TRAZER SO AS NOTAS DAS AVALIACOES NECESSARIAS
            $gradesWithPercentage = DB::table('percentage_avaliation')
                                    ->where('discipline_id', $discipline_id)
                                    ->where('class_id', $class_id)
                                    ->select('user_id', \DB::raw('percentage_mac + percentage_neen as grade'))
                                    ->get();

            $data = [
                'avaliacaos'=> $avaliacaos,
                'students' => $students,
                'finalGrades' => $finalGrades,
                'grades' => $grades,
                'metrics' => $metrics,
                'example' => $example,
                'gradesWithPercentage' => $gradesWithPercentage,
                'discipline' => $discipline,
                'class' => $class,
                'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam
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

            $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream('Pauta Final'. '.pdf');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }














    public function getStudentSummaryGrades()
    {
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


    public function getSummaryGrades($stdp_edition, $discipline_id)
    {
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

    public function AddOAGrades()
    {
        try {


               //Pegar o ano lectivo na select
               $lectiveYears = LectiveYear::with(['currentTranslation'])
               ->get();
               $currentData = Carbon::now();
               $lectiveYearSelected = DB::table('lective_years')
                   ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                   ->first();
               $lectiveYearSelected = $lectiveYearSelected->id ?? 6;


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


         return view("Avaliations::avaliacao-aluno.add-oa-grade", compact('pea','lectiveYearSelected','lectiveYears'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function StoreOAGrades(Request $request)
    {
        //IMPORTANTE APOS A AVALIACAO SE CONCLUIDA LIMPAR A TABELA TMP_OA
        //NAO LIMPAR TODA TABELA LIMPAR SO OS DADOS EM QUESTAO (Estudantes da metrica_ SELECIONADA)
        //return $request->all();

        DB::transaction(function () use ($request) {
            $metrica_id = $request->get('metrica_id');
            $sped = $request->get('course_id');
            $discipline_id = $request->get('discipline_id');
            $avaliacao_id = $request->get('avaliacao_id');


            $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                        ->select('plano_estudo_avaliacaos.id')
                        ->where('plano_estudo_avaliacaos.study_plan_editions_id', $sped)
                        ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                        ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                        ->first();


            $course = PlanoEstudoAvaliacao::join('study_plan_editions', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'study_plan_editions.id')
                        ->join('study_plans', 'study_plan_editions.study_plans_id', '=', 'study_plans.id')
                        ->select('study_plans.courses_id')
                        ->where('study_plan_editions_id', $sped)
                        ->first();

                        $oas = [7,8,9,10,11,12];
                        if(in_array($request->get('oa_number'), $oas))
                        {
                          $spea = Discipline::Join('study_plan_edition_disciplines', 'study_plan_edition_disciplines.discipline_id', '=', 'disciplines.id')
                                        ->join('study_plan_editions', 'study_plan_editions.id', '=', 'study_plan_edition_disciplines.study_plan_edition_id')
                                        ->join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'study_plan_editions.id')
                                        ->select([
                                                    'disciplines.id as discipline_id',
                                                    'study_plan_editions.period_type_id as period_type_id',
                                                    'plano_estudo_avaliacaos.id as id'
                                                ])
                                        ->where('study_plan_editions.period_type_id', 2)
                                        ->where('plano_estudo_avaliacaos.disciplines_id', $request->get('discipline_id'))
                                        ->where('disciplines.id', $request->get('discipline_id'))
                                        ->first();
                        }

            $data = [
                    'notas' => $request->get('notas'),
                    'estudantes' => $request->get('estudantes'),
                  ];



            for ($i=0; $i < count($data['notas']); $i++) {
                DB::table('tmp_oa')->updateOrInsert(
                    [
                                'user_id'       => $data['estudantes'][$i],
                                'oa_number'     => $request->get('oa_number'),
                                'avaliacaos_id' => $avaliacao_id,
                                'discipline_id' => $discipline_id,
                                'courses_id'    => $course->courses_id,
                                'class_id'      => $request->get('class_id')
                            ],
                    [
                                'grade'        => $data['notas'][$i] ?: 0,
                                'metricas_id'  => $metrica_id
                            ]
                );
                /*
                    Depois de salvar fazer uma selecao de todas as notas do estudante[$i]
                    somar todas as notas e dividir pelo count() do resultado.
                    salvar o resultado na tabela de notas e dizer que e a metrica da OA
                */
                $somaOA =  DB::table('tmp_oa')
                                ->where('user_id', $data['estudantes'][$i])
                                ->where('class_id', $request->get('class_id'))
                                ->where('discipline_id', $discipline_id)
                                //aqui falta adicionar um where para quando a edicao de plano de estudo mudar (proximo ano)
                                ->sum('grade');

                $totalOA =  DB::table('tmp_oa')
                                ->where('user_id', $data['estudantes'][$i])
                                ->where('class_id', $request->get('class_id'))
                                ->where('discipline_id', $discipline_id)
                                //aqui falta adicionar um where para quando a edicao de plano de estudo mudar (proximo ano)

                                ->count();
                $mediaOA = $somaOA / $totalOA;

                $avaliacaoAluno =  AvaliacaoAluno::updateOrCreate(
                    [
                            'plano_estudo_avaliacaos_id' => $spea->id,
                            'metricas_id' => $metrica_id,
                            'users_id' => $data['estudantes'][$i]
                        ],
                    [
                            'nota' => $mediaOA,
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id
                        ]
                );
             }

              // setando lançamento de pauta
             
         }); 
        // 
        //Success message
        Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
        return back();
    }

    public function setPautaOA(){

        try{
            DB::beginTransaction();

            $grades = DB::table('avaliacao_alunos as av')
                        ->where('av.metricas_id',98)
                        ->join('plano_estudo_avaliacaos as plan','plan.id','av.plano_estudo_avaliacaos_id')
                        ->select([
                            'plan.disciplines_id',
                            'av.*'
                        ])
                        ->get();

                        $grades = collect($grades)->unique(function ($item) {
                            return $item->disciplines_id . '-' . $item->id_turma;
                        });

                        $i = 0;
                       foreach($grades as $item){
                        $line = [
                         
                            "id_turma" => $item->id_turma,
                            "id_disciplina" => $item->disciplines_id,
                            "id_ano_lectivo" => 9,
                            "estado" => 1,
                            "created_by" => $item->created_by,
                            "updated_by" => $item->updated_by,
                            "created_at" => $item->created_at,
                            "updated_at" => $item->updated_at,
                            "id_user_launched" => $item->created_by,
                            "pauta_tipo" => "OA",
                            "tipo" => 40,
                            "segunda_chamada" => null,
                            "version" => 1,
                            "description" => null,
                            "path" => null,
                            "active" => 0,
                        ];

                        $pauta_id = DB::table('lancar_pauta')
                       ->insertGetId($line);

                        $obj = new AvaliacaoAlunoControllerNew();
                       $obj->lockPautaOA($pauta_id);

                       $i++;

                       }

                       

            DB::commit();

            dd('Sucesso!!!',$i);
        }
        catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    }
    public function studentOAAjax($id, $study_plan_id, $avaliacao_id, $class_id, $oa)
    {
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







    public function avaliacaoAjaxOA($id)
    {
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



















    public function metricaAjaxOA($id)


    {
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

    public function generatePartialPDF($avaliacao_id, $discipline_id, $stdplanedition, $class_id)
    {
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

        }elseif($avaliacao_id == 22){
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


    public function studentGrade()
    {
        // return "ewdeqwd";
        /*
        #ancora
        1- tratar de erros para quando nao tiver notas uma disciplina selecionada
        2 - exibir notas das avaliacoes que so lançaram*/
        try {
            
            $student_id = Auth::user()->id;
            $courses = Course::with(['currentTranslation'])
            ->where('courses.deleted_at',null)
            ->get();

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
                    // ->join('published_metric_grade', 'published_metric_grade.metric_id','=','avaliacao_alunos.metricas_id')

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
                                    ->select('user_id',DB::raw('percentage_mac + percentage_neen as grade'), 'discipline_id')
                                    ->get();
                

               //Pegar o ano lectivo na select
               $lectiveYears = LectiveYear::with(['currentTranslation'])
               ->get();
               $currentData = Carbon::now();
               $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
               ->first();
               $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
               //-----------------------------------------------------------------------



            $data = [
                        'courses' => $courses,
                        'lectiveYearSelected'=>$lectiveYearSelected,
                        'lectiveYears'=>$lectiveYears,
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



    public function getStudentsByCourse($course_id,$id_anolectivo)
    {
        /*está a trazer ate professores */
        /* $students = User::join('user_courses','user_courses.users_id','=','users.id')
                 ->where('user_courses.courses_id', $course_id)
                 ->orderBy('name')
                 ->get();*/
                 $lectiveYearSelected = DB::table('lective_years')
                 ->where('id', $id_anolectivo)
                 ->first();

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
                ->where('matriculations.deleted_at',null)
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
                ]) ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                   ->orderBy('name')
                   ->get();


        return response()->json($students);
  
     
 
        
    }










    private function pegaressa(){



        
    }























    public function getGradeByStudent($student_id,$id_anolectivo,$course_id)
    {
        /*
           1- tratar de erros para quando nao tiver notas uma disciplina selecionada
           2 - exibir notas das avaliacoes que so lançaram*/
        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anolectivo)
                ->first();

                $model =DB::table('matriculations')
                ->join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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
               
                ->select('matriculations.course_year','ct.courses_id')
                ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                 ->where('matriculations.user_id',$student_id)
                ->get();
                $contaCnsulta=count($model);
                $listaStady_plan=[];
                $listaStady_periodo_anual=[];
                if ($contaCnsulta>0) {  
                    foreach ($model as $item_model) {}
                    $ano_courso=$item_model->course_year;
                    $executa_sql="SELECT DISTINCT(study_plan_editions.id) FROM study_plans JOIN study_plan_editions  ON study_plan_editions.study_plans_id=study_plans.id WHERE study_plans.courses_id='$course_id' AND study_plan_editions.lective_years_id=' $id_anolectivo' AND study_plan_editions.course_year='$ano_courso' ";
                    $trazer_disciplina=DB::select($executa_sql);
                      $activa=true;
                    
                    //   SELECT DISTINCT(users.id),users.name,disciplines_translations.display_name FROM study_plan_editions JOIN study_plan_edition_disciplines JOIN disciplines_translations JOIN discipline_periods JOIN study_plans_has_disciplines JOIN disciplines JOIN disciplines_articles JOIN users ON study_plan_edition_disciplines.study_plan_edition_id=study_plan_editions.id AND study_plan_edition_disciplines.discipline_id=disciplines.id AND disciplines_translations.discipline_id=disciplines.id AND disciplines_articles.discipline_id=disciplines.id AND disciplines.id AND disciplines_articles.user_id=users.id WHERE disciplines_articles.user_id='$student_id' AND disciplines_translations.display_name!='' AND disciplines_translations.active=1 AND study_plan_editions.lective_years_id='$id_anolectivo' AND study_plan_editions.course_year='$ano_courso' 
                    
                    $ex_sql_cadeira="SELECT DISTINCT disciplines_translations.display_name,disciplines.code as discipli_code,study_plans_has_disciplines.years,discipline_periods.code FROM matriculations JOIN matriculation_disciplines JOIN disciplines JOIN disciplines_translations JOIN study_plans_has_disciplines JOIN discipline_periods ON matriculation_disciplines.matriculation_id=matriculations.id AND matriculation_disciplines.discipline_id=disciplines.id AND disciplines_translations.discipline_id=disciplines.id AND study_plans_has_disciplines.disciplines_id=disciplines.id AND study_plans_has_disciplines.discipline_periods_id=discipline_periods.id WHERE matriculations.user_id='$student_id' and matriculation_disciplines.exam_only=1 AND disciplines_translations.active=1 AND matriculations.course_year='$ano_courso' ";
                    $ext_cadeira=DB::select($ex_sql_cadeira);
                       
                    // 

                    $ex_sql="SELECT DISTINCT(study_plans.courses_id),disciplines_translations.display_name,discipline_periods.code as disc_code,disciplines.code FROM disciplines_translations JOIN disciplines JOIN discipline_periods JOIN study_plans_has_disciplines JOIN study_plans JOIN study_plan_edition_disciplines JOIN study_plan_editions ON disciplines_translations.discipline_id=disciplines.id AND study_plans_has_disciplines.disciplines_id=disciplines.id AND study_plans_has_disciplines.discipline_periods_id=discipline_periods.id AND study_plans_has_disciplines.study_plans_id=study_plans.id AND study_plan_editions.study_plans_id=study_plans.id AND study_plan_edition_disciplines.study_plan_edition_id=study_plan_editions.id WHERE  study_plans.courses_id='$course_id' AND study_plan_editions.lective_years_id='$id_anolectivo' AND study_plans_has_disciplines.years='$ano_courso' AND disciplines_translations.active='$activa' AND disciplines_translations.display_name!='' ORDER BY discipline_periods.code ASC";
                    $ext_disciplina=DB::select($ex_sql);  

                    $avaliacaos = DB::table('avaliacaos as avl')
                        // join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                        // ->join('avaliacaos as avl', 'avl.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                        // ->join('metricas', 'metricas.avaliacaos_id' , '=', 'avl.id')
                        ->select('avl.id as avaliacaos_id', 'avl.nome as nome')
                        // ->where('plano_estudo_avaliacaos.disciplines_id', $ext_disciplina->pluck('discipline_id'))
                        ->where('avl.anoLectivo', $id_anolectivo)
                        ->distinct()
                        ->get();

                    $metricas =DB::table('avaliacaos as avl')
                        ->join('metricas','metricas.avaliacaos_id','=','avl.id')
                        // ->join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                        // ->leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                        ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                        // ->whereIn('pea.disciplines_id', $ext_disciplina->pluck('discipline_id'))
                         ->where('avl.anoLectivo', $id_anolectivo)
                        ->where('metricas.deleted_at', null)
                        ->orderBy('metricas.id')->distinct() ->get();


                        $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                        ->where('avaliacao_alunos.users_id', $student_id)
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
                                        ->select('user_id', \DB::raw('percentage_mac + percentage_neen as grade'), 'discipline_id')
                                        ->get();
                        $data = [
                            'ext_cadeira'=>$ext_cadeira,
                            'avaliacaos' => $avaliacaos,
                            'metricas' => $metricas,
                            'ext_disciplina' => $ext_disciplina,
                            'trazer_disciplina'=>$trazer_disciplina,
                            'ano_courso' => $ano_courso,
                            'id_anolectivo'=>$id_anolectivo,
                            'course_id'=>$course_id
                            
                        ];
    
                    $view = view("Avaliations::avaliacao-aluno.student.content")->with($data)->render();
                    return response()->json(['html'=>$view]);

                }else{
                    return response()->json(1);
                }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    




















































    public function getDisciplinesByStudent($student_id)
    {
        try {
            if (Auth::user()->hasAnyRole(['superadmin','staff_forlearn','teacher'])) {
                $disciplines = User::join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
                ->join('disciplines as dc', 'dc.id', '=', 'matriculation_disciplines.discipline_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'dc.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('users.id', $student_id)
                ->select('dc.id as discipline_id', 'dt.display_name as display_name')
                ->get();
                return response()->json($disciplines);
            } elseif (Auth::user()->hasAnyRole(['student'])) {
                $student_id = Auth::user()->id;
                $disciplines = User::join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
                ->join('disciplines as dc', 'dc.id', '=', 'matriculation_disciplines.discipline_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'dc.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('users.id', $student_id)
                ->select('dc.id as discipline_id', 'dt.display_name as display_name')
                ->get();
                return response()->json($disciplines);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function printGradeStudent($student_id)
    {
        try {
            $student = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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
                ->join('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'matriculations.id')
                ->join('classes', 'classes.id', '=', 'matriculation_classes.class_id')
                ->where('u0.id', $student_id)
                ->select([
                    'matriculations.*',
                    //'u0.name as student',
                    'u_p.value as student',
                    'u0.email as email',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name as course',
                    'up_meca.value as meca_number',
                    'classes.display_name as class'
                ])->firstOrFail();

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
                    ->where('avaliacao_alunos.users_id', $student_id)
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
                                    ->select('user_id', \DB::raw('percentage_mac + percentage_neen as grade'), 'discipline_id')
                                    ->get();

            $data = [
                        'courses' => $courses,
                        'classes' => $classes[0]['display_name'],
                        'disciplines' => $disciplines,
                        'avaliacaos' => $avaliacaos,
                        'metricas' => $metricas,
                        'grades' => $grades,
                        'finalGrades' => $finalGrades,
                        'gradesWithPercentage' => $gradesWithPercentage,
                        'student' => $student
                    ];

            //return view("Avaliations::avaliacao-aluno.student.pdf.print")->with($data);
            $pdf = PDF::loadView("Avaliations::avaliacao-aluno.student.pdf.print", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '12mm');
            $pdf->setOption('margin-right', '2mm');
            /*$pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);*/
            $pdf->setPaper('a4');

            $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream('Notas das avaliacoes'. '.pdf');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function showGrade($class_id, $discipline_id)
    {
        try {
            $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
                                                ->select('discipline_has_exam.has_mandatory_exam as exam')
                                                ->where('disciplines.id', $discipline_id)
                                                ->firstOrFail();


            $avaliacaos = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();


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
                     ->leftJoin('user_parameters as up_meca', function ($join) {
                         $join->on('users.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                     })
                     //Estudantes por turma
                     ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                    ->select([
                        //'mt.user_id',
                        'md.discipline_id',
                        'users.id as user_id',
                        'u_p.value as user_name',
                        'ct.display_name as course',
                        'dt.display_name as discipline',
                        'up_meca.value as student_number'
                        //'avaliacao_alunos.*'
                    ])
                  ->where('md.discipline_id', $discipline_id)
                  ->where('mc.class_id', $class_id)
                  ->orderBy('user_name', 'ASC')
                  ->where('user_id', 4837)
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
                    //OU WHERE COM A TURMA
                ->where('pea.disciplines_id', $discipline_id)
                ->where('users.id', 4837)
                ->get();

            $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    ->where('avaliacao_alunos.users_id', 4837)
                    ->get();

            $metrics = Metrica::join('avaliacao_alunos', 'avaliacao_alunos.metricas_id', '=', 'metricas.id')
                    ->join('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->select('metricas.id as metrica_id', 'metricas.nome as nome', 'metricas.avaliacaos_id as avaliacao_id')
                    //Adicionar um where com a turma para retomar so aquela turma
                    //SE POSSIVEL UM WHERE COM O PLANO DE ESTUDO AVALIACAO
                    ->where('pea.disciplines_id', $discipline_id)
                    ->orderBy('metricas.id')
                    ->distinct()
                    ->get();


            $example = PlanoEstudoAvaliacao::leftJoin('avaliacaos as avl', 'plano_estudo_avaliacaos.avaliacaos_id', '=', 'avl.id')
                    ->leftJoin('study_plan_editions as stpe', 'plano_estudo_avaliacaos.study_plan_editions_id', '=', 'stpe.id')
                    ->leftJoin('metricas', 'avl.id', '=', 'metricas.avaliacaos_id')
                    ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
                    ->select('avl.id as avaliacaos_id', 'avl.nome as nome', 'metricas.id as metrica_id', 'metricas.nome as metrica_nome')
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    /*->whereExists(function ($q) {
                        $q->select('plano_estudo_avaliacaos.id')
                        //$q->select('plano_estudo_avaliacaos.avaliacaos_id')
                          ->from('plano_estudo_avaliacaos')
                          ->whereRaw('avaliacao_aluno_historicos.plano_estudo_avaliacaos_id = plano_estudo_avaliacaos.id');
                        //->whereRaw('avaliacao_aluno_historicos.avaliacaos_id = plano_estudo_avaliacaos.avaliacaos_id');
                    })*/
                    //TODO: FAZER ESSE WHERE FUNCIONAR
                    //->where('plano_estudo_avaliacaos.study_plan_editions_id', $stdp_edition)
                    ->distinct()
                    ->get();

            //AQUI É IMPORTANTE QUE SE UTILIZA UM WHERE PARA TRAZER SO AS NOTAS DAS AVALIACOES NECESSARIAS


            $gradesWithPercentage = DB::table('percentage_avaliation')
                                    ->where('discipline_id', $discipline_id)
                                    ->where('class_id', $class_id)
                                    ->select('user_id', \DB::raw('percentage_mac + percentage_neen as grade'))
                                    ->where('user_id', 4837)
                                    ->get();


            return json_encode(array('avaliacaos'=> $avaliacaos,
                                     'students' => $students,
                                     'finalGrades' => $finalGrades,
                                     'grades' => $grades,
                                     'metrics' => $metrics,
                                     'example' => $example,
                                     //'gradeWithPercentage1' => $gradeWithPercentage1,
                                     //'gradeWithPercentage2' => $gradeWithPercentage2,
                                     'gradesWithPercentage' => $gradesWithPercentage,
                                     'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam
                                ));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
















































     

    public function getDisciplinesByRole($id)
    {
        $teacher_id = Auth::user()->id;
        $user = User::whereId($teacher_id)->firstOrFail();


       $period = PlanoEstudoAvaliacao::Join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                                    ->select(['study_plan_editions.period_type_id'])
                                    ->where('study_plan_editions.id', $id)
                                    ->first();
        $courseYear = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                      ->where('stpeid.id', $id)
                      ->first();



        if ($user->hasAnyRole(['staff_gabinete_termos','staff_forlearn','superadmin'])) {

            
            $classes = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                    ->leftJoin('teacher_classes', 'teacher_classes.class_id', '=', 'classes.id')
                    //->where('teacher_classes.user_id', $teacher_id)
                    ->where('stpeid.id', $id)
                    ->where('classes.year', $courseYear->course_year)
                    ->select('classes.id as id', 'classes.display_name as display_name')
                    //Essa query carrega todas as turmas do professor em cada ano lectivo (2020)
                    //tenho que voltar a colocar porem com base no modelo de ano civil (2020/2021)
                    //->where('teacher_classes.lective_year', date('Y'))
                    ->distinct()
                    ->get();

            $disciplines = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('stpeid.id', $id)
                    //->where('user_disciplines.users_id', $teacher_id)
                    ->distinct()
                    ->get();
                    
            return json_encode(array('data'=>$disciplines, 'classes' => $classes));

        }


         elseif ($user->hasAnyRole(['coordenador-curso'])) {
          
            $course_id = DB::table('coordinator_course')
                                ->where('user_id', $teacher_id)
                                ->first();

         $myClasses = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                    ->leftJoin('teacher_classes', 'teacher_classes.class_id', '=', 'classes.id')
                    ->where('teacher_classes.user_id', $teacher_id)
                    //->where('classes.courses_id', $course_id->courses_id)
                    ->where('stpeid.id', $id)
                    ->where('classes.year', $courseYear->course_year)
                    ->select('classes.id as id', 'classes.display_name as display_name')
                    //Essa query carrega todas as turmas do professor em cada ano lectivo (2020)
                    //tenho que voltar a colocar porem com base no modelo de ano civil (2020/2021)
                    //->where('teacher_classes.lective_year', date('Y'))
                    ->distinct()
                    ->get();

            $allClasses = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                    ->leftJoin('teacher_classes', 'teacher_classes.class_id', '=', 'classes.id')
                    //->where('teacher_classes.user_id', $teacher_id)
                    ->where('classes.courses_id', $course_id->courses_id)
                    ->where('stpeid.id', $id)
                    ->where('classes.year', $courseYear->course_year)
                    ->select('classes.id as id', 'classes.display_name as display_name')
                    //Essa query carrega todas as turmas do professor em cada ano lectivo (2020)
                    //tenho que voltar a colocar porem com base no modelo de ano civil (2020/2021)
                    //->where('teacher_classes.lective_year', date('Y'))
                    ->distinct()
                    ->get();
            /*
                *** *** ***  ***  ***  ***  ***  ***  ***  ***  **
                Como não da para pegar as turmas que ele da e todas as turmas
                do curso que ele coordena eu fiz o seguinte ...
                a collection $myClasses vai trazer todas as turmas que ele leciona
                a collection $allClasses vai trazer todas as turmas do curso que coordena.
                *** *** *** *** **xxxxxx Claudio sa
            */


            $classes = collect($myClasses)->merge($allClasses);


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
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('stpeid.id', $id)
                    ->where('user_disciplines.users_id', $teacher_id)
                    //->where('dp.courses_id',$course_id->courses_id)
                    ->distinct()
                    ->get();



            $getAllDisciplines = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('stpeid.id', $id)
                    //->where('user_disciplines.users_id', $teacher_id)
                    ->where('dp.courses_id', $course_id->courses_id)
                    ->distinct()
                    ->get();

                    
            /*
                Como não da para pegar as diciplinas que ele da e todas as disciplinas
                do curso que ele coordena eu fiz o seguinte...
                a collection $getMyDisciplines vai trazer todas as disciplinas que ele leciona
                a collection $getAllDisciplines vai trazer todas as disciplinas do curso que coordena.
            */
            $disciplines = collect($getMyDisciplines)->merge($getAllDisciplines);


            return json_encode(array('data'=>$disciplines, 'classes' => $classes, 'period' => $period));
        }



        //Obter todas as turmas que o professor leciona num determinado curso
        //
        $classes = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                    ->leftJoin('teacher_classes', 'teacher_classes.class_id', '=', 'classes.id')
                    ->where('teacher_classes.user_id', $teacher_id)
                    ->where('stpeid.id', $id)
                    ->where('classes.year', $courseYear->course_year)
                    ->select('classes.id as id', 'classes.display_name as display_name')
                    //Essa query carrega todas as turmas do professor em cada ano lectivo (2020)
                    //tenho que voltar a colocar porem com base no modelo de ano civil (2020/2021)
                    //->where('teacher_classes.lective_year', date('Y'))
                    ->distinct()
                    ->get();

        $disciplines = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('stpeid.id', $id)
                    ->where('user_disciplines.users_id', $teacher_id)
                    ->distinct()
                    ->get();

        return json_encode(array('data'=>$disciplines, 'classes' => $classes, 'period' => $period));
    }























































    public function showPublishMetricForm()
     
    
    {
        return view("Avaliations::avaliacao-aluno.publish-metric");
     }




  











































    public function publishMetricGrade(Request $request)
    {
        $validated = $request->validate([
            'study_plan_edition_id' => 'required',
             'discipline_id' => 'required',
             'avaliation_id' => 'required',
             'class_id' => 'required',
             'metric_id' => 'required'
        ]);
        DB::table('published_metric_grade')
            ->updateOrInsert(
                ['study_plan_edition_id' => $request->study_plan_edition_id,
             'discipline_id' => $request->discipline_id,
             'avaliation_id' => $request->avaliation_id,
             'class_id' => $request->class_id,
             'metric_id' => $request->metric_id,
             'lecive_year' => date('Y')],
                ['study_plan_edition_id' => $request->study_plan_edition_id,
             'discipline_id' => $request->discipline_id,
             'avaliation_id' => $request->avaliation_id,
             'class_id' => $request->class_id,
             'metric_id' => $request->metric_id,
             'lecive_year' => date('Y'),
             'published' => true]
            );

        Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
        return back();
    }






    /*
    * Francisco Campos
    * Metodo para exibir formulario para inserir notas UC.
    *
    **/














    public function addUCGrades()
    {
        return view("Avaliations::avaliacao-aluno.create-uc-grade");
    }

























    public function disciplineAjaxUC($courseId)
    {
        $teacher_id = Auth::user()->id;
        $assocDisciplines = [177,208,112,460,414,217,326,
                             172,180,298,111,128,458,467,
                             412,433,213,224,325,350,
                             148,515,607,66,535,550,145,
                             236,624,638,514,525,397,
                             608,613,65,71,538,566, 534,
                             541];

        $disciplines = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
                        'dt.display_name as dt_display_name'
                    ])
                    ->where('stpeid.id', $courseId)
                    ->where('user_disciplines.users_id', $teacher_id)
                    ->whereIn('user_disciplines.disciplines_id', $assocDisciplines)
                    ->distinct()
                    ->get();
        
                    
        $classes = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                    ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                    ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                    ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
                    ->leftJoin('teacher_classes', 'teacher_classes.class_id', '=', 'classes.id')
                    ->select('classes.id as id', 'classes.display_name as display_name')
                    ->where('teacher_classes.user_id', $teacher_id)
                    ->where('stpeid.id', $courseId)
                    ->distinct()
                    ->get();



        return json_encode(array('disciplines'=>$disciplines, 'classes' => $classes));
    }



































    public function avaliacaoAjaxUC($id)
    {
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
                    ->where('avl.id', 24) //trazer apenas a avaliacao classificacao final
                   ->distinct()
                   ->get();
        return json_encode(array('data'=>$avaliacaos));
    }









































    public function studentAjaxUC($discipline_id, $class_id)


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
                  ->orderBy('user_name', 'ASC')
                  ->distinct()
                  ->get();

        return json_encode(array('students' => $students));
    }

    /*
    * Francisco Campos
    * Metodo para salvar formulario para inserir notas UC.
    *
    **/

    public function storeUCGrades(Request $request)
    {
        return $request;
    }

    //Zacarias Método Que Tranca as Notas
    public function storeFinalGrade(Request $request)
    {        
        $array_aval = [];
        $a = "";
        for ($i = 0; $i < strlen($request->dados_avalicao_id) ; $i++) {
            // Cria um array com os valores
            if ($request->dados_avalicao_id[$i] != ",") {
                if ($request->dados_avalicao_id[$i] != -1) {
                    $a = $a.$request->dados_avalicao_id[$i];
                }
            }
            // Faz uma busca pela avaliação se encontrar tranca a nota
            else {
                $array_aval[] = $a;
                $AlunoAvalicao = DB::table('avaliacao_alunos')
                        //  ->select('lective_year_translations.display_name')
                         ->where('avaliacao_alunos.id',$a)
                         ->get(); 
                
                $a = "";
            }
        }

        // dd($array_aval, $request->dados_avalicao_id, $AlunoAvalicao); 
    }
}

//Mapa mental, Cláudio 
//Pegar um conjunto de disciplina que apenas o professor leciona
//Pegar apenas o conjunto de turmas que o mesmo leciona no ano
//Pegar as métricas no devido intervalo de tempo .