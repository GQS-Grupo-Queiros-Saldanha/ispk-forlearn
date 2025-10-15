<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineTranslation;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Models\StudyPlanEditionTranslation;
use App\Modules\GA\Models\DisciplineCurricula;
use App\Modules\GA\Models\DisciplineCurriculaTranslation;
use App\Modules\GA\Requests\DisciplineCurriculaRequest;
use Carbon\Carbon;
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

class DisciplineCurriculaController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-curricula.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplineCurricula::
                leftJoin('disciplines as d', 'd.id', '=', 'discipline_curricula.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('study_plan_editions as spe', 'spe.id', '=', 'discipline_curricula.study_plan_editions_id')
                ->leftJoin('study_plan_edition_translations as spet', function ($join) {
                    $join->on('spet.study_plan_editions_id', '=', 'spe.id');
                    $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('spet.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_curricula_translations as dct', function ($join) {
                    $join->on('dct.discipline_curricula_id', '=', 'discipline_curricula.id');
                    $join->on('dct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dct.active', '=', DB::raw(true));
                })
                ->select([
                    'spe.id as study_plan_edition_id',
                    'spet.display_name as study_plan_edition',
                    'd.id as discipline_id',
                    'dt.display_name as discipline',
                    'dct.presentation',
                    'dct.bibliography',
                    'discipline_curricula.id',
                ]);

                return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::discipline-curricula.datatables.actions')->with('item', $item);
                })
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


            $data = [
                'action' => 'create',
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::discipline-curricula.discipline-curricula')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineCurriculaRequest $request
     * @return Response
     */
    public function store(DisciplineCurriculaRequest $request)
    {
        try {

            // Create
            $discipline_curricula = DisciplineCurricula::create([
                'disciplines_id' => $request->get('discipline'),
                'study_plan_editions_id' => $request->get('study_plan_editions')
            ]);
            $discipline_curricula->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $translations[] = [
                    'discipline_curricula_id' => $discipline_curricula->id,
                    'language_id' => $language->id,
                    'presentation' => $request->get('presentation')[$language->id],
                    'bibliography' => $request->get('bibliography')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }
            if (!empty($translations)) {
                DisciplineCurriculaTranslation::insert($translations);
            }

            // Success message
            Toastr::success(__('GA::discipline-curricula.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-curricula.index');

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
            $discipline_curricula = DisciplineCurricula::whereId($id)->with([

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
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $disciplines = Discipline::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $study_plan_editions = StudyPlanEdition::with([
                'translations' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $data = [
                'action' => $action,
                'disciplines' => $disciplines,
                'study_plan_editions' => $study_plan_editions,
                'discipline_curricula' => $discipline_curricula,
                'translations' => $discipline_curricula->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];

            //dd($data);

            return view('GA::discipline-curricula.discipline-curricula')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-curricula.not_found_message'), __('toastr.error'));
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
     * @param DisciplineCurriculaRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineCurriculaRequest $request, $id)
    {
        try {


            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_curricula = DisciplineCurricula::whereId($id)->firstOrFail();

            // Dissociate relations
            $discipline_curricula->study_plan_edition()->dissociate();
            $discipline_curricula->discipline()->dissociate();

            // Foreign keys
            $discipline_curricula->study_plan_edition()->associate($request->get('study_plan_editions'));
            $discipline_curricula->discipline()->associate($request->get('discipline'));
            $discipline_curricula->save();

            // Disable previous translations
            DisciplineCurriculaTranslation::where('discipline_curricula_id', $discipline_curricula->id)->update(['active' => false]);

            $version = DisciplineCurriculaTranslation::where('discipline_curricula_id', $discipline_curricula->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $discipline_curricula_translations[] = [
                    'discipline_curricula_id' => $discipline_curricula->id,
                    'language_id' => $language->id,
                    'presentation' => $request->get('presentation')[$language->id] ?? null,
                    'bibliography' => $request->get('bibliography')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($discipline_curricula_translations)) {
                DisciplineCurriculaTranslation::insert($discipline_curricula_translations);
            }

            // Success message
            Toastr::success(__('GA::discipline-curricula.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-curricula.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-curricula.not_found_message'), __('toastr.error'));
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
            $discipline_curricula = DisciplineCurricula::whereId($id)->firstOrFail();
            $discipline_curricula->delete();

            // Success message
            Toastr::success(__('GA::discipline-curricula.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-curricula.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-curricula.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
