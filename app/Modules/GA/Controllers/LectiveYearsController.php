<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\LectiveYearTranslation;
use App\Modules\GA\Requests\LectiveYearRequest;
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

class LectiveYearsController extends Controller
{

    public function index()
    {
        try {
            return view('GA::lective-years.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = LectiveYear::join('users as u1', 'u1.id', '=', 'lective_years.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'lective_years.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'lective_years.deleted_by')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->select([
                    'lective_years.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'lyt.display_name',
                ]);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::lective-years.datatables.actions')->with('item', $item);
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
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::lective-years.lective-year')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(LectiveYearRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $lective_year = LectiveYear::create([
                'code' => $request->get('code'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ]);
            $lective_year->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $lective_year_translations[] = [
                    'lective_years_id' => $lective_year->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($lective_year_translations)) {
                LectiveYearTranslation::insert($lective_year_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::lective-years.store_success_message'), __('toastr.success'));
            return redirect()->route('lective-years.index');

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
            $lective_year = LectiveYear::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'lective_year' => $lective_year,
                'translations' => $lective_year->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::lective-years.lective-year')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
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

   public function update(LectiveYearRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $lective_year = LectiveYear::whereId($id)->firstOrFail();
            $lective_year->code = $request->get('code');
            $lective_year->start_date = $request->get('start_date');
            $lective_year->end_date = $request->get('end_date');
            $lective_year->save();

            // Disable previous translations
            LectiveYearTranslation::where('lective_years_id', $lective_year->id)->update(['active' => false]);

            $version = LectiveYearTranslation::where('lective_years_id', $lective_year->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $lective_year_translations[] = [
                    'lective_years_id' => $lective_year->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($lective_year_translations)) {
                LectiveYearTranslation::insert($lective_year_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::lective-years.update_success_message'), __('toastr.success'));
            return redirect()->route('lective-years.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
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
            $lective_year = LectiveYear::whereId($id)->firstOrFail();
            $lective_year->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::lective-years.destroy_success_message'), __('toastr.success'));
            return redirect()->route('lective-years.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
