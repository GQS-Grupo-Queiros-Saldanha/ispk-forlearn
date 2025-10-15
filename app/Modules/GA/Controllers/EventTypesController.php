<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\EventType;
use App\Modules\GA\Models\EventTypeTranslation;
use App\Modules\GA\Requests\EventTypeRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;

class EventTypesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::event-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = EventType::join('users as u1', 'u1.id', '=', 'event_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'event_types.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'event_types.deleted_by')
                ->leftJoin('event_type_translations as ett', function ($join) {
                    $join->on('ett.event_type_id', '=', 'event_types.id');
                    $join->on('ett.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ett.active', '=', DB::raw(true));
                })
                ->select([
                    'event_types.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ett.display_name']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::event-types.datatables.actions')->with('item', $item);
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
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::event-types.event-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventTypeRequest $request
     * @return Response
     */
    public function store(EventTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $event_type = EventType::create([
                'code' => $request->get('code')
            ]);
            $event_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $event_type_translations[] = [
                    'event_type_id' => $event_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($event_type_translations)) {
                EventTypeTranslation::insert($event_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::event-types.store_success_message'), __('toastr.success'));
            return redirect()->route('event-types.index');

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
            $event_type = EventType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'event_type' => $event_type,
                'translations' => $event_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::event-types.event-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::event-types.not_found_message'), __('toastr.error'));
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
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
     * @param EventTypeRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(EventTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $event_type = EventType::whereId($id)->firstOrFail();
            $event_type->code = $request->get('code');
            $event_type->save();

            // Disable previous translations
            EventTypeTranslation::where('event_type_id', $event_type->id)->update(['active' => false]);
            $version = EventTypeTranslation::where('event_type_id', $event_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $event_type_translations[] = [
                    'event_type_id' => $event_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($event_type_translations)) {
                EventTypeTranslation::insert($event_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::event-types.update_success_message'), __('toastr.success'));
            return redirect()->route('event-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::event-types.not_found_message'), __('toastr.error'));
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
            $event_type = EventType::whereId($id)->firstOrFail();
            $event_type->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::event-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('event-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::event-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
