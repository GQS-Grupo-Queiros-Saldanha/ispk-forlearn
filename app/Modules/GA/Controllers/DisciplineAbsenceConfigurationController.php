<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineAbsenceConfiguration;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Requests\DisciplineAbsenceConfigurationRequest;
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

class DisciplineAbsenceConfigurationController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-absence-configuration.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplineAbsenceConfiguration::join('study_plan_editions as spe', 'spe.id', '=', 'discipline_absence_configuration.study_plan_editions_id')
                ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                    $join->on('spet.study_plan_editions_id', '=', 'spe.id');
                    $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('spet.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_regimes as dr', 'dr.id', '=', 'discipline_absence_configuration.discipline_regimes_id')
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'dr.id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines as d', 'd.id', '=', 'discipline_absence_configuration.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select([
                    'discipline_absence_configuration.*',
                    'spet.display_name as spet',
                    'drt.display_name as drt',
                    'dt.display_name as dt'
                ]);

                return Datatables::eloquent($model)
                    ->addColumn('actions', function ($item) {
                        return view('GA::discipline-absence-configuration.datatables.actions')->with('item', $item);
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
                },
                // 'regime' => function ($q) {
                //     $q->with([
                //         'translation' => function ($q) {
                //             $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                //         }
                //     ]);
                // }
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


            $data = [
                'action' => 'create',
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'discipline_regimes' => $discipline_regimes,
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-absence-configuration.discipline-absence-configuration')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineAbsenceConfigurationRequest $request
     * @return Response
     */
    public function store(DisciplineAbsenceConfigurationRequest $request)
    {
        try {

            // Create
            $data = [
                'max_absence' => $request->get('max_absence'),
                'is_total' => $request->has('is_total') ? 1 : 0,
                'study_plan_editions_id' => $request->get('study_plan_editions'),
                'disciplines_id' => $request->get('disciplines'),
                'max_absences' => $request->get('max_absences'),
                'discipline_regimes_id' => $request->has('is_total') ? 0 : $request->get('discipline_regimes')
            ];

            $discipline_absence_configuration = DisciplineAbsenceConfiguration::create($data);
            $discipline_absence_configuration->save();

            // Success message
            Toastr::success(__('GA::discipline-absence-configuration.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-absence-configuration.index');

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
            $discipline_absence_configuration = DisciplineAbsenceConfiguration::whereId($id)->with([
                'study_plan_edition' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                },
                'discipline' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                },
                'discipline_regime' => function ($q) {
                    $q->with([
                        'translations' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ]);
                },
            ])->firstOrFail();

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


            $data = [
                'action' => $action,
                'discipline_absence_configuration' => $discipline_absence_configuration,
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'discipline_regimes' => $discipline_regimes,
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-absence-configuration.discipline-absence-configuration')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-absence-configuration.not_found_message'), __('toastr.error'));
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
     * @param DisciplineAbsenceConfigurationRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineAbsenceConfigurationRequest $request, $id)
    {
        try {
            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_absence_configuration = DisciplineAbsenceConfiguration::whereId($id)->firstOrFail();
            $discipline_absence_configuration->max_absences = $request->get('max_absences');
            $discipline_absence_configuration->is_total = $request->has('is_total') ? 1 : 0;

            // Dissociate relations
            $discipline_absence_configuration->study_plan_edition()->dissociate();
            $discipline_absence_configuration->discipline()->dissociate();

            // Foreign keys
            $discipline_absence_configuration->study_plan_edition()->associate($request->get('study_plan_editions'));
            $discipline_absence_configuration->discipline()->associate($request->get('disciplines'));

            if(!$request->has('is_total')){
                $discipline_absence_configuration->discipline_regime()->dissociate();
                $discipline_absence_configuration->discipline_regime()->associate($request->get('discipline_regimes'));
            }

            $discipline_absence_configuration->save();

            // Success message
            Toastr::success(__('GA::discipline-absence-configuration.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-absence-configuration.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-absence-configuration.not_found_message'), __('toastr.error'));
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
            // Find and delete
            $discipline_absence_configuration = DisciplineAbsenceConfiguration::whereId($id)->firstOrFail();
            $discipline_absence_configuration->delete();

            // Success message
            Toastr::success(__('GA::discipline-absence-configuration.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-absence-configuration.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-absence-configuration.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
