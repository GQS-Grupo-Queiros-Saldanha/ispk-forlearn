<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\DurationType;
use App\Modules\GA\Models\DurationTypeTranslation;
use App\Modules\GA\Requests\DurationTypeRequest;
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

class DurationTypesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::duration-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = DurationType::join('users as u1', 'u1.id', '=', 'duration_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'duration_types.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'duration_types.deleted_by')
                ->leftJoin('duration_type_translations as dtt', function ($join) {
                    $join->on('dtt.duration_types_id', '=', 'duration_types.id');
                    $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dtt.active', '=', DB::raw(true));
                })
                ->select(['duration_types.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'dtt.display_name']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::duration-types.datatables.actions')->with('item', $item);
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
            return view('GA::duration-types.duration-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DurationTypeRequest $request
     * @return Response
     */
    public function store(DurationTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $duration_type = DurationType::create([
                'code' => $request->get('code')
            ]);
            $duration_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $duration_type_translations[] = [
                    'duration_types_id' => $duration_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($duration_type_translations)) {
                DurationTypeTranslation::insert($duration_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::duration-types.store_success_message'), __('toastr.success'));
            return redirect()->route('duration-types.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $duration_type = DurationType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'duration_type' => $duration_type,
                'translations' => $duration_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::duration-types.duration-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::duration-types.not_found_message'), __('toastr.error'));
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
     * @param DurationTypeRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DurationTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $duration_type = DurationType::whereId($id)->firstOrFail();
            $duration_type->code = $request->get('code');
            $duration_type->save();

            // Disable previous translations
            DurationTypeTranslation::where('duration_types_id', $duration_type->id)->update(['active' => false]);

            $version = DurationTypeTranslation::where('duration_types_id', $duration_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $duration_type_translations[] = [
                    'duration_types_id' => $duration_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($duration_type_translations)) {
                DurationTypeTranslation::insert($duration_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::duration-types.update_success_message'), __('toastr.success'));
            return redirect()->route('duration-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::duration-types.not_found_message'), __('toastr.error'));
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
            $duration_type = DurationType::whereId($id)->firstOrFail();
            $duration_type->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::duration-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('duration-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::duration-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
