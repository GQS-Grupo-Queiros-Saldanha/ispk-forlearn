<?php

namespace App\Modules\Users\Controllers;

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

use PDF;
use App\Model\Institution;

class estatisticaMatriculationController extends Controller
{

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            $courses = Course::with(['currentTranslation'])->get();

            $Pauta = DB::table('tb_estatistic_avaliation')
                ->select(['pautaType as PautaCode', 'descrition_type_p as NamePauta'])
                ->distinct()
                ->get();

            $data = [
                'courses' => $courses,
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'Pautas' => $Pauta
            ];



            return view("Users::estatistica-matriculation.index")->with($data);
        } catch (Exception | Throwable $e) {

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function relatorios(){

        $courses = Course::with([
            'currentTranslation'
        ])
            ->where('id', '!=', 22)
            ->where('id', '!=', 18);


        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
            

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 9;

        $data = [
            'courses' => $courses->get(),
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears
        ];
        //dd($data);
        return view("Users::matriculations.relatorios")->with($data);
    }

    public function relatoriosPDF(Request $request){

        //dd($request);
       
        $tranf_type = 'payment';

        $lectiveYears = DB::table('lective_years')
            ->where("id", $request->lective_year)
            ->first();
        //dd($lectiveYears);

        $lt = DB::table('lective_year_translations')
            ->where("lective_years_id", $request->lective_year)
            ->first();

        $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($lectiveYears->id);
        //dd($emolumento_confirma_prematricula);

        $new_model = DB::table('matriculations')
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
            ->leftJoin('user_parameters as up_bi', function ($join) {
                $join->on('u0.id', '=', 'up_bi.users_id')
                    ->where('up_bi.parameters_id', 14);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('u0.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 2);
            })
            ->join('article_requests as art_requests', 'art_requests.user_id', '=', 'matriculations.user_id')
            ->join('articles', function ($join) {
                $join->on('art_requests.article_id', '=', 'articles.id')
                    ->whereNull('articles.deleted_by')
                    ->whereNull('articles.deleted_at');
            })
            ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
            ->where('matriculations.lective_year', $lectiveYears->id)
            ->where('matriculations.deleted_at', null)
            ->select([
                'matriculations.*',

            ])
            //->groupBy('id_usuario')
            ->distinct('matriculations.id')
            ->get();
            
            dd($new_model);


        $mod = DB::table('matriculations')
            ->join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->join('article_requests as art_requests', 'art_requests.user_id', '=', 'matriculations.user_id')
            ->join('articles', function ($join) {
                $join->on('art_requests.article_id', '=', 'articles.id')
                    ->whereNull('articles.deleted_by')
                    ->whereNull('articles.deleted_at');
            })
            ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula)
            ->where('matriculations.lective_year', $lectiveYears->id)
            ->where('art_requests.status', 'total')
            ->select([
                'art_requests.id as id',
                'articles.id_code_dev as id_code_dev',
                'matriculations.deleted_by as deleted_by',
                'matriculations.created_at as created_at',
                'matriculations.created_by as created_by',
                'matriculations.id as m_id'
            ])
            ->distinct('matriculations.id')
            ->get();


        if ($new_model->isEmpty()) {
            Toastr::warning(__('A forLEARN não detectou nenhuma matrícula neste ano lectivo'), __('toastr.warning'));
            return redirect()->back();
        }

        $mode = [];

        foreach ($mod as $item) {
            // Verifica se o item ainda não está no array usando o campo 'id'
            $exists = array_filter($mode, function ($existingItem) use ($item) {
                return $existingItem->m_id == $item->m_id;
            });

            if (empty($exists)) {
                // Adiciona o item ao array
                $mode[] = $item;
            }
        }




        $new_model = collect($new_model)->map(function ($item) {
            if ($item->sexo == "Feminino" || $item->sexo == 125 || $item->sexo == "feminino" || $item->sexo == "f" || $item->sexo == "F")
                $item->sexo = 'F';

            if ($item->sexo == "Masculino" || $item->sexo == 124 || $item->sexo == "masculino" || $item->sexo == "m" || $item->sexo == "M")
                $item->sexo = 'M';

            return $item;
        });
        $total_matriculas = count($new_model);

        $datas = collect($mode)->groupBy(
            function ($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item->created_at->format('Y-m-d');
            }
        );




        $datas_inscricao = $datas->map(function ($candidato) {
            $estatisticas = ["matriculas" => 0];
            $p_total = 0;
            foreach ($candidato as $item) {

                $estatisticas["matriculas"] = ++$p_total;
            }

            return $estatisticas;
        });

        $total_matriculas = count($mode);

        $matriculas_staff = collect($mode)->groupBy('created_by')->map(
            function ($candidato) use ($total_matriculas) {

                $estatisticas = ["matriculas" => 0, "percentagem" => 0];
                $p_total = 0;

                foreach ($candidato as $item) {

                    $estatisticas["matriculas"] = ++$p_total;
                }

                $estatisticas["percentagem"] = round((($p_total / $total_matriculas) * 100), 2);

                return $estatisticas;
            }
        );
        $keys = array_keys($matriculas_staff->toArray());

        for ($i = 0; $i < count($keys); $i++) {
            $staff[$i] = DB::table('user_parameters')
                ->where('parameters_id', 1)
                ->where('users_id', $keys[$i])
                ->select(['users_id', 'value as nome'])
                ->first();

            $nome = explode(" ", $staff[$i]->nome);
            $staff[$i]->nome = $nome[0] . " " . $nome[count($nome) - 1];
        }

        // resumo das matriculas


        $cf_matriculas = [
            "m_iniciadas" => 0,
            "m_eliminadas" => 0,
            "m_n_concluidas" => 0
        ];

        $p_matriculas = [
            "m_iniciadas" => 0,
            "m_eliminadas" => 0,
            "m_n_concluidas" => 0
        ];



        foreach ($mode as $item) {

            if ($item->id_code_dev === 3) {

                $p_matriculas["m_iniciadas"] += 1;

                if (isset($item->deleted_by))
                    $p_matriculas["m_eliminadas"] = $p_matriculas["m_eliminadas"] + 1;
            } elseif ($item->id_code_dev === 1) {
                $cf_matriculas["m_iniciadas"] += 1;

                if (isset($item->deleted_by))
                    $cf_matriculas["m_eliminadas"] = $cf_matriculas["m_eliminadas"] + 1;

            }


        }



        foreach ($new_model as $item) {

            if ($item->id_code_dev == 3) {

                if ($item->state == 'pending' && !isset($item->deleted_by))
                    $p_matriculas["m_n_concluidas"] += 1;
            }

            if ($item->id_code_dev == 1) {

                if ($item->state == 'pending' && !isset($item->deleted_by))
                    $cf_matriculas["m_n_concluidas"] += 1;

            }
        }



        $courses = DB::table('courses as curso')
            ->whereNotIn('curso.id', [8])
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'curso.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->leftjoin('department_translations as dpt', 'dpt.departments_id', '=', 'curso.departments_id')
            ->where([
                ['dpt.language_id', '=', 1],
                ['dpt.active', '=', 1],
                ['dpt.deleted_at', '=', null],
            ])
            ->whereNull('curso.deleted_at')
            ->whereNull('ct.deleted_at')
            ->orderBy("name")
            ->select([
                "curso.id",
                "ct.display_name as name",
                "curso.departments_id as departament",
                "dpt.display_name as departamento"
            ])
            ->get();



        // Primeiro, vamos fazer um diagnóstico detalhado dos dados
        function diagnosticEnrollmentData($new_model, $courses)
        {
            $diagnostics = [
                'total_registros' => $new_model->count(),
                'alunos_unicos' => $new_model->unique('id_usuario')->count(),
                'por_estado' => $new_model->groupBy('state')->map->count(),
                'por_curso' => [],
                'registros_invalidos' => []
            ];

            // Analisar cada registro
            foreach ($new_model as $registro) {
                if (!isset($diagnostics['por_curso'][$registro->id_course])) {
                    $diagnostics['por_curso'][$registro->id_course] = [
                        'total' => 0,
                        'validos' => 0,
                        'invalidos' => 0,
                        'por_estado' => []
                    ];
                }

                $diagnostics['por_curso'][$registro->id_course]['total']++;

                // Verificar condições de validade
                if ($registro->state == 'total' && !isset($registro->deleted_at)) {
                    $diagnostics['por_curso'][$registro->id_course]['validos']++;
                } else {
                    $diagnostics['por_curso'][$registro->id_course]['invalidos']++;
                    $diagnostics['registros_invalidos'][] = [
                        'id_usuario' => $registro->id_usuario,
                        'curso' => $registro->id_course,
                        'estado' => $registro->state,
                        'deleted_at' => $registro->deleted_at
                    ];
                }

                // Contar por estado
                if (!isset($diagnostics['por_curso'][$registro->id_course]['por_estado'][$registro->state])) {
                    $diagnostics['por_curso'][$registro->id_course]['por_estado'][$registro->state] = 0;
                }
                $diagnostics['por_curso'][$registro->id_course]['por_estado'][$registro->state]++;
            }

            return $diagnostics;
        }
        
        $usuarios = [];
        // Agora vamos corrigir o código de contagem
        $confirmados = collect($courses)->groupBy("name")->map(function ($cursos) use ($new_model, $usuarios) {
            $estatisticas = [
                "ano1" => 0,
                "ano2" => 0,
                "ano3" => 0,
                "ano4" => 0,
                "ano5" => 0,
                "dep" => $cursos[0]->departament,
                "curso" => $cursos[0]->name,
                "r1" => ["m" => 0, "t" => 0, "n" => 0, "sm" => ["m" => 0, "f" => 0], "st" => ["m" => 0, "f" => 0], "sn" => ["m" => 0, "f" => 0]],
                "r2" => ["m" => 0, "t" => 0, "n" => 0, "sm" => ["m" => 0, "f" => 0], "st" => ["m" => 0, "f" => 0], "sn" => ["m" => 0, "f" => 0]],
                "r3" => ["m" => 0, "t" => 0, "n" => 0, "sm" => ["m" => 0, "f" => 0], "st" => ["m" => 0, "f" => 0], "sn" => ["m" => 0, "f" => 0]],
                "r4" => ["m" => 0, "t" => 0, "n" => 0, "sm" => ["m" => 0, "f" => 0], "st" => ["m" => 0, "f" => 0], "sn" => ["m" => 0, "f" => 0]],
                "r5" => ["m" => 0, "t" => 0, "n" => 0, "sm" => ["m" => 0, "f" => 0], "st" => ["m" => 0, "f" => 0], "sn" => ["m" => 0, "f" => 0]]
            ];

            // Filtrar e agrupar estudantes válidos do curso
            $courseStudents = $new_model
                ->filter(function ($item) use ($cursos) {
                    return $item->id_course == $cursos[0]->id
                        && $item->state == 'total';   // Garantir que tem sexo definido
                })
                ->groupBy('id_usuario')
                ->map(function ($studentRecords) {
                    // Pegar o registro mais recente do aluno
                    return $studentRecords->sortByDesc('created_at')->first();
                });
              
            // Processar cada aluno único
           foreach ($courseStudents as $enrollment) {
            $year = $enrollment->course_year;
            $yearKey = "ano{$year}";
            $rKey = "r{$year}";

            // Incrementar contagem do ano
            $estatisticas[$yearKey]++;

            // Processar turno usando regex
            if (preg_match('/([MTN])/', $enrollment->classe, $matches)) {
                $shift = $matches[1]; // Extrai M, T ou N
            } else {
                $shift = null; // fallback caso não consiga extrair
            }

            // Determinar o turno principal
            if ($shift === 'M') {
                $estatisticas[$rKey]['m']++;
                if ($enrollment->sexo == 'M') $estatisticas[$rKey]['sm']['m']++;
                if ($enrollment->sexo == 'F') $estatisticas[$rKey]['sm']['f']++;
            } elseif ($shift === 'T') {
                $estatisticas[$rKey]['t']++;
                if ($enrollment->sexo == 'M') $estatisticas[$rKey]['st']['m']++;
                if ($enrollment->sexo == 'F') $estatisticas[$rKey]['st']['f']++;
            } elseif ($shift === 'N') {
                $estatisticas[$rKey]['n']++;
                if ($enrollment->sexo == 'M') $estatisticas[$rKey]['sn']['m']++;
                if ($enrollment->sexo == 'F') $estatisticas[$rKey]['sn']['f']++;
            }
        }

        
            return $estatisticas;
        });
       
       
        $total_new_model = $new_model->unique('id_usuario')->count();
        $total_confirmados = 0;

        foreach ($confirmados as $curso => $dados) {
            for ($i = 1; $i <= 5; $i++) {
                $total_confirmados += $dados["ano$i"];
            }
        }
        $departamentos = collect($courses)->groupBy("departamento")->map(function ($departamentos) use ($confirmados) {

            $cursos = [];
            foreach ($confirmados as $item) {
                if ($departamentos[0]->departament == $item["dep"]) {
                    array_push($cursos, $item);
                }
            }
            return $cursos;

        });


        $institution = Institution::latest()->first();
        $cordenador = isset($cordenador->value) ? ($cordenador->value) : "";
        $titulo_documento = "Relatório: Confirmação de Matrículas";
        $anoLectivo_documento = "Ano ACADÊMICO: ";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 5;
        $logotipo = "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo;
        $date_generated = date("Y/m/d");

        // Debug para ver a estrutura completa
        /*dd([
            'confirmados' => $confirmados,
            'courses' => $courses,
            'departamentos' => $departamentos,
            'datas_inscricao' => $datas_inscricao,
            'matriculas_staff' => $matriculas_staff,
            'total_matriculas' => $total_matriculas,
            'staff' => $staff,
            'p_matriculas' => $p_matriculas,
            'cf_matriculas' => $cf_matriculas,
        ]);*/

        $pdf = PDF::loadView(
            "Users::matriculations.pdf-relatorios",
            // "Users::matriculations.pdf-relatorios",
            compact(

                'cordenador',
                'lectiveYears',
                'lt',
                'institution',
                'titulo_documento',
                'anoLectivo_documento',
                'documentoGerado_documento',
                'documentoCode_documento',
                'date_generated',
                'logotipo',
                'confirmados',
                'courses',
                'departamentos',
                'datas_inscricao',
                'matriculas_staff',
                'total_matriculas',
                'staff',
                'p_matriculas',
                'cf_matriculas'
            )
        );

        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '1mm');
        $pdf->setOption('margin-right', '2mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        $pdf_name = "Relatório_de_confirmados_( " . $lt->display_name . " )";

        // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name . '.pdf');
    }


    private function pre_matricula_confirma_emolumento($lectiveYearSelected){
     
        $confirm=EmolumentCodevLective("confirm",$lectiveYearSelected)->first();
        $Prematricula=EmolumentCodevLective("p_matricula",$lectiveYearSelected)->first() ;   
        $emolumentos=[];

        if($confirm!=null){
            $emolumentos[]=$confirm->id_emolumento;
        }
        if($Prematricula!=null){
            $emolumentos[]=$Prematricula->id_emolumento;
        }
        return $emolumentos;


    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            return view("Avaliations::avaliacao-aluno.create-avaliacao-aluno");
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

        //Bem no final de lançar as notas alguém tem que fechar elas.

        try {
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return $e;
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



    public function turma_estatistica($curso,$id_anoLectivo,$anoCurricular)
    {
        // return $curso. $id_anoLectivo .$anoCurricular;
        
        $anoCurricular = explode(',',$anoCurricular);

        $turma=DB::table('classes as class')
        ->select(['class.id as id','class.display_name as turma'])
        ->where('class.courses_id',$curso)
        ->whereIn('class.year',$anoCurricular) 
        ->where('class.lective_year_id',$id_anoLectivo)
        ->whereNull('class.deleted_by')
        ->whereNull('class.deleted_at')
        ->get();


      return $turma;
        

    }


    // ===========================================  Estatísticas Matriculado ========================================


    public function generateEstatistic(Request $request)
    {
        try {

            // return $request;
            if (empty($request->classe)) {
                Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
                return redirect()->back();
            }
            $courses = DB::table('courses as curso')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'curso.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('curso.id', $request->course)
                ->get();


            //Consulta do Ano Lectivo
            $lectiveYearSelectedP = DB::table('lective_years')
                ->where('id', $request->AnoLectivo)
                ->get();

            $turma = DB::table('classes as turma')
                ->where([

                    ['turma.courses_id', '=', $request->course],
                    ['turma.lective_year_id', '=', $request->AnoLectivo],
                    // ['turma.year', '=',  $request->curricular_year],
                    ['turma.deleted_at', '=', null]

                ])
                ->whereIn('turma.id', $request->classe)
                ->whereIn('turma.year', $request->curricular_year)
                ->select([
                    'turma.id as id_turma',
                    'turma.code as code_turma',
                    'turma.display_name as nome_turma',
                ])
                ->get();


            //Vai ser a consulta geral
            $model = DB::table('matriculation_classes as mat_class')
                ->join("matriculations as mat", 'mat.id', 'mat_class.matriculation_id')
                ->join("matriculation_disciplines as mat_disc", 'mat.id', 'mat_disc.matriculation_id')
                ->join("classes as turma", function($join){
                    $join->on('mat_class.class_id', 'turma.id')
                         ->on('turma.year', 'mat.course_year');
                })
                ->join("users as user", 'mat.user_id', 'user.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('user.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('user.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('user.id', '=', 'up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                })
                ->leftJoin('user_parameters as sexo', function ($join) {
                    $join->on('user.id', '=', 'sexo.users_id')
                        ->where('sexo.parameters_id', 2);
                })
                ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
                //Os que pagaram os emolumentos de confirmação de matricula e pré-matricula
                ->join("article_requests as user_emolumento", 'user_emolumento.user_id', 'user.id')
                ->join("articles as article_emolumento", 'user_emolumento.article_id', 'article_emolumento.id')
                ->join("code_developer as code_dev", 'code_dev.id', 'article_emolumento.id_code_dev')
                ->whereIn('code_dev.code', ["confirm", "p_matricula"])
                // ->where('user_emolumento.status', "total")
                ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
                //fim dos pagos 

                ->select([
                    'user_emolumento.status as pago',
                    'article_emolumento.id as id_article',
                    'article_emolumento.code as code_article',
                    'turma.display_name as turma',
                    'turma.year as ano',
                    'user.email',
                    'mat.code',
                    'up_meca.value as matricula',
                    'up_bi.value as n_bi',
                    'u_p.value as student',
                    'turma.lective_year_id as id_anoLectivo',
                    'mat_disc.exam_only',
                    'sexo_value.code as sexo',
                    'mat.id as mat_id',
                    'mat.user_id'
                ])

                ->orderBy('student', 'ASC')
                ->distinct(['up_bi.value', 'mat.code', 'u_p.value'])
                ->whereBetween('mat.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
                ->whereIn('mat.course_year',$request->curricular_year)
                ->where("turma.lective_year_id", $request->AnoLectivo)
                ->whereIn("mat_class.class_id", $request->classe)
                ->whereNull('mat.deleted_at')
                ->where('mat_disc.exam_only', '=', 0)
                 ->whereNull('article_emolumento.deleted_at')
                ->get()->unique('mat_id');


        //    if(auth()->user()->id == 845)
        //     dd($model);

            //    Validação se for vazio a lista de alunos
            if ($model->isEmpty()) {
                Toastr::error(__('Não foram encontrado(s) aluno(s) matriculados na turma selecionada.'), __('toastr.error'));
                return redirect()->back();
            }


             $total_matriculados = collect($model)->groupBy('turma')->map(function ($item, $key) {

                $matricula = ["total" => 0, "masculino" => 0, "femenino" => 0,"ano"=>0,"turma"=>""];

                foreach ($item as $estudante) {
                    $matricula["total"] = $matricula["total"] + 1;
                    if ($estudante->sexo == "Masculino") {
                        $matricula["masculino"] = $matricula["masculino"] + 1;
                    }
                    else if ($estudante->sexo == "Feminino") {
                        $matricula["femenino"] = $matricula["femenino"] + 1;
                    }
                    else{
                       $sexo = DB::table('user_parameters')
                                ->where('users_id',$estudante->user_id)
                                ->where('parameters_id',2)
                                ->pluck('value')
                                ->first();
                     
                    }

                }
                $matricula["ano"] = $item[0]->ano;
                $matricula["turma"] = $item[0]->turma;

                return ["turma" => $matricula["turma"],"ano" => $matricula["ano"],"masculino" => $matricula["masculino"], "femenino" => $matricula["femenino"], "total" => $matricula["total"]];
            });

            $matriculados = collect($total_matriculados)->groupBy('ano')->map(function ($item, $key) {
                return $item;
            });
         
            $turmaC = $model[0]->turma;
            $curso = $courses[0]->display_name;

            
            $ano=$request->curricular_year;
            $anos=null;

            foreach ($request->curricular_year as $value) {
                $anos = $anos. $value. "º ,";
            }
            
            $anos = substr($anos, 0, -1);

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->where('id', $model[0]->id_anoLectivo)
                ->get();

            // view("Users::list-disciplines-matriculations.pdf_lista")->with($id_discipline);

            $institution = Institution::latest()->first();
            $Pauta_Name  = "MATRICULADOS";
            $anoLectivo_documento = "Ano Lectivo :";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 501;
            $logotipo = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo;

            
            $pdf = PDF::loadView("Users::estatistica-matriculation.PDF.pdf", compact(
                'model',
                'turmaC',
                'turma',
                'turma',
                'logotipo',
                'curso',
                'matriculados',
                'lectiveYears',
                'ano',
                'anos',
                'institution',
                'Pauta_Name',
                'anoLectivo_documento',
                'documentoGerado_documento',
                'documentoCode_documento'
            ));


            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setPaper('a4', 'portrait');

            $pdf_name = "Estatística_matriculados";
            // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
