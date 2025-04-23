<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\MatriculationRequest;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use Throwable;
use Toastr;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;

class MatriculatioPointController extends Controller
{
    public function index()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                        ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('Users::matriculations.index', compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create()
    {

        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                        ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;


            $data = [
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected,
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
                'users' => $this->studentsWithCourseWithoutMatriculationSelectList()
            ];

            return view('Users::matriculations.matriculation-point')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(Request $request)
    {
        // return $request;
        $lectiveYears = LectiveYear::whereId($request->lective_years)->first();
        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->get('user'));

            $estadoInscricao = DB::table('article_requests')->where([
                ['user_id','=', $user->id],
                ['article_id','=','6']
            ])->first();


            //TODO: TRATAR QUANDO O ARRAY VIER VAZIO;
            if ($user->hasRole('candidado-a-estudante')) {
                if ($estadoInscricao->status == "pending" || $estadoInscricao->status == "partial") {
                    //Error message (Pagamento de Inscrição de Accesso Pendente ou Parcial)
                    Toastr::error(__('Pagamento em atraso'), __('toastr.error'));
                    return redirect()->route('matriculations.index');
                }
            }

            $year = new Carbon($lectiveYears->start_date);

            $nextCodeNumber = $year->year . $user->id;
            //$latestsMatriculation = Matriculation::latest()->first();
            /*if ($latestsMatriculation && Carbon::parse($latestsMatriculation->created_at)->year === Carbon::now()->year) {
                $nextCodeNumber = ((int)ltrim($latestsMatriculation->code, 'MT') + 1);
            }*/
            $nextCode = 'MT' . $nextCodeNumber;

            $maxSelectedYear = (int)collect($request->get('years'))->max();
            $matriculation = Matriculation::create([
                'user_id' => $user->id,
                'code' => $nextCode,
                'course_year' => $maxSelectedYear,
                'created_at' => $lectiveYears->start_date
            ]);

            $estadoAntigo = $user->hasRole('candidado-a-estudante') ? 1 : 2;

            // role candidate to student
            if ($user->hasRole('candidado-a-estudante')) {
                $user->syncRoles('student');

                $currentNumber = $user->parameters()->where('parameters.id', 19)->first();
                $courseNumericCode = $user->courses()->first()->numeric_code;
                $newNumber = Carbon::now()->format('y') . $courseNumericCode . ltrim($nextCodeNumber, '20');

                if ($currentNumber) {
                    $currentNumber->pivot->value = $newNumber;
                    $currentNumber->pivot->save();
                } else {
                    $user_n_mecanografico[] = [
                        'parameters_id' => 19,
                        'created_by' => auth()->user()->id ?? 0,
                        'parameter_group_id' => 1,
                        'value' => $newNumber
                    ];

                    $user->parameters()->attach($user_n_mecanografico);
                }
            }

            // disciplines
            $userDisciplines = [];
            $yearsWithDisciplines = [];
            $allDisciplinesInCurricularYear = [];
            $allDisciplinesOffCurricularYear = [];
            $disciplineByYear = $request->get('disciplines');
            foreach ($disciplineByYear as $year => $disciplines) {
                if (is_array($disciplines) && count($disciplines)) {
                    $yearsWithDisciplines[] = (int)$year;
                    foreach ($disciplines as $d) {
                        $userDisciplines[$d] = ['exam_only' => false];
                        if ((int)$year !== $maxSelectedYear) {
                            $allDisciplinesOffCurricularYear[$d] = false;
                        } else {
                            $allDisciplinesInCurricularYear[$d] = false;
                        }
                    }
                }
            }

            // exam only disciplines
            $examOnlyDisciplinesByYear = $request->get('disciplines_exam_only');
            if (is_array($examOnlyDisciplinesByYear)) {
                foreach ($examOnlyDisciplinesByYear as $year => $disciplines) {
                    if (is_array($disciplines) && count($disciplines)) {
                        foreach ($disciplines as $d) {
                            $userDisciplines[$d]['exam_only'] = true;
                            $allDisciplinesOffCurricularYear[$d] = true;
                        }
                    }
                }
            }

            // classes
            $userClasses = [];
            $yearsWithClasses = [];
            foreach ($request->get('classes') as $year => $class) {
                if ($class) {
                    $yearsWithClasses[] = $year;
                    $userClasses[] = $class;
                }
            }
            //Obter o total de matriculas feitas em uma determinada turma
            $get_matriculation_class_total = DB::table('matriculation_classes')
                                        ->where('class_id', $userClasses)
                                        ->count();

            //Obter o total de vagas de uma determinada turma
            $get_class_vacancies = Classes::whereId($userClasses)->firstOrFail();

            //Avaliar se o total de matriculas feitas em uma determinada turma + 1 (mais a proxima a ser feita) for menor ou igual ao total de vagas
            //if ($get_matriculation_class_total + 1 <= $get_class_vacancies->vacancies) {
                //return "Pode Efetuar Matrícula";


                if ($yearsWithDisciplines !== $yearsWithClasses) {
                    return redirect()->back()->withErrors(['Definição de turmas e/ou disciplinas inválida.'])->withInput();
                }

                if (!empty($userDisciplines)) {
                    $matriculation->disciplines()->sync($userDisciplines);
                }

                if (!empty($userClasses)) {
                    $matriculation->classes()->sync($userClasses);
                }

                $articleRequets = [];
                // // Confirmação de matrícula (id: 8)
                // $r1 = createAutomaticArticleRequest($user->id, $estadoAntigo == 1 ? 7 : 8, null, null);
                // if (!$r1) {
                //     throw new Exception('Could not create automatic [Confirmação de matrícula (id: 8)] article request payment for student (id: ' . $user->id . ') matriculation');
                // }
                // $articleRequets[$r1]['updatable'] = false;

                // // Emissão de Cartão de Estudante (id: 31)
                // $r2 = createAutomaticArticleRequest($user->id, 31, null, null);
                // if (!$r2) {
                //     throw new Exception('Could not create automatic [Emissão de Cartão de Estudante (id: 31)] article request payment for student (id: ' . $user->id . ') matriculation');
                // }
                // $articleRequets[$r2]['updatable'] = false;

                // Pagamento de Propina
                $articlePropinaId = null;
                $courseID = $user->courses()->first()->id;

                $courseData = Course::where('id', $courseID)
                ->with([
                    'studyPlans' => function ($q) use ($maxSelectedYear) {
                        $q->with([
                            'study_plans_has_disciplines' => function ($q) use ($maxSelectedYear) {
                                $q->where('years', $maxSelectedYear);
                                $q->with('discipline');
                            }
                        ]);
                    },
                ])
                ->first();

                $curricularYearAllDisciplinesCount = $courseData ? $courseData->studyPlans->study_plans_has_disciplines->count() : 0;
                $curricularYearSelectedDisciplinesCount = count($allDisciplinesInCurricularYear);

                $currentYearToValidate = $maxSelectedYear;
                if ($courseData && $courseID === 25 && $currentYearToValidate > 2) {
                    $specializationCode = null;

                    $courseData = Course::where('id', $courseID)
                    ->with([
                        'studyPlans' => function ($q) use ($maxSelectedYear) {
                            $q->with([
                                'study_plans_has_disciplines' => function ($q) {
                                    $q->with('discipline');
                                }
                            ]);
                        },
                    ])
                    ->first();

                    while ($currentYearToValidate > 2) {
                        if (collect($request->get('years'))->contains((string)$currentYearToValidate)) {
                            $disciplineGlobalCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                            $disciplineSelectedCount = [
                            'GEE' => 0,
                            'COA' => 0,
                            'ECO' => 0,
                        ];
                            $specializationCodeForTheYear = null;

                            foreach ($courseData->studyPlans->study_plans_has_disciplines as $spDiscipline) {
                                if ($spDiscipline->years === $currentYearToValidate) {
                                    $code = substr($spDiscipline->discipline->code, 0, 3);
                                    if (isset($disciplineGlobalCount[$code])) {
                                        ++$disciplineGlobalCount[$code];
                                        if (in_array((string)$spDiscipline->discipline->id, $disciplineByYear[$currentYearToValidate], true)) {
                                            ++$disciplineSelectedCount[$code];
                                        }
                                    }
                                }
                            }

                            $courseBranchesWithSelectedDisciplines = array_filter($disciplineSelectedCount, function ($item) {
                                return $item;
                            });
                            $specializationCodeForTheYear = array_key_first($courseBranchesWithSelectedDisciplines);

                            if ($currentYearToValidate === $maxSelectedYear) {
                                $specializationCode = $specializationCodeForTheYear;
                                $curricularYearAllDisciplinesCount = $disciplineGlobalCount[$specializationCode];
                            }

                            $differentSpecializationsBetweenYears = $specializationCodeForTheYear !== $specializationCode;
                            $notOneSpecializationSelected = count($courseBranchesWithSelectedDisciplines) !== 1;

                            if ($notOneSpecializationSelected || $differentSpecializationsBetweenYears) {
                                return redirect()->back()->withErrors(['Disciplinas de especialidades selecionadas de forma inválida.'])->withInput();
                            }
                        }

                        --$currentYearToValidate;
                    }

                    switch ($specializationCode) {
                    case 'GEE':
                        $articlePropinaId = 47;
                        break;
                    case 'COA':
                        $articlePropinaId = 45;
                        break;
                    case 'ECO':
                        $articlePropinaId = 46;
                        break;
                }
                }

                // if ($curricularYearAllDisciplinesCount === $curricularYearSelectedDisciplinesCount) {
                //     if (!$articlePropinaId) {
                //         $articlePropina = Article::whereHas('monthly_charges', function ($q) use ($courseID) {
                //             $q->where('course_id', $courseID);
                //         })->first();

                //         $articlePropinaId = $articlePropina->id;
                //     }

                //     $r3 = createAutomaticArticleRequest($user->id, $articlePropinaId, Carbon::now()->format('Y'), 3);
                //     if (!$r3) {
                //         throw new Exception('Could not create automatic [Pagamento de Propina (id: ' . $articlePropinaId . ')] article request payment for student (id: ' . $user->id . ') matriculation');
                //     }
                //     $articleRequets[$r3]['updatable'] = false;
                // } else {
                //     $allDisciplinesOffCurricularYear = array_replace($allDisciplinesInCurricularYear, $allDisciplinesOffCurricularYear);
                // }

                // Cadeiras em atraso
                // foreach ($allDisciplinesOffCurricularYear as $offDiscipline => $examOnly) {
                //     if ($examOnly) {
                //         // Inscrição Por Exame Cadeira Em Atraso (id: 41)
                //         $r4 = createAutomaticArticleRequest($user->id, 41, null, null);

                //         //return $r4;

                //         //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para exame
                //         $group = [
                //             'discipline_id' => $offDiscipline,
                //             'article_request_id' => $r4,
                //             'user_id' => $user->id
                //         ];

                //         DisciplineArticle::insert($group);

                //         if (!$r4) {
                //             throw new Exception('Could not create automatic [Inscrição Por Exame Cadeira Em Atraso (id: 41)] article request payment for student (id: ' . $user->id . ') matriculation');
                //         }
                //         $articleRequets[$r4]['updatable'] = true;
                //     } else {
                //         // Inscrição Por Frequência Cadeira Em Atraso (id: 42)
                //         $r5 = createAutomaticArticleRequest($user->id, 42, null, null);

                //         //Array que vai para a tabela 'disciplines_request' caso existirem disciplinas em atraso registados para frequencia
                //         $group = [
                //             'discipline_id' => $offDiscipline,
                //             'article_request_id' => $r5,
                //             'user_id' => $user->id
                //         ];

                //         DisciplineArticle::insert($group);

                //         if (!$r5) {
                //             throw new Exception('Could not create automatic [Inscrição Por Frequência Cadeira Em Atraso (id: 42)] article request payment for student (id: ' . $user->id . ') matriculation');
                //         }
                //         $articleRequets[$r5]['updatable'] = true;
                //     }
                // }

                // $matriculation->articleRequests()->sync($articleRequets);

                // $this->changeState("STORE", $request->get('user'), $maxSelectedYear, $courseID);

                DB::commit();

                // Success message
                Toastr::success(__('Users::matriculations.store_success_message'), __('toastr.success'));
                return redirect()->route('matriculations.index');
            //} else {
                //Error message (total de vagas excedidos)
                //Toastr::error(__('Total de vagas excedidas para esta turma'), __('toastr.error'));
                //return redirect()->route('matriculations.index');
            //}
        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // HELPERS //////

    protected function studentsWithCourseWithoutMatriculationSelectList()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->where('id', 6);
        })
            // ->whereHas('courses')
            ->doesntHave('matriculation')
            ->with(['parameters' => function ($q) {
                $q->whereIn('code', ['nome', 'n_mecanografico']);
            }])
            ->get()
            ->map(function ($user) {
                $displayName = $this->formatUserName($user);
                return ['id' => $user->id, 'display_name' => $displayName];
            });

