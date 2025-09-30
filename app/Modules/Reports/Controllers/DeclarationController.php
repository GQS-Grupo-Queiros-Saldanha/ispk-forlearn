<?php

namespace App\Modules\Reports\Controllers;

use App\Exports\IncomeExport;
use App\Exports\PendingExport;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\configDocumentation;
use App\Modules\Avaliations\Models\GradePath;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\Student;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\DepartmentTranslation;
use App\Modules\GA\Requests\DepartmentRequest;
use Toastr;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\RoleTranslation;
use App\Modules\Users\Models\Matriculation;
use Dotenv\Regex\Success;
use PDF;
use App\Model\Institution;
use App\Modules\Users\Enum\ParameterEnum;
use Log;
use Throwable;
use Exception;
use App\Modules\GA\Models\DisciplineArea;
use App\Modules\GA\Models\DisciplineProfile;
use App\Modules\GA\Models\DisciplineTranslation;
use App\Modules\Cms\Models\Language;
class DeclarationController extends Controller
{

    public function index()
    {
        echo "Olá mundo teste";
    }

    public function create()
    {
        //    $users = User::whereHas('roles', function ($q) {
        //         $q->whereIn('id', [6]);
        //     })  ->whereHas('courses')
        //         ->whereHas('matriculation')
        //         ->with(['parameters' => function ($q) {
        //             $q->whereIn('code', ['nome', 'n_mecanografico']);
        //         }])
        //         ->get()
        //         ->map(function ($user) {
        //             $displayName = $this->formatUserName($user);
        //             // return ['display_name' => $displayName];
        //             return ['id' => $user->id, 'display_name' => $displayName];
        //         }) ;
        //         // $data=[
        //         //         'Studantes'=>$users,
        //         //         'Devagar'=>'Ruanda'
        //         //       ];
        return view('Reports::declaration.general');
    }

    protected function formatUserName($user)
    {

        $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
        $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
            $fullNameParameter->pivot->value : $user->name;
        $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
        $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
            $studentNumberParameter->pivot->value : "000";

        return "$fullName #$studentNumber ($user->email)";

    }





    public function show($id)
    {

    }
    public function update($id)
    {

    }

    private function dataActual()
    {
        $m = date("m");
        $mes = array(
            "01" => "Janeiro",
            "02" => "Fevereiro",
            "03" => "Março",
            "04" => "Abril",
            "05" => "Maio",
            "06" => "Junho",
            "07" => "Julho",
            "08" => "Agosto",
            "09" => "Setembro",
            "10" => "Outubro",
            "11" => "Novembro",
            "12" => "Dezembro"
        );
        $data = date("d") . " de " . $mes[$m] . " de " . date("Y");
        return $data;
    }




    public function generatePdfDeclaracao(Request $request){
        
        //dd($request);
        switch ($request->type_document) {
            case '1':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->semnotas($request, $config);
                break;

            case '2':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->withNoteI($request, $config);
                break;
            case '3':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->CertificateMerito($request, $config);
                break;
            case '4':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->finalCertificate($request, $config);
                break;
            case '5':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->diplomas($request, $config);
                break;
            case '6':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->frequencia($request, $config);
                break;
            case '7':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->anulacao($request, $config);
                break;
            case '8':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->withNoteEnd($request, $config);
                break;
            case '9':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->exame_acesso($request, $config);
                break;
            case '10':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->pedido_entrada($request, $config);
                break;
            case '11':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->pedido_saida($request, $config);
                break;
            case '14':
                    $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                    return $this->plano_disciplinas($request, $config);
                    break;

            case '15':
                $config = ConfigDocumentation::where('document_type', $request->type_document)->firstOrFail();
                return $this->solicitacao_estagio($request, $config);
                break;
        }



    }






    // ------------ ------------ ------------ ------------ ------------ ------------ ------------ ------------//        


    // ------------ ------------ ------------ ------------ ------------ ------------ ------------ ------------//







    private function anulacao($request, $config){




        if (!isset($request->requerimento)) {
            Toastr::warning(__('A forLEARN não detectou o código do requerimento!'), __('toastr.warning'));
            return redirect()->back();
        }

        $this->gerar_codigo_documento($request->requerimento);
        $requerimento = DB::table('requerimento')
            ->where("id", $request->requerimento)
            ->first();






        $recibo = $this->referenceGetRecibo($requerimento->article_id);

        $studentId = $request->students;
        $type_document = $request->type_document;



        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "asc")
            ->select(["sphd.years as ano", "notas.lective_year as ano_lectivo"])
            ->get();
        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');
        // ->get();



        // $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('user_parameters as u_p7', function ($join) {
                $join->on('users.id', '=', 'u_p7.users_id')
                    ->where('u_p7.parameters_id', 15);
            })


            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')

            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p6.value as province',
                'u_p7.value as emitido',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->firstOrFail();


        $lectivo = DB::table('lective_year_translations')
            ->where("lective_years_id", $studentInfo->lectivo)
            ->where("active", 1)
            ->select(["display_name as ano"])
            ->first();





        $pdf_name = "Anulacao_Matricula_" . $lectivo->ano . "_" . $studentInfo->number . "_" . $studentInfo->name;

        $nascimento = $this->dataEscrita($studentInfo->barthday);

        $userFoto = User::whereId($studentId)->with([

            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();
        $courses = Course::with([
            'currentTranslation'
        ])->get();


        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $secretario = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->secretaria_academica)
            ->where('usuario_cargo.role_id', 18)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();





        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'secretario' => $secretario,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'anos' => $anos,
            'media' => $media,

            'lectivo' => $lectivo,
            "nascimento" => $nascimento

        ];



        $institution = Institution::latest()->first();

        $pdf_name = 'declaracao_anulacao'; // ou gere com base no estudante, etc.
        $pdf = PDF::loadView("Reports::declaration.anulacao", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'secretario',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'anos',
            'lectivo',
            'media',
            'nascimento',
            "requerimento",
            "recibo"
        ));
      
        // ✅ Opções de formatação e papel
$pdf->setOption('margin-top', '2mm');
$pdf->setOption('margin-left', '2mm');
$pdf->setOption('margin-bottom', '1mm');
$pdf->setOption('margin-right', '2mm');
$pdf->setPaper('a4', 'portrait');

// ✅ Opções para scripts e recursos locais
$pdf->setOption('enable-javascript', true);
$pdf->setOption('javascript-delay', 1000);
$pdf->setOption('no-stop-slow-scripts', true);
$pdf->setOption('enable-smart-shrinking', true);
$pdf->setOption('enable-local-file-access', true); // essencial para imagens locais no footer

// ✅ Gerar footer HTML e salvar com caminho acessível
$footer_html = view('Reports::pdf_model.pdf_footer', compact('institution'))->render();
$footer_path = storage_path('app/public/pdf_footer.html'); // local fixo e seguro

file_put_contents($footer_path, $footer_html);
chmod($footer_path, 0644); // garantir leitura pelo wkhtmltopdf

$pdf->setOption('footer-html', $footer_path);

// ✅ Nome amigável do arquivo PDF
$lectiveYear = $lectiveYears[0] ?? null;
$pdf_name = "Relatório_candidaturas_" .
    ($lectiveYear->currentTranslation->display_name ?? 'AnoDesconhecido') .
    " (" . 2 . "ª Fase)";

// ✅ Log extra de debug (opcional)
if (!file_exists($footer_path)) {
    \Log::error("Footer HTML file não encontrado em: {$footer_path}");
} else {
    \Log::info("PDF Footer salvo com sucesso: {$footer_path}");
}

