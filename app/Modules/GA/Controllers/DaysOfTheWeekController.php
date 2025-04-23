<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\DayOfTheWeekTranslation;
use App\Modules\GA\Requests\DayOfTheWeekRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;

class DaysOfTheWeekController extends Controller
{
    public function index()
    {
        try {
            return view('GA::days-of-the-week.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DayOfTheWeek::join('users as u1', 'u1.id', '=', 'days_of_the_week.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'days_of_the_week.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'days_of_the_week.deleted_by')
                ->leftJoin('day_of_the_week_translations as dt', function ($join) {
                    $join->on('dt.day_of_the_week_id', '=', 'days_of_the_week.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->select([
                    'days_of_the_week.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::days-of-the-week.datatables.actions')->with('item', $item);
                })
                ->editColumn('is_start_of_week', function ($item) {
                    return $item->is_start_of_week ? __('common.yes') : __('common.no');
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
            return response()->json($e->getMessage());
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $day_of_the_week = DayOfTheWeek::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'day_of_the_week' => $day_of_the_week,
                'translations' => $day_of_the_week->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::days-of-the-week.day-of-the-week')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::days-of-the-week.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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

    public function update(DayOfTheWeekRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $day_of_the_week = DayOfTheWeek::whereId($id)->firstOrFail();
            $day_of_the_week->code = $request->get('code');
            $day_of_the_week->save();

            // Disable previous translations
            DayOfTheWeekTranslation::where('day_of_the_week_id', $day_of_the_week->id)->update(['active' => false]);
            $version = DayOfTheWeekTranslation::where('day_of_the_week_id', $day_of_the_week->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $day_of_the_week_translations[] = [
                    'day_of_the_week_id' => $day_of_the_week->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($day_of_the_week_translations)) {
                DayOfTheWeekTranslation::insert($day_of_the_week_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::days-of-the-week.update_success_message'), __('toastr.success'));
            return redirect()->route('days-of-the-week.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::days-of-the-week.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function start_of_week($id)
    {
        try {
            DB::beginTransaction();

            // Find and update
            $day_of_the_week = DayOfTheWeek::whereId($id)->firstOrFail();
            DayOfTheWeek::whereIsStartOfWeek(true)->update(['is_start_of_week' => false]);
            $day_of_the_week->is_start_of_week = true;
            $day_of_the_week->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::days-of-the-week.update_success_message'), __('toastr.success'));
            return redirect()->route('days-of-the-week.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Cms::days-of-the-week.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


}
