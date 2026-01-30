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
use Log;
use App\Modules\Users\Controllers\MatriculationDisciplineListController;
use App\Modules\Avaliations\Events\GeneratePdfAvaliationEvent;
class PautaMacController extends Controller
{

    public function mac_pdf(Request $request, $id, $metrica_id, $study_plan_id, $avaliacao_id, $class_id, $id_anoLectivo)
    {
        try {
            // 1. Preparación de datos iniciales
            $segunda_chamada = (bool)$request->query('segunda_chamada', false);
            $accentReplacement = $this->getAccentReplacementArrays();
            
            // 2. Obtener datos principales
            $turmaInfo = $this->getTurmaInfo($class_id, $id_anoLectivo);
            $metrica = $this->getMetrica($metrica_id);
            $students = $this->getStudents($id, $metrica_id, $study_plan_id, $class_id);
            
            if ($students->isEmpty()) {
                Toastr::warning(__('Nenhuma nota lançada'), __('toastr.warning'));
                return redirect()->back();
            }
            
            // 3. Obtener datos complementarios
            $utilizadores = $this->getUsersWhoAddedGrades($id, $class_id, $metrica->code_dev, $segunda_chamada);
            $coordenadores = $this->getCoordinatorsWhoUpdatedGrades($id, $class_id, $metrica->code_dev, $segunda_chamada);
            $disciplina = $this->getDisciplinaInfo($id);
            $course = $this->getCourseInfo($study_plan_id);
            $estatisticas = $this->escala_estatistica_mac($students);
            
            // 4. Preparar datos para la vista
            $pdfData = $this->preparePdfData(
                $turmaInfo,
                $disciplina,
                $course,
                $metrica,
                $students,
                $utilizadores,
                $coordenadores,
                $estatisticas,
                $segunda_chamada,
                $accentReplacement
            );
            
            // 5. Generar PDF
            return $this->generatePdf($pdfData, $metrica, $disciplina, $turmaInfo, $accentReplacement);
            
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Obtener arrays para reemplazo de acentos
     */
    private function getAccentReplacementArrays(): array
    {
        return [
            'withAccents' => ['à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú'],
            'withoutAccents' => ['a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U']
        ];
    }

    /**
     * Obtener información de la turma
     */
    private function getTurmaInfo(int $classId, int $lectiveYearId): Collection
    {
        return DB::table('classes as turma')
            ->join('lective_years as ano', 'ano.id', 'turma.lective_year_id')
            ->leftJoin('lective_year_translations as Lectivo', function ($join) {
                $join->on('Lectivo.lective_years_id', '=', 'ano.id')
                    ->on('Lectivo.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                    ->on('Lectivo.active', '=', DB::raw(true));
            })
            ->select([
                'turma.display_name as turma',
                'turma.id as id_turma',
                'turma.year as Anocurricular',
                'Lectivo.display_name as anoLetivo'
            ])
            ->where([
                'turma.id' => $classId,
                'turma.lective_year_id' => $lectiveYearId
            ])
            ->get();
    }

/**
 * Obtener la métrica
 */
    private function getMetrica(int $metricaId): ?Metrica
    {
        return Metrica::where('id', $metricaId)->first();
    }

    /**
     * Obtener estudiantes con sus notas
     */
    private function getStudents(int $disciplinaId, int $metricaId, int $studyPlanId, int $classId): Collection
    {
        return AvaliacaoAluno::leftJoin('plano_estudo_avaliacaos as pea', 'pea.id', '=', 'avaliacao_alunos.plano_estudo_avaliacaos_id')
            ->leftJoin('matriculations as mt', 'mt.user_id', '=', 'avaliacao_alunos.users_id')
            ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'mt.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('mt.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('mt.user_id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 19);
            })
            ->where('pea.study_plan_editions_id', $studyPlanId)
            ->where('avaliacao_alunos.metricas_id', $metricaId)
            ->where('pea.disciplines_id', $disciplinaId)
            ->where('mc.class_id', $classId)
            ->where('avaliacao_alunos.id_turma', $classId)
            ->select(
                'u_p1.user_id',
                'u_p.value as nome',
                'u_p1.value as mat',
                DB::raw('MAX(avaliacao_alunos.nota) as grade')
            )
            ->groupBy('mt.user_id', 'u_p.value', 'u_p1.value')
            ->orderBy('nome', 'ASC')
            ->get();
    }

    /**
     * Obtener usuarios que añadieron las notas
     */
    private function getUsersWhoAddedGrades(int $disciplinaId, int $classId, string $metricCode, bool $segundaChamada): Collection
    {
        $query = DB::table('avaliacao_alunos as avl')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.created_by', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select([
                'avl.created_by as criado_por',
                'avl.created_at as criado_a',
                'mt.nome as metricas',
                'u_p9.value as criador_fullname',
                'plano.disciplines_id as disciplina'
            ])
            ->where('avl.id_turma', $classId)
            ->where('mt.code_dev', $metricCode)
            ->where('plano.disciplines_id', $disciplinaId);
        
        // Aplicar filtro de segunda chamada
        $query->when($segundaChamada, function ($q) {
            return $q->where('avl.segunda_chamada', 1);
        }, function ($q) {
            return $q->whereNull('avl.segunda_chamada');
        });
        
        return $query->distinct('avl.metricas_id')
            ->orderBy('avl.created_at', 'asc')
            ->get()
            ->unique('criado_por');
    }

