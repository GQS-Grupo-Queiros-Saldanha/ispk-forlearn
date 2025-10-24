<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\AvaliacaoConfig;
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
use App\Modules\Avaliations\util\PautaGeralAvaliacoesUtil;
use App\Modules\Cms\Controllers\mainController;
use Illuminate\Support\Facades\Log;
use App\Modules\Users\Controllers\MatriculationDisciplineListController;
class PautaGeralAvaliacoesController extends Controller
{

    /**
     * Display a listing of the resource.
     * Controller criadao Por gelson Matias
     * Pauto final 
     * 
     * @return \Illuminate\Http\Response
     */
    private $userMacValue = [];
    private $filterType = "";
    private $turma_id = null;





    public function discipline_grades_seminario($code)
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'code' => $code,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];


            if ($code == 15) {
                return  view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-seminario")->with($data);
            } else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-seminario")->with($data);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function getStudentNotasPautaSeminario($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $tipo_pauta, $pub_print)
    {
        // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);



        // PEGA O LIMITE DE PAGAMENTO DA PROPINA
        $validacao_proprina = DB::table('pauta_avaliation_student_shows')
            ->where('lective_year_id', $id_anoLectivo)
            ->first();

        // dd($validacao_proprina->quantidade_mes);


        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();

        //Estado da Publicação da pauta
        $estado_publicar = DB::table('publicar_pauta')
            ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => $tipo_pauta])
            ->orderBy('id', 'DESC')->first();

        $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
        $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;


        $exame = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('discipline_has_exam as d_exame', 'd_exame.id_plain_study', '=', 'stpeid.id')

            ->where('d_exame.discipline_id', $id_disciplina)
            ->where('dp.id', $id_disciplina)
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->distinct()
            ->first();



        // PUBLICAR
        if (in_array($pub_print, [4, 3])) {
            $avaliacaos_student = $this->getStudentNotasSeminario($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected, $tipo_pauta);
            // return $pub_print;
        } else {
            // IMPRMIR
            $mesActual = date('m') > 9 ? date('m') : date('m')[1];
            $diaActual = date('d');

            if ($validacao_proprina->quantidade_mes > 1) {
                $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
            } else {
                $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
            }

            // return $tipo_pauta;

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
                ->leftJoin('user_parameters as sexo', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
                ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                    $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                    $join->on('matricula_disci.discipline_id', '=', 'dp.id');
                })

                //Verificar os meses pagos.
                ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
                ->leftJoin('articles as art', function ($join) {
                    $join->on('artR.article_id', '=', 'art.id');
                })
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


                ->select([
                    'sexo_value.code as sexo',
                    'avl.nome as Avaliacao_nome',
                    'full_name.value as full_name',
                    'avl_aluno.nota as nota_anluno',
                    'up_meca.value as code_matricula',
                    'avl_aluno.id as Avaliacao_aluno_id',
                    'avl_aluno.id_turma as Avaliacao_aluno_turma',
                    'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                    'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                    'mt.id as Metrica_id',
                    'avl_aluno.users_id as user_id',
                    'dp.id as Disciplia_id',
                    'mt.nome as Metrica_nome',
                    'mt.percentagem as percentagem_metrica',
                    'stpeid.course_year as ano_curricular',
                    'matricula_disci.exam_only as exam_only',
                    'matricula.id as id_mat',
                    'at.display_name as article_name',
                    'artR.status as estado_do_mes',
                    'artR.month as mes',
                    'mt.code_dev as MT_CodeDV',

                ])
                ->where('avl_aluno.id_turma', $Turma_id_Select)
                ->where('stp.courses_id', $id_curso)
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('dp.id', $id_disciplina)
                ->where('matricula_disci.exam_only', 0)
                ->where('code_dev.code', "propina")
                // ->whereIn('mt.code_dev', ["Trabalho"])
                // ->where('artR.month', $mesActual)
                ->whereIn('mt.code_dev', ["TESP"])
                // ->whereIn('mt.nome', ["Classificação"])
                ->whereNull('artR.deleted_at')
                ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
                ->orderBy('mt.id', 'asc')
                ->orderBy('full_name.value', 'asc')
                ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->distinct()
                ->get();
        }


        $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            // ->leftJoin('courses_translations as ct', function ($join) {
            //     $join->on('ct.courses_id', '=', 'crs.id');
            //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            //     $join->on('ct.active', '=', DB::raw(true));
            // })


            ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
            ->leftJoin('discipline_periods as dt', function ($join) {
                $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
            })

            ->select([
                'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                'dt.code as value_disc'
            ])
            ->where('stpeid_discipl.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->orderBy('stpeid_discipl.disciplines_id', 'asc')
            ->distinct()
            ->get();

        $collection = collect($avaliacaos_student);
        $dados = $collection->groupBy('full_name', function ($item) {
            return ($item);
        });

        // PEGA AS PROPINAS DOS ESTUDANTES
        // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

        $date = [

            'ano' => $id_anoLectivo,
            'estado_pauta' => $estado_p,
            'estado_tipo' => $estado_tipo,
            
            'curso' => $id_curso,
            'turma' => $Turma_id_Select,
            'disciplina' => $id_disciplina,
            'periodo_disc' => $discipline_periodo,
            'alunos_notas' => $avaliacaos_student,
            'dados' => $dados,
            'exame' => $exame != null ? $exame : 0,
            'professor' => auth()->user()->name,
            // 'propinas' => $propinas,
            'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
            'validacao_proprina' => $validacao_proprina,
            'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
        ];

        return response()->json(array('data' => $date));
    }



    // TAZER OS ESTUDANTES E SUAS PROPINAS
    private function getStudentNotasSeminario($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected)
    {

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
            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })

            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })

            //Verificar os meses pagos.
            ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
            ->leftJoin('articles as art', function ($join) {
                $join->on('artR.article_id', '=', 'art.id');
            })
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula.id as id_mat',
                'at.display_name as article_name',
                'artR.status as estado_do_mes',
                'artR.month as mes',
                'mt.code_dev as MT_CodeDV',

            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->where('matricula_disci.exam_only', 0)
            // ->where('code_dev.code', "propina")
            // ->where('artR.month', $mesActual)
            ->whereIn('mt.code_dev', ["TESP"])
            // ->whereIn('mt.nome', ["Classificação"])
            ->whereNull('artR.deleted_at')
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            // ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            ->distinct()
            ->get();

        return $avaliacaos_student;
    }


    //  BOLETIM DE NOTAS
    public function discipline_boletimNotas()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
           
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $id_user = auth()->user()->id;

            $student_course = DB::table('user_courses')->where('users_id', $id_user)->get();

            if (auth()->user()->hasRole('student')) {

                // $date = BoletimNotas_Student($id_anoLectivo, $id_curso, $id_user);

                // return $student_course[0]->users_id;
                

                $data = [
                    'lectiveYearSelected' => $lectiveYearSelected,
                    'lectiveYears' => $lectiveYears,
                    'course_st' => $student_course[0]->courses_id,
                    'st_st' => $student_course[0]->users_id
                ];

                return view("Avaliations::avaliacao-aluno.pauta_grades.student_boletim")->with($data);
            } else {
                $data = [
                    'lectiveYearSelected' => $lectiveYearSelected,
                    'lectiveYears' => $lectiveYears,
                    'course_st' => 0,
                    'st_st' => 0
                ];

                return view("Avaliations::avaliacao-aluno.pauta_grades.student_boletim")->with($data);
            }



            // $data=[
            //     'lectiveYearSelected'=>$lectiveYearSelected,
            //     'lectiveYears'=>$lectiveYears
            // ];


            // return view("Avaliations::avaliacao-aluno.pauta_grades.student_boletim")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function getStudent_boletimNotas($id_anoLectivo, $id_curso, $id_user)
    {
        try {


            $payment_state = mainController::get_payments($id_anoLectivo);

            if(isset($payment_state['dividas']["pending"]) && ($payment_state['dividas']["pending"]>0)){
                return json_encode("devendo"); 
            }

           
            $student_matriculation = DB::table('matriculations')
                ->where('user_id', $id_user)
                ->where('lective_year', $id_anoLectivo)
                ->select([
                    'matriculations.id as user_id',
                    'matriculations.lective_year as lective_year',
                ])
                ->get();
            // dd($student_matriculation);
            if (count($student_matriculation) > 0) {
                $student_matriculation = $student_matriculation[0]->user_id;
            } else {
                $student_matriculation = 0;
            }
            $students_grades = BoletimNotas_Student($id_anoLectivo, $id_curso, $student_matriculation);

            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->get();

            $student_propina = DB::table('article_requests')
                ->join('users as student', 'article_requests.user_id', 'student.id')
                ->join('articles as art', function ($join) {
                    $join->on('article_requests.article_id', '=', 'art.id');
                })
                ->join('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')
                ->select([
                    'student.id as student_id',
                    'article_requests.status as estado_do_mes',
                    'article_requests.month as mes',
                    'code_dev.code as emulu_name',
                ])
                ->where('article_requests.status', "total")
                ->where('art.anoLectivo', $id_anoLectivo)
                ->where('article_requests.user_id', $id_user)
                ->where('code_dev.code', "propina")
                ->whereBetween('article_requests.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                ->get()
                ->last();

            $date = [$students_grades, $student_propina];

            return response()->json(array('data' => $date));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getStudentCourse($id_curso, $id_anoLectivo)
    {
        try {

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->where('lective_years.id', $id_anoLectivo)
                ->first();


            $model = User::query()
                ->whereHas('roles', function ($q) {
                    // $q->where('id', '!=', 15);
                    $q->where('id', '=', 6);
                })
                /*->with(['roles' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }])*/
                ->join('users as u1', 'u1.id', '=', 'users.created_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                // ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                // ->leftJoin('states', 'us.state_id', '=', 'states.id')
                // ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                // ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')
                /* ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function($join){
                    $join->on('model_has_roles.role_id', '=', 'roles.id');
                })*/
                //->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])

                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('users.id', '=', 'up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                })
                ->join('matriculations as matricula', 'matricula.user_id', '=', 'full_name.users_id')
                ->join('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matricula.id')
                ->select([
                    'users.*',
                    'full_name.value as nome_student',
                    'up_meca.value as matricula',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'up_bi.value as n_bi',
                    'ct.display_name as course',
                    // 'states.name as state_name',
                    // 'scholarship_entity.company as company'
                    //'roles.name as roles'
                    'mc.matriculation_id as matricula_id',
                    'matricula.created_at as mat_created_at'
                ])
                ->where('uc.courses_id', $id_curso)
                // ->distinct('up_meca.value')

                // ->where('art_requests.deleted_by', null) 
                // ->where('art_requests.deleted_at', null)
                ->groupBy('full_name.value')

                ->distinct('id')

                ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();

            return response()->json(array('data' => $model));

            // return Datatables::eloquent($model)
            //     ->addColumn('actions', function ($item) {
            //         return view('Users::users.datatables.actions')->with('item', $item);
            //     })
            //     ->addColumn('states', function ($item) {
            //         return view('Users::users.datatables.states')->with('item', $item);
            //     })
            //     ->addColumn('scholarship-entity', function($item) {
            //         return view('Users::users.datatables.scholarship-entity')->with('item', $item);
            //     })

            //     // ->addColumn('roles', function ($item) {
            //     //     return $item->roles->map(function ($role) {
            //     //         return $role->currentTranslation->display_name;
            //     //     })->implode(", ");
            //     //     //return $item->roles->first()->currentTranslation->display_name;
            //     // })
            //     ->rawColumns(['actions'])
            //      ->addIndexColumn()
            //     ->toJson();


        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }




    public function discipline_tfc_grades($code)
    {
        // return $code;

        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];

            if ($code == 1) {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-tfc")->with($data);
            } else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-tfc")->with($data);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }









    public function publisher_final_grade_tfc(Request $request){

        try {
            
            //OS ERROS COMEÇAM AQUI
            //Dados de publicar pauta -- pelo coordenandor
            $id_user = Auth::user()->id;
            $id_turma = $request->id_turma;
            $id_disciplina = explode(",", $request->discipline_id);
            $id_anoLectivo = $request->id_anoLectivo;

            $lectiveYearSelected = DB::table('lective_years')
                ->join('lective_year_translations as dt', function ($join) {
                    $join->on('dt.lective_years_id', '=', 'lective_years.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('lective_years.id', $id_anoLectivo)
                ->first();

            //saber o estado  de publicação da pauta<--->

            
            //return $id_disciplina;

            foreach ($id_disciplina as $disciplina) {

                if ($request->pauta_code == "50") {

                    $consulta = DB::table('publicar_pauta')
                        //->join('disciplines as dp', 'dp.id', '=', 'publicar_pauta.id_disciplina')
                        ->where('publicar_pauta.id_disciplina', $disciplina)
                        ->where('publicar_pauta.id_ano_lectivo', $id_anoLectivo)
                        ->where('publicar_pauta.tipo', 50)
                        //->where('dp.courses_id', $id_curso)
                        ->orderBy('publicar_pauta.id', 'DESC')
                        ->get();

                    //return [2823, $consulta[0]->estado];

                } else {

                    $consulta = DB::table('publicar_pauta')
                        ->where(['id_ano_lectivo' => $id_anoLectivo, 'tipo' =>     (int)$request->pauta_code])
                        //->where(['id_ano_lectivo'=>$id_anoLectivo,'id_disciplina' => $disciplina, 'tipo' =>     (int)$request->pauta_code])
                        ->orderBy('publicar_pauta.id', 'DESC')
                        ->get();

                    //return [2328, $consulta[0]->estado];

                }

                if (!$consulta->isEmpty()) {
                    $estado_pauta_Percurso = $consulta[0]->estado;
                } else {
                    //Caso não tenha registro
                    $estado_pauta_Percurso = 0;
                }


                //data actual
                $dataCorrent = carbon::now();
                //VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER GERADA
                $pauta_tipo = "";
                $pauta_tipoE = "";
                // 10 -> Pauta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final


                // ZACARIAS ACRESTOU ESSA LINA
                if ($request->pauta_code == "50") {
                    $pauta_tipo = "Pauta TFC";
                    $pauta_tipoE = "TFC";
                    $tipo_pauta = 50;
                    $recurso = explode("@", $request->pauta_dados);
                    $this->Configurar_transation_tfc($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso, $request->curso_id ?? null);

                    //Estatistica geral sedno
                    //    $dados_estatistico=$this->escala_estatistica($request, $pauta_tipoE ); 
                }


                if (!$consulta->isEmpty()) {

                    $id_Publicação = $consulta[0]->id;
                    $estado = $consulta[0]->estado == 1 ? 0 : 1;
                    $message = $consulta[0]->estado == 1 ? "A Pauta foi desbloqueada com sucesso, com esta acção os docentes podem editar novamente as notas lançadas com base no calendário actual." : "A Pauta foi publicada com sucesso.";
                    
                    $status = DB::table('publicar_pauta')
                        ->where('id', $id_Publicação)
                        ->update(
                            [
                                'estado' => $estado,
                                'id_user_publish' => $id_user,
                                'updated_by' => $id_user
                            ]
                        );
                    
                    // return [2025, $consulta[0]->id];

                    //Gerar PDF da Pauta
                    // $Gerar = $consulta[0]->estado==0 ?  $this->generatePDF_Grades($request,$consulta[0]->id,$dados_estatistico) : "Não gera Pauta" ;
                    $Gerar = $consulta[0]->estado == 0 ?  $this->generatePDF_Grades_tfc($request, $consulta[0]->id, null) : "Não gera Pauta";

                    Toastr::success(__($message), __('toastr.success'));
                    // Toastr::success(__('A pauta foi publicada com sucesso.'), __('toastr.success'));
                    return back();
                } else {
                    //dd($request->pauta_code  );

                    $id = DB::table('publicar_pauta')->insertGetId(
                        [
                            'id_turma' => $id_turma,
                            'id_ano_lectivo' =>  $id_anoLectivo,
                            'id_disciplina' => $disciplina,
                            'created_by' =>  $id_user,
                            'updated_by' =>  $id_user,
                            'created_at' =>  $dataCorrent,
                            'estado' => 1,
                            'pauta_tipo' => $pauta_tipo,
                            'id_user_publish' => $id_user,
                            'tipo' => $tipo_pauta
                        ]
                    );

                    /*
                    dd($id_turma, $id_anoLectivo, $disciplina, $pauta_tipo, $tipo_pauta, $id_user, $dataCorrent);
                    
                    $id = DB::table('publicar_pauta')->updateOrInsert(
                        [
                            'id_turma' => $id_turma,
                            'id_ano_lectivo' =>  $id_anoLectivo,
                            'id_disciplina' => $disciplina,
                            'pauta_tipo' => $pauta_tipo,
                            'tipo' => $tipo_pauta
                        ],
                        [
                            'created_by' =>  $id_user, 
                            'updated_by' =>  $id_user, 
                            'created_at' =>  $dataCorrent, 
                            'estado' => 1, 
                            'id_user_publish' => $id_user
                        ]
                    ); 
                    */


                    $recurso = explode("@", $request->pauta_dados);
                    $this->Configurar_transation_tfc($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);


                    // $this->generatePDF_Grades_tfc($request,$id,$dados_estatistico);
                    $this->generatePDF_Grades_tfc($request, $id, null);
                    Toastr::success(__('A pauta foi publicada com sucesso.'), __('toastr.success'));
                    return back();
                }
            }
        } catch (Exception | Throwable $e) {
            // logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function deleteNullColetion($coletion)
    {
        foreach ($coletion as $key => $value) {
            if ($coletion[$key] == null) {
                unset($coletion[$key]);
            } else {
                foreach ($coletion[$key] as $chave => $valor) {
                    if ($coletion[$key][$chave] == null) {
                        unset($coletion[$key][$chave]);
                    }
                }
            }
        }
    }

    private function Configurar_transation_tfc($dados_Pauta, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso, $id_curso_slc = null)
    {

        $coletion = collect($dados_Pauta)->map(function ($item, $key) {
            if ($item != "") {
                $dados = explode(',', $item);
                return collect($dados)->map(function ($date, $key) {
                    if ($date != "" || $date != null) {
                        return $date;
                    }
                });
            }
        });

        //guardar as notas no percurso acadêmico do aluno

        $this->deleteNullColetion($coletion);

        foreach ($coletion as $recurso) {
            if (isset($recurso)) {
                if (count($recurso) > 6) {
                    foreach ($id_disciplina as $disciplina) {
                        $user_has_discipline = DB::table('matriculation_disciplines')
                            ->where('matriculation_id', $recurso[5])
                            ->where('discipline_id', $disciplina)
                            ->get();
                        if (count($user_has_discipline) > 0) {

                            if (isset($recurso)) {
                                $mt = $recurso[0];
                                $User = $recurso[2];
                                $Nota = $recurso[3];

                                $compara = $User;
                                $compara != null ? $this->actualizar_percurso_tfc($User, $disciplina, $lectiveYearSelected->display_name, $Nota, $estado_pauta_Percurso) : "";
                            } else {
                                $mt = $recurso[0];
                                $User_id = $recurso[2];
                                $Nota = $recurso[3];

                                $compara = $User_id;
                                $compara != null ? $this->actualizar_percurso_tfc($User_id, $disciplina, $lectiveYearSelected->display_name, $Nota, $estado_pauta_Percurso) : "";
                            }
                        }
                    }
                } // para analisar se as notas estão caindo no percurso acad 
                else if (count($recurso) == 5) {
                    foreach ($id_disciplina as $disciplina) {
                        $user_has_discipline = DB::table('matriculation_disciplines')
                            ->where('matriculation_id', $recurso[4] ?? $recurso['5'])
                            ->where('discipline_id', $disciplina)
                            ->get();

                        $findFinalist = count($user_has_discipline) > 0;

                        if (!$findFinalist) {
                            $user = isset($recurso[0]) ? $recurso[1] : $recurso['2'];
                            $bool = !isset($id_curso_slc) ? false : PautaGeralAvaliacoesUtil::userIsFinaLista($user, $id_curso_slc, $lectiveYearSelected->id);
                            if ($bool) {
                                $findFinalist = true;
                            }
                        }

                        if ($findFinalist) {
                            if (isset($recurso[0])) {
                                $mt = $recurso[0];
                                $User = $recurso[1];
                                $Nota = $recurso[2];
                                $compara = $User;
                                $compara!=null? $this->actualizar_percurso_tfc($User,$disciplina,$lectiveYearSelected->display_name,$Nota,$estado_pauta_Percurso):"";
                            } else {
                                $mt = $recurso['1'];
                                $User_id = $recurso['2'];
                                $Nota = $recurso['3'];
                                $compara = $User_id;
                                $compara!=null? $this->actualizar_percurso_tfc($User_id,$disciplina,$lectiveYearSelected->display_name,$Nota,$estado_pauta_Percurso):"";
                            }
                        }

                    }
                }
            }
        }

    }




    private function  actualizar_percurso_tfc($User, $disciplina, $lective_year, $nota, $estado_pauta_Percurso)
    {
        //data actual

        //dd($User, $disciplina, $lective_year, $nota, $estado_pauta_Percurso);
        if ($estado_pauta_Percurso == 0) {
            //Quando o processo é de desbloqueiar a pauta 
            $dataCorrent = carbon::now();

            //Condicao para eliminar a mesma disciplina no percurso >2
            $consulta = DB::table('new_old_grades')
                ->where('user_id', $User)
                ->where('discipline_id', $disciplina)
                ->get();

            if (count($consulta) > 1) {
                $consulta = DB::table('new_old_grades')
                    ->where('user_id', $User)
                    ->where('discipline_id', $disciplina)
                    ->delete();
            }

            //dd($consulta, $User, $disciplina);


            $Percurso = DB::table('new_old_grades')->updateOrInsert(
                [
                    'user_id' => $User,
                    'discipline_id' => $disciplina,
                ],
                [
                    'lective_year' => $lective_year,
                    'grade' => $nota,
                    'created_at' => $dataCorrent,
                    'updated_at' => $dataCorrent,
                    "created_by" => Auth::user()->id
                ]
            );
        } else if ($estado_pauta_Percurso == 1) {

        }
    }



    private function generatePDF_Grades_tfc($request, $id_pauta_publicada, $dados_estatistico)
    {

        //  return [$request->id_turma, $request->id_anoLectivo, $id_pauta_publicada, $request->pauta_code];

        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

        $estatistica = explode(",", $request->pauta_estatistica);

        if ($estatistica == null) {
            $estatistica = ['S/N', 'S/N', 'S/N', 'S/N'];
        }

        //Pegar a turma e o ano Lectivo
        $turna_anoLectivo = DB::table('classes as turma')
            ->join('lective_years as ano', 'ano.id', 'turma.lective_year_id')
            ->leftJoin('lective_year_translations as Lectivo', function ($join) {
                $join->on('Lectivo.lective_years_id', '=', 'ano.id');
                $join->on('Lectivo.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('Lectivo.active', '=', DB::raw(true));
            })
            ->select(['turma.display_name as turma', 'turma.id as id_turma', 'turma.year as Anocurricular', 'Lectivo.display_name as anoLetivo'])
            ->where(['turma.id' => $request->id_turma, 'turma.lective_year_id' => $request->id_anoLectivo])
            ->get();

        // return [$turna_anoLectivo, $request->id_turma, $request->id_anoLectivo];


        // ZAcarias MINHA LINHA
        if ($request->pauta_code == "50") {
            $MetricasCOde_dev = ['Trabalho', 'Defesa'];
        }
        if ($request->pauta_code == "35") {
            $MetricasCOde_dev = ['Exame_especial'];
        }

        //pegar os utilizadores que lançaram as notas 
       $utilizadores = DB::table('avaliacao_alunos as avl')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.updated_by as criado_por', 'mt.nome as metricas', 'u_p9.value as criador_fullname','plano.disciplines_id as disciplina'])

            ->where('avl.id_turma',$request->id_turma)
            ->whereIn('mt.code_dev', $MetricasCOde_dev)
            ->where('plano.disciplines_id',$request->id_disciplina)
            ->distinct('avl.metricas_id')
            ->orderBy('avl.created_at', 'asc')
            ->get();


        //Pegar a disciplina 
        $disciplina = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                $join->on('trans.discipline_id', '=', 'disc.id');
                $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('trans.active', '=', DB::raw(true));
            })

            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where(['disc.id' => $request->discipline_id])
            ->get();
        $nova_disciplina = str_replace($comAcentos, $semAcentos, $disciplina[0]->disciplina);
        //Pegar área , regime e
        $regime = substr($disciplina[0]->codigo, -3, 1);
        $regimeFinal = "";
        if ($regime == "1" || $regime == "2") {
            $regimeFinal = $regime . 'º ' . "Semestre";
        } else if ($regime == "A") {
            $regimeFinal = "Anual";
        }

        //Dados do curso
        $course = DB::table('courses')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['ct.display_name'])
            ->where('courses.id', $request->curso_id)
            ->first();



        //dados da instituição
        $institution = Institution::latest()->first();
        //Logotipo
        $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/attachment/" . $institution->logotipo;
        // $titulo_documento = "Pauta de";
        // $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 101;

        $coordenador_id = DB::table('coordinator_course')
        ->where('coordinator_course.courses_id', $request->curso_id)
        ->whereNotIn('coordinator_course.user_id', [23, 24,734])
        ->first();

        $coordenador_id = $coordenador_id->user_id;

        //Dados do chefe do gabinente
        $gabinete_chefe = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [47]);
        })->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })->first();
        //Coordenador

        $coordenador = DB::table('users')->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })
            ->where('users.id', $coordenador_id)
            ->first();


        $data = [
            'turma' => $request->id_turma, //$turna_anoLectivo[0]->turma,  
            'lectiveYear' => $request->id_anoLectivo, //$turna_anoLectivo[0]->anoLetivo, 
            'discipline_code' => $disciplina[0]->codigo . ' - ' . $disciplina[0]->disciplina,
            'discipline_name' => $disciplina[0]->disciplina,
            'regimeFinal' => $regimeFinal,
            'curso' => $course->display_name,
            'ano_curricular' => 4, //$turna_anoLectivo[0]->Anocurricular,
            'html_table_pauta' => $request->data_html,
            'institution' => $institution,
            'chefe_gabinet' => $gabinete_chefe,
            'coordenador_publicou' => $coordenador->value,
            'logotipo' => $Logotipo_instituicao,
            'utilizadores' => $utilizadores,
            'documentoCode_documento' => $documentoCode_documento,
            'estatistica' => $estatistica,
            'estatistica_tabela' => []
        ];

        $parts = explode('/', $request->id_anoLectivo);
        $fileName = 'Pauta-' . Carbon::now()->format('h:i:s') . '-' . $nova_disciplina . '-' . $request->id_turma . '_' . $parts[0] . '.pdf';

        // VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER GERADA
        // 10 -> PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final

        if ($request->pauta_code == "50") {
            $path = "/storage/pautas-tfc/" . $fileName;
        }

        // MUDA O ESTADO DA PAUTA
        $pauta_pesquisa = DB::table('pauta_path as pauta')
            ->where(['pauta.id_publicar_pauta' => $id_pauta_publicada, 'code' => $request->pauta_code])->orderBy('id', 'DESC')->get();

        if (count($pauta_pesquisa) > 0) {
            $pauta_muda_estado = DB::table('pauta_path as pauta')
                ->where(['pauta.id_publicar_pauta' => $id_pauta_publicada, 'code' => $request->pauta_code])->orderBy('id', 'DESC')->update(['last' => 0]);
        }

        //data actual
        $dataCorrent = carbon::now();
        //Guardar o caminho na tabela 
        DB::table('pauta_path')->insert(
            [
                'id_publicar_pauta' => $id_pauta_publicada,
                'path' => $path,
                'last' => 1,
                'code' => $request->pauta_code,
                'created_at' =>  $dataCorrent
            ]
        );

        //desenhar a tabela no arquivo
        $path_tabela = storage_path('app/public/pautas-final/tabela.blade.php');
        file_put_contents($path_tabela, $request->data_html);

        //fechar o arquivo
        // return view("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);
        //---  ###  ---   ###   ---   ###   ---   ###   ---//
        //---  ###  ---   ###   ---   ###   ---   ###   ---//
        $pdf = PDF::loadView("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);
        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '13mm');
        $pdf->setOption('margin-right', '2mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        $pdf->setPaper('a4');
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        //VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER ARMAZENADA
        //10->PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
        $Pauta_tipo_Not = "";

        // Zacarias MINHA LINHA
        if ($request->pauta_code == "50") {
            $pdf->save(storage_path('app/public/pautas-tfc/' . $fileName));
            $nome = "Pauta_tfc";
            $Pauta_tipo_Not = "TFC";
        }



        //Pegar Professor da turma e disciplina --Publicação será no PDF
        $Professores = DB::table('user_disciplines as professor')
            ->join('user_classes as turma', 'turma.user_id', 'professor.users_id')
            ->join('users as u', 'u.id', 'professor.users_id')
            ->where('turma.class_id', $request->id_turma)
            ->where('professor.disciplines_id', $request->discipline_id)
            ->select(['professor.*', 'u.name'])
            ->get()
            ->map(function ($item) {
                return  [
                    "id" => $item->users_id,
                    "name" => $item->name
                ];
            });

        // turma
        $turma_geral = DB::table('classes')
            ->where('id', $request->id_turma)
            ->first();


        $disciplina_notification = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                $join->on('trans.discipline_id', '=', 'disc.id');
                $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('trans.active', '=', DB::raw(true));
            })
            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where('disc.id', $request->discipline_id)
            ->first();





        //  $icon="fa fa-file-text";
        //  $subject="[".$disciplina_notification->disciplina."-".$turma_geral->display_name."]-Pauta de ".$Pauta_tipo_Not ;
        //  $file=$path;

        //  $discipline_p=$disciplina_notification->disciplina;
        //  $turma_p=$turma_geral->display_name;

        //  $notificar=collect($Professores)->map(function($item) Use($icon,$subject,$file,$discipline_p,$Pauta_tipo_Not,$turma_p){
        //  $body="
        //     <p>Caro(a) professor(a) <b>".$item['name']."</b> a pauta <b>".$Pauta_tipo_Not ."</b> da disciplina <b>".$discipline_p."</b>, referente à turma <b>".$turma_p."</b>, e do qual é docente foi publicada pelo seu coordenador!
        //      <br>
        //      </p>
        //     ";
        //     $destination[]=$item['id'];
        //     notification($icon,$subject,$body,$destination,$file,null);
        //  });



        //Notificar alunos da Pauta
        //     if ($request->pauta_dados){   

        //          $escala1 = $request->pauta_dados;
        //          $escala=explode(',@,',$escala1);

        //          for($i=0;$i<count($escala);$i++){
        //             $without_arroba[$i]=str_replace(",@","",$escala[$i]);
        //             $aluno = explode(',',$without_arroba[$i]);
        //             $userStudant=DB::table('users')->where('id',$aluno[1])->first();

        //             $body="<p>Caro(a) estudante(a), <b>".$userStudant->name."</b>, a Pauta de <b>".$Pauta_tipo_Not."</b>  da disciplina <b>".$discipline_p."</b>, referente à
        //             sua turma <b>".$turma_p."</b>, foi publicada pelo seu coordenador
        //             de curso!</p>";

        //              $destination[]=$aluno[1];
        //              notification($icon,$subject,$body,$destination,null,null);
        //            }

        //    }




        return $pdf->stream($nome . '.pdf');
    }











    // PAUTA DE TRABALHO DE FIM DE CURSO
    public function getStudentGradesTFC($id_anoLectivo, $id_curso, $pub_print)
    {

        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->get();

            $tranf_type = 'payment';
            $currentData = Carbon::now();

            //Estado da Publicação da pauta
            // $estado_publicar=DB::table('publicar_pauta')
            // // ->where(['id_ano_lectivo'=>$id_anoLectivo, 'tipo' => 50])
            // ->where(['id_turma'=>204,'id_ano_lectivo'=>$id_anoLectivo,'id_disciplina' => 395, 'tipo' => 50])
            // ->orderBy('id', 'DESC')->first();

            $disciplinaTFC = PautaGeralAvaliacoesUtil::study($id_curso,$id_anoLectivo);

            $estado_publicar = DB::table('publicar_pauta')
                ->join('disciplines as dp', 'dp.id', '=', 'publicar_pauta.id_disciplina')
                // ->where(['id_turma'=>204,'id_ano_lectivo'=>$id_anoLectivo,'id_disciplina' => 395, 'tipo' => 50])
                ->where('publicar_pauta.id_ano_lectivo', $id_anoLectivo)
                ->where('publicar_pauta.tipo', 50)
                ->where('dp.courses_id', $id_curso)
                ->where('dp.id',$disciplinaTFC->discipline_id)
                ->orderBy('publicar_pauta.id', 'DESC')
                ->get();

            if (count($estado_publicar) != 0) {
                $estado_p = $estado_publicar != "" ? $estado_publicar[0]->estado : Null;
                $estado_tipo = $estado_publicar != "" ? $estado_publicar[0]->tipo : Null;
            } else {
                $estado_p = "";
                $estado_tipo = "";
            }


            //$curso_anoCurricular = $this->getCursoAno($id_curso);

            if ($pub_print == 1) {

                $avaliacao = ["Trabalho", "Defesa"];

                $TrabalhoNormal = Matriculation::join('user_courses as user_course', 'user_course.users_id', '=', 'matriculations.user_id')
                    ->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id', '=', 'matriculations.user_id')
                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })

                    ->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'plano_estudo_avaliacao.avaliacaos_id')

                    ->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })

                    ->join('user_parameters as full_name', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->join('article_requests as artR', 'artR.user_id', 'avl_aluno.users_id')
                    ->join('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                    })
                    ->join('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.created_at as avl_aluno_creadted_at',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',
                        'matriculations.id as mat_id',
                        'at.display_name as article_name',
                        'artR.status as estado_do_mes',
                        'artR.month as mes',
                        'code_dev.code as emulu_name',
                        'dp.courses_id as dp_coursesId',
                        'matriculation_discipline.discipline_id as dt_discipId',
                        'avaliacao.anoLectivo as avaliacao_anoLectivo'
                    ])

                    ->where('user_course.courses_id', $id_curso)
                    ->where('dp.courses_id', $id_curso)
                    ->where('matriculations.lective_year', $id_anoLectivo)
                    ->where('art.anoLectivo', $id_anoLectivo)
                    ->where('avaliacao.anoLectivo', $id_anoLectivo)
                    ->where('mt.code_dev', "Trabalho")
                    ->whereIn('code_dev.code', ["confirm", "p_matricula"])
                    ->where('artR.status', "total")
                    // ->whereBetween('matriculations.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    //->where('matriculations.lective_year', $lectiveYearSelected[0]->id)
                    ->whereBetween('art.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->orderBy('full_name.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();

                //FINALISTA FORA DE EPOCA
                $TrabalhoFinalista = DB::table('matriculation_finalist as matricula_finalist')

                    //->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id','=','matricula_finalist.user_id')
                    ->join('avaliacao_alunos as avl_aluno', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'matricula_finalist.user_id')
                            //->where('avl_aluno.id','=', 173891);
                            ->latest();
                    })

                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('matriculations as matriculation', function ($join) {
                        $join->on('matriculation.user_id', '=', 'matricula_finalist.user_id')
                            ->latest();
                    })

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', 'plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id')
                    ->join('disciplines as dp', function ($join) {
                        $join->on('dp.courses_id', '=', 'matricula_finalist.id_curso');
                        $join->on('dp.id', '=', 'plano_estudo_avaliacao.disciplines_id');
                    })

                    /*
                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })
                    */

                    //->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'plano_estudo_avaliacao.avaliacaos_id')

                    //->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    /*
                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculation.id');
                        $join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })
                    */


                    ->join('user_parameters as full_name', function ($q) {
                        $q->on('full_name.users_id', '=', 'matricula_finalist.user_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',

                        'matriculation.id as mat_id',
                        'dp.id as dt_discipId',
                        //'dp.courses_id as dp_coursesId',
                        //'matriculation_discipline.discipline_id as dt_discipId',
                        //'avaliacao.id as avaliacao_id',

                        // 'matricula_finalist.id as id_finalista',
                        'matricula_finalist.user_id as user_id',
                        // 'matricula_finalist.num_confirmaMatricula',
                        // 'avl_aluno.nota as avlAluno_nota',
                        // 'avl_aluno.id as id_avaliacao',
                        // 'name_full.value as name_student'
                        //'avl_aluno.*'
                    ])
                    ->where('matricula_finalist.year_lectivo', $id_anoLectivo)
                    ->where('matricula_finalist.id_curso', $id_curso)
                    //->where('dp.courses_id', $id_curso)
                    ->whereNull('matricula_finalist.deleted_at')
                    ->whereNull('avl_aluno.deleted_at')
                    ->where('mt.code_dev', "Trabalho")
                    //->where('avaliacao.anoLectivo', '=', $lectiveYearSelected[0]->id)
                    //->whereBetween('avl_aluno.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    //->where('mt.id', '=', 93)
                    ->orderBy('full_name.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();


                $Trabalho = array_merge((array)json_decode($TrabalhoNormal), (array) json_decode($TrabalhoFinalista));


                $DefesaNormal = Matriculation::join('user_courses as user_course', 'user_course.users_id', '=', 'matriculations.user_id')
                    ->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id', '=', 'matriculations.user_id')
                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })

                    ->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'plano_estudo_avaliacao.avaliacaos_id')

                    ->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })

                    ->join('user_parameters as full_name', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->join('article_requests as artR', 'artR.user_id', 'avl_aluno.users_id')
                    ->join('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                    })
                    ->join('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',
                        'matriculations.id as mat_id',
                        'at.display_name as article_name',
                        'artR.status as estado_do_mes',
                        'artR.month as mes',
                        'code_dev.code as emulu_name',
                        'dp.courses_id as dp_coursesId',
                        'matriculation_discipline.discipline_id as dt_discipId',
                        'avaliacao.anoLectivo as avaliacao_anoLectivo'
                    ])

                    ->where('user_course.courses_id', $id_curso)
                    ->where('dp.courses_id', $id_curso)
                    ->where('matriculations.lective_year', $id_anoLectivo)
                    ->where('art.anoLectivo', $id_anoLectivo)
                    ->where('avaliacao.anoLectivo', $id_anoLectivo)
                    ->where('mt.code_dev', "Defesa")
                    ->whereIn('code_dev.code', ["confirm", "p_matricula"])
                    ->where('artR.status', "total")
                    // ->whereBetween('matriculations.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    // ->where('avaliacao.anoLectivo', '=', $lectiveYearSelected[0]->id)
                    ->where('matriculations.lective_year', $lectiveYearSelected[0]->id)
                    ->whereBetween('art.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->orderBy('full_name.value', 'ASC')                    // ->orderBy('up_meca.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();

                //FINALISTA FORA DE EPOCA
                $DefesaFinalista = DB::table('matriculation_finalist as matricula_finalist')

                    ->join('avaliacao_alunos as avl_aluno', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'matricula_finalist.user_id')
                            ->latest();
                    })
                    //->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id','=','matricula_finalist.user_id')
                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('matriculations as matriculation', function ($join) {
                        $join->on('matriculation.user_id', '=', 'matricula_finalist.user_id')
                            ->latest();
                    })

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', 'plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id')
                    ->join('disciplines as dp', function ($join) {
                        $join->on('dp.courses_id', '=', 'matricula_finalist.id_curso');
                        $join->on('dp.id', '=', 'plano_estudo_avaliacao.disciplines_id');
                    })

                    /*
                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })
                    */

                    //->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'plano_estudo_avaliacao.avaliacaos_id')

                    //->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculation.id');
                        //$join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })


                    ->join('user_parameters as full_name', function ($q) {
                        $q->on('full_name.users_id', '=', 'matricula_finalist.user_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',
                        'matriculation.id as mat_id',
                        //'dp.courses_id as dp_coursesId',
                        //'matriculation_discipline.discipline_id as dt_discipId',
                        'dp.id as dt_discipId',

                        // 'matricula_finalist.id as id_finalista',
                        // 'matricula_finalist.user_id as user_id',
                        // 'matricula_finalist.num_confirmaMatricula',
                        // 'avl_aluno.nota as avlAluno_nota',
                        // 'avl_aluno.id as id_avaliacao',
                        // 'name_full.value as name_student'
                    ])
                    // ->distinct('matricula_finalist.id')
                    ->where('matricula_finalist.year_lectivo', $id_anoLectivo)
                    ->where('matricula_finalist.id_curso', $id_curso)
                    //->where('dp.courses_id', $id_curso)
                    ->whereNull('matricula_finalist.deleted_at')
                    ->whereNull('avl_aluno.deleted_at')
                    ->where('mt.code_dev', "Defesa")
                    //->where('avaliacao.anoLectivo', '=', $lectiveYearSelected[0]->id)
                    //->whereBetween('avl_aluno.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    //->where('mt.id', '=', 94)
                    ->orderBy('full_name.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();

             
                $Defesa = array_merge((array)json_decode($DefesaNormal), (array) json_decode($DefesaFinalista));


                $avaliacaos = collect($Trabalho)->merge($Defesa);

                $collection = collect($avaliacaos);
                $dados = $collection->groupBy('fullName_value', function ($item) {
                    return ($item);
                });
            } else {
                
                $mesActual = date('m') > 9 ? date('m') : date('m')[1];
                $diaActual = date('d');

                $validacao_proprina = DB::table('pauta_avaliation_student_shows')
                    ->where('lective_year_id', $id_anoLectivo)
                    ->first();

                // return $validacao_proprina;

                if ($validacao_proprina->quantidade_mes > 1) {
                    $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
                } else {
                    $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
                }

                $avaliacao = ["Trabalho", "Defesa"];

                // return $lectiveYearSelected;

                $Trabalho = Matriculation::join('user_courses as user_course', 'user_course.users_id', '=', 'matriculations.user_id')
                    ->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id', '=', 'matriculations.user_id')
                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })

                    ->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })

                    ->join('user_parameters as full_name', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->join('article_requests as artR', 'artR.user_id', 'avl_aluno.users_id')
                    ->join('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                    })
                    ->join('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',
                        'matriculations.id as mat_id',
                        'at.display_name as article_name',
                        'artR.status as estado_do_mes',
                        'artR.month as mes',
                        'code_dev.code as emulu_name',
                        'dp.courses_id as dp_coursesId',
                        'matriculation_discipline.discipline_id as dt_discipId',
                    ])

                    ->where('user_course.courses_id', $id_curso)
                    ->where('dp.courses_id', $id_curso)
                    ->where('matriculations.lective_year', $id_anoLectivo)
                    ->where('art.anoLectivo', $id_anoLectivo)
                    // ->whereIn('mt.code_dev', $avaliacao) 
                    ->where('mt.code_dev', "Trabalho")
                    ->whereIn('code_dev.code', ["confirm", "p_matricula"])
                    // ->where('artR.status', "total")
                    // ->where('artR.month', $mesActual)
                    // ->where('code_dev.code', "propina")

                    // ->where('matriculations.course_year', 4)
                    // ->whereBetween('matriculations.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->where('matriculations.lective_year', $lectiveYearSelected[0]->id)
                    ->whereBetween('art.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->orderBy('full_name.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();

                // return count($Trabalho);

                $Defesa = Matriculation::join('user_courses as user_course', 'user_course.users_id', '=', 'matriculations.user_id')
                    ->join('avaliacao_alunos as avl_aluno', 'avl_aluno.users_id', '=', 'matriculations.user_id')
                    ->join('metricas as mt', 'mt.id', '=', 'avl_aluno.metricas_id')

                    ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                        $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                        $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    })

                    ->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'plano_estudo_avaliacao.avaliacaos_id')

                    ->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('matriculation_disciplines as matriculation_discipline', function ($join) {
                        $join->on('matriculation_discipline.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculation_discipline.discipline_id', '=', 'dp.id');
                    })


                    // ->join('plano_estudo_avaliacaos as plano_estudo_avaliacao', function ($join) {
                    //     $join->on('plano_estudo_avaliacao.id', '=', 'avl_aluno.plano_estudo_avaliacaos_id');
                    //     $join->on('plano_estudo_avaliacao.avaliacaos_id', '=', 'mt.avaliacaos_id');
                    // })

                    //->join('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacao.disciplines_id')

                    ->join('user_parameters as full_name', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })
                    ->join('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->join('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                    ->join('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->join('article_requests as artR', 'artR.user_id', 'avl_aluno.users_id')
                    ->join('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                    })
                    ->join('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')

                    ->select([
                        'mt.id as mt_id',
                        'mt.nome as mt_nome',
                        'mt.percentagem as mt_percentagem',
                        'mt.code_dev as mt_code_dev',
                        'avl_aluno.id_turma as avaliacao_aluno_turma',
                        'avl_aluno.id as avlAluno_id',
                        'avl_aluno.plano_estudo_avaliacaos_id as avlAluno_peAvlId',
                        'avl_aluno.metricas_id as avlAluno_metricasId',
                        'avl_aluno.users_id as avlAluno_usersId',
                        'avl_aluno.nota as avlAluno_nota',
                        'avl_aluno.presence as avlAluno_presence',
                        'full_name.id as fullName_Id',
                        'full_name.users_id as fullName_usersId',
                        'full_name.value as fullName_value',
                        'sexo_value.code as sexo',
                        'up_meca.value as up_meca_matriculaId',
                        'matriculations.id as mat_id',
                        'at.display_name as article_name',
                        'artR.status as estado_do_mes',
                        'artR.month as mes',
                        'code_dev.code as emulu_name',
                        'dp.courses_id as dp_coursesId',
                        'matriculation_discipline.discipline_id as dt_discipId',
                    ])

                    ->where('user_course.courses_id', $id_curso)
                    ->where('dp.courses_id', $id_curso)
                    ->where('matriculations.lective_year', $id_anoLectivo)
                    ->where('art.anoLectivo', $id_anoLectivo)
                    // ->whereIn('mt.code_dev', $avaliacao) 
                    ->where('mt.code_dev', "Defesa")
                    ->whereIn('code_dev.code', ["confirm", "p_matricula"])

                    // ->where('artR.status', "total")
                    // ->where('artR.month', $mesActual)
                    // ->where('code_dev.code', "propina")

                    // ->where('matriculations.course_year', 4)
                    ->whereBetween('art.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->orderBy('full_name.value', 'ASC')
                    ->groupBy('up_meca.value')
                    ->distinct()
                    ->get();
               
                // return count($avaliacaos);


                $avaliacaos = collect($Trabalho)->merge($Defesa);

                $collection = collect($avaliacaos);
                $dados = $collection->groupBy('fullName_value', function ($item) {
                    return ($item);
                });
            }

            $date = [
                'avaliacoes' => $avaliacaos,
                'dados' => $dados,
                'estado_pauta' => $estado_p,
                'estado_tipo' => $estado_tipo,
                
                'Trabalho' => count($Trabalho),
                'Defesa' => count($Defesa),
                'disciplina' => $disciplinaTFC,
                'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
            ];

            return response()->json(array('data' => $date));
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    // PEGA OS ANO CURRICULARES DO CURO
    private function getCursoAno($curso)
    {
        try {

            // // $tranf_type='payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join) {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');
                    $join->on('matriculations.course_year', '=', 'cl.year');
                })

                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('article_requests as art_requests', function ($join) {
                    $join->on('art_requests.user_id', '=',   'u0.id')
                        ->whereIn('art_requests.article_id', [117, 79]);
                })
                ->select([
                    'matriculations.course_year as course_year'
                    // 'cl.display_name as classe'
                ])
                ->where('art_requests.deleted_by', null)
                ->where('art_requests.deleted_at', null)
                ->where('uc.courses_id', $curso)

                ->groupBy('u_p.value')
                ->distinct('id')

                ->orderBy('matriculations.course_year', 'DESC')
                ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->first();
            // ->get();

            return $model->course_year;

            // return response()->json(array('data'=>$model));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    /* PAUTA DISCPLINA COM NOTAS */
    public function discipline_grades_st()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];

            return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-final")->with($data);
            // view("Avaliations::avaliacao-aluno.discipline_studentes_grades")->with($data);
            #return view("Avaliations::avaliacao-aluno.show_pauta_final")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function discipline_grades_mac($code)
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'code' => $code,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];


            if ($code == 15) {
                return  view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-mac")->with($data);
            } else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-mac")->with($data);
            }
            // if ($code == 0) {
            //     return view("Avaliations::avaliacao-aluno.discipline_studentes_grades_mac")->with($data, $code);
            // }
            // else {
            //     return view("Avaliations::avaliacao-aluno.discipline_studentes_grades_mac")->with($data, $code);
            // }
            #return view("Avaliations::avaliacao-aluno.show_pauta_final")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function discipline_exame_especial_grades($code){
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'code' => $code,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];


            if ($code == 35) {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-exame-especial")->with($data);
                // view("Avaliations::avaliacao-aluno.discipline_studentes_exame")->with($data);
            } else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-exame-especial")->with($data);
                // view("Avaliations::avaliacao-aluno.pauta_grades.docente.pauta_docente_exame")->with($data);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function discipline_exame_extraordinario_grades(){
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 9;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];

                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-extraordinario")->with($data);
                
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function getStudentNotasExameEspecial($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected)
    {
        // 
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

            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })

            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })

            //Verificar os meses pagos.
            ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
            ->leftJoin('articles as art', function ($join) {
                $join->on('artR.article_id', '=', 'art.id');
            })
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            // ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')



            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula.id as id_mat',
                'at.display_name as article_name',
                'artR.status as estado_do_mes',
                'artR.month as mes',
                'mt.code_dev as MT_CodeDV',
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            // ->where('matricula_disci.exam_only',0)  
            // ->where('code_dev.code', "propina")
            // ->where('artR.month', $mesActual)         
            ->where('mt.code_dev', "Exame_especial")
            // ->where('avl_aluno.plano_estudo_avaliacaos_id',1565)
            ->whereNull('artR.deleted_at')
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->where('matricula.lective_year', $lectiveYearSelected->id)
            ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->distinct()
            ->get();
    }




    
    public function getStudentGradesExameEspecial($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $pub_print){

        // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);
        // return 1;

        try {

            $validacao_proprina = DB::table('pauta_avaliation_student_shows')
                ->where('lective_year_id', $id_anoLectivo)
                ->first();


            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $id_anoLectivo)
                ->first();

            //Estado da Publicação da pauta
            $estado_publicar = DB::table('publicar_pauta')
                ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => 35])
                ->orderBy('id', 'DESC')->first();
         
            $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
            $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;

 
            $mesActual = date('m') > 9 ? date('m') : date('m')[1];
            $diaActual = date('d');
            if ($validacao_proprina->quantidade_mes > 1) {
                $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
            } else {
                $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
            }



            if (in_array($pub_print, [12])) {
                // return $pub_print;
                $avaliacaos_student = $this->getStudentNotasExameEspecial($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected);
                // return $avaliacaos_student;
            } else {
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

                    ->leftJoin('user_parameters as sexo', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                            ->where('sexo.parameters_id', 2);
                    })
                    ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })

                    ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
                    ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                        $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                        $join->on('matricula_disci.discipline_id', '=', 'dp.id');
                    })

                    //Verificar os meses pagos.
                    ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
                    ->leftJoin('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                    })
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    // ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')



                    ->select([
                        'sexo_value.code as sexo',
                        'avl.nome as Avaliacao_nome',
                        'full_name.value as full_name',
                        'avl_aluno.nota as nota_anluno',
                        'up_meca.value as code_matricula',
                        'avl_aluno.id as Avaliacao_aluno_id',
                        'avl_aluno.id_turma as Avaliacao_aluno_turma',
                        'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                        'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                        'mt.id as Metrica_id',
                        'avl_aluno.users_id as user_id',
                        'dp.id as Disciplia_id',
                        'mt.nome as Metrica_nome',
                        'mt.percentagem as percentagem_metrica',
                        'stpeid.course_year as ano_curricular',
                        'matricula_disci.exam_only as exam_only',
                        'matricula.id as id_mat',
                        'at.display_name as article_name',
                        'artR.status as estado_do_mes',
                        'artR.month as mes',
                        'mt.code_dev as MT_CodeDV',
                    ])
                    ->where('avl_aluno.id_turma', $Turma_id_Select)
                    ->where('stp.courses_id', $id_curso)
                    ->where('stpeid.lective_years_id', $id_anoLectivo)
                    ->where('dp.id', $id_disciplina)
                    // ->where('matricula_disci.exam_only',0)  
                    // ->where('code_dev.code', "propina")
                    // ->where('artR.month', $mesActual)         
                    ->where('mt.code_dev', "Exame_especial")
                    // ->where('avl_aluno.plano_estudo_avaliacaos_id',1565)
                    ->whereNull('artR.deleted_at')
                    ->orderBy('mt.id', 'asc')
                    ->orderBy('full_name.value', 'asc')
                    ->where('matricula.lective_year', $lectiveYearSelected->id)
                    //->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
                    ->distinct()
                    ->get();
            }


            $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
                // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
                // ->leftJoin('courses_translations as ct', function ($join) {
                //     $join->on('ct.courses_id', '=', 'crs.id');
                //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                //     $join->on('ct.active', '=', DB::raw(true));
                // })


                ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
                ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
                ->leftJoin('discipline_periods as dt', function ($join) {
                    $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
                })

                ->select([
                    'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                    'dt.code as value_disc'
                ])
                ->where('stpeid_discipl.disciplines_id', $id_disciplina)
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('dp.id', $id_disciplina)
                ->orderBy('stpeid_discipl.disciplines_id', 'asc')
                ->distinct()
                ->get();

            $collection = collect($avaliacaos_student);
            $dados = $collection->groupBy('full_name', function ($item) {
                return ($item);
            });

            // PEGA AS PROPINAS DOS ESTUDANTES
            // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

            $date = [

                'ano' => $id_anoLectivo,
                'estado_pauta' => $estado_p,
                'estado_tipo' => $estado_tipo,
                
                'curso' => $id_curso,
                'turma' => $Turma_id_Select,
                'disciplina' => $id_disciplina,
                'periodo_disc' => $discipline_periodo,
                'alunos_notas' => $avaliacaos_student,
                'dados' => $dados,
                // 'exame' => $exame!=null?$exame:0,
                'professor' => auth()->user()->name,
                // 'propinas' => $propinas,
                'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
                'validacao_proprina' => $validacao_proprina,
                'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
            ];

            return response()->json(array('data' => $date));
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


 // PAUTA DE RECURSO
 public function getStudentGradesExameExtraordinario($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina){

     // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);
     // return 1;

     try {

         $validacao_proprina = DB::table('pauta_avaliation_student_shows')
             ->where('lective_year_id', $id_anoLectivo)
             ->first();


         $lectiveYearSelected = DB::table('lective_years')
             ->where('id', $id_anoLectivo)
             ->first();

         //Estado da Publicação da pauta
         $estado_publicar = DB::table('publicar_pauta')
             ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => 45])
             ->orderBy('id', 'DESC')->first();
      
         $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
         $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;


         $mesActual = date('m') > 9 ? date('m') : date('m')[1];
         $diaActual = date('d');
         if ($validacao_proprina->quantidade_mes > 1) {
             $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
         } else {
             $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
         }



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

         ->leftJoin('user_parameters as sexo', function ($join) {
             $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                 ->where('sexo.parameters_id', 2);
         })
         ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

         ->leftJoin('user_parameters as up_meca', function ($join) {
             $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                 ->where('up_meca.parameters_id', 19);
         })

         ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
         ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
             $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
             $join->on('matricula_disci.discipline_id', '=', 'dp.id');
         })

         //Verificar os meses pagos.
         ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
         ->leftJoin('articles as art', function ($join) {
             $join->on('artR.article_id', '=', 'art.id');
         })
         ->leftJoin('article_translations as at', function ($join) {
             $join->on('art.id', '=', 'at.article_id');
             $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
             $join->on('at.active', '=', DB::raw(true));
         })
         // ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')



         ->select([
             'sexo_value.code as sexo',
             'avl.nome as Avaliacao_nome',
             'full_name.value as full_name',
             'avl_aluno.nota as nota_anluno',
             'up_meca.value as code_matricula',
             'avl_aluno.id as Avaliacao_aluno_id',
             'avl_aluno.id_turma as Avaliacao_aluno_turma',
             'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
             'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
             'mt.id as Metrica_id',
             'avl_aluno.users_id as user_id',
             'dp.id as Disciplia_id',
             'mt.nome as Metrica_nome',
             'mt.percentagem as percentagem_metrica',
             'stpeid.course_year as ano_curricular',
             'matricula_disci.exam_only as exam_only',
             'matricula.id as id_mat',
             'at.display_name as article_name',
             'artR.status as estado_do_mes',
             'artR.month as mes',
             'mt.code_dev as MT_CodeDV',
         ])
         ->where('avl_aluno.id_turma', $Turma_id_Select)
         ->where('stp.courses_id', $id_curso)
         ->where('stpeid.lective_years_id', $id_anoLectivo)
         ->where('dp.id', $id_disciplina)
         // ->where('matricula_disci.exam_only',0)  
         // ->where('code_dev.code', "propina")
         // ->where('artR.month', $mesActual)         
         ->where('mt.code_dev', "Extraordinario")
         // ->where('avl_aluno.plano_estudo_avaliacaos_id',1565)
         ->whereNull('artR.deleted_at')
         ->orderBy('mt.id', 'asc')
         ->orderBy('full_name.value', 'asc')
         ->where('matricula.lective_year', $lectiveYearSelected->id)
         ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
         ->distinct()
         ->get();


         $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
             ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
             // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
             // ->leftJoin('courses_translations as ct', function ($join) {
             //     $join->on('ct.courses_id', '=', 'crs.id');
             //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
             //     $join->on('ct.active', '=', DB::raw(true));
             // })


             ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
             ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
             ->leftJoin('discipline_periods as dt', function ($join) {
                 $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
             })

             ->select([
                 'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                 'dt.code as value_disc'
             ])
             ->where('stpeid_discipl.disciplines_id', $id_disciplina)
             ->where('stpeid.lective_years_id', $id_anoLectivo)
             ->where('dp.id', $id_disciplina)
             ->orderBy('stpeid_discipl.disciplines_id', 'asc')
             ->distinct()
             ->get();

         $collection = collect($avaliacaos_student);
         $dados = $collection->groupBy('full_name', function ($item) {
             return ($item);
         });

         // PEGA AS PROPINAS DOS ESTUDANTES
         // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

         $date = [

             'ano' => $id_anoLectivo,
             'estado_pauta' => $estado_p,
             'estado_tipo' => $estado_tipo,
             
             'curso' => $id_curso,
             'turma' => $Turma_id_Select,
             'disciplina' => $id_disciplina,
             'periodo_disc' => $discipline_periodo,
             'alunos_notas' => $avaliacaos_student,
             'dados' => $dados,
             // 'exame' => $exame!=null?$exame:0,
             'professor' => auth()->user()->name,
             // 'propinas' => $propinas,
             'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
             'validacao_proprina' => $validacao_proprina,
             'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
         ];

         return response()->json(array('data' => $date));
     } catch (Exception | Throwable $e) {
         return $e;
         Log::error($e);
         return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
     }
 }





















    public function discipline_grades_coordenador()
    {


        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];

            return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-final")->with($data);
            // view("Avaliations::avaliacao-aluno.discipline_studentes_coordenador")->with($data);
            #return view("Avaliations::avaliacao-aluno.show_pauta_final")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


























    public function discipline_exame_grades($code)
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'code' => $code,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];


            if ($code == 25) {
                
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-exame")->with($data);
                // view("Avaliations::avaliacao-aluno.discipline_studentes_exame")->with($data);
            }
            else if($code==26) {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-exame-oral")->with($data);

            }
            else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-exame")->with($data);
                // view("Avaliations::avaliacao-aluno.pauta_grades.docente.pauta_docente_exame")->with($data);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }














    public function discipline_recurso_grades($code)
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'whoIs'=> auth()->user()->hasAnyRole(['coordenador-curso']) ? "coordenador" : "teacher",
            ];

            if ($code == 1) {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_publicar.publicar-pauta-recurso")->with($data);
            } else {
                return view("Avaliations::avaliacao-aluno.pauta_grades.pauta_impressao.imprimir-pauta-recurso")->with($data);
            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    private function caseInArray($usermac, $trueOrFalse){
        $split = explode("-",$usermac);
        $user_id = $split[0];
        $mac = $split[1];
        $this->userMacValue[$user_id] = $mac;
    }


    private function filterUserMac($usersMac = [],$lective, $greatDispense = "mac"){
        if(sizeof($usersMac) == 0) return;

        $this->userMacValue = [];
        $this->filterType = $greatDispense;
        $avaliacaoConfig = $this->avaliacaoConfig($lective);
        $note_dispensa = $avaliacaoConfig->mac_nota_dispensa ?? 14;
        
        foreach($usersMac as $usermac){
          $split = explode("-",$usermac);
          $user_id = $split[0];
          $mac = $split[1];
          
          if($mac >= $note_dispensa && $this->filterType == "mac") 
             $this->userMacValue[$user_id] = $mac;
          
          if( ( $mac >= $avaliacaoConfig->exame_nota_inicial &&  $mac <= $avaliacaoConfig->exame_nota_final ) && $this->filterType == "exame") {
            $this->userMacValue[$user_id] = $mac;
          }
              
        }
        
    }
    
    
    private function verifyPautaPublicado($disciplina, array $code, $not_analise = false){
        return $not_analise ? true : DB::table('publicar_pauta as pauta')
                ->where('pauta.id_disciplina',$disciplina)
                ->where('pauta.estado',1)
                ->whereIn('pauta.tipo',$code)
                ->exists();
    }




    public function publisher_final_grade(Request $request){

        try {
            //$request->pauta_dados;
            //Dados de publicar pauta -- pelo coordenandor
            $id_user = Auth::user()->id;
            $id_disciplina = $request->id_disciplina;
            $id_anoLectivo = $request->id_anoLectivo;
            $this->turma_id = $id_turma = $request->id_turma;
             
            $lectiveYearSelected = DB::table('lective_years')
                ->join('lective_year_translations as dt', function ($join) {
                    $join->on('dt.lective_years_id', '=', 'lective_years.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('lective_years.id', $id_anoLectivo)
                ->first();


            //saber o estado  de publicação da pauta<--->
            $consulta = DB::table('publicar_pauta')
                ->where(['id_turma' => $id_turma, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' =>     $request->pauta_code])
                ->get();

            if (!$consulta->isEmpty()) {
                $estado_pauta_Percurso = $consulta[0]->estado;
            } else {
                //Caso não tenha registro
                $estado_pauta_Percurso = 0;
            }
            $all_dispensa = isset($request->x_d);
            $is_not_publish = $all_dispensa ? true : $estado_pauta_Percurso != 1;
            //data actual
            $dataCorrent = carbon::now();
            // VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER GERADA
            $pauta_tipo = "";
            $pauta_tipoE = ""; 
            
            // 10 -> Pauta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
            if ($request->pauta_code == "10") {
                $pauta_tipo = "Pauta de Recurso";
                $pauta_tipoE = "Recurso";
                $tipo_pauta = 10;
                //$estado_pauta_Percurso
                
                if(!$this->verifyPautaPublicado($id_disciplina ,[30],$all_dispensa) && $is_not_publish){
                    Toastr::warning(__('A forLEARN detectou que falta realizar o processo de publicação do MAC+EXAME'), __('toastr.warning'));
                    return back();
                }                 
                
                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);
                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            }

            if ($request->pauta_code == "20") {
                $pauta_tipo = "Pauta de Exame Escrito";
                $pauta_tipoE = "Escrito";
                $tipo_pauta = 20;
                
                if(!$this->verifyPautaPublicado($id_disciplina ,[40],$all_dispensa) && $is_not_publish){
                    Toastr::warning(__('A forLEARN detectou que falta realizar o processo de publicação da pauta de frequência'), __('toastr.warning'));
                    return back();
                }              
                
                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);
                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
                //$this->Exame_oral($request);
            }
            if ($request->pauta_code == "25") {
                $pauta_tipo = "Pauta de Exame Oral";
                $pauta_tipoE = "Oral";
                $tipo_pauta = 25;
                
                if(!$this->verifyPautaPublicado($id_disciplina ,[20],$all_dispensa) && $is_not_publish){
                    Toastr::warning(__('A forLEARN detectou que falta realizar o processo de publicação da pauta Exame Escrito'), __('toastr.warning'));
                    return back();
                }                
                
                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);
                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            }

            if ($request->pauta_code == "30") {
                // return "<center><h1>A publicação de classificação final encontra-se em manutenção !! <br> Comunicaremos quando tudo estiver pronto. <br></h1></center>";
                $pauta_tipo = "Pauta Final";
                $pauta_tipoE = "Classificação final";
                $tipo_pauta = 30;
                
                // if(!$this->verifyPautaPublicado($id_disciplina ,[20],$all_dispensa) && $is_not_publish){
                //     Toastr::warning(__('A forLEARN detectou que falta realizar o processo de publicação da pauta de Exame Escrito'), __('toastr.warning'));
                //     return back();
                // }    
                /*
                if(!$this->verifyPautaPublicado($id_disciplina ,[25],$all_dispensa) && $is_not_publish){
                    Toastr::warning(__('A forLEARN detectou que falta realizar o processo de publicação da pauta de Exame Oral'), __('toastr.warning'));
                    return back();
                }                   
                */
                $recurso = explode("@", $request->pauta_dados);

                $this->filterUserMac($request->usersMac ?? [], $id_anoLectivo);

                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);

                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            
            }

            if ($request->pauta_code == "35") {
                $pauta_tipo = "Pauta Exame Especial";
                $pauta_tipoE = "Especial";
                $tipo_pauta = 35;

                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);

                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            }

            if ($request->pauta_code == "40") {
                $pauta_tipo = "Pauta Frequência";
                $pauta_tipoE = "MAC";
                $tipo_pauta = 40;
                $dados_estatistico = [];

                $MetricasCOde_dev = [0 =>'PF1',1 =>'PF2',2 => 'OA'];

                $exists = [];
                $falses = null;
                for($i = 0;$i<3;$i++){
                    $exists[$i] = DB::table('avaliacao_alunos as avl')
                    ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
                    ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
                    ->where('avl.id_turma',$request->id_turma)
                    ->where('mt.code_dev', $MetricasCOde_dev[$i])
                    ->where('plano.disciplines_id',$request->id_disciplina)
                    ->exists();

                    if(!$exists[$i])
                $falses = $falses  . $MetricasCOde_dev[$i] . ',';


                }

               
                $exist = $exists[0] && $exists[1] && $exists[2];
               
               
                
              
                if(!$exist){
                    Toastr::warning(__('A forLEARN detectou que falta lançar as notas de:' . $falses), __('toastr.warning'));
                    return back();
                }   

                
                $recurso=explode("@",$request->pauta_dados);
                
                $this->filterUserMac($request->usersMac ?? [], $id_anoLectivo, "exame");
                //dd($this->userMacValue, $this->filterType );
                $this->Configurar_transation($recurso,$id_disciplina,$lectiveYearSelected,$estado_pauta_Percurso);
                
                //Estatistica geral
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
               
            }

            if ($request->pauta_code == "45") {
                $pauta_tipo = "Pauta Exame Extraordinário";
                $pauta_tipoE = "Extraordinário";
                $tipo_pauta = 45;

                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);

                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            }

            if ($request->pauta_code == "60") {

                $pauta_tipo = "Pauta Seminário";
                $pauta_tipoE = "Seminario";
                $tipo_pauta = 60;
                $dados_estatistico = [];
                $recurso = explode("@", $request->pauta_dados);
                $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);

                //Estatistica geral 
                $dados_estatistico = $this->escala_estatistica($request, $pauta_tipoE);
            }




            if (!$consulta->isEmpty()) {

                if($consulta[0]->estado == 1){
                    if ($request->pauta_code == "40"){
                        $next = 30;
                        $name = 'Classificação Final';
                        $actual = 'MAC';
                    }

                    if ($request->pauta_code == "30"){
                        $next = 10;
                        $name = 'Recurso';
                        $actual = 'Classificação Final';
                    }
                    if ($request->pauta_code == "10"){
                        $next = 35;
                        $name = 'Exame Especial';
                        $actual = 'Recurso';
                    }
                    if ($request->pauta_code == "35"){
                        $next = 45;
                        $name = 'Exame Extraordinário';
                        $actual = 'Exame Especial';
                    }
                    if ($request->pauta_code == "60"){
                       /*Caso exolado para a pauta final*/
                       $id_Publicação = $consulta[0]->id;
                        $message = $consulta[0]->estado == 1 ? "A Pauta foi desbloqueada com sucesso, com esta acção os docentes podem editar novamente as notas lançadas com base no calendário actual." : "A Pauta foi publicada com sucesso.";

                        DB::table('publicar_pauta')
                            ->where('id', $id_Publicação)
                            ->update(
                                [
                                    'estado' => 0,
                                    'id_user_publish' => $id_user,
                                    'updated_by' => $id_user
                                ]
                            );
                        
                        //Gerar PDF da Pauta
                        //$Gerar = $this->generatePDF_Grades($request, $consulta[0]->id, $dados_estatistico);
                        Toastr::success(__($message), __('toastr.success'));
                        return back();
                    }

                  if($request->pauta_code != "45"){
                    $verify_next =  DB::table('publicar_pauta')
                    ->where(['id_turma' => $id_turma, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => $next])
                    ->get();

                    if(!$verify_next->isEmpty() && $verify_next[0]->estado == 1){
                      
                            $text = 'A forLEARN detectou que a pauta de '.$name.' já se encontra publicada! Não sendo mais possível desbloquear esta pauta de '.$actual;
                            Toastr::warning(__($text), __('toastr.warning'));
                            return back();
                    }
                 }
                }

                $id_Publicação = $consulta[0]->id;
                $estado = $consulta[0]->estado == 1 ? 0 : 1;
                $message = $consulta[0]->estado == 1 ? "A Pauta foi desbloqueada com sucesso, com esta acção os docentes podem editar novamente as notas lançadas com base no calendário actual." : "A Pauta foi publicada com sucesso.";

                DB::table('publicar_pauta')
                    ->where('id', $id_Publicação)
                    ->update(
                        [
                            'estado' => $estado,
                            'id_user_publish' => $id_user,
                            'updated_by' => $id_user
                        ]
                    );
                //Gerar PDF da Pauta
                $Gerar = $consulta[0]->estado == 0 ?  $this->generatePDF_Grades($request, $consulta[0]->id, $dados_estatistico) : "Não gera Pauta";
                Toastr::success(__($message), __('toastr.success'));
                return back();
            } else {

                $id = DB::table('publicar_pauta')->insertGetId(
                    [
                        'id_turma' => $id_turma,
                        'id_ano_lectivo' =>  $id_anoLectivo,
                        'id_disciplina' => $id_disciplina,
                        'created_by' =>  $id_user,
                        'updated_by' =>  $id_user,
                        'created_at' =>  $dataCorrent,
                        'estado' => 1,
                        'pauta_tipo' => $pauta_tipo,
                        'id_user_publish' => $id_user,
                        'tipo' => $tipo_pauta
                    ]
                );


                if ($tipo_pauta != 40) {
                    //só entrar aqui quando não for Mac a ser publicada
                    $recurso = explode("@", $request->pauta_dados);
                    $this->Configurar_transation($recurso, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso);
                }
                $this->generatePDF_Grades($request, $id, $dados_estatistico);
                Toastr::success(__('A pauta foi publicada com sucesso.'), __('toastr.success'));
                return back();
            }
        } catch (Exception | Throwable $e) {
            dd($e->getMessage());
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    private function sexoUser($user_id){
        $user = DB::table('user_parameters as up')
        ->join('parameter_options as po','up.value','po.id')
        ->select('up.value','po.code')
        ->where([
            "up.users_id" => $user_id,
            "up.parameters_id" => 2
        ])->first();
        return $user;
    }

    private function clearArray($alunos){
        $array = [];
        foreach($alunos as $aluno){
            if($aluno != "" && $aluno != null){
                $array[] = $aluno;
            }
        }
        return $array;
    }

    private function Exame_oral($request){
        $config=$this->avaliacaoConfig($request->id_anoLectivo);
        $id_turma =explode(',',$request->id_turma);
        $dataCorrent = carbon::now();
        $data = str_replace(',@', '@', $request->pauta_dados);
        $objetos = explode('@', $data);
        // Remover vírgula inicial dos objetos de 1 a 7
        for ($i = 1; $i <= count($objetos); $i++) {
            if (isset($objetos[$i])) {
                $objetos[$i] = ltrim($objetos[$i], ',');
            }
        }
        // Remover último índice vazio
        array_pop($objetos);
        foreach ($objetos as $item) {
            $studant = explode(',', $item);
            if($studant[2]>=$config->exame_nota_inicial && $studant[2]<=$config->exame_oral_final){
             $exame_oral_cadastro = DB::table('tb_exame_oral_student')
              ->updateOrInsert(
                [
                    'matriculation_id' => $studant[0],
                    'discipline_id' => $request->id_disciplina,
                    'id_lectiveYear' => $request->id_anoLectivo,
                    'id_turma' => $id_turma[0],
                ],

                [
                    'nota_exame_escrito' => $studant[2],
                    'descricao' => "Estudante terá que fazer a Exame Oral",
                    'created_by' => Auth::user()->id,
                    'created_at' => $dataCorrent,
                    'updated_by' => Auth::user()->id,        
                ]

               );
             }
           }
        
    }

    private function escala_estatistica($request, $pauta_tipo)
    {

        $escala1 = $request->pauta_dados;
        $escala = explode(',@,', $escala1);
        $resumo = explode(',', $request->pauta_estatistica);
        $nota_aluno = 0;
        $sexo_aluno = "";
        $count = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
        $count_sexo_F = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
        $count_sexo_M = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
        $escala_result = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
      
        for ($i = 0; $i < count($escala); $i++) {
            $without_arroba[$i] = str_replace(",@", "", $escala[$i]);
            $aluno = $this->clearArray(explode(',', $without_arroba[$i]));
            $tam = sizeof($aluno);
            if($tam == 4){
                $nota_aluno = $aluno[2];
                $sexo_aluno = substr($aluno[3], 0, 1);
            }
            if($tam == 3){
                $obj = $this->sexoUser($aluno[1]);
                if(isset($obj->value)){
                    $nota_aluno = $aluno[2];
                    $sexo_aluno = substr($obj->code, 0, 1);
                }
            }
            
            //Escala dos reporvados Processamento
            if ($nota_aluno >= 0 && $nota_aluno < 7) {
                $count["first"] = $count['first'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["first"] = $count_sexo_M['first'] + 1
                    : $count_sexo_F["first"] = $count_sexo_F['first'] + 1;
            }
            if ($nota_aluno > 6 && $nota_aluno < 10) {
                $count["second"] = $count['second'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["second"] = $count_sexo_M['second'] + 1
                    : $count_sexo_F["second"] = $count_sexo_F['second'] + 1;
            }

            //Escala dos aprovados Processamento
            if ($nota_aluno > 9 && $nota_aluno < 14) {
                $count["thirst"] = $count['thirst'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["thirst"] = $count_sexo_M['thirst'] + 1
                    : $count_sexo_F["thirst"] = $count_sexo_F['thirst'] + 1;
            }

            if ($nota_aluno > 13 && $nota_aluno < 17) {
                $count["fourth"] = $count['fourth'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["fourth"] = $count_sexo_M['fourth'] + 1
                    : $count_sexo_F["fourth"] = $count_sexo_F['fourth'] + 1;
            }

            if ($nota_aluno > 16 && $nota_aluno < 20) {
                $count["fiveth"] = $count['fiveth'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["fiveth"] = $count_sexo_M['fiveth'] + 1
                    : $count_sexo_F["fiveth"] = $count_sexo_F['fiveth'] + 1;
            }
            if ($nota_aluno == 20) {
                $count["sixth"] = $count['sixth'] + 1;
                //validade sexo
                $sexo_aluno == "M" ?
                    $count_sexo_M["sixth"] = $count_sexo_M['sixth'] + 1
                    : $count_sexo_F["sixth"] = $count_sexo_F['sixth'] + 1;
            }
        }
       
        foreach ($escala_result as $key => $escala_item) {
            $escala_result[$key] = [
                "M" => $count_sexo_M[$key],
                "Percent_M" =>  $count[$key] != 0 ? (int) round(($count_sexo_M[$key] / $count[$key]) * 100, 0) : 0,
                "F" => $count_sexo_F[$key],
                "Percent_F" => $count[$key] != 0 ? (int)  round(($count_sexo_F[$key] / $count[$key]) * 100, 0) : 0,
                "T" => $count[$key],
                'Escala' => $key
            ];

            //Guardar os dados das estatítica
            $estatistica_cadastro = DB::table('tb_estatistic_avaliation')
                ->updateOrInsert(
                    [
                        'id_course' => $request->curso_id,
                        'id_class' => $request->id_turma,
                        'scale' => $key,
                        'id_lective_year' => $request->id_anoLectivo,
                        'id_discipline' => $request->id_disciplina,
                        'pautaType' => $request->pauta_code,
                    ],
                    [
                        'masculine' => $count_sexo_M[$key],
                        'feminine' =>  $count_sexo_F[$key],
                        'total' => $count[$key],
                        'percent_masculine' => $count[$key] != 0 ? (int) round(($count_sexo_M[$key] / $count[$key]) * 100, 0) : 0,
                        'percent_feminine' => $count[$key] != 0 ? (int)  round(($count_sexo_F[$key] / $count[$key]) * 100, 0) : 0,
                        'descrition_type_p' =>  $pauta_tipo
                    ]

                );
        }

        //Estatística geral
        $estatistica_geral = [
            "total" => $resumo[0],
            "aprovados" => $resumo[1],
            "reprovados" => $resumo[2],
            "aprovados_femenino" => $resumo[3],
            "aprovados_masculino" => $resumo[4],
            "reprovados_femenino" => $resumo[5],
            "reprovados_masculino" => $resumo[6],
        ];

        $m = $estatistica_geral['aprovados_masculino'] + $estatistica_geral['reprovados_masculino'];
        $f = $estatistica_geral['aprovados_femenino'] + $estatistica_geral['reprovados_femenino'];
        $estatistica_total_avaliado = DB::table('tb_estatistic_avaliation')
            ->updateOrInsert(
                [
                    'id_course' => $request->curso_id,
                    'id_class' => $request->id_turma,
                    'scale' => "total",
                    'id_lective_year' => $request->id_anoLectivo,
                    'id_discipline' => $request->id_disciplina,
                    'pautaType' => $request->pauta_code,
                ],
                [
                    'masculine' => $m,
                    'feminine' =>  $f,
                    'total' => $estatistica_geral['total'],
                    'percent_masculine' => $estatistica_geral['total'] != 0 ? (int) round(($m / $estatistica_geral['total']) * 100, 0) : 0,
                    'percent_feminine' => $estatistica_geral['total'] != 0 ? (int)  round(($f / $estatistica_geral['total']) * 100, 0) : 0,
                    'descrition_type_p' =>  $pauta_tipo
                ]

            );



        $dados = [
            "total" => $estatistica_geral,
            "escala" => $escala_result
        ];

        return $dados;
    }




    //Mambos
    private function Configurar_transation($dados_Pauta, $id_disciplina, $lectiveYearSelected, $estado_pauta_Percurso)
    {
        $coletion = collect($dados_Pauta)->map(function ($item, $key) {
            if ($item != "") {
                $dados = explode(',', $item);
                return collect($dados)->map(function ($date, $key) {
                    if ($date != "" || $date != null) {
                        return $date;
                    }
                });
            }
        });
    
        $this->deleteNullColetion($coletion);
        
        //guardar as notas no percurso acadêmico do aluno
        foreach ($coletion as $recurso) {
            if (isset($recurso[0])) {
                $mt = $recurso[0];
                $User = $recurso[1];
                $Nota = $recurso[2];

                $compara = $User;
                $compara != null ? $this->actualizar_percurso($User, $id_disciplina, $lectiveYearSelected->display_name, $Nota, $estado_pauta_Percurso) : "";
            } else if(isset($recurso[1])){
                $mt = $recurso[1];
                $User_id = $recurso[2];
                $Nota = $recurso[3];
                $compara = $User_id;
                $compara != null ? $this->actualizar_percurso($User_id, $id_disciplina, $lectiveYearSelected->display_name, $Nota, $estado_pauta_Percurso) : "";
            }else{
                if(isset($recurso[2])){
                    $mt = $recurso[2];
                    $User_id = $recurso[3];
                    $Nota = $recurso[4];
                    $compara = $User_id;
                    $compara != null ? $this->actualizar_percurso($User_id, $id_disciplina, $lectiveYearSelected->display_name, $Nota, $estado_pauta_Percurso) : "";
                }
            }
        }
    }


    private function exame_escrito_studen($User, $disciplina, $lective_year, $nota){
        if(isset($this->turma_id)){
            $dataCorrent = Carbon::now();
            $userCorrent = auth()->user()->id ?? 1;
            DB::table('tb_exame_escrito_student')->updateOrInsert([
                'user_id' => $User, 'discipline_id' => $disciplina, 'id_turma' => $this->turma_id
            ],[
                'id_lectiveYear' => $lective_year,
                'nota_exame_mac' => $nota,
                'descricao' => 'exame',
                'created_at' => $dataCorrent,
                'updated_at' => $dataCorrent,
                'created_by' => $userCorrent,
                'updated_by' => $userCorrent,
            ]);
        }
    }


    private function actualizar_percurso($User, $disciplina, $lective_year, $nota, $estado_pauta_Percurso)
    {
        //data actual
        
        if ($estado_pauta_Percurso == 0) {
            //Quando o processo é de desbloqueiar a pauta 
            $dataCorrent = carbon::now();

            //Condicao para eliminar a mesma disciplina no percurso >2
            $consulta = DB::table('new_old_grades')
                ->where('user_id', $User)
                ->where('discipline_id', $disciplina)
                ->get();

            if (count($consulta) > 1) {
                $consulta = DB::table('new_old_grades')
                    ->where('user_id', $User)
                    ->where('discipline_id', $disciplina)
                    ->delete();
            }
            
            if($this->filterType == "exame" && isset($this->userMacValue[$User]) ){
                $this->exame_escrito_studen($User, $disciplina, $lective_year, $this->userMacValue[$User]);
            }
            

            $Percurso = DB::table('new_old_grades')->updateOrInsert(
                [
                    'user_id' => $User,
                    'discipline_id' => $disciplina,
                ],
                [
                    'lective_year' => $lective_year,
                    'grade' => $nota,
                    'created_at' => $dataCorrent,
                    'updated_at' => $dataCorrent

                ]

            );
        } else if ($estado_pauta_Percurso == 1) {

        }
    }



    public function imprimirPDF_Grades(Request $request)
    {
        try {


            // if(Auth::user()->id!=4428){
            //     return "Esta página está em manutenção";
            // }



            // Não é possivel imprimir a pauta devido a estatistica, por está razão o retorno
            // Toastr::error(__('Não é possivél imprimir pauta, por favor contactar o apoio a forLEARN.'), __('toastr.error'));
            // return back();


            // return [$request->id_turma, $request->id_anoLectivo, $id_pauta_publicada, $request->pauta_code];
            $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
            $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

            $estatistica = explode(",", $request->pauta_estatistica);
            if ($estatistica == null) {
                $estatistica = ['S/N', 'S/N', 'S/N', 'S/N'];
            }

            //Pegar a turma e o ano Lectivo
            $turna_anoLectivo = DB::table('classes as turma')
                ->join('lective_years as ano', 'ano.id', 'turma.lective_year_id')
                ->leftJoin('lective_year_translations as Lectivo', function ($join) {
                    $join->on('Lectivo.lective_years_id', '=', 'ano.id');
                    $join->on('Lectivo.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('Lectivo.active', '=', DB::raw(true));
                })
                ->select(['turma.display_name as turma', 'turma.id as id_turma', 'turma.year as Anocurricular', 'Lectivo.display_name as anoLetivo'])
                //->where(['turma.id' => $request->id_turma, 'turma.lective_year_id' => $request->id_anoLectivo])
                ->where(['turma.id' => $request->id_turma])
                ->get();

                $coordenador_id = DB::table('coordinator_course')
                ->where('coordinator_course.courses_id', $request->curso_id)
                ->whereNotIn('coordinator_course.user_id', [23, 24,734])
                ->first();
        
                $coordenador_id = $coordenador_id->user_id;

            $coordenador = DB::table('users')->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
                ->where('users.id', $coordenador_id)
                ->first();



            if ($request->pauta_code == "10") {
                $MetricasCOde_dev = ['Recurso'];
            }
            if ($request->pauta_code == "20") {
                $MetricasCOde_dev = ['Neen'];
            }
            if ($request->pauta_code == "25") {
                $MetricasCOde_dev = ['oral'];
            }
            if ($request->pauta_code == "30") {
                $MetricasCOde_dev = ['PF1', 'PF2', 'OA', 'Neen','Oral'];
            }
            if ($request->pauta_code == "40") {
                $MetricasCOde_dev = ['PF1', 'PF2', 'OA'];
            }
            // ZACARIAS MINHA LINHA
            if ($request->pauta_code == "60") {
                $MetricasCOde_dev = ['Trabalho'];
            }
            if ($request->pauta_code == "35") {
                $MetricasCOde_dev = ['Exame_especial'];
            }
            if ($request->pauta_code == "45") {
                $MetricasCOde_dev = ['Extraordinario'];
            }
            if ($request->pauta_code == "50") {
                $MetricasCOde_dev = ['Trabalho_de_fim_curso'];
            }
            
            //return $request; 
            
            //pegar os utilizadores que lançaram as notas 
             $utilizadores = DB::table('avaliacao_alunos as avl')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.updated_by as criado_por', 'mt.nome as metricas', 'u_p9.value as criador_fullname','plano.disciplines_id as disciplina'])

            ->where('avl.id_turma',$request->id_turma)
            ->whereIn('mt.code_dev', $MetricasCOde_dev)
            ->where('plano.disciplines_id',$request->id_disciplina)
            ->distinct('avl.metricas_id')
            ->orderBy('avl.created_at', 'asc')
            ->get();


            //return [$request->id_anoLectivo, $request->curso_id];

            $estado_publicar = DB::table('publicar_pauta')
                ->join('disciplines as dp', 'dp.id', '=', 'publicar_pauta.id_disciplina')
                //->join('classes as c','dp.courses_id','=','c.courses_id')
                // ->where(['id_turma'=>204,'id_ano_lectivo'=>$id_anoLectivo,'id_disciplina' => 395, 'tipo' => 50])
                ->where('publicar_pauta.id_ano_lectivo', $request->id_anoLectivo)
                ->where('publicar_pauta.tipo', $request->pauta_code)
                ->where('dp.courses_id', $request->curso_id)
                //->where('c.id',$request->turma_id)
                ->orderBy('publicar_pauta.id', 'DESC')
                ->get();

            //Pegar a disciplina 
            $disciplina = DB::table('disciplines as disc')
                ->leftJoin('disciplines_translations as trans', function ($join) {
                    $join->on('trans.discipline_id', '=', 'disc.id');
                    $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('trans.active', '=', DB::raw(true));
                })
                ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
                ->where(['disc.id' => $request->id_disciplina])
                //  ->where(['disc.id'=> $estado_publicar[0]->id_disciplina])
                ->get();
            
            $nova_disciplina = str_replace($comAcentos, $semAcentos, $disciplina[0]->disciplina);
            //Pegar área , regime e
            $regime = substr($disciplina[0]->codigo, -3, 1);
            $regimeFinal = "";
            if ($regime == "1" || $regime == "2") {
                $regimeFinal = $regime . 'º ' . "Semestre";
            } else if ($regime == "A") {
                $regimeFinal = "Anual";
            }

            //Dados do curso
            $course = DB::table('courses')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select(['ct.display_name'])
                ->where('courses.id', $request->curso_id)
                ->first();
            //Chefe do gabinete
            $gabinete_chefe = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [47]);
            })->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('users.id', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })->first();

            //dados da instituição
            $institution = Institution::latest()->first();
            // $titulo_documento = "Pauta de";
            $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
            // $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 10;
           
            $data = [
                'turma' => $turna_anoLectivo[0]->turma,
                'lectiveYear' => $turna_anoLectivo[0]->anoLetivo,
                'discipline_code' => $disciplina[0]->codigo . ' - ' . $disciplina[0]->disciplina,
                'discipline_name' => $disciplina[0]->disciplina,
                'regimeFinal' => $regimeFinal,
                'curso' => $course->display_name,
                'ano_curricular' => $turna_anoLectivo[0]->Anocurricular,
                'html_table_pauta' => $request->data_html,
                'institution' => $institution,
                'logotipo' => $Logotipo_instituicao,
                'chefe_gabinet' => $gabinete_chefe,
                'utilizadores' => $utilizadores,
                'coordenador_publicou' => $coordenador->value,
                'documentoCode_documento' => $documentoCode_documento,
                'estatistica' => $estatistica,
                'estatistica_tabela' => []
            ];

            $parts = explode('/', $turna_anoLectivo[0]->anoLetivo);
            $fileName = 'Pauta-' . Carbon::now()->format('h:i:s') . '-' . $nova_disciplina . '-' . $turna_anoLectivo[0]->turma . '_' . $parts[0] . '.pdf';

            // VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER GERADA
            // 10 -> PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
            //    if ($request->pauta_code == "10") {
            //         $path="/storage/pautas-recurso/".$fileName;
            //     }
            //     if ($request->pauta_code == "20") {
            //         $path="/storage/pautas-exame/".$fileName;
            //     }
            //     if ($request->pauta_code == "30") {
            //         $path="/storage/pautas-final/".$fileName;
            //     }
            //     if ($request->pauta_code == "40") {
            //         $path="/storage/pautas-frequencia/".$fileName;
            //     }
            //Guardar o caminho na tabela 
            // DB::table('pauta_path')->insert(
            //     ['id_publicar_pauta' => $id_pauta_publicada,
            //      'path' => $path
            //     ]
            //   );

            //desenhar a tabela no arquivo
            $path_tabela = storage_path('app/public/pautas-final/tabela.blade.php');
            file_put_contents($path_tabela, $request->data_html);
            //fechar o arquivo

            //    return view("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);

            $pdf = PDF::loadView("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);
            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);

            $pdf->setPaper('a4');
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
            // VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER ARMAZENADA
            // 10 -> PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
            // if ($request->pauta_code == "10") {
            //     $pdf->save(storage_path('app/public/pautas-recurso/' . $fileName));
            // }
            // if ($request->pauta_code == "20") {
            //     $pdf->save(storage_path('app/public/pautas-exame/' . $fileName));
            // }
            // if ($request->pauta_code == "30") {
            //     $pdf->save(storage_path('app/public/pautas-final/' . $fileName));
            // }
            // if ($request->pauta_code == "40") {
            //     $pdf->save(storage_path('app/public/pautas-frequencia/' . $fileName));
            // }
            return $pdf->stream('Pauta Final' . '.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            //logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    private function generatePDF_Grades($request, $id_pauta_publicada, $dados_estatistico)
    
    {
        try{
            
        // return [$request->id_turma, $request->id_anoLectivo, $id_pauta_publicada, $request->pauta_code];

        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
      
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

        $estatistica = explode(",", $request->pauta_estatistica);
 
        if ($estatistica == null) {
            $estatistica = ['S/N', 'S/N', 'S/N', 'S/N'];
        }
        //Pegar a turma e o ano Lectivo
        $turna_anoLectivo = DB::table('classes as turma')
            ->join('lective_years as ano', 'ano.id', 'turma.lective_year_id')
            ->leftJoin('lective_year_translations as Lectivo', function ($join) {
                $join->on('Lectivo.lective_years_id', '=', 'ano.id');
                $join->on('Lectivo.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('Lectivo.active', '=', DB::raw(true));
            })
            ->select(['turma.display_name as turma', 'turma.id as id_turma', 'turma.year as Anocurricular', 'Lectivo.display_name as anoLetivo'])
            ->where(['turma.id' => $request->id_turma, 'turma.lective_year_id' => $request->id_anoLectivo])
            ->get();




        if ($request->pauta_code == "10") {
            $MetricasCOde_dev = ['Recurso'];
        }
        if ($request->pauta_code == "20") {
            $MetricasCOde_dev = ['Neen'];
        }
        if ($request->pauta_code == "25") {
            $MetricasCOde_dev = ['oral'];
        }        
        if ($request->pauta_code == "30") {
            $MetricasCOde_dev = ['PF1', 'PF2', 'OA', 'Neen','Oral'];
        }
        if ($request->pauta_code == "40") {
            $MetricasCOde_dev = ['PF1', 'PF2', 'OA'];
        }
        if ($request->pauta_code == "50") {
            $MetricasCOde_dev = ['Trabalho', 'Defesa'];
        }
        if ($request->pauta_code == "60") {
            $MetricasCOde_dev = ['TESP'];
        }
        if ($request->pauta_code == "35") {
            $MetricasCOde_dev = ['Exame_especial'];
        }
        if ($request->pauta_code == "45") {
            $MetricasCOde_dev = ['Extraordinario'];
        }

        //pegar os utilizadores que lançaram as notas 
        $utilizadores = DB::table('avaliacao_alunos as avl')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                    ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select(['avl.updated_by as criado_por', 'mt.nome as metricas', 'u_p9.value as criador_fullname','plano.disciplines_id as disciplina'])

            ->where('avl.id_turma',$request->id_turma)
            ->whereIn('mt.code_dev', $MetricasCOde_dev)
            ->where('plano.disciplines_id',$request->id_disciplina)
            ->distinct('avl.metricas_id')
            ->orderBy('avl.created_at', 'asc')
            ->get();


        //Pegar a disciplina 
        $disciplina = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                $join->on('trans.discipline_id', '=', 'disc.id');
                $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('trans.active', '=', DB::raw(true));
            })

            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where(['disc.id' => $request->id_disciplina])
            ->get();
        $nova_disciplina = str_replace($comAcentos, $semAcentos, $disciplina[0]->disciplina);
        //Pegar área , regime e
        $regime = substr($disciplina[0]->codigo, -3, 1);
        $regimeFinal = "";
        if ($regime == "1" || $regime == "2") {
            $regimeFinal = $regime . 'º ' . "Semestre";
        } else if ($regime == "A") {
            $regimeFinal = "Anual";
        }

        //Dados do curso
       $course = DB::table('courses')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select(['ct.display_name'])
            ->where('courses.id', $request->curso_id)
            ->first();



        //dados da instituição
        $institution = Institution::latest()->first();
        //Logotipo
        $Logotipo_instituicao = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
        // $titulo_documento = "Pauta de";
        // $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 10;

       
        $coordenador_id = DB::table('coordinator_course')
                ->where('coordinator_course.courses_id', $request->curso_id)
                ->whereNotIn('coordinator_course.user_id', [23, 24,734])
                ->first();
        
        $coordenador_id = $coordenador_id->user_id;

        //Dados do chefe do gabinente
        $gabinete_chefe = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [47]);
        })->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })->first();
        //Coordenador

        $coordenador = DB::table('users')->leftJoin('user_parameters as u_p9', function ($q) {
            $q->on('users.id', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
        })
            ->where('users.id', $coordenador_id)
            ->first();


        $data = [
            'turma' => $turna_anoLectivo[0]->turma,
            'lectiveYear' => $turna_anoLectivo[0]->anoLetivo,
            'discipline_code' => $disciplina[0]->codigo . ' - ' . $disciplina[0]->disciplina,
            'discipline_name' => $disciplina[0]->disciplina,
            'regimeFinal' => $regimeFinal,
            'curso' => $course->display_name,
            'ano_curricular' => $turna_anoLectivo[0]->Anocurricular,
            'html_table_pauta' => $request->data_html,
            'institution' => $institution,
            'chefe_gabinet' => $gabinete_chefe,
            'coordenador_publicou' => $coordenador->value,
            'logotipo' => $Logotipo_instituicao,
            'utilizadores' => $utilizadores,
            'documentoCode_documento' => $documentoCode_documento,
            'estatistica' => $estatistica,
            'estatistica_tabela' => $dados_estatistico
        ];

        $parts = explode('/', $turna_anoLectivo[0]->anoLetivo);
        $fileName = 'Pauta-' . Carbon::now()->format('h:i:s') . '-' . $nova_disciplina . '-' . $turna_anoLectivo[0]->turma . '_' . $parts[0] . '.pdf';

        // VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER GERADA
        // 10 -> PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
        if ($request->pauta_code == "10") {
            $path = "/storage/pautas-recurso/" . $fileName;
        }
        if ($request->pauta_code == "20") {
            $path = "/storage/pautas-exame/" . $fileName;
        }
        if ($request->pauta_code == "25") {
            $path = "/storage/pautas-exame-oral/" . $fileName;
        }        
        if ($request->pauta_code == "30") {
            $path = "/storage/pautas-final/" . $fileName;
        }
        if ($request->pauta_code == "40") {
            $path = "/storage/pautas-frequencia/" . $fileName;
        }
        // Zacarias MINHA LINHA
        if ($request->pauta_code == "60") {
            $path = "/storage/pautas-seminario/" . $fileName;
        }
        if ($request->pauta_code == "35") {
            $path = "/storage/pautas-exame-especial/" . $fileName;
        }
        if ($request->pauta_code == "45") {
            $path = "/storage/pautas-exame-extraordinario/" . $fileName;
        }
        // MUDA O ESTADO DA PAUTA
        $pauta_pesquisa = DB::table('pauta_path as pauta')
            ->where(['pauta.id_publicar_pauta' => $id_pauta_publicada, 'code' => $request->pauta_code])->orderBy('id', 'DESC')->get();

        if (count($pauta_pesquisa) > 0) {
            $pauta_muda_estado = DB::table('pauta_path as pauta')
                ->where(['pauta.id_publicar_pauta' => $id_pauta_publicada, 'code' => $request->pauta_code])->orderBy('id', 'DESC')->update(['last' => 0]);
        }

        //data actual
        $dataCorrent = carbon::now();
        //Guardar o caminho na tabela 
        DB::table('pauta_path')->insert(
            [
                'id_publicar_pauta' => $id_pauta_publicada,
                'path' => $path,
                'last' => 1,
                'code' => $request->pauta_code,
                'created_at' =>  $dataCorrent
            ]
        );

        //desenhar a tabela no arquivo
        $path_tabela = storage_path('app/public/pautas-final/tabela.blade.php');
        file_put_contents($path_tabela, $request->data_html);

        //fechar o arquivo
        //   return view("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);
        //---  ###  ---   ###   ---   ###   ---   ###   ---//
        //---  ###  ---   ###   ---   ###   ---   ###   ---//
        $pdf = PDF::loadView("Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaFinal", $data);
        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '13mm');
        $pdf->setOption('margin-right', '2mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        $pdf->setPaper('a4');
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        //VERIFICA A REQUISÃO PARA SABER O TIPO DE PAUTA A SER ARMAZENADA
        //10->PAuta de Recurso | 20 -> Pauta de Exame | 30 -> Pauta Final
        if ($request->pauta_code == "10") {
            $pdf->save(storage_path('app/public/pautas-recurso/' . $fileName));
            $nome = "Pauta_recurso";
            $Pauta_tipo_Not = "Recurso";
        }
        if ($request->pauta_code == "20") {
            $pdf->save(storage_path('app/public/pautas-exame/' . $fileName));
            $nome = "Pauta_exame";
            $Pauta_tipo_Not = "Exame";
        }
        if ($request->pauta_code == "25") {
            $pdf->save(storage_path('app/public/pautas-exame-oral/' . $fileName));
            $nome = "Pauta_exame";
            $Pauta_tipo_Not = "Exame";
        }        
        if ($request->pauta_code == "30") {
            $pdf->save(storage_path('app/public/pautas-final/' . $fileName));
            $nome = "Pauta_final";
            $Pauta_tipo_Not = "Classificação final";
        }
        if ($request->pauta_code == "40") {
            $pdf->save(storage_path('app/public/pautas-frequencia/' . $fileName));
            $nome = "Pauta_frequencia";
            $Pauta_tipo_Not = "MAC";
        }
        // Zacarias MINHA LINHA
        if ($request->pauta_code == "60") {
            $pdf->save(storage_path('app/public/pautas-seminario/' . $fileName));
            $nome = "Pauta_seminario";
            $Pauta_tipo_Not = "Seminário";
        }
        if ($request->pauta_code == "35") {
            $pdf->save(storage_path('app/public/pautas-exame-especial/' . $fileName));
            $nome = "Exame_especial";
            $Pauta_tipo_Not = "Exame especial";
        }
        if ($request->pauta_code == "45") {
            $pdf->save(storage_path('app/public/pautas-exame-extraordinario/' . $fileName));
            $nome = "Extraordinario";
            $Pauta_tipo_Not = "Exame extraordinário";
        }


        //Pegar Professor da turma e disciplina --Publicação será no PDF
        $Professores = DB::table('user_disciplines as professor')
            ->join('user_classes as turma', 'turma.user_id', 'professor.users_id')
            ->join('users as u', 'u.id', 'professor.users_id')
            ->where('turma.class_id', $request->id_turma)
            ->where('professor.disciplines_id', $request->id_disciplina)
            ->select(['professor.*', 'u.name'])
            ->get()
            ->map(function ($item) {
                return  [
                    "id" => $item->users_id,
                    "name" => $item->name
                ];
            });

        // turma
        $turma_geral = DB::table('classes')
            ->where('id', $request->id_turma)
            ->first();


        $disciplina_notification = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                $join->on('trans.discipline_id', '=', 'disc.id');
                $join->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('trans.active', '=', DB::raw(true));
            })
            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where('disc.id', $request->id_disciplina)
            ->first();





        $icon = "fa fa-file-text";
        $subject = "[" . $disciplina_notification->disciplina . "-" . $turma_geral->display_name . "]-Pauta de " . $Pauta_tipo_Not;
        $file = $path;

        $discipline_p = $disciplina_notification->disciplina ?? "";
        $turma_p = $turma_geral->display_name ?? "";

        $notificar = collect($Professores)->map(function ($item) use ($icon, $subject, $file, $discipline_p, $Pauta_tipo_Not, $turma_p) {
            $body = "
            <p>Caro(a) professor(a) <b>" . $item['name'] ?? "" . "</b> a pauta <b>" . $Pauta_tipo_Not . "</b> da disciplina <b>" . $discipline_p . "</b>, referente à turma <b>" . $turma_p . "</b>, e do qual é docente foi publicada pelo seu coordenador!
             <br>
             </p>
            ";
            $destination[] = $item['id'];
            notification($icon, $subject, $body, $destination, $file, null);
        });



        //Notificar alunos da Pauta
        if ($request->pauta_dados) {

            $escala1 = $request->pauta_dados;
            $escala = explode(',@,', $escala1);

            for ($i = 0; $i < count($escala); $i++) {
                $without_arroba[$i] = str_replace(",@", "", $escala[$i]);
                $aluno = explode(',', $without_arroba[$i]);
                $userStudant = DB::table('users')->where('id', $aluno[1])->first();
                if ($userStudant != null) {
                    $estudanteNome = $userStudant->name;

                    $body = "<p>Caro(a) estudante(a), <b>" . $estudanteNome . "</b>, a Pauta de <b>" . $Pauta_tipo_Not ?? "" . "</b>  da disciplina <b>" . $discipline_p ?? "" . "</b>, referente à
                sua turma <b>" . $turma_p ?? "" . "</b>, foi publicada pelo seu coordenador
                de curso!</p>";

                    $destination[] = $aluno[1];
                    notification($icon, $subject, $body, $destination, null, null);
                }
            }
        }   
            return $pdf->stream($nome . '.pdf');
       } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }






    public function index()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];

            return view("Avaliations::avaliacao-aluno.show_pauta_final")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function getCurso($id_anolectivo,$whoIs = null)
    {
        if($whoIs == 'teacher')
        {
            $courses_id = DB::table('user_courses')
            ->where('users_id', auth()->user()->id)
            ->get()
            ->pluck('courses_id')
            ->toArray();
        }
        else{
            $courses_id = DB::table('coordinator_course')
            ->where('user_id', auth()->user()->id)
            ->get()
            ->pluck('courses_id')
            ->toArray();
        }

       
      

        $curso_model = Course::whereIn('courses.id',$courses_id)
            ->join('users as u1', 'u1.id', '=', 'courses.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'courses.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'courses.deleted_by')
            ->leftJoin('duration_type_translations as dtt', function ($join) {
                $join->on('dtt.duration_types_id', '=', 'courses.duration_types_id');
                $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dtt.active', '=', DB::raw(true));
            })
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->select([
                'courses.*',
                'u1.name as created_by',
                'u2.name as updated_by',
                'u3.name as deleted_by',
                'ct.display_name as nome_curso',
                'ct.abbreviation as conta',
                DB::raw('CONCAT(ct.display_name," ",courses.duration_value, " ", dtt.display_name) as duration')
            ])
            ->get();

        return response()->json(array('data' => $curso_model));
    }



    public function getCursoCoordenador($id_anolectivo,$whoIs = null)
    {
        
        try {
        $teacher_id = Auth::user()->id;
        $user = User::whereId($teacher_id)->firstOrFail();

        if($whoIs == 'teacher')
        {
            $course_id = DB::table('user_courses')
            ->where('users_id', auth()->user()->id)
            ->get();
        }
        else{
            $course_id = DB::table('coordinator_course')
            ->where('user_id', auth()->user()->id)
            ->get();
         
        }
        // $disciplinas_coordenador=$this->disciplinas_coordenador_todas($course_id[0]->courses_id);
    
      $idCursos = $course_id->pluck('courses_id');

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
                // 'user_disciplines.users_id as id_teacher'
            ])
             ->whereIn('stp.courses_id',$idCursos)
            // ->where('stpeid.id', $id)
            // ->where('lective_years_id',$anolectivo)
            ->when($whoIs == 'teacher',function($q)use($teacher_id){
                $q->where('user_disciplines.users_id', $teacher_id);
            })
            // ->where('dp.courses_id',$course_id[0]->courses_id)
            ->distinct()
            ->get();


        return response()->json(array('data' => $getDisciplinesAll));

         } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    public function getDiscipline($id_anoLectivo, $anoCurso_id_Select, $arrayCurso,$whoIs = null)
    {
        try {
            $getDisciplinesAno = DB::table('courses as crs')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'crs.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftJoin('study_plans as stp', 'stp.courses_id', '=', 'crs.id')

                ->leftJoin('study_plan_editions as stpeid', 'stpeid.study_plans_id', '=', 'stp.id')


                ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')

                ->when($whoIs == 'teacher', function($q){
                    $q->join('user_disciplines as tc','tc.disciplines_id','stpeid_discipl.disciplines_id');
                    $q->where('tc.users_id',auth()->user()->id);
                })
                ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'dp.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })

                ->join('study_plan_edition_disciplines as stpeid_edtdiscipl', function ($join) {
                    $join->on('stpeid_edtdiscipl.study_plan_edition_id', '=', 'stpeid.id');
                    $join->on('stpeid_edtdiscipl.discipline_id', '=', 'dp.id');
                })
                ->select([
                    'dt.display_name as dt_display_name',
                    'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                    'dp.code as code',
                    'dp.id as id_disciplina'
                ])
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('stpeid_discipl.years', $anoCurso_id_Select)
                ->where('stp.courses_id', $arrayCurso)
                ->distinct('stp.courses_id')
                ->get();
            // study_plan_editions.study_plans_id=study_plans.id
            // study_plan_edition_disciplines.study_plan_edition_id=study_plan_editions.id
            return response()->json(array('data' => $getDisciplinesAno));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function getTurma($ano_lectivo, $id_curso,$whoIs = null)
    {
        try {
            $turma =  DB::table('classes')
                ->where('classes.deleted_at', null)
                ->where('classes.deleted_by', null)
                ->where('classes.courses_id', $id_curso)
                ->where('classes.lective_year_id', $ano_lectivo)
                ->when($whoIs == 'teacher', function($q){
                    $q->join('teacher_classes as tc','tc.class_id','classes.id');
                    $q->where('tc.user_id',auth()->user()->id);
                })
                ->select('classes.*')
                ->distinct()
                ->get();

            return response()->json(array('data' => $turma));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function getMenuAvaliacoesDisciplina($id_turma, $ano_lectivo, $id_curso, $id_disciplina, $ano_curso, $periodo_disciplina)
    {
        try {

            $avalicaDisciplinaMenu = $this->getMenuAvalicaoDisciplina($id_disciplina, $ano_lectivo, $id_curso, $periodo_disciplina);
            $MetricasAvalicaDisciplinaMenu = $this->getMenuMetricasAvaliacaDisciplina($id_disciplina, $ano_lectivo, $id_curso, $periodo_disciplina);
            return response()->json(array('data' => $avalicaDisciplinaMenu, 'vetor' => $MetricasAvalicaDisciplinaMenu));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function getMenuAvalicaoDisciplina($id_disciplina, $ano_lectivo, $id_curso, $periodo_disciplina)
    {
        $cabecalhoDisciplina = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
            // ->leftJoin('metricas as mt','mt.avaliacaos_id','=','plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('calendario_prova as calend', 'calend.id_avaliacao', '=', 'plano_estudo_avaliacaos.avaliacaos_id')

            ->select([
                'avl.id as id_avaliacao',
                // 'mt.avaliacaos_id as id_avaliacaoMetrica',
                'avl.nome as nome_avaliacao',
                // 'mt.nome as nome_mterica',
                'ct.display_name as nome_courso',
                'crs.id as id_courso',
                'calend.date_start as date_startProva',
                'calend.data_end as date_endProva'
            ])
            ->where('crs.id', $id_curso)
            ->where('calend.simestre', $periodo_disciplina)
            ->orWhere('calend.simestre', null)
            ->where('dp.id', $id_disciplina)
            ->where('stpeid.lective_years_id', $ano_lectivo)
            ->orderBy('calend.date_start')
            ->distinct('calend.date_start')
            ->get();
        return $cabecalhoDisciplina;
    }


    private function getMenuMetricasAvaliacaDisciplina($id_disciplina, $ano_lectivo, $id_curso, $periodo_disciplina)
    {

        $cabecalhoDisciplinaMetricas = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
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
            ->leftJoin('metricas as mt', 'mt.avaliacaos_id', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('calendario_prova as calend', 'calend.id_avaliacao', '=', 'plano_estudo_avaliacaos.avaliacaos_id')
            ->leftJoin('calendarie_metrica as calend_metrica', 'calend_metrica.id_metrica', '=', 'mt.id')

            ->select([
                'avl.id as id_avaliacao',
                'mt.avaliacaos_id as id_avaliacaoMetrica',
                'avl.nome as nome_avaliacao',
                'mt.nome as nome_mterica',
                'ct.display_name as nome_courso',
                'crs.id as id_courso',
                'calend_metrica.data_inicio as date_incioMetrica',
                'calend_metrica.data_fim as date_fimMetrica'
            ])              
            ->where('crs.id', $id_curso)
            ->where('calend_metrica.id_periodo_simestre', $periodo_disciplina)
            ->orWhere('calend_metrica.id_periodo_simestre', null)
            ->where('dp.id', $id_disciplina)
            ->where('stpeid.lective_years_id', $ano_lectivo)
            // ->orderBy('calend_metrica.data_inicio')
            ->distinct('calend_metrica.data_inicio')
            ->get();
        return $cabecalhoDisciplinaMetricas;
    }







    private function getStudentNotasExame($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected, $code = 20)
    {
        
        try {

        
        $metrica = DB::table('metricas as mt')
            ->join('avaliacaos as av','av.id','mt.avaliacaos_id')
            ->where('av.anoLectivo',$id_anoLectivo)
            ->where('av.nome','Exame')
            ->where('mt.code_dev', $code == 25 ? "oral" : "Neen")
            ->select('mt.*')
            ->first();
            
           
 
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
            ->leftJoin('avaliacao_alunos as avl_aluno', function ($join) use ($metrica) {
                $join->on('avl_aluno.metricas_id', '=','mt.id');
                $join->on('avl_aluno.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id');
                $join->where('avl_aluno.metricas_id',$metrica->id);
            })
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })

            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })

            //Verificar os meses pagos.
            ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
            ->leftJoin('articles as art', function ($join) {
                $join->on('artR.article_id', '=', 'art.id');
            })
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula.id as id_mat',
                'at.display_name as article_name',
                'artR.status as estado_do_mes',
                'artR.month as mes',
                'mt.code_dev as MT_CodeDV',
                'avl_aluno.segunda_chamada as segunda_chamada',
                'avl_aluno.presence as presence'
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            // ->where('matricula_disci.exam_only', 1)
 
            ->whereNull('artR.deleted_at')
            ->whereNotNull('artR.month')
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->distinct();
            
         
            if($code == 25){
               /* $avaliacaos_student = $avaliacaos_student
                    ->join('tb_exame_oral_student as teos','matricula.id','teos.matriculation_id')
                    ->where('teos.discipline_id','dp.id')
                    ->where('mt.code_dev', "oral");*/
            }
            else{
              $avaliacaos_student = $avaliacaos_student->where('mt.code_dev', "Neen");
            }
            
             $avaliacaos_student = $avaliacaos_student->get();

             $avaliacaos_student = $avaliacaos_student->reject(function($avl) use($avaliacaos_student){
                $faltou =  isset($avl->presence);
                $nota_normal = !isset($avl->segunda_chamada);
               
                $fez_segunda_chamada = $avaliacaos_student->where('user_id', $avl->user_id)
            ->where('Disciplia_id', $avl->Disciplia_id)
            ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
            ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
            ->where('segunda_chamada', 1)
            ->isNotEmpty();

            

             
             $sai =  $faltou && $nota_normal && $fez_segunda_chamada;
            
            
        return $sai;
            });
    
            
             
            return    $avaliacaos_student;

        $avaliacaos_student;
       
        
        } catch (Exception | Throwable $e) {
                return $e;
                // return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    }





   //VERIFICA SE O ESTUDANTE ESTÁ A FREQUÊNTAR OU A FAZER EXAME
    public function getStudentMatriculation($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $pub_print,$codeExame)
    {
        try{ 
        // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);
    
        $validacao_proprina = DB::table('pauta_avaliation_student_shows')
            ->where('lective_year_id', $id_anoLectivo)
            ->first();

    
        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();

        //Estado da Publicação da pauta
        $estado_publicar = DB::table('publicar_pauta')
            ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => $codeExame])
            ->orderBy('id', 'DESC')->first();

        $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
        $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;

        // VERIFICA SE A DISCIPLINA TÊM EXAME OBRIGATÓRIO
        // $exame = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
        //     ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
        //     ->leftJoin('discipline_has_exam as d_exame', 'd_exame.id_plain_study', '=', 'stpeid.id')

        //     ->where('d_exame.discipline_id',$id_disciplina)  
        //     ->where('dp.id',$id_disciplina)  
        //     ->where('plano_estudo_avaliacaos.disciplines_id',$id_disciplina)                         
        //     ->distinct()
        //     ->first();

        $mesActual = date('m') > 9 ? date('m') : date('m')[1];
        $diaActual = date('d');
        if ( $validacao_proprina->quantidade_mes >= 1) {
            $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
        } else {
            $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
        }

        $array = explode(",", $Turma_id_Select);
        $Turma_id_Select = trim($array[0]);

          // PUBLICAR

        if (in_array($pub_print, [5])) {
        
           $avaliacaos_student = $this->getStudentNotasExame($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected, $codeExame);
        } 
        
        else {
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
                ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                    $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                    $join->on('matricula_disci.discipline_id', '=', 'dp.id');
                })

                //Verificar os meses pagos.
                ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
                ->leftJoin('articles as art', function ($join) {
                    $join->on('artR.article_id', '=', 'art.id');
                })
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


                ->select([

                    'avl.nome as Avaliacao_nome',
                    'full_name.value as full_name',
                    'avl_aluno.nota as nota_anluno',
                    'up_meca.value as code_matricula',
                    'avl_aluno.id as Avaliacao_aluno_id',
                    'avl_aluno.id_turma as Avaliacao_aluno_turma',
                    'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                    'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                    'mt.id as Metrica_id',
                    'avl_aluno.users_id as user_id',
                    'dp.id as Disciplia_id',
                    'mt.nome as Metrica_nome',
                    'mt.percentagem as percentagem_metrica',
                    'stpeid.course_year as ano_curricular',
                    'matricula_disci.exam_only as exam_only',
                    'matricula.id as id_mat',
                    'at.display_name as article_name',
                    'artR.status as estado_do_mes',
                    'artR.month as mes',
                    'mt.code_dev as MT_CodeDV',
                    'avl_aluno.segunda_chamada as segunda_chamada',
                    'avl_aluno.presence as presence'
                ])
                ->where('avl_aluno.id_turma', $Turma_id_Select)
                ->where('stp.courses_id', $id_curso)
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('dp.id', $id_disciplina)
                // ->where('matricula_disci.exam_only', 1)
                ->where('code_dev.code', "propina")
                ->where('artR.month', '=', $mesActual)
                ->whereNull('artR.deleted_at')
                ->where('mt.code_dev', "Neen")
                ->orderBy('mt.id', 'asc')
                ->orderBy('full_name.value', 'asc')
                // ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]) 
                ->where('matricula.lective_year', $lectiveYearSelected->id)
                //->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->distinct('avl_aluno.users_id')
                ->get();
        }

      

        $avaliacaos_student = $avaliacaos_student->reject(function($avl) use($avaliacaos_student){
            $faltou =  isset($avl->presence);
            $nota_normal = !isset($avl->segunda_chamada);
           
            $fez_segunda_chamada = $avaliacaos_student->where('user_id', $avl->user_id)
            ->where('Disciplia_id', $avl->Disciplia_id)
            ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
            ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
            ->where('segunda_chamada', 1)
            ->isNotEmpty();

            

             
             $sai =  $faltou && $nota_normal && $fez_segunda_chamada;
            
            
        return $sai;
        });

       
        
        $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
         

            ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
            ->leftJoin('discipline_periods as dt', function ($join) {
                $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
            })

            ->select([
                'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                'dt.code as value_disc'
            ])
            ->where('stpeid_discipl.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->orderBy('stpeid_discipl.disciplines_id', 'asc')
            ->distinct()
            ->get();

          

                $object = new MatriculationDisciplineListController();
    
                $devedores = collect($avaliacaos_student)->filter(function($item,$key) use($lectiveYearSelected, $object){    
                    $dividas = $object->get_payments($lectiveYearSelected->id,$item->id_mat);
                    return isset($dividas) && ($dividas>0);
                });
                $devedores = $devedores->pluck('user_id');
    
                
    


        $collection = collect($avaliacaos_student);
        $dados = $collection->groupBy('full_name', function ($item) {
            return ($item);
        });

        // PEGA AS PROPINAS DOS ESTUDANTES
        // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);
       

        $date = [

         
            'ano' => $id_anoLectivo,
            'estado_pauta' => $estado_p,
            'estado_tipo' => $estado_tipo,
            
            'curso' => $id_curso,
            'turma' => $Turma_id_Select,
            'disciplina' => $id_disciplina,
            'periodo_disc' => $discipline_periodo,


            'dados' => $dados,
            //'exame' => $exame!=null?$exame:0,
            'professor' => auth()->user()->name,
            //'propinas' => $propinas,
            'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
            'validacao_proprina' => $validacao_proprina,
            'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
            'devedores' => $devedores
        ];

        return response()->json(array('data' => $date));
        
        
    } catch (Exception | Throwable $e) {
                // logError($e);
                return $e;
                return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
        
        
    }




    private function getStudentNotasRecurso($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected)
    {

        // return [$id_anoLectivo,$id_curso,$Turma_id_Select,$id_disciplina,$lectiveYearSelected];
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
            })->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })

            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })

            //Verificar os meses pagos.
            ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
            ->leftJoin('articles as art', function ($join) {
                $join->on('artR.article_id', '=', 'art.id');
            })
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            // ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')



            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula.id as id_mat',
                'at.display_name as article_name',
                'artR.status as estado_do_mes',
                'artR.month as mes',
                'mt.code_dev as MT_CodeDV',
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            // ->where('matricula_disci.exam_only',0)  
            // ->where('code_dev.code', "propina")
            // ->where('artR.month', $mesActual)         
            ->where('mt.code_dev', "Recurso")
            // ->where('avl_aluno.plano_estudo_avaliacaos_id',1565)
            ->whereNull('artR.deleted_at')
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            // ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            ->distinct()
            ->get();

        return $avaliacaos_student;
    }




    // PAUTA DE RECURSO
    public function getStudentGradesRecurso($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $pub_print)
    {

        // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);

        $validacao_proprina = DB::table('pauta_avaliation_student_shows')
            ->where('lective_year_id', $id_anoLectivo)
            ->first();


        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();

        //Estado da Publicação da pauta
        $estado_publicar = DB::table('publicar_pauta')
            ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => 10])
            ->orderBy('id', 'DESC')->first();

        $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
        $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;

        // VERIFICA SE A DISCIPLINA TÊM EXAME OBRIGATÓRIO
        // $exame = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
        //     ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
        //     ->leftJoin('discipline_has_exam as d_exame', 'd_exame.id_plain_study', '=', 'stpeid.id')

        //     ->where('d_exame.discipline_id',$id_disciplina)  
        //     ->where('dp.id',$id_disciplina)  
        //     ->where('plano_estudo_avaliacaos.disciplines_id',$id_disciplina)                         
        //     ->distinct()
        //     ->first();

        $mesActual = date('m') > 9 ? date('m') : date('m')[1];
        $diaActual = date('d');
        if ($validacao_proprina->quantidade_mes >= 1) {
            $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
        } else {
            $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
        }



        if (in_array($pub_print, [6])) {
            $avaliacaos_student = $this->getStudentNotasRecurso($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected);
        } else {
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
                ->leftJoin('user_parameters as sexo', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
                ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                    $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                    $join->on('matricula_disci.discipline_id', '=', 'dp.id');
                })

                //Verificar os meses pagos.
                ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
                ->leftJoin('articles as art', function ($join) {
                    $join->on('artR.article_id', '=', 'art.id');
                })
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                // ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')



                ->select([
                    'sexo_value.code as sexo',
                    'avl.nome as Avaliacao_nome',
                    'full_name.value as full_name',
                    'avl_aluno.nota as nota_anluno',
                    'up_meca.value as code_matricula',
                    'avl_aluno.id as Avaliacao_aluno_id',
                    'avl_aluno.id_turma as Avaliacao_aluno_turma',
                    'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                    'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                    'mt.id as Metrica_id',
                    'avl_aluno.users_id as user_id',
                    'dp.id as Disciplia_id',
                    'mt.nome as Metrica_nome',
                    'mt.percentagem as percentagem_metrica',
                    'stpeid.course_year as ano_curricular',
                    'matricula_disci.exam_only as exam_only',
                    'matricula.id as id_mat',
                    'at.display_name as article_name',
                    'artR.status as estado_do_mes',
                    'artR.month as mes',
                    'mt.code_dev as MT_CodeDV',
                ])
                ->where('avl_aluno.id_turma', $Turma_id_Select)
                ->where('stp.courses_id', $id_curso)
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('dp.id', $id_disciplina)
                // ->where('matricula_disci.exam_only',0)  
                // ->where('code_dev.code', "propina")
                ->where('artR.month', '=', $mesActual)
                ->where('mt.code_dev', "Recurso")
                // ->where('avl_aluno.plano_estudo_avaliacaos_id',1565)
                ->whereNull('artR.deleted_at')
                ->orderBy('mt.id', 'asc')
                ->orderBy('full_name.value', 'asc')
                // ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
                ->where('matricula.lective_year', $lectiveYearSelected->id)
                ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->distinct()
                ->get();

            // return $avaliacaos_student;
        }


        $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            // ->leftJoin('courses_translations as ct', function ($join) {
            //     $join->on('ct.courses_id', '=', 'crs.id');
            //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            //     $join->on('ct.active', '=', DB::raw(true));
            // })


            ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
            ->leftJoin('discipline_periods as dt', function ($join) {
                $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
            })

            ->select([
                'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                'dt.code as value_disc'
            ])
            ->where('stpeid_discipl.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->orderBy('stpeid_discipl.disciplines_id', 'asc')
            ->distinct()
            ->get();

        $collection = collect($avaliacaos_student);
        $dados = $collection->groupBy('full_name', function ($item) {
            return ($item);
        });

        // PEGA AS PROPINAS DOS ESTUDANTES
        // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

        $date = [

            'ano' => $id_anoLectivo,
            'estado_pauta' => $estado_p,
            'estado_tipo' => $estado_tipo,
            
            'curso' => $id_curso,
            'turma' => $Turma_id_Select,
            'disciplina' => $id_disciplina,
            'periodo_disc' => $discipline_periodo,
            // 'alunos_notas' =>$avaliacaos_student,
            'dados' => $dados,
            // 'exame' => $exame!=null?$exame:0,
            'professor' => auth()->user()->name,
            // 'propinas' => $propinas,
            'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
            'validacao_proprina' => $validacao_proprina,
            'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),
        ];

        return response()->json(array('data' => $date));
    }







    // TAZER OS ESTUDANTES E SUAS PROPINAS
    private function getStudentNotas($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $lectiveYearSelected)
    {
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
            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })

            ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })

            /*
        ->leftJoin('discipline_has_exam as discipline_exam', function ($join){
            $join->on('discipline_exam.discipline_id','=','dp.id');
            $join->on('discipline_exam.id_plain_study','=','stpeid.id');
        })
        */

            //Verificar os meses pagos.
            ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
            ->leftJoin('articles as art', function ($join) {
                $join->on('artR.article_id', '=', 'art.id');
            })
            ->leftJoin('article_translations as at', function ($join) {
                $join->on('art.id', '=', 'at.article_id');
                $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('at.active', '=', DB::raw(true));
            })
            ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


            ->select([
                'sexo_value.code as sexo',
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                //'discipline_exam.has_mandatory_exam as exam_only',
                'matricula.id as id_mat',
                'at.display_name as article_name',
                'artR.status as estado_do_mes',
                'artR.month as mes',
                'mt.code_dev as MT_CodeDV',
                'avl_aluno.segunda_chamada as segunda_chamada',
                'avl_aluno.presence as presence'
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            //->where('matricula_disci.exam_only',0)  
            // ->where('code_dev.code', "propina")
            // ->where('artR.month', $mesActual)
            ->whereNull('artR.deleted_at')
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->where('matricula.lective_year', $id_anoLectivo)
            ->whereIn('matricula.id', PautaGeralAvaliacoesUtil::usersMatriculationNotAnulate($Turma_id_Select))
            //->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            // ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
            ->distinct()
            ->get();
               
        return $avaliacaos_student;
    }







    public function getStudentNotasPautaFinal($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $tipo_pauta, $pub_print)
    {
        set_time_limit(300);
        // $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo);
        
        // PEGA O LIMITE DE PAGAMENTO DA PROPINA
        $validacao_proprina = DB::table('pauta_avaliation_student_shows')
            ->where('lective_year_id', $id_anoLectivo)
            ->first();

        // dd($validacao_proprina->quantidade_mes);


        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();

        //Estado da Publicação da pauta
        $estado_publicar = DB::table('publicar_pauta')
            ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => $tipo_pauta])
            ->orderBy('id', 'DESC')->first();

        $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
        $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;


        $exame = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('discipline_has_exam as discipline_exam', function ($join) {
                $join->on('discipline_exam.discipline_id', '=', 'dp.id');
                $join->on('discipline_exam.id_plain_study', '=', 'stpeid.id');
            })
            //->where('d_exame.discipline_id',$id_disciplina)  
            ->where('dp.id', $id_disciplina)
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            //->distinct()
            ->first();


        $turmaObj = explode(" ,", $Turma_id_Select);
        // PUBLICAR
        if (in_array($pub_print, [4, 3])) {
          
            $avaliacaos_student = $this->getStudentNotas($id_anoLectivo, $id_curso, $turmaObj[0], $id_disciplina, $lectiveYearSelected, $tipo_pauta);
            // return $pub_print;
           
        } else {
            // IMPRMIR
            $mesActual = date('m') > 9 ? date('m') : date('m')[1];
            $diaActual = date('d');

            if ($validacao_proprina->quantidade_mes >= 1) {
                // $mesActual= $mesActual - $validacao_proprina->quantidade_mes;
                $mesActual = $mesActual;
            } else {
                // $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual: $mesActual - $validacao_proprina->quantidade_mes;
                $mesActual = $diaActual;
            }
            
            // return $tipo_pauta;
            $turmaObj = explode(" ,", $Turma_id_Select);
        
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
                ->leftJoin('user_parameters as sexo', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')

                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('avl_aluno.users_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('matriculations as matricula', 'matricula.user_id', '=', 'avl_aluno.users_id')
                ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                    $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                    $join->on('matricula_disci.discipline_id', '=', 'dp.id');
                })

                /*
            ->leftJoin('discipline_has_exam as discipline_exam', function ($join){
                $join->on('discipline_exam.discipline_id','=','dp.id');
                $join->on('discipline_exam.id_plain_study','=','stpeid.id');
            })
            */

                //Verificar os meses pagos.
                ->leftJoin('article_requests as artR', 'artR.user_id', 'full_name.users_id')
                ->leftJoin('articles as art', function ($join) {
                    $join->on('artR.article_id', '=', 'art.id');
                })
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')


                ->select([

                    'sexo_value.code as sexo',
                    'avl.nome as Avaliacao_nome',
                    'full_name.value as full_name',
                    'avl_aluno.nota as nota_anluno',
                    'up_meca.value as code_matricula',
                    'avl_aluno.id as Avaliacao_aluno_id',
                    'avl_aluno.id_turma as Avaliacao_aluno_turma',
                    'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                    'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                    'mt.id as Metrica_id',
                    'avl_aluno.users_id as user_id',
                    'dp.id as Disciplia_id',
                    'mt.nome as Metrica_nome',
                    'mt.percentagem as percentagem_metrica',
                    'stpeid.course_year as ano_curricular',
                    'matricula_disci.exam_only as exam_only',
                    //'discipline_exam.has_mandatory_exam as exam_only',
                    'matricula.id as id_mat',
                    'at.display_name as article_name',
                    'artR.status as estado_do_mes',
                    'artR.month as mes',
                    'mt.code_dev as MT_CodeDV',
                    'avl_aluno.segunda_chamada as segunda_chamada',
                    'avl_aluno.presence as presence'

                ])
                ->where('avl_aluno.id_turma', $turmaObj[0])
                ->where('stp.courses_id', $id_curso)
                ->where('stpeid.lective_years_id', $id_anoLectivo)
                ->where('dp.id', $id_disciplina)
                ->where('matricula_disci.exam_only', 0)
                // ->where('code_dev.code', "propina")
                ->where('artR.month', $mesActual)
                ->whereNull('artR.deleted_at')
                // ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)  
                ->orderBy('full_name.value', 'asc')
                ->orderBy('mt.id', 'asc')
                ->where('matricula.lective_year', $id_anoLectivo)
                ->whereIn('matricula.id', PautaGeralAvaliacoesUtil::usersMatriculationNotAnulate($turmaObj[0]))
                ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
                ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])  
                ->distinct()
                ->get();
        }
        
        $avaliacaos_student = $avaliacaos_student->reject(function($avl) use($avaliacaos_student){
            $faltou =  isset($avl->presence);
            $nota_normal = !isset($avl->segunda_chamada);
            
            $fez_segunda_chamada = $avaliacaos_student->where('user_id', $avl->user_id)
            ->where('Disciplia_id', $avl->Disciplia_id)
            ->where('Avaliacao_aluno_Metrica', $avl->Avaliacao_aluno_Metrica)
            ->where('Avaliacao_aluno_turma', $avl->Avaliacao_aluno_turma)
            ->where('segunda_chamada', 1)
            ->isNotEmpty();


             $sai =  $faltou && $nota_normal && $fez_segunda_chamada;
            
            
        return $sai;
        });
    
       
        // return $propinas = $ths->getEmolumentoEstudent($id_anoLectivo,$avaliacaos_student);
        // return count($avaliacaos_student);


        $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            // ->leftJoin('courses_translations as ct', function ($join) {
            //     $join->on('ct.courses_id', '=', 'crs.id');
            //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            //     $join->on('ct.active', '=', DB::raw(true));
            // })


            ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
            ->leftJoin('discipline_periods as dt', function ($join) {
                $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
            })

            ->select([
                'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                'dt.code as value_disc'
            ])
            ->where('stpeid_discipl.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->orderBy('stpeid_discipl.disciplines_id', 'asc')
            ->distinct()
            ->get();

        $collection = collect($avaliacaos_student);
        $dados = $collection->groupBy('full_name', function ($item) {
            return ($item);
        });
      
        // PEGA AS PROPINAS DOS ESTUDANTES
        // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

        $date = [

            'ano' => $id_anoLectivo,
            'estado_pauta' => $estado_p,
            'estado_tipo' => $estado_tipo,
            'curso' => $id_curso,
            'turma' => $turmaObj[0],
            'disciplina' => $id_disciplina,
            'periodo_disc' => $discipline_periodo,
            // 'alunos_notas' =>$avaliacaos_student,
            'dados' => $dados,
            'exame' => $exame,
            'professor' => auth()->user()->name,
            // 'propinas' => $propinas,
            'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $turmaObj[0] . "-Disciplina:" . $id_disciplina,
            'validacao_proprina' => $validacao_proprina,
            'avaliacao_config' => $this->avaliacaoConfig($id_anoLectivo),

        ];

        return response()->json(array('data' => $date));
    }

    private function avaliacaoConfig($id_anoLectivo){
       return AvaliacaoConfig::where(['lective_year' => $id_anoLectivo])->first();
    }





    // PUBLICAR PAUTAS PELOS COORDENADORES
    public function getPautaPublicar($id_anoLectivo, $id_curso, $Turma_id_Select, $id_disciplina, $tipo_pauta)
    {


        $validacao_proprina = DB::table('pauta_avaliation_student_shows')
            ->where('lective_year_id', $id_anoLectivo)
            ->first();


        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $id_anoLectivo)
            ->first();

        //Estado da Publicação da pauta
        $estado_publicar = DB::table('publicar_pauta')
            ->where(['id_turma' => $Turma_id_Select, 'id_ano_lectivo' => $id_anoLectivo, 'id_disciplina' => $id_disciplina, 'tipo' => $tipo_pauta])
            ->orderBy('id', 'DESC')->first();

        $estado_p = $estado_publicar != "" ? $estado_publicar->estado : Null;
        $estado_tipo = $estado_publicar != "" ? $estado_publicar->tipo : Null;


        $exame = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'plano_estudo_avaliacaos.disciplines_id')
            ->leftJoin('discipline_has_exam as d_exame', 'd_exame.id_plain_study', '=', 'stpeid.id')

            ->where('d_exame.discipline_id', $id_disciplina)
            ->where('dp.id', $id_disciplina)
            ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)
            ->distinct()
            ->first();

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
            ->leftJoin('matriculation_disciplines as matricula_disci', function ($join) {
                $join->on('matricula_disci.matriculation_id', '=', 'matricula.id');
                $join->on('matricula_disci.discipline_id', '=', 'dp.id');
            })


            ->select([
                'avl.nome as Avaliacao_nome',
                'full_name.value as full_name',
                'avl_aluno.nota as nota_anluno',
                'up_meca.value as code_matricula',
                'avl_aluno.id as Avaliacao_aluno_id',
                'avl_aluno.id_turma as Avaliacao_aluno_turma',
                'avl_aluno.metricas_id as Avaliacao_aluno_Metrica',
                'avl_aluno.plano_estudo_avaliacaos_id as Avaliacao_PEA',
                'mt.id as Metrica_id',
                'avl_aluno.users_id as user_id',
                'dp.id as Disciplia_id',
                'mt.nome as Metrica_nome',
                'mt.percentagem as percentagem_metrica',
                'stpeid.course_year as ano_curricular',
                'matricula_disci.exam_only as exam_only',
                'matricula.id as id_mat'
            ])
            ->where('avl_aluno.id_turma', $Turma_id_Select)
            ->where('stp.courses_id', $id_curso)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->where('matricula_disci.exam_only', 0)
            // ->where('plano_estudo_avaliacaos.disciplines_id', $id_disciplina)  
            ->orderBy('mt.id', 'asc')
            ->orderBy('full_name.value', 'asc')
            ->whereBetween('matricula.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
            ->distinct()
            ->get();


        //Verificar propinas
        //   return   $propinas = $this->getMatriculations_paymentsAlectivo($id_anoLectivo,$avaliacaos_student);

        $discipline_periodo = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
            ->leftJoin('study_plans as stp', 'stp.id', '=', 'stpeid.study_plans_id')
            // ->leftJoin('courses as crs', 'crs.id', '=', 'stp.courses_id')
            // ->leftJoin('courses_translations as ct', function ($join) {
            //     $join->on('ct.courses_id', '=', 'crs.id');
            //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            //     $join->on('ct.active', '=', DB::raw(true));
            // })


            ->leftJoin('study_plans_has_disciplines as stpeid_discipl', 'stpeid_discipl.study_plans_id', '=', 'stp.id')
            ->leftJoin('disciplines as dp', 'dp.id', '=', 'stpeid_discipl.disciplines_id')
            ->leftJoin('discipline_periods as dt', function ($join) {
                $join->on('dt.id', '=', 'stpeid_discipl.discipline_periods_id');
            })

            ->select([
                'stpeid_discipl.discipline_periods_id as periodo_disciplina',
                'dt.code as value_disc'
            ])
            ->where('stpeid_discipl.disciplines_id', $id_disciplina)
            ->where('stpeid.lective_years_id', $id_anoLectivo)
            ->where('dp.id', $id_disciplina)
            ->orderBy('stpeid_discipl.disciplines_id', 'asc')
            ->distinct()
            ->get();

        $collection = collect($avaliacaos_student);
        $dados = $collection->groupBy('full_name', function ($item) {
            return ($item);
        });

        // PEGA AS PROPINAS DOS ESTUDANTES
        // $propinas_estudantes = $this->getEmolumentoEstudent($id_anoLectivo);

        $date = [

            'ano' => $id_anoLectivo,
            'estado_pauta' => $estado_p,
            'estado_tipo' => $estado_tipo,
            
            'curso' => $id_curso,
            'turma' => $Turma_id_Select,
            'disciplina' => $id_disciplina,
            'periodo_disc' => $discipline_periodo,
            'alunos_notas' => $avaliacaos_student,
            'dados' => $dados,
            'exame' => $exame != null ? $exame : 0,
            'professor' => auth()->user()->name,
            // 'propinas' => $propinas,
            'dados_enviado' => "anoLectivo:" . $id_anoLectivo . "-IdCurso:" . $id_curso . "-Turma:" . $Turma_id_Select . "-Disciplina:" . $id_disciplina,
            'validacao_proprina' => $validacao_proprina

        ];

        return response()->json(array('data' => $date));
    }








    // PEGA AS PROPRINAS DOS ESTUDANTES
    // public function getMatriculations_paymentsAlectivo($anoLectivo)
    // {
    //     try{
    //         $lectiveYearSelected = DB::table('lective_years')
    //         ->where('lective_years.id','=',$anoLectivo)
    //         ->first();

    //         $getArtclis_estudent=$this->getEmolumentoEstudent($lectiveYearSelected);

    //         $model = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
    //             ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
    //             ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
    //             ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
    //             ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
    //             ->join('courses_translations as ct', function ($join) {
    //                 $join->on('ct.courses_id', '=', 'uc.courses_id');
    //                 $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //                 $join->on('ct.active', '=', DB::raw(true));
    //             })


    //             ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
    //             ->join('classes as cl', function ($join)  {
    //                 $join->on('cl.id', '=', 'mc.class_id');
    //                 $join->on('mc.matriculation_id', '=', 'matriculations.id');
    //                 $join->on('matriculations.course_year', '=', 'cl.year');
    //             })

    //             ->leftJoin('user_parameters as u_p', function ($join) {
    //                     $join->on('u0.id', '=', 'u_p.users_id')
    //                     ->where('u_p.parameters_id', 1);
    //             })
    //             ->leftJoin('user_parameters as up_meca', function ($join) {
    //                     $join->on('u0.id', '=', 'up_meca.users_id')
    //                     ->where('up_meca.parameters_id', 19);
    //             })
    //             ->leftJoin('user_parameters as up_bi', function ($join) {
    //                 $join->on('u0.id','=','up_bi.users_id')
    //                 ->where('up_bi.parameters_id', 14);
    //         })

    //         ->leftJoin('article_requests as art_requests',function ($join)
    //             {
    //                     $join->on('art_requests.user_id', '=',   'u0.id')
    //                     ->whereIn('art_requests.article_id', [117, 79]);

    //             })

    //         ->select([
    //             'matriculations.*',
    //             'up_meca.value as matricula',
    //             'u0.id as id_usuario', 
    //             'u_p.value as student',
    //         ])
    //         ->distinct('id')
    //         ->where('art_requests.deleted_by', null) 
    //         ->where('art_requests.deleted_at', null) 
    //         ->groupBy('u_p.value')
    //         ->distinct('id')
    //         ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);

    //         // return Datatables::eloquent($model)->toJson();

    //         return Datatables::eloquent($model)
    //             ->addColumn('mes_outubro', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_outubro',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_novembro', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_novembro',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_dezembro', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_dezembro',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_janeiro', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_janeiro',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_fevereiro', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_fevereiro',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_marco', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_marco',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_abril', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_abril',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_maio', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_maio',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_junho', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_junho',compact('item','getArtclis_estudent'));
    //             })
    //             ->addColumn('mes_julho', function($item)use($getArtclis_estudent) { 
    //                 return view('Avaliations::avaliacao-aluno.datatables_meses.mes_julho',compact('item','getArtclis_estudent'));
    //             })
    //             // ->rawColumns(['mes_outubro','mes_novembro','mes_dezembro','mes_janeiro','mes_fevereiro','mes_marco','mes_abril','mes_maio','mes_junho','mes_julho'])
    //             ->addIndexColumn()
    //             ->toJson();
    //         } catch (Exception | Throwable $e) {
    //             // logError($e);
    //             return response()->json($e);
    //             return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    //         }
    // }


    // // -------------------------------
    // private function getEmolumentoEstudent($lectiveYearSelected,$students)
    // {


    //         if ($students->isEmpty()){
    //             return null;
    //         }else{ 
    //           $usuarios_id=collect($students)
    //           ->groupBy('user_id')
    //           ->map(function($item,$key){
    //             return $item[0]->user_id;
    //           });  


    //           //return $usuarios_id;
    //           //Pegar o ano lectivo 
    //           $currentData = Carbon::now();
    //           $lectiveYearSelected = DB::table('lective_years')
    //               ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
    //               ->first(); 

    //           //-----------------------------------------------------------------------//

    //         $consultArt = DB::table('articles as art')




    //         ->leftJoin('article_translations as at', function ($join) {
    //             $join->on('art.id', '=', 'at.article_id');
    //             $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //             $join->on('at.active', '=', DB::raw(true));
    //         })
    //         ->leftJoin('article_requests as article_ret', function ($join) {
    //             $join->on('art.id', '=', 'article_ret.article_id');
    //         })
    //         ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
    //             $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
    //         })

    //         ->leftJoin('transactions as tran', function ($join) {
    //             $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
    //         })

    //         ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
    //             $join->on('tran.id', '=', 'trant_receipts.transaction_id');
    //         })
    //         ->leftJoin('historic_user_balance as historic_saldo',function ($join){
    //             $join->on('tran.id','=','historic_saldo.id_transaction');
    //         })
    //         ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
    //         ->leftJoin('user_parameters as u_p', function ($join) {
    //             $join->on('u_p.users_id', '=', 'article_ret.user_id')
    //             ->where('u_p.parameters_id', '=', 1);
    //         })
    //         ->select([
    //             'u_p.value as name_student',
    //             'article_ret.id as article_req_id',
    //             'article_ret.user_id as user_id',
    //             'tran.id as transaction_id',
    //             'historic_saldo.valor_credit as valor_credit',
    //             'at.display_name as article_name',
    //             'article_ret.year as article_year',
    //             'article_ret.month as article_month',
    //             'article_ret.base_value as base_value',
    //             'article_ret.extra_fees_value as extra_fees_value',
    //             'article_ret.status as status',
    //             'article_ret.discipline_id as art_idDisciplina',
    //             'article_ret.meta as meta',
    //             'trant_receipts.created_at as created_at_arti',
    //             'tran.data_from as data_from',
    //             'trant_receipts.code as code'
    //         ])
    //         ->whereIn('article_ret.user_id',$usuarios_id)
    //         ->whereIn('code_dev.code', ["propina"])
    //         ->whereNull('article_ret.deleted_at')
    //         ->whereNull('article_ret.deleted_by')
    //         ->whereNull('tran.deleted_at')
    //         ->where('tran.type','!=','debit')
    //         ->orderBy('article_ret.status', 'ASC')
    //         ->orderBy('article_ret.year', 'ASC')
    //         ->orderBy('article_ret.month', 'ASC')
    //         ->orderBy('tran.id', 'DESC')
    //         ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
    //     ->get();

    //     $collet=collect($consultArt)->map(function($item){
    //         return $item->article_req_id;
    //     });


    // }
    // $consultRecibos = DB::table('articles as art')
    //     ->leftJoin('article_translations as at', function ($join) {
    //         $join->on('art.id', '=', 'at.article_id');
    //         $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //         $join->on('at.active', '=', DB::raw(true));
    //     })
    //     ->leftJoin('article_requests as article_ret', function ($join) {
    //         $join->on('art.id', '=', 'article_ret.article_id');
    //     })
    //     ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
    //         $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
    //     })

    //     ->leftJoin('transactions as tran', function ($join) {
    //         $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
    //     })

    //     ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
    //         $join->on('tran.id', '=', 'trant_receipts.transaction_id');
    //     })
    //     ->leftJoin('historic_user_balance as historic_saldo',function ($join){
    //         $join->on('tran.id','=','historic_saldo.id_transaction');
    //     })
    //     ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
    //     ->leftJoin('user_parameters as u_p', function ($join) {
    //         $join->on('u_p.users_id', '=', 'article_ret.user_id')
    //         ->where('u_p.parameters_id', '=', 1);

    //     })
    //     ->select([
    //     'u_p.value as name_student',
    //     'article_ret.id as article_req_id',
    //     'article_ret.user_id as user_id',
    //     'tran.id as transaction_id',
    //     'historic_saldo.valor_credit as valor_credit',
    //     'at.display_name as article_name',
    //     'article_ret.year as article_year',
    //     'article_ret.month as article_month',
    //     'article_ret.base_value as base_value',
    //     'article_ret.discipline_id as art_idDisciplina',
    //     'article_ret.meta as meta',
    //     'article_ret.extra_fees_value as extra_fees_value',
    //     'article_ret.status as status',
    //     'tran.data_from as data_from',
    //     'trant_receipts.code as code'
    //     ])
    //     // ->whereIn('article_ret.user_id',[1086,3099])
    //     ->where('tran.type', '=', 'debit')
    //     ->whereIn('code_dev.code', ["propina"])
    //     ->whereNull('article_ret.deleted_at')
    //     ->whereNull('article_ret.deleted_by')
    //     ->whereNull('tran.deleted_at')
    //     ->whereNotin('trans_artic_req.article_request_id',$collet) 
    //     ->orderBy('article_ret.status', 'ASC')
    //     ->orderBy('article_ret.year', 'ASC')
    //     ->orderBy('article_ret.month', 'ASC')
    //     ->orderBy('tran.id', 'DESC')
    //     ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
    // ->get();
    // return $model= $consultArt->merge($consultRecibos);



    // }

    // -----------------------------------------------------







    // TRAZ AS PROPINAS DOS ESTUDANTES
    // private function getEmolumentoEstudent($lectiveYearSelected)
    // {
    //         $consultArt = DB::table('articles as art')
    //         ->leftJoin('article_translations as at', function ($join) {
    //             $join->on('art.id', '=', 'at.article_id');
    //             $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //             $join->on('at.active', '=', DB::raw(true));
    //         })
    //         ->leftJoin('article_requests as article_ret', function ($join) {
    //             $join->on('art.id', '=', 'article_ret.article_id');
    //         })
    //         ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
    //             $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
    //         })

    //         ->leftJoin('transactions as tran', function ($join) {
    //             $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
    //         })

    //         ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
    //             $join->on('tran.id', '=', 'trant_receipts.transaction_id');
    //         })
    //         ->leftJoin('historic_user_balance as historic_saldo',function ($join){
    //             $join->on('tran.id','=','historic_saldo.id_transaction');
    //         })
    //         ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
    //         ->select([
    //             'article_ret.id as article_req_id',
    //             'article_ret.user_id as user_id',
    //             'tran.id as transaction_id',
    //             'historic_saldo.valor_credit as valor_credit',
    //             'at.display_name as article_name',
    //             'article_ret.year as article_year',
    //             'article_ret.month as article_month',
    //             'article_ret.base_value as base_value',
    //             'article_ret.extra_fees_value as extra_fees_value',
    //             'article_ret.status as status',
    //             'article_ret.discipline_id as art_idDisciplina',
    //             'article_ret.meta as meta',
    //             'trant_receipts.created_at as created_at_arti',
    //             'tran.data_from as data_from',
    //             'trant_receipts.code as code'
    //         ])

    //         ->whereNull('article_ret.deleted_at')
    //         ->where('tran.type','!=', 'debit')
    //         ->whereIn('code_dev.code', ["confirm","propina"])
    //         ->orderBy('article_ret.year', 'ASC')
    //         ->orderBy('article_ret.month', 'ASC')
    //         ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
    //     ->get();
    //     $collet=collect($consultArt)->map(function($item){
    //         return $item->article_req_id;
    //     });



    //     $consultRecibos = DB::table('articles as art')
    //         ->leftJoin('article_translations as at', function ($join) {
    //             $join->on('art.id', '=', 'at.article_id');
    //             $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
    //             $join->on('at.active', '=', DB::raw(true));
    //         })
    //         ->leftJoin('article_requests as article_ret', function ($join) {
    //             $join->on('art.id', '=', 'article_ret.article_id');
    //         })
    //         ->leftJoin('transaction_article_requests as trans_artic_req', function ($join) {
    //             $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
    //         })

    //         ->leftJoin('transactions as tran', function ($join) {
    //             $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
    //         })

    //         ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
    //             $join->on('tran.id', '=', 'trant_receipts.transaction_id');
    //         })
    //         ->leftJoin('historic_user_balance as historic_saldo',function ($join){
    //             $join->on('tran.id','=','historic_saldo.id_transaction');
    //         })
    //         ->join("code_developer as code_dev",'code_dev.id','art.id_code_dev')
    //         ->select([
    //         'article_ret.id as article_req_id',
    //         'article_ret.user_id as user_id',
    //         'tran.id as transaction_id',
    //         'historic_saldo.valor_credit as valor_credit',
    //         'at.display_name as article_name',
    //         'article_ret.year as article_year',
    //         'article_ret.month as article_month',
    //         'article_ret.base_value as base_value',
    //         'article_ret.discipline_id as art_idDisciplina',
    //         'article_ret.meta as meta',
    //         'article_ret.extra_fees_value as extra_fees_value',
    //         'article_ret.status as status',
    //         'tran.data_from as data_from',
    //         'trant_receipts.code as code'
    //         ])
    //         ->where('tran.type', '=', 'debit')
    //         ->whereIn('code_dev.code', ["confirm","propina"])
    //         ->whereNull('article_ret.deleted_at')
    //         ->whereNotin('trans_artic_req.article_request_id',$collet) 
    //         ->orderBy('article_ret.year', 'ASC')
    //         ->orderBy('article_ret.month', 'ASC')
    //         ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
    //     ->get();
    //     return $model= $consultArt->merge($consultRecibos);
    // }











    public function gerentePDF_pautaFinal()
    {
        //VARIAVEIS
        $ano_lectivo = 7;
        $id_turma = 158;
        $valor_oa = 2;
        $metrica = 110;
        $id_disciplina = 139;


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
            ->leftJoin('tmp_oa as oa', function ($join) {
                $join->on('oa.metricas_id', '=', 'mt.id');
                $join->on('oa.discipline_id', '=', 'dp.id');
            })

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
            // ->leftJoin('avaliacao_aluno_historicos', 'avaliacao_aluno_historicos.plano_estudo_avaliacaos_id', '=', 'plano_estudo_avaliacaos.id')
            // ->join('calendario_prova as c_p','c_p.id_avaliacao' ,'=','avl.id')
            ->select([
                'ct.display_name as nome_courso',
                'dt.display_name as nome_disciplina',
                'full_name.value as nome_aluno',
                'avl_aluno.nota as nota_aluno',
                'stpeid.course_year as ano',
                'up_meca.value as code_aluno',
                'dt.discipline_id as id_disciplina',
                'avl_aluno.metricas_id as metrica',
                'avl.nome as metrica_nome',
                'oa.grade as nota_aluno',
                'oa.metricas_id as metri',
                'oa.oa_number as oa_number',
                'oa.discipline_id as disc'
            ])
            /*
                ->where('dp.id', 11)
                ->where('mt.deleted_by', null)
                ->where('avl_aluno.id_turma',158)
                ->where('stp.courses_id', 110)    */
            ->where('stpeid.lective_years_id', $ano_lectivo)
            ->distinct('matriculations.code')
            ->where('oa.class_id', $id_turma)
            ->where('mt.id', $metrica)
            ->where('oa.discipline_id', $id_disciplina)

            ->get();


        //PEGA O ANO LECTIVO
        $lectiveYear = DB::table('lective_year_translations')
            ->select('lective_year_translations.display_name')
            ->where('lective_year_translations.lective_years_id', $ano_lectivo)
            ->where('lective_year_translations.active', 1)
            ->get();


        //PEGA O REGIME DA DISCIPLINA        
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
        ])->where('id', $avaliacaos_student[0]->id_disciplina)->firstOrFail();

        //PEGA OS DADOS DA TURMA
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
        /*   
        $metric = PlanoEstudoAvaliacao::leftJoin('study_plan_editions as stpeid', 'stpeid.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')                    
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
            ->select([
                'oa.*',
            ])
            ->where('mt.id', $metrica)  
            ->where('oa.discipline_id', $id_disciplina)          
            ->get();                
       */
        //dd($avaliacaos_student[0], $metric);


        $data = [
            'avaliacaos_student' => $avaliacaos_student,
            'lectiveYear' => $lectiveYear,
            'discipline' => $discipline,
            'turma' => $turma
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
        $pdf->setPaper('a4', 'landscape');

        $footer_html = view()->make('Reports::partials.enrollment-income-footer')->render();
        $pdf->setOption('footer-html', $footer_html);
        return  $pdf->stream('Pauta Final' . '.pdf');
    }

    public function unpublishedGrades()
    {
        try {

           $p = DB::table('publicar_pauta as pp')
           ->where('pp.estado',1)
           ->where('pp.tipo',30)
           ->where('pp.id_ano_lectivo',9)
           ->join('disciplines','disciplines.id','pp.id_disciplina')
           ->join('classes','classes.id','pp.id_turma')
           ->select(['pp.id_turma','pp.id_disciplina',
                    'disciplines.code as disciplina',
                    'classes.display_name as turma'])
                            ->get();

            $pautas = collect();
            $p->each(function($item)use($pautas){

                $model = DB::table('matriculation_classes as mc')
                ->join('matriculations as m', 'mc.matriculation_id', '=', 'm.id')
                ->join('new_old_grades as nog', 'm.user_id', '=', 'nog.user_id')
                ->join('matriculation_disciplines as md', 'md.matriculation_id', '=', 'm.id')
                ->where('mc.class_id', $item->id_turma)
                ->whereNull('m.deleted_at')
                ->where('nog.discipline_id', $item->id_disciplina)
                ->where('md.discipline_id', $item->id_disciplina)
                ->select('nog.id')
                ->get();

                if($model->isEmpty())
                $pautas->push($item->turma. '-' . $item->disciplina);
            });

           
                     dd($pautas);
           return json_encode(["pautas"=> $pautas->chunk(round($pautas->count()/2))[0]]);

         
        } catch (Exception | Throwable $e) {
           return $e;
        }
    }
}
