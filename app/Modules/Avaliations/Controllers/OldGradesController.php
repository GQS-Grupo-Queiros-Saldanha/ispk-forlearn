<?php

namespace App\Modules\Avaliations\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\Avaliations\Controllers\OldGradesController;
use App\Modules\Avaliations\Models\GradePath;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\util\FaseCandidaturaUtil;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Users\Models\TranferredStudent;
use App\Modules\Users\Models\TransferredStudent;
use App\Modules\Users\Models\UserState;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Log;
use PDF;
use Throwable;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use App\Model\Institution;
use App\Modules\Users\Enum\ParameterEnum;

class OldGradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $faseActual = FaseCandidaturaUtil::faseActual();
        $lectiveYearSelected = $faseActual->id_years ?? 6;
        return view("Avaliations::academic-path.list-students", compact('lectiveYears', 'lectiveYearSelected'));
    }

    public function list()
    {
        $lectiveYearSelected =  DB::table('lective_years')
            ->where('lective_years.id', 6)
            ->first();

        //Retornar todos os matriculados com o ano acima do 1.
        $students = DB::table('Import_data_forlearn')
            ->join('users', 'users.id', '=', 'Import_data_forlearn.id_user')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->leftJoin('user_publish_grade_Transition', 'user_publish_grade_Transition.id_student', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as created_by', function ($join) {
                $join->on('user_publish_grade_Transition.created_by', '=', 'created_by.users_id')
                    ->where('created_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as updated_by', function ($join) {
                $join->on('user_publish_grade_Transition.updated_by', '=', 'updated_by.users_id')
                    ->where('updated_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select([
                'u_p0.value as matriculation',
                'u_p.value as student',
                'users.id as id',
                'users.email as email',
                'Import_data_forlearn.codigo_old as code',
                'users.name as name',
                'Import_data_forlearn.ano_curricular as year',
                'ct.display_name as course',
                'user_publish_grade_Transition.created_at',
                'user_publish_grade_Transition.updated_at',
                'created_by.value as created_by',
                'updated_by.value as updated_by',
 ]
            )
            ->whereNull('users.deleted_by')
            // ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]) 
            ->where('Import_data_forlearn.type_user', 1)
            ->where('Import_data_forlearn.ano_curricular', '>=', 1)
            // ->whereNull('matriculations.deleted_at')
            ->get();


        return (DataTables::of($students)
            ->addColumn('actions', function ($item) {
                return view('Avaliations::academic-path.datatables.actions')->with('item', $item);
            })
            ->addColumn('created_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'created']);
            })
            ->addColumn('updated_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'updated']);
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'created_at', 'updated_at'])
            ->make('true'));
    }






    public function listWithGrades()
    {



        //Retornar todos os matriculados com o ano acima do 1.
        $students = DB::table('Import_data_forlearn as imp')
            ->join('users', 'users.id', '=', 'imp.id_user')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->leftJoin('user_publish_grade_Transition', 'user_publish_grade_Transition.id_student', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as created_by', function ($join) {
                $join->on('user_publish_grade_Transition.created_by', '=', 'created_by.users_id')
                    ->where('created_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as updated_by', function ($join) {
                $join->on('user_publish_grade_Transition.updated_by', '=', 'updated_by.users_id')
                    ->where('updated_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select(
                'u_p0.value as matriculation',
                'u_p.value as student',
                'users.id as id',
                'users.email as email',
                'u_p0.value as code',
                'users.name as name',
                'imp.ano_curricular as year',
                'ct.display_name as course',
                'user_publish_grade_Transition.created_at',
                'user_publish_grade_Transition.updated_at',
                'created_by.value as created_by',
                'updated_by.value as updated_by',
            )

            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('new_old_grades')
                    ->whereRaw('new_old_grades.user_id = users.id');
            })
            ->where('imp.type_user', 1)
            ->whereNull('users.deleted_by')
            ->get();


        return (DataTables::of($students)
            ->addColumn('actions', function ($item) {
                return view('Avaliations::academic-path.datatables.actions')->with('item', $item);
            })
            ->addColumn('created_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'created']);
            })
            ->addColumn('updated_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'updated']);
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'created_at', 'updated_at'])
            ->make('true'));
    }

    public function listWithoutGrades()
    {



        //Retornar todos os matriculados com o ano acima do 1.
        $students = DB::table('Import_data_forlearn as imp')
            ->join('users', 'users.id', '=', 'imp.id_user')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->leftJoin('user_publish_grade_Transition', 'user_publish_grade_Transition.id_student', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as created_by', function ($join) {
                $join->on('user_publish_grade_Transition.created_by', '=', 'created_by.users_id')
                    ->where('created_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as updated_by', function ($join) {
                $join->on('user_publish_grade_Transition.updated_by', '=', 'updated_by.users_id')
                    ->where('updated_by.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select(
                'u_p0.value as matriculation',
                'u_p.value as student',
                'users.id as id',
                'users.email as email',
                'u_p0.value as code',
                'users.name as name',
                'imp.ano_curricular as year',
                'ct.display_name as course',
                'user_publish_grade_Transition.created_at',
                'user_publish_grade_Transition.updated_at',
                'created_by.value as created_by',
                'updated_by.value as updated_by',
            )

            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('new_old_grades')
                    ->whereRaw('new_old_grades.user_id = users.id');
            })
            ->where('imp.type_user', 1)
            ->whereNull('users.deleted_by')
            ->get();


        return (DataTables::of($students)
            ->addColumn('actions', function ($item) {
                return view('Avaliations::academic-path.datatables.actions')->with('item', $item);
            })
            ->addColumn('created_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'created']);
            })
            ->addColumn('updated_at', function ($item) {
                return view('Avaliations::academic-path.datatables.dateTime')->with(['item' => $item, 'flag' => 'updated']);
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'created_at', 'updated_at'])
            ->make('true'));
    }

    public function storeGrade($id)
    {

        try {


            $documentoCode_documento = 1;
            $titulo_documento = "Percurso Académico";
            $documentoGerado_documento = "Documento gerado a";
            $institution = Institution::latest();


            $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

            //fazer pesquisa para verficiar se já existe notas desse estudante
            $grades = DB::table('new_old_grades')
                ->select('discipline_id', 'lective_year', 'grade')
                ->where('user_id', $id)
                ->get();

            $studentInfo = User::where('id', $id)
                ->with([
                    'parameters' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'groups'
                        ]);
                    },
                    'courses' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'matriculation'
                ])
                ->firstOrFail();



            $parameterNome = $studentInfo->parameters->where('code', 'nome')->first();
            $personalName = $parameterNome ? $parameterNome->pivot->value : '';

            $parameterMatriculation = $studentInfo->parameters->where('code', 'n_mecanografico')->first();
            $matriculationCode = $parameterMatriculation ? $parameterMatriculation->pivot->value : '';

            //disciplinas que ele esta matriculado


            $getDisciplinesMatriculed = DB::table('Import_data_forlearn as imp')
                ->join('users', 'imp.id_user', '=', 'users.id')
                ->join('user_courses', 'user_courses.users_id', '=', 'imp.id_user')
                ->join('study_plans', 'study_plans.courses_id', '=', 'user_courses.courses_id')
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'study_plans_has_disciplines.disciplines_id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })

                ->select('imp.ano_curricular as year', 'disciplines.id as discipline_id', 'disciplines.code as discipline_code')
                ->where('users.id', $id)
                // ->whereNotIn('disciplines.id', $disciplines)
                ->first();


            $otherDisciplines = DB::table('users')
                ->join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')


                /*->leftJoin('disciplines_translations as dcp', function ($join) {
                                $join->on('dcp.discipline_id', '=', 'matriculation_disciplines.discipline_id');
                                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('dcp.active', '=', DB::raw(true));
                            })*/


                ->where('users.id', $id)
                //->where('dcp.display_name', '!=', "Trabalho de fim de curso")
                ->pluck('matriculation_disciplines.discipline_id')
                ->whereNotIn('matriculation_disciplines.discipline_id', $disciplines)
                ->all();

            $dadosImportUser = DB::table('Import_data_forlearn as imp')
                ->select('imp.*')
                ->where('imp.id_user', $id)
                ->first();


            $disciplines = DB::table('users')
                ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
                ->join('study_plans', 'study_plans.courses_id', '=', 'user_courses.courses_id')
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
                ->join('disciplines', 'disciplines.id', '=', 'study_plans_has_disciplines.disciplines_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'study_plans_has_disciplines.disciplines_id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })

                ->where('users.id', $id)
                ->where('study_plans_has_disciplines.years', '<=', $dadosImportUser->ano_curricular)

                // ->orderBy('study_plans_has_disciplines.years', 'ASC')
                ->orderBy('study_plans_has_disciplines.disciplines_id')

                ->get();


            // dd($disciplines);



            $avaliationsType = DB::table('tipo_avaliacaos')
                ->select('tipo_avaliacaos.id as id', 'tipo_avaliacaos.nome as name')
                ->get();

            //verificar se o estudante é transferido
            $state = UserState::whereUserId($id)->get();
            //caso o estudante for transferido carregar instituicao de origem
            $home_institution = TranferredStudent::whereUserId($id)->get();

            $data = [
                'disciplines' => $disciplines,
                'studentInfo' => $studentInfo,
                'personalName' => $personalName,
                'avaliationsType' => $avaliationsType,
                'matriculationCode' => $matriculationCode,
                'grades' => $grades,
                'getDisciplinesMatriculed' => $getDisciplinesMatriculed,
                'state' => $state,
                'home_institution' => $home_institution,
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento
            ];

            return view("Avaliations::academic-path.add-grade-students")->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            dd($e->getMessage());
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //return $request;
        try {

            DB::transaction(function () use ($request) {
                $data = [
                    'discipline_id' => $request->get('discipline_id'),
                    'grade' => $request->get('grade'),
                    'lective_year' => $request->get('lective_year'),
                    //'tipo_avaliacaos_id' => $request->get('avaliations_type')
                ];

                $userHaveState = UserState::whereUserId($request->get('user_id'))->get();

                if (!$userHaveState->isEmpty() && $userHaveState[0]->state_id == 14) {
                    for ($i = 0; $i < count($data['grade']); $i++) {
                        GradePath::updateOrCreate(
                            [
                                'user_id' => $request->get('user_id'),
                                'discipline_id' => $data['discipline_id'][$i],
                            ],
                            [
                                'lective_year' => $data['lective_year'][$i] ?: 2020,
                                'grade' => $data['grade'][$i] ?: 0,
                                'type' => "tranferido"
                            ]
                        );

                        TranferredStudent::updateOrCreate(['user_id' => $request->get('user_id')], ['home_institution' => $request->get('home_institution')]);

                        $article = Article::findOrFail(39);

                        //criar emolumento de Pedido de Equivalência Por Disciplina
                        $articleRequest = new ArticleRequest([
                            'user_id' => $request->get('user_id'),
                            'article_id' => $article->id,
                            'year' => null,
                            'month' => null,
                            'base_value' => $article->base_value,
                            'meta' => " "
                        ]);

                        $articleRequest->save();

                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base'
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    }
                } else {

                    for ($i = 0; $i < count($data['grade']); $i++) {
                        GradePath::updateOrCreate(
                            [
                                'user_id' => $request->get('user_id'),
                                'discipline_id' => $data['discipline_id'][$i],
                            ],
                            [
                                'lective_year' => $data['lective_year'][$i] ?: 2020,
                                'grade' => $data['grade'][$i] ?: 0
                            ]
                        );
                    }
                }

                //Actualizar os criadores do registo
                $this->user_publish_grade($request->get('user_id'));
            });



            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function user_publish_grade($id_student)
    {
        $userPublishGrade = DB::table('user_publish_grade_Transition')
            ->where('id_student', $id_student)->first();

        if ($userPublishGrade) {
            DB::table('user_publish_grade_Transition')
                ->where('id_student', $userPublishGrade->id_student)
                ->update([
                    'updated_by' => auth()->user()->id,
                    'updated_at' => Carbon::now(),
                ]);
        } else {
            // Criar novo registro
            DB::table('user_publish_grade_Transition')->insert([
                'id_student' => $id_student,
                'created_by' => auth()->user()->id,
                'created_at' => Carbon::now(),
                'updated_by' => auth()->user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$id = 3800;

        $documentoCode_documento = 1;
        $titulo_documento = "Percurso Académico";
        $documentoGerado_documento = "Documento gerado a";
        $institution = Institution::latest();
        $studentInfo = User::where('id', $id)
            ->with([
                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                        'groups'
                    ]);
                },
                'courses' => function ($q) {
                    $q->with('currentTranslation');
                },
                'matriculation'
            ])
            ->firstOrFail();

        $parameterNome = $studentInfo->parameters->where('code', 'nome')->first();
        $personalName = $parameterNome ? $parameterNome->pivot->value : '';

        $parameterMatriculation = $studentInfo->parameters->where('code', 'n_mecanografico')->first();
        $matriculationCode = $parameterMatriculation ? $parameterMatriculation->pivot->value : '';


        $disciplines = DB::table('new_old_grades')
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'new_old_grades.discipline_id')
            ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->select([
                'new_old_grades.lective_year as lective_year',
                'new_old_grades.user_id as user_id',
                'study_plans_has_disciplines.years as year',
                'new_old_grades.discipline_id as discipline_id',
                'new_old_grades.lective_year as lective_year',
                'disciplines.code as code',
                'dcp.display_name as name',
                'new_old_grades.grade as grade'
            ])
            ->where('new_old_grades.user_id', $id)
            ->orderBy('year')
            ->get();

        $data = [
            'studentInfo' => $studentInfo,
            'personalName' => $personalName,
            'disciplines' => $disciplines,
            'matriculationCode' => $matriculationCode,

            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento
        ];


        return view("Avaliations::academic-path.curricular-path")->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function studentWithFinalCourse()
    {
        return view("Avaliations::academic-path.add-final-course-grade");
    }
    public function storeFinalCourse(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                GradePath::updateOrCreate(
                    [
                        'user_id' => $request->get('student_id'),
                        'discipline_id' => $request->get('discipline_id'),
                    ],
                    [
                        'lective_year' => $request->get('lective_year') ?: 2020,
                        'grade' => $request->get('grade') ?: 0,
                        'tfc_trabalho' => $request->get('trabalho') ?: 0,
                        'tfc_defesa' => $request->get('defesa') ?: 0
                    ]
                );
            });
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return redirect()->route('old_student.finalGrade');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function getDisciplinesFinalCourseByStudent()
    {
        $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $students = Matriculation::join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
            ->join('users', 'matriculations.user_id', '=', 'users.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select('users.id as id', 'u_p.value as name', 'u_p0.value as n_mecanografico', 'users.email as email')
            ->whereIn('matriculation_disciplines.discipline_id', $disciplines)
            ->get();
        return (DataTables::of($students)
            ->addColumn('grade', function ($item) {
                return view('Avaliations::academic-path.datatables.grade')->with('item', $item);
            })
            ->addIndexColumn()
            ->rawColumns(['grade'])
            ->make('true'));
    }

    public function callViewFinalGrades($id)
    {
        $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $student = Matriculation::join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
            ->join('users', 'matriculations.user_id', '=', 'users.id')
            ->join('disciplines', 'matriculation_disciplines.discipline_id', '=', 'disciplines.id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'matriculation_disciplines.discipline_id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            /*->leftJoin('new_old_grades', function($join) use ($disciplines){
                                $join->on('new_old_grades.user_id','=', 'users.id')
                                    ->whereIn('new_old_grades.discipline_id', $disciplines);
                            })*/
            ->select('users.id as id', 'u_p.value as name', 'u_p0.value as n_mecanografico', 'users.email as email', 'disciplines.id as discipline_id', 'dcp.display_name as display_name')
            ->whereIn('matriculation_disciplines.discipline_id', $disciplines)
            ->where('users.id', $id)
            ->firstOrFail();

        $grades = GradePath::where('user_id', $id)
            ->whereIn('discipline_id', $disciplines)
            ->get();



        return view('Avaliations::academic-path.store-final-course-grade', compact('student', 'grades'));
    }

    public function createPastStudent()
    {
        return view("Avaliations::academic-path.add-grade-past-students");
    }
    public function studentsNotMatriculed()
    {
        $students = User::whereHas('roles', function ($q) {
            $q->whereIn('id', [6]);
        })
            ->whereHas('courses')
            ->doesntHave('matriculation')
            ->with(['parameters' => function ($q) {
                $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->get()->map(function ($student) {
                $displayName = $this->formatUserName($student);
                return ['id' => $student->id, 'display_name' => $displayName];
            });
        return $students;
    }

    public function getDisciplinesByCourse($student_id)
    {
        //todas as disciplinas excepto a exame de admissão
        $student = User::whereId($student_id)
            ->with('courses')
            ->first();

        $disciplines = Discipline::leftJoin('disciplines_translations as dcp', function ($join) {
            $join->on('dcp.discipline_id', '=', 'disciplines.id');
            $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dcp.active', '=', DB::raw(true));
        })
            //->leftJoin('new_old_grades', 'new_old_grades.discipline_id', '=', 'disciplines.id')
            ->select('disciplines.id as id', 'disciplines.code as code', 'dcp.display_name as display_name')
            ->where('disciplines.courses_id', $student->courses[0]->id)
            ->where('dcp.display_name', '!=', 'Exame de admissão')
            //->where('new_old_grades.user_id', $student->id)
            ->get();
        return $disciplines;
    }
    public function storePastStudent(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = [
                    'discipline_id' => $request->get('discipline_id'),
                    'positiva' => $request->get('positiva'),
                    'negativa' => $request->get('negativa'),
                    'lective_year' => $request->get('lective_year')
                ];
                for ($i = 0; $i < count($data['positiva']); $i++) {
                    GradePath::updateOrCreate(
                        [
                            'user_id' => $request->get('students'),
                            'discipline_id' => $data['discipline_id'][$i],
                        ],
                        [
                            'lective_year' => $data['lective_year'][$i],
                            'grade' => $data['positiva'][$i] ?: 0
                        ]
                    );
                }

                for ($i = 0; $i < count($data['negativa']); $i++) {
                    GradePath::updateOrCreate(
                        [
                            'user_id' => $request->get('students'),
                            'discipline_id' => $data['discipline_id'][$i],
                        ],
                        [
                            'lective_year' => $data['lective_year'][$i],
                            'grade' => $data['negativa'][$i] ?: 0
                        ]
                    );
                }
            });
            Toastr::success(__('Registo inserido com sucesso'), __('toastr.success'));
            return redirect()->route('old_student.pastStudent');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
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



    public function print($id)
    {

        try {

            $documentoCode_documento = 1;
            $titulo_documento = "Percurso Académico";
            $documentoGerado_documento = "Documento gerado a";
            $institution = Institution::latest();

            $studentInfo = User::where('id', $id)
                ->with([
                    'parameters' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'groups'
                        ]);
                    },
                    'courses' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'matriculation'
                ])
                ->firstOrFail();

            $parameterNome = $studentInfo->parameters->where('code', 'nome')->first();
            $personalName = $parameterNome ? $parameterNome->pivot->value : '';

            $parameterMatriculation = $studentInfo->parameters->where('code', 'n_mecanografico')->first();
            $matriculationCode = $parameterMatriculation ? $parameterMatriculation->pivot->value : '';

            $userFoto = $studentInfo->parameters->where('code', 'fotografia')->first();
            /*
                $userFoto = User::whereId($id)->leftJoin('user_parameters as u_p', function ($join) {
                                    $join->on('users.id', '=', 'u_p.users_id')
                                            ->where('u_p.parameters_id', 25);
                                })->select('u_p.value as foto')
                                ->first();
                */

            $disciplines = DB::table('new_old_grades')
                ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.disciplines_id', '=', 'new_old_grades.discipline_id')
                ->join('disciplines', 'disciplines.id', '=', 'new_old_grades.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->select([
                    'new_old_grades.lective_year as lective_year',
                    'new_old_grades.user_id as user_id',
                    'study_plans_has_disciplines.years as year',
                    'new_old_grades.discipline_id as discipline_id',
                    'new_old_grades.lective_year as lective_year',
                    'disciplines.code as code',
                    'dcp.display_name as name',
                    'new_old_grades.grade as grade'
                ])
                ->where('new_old_grades.user_id', $id)
                ->orderBy('year')
                ->get();

            $data = [
                'studentInfo' => $studentInfo,
                'personalName' => $personalName,
                'disciplines' => $disciplines,
                'matriculationCode' => $matriculationCode,
                'userFoto' => $userFoto,

                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento
            ];

            return view("Avaliations::academic-path.pdf")->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function filterCode($list, $abbr)
    {
        $array = [];
        foreach ($list as $item) {
            if (str_contains($item->code, $abbr) || str_contains($item->code, "CEE")) {
                $array[] = $item;
            }
        }
        return $array;
    }

    public function studentAcademicPath($studentId)
    {

        


        $stundent_v = AvaliacaoAlunoHistoricoController::getRoles(auth()->user()->id, 6);

        if (($stundent_v == 1)) {
            $studentId = auth()->user()->id;
        }


        $documentoCode_documento = 1;
        $titulo_documento = "Percurso Académico";
        $documentoGerado_documento = "Documento gerado a";


        // $outDisciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];
        
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
                    ->where('u_p2.parameters_id', 14);
                    
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 24);
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
                'u_p2.value as bi',
                'u_p3.value as dad',
                'u_p4.value as mam',
                'ct.display_name as course',
                'matriculations.course_year as year',
                'courses.id as course_id',
                'users.email as email'
            ])
            ->firstOrFail();

        //trazer todas as disciplinas matriculadas

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
            ->where('matriculations.user_id', $studentId)
            ->select([
                'disciplines.id as id',
                'disciplines.code as code',
                // 'disciplines.uc as uc',
                'dcp.display_name as name',
                'dat.display_name as area',
                //'course_year as course_year',
                'study_plans_has_disciplines.years as course_year',
                'discipline_areas.id as area_id'
            ])
            // ->whereNotIn('matriculation_disciplines.discipline_id', $outDisciplines)
            ->orderBy('study_plans_has_disciplines.disciplines_id')
            ->distinct('id')
            ->get();

        //$disciplines = $disciplines->unique('id')->values()->all();

        $last_discipline = count($disciplines) - 1;
        $student_course = null;
        $student_abbr = "";
        //return substr($disciplines[$last_discipline]->code, 0, 3);

        if (substr($disciplines[$last_discipline]->code, 0, 3) == "GEE") {
            $student_course = "Gestão Empresarial";
            $student_abbr = "GEE";
        }
        if (substr($disciplines[$last_discipline]->code, 0, 3) == "COA") {
            $student_course = "Contabilidade e Auditoria";
            $student_abbr = "COA";
        }
        if (substr($disciplines[$last_discipline]->code, 0, 3) == "ECO") {
            $student_course = "Economia";
            $student_abbr = "ECO";
        }

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
            // ->whereNotIn('disciplines.id', $outDisciplines)
            ->select([
                'disciplines.id as id',
                'disciplines.code as code',
                // 'disciplines.uc as uc',
                'dcp.display_name as name',
                'dat.display_name as area',
                'study_plans_has_disciplines.years as course_year',
                'discipline_areas.id as area_id'
            ])
             ->orderBy('study_plans_has_disciplines.disciplines_id')
            ->distinct()
            ->get();

        if (count($oldDisciplines) < 1) {
            Toastr::warning(__("O estudante não possui notas publicadas!"), __('toastr.warning'));
            return redirect()->back();
        }




        //$disciplines = $student_abbr != "" ? $this->filterCode($oldDisciplines, $student_abbr) : $oldDisciplines;
        $disciplines =  $oldDisciplines;
        // $oldDisciplines->isEmpty()
        //             ? $disciplines = $disciplines
        //             : $disciplines = $oldDisciplines->merge($disciplines);

        // $disciplines->sortBy('course_year')->values()->all();

        //$disciplines->unique('id')->values()->all();

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
            // ->whereNotIn('discipline_id', $outDisciplines)
            ->orderBy('lective_year', 'ASC')
            ->distinct()
            ->get()
            ->groupBy('lective_year');

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
            //as notas trazer por where between de datas nas notas

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


        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

            ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
            ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
            ->where('disciplines.courses_id', $studentInfo->course_id)
            ->distinct(['id_disciplina', 'hora'])
            ->get();

        $cargaHoraria = $cargaHoraria->unique('id_disciplina')
            ->values()
            ->all();
            
        $recibo = null;
         $requerimento = null;
         
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
            ->where('rt.active',1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario', 'usuario.email as email_usuario', 'usuario.name as name',
                'u_p0.value as nome_completo', 'ga.nome as grau_academico', 'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $dataActual = $this->dataActual();


        $data = [
            'cargaHoraria' => $cargaHoraria,
            'studentInfo' => $studentInfo,
            'countDisciplines' => $countDisciplines,
            'disciplines' => $disciplines,
            'state' => $state,
            'var' => $var,
            'studyPlanEditions' => $studyPlanEditions,
            'oldGrades' => $oldGrades,
            'grades' => $grades,
            'countAllDisciplines' => $countAllDisciplines,
            'disciplinesAreas' => $disciplinesAreas,
            'finalDisciplineGrade' => $finalDisciplineGrade,
            'userFoto' => $userFoto,
            'student_course' => $student_course,
            'recibo' => $recibo,
            'requerimento' => $requerimento,
            'direitor' => $direitor,
            'dataActual' => $dataActual

        ];


       

        /*---------------------------------------------------------------------*/
        //--------------------------------------------------------------------
        $institution = Institution::latest()->first();

        $chefe_gab_termos = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
            })
            ->select('name_full.value as full_name')
            ->where('u.id', $institution->gabinete_termos)
            ->first();

        $chefe_daac = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
            })
            ->select('name_full.value as full_name')
            ->where('u.id', $institution->daac)
            ->first();


        $vice_director_academica = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
        })->select('name_full.value as full_name')
          ->where('u.id', $institution->vice_director_academica)
          ->first();

        $pdf = PDF::loadView("Avaliations::academic-path.academic_path_new", compact(
            'cargaHoraria',
            'userFoto',
            'studentInfo',
            'countDisciplines',
            'disciplines',
            'state',
            'var',
            'outDisciplines',
            'studyPlanEditions',
            'oldGrades',
            'grades',
            'countAllDisciplines',
            'disciplinesAreas',
            'finalDisciplineGrade',
            'institution',
            'chefe_gab_termos',
            'chefe_daac',
            'documentoCode_documento',
            'titulo_documento',
            'documentoGerado_documento',
            'student_course',
            'vice_director_academica',
             'recibo',
            'requerimento',
            'direitor',
            'dataActual'

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

        // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
      
        $nomeDocument="PA_$studentInfo->number.pdf";
        return $pdf->stream($nomeDocument);
    }
    
    
        public function studentAcademicPathImported(Request $request, $studentId)
    {
    
       
         $recibo = null;
         $requerimento = null;
         
         $requerimento_id = $request->requerimento_id ?? null;
        if(isset($requerimento_id)){

        $requerimento = DB::table('requerimento')
                            ->where('id', $requerimento_id)
                            ->first();
                       
         $recibo = $this->referenceGetRecibo($requerimento->article_id);
        }
        
      
          

        $stundent_v = AvaliacaoAlunoHistoricoController::getRoles(auth()->user()->id, 6);
        
        if (($stundent_v == 1)) {
            $studentId = auth()->user()->id;
        }
        
        
        $documentoCode_documento = 1;
        $titulo_documento = "Percurso Académico";
        $documentoGerado_documento = "Documento gerado a";
        
        
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
                    ->where('u_p2.parameters_id', 14);
                    
            })
            ->leftJoin('user_parameters as u_p3', function ($join) {
                $join->on('users.id', '=', 'u_p3.users_id')
                    ->where('u_p3.parameters_id', 23);
            })
            ->leftJoin('user_parameters as u_p4', function ($join) {
                $join->on('users.id', '=', 'u_p4.users_id')
                    ->where('u_p4.parameters_id', 24);
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
                'u_p2.value as bi',
                'u_p3.value as dad',
                'u_p4.value as mam',
                'ct.display_name as course',
                // 'matriculations.course_year as year',
                'courses.id as course_id',
                'users.email as email'
            ])
            ->distinct()
            ->first();
        
                // dd($studentInfo);
                

        //trazer todas as disciplinas matriculadas

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
            ->where('matriculations.user_id', $studentId)
            ->select([
                'disciplines.id as id',
                'disciplines.code as code',
                'disciplines.uc as uc',
                'dcp.display_name as name',
                'dat.display_name as area',
                //'course_year as course_year',
                'study_plans_has_disciplines.years as course_year',
                'discipline_areas.id as area_id'
            ])
            // ->whereNotIn('matriculation_disciplines.discipline_id', $outDisciplines)
             ->orderBy('study_plans_has_disciplines.disciplines_id')
            ->distinct('id')
            ->get();

        //$disciplines = $disciplines->unique('id')->values()->all();

        $last_discipline = count($disciplines) - 1;
        $student_course = null;
        $student_abbr = "";
        //return substr($disciplines[$last_discipline]->code, 0, 3);
           
        // if (substr($disciplines[$last_discipline]->code, 0, 3) == "GEE") {
        //     $student_course = "Gestão Empresarial";
        //     $student_abbr = "GEE";
        // }
        // if (substr($disciplines[$last_discipline]->code, 0, 3) == "COA") {
        //     $student_course = "Contabilidade e Auditoria";
        //     $student_abbr = "COA";
        // }
        // if (substr($disciplines[$last_discipline]->code, 0, 3) == "ECO") {
        //     $student_course = "Economia";
        //     $student_abbr = "ECO";
        // }

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
           
            ->select([
                'disciplines.id as id',
                'disciplines.code as code',
                'disciplines.uc as uc',
                'dcp.display_name as name',
                'dat.display_name as area',
                'study_plans_has_disciplines.years as course_year',
                'discipline_areas.id as area_id'
            ])
            ->orderBy('study_plans_has_disciplines.disciplines_id')
            ->distinct()
            ->get();
            
        if (count($oldDisciplines) < 1) {
            Toastr::warning(__("O estudante não possui notas publicadas!"), __('toastr.warning'));
            return redirect()->back();
        }

        $disciplines =  $oldDisciplines;
        
        $disciplines = $this->ordena_plano($disciplines);
    

        $countDisciplines = count($disciplines);

        $countAllDisciplines = StudyPlan::where('courses_id', $studentInfo->course_id)
            ->join('study_plans_has_disciplines', 'study_plans_has_disciplines.study_plans_id', '=', 'study_plans.id')
            ->count();

        $state = DB::table('users_states')
            ->join('states', 'states.id', '=', 'users_states.state_id')
            ->where('user_id', $studentId)
            ->first();

        $state = $state->name ?? 'N/A';

       $oldGrades = DB::table('new_old_grades')
            ->where('user_id', $studentId)
            // ->whereNotIn('discipline_id', $outDisciplines)
            ->orderBy('lective_year', 'ASC')
            ->distinct()
            ->get()
            ->groupBy('lective_year');

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
            //as notas trazer por where between de datas nas notas

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


        $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

            ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
            ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
            ->where('disciplines.courses_id', $studentInfo->course_id)
            ->distinct(['id_disciplina', 'hora'])
            ->get();

        $cargaHoraria = $cargaHoraria->unique('id_disciplina')
            ->values()
            ->all();
            
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
            ->where('rt.active',1)
            ->whereNull('usuario.deleted_at')
            ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
            ->select([
                'usuario.id as id_usuario', 'usuario.email as email_usuario', 'usuario.name as name',
                'u_p0.value as nome_completo', 'ga.nome as grau_academico', 'cp.nome as categoria_profissional',
                'rt.display_name as cargo'
            ])
            ->orderBy('usuario.name')
            ->groupBy('id_usuario')
            ->first();

        $dataActual = $this->dataActual();
  
       $data = [
            'cargaHoraria' => $cargaHoraria,
            'studentInfo' => $studentInfo,
            'countDisciplines' => $countDisciplines,
            'disciplines' => $disciplines,
            'state' => $state,
            'var' => $var,
            'studyPlanEditions' => $studyPlanEditions,
            'oldGrades' => $oldGrades,
            'grades' => $grades,
            'countAllDisciplines' => $countAllDisciplines,
            'disciplinesAreas' => $disciplinesAreas,
            'finalDisciplineGrade' => $finalDisciplineGrade,
            'userFoto' => $userFoto,
            'student_course' => $student_course,
            'recibo' => $recibo,
            'requerimento' => $requerimento,
            'direitor' => $direitor,
            'dataActual' => $dataActual

        ];






        //return view("Avaliations::academic-path.academic_path")->with($data);
        $institution = Institution::latest()->first();

        $chefe_gab_termos = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
            })
            ->select('name_full.value as full_name')
            ->where('u.id', $institution->gabinete_termos)
            ->first();

        $chefe_daac = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
            })
            ->select('name_full.value as full_name')
            ->where('u.id', $institution->daac)
            ->first();


        $vice_director_academica = DB::table('users as u')
            ->leftJoin('user_parameters as name_full', function ($q) {
                $q->on('name_full.users_id', '=', 'u.id')
                    ->where('name_full.parameters_id', 1);
            })
            ->select('name_full.value as full_name')
            ->where('u.id', $institution->vice_director_academica)
            ->first();
            
         
        // return $pdf = View("Avaliations::academic-path.academic_path", compact(
        $pdf = PDF::loadView("Avaliations::academic-path.academic_path_new", compact(
            'cargaHoraria',
            'userFoto',
            'studentInfo',
            'countDisciplines',
            'disciplines',
            'state',
            'var',
            'outDisciplines',
            'studyPlanEditions',
            'oldGrades',
            'grades',
            'countAllDisciplines',
            'disciplinesAreas',
            'finalDisciplineGrade',
            'institution',
            'chefe_gab_termos',
            'chefe_daac',
            'documentoCode_documento',
            'titulo_documento',
            'documentoGerado_documento',
            'student_course',
            'vice_director_academica',
            'recibo',
            'requerimento',
            'direitor',
            'dataActual'

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
   
        if(isset($requerimento)){
     
      $string = str_replace('/', '_', $requerimento->code);
     $nomeDocument = 'PA_' . $string;
        }
        else {
        // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
        // $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        // $pdf->setOption('footer-html', $footer_html);
          
       
          $nomeDocument='PA_' . $studentInfo->number;
        }
      
        return $pdf->stream($nomeDocument . '.pdf');
    }
    
            private function dataActual(){
            $m=date("m");
            $mes =array( "01"=>"Janeiro","02"=>"Fevereiro",
                         "03"=>"Março","04"=>"Abril",
                         "05"=>"Maio", "06"=>"Junho",
                         "07"=>"Julho","08"=>"Agosto", 
                         "09"=>"Setembro", "10"=>"Outubro",
                         "11"=>"Novembro", "12"=>"Dezembro"
                     );
               $data=date("d")." de ".$mes[$m]." de ".date("Y");
               return $data;
          }

      public function referenceGetRecibo($article)
        {
            
            
        // Pegando os dados completos da referência

        $transacao=DB::table('transaction_info as tinfo')
        ->leftJoin('transaction_receipts as tr','tr.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('transaction_article_requests as tar','tar.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('banks as bk','bk.id',"=","tinfo.bank_id")
        ->leftJoin('article_requests as ar','ar.id',"=","tar.article_request_id")
        ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
        ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
        ->leftJoin('articles as art','art.id',"=","ar.article_id")
        ->where('ar.id',$article)
        ->where('up.parameters_id',1)
        ->where('at.active',1)
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
        
        if(!isset($transacao)){
            return null;
        }
         
        
        $usuario = DB::table('users')->select(['name as nome'])->where('id',auth()->user()->id)->get();

        // $json['success'] = $transacao; 
      
        $n_recibo = $transacao[0]->recibo; 
        $rt = explode('-',explode('/',$n_recibo )[3]);
      

        $recibo = $rt[1]."-".explode('.',$rt[2])[0];

        // $json=[
        //     // 'info'=>$transacao[0],
        //     // 'nome'=>$usuario[0]->nome,
        //     'recibo'=>$recibo 
        // ];
       
        return $recibo;

    }

public function ordena_plano($plano){

        for($i=0; $i < count($plano); $i++) {

            for($j=$i+1; $j < count($plano); $j++) {

                
            $min = $i;
            // pegar os códigos dos objecto
            $objA = $plano[$i]->code;
            $objB = $plano[$j]->code;

            // pegar a substring
            
            $subA = substr($objA, 0, 3); 
            $subB = substr($objB, 0, 3);
            
          
            if (preg_match('/\d/', $subA)) {
               
                 $subA = substr($objA, 2);
                  
                }
            else{
               $subA = substr($objA, 3); 
            }
                
            if (preg_match('/\d/', $subB)) {
                
                 $subB = substr($objB, 2);
                } 
                else {
                 $subB = substr($objB, 3);
                }

            //verificar se a sub-string contém a letra A
            if(strpos($subA, 'A') !== false && strpos($subB, 'A') !== false) {

                // substituir o A por 0

                $subA = str_replace('A', '0', $subA);
                $subB = str_replace('A', '0', $subB);

                // convertendo em inteiros
                $subA = intval($subA);
                $subB = intval($subB);

                // comparando
                if($subB < $subA){
                   // Ordenar
                    $min = $j; 
                    
                }

                $aux = $plano[$min];
                $plano[$min] = $plano[$i];
                $plano[$i] = $aux;
                continue;
                
            } 
            else if(strpos($subA, 'A') !== false && strpos($subB, 'B') === false)
            {
                // substituir o A por 0

                $subA = str_replace('A', '0', $subA);

                // convertendo em inteiros
                $subA = intval($subA);
                $subB = intval($subB);

                // comparando
                if($subB < $subA){
                   // Ordenar
                    $min = $j; 
                    
                }

                $aux = $plano[$min];
                $plano[$min] = $plano[$i];
                $plano[$i] = $aux;
                continue;
            }
            else if(strpos($subB, 'A') !== false && strpos($subA, 'B') === false)
            {
                               // substituir o A por 0

                               $subB = str_replace('A', '0', $subB);

                               // convertendo em inteiros
                               $subB = intval($subB);
                               $subA = intval($subA);
                               // comparando
                               if($subB < $subA){
                                  // Ordenar
                                   $min = $j; 
                                   
                               }
               
                               $aux = $plano[$min];
                               $plano[$min] = $plano[$i];
                               $plano[$i] = $aux;
                               continue;
            }
            else if(strpos($subA, 'A') === false && strpos($subB, 'A') === false)
            {
                 // convertendo em inteiros
                 $subA = intval($subA);
                 $subB = intval($subB);
 
                 // comparando
                 if($subB < $subA){
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




    public function finalListWithGrades()
    {
        $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        //Retornar todos os matriculados com o ano acima do 1.
        $students = DB::table('matriculations')
            ->join('users', 'users.id', '=', 'matriculations.user_id')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->join('new_old_grades', 'users.id', '=', 'new_old_grades.user_id')
            ->select('u_p0.value as n_mecanografico', 'u_p.value as student', 'users.id as id', 'users.email as email', 'matriculations.code as code', 'users.name as name', 'matriculations.course_year as year', 'ct.display_name as course')
            ->whereIn('new_old_grades.discipline_id', $disciplines)
            ->where('grade', '>', 0)
            //->where('matriculations.course_year', '>', 1)
            ->get();


        return (DataTables::of($students)
            ->addColumn('grade', function ($item) {
                return view('Avaliations::academic-path.datatables.grade')->with('item', $item);
            })
            ->addIndexColumn()
            ->rawColumns(['grade'])
            ->make('true'));
    }

    public function finalList()
    {
        $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        $students = Matriculation::join('matriculation_disciplines', 'matriculation_disciplines.matriculation_id', '=', 'matriculations.id')
            ->join('users', 'matriculations.user_id', '=', 'users.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->select('users.id as id', 'u_p.value as name', 'u_p0.value as n_mecanografico', 'users.email as email')
            ->whereIn('matriculation_disciplines.discipline_id', $disciplines)
            ->get();
        return (DataTables::of($students)
            ->addColumn('grade', function ($item) {
                return view('Avaliations::academic-path.datatables.grade')->with('item', $item);
            })
            ->addIndexColumn()
            ->rawColumns(['grade'])
            ->make('true'));
    }

    public function finalListWithoutGrades()
    {
        $disciplines = [181, 551, 237, 637, 299, 527, 129, 395, 468, 619, 430, 229, 72, 565, 351];

        //Retornar todos os matriculados com o ano acima do 1.
        $students = DB::table('matriculations')
            ->join('users', 'users.id', '=', 'matriculations.user_id')
            ->join('user_courses', 'user_courses.users_id', '=', 'users.id')
            ->join('courses', 'courses.id', '=', 'user_courses.courses_id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'courses.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('user_parameters as u_p0', function ($join) {
                $join->on('users.id', '=', 'u_p0.users_id')
                    ->where('u_p0.parameters_id', 19);
            })
            ->join('new_old_grades', 'users.id', '=', 'new_old_grades.user_id')
            ->select('u_p0.value as n_mecanografico', 'u_p.value as student', 'users.id as id', 'users.email as email', 'matriculations.code as code', 'users.name as name', 'matriculations.course_year as year', 'ct.display_name as course')
            ->whereIn('new_old_grades.discipline_id', $disciplines)
            ->where('grade', 0)
            //->where('matriculations.course_year', '>', 1)
            ->whereNull('users.deleted_by')
            ->get();


        return (DataTables::of($students)
            ->addColumn('grade', function ($item) {
                return view('Avaliations::academic-path.datatables.grade')->with('item', $item);
            })
            ->addIndexColumn()
            ->rawColumns(['grade'])
            ->make('true'));
    }






















    //Metodo que gera PDF

    public function studentAcademicPercurso($studentId)
    {



        try {

            $institution = Institution::latest()->first();
            $documentoCode_documento = 1;
            $titulo_documento = "Percurso Académico";
            $documentoGerado_documento = "Documento gerado a";

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
                    'ct.display_name as course',
                    'matriculations.course_year as year',
                    'courses.id as course_id',
                    'users.email as email'
                ])
                ->firstOrFail();

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
                ->where('matriculations.user_id', $studentId)
                ->select([
                    'disciplines.id as id',
                    'disciplines.code as code',
                    'dcp.display_name as name',
                    'dat.display_name as area',
                    //'course_year as course_year',
                    'study_plans_has_disciplines.years as course_year',
                    'discipline_areas.id as area_id'
                ])
                ->whereNotIn('matriculation_disciplines.discipline_id', $outDisciplines)
                ->orderBy('course_year', 'ASC')
                ->distinct('id')
                ->get();

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
                ->whereNotIn('disciplines.id', $outDisciplines)
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
            // $oldDisciplines->isEmpty()
            //             ? $disciplines = $disciplines
            //             : $disciplines = $oldDisciplines->merge($disciplines);

            // $disciplines->sortBy('course_year')->values()->all();

            //$disciplines->unique('id')->values()->all();

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
                //as notas trazer por where between de datas nas notas

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


            $cargaHoraria = DB::table('study_plans_has_disciplines as dc')

                ->join('disciplines', 'disciplines.id', '=', 'dc.disciplines_id')
                ->select(['disciplines.id as id_disciplina', 'dc.total_hours as hora'])
                ->where('disciplines.courses_id', $studentInfo->course_id)
                ->distinct(['id_disciplina', 'hora'])
                ->get();

            $cargaHoraria = $cargaHoraria->unique('id_disciplina')
                ->values()
                ->all();


            $chefe_gab_termos = DB::table('users as u')
                ->leftJoin('user_parameters as name_full', function ($q) {
                    $q->on('name_full.users_id', '=', 'u.id')
                        ->where('name_full.parameters_id', 1);
                })
                ->select('name_full.value as full_name')
                ->where('u.id', $institution->gabinete_termos)
                ->first();

            $chefe_daac = DB::table('users as u')
                ->leftJoin('user_parameters as name_full', function ($q) {
                    $q->on('name_full.users_id', '=', 'u.id')
                        ->where('name_full.parameters_id', 1);
                })
                ->select('name_full.value as full_name')
                ->where('u.id', $institution->daac)
                ->first();


            $data = [
                'cargaHoraria' => $cargaHoraria,
                'studentInfo' => $studentInfo,
                'countDisciplines' => $countDisciplines,
                'disciplines' => $disciplines,
                'state' => $state,
                'var' => $var,
                'studyPlanEditions' => $studyPlanEditions,
                'oldGrades' => $oldGrades,
                'grades' => $grades,
                'countAllDisciplines' => $countAllDisciplines,
                'disciplinesAreas' => $disciplinesAreas,
                'finalDisciplineGrade' => $finalDisciplineGrade,
                'userFoto' => $userFoto,

                'institution' => $institution,
                'chefe_gab_termos' => $chefe_gab_termos,
                'chefe_daac' => $chefe_daac,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento
            ];


            /*
        */

            // return view("Avaliations::academic-path.academic_path")->with($data);



            $pdf = PDF::loadView("Avaliations::academic-path.academic_path", compact(
                'cargaHoraria',
                'userFoto',
                'studentInfo',
                'countDisciplines',
                'disciplines',
                'state',
                'var',
                'studyPlanEditions',
                'oldGrades',
                'grades',
                'countAllDisciplines',
                'disciplinesAreas',
                'finalDisciplineGrade',
                'institution',
                'chefe_gab_termos',
                'chefe_daac',
                'outDisciplines',
                'titulo_documento',
                'documentoCode_documento',
                'documentoGerado_documento'

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

            // $footer_html = view()->make('Reports::partials.enrollment-income-footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream('percurso_academico.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            dd($e->getMessage());
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
