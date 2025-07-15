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
use Illuminate\Support\Facades\Log;

use PDF;
use App\Model\Institution;
use App\Modules\GA\Models\Student;

class EstatisticaController extends Controller
{ 
   public function index(){
    $data = $this->api();
    return view('Users::estatisticaget.index')->with($data);
   }

   public function api(){
     //se o usuario for candidato a estudante, redirecionar para o perfil
     $userId = auth()->user()->id;
     $user = User::whereId($userId)->first();

     $courses = Course::with([
         'currentTranslation'
     ])
         ->where('id', '!=', 22)
         ->where('id', '!=', 18);

     if ($user->hasAnyRole(['candidado-a-estudante'])) {

         return redirect()->route('candidates.show', $userId)->with($data);
     }

     $lectiveYears = LectiveYear::with(['currentTranslation'])
         ->get();

     $currentData = Carbon::now();
     $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
         ->first();
     $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

     $dd = [
         'courses' => $courses->get(),
         'lectiveYearSelected' => $lectiveYearSelected,
         'lectiveYears' => $lectiveYears
     ];
     $data = [
        'courses' => $courses->get(),
        'lectiveYearSelected' => $lectiveYearSelected,
        'lectiveYears' => $lectiveYears
    ];
    return $data;
   }
   public function student($classId, $courseYear)
   {    
       try {
           $currentDate = Carbon::now();
   
           $lectiveYearSelected = DB::table('lective_years')
               ->whereRaw('"' . $currentDate . '" between `start_date` and `end_date`')
               ->first();
   
           if (!$lectiveYearSelected) {
               return response()->json(['erro' => 'Ano lectivo não encontrado.'], 404);
           }
          
           $alunos = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->where('matriculations.lective_year', $lectiveYearSelected->id)
            ->where('matriculations.course_year', $courseYear)
            ->join('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
            ->where('mc.class_id', $classId)
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u0.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1); // Nome do aluno
            })
            ->leftJoin('scholarship_holder as sh', function ($join) {
                $join->on('u0.id', '=', 'sh.user_id')
                    ->where('sh.are_scholarship_holder', 1)
                    ->whereIn('sh.scholarship_entity_id', [1, 10, 17]);

            })
            ->leftJoin('scholarship_entity as se', 'se.id', '=', 'sh.scholarship_entity_id')
            ->select([
                'u0.id as user_id',
                'u_p.value as student_name',
                'sh.are_scholarship_holder as is_scholar',
                'se.company as entidade',
                'se.type as categoria'
            ])
            ->groupBy('u0.id', 'u_p.value', 'sh.are_scholarship_holder', 'se.company', 'se.type')
            ->orderBy('u_p.value', 'asc')
            ->get();

   
           // Separar os bolseiros
           $bolseiros = $alunos->filter(function ($aluno) {
               return $aluno->is_scholar == 1 && $aluno->entidade !== null;
           });
           // Alunos sem bolsa
            $semBolsa = $alunos->reject(function ($aluno) {
                return $aluno->is_scholar == 1 && $aluno->entidade !== null;
            });

           //log::info($bolseiros);
           return response()->json([
               'total' => $semBolsa->count(),
               'protocolo' => $bolseiros->count(),
               'alunos' => $alunos->count()
           ]);
   
       } catch (Exception | Throwable $e) {
           logError($e);
           return response()->json(['erro' => $e->getMessage()], 500);
       }
   }
   public function gerarPDF() {
    try {
        $institution = Institution::latest()->first();

        $mapeamento = [
            'EC' => [
                1 => [['id' => 43, 'periodo' => 'M'], ['id' => 44, 'periodo' => 'T'], ['id' => 45, 'periodo' => 'N']],
                2 => [['id' => 46, 'periodo' => 'M'], ['id' => 47, 'periodo' => 'T']],
                3 => [['id' => 48, 'periodo' => 'M'], ['id' => 49, 'periodo' => 'T']],
                4 => [['id' => 50, 'periodo' => 'M'], ['id' => 51, 'periodo' => 'T']],
                5 => [['id' => 52, 'periodo' => 'M'], ['id' => 53, 'periodo' => 'T']],
            ],
            // Podes adicionar mais cursos aqui...
        ];

        // Exemplo de lista de cursos com códigos (deves substituir pela tua fonte real)
        $courses = [
            (object)['id' => 1, 'code' => 'EC', 'currentTranslation' => (object)['display_name' => 'Curso EC']],
            // outros cursos...
        ];

        $estatisticas = [];

        foreach ($courses as $course) {
            $code = $course->code;
            $estatisticas[$code] = [];

            for ($ano = 1; $ano <= 5; $ano++) {
                $estatisticas[$code][$ano] = ['M' => 0, 'T' => 0, 'N' => 0, 'PT' => 0, 'TOTAL' => 0];

                if (isset($mapeamento[$code][$ano])) {
                    foreach ($mapeamento[$code][$ano] as $turma) {
                        $idTurma = $turma['id'];
                        $periodo = $turma['periodo'];

                        $student = $this->student($idTurma, $ano);
                        $studentData = json_decode($student->getContent(), true);

                        $totalAlunos = $studentData['total'] ?? 0;
                        $protocolo = $studentData['protocolo'] ?? 0;

                        // Somar totais conforme o período
                        if ($periodo === 'M') $estatisticas[$code][$ano]['M'] += $totalAlunos;
                        if ($periodo === 'T') $estatisticas[$code][$ano]['T'] += $totalAlunos;
                        if ($periodo === 'N') $estatisticas[$code][$ano]['N'] += $totalAlunos;

                        $estatisticas[$code][$ano]['PT'] += $protocolo;
                        $estatisticas[$code][$ano]['TOTAL'] += $totalAlunos + $protocolo;
                    }
                }
            }
        }

        $dados = [
            'courses' => $courses,
            'estatisticas' => $estatisticas,
            'institution' => $institution,
        ];

        $pdf = PDF::loadView("Avaliations::avaliacao-estatistica.pdf.estatisticaget", $dados);
        $pdf->setOption('margin-top', '2mm');
        $pdf->setOption('margin-left', '2mm');
        $pdf->setOption('margin-bottom', '13mm');
        $pdf->setOption('margin-right', '2mm');
        $pdf->setPaper('a4', 'landscape');

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);

        $filename = 'Dados_Estatistica_Geral.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');

        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

   
}