        return $users->sortBy(function ($item) {
            return strtr(
                utf8_decode($item['display_name']),
                utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
            );
        });
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

    public function changeState($type, $userId, $maxSelectedYear, $courseID)
    {
        if ($type == "STORE") {
            //Avaliar apenas se ele esta a ser matriculado no ultimo ano do curso - se estiver a ser matriculado, mudar o estado para finalista
            //$yearsMatriculated = Matriculation::whereUserId($userId)->get();
            $maxCourseYear = Course::whereId($courseID)->firstOrFail();

            //se o ano em que ele esta matriculado for igual ao maior do curso. (ex: se estiver no 5 ano e o curso tem 5 anos.)
            if ($maxCourseYear->duration_value == $maxSelectedYear) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 19, 'courses_id' => null] //19 => Finalista
                );
                UserStateHistoric::create([
                                    'user_id' => $userId,
                                    'state_id' => 19
                                ]);
            }

        } elseif ($type == "CHANGE") {
            $user_state = UserState::whereUserId($userId)->first();

            if (!$user_state == null && $user_state->state_id == 9) {
                UserState::updateOrCreate(
                    ['user_id' => $userId],
                    ['state_id' => 7]
                );
                UserStateHistoric::create([
                        'user_id' => $userId,
                        'state_id' => 7
                    ]);
            }
        }
    }
}
