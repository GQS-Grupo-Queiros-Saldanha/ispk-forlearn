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

class  AvaliacaoAlunoFinalistaController extends Controller
{

    /**
     * Display a listing of the resource.
     * Controller criadao Por gelson Matias
     * Pauto final  
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

        

            $currentData = Carbon::now();
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $getMetrica = DB::table('metricas as metrica')
                ->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'metrica.avaliacaos_id')
                ->select([
                    'metrica.nome as nome',
                    'metrica.id as id_metrica'
                ])
                ->whereIn('metrica.code_dev', ['Trabalho', 'Defesa'])
                ->whereNull('metrica.deleted_at')
                ->whereNull('metrica.deleted_by')
                ->where('avaliacao.anoLectivo', $lectiveYearSelected->id)
                ->get(); 
            
            

            $data = [
                'action' => 'create',
                // 'languages' => Language::whereActive(true)->get(),
                'courses' => $courses,
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id,
                'getMetrica' => $getMetrica
            ];
            return view("Avaliations::avaliacao-finalista.index")->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function getEstudent_finalist_courso($id_curso, $id_anolectivo, $id_metrica)
    {
        //return $id_metrica;
        
        $getstundets_finalist = DB::table('matriculation_finalist as matricula_finalist')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'matricula_finalist.user_id')
                    ->where('name_full.parameters_id', 1);
            })
            ->leftJoin('avaliacao_alunos as avaliacao_aluno', function ($q) use ($id_metrica) {
                $q->on('avaliacao_aluno.users_id', '=', 'matricula_finalist.user_id')
                    ->where('avaliacao_aluno.metricas_id', $id_metrica);
            })
            ->select([
                'matricula_finalist.id as id_finalista',
                'matricula_finalist.user_id as user_id',
                'matricula_finalist.num_confirmaMatricula',
                'avaliacao_aluno.nota as nota',
                'avaliacao_aluno.id as id_avaliacao',
                'name_full.value as name_student'   
            ])
            // ->distinct('matricula_finalist.id')
            ->where('matricula_finalist.year_lectivo', $id_anolectivo)
            ->where('matricula_finalist.id_curso', $id_curso)
            ->whereNull('matricula_finalist.deleted_by')
            ->whereNull('matricula_finalist.deleted_at')
            ->get();
 
            $data = [ 'data' => $getstundets_finalist ];
            return response()->json($data);
    }


    public function getMetrica_lective_year($id_anolectivo){
        return DB::table('metricas as metrica')
        ->join('avaliacaos as avaliacao', 'avaliacao.id', '=', 'metrica.avaliacaos_id')
        ->select([
            'metrica.nome as nome',
            'metrica.id as id_metrica'
        ])
        ->whereIn('metrica.code_dev', ['Trabalho', 'Defesa'])
        ->whereNull('metrica.deleted_at')
        ->whereNull('metrica.deleted_by')
        ->where('avaliacao.anoLectivo',$id_anolectivo)
        ->get(); 
    }    
    
    
    

    
    public function notaAvaliacaoFinalista(Request $request)  {
        try {
            // return $request;
            DB::beginTransaction();
            $dataArray = array_keys((array)$request->nota);
            foreach ($dataArray as $item) {
                // return $request->nota[$item][0];
                $explode = explode('/', $item);
                $nota = $request->nota[$item][0];
                $id_student = $explode[0];
                $id_avaliacaoNota = $explode[1];
                $metrica = $request->metrica;

                if ($id_avaliacaoNota != 'null') {
                    $getAvalicaoStudent = DB::table('avaliacao_alunos')
                        ->whereNull('avaliacao_alunos.deleted_by')
                        ->whereNull('avaliacao_alunos.deleted_at')
                        ->where('avaliacao_alunos.id', $id_avaliacaoNota)
                        ->first();
                    $id_avaliacao = $getAvalicaoStudent->id;
                    $this->editarNotaValiacao($id_student, $nota, $metrica, $id_avaliacao);
                } else {
                    //returno para validar
                    $lancamento = $this->newNotaAvaliacao($id_student, $nota, $metrica);
                    if ($lancamento == 1) {
                        Toastr::warning(__('Não foi encontrado nenhuma edição de plano de estudo do último ano curricular deste curso !'), __('toastr.warning'));
                        return redirect()->back();
                    } elseif ($lancamento == 2) {
                        Toastr::warning(__('Atenção! a forLEARN detectou que a disciplina trabalho de trabalho de final de curso não está associada com a avaliação, por favor verifique a edição de plano de estudo do respectivo ano lectivo!'), __('toastr.warning'));
                        return redirect()->back();
                    }
                }
            }

            DB::commit();
            Toastr::success(__('Nota lançada com sucesso'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {

            return $e;

            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    private function editarNotaValiacao($id_student, $nota, $metrica, $id_avaliacao)
    {
        $update = DB::table('avaliacao_alunos')
            ->where('id', $id_avaliacao)
            ->update([
                'nota' => $nota,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => Carbon::Now(),
                'updated_at' => Carbon::Now(),
                'presence' => null,
                'id_turma' => 161,
                'metricas_id' => $metrica
            ]);
    }

    private function newNotaAvaliacao($id_student, $nota, $metrica)
    {
        $getMatriculationFinalista = DB::table('matriculation_finalist as matricula_finalist')
            ->join('matriculations as matriculation', 'matriculation.user_id', '=', 'matricula_finalist.user_id')
            ->where('matricula_finalist.user_id', $id_student)
            ->orderBy('matriculation.id', 'DESC')
            ->whereNull('matricula_finalist.deleted_by')
            ->whereNull('matricula_finalist.deleted_at')
            ->first();

        // dd($getMatriculationFinalista->id_curso);


        $getStudy_plan = DB::table('study_plans as study_plan')
            ->join('study_plan_editions as study_plan_edition', 'study_plan_edition.study_plans_id', '=', 'study_plan.id')
            ->join('study_plan_edition_disciplines as study_plan_edition_discipline', 'study_plan_edition_discipline.study_plan_edition_id', '=', 'study_plan_edition.id')
            ->join('disciplines_translations as disciplines_trans', function ($q) {
                $q->on('disciplines_trans.discipline_id', '=', 'study_plan_edition_discipline.discipline_id')
                    ->where('disciplines_trans.active', 1)
                    ->where('disciplines_trans.language_id', 1);
            })
            ->where('study_plan.courses_id', $getMatriculationFinalista->id_curso)
            ->where('study_plan_edition.lective_years_id', $getMatriculationFinalista->year_lectivo)
            ->where('study_plan_edition.course_year', $getMatriculationFinalista->year_curso)
            // ->where('study_plan_edition.lective_years_id', $getMatriculationFinalista->year_lectivo)
            ->where('disciplines_trans.display_name', 'LIKE', '%Trabalho de Fim%')
            ->select([
                'study_plan_edition.id as  id_planEdition',
                'disciplines_trans.display_name as  display_name',
                'disciplines_trans.discipline_id as  id_disciplina',
                'study_plan.id as  id_study_plan',
                'study_plan_edition.*'
            ])->distinct('study_plan.id')
            //->orderBy('study_plan_edition_discipline.discipline_id','ASC')
            ->first();



        if (isset($getStudy_plan->id_planEdition)) {


            $getPlanoEstudo_avaliacao = DB::table('plano_estudo_avaliacaos as plano_estudo_avaliacao')
                ->where('plano_estudo_avaliacao.study_plan_editions_id', $getStudy_plan->id_planEdition)
                ->where('plano_estudo_avaliacao.disciplines_id', $getStudy_plan->id_disciplina)
                ->first();


            if (isset($getPlanoEstudo_avaliacao->id)) {

                DB::table('avaliacao_alunos')->updateOrInsert(
                    [
                        'plano_estudo_avaliacaos_id' => $getPlanoEstudo_avaliacao->id,
                        'users_id' => $id_student,
                        'metricas_id' => $metrica
                    ],
                    [
                        'nota' => $nota,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => Carbon::Now(),
                        'updated_at' => Carbon::Now(),
                        'presence' => null,
                        'id_turma' => 161,

                    ]
                );

                return 0;
                //Este retorno significa que está tudo bem lançou a nota
            } else {
                return 2; //Este retorno é porque a disciplina não está adicionada ao plano de estudo e avaliação. 
            }
        } else {

            //Este retorno indica que não foi encontrado nenhuma edição de plano de estudo este ano lectivo da matrícula do aluno
            return 1;
        }
        //fim do else 

    }
}
