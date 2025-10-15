<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\DisciplineRegimeTranslation;
use App\Modules\GA\Requests\DisciplineRegimeRequest;
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

class DisciplineRegimesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-regimes.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplineRegime::
            join('users as u1', 'u1.id', '=', 'discipline_regimes.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'discipline_regimes.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'discipline_regimes.deleted_by')
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'discipline_regimes.id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->select(['discipline_regimes.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'drt.display_name']);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::discipline-regimes.datatables.actions')->with('item', $item);
                })
              /*  ->editColumn('created_at', function ($item) {
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
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-regimes.discipline-regime')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineRegimeRequest $request
     * @return Response
     */
    public function store(DisciplineRegimeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $discipline_regime = DisciplineRegime::create([
                'code' => $request->get('code')
            ]);
            $discipline_regime->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $discipline_regime_translations[] = [
                    'discipline_regimes_id' => $discipline_regime->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($discipline_regime_translations)) {
                DisciplineRegimeTranslation::insert($discipline_regime_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-regimes.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-regimes.index');

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
            $discipline_regime = DisciplineRegime::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'discipline_regime' => $discipline_regime,
                'translations' => $discipline_regime->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-regimes.discipline-regime')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-regimes.not_found_message'), __('toastr.error'));
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
     * @param DisciplineRegimeRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineRegimeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_regime = DisciplineRegime::whereId($id)->firstOrFail();
            $discipline_regime->code = $request->get('code');
            $discipline_regime->save();

            // Disable previous translations
            DisciplineRegimeTranslation::where('discipline_regimes_id', $discipline_regime->id)->update(['active' => false]);

            $version = DisciplineRegimeTranslation::where('discipline_regimes_id', $discipline_regime->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $discipline_regime_translations[] = [
                    'discipline_regimes_id' => $discipline_regime->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($discipline_regime_translations)) {
                DisciplineRegimeTranslation::insert($discipline_regime_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-regimes.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-regimes.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-regimes.not_found_message'), __('toastr.error'));
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
            $discipline_regime = DisciplineRegime::whereId($id)->firstOrFail();
            $discipline_regime->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-regimes.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-regimes.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-regimes.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
