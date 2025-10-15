<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\Summary;
use App\Modules\GA\Models\SummaryTranslation;
use App\Modules\GA\Requests\SummaryRequest;
use App\Modules\Users\Models\Matriculation;
use App\Modules\GA\Models\LectiveYear;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;
use Toastr;

class SummariesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
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
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];
            $auth = auth()->user();
            if ($auth->hasRole('student')) {
                return redirect()->route('summaryStudent');
            }
            return view('GA::summaries.index')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax($lective_year)
    {                
        try {
            $auth = auth()->user();
            
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $lective_year)
            // ->first()
            ->get();

            if ($auth->hasRole('teacher')) {
                $disciplines = DB::table('user_disciplines')
                    ->select('disciplines_id as id')
                    ->where('users_id', $auth->id)
                    ->get();

                 $model = Summary::join('users as u1', 'u1.id', '=', 'summaries.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'summaries.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'summaries.deleted_by')

                    ->leftJoin('summary_translations as st', function ($join) {
                        $join->on('st.summaries_id', '=', 'summaries.id');
                        $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('st.active', '=', DB::raw(true));
                    })
                    ->leftJoin('study_plan_translations as spt', function ($join) {
                        $join->on('spt.study_plans_id', '=', 'summaries.study_plan_id');
                        $join->on('spt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('spt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as d', 'd.id', '=', 'summaries.discipline_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'summaries.discipline_id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('discipline_regime_translations as drt', function ($join) {
                        $join->on('drt.discipline_regimes_id', '=', 'summaries.discipline_regime_id');
                        $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('drt.active', '=', DB::raw(true));
                    })
                    ->select([

                        'summaries.id',
                        'summaries.order as order',
                        'summaries.created_at',
                        'summaries.updated_at',
                        'summaries.deleted_at',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        'st.display_name as display_name',
                        'spt.display_name as study_plan',
                        DB::raw('CONCAT(\'#\', d.code, \' - \', dt.display_name) as discipline'),
                        'drt.display_name as regime'
                    ])
                    ->whereIn('summaries.discipline_id', $disciplines->pluck('id')->all())
                    ->whereBetween('summaries.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])  
                    //->orderBy('spt.study_plans_id', 'DESC')
                    ->orderBy('dt.discipline_id')
                    //->orderBy('drt.discipline_regimes_id')
                    ->orderBy('summaries.order')
                    ->get();
                /* $auth = auth()->user();
                 if ($auth->hasRole('teacher')) {
                    //return $disciplines->pluck('id');
                    $model = $model->whereIn('summaries.discipline_id', $disciplines->pluck('id')->all());

                }*/
                return (DataTables::of($model)
                    ->addColumn('actions', function ($item) {
                        return view('GA::summaries.datatables.actions')->with('item', $item);
                    })
                    ->rawColumns(['actions'])
                    ->make('true'));
            } else {

                $model = Summary::join('users as u1', 'u1.id', '=', 'summaries.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'summaries.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'summaries.deleted_by')

                    ->leftJoin('summary_translations as st', function ($join) {
                        $join->on('st.summaries_id', '=', 'summaries.id');
                        $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('st.active', '=', DB::raw(true));
                    })
                    ->leftJoin('study_plan_translations as spt', function ($join) {
                        $join->on('spt.study_plans_id', '=', 'summaries.study_plan_id');
                        $join->on('spt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('spt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('disciplines as d', 'd.id', '=', 'summaries.discipline_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'summaries.discipline_id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                    ->leftJoin('discipline_regime_translations as drt', function ($join) {
                        $join->on('drt.discipline_regimes_id', '=', 'summaries.discipline_regime_id');
                        $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('drt.active', '=', DB::raw(true));
                    })
                    ->select([

                        'summaries.id',
                        'summaries.order as order',
                        'summaries.created_at',
                        'summaries.updated_at',
                        'summaries.deleted_at',
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        'st.display_name as display_name',
                        'spt.display_name as study_plan',
                        DB::raw('CONCAT(\'#\', d.code, \' - \', dt.display_name) as discipline'),
                        'drt.display_name as regime'
                    ])
                    //->orderBy('spt.study_plans_id', 'DESC')
                    ->orderBy('dt.discipline_id')
                    //->orderBy('drt.discipline_regimes_id')
                    ->whereBetween('summaries.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])  
                    ->orderBy('summaries.order')
                    ->get();

                return (DataTables::of($model)
                    ->addColumn('actions', function ($item) {
                        return view('GA::summaries.datatables.actions')->with('item', $item);
                    })
                    ->rawColumns(['actions'])
                    ->make('true'));
            }
            /*try {

            $model = Summary::query()->join('users as u1', 'u1.id', '=', 'summaries.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'summaries.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'summaries.deleted_by')

                ->leftJoin('summary_translations as st', function ($join) {
                    $join->on('st.summaries_id', '=', 'summaries.id');
                    $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('st.active', '=', DB::raw(true));
                })
                ->leftJoin('study_plan_translations as spt', function ($join) {
                    $join->on('spt.study_plans_id', '=', 'summaries.study_plan_id');
                    $join->on('spt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('spt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines as d', 'd.id', '=', 'summaries.discipline_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'summaries.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'summaries.discipline_regime_id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->select([

                    'summaries.id',
                    'summaries.order',
                    'summaries.created_at',
                    'summaries.updated_at',
                    'summaries.deleted_at',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    //'st.display_name as display_name',
                    'spt.display_name as study_plan_id',
                    DB::raw('CONCAT(\'#\', d.code, \' - \', dt.display_name) as discipline_id'),
                    'drt.display_name as discipline_regime_id'
                ])
                ->orderBy('spt.study_plans_id')
                ->orderBy('dt.discipline_id')
                ->orderBy('drt.discipline_regimes_id')
                ->orderBy('summaries.order');

            $auth = auth()->user();
            if ($auth->hasRole('teacher')) {
                $model = $model->whereIn('summaries.discipline_id', $auth->disciplines()->pluck('id')->all());
            }

            return (DataTables::of($model)
                    ->addColumn('actions', function($item) {
                        return view('GA::summaries.datatables.actions')->with('item', $item);
                    })
                    ->rawColumns(['actions'])
                    ->make('true'));
           /* return DataTables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::summaries.datatables.actions')->with('item', $item);
                })*/
            /*   ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })*/
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return response()->json($e->getMessage(), 500);
        }
    }

    /** @noinspection PhpUnused */
    public function ajaxDisciplines($id)
    {
        try {
            $studyPlan = StudyPlan::findOrFail($id)->load('course');

            $courseId = $studyPlan->course->id;

            /*if (auth()->user()->hasRole('teacher')) {
                $disciplines = disciplinesSelect([$courseId], auth()->user());
            } else {
                $disciplines = disciplinesSelect([$courseId]);
            }*/

            if (auth()->user()->hasRole('teacher')) {
                $user_disciplines = DB::table('user_disciplines')
                    ->select('disciplines_id as id')
                    ->where('users_id', auth()->user()->id)
                    ->get();

                $disciplines = Discipline::with([
                    'currentTranslation'
                ])
                    ->whereIn('id', $user_disciplines->pluck('id'))
                    ->where('courses_id', $courseId)
                    ->get();
            } else {
                $disciplines = Discipline::with([
                    'currentTranslation'
                ])
                    ->where('courses_id', $courseId)
                    ->get();
            }



            return response()->json($disciplines);
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /** @noinspection PhpUnused */
    public function ajaxDisciplineRegimes($studyPlanId, $disciplineId)
    {
        try {
            $discipline = Discipline::where('id', $disciplineId)
                ->with(['study_plans_has_disciplines' => function (HasMany $q) use ($studyPlanId) {
                    $q->where('study_plans_id', $studyPlanId)
                        ->with([
                            'study_plans_has_discipline_regimes' => function (HasMany $q) {
                                $q->with(['discipline_regime' => function (BelongsTo $q) {
                                    $q->with('currentTranslation');
                                }]);
                            }
                        ]);
                }])
                ->first();

            if (!$discipline) {
                throw new Exception('Discipline not found!');
            }


            if (!$discipline->study_plans_has_disciplines->count()) {
                throw new Exception('Discipline does not belong to a study plan!');
            }


            if (!$discipline->study_plans_has_disciplines
                ->first()->study_plans_has_discipline_regimes->count()) {
                throw new Exception('Discipline does not regimes!');
            }


            $selectData = $discipline->study_plans_has_disciplines
                ->first()
                ->study_plans_has_discipline_regimes
                ->map(function ($regime) {
                    return [
                        'id' => $regime->discipline_regime->id,
                        'display_name' => $regime->discipline_regime->currentTranslation->display_name,
                        'total_hours' => $regime->hours
                    ];
                });

            return response()->json($selectData);
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        try {

            $study_plans = StudyPlan::with([
                'currentTranslation',
            ]);

            $auth = auth()->user();
            if ($auth->hasRole('teacher')) {
                $study_plans = $study_plans->whereHas('course', function ($query) use ($auth) {
                    $query->whereIn('id', $auth->courses()->pluck('id')->all());
                });
            }

            $study_plans = $study_plans->get();

            $data = [
                'action' => 'create',
                'study_plans' => $study_plans,
                'languages' => Language::whereActive(true)->get(),
            ];

            return view('GA::summaries.summary')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SummaryRequest $request
     * @return JsonResponse|RedirectResponse|void
     */
    public function store(SummaryRequest $request)
    {

        try {

            DB::beginTransaction();

            $studyPlanId = $request->get('study_plan');
            $disciplineId = $request->get('discipline');
            $regimeId = $request->get('regime');
            // $file = $request->file('files');

            $latestSummary = Summary::where('study_plan_id', $studyPlanId)
                ->where('discipline_id', $disciplineId)
                //->where('discipline_regime_id', $regimeId)
                ->latest()
                ->first();

            // $latestSummaryID = Summary::latest()->first();
            $latestSummaryID = DB::table('summaries')->get()->last();


            // Create
            $summary = new Summary([
                'study_plan_id' => $studyPlanId,
                'discipline_id' => $disciplineId,
                'discipline_regime_id' => $regimeId,
                'order' => $latestSummary ? $latestSummary->order + 1 : 1,
                'content' => $request->get('text'),
            ]);

            // if ($request->hasFile('files')) {
            //     $filename = $latestSummaryID->id + 1 . '_' . $file->getClientOriginalName();
            //     $summary->file = "/storage/attachment/" . $filename;
            //     $file->storeAs('attachment', $filename);
            // }

            $count = 0;

            // SALVA MULTIPLO ARQUIVOS            
            $multifile = $request->file('filenames');
            if ($request->hasFile('filenames')) {
                foreach ($multifile as $file) {   
                    $count = $count + 1;                    
                }            

                if ($count > 0) {
                    foreach ($multifile as $file) {
                        $filename = $latestSummaryID->id + 1 . '_' . $file->getClientOriginalName();                        
                        // SALVA OS ARQUIVOS NA NOVA TABELA
                        DB::table('summaries_archive')->insert(
                            [
                                'summaries_id' => $latestSummaryID->id + 1 , 
                                'archive'=> "/storage/attachment/" . $filename,
                            ]
                        );

                        $file->storeAs('attachment', $filename);
                    }
                }
            }
            
            $summary->save();

            // translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $summary_translations[] = [
                    'summaries_id' => $summary->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($summary_translations)) {
                SummaryTranslation::insert($summary_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::summaries.store_success_message'), __('toastr.success'));

            return redirect()->route('summaries.index');
        } catch (Exception | Throwable $e) {
            // return $e;
            Toastr::error($e->getMessage(), __('toastr.error'));

            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $summary = Summary::whereId($id)->with([
                'translations' => function ($q) {
                    /** @var SummaryTranslation $q */
                    $q->whereActive(true);
                },
                'studyPlan' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                },
                'discipline' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                },
                'regime' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                }
            ])->firstOrFail();

            $summaries_archive = DB::table('summaries_archive')
            ->where('summaries_id', '=', $id)
            ->get();
            
            $data = [
                'action' => $action,
                'summary' => $summary,
                'summaries_archive' => $summaries_archive,
                'archive_lenght' => count($summaries_archive),
                'translations' => $summary->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::summaries.summary')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::summaries.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Factory|JsonResponse|RedirectResponse|Response|View|void
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|JsonResponse|RedirectResponse|Response|View|void
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SummaryRequest $request
     * @param int $id
     * @return JsonResponse|RedirectResponse|Response|void
     */
    public function update(SummaryRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // $file = $request->file('files');

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $summary = Summary::whereId($id)->firstOrFail();

            $summary->content = $request->get('text');            

            $summary_archive = DB::table('summaries_archive')
            ->where('summaries_id', '=', $id)
            ->get();
            
            $file = $request->file('filenames');
            $multifile = $request->file('filenames');
            $count = 0;

            if ($request->hasFile('filenames')) {
                foreach ($multifile as $file) {   
                    $count = $count + 1;                    
                }
            }

            if ((count($summary_archive) > 0) && ($count > 0)) {                

                if ($count > 0) {
                    $index = 0;
                    foreach ($multifile as $file) {
                        // $filename = $latestSummaryID->id + 1 . '_' . $file->getClientOriginalName();
                        $filename = $summary->id . '_' . $file->getClientOriginalName();
                        
                        // ATUALIZA OS ARQUIVOS NA NOVA TABELA
                        $summary_archive[$index]->archive = "/storage/attachment/" . $filename;
                        $summary_archive[$index]->save();
                        
                        $index = $index + 1;
    
                        $file->storeAs('attachment', $filename);
                    }
                }
            }
            else {
                if ($request->hasFile('filenames')) {
                    $filename = $summary->id . '_' . $file->getClientOriginalName();
                    $summary->file = "/storage/attachment/" . $filename;
                    $file->storeAs('attachment', $filename);
                } else {
                }        
            }

            if ($request->get('order') != $summary->order) {
                $summary = reorderSummaries($summary, (int)$request->get('order'));
            }

            $summary->save();




            // Disable previous translations
            SummaryTranslation::where('summaries_id', $summary->id)->update(['active' => false]);
            $version = SummaryTranslation::where('summaries_id', $summary->id)
                ->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $summary_translations[] = [
                    'summaries_id' => $summary->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($summary_translations)) {
                SummaryTranslation::insert($summary_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::summaries.update_success_message'), __('toastr.success'));

            return redirect()->route('summaries.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::summaries.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse|RedirectResponse|Response|void
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $summary = Summary::whereId($id)->firstOrFail();
            DB::table('summaries_archive')
            ->where('summaries_id', '=', $id)
            ->delete();

            $summary->translations()->forceDelete();
            $summary->delete();

            reorderSummaries($summary, null);

            $summary->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::summaries.destroy_success_message'), __('toastr.success'));

            return redirect()->route('summaries.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::summaries.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function summaryByStudent()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        
        
        $studentId = Auth::user()->id;
       $allDisciplines = Matriculation::with(['disciplines' => function ($q) {
            $q->with('currentTranslation');
        }])
            ->where('matriculations.user_id', $studentId)
            ->where('matriculations.lective_year', $lectiveYearSelected)
            ->get();

        return view('GA::summaries.student-summary', compact('allDisciplines', 'lectiveYears', 'lectiveYearSelected'));
    }

    public function getSummary($disciplineId, $lective_year)
    {
        $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $lective_year)
            // ->first()
        ->get();

        
        $summaries = Summary::whereDisciplineId($disciplineId)
            ->with([
                'translations' => function ($q) {
                    /** @var SummaryTranslation $q */
                    $q->whereActive(true);
                },
                /*'studyPlan' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                },*/
                'discipline' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                },
                'regime' => function (BelongsTo $q) {
                    $q->with('currentTranslation');
                }
            ])
        ->whereBetween('summaries.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
        ->get();
        
        
        $studentId = Auth::user()->id;
        $allDisciplines = Matriculation::with(['disciplines' => function ($q) {
            $q->with('currentTranslation');
        }])
        ->where('matriculations.user_id', $studentId)
        ->where('matriculations.lective_year', $lective_year)
        ->get();

        return [$summaries, $allDisciplines];
    }

    public function summaryInfo($summaryId)
    {
        try {
            return $this->fetch($summaryId, 'show');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxSummaryArchive($id)
    {
        $summary = Summary::whereId($id)
            ->first();

        return $summary ? $summary->file : null;
    }
}
