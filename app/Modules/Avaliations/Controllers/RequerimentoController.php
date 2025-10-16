<?php

namespace App\Modules\Avaliations\Controllers;

use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Event;
use App\Modules\GA\Models\EventOption;
use App\Modules\GA\Models\EventTranslation;
use App\Modules\GA\Models\EventType;
use App\Modules\GA\Requests\EventRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\DepartmentTranslation;
use App\Modules\GA\Requests\DepartmentRequest;
use App\Model\Institution;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Toastr;
use App\Modules\GA\Models\Course;
use App\Modules\Users\Events\PaidStudentCardEvent;
use App\Modules\GA\Models\Discipline;
class RequerimentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;

            $institution = Institution::latest()->first();

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'institution' => $institution
            ];

            return view('Avaliations::requerimento.my_request')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    /*Esta zona é para a solicitação de revisão de Prova!*/
    public function solicitacao_revisao_prova(){
        try {
            
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;
            $courses = Course::with(['currentTranslation'])->get();
            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'courses' => $courses
            ];
            return view('Avaliations::requerimento.solicitacao_revisao_prova')->with($data);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
  
    public function getEstudante($course_id){
        try {
            
            $students = DB::table('users')
                // ->whereIn('users.id',$students_ids)
                ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->where('usuario_cargo.role_id', 6)
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('uc.courses_id', $course_id)
                ->whereNull('up.deleted_at')
                ->where('up.parameters_id', 1)
                ->where('up0.parameters_id', 19)
                ->whereNull('up0.deleted_at')
                ->whereNull('users.deleted_at')
                ->whereNull('users.deleted_by')
                ->select([
                    'users.id as user_id',
                    'ct.display_name as course',
                    'up.value as name',
                    'users.email as email',
                    'up0.value as student_number'
                ])
                ->orderBy("name")
                ->distinct('id')
                ->get();

            return response()->json($students);


        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json(['error' => 'Failed to fetch students'], 500);
        }

    }

    public function getDisciplinas($student_id, $lective_year, $course_id){
    
       try {

            $lista = [
                6  => '20/21',
                9 => '24/25',
                11 => '25/26',
            ];

            // Garante que o ano existe no array
            if (!array_key_exists($lective_year, $lista)) {
                return collect(); // coleção vazia
            }

            $ano = $lista[$lective_year];

            $ultimaMatricula = DB::table('matriculations')
                ->where('user_id', $student_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $disciplinas = collect(); // coleção vazia por defeito

            if ($ultimaMatricula) {
                $disciplinas = DB::table('matriculations as m')
                    ->join('study_plans_has_disciplines as sphd', 'sphd.years', '=', 'm.course_year')
                    ->join('study_plans as sp', 'sp.id', '=', 'sphd.study_plans_id')
                    ->join('disciplines as d', 'sphd.disciplines_id', '=', 'd.id')
                    ->join('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'd.id')
                            ->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    })
                    ->where('m.id', $ultimaMatricula->id) // usamos direto o ID
                    ->where('sp.courses_id', $course_id)
                    ->select([
                        'dt.display_name as name',
                        'd.code as code',
                        'd.id'
                    ])
                    ->distinct()
                    ->orderBy('name')
                    ->get();
            }
            // se ainda estiver vazio → tenta user_courses
            if ($disciplinas->isEmpty()) {
                $disciplinas = DB::table('user_courses as uc')
                    ->join('Import_data_forlearn as idf', 'idf.id_user', '=', 'uc.users_id')
                    ->join('study_plans_has_disciplines as sphd', 'sphd.years', '=', 'idf.ano_curricular')
                    ->join('study_plans as sp', 'sp.id', '=', 'sphd.study_plans_id')
                    ->join('disciplines as d', 'sphd.disciplines_id', '=', 'd.id')
                    ->join('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'd.id')
                            ->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    })
                    ->where('uc.users_id', $student_id)
                    ->where('sp.courses_id', $course_id)
                    ->select([
                        'dt.display_name as name',
                        'd.code as code',
                        'd.id'
                    ])
                    ->distinct()
                    ->orderBy('name')
                    ->get();
            }

            return response()->json($disciplinas);

            } catch (\Throwable $e) {
                dd($e->getMessage(), $e->getTraceAsString());
            }

    }

    public function solicitacao_revisao_prova_store(Request $request){
        try {
            
            // Validar os dados recebidos
            $user_id = request()->input('student_id');
            $lective_year = request()->input('lective_year');
            $code = "revisao_prova";
            $created_by = auth()->user()->id;
            $dicipline_id = request()->input('disciplina_id');

            // Validar se veio ano lectivo
            if (!$lective_year) {
                Toastr::error(__('Ano lectivo inválido'), __('toastr.error'));
                return redirect()->back();
            }

            $emolumento = DB::table('articles as art')
                ->join('code_developer as cd', 'art.id_code_dev', '=', 'cd.id')
                ->where('art.anoLectivo', $lective_year)
                ->where('cd.code', $code)
                ->select('art.id', 'art.base_value')
                ->first();
        
            if (!$emolumento) {
                Toastr::error(__('Não foi possível solicitar o emolumento de revisão de prova, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }
                        
            $articleRequestId = DB::table('article_requests')->insertGetId([ 
                'user_id' => $user_id, 
                'article_id' => $emolumento->id, 
                'base_value' => $emolumento->base_value, 
                'discipline_id' =>$dicipline_id,
                'status' => 'pending',
                'created_by' => $created_by,
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
            // Criar uma transação
            $transaction = DB::table('transactions')->insertGetId([
                    'type' => 'debit',
                    'value' => $emolumento->base_value,
                    'notes' => 'Débito inicial do valor base',
                    "created_by" => auth()->user()->id,
                    "created_at" => Carbon::now()
            ]);

            // Criar article e transações
            $article_transaction = DB::table('transaction_article_requests')->insertGetId(
                [
                    'article_request_id' => $articleRequestId,
                    'transaction_id' => $transaction,
                    "value" => $emolumento->base_value,
                    "created_at" => Carbon::now()
                ]
            );

            // Guarda o codigo do documento 
            $requerimento = DB::table('requerimento')->insertGetId(
                [
                    'article_id' => $emolumento->id,
                    //'codigo_documento' => $this->gerar_codigo_documento(),
                    'efeito' => 'Revisão de Prova',
                    "user_id" => $created_by,
                    'year' => $lective_year
                ]
            );

            Toastr::success(__('Solicitação de revisão de prova enviada com sucesso!'), __('toastr.success'));
            return redirect()->back();


        }catch (Exception $e) {   
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }

    }
    /*Esta zona é para a solicitação de revisão de Prova!*/

    /*Esta zona é para Defesa Extraordinaria!*/
    public function solicitacao_defesa_extraordinaria(){
        try {
            
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;
            $courses = Course::with(['currentTranslation'])->get();
            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'courses' => $courses
            ];
            return view('Avaliations::requerimento.solicitacao_defesa_extraordinaria')->with($data);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
  
    public function getEstudante_extraordinario($course_id){
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
            $lective_year = $lectiveYearSelected->id ?? 11;

            $lista = [
                6  => '20/21',
                9 => '24/25',
                11 => '25/26',
            ];

            // Garante que o ano existe no array
            if (!array_key_exists($lective_year, $lista)) {
                return collect(); // coleção vazia
            }

            $ano = $lista[$lective_year];

            
            $students = DB::table('users')
                // ->whereIn('users.id',$students_ids)
                ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                //->join('new_old_grades as nog', 'nog.user_id', '=', 'users.id')
                //->join('study_plans', 'study_plans.courses_id', '=', $course_id)
                //->join('study_plans_has_disciplines as sphd', 'sphd.study_plans_id', '=', 'study_plans.id')
                //->where('nog.lective_year', 'like', '%' . $ano . '%')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->where('usuario_cargo.role_id', 6)
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('uc.courses_id', $course_id)
                ->whereNull('up.deleted_at')
                ->where('up.parameters_id', 1)
                ->where('up0.parameters_id', 19)
                ->whereNull('up0.deleted_at')
                ->whereNull('users.deleted_at')
                ->whereNull('users.deleted_by')
                ->select([
                    'users.id as user_id',
                    'ct.display_name as course',
                    'up.value as name',
                    'users.email as email',
                    'up0.value as student_number'
                ])
                ->orderBy("name")
                ->distinct('id')
                ->get();

            return response()->json($students);


        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json(['error' => 'Failed to fetch students'], 500);
        }

    }

    public function getDisciplinas_extraordinaria($student_id, $lective_year, $course_id){
    
       try {

            $lista = [
                6  => '20/21',
                9 => '24/25',
                11 => '25/26',
            ];

            // Garante que o ano existe no array
            if (!array_key_exists($lective_year, $lista)) {
                return collect(); // coleção vazia
            }

            $ano = $lista[$lective_year];

            $ultimaMatricula = DB::table('matriculations')
                ->where('user_id', $student_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $disciplinas = collect(); // coleção vazia por defeito

            if ($ultimaMatricula) {
                $disciplinas = DB::table('matriculations as m')
                    ->join('study_plans_has_disciplines as sphd', 'sphd.years', '=', 'm.course_year')
                    ->join('study_plans as sp', 'sp.id', '=', 'sphd.study_plans_id')
                    ->join('disciplines as d', 'sphd.disciplines_id', '=', 'd.id')
                    ->join('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'd.id')
                            ->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    })
                    ->where('m.id', $ultimaMatricula->id) // usamos direto o ID
                    ->where('sp.courses_id', $course_id)
                    ->select([
                        'dt.display_name as name',
                        'd.code as code',
                        'd.id'
                    ])
                    ->distinct()
                    ->orderBy('name')
                    ->get();
            }
            // se ainda estiver vazio → tenta user_courses
            if ($disciplinas->isEmpty()) {
                $disciplinas = DB::table('user_courses as uc')
                    ->join('Import_data_forlearn as idf', 'idf.id_user', '=', 'uc.users_id')
                    ->join('study_plans_has_disciplines as sphd', 'sphd.years', '=', 'idf.ano_curricular')
                    ->join('study_plans as sp', 'sp.id', '=', 'sphd.study_plans_id')
                    ->join('disciplines as d', 'sphd.disciplines_id', '=', 'd.id')
                    ->join('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'd.id')
                            ->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    })
                    ->where('uc.users_id', $student_id)
                    ->where('sp.courses_id', $course_id)
                    ->select([
                        'dt.display_name as name',
                        'd.code as code',
                        'd.id'
                    ])
                    ->distinct()
                    ->orderBy('name')
                    ->get();

            }/*if ($disciplinas->isEmpty()) {
                
                $disciplinas = DB::table('new_old_grades as nog')
                ->join('disciplines as d', 'nog.discipline_id', '=', 'd.id')
                ->join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                })                
                ->where('nog.user_id', $student_id)
                ->where('nog.lective_year', 'like', '%' . $ano . '%')
                ->select([
                    'dt.display_name as name',
                    'd.code as code',
                    'd.id'
                ])
                ->distinct()
                ->orderBy("name")
                ->get();

            }*/

            return response()->json($disciplinas);
            } catch (\Throwable $e) {
                dd($e->getMessage(), $e->getTraceAsString());
            }

    }

    public function solicitacao_solicitacao_defesa_extraordinaria_store(Request $request){
        try {
            
            // Validar os dados recebidos
            $user_id = request()->input('student_id');
            $lective_year = request()->input('lective_year');
            $code = "defesa_extraordinaria";
            $created_by = auth()->user()->id;
            $dicipline_id = request()->input('disciplina_id');

            // Validar se veio ano lectivo
            if (!$lective_year) {
                Toastr::error(__('Ano lectivo inválido'), __('toastr.error'));
                return redirect()->back();
            }

            $emolumento = DB::table('articles as art')
                ->join('code_developer as cd', 'art.id_code_dev', '=', 'cd.id')
                ->where('art.anoLectivo', $lective_year)
                ->where('cd.code', $code)
                ->select('art.id', 'art.base_value')
                ->first();
        
            if (!$emolumento) {
                Toastr::error(__('Não foi possível solicitar o emolumento, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }
                        
            $articleRequestId = DB::table('article_requests')->insertGetId([ 
                'user_id' => $user_id, 
                'article_id' => $emolumento->id, 
                'base_value' => $emolumento->base_value, 
                //'discipline_id' =>$dicipline_id,
                'status' => 'pending',
                'created_by' => $created_by,
                'created_at' => now(), 
                'updated_at' => now(), 
            ]);
            // Criar uma transação
            $transaction = DB::table('transactions')->insertGetId([
                    'type' => 'debit',
                    'value' => $emolumento->base_value,
                    'notes' => 'Débito inicial do valor base',
                    "created_by" => auth()->user()->id,
                    "created_at" => Carbon::now()
            ]);

            // Criar article e transações
            $article_transaction = DB::table('transaction_article_requests')->insertGetId(
                [
                    'article_request_id' => $articleRequestId,
                    'transaction_id' => $transaction,
                    "value" => $emolumento->base_value,
                    "created_at" => Carbon::now()
                ]
            );

            // Guarda o codigo do documento 
            $requerimento = DB::table('requerimento')->insertGetId(
                [
                    'article_id' => $emolumento->id,
                    //'codigo_documento' => $this->gerar_codigo_documento(),
                    'efeito' => 'Revisão de Prova',
                    "user_id" => $created_by,
                    'year' => $lective_year
                ]
            );

            Toastr::success(__('Solicitação de Defesa Extraordinaria enviada com sucesso!'), __('toastr.success'));
            return redirect()->back();


        }catch (Exception $e) {   
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }

    }
    /*Esta zona é para a solicitação de Defesa extraordinaria!*/



    public function merito()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;

            // Listar todos os cargos

            $roles = DB::table('role_translations')
                ->whereNull('deleted_at')
                ->where('active', '1')
                ->where('role_id', "!=", 2)
                ->select(['role_id', 'display_name'])
                ->orderBy('display_name')
                ->get();

            $departamento = Department::join('users as u1', 'u1.id', '=', 'departments.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'departments.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'departments.deleted_by')
                ->leftJoin('department_translations as dt', function ($join) {
                    $join->on('dt.departments_id', '=', 'departments.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select([
                    'departments.id',
                    'dt.display_name'
                ])
                ->get();


            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'roles' => $roles,
                'departamento' => $departamento
            ];

            return view('Avaliations::requerimento.requerimento_merito')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function cerimonia()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $diploma = DB::table('estudante_diploma')
                ->select(["diploma"])
                ->orderBy("id", "desc")
                ->first();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $emo = array("diploma", "certificado", "cerimônia de outorga");

            $emolumentos = DB::table('articles')
                ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->whereNull('articles.deleted_by')
                ->whereNull('traducao.deleted_at')
                ->where('traducao.active', 1)
                ->whereIn('traducao.display_name', $emo)
                ->orderBy('traducao.display_name')
                ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                ->get();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;


            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'emolumentos' => $emolumentos,
                'diploma' => $diploma->diploma,

            ];




            return view('Avaliations::requerimento.requerimento_cerimonia')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function mudanca_turma()
    {
        try {

            // return $this->studant_get_year(9178,8); 

            $turnos = DB::table('schedule_types as st')
                ->join('schedule_type_translations as stt', "st.id", "stt.schedule_type_id")
                ->where('stt.active', 1)
                ->where('stt.language_id', 1)
                ->whereNull('stt.deleted_at')
                ->whereNull('st.deleted_at')
                ->orderBy("stt.id", "desc")
                ->select(["st.id", "stt.display_name"])
                ->get();



            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();


            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;


            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'turnos' => $turnos,
            ];




            return view('Avaliations::requerimento.mudanca_turma')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function doc(){
        try {

            $diploma = DB::table('estudante_diploma')
                ->select(["diploma"])
                ->orderBy("id", "desc")
                ->first();

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;
            $article_dados = DB::table('articles_documents')->get();

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'diploma' => $diploma->diploma,
                'code' => $article_dados,
            ];

            return view('Avaliations::requerimento.requerimento_doc')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ajax_help($id)
    {
        try {




            $tranf_type = 'payment';
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();







            // PEGA O LIMITE DE PAGAMENTO DA PROPINA
            $validacao_proprina = DB::table('pauta_avaliation_student_shows')
                ->where('lective_year_id', 7)
                ->first();

            // IMPRMIR
            $mesActual = date('m') > 9 ? date('m') : date('m')[1];
            $diaActual = date('d');

            if ($validacao_proprina->quantidade_mes > 1) {
                $mesActual = $mesActual - $validacao_proprina->quantidade_mes;
            } else {
                $mesActual = $diaActual > $validacao_proprina->quatidade_day ? $mesActual : $mesActual - $validacao_proprina->quantidade_mes;
            }

            $propinas = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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

                ->Join('article_requests as artR', 'artR.user_id', 'u0.id')
                ->leftJoin('articles as art', function ($join) {
                    $join->on('artR.article_id', '=', 'art.id');
                })
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->leftJoin('code_developer as code_dev', 'code_dev.id', 'art.id_code_dev')
                ->where('u0.id', $id)
                // ->where('artR.month', $mesActual)
                ->where('code_dev.code', "propina")
                ->where('artR.deleted_by', null)
                ->distinct('matriculations.user_id')
                ->orderBy('artR.year', 'desc')
                // ->whereBetween('artR.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->select(['u0.id as codigo', 'cl.id as codigo_turma', 'ct.courses_id as codigo_curso', 'artR.month as mes', 'artR.status as estado', 'course_year as ano_curricular'])
                ->first();


            $lectiveYears = LectiveYear::with(['currentTranslation'])

                ->get();

            $lectiveYears = collect($lectiveYears)->map(function ($item) {
                return ["id" => $item->id, "ano" => $item->currentTranslation->display_name];
            });

            $lectiveYearSelected = $lectiveYearSelected->id ?? 11;

            return $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'propinas' => $propinas
            ];

            // return $model;
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    # Pegar o ano do estudante
    public function ajax($id)
    {
        // return $id;

        $anos = DB::table('study_plans_has_disciplines as sphd')
            ->join('new_old_grades as notas', "sphd.disciplines_id", "notas.discipline_id")
            ->where('notas.user_id', $id)
            // ->where('years', 4)
            ->groupBy("sphd.years")
            ->orderBy("sphd.years", "desc")
            ->select(["sphd.years as ano"])
            ->get();



        return $data = [
            'anos' => $anos
        ];
    }


    # Pegar os articles do requerimentos que serão listados na datatables da página ( INDEX )

    public function my_articles($id){
        //TODO: avaliar para quando o select estiver vazio
        Log::info('dados que vem do ajax '.$id );
        try {
            $dados = explode(',', $id);
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();

            $currentData = Carbon::now();
            $lective = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lective = $lective->id ?? 11;
            $lectiveYearSelected = LectiveYear::whereId($dados[0])->first();

            switch ($dados[1]) {
                case 0:

                    $art_req = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->join('article_requests as ar', 'ar.article_id', "=", "articles.id")
                        ->join("requerimento as rq", "rq.article_id", "=", "ar.id")
                        ->join('users as u0', 'u0.id', "=", "ar.user_id")
                        ->join('user_parameters as up', 'up.users_id', '=', 'u0.id')
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->leftjoin("disciplines as disc", 'disc.id', 'ar.discipline_id')
                        ->leftjoin('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'disc.id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                        })
                        ->leftJoin('metricas as mt','mt.id','ar.metric_id')
                        ->whereNull('articles.deleted_by')
                        ->whereNull('ar.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('up.parameters_id', 1)
                        ->orderBy('traducao.display_name')
                        ->select([
                            'rq.code',
                            'rq.codigo_documento',
                            'rq.year',
                            'u0.id as codigo_estudante',
                            'u0.email',
                            'up.value as nome_estudante',
                            'articles.id as id_article',
                            'ar.id as art_id',
                            'traducao.display_name as nome',
                            'ar.status',
                            'disc.code as code_discipline',
                            'dt.display_name as discipline',
                            'articles.base_value',
                            'ar.created_at',
                            'rq.transference_request as transference_request',
                            'rq.id as requerimento_id',
                            'articles.id_code_dev as code_dev',
                            'mt.nome as metric'
                        ])
                        ->get();

                    $requerimento = DB::table('requerimento')
                        ->get();


                    // gerar código documento

                    foreach ($art_req as $ar) {

                        if ($ar->status == "total" && isset($ar->codigo_documento) && !isset($ar->code)) {
                            $ar->code = $this->gerar_codigo_documento($ar->requerimento_id);
                        }

                    }

                    $art_req = $this->ordena_plano($art_req);


                    return Datatables::of($art_req)
                        ->addColumn('status', function ($state) {
                            return view('Avaliations::requerimento.datatables.state', compact('state'));
                        })
                        ->addColumn('base_value', function ($money) {
                            return view('Avaliations::requerimento.datatables.money', compact('money'));
                        })

                        ->addColumn('doc', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.doc', compact('state', 'requerimento'));
                        })
                        ->addColumn('code', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.code', compact('state', 'requerimento'));
                        })
                        ->addColumn('actions', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.action', compact('state', 'requerimento'));
                        })
                        ->addColumn('type', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.type', compact('state', 'requerimento'));
                        })
                        ->rawColumns(['status', 'code', 'base_value', 'doc', 'actions', 'type'])
                        ->addIndexColumn()
                        ->toJson();

                    break;
                case 1:
                    $texto = "Certificado";
                    break;
                case 2:
                    $texto = "%declaração com notas%";
                    break;
                case 3:
                    $texto = "%declaração sem notas%";
                    break;
                case 4:
                    $texto = "Exame de recurso";
                    break;
                case 5:
                    $texto = "Exame especial";
                    break;
                case 6:


                    $requerimento = DB::table('requerimento_merito')
                        ->whereNull("requerimento_merito.deleted_at")
                        ->join('users as u0', 'u0.id', "=", "requerimento_merito.user_id")
                        ->join('user_parameters as up', 'up.users_id', '=', 'requerimento_merito.user_id')
                        ->join('lective_year_translations as ly', 'ly.lective_years_id', '=', 'requerimento_merito.ano_lectivo')
                        ->where('up.parameters_id', 1)
                        ->where('ly.active', 1)
                        ->select(['u0.id as codigo_estudante', 'u0.email', 'up.value as nome_estudante', 'requerimento_merito.*', 'ly.display_name as ano_l'])
                        ->get();

                    return Datatables::of($requerimento)
                        ->addColumn('actions', function ($item) {
                            return view('Avaliations::requerimento.datatables.actions_m', compact('item'));
                        })
                        ->addColumn('ano', function ($item) {
                            return view('Avaliations::requerimento.datatables.ano', compact('item'));
                        })
                        ->addColumn('tipo', function ($item) {
                            return view('Avaliations::requerimento.datatables.tipo', compact('item'));
                        })
                        ->rawColumns(['actions', 'ano', 'tipo'])
                        ->addIndexColumn()
                        ->toJson();

                    break;
                case 7:
                    $texto = "%Diploma%";
                    break;
                case 8:
                    $texto = "%declaração de frequência%";
                    break;
                case 9:
                    $texto = "%nulação de matr%";
                    break;
                case 10:
                    $texto = "%Declaração de Fim de Curso%";
                    break;
                case 11:
                    $texto = "cerimônia de outorga";
                    break;
                case 12:


                    $mudanca = DB::table('tb_change_class_student as change_class')
                        ->leftJoin("classes as old_c", "old_c.id", "change_class.id_old_class")
                        ->leftJoin("classes as new_c", "new_c.id", "change_class.id_new_class")
                        ->Join("article_requests as ar", "change_class.article_request_id", "ar.id")
                        ->join('user_parameters as up', 'up.users_id', '=', 'ar.user_id')
                        ->join('user_parameters as up1', 'up1.users_id', '=', 'ar.user_id')
                        ->where('up.parameters_id', 1)
                        ->where('up1.parameters_id', 19)
                        ->join('users as u0', 'u0.id', "=", "ar.user_id")

                        ->whereNull('change_class.deleted_at')
                        ->whereNull('ar.deleted_at')
                        ->select(
                            [
                                'ar.user_id as student_id',
                                'change_class.id',
                                'up.value as student_name',
                                'u0.email',
                                'old_c.display_name as turma_antiga',
                                'new_c.display_name as turma_nova',
                                'ar.status',
                                'ar.id as article',
                                'change_class.status as status_change',

                            ]
                        )
                        ->get();



                    return Datatables::of($mudanca)
                        ->addColumn('status', function ($state) {
                            return view('Avaliations::requerimento.datatables.state', compact('state'));
                        })
                        ->addColumn('status_change', function ($status_change) {
                            return view('Avaliations::requerimento.datatables.state_mudanca', compact('status_change'));
                        })
                        ->addColumn('actions', function ($item) {
                            return view('Avaliations::requerimento.datatables.action_mudanca', compact('item'));
                        })
                        ->rawColumns(['status', 'status_change', 'actions'])
                        ->addIndexColumn()
                        ->toJson();

                    break;
                case 13:
                    $texto = "%Declaração com Notas de Exame de Acesso%";
                    break;
                case 14:
                    $texto = "%Pedido de transferência (de entrada%";
                    break;
                case 15:
                    $texto = "%Pedido de transferência (de saída%";
                    break;
                case 16:
                    $texto = "%Percurso académico%";
                    break;
                case 17:
                    $texto = "%Solicitação de estágio%";
                    break;
                case 18:
                        $texto = "%Prova parcelar (2ª chamada)%";
                        break;
                case 19:
                        $texto = "%Carta de recomendação%";
                        break;
                case 20:
                    //Solicitação de Revisão de prova
                    $art_req = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->join('article_requests as ar', 'ar.article_id', "=", "articles.id")
                        ->join("requerimento as rq", "rq.article_id", "=", "ar.article_id")
                        ->join('users as u0', 'u0.id', "=", "ar.user_id")
                        ->join('user_parameters as up', 'up.users_id', '=', 'u0.id')
                        //->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->leftjoin("disciplines as disc", 'disc.id', 'ar.discipline_id')
                        ->leftjoin('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'disc.id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                        })
                        ->whereNull('articles.deleted_by')
                        ->whereNull('ar.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('up.parameters_id', 1)
                        ->where('rq.year', $dados[0])
                        ->whereIn('articles.id', [255, 358])
                        ->orderBy('traducao.display_name')
                        ->select([
                            'rq.code',
                            'rq.codigo_documento',
                            'rq.year',
                            'u0.id as codigo_estudante',
                            'u0.email',
                            'up.value as nome_estudante',
                            'articles.id as id_article',
                            'ar.id as art_id',
                            'traducao.display_name as nome',
                            'ar.status',
                            'disc.code as code_discipline',
                            'dt.display_name as discipline',
                            'articles.base_value',
                            'ar.created_at',
                            'rq.transference_request as transference_request',
                            'articles.id_code_dev as code_dev'
                        ])
                        ->get();
                    Log::info($art_req);


                    $art_req = $this->ordena_plano($art_req);

                    $requerimento = DB::table('requerimento')->get();

                    return Datatables::of($art_req)

                        ->addColumn('status', function ($state) {
                            return view('Avaliations::requerimento.datatables.state', compact('state'));
                        })
                        ->addColumn('base_value', function ($money) {
                            return view('Avaliations::requerimento.datatables.money', compact('money'));
                        })
                        ->addColumn('doc', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.doc', compact('state', 'requerimento'));
                        })
                        ->addColumn('code', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.code', compact('state', 'requerimento'));
                        })
                        ->addColumn('actions', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.action', compact('state', 'requerimento'));
                        })
                        ->addColumn('type', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.type', compact('state', 'requerimento'));
                        })
                        ->rawColumns(['status', 'code', 'base_value', 'doc', 'actions', 'type'])
                        ->addIndexColumn()
                        ->toJson();

                
                case 21:
                    $art_req = DB::table('articles')
                    ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                    ->join('article_requests as ar', 'ar.article_id', "=", "articles.id")
                    ->join("requerimento as rq", "rq.article_id", "=", "ar.id")
                    ->join('users as u0', 'u0.id', "=", "ar.user_id")
                    ->join('user_parameters as up', 'up.users_id', '=', 'u0.id')
                    ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->leftjoin("disciplines as disc", 'disc.id', 'ar.discipline_id')
                    ->leftjoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'disc.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('metricas as mt','mt.id','ar.metric_id')
                    ->whereNull('articles.deleted_by')
                    ->whereNull('ar.deleted_by')
                    ->whereNull('traducao.deleted_at')
                    ->where('traducao.active', 1)
                    ->where('up.parameters_id', 1)
                    ->where('traducao.article_id', 387)
                    ->orderBy('traducao.display_name')
                    ->select([
                        'rq.code',
                        'rq.codigo_documento',
                        'rq.year',
                        'u0.id as codigo_estudante',
                        'u0.email',
                        'up.value as nome_estudante',
                        'articles.id as id_article',
                        'ar.id as art_id',
                        'traducao.display_name as nome',
                        'ar.status',
                        'disc.code as code_discipline',
                        'dt.display_name as discipline',
                        'articles.base_value',
                        'ar.created_at',
                        'rq.transference_request as transference_request',
                        'articles.id_code_dev as code_dev',
                        'mt.nome as metric'
                    ])
                    ->get();

                    $art_req = $this->ordena_plano($art_req);

                    $requerimento = DB::table('requerimento')
                        ->get();


                    return Datatables::of($art_req)

                        ->addColumn('status', function ($state) {
                            return view('Avaliations::requerimento.datatables.state', compact('state'));
                        })
                        ->addColumn('base_value', function ($money) {
                            return view('Avaliations::requerimento.datatables.money', compact('money'));
                        })

                        ->addColumn('doc', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.doc', compact('state', 'requerimento'));
                        })
                        ->addColumn('code', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.code', compact('state', 'requerimento'));
                        })
                        ->addColumn('actions', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.action', compact('state', 'requerimento'));
                        })
                        ->addColumn('type', function ($state) use ($requerimento) {
                            return view('Avaliations::requerimento.datatables.type', compact('state', 'requerimento'));
                        })
                        ->rawColumns(['status', 'code', 'base_value', 'doc', 'actions', 'type'])
                        ->addIndexColumn()
                        ->toJson();

                    break;


            }

            $art_req = DB::table('articles')
                ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                ->join('article_requests as ar', 'ar.article_id', "=", "articles.id")
                ->join("requerimento as rq", "rq.article_id", "=", "ar.id")
                ->join('users as u0', 'u0.id', "=", "ar.user_id")
                ->join('user_parameters as up', 'up.users_id', '=', 'u0.id')
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->leftjoin("disciplines as disc", 'disc.id', 'ar.discipline_id')
                ->leftjoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disc.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('metricas as mt','mt.id','ar.metric_id')
                ->whereNull('articles.deleted_by')
                ->whereNull('ar.deleted_by')
                ->whereNull('traducao.deleted_at')
                ->where('traducao.active', 1)
                ->where('up.parameters_id', 1)
                ->where('traducao.display_name', 'like', $texto)
                ->orderBy('traducao.display_name')
                ->select([
                    'rq.code',
                    'rq.codigo_documento',
                    'rq.year',
                    'u0.id as codigo_estudante',
                    'u0.email',
                    'up.value as nome_estudante',
                    'articles.id as id_article',
                    'ar.id as art_id',
                    'traducao.display_name as nome',
                    'ar.status',
                    'disc.code as code_discipline',
                    'dt.display_name as discipline',
                    'articles.base_value',
                    'ar.created_at',
                    'rq.transference_request as transference_request',
                    'articles.id_code_dev as code_dev',
                    'mt.nome as metric'
                ])
                ->get();

            $art_req = $this->ordena_plano($art_req);

            $requerimento = DB::table('requerimento')->get();

            return Datatables::of($art_req)

                ->addColumn('status', function ($state) {
                    return view('Avaliations::requerimento.datatables.state', compact('state'));
                })
                ->addColumn('base_value', function ($money) {
                    return view('Avaliations::requerimento.datatables.money', compact('money'));
                })

                ->addColumn('doc', function ($state) use ($requerimento) {
                    return view('Avaliations::requerimento.datatables.doc', compact('state', 'requerimento'));
                })
                ->addColumn('code', function ($state) use ($requerimento) {
                    return view('Avaliations::requerimento.datatables.code', compact('state', 'requerimento'));
                })
                ->addColumn('actions', function ($state) use ($requerimento) {
                    return view('Avaliations::requerimento.datatables.action', compact('state', 'requerimento'));
                })
                ->addColumn('type', function ($state) use ($requerimento) {
                    return view('Avaliations::requerimento.datatables.type', compact('state', 'requerimento'));
                })
                ->rawColumns(['status', 'code', 'base_value', 'doc', 'actions', 'type'])
                ->addIndexColumn()
                ->toJson();

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ordena_plano($plano){


        for ($i = 0; $i < count($plano); $i++) {

            for ($j = $i + 1; $j < count($plano); $j++) {


                $min = $i;
                // pegar os códigos dos objecto
                $objA = $plano[$i]->code;
                $objB = $plano[$j]->code;

                // pegar a substring apartir do 7 caractere
                $subA = substr($objA, 6);
                $subB = substr($objB, 6);



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




        return $plano;
    }

    # Pegar os articles que serão requeridos 

    public function getUserArticle($id){
        
        //TODO: avaliar para quando o select estiver vazio
        try {
            $dados = explode(',', $id);

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lective = DB::table('lective_years')
                // ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->where("id", $dados[0])
                ->first();
            $lective = $lective->id ?? 11;



            $lectiveYearSelected = LectiveYear::whereId($lective)->first();

            $texto = "";

            switch ($dados[1]) {
                case '2':

                    // Certificados 
                    $articles = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('articles.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('traducao.display_name', 'like', "certificado")
                        ->orderBy('traducao.display_name')
                        ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                        ->get();

                    $articles = collect($articles);

                    $articles = $articles->unique("nome");

                    $articles->values()->all();

                    return $data = [
                        'articles' => $articles,
                        'type' => 2
                    ];
                    break;
                case '1':

                    $articles = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('articles.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('traducao.display_name', 'like', "%Declaração%")
                        ->orderBy('traducao.display_name')
                        ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                        ->get();

                    $articles = collect($articles);

                    $articles = $articles->unique("nome");

                    $articles->values()->all();

                    return $data = [
                        'articles'=>$articles,
                        'type' => 1

                    ];

                    break;
                case '3':

                    // Diploma
                    $articles = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('articles.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('traducao.display_name', 'like', "diploma")
                        ->orderBy('traducao.display_name')
                        ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                        ->get();

                    $articles = collect($articles);

                    $diplomas = $articles->unique("nome");

                    $diplomas->values()->all();



                    return $data = [
                        'diploma' => $diplomas,
                        'type' => 3
                    ];

                    break;

                case '4':

                    // Anulação de matrícula
                    $articles = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('articles.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('traducao.display_name', 'like', "%Anulação de Matr%")
                        ->orderBy('traducao.display_name')
                        ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                        ->get();

                    $articles = collect($articles);

                    $anulacao = $articles->unique("nome");

                    $anulacao->values()->all();



                    return $data = [
                        'anulacao' => $anulacao,
                        'type' => 4
                    ];

                    break;
                case '5':

                    // Percurso académico
                    $articles = DB::table('articles')
                        ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                        ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->whereNull('articles.deleted_by')
                        ->whereNull('traducao.deleted_at')
                        ->where('traducao.active', 1)
                        ->where('articles.id_code_dev', 25)
                        ->orderBy('traducao.display_name')
                        ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                        ->get();

                    $articles = collect($articles);

                    $articles = $articles->unique("nome");

                    $articles->values()->all();



                    return $data = [
                        'percurso' => $articles,
                        'type' => 5
                    ];

                    break;

                default:


                    break;
            }






            // return view('Payments::requests.request-create')->with($data);

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    # Pegar os articles que serão requeridos para cerimonia
    public function getUserArticleCerimonia($id){
        //TODO: avaliar para quando o select estiver vazio
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->where("id", $id)
                ->first();

            $emo = array("diploma", "certificado", "cerimônia de outorga");

            $emolumentos = DB::table('articles')
                ->join('article_translations as traducao', 'traducao.article_id', "=", "articles.id")
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->whereNull('articles.deleted_by')
                ->whereNull('traducao.deleted_at')
                ->where('traducao.active', 1)
                ->whereIn('traducao.display_name', $emo)
                ->orderBy('traducao.display_name')
                ->select(['articles.id as id_article', 'traducao.display_name as nome', 'articles.id_code_dev', 'articles.base_value as money'])
                ->get();

            return $data = $emolumentos;

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    # Pegar a matrícula do estudante
    public function matriculation($data){
       
        $data = explode(',', $data);
        $ano = $data[0];
        $doc_type = $data[1];
       
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

        $currentData = Carbon::now();
        $lective = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lective = $lective->id ?? 11;


        $lectiveYearSelected = LectiveYear::whereId($ano)->first();

        if ($doc_type == 9) {
            $matriculation = DB::table('user_candidate as uc')
                ->leftjoin('users', 'uc.user_id', '=', 'users.id')
                ->join('grades as g', 'g.student_id', '=', 'users.id')
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->whereColumn('g.student_id', 'user_id')
                ->where('uc.year', $ano)
                ->where('up.parameters_id', 1)
                ->whereNull('up.deleted_at')
                ->whereNull('users.deleted_at')
                ->select(['users.id as codigo', 'up.value as name', 'users.email', 'uc.code as code'])
                ->orderBy("up.value")
                ->distinct()
                ->get();


        } else if ($doc_type == 12 || $doc_type == 4 || $doc_type == 5 || $doc_type == 6 || $doc_type == 2) {
            
            $matriculation = DB::table('users')
                ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as u_p0', function ($join) {
                    $join->on('users.id', '=', 'u_p0.users_id')
                        ->where('u_p0.parameters_id', 19);
                })
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->where('usuario_cargo.role_id', 6)
                ->whereNull('u_p.deleted_at')
                ->whereNull('u_p0.deleted_at')
                ->whereNull('users.deleted_at')
                ->whereNull('users.deleted_by')
                ->select(['u_p0.value as code', 'users.id as codigo', 'u_p.value as name', 'users.email'])
                ->orderBy("u_p.value")
                ->distinct()
                ->get();
                

        } else {

            $matriculation = DB::table('matriculations as mt')
                ->leftjoin('users', 'mt.user_id', '=', 'users.id')
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                ->where('up.parameters_id', 1)
                ->where('up0.parameters_id', 19)
                ->whereNull('mt.deleted_at')
                ->whereNull('up.deleted_at')
                ->whereNull('users.deleted_at')
                // ->whereBetween('mt.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->select(['users.id as codigo', 'up.value as name', 'users.email', 'up0.value as matricula'])
                ->orderBy("up.value")
                ->distinct()
                ->get();
        }
       
        if($doc_type == 4){
            $disciplina_id = [71, 147, 223,287,355,421,489];
            // pegar os ids dos alunos já retornados na coleção $matriculation
            $idsMatriculados = $matriculation->pluck('codigo');

            // buscar apenas os que têm nota >= 10 nessas disciplinas
            $idsPermitidos = DB::table('new_old_grades')
                ->whereIn('user_id', $idsMatriculados)
                ->whereIn('discipline_id', $disciplina_id)
                ->where('grade', '>=', 10)
                ->pluck('user_id');

            // filtrar a coleção original
            $matriculation = $matriculation->whereIn('codigo', $idsPermitidos);

            return [
                'doc_type' => $doc_type,
                'matriculation' => $matriculation->values()->toArray()
            ];
        }else{
            return [
                'doc_type' => $doc_type,
                'matriculation' => $matriculation->values()->toArray()
            ];
        }
        

      

    }

    # Requerer qualquer documento
    public function store_doc($dados){
    Log::info("dados do store_doc:".$dados);
        $dados = explode(',', $dados);
        $vazio = 0;


        foreach ($dados as $item) {
            if ($item == "") {
                $vazio = "1";
            } else {
            }
        };

        if ($vazio == 1) {
            return $data = ['dados' => "Por favor preencha todos os campos!!!", 'code' => "empty"];
        } else {

            $lective_year = $dados[0];
            $article_id = $dados[1];
            $value = $dados[2];
            $type = $dados[3];
            $user = $dados[4];
            $year = intval($dados[5]);
            $efeito = $dados[6];
            $ano_diploma = $dados[7];
            $folha = $dados[8];
            $data_outorga = $dados[9];
            $n_registro = $dados[10];

            if ($type == 3) {

                if (
                    DB::table('requerimento')
                        ->where('user_id', "=", $user)
                        ->where('codigo_documento', "=", $type)
                        ->where('ano', "=", $year)
                        ->exists()
                ) {
                    return $data = ['dados' => 'O documento selecionado já foi requerido!', 'code' => "exist"];
                } else {

                    $requerimento = DB::table('requerimento')->insertGetId(
                        [
                            'article_id' => 1,
                            'codigo_documento' => $type,
                            'ano' => $year,
                            "user_id" => $user,
                            'year' => $lective_year
                        ]
                    );

                    return $data = ['dados' => 'Requerimento criado com sucesso !!!', 'code' => "success"];
                }

            }

            // Criar um article
            $article = DB::table('article_requests')->insertGetId(
                [
                    "user_id" => $user,
                    "base_value" => $value,
                    "article_id" => $article_id,
                    "status" => "pending",
                    "created_by" => auth()->user()->id,
                    "created_at" => Carbon::now()
                ]
            );

            // Criar uma transação
            $transaction = DB::table('transactions')->insertGetId(
                [
                    'type' => 'debit',
                    'value' => $value,
                    'notes' => 'Débito inicial do valor base',
                    "created_by" => auth()->user()->id,
                    "created_at" => Carbon::now()
                ]
            );

            // Criar article e transações
            $article_transaction = DB::table('transaction_article_requests')->insertGetId(
                [
                    'article_request_id' => $article,
                    'transaction_id' => $transaction,
                    "value" => $value
                ]
            );

            // Guarda o codigo do documento 
            $requerimento = DB::table('requerimento')->insertGetId(
                [
                    'article_id' => $article,
                    'codigo_documento' => $type,
                    'ano' => $year,
                    'efeito' => $efeito,
                    "user_id" => $user,
                    'year' => $lective_year
                ]
            );



            // Se for um diploma 
            if ($type == 5) {

                $this->gerar_codigo_diploma($user, $ano_diploma, $folha, $data_outorga, $n_registro);

            }


            // Notificar o respectivo Estudante
            $users = DB::table('users')
                ->where('id', '=', $user)
                ->first();

            $doc = DB::table('documentation_type')
                ->where('id', '=', $type)
                ->first();


            $body = '<p>Caro(a) Estudante, ' . $users->name . ',  foi emitido um requerimento para <b>' . $doc->name . '</b>.</p>';
            $icon = "fas fa-receipt";
            $subjet = "[Secretaria]-Requerimento";
            $destinetion[] = $user;


            notification($icon, $subjet, $body, $destinetion, null, null);


            // Registrar o requerimento a tabela dos requerimentos
            return $data = ['dados' => 'Requerimento criado com sucesso !!!', 'code' => "success"];
        }


        // }
    }


    # Gerar o código do documento

    private function gerar_codigo_documento($requerimento_id)
    {

        try {

            $requerimento = DB::table('requerimento')
                ->where('requerimento.id', "=", $requerimento_id)
                ->join('lective_years as ly', 'requerimento.year', '=', 'ly.id')
                ->join('lective_year_translations as lyt', 'ly.id', '=', 'lyt.lective_years_id')
                ->select([
                    'requerimento.year as year',
                    'lyt.display_name as ano_lectivo'
                ])
                ->first();


            $last_code = DB::table('requerimento')
                ->where('year', $requerimento->year)
                ->select("code", "year")
                ->whereNotNull("code")
                ->orderBy("id", "desc")
                ->first();






            if (isset($last_code) && isset($last_code->code)) {
                $last_code->code = substr($last_code->code, 7);
                $code = 1 + intval($last_code->code);


                $code = $requerimento->ano_lectivo . '-' . sprintf('%04d', $code);

                DB::table('requerimento')
                    ->where('id', "=", $requerimento_id)
                    ->update(
                        [
                            "code" => $code
                        ]
                    );


                return $code;
            } else {
                $code = 1;

                $code = $requerimento->ano_lectivo . '-' . sprintf('%04d', $code);

                DB::table('requerimento')
                    ->where('id', "=", $requerimento_id)
                    ->update(
                        [
                            "code" => $code
                        ]
                    );
            }
        } catch (Exception $e) {
            dd($e);
        }


    }

    # PEGAR O Código do documento

    public static function get_code_doc($code, $year, $codigo_documento, $form)
    {

        $institution = Institution::latest()->first();

        if ($form == 1) {
            return substr($year, -2) . "-" . str_pad($code, 4, '0', STR_PAD_LEFT);
        }

        if ($form == 2) {
            $sigla = "";
            switch ($codigo_documento) {
                case '1':
                    $sigla = "DSN";
                    break;
                case '2':
                    $sigla = "DCN";
                    break;
                case '4':
                    $sigla = "CTF";
                    break;
                case '6':
                    $sigla = "DF";
                    break;
                case '8':
                    $sigla = "DFC";
                    break;
                case '5':
                    $sigla = "DPM";
                    break;
                case '9':
                    $sigla = "DCNEA";
                    break;
                case '10':
                    $sigla = "SdE";
                    break;
                default:
                    # code...
                    break;
            }

            return $institution->abrev . "." . $sigla . "." . substr($year, -2) . "." . str_pad($code, 4, '0', STR_PAD_LEFT);
        }
    }


    # Gerar o codigo do diploma de forma automática

    private function gerar_codigo_diploma($estudante, $ano, $folha, $data_outorga, $n_registro)
    {


        # Verificar se o estudante já possui um diploma

        $cde = 0;
        $cdn = 0;

        if (
            DB::table('estudante_diploma')
                ->where('user_id', $estudante)
                ->exists()
        ) {
            $cde = DB::table('estudante_diploma')
                ->where('user_id', "=", $estudante)
                ->select(["diploma"])
                ->first();

        } else {

            $new = DB::table('estudante_diploma')->insertGetId(
                [
                    'user_id' => $estudante,
                    'diploma' => $folha,
                    'n_registro' => $n_registro,
                    'data_final' => $ano,
                    'data_outorga' => $data_outorga,
                ]
            );

        }







        # Gerar um novo código número



    }

    # Requerer documentos de mérito

    public function store_doc_merito($dados)
    {



        $dados = explode(',', $dados);

        $tipo = $dados[0];







        switch ($tipo) {
            case 1:
                # Estudante...

                $estudante = $dados[1];
                $ano_curricula = $dados[2];
                $ano_lectivo = $dados[3];


                if (
                    DB::table('requerimento_merito')
                        ->where('user_id', "=", $estudante)
                        ->where('ano_curricular', "=", $ano_curricula)
                        ->where('ano_lectivo', "=", $ano_lectivo)
                        ->whereNull('deleted_at')
                        ->exists()
                ) {
                    return $data = ['dados' => 'O documento selecionado já foi requerido!', 'code' => "exist"];
                } else {

                    $requerimento = DB::table('requerimento_merito')->insertGetId(
                        [
                            'user_id' => $estudante,
                            'ano_curricular' => $ano_curricula,
                            "ano_lectivo" => $ano_lectivo,
                            "tipo" => $tipo,
                        ]
                    );

                    return $data = ['dados' => 'Requerimento criado com sucesso !!!', 'code' => "success"];
                }



                break;
            case 2:
                # Docente...

                $docente = $dados[1];
                $departamento = $dados[2];
                $ano_lectivo = $dados[3];


                if (
                    DB::table('requerimento_merito')
                        ->where('user_id', "=", $docente)
                        ->where('ano_lectivo', "=", $ano_lectivo)
                        ->where('departamento', "=", $departamento)
                        ->whereNull('deleted_at')
                        ->exists()
                ) {
                    return $data = ['dados' => 'O documento selecionado já foi requerido!', 'code' => "exist"];
                } else {

                    $requerimento = DB::table('requerimento_merito')->insertGetId(
                        [
                            'user_id' => $docente,
                            "ano_lectivo" => $ano_lectivo,
                            'departamento' => $departamento,
                            "tipo" => $tipo,
                        ]
                    );

                    return $data = ['dados' => 'Requerimento criado com sucesso !!!', 'code' => "success"];
                }


                break;
            case 3:
                # Funcionario...caminho = "/avaliations/store_doc_merito/" + [tipo, funcionario, seccao, ano_lectivo.val()];

                $funcionaro = $dados[1];
                $seccao = $dados[2];
                $ano_lectivo = $dados[3];


                if (
                    DB::table('requerimento_merito')
                        ->where('user_id', "=", $funcionaro)
                        ->where('ano_lectivo', "=", $ano_lectivo)
                        ->where('seccao', "=", $seccao)
                        ->whereNull('deleted_at')
                        ->exists()
                ) {
                    return $data = ['dados' => 'O documento selecionado já foi requerido!', 'code' => "exist"];
                } else {

                    $requerimento = DB::table('requerimento_merito')->insertGetId(
                        [
                            'user_id' => $funcionaro,
                            "ano_lectivo" => $ano_lectivo,
                            'seccao' => $seccao,
                            "tipo" => $tipo,
                        ]
                    );

                    return $data = ['dados' => 'Requerimento criado com sucesso !!!', 'code' => "success"];
                }





                break;

            default:
                # code...
                break;
        }
    }







    public function studant_get_year($matriculation_id)
    {

        $years = DB::table('matriculation_classes as mc')
            ->join('classes', 'classes.id', '=', 'mc.class_id')
            ->where('mc.matriculation_id', $matriculation_id)
            ->whereNull('classes.deleted_at')
            ->whereNull('classes.deleted_at')
            ->select(["classes.id", "classes.display_name as classes", "classes.year"])
            ->get();

        // $year=  DB::table('matriculations as mt')
        // ->join("")
        // ->where('user_id',$id) 
        // ->where('lective_year',$lective_year) 
        // ->whereNull('deleted_at')
        // ->whereNull('deleted_by')
        // ->select(["id","course_year"])
        // ->get();



        return ["years" => $years];

    }
    public function get_classes($year, $lective_year, $matriculation_id, $turno)
    {

        $matriculation_class = DB::table('matriculation_classes as mc')
            ->join('classes', 'classes.id', '=', 'mc.class_id')
            ->where('mc.matriculation_id', $matriculation_id)
            ->where('classes.year', $year)
            ->where('classes.lective_year_id', $lective_year)
            ->whereNull('classes.deleted_at')
            ->whereNull('classes.deleted_at')
            ->select(["classes.id", "classes.display_name as classes", "classes.year", "classes.courses_id"])
            ->first();

        $classes = DB::table('classes')
            ->where('lective_year_id', $lective_year)
            ->where('year', $year)
            ->where('schedule_type_id', "=", $turno)
            ->where('courses_id', "=", $matriculation_class->courses_id)
            ->where('id', "!=", $matriculation_class->id)
            ->whereNull('deleted_at')
            ->whereNull('deleted_at')
            ->select(["id", "display_name as classes"])
            ->get();


        return ["classes" => $classes];

    }


    public function get_students_matriculation($lective_year)
    {


        $emolumento_confirma_prematricula = $this->pre_matricula_confirma_emolumento($lective_year);
        $matriculation = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
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

            ->leftJoin('article_requests as art_requests', function ($join) use ($emolumento_confirma_prematricula) {
                $join->on('art_requests.user_id', '=', 'u0.id')
                    ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
            })

            ->where('art_requests.deleted_by', null)
            ->where('art_requests.deleted_at', null)
            ->groupBy('u_p.value')
            ->distinct('id')
            ->where('matriculations.lective_year', $lective_year)
            ->select([

                'u0.id as id',
                'matriculations.id as matriculation_id',
                'uc.courses_id',
                'u0.name',
                'u_p.value as full_name',
                'up_meca.value as matriculation',
                'u0.email as email',
                'ct.display_name as course',
            ])
            ->get();


        return ["matriculation" => $matriculation];

    }

    private function pre_matricula_confirma_emolumento($lective_year)
    {
        $confirm = EmolumentCodevLective("confirm", $lective_year)->first();
        $Prematricula = EmolumentCodevLective("p_matricula", $lective_year)->first();
        $emolumentos = [];

        if ($confirm != null) {
            $emolumentos[] = $confirm->id_emolumento;
        }
        if ($Prematricula != null) {
            $emolumentos[] = $Prematricula->id_emolumento;
        }
        return $emolumentos;


    }


    public function mudanca_turma_store(Request $request)
    {




        if (
            DB::table('tb_change_class_student')
                ->where('id_confirmation_matriculation', "=", $request->student)
                ->where('lectiveYear', "=", $request->lective_years)
                ->where('id_new_class', "=", $request->new_class)
                ->where('id_old_class', "=", $request->old_class)
                ->where('description', "=", $request->description)
                ->where('status', "=", "Pending")
                ->whereNull('deleted_at')
                ->exists()
        ) {

            Toastr::warning(__('A forLEARN detectou que o estundate selecionado, já possui um pedido de mudança de turma em andamento!'), __('toastr.warning'));

            return redirect()->back();
        } else {


            $user = DB::table('matriculations')
                ->where('id', $request->student)
                ->select(["user_id"])
                ->first();

            $article = $this->generate_emolument($user->user_id, $request->lective_years);

            $requerimento = DB::table('tb_change_class_student')->insertGetId(
                [
                    'id_confirmation_matriculation' => $request->student,
                    'lectiveYear' => $request->lective_years,
                    'id_new_class' => $request->new_class,
                    'id_old_class' => $request->old_class,
                    'description' => $request->description,
                    'article_request_id' => $article,
                    'status' => "pending",
                    'created_by' => auth()->user()->id,
                ]
            );

            Toastr::success(__('Mudança de turma requerida com sucesso'), __('toastr.success'));

            return redirect()->route('requerimento.index');
        }

    }

    public function generate_emolument($user, $lective_year)
    {

        $articles = DB::table('articles')
            ->where("anoLectivo", $lective_year)
            ->where("id_code_dev", 23)
            ->whereNull("deleted_at")
            ->select(["id", "base_value"])
            ->first();

        if (!isset($articles->id)) {
            Toastr::warning(__('A forLEARN não detectou um emolumento de mudança de turma!'), __('toastr.warning'));
            return redirect()->back();
        }



        $article = DB::table('article_requests')->insertGetId(
            [
                "user_id" => $user,
                "base_value" => $articles->base_value,
                "article_id" => $articles->id,
                "status" => "pending",
                "created_by" => auth()->user()->id,
                "created_at" => Carbon::now()
            ]
        );

        $transaction = DB::table('transactions')->insertGetId(
            [
                'type' => 'debit',
                'value' => $articles->base_value,
                'notes' => 'Débito inicial do valor base',
                "created_by" => auth()->user()->id,
                "created_at" => Carbon::now()
            ]
        );

        // Criar article e transações

        $article_transaction = DB::table('transaction_article_requests')->insertGetId(
            [
                'article_request_id' => $article,
                'transaction_id' => $transaction,
                "value" => $articles->base_value
            ]
        );

        return $article;


    }

    public function destroy(Request $request)
    {
        try {


            switch ($request->get("type")) {
                case 'emolumento':
                    $requires = DB::table('requerimento')
                        ->where('id', "=", $request->get("id"))
                        ->first();

                    DB::table('requerimento')
                        ->where('id', "=", $request->get("id"))
                        ->update(
                            [
                                "deleted_by" => auth()->user()->id,
                                "deleted_at" => Carbon::now()
                            ]
                        );


                    $articles = DB::table('article_requests')
                        ->where('id', "=", $requires->article_id)
                        ->where('user_id', "=", $requires->user_id)
                        ->update(
                            [
                                "deleted_by" => auth()->user()->id,
                                "deleted_at" => Carbon::now()
                            ]
                        );

                    if (isset($requires->transference_request)) {
                        DB::table('tb_transference_studant')
                            ->where('id', "=", $requires->transference_request)
                            ->update(
                                [
                                    "deleted_by" => auth()->user()->id,
                                    "deleted_at" => Carbon::now()
                                ]
                            );
                    }

                    break;
                case 'mudanca_curso':

                    $require = DB::table('tb_change_class_student')
                        ->where('id', "=", $request->get("id"))
                        ->first();


                    $user = DB::table('matriculations')
                        ->where('id', $require->id_confirmation_matriculation)
                        ->select(["user_id"])
                        ->first();


                    $articles = DB::table('article_requests')
                        ->where('id', "=", $require->article_request_id)
                        ->where('user_id', "=", $user->user_id)
                        ->update(
                            [
                                "deleted_by" => auth()->user()->id,
                                "deleted_at" => Carbon::now()
                            ]
                        );

                    $change = DB::table('tb_change_class_student')
                        ->where('id', "=", $request->get("id"))
                        ->update(
                            [
                                "deleted_by" => auth()->user()->id,
                                "deleted_at" => Carbon::now()
                            ]
                        );

                    break;
                case 'diploma':
                    $update = DB::table('requerimento_merito')
                        ->where('id', "=", $request->get("id"))
                        ->update(
                            [
                                "deleted_by" => auth()->user()->id,
                                "deleted_at" => Carbon::now()
                            ]
                        );
                    break;

                default:

                    break;
            }



            // Success message
            Toastr::success(__('Requerimento eliminado com sucesso'), __('toastr.success'));

            return redirect()->route('requerimento.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function store_doc_cerimonia(Request $request)
    {
        try {

            // return $request;

            $emolumentos = $request->emolumentos;
            $user = $request->student;
            $ano = $request->dataconclusao;
            $folha = $request->folha;

            foreach ($emolumentos as $item) {
                $emo = explode("-", $item);


                switch ($emo[2]) {
                    case '0':

                        // Criar um article

                        $value = $emo[1];
                        $article_id = $emo[0];
                        $type = $emo[2];

                        $article = DB::table('article_requests')->insertGetId(
                            [
                                "user_id" => $user,
                                "base_value" => $value,
                                "article_id" => $article_id,
                                "status" => "pending",
                                "created_by" => auth()->user()->id,
                                "created_at" => Carbon::now()
                            ]
                        );

                        // Criar uma transação

                        $transaction = DB::table('transactions')->insertGetId(
                            [
                                'type' => 'debit',
                                'value' => $value,
                                'notes' => 'Débito inicial do valor base',
                                "created_by" => auth()->user()->id,
                                "created_at" => Carbon::now()
                            ]
                        );

                        // Criar article e transações

                        $article_transaction = DB::table('transaction_article_requests')->insertGetId(
                            [
                                'article_request_id' => $article,
                                'transaction_id' => $transaction,
                                "value" => $value
                            ]
                        );


                        // Notificar o respectivo Estudante

                        $users = DB::table('users')
                            ->where('id', '=', $user)
                            ->first();



                        $body = '<p>Caro(a) Estudante, ' . $users->name . ',  foi emitido um requerimento para <b>Cerimônia de Outorga</b>.</p>';
                        $icon = "fas fa-receipt";
                        $subjet = "[Secretaria]-Requerimento";
                        $destinetion[] = $user;


                        notification($icon, $subjet, $body, $destinetion, null, null);



                        break;

                    default:
                        # Criação do emolumento para o certificado

                        // Criar um article

                        $value = $emo[1];
                        $article_id = $emo[0];
                        $type = $emo[2];

                        $article = DB::table('article_requests')->insertGetId(
                            [
                                "user_id" => $user,
                                "base_value" => $value,
                                "article_id" => $article_id,
                                "status" => "pending",
                                "created_by" => auth()->user()->id,
                                "created_at" => Carbon::now()
                            ]
                        );

                        // Criar uma transação

                        $transaction = DB::table('transactions')->insertGetId(
                            [
                                'type' => 'debit',
                                'value' => $value,
                                'notes' => 'Débito inicial do valor base',
                                "created_by" => auth()->user()->id,
                                "created_at" => Carbon::now()
                            ]
                        );

                        // Criar article e transações

                        $article_transaction = DB::table('transaction_article_requests')->insertGetId(
                            [
                                'article_request_id' => $article,
                                'transaction_id' => $transaction,
                                "value" => $value
                            ]
                        );

                        // Guarda o codigo do documento 

                        if ($emo[2] == '4') {

                            $requerimento = DB::table('requerimento')->insertGetId(
                                [
                                    'article_id' => $article,
                                    'codigo_documento' => 4,
                                    'ano' => "null",
                                    'efeito' => "null",
                                    "user_id" => $user
                                ]
                            );
                        } else {
                            $this->gerar_codigo_diploma($user, $request->dataconclusao, $folha, $request->data_outorga);
                            $requerimento = DB::table('requerimento')->insertGetId(
                                [
                                    'article_id' => $article,
                                    'codigo_documento' => 5,
                                    'ano' => "null",
                                    'efeito' => "null",
                                    "user_id" => $user,

                                ]
                            );
                        }


                        // Notificar o respectivo Estudante

                        $users = DB::table('users')
                            ->where('id', '=', $user)
                            ->first();

                        $doc = DB::table('documentation_type')
                            ->where('id', '=', $type)
                            ->first();


                        $body = '<p>Caro(a) Estudante, ' . $users->name . ',  foi emitido um requerimento para <b>' . $doc->name . '</b>.</p>';
                        $icon = "fas fa-receipt";
                        $subjet = "[Secretaria]-Requerimento";
                        $destinetion[] = $user;


                        notification($icon, $subjet, $body, $destinetion, null, null);
                        break;
                }
            }


            // Success message
            Toastr::success(__('Requerido com sucesso'), __('toastr.success'));

            return redirect()->route('requerimento.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::budget.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function get_folha($number)
    {

        if (DB::table('estudante_diploma')->where('diploma', $number)->exists()) {
            return response()->json(["estado" => 1]);
        } else {
            return response()->json(["estado" => 0]);
        }

    }

    public function request_articles()
    {
        return "Preencha os dados abaixo correctamente";
        $article_id = 216;
        $user = 9372;
        $funcionario = 8831;

        $article = DB::table('articles')->where("id", $article_id)->first();

        $article_request = DB::table('article_requests')->insertGetId(
            [
                "user_id" => $user,
                "base_value" => $article->base_value,
                "article_id" => $article->id,
                "status" => "pending",
                "created_by" => $funcionario,
                "created_at" => Carbon::now()
            ]
        );

        // Criar uma transação

        $transaction = DB::table('transactions')->insertGetId(
            [
                'type' => 'debit',
                'value' => $article->base_value,
                'notes' => 'Débito inicial do valor base',
                "created_by" => $funcionario,
                "created_at" => Carbon::now()
            ]
        );

        // Criar article e transações

        $article_transaction = DB::table('transaction_article_requests')->insertGetId(
            [
                'article_request_id' => $article_request,
                'transaction_id' => $transaction,
                "value" => $article->base_value
            ]
        );
    }

    public static function get_requerimento($article)
    {
        return $requerimento = DB::table("requerimento")
            ->where("article_id", $article)
            ->select(["id"])
            ->get();
    }

    public function create_defesa($type)
    {
        if ($type != 1 && $type != 2) {
            abort(404);
        }
        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 10;
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'courses' => $courses,
            'type' => $type
        ];

        return view("Avaliations::requerimento.defesa")->with($data);
    }
    //referencia para pegar os estudantes finalistas
    public function get_finalists(Request $request, $course_id, $lective_year){

        
        $type = $request->query('type',null);

        $lectiveYearSelected = DB::table('lective_years')->where('id', $lective_year)->first();
        $students = [];

        if(isset($type) && $type == 'all'){
            
            

            // $students_ids= studentsSelectList()->pluck('id')->toArray();
           
            $students = DB::table('users')
                // ->whereIn('users.id',$students_ids)
                ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->where('usuario_cargo.role_id', 6)
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->where('uc.courses_id', $course_id)
                ->whereNull('up.deleted_at')
                ->where('up.parameters_id', 1)
                ->where('up0.parameters_id', 19)
                ->whereNull('up0.deleted_at')
                ->whereNull('users.deleted_at')
                ->whereNull('users.deleted_by')
                ->select([
                    'users.id as user_id',
                    'ct.display_name as course',
                    'up.value as name',
                    'users.email as email',
                    'up0.value as student_number'
                ])
                ->orderBy("name")
                ->distinct('id')
                ->get();
          
           

        }
        else if(isset($type) && $type =='finalists'){

            $allDiscipline = Discipline::whereCoursesId($course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'disciplines.id')
            ->select('disciplines.id as id')
            ->get();

            $students = DB::table('users')
            // ->whereIn('users.id',$students_ids)
            ->join('model_has_roles as usuario_cargo', 'users.id', '=', 'usuario_cargo.model_id')
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->where('usuario_cargo.role_id', 6)
            ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
            ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->where('uc.courses_id', $course_id)
            ->whereNull('up.deleted_at')
            ->where('up.parameters_id', 1)
            ->where('up0.parameters_id', 19)
            ->whereNull('up0.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNull('users.deleted_by')
            ->select([
                'users.id as user_id',
                'ct.display_name as course',
                'up.value as name',
                'users.email as email',
                'up0.value as student_number'
            ])
            ->orderBy("name")
            ->distinct('id')
            ->get();


            // $students = $students->filter(function($student) use ($allDiscipline) {

            //     $grades = DB::table('new_old_grades')
            //             ->where('user_id', $student->user_id)
            //             ->distinct(['user_id','grade'])  
            //             ->get();

                        

            //             $tfc = $allDiscipline->where('tfc',1)->first();

            //             $dispensou_tfc = false;
                        
            //             if(isset($tfc)){
            //                 $tfc = $tfc->id;

            //                 $dispensou_tfc =  $grades->contains(function($g) use ($tfc) {
            //                     return $g->discipline_id == $tfc && $g->grade >= 10;
            //                 });

            //                  $allDiscipline = $allDiscipline->whereNotIn('id',$tfc);
            //             }

            //             $grades_count =  $allDiscipline->filter(function($d) use ($grades) {
            //                 return $grades->contains('discipline_id',$d->id);
            //             })->count();

            //             $completou = $allDiscipline->count() == $grades_count;
             
            //         return $completou && !$dispensou_tfc;
         
            //         });


        }
        else{

            if (isset($lectiveYearSelected)) {


                $students = DB::table('matriculations as mt')
                    ->leftjoin('users', 'mt.user_id', '=', 'users.id')
                    ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                    ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->where('up.parameters_id', 1)
                    ->where('up0.parameters_id', 19)
                    ->where('uc.courses_id', $course_id)
                    ->whereNull('mt.deleted_at')
                    ->whereNull('up.deleted_at')
                    ->whereNull('users.deleted_at')
                    ->whereBetween('mt.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->select([
                        'users.id as user_id',
                        'ct.display_name as course',
                        'up.value as name',
                        'users.email as email',
                        'up0.value as student_number'
                    ])
                    ->orderBy("name")
                    ->distinct('id')
                    ->get();
    
    
            }


        }
       

        return response()->json($students);
    }

    public function defesa_store(Request $request)
    {

        try {


            DB::beginTransaction();
            //codev dos emolumentos
            $request->type == 1 ? $codev = "defesa_acta" : $codev = "defesa_extraordinaria";

            //Emolumento com base no ano lectivo
            $emolumento_defesa = EmolumentCodevLective($codev, $request->anoLectivo);

            if ($emolumento_defesa->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de defesa configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }
            $article_id = $emolumento_defesa[0]->id_emolumento;

            //Emolumento
            $consulta = DB::table('article_requests')
                ->where('user_id', $request->students)
                ->where('article_id', $article_id)
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->get();

            if (!$consulta->isEmpty()) {
                // Toastr::warning(__('A forLEARN não detectou que já existe uma marcação de pedido de transferência para este estudante, por favor verifique a existência do emolumento na tesouraria para validar a mesma , caso contrário contacte o apoio a forLEARN'), __('toastr.warning'));
                Toastr::warning(__('A forLEARN detectou que já existe um emolumento de defesa para este estudante, por favor verifique a tabela para validar a mesma , caso contrário contacte o apoio a forLEARN'), __('toastr.warning'));
                return redirect()->back();
            }

            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de defesa, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }

            if($codev == "defesa_acta"){

                $codev2 = "pre_defesa_acta";
                //dd($request->anoLectivo);
                $emolumento_defesa = EmolumentCodevLective($codev2, $request->anoLectivo);

            if ($emolumento_defesa->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de pré-defesa configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }
            $article_id = $emolumento_defesa[0]->id_emolumento;

            //Emolumento
            $consulta = DB::table('article_requests')
                ->where('user_id', $request->students)
                ->where('article_id', $article_id)
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->get();

            if (!$consulta->isEmpty()) {
                // Toastr::warning(__('A forLEARN não detectou que já existe uma marcação de pedido de transferência para este estudante, por favor verifique a existência do emolumento na tesouraria para validar a mesma , caso contrário contacte o apoio a forLEARN'), __('toastr.warning'));
                Toastr::warning(__('A forLEARN detectou que já existe um emolumento de pré-defesa para este estudante, por favor verifique a tabela para validar a mesma , caso contrário contacte o apoio a forLEARN'), __('toastr.warning'));
                return redirect()->back();
            }

            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de pré-defesa, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }
            }


            // GUARDAR REQUERIMENTO

            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo
                ]
            );


            Toastr::success(__('O pedido de defesa foi efectuado com sucesso.'), __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    public function createStudentCard()
    {

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 10;
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'courses' => $courses
        ];

        return view("Avaliations::requerimento.student_card")->with($data);
    }

    public function student_card_store(Request $request)
    {

        try {


            DB::beginTransaction();
            //codev dos emolumentos
            $codev = "cartao_estudante";

            //Emolumento com base no ano lectivo
            $emolumento = EmolumentCodevLective($codev, $request->anoLectivo);

            if ($emolumento->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de cartão de estudante configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }
            $article_id = $emolumento[0]->id_emolumento;



            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de cartão de estudante, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }

            //gerar validade 
            event(new PaidStudentCardEvent($request->students));

            // GUARDAR REQUERIMENTO

            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo
                ]
            );


            Toastr::success(__('O pedido de cartão de estudante foi efectuado com sucesso.'), __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }



    public function createRequerimento($codev)
    {

        //Emolumento com base no ano lectivo
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
                        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                        ->first();

        $emolumento = EmolumentCodevLective($codev, $lectiveYearSelected->id);

        if ($emolumento->isEmpty()) {
            Toastr::warning(__('A forLEARN não encontrou um emolumento configurado no ano lectivo selecionado.'), __('toastr.warning'));
            return redirect()->back();
        }
        $article_id = $emolumento[0]->id_emolumento;

        $article = Article::whereId($article_id)
        ->with('currentTranslation')
        ->firstOrFail();

        $titulo = $article->currentTranslation->display_name;
       

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 10;
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'courses' => $courses,
            'titulo' => $titulo,
            'codev' => $codev
        ];

        return view("Avaliations::requerimento.create_requerimento")->with($data);
    }

    public function student_requerimento_store(Request $request)
    {

        try {


            DB::beginTransaction();
            //codev dos emolumentos
            $codev = $request->codev;

            //Emolumento com base no ano lectivo
            $emolumento = EmolumentCodevLective($codev, $request->anoLectivo);

            if ($emolumento->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento configurado no ano lectivo selecionado.'), __('toastr.warning'));
                return redirect()->back();
            }
            $article_id = $emolumento[0]->id_emolumento;



            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de cartão de estudante, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }


            $id_codev= DB::table('code_developer')
                        ->where('code',$codev)
                        ->first()->id;

            // GUARDAR REQUERIMENTO
            $codigo_documento = DB::table('documentation_type')
                                ->where('codev',$id_codev)
                                ->first();

            if(isset($codigo_documento))
            $id_codigo_documento = $codigo_documento->id;
          


            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo,
                    'codigo_documento' => $id_codigo_documento ?? null
                ]
            );


            Toastr::success(__('O requerimento foi efectuado com sucesso.'), __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }


    
    public function solicitacao_estagio_store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Definir código do emolumento para solicitação de estágio
            
            $codev = ($request->type === 'estagio') ? "solicitacao_estagio" :
            (($request->type === 'carta') ? "carta_recomendacao" : '');
   
   $title = ($request->type === 'estagio') ? "Solicitação de Estágio" :
            (($request->type === 'carta') ? "Carta de Recomendação" : '');

            // Obter emolumento baseado no ano letivo
            $emolumento = EmolumentCodevLective($codev, $request->anoLectivo);

            if ($emolumento->isEmpty()) {
                $message = 'A forLEARN não encontrou um emolumento de ' . $title . ' configurado para o ano letivo selecionado.';
                Toastr::warning($message, __('toastr.warning'));
                return redirect()->back();
            }

            $article_id = $emolumento[0]->id_emolumento;

          
            // Criar requisição de artigo automaticamente
            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                $message = 'Não foi possível criar o emolumento de ' . $title .'. Por favor, tente novamente.';
                Toastr::error($message, __('toastr.error'));
                return redirect()->back();
            }

            // Salvar requerimento
            DB::table('requerimento')->insert([
                'article_id' => $article_request_id,
                'user_id' => $request->students,
                'year' => $request->anoLectivo,
                'nomedainstituicao_SdE' => $request ->nomedainstituicao_SdE,
                'codigo_documento' => ($codev == "solicitacao_estagio") ? 15 : 16
            ]);

            // Mensagem de sucesso
            $message = 'O pedido de solicitação de '. $title .' foi efetuado com sucesso.';
            Toastr::success($message, __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    public function getStudentsByCourse($courseId)
    {
        try {
            Log::info("Buscando estudantes para o curso: " . $courseId);

            // Busca apenas estudantes ativos relacionados ao curso com os joins especificados
            $students = DB::table('users')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up', 'up.users_id', '=', 'users.id')
                ->leftjoin('user_parameters as up0', 'up0.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) use ($courseId) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id')
                        ->where('uc.courses_id', '=', $courseId) // Condição para o curso específico
                        ->where('ct.language_id', '=', LanguageHelper::getCurrentLanguage()) // Obtém a linguagem atual
                        ->where('ct.active', true); // Apenas cursos ativos
                })
                ->where('up.parameters_id', 1)
                ->where('up0.parameters_id', 19)
                ->select(
                    'users.id',
                    'users.name',
                    'ct.display_name as course_name',
                    'up.value as name',
                    'users.email as email',
                    'up0.value as student_number'
                )
                // Inclui o nome do curso traduzido
                ->groupBy('up.value')
                ->get();

            Log::info("Estudantes encontrados: ", $students->toArray());

            return response()->json($students);
        } catch (Exception $e) {
            Log::error("Erro ao buscar estudantes: " . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar estudantes: ' . $e->getMessage()], 500);
        }
    }
    public function solicitacao_estagio($type)
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 7;

            // Obter todos os cursos
            $allCourses = Course::join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
                ->select([
                    'courses.id as courses_id',
                    'ct.display_name as course_name'
                ])
                ->distinct()
                ->get();

            // Obter todos os anos letivos disponíveis
            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
             
            // Preparar dados para a view
            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears,
                'allCourses' => $allCourses, // Inclui todos os cursos
                'type' => $type
            ];

            return view('Avaliations::requerimento.solicitacao_estagio')->with($data);
        } catch (Exception $e) {
            Log::error("Erro ao carregar dados para solicitação de estágio: " . $e->getMessage());
            return response()->json(['error' => 'Erro ao carregar dados. Verifique o log para mais detalhes.'], 500);
        }
    }


    public function createStudentSchedule()
    {

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 10;
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'courses' => $courses
        ];

        return view("Avaliations::requerimento.student_schedule")->with($data);
    }

    public function student_schedule_store(Request $request)
    {

        try {


            DB::beginTransaction();
            //codev dos emolumentos
            $codev = "solicitacao_horario";

            //Emolumento com base no ano lectivo
            $emolumento = EmolumentCodevLective($codev, $request->anoLectivo);

            if ($emolumento->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de solicitação de horário configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }
            $article_id = $emolumento[0]->id_emolumento;



            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de solicitação de horário, por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }


            // GUARDAR REQUERIMENTO

            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo
                ]
            );


            Toastr::success(__('A solicitação de horário foi efectuado com sucesso.'), __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }


    public function createStudentTfc()
    {

        $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();

        $lectiveYearSelected = $lectiveYearSelected->id ?? 11;
        $courses = Course::with(['currentTranslation'])->get();

        $data = [
            'lectiveYearSelected' => $lectiveYearSelected,
            'lectiveYears' => $lectiveYears,
            'courses' => $courses
        ];

        return view("Avaliations::requerimento.student_tfc")->with($data);
    }

    public function student_tfc_store(Request $request)
    {

        try {

            DB::beginTransaction();
            //codev dos emolumentos
            $codev = "trabalho_fim_curso";
            //dd($request->anoLectivo);
            //Emolumento com base no ano lectivo
            $emolumento = EmolumentCodevLective($codev, $request->anoLectivo);
            
            if ($emolumento->isEmpty()) {
                Toastr::warning(__('A forLEARN não encontrou um emolumento de trabalho de fim de curso (inscrição) configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                return redirect()->back();
            }

            

            $article_id = $emolumento[0]->id_emolumento;



            $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

            if (!$article_request_id) {
                Toastr::error(__(' Não foi possivel criar o emolumento de  trabalho de fim de curso (inscrição), por favor tente novamente'), __('toastr.error'));
                return redirect()->back();
            }

            // GUARDAR REQUERIMENTO

            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo
                ]
            );


              //codev dos emolumentos
              $codev = "trabalho_fim_curso";

              //Emolumento com base no ano lectivo
              $emolumentos = EmolumentCodevLective($codev, $request->anoLectivo);
  
              if ($emolumentos->isEmpty()) {
                  Toastr::warning(__('A forLEARN não encontrou um emolumento de trabalho de fim de curso configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                  return redirect()->back();
              }

            foreach($emolumentos as $emolumento){

                $article_id = $emolumento->id_emolumento;

                $article_request_id = createAutomaticArticleRequest($request->students, $article_id, null, null);

                if (!$article_request_id) {
                    Toastr::error(__(' Não foi possivel criar o emolumento de  trabalho de fim de curso, por favor tente novamente'), __('toastr.error'));
                    return redirect()->back();
                }

                // GUARDAR REQUERIMENTO

            DB::table('requerimento')->insert(
                [
                    'article_id' => $article_request_id,
                    "user_id" => $request->students,
                    'year' => $request->anoLectivo
                ]
            );

            }

            Toastr::success(__('O requerimento  trabalho final de curso foi criado com sucesso.'), __('toastr.success'));

            DB::commit();
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


    }

    public function two_c(){
        try {


            DB::beginTransaction();

            $article_requests = DB::table('article_requests')
                                    ->where('article_id', 327)
                                    ->select(['id','user_id'])
                                    ->get();
          
            foreach($article_requests as $ar){

 // GUARDAR REQUERIMENTO
 if(!DB::table('requerimento')->where('article_id',$ar->id)->exists()){

    DB::table('requerimento')->insert(
        [
            'article_id' => $ar->id,
            "user_id" => $ar->user_id,
            'year' => 9
        ]
    );
}


            }

           
            DB::commit();

          dd('sucesso');
        } catch (Exception | Throwable $e) {
            DB::rollBack();

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }




}
