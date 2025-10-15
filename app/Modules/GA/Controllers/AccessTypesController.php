<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\AccessType;
use App\Modules\GA\Models\AccessTypeTranslation;
use App\Modules\GA\Requests\AccessTypeRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;

class AccessTypesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::access-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = AccessType::join('users as u1', 'u1.id', '=', 'access_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'access_types.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'access_types.deleted_by')
                ->leftJoin('access_type_translations as att', function ($join) {
                    $join->on('att.access_type_id', '=', 'access_types.id');
                    $join->on('att.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('att.active', '=', DB::raw(true));
                })
                ->select([
                    'access_types.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'att.display_name',
                ]);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::access-types.datatables.actions')->with('item', $item);
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
            return view('GA::access-types.access-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Modules\GA\Controllers\AccessTypeRequest $request
     * @return Response
     */
    public function store(AccessTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $access_type = AccessType::create([
                'code' => $request->get('code'),
            ]);
            $access_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $access_type_translations[] = [
                    'access_type_id' => $access_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($access_type_translations)) {
                AccessTypeTranslation::insert($access_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::access-types.store_success_message'), __('toastr.success'));
            return redirect()->route('access-types.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $access_type = AccessType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'access_type' => $access_type,
                'translations' => $access_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::access-types.access-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::access-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $access_type = AccessType::whereId($id)->firstOrFail();
            $access_type->code = $request->get('code');
            $access_type->save();

            // Disable previous translations
            AccessTypeTranslation::where('access_type_id', $access_type->id)->update(['active' => false]);

            $version = AccessTypeTranslation::where('access_type_id', $access_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $access_type_translations[] = [
                    'access_type_id' => $access_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($access_type_translations)) {
                AccessTypeTranslation::insert($access_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::access-types.update_success_message'), __('toastr.success'));
            return redirect()->route('access-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::access-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $access_type = AccessType::whereId($id)->firstOrFail();
            $access_type->delete();

            $access_type->deleted_by = Auth::user()->id;
            $access_type->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::access-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('access-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::access-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
