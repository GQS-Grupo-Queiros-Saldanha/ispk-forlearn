<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Log;
use Exception;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Classes;
use Illuminate\Http\Request;
use App\Modules\GA\Models\LectiveYear;
use Carbon\Carbon;
use DB;
use App\Modules\Users\Models\User;
use App\Modules\Users\Enum\ParameterGroupEnum;
use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Enum\CodevEnum;
use Yajra\DataTables\Facades\DataTables;
use Toastr;
use App\Model\Institution;
use PDF;

class StudentSpecialCourseController extends Controller
{

    public function index()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $courses = DB::table('special_courses')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 9;
            return view('Users::student-special-course.index', compact(
                'lectiveYears',
                'lectiveYearSelected',
                'courses'
            ));
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getStudentsBy($lectiveYear, $course, $edition)
    {
        try {
            $lectiveYear = DB::table('lective_years')
                ->where('id', $lectiveYear)
                ->first();

            $emolumento_inscricao = EmolumentCodevLective("curso_especial", $lectiveYear->id);

            $article_id = $emolumento_inscricao[0]->id_emolumento;

            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('id', [6]);
            })
                ->join('students_special_course as sc', 'sc.user_id', 'users.id')
                ->whereNull('sc.deleted_at')
                ->whereNull('sc.deleted_by')
                ->join('special_course_edition as sce', 'sce.id', 'sc.special_course_edition_id')
                ->when($course != "null", function ($join) use ($course) {
                    return $join->where('sce.special_course_id', $course);
                })
                ->when($edition != "null", function ($join) use ($edition) {
                    return $join->where('sce.id', $edition);
                })
                ->join('special_courses as ct', 'ct.id', 'sce.special_course_id')
                ->where('sce.lective_year_id', $lectiveYear->id)
                ->leftJoin('users as u2', 'u2.id', '=', 'sc.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'sc.deleted_by')
                ->leftJoin('users as u1', 'u1.id', '=', 'sc.created_by')
                ->leftJoin('article_requests', function ($join) use ($article_id) {
                    $join->on('article_requests.user_id', 'users.id')
                        ->whereNull('article_requests.deleted_at')
                        ->whereNull('article_requests.deleted_by')
                        ->where('article_requests.article_id', $article_id);
                })


                ->join('user_parameters as full_name', 'full_name.users_id', 'users.id')
                ->where('full_name.parameters_id', ParameterEnum::NOME)
                ->select([
                    'sce.number',
                    'sc.id',
                    'users.email',
                    'full_name.value as name_name',
                    'sc.diploma as diploma',
                    'u1.name as us_created_by',
                    'u2.name as us_updated_by',
                    'sc.updated_at as updated_at',
                    'sc.created_at as created_at',
                    'ct.display_name as cursos',
                    'ct.id as id_curso',
                    'article_requests.status as states',
                    'article_requests.id as art',
                    'sc.code as cand_number',
                    'sc.articles'

                ])
                ->orderBy('sc.id', 'DESC')
                ->get();


            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('diploma', function ($item) {
                    return view('Users::student-special-course.datatables.diploma')->with('item', $item);
                })
                ->addColumn('actions', function ($item) {
                    return view('Users::student-special-course.datatables.actions')->with('item', $item);
                })
                ->addColumn('states', function ($item) {
                    return view('Users::student-special-course.datatables.states')->with('item', $item);
                })
                ->rawColumns(['actions', 'diploma', 'states'])
                ->toJson();
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {

        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 9;

            $courses = DB::table('special_courses')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();


            return view(
                'Users::student-special-course.create',
                compact(
                    'lectiveYears',
                    'lectiveYearSelected',
                    'courses'
                )
            );
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function fetch($user_id) {}

    public function show() {}

    public function store(Request $request)
    {
        try {

            if ($request->student_type == 1) {
                DB::beginTransaction();

                $auth_id = auth()->user()->id ?? 1;
                $user = User::withTrashed()->where('email', $request->get('email'))->first();

                if (isset($user)) {
                    $user->name = $request->get('name');
                    $user->email = $request->get('email');
                    $user->password = bcrypt($request->get('id_number'));
                    $user->updated_by = $auth_id;
                    $user->deleted_at = null;
                    $user->save();
                } else {
                    $user = User::create([
                        'name' => $request->get('name'),
                        'email' => $request->get('email'),
                        'password' => bcrypt($request->get('id_number')),
                        'created_by' => $auth_id
                    ]);
                    $user->save();
                }

                $user_parameters[] = [
                    'parameters_id' => ParameterEnum::NOME,
                    'created_by' => $auth_id,
                    'parameter_group_id' => ParameterGroupEnum::DADOS_PESSOAIS,
                    'value' => $request->get('full_name')
                ];

                // id_number
                $user_parameters[] = [
                    'parameters_id' => 14,
                    'created_by' => auth()->user()->id ?? 0,
                    'parameter_group_id' => 3,
                    'value' => $request->get('id_number')
                ];


                $user->parameters()->sync($user_parameters);

                // Roles
                $user->syncRoles(6);

                DB::commit();
            } else {

                $user = User::where('id', $request->student)->first();
            }

            DB::beginTransaction();
            $articles = collect();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();


            $hasMatriculation = DB::table('matriculations')
                ->where('user_id', $user->id)
                ->where('lective_year', $lectiveYearSelected->id)
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->exists();


            if (!$hasMatriculation) {
                $emolumento_inscricao = EmolumentCodevLective("curso_especial", $lectiveYearSelected->id);

                if ($emolumento_inscricao->isEmpty()) {
                    Toastr::warning(__('A forLEARN não encontrou um emolumento de inscrição configurado[ configurado no ano lectivo selecionado].'), __('toastr.warning'));
                    return redirect()->back();
                }

                $article_id = $emolumento_inscricao[0]->id_emolumento;

                $article_request_id = createAutomaticArticleRequest($user->id, $article_id, null, null);

                if (!$article_request_id) {
                    Toastr::error(__(' Não foi possivel criar o emolumento de inscrição, por favor tente novamente'), __('toastr.error'));
                    return redirect()->back();
                }

                $articles->push($article_request_id);

                $article_request_id = $this->generateTaxaArticleRequest($user->id, CodevEnum::TAXA_CURSO_ESPECIAL_EXTERNO, $lectiveYearSelected);
                $articles->push($article_request_id);
            } else {
                $article_request_id = $this->generateTaxaArticleRequest($user->id, CodevEnum::TAXA_CURSO_ESPECIAL_INTERNO, $lectiveYearSelected);
                $articles->push($article_request_id);
            }
            $articles->toJson();
            $sc_edition = $request->special_course_edition;

            $hasInscription = DB::table('students_special_course')
                ->where('user_id', $user->id)
                ->where('special_course_edition_id', $sc_edition)
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->exists();

            if ($hasInscription) {
                Toastr::warning(__('Esta inscrição já existe'), __('toastr.warning'));
                return redirect()->back();
            }
            $latestsCandidate = DB::table('students_special_course')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->orderBy('id', 'DESC')->first();

            $nextCode = $this->createNewCode($latestsCandidate);

            DB::table('students_special_course')
                ->insert([
                    'articles' => $articles,
                    'code' => $nextCode,
                    'user_id' => $user->id,
                    'special_course_edition_id' => $sc_edition,
                    'course_id' => $request->course,
                    'created_by' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                ]);


            DB::commit();



            Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
            return redirect()->route('student-special-course.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    private function createNewCode($latestsCandidate)
    {
        if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
            $nextCode = $latestsCandidate->code + 1;
        } else {
            $nextCode = substr(Carbon::now()->format('Y'), -2) . '0001';
        }
        return $nextCode;
    }

    private function generateTaxaArticleRequest($user_id, $code_dev, $lectiveYear)
    {
        $propina = DB::table('articles')
            ->where('id_code_dev', $code_dev)
            ->where('anoLectivo', $lectiveYear->id)
            ->first();

        $article_id = $propina->id;
        $article_request_id = createAutomaticArticleRequest($user_id, $article_id, null, null);
        if (!$article_request_id) {
            Toastr::error(__(' Não foi possivel criar o emolumento, por favor tente novamente'), __('toastr.error'));
            return redirect()->back();
        }
        return $article_request_id;
    }

    public function getClasses($course_id, $lectiveYear)
    {

        try {

            if ($lectiveYear == "null") {
                $currentData = Carbon::now();
                $lectiveYearSelected = DB::table('lective_years')
                    ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                    ->first();



                $classes = DB::table('special_course_edition')
                    ->where('special_course_id', $course_id)
                    ->where('lective_year_id', $lectiveYearSelected->id)
                    ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                    ->get();
            } else {
                $classes = DB::table('special_course_edition')
                    ->where('special_course_id', $course_id)
                    ->where('lective_year_id', $lectiveYear)
                    ->get();
            }


            return json_encode(['classes' => $classes]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getStudents()
    {

        try {

            $students = DB::table('users')
                ->join('model_has_roles as mr', 'mr.model_id', 'users.id')
                ->where('model_type', 'App\Modules\Users\Models\User')
                ->join('roles', 'roles.id', 'mr.role_id')
                ->where('roles.id', 6)
                ->whereNotIn('users.id', [25])
                ->join('user_parameters as number', function ($join) {
                    $join->on('number.users_id', 'users.id');
                    $join->where('number.parameters_id', ParameterEnum::N_MECANOGRAFICO);
                })
                ->join('user_parameters as full_name', function ($join) {
                    $join->on('full_name.users_id', 'users.id');
                    $join->where('full_name.parameters_id', ParameterEnum::NOME);
                })
                ->select([
                    'users.id',
                    'users.email',
                    'full_name.value as name',
                    'number.value as matricula'
                ])
                ->get();

            return json_encode(['students' => $students]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $requires =  DB::table('students_special_course')
                ->find($id);


            DB::table('article_requests')
                ->whereIn('id', json_decode($requires->articles))
                ->update(
                    [
                        "deleted_by" => auth()->user()->id,
                        "deleted_at" => Carbon::now()
                    ]
                );

            DB::table('students_special_course')
                ->where('id', $requires->id)
                ->update([
                    'deleted_by' => auth()->user()->id,
                    'deleted_at' => Carbon::now()
                ]);

            Toastr::success(__('Inscrição eliminada com sucesso'), __('toastr.success'));
            return redirect()->route('student-special-course.index');
        } catch (Exception $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public static function openReport($id)
    {

        $inscription = DB::table('students_special_course as sc')
            ->where('sc.id', $id)
            ->join('special_courses as scs', 'scs.id', 'sc.course_id')
            ->select([
                'sc.*',
                'scs.display_name as course'
            ])
            ->first();

        $user = User::where('id', $inscription->user_id)->firstOrFail();


        $parameterPhoto = $user->parameters->where('code', 'fotografia')->first();
        $photo = $parameterPhoto ? $parameterPhoto->pivot->value : '';

        $parameterNome = $user->parameters->where('code', 'nome')->first();
        $personalName = $parameterNome ? $parameterNome->pivot->value : '';

        $parameterBI = $user->parameters->where('code', 'n_bilhete_de_identidade')->first();
        $personalBI = $parameterBI ? $parameterBI->pivot->value : '';

        $parameterMobilePhone = $user->parameters->where('code', 'telemovel_principal')->first();
        $personalMobilePhone = $parameterMobilePhone ? $parameterMobilePhone->pivot->value : '';

        $parameterMobilePhoneAlt = $user->parameters->where('code', 'telemovel_alternativo')->first();
        $personalMobilePhoneAlt = $parameterMobilePhoneAlt ? $parameterMobilePhoneAlt->pivot->value : '';

        $parameterPhone = $user->parameters->where('code', 'telefone_fixo')->first();
        $personalPhone = $parameterPhone ? $parameterPhone->pivot->value : '';

        $parameterEmail = $user->parameters->where('code', 'e-mail_2')->first();
        $personalEmail = $parameterEmail ? $parameterEmail->pivot->value : '';


        $institution = Institution::latest()->first();
        $titulo_documento = "Boletim de Matrícula";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 6;



        $data = [
            'photo' => $photo,
            'inscription' => $inscription,
            'personal' => [
                'name' => $personalName,
                'bi' => $personalBI,
                'mobile_phone' => $personalMobilePhone,
                'mobile_phone_alt' => $personalMobilePhoneAlt,
                'phone' => $personalPhone,
                'email' => $user->email,
                'email_2' => $personalEmail
            ],

            'created_by' => isset(auth()->user()->name) ? auth()->user()->name : null,

            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento,
        ];


        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf = PDF::loadView('Users::student-special-course.report', $data)
            ->setOption('margin-top', '10')
            ->setOption('margin-top', '2mm')
            ->setOption('margin-left', '2mm')
            ->setOption('margin-bottom', '2cm')
            ->setOption('margin-right', '2mm')
            ->setOption('header-html', '<header></header>')
            ->setOption('footer-html', $footer_html)
            ->setPaper('a4');

        return $pdf->stream($inscription->code . '.pdf');
    }


    public function listStudents()
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
            //-----------------------------------------------------------------------//

            $courses = DB::table('special_courses')
                ->whereNull('deleted_at')
                ->whereNull('deleted_by')
                ->get();


            $data = [
                'courses' => $courses,
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];

            return view('Users::student-special-course.list')->with($data);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function listPDF($edition_id)
    {
        try {
            $edition = DB::table('special_course_edition')
                ->where('special_course_edition.id', $edition_id)
                ->join('special_courses', 'special_courses.id', 'special_course_edition.special_course_id')
                ->select([
                    'special_course_edition.*',
                    'special_courses.display_name as course'
                ])
                ->first();

            $lectiveYears = LectiveYear::where('id', $edition->lective_year_id)
                ->with('currentTranslation')
                ->first();

            $institution = Institution::latest()->first();

            $emolumento_inscricao1 = EmolumentCodevLective("taxa_curso_especial", $lectiveYears->id);
            $emolumento_inscricao2 = EmolumentCodevLective("taxa_curso_especial_externo", $lectiveYears->id);

            if (empty($emolumento_inscricao1) || empty($emolumento_inscricao2)) {
                Toastr::warning(__('Emolumento em falta'), __('toastr.warning'));
                return back();
            }
            $article_id = [];

            $articles[0] = $emolumento_inscricao1[0]->id_emolumento;
            $articles[1] = $emolumento_inscricao2[0]->id_emolumento;

            $model = DB::table('students_special_course as ssc')
                ->where('ssc.special_course_edition_id', $edition_id)
                ->whereNull('ssc.deleted_at')
                ->whereNull('ssc.deleted_by')
                ->leftJoin('user_parameters as nome', 'nome.users_id', 'ssc.user_id')
                ->where('nome.parameters_id', ParameterEnum::NOME)
                ->leftJoin('users as email', 'email.id', 'ssc.user_id')

                ->select([
                    'ssc.code as code',
                    'ssc.articles as articles',
                    'nome.value as nome',
                    'email.email as email'
                ])

                ->get();

            $articlesParsed = $model->pluck('articles')->map(function ($article) {
                return collect(json_decode($article));
            })->flatten();


            $allArticles = DB::table('article_requests')
                ->whereIn('id', $articlesParsed->toArray())
                ->get();

            $model = $model->reject(function ($item) use ($allArticles) {
                $out = false;

                $articles = json_decode($item->articles);
                foreach ($articles as $ar) {

                    $out =  $allArticles->where('id', $ar)->first()->status == 'pending';
                    if ($out)
                        break;
                }

                return $out;
            });



            $pdf = PDF::loadView("Users::student-special-course.pdf-list", compact(
                'model',
                'lectiveYears',
                'edition',
                'institution',

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
            $pdf->setPaper('a4', 'landscape');
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);

            $pdf_name = "Lista_inscritos_cp";
            //$footer_html = view()->make('Users::users.partials.pdf_footer')->render();
            //$pdf->setOption('footer-html', $footer_html);
            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
}