// ✅ Retornar PDF para visualização no navegador
return $pdf->stream($pdf_name . '.pdf');
    }

















    private function withNoteI($request, $config)
    {
        try {
            $studentId = $request->students;
            $type_document = $request->type_document;
            $ano = $request->student_year;
            $efeito = $request->efeito_type;

            $requerimento = DB::table('requerimento')
                ->where("id", $request->requerimento)
                ->first();

            $lective = DB::table('lective_years as ly')->where('is_termina', 0)
                ->join('lective_year_translations as lyt', 'ly.id', '=', 'lyt.lective_years_id')
                ->where('lyt.active', 1)
                ->select('ly.id')
                ->pluck('id')
                ->first();

            $recibo = $this->referenceGetRecibo($requerimento->article_id);


            //$outDisciplines = [181,551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

            $outDisciplines = [];

            $studentInfo = User::where('users.id', $studentId)
                ->leftJoin('user_parameters as u_p0', function ($join) {
                    $join->on('users.id', '=', 'u_p0.users_id')
                        ->where('u_p0.parameters_id', 19);
                })
                ->leftJoin('user_parameters as u_p1', function ($join) {
                    $join->on('users.id', '=', 'u_p1.users_id')
                        ->where('u_p1.parameters_id', 1);
                })
                ->leftJoin('user_parameters as u_p2', function ($join) {
                    $join->on('users.id', '=', 'u_p2.users_id')
                        ->where('u_p2.parameters_id', 23);
                })
                ->leftJoin('user_parameters as u_p3', function ($join) {
                    $join->on('users.id', '=', 'u_p3.users_id')
                        ->where('u_p3.parameters_id', 24);
                })
                ->leftJoin('user_parameters as u_p4', function ($join) {
                    $join->on('users.id', '=', 'u_p4.users_id')
                        ->where('u_p4.parameters_id', 5);
                })
                ->leftJoin('user_parameters as u_p5', function ($join) {
                    $join->on('users.id', '=', 'u_p5.users_id')
                        ->where('u_p5.parameters_id', 14);
                })

                ->leftJoin('user_parameters as u_p6', function ($join) {
                    $join->on('users.id', '=', 'u_p6.users_id')
                        ->where('u_p6.parameters_id', 150);
                })

                ->leftJoin('parameter_option_translations as u_p7', function ($join) {
                    $join->on('u_p7.parameter_options_id', '=', 'u_p6.value')
                        ->where('u_p6.parameters_id', 150);
                })
                ->leftJoin('user_parameters as u_p9', function ($join) {
                    $join->on('users.id', '=', 'u_p9.users_id')
                        ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
                })
                ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                    $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
                })
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('matriculations', function ($join) use ($lective, $studentId) {
                    $join->on('matriculations.user_id', '=', 'users.id');
                    $join->where('matriculations.lective_year', $lective);
                    $join->where('matriculations.user_id', $studentId);
                })
                ->leftJoin('user_parameters as u_p10', function ($join) {
                    $join->on('users.id', '=', 'u_p10.users_id')
                        ->where('u_p10.parameters_id', 15);
                })
                ->select([
                    'u_p0.value as number',
                    'u_p1.value as name',
                    'u_p2.value as dad',
                    'u_p3.value as mam',
                    'u_p4.value as barthday',
                    'u_p5.value as bi',
                    'u_p7.display_name as province',
                    'u_p8.display_name as municipio',
                    'ct.display_name as course',
                    'matriculations.course_year as year',
                    'matriculations.lective_year as lectivo',
                    'courses.id as course_id',
                    'u_p10.value as emitido'
                ])
                ->where('users.id', $studentId)
                ->first();


            $lectivo = DB::table('lective_year_translations')
                ->where("lective_years_id", $studentInfo->lectivo)
                ->where("active", 1)
                ->select(["display_name as ano"])
                ->first();

            $string = str_replace('/', '_', $requerimento->code);
            $pdf_name = "DcN_" . $string;
            //trazer todas as disciplinas matriculadas
            //anchor
            $disciplines = DB::table('matriculations')
                ->join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
                ->join('disciplines', 'disciplines.id', '=', 'matriculation_disciplines.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->join('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
                ->join('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
                ->join('study_plan_edition_disciplines as sped', 'sped.discipline_id', '=', 'study_plans_has_disciplines.disciplines_id')
                ->where('matriculations.user_id', $studentId)
                ->select([
                    'disciplines.id as id',
                    'disciplines.code as code',
                    'dcp.display_name as name',
                    'dat.display_name as area',
                    'disciplines.uc as uc',
                    'study_plans_has_disciplines.years as course_year',
                    'discipline_areas.id as area_id'
                ])
                ->whereNotIn('matriculation_disciplines.discipline_id', $outDisciplines)
                ->orderBy('course_year', 'ASC')
                ->distinct('id')
                ->get();

            $disciplines = $this->ordena_plano($disciplines);

            //$disciplines = $disciplines->unique('id')->values()->all();

            //trazer todas as disciplinas do historico

            $oldDisciplines = DB::table('new_old_grades')
                ->where('user_id', $studentId)
                ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->join('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
                ->join('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
                ->leftJoin('study_plan_edition_disciplines as sped', 'sped.discipline_id', '=', 'study_plans_has_disciplines.disciplines_id')
                ->whereNotIn('disciplines.id', $outDisciplines)
                ->where('study_plans_has_disciplines.years', intVal($ano))
                ->select([
                    'disciplines.id as id',
                    'disciplines.code as code',
                    'dcp.display_name as name',
                    'dat.display_name as area',
                    'study_plans_has_disciplines.years as course_year',
                    'discipline_areas.id as area_id'
                ])
                ->orderBy('course_year', 'ASC')
                ->distinct()
                ->get();

            $disciplines = $oldDisciplines;
            $disciplines = $this->ordena_plano($disciplines);

            //trazer todas as disciplinas que o estudante esteve matriculado o ano lectivo.
            //e do historico se tiver

            $countDisciplines = count($disciplines);

            //avaliar o caso do curso de CEE
            //no caso das especialidades

            //avaliar se o estudante esta matriculado no ano maior que 2.
            //e ver a especialidade.
            $countAllDisciplines = StudyPlan::where('courses_id', $studentInfo->course_id)
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                ->count();

            $state = DB::table('users_states')
                ->join('states', 'states.id', '=', 'users_states.state_id')
                ->where('user_id', $studentId)
                ->first();

            $state = $state->name ?? 'N/A';


            //retornar as disciplinas antigas Armazenadas no historico.
            //depois exibir as notas novas do historico
            //IMPORTANTE: Trazer as notas por edição de plano de estudo
            //porcausa das disciplinas que vao acarretar negativa
            //(relacionado ao plano_avaliacao_estudos)

            $oldGrades = DB::table('new_old_grades')
                ->where('user_id', $studentId)
                ->whereNotIn('discipline_id', $outDisciplines)
                ->orderBy('lective_year', 'ASC')
                ->distinct()
                ->get()
                ->groupBy('lective_year');

            $media = DB::table('new_old_grades as notas')
                ->where('notas.user_id', $studentId)
                ->whereNotIn('notas.discipline_id', $outDisciplines)
                ->avg('notas.grade');


            $finalDisciplineGrade = DB::table('new_old_grades')
                ->where('user_id', $studentId)
                ->whereIn('discipline_id', $outDisciplines)
                ->distinct()
                ->get();

            if ($finalDisciplineGrade->isNotEmpty() && $finalDisciplineGrade->first()->lective_year > 2020) {
                $var = 1;
            } elseif ($finalDisciplineGrade->isNotEmpty()) {
                $var = 2;
            } else {
                $var = 0;
            }
            //avaliar se a ultima disciplina (trablho de fim de curso) foi aprovada antes de 2021
            if ($var == 1) {
                //listar todos os planos de estudo daquele curso.
                //para o aluno ate o ano maximo que ele esta matriculado.
                //as notas trazer por where between de datas nas notas.
                return $studyPlanEditions = StudyPlan::where('courses_id', $studentInfo->course_id)
                    ->join('study_plan_editions', 'study_plan_editions.study_plans_id', '=', 'study_plans.id')
                    ->join('lective_years', 'lective_years.id', '=', 'study_plan_editions.lective_years_id')
                    ->leftJoin('lective_year_translations as lyt', function ($join) {
                        $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                        $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('lyt.active', '=', DB::raw(true));
                    })
                    ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                    //->whereNotIn('study_plans_has_disciplines.disciplines_id', $outDisciplines)
                    ->select(['lyt.display_name as lective_year', 'study_plans_has_disciplines.disciplines_id as disciplines_id'])
                    //   ->whereIn('study_plans_has_disciplines.disciplines_id', $disciplines->pluck('id'))
                    ->distinct()
                    ->get();

                // $studyPlanEditions = $studyPlanEditions->unique('disciplines_id')->values()->all();
            }


            //trazer as notas do estudantes apartir da ultima a ser lançada.
            //podem vir do Recurso, do Exame ou do MAC
            //no if do blade comparar se a nota foi lancada entre a datas comparando
            //com o start_date and end_date do plano_edicao_de_estudo

            //como nao tenho data na tabela de percentagem vou avaliar cada situacao
            //avaliar, sem tem MAC > 14 ou disciplina que se dispensa.
            //avaliar se tem exame ou recurso.
            //preciso testar a ideia de cima:: TODO
            //mas vou fazer isso na view
            //como segue o modelo MAC -> EXAME -> Recurso -> E.Esp.
            //vou pegar esse modelo e avaliar um por um


            $grades = DB::table('percentage_avaliation')
                ->join('plano_estudo_avaliacaos', 'plano_estudo_avaliacaos.id', '=', 'percentage_avaliation.plano_estudo_avaliacaos_id')
                ->join('study_plan_editions', 'study_plan_editions.id', '=', 'plano_estudo_avaliacaos.study_plan_editions_id')
                ->where('user_id', $studentId)
                ->get();

            $areas = [13, 14, 15];
            //TODO:: TRAZER SO AS DO PLANO CURRICULAR DO CURSO, NAO TODAS
            $disciplinesAreas = DB::table('discipline_areas')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->whereIn('discipline_areas.id', $areas)
                ->get();


            $studyPlanEditions = $var == 1 ? $studyPlanEditions : "";


            $userFoto = User::whereId($studentId)->with([

                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                    ]);
                }
            ])->firstOrFail();
            $courses = Course::with([
                'currentTranslation'
            ])->get();


            //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
            //Neste caso o direitor.

            //  pega o cargo
            $role = Role::whereId(8)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();
            $cargo = $role->translations[0]->description;
            $dataActual = $this->dataActual();
            $institution = Institution::latest()->first();
            $direitor = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
                ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
                ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
                ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
                ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
                ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
                ->where('usuario.id', $institution->vice_director_academica)
                ->where('usuario_cargo.role_id', 9)
                ->where('u_p0.parameters_id', 1)
                ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
                ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
                ->where('rt.active', 1)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->select([
                    'usuario.id as id_usuario',
                    'usuario.email as email_usuario',
                    'usuario.name as name',
                    'u_p0.value as nome_completo',
                    'ga.nome as grau_academico',
                    'cp.nome as categoria_profissional',
                    'rt.display_name as cargo'
                ])
                ->orderBy('usuario.name')
                ->groupBy('id_usuario')
                ->first();

            $secretario = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
                ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
                ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
                ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
                ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
                ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
                ->where('usuario.id', $institution->secretaria_academica)
                ->where('usuario_cargo.role_id', 18)
                ->where('u_p0.parameters_id', 1)
                ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
                ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
                ->where('rt.active', 1)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->select([
                    'usuario.id as id_usuario',
                    'usuario.email as email_usuario',
                    'usuario.name as name',
                    'u_p0.value as nome_completo',
                    'ga.nome as grau_academico',
                    'cp.nome as categoria_profissional',
                    'rt.display_name as cargo'
                ])
                ->orderBy('usuario.name')
                ->groupBy('id_usuario')
                ->first();



            $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

                ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
                ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
                ->where('disciplines.courses_id', $studentInfo->course_id)
                ->distinct(['id_disciplina', 'hora'])
                ->get();

            $cargaHoraria = $cargaHoraria->unique('id_disciplina')
                ->values()
                ->all();

            $final_note = DB::table('disciplines_translations as dt')
                ->join('disciplines', 'disciplines.id', 'dt.discipline_id')
                ->leftjoin('new_old_grades as notas', "disciplines.id", "notas.discipline_id")
                ->where('notas.user_id', $studentId)
                ->where("dt.active", 1)
                ->where("dt.language_id", 1)
                ->where("disciplines.courses_id", $studentInfo->course_id)
                ->where("dt.display_name", "LIKE", "Trabalho de fim de curso")
                ->select(["notas.grade", "display_name"])
                ->get();




            $data = [
                'config' => $config,
                'cargaHoraria' => $cargaHoraria,
                'direitor' => $direitor,
                'cargo' => $cargo,
                'efeito' => $efeito,
                'studentInfo' => $studentInfo,
                'dataActual' => $dataActual,
                'countDisciplines' => $countDisciplines,
                'disciplines' => $disciplines,
                'year_document' => $ano,
                'state' => $state,
                'var' => $var,
                'studyPlanEditions' => $studyPlanEditions,
                'oldGrades' => $oldGrades,
                'grades' => $grades,
                'countAllDisciplines' => $countAllDisciplines,
                'disciplinesAreas' => $disciplinesAreas,
                'finalDisciplineGrade' => $finalDisciplineGrade,
                'userFoto' => $userFoto,
                'secretario' => $secretario,
                'final_note' => $final_note,
                'requerimento' => $requerimento,
                'recibo' => $recibo,
                'ano' => $ano
            ];

            $nascimento = $this->dataEscrita($studentInfo->barthday);



            // Subir tudo que estiver aqui 


            $courses_duration = DB::table("courses")
                ->where("id", $studentInfo->course_id)
                ->select(["duration_value"])
                ->first();

            $status = 0;
            $status_finalist = 0;

            if (($ano == 4) || $ano == 5) {
                $status = $this->grades_calculat($disciplines, $studentId);
            }

            if ($ano == $courses_duration->duration_value) {

                if (count($final_note) > 0) {
                    $status_finalist = "1";
                } else {
                    $status_finalist = "2";
                }
            }



            if (isset($lectivo->ano)) {


                switch ($lectivo->ano) {

                    case '20/21':
                        $lectivo->ano = "2020/21";
                        break;
                    case '21/22':
                        $lectivo->ano = "2021/22";
                        break;
                    case '22/23':
                        $lectivo->ano = "2022/23";
                        break;
                    case '23/24':
                        $lectivo->ano = "2023/24";
                        break;
                    case '24/25':
                        $lectivo->ano = "2023/24";
                        break;
                    case '25/26':
                        $lectivo->ano = "2023/24";
                        break;
                }

            } else {
                $ano_final = "";

            }

            // if(auth()->user()->id == 845)dd($disciplines);
            $institution = Institution::latest()->first();

            $code_doc = $this->get_code_doc($requerimento->code, $requerimento->year);

            $pdf = PDF::loadView("Reports::declaration.withNotePercurso", compact(
                'config',
                'cargaHoraria',
                'direitor',
                'cargo',
                'dataActual',
                'userFoto',
                'nascimento',
                'studentInfo',
                'countDisciplines',
                'disciplines',
                'state',
                'ano',
                'lectivo',
                'var',
                'media',
                'efeito',
                'studyPlanEditions',
                'oldGrades',
                'grades',
                'countAllDisciplines',
                'disciplinesAreas',
                'finalDisciplineGrade',
                'institution',
                'secretario',
                'status',
                'status_finalist',
                'final_note',
                'requerimento',
                'ano',
                'code_doc',
                'recibo'
            ));


            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '7mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo', 'code_doc'))->render();

            $pdf->setOption('footer-html', $footer_html);


            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception | Throwable $e) {
            dd($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    private function withNoteEnd($request, $config)
    {



        $studentId = $request->students;
        $type_document = $request->type_document;
        $ano = $request->student_year;
        $efeito = $request->efeito_type;

        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('parameter_option_translations as u_p7', function ($join) {
                $join->on('u_p7.parameter_options_id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('user_parameters as u_p9', function ($join) {
                $join->on('users.id', '=', 'u_p9.users_id')
                    ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
            })
            ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
            })

            ->leftJoin('user_parameters as u_p10', function ($join) {
                $join->on('users.id', '=', 'u_p10.users_id')
                    ->where('u_p10.parameters_id', 15);
            })



            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')

            // ->where("matriculations.course_year",$ano)
            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.display_name as province',
                'u_p10.value as emitido',
                'u_p8.display_name as municipio',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->firstOrFail();

        $lectivo = DB::table('lective_year_translations')
            ->where("lective_years_id", $studentInfo->lectivo)
            ->where("active", 1)
            ->select(["display_name as ano"])
            ->first();

        $pdf_name = "DCNF_" . $studentInfo->number;
        //trazer todas as disciplinas matriculadas
        //anchor


        $allDiscipline = Discipline::whereCoursesId($studentInfo->course_id)
            ->join('courses', 'courses.id', '=', 'disciplines.courses_id')
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->select(['dcp.display_name as disciplina', 'disciplines.id as discipline_id', 'study_plans_has_disciplines.years as years'])
            ->distinct('disciplina')
            ->orderBy('years')
            ->get()
            ->groupBy('years');



        $media = DB::table('new_old_grades as notas')
            ->where('notas.user_id', $studentId)
            ->whereNotIn('notas.discipline_id', $outDisciplines)
            ->avg('notas.grade');


        $finalDisciplineGrade = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->whereIn('discipline_id', $outDisciplines)
            ->distinct()
            ->get();

        if ($finalDisciplineGrade->isNotEmpty() && $finalDisciplineGrade->first()->lective_year > 2020) {
            $var = 1;
        } elseif ($finalDisciplineGrade->isNotEmpty()) {
            $var = 2;
        } else {
            $var = 0;
        }
        //avaliar se a ultima disciplina (trablho de fim de curso) foi aprovada antes de 2021
        if ($var == 1) {
            //listar todos os planos de estudo daquele curso.
            //para o aluno ate o ano maximo que ele esta matriculado.
            //as notas trazer por where between de datas nas notas.
            $studyPlanEditions = StudyPlan::where('courses_id', $studentInfo->course_id)
                ->join('study_plan_editions', 'study_plan_editions.study_plans_id', '=', 'study_plans.id')
                ->join('lective_years', 'lective_years.id', '=', 'study_plan_editions.lective_years_id')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                //->whereNotIn('study_plans_has_disciplines.disciplines_id', $outDisciplines)
                ->select(['lyt.display_name as lective_year', 'study_plans_has_disciplines.disciplines_id as disciplines_id'])
                //   ->whereIn('study_plans_has_disciplines.disciplines_id', $disciplines->pluck('id'))
                ->distinct()
                ->get();

            // $studyPlanEditions = $studyPlanEditions->unique('disciplines_id')->values()->all();
        }


        //trazer as notas do estudantes apartir da ultima a ser lançada.
        //podem vir do Recurso, do Exame ou do MAC
        //no if do blade comparar se a nota foi lancada entre a datas comparando
        //com o start_date and end_date do plano_edicao_de_estudo

        //como nao tenho data na tabela de percentagem vou avaliar cada situacao
        //avaliar, sem tem MAC > 14 ou disciplina que se dispensa.
        //avaliar se tem exame ou recurso.
        //preciso testar a ideia de cima:: TODO
        //mas vou fazer isso na view
        //como segue o modelo MAC -> EXAME -> Recurso -> E.Esp.
        //vou pegar esse modelo e avaliar um por um


        $grades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->get();

        $areas = [13, 14, 15];
        //TODO:: TRAZER SO AS DO PLANO CURRICULAR DO CURSO, NAO TODAS
        $disciplinesAreas = DB::table('discipline_areas')
            ->leftJoin('discipline_areas_translations as dat', function ($join) {
                $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dat.active', '=', DB::raw(true));
            })
            ->whereIn('discipline_areas.id', $areas)
            ->get();


        $studyPlanEditions = $var == 1 ? $studyPlanEditions : "";


        $userFoto = User::whereId($studentId)->with([

            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();
        $courses = Course::with([
            'currentTranslation'
        ])->get();


        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;
        $dataActual = $this->dataActual();
        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $secretario = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->secretaria_academica)
            ->where('usuario_cargo.role_id', 18)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

            ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
            ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
            ->where('disciplines.courses_id', $studentInfo->course_id)
            ->distinct(['id_disciplina', 'hora'])
            ->get();

        $cargaHoraria = $cargaHoraria->unique('id_disciplina')
            ->values()
            ->all();




        $discp = 0;
        foreach ($allDiscipline as $item) {
            foreach ($item as $disciplinas) {
                $discp++;
            }
        }



        if ($discp > count($grades)) {
            Toastr::warning(__('O Estudante não tem as notas regularizadas'), __('toastr.warning'));
            return redirect()->back();
        }

        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'cargo' => $cargo,
            'efeito' => $efeito,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'year_document' => $ano,
            'var' => $var,
            'studyPlanEditions' => $studyPlanEditions,
            'grades' => $grades,
            'disciplinesAreas' => $disciplinesAreas,
            'finalDisciplineGrade' => $finalDisciplineGrade,
            'userFoto' => $userFoto,
            'secretario' => $secretario
        ];

        $nascimento = $this->dataEscrita($studentInfo->barthday);

        // return view("Reports::declaration.withNotePercurso")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.declarationFinal", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'cargo',
            'dataActual',
            'userFoto',
            'nascimento',
            'studentInfo',
            'ano',
            'lectivo',
            'var',
            'media',
            'efeito',
            'grades',
            'allDiscipline',
            'disciplinesAreas',
            'finalDisciplineGrade',
            'institution',
            'secretario'

        ));


        $pdf->setOption('margin-top', '3mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');



        return $pdf->stream($pdf_name . '.pdf');
    }




    private function CertificateMerito($request, $config)
    {


        switch ($request->tipo) {
            case 2:
                return $this->CertificateMeritoDocente($request, $config);
                break;

            case 3:
                return $this->CertificateMeritoFuncionario($request, $config);
                break;
        }



        $tipo = $request->tipo;
        $studentId = $request->students;
        $ano = $request->student_year;
        $type_document = $request->type_document;

        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->where('sphd.years', $ano)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "asc")
            ->select(["sphd.years as ano", "notas.lective_year as ano_lectivo"])
            ->get();
        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->where('sphd.years', $ano)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');
        // ->get();



        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('parameter_options as u_p7', function ($join) {
                $join->on('u_p7.id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })


            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')

            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.code as province',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'courses.id as course_id'

            ])
            ->firstOrFail();


        $pdf_name = "CM_" . $studentInfo->number;


        $userFoto = User::whereId($studentId)->with([

            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();
        $courses = Course::with([
            'currentTranslation'
        ])->get();


        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();


        $nome = preg_split("/((de|da|do|dos|das)?)[\s,_-]+/", $institution->nome);

        $iniciais = "";
        foreach ($nome as $n) {
            if (strlen($n) > 0) {
                $iniciais .= $n[0] . ".";
            }
        }

        $iniciais = substr($iniciais, 0, (strlen($iniciais) - 1));



        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();





        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'anos' => $anos,
            'media' => $media,
            'tipo' => $tipo

        ];



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.certificadoMerito", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'anos',
            'media',
            'iniciais',
            'tipo'

        ));

        $pdf->setOption('margin-top', '0mm');
        $pdf->setOption('margin-left', '0mm');
        $pdf->setOption('margin-bottom', '0mm');
        $pdf->setOption('margin-right', '0mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        if ($config->rodape == 1) {
            //  $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            // $pdf->setOption('footer-html', $footer_html);
        }

        return $pdf->stream($pdf_name . '.pdf');
    }



    private function CertificateMeritoDocente($request, $config)
    {




        $tipo = $request->tipo;
        $studentId = $request->students;

        $departamento = $request->departamento;
        $ano = $request->lective_year;


        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->where("id", $ano)
            ->firstOrFail();

        $anos = $lectiveYears->currentTranslation->display_name;


        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->select([
                'u_p1.value as name'
            ])
            ->firstOrFail();


        $departamento = Department::join('users as u1', 'u1.id', '=', 'departments.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'departments.updated_by')
            ->leftJoin('users as u3', 'u3.id', '=', 'departments.deleted_by')
            ->leftJoin('department_translations as dt', function ($join) {
                $join->on('dt.departments_id', '=', 'departments.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->where('dt.departments_id', $departamento)
            ->select([
                'dt.display_name'
            ])
            ->firstOrFail();


        $pdf_name = "CM_" . $studentInfo->number;



        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $nome = preg_split("/((de|da|do|dos|das)?)[\s,_-]+/", $institution->nome);

        $iniciais = "";
        foreach ($nome as $n) {
            if (strlen($n) > 0) {
                $iniciais .= $n[0];
            }
        }

        $iniciais = substr($iniciais, 0, (strlen($iniciais)));

        $pdf = PDF::loadView("Reports::declaration.certificadoMerito", compact(
            'config',
            'studentInfo',
            'direitor',
            'cargo',
            'dataActual',
            'institution',
            'iniciais',
            'departamento',
            'tipo',
            'anos'
        ));

        $pdf->setOption('margin-top', '0mm');
        $pdf->setOption('margin-left', '0mm');
        $pdf->setOption('margin-bottom', '0mm');
        $pdf->setOption('margin-right', '0mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($pdf_name . '.pdf');
    }
    private function CertificateMeritoFuncionario($request, $config)
    {




        $tipo = $request->tipo;
        $studentId = $request->students;


        $ano = $request->lective_year;
        $seccao = $request->seccao;


        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->where("id", $ano)
            ->firstOrFail();

        $anos = $lectiveYears->currentTranslation->display_name;


        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->select([
                'u_p1.value as name'
            ])
            ->firstOrFail();


        $pdf_name = "CM_" . $studentInfo->number;



        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $nome = preg_split("/((de|da|do|dos|das)?)[\s,_-]+/", $institution->nome);

        $iniciais = "";
        foreach ($nome as $n) {
            if (strlen($n) > 0) {
                $iniciais .= $n[0];
            }
        }

        $iniciais = substr($iniciais, 0, (strlen($iniciais)));

        $pdf = PDF::loadView("Reports::declaration.certificadoMerito", compact(
            'config',
            'studentInfo',
            'direitor',
            'cargo',
            'dataActual',
            'institution',
            'iniciais',
            'seccao',
            'tipo',
            'anos'
        ));

        $pdf->setOption('margin-top', '0mm');
        $pdf->setOption('margin-left', '0mm');
        $pdf->setOption('margin-bottom', '0mm');
        $pdf->setOption('margin-right', '0mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($pdf_name . '.pdf');
    }




    private function finalCertificate($request, $config)
    {



        $studentId = $request->students;
        $type_document = $request->type_document;

        $requerimento = DB::table('requerimento')
            ->where('id', $request->requerimento)
            ->first();




        // $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];
        $outDisciplines = [];
        $studentInfo = User::where('users.id', $studentId)
            ->join('model_has_roles as usuario_cargo', function ($join) {
                $join->on('users.id', '=', 'usuario_cargo.model_id')
                    ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                    ->where('usuario_cargo.role_id', 6);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('parameter_options as u_p7', function ($join) {
                $join->on('u_p7.id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })


            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })


            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.code as province',
                'ct.display_name as course',
                'courses.id as course_id'
            ])
            ->firstOrFail();




        $nascimento = $this->dataEscrita($studentInfo->barthday);
        //  Esse trecho de código foi primeiro que a Porra do Francisco , conseguiu me dar atenção 
        // ele está a rolling
        // Pegamos as disciplinas agrupadas por anos 

        $allDiscipline = Discipline::whereCoursesId($studentInfo->course_id)
            ->join('courses', 'courses.id', '=', 'disciplines.courses_id')
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->select(['dcp.display_name as disciplina', 'disciplines.id as discipline_id', 'study_plans_has_disciplines.years as years'])
            ->distinct('disciplina')
            ->orderBy('years')
            ->get()
            ->groupBy('years');

        // Carga horária
        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

            ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
            ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
            ->where('disciplines.courses_id', $studentInfo->course_id)
            ->distinct(['id_disciplina', 'hora'])
            ->get();

        $cargaHoraria = $cargaHoraria->unique('id_disciplina')
            ->values()
            ->all();


        // Todas as notas 
        $grades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->get();
        $state = DB::table('users_states')
            ->join('states', 'states.id', '=', 'users_states.state_id')
            ->where('user_id', $studentId)
            ->first();
        $state = $state->name ?? 'N/A';


        $final = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("nota", "desc")
            ->select(["notas.tfc_trabalho as nota"])
            ->first();
        // ->select('notas.gr ade');



        if (isset($final)) {
            $final = round($final->nota);
        } else {
            $final = 0;
        }


        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');



        $tfc = Discipline::whereCoursesId($studentInfo->course_id)
            ->join('courses', 'courses.id', '=', 'disciplines.courses_id')
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->where("dcp.display_name", "like", "%fim de curso%")
            ->select([
                'dcp.display_name as disciplina',
                'disciplines.id as discipline_id',
                'study_plans_has_disciplines.years as years'
            ])
            ->distinct('disciplina')
            ->orderBy('years')
            ->first();











        $areas = [13, 14, 15];
        //TODO:: TRAZER SO AS DO PLANO CURRICULAR DO CURSO, NAO TODAS
        $disciplinesAreas = DB::table('discipline_areas')
            ->leftJoin('discipline_areas_translations as dat', function ($join) {
                $join->on('dat.discipline_areas_id', '=', 'discipline_areas.id');
                $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dat.active', '=', DB::raw(true));
            })
            ->whereIn('discipline_areas.id', $areas)
            ->get();

        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.
        $institution = Institution::latest()->first();


        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
            ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
            ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
            ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
            ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
            ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p0.parameters_id', 1)
            ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
            ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
            ->where('rt.active', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p0.value as nome_completo',
                'ga.nome as grau_academico',
                'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        $dataActual = $this->dataActual();

        $recibo = $recibo = $this->referenceGetRecibo($requerimento->article_id);

        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'grades' => $grades,
            'allDiscipline' => $allDiscipline,
            'final' => $final,
            'nascimento' => $nascimento,
            'requerimento' => $requerimento,
            'recibo' => $recibo

        ];

        // return ($grades);

        // return view("Reports::declaration.certificadofinal")->with($data);
        $institution = Institution::latest()->first();

        $discp = 0;
        foreach ($allDiscipline as $item) {
            foreach ($item as $disciplinas) {
                $discp++;
            }
        }

        // return ($nada);  

        if ($discp > count($grades)) {
            Toastr::warning(__('O Estudante não tem as notas regularizadas'), __('toastr.warning'));
            return redirect()->back();
        }


        $pdf = PDF::loadView("Reports::declaration.certificadofinal", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'dataActual',
            'studentInfo',
            'grades',
            'allDiscipline',
            'institution',
            'final',
            'nascimento',
            'media',
            'requerimento',
            'recibo'

        ));


        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');


        $pdf_name = "CERTIFICADO_" . $studentInfo->number;


        return $pdf->stream($pdf_name . '.pdf');
    }




    private function dataEscrita($dia)
    {

        $data = explode("-", $dia);


        $m = $data[1];
        $d = $data[2];
        $ano = $data[0];
        $mes = array(
            "01" => "Janeiro",
            "02" => "Fevereiro",
            "03" => "Março",
            "04" => "Abril",
            "05" => "Maio",
            "06" => "Junho",
            "07" => "Julho",
            "08" => "Agosto",
            "09" => "Setembro",
            "10" => "Outubro",
            "11" => "Novembro",
            "12" => "Dezembro"
        );
        $nascimento = $d . " de " . $mes[$m] . " de " . $ano;
        return $nascimento;
    }

    // Código numérico para o diploma dos estudantes

    private function codigo_numerico($curso)
    {


        // Listar o curso 

        $courses = DB::table('courses as curso')
            ->where('curso.id', $curso)
            ->select(["curso.code as codigo"])
            ->get();

        $codigo = array(
            "BIO" => "1",
            "COA" => "2",
            "DTO" => "3",
            "EFD" => "4",
            "ECON" => "5",
            "GEO" => "6",
            "GEE" => "7",
            "GRH" => "8",
            "HIS" => "9",
            "INF" => "10",
            "PED" => "11",
            "PSG" => "12",
            "PSJ" => "13",
            "RI" => "14",
            "SOC" => "15",
            "EDI" => "16",
            "EP" => "17",
            "CEE" => "0"
        );

        $cd = 0;

        foreach ($codigo as $key => $value) {

            if ($key == $courses[0]->codigo) {

                # Pegar o número

                $cd = $value;


            }


        }


        return $cd;
    }

    private function gerar_codigo_diploma($estudante)
    {

        # Verificar se o estudante já possui um diploma

        $cdn = 0;

        $cde = DB::table('estudante_diploma')
            ->where('user_id', "=", $estudante)
            ->select(["diploma", "data_final", "data_outorga", "n_registro"])
            ->first();

        return [$cde->diploma, $cde->data_final, $cde->data_outorga, $cde->n_registro];



        # Gerar um novo código número

    }






























































































    // ------------ ------------ ------------ ------------ ------------ ------------ ------------ ------------//


    private function withoutNote($request, $config)
    {

        $studentId = $request->students;
        $type_document = $request->type_document;
        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('parameter_options as u_p17', function ($join) {
                $join->on('u_p17.id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('user_parameters as u_p7', function ($join) {
                $join->on('users.id', '=', 'u_p7.users_id')
                    ->where('u_p7.parameters_id', 15);
            })

            ->leftJoin('user_parameters as u_p9', function ($join) {
                $join->on('users.id', '=', 'u_p9.users_id')
                    ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
            })
            ->leftJoin('parameter_options as u_p8', function ($join) {
                $join->on('u_p8.id', '=', 'u_p9.value');
            })

            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')

            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.value as emitido',
                'u_p8.code as municipio',
                'u_p17.code as province',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->firstOrFail();

        $pdf_name = "DSN_" . $studentInfo->number;
        //dd($studentInfo);
        //Pegar o direitor institucional 
        //cláudio 

        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();
        $secretario = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->secretaria_academica)
            ->where('usuario_cargo.role_id', 18)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();
        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;
        $dataActual = $this->dataActual();

        // return view('Reports::declaration.notaDeclaration',compact('studentInfo', 'direitor','cargo','dataActual'));
        // return view('Reports::declaration.normalDeclaration',compact('studentInfo', 'direitor','cargo','dataActual','config'));


        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.normalDeclaration", compact('studentInfo', 'secretario', 'direitor', 'cargo', 'dataActual', 'config', 'institution'));
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');


        if ($config->rodape == 1) {
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
        }


        return $pdf->stream($pdf_name . '.pdf');
    }




    // ------------ ------------ ------------ ------------ ------------ ------------ ------------ ------------//



    private function diplomas($request, $config)
    {


        $studentId = $request->students;
        $type_document = $request->type_document;


        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->join('model_has_roles as usuario_cargo', function ($join) {
                $join->on('users.id', '=', 'usuario_cargo.model_id')
                    ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                    ->where('usuario_cargo.role_id', 6);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('parameter_options as u_p7', function ($join) {
                $join->on('u_p7.id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })


            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })


            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.code as province',
                'ct.display_name as course',
                'courses.id as course_id'
            ])
            ->firstOrFail();


        # Pegar o código do diploma

        $cd = $this->gerar_codigo_diploma($studentId);

        $codigo_diploma = $cd[0];
        $ano_diploma = $cd[2];
        $ano_conclusao = $cd[1];

        $n_registro = $cd[3];

        # Pegar o código do curso

        $codigo_curso = $this->codigo_numerico($studentInfo->course_id);

        # Formatar da data de nascimento

        $nascimento = $this->dataEscrita($studentInfo->barthday);

        # Formatar da data do diploma

        $data_diploma = $this->dataEscrita($ano_diploma);
        $data_conclusao = $this->dataEscrita($ano_conclusao);

        $pdf_name = "DIPLOMA_" . $studentInfo->number;


        $userFoto = User::whereId($studentId)->with([

            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();



        $courses = Course::with([
            'currentTranslation'
        ])->get();



        $final = DB::table('new_old_grades as notas')
            ->where('notas.user_id', $studentId)

            ->avg('notas.grade');

        if (isset($final)) {
            $final = round($final);
        } else {
            $final = 0;
        }





        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
            ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
            ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
            ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
            ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
            ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p0.parameters_id', 1)
            ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
            ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
            ->where('rt.active', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p0.value as nome_completo',
                'ga.nome as grau_academico',
                'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();





        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'nascimento' => $nascimento,
            'final' => $final,
            'n_registro' => $n_registro,
            'data_conclusao' => $data_conclusao

        ];



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.diplomas", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'codigo_curso',
            'codigo_diploma',
            'nascimento',
            'ano_diploma',
            'data_diploma',
            'final',
            'n_registro',
            'data_conclusao'

        ));

        $pdf->setOption('margin-top', '10mm');
        $pdf->setOption('margin-left', '10mm');
        $pdf->setOption('margin-bottom', '10mm');
        $pdf->setOption('margin-right', '10mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'landscape');

        if ($config->rodape == 1) {
            //  $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
        }

        return $pdf->stream($pdf_name . '.pdf');
    }



    // -------------------------------------------------------------------------------------------------------- //


    private function frequencia($request, $config)
    {

        $this->gerar_codigo_documento($request->requerimento);
        $requerimento = DB::table('requerimento')
            ->where("id", $request->requerimento)
            ->first();

        $recibo = $this->referenceGetRecibo($requerimento->article_id);

        $studentId = $request->students;
        $type_document = $request->type_document;
        $efeito = $request->efeito_type;
        $ano = $request->student_year;

        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "asc")
            ->select(["sphd.years as ano", "notas.lective_year as ano_lectivo"])
            ->get();
        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');
        // ->get();



        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('parameter_option_translations as u_p17', function ($join) {
                $join->on('u_p17.parameter_options_id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('user_parameters as u_p7', function ($join) {
                $join->on('users.id', '=', 'u_p7.users_id')
                    ->where('u_p7.parameters_id', 15);
            })

            ->leftJoin('user_parameters as u_p9', function ($join) {
                $join->on('users.id', '=', 'u_p9.users_id')
                    ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
            })
            ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
            })

            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
            // ->where("matriculations.course_year",$ano)
            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.value as emitido',
                'u_p8.display_name as municipio',
                'u_p17.display_name as province',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->orderBy("matriculations.course_year", "desc")
            ->firstOrfail();


        $lectivo = DB::table('lective_year_translations')
            ->where("lective_years_id", $studentInfo->lectivo)
            ->where("active", 1)
            ->select(["display_name as ano"])
            ->first();




        $string = str_replace('/', '_', $requerimento->code);
        $pdf_name = "DdF_" . $string;

        $nascimento = $this->dataEscrita($studentInfo->barthday);

        $userFoto = User::whereId($studentId)->with([
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();

        $courses = Course::with([
            'currentTranslation'
        ])->get();


        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
            ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
            ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
            ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
            ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
            ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
            ->where('usuario.id', $institution->vice_director_academica)
            ->where('usuario_cargo.role_id', 9)
            ->where('u_p0.parameters_id', 1)
            ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
            ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
            ->where('rt.active', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p0.value as nome_completo',
                'ga.nome as grau_academico',
                'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();





        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'anos' => $anos,
            'media' => $media,
            'efeito' => $efeito,
            'lectivo' => $lectivo,
            "nascimento" => $nascimento

        ];



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.frequencia", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'anos',
            'lectivo',
            'media',
            'efeito',
            'nascimento',
            "requerimento",
            "recibo"

        ));

        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        $code_doc = $this->get_code_doc($requerimento->code, $requerimento->year);
        $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo', 'code_doc'))->render();

        $pdf->setOption('footer-html', $footer_html);

        if ($config->rodape == 1) {
            //  $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
        }

        return $pdf->stream($pdf_name . '.pdf');
    }


    private function semnotas($request, $config)
    {
        $this->gerar_codigo_documento($request->requerimento);
        $requerimento = DB::table('requerimento')
            ->where("id", $request->requerimento)
            ->first();

        $recibo = $this->referenceGetRecibo($requerimento->article_id);

        $studentId = $request->students;
        $type_document = $request->type_document;
        $efeito = $request->efeito_type;
        $ano = $request->student_year;

        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "asc")
            ->select(["sphd.years as ano", "notas.lective_year as ano_lectivo"])
            ->get();
        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');
        // ->get();



        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('parameter_option_translations as u_p17', function ($join) {
                $join->on('u_p17.parameter_options_id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('user_parameters as u_p7', function ($join) {
                $join->on('users.id', '=', 'u_p7.users_id')
                    ->where('u_p7.parameters_id', 15);
            })

            ->leftJoin('user_parameters as u_p9', function ($join) {
                $join->on('users.id', '=', 'u_p9.users_id')
                    ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
            })
            ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
            })

            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
            ->where("matriculations.course_year", $ano)
            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.value as emitido',
                'u_p8.display_name as municipio',
                'u_p17.display_name as province',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->firstOrFail();


        $lectivo = DB::table('lective_year_translations')
            ->where("lective_years_id", $studentInfo->lectivo)
            ->where("active", 1)
            ->select(["display_name as ano"])
            ->first();





        $pdf_name = "DF" . $studentInfo->number;

        $nascimento = $this->dataEscrita($studentInfo->barthday);

        $userFoto = User::whereId($studentId)->with([

            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();
        $courses = Course::with([
            'currentTranslation'
        ])->get();


        //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->director_geral)
            ->where('usuario_cargo.role_id', 8)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $secretario = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
            ->where('usuario.id', $institution->secretaria_academica)
            ->where('usuario_cargo.role_id', 18)
            ->where('u_p.parameters_id', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p.value'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();


        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();



        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();





        $data = [

            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'secretario' => $secretario,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'anos' => $anos,
            'media' => $media,
            'efeito' => $efeito,
            'lectivo' => $lectivo,
            "nascimento" => $nascimento


        ];



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.normalDeclaration", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'secretario',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'anos',
            'lectivo',
            'media',
            'efeito',
            'nascimento',
            'requerimento',
            "recibo"

        ));

        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        $code_doc = $this->get_code_doc($requerimento->code, $requerimento->year);
        $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo', 'code_doc'))->render();
        $pdf->setOption('footer-html', $footer_html);

        if ($config->rodape == 1) {
            //  $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            // $pdf->setOption('footer-html', $footer_html);
        }

        return $pdf->stream($pdf_name . '.pdf');
    }



    public function referenceGetRecibo($article)
    {
        // Pegando os dados completos da referência

        $transacao = DB::table('transaction_info as tinfo')
            ->leftJoin('transaction_receipts as tr', 'tr.transaction_id', "=", "tinfo.transaction_id")
            ->leftJoin('transaction_article_requests as tar', 'tar.transaction_id', "=", "tinfo.transaction_id")
            ->leftJoin('banks as bk', 'bk.id', "=", "tinfo.bank_id")
            ->leftJoin('article_requests as ar', 'ar.id', "=", "tar.article_request_id")
            ->leftJoin('user_parameters as up', 'up.users_id', "=", "ar.user_id")
            ->leftJoin('article_translations as at', 'at.article_id', "=", "ar.article_id")
            ->leftJoin('articles as art', 'art.id', "=", "ar.article_id")
            ->where('ar.id', $article)
            ->where('up.parameters_id', 1)
            ->where('at.active', 1)
            ->select([
                "tinfo.reference as referencia",
                "ar.id as article_request_id",
                "tr.path as recibo",
                /*"tinfo.fulfilled_at as dia"
                 "bk.display_name as banco",
                 "ar.user_id as estudante_id",
                 "ar.article_id as article_id",
                 "ar.year as ano",
                 "ar.month as mes",
                 "up.value as estudante_nome",
                 "art.code as code_emolumento", 
                 */
                "at.display_name as emolumento"
            ])
            ->get();

        $usuario = DB::table('users')->select(['name as nome'])->where('id', auth()->user()->id)->get();

        // $json['success'] = $transacao;
        $n_recibo = $transacao[0]->recibo;
        $rt = explode('-', explode('/', $n_recibo)[3]);


        $recibo = $rt[1] . "-" . explode('.', $rt[2])[0];

        // $json=[
        //     // 'info'=>$transacao[0],
        //     // 'nome'=>$usuario[0]->nome,
        //     'recibo'=>$recibo 
        // ];

        return $recibo;

    }

    private function gerar_codigo_documento($requerimento)
    {

        $exist = DB::table('requerimento')
            ->where('id', "=", $requerimento)->select(["code"])->first();


        if ($exist->code > 0) {
            return "Existe";
        }

        $last_code = DB::table('requerimento')
            ->where('id', '!=', $requerimento)
            ->whereNotNull('code')
            ->select("code", "year")
            ->orderBy("id", "desc")
            ->first();


        $code = $last_code->code;
        if (isset($last_code->code) && ($last_code->year == date("Y"))) {
            $code = 1 + intval($last_code->code);
        } else {
            $code = 1;
        }

        DB::table('requerimento')
            ->where('id', "=", $requerimento)
            ->update([
                "code" => $code,
                "year" => date("Y")
            ]);

        return $code;

    }









    // Method pega curso 
    #Cláudio Salvador
    public function course_documentation()
    {
        $courses = Course::with('currentTranslation')->get();
        return response()->json($courses);
    }

    // Method pega estudantes do respectivos cursos
    #Cláudio Salvador
    public function studants_course_documentation($courseId)
    {
        $students = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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
            ->where('uc.courses_id', $courseId)
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

    private function exame_acesso($request, $config)
    {

        try {




            $this->gerar_codigo_documento($request->requerimento);

            $requerimento = DB::table('requerimento')
                ->where("id", $request->requerimento)
                ->first();


            $recibo = $this->referenceGetRecibo($requerimento->article_id);




            $type_document = $request->type_document;
            $efeito = $request->efeito_type;
            $ano = $request->student_year;
            $studentId = $request->students;

            $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

            $studentInfo = User::where('users.id', $studentId)
                ->leftJoin('user_parameters as u_p1', function ($join) {
                    $join->on('users.id', '=', 'u_p1.users_id')
                        ->where('u_p1.parameters_id', 1);
                })
                ->leftJoin('user_parameters as u_p2', function ($join) {
                    $join->on('users.id', '=', 'u_p2.users_id')
                        ->where('u_p2.parameters_id', 23);
                })
                ->leftJoin('user_parameters as u_p3', function ($join) {
                    $join->on('users.id', '=', 'u_p3.users_id')
                        ->where('u_p3.parameters_id', 24);
                })
                ->leftJoin('user_parameters as u_p4', function ($join) {
                    $join->on('users.id', '=', 'u_p4.users_id')
                        ->where('u_p4.parameters_id', 5);
                })
                ->leftJoin('user_parameters as u_p5', function ($join) {
                    $join->on('users.id', '=', 'u_p5.users_id')
                        ->where('u_p5.parameters_id', 14);
                })

                ->leftJoin('user_parameters as u_p6', function ($join) {
                    $join->on('users.id', '=', 'u_p6.users_id')
                        ->where('u_p6.parameters_id', 150);
                })
                ->leftJoin('parameter_option_translations as u_p17', function ($join) {
                    $join->on('u_p17.parameter_options_id', '=', 'u_p6.value')
                        ->where('u_p6.parameters_id', 150);
                })

                ->leftJoin('user_parameters as u_p7', function ($join) {
                    $join->on('users.id', '=', 'u_p7.users_id')
                        ->where('u_p7.parameters_id', 15);
                })

                ->leftJoin('user_parameters as u_p9', function ($join) {
                    $join->on('users.id', '=', 'u_p9.users_id')
                        ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
                })
                ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                    $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
                })
                ->leftJoin('user_candidate as uc', 'users.id', '=', 'uc.user_id')

                ->select([
                    'u_p1.value as name',
                    'u_p2.value as dad',
                    'u_p3.value as mam',
                    'u_p4.value as barthday',
                    'u_p5.value as bi',
                    'u_p7.value as emitido',
                    'u_p8.display_name as municipio',
                    'u_p17.display_name as province',
                    'uc.code as code'

                ])
                ->firstOrfail();


            $lectivo = DB::table('lective_year_translations')
                ->where("lective_years_id", $studentInfo->lectivo)
                ->where("active", 1)
                ->select(["display_name as ano"])
                ->first();






            $pdf_name = "DCNEA" . "_" . $studentInfo->code;

            $nascimento = $this->dataEscrita($studentInfo->barthday);

            $userFoto = User::whereId($studentId)->with([

                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                    ]);
                }
            ])->firstOrFail();
            $courses = Course::with([
                'currentTranslation'
            ])->get();


            //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
            //Neste caso o direitor.

            $institution = Institution::latest()->first();


            $direitor = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftjoin('user_parameters as u_p', 'usuario.id', '=', 'u_p.users_id')
                ->where('usuario.id', $institution->director_geral)
                ->where('usuario_cargo.role_id', 8)
                ->where('u_p.parameters_id', 1)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->select([
                    'usuario.id as id_usuario',
                    'usuario.email as email_usuario',
                    'usuario.name as name',
                    'u_p.value as nome_completo'
                ])
                ->orderBy('usuario.name')
                ->groupBy('id_usuario')
                ->first();


            //  pega o cargo
            $role = Role::whereId(8)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();
            $cargo = $role->translations[0]->description;

            $dataActual = $this->dataActual();



            $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
                ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
                ->get();


            // Aqui!
            $id_usuario = $direitor->id_usuario;

            // buscando o grau academico do director
            $grau_academico_director = DB::table('user_parameters as up')
                ->where('users_id', '=', $id_usuario)
                ->where('parameters_id', '=', ParameterEnum::GRAU_ACADEMICO)
                ->join('grau_academico as ga', 'up.value', '=', 'ga.id')
                ->pluck('nome')
                ->first();

            // buscando o grau academico do director
            $categoria_profissional_director = DB::table('user_parameters as up')
                ->where('users_id', '=', $id_usuario)
                ->where('parameters_id', '=', ParameterEnum::CATEGORIA_PROFISSIONAL)
                ->join('categoria_profissional as cp', 'up.value', '=', 'cp.id')
                ->pluck('nome')
                ->first();

            $grades = DB::table('grades as g')
                ->where('student_id', $studentId)
                ->join('disciplines as d', 'g.discipline_id', '=', 'd.id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->where('d.discipline_profiles_id', 8)
                ->select(['g.value as nota', 'dt.display_name as disciplina', 'd.percentagem as percentagem'])
                ->get();

            $matriculationYear = DB::table('user_candidate as uc')
                ->where('user_id', $studentId)
                ->join('lective_years as ly', 'uc.year', '=', 'ly.id')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'ly.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->pluck('lyt.display_name')
                ->first();



            //Update

            if (isset($grades[0]->nota, $grades[1]->nota)) {
                $resultado = round(($grades[0]->nota * ($grades[0]->percentagem / 100)) + ($grades[1]->nota * ($grades[1]->percentagem / 100)));
            } else if (isset($grades[0]->nota) && !isset($grades[1]->nota)) {
                $resultado = $grades[0]->nota;
            } else {
                $resultado = round((($grades[0]->nota ?? 0) + ($grades[1]->nota ?? 0)) / 2);
            }




            $data = [

                'config' => $config,
                'cargaHoraria' => $cargaHoraria,
                'direitor' => $direitor,
                'studentInfo' => $studentInfo,
                'dataActual' => $dataActual,
                'efeito' => $efeito,
                'lectivo' => $lectivo,
                "nascimento" => $nascimento,
                "grau_academico_director" => $grau_academico_director,
                "categoria_profissional_director" => $categoria_profissional_director,
                'matriculationYear' => $matriculationYear,
                'grades' => $grades,
                'resultado' => $resultado

            ];


            $institution = Institution::latest()->first();

            $pdf = PDF::loadView("Reports::declaration.exame-acesso", compact(
                'config',
                'cargaHoraria',
                'direitor',
                'grau_academico_director',
                'categoria_profissional_director',
                'dataActual',
                'studentInfo',
                'institution',
                'lectivo',
                'efeito',
                'nascimento',
                "requerimento",
                "recibo",
                'matriculationYear',
                'grades',
                'resultado'

            ));

            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '12mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            $code_doc = $this->get_code_doc($requerimento->code, $requerimento->year);
            $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo', 'code_doc'))->render();
            if ($config->rodape == 1) {
                $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution'))->render();
                $pdf->setOption('footer-html', $footer_html);
            }

            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception | Throwable $e) {

            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }


    private function pedido_entrada($request, $config){
        try {
            
            $this->gerar_codigo_documento($request->requerimento);
            //dd($request->requerimento);
            $requerimento = DB::table('requerimento')
                ->where("id", $request->requerimento)
                ->first();
            


            $recibo = $this->referenceGetRecibo($requerimento->article_id);

            $lective = DB::table('lective_years as ly')->where('is_termina', 0)
                ->join('lective_year_translations as lyt', 'ly.id', '=', 'lyt.lective_years_id')
                ->where('lyt.active', 1)
                ->select('ly.id')
                ->pluck('id')
                ->first();


            $type_document = $request->type_document;
            $efeito = $request->efeito_type;
            $ano = $request->student_year;
            $studentId = $request->students;

            $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

            $studentInfo = User::where('users.id', $studentId)
                ->leftJoin('user_parameters as u_p0', function ($join) {
                    $join->on('users.id', '=', 'u_p0.users_id')
                        ->where('u_p0.parameters_id', 19);
                })
                ->leftJoin('user_parameters as u_p1', function ($join) {
                    $join->on('users.id', '=', 'u_p1.users_id')
                        ->where('u_p1.parameters_id', 1);
                })
                ->leftJoin('user_parameters as u_p2', function ($join) {
                    $join->on('users.id', '=', 'u_p2.users_id')
                        ->where('u_p2.parameters_id', 23);
                })
                ->leftJoin('user_parameters as u_p3', function ($join) {
                    $join->on('users.id', '=', 'u_p3.users_id')
                        ->where('u_p3.parameters_id', 24);
                })
                ->leftJoin('user_parameters as u_p4', function ($join) {
                    $join->on('users.id', '=', 'u_p4.users_id')
                        ->where('u_p4.parameters_id', 5);
                })
                ->leftJoin('user_parameters as u_p5', function ($join) {
                    $join->on('users.id', '=', 'u_p5.users_id')
                        ->where('u_p5.parameters_id', 14);
                })

                ->leftJoin('user_parameters as u_p6', function ($join) {
                    $join->on('users.id', '=', 'u_p6.users_id')
                        ->where('u_p6.parameters_id', 150);
                })

                ->leftJoin('parameter_option_translations as u_p7', function ($join) {
                    $join->on('u_p7.parameter_options_id', '=', 'u_p6.value')
                        ->where('u_p6.parameters_id', 150);
                })
                ->leftJoin('user_parameters as u_p9', function ($join) {
                    $join->on('users.id', '=', 'u_p9.users_id')
                        ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
                })
                ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                    $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
                })
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('matriculations', function ($join) use ($lective, $studentId) {
                    $join->on('matriculations.user_id', '=', 'users.id');
                    $join->where('matriculations.lective_year', $lective);
                    $join->where('matriculations.user_id', $studentId);
                })
                ->leftJoin('user_parameters as u_p10', function ($join) {
                    $join->on('users.id', '=', 'u_p10.users_id')
                        ->where('u_p10.parameters_id', 15);
                })
                ->select([
                    'u_p0.value as number',
                    'u_p1.value as name',
                    'u_p2.value as dad',
                    'u_p3.value as mam',
                    'u_p4.value as barthday',
                    'u_p5.value as bi',
                    'u_p7.display_name as province',
                    'u_p8.display_name as municipio',
                    'ct.display_name as course',
                    'matriculations.course_year as year',
                    'matriculations.lective_year as lectivo',
                    'courses.id as course_id',
                    'u_p10.value as emitido'
                ])
                ->where('users.id', $studentId)
                ->first();


            $lectivo = DB::table('lective_year_translations')
                ->where("lective_years_id", $studentInfo->lectivo)
                ->where("active", 1)
                ->select(["display_name as ano"])
                ->first();





            $string = str_replace('/', '_', $requerimento->code);
            $pdf_name = "PdTE" . "_" . $string;

            $nascimento = $this->dataEscrita($studentInfo->barthday);

            $userFoto = User::whereId($studentId)->with([

                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                    ]);
                }
            ])->firstOrFail();
            $courses = Course::with([
                'currentTranslation'
            ])->get();


            //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
            //Neste caso o direitor.

            $institution = Institution::latest()->first();


            $direitor = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
                ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
                ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
                ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
                ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
                ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
                ->where('usuario.id', $institution->vice_director_academica)
                ->where('usuario_cargo.role_id', 9)
                ->where('u_p0.parameters_id', 1)
                ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
                ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
                ->where('rt.active', 1)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->select([
                    'usuario.id as id_usuario',
                    'usuario.email as email_usuario',
                    'usuario.name as name',
                    'u_p0.value as nome_completo',
                    'ga.nome as grau_academico',
                    'cp.nome as categoria_profissional',
                    'rt.display_name as cargo'
                ])
                ->orderBy('usuario.name')
                ->groupBy('id_usuario')
                ->first();


            //  pega o cargo
            $role = Role::whereId(8)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();
            $cargo = $role->translations[0]->description;

            $dataActual = $this->dataActual();



            $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
                ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
                ->get();



            $matriculationYear = DB::table('user_candidate as uc')
                ->where('user_id', $studentId)
                ->join('lective_years as ly', 'uc.year', '=', 'ly.id')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'ly.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->pluck('lyt.display_name')
                ->first();


            //dd($requerimento->transference_request);
            $transference = DB::table('tb_transference_studant')
                ->where('id', $requerimento->transference_request)
                ->first();

            $documentation = null;

            if (is_null($transference) || is_null($transference->documentation)) {
                Toastr::warning('Nenhum dado foi encontrado para esta solicitação.', 'Atenção');
                return redirect()->back();
            } else {
                $documentation = $transference->documentation;
            }

            //$documentation = $transference->documentation;
            $documentation = preg_split('/\r\n|\r|\n/', $documentation);
                
            $data = [

                'config' => $config,
                'cargaHoraria' => $cargaHoraria,
                'direitor' => $direitor,
                'studentInfo' => $studentInfo,
                'dataActual' => $dataActual,
                'efeito' => $efeito,
                'lectivo' => $lectivo,
                "nascimento" => $nascimento,
                'matriculationYear' => $matriculationYear,
                'transference' => $transference,
                'documentation' => $documentation

            ];
        
            $institution = Institution::latest()->first();

            $pdf = PDF::loadView("Reports::declaration.pedido-entrada", compact(
                'config',
                'cargaHoraria',
                'direitor',
                'dataActual',
                'studentInfo',
                'institution',
                'lectivo',
                'efeito',
                'nascimento',
                "requerimento",
                "recibo",
                'matriculationYear',
                'transference',
                'documentation'

            ));

            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '12mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            //  $code_doc = $this->get_code_doc($requerimento->code,$requerimento->year);
            $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo'))->render();
            $pdf->setOption('footer-html', $footer_html);

            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    private function pedido_saida($request, $config){

        try {



            $this->gerar_codigo_documento($request->requerimento);

            $requerimento = DB::table('requerimento')
                ->where("id", $request->requerimento)
                ->first();


            $recibo = $this->referenceGetRecibo($requerimento->article_id);

            $lective = DB::table('lective_years as ly')->where('is_termina', 0)
                ->join('lective_year_translations as lyt', 'ly.id', '=', 'lyt.lective_years_id')
                ->where('lyt.active', 1)
                ->select('ly.id')
                ->pluck('id')
                ->first();


            $type_document = $request->type_document;
            $efeito = $request->efeito_type;
            $ano = $request->student_year;
            $studentId = $request->students;

            //  $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];
            $outDisciplines = [];

            $studentInfo = User::where('users.id', $studentId)
                ->leftJoin('user_parameters as u_p0', function ($join) {
                    $join->on('users.id', '=', 'u_p0.users_id')
                        ->where('u_p0.parameters_id', 19);
                })
                ->leftJoin('user_parameters as u_p1', function ($join) {
                    $join->on('users.id', '=', 'u_p1.users_id')
                        ->where('u_p1.parameters_id', 1);
                })
                ->leftJoin('user_parameters as u_p2', function ($join) {
                    $join->on('users.id', '=', 'u_p2.users_id')
                        ->where('u_p2.parameters_id', 23);
                })
                ->leftJoin('user_parameters as u_p3', function ($join) {
                    $join->on('users.id', '=', 'u_p3.users_id')
                        ->where('u_p3.parameters_id', 24);
                })
                ->leftJoin('user_parameters as u_p4', function ($join) {
                    $join->on('users.id', '=', 'u_p4.users_id')
                        ->where('u_p4.parameters_id', 5);
                })
                ->leftJoin('user_parameters as u_p5', function ($join) {
                    $join->on('users.id', '=', 'u_p5.users_id')
                        ->where('u_p5.parameters_id', 14);
                })

                ->leftJoin('user_parameters as u_p6', function ($join) {
                    $join->on('users.id', '=', 'u_p6.users_id')
                        ->where('u_p6.parameters_id', 150);
                })

                ->leftJoin('parameter_option_translations as u_p7', function ($join) {
                    $join->on('u_p7.parameter_options_id', '=', 'u_p6.value')
                        ->where('u_p6.parameters_id', 150);
                })
                ->leftJoin('user_parameters as u_p9', function ($join) {
                    $join->on('users.id', '=', 'u_p9.users_id')
                        ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
                })
                ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                    $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
                })
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('matriculations', function ($join) use ($lective, $studentId) {
                    $join->on('matriculations.user_id', '=', 'users.id');

                    $join->where('matriculations.user_id', $studentId);
                })
                ->leftJoin('user_parameters as u_p10', function ($join) {
                    $join->on('users.id', '=', 'u_p10.users_id')
                        ->where('u_p10.parameters_id', 15);
                })
                ->select([
                    'u_p0.value as number',
                    'u_p1.value as name',
                    'u_p2.value as dad',
                    'u_p3.value as mam',
                    'u_p4.value as barthday',
                    'u_p5.value as bi',
                    'u_p7.display_name as province',
                    'u_p8.display_name as municipio',
                    'ct.display_name as course',
                    'matriculations.course_year as year',
                    'matriculations.lective_year as lectivo',
                    'courses.id as course_id',
                    'u_p10.value as emitido'
                ])
                ->where('users.id', $studentId)
                ->first();




            $lectivo = DB::table('lective_year_translations')
                ->where("lective_years_id", $studentInfo->lectivo)
                ->where("active", 1)
                ->select(["display_name as ano"])
                ->first();





            $string = str_replace('/', '_', $requerimento->code);
            $pdf_name = "PdTS" . "_" . $string;

            $nascimento = $this->dataEscrita($studentInfo->barthday);

            $userFoto = User::whereId($studentId)->with([

                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                    ]);
                }
            ])->firstOrFail();
            $courses = Course::with([
                'currentTranslation'
            ])->get();


            //Att: esse mesmo códio vai pegar os usuarios com os cargos definidos por parametro
            //Neste caso o direitor.

            $institution = Institution::latest()->first();


            $direitor = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
                ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
                ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
                ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
                ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
                ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
                ->where('usuario.id', $institution->vice_director_academica)
                ->where('usuario_cargo.role_id', 9)
                ->where('u_p0.parameters_id', 1)
                ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
                ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
                ->where('rt.active', 1)
                ->whereNull('usuario.deleted_at')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->select([
                    'usuario.id as id_usuario',
                    'usuario.email as email_usuario',
                    'usuario.name as name',
                    'u_p0.value as nome_completo',
                    'ga.nome as grau_academico',
                    'cp.nome as categoria_profissional',
                    'rt.display_name as cargo'
                ])
                ->orderBy('usuario.name')
                ->groupBy('id_usuario')
                ->first();


            //  pega o cargo
            $role = Role::whereId(8)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();
            $cargo = $role->translations[0]->description;

            $dataActual = $this->dataActual();



            $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
                ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
                ->get();



            $matriculationYear = DB::table('user_candidate as uc')
                ->where('user_id', $studentId)
                ->join('lective_years as ly', 'uc.year', '=', 'ly.id')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'ly.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->pluck('lyt.display_name')
                ->first();



            $transference = DB::table('tb_transference_studant')
                ->where('id', $requerimento->transference_request)
                ->first();

            $documentation = $transference->documentation;

            $documentation = preg_split('/\r\n|\r|\n/', $documentation);



            $data = [

                'config' => $config,
                'cargaHoraria' => $cargaHoraria,
                'direitor' => $direitor,
                'studentInfo' => $studentInfo,
                'dataActual' => $dataActual,
                'efeito' => $efeito,
                'lectivo' => $lectivo,
                "nascimento" => $nascimento,
                'matriculationYear' => $matriculationYear,
                'transference' => $transference,
                'documentation' => $documentation

            ];


            $institution = Institution::latest()->first();

            $pdf = PDF::loadView("Reports::declaration.pedido-saida", compact(
                'config',
                'cargaHoraria',
                'direitor',
                'dataActual',
                'studentInfo',
                'institution',
                'lectivo',
                'efeito',
                'nascimento',
                "requerimento",
                "recibo",
                'matriculationYear',
                'transference',
                'documentation'

            ));

            $pdf->setOption('margin-top', '1mm');
            $pdf->setOption('margin-left', '1mm');
            $pdf->setOption('margin-bottom', '12mm');
            $pdf->setOption('margin-right', '1mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');


            $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo'))->render();
            $pdf->setOption('footer-html', $footer_html);

            return $pdf->stream($pdf_name . '.pdf');

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    public static function get_code_doc($code, $year){
        return substr($year, -2) . "-" . str_pad($code, 4, '0', STR_PAD_LEFT);

    }


    public function grades_calculat($disciplines, $studentId){

        $status = 0;
        $array_disc = array();

        foreach ($disciplines as $key => $item) {
            array_push($array_disc, $item->id);
        }


        $note = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            ->whereIn('discipline_id', $array_disc)
            ->orderBy('lective_year', 'ASC')
            ->select(["grade"])
            ->get();

        foreach ($note as $item) {

            if ($item->grade < 10) {
                $status = 1;
            }
        }




        return $status;
    }


    public function ordena_plano($plano){

        for ($i = 0; $i < count($plano); $i++) {

            for ($j = $i + 1; $j < count($plano); $j++) {


                $min = $i;
                // pegar os códigos dos objecto
                $objA = is_array($plano[$i]) ? $plano[$i]['code']:$plano[$i]->code;
                $objB = is_array($plano[$j]) ? $plano[$j]['code']:$plano[$j]->code;

                // pegar a substring apartir do 4 caractere
                $subA = substr($objA, 3);
                $subB = substr($objB, 3);

                //verificar se a sub-string contém a letra A
                if (strpos($subA, 'A') !== false && strpos($subB, 'A') !== false) {

                    // substituir o A por 0

                    $subA = str_replace('A', '0', $subA);
                    $subB = str_replace('A', '0', $subB);

                    // convertendo em inteiros
                    $subA = intval($subA);
                    $subB = intval($subB);

                    // comparando
                    if ($subB < $subA) {
                        // Ordenar
                        $min = $j;

                    }

                    $aux = $plano[$min];
                    $plano[$min] = $plano[$i];
                    $plano[$i] = $aux;
                    continue;

                } else if (strpos($subA, 'A') !== false && strpos($subB, 'B') === false) {
                    // substituir o A por 0

                    $subA = str_replace('A', '0', $subA);

                    // convertendo em inteiros
                    $subA = intval($subA);
                    $subB = intval($subB);

                    // comparando
                    if ($subB < $subA) {
                        // Ordenar
                        $min = $j;

                    }

                    $aux = $plano[$min];
                    $plano[$min] = $plano[$i];
                    $plano[$i] = $aux;
                    continue;
                } else if (strpos($subB, 'A') !== false && strpos($subA, 'B') === false) {
                    // substituir o A por 0

                    $subB = str_replace('A', '0', $subB);

                    // convertendo em inteiros
                    $subB = intval($subB);
                    $subA = intval($subA);
                    // comparando
                    if ($subB < $subA) {
                        // Ordenar
                        $min = $j;

                    }

                    $aux = $plano[$min];
                    $plano[$min] = $plano[$i];
                    $plano[$i] = $aux;
                    continue;
                } else if (strpos($subA, 'A') === false && strpos($subB, 'A') === false) {
                    // convertendo em inteiros
                    $subA = intval($subA);
                    $subB = intval($subB);

                    // comparando
                    if ($subB < $subA) {
                        // Ordenar
                        $min = $j;
                    }

                    $aux = $plano[$min];
                    $plano[$min] = $plano[$i];
                    $plano[$i] = $aux;
                    continue;

                }

            }


        }

        return $plano;
    }


    private function solicitacao_estagio($request, $config){
        $this->gerar_codigo_documento($request->requerimento);
        $requerimento = DB::table('requerimento')
            ->where("id", $request->requerimento)
            ->first();

        $recibo = $this->referenceGetRecibo($requerimento->article_id);
        $studentId = $request->students;
        $type_document = $request->type_document;
        $efeito = $request->efeito_type;
        $ano = $request->student_year;
        $nomedainstituicao_SdE = $request->nomedainstituicao_SdE;  // Recebendo o valor da instituição



        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "asc")
            ->select(["sphd.years as ano", "notas.lective_year as ano_lectivo"])
            ->get();

        $media = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $studentId)
            ->orderBy("sphd.years", "asc")
            // ->select(["notas.grade as nota"])
            ->avg('notas.grade');
        // ->get();



        $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $studentInfo = User::where('users.id', $studentId)
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->leftJoin('user_parameters as u_p1', function ($join) {
                $join->on('users.id', '=', 'u_p1.users_id')
                    ->where('u_p1.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p2', function ($join) {
                $join->on('users.id', '=', 'u_p2.users_id')
                    ->where('u_p2.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 24);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 5);
            })
            ->leftJoin('user_parameters as u_p5', function ($join) {
                $join->on('users.id', '=', 'u_p5.users_id')
                    ->where('u_p5.parameters_id', 14);
            })

            ->leftJoin('user_parameters as u_p6', function ($join) {
                $join->on('users.id', '=', 'u_p6.users_id')
                    ->where('u_p6.parameters_id', 150);
            })
            ->leftJoin('parameter_option_translations as u_p17', function ($join) {
                $join->on('u_p17.parameter_options_id', '=', 'u_p6.value')
                    ->where('u_p6.parameters_id', 150);
            })

            ->leftJoin('user_parameters as u_p7', function ($join) {
                $join->on('users.id', '=', 'u_p7.users_id')
                    ->where('u_p7.parameters_id', 15);
            })

            ->leftJoin('user_parameters as u_p9', function ($join) {
                $join->on('users.id', '=', 'u_p9.users_id')
                    ->whereIn('u_p9.parameters_id', [69, 71, 151, 152, 153, 155, 156, 157, 159, 170, 182, 204, 205, 206, 218, 225]);
            })
            ->leftJoin('parameter_option_translations as u_p8', function ($join) {
                $join->on('u_p8.parameter_options_id', '=', 'u_p9.value');
            })

            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

            ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
            // ->where("matriculations.course_year",$ano)
            ->select([
                'u_p0.value as number',
                'u_p1.value as name',
                'u_p2.value as dad',
                'u_p3.value as mam',
                'u_p4.value as barthday',
                'u_p5.value as bi',
                'u_p7.value as emitido',
                'u_p8.display_name as municipio',
                'u_p17.display_name as province',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'matriculations.lective_year as lectivo',
                'courses.id as course_id'

            ])
            ->orderBy("matriculations.course_year", "desc")
            ->firstOrfail();


        $lectivo = DB::table('lective_year_translations')
            ->where("lective_years_id", $studentInfo->lectivo)
            ->where("active", 1)
            ->select(["display_name as ano"])
            ->first();

        $string = str_replace('/', '_', $requerimento->code);
        $pdf_name = "SdE_" . $string;
        $nascimento = $this->dataEscrita($studentInfo->barthday);

        $userFoto = User::whereId($studentId)->with([
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                ]);
            }
        ])->firstOrFail();


        $courses = Course::join('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'courses.id')
                ->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
                ->on('ct.active', '=', DB::raw(true));
        })
            ->where('ct.display_name', 'like', '%engenharia%') // Filtra apenas cursos que contêm "engenharia" no display_name
            ->select([
                'courses.id as courses_id',
                'ct.display_name as course_name'
            ])
            ->distinct()
            ->orderBy('ct.display_name') // Ordena os cursos alfabeticamente pelo nome
            ->get();


        $requerimento = DB::table('requerimento')
            ->join('users', 'users.id', '=', 'requerimento.user_id')
            ->select(
                'requerimento.id as requerimento_id',
                'requerimento.nomedainstituicao_SdE as nome_instituicao',
                'requerimento.code',
                'requerimento.year',
                'requerimento.codigo_documento',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->where("requerimento.id", $request->requerimento)
            ->first();


        // Verifica se 'nome_instituicao' foi retornado pela consulta
        $instituicao_nome = $requerimento->nome_instituicao ?? null;
        $code = $requerimento->code ?? null;


        //Att: esse mesmo código vai pegar os usuarios com os cargos definidos por parametro
        //Neste caso o direitor.

        $institution = Institution::latest()->first();
        $direitor = DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->leftjoin('user_parameters as u_p0', 'usuario.id', '=', 'u_p0.users_id')
            ->leftjoin('user_parameters as u_p1', 'usuario.id', '=', 'u_p1.users_id')
            ->leftjoin('user_parameters as u_p2', 'usuario.id', '=', 'u_p2.users_id')
            ->leftjoin('grau_academico as ga', 'u_p1.value', '=', 'ga.id')
            ->leftjoin('categoria_profissional as cp', 'u_p2.value', '=', 'cp.id')
            ->leftjoin('role_translations as rt', 'cargo.id', '=', 'rt.role_id')
            ->where('usuario.id', $institution->vice_director_cientifica)
            ->where('usuario_cargo.role_id', 10)
            ->where('u_p0.parameters_id', 1)
            ->where('u_p1.parameters_id', ParameterEnum::GRAU_ACADEMICO)
            ->where('u_p2.parameters_id', ParameterEnum::CATEGORIA_PROFISSIONAL)
            ->where('rt.active', 1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario',
                'usuario.email as email_usuario',
                'usuario.name as name',
                'u_p0.value as nome_completo',
                'ga.nome as grau_academico',
                'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();



        //  pega o cargo
        $role = Role::whereId(8)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            }
        ])->firstOrFail();
        $cargo = $role->translations[0]->description;

        $dataActual = $this->dataActual();

        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')
            ->select(['dc.disciplines_id as id_disciplina', 'dc.total_hours as hora'])
            ->get();

        $data = [
            'config' => $config,
            'cargaHoraria' => $cargaHoraria,
            'direitor' => $direitor,
            'cargo' => $cargo,
            'studentInfo' => $studentInfo,
            'dataActual' => $dataActual,
            'userFoto' => $userFoto,
            'anos' => $anos,
            'media' => $media,
            'efeito' => $efeito,
            'lectivo' => $lectivo,
            "nascimento" => $nascimento,
            "nomedainstituicao_SdE" => $nomedainstituicao_SdE,
            'instituicao_nome' => $requerimento->nome_instituicao
        ];



        // return view("Reports::declaration.certificadoMerito")->with($data);
        $institution = Institution::latest()->first();

        $pdf = PDF::loadView("Reports::declaration.solicitacao_estagio", compact(
            'config',
            'cargaHoraria',
            'direitor',
            'cargo',
            'dataActual',
            'userFoto',
            'studentInfo',
            'institution',
            'anos',
            'lectivo',
            'media',
            'efeito',
            'nascimento',
            'requerimento',
            'courses',
            'recibo',
            'nomedainstituicao_SdE',
            'instituicao_nome'
        ));

        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        // $code_doc = $this->get_code_doc($requerimento->code, $requerimento->year);
        // $footer_html = view()->make('Reports::pdf_model.forLEARN_footer', compact('institution', 'requerimento', 'recibo', 'code_doc'))->render();
        // $pdf->setOption('footer-html', $footer_html);

        // if ($config->rodape == 1) {
        //     //  $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
        //     $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        //     $pdf->setOption('footer-html', $footer_html);
        // }

        return $pdf->stream($pdf_name . '.pdf');
    }


    private function plano_disciplinas($request, $config){
        try {

           

            $studentId = $request->students;

            $requerimento = DB::table('requerimento')
                            ->where('id',$request->requerimento)
                            ->first();

            $lective_id = $requerimento->year;

            $lectivo = DB::table('lective_years')
            ->where("id", $lective_id)
            ->first();

           $matricula = DB::table('user_parameters as up')
                            ->where('users_id',$studentId)
                            ->where('up.parameters_id',19)
                            ->first()->value;
            

            $disciplinas = DB::table('new_old_grades as g')
                                ->where('g.user_id',$studentId)
                                ->where('grade','>=',10)
                                ->select('g.discipline_id')
                                ->pluck('discipline_id')->toArray();


            $disciplines = Discipline::whereIn('id',$disciplinas)->with([
                'translations' => function ($q) {
                  $q->whereActive(true);
                },
                'disciplineAreas' => function ($q) {
                  $q->with(['currentTranslation']);
                },
                'disciplineProfile' => function ($q) {
                  $q->with(['currentTranslation']);
                },
                'course' => function ($q) {
                  $q->with(['currentTranslation']);
                },
                'study_plans_has_disciplines' => function ($q) {
                  $q->with([
                    'discipline' => function ($q) {
                      $q->with([
                        'currentTranslation',
                        'disciplineAreas' => function ($q) {
                          $q->with([
                            'translations'
                          ]);
                        }
                      ]);
                    },
                    'study_plans_has_discipline_regimes' => function ($q) {
                      $q->with([
                        'discipline_regime' => function ($q) {
                          $q->with([
                            'currentTranslation'
                          ]);
                        }
                      ]);
                    },
                    'discipline_period' => function ($q) {
                      $q->with([
                        'currentTranslation'
                      ])
                        ->orderBy("code");
                    }
                  ])
        
                    ->orderBy('years')
                    ->orderBy('discipline_periods_id');
                },
              ])->get();

        
              $courses = Course::with(['currentTranslation'])->get();
              $areas = DisciplineArea::with(['currentTranslation'])->get();
              $profiles = DisciplineProfile::with(['currentTranslation'])->get();
        
              $hasMandatoryExam = DB::table('discipline_has_exam')
                ->whereIn('discipline_id', $disciplinas)
                ->get();
        
              // Verifique se há dados suficientes para gerar o PDF
              if (!$disciplines || $courses->isEmpty() || $areas->isEmpty() || $profiles->isEmpty()) {
                return abort(404, 'Dados insuficientes para gerar o PDF.');
              }
        
              $plano_regime = DB::table('study_plans_has_disciplines as sthd')
                ->join("sp_has_discipline_regimes as sthdr", "sthdr.sp_has_disciplines_id", "=", "sthd.id")
                ->join("discipline_regimes as dr", "dr.id", "=", "sthdr.discipline_regimes_id")
                ->whereIn("sthd.disciplines_id", $disciplinas)
                ->select([
                  'sthd.id as id',
                  'sthdr.discipline_regimes_id as regime',
                  'sthdr.hours as horas',
                  'dr.code as codigo',
        
                ])
                ->get();
        
              // Obtendo a instituição
              $institution = Institution::latest()->first();
              $languages = Language::whereActive(true)->get();
              
        
              // Definindo os dados para o PDF
              $titulo_documento = "Relatório de Disciplina " . date("Y/m/d");
              $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");
        
              // Gerando o PDF
              $pdf = PDF::loadView(
                'Reports::declaration.plano_disciplinas',
                compact(
                  'languages',
                  'disciplines',
                  'courses',
                  'areas',
                  'profiles',
                  'hasMandatoryExam',
                  'institution',
                  'titulo_documento',
                  'documentoGerado_documento'
                )
              );
        
              $pdf->setOption('margin-top', '1mm');
              $pdf->setOption('margin-left', '1mm');
              $pdf->setOption('margin-bottom', '4mm');
              $pdf->setOption('margin-right', '1mm');
              $pdf->setOption('enable-javascript', true);
              $pdf->setOption('debug-javascript', true);
              $pdf->setOption('javascript-delay', 3000);
              $pdf->setOption('enable-smart-shrinking', true);
              $pdf->setOption('no-stop-slow-scripts', true);
              $pdf->setPaper('a4', 'portrait');
        
              $pdf_name = "PCdD_" . $matricula;
        
              return $pdf->stream($pdf_name . '.pdf');


        }
        catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }



    }






}