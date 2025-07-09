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
   public function student($classId){
    
    $alunos = DB::table('user_classes as uc')
        ->join('user_parameters as up', function ($join) {
            $join->on('up.users_id', '=', 'uc.user_id')
                ->where('up.parameters_id', '=', 1); // só pega o valor se for o nome (id = 1)
        })
        ->join('classes as c', 'c.id', '=', 'uc.class_id')
        ->where('uc.class_id', $classId)
        ->select('up.users_id as aluno_id', 'up.value as aluno', 'c.code as turma')
        ->groupBy('up.users_id', 'up.value', 'c.code')
        ->get();

        $totalAlunos = $alunos->count(); 
    
        return response()->json(['alunos' => $totalAlunos]);
   }
}
