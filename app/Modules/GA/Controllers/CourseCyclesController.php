<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\CourseCycle;
use App\Modules\GA\Models\CourseCycleTranslation;
use App\Modules\GA\Requests\CourseCycleRequest;
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

class CourseCyclesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::course-cycles.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = CourseCycle::join('users as u1', 'u1.id', '=', 'course_cycles.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'course_cycles.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'course_cycles.deleted_by')
                ->leftJoin('course_cycle_translations as cct', function ($join) {
                    $join->on('cct.course_cycles_id', '=', 'course_cycles.id');
                    $join->on('cct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('cct.active', '=', DB::raw(true));
                })
                ->select(['course_cycles.*', 'u1.name as created_by', 'u2.name as updated_by', 'u3.name as deleted_by', 'cct.display_name', 'cct.abbreviation']);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::course-cycles.datatables.actions')->with('item', $item);
                })
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
            return view('GA::course-cycles.course-cycle')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CourseCycleRequest $request
     * @return Response
     */
    public function store(CourseCycleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $course_cycle = CourseCycle::create([
                'code' => $request->get('code')
            ]);
            $course_cycle->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $course_cycle_translations[] = [
                    'course_cycles_id' => $course_cycle->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($course_cycle_translations)) {
                CourseCycleTranslation::insert($course_cycle_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::course-cycles.store_success_message'), __('toastr.success'));
            return redirect()->route('course-cycles.index');

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
            $course_cycle = CourseCycle::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                }
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'course_cycle' => $course_cycle,
                'translations' => $course_cycle->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::course-cycles.course-cycle')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::course-cycles.not_found_message'), __('toastr.error'));
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
     * @param CourseCycleRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(CourseCycleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $course_cycle = CourseCycle::whereId($id)->firstOrFail();
            $course_cycle->code = $request->get('code');
            $course_cycle->save();

            // Disable previous translations
            CourseCycleTranslation::where('course_cycles_id', $course_cycle->id)->update(['active' => false]);

            $version = CourseCycleTranslation::where('course_cycles_id', $course_cycle->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $course_cycle_translations[] = [
                    'course_cycles_id' => $course_cycle->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($course_cycle_translations)) {
                CourseCycleTranslation::insert($course_cycle_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::course-cycles.update_success_message'), __('toastr.success'));
            return redirect()->route('course-cycles.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::course-cycles.not_found_message'), __('toastr.error'));
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
            $course_cycle = CourseCycle::whereId($id)->firstOrFail();
            $course_cycle->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::course-cycles.destroy_success_message'), __('toastr.success'));
            return redirect()->route('course-cycles.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::course-cycles.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
