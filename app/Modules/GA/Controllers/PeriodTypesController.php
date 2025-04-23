<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DisciplinePeriod;
use App\Modules\GA\Models\PeriodType;
use App\Modules\GA\Models\PeriodTypeTranslation;
use App\Modules\GA\Requests\PeriodTypeRequest;
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

class PeriodTypesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::period-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = PeriodType::join('users as u1', 'u1.id', '=', 'period_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'period_types.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'period_types.deleted_by')
                ->leftJoin('period_type_translations as ptt', function ($join) {
                    $join->on('ptt.period_types_id', '=', 'period_types.id');
                    $join->on('ptt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ptt.active', '=', DB::raw(true));
                })
                ->select([
                    'period_types.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ptt.display_name',
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::period-types.datatables.actions')->with('item', $item);
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
            $periods = DisciplinePeriod::with([
                'translation' => function($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $data = [
                'action' => 'create',
                'periods' => $periods,
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::period-types.period-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PeriodTypeRequest $request
     * @return Response
     */
    public function store(PeriodTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $period_type = new PeriodType([
                'code' => $request->get('code'),
            ]);
            $period_type->disciplinePeriod()->associate($request->get('period'));
            $period_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $period_type_translations[] = [
                    'period_types_id' => $period_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($period_type_translations)) {
                PeriodTypeTranslation::insert($period_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::period-types.store_success_message'), __('toastr.success'));
            return redirect()->route('period-types.index');

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
            $period_type = PeriodType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'period_type' => $period_type,
                'translations' => $period_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get(),
                'periods' => DisciplinePeriod::all(),
            ];
            return view('GA::period-types.period-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::period-types.not_found_message'), __('toastr.error'));
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
     * @param PeriodTypeRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(PeriodTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $period_type = PeriodType::whereId($id)->firstOrFail();
            $period_type->code = $request->get('code');
            $period_type->disciplinePeriod()->dissociate();
            $period_type->disciplinePeriod()->associate($request->get('period'));
            $period_type->save();

            // Disable previous translations
            PeriodTypeTranslation::where('period_types_id', $period_type->id)->update(['active' => false]);

            $version = PeriodTypeTranslation::where('period_types_id', $period_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $period_type_translations[] = [
                    'period_types_id' => $period_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($period_type_translations)) {
                PeriodTypeTranslation::insert($period_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::period-types.update_success_message'), __('toastr.success'));
            return redirect()->route('period-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::period-types.not_found_message'), __('toastr.error'));
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
            $period_type = PeriodType::whereId($id)->firstOrFail();
            $period_type->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::period-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('period-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::period-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
