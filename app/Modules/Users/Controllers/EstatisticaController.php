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
        Log::info("Turma: $classId | Ano curricular: $courseYear");

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
                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                ->join('classes as cl', function ($join) use ($classId) {
                    $join->on('cl.id', '=', 'mc.class_id');
                    $join->on('mc.matriculation_id', '=', 'matriculations.id');
                    $join->where('cl.id', '=', $classId);
                })
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->select('u0.id as user_id', 'u_p.value as student_name')
                ->groupBy('u0.id', 'u_p.value')
                ->orderBy('u_p.value', 'asc')
                ->get();

            $userIds = $alunos->pluck('user_id')->toArray();

            $protocolos = DB::table('scholarship_holder')
                ->whereIn('user_id', $userIds)
                ->whereIn('scholarship_entity_id', [1, 10, 17])
                ->pluck('user_id')
                ->toArray();

            return response()->json([
                'total' => count($alunos),
                'protocolo' => count($protocolos)
            ]);

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

}