    /**
     * Obtener coordinadores que actualizaron las notas
     */
    private function getCoordinatorsWhoUpdatedGrades(int $disciplinaId, int $classId, string $metricCode, bool $segundaChamada): Collection
    {
        $query = DB::table('avaliacao_alunos as avl')
            ->join('model_has_roles as mr', 'mr.model_id', 'avl.updated_by')
            ->join('metricas as mt', 'mt.id', 'avl.metricas_id')
            ->leftJoin('user_parameters as u_p9', function ($q) {
                $q->on('avl.updated_by', '=', 'u_p9.users_id')
                ->where('u_p9.parameters_id', 1);
            })
            ->join('plano_estudo_avaliacaos as plano', 'plano.id', 'avl.plano_estudo_avaliacaos_id')
            ->select([
                'avl.updated_by as actualizado_por',
                'avl.updated_at as actualizado_a',
                'mt.nome as metricas',
                'u_p9.value as actualizador_fullname',
                'plano.disciplines_id as disciplina'
            ])
            ->where('avl.id_turma', $classId)
            ->where('mt.code_dev', $metricCode)
            ->where('plano.disciplines_id', $disciplinaId)
            ->where('mr.role_id', 12);
        
        // Aplicar filtro de segunda chamada
        $query->when($segundaChamada, function ($q) {
            return $q->where('avl.segunda_chamada', 1);
        }, function ($q) {
            return $q->whereNull('avl.segunda_chamada');
        });
        
        return $query->distinct('avl.metricas_id')
            ->orderBy('avl.updated_at', 'asc')
            ->get();
    }

