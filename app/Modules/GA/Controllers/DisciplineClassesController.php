<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineClass;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Requests\DisciplineClassRequest;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Request;
use Throwable;
use Toastr;

class DisciplineClassesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-classes.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplineClass::
                leftJoin('disciplines as d', 'd.id', '=', 'discipline_classes.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('study_plan_editions as spe', 'spe.id', '=', 'discipline_classes.study_plan_editions_id')
                ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                    $join->on('spet.study_plan_editions_id', '=', 'spe.id');
                    $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('spet.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_regimes as dr', 'dr.id', '=', 'discipline_classes.discipline_regimes_id')
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'dr.id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->leftJoin('classes as c', 'c.id', '=', 'discipline_classes.classes_id')
                ->select([
                    'dr.id as discipline_regime_id',
                    'drt.display_name as discipline_regime',
                    'spe.id as study_plan_edition_id',
                    'spet.display_name as study_plan_edition',
                    'c.id as discipline_id',
                    'dt.display_name as discipline',
                    'c.id as class_id',
                    'c.display_name as class',
                    'discipline_classes.display_name as discipline_class',
                    'discipline_classes.id',
                ]);

                return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::discipline-classes.datatables.actions')->with('item', $item);
                })
               /* ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {


            $disciplines = Discipline::with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->get();

            $study_plan_editions = StudyPlanEdition::with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->get();

            $discipline_regimes = DisciplineRegime::with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->get();

            $classes = Classes::get();

            $data = [
                'action' => 'create',
                'classes' => $classes,
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'discipline_regimes' => $discipline_regimes,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::discipline-classes.discipline-classes')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineClassRequest $request
     * @return Response
     */
    public function store(DisciplineClassRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $discipline_classes = DisciplineClass::create([
                'display_name' => $request->get('display_name'),
                'classes_id' => $request->get('class'),
                'disciplines_id' => $request->get('discipline'),
                'study_plan_editions_id' => $request->get('study_plan_edition'),
                'discipline_regimes_id' => $request->get('discipline_regime')
            ]);
            $discipline_classes->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-classes.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-classes.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {

            // Find
            $discipline_classes = DisciplineClass::whereId($id)->with([

                'discipline' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                },
                'study_plan_edition' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                },
                'classes' => function ($q) {

                },
                'discipline_regime' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                }
            ])->firstOrFail();

            $disciplines = Discipline::with([
                'translations' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $study_plan_editions = StudyPlanEdition::with([
                'translations' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $discipline_regimes = DisciplineRegime::with([
                'translations' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $classes = Classes::with([

            ])->get();

            $data = [
                'action' => $action,
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'discipline_regimes' => $discipline_regimes,
                'discipline_classes' => $discipline_classes,
                'classes' => $classes,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::discipline-classes.discipline-classes')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DisciplineClassRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineClassRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            //$default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_classes = DisciplineClass::whereId($id)->firstOrFail();
            //$discipline_classes->code = $request->get('code');
            $discipline_classes->display_name = $request->get('display_name');


            // Dissociate relations
            $discipline_classes->study_plan_edition()->dissociate();
            $discipline_classes->discipline()->dissociate();
            $discipline_classes->discipline_regime()->dissociate();
            $discipline_classes->classes()->dissociate();

            // Foreign keys
            $discipline_classes->study_plan_edition()->associate($request->get('study_plan_edition'));
            $discipline_classes->discipline()->associate($request->get('discipline'));
            $discipline_classes->discipline_regime()->associate($request->get('discipline_regime'));
            $discipline_classes->classes()->associate($request->get('class'));
            $discipline_classes->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-classes.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-classes.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

   public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $discipline_classes = DisciplineClass::whereId($id)->firstOrFail();
            $discipline_classes->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-classes.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-classes.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-classes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
