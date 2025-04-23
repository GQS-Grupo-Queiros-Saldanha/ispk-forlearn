<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DisciplinePeriod;
use App\Modules\GA\Models\DisciplinePeriodTranslation;
use App\Modules\GA\Requests\DisciplinePeriodRequest;
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

class DisciplinePeriodsController extends Controller
{

    public function index()
    {
        try {
            return view('GA::discipline-periods.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DisciplinePeriod::join('users as u1', 'u1.id', '=', 'discipline_periods.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'discipline_periods.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'discipline_periods.deleted_by')
                ->leftJoin('discipline_period_translations as dpt', function ($join) {
                    $join->on('dpt.discipline_periods_id', '=', 'discipline_periods.id');
                    $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dpt.active', '=', DB::raw(true));
                })
                ->select(['discipline_periods.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'dpt.display_name']);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::discipline-periods.datatables.actions')->with('item', $item);
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
            return view('GA::discipline-periods.discipline-period')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplinePeriodRequest $request
     * @return Response
     */
    public function store(DisciplinePeriodRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $discipline_period = DisciplinePeriod::create([
                'code' => $request->get('code')
            ]);
            $discipline_period->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $discipline_period_translations[] = [
                    'discipline_periods_id' => $discipline_period->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($discipline_period_translations)) {
                DisciplinePeriodTranslation::insert($discipline_period_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-periods.store_success_message'), __('toastr.success'));
            return redirect()->route('discipline-periods.index');

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
            $discipline_period = DisciplinePeriod::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'discipline_period' => $discipline_period,
                'translations' => $discipline_period->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::discipline-periods.discipline-period')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-periods.not_found_message'), __('toastr.error'));
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
     * @param DisciplinePeriodRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplinePeriodRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline_period = DisciplinePeriod::whereId($id)->firstOrFail();
            $discipline_period->code = $request->get('code');
            $discipline_period->save();

            // Disable previous translations
            DisciplinePeriodTranslation::where('discipline_periods_id', $discipline_period->id)->update(['active' => false]);

            $version = DisciplinePeriodTranslation::where('discipline_periods_id', $discipline_period->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $discipline_period_translations[] = [
                    'discipline_periods_id' => $discipline_period->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($discipline_period_translations)) {
                DisciplinePeriodTranslation::insert($discipline_period_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-periods.update_success_message'), __('toastr.success'));
            return redirect()->route('discipline-periods.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-periods.not_found_message'), __('toastr.error'));
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
            $discipline_period = DisciplinePeriod::whereId($id)->firstOrFail();
            $discipline_period->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::discipline-periods.destroy_success_message'), __('toastr.success'));
            return redirect()->route('discipline-periods.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-periods.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
