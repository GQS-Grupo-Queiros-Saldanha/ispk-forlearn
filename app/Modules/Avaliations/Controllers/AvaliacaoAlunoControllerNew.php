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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
//use Barryvdh\DomPDF\PDF;
use App\Modules\GA\Models\LectiveYear;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LDAP\Result;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Users\util\VerificarDisciplina;
use App\Modules\Avaliations\Events\GeneratePdfAvaliationEvent;
use PDF;
use App\Modules\Users\Controllers\MatriculationDisciplineListController;
use Illuminate\Support\Facades\Storage;

class AvaliacaoAlunoControllerNew extends Controller
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
    public function create()
    {


        try {

            //Pegar o ano lectivo na select
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            //-----------------------------------------------------------------------//
            $data = [
                //'courses' => $courses->get(),
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
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



    public function store(Request $request)
    {
      
        $alunos = $request->estudantes ?? [];
        $notas = $request->notas ?? [];

        // Extrair apenas o último número da string "todos,5,206"
        $disciplinasArray = explode(',', $request->disciplina);
        $disciplinaId = end($disciplinasArray);

        if (!is_array($alunos) || !is_array($notas) || count($alunos) !== count($notas)) {
            return response()->json(['error' => 'Dados inválidos.'], 422);
        }

        foreach ($alunos as $index => $alunoId) {
            $nota = $notas[$index];

            DB::table('new_old_grades')
                ->where('user_id', $alunoId)
                ->where('discipline_id', $disciplinaId)
                ->update(['grade' => $nota]);
        }
        
        //Bem no final de lançar as notas alguém tem que fechar elas.
        try {

            $description = $request->description;
            $version = $request->version  + 1;


            $id_d = explode(",", $request->disciplina);
            $id_curso = $id_d[1];


            $lective = $request->selectedLective;

            $pauta_tipo = explode(",", $request->pauta)[0];
            $tipo = intval(explode(",", $request->pauta)[1]);

            $segunda_chamada = isset($request->segunda_chamada) ? (int) $request->segunda_chamada : null;

            //disciplina nome

            if (!$request->estudantes) {
                Toastr::warning(__('O sistema detetou que tentou guardar notas de uma turma sem estudantes listado, por favor selecione uma turma que tenha estudante listados na tabela, para que lhe seja permitido(a) guardar notas.'), __('toastr.warning'));
                return back();
            }

            //turma
            $turma_geral = DB::table('classes')
                ->where('id', $request->turma)
                ->first();


            $disciplina_notification = DB::table('disciplines as disc')
                ->leftJoin('disciplines_translations as trans', function ($join) {
                    $join->on('trans.discipline_id', '=', 'disc.id');
                    $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('trans.active', '=', DB::raw(true));
                })
                ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
                ->where('disc.id', $id_d[2])
                ->first();



            if ($request->whoIs == "super") {

                $prova = Metrica::where('id', $request->metrica)
                    ->select(['avaliacaos_id as id', 'nome as prova', 'code_dev'])
                    ->first();

                DB::beginTransaction();


                //Esse código faz o cadastro da nota quando for O Coordenador
                $turma = $request->turma;
                $turma_id = $request->turma;
                $id_disc = explode(",", $request->disciplina);
                $metrica_id = $request->metrica;
                $plano_estudo = $request->id_plano_estudo;
                $discipline_id = $id_disc[2];
                $avaliacao_id = $request->avaliacao;
                //
                $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->select('plano_estudo_avaliacaos.id')
                    ->where('plano_estudo_avaliacaos.study_plan_editions_id', $plano_estudo)
                    ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id)
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    ->first();

                if (!$spea) {
                    Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que essa avaliação não está definida na edição de plano de estudo desta disciplina.'), __('toastr.success'));
                    return back();
                }


                $data = [
                    'notas' => $request->notas,
                    'estudantes' => $request->estudantes,
                    'presences' => $request->get('inputCheckBox') ? $request->get('inputCheckBox') : false
                ];

                //Quando a vida está a trás do que é meu eu faço
                for ($i = 0; $i < count($data['estudantes']); $i++) {

                    if ($prova->code_dev == 'Recurso') {
                        $notaMaior = $data['notas'][$i] > 14 ? 1 : 0;
                        if ($notaMaior == 1) {
                            Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque detetamos que a nota inserida para a um determinado estudante foi superior a 14 valoreses, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                            return back();
                        }
                    } else {
                        //validar nota superior a 20 valores
                        $notaMaior = $data['notas'][$i] > 20 ? 1 : 0;
                        if ($notaMaior == 1) {
                            Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que a nota inserida para a um determinado estudante foi superior a 20 valoreses, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                            return back();
                        }
                    }

                    //fim validação
                    $avaliacaoAluno = AvaliacaoAluno::updateOrCreate(
                        [
                            'plano_estudo_avaliacaos_id' => $spea->id,
                            'metricas_id' => $metrica_id,
                            'users_id' => $data['estudantes'][$i],
                            'id_turma' => $turma_id,
                            'segunda_chamada' => $segunda_chamada
                        ],
                        [
                            'nota' => $data['notas'][$i],
                            'presence' => null,
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id
                        ]

                    );
                }

                //Actualizar faltas
                if ($request->get('inputCheckBox')) {
                    $Mundo = [];
                    foreach ($request->get('inputCheckBox') as $item) {
                        $estudante_ausente = explode(",", $item);


                        $avaliacaoAluno_falta = AvaliacaoAluno::updateOrCreate(
                            [
                                'plano_estudo_avaliacaos_id' => $spea->id,
                                'metricas_id' => $metrica_id,
                                'users_id' => $estudante_ausente[1],
                                'id_turma' => $turma_id,
                                'segunda_chamada' => $segunda_chamada
                            ],
                            [
                                'nota' => null,
                                'presence' => 1,
                                'updated_by' => Auth::user()->id,
                                'created_by' => Auth::user()->id
                            ]
                        );
                    }
                }



                // setando lançamento de pauta


                DB::table('lancar_pauta')->updateOrInsert(
                    [
                        'id_turma' => (int)$turma_id,
                        'id_ano_lectivo' => (int)$lective,
                        'id_disciplina' => (int)$id_disc[2],
                        'tipo' => (int)$tipo,
                        'pauta_tipo' => (string)$pauta_tipo,
                        'segunda_chamada' => $segunda_chamada,
                        'version' => $version
                    ],
                    [
                        'created_by' => (int) auth()->user()->id,
                        'updated_by' => (int) auth()->user()->id,
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'estado' => 0,
                        'description' => $description,
                        'active' => 0
                    ]
                );


                // });



                //Pegar Professor da turma e disciplina
                $Professores = DB::table('user_disciplines as professor')
                    ->join('user_classes as turma', 'turma.user_id', 'professor.users_id')
                    ->join('users as u', 'u.id', 'professor.users_id')
                    ->where('turma.class_id', $turma_id)
                    ->where('professor.disciplines_id', $id_d[2])
                    ->select(['u.*'])
                    ->get()
                    ->map(function ($item) {
                        return  [
                            "id" => $item->id,
                            "name" => $item->name
                        ];
                    });


                //notificar o professor
                $icon = "fa fa-list";
                $subject = "[" . $disciplina_notification->disciplina . "-" . $prova->prova . "]-Lançamento de notas";
                $disciplina_n = $disciplina_notification->disciplina;
                $prova_n = $prova->prova;
                $turma_n = $turma_geral->display_name;

                $notificar = collect($Professores)->map(function ($item) use ($icon, $subject, $disciplina_n, $prova_n, $turma_n) {
                    $body = "
                <p>
                Caro(a) professor(a),<b>" . $item["name"] . "</b> a prova <b>" . $prova_n . "</b> da disciplina
                <b>" . $disciplina_n . "</b> referente à turma <b>" . $turma_n . "</b> no qual estás associado(a) como docente, foi feita a atribuição de notas aos estudantes pelo seu coordenador de curso.<br>
                Clique no botão abaixo para ir ao painel de visualização.
                 <br>
                 <br>
                 <br>
                 <br>
                 <center>
                     <a  href='/avaliations/show_final_grades' target='_blank' class='btn btn-success'>PAINEL EXIBIR AVALIAÇÃO</a>
                 </center>
              </p>";

                    $destination[] = $item["id"];
                    notification($icon, $subject, $body, $destination, null, null);
                });



                DB::commit();

                // Success message
                Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));


                return back();
            }






            //Código abaixo é quando o  Teacher está a lançar as notas.
            else {
                $prova = Metrica::where('id', $request->metrica_teacher)
                    ->select(['avaliacaos_id as id', 'nome as prova', 'code_dev'])
                    ->first();


                DB::beginTransaction();
                // return $request;
                $avaliacao_id = Metrica::where('id', $request->metrica_teacher)
                    ->select(['avaliacaos_id as id'])
                    ->get();

                // DB::transaction(function () use ($request,$avaliacao_id) {

                $turma_id = $request->turma;
                $id_disc = explode(",", $request->disciplina);
                $metrica_id = $request->metrica_teacher;
                $plano_estudo = $request->id_plano_estudo;
                $discipline_id = $id_disc[2];
                //$sped = $request->get('course_id');
                $spea = PlanoEstudoAvaliacao::join('avaliacaos', 'avaliacaos.id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
                    ->select('plano_estudo_avaliacaos.id')
                    ->where('plano_estudo_avaliacaos.study_plan_editions_id', $plano_estudo)
                    ->where('plano_estudo_avaliacaos.avaliacaos_id', $avaliacao_id[0]->id)
                    ->where('plano_estudo_avaliacaos.disciplines_id', $discipline_id)
                    ->first();

                if (!$spea) {
                    Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que essa avaliação não está definida na edição de plano de estudo desta disciplina.'), __('toastr.success'));
                    return back();
                }

                $data = [
                    'notas' => $request->notas,
                    'estudantes' => $request->estudantes,
                    'presences' => $request->get('inputCheckBox') ? $request->get('inputCheckBox') : false
                ];


                for ($i = 0; $i < count($data['estudantes']); $i++) {

                    //validar nota superior a 14 valores

                    if ($prova->code_dev == 'Recurso') {
                        $notaMaior = $data['notas'][$i] > 14 ? 1 : 0;
                        if ($notaMaior == 1) {
                            Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque detetamos que a nota inserida para a um determinado estudante foi superior a 14 valoreses, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                            return back();
                        }
                    } else {
                        //validar nota superior a 20 valores
                        $notaMaior = $data['notas'][$i] > 20 ? 1 : 0;
                        if ($notaMaior == 1) {
                            Toastr::warning(__('Atenção! não foi possivel atribuir as notas porque o detetamos que a nota inserida para a um determinado estudante foi superior a 20 valores, verifica os campos ao atribuir as notas.'), __('toastr.success'));
                            return back();
                        }
                    }
                    //fim validação

                    $avaliacaoAluno = AvaliacaoAluno::updateOrCreate(
                        [
                            'plano_estudo_avaliacaos_id' => $spea->id,
                            'metricas_id' => $metrica_id,
                            'users_id' => $data['estudantes'][$i],
                            'id_turma' => $turma_id,
                            'segunda_chamada' => $segunda_chamada
                        ],
                        [
                            'nota' => $data['notas'][$i] ? $data['notas'][$i] : null,
                            'presence' => null,
                            'updated_by' => Auth::user()->id,
                            'created_by' => Auth::user()->id
                        ]
                    );
                }

                //Actualizar faltas
                if ($request->get('inputCheckBox')) {
                    $Mundo = [];
                    foreach ($request->get('inputCheckBox') as $item) {
                        $estudante_ausente = explode(",", $item);
                        $Mundo[] = $estudante_ausente[1] . " PEA:" . $spea->id . " Metrica" . $metrica_id . " Turma:" . $turma_id;

                        $avaliacaoAluno_falta = AvaliacaoAluno::updateOrCreate(
                            [
                                'plano_estudo_avaliacaos_id' => $spea->id,
                                'metricas_id' => $metrica_id,
                                'users_id' => $estudante_ausente[1],
                                'id_turma' => $turma_id,
                                'segunda_chamada' => $segunda_chamada
                            ],
                            [
                                'nota' => null,
                                'presence' => 1,
                                'updated_by' => Auth::user()->id,
                                'created_by' => Auth::user()->id
                            ]
                        );
                    }
                }


                DB::table('lancar_pauta')->updateOrInsert(

                    [
                        'id_turma' => (int)$turma_id,
                        'id_ano_lectivo' => (int)$lective,
                        'id_disciplina' => (int)$id_disc[2],
                        'tipo' => (int)$tipo,
                        'pauta_tipo' => (string)$pauta_tipo,
                        'segunda_chamada' => $segunda_chamada,
                        'version' => $version
                    ],
                    [
                        'created_by' => (int) auth()->user()->id,
                        'updated_by' => (int) auth()->user()->id,
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'estado' => 0,
                        'description' => $description,
                        'active' => 0
                    ]
                );



                $coordenadores = DB::table('coordinator_course as c')
                    ->join('users as u', 'u.id', 'c.user_id')
                    ->where('c.courses_id', $id_curso)
                    ->select(['u.*'])
                    ->get()
                    ->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "name" => $item->name
                        ];
                    });


                //notificar o coordenador
                $icon = "fa fa-list";
                $subject = "[" . $disciplina_notification->disciplina . "-" . $avaliacao_id[0]->prova . "]-Lançamento de notas";

                $disciplina_n = $disciplina_notification->disciplina;
                $prova_n = $avaliacao_id[0]->prova;
                $turma_n = $turma_geral->display_name;


                $notificar = collect($coordenadores)->map(function ($item) use ($icon, $subject, $disciplina_n, $prova_n, $turma_n) {
                    $body = "
        <p>
        Caro(a) coordenador(a),<b>" . $item["name"] . "</b> a prova <b>" . $prova_n . "</b> da disciplina
        <b>" . $disciplina_n . "</b> referente à turma <b>" . $turma_n . "</b> no qual és coordenador do curso foi lançada com sucesso!<br>
        Clique no botão abaixo para ir ao painel de visualização.
         <br>
         <br>
         <br>
         <br>
         <center>
             <a  href='/avaliations/show_final_grades' target='_blank' class='btn btn-success'>PAINEL EXIBIR AVALIAÇÃO</a>
         </center>
      </p>";

                    $destination[] = $item["id"];
                    notification($icon, $subject, $body, $destination, null, null);
                });





                DB::commit();

                //Success message
                Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));


                return back();
            }
        } catch (Exception | Throwable $e) {
            DB::rollBack();
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            // return $e;
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
    public function edit($id) {}


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
            ->where('dp.id', $id)
            // ->when($segunda_chamada,function($query){
            //     return $query->whereNotIn('avl.code_dev',['recursos']);
            // })
            ->distinct()
            ->get();

        return json_encode(array('data' => $avaliacaos));
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

        return json_encode(array('data' => $metricas, 'pea' => $pea));
    }














































































    public function metricaAjaxCoordenador($id_avaliacao)
    {
        $metrics = DB::table('metricas')
            ->where('avaliacaos_id', $id_avaliacao)
            ->where('deleted_at', null)
            ->where('deleted_by', null)
            ->where('calendario', 0)
            ->get();

        return json_encode(array('metricas' => $metrics));
    }




    public function studentAjax(Request $request, $id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id, $id_anoLectivo)
    {

        try {
            // echo $metrica_id;
            //avaliar se a metrica ja foi concluida, se retornar algo é porque já foi concluida
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->first();
            $whoIs = $request->query('whoIs', null);
            $segunda_chamada = $request->query('segunda_chamada', null);

            //Consultar os estudantes matriculados
            $id_curso = $this->students_matriculado($id, $lectiveYearSelected->id)->first();
            $consulta_aluno = $this->students_matriculado($id, $lectiveYearSelected->id);

            //$consulta_aluno->where('mc.class_id', $class_id)->get();
            $consulta_aluno = $consulta_aluno->where('mc.class_id', $class_id);

            //Metrica única -- ver se é de exame obrigatório ) obs. o Code_Dev é atribuido na metríca que é considerada a neen
            //Disciplnaha has mandatory exame
            $Discipline_exame_mandatory = DB::table('discipline_has_exam as dhe')
                ->join('disciplines as disc', 'disc.id', '=', 'dhe.discipline_id')
                ->where('disc.id', $id)
                ->where('dhe.id_plain_study', $study_plan_id)
                ->get();


            $metrics_analise_exame = Metrica::where('id', $metrica_id)->where('code_dev', 'Neen')->get();
            $metrics_analise_Recurso_Ou_exame_especial_oral = Metrica::where('id', $metrica_id)->first();
            //Condição Para ver se a disciplina não tem exame obrigatório e a metrica em questão é NEEN
            //Trazer apenas os alunos que na MAC têm nota maior >= 7 e Menor < 14

            //atribuir exame padrao , quando a disciplina não tem o exame obrigatório marcado
            $obrigatorioExame = $Discipline_exame_mandatory->isNotEmpty() ? $Discipline_exame_mandatory[0]->has_mandatory_exam : 0;


            if ($metrics_analise_exame->isNotEmpty()) {
                //Chamar o método que trás todos os alunos com suas notas.
                //O último parametro ano lectivo é apenas para trazer a matricula recente.
                //O id da métrica é para difinir o intervalo de notas que deve trazer ou seja != NEE (PF1,PF2,OA).

                if ($obrigatorioExame == 0) {

                    $config = DB::table('avalicao_config')->first();

                    $Ids_da_User_exame = $this->EstudanteMac($class_id, $id_curso->id_curso, $id_anoLectivo, $id, $lectiveYearSelected, $metrics_analise_exame, $config);

                    //echo "não obigatório: class={$class_id} <br>";

                    $reprovados = $consulta_aluno
                        ->whereIn('users.id', $Ids_da_User_exame)
                        ->distinct()
                        ->get();

                    $dados = $reprovados->unique('user_id');
                } else {
                    //$consulta_alunoG=$this->students_matriculado($id,$lectiveYearSelected);
                    //$consulta_alunoG->where('mc.class_id', $class_id)->where('md.exam_only',1) ->get();
                    $dados = $consulta_aluno->where('mc.class_id', $class_id)->get();
                    //$dados=$Colection;

                }
            } else {


                if ($metrics_analise_Recurso_Ou_exame_especial_oral != null) {

                    if ($metrics_analise_Recurso_Ou_exame_especial_oral->code_dev == "Recurso") {

                        $consulta_alunoR = $this->students_matriculado($id, $lectiveYearSelected->id);
                        $id_matriculation_users = $consulta_alunoR->where('mc.class_id', $class_id)
                            ->get()
                            ->map(function ($item, $key) {
                                return $item->id_mat;
                            });
                        //Pegar todos que marcaram o recurso e Pagaram o Emolumento exame de recurso.

                        $Id_Users_exame_recurso = $this->EstudanteRecurso($id, $id_matriculation_users, $lectiveYearSelected);
                        $dados = $consulta_alunoR->whereIn('mt.id', $Id_Users_exame_recurso)->get();
                    } elseif ($metrics_analise_Recurso_Ou_exame_especial_oral->code_dev == "oral") {

                        $consulta_alunoExameOral = $this->students_matriculado($id, $lectiveYearSelected->id);
                        $id_matriculation_users = $consulta_alunoExameOral->where('mc.class_id', $class_id)
                            ->get()
                            ->map(function ($item, $key) {
                                return $item->id_mat;
                            });
                        //Pegar todos que marcaram o recurso e Pagaram o Emolumento exame de recurso.

                        $Id_Users_exame_recurso = $this->EstudanteExameOral($id, $id_matriculation_users, $lectiveYearSelected);
                        $dados = $consulta_alunoExameOral->whereIn('mt.id', $Id_Users_exame_recurso)->get();


                        // return "oral entrou aqui";
                    } elseif ($metrics_analise_Recurso_Ou_exame_especial_oral->code_dev == "Exame_especial") {
                        $dados = $this->EstudanteExameEspecial($id, $class_id, $lectiveYearSelected);
                        // return "Exame especial";
                    } elseif ($metrics_analise_Recurso_Ou_exame_especial_oral->code_dev == "Extraordinario") {
                        $dados = $this->EstudanteExameExtraordinario($id, $class_id, $lectiveYearSelected);
                    } else {


                        //Dados dos estudantes matriculados na disciplina selecionada no formulario de atribuir nota.

                        $dados = $consulta_aluno->distinct()->where('md.exam_only', 0)->get();
                    }
                }
            }

            $metrics = Metrica::whereAvaliacaosId($avaliacao_id)->get();
            //________________________________________________________________________________________//

            $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
                ->leftJoin('matriculations as mt', 'mt.user_id', '=', 'avaliacao_alunos.users_id')
                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
                ->leftJoin('matriculation_disciplines as mat_disc', 'mat_disc.matriculation_id', '=', 'mt.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('mt.user_id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->when($segunda_chamada, function ($join) {
                    $join->where('avaliacao_alunos.segunda_chamada', 1);
                })
                ->when(!$segunda_chamada, function ($join) {
                    $join->where('avaliacao_alunos.segunda_chamada', null);
                })
                ->select(
                    'avaliacao_alunos.id as aaid',
                    'avaliacao_alunos.nota as aanota',
                    'avaliacao_alunos.users_id as user_id',
                    'mc.class_id as class_id',
                    'u_p.value as user_name',
                    'mat_disc.exam_only as e_f',
                    'avaliacao_alunos.presence as presence',
                    'pea.id as pea_id'
                )
                // Aqui não seria o ID do Plano Estudo Avaliacaos?
                ->where('pea.study_plan_editions_id', $study_plan_id)
                ->where('avaliacao_alunos.metricas_id', $metrica_id)
                ->where('pea.disciplines_id', $id)
                ->where('mc.class_id', $class_id)
                ->where('avaliacao_alunos.id_turma', $class_id)
                ->orderBy('user_name', 'ASC')
                ->distinct()
                ->get();


            if ($metrics_analise_Recurso_Ou_exame_especial_oral->code_dev == "Exame_especial") {

                $grades = false;
                $grades = AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=',  'avaliacao_alunos.plano_estudo_avaliacaos_id')
                    ->leftJoin('user_parameters as u_p', function ($join) {
                        $join->on('avaliacao_alunos.users_id', '=', 'u_p.users_id')
                            ->where('u_p.parameters_id', 1);
                    })
                    ->select(
                        'avaliacao_alunos.id as aaid',
                        'avaliacao_alunos.nota as aanota',
                        'avaliacao_alunos.users_id as user_id',
                        'u_p.value as user_name',
                        'avaliacao_alunos.presence as presence'
                    )
                    // Aqui não seria o ID do Plano Estudo Avaliacaos?
                    ->where('pea.study_plan_editions_id', $study_plan_id)
                    ->where('avaliacao_alunos.metricas_id', $metrica_id)
                    ->where('pea.disciplines_id', $id)
                    ->where('avaliacao_alunos.id_turma', $class_id)
                    ->orderBy('user_name', 'ASC')
                    ->distinct()
                    ->get();
            }

            $metrics_Pauta = Metrica::where('id', $metrica_id)->first();

            $pauta_status = [
                'PF1' => "40",
                'PF2' => "40",
                'OA' => "40",
                'Recurso' => "10",
                'Neen' => "20",
                'oral' => "25",
                'Exame_especial' => "35",
                'Extraordinario' => "45",
                'Trabalho' => "50",
                'Defesa' => "50",
                'TESP' => "60",
            ];

            //Estado da Publicação da pauta
            $estado_publicar = DB::table('publicar_pauta')
                ->where(['id_turma' => $class_id, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id, 'tipo' => $pauta_status[$metrics_Pauta->code_dev]])
                ->first();

            $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
            $estado_l  = Null;


            $estado_lancar = DB::table('lancar_pauta')
                ->where(['id_turma' => $class_id, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id, 'pauta_tipo' => $metrics_Pauta->code_dev])
                ->when($segunda_chamada, function ($query) {
                    return $query->where('segunda_chamada', 1);
                })
                ->when(!$segunda_chamada, function ($query) {
                    return $query->where('segunda_chamada', null);
                })
                ->orderBy('version', 'DESC')
                ->first();

            // dd($estado_lancar,$metrics_Pauta->code_dev);


            $estado_l = isset($estado_lancar) && isset($estado_lancar->path) ? $estado_lancar->estado : Null;
            $pauta_id = isset($estado_lancar) ? $estado_lancar->id : Null;
            $pauta_version = isset($estado_lancar) && isset($estado_lancar->version) ? $estado_lancar->version : 0;
            $pauta_path = isset($estado_lancar) ? $estado_lancar->path : Null;

            $object = new MatriculationDisciplineListController();

            $students_segunda_chamada = null;




            if (!$segunda_chamada) {

                $students_segunda_chamada = DB::table("matriculations as mat")
                    ->join("users as user", 'mat.user_id', 'user.id')
                    ->join("tb_segunda_chamada_prova_parcelar as sc", 'sc.matriculation_id', 'mat.id')
                    ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
                    ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
                    ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
                    ->where('code_dev.code', 'prova_parcelar')
                    ->where('user_emolumento.status', "total")
                    ->whereBetween('article_emolumento.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->where('sc.discipline_id', $id)
                    ->where('sc.id_class', $class_id)
                    ->where('sc.metric_id', $metrica_id)
                    ->where('sc.lectiveYear_id', $id_anoLectivo)
                    ->select('user.id as user_id')
                    ->get()
                    ->pluck('user_id');
            }

            if ($segunda_chamada) {
                /*$estudantesimportados = DB::table('Import_data_forlearn as import')
                    ->join('user_classes as uc', 'uc.user_id', 'import.id_user')
                    ->join('article_requests as ar', 'ar.user_id', 'import.id_user')
                    ->where('uc.class_id', $class_id)
                    ->where('import.ano_curricular', 5)*/

                $dado = collect();
                $dados = $dados->whereIn(
                    'id_mat',
                    DB::table('article_requests as art')
                        ->join('matriculations as mat', 'mat.user_id', 'art.user_id')
                        ->join('tb_segunda_chamada_prova_parcelar as sc', 'mat.id', 'sc.matriculation_id')
                        ->where('sc.discipline_id', $id)
                        ->where('sc.metric_id', $metrica_id)
                        ->where('sc.id_class', $class_id)
                        ->where('sc.lectiveYear_id', $id_anoLectivo)
                        ->where('mat.lective_year', $id_anoLectivo)

                        ->join('articles', 'art.article_id', 'articles.id')
                        ->where('articles.id_code_dev', 35)
                        ->where('art.status', 'total')
                        ->select(['sc.*'])
                        ->get()
                        ->pluck('matriculation_id')
                        ->toArray()
                );
                $dados->each(
                    function ($item) use ($dado) {
                        $dado->push($item);
                    }
                );


                $dados = $dado;

                // $grades = $grades->whereNotIn('user_id',$dados->pluck('user_id')->toArray());
                $grade = collect();
                $grades->each(function ($item) use ($grade) {
                    $grade->push($item);
                });
                $grades = $grade;
            }




            $config = DB::table('avalicao_config')->first();

            return json_encode(
                array(
                    'metricas' => $metrics,
                    'students' => $dados,
                    'grades' => $grades,
                    'estado_pauta' => $estado_p,
                    'config' => $config,
                    'estado_pauta_lancar' => $estado_l,
                    'students_segunda_chamada' => $students_segunda_chamada,
                    'version' => $pauta_version,
                    'pauta_id' => $pauta_id,
                    'pauta_path' => $pauta_path
                )
            );
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





















    private function EstudanteMac($Turma_id_Select, $id_curso, $id_anoLectivo, $id_disciplina, $lectiveYearSelected, $metrics_analise_exame, $config)
    {
        // return $Turma_id_Select." ". $id_curso." - ". $id_anoLectivo." - ".$id_disciplina;
        //ESTÁ FUNCIONANDO - SÓ FALTA VALIDAR
        $avaliacaos_student = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
            ->leftJoin('metricas as mt', 'mt.avaliacaos_id', '=', 'avl.id')
            ->leftJoin('avaliacao_alunos as avl_aluno', function ($join) {
                $join->on('avl_aluno.metricas_id', '=', 'mt.id');
                $join->on('avl_aluno.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id');
            })

            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', 'matricula_disci.matriculation_id', '=', 'matricula.id')

            ->select([
                'avl.nome as Avaliacao_nome',
                'full_name.value as user_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as n_student',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as class_id',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as discipline_id',
                'mt.nome as Metrica_nome',
                'dt.display_name as discipline',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula.id as id_mat',
                'matricula_disci.discipline_id as matricula_disciplineID',
                'matricula_disci.exam_only as e_f',
                'avl_aluno.presence as presenca'
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            //Todos com matriculados por frequencia.
            ->where('matricula_disci.exam_only', '=', 0)
            ->where('mt.id', '!=', $metrics_analise_exame[0]->id)
            ->whereIn('mt.code_dev', ['PF1', 'PF2', 'OA'])
            ->where('matricula_disci.discipline_id', $id_disciplina)
            ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->whereBetween('mt.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->whereNull('matricula.deleted_at')
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->distinct()
            ->get();


        //Filtrar os com negativa em mac


        $collection = collect($avaliacaos_student)
            ->groupBy('user_name')
            ->map(function ($item, $key) use ($config) {
                $count = count($item);

                $pct1 = isset($item[0]->percentagem_metrica) ? $item[0]->percentagem_metrica / 100 : 35 / 100;
                $pct2 = isset($item[1]->percentagem_metrica) ? $item[1]->percentagem_metrica / 100 : 35 / 100;
                $pct3 = isset($item[2]->percentagem_metrica) ? $item[2]->percentagem_metrica / 100 : 30 / 100;
                //Nota__>


                $Nota1 = isset($item[0]->nota_anluno) ? $item[0]->nota_anluno : 0;
                $Nota2 = isset($item[1]->nota_anluno) ? $item[1]->nota_anluno : 0;
                $Nota3 = isset($item[2]->nota_anluno) ? $item[2]->nota_anluno : 0;

                $PF1 = $Nota1 != null ? $Nota1 * $pct1 : 0;
                $PF2 = $Nota2 != null ? $Nota2 * $pct2 : 0;
                $OA = $Nota3 != null ? $Nota3 * $pct3 : 0;



                $resulatado = round($PF1 + $PF2 + $OA / 1);

                if ($resulatado >= $config->exame_nota_inicial && $resulatado <= $config->exame_nota_final) {
                    return $array[] = $item;
                }
            });

        $ID_users_EXame = [];

        foreach ($collection as $item) {
            if ($item != null) {
                $ID_users_EXame[] = $item[0]->user_id;
            }
        }

        return $ID_users_EXame;
    }



































    private function EstudanteRecurso($id_disciplina, $id_matricula_users, $lectiveYearSelected)
    {
        // return $Turma_id_Select." ". $id_curso." - ". $id_anoLectivo." - ".$id_disciplina;
        //Método para filtrar os alunos que foram a recurso
        $article_id = "exame_recurso";

        return  $studantes = DB::table('tb_recurso_student as tb_recurso')
            ->join("matriculations as matri", 'matri.id', 'tb_recurso.matriculation_id')
            ->join("users as user", 'user.id', 'matri.user_id')
            //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
            ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
            ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
            ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
            ->where('code_dev.code', $article_id)
            ->where('user_emolumento.status', "total")
            ->whereBetween('article_emolumento.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            //fim dos pagos
            ->where('tb_recurso.id_lectiveYear', $lectiveYearSelected->id)
            ->where('tb_recurso.discipline_id', $id_disciplina)
            ->where('tb_recurso.estado_exame', 1)
            ->whereIn('tb_recurso.matriculation_id', $id_matricula_users)


            ->get()
            ->map(function ($item, $key) {
                return $item->matriculation_id;
            });
    }


    private function EstudanteExameOral($id_disciplina, $id_matricula_users, $lectiveYearSelected)
    {
        // return $Turma_id_Select." ". $id_curso." - ". $id_anoLectivo." - ".$id_disciplina;

        return  $studantes = DB::table('tb_exame_oral_student as tb_ExameOral')
            ->join("matriculations as matri", 'matri.id', 'tb_ExameOral.matriculation_id')
            ->join("users as user", 'user.id', 'matri.user_id')

            ->where('tb_ExameOral.id_lectiveYear', $lectiveYearSelected->id)
            ->where('tb_ExameOral.discipline_id', $id_disciplina)
            ->whereIn('tb_ExameOral.matriculation_id', $id_matricula_users)


            ->get()
            ->map(function ($item, $key) {
                return $item->matriculation_id;
            });
    }












    private function EstudanteExameEspecial($id_disciplina, $id_turma, $lectiveYearSelected)
    {

        //Método para filtrar os alunos que foram a recurso
        $article_id = "exame_especial";

        return  $studantes = DB::table('tb_exame_studant as tb_exame_studant')
            ->join("users as user", 'user.id', 'tb_exame_studant.id_user')

            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
            ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
            ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
            ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
            ->where('code_dev.code', $article_id)
            ->where('user_emolumento.status', "total")
            ->where('article_emolumento.anoLectivo', $lectiveYearSelected->id)
            //fim dos pagos
            ->where('tb_exame_studant.id_lectiveYear', $lectiveYearSelected->id)
            ->where('tb_exame_studant.id_discipline', $id_disciplina)
            ->where('tb_exame_studant.id_class', $id_turma)
            ->where('tb_exame_studant.status', 1)
            ->select('user.id as user_id', 'up_meca.value as n_student', 'u_p.value as user_name')

            ->distinct()
            ->get()

            ->map(function ($item, $key) use ($id_disciplina, $id_turma) {

                return [

                    "user_id" => $item->user_id,
                    "user_name" => $item->user_name,
                    "n_student" => $item->n_student,
                    "discipline_id" => $id_disciplina,
                    "class_id" => $id_turma,
                    "e_f" => 0

                ];
            });
    }





    private function EstudanteExameExtraordinario($id_disciplina, $id_turma, $lectiveYearSelected)
    {

        //Método para filtrar os alunos que foram a recurso
        $article_id = "exame_extraordinario";

        return  $studantes = DB::table('tb_exame_melhoria_nota as tb_exame_studant')
            ->join('matriculations as mt', 'mt.user_id', '=', 'tb_exame_studant.id_user')
            ->join('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
            ->where('tb_exame_studant.finalist', 1)
            ->join("users as user", 'user.id', 'tb_exame_studant.id_user')

            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
            ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
            ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
            ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
            ->where('code_dev.code', $article_id)
            ->where('user_emolumento.status', "total")
            ->where('article_emolumento.anoLectivo', $lectiveYearSelected->id)
            //fim dos pagos
            ->where('tb_exame_studant.id_lectiveYear', $lectiveYearSelected->id)
            ->where('tb_exame_studant.id_discipline', $id_disciplina)
            ->where('mc.class_id', $id_turma)
            ->where('tb_exame_studant.status', 1)
            ->select('user.id as user_id', 'up_meca.value as n_student', 'u_p.value as user_name')

            ->distinct()
            ->get()

            ->map(function ($item, $key) use ($id_disciplina, $id_turma) {

                return [

                    "user_id" => $item->user_id,
                    "user_name" => $item->user_name,
                    "n_student" => $item->n_student,
                    "discipline_id" => $id_disciplina,
                    "class_id" => $id_turma,
                    "e_f" => 0

                ];
            });

    }
















    //Metodo que pega todos os estudantes para atribuir OA
    public function studentAjaxOA_new($id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id, $id_anoLectivo, $numero_prova)
    {
        //avaliar se a metrica ja foi concluida, se retornar algo é porque já foi concluida
        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();
        $consulta_aluno = $this->students_matriculado($id, $lectiveYearSelected->id);

        $consulta_aluno
            ->where('mc.class_id', $class_id)
            ->where('md.exam_only', 0)

            ->get();

        $dados = $consulta_aluno->get();
        // return $ener = $consulta_aluno->get();



        $grades = DB::table('tmp_oa')
            ->where('avaliacaos_id', $avaliacao_id)
            ->where('class_id', $class_id)
            ->where('oa_number', $numero_prova)
            ->where('discipline_id', $id)
            ->get();

        $estado_lancar = DB::table('lancar_pauta')
            ->where(['id_turma' => $class_id, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id, 'pauta_tipo' => 'OA'])
            ->orderBy('version', 'DESC')
            ->first();

        $pauta_version = isset($estado_lancar) && isset($estado_lancar->version) ? $estado_lancar->version : 0;
        $pauta_path = isset($estado_lancar) ? $estado_lancar->path : Null;

        return json_encode(array(
            'students' => $dados,
            'grades' => $grades,
            'version' => $pauta_version,
            'pauta_path' => $pauta_path
        ));
    }





















    //Pega os estudades matriculados
    public function students_matriculado($id, $lectiveYearSelectedId)
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
            //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
            ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'users.id')
            ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
            ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
            ->whereIn('code_dev.code', ["confirm", "p_matricula"])
            ->where('user_emolumento.status', "total")
            ///->whereBetween('article_emolumento.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ///->whereBetween('mt.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->where('mt.lective_year', $lectiveYearSelectedId)
            //fim dos pagos
            //->where('md.discipline_id','dp.id')

            ->select([
                'mt.id as id_mat',
                'md.discipline_id',
                'users.id as user_id',
                'crs.id as id_curso',
                'u_p.value as user_name',
                'ct.display_name as course',
                'dt.display_name as discipline',
                'up_n.value as n_student',
                'mc.class_id as class_id',
                'md.exam_only as e_f',
            ])
            ->where('md.discipline_id', $id)
            ->whereNull('mt.deleted_at')
            //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
            ->orderBy('user_name', 'ASC')
            ->distinct();

        return $students;
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
            if ($avaliacao_id == 21 || $avaliacao_id == 23) {

                $current_pea = PlanoEstudoAvaliacao::where('disciplines_id', $discipline_id)
                    ->where('avaliacaos_id', 21)
                    ->firstOrFail();
            } else {
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
            } else if ($avaliacao_id == 22) {


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
            } elseif ($avaliacao_id == 25) {
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
                    ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
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
                    ->where('article_requests.status', "total")
                    ->where('article_requests.article_id', 32) //emolumento (exame de especial)

                    //avaliar recurso (avaliar tanto os que vêem do exame ou MAC)
                    ->orderBy('user_name', 'ASC')
                    ->distinct()
                    ->get();
            } else {
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


            return json_encode(array('metricas' => $metricas, 'students' => $students, 'grades' => $grades, 'avaliacao' => $avaliacao, 'disciplineHasMandatoryExam' => $disciplineHasMandatoryExam));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

        /*
            1 - Avaliar se a avaliacao (ou tipo de avaliacao) é exame
            2 -  Avaliar se a disciplina te exame obrigatorio ou nao
            3 - Avaliar se tem notas (MAC):
                    - Maior ou igual a 6,5 exame
                    - Menor que 50 recurso directo
                    - Menor que 15 recurso
            4 - Levar a mesma logica na hora de concluir a avaliacao
            Como é que pego a nota do MAC de cada estudante?
            R: a avaliacao tem que ser concluida e pegar a nota no historico
        */
    }



























































































    public function disciplina_teacher($anolectivo)
    {
        //Método pega todas as disciplinas associadas aos planos de estudo e avaliaçoes
        //do respectivos professores, coordenador do curso ou outro cargo

        try {
            $teacher_id = Auth::user()->id;
            $user = User::whereId($teacher_id)->firstOrFail();

            //Quando for Professor e coordenador de um curso pegar as disciplina
            if ($user->hasAnyRole(['teacher']) && $user->hasAnyRole(['coordenador-curso'])) {
                $course_id = DB::table('coordinator_course')
                    ->where('user_id', $teacher_id)
                    ->get();
                //Pegar as disciplinas do professor/coordenador Logado
                $disciplines_coordenador = collect($this->disciplinas_coordenador_todas($course_id->pluck('courses_id')->toArray()));
                $disciplines_professor = collect($this->disciplina_teacher_apenas($teacher_id));

                $all_disciplines = $disciplines_coordenador->merge($disciplines_professor);

                return response()->json(['disciplina' => $all_disciplines, 'whoIs' => "todos"]);
            }

            //se o coordenador for o logado na plataforma
            //Entra neste bloco e trás toda as disciplinas do curso
            if ($user->hasAnyRole(['coordenador-curso'])) {
                $course_id = DB::table('coordinator_course')
                    ->where('user_id', $teacher_id)
                    ->get();
                $disciplinas_coordenador = $this->disciplinas_coordenador_todas($course_id->pluck('courses_id')->toArray());
                return response()->json(['disciplina' => $disciplinas_coordenador, 'whoIs' => "coordenador"]);
            }
            //Quando for Professor pegar as disciplina
            //que ele leciona.

            else if ($user->hasAnyRole(['teacher'])) {
                //Pegar as disciplinas do professor Logado
                $disciplines = $this->disciplina_teacher_apenas($teacher_id);
                return response()->json(['disciplina' => $disciplines, 'whoIs' => "teacher"]);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




















    // metodo para pegar apenas as disciplinas de um professor.
    private function disciplina_teacher_apenas($teacher_id)
    {
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
            // ->where('lective_years_id',$anolectivo)
            ->where('user_disciplines.users_id', $teacher_id)
            //->where('dp.courses_id',$course_id->courses_id)
            ->distinct()
            ->get();

        //dd($getMyDisciplines->where('code','EP4107'));

        return  $dados = $getMyDisciplines;
    }

    //metodo para pegar as disciplinas do cordenandor
    private function disciplinas_coordenador_todas($id_curso)
    {

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
            // ->leftJoin('user_disciplines', 'user_disciplines.disciplines_id', '=', 'dp.id')
            ->select([
                'crs.id as course_id',
                'ct.display_name as course_name',
                'dp.id as discipline_id',
                'dp.code as code',
                'dt.display_name as dt_display_name',
                // 'user_disciplines.users_id as id_teacher'
            ])
            // ->where('stpeid.id', $id)
            // ->where('lective_years_id',$anolectivo)
            // ->where('user_disciplines.users_id', $teacher_id)
            ->whereIn('dp.courses_id', $id_curso)
            ->whereNotNull('dt.display_name')
            ->distinct()
            ->get();

        return $getDisciplinesAll;
    }




















































    public function getTurmasDisciplinaOA($id_edicao_plain, $anoLectivo)

    {
        try {

            $id = explode(",", $id_edicao_plain);

            $cargo = $id[0];
            $id_curso = $id[1];
            $id_disciplina = $id[2];
            $currentData = Carbon::now();
            $teacher_id = Auth::id();
            //Pega o ano curricular da disciplina.
            //pega tbm o id_plano_estudo.


            //Periodo da disciplina (saber se é anual ou simestral)
            $period_disciplina = DB::table('disciplines')
                ->where('id', $id_disciplina)
                ->get();

            $Simestre = $period_disciplina->map(function ($item, $key) {
                $periodo = substr($item->code, -3, 1);
                if ($periodo == "1") {
                    return "1_simestre";
                }
                if ($periodo == "2") {
                    return "2_simestre";
                }
                if ($periodo == "A") {
                    return "Anual";
                } else {
                    return 0;
                }
            });
            //Fim do perios

            $courseYear = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                // ->where('stpeid.id', $id_edicao_plain)
                ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
                ->where('stpeid.lective_years_id', $anoLectivo)
                ->select(['plano_estudo_avaliacaos.*', 'stpeid.*'])
                ->get();

            //Primeiro if compara se está fazia a associação entre disciplina e plano de estudo e avalização.
            if (!$courseYear->isEmpty()) {
                $id_plano_estudo = $courseYear[0]['study_plan_editions_id'];

                $verificarDisciplina = new VerificarDisciplina($id_disciplina);
                if ($cargo == "todos") {
                    //Bloco que verifica se os dois cargos e descobre se vai retornar as turmas dele sendo
                    //professor ou coordenandor
                    $verifyCoordenador = $verificarDisciplina->verifyIsCoordernador($teacher_id);
                    $verificarDisciplina->user_type = $verifyCoordenador ? "coordenador" : "teacher";
                }

                if ($cargo == "coordenador" || $verificarDisciplina->user_type == "coordenador") {
                    //Pega avalicao mac e metrica OA (Sem cumprir a regra do calendário de data)
                    $avaliacao = $this->avaliacaoesOA($id_disciplina, $anoLectivo);
                    //Pega as todas as turmas do coordenador
                    $turmas = $this->turmas_coordenador($courseYear, $id_plano_estudo, $id_curso, $anoLectivo);
                    return response()->json(['turma' => $turmas, 'avaliacao' => $avaliacao->first(), 'whoIs' => "super", 'plano_estudo' => $id_plano_estudo, 'disciplina' => $id_disciplina, 'periodo' => $Simestre]);
                }

                if ($cargo == "teacher" ||  $verificarDisciplina->user_type == "teacher") {
                    //Pega avalicao no intervalo de data
                    $avaliacao_time = $this->avaliacaoesOA($id_disciplina, $anoLectivo);
                    //Pega toda as turmas do professor onde ele leciona esta disciplina
                    $turmas = $this->turmas_teacher($teacher_id, $courseYear, $id_plano_estudo, $anoLectivo);

                    return response()->json(['turma' => $turmas, 'avaliacao' => $avaliacao_time->first(), 'whoIs' => "teacher", 'plano_estudo' => $id_plano_estudo, 'disciplina' => $id_disciplina, 'periodo' => $Simestre]);
                }
            } else {
                return response()->json(500);
            }

            //Pega todas as avaliaçoes das disciplina selecionada
            //como o objectivo é retornar por época de calendário
            //A ideia é colocar um calendário nas Mac e criar também calendário de cada item da Mac(PF1, PF2 e OA)
            //No final retornar apenas a pauta do item da avaliaçao selecionada,
            //EX: seleciona a disciplina, seleciona a turma e no final aparece a pauta daquela época.
            // return response()->json(array('turma'=>$turma, 'metrica'=>$metrica_filtrada, 'pea'=>$pea));
        } catch (Exception | Throwable $e) {
            logError($e);

            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


































































    public function getTurmasDisciplina(Request $request, $id_edicao_plain, $anoLectivo){
    try {
        Log::info('Iniciando getTurmasDisciplina', [
            'id_edicao_plain' => $id_edicao_plain,
            'anoLectivo' => $anoLectivo,
            'segunda_chamada' => $request->query('segunda_chamada', null)
        ]);

        $segunda_chamada = $request->query('segunda_chamada', null);
        $id = explode(",", $id_edicao_plain);

        $cargo = $id[0];
        $id_curso = $id[1];
        $id_disciplina = trim($id[2]);
        $currentData = Carbon::now();
        $teacher_id = Auth::id();

        Log::info('Parâmetros extraídos', [
            'cargo' => $cargo,
            'id_curso' => $id_curso,
            'id_disciplina' => $id_disciplina,
            'teacher_id' => $teacher_id,
            'currentData' => $currentData->toDateTimeString()
        ]);

        // Pega o ano curricular da disciplina e o id_plano_estudo
        $courseYear = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $anoLectivo)
            ->select(['plano_estudo_avaliacaos.*', 'stpeid.*']);

        Log::info('Consulta courseYear construída', [
            'query' => $courseYear->toSql(),
            'bindings' => $courseYear->getBindings()
        ]);

        $courseYear = $courseYear->get();

        Log::info('Resultado courseYear', [
            'count' => $courseYear->count(),
            'data' => $courseYear->toArray()
        ]);

        // Verifica se a associação entre disciplina e plano de estudo existe
        if (!$courseYear->isEmpty()) {
            $id_plano_estudo = $courseYear[0]['study_plan_editions_id'];

            Log::info('id_plano_estudo extraído', ['id_plano_estudo' => $id_plano_estudo]);

            $verificarDisciplina = new VerificarDisciplina($id_disciplina);
            if ($cargo == "todos") {
                $verifyCoordenador = $verificarDisciplina->verifyIsCoordernador($teacher_id);
                $verificarDisciplina->user_type = $verifyCoordenador ? "coordenador" : "teacher";

                Log::info('Cargo "todos" verificado', [
                    'verifyCoordenador' => $verifyCoordenador,
                    'user_type' => $verificarDisciplina->user_type
                ]);
            }

            if ($cargo == "coordenador" || $verificarDisciplina->user_type == "coordenador") {
                Log::info('Entrando no bloco de coordenador');

                // Pega avaliações para coordenador
                $avaliacao = $this->avaliacaoes_coordenador($id_disciplina, $anoLectivo);

                if (isset($segunda_chamada)) {
                    $avaliacao = $avaliacao->whereNotIn('avl.nome', ['Recursos']);
                }

                Log::info('Consulta avaliacao (coordenador) construída', [
                    'query' => $avaliacao->toSql(),
                    'bindings' => $avaliacao->getBindings()
                ]);

                // Pega turmas do coordenador
                $turmas = $this->turmas_coordenador($courseYear, $id_plano_estudo, $id_curso, $anoLectivo);

                $avaliacaoResult = $avaliacao->get();

                Log::info('Resultados do bloco coordenador', [
                    'turmas_count' => count($turmas),
                    'avaliacao_count' => $avaliacaoResult->count(),
                    'avaliacao_data' => $avaliacaoResult->toArray()
                ]);

                // Verifica campos nulos em avaliacao
                foreach ($avaliacaoResult as $row) {
                    if (is_null($row->avl_id) || is_null($row->avl_nome)) {
                        Log::warning('Campos nulos detectados em avaliacao (coordenador)', [
                            'row' => $row->toArray()
                        ]);
                    }
                }

                return response()->json([
                    'turma' => $turmas,
                    'avaliacao' => $avaliacaoResult,
                    'whoIs' => "super",
                    'plano_estudo' => $id_plano_estudo,
                    'disciplina' => $id_disciplina
                ]);
            } else if ($cargo == "teacher" || $verificarDisciplina->user_type == "teacher") {
                Log::info('Entrando no bloco de teacher');

                // Pega o período da disciplina
                $period_disciplina = DB::table('disciplines')
                    ->where('id', $id_disciplina)
                    ->get();

                Log::info('Resultado period_disciplina', [
                    'count' => $period_disciplina->count(),
                    'data' => $period_disciplina->toArray()
                ]);

                $Simestre = $period_disciplina->map(function ($item, $key) {
                    $periodo = substr($item->code, -3, 1);
                    if ($periodo == "1") {
                        return 1;
                    }
                    if ($periodo == "2") {
                        return 4;
                    }
                    if ($periodo == "A") {
                        return 2;
                    }
                    return 0;
                });

                Log::info('Semestre calculado', ['Simestre' => 2]);

                // Pega avaliações no intervalo de data
                $avaliacao_time = $this->avaliacaoes($id_disciplina, $anoLectivo);

                Log::info('Consulta avaliacao_time construída', [
                    'query' => $avaliacao_time->toSql(),
                    'bindings' => $avaliacao_time->getBindings()
                ]);

                $avaliacao = $avaliacao_time
                    ->whereRaw('"' . date("Y-m-d") . '" between `date_start` and `data_end`')
                    ->where('simestre', $Simestre)
                    ->first();

                Log::info('Resultado avaliacao (teacher)', [
                    'avaliacao' => $avaliacao ? (array)$avaliacao : null
                ]);

                if (!$avaliacao) {
                    Log::warning('Nenhuma avaliação encontrada para o teacher', [
                        'id_disciplina' => $id_disciplina,
                        'anoLectivo' => $anoLectivo,
                        'Simestre' => $Simestre->toArray(),
                        'current_date' => date("Y-m-d")
                    ]);
                }

                $id_avl = $avaliacao ? $avaliacao['avl_id'] : null;

                Log::info('ID da avaliação', ['id_avl' => $id_avl]);

                // Pega turmas do professor
                $turmas = $this->turmas_teacher($teacher_id, $courseYear, $id_plano_estudo, $anoLectivo);

                Log::info('Turmas do teacher', ['turmas_count' => count($turmas)]);

                // Pega métricas
                $metrica = $this->metricas_avaliacoes($currentData);

                Log::info('Consulta metrica construída', [
                    'query' => $metrica->toSql(),
                    'bindings' => $metrica->getBindings()
                ]);

                if (isset($segunda_chamada)) {
                    $Metrica_calendario = $metrica
                        ->join('calendarie_metrica_segunda_chamada as sc', 'sc.id_calendarie_metrica', 'c_m.id')
                        ->where('mtrc.avaliacaos_id', $id_avl)
                        ->where('mtrc.calendario', '!=', 1)
                        ->where('c_m.id_periodo_simestre', $Simestre)
                        ->whereDate('sc.data_inicio', '<=', date("Y-m-d"))
                        ->whereDate('sc.data_fim', '>=', date("Y-m-d"))
                        ->orderBy('sc.data_inicio', 'DESC');

                    Log::info('Consulta Metrica_calendario (segunda chamada) construída', [
                        'query' => $Metrica_calendario->toSql(),
                        'bindings' => $Metrica_calendario->getBindings()
                    ]);

                    $Metrica_calendario = $Metrica_calendario->get();

                    $avaliacao = $avaliacao && $avaliacao->avl_nome == 'Recursos' ? null : $avaliacao;
                } else {
                    $Metrica_calendario = $metrica
                        ->where('mtrc.avaliacaos_id', $id_avl)
                        ->where('mtrc.calendario', '!=', 1)
                        ->where('c_m.id_periodo_simestre', $Simestre)
                        ->whereDate('c_m.data_inicio', '<=', date("Y-m-d"))
                        ->whereDate('c_m.data_fim', '>=', date("Y-m-d"))
                        ->orderBy('c_m.data_inicio', 'DESC');

                    Log::info('Consulta Metrica_calendario construída', [
                        'query' => $Metrica_calendario->toSql(),
                        'bindings' => $Metrica_calendario->getBindings()
                    ]);

                    $Metrica_calendario = $Metrica_calendario->get();
                }

                Log::info('Resultado Metrica_calendario', [
                    'count' => $Metrica_calendario->count(),
                    'data' => $Metrica_calendario->toArray()
                ]);

                // Verifica campos nulos em Metrica_calendario
                foreach ($Metrica_calendario as $row) {
                    if (is_null($row->mtrc_id) || is_null($row->mtrc_nome) || is_null($row->c_m_inicio) || is_null($row->c_m_fim)) {
                        Log::warning('Campos nulos detectados em Metrica_calendario', [
                            'row' => $row->toArray()
                        ]);
                    }
                }

                return response()->json([
                    'turma' => $turmas,
                    'avaliacao' => $avaliacao,
                    'metrica' => $Metrica_calendario,
                    'whoIs' => 'teacher',
                    'plano_estudo' => $id_plano_estudo,
                    'disciplina' => $id_disciplina
                ]);
            }
        } else {
            Log::warning('Nenhum courseYear encontrado', [
                'id_disciplina' => $id_disciplina,
                'anoLectivo' => $anoLectivo
            ]);
            return response()->json(500);
        }
    } catch (Exception | Throwable $e) {
        Log::error('Erro em getTurmasDisciplina', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
}









    private function turmas_teacher($id_teacher, $courseYear, $plano_edition, $anoLectivo)
    {
        Log::info('dados da consulta', [
            'teacher_id' => $id_teacher,
            'courseYear' => $courseYear,
            'plano_edition' => $plano_edition,
            'anoLectivo' => $anoLectivo,
        ]);

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












    //Pegar as turmas
    private function turmas_coordenador($courseYear, $plano_edition, $id_curso, $anoLectivo)
    {
        $turma =  PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            ->leftJoin('classes', 'classes.courses_id', '=', 'crs.id')
            ->leftJoin('user_classes', 'user_classes.class_id', '=', 'classes.id')
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



    //Pegar as avaliações ao atribuir notas com calendário
    private function avaliacaoes($id_disciplina, $anoLectivo)
    {
        Log::info('Iniciando avaliacaoes', [
            'id_disciplina' => $id_disciplina,
            'anoLectivo' => $anoLectivo
        ]);

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
            ->join('calendario_prova as c_p', 'c_p.id_avaliacao', '=', 'avl.id')
            ->select([
                'avl.code_dev as codev',
                'avl.id as avl_id',
                'avl.nome as avl_nome',
                'dp.code as discipline_code',
                'c_p.date_start as inicio',
                'c_p.data_end as fim',
                'c_p.simestre'
            ])
            ->where('dp.id', $id_disciplina)
            ->where('c_p.deleted_by', null)
            ->where('c_p.lectiveYear', $anoLectivo)
            ->distinct();

        Log::info('Consulta avaliacaoes construída', [
            'query' => $avaliacaos->toSql(),
            'bindings' => $avaliacaos->getBindings()
        ]);

        $result = $avaliacaos->get();

        Log::info('Resultado da consulta avaliacaoes', [
            'count' => $result->count(),
            'data' => $result->toArray()
        ]);

        if ($result->isEmpty()) {
            Log::warning('Nenhum resultado encontrado para avaliacaoes', [
                'id_disciplina' => $id_disciplina,
                'anoLectivo' => $anoLectivo
            ]);
        }

        foreach ($result as $row) {
            if (is_null($row->codev) || is_null($row->avl_id) || is_null($row->avl_nome) ||
                is_null($row->discipline_code) || is_null($row->inicio) || is_null($row->fim) ||
                is_null($row->simestre)) {
                Log::warning('Campos nulos detectados em avaliacaoes', [
                    'row' => $row->toArray()
                ]);
            }
        }

        return $avaliacaos;
    }

    //Pegar as avalições do coordenador ... por distinção
    private function avaliacaoes_coordenador($id_disciplina, $anoLectivo){


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
            ->leftJoin('calendario_prova as c_p', 'c_p.id_avaliacao', '=', 'avl.id')
            ->select(['avl.id as avl_id', 'avl.nome as avl_nome', 'dp.code as discipline_code'])
            ->where('dp.id', $id_disciplina)
            ->where('c_p.deleted_by', null)
            ->where('avl.anoLectivo', $anoLectivo)
            ->distinct('avl_id');

        return $avaliacaos;
    }

    //Pegar as avaliações ao atribuir notas OA sem calendário
    private function avaliacaoesOA($id_disciplina, $anoLectivo)
{
    Log::info('Iniciando avaliacaoesOA', [
        'id_disciplina' => $id_disciplina,
        'anoLectivo' => $anoLectivo
    ]);

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
        ->join('metricas as mtr', 'mtr.avaliacaos_id', '=', 'avl.id')
        ->select([
            'avl.id as avl_id',
            'avl.nome as avl_nome',
            'dp.code as discipline_code',
            'mtr.nome as metrica',
            'mtr.id as id_metrica'
        ])
        ->where('dp.id', $id_disciplina)
        ->where('avl.deleted_by', null)
        ->where('mtr.calendario', 1)
        ->where('avl.anoLectivo', $anoLectivo)
        ->distinct();

    Log::info('Consulta construída', [
        'query' => $avaliacaos->toSql(),
        'bindings' => $avaliacaos->getBindings()
    ]);

    $result = $avaliacaos->get();

    Log::info('Resultado da consulta', [
        'count' => $result->count(),
        'data' => $result->toArray()
    ]);

    if ($result->isEmpty()) {
        Log::warning('Nenhum resultado encontrado para a consulta', [
            'id_disciplina' => $id_disciplina,
            'anoLectivo' => $anoLectivo
        ]);
    }

    // Verificar se campos específicos estão retornando null
    foreach ($result as $row) {
        if (is_null($row->avl_id) || is_null($row->avl_nome) || is_null($row->discipline_code) ||
            is_null($row->metrica) || is_null($row->id_metrica)) {
            Log::warning('Campos nulos detectados', [
                'row' => $row->toArray()
            ]);
        }
    }

    return $avaliacaos;
}





   private function metricas_avaliacoes($data){
    Log::info('Iniciando metricas_avaliacoes', [
        'data' => $data
    ]);

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
        ->leftJoin('calendarie_metrica as c_m', 'mtrc.id', '=', 'c_m.id_metrica')
        ->whereNull('c_m.deleted_at')
        ->select([
            'mtrc.id as mtrc_id',
            'mtrc.avaliacaos_id as mtrc_avaliacaos_id',
            'mtrc.nome as mtrc_nome',
            'c_m.data_inicio as c_m_inicio',
            'c_m.data_fim as c_m_fim',
            'avl.nome as avalicacao',
            'avl.id as avl_id',
            'mtrc.code_dev as code_dev',
            'c_m.id as cm_id'
        ])
        ->distinct();

    Log::info('Consulta construída', [
        'query' => $metricas->toSql(),
        'bindings' => $metricas->getBindings()
    ]);

    $result = $metricas->get();

    Log::info('Resultado da consulta', [
        'count' => $result->count(),
        'data' => $result->toArray()
    ]);

    if ($result->isEmpty()) {
        Log::warning('Nenhum resultado encontrado para a consulta', [
            'data' => $data
        ]);
    }

    // Verificar se campos específicos estão retornando null
    foreach ($result as $row) {
        if (is_null($row->mtrc_id) || is_null($row->mtrc_avaliacaos_id) || is_null($row->mtrc_nome) ||
            is_null($row->c_m_inicio) || is_null($row->c_m_fim) || is_null($row->avalicacao) ||
            is_null($row->avl_id) || is_null($row->code_dev) || is_null($row->cm_id)) {
            Log::warning('Campos nulos detectados', [
                'row' => $row->toArray()
            ]);
        }
    }

    return $metricas;
}

























    public function getStudentFinalGrades()
    {
        try {
            //Pegar o ano lectivo na select
            return  $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
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
            //dd();
            return view("Avaliations::avaliacao-aluno.show-final-avaliacao-aluno", compact('pea', 'lectiveYearSelected', 'lectiveYears'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
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


            return json_encode(array(
                'avaliacaos' => $avaliacaos,
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
                }
            ])->where('id', $discipline_id)->firstOrFail();

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
                'avaliacaos' => $avaliacaos,
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
            return $pdf->stream('Pauta Final' . '.pdf');
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

            return json_encode(array('avaliacaos' => $avaliacaos, 'students' => $students, 'finalGrades' => $finalGrades));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function AddOAGrades()
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





























































































    public function StoreOAGrades(Request $request)

    {
        try {

            if (!$request->estudantes) {
                Toastr::warning(__('O sistema detetou que tentou guardar notas de uma turma sem estudantes listado, por favor selecione uma turma que tenha estudante listados na tabela, para que lhe seja permitido(a) guardar notas.'), __('toastr.warning'));
                return back();
            }

            $notaInvalida = false;
            foreach (($request->notas ?? []) as $key => $value)
                if ($value > 20 || $value < 0) {
                    $notaInvalida = true;
                    break;
                }

            if ($notaInvalida) {
                Toastr::warning(__('O sistema detetou que tentou guardar nota que não esta no intervala de 0 à 20.'), __('toastr.warning'));
                return back();
            }

            // Return  $request;
            //IMPORTANTE APOS A AVALIACAO SE CONCLUIDA LIMPAR A TABELA TMP_OA
            //NAO LIMPAR TODA TABELA LIMPAR SO OS DADOS EM QUESTAO (Estudantes da metrica_ SELECIONADA)

            // DB::transaction(function () use ($request) {

            $discipline = explode(",", $request->disciplina);
            //Pegar o id do plano de estudo e avaliações
            $Plano_E_avaliacao = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                ->where('stpeid.id', $request->id_plano)
                ->where('plano_estudo_avaliacaos.disciplines_id', $discipline[2])
                ->where('plano_estudo_avaliacaos.avaliacaos_id', $request->id_avaliacao)
                ->select(['plano_estudo_avaliacaos.*', 'stpeid.id as p_e_a'])
                ->first();



            $metrica_id = $request->metrica_teacher;
            $course = $discipline[1]; //curso
            $discipline_id = $discipline[2];
            $avaliacao_id = $request->id_avaliacao;

            //Saber se é Anual ou simestral
            $disciplinaREgime = DB::table('disciplines')->where('id', $discipline_id)->first();
            //REgime varialvel
            $regime = "";
            $periodo = substr($disciplinaREgime->code, -3, 1);
            if ($periodo == "1" || $periodo == "2") {
                $regime = 6;
            } else if ($periodo == "A") {
                $regime = 12;
            }


            $data = [
                'notas' => $request->notas,
                'estudantes' => $request->estudantes,
                'falta' => $request->inputCheckBox
            ];

            //Presença é vista
            for ($i = 0; $i < count($data['notas']); $i++) {

                $nota = $data['notas'][$i] == null ? 0 : $data['notas'][$i];



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
                        'grade'         => $nota,
                        'metricas_id'   => $metrica_id,
                        'created_at'    => date("Y-m-d H:i:s"),
                        'updated_at'    => date("Y-m-d H:i:s"),
                        'presence' => $nota == null ? 1 : null,
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

                $QTDOA =  DB::table('tmp_oa')
                    ->where('user_id', $data['estudantes'][$i])
                    ->where('class_id', $request->turma)
                    ->where('discipline_id', $discipline_id)
                    //Aqui falta adicionar um where para quando a edicao de plano de estudo mudar (proximo ano)
                    ->count();

                $presense_OA = null;
                if ($QTDOA == 1) {
                    $estado_Presence =  DB::table('tmp_oa')
                        ->where('user_id', $data['estudantes'][$i])
                        ->where('class_id', $request->turma)
                        ->where('discipline_id', $discipline_id)
                        ->first();

                    $presense_OA = $estado_Presence->presence;
                }

                $mediaOA = $somaOA / $QTDOA;


                $avaliacaoAluno =  AvaliacaoAluno::updateOrCreate([
                    'plano_estudo_avaliacaos_id' => $Plano_E_avaliacao->id,
                    'metricas_id' => $metrica_id,
                    'users_id' => $data['estudantes'][$i],
                    'id_turma' => $request->turma
                ], [
                    'nota' => $presense_OA == null ? $mediaOA : null,
                    'updated_by' => Auth::user()->id,
                    'created_by' => Auth::user()->id,
                    'presence' => $presense_OA,
                ]);




                $lective = $request->anoLectivo;

                $version = $request->version  + 1;
                DB::table('lancar_pauta')
                    ->where('id_turma', $request->turma)
                    ->where('id_ano_lectivo', $lective)
                    ->where('id_disciplina', $discipline_id)
                    ->where('pauta_tipo', 'OA')
                    ->where('active', 1)
                    ->update([
                        'active' => 0
                    ]);

                $e = DB::table('lancar_pauta')
                    ->where('id_turma', $request->turma)
                    ->where('id_ano_lectivo', $lective)
                    ->where('id_disciplina', $discipline_id)
                    ->where('pauta_tipo', 'OA')
                    ->where('active', 1)
                    ->where('version', $version)
                    ->exists();

                if ($e) {
                    $version = $version + 1;
                }


                $pauta_id =  DB::table('lancar_pauta')->insertGetId(
                    [
                        'id_turma' => $request->turma,
                        'id_ano_lectivo' => $lective,
                        'id_disciplina' => $discipline_id,
                        'tipo' => 40,
                        'pauta_tipo' => "OA",
                        'version' => $version,
                        'created_by' => (int) auth()->user()->id,
                        'updated_by' => (int) auth()->user()->id,
                        'updated_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'id_user_launched' => auth()->user()->id,
                        'estado' => 1,
                        'active' => 1
                    ]
                );

                event(new GeneratePdfAvaliationEvent($discipline_id, $metrica_id, $request->id_plano, $avaliacao_id, $request->turma, $lective, null, $version));
            }






            //Sucess message
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return back();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
            )
            ->where('oa_number', $oa)
            ->where('class_id', $class_id)
            ->where('discipline_id', $id)
            ->get();


        return json_encode(array('data' => $grades, 'students' => $students, 'metrics' => $metrics));
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
        return json_encode(array('data' => $avaliacaos));
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
        return json_encode(array('data' => $metricas));
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
            }
        ])->where('id', $discipline_id)->firstOrFail();

        $class = Classes::whereId($class_id)->firstOrFail();

        $disciplineHasMandatoryExam = Discipline::join('discipline_has_exam', 'discipline_has_exam.discipline_id', '=', 'disciplines.id')
            ->select('discipline_has_exam.has_mandatory_exam as exam')
            ->where('disciplines.id', $discipline_id)
            ->firstOrFail();


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
        } elseif ($avaliacao_id == 22) {
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
        } else {
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
            'metricas' => $metricas,
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





































    public function getStudentsByCourse($course_id)
    {
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








    public function getGradeByStudent($student_id)
    {
        /*
           1- tratar de erros para quando nao tiver notas uma disciplina selecionada
           2 - exibir notas das avaliacoes que so lançaram*/
        try {
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
                'classes' => $classes,
                'disciplines' => $disciplines,
                'avaliacaos' => $avaliacaos,
                'metricas' => $metricas,
                'grades' => $grades,
                'finalGrades' => $finalGrades,
                'gradesWithPercentage' => $gradesWithPercentage
            ];

            $view = view("Avaliations::avaliacao-aluno.student.content")->with($data)->render();
            return response()->json(['html' => $view]);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }













































    public function getDisciplinesByStudent($student_id)
    {
        try {
            if (Auth::user()->hasAnyRole(['superadmin', 'staff_forlearn', 'teacher'])) {
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
            $pdf->setPaper('a4');

            $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream('Notas das avaliacoes' . '.pdf');
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


            return json_encode(array(
                'avaliacaos' => $avaliacaos,
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



        if ($user->hasAnyRole(['staff_gabinete_termos', 'staff_forlearn', 'superadmin'])) {

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
            return json_encode(array('data' => $disciplines, 'classes' => $classes));
        } elseif ($user->hasAnyRole(['coordenador-curso'])) {

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



            // $turma=DB::table('classes as class')
            // ->select(['class.id as id','class.display_name as turma'])
            // ->where('class.courses_id',$course_id->courses_id)
            // ->where('class.year',$courseYear->course_year)
            // ->where('class.lective_year_id',$id_anoLectivo)
            // ->whereNull('class.deleted_by')
            // ->whereNull('class.deleted_at')
            // ->get();


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


            return json_encode(array('data' => $disciplines, 'classes' => $classes, 'period' => $period));
        }


        //Obter todas as turmas que o professor leciona num determinado curso
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

        return json_encode(array('data' => $disciplines, 'classes' => $classes, 'period' => $period));
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
                [
                    'study_plan_edition_id' => $request->study_plan_edition_id,
                    'discipline_id' => $request->discipline_id,
                    'avaliation_id' => $request->avaliation_id,
                    'class_id' => $request->class_id,
                    'metric_id' => $request->metric_id,
                    'lecive_year' => date('Y')
                ],
                [
                    'study_plan_edition_id' => $request->study_plan_edition_id,
                    'discipline_id' => $request->discipline_id,
                    'avaliation_id' => $request->avaliation_id,
                    'class_id' => $request->class_id,
                    'metric_id' => $request->metric_id,
                    'lecive_year' => date('Y'),
                    'published' => true
                ]
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

    public function historico_pauta_ajax($pauta_id)
    {
        dd($pauta_id);
        try {
            $pauta = DB::table('lancar_pauta')
                ->where('id', $pauta_id)
                ->first();

            $versions = DB::table('lancar_pauta as lp')
                ->where('id_disciplina', '=', $pauta->id_disciplina)
                ->where('id_turma', '=', $pauta->id_turma)
                ->where('pauta_tipo', '=', $pauta->pauta_tipo)
                ->where('segunda_chamada', '=', $pauta->segunda_chamada)
                ->whereNotNull('path')
                ->join('users', 'users.id', 'lp.id_user_launched')
                ->select([
                    'users.name',
                    'lp.*'
                ])
                ->orderBy('lp.updated_at', 'DESC')
                ->get();




            return DataTables::of($versions)
                ->addIndexColumn()
                ->addColumn('file', function ($item) {
                    return view('Avaliations::avaliacao-aluno.datatables.file')->with('item', $item);
                })
                ->rawColumns(['file'])
                ->toJson();
        } catch (Exception $e) {

            dd($e);
        }
    }


    public function lockPauta(Request $request)
    {
        try {
            DB::beginTransaction();
            $pauta_id = $request->pauta_id;

            $pauta = DB::table('lancar_pauta')
                ->where('id', $pauta_id)
                ->first();

            $disciplina = $pauta->id_disciplina;
            $pauta_tipo = $pauta->pauta_tipo;
            $id_ano_lectivo = $pauta->id_ano_lectivo;

            $m = DB::table('metricas')
                ->where('metricas.code_dev', $pauta_tipo)
                ->join('avaliacaos as av', 'av.id', 'metricas.avaliacaos_id')
                ->where('av.anoLectivo', $id_ano_lectivo)
                ->whereNull('metricas.deleted_at')
                ->whereNull('metricas.deleted_by')
                ->select([
                    'metricas.*'
                ])
                ->first();

            $metrica = $m->id;

            $plano_estudo = DB::table('study_plan_edition_disciplines as sped')
                ->where('sped.discipline_id', $disciplina)
                ->join('study_plan_editions as spe', 'spe.id', 'sped.study_plan_edition_id')
                ->where('lective_years_id', $id_ano_lectivo)
                ->select('spe.id as id')
                ->first();
                
            if (!$plano_estudo) {
                DB::rollBack();
                Toastr::error(__('Não foi possível encontrar o plano de estudo para esta pauta.'), __('toastr.error'));
                return redirect()->back();
            }

            $plano_estudo = $plano_estudo->id;
            $avaliacao = $m->avaliacaos_id;
            $turma = $pauta->id_turma;
            $segunda_chamada = $pauta->segunda_chamada;
            $version = $pauta->version;

            DB::table('lancar_pauta')
                ->where('id_turma', $turma)
                ->where('id_ano_lectivo', $id_ano_lectivo)
                ->where('id_disciplina', $disciplina)
                ->where('pauta_tipo', $pauta_tipo)
                ->where('segunda_chamada', $segunda_chamada)
                ->where('active', 1)
                ->update([
                    'active' => 0
                ]);


            event(new GeneratePdfAvaliationEvent($disciplina, $metrica, $plano_estudo, $avaliacao, $turma, $id_ano_lectivo, $segunda_chamada, $version));

            DB::table('lancar_pauta')
                ->where('id', $pauta_id)
                ->update([
                    'active' => 1,
                    'estado' => 1,
                    'id_user_launched' => auth()->user()->id,
                    'updated_at' => Carbon::now()
                ]);



            DB::commit();
            Toastr::success(__('Pauta fechada com sucesso'), __('toastr.success'));
            // return redirect()->route('avaliacao_aluno.index');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }






    public function openPauta($pauta_id)
    {
        try {
            DB::beginTransaction();

            DB::table('lancar_pauta')
                ->where('id', $pauta_id)
                ->update([
                    'estado' => 0
                ]);

            DB::commit();
            Toastr::success(__('Pauta aberta com sucesso! Para que as alterações a serem realizadas seja reflectidas no PDF, deve voltar a fechar a pauta'), __('toastr.success'));
            //return redirect()->route('avaliacao_aluno.index');
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }


    public function addAcademicPathRepair()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            "courses" => $courses,
            "lectiveYears" => $lectiveYears
        ];



        //   if(auth()->user()->id == 12438){

        //     return view("Avaliations::academic-path.repair_academic_path2")->with($data);
        //   }
        return view("Avaliations::academic-path.repair_academic_path")->with($data);
    }

    public function GetStudentAcademicPathRepair(Request $request)
    {


        $studentInfo = DB::table('matriculations')
            ->join('users', 'matriculations.user_id', '=', 'users.id')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['matriculations.course_year as year', 'matriculations.id', 'matriculations.code as mtcode', 'courses.code as code', 'users.name', 'users.email', 'users.id as user_id'])
            ->where('matriculations.lective_year', $request->id_lective_year)
            ->where('courses.id', $request->course_id)
            ->get();

        return $studentInfo;
    }



    public function GetStudentDisciplineAcademicPathRepair(Request $request)
    {
       
        $studentInfo = DB::table('matriculations')

            ->join('users', 'matriculations.user_id', '=', 'users.id')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['matriculations.course_year as year', 'courses.id as course_id'])
            ->where('matriculations.id', $request->id_matriculation)
            ->first();


        $disciplina = DB::table('disciplines')
            ->join('study_plans_has_disciplines as std', 'std.disciplines_id', '=', 'disciplines.id')
            ->join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->select(['dt.display_name', 'disciplines.id', 'disciplines.code'])
            ->where('std.years', $studentInfo->year)
            ->where('disciplines.courses_id', $studentInfo->course_id)
            ->whereNull('disciplines.deleted_by')
            ->get();



        return $disciplina;
    }



    public function GetGradePercurso(Request $request)
    {
        // return $request;



        return  $grade = DB::table('new_old_grades')
            ->select(['grade'])
            ->where('new_old_grades.discipline_id', $request->discipline_id)
            ->where('new_old_grades.user_id', $request->user_id)
            ->get();



        return $grade ?? "NOT";
    }


    public function StoreGradePercurso(Request $request)
    {




        DB::transaction(function () use ($request) {

            $studant = DB::table('matriculations')->where('id', $request->id_matriculation)->first();



            if (!$studant) {
                return false;
            }


            $percurso_existe = DB::table('new_old_grades')
                ->where('user_id', $studant->user_id)
                ->where('discipline_id', $request->discipline_id)
                ->first();

            $old_grade = null;
            if ($percurso_existe) {
                $old_grade = $percurso_existe->grade;
            }


            $data = [
                'id_user' => $studant->user_id,
                'id_discipline' => $request->discipline_id,
                'lective_year_grade' => $request->lective_year,
                'type_pauta' => $request->pauta_type,
                'old_grade' => $old_grade,
                'grade' => $request->grade,
                'created_by' => Auth::user()->id,
                'created_at' => now(),
            ];

            $conditions = [
                'id_user' => $studant->user_id,
                'id_discipline' => $request->discipline_id,
                'lective_year_grade' => $request->lective_year,
            ];

            DB::table('repair_academic_path_avaliation')->updateOrInsert($conditions, $data);

            //Abaixo guarda na tabela de percurso

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->where('id', $request->lective_year)
                ->first();
            $Percursoconditions = [
                'user_id' => $studant->user_id,
                'discipline_id' => $request->discipline_id,
                'lective_year' => $lectiveYears->currentTranslation->display_name,
            ];

            $Percursodata = [
                'user_id' =>  $studant->user_id,
                'discipline_id' => $request->discipline_id,
                'lective_year' => $lectiveYears->currentTranslation->display_name,
                'grade' => $request->grade,
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),

            ];

            // Executar o updateOrInsert
            DB::table('new_old_grades')->updateOrInsert($Percursoconditions, $Percursodata);
        });

        return 1;
    }






    // Este metodo retorna os dados para a tabeta da pagina de reparação de notas
    public function get_student_data(Request $request)
    {



        // if(auth()->user()->id == 12438){



        //     $dados = DB::table("courses_translations as curso")
        //                     ->join("user_courses", "curso.courses_id ", "user_courses.courses_id")
        //                     ->where("repair_academic_path_avaliation as rapa", "rapa.id_user", " .users_id")
        //                     ->get();


        //      dd($dados);


        //     //return response()->json($dados);
        // }





        try {

            $studant = DB::table('matriculations')->where('id', $request->student_id)->first();


            $dados = DB::table("repair_academic_path_avaliation as rapa")
                ->join("disciplines_translations as disTrans", "rapa.id_discipline", "disTrans.discipline_id")
                ->join("users", "rapa.id_user", "users.id")
                ->join("users as userC", function ($join) {
                    $join->on("userC.id", "rapa.created_by");
                })
                ->where("rapa.lective_year_grade", $request->lective_year)
                ->where("rapa.id_user", $studant->user_id)
                ->distinct()
                ->select(
                    "users.name as nome",
                    "rapa.type_pauta as pauta",
                    "disTrans.display_name as discplina",
                    "rapa.old_grade as notaAntiga",
                    "rapa.grade as nota",
                    "rapa.lective_year_grade as ano",
                    "rapa.created_at as created_at",
                    "userC.name as created_by"
                )->get();





            return response()->json($dados);
















            return response()->json($dados);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
