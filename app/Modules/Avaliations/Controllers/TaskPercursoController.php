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
use LDAP\Result;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

use PDF;
use App\Model\Institution;


class TaskPercursoController extends Controller
{

    public function index()
    {
        try {
            $courses = Course::with(['currentTranslation'])->get();

            $funcionario = DB::table('user_parameters as up')
            ->where('up.users_id',auth()->user()->id)
            ->select(['up.value as nome'])
            ->first();
            
            $data = [
                'courses' => $courses,
                'funcionario'=>$funcionario
            ];

            return view("Avaliations::task-percurso.index")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function painel()
    {
        try {
            $courses = Course::with(['currentTranslation'])->get();

            $data = [
                'courses' => $courses
            ];

            return view("Avaliations::task-percurso.painel_restaurar")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function ajax($curso_id)
    {
        try {

            $estado = "erro";

            $disciplina = DB::table('study_plan_editions as spd')
                ->Join('study_plan_edition_disciplines as disc_spde', 'disc_spde.study_plan_edition_id', 'spd.id')
                ->Join('study_plans as stdp', 'stdp.id', 'spd.study_plans_id')
                ->Join('disciplines as disci', 'disc_spde.discipline_id', 'disci.id')

                ->Join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disci.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('stdp.courses_id', $curso_id)
                ->select(['dt.discipline_id as id_disciplina', 'stdp.courses_id as curso', 'spd.lective_years_id as anolectivo', 'spd.course_year as anocurricular', 'disci.code as codigo_disci'])
                ->distinct('dt.display_name')
                ->orderBy('spd.course_year', 'ASC')
                ->orderBy('stdp.code', 'ASC')
                ->first();

            $code = "";

            switch ($curso_id) {
                case 23:

                    $code = $disciplina->codigo_disci[0] . $disciplina->codigo_disci[1];

                    break;

                default:
                    $code = $disciplina->codigo_disci[0] . $disciplina->codigo_disci[1] . $disciplina->codigo_disci[2];
                    break;
            }


            $avaliados = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('new_old_grades as Percurso', 'matricula.user_id', '=', 'Percurso.user_id')
                ->Join('disciplines as disci', 'Percurso.discipline_id', 'disci.id')
                ->Join('user_parameters as up0', function ($join) {
                    $join->on('estudante.id', '=', 'up0.users_id')
                        ->where('up0.parameters_id', 1);
                })
                ->where('curso.courses_id', $curso_id)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select(['estudante.id as codigo', 'estudante.email', 'estudante.name as nome', 'up0.value as nome_completo ', 'Percurso.grade as nota', 'Percurso.discipline_id as disciplina', 'disci.code as code'])
                ->orderBy('estudante.id')
                ->get();

            $percurso = collect($avaliados)->groupBy('codigo')->map(function ($item, $key) use ($curso_id, $code) {

                $nota = ["v" => 0, "qtd" => 0, "disciplina" => 0];

                foreach ($item as $grade) {

                    $code_II = "";


                    // Se o curso tiver apenas duas letras inicias

                    switch ($curso_id) {
                        case 23:
                            $code_II = $grade->code[0] . $grade->code[1];
                            break;
                        default:
                            $code_II = $grade->code[0] . $grade->code[1] . $grade->code[2];
                            break;
                    }

                    switch ($curso_id) {
                        case 25:

                            if (($code_II == $code) || ($code_II == "COA") || ($code_II == "ECO") || ($code_II == "GEE")  ) {

                                
                            }else{

                                // Se a disciplina pertencer a este curso
                                $nota["disciplina"] = $nota["disciplina"] . "," . $grade->disciplina;
                                $nota["qtd"] = $nota["qtd"] + 1;
                            }

                            $code_II = $grade->code[0] . $grade->code[1] . $grade->code[2];

                            break;
                        default:


                            if ($code_II == $code) {

                                // Se a disciplina pertencer a este curso

                                // $nota["v"] = $nota["v"] + 1;
                            } else {

                                // Se a disciplina pertencer a este curso
                                $nota["disciplina"] = $nota["disciplina"] . "," . $grade->disciplina;
                                $nota["qtd"] = $nota["qtd"] + 1;
                            }

                            $code_II = $grade->code[0] . $grade->code[1] . $grade->code[2];
                            break;
                    }
                }

                $nome = "";

                if (isset($item[0]->nome_completo)) {
                    $nome = $item[0]->nome_completo;
                } else {
                    $nome = $item[0]->nome;
                }


                return [
                    "codigo" => $item[0]->codigo,
                    "nome" => $nome,
                    "email" => $item[0]->email,
                    "qtd" => $nota["qtd"],
                    "disciplina" => $nota["disciplina"],

                ];
            });




            $estudantes = array();

            foreach ($percurso as $key => $value) {

                // Pegar a quantidade disciplina que não pertencem no curso

                $d = explode(",", $value["disciplina"]);

                if (count($d) > 1) {

                    array_push($estudantes, $key);
                } else {
                }
            }


            $matriculados = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('user_parameters as up0', function ($join) {
                    $join->on('matricula.user_id', '=', 'up0.users_id')
                        ->where('up0.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up1', function ($join) {
                    $join->on('estudante.id', '=', 'up1.users_id')
                        ->where('up1.parameters_id', 19);
                })
                ->where('curso.courses_id', $curso_id)
                ->whereIn('estudante.id', $estudantes)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select(['estudante.id as codigo', 'estudante.email', 'up0.value as nome', 'up1.value as matricula', 'matricula.id as id_matricula'])
                ->groupBy('estudante.id');

            $restauro = 0;

            return Datatables::queryBuilder($matriculados)
                ->addColumn('actions', function ($item)  use ($percurso, $restauro) {
                    return view("Avaliations::task-percurso.datatables.actions", compact('item', 'percurso', 'restauro'));
                })
                ->addColumn('quantidade', function ($item) use ($percurso) {
                    return view("Avaliations::task-percurso.datatables.quantidade", compact('item', 'percurso'));
                })
                // ->addColumn('states', function ($item) use ($estado) {
                //     return view("Avaliations::task-percurso.datatables.states", compact('item', 'estado'));
                // })
                ->rawColumns(['actions', 'states', 'quantidade'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    
    public function ajax_last($curso_id)
    {
        try {

            $estado = "erro";

            $avaliados = DB::table('users as estudante')
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'estudante.id')
                ->leftJoin('user_parameters as up1', function ($join) {
                    $join->on('estudante.id', '=', 'up1.users_id')
                        ->where('up1.parameters_id', 19);
                })
                ->join('user_courses as curso', 'curso.users_id', '=', 'matricula.user_id')
                ->Join('task_grade as Percurso', 'matricula.user_id', '=', 'Percurso.user_id')
                ->Join('disciplines as disci', 'Percurso.discipline_id', 'disci.id')
                ->Join('user_parameters as up0', function ($join) {
                    $join->on('estudante.id', '=', 'up0.users_id')
                        ->where('up0.parameters_id', 1);
                })
                ->where('curso.courses_id', $curso_id)
                ->whereNull('estudante.deleted_by')
                ->whereNull('matricula.deleted_at')
                ->select([
                    'estudante.id as codigo',
                    'estudante.email',
                    'estudante.name as nome',
                    'up0.value as nome ',
                    'up1.value as matricula',
                    'matricula.id as id_matricula',
                    ])
                ->orderBy('estudante.id')
                ->groupBy('estudante.id');

         

            return Datatables::queryBuilder($avaliados)
                ->addColumn('actions', function ($item) {
                    return view("Avaliations::task-percurso.datatables.actions_recicle", compact('item'));
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
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

            // Listar todos os tipos de eventos

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {


            // Toastr::success(__('Tipo de orçamento criado com sucesso'), __('toastr.success'));
            // return redirect()->route('budget_type.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {


            // Pegar os dados do formulário 

            $estudante = $request->estudante;
            $nome = $request->nome;
            $disciplinas = explode(",", $request->disciplina);

            // Pegar todas as notas que não deveriam estar neste curso

            $notas = DB::table('new_old_grades as Percurso', 'matricula.user_id', '=', 'Percurso.user_id')
                ->whereIn('Percurso.discipline_id', $disciplinas)
                ->where('Percurso.user_id', $estudante)
                ->select(['Percurso.discipline_id', 'Percurso.grade'])
                ->get();

            // Pegar todos os dados destas disciplinas

            $disciplina = DB::table('disciplines as discipline')
                ->Join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'discipline.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->whereIn('discipline.id', $disciplinas)
                ->select(['discipline.id as id_disciplina', 'discipline.code as code_disciplina', 'dt.display_name as nome_disciplina'])
                ->get();



            $data = [
                'estudante' => $nome,
                'codigo' => $estudante,
                'disciplina' => $disciplina,
                'notas' => $notas,
            ];

            return view("Avaliations::task-percurso.task")->with($data);;
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // Métodos para reciclar as notas apagadas no percuso dos estudantes

    public function recicle($id)
    {
        try {


                        

            // Pegar todas as notas que não deveriam estar neste curso

             $disciplina = DB::table('task_grade as Percurso')
                ->where('Percurso.user_id', $id)
                ->join('disciplines as discipline','Percurso.discipline_id',"=","discipline.id")
                ->Join('user_parameters as up1', function ($join) {
                    $join->on('Percurso.deleted_by', '=', 'up1.users_id')
                        ->where('up1.parameters_id', 1);
                })
                ->Join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'discipline.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select(['Percurso.user_id as codigo','Percurso.discipline_id', 'Percurso.grade','discipline.id as id_disciplina', 'discipline.code as code_disciplina', 'dt.display_name as nome_disciplina','up1.value as funcionario'])
                ->get();

            $data = [
                'disciplina' => $disciplina
            ];

            return view("Avaliations::task-percurso.restart")->with($data);;
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function recicles($id)
    {
        try {


                        

            // Pegar todas as notas que não deveriam estar neste curso

             $disciplina = DB::table('task_grade as Percurso')
                ->where('Percurso.user_id', $id)
                ->join('disciplines as discipline','Percurso.discipline_id',"=","discipline.id")
                ->Join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'discipline.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select(['Percurso.user_id as codigo','Percurso.discipline_id', 'Percurso.grade','discipline.id as id_disciplina', 'discipline.code as code_disciplina', 'dt.display_name as nome_disciplina'])
                ->get();

            $data = [
                'disciplina' => $disciplina
            ];

            return view("Avaliations::task-percurso.restart")->with($data);;
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {


           
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::events.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {




            $estudante = $request->estudante;
            $disciplinas = $request->disciplina_check;

            foreach ($disciplinas as $item) {

                $notas = DB::table('new_old_grades as Percurso')
                    ->where('Percurso.user_id', $estudante)
                    ->where('Percurso.discipline_id', $item)
                    ->first();
                   
                $eliminar =  DB::table('task_grade')->updateOrInsert(
                        [
                            'discipline_id' => $item,
                            'user_id' => $notas->user_id,
                        ]
                        ,
                        [
                            'discipline_id' => $item,
                            'user_id' => $notas->user_id,
                            'grade' => $notas->grade,
                            "lective_year" => $notas->lective_year,
                            "updated_at" => $notas->updated_at,
                            "created_at" => $notas->created_at,
                            "tfc_trabalho" => $notas->tfc_trabalho,
                            "tfc_defesa" => $notas->tfc_defesa,
                            "deleted_by" => auth()->user()->id,
                        ]
                    );

                DB::table('new_old_grades')
                    ->where('id',  $notas->id)
                    ->delete();
            }
 

            Toastr::success(__('Eliminado com sucesso'), __('toastr.success'));
            
           return redirect()->route('percurso_task.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget-type.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // Restaurar as notas

    public function restaurar(Request $request)
    {
        try {

            


            $estudante = $request->estudante;
            $disciplinas = $request->disciplina_check;

            foreach ($disciplinas as $item) {

              $notas = DB::table('task_grade as Percurso')
                    ->where('Percurso.user_id', $estudante)
                    ->where('Percurso.discipline_id', $item)
                    ->first();
                   
                $restaurar =  DB::table('new_old_grades')->updateOrInsert(
                        [
                            'discipline_id' => $item,
                            'user_id' => $notas->user_id,
                        ]
                        ,
                        [
                            'discipline_id' => $item,
                            'user_id' => $notas->user_id,
                            'grade' => $notas->grade,
                            "lective_year" => $notas->lective_year,
                            "updated_at" => $notas->updated_at,
                            "created_at" => $notas->created_at,
                            "tfc_trabalho" => $notas->tfc_trabalho,
                            "tfc_defesa" => $notas->tfc_defesa
                        ]
                    );

                    DB::table('task_grade')
                    ->where('id',  $notas->id)
                    ->delete();
            }
 

            Toastr::success(__('Restaurado com sucesso'), __('toastr.success'));
           
            return redirect()->route('percurso_task.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget-type.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
