<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\ScheduleType;
use App\Modules\GA\Models\ScheduleTypeTime;
use App\Modules\GA\Models\ScheduleTypeTimeTranslation;
use App\Modules\GA\Models\ScheduleTypeTranslation;
use App\Modules\GA\Requests\ScheduleTypeRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Toastr;
use Auth;

class ScheduleTypesController extends Controller
{
    public function index()
    {
        try {
            return view('GA::schedule-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = ScheduleType::join('users as u1', 'u1.id', '=', 'schedule_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'schedule_types.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'schedule_types.deleted_by')
                ->leftJoin('schedule_type_translations as stt', function ($join) {
                    $join->on('stt.schedule_type_id', '=', 'schedule_types.id');
                    $join->on('stt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('stt.active', '=', DB::raw(true));
                })
                ->select([
                    'schedule_types.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'stt.display_name'
                ]);
                
                if(auth()->user()->id == 865) dd("OI");
                
                return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::schedule-types.datatables.actions')->with('item', $item);
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
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::schedule-types.schedule-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(ScheduleTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $schedule_type = ScheduleType::create([
                'code' => $request->get('code')
            ]);
            $schedule_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $item_translations[] = [
                    'schedule_type_id' => $schedule_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }
            if (!empty($item_translations)) {
                ScheduleTypeTranslation::insert($item_translations);
            }

            // Times
            if ($request->has('times')) {
                $schedule_type_time_translations = [];

                // Get ordered array
                $times = array_values($request->get('times'));
                foreach ($times as $order => $time) {
                    $schedule_type_time = new ScheduleTypeTime([
                        'schedule_type_id' => $schedule_type->id,
                        'code' => $time['code'],
                        'start' => $time['start'],
                        'end' => $time['end'],
                        'order' => $order
                    ]);
                    $schedule_type_time->save();

                    // Translations
                    foreach ($languages as $language) {
                        $schedule_type_time_translations[] = [
                            'schedule_type_time_id' => $schedule_type_time->id,
                            'language_id' => $language->id,
                            'display_name' => $time['display_name'][$language->id],
                            'description' => $time['description'][$language->id],
                            'created_at' => Carbon::now(),
                            'version' => 1,
                            'active' => true
                        ];
                    }
                }

                if (!empty($schedule_type_time_translations)) {
                    ScheduleTypeTimeTranslation::insert($schedule_type_time_translations);
                }
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedule-types.store_success_message'), __('toastr.success'));
            return redirect()->route('schedule-types.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $schedule_type = ScheduleType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'times' => function ($q) {
                    $q->with([
                        'translations'
                    ]);
                }
            ])->firstOrFail();

            // Times
            $times = $schedule_type->times->toArray();
            foreach ($times as $index => $time) {
                $times[$index]['translations'] = $schedule_type->times[$index]->translations->keyBy('language_id')->toArray();
            }
            $times = json_encode($times, JSON_THROW_ON_ERROR);

            $data = [
                'action' => $action,
                'schedule_type' => $schedule_type,
                'times' => $times,
                'translations' => $schedule_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::schedule-types.schedule-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedule-types.not_found_message'), __('toastr.error'));
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
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update(ScheduleTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();
            $languages = Language::whereActive(true)->get();

            // Find and update
            $schedule_type = ScheduleType::whereId($id)->with([
                'times' => function ($q) {
                    $q->with([
                        'translations'
                    ]);
                }
            ])->firstOrFail();
            $schedule_type->code = $request->get('code');
            $schedule_type->save();

            // Disable previous translations
            ScheduleTypeTranslation::where('schedule_type_id', $schedule_type->id)->update(['active' => false]);
            $version = ScheduleTypeTranslation::where('schedule_type_id', $schedule_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            foreach ($languages as $language) {
                $schedule_type_translations[] = [
                    'schedule_type_id' => $schedule_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($schedule_type_translations)) {
                ScheduleTypeTranslation::insert($schedule_type_translations);
            }

            // Times
            if ($request->has('times')) {
                $schedule_type_time_translations = [];

                // Get ordered array
                $times = array_values($request->get('times'));

                //================================================================================
                // Delete removed
                //================================================================================
                ScheduleTypeTimeTranslation::whereNotIn('schedule_type_time_id', array_column($times, 'id'))->delete();
                ScheduleTypeTime::whereNotIn('id', array_column($times, 'id'))->delete();

                foreach ($times as $order => $time) {


                    //================================================================================
                    // Update existing
                    //================================================================================
                    if (isset($time['id'])) {
                        ScheduleTypeTime::whereId($time['id'])->update([
                            'schedule_type_id' => $schedule_type->id,
                            'code' => $time['code'],
                            'start' => $time['start'],
                            'end' => $time['end'],
                            'order' => $order
                        ]);

                        // Disable previous translations
                        ScheduleTypeTimeTranslation::where('schedule_type_time_id', $time['id'])->update(['active' => false]);
                        $version = ScheduleTypeTimeTranslation::where('schedule_type_time_id', $time['id'])->whereLanguageId($default_language->id)->count() + 1;

                        // Associated translations
                        foreach ($languages as $language) {
                            $schedule_type_time_translations[] = [
                                'schedule_type_time_id' => $time['id'],
                                'language_id' => $language->id,
                                'display_name' => $time['display_name'][$language->id],
                                'description' => $time['description'][$language->id],
                                'created_at' => Carbon::now(),
                                'version' => $version,
                                'active' => true,
                            ];
                        }

                    } else {

                        //================================================================================
                        // Insert new
                        //================================================================================
                        $schedule_type_time = new ScheduleTypeTime([
                            'schedule_type_id' => $schedule_type->id,
                            'code' => $time['code'],
                            'start' => $time['start'],
                            'end' => $time['end'],
                            'order' => $order
                        ]);
                        $schedule_type_time->save();

                        // Translations
                        foreach ($languages as $language) {
                            $schedule_type_time_translations[] = [
                                'schedule_type_time_id' => $schedule_type_time->id,
                                'language_id' => $language->id,
                                'display_name' => $time['display_name'][$language->id],
                                'description' => $time['description'][$language->id],
                                'created_at' => Carbon::now(),
                                'version' => 1,
                                'active' => true
                            ];
                        }

                    }
                }

                if (!empty($schedule_type_time_translations)) {
                    ScheduleTypeTimeTranslation::insert($schedule_type_time_translations);
                }
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedule-types.update_success_message'), __('toastr.success'));
            return redirect()->route('schedule-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedule-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $schedule_type = ScheduleType::whereId($id)->with([
                'times'
            ])->firstOrFail();
            foreach ($schedule_type->times as $time) {
                $time->translations()->delete();
                $time->delete();
            }
            $schedule_type->translations()->delete();
            $schedule_type->delete();

            $schedule_type->deleted_by = Auth::user()->id;
            $schedule_type->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedule-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('schedule-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedule-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function times($id)
    {
        $schedule_type = ScheduleType::with([
            'times' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            }
        ])->findOrFail($id);

        return $schedule_type->times;
    }

}