    /**
     * Obtener información de la disciplina
     */
    private function getDisciplinaInfo(int $disciplinaId): object
    {
        $disciplina = DB::table('disciplines as disc')
            ->leftJoin('disciplines_translations as trans', function ($join) {
                    ->on('trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                    ->on('trans.active', '=', DB::raw(true));
            })
            ->select(['disc.code as codigo', 'trans.display_name as disciplina'])
            ->where(['disc.id' => $disciplinaId])
            ->first();
        
        $disciplina->regime = $this->determinarRegime($disciplina->codigo);
        
        return $disciplina;
    }

    /**
     * Determinar el régimen basado en el código
     */
    private function determinarRegime(string $codigo): string
    {
        $regime = substr($codigo, -3, 1);
        
        return match ($regime) {
            '1', '2' => $regime . 'º Semestre',
            'A' => 'Anual',
            default => 'Desconhecido'
        };
    }

    /**
     * Obtener información del curso
     */
    private function getCourseInfo(int $studyPlanId): object
    {
        $cursoId = DB::table('study_plan_editions as spe')
            ->where('spe.id', $studyPlanId)
            ->join('study_plans as sp', 'sp.id', 'spe.study_plans_id')
            ->select('sp.courses_id')
            ->first()
            ->courses_id;
        
        return DB::table('courses')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id')
                    ->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                    ->on('ct.active', '=', DB::raw(true));
            })
            ->select(['ct.display_name'])
            ->where('courses.id', $cursoId)
            ->first();
    }

    /**
     * Obtener datos de la institución
     */
    private function getInstitutionData(): array
    {
        $institution = Institution::latest()->first();
        
        return [
            'institution' => $institution,
            'logotipo' => "https://" . $_SERVER['HTTP_HOST'] . "/storage/" . $institution->logotipo
        ];
    }

    /**
     * Preparar datos para el PDF
     */
    
    private function preparePdfData(
        Collection $turmaInfo,
        object $disciplina,
        object $course,
        Metrica $metrica,
        Collection $students,
        Collection $utilizadores,
        Collection $coordenadores,
        $estatisticas,
        bool $segundaChamada,
        array $accentReplacement
    ): array {
        $institutionData = $this->getInstitutionData();
        
        return [
            'turma' => $turmaInfo[0]->turma,
            'lectiveYear' => $turmaInfo[0]->anoLetivo,
            'discipline_code' => $disciplina->codigo . ' - ' . $disciplina->disciplina,
            'discipline_name' => $disciplina->disciplina,
            'regimeFinal' => $disciplina->regime,
            'curso' => $course->display_name,
            'ano_curricular' => $turmaInfo[0]->Anocurricular,
            'institution' => $institutionData['institution'],
            'logotipo' => $institutionData['logotipo'],
            'utilizadores' => $utilizadores,
            'documentoCode_documento' => 10, // Valor fijo según el código original
            'code_dev' => $metrica->code_dev,
            'students' => $students,
            'segunda_chamada' => $segundaChamada,
            'coordenadores' => $coordenadores,
            'estatistica_tabela' => $estatisticas
        ];
    }

    /**
     * Generar el PDF
     */
    private function generatePdf(array $data, Metrica $metrica, object $disciplina, Collection $turmaInfo, array $accentReplacement)
    {
        // Determinar la vista según el usuario
        $view = auth()->user()->id == 845 ? "Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaMacNew" : "Avaliations::avaliacao-aluno.pauta_grades.pdf.pautaMac";
        $data['students'] = collect($data['students'])
            ->groupBy('mat')
            ->map(function ($group) {

                // pega o aluno com MAIOR nota (convertendo para float)
                return $group->sortByDesc(function ($item) {
                    return is_null($item->grade) ? -1 : (float) $item->grade;
                })->first();

            })
            ->values();


        $pdf = PDF::loadView($view, $data);
        // Configurar opciones del PDF
        $this->configurePdfOptions($pdf);
        
        // Caso especial para usuario con ID 23 (probablemente para desarrollo/testing)
        if (auth()->user()->id == 23) {
            return $this->savePdfForDevelopment($pdf, $metrica, $disciplina, $turmaInfo, $accentReplacement);
        }
        
        // Caso normal: mostrar PDF en stream
        return $pdf->stream($metrica->code_dev . '.pdf');
    }

    /**
     * Configurar opciones del PDF
     */
    private function configurePdfOptions($pdf): void
    {
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
        
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', [
            'institution' => Institution::latest()->first()
        ])->render();
        
        $pdf->setOption('footer-html', $footer_html);
    }

    /**
     * Guardar PDF para desarrollo/testing
     */
    private function savePdfForDevelopment($pdf, Metrica $metrica, object $disciplina, Collection $turmaInfo, array $accentReplacement)
    {
        $version = 1;
        $disciplinaNormalizada = str_replace($accentReplacement['withAccents'], $accentReplacement['withoutAccents'], $disciplina->disciplina);
        $turmaNormalizada = str_replace($accentReplacement['withAccents'], $accentReplacement['withoutAccents'], $turmaInfo[0]->turma);
        $metricaNormalizada = str_replace($accentReplacement['withAccents'], $accentReplacement['withoutAccents'], $metrica->nome);
        
        $fileName = $disciplinaNormalizada . '-' . $turmaNormalizada . '-' . $metricaNormalizada . '-' . $version . '.pdf';
        $path = storage_path('app/public/pautas-mac/') . $fileName;
        
        // Descomentar para guardar el archivo
        // $pdf->save($path);
        
        // Para debugging
        dd(link_storage('/storage/pautas-mac/' . $fileName), $path);
    }
            $join->on('trans.discipline_id', '=', 'disc.id')

        private function escala_estatistica_mac($students){
            try {
                $count = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
                $count_sexo_F = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
                $count_sexo_M = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];
                $escala_result = ['first' => 0, 'second' => 0, 'thirst' => 0, 'fourth' => 0, 'fiveth' => 0, 'sixth' => 0];

                for ($i = 0; $i < count($students); $i++) {
                    $sexo_aluno = DB::table('user_parameters as sexo')
                                ->where('sexo.parameters_id', 2)
                                ->leftJoin('parameter_option_translations as sexo_value', function ($join) {
                                    $join->on('sexo_value.parameter_options_id', '=', 'sexo.value');
                                })
                                ->select('sexo_value.display_name as sexo')
                                ->pluck('sexo')
                                ->first();

                    $nota_aluno = $students[$i]->grade;

                    //Escala dos reporvados Processamento
                if ($nota_aluno >= 0 && $nota_aluno < 7) {
                    $count["first"] = $count['first'] + 1;
                    //validade sexo
                
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["first"] = $count_sexo_M['first'] + 1
                        : $count_sexo_F["first"] = $count_sexo_F['first'] + 1;
                }
                if ($nota_aluno > 6 && $nota_aluno < 10) {
                    $count["second"] = $count['second'] + 1;
                    //validade sexo
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["second"] = $count_sexo_M['second'] + 1
                        : $count_sexo_F["second"] = $count_sexo_F['second'] + 1;
                }
                //Escala dos aprovados Processamento
                if ($nota_aluno > 9 && $nota_aluno < 14) {
                    $count["thirst"] = $count['thirst'] + 1;
                    //validade sexo
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["thirst"] = $count_sexo_M['thirst'] + 1
                        : $count_sexo_F["thirst"] = $count_sexo_F['thirst'] + 1;
                }

                if ($nota_aluno > 13 && $nota_aluno < 17) {
                    $count["fourth"] = $count['fourth'] + 1;
                    //validade sexo
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["fourth"] = $count_sexo_M['fourth'] + 1
                        : $count_sexo_F["fourth"] = $count_sexo_F['fourth'] + 1;
                }

                if ($nota_aluno > 16 && $nota_aluno < 20) {
                    $count["fiveth"] = $count['fiveth'] + 1;
                    //validade sexo
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["fiveth"] = $count_sexo_M['fiveth'] + 1
                        : $count_sexo_F["fiveth"] = $count_sexo_F['fiveth'] + 1;
                }
                if ($nota_aluno == 20) {
                    $count["sixth"] = $count['sixth'] + 1;
                    //validade sexo
                    $sexo_aluno == "Masculino" ?
                        $count_sexo_M["sixth"] = $count_sexo_M['sixth'] + 1
                        : $count_sexo_F["sixth"] = $count_sexo_F['sixth'] + 1;
                }


                }
                $total = 0;
                $aprovados = 0;
                $reprovados = 0;
                $aprovados_femenino = 0;
                $aprovados_masculino = 0;     
                $reprovados_femenino = 0;
                $reprovados_masculino = 0;


                foreach ($escala_result as $key => $escala_item) {
                    $escala_result[$key] = [
                        "M" => $count_sexo_M[$key],
                        "Percent_M" =>  $count[$key] != 0 ? (int) round(($count_sexo_M[$key] / $count[$key]) * 100, 0) : 0,
                        "F" => $count_sexo_F[$key],
                        "Percent_F" => $count[$key] != 0 ? (int)  round(($count_sexo_F[$key] / $count[$key]) * 100, 0) : 0,
                        "T" => $count[$key],
                        'Escala' => $key
                    ];

                    $total += $count[$key];

                    if($key == 'first' || $key == 'second'){
                        $reprovados += $count[$key];
                        $reprovados_femenino += $count_sexo_F[$key];
                        $reprovados_masculino += $count_sexo_M[$key];
                    }
                    else{
                        $aprovados += $count[$key];
                        $aprovados_femenino += $count_sexo_F[$key];
                        $aprovados_masculino += $count_sexo_M[$key];
                    }

                    

                }

                $estatistica_geral = [
                    "total" => $total,
                    "aprovados" => $aprovados,
                    "reprovados" => $reprovados,
                    "aprovados_femenino" => $aprovados_femenino,
                    "aprovados_masculino" => $aprovados_masculino,
                    "reprovados_femenino" => $reprovados_femenino,
                    "reprovados_masculino" => $reprovados_masculino,
                ];
                return [
                    "escala" => $escala_result,
                    "total" => $estatistica_geral
                ];

                    
            } catch (Exception  $e) {
                logError($e);
                return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
        }


        public function sc(){
        $sc = DB::table('lancar_pauta')
                ->whereNull('path')
                ->get();

            

                $sc->each(function($item) {
                    $metrica_id = $item->pauta_tipo == 'PF1' ? 96 : 97;
                    $sc = $item->segunda_chamada ?? null;

                    $plano_estudo = DB::table('study_plan_edition_disciplines as sped')
                                    ->where('sped.discipline_id',$item->id_disciplina)
                                    ->join('study_plan_editions as spe','spe.id','sped.study_plan_edition_id')
                                    ->select('spe.id as id')
                                    ->first();

                    if(!isset($plano_estudo)){
                        dd('Invalid! '.$item->id_disciplina);
                    }

                    $plano_estudo = $plano_estudo->id;
                    $m = DB::table('metricas')
                            ->where('id',$metrica_id)->first();
                    $avaliacao_id = $m->avaliacaos_id;

                    event(new GeneratePdfAvaliationEvent($item->id_disciplina ,$metrica_id,$plano_estudo,$avaliacao_id,$item->id_turma,9,$sc,$item->version));
            
                });

            
                dd('ok ok fly');
            
    

    
    }

    

    

}