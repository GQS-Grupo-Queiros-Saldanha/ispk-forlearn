<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\EnrollmentStateType;
use App\Modules\GA\Models\EnrollmentStateTypeTranslation;
use App\Modules\GA\Requests\EnrollmentStateTypeRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use Throwable;
use Toastr;

class EnrollmentStateTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('GA::enrollment-state-types.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = EnrollmentStateType::join('users as u1', 'u1.id', '=', 'enrollment_state_types.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'enrollment_state_types.updated_by')
                ->leftJoin('enrollment_state_type_translations as estt', function ($join) {
                    $join->on('estt.enrollment_state_types_id', '=', 'enrollment_state_types.id');
                    $join->on('estt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('estt.active', '=', DB::raw(true));
                })
                ->select([
                    'enrollment_state_types.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'estt.display_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::enrollment-state-types.datatables.actions')->with('item', $item);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::enrollment-state-types.enrollment-state-type')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EnrollmentStateTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $enrollment_state_type = EnrollmentStateType::create([
                'code' => $request->get('code')
            ]);
            $enrollment_state_type->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $enrollment_state_type_translations[] = [
                    'enrollment_state_types_id' => $enrollment_state_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($enrollment_state_type_translations)) {
                EnrollmentStateTypeTranslation::insert($enrollment_state_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::enrollment-state-types.store_success_message'), __('toastr.success'));
            return redirect()->route('enrollment-state-types.index');

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
            $enrollment_state_type = EnrollmentStateType::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'enrollment_state_type' => $enrollment_state_type,
                'translations' => $enrollment_state_type->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::enrollment-state-types.enrollment-state-type')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::enrollment-state-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
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
     * @param EnrollmentStateTypeRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(EnrollmentStateTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $enrollment_state_type = EnrollmentStateType::whereId($id)->firstOrFail();
            $enrollment_state_type->code = $request->get('code');
            $enrollment_state_type->save();

            // Disable previous translations
            EnrollmentStateTypeTranslation::where('enrollment_state_types_id', $enrollment_state_type->id)->update(['active' => false]);

            $version = EnrollmentStateTypeTranslation::where('enrollment_state_types_id', $enrollment_state_type->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $enrollment_state_type_translations[] = [
                    'enrollment_state_types_id' => $enrollment_state_type->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($enrollment_state_type_translations)) {
                EnrollmentStateTypeTranslation::insert($enrollment_state_type_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::enrollment-state-types.update_success_message'), __('toastr.success'));
            return redirect()->route('enrollment-state-types.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::enrollment-state-types.not_found_message'), __('toastr.error'));
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $enrollment_state_type = EnrollmentStateType::whereId($id)->firstOrFail();
            $enrollment_state_type->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::enrollment-state-types.destroy_success_message'), __('toastr.success'));
            return redirect()->route('enrollment-state-types.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::enrollment-state-types.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
