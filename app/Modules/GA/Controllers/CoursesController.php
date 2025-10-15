<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\CourseCycle;
use App\Modules\GA\Models\CourseRegime;
use App\Modules\GA\Models\CourseTranslation;
use App\Modules\GA\Models\Degree;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\DurationType;
use App\Modules\GA\Requests\CourseRequest;
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

class CoursesController extends Controller
{

    public function index()
    {
        try {
            return view('GA::courses.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Course::join('users as u1', 'u1.id', '=', 'courses.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'courses.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'courses.deleted_by')
                ->leftJoin('duration_type_translations as dtt', function ($join) {
                    $join->on('dtt.duration_types_id', '=', 'courses.duration_types_id');
                    $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dtt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'courses.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('department_translations as dt', function ($join) {
                    $join->on('dt.departments_id', '=', 'courses.departments_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })                 
                ->select([
                    'courses.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name',
                    'dt.display_name as department_name',
                    'ct.abbreviation',
                    DB::raw('CONCAT(courses.duration_value, " ", dtt.display_name) as duration')
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::courses.datatables.actions')->with('item', $item);
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
                ->addIndexColumn()
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {

            $departments = Department::with([
                'currentTranslation'
            ])->get();

            $course_cycles = CourseCycle::with([
                'currentTranslation'
            ])->get();

            $course_regimes = CourseRegime::with([
                'currentTranslation'
            ])->get();

            $degrees = Degree::with([
                'currentTranslation'
            ])->get();

            $duration_types = DurationType::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => 'create',
                'departments' => $departments,
                'course_cycles' => $course_cycles,
                'course_regimes' => $course_regimes,
                'degrees' => $degrees,
                'duration_types' => $duration_types,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::courses.course')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CourseRequest $request
     * @return Response
     */
    public function store(CourseRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $course = new Course([
                'code' => $request->get('code'),
                'numeric_code' => $request->get('numeric_code'),
                'duration_value' => $request->get('duration_value'),
                'is_special' => $request->get('is_special') ? 1 : 0
            ]);

            // Associations
            $course->department()->associate($request->get('department'));
            $course->course_cycle()->associate($request->get('course_cycle'));
            $course->degree()->associate($request->get('degree'));
            $course->duration_type()->associate($request->get('duration_type'));
            $course->save();
            $course->course_regimes()->sync($request->get('course_regimes'));
            $course->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {

                $course_translations[] = [
                    'courses_id' => $course->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($course_translations)) {
                CourseTranslation::insert($course_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::courses.store_success_message'), __('toastr.success'));
            return redirect()->route('courses.index');

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {

            $departments = Department::with([
                'currentTranslation'
            ])->get();

            $course_cycles = CourseCycle::with([
                'currentTranslation'
            ])->get();

            $course_regimes = CourseRegime::with([
                'currentTranslation'
            ])->get();

            $degrees = Degree::with([
                'currentTranslation'
            ])->get();

            $duration_types = DurationType::with([
                'currentTranslation'
            ])->get();

            // Find
            $course = Course::whereId($id)->with(
                [
                    'department' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'course_cycle' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'course_regimes' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'degree' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'duration_type' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                ])->firstOrFail();

            

            // Set relation keys
            $data = [
                'action' => $action,
                'course' => $course,
                'departments' => $departments,
                'course_cycles' => $course_cycles,
                'course_regimes' => $course_regimes,
                'degrees' => $degrees,
                'duration_types' => $duration_types,
                'translations' => $course->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::courses.course')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::courses.not_found_message'), __('toastr.error'));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
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
     * @param CourseRequest $request
     * @param int $id
     * @return Response
     */
    public function update(CourseRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find
            $course = Course::whereId($id)->firstOrFail();
           
            // Update
            $course->code = $request->get('code');
            $course->numeric_code = $request->get('numeric_code');
            $course->duration_value = $request->get('duration_value');
            $course->is_special = $request->get('is_special') ? 1 : 0;

            // Delete all relations
            $course->department()->dissociate();
            $course->degree()->dissociate();
            $course->course_cycle()->dissociate();
            $course->duration_type()->dissociate();

            // Associate
            $course->department()->associate($request->get('department'));
            $course->degree()->associate($request->get('degree'));
            $course->course_cycle()->associate($request->get('course_cycle'));
            $course->course_regimes()->sync($request->get('course_regimes'));
            $course->duration_type()->associate($request->get('duration_type'));

            // Disable previous translations
            CourseTranslation::where('courses_id', $course->id)->update(['active' => false]);

            $version = CourseTranslation::where('courses_id', $course->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $course_translations[] = [
                    'courses_id' => $course->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($course_translations)) {
                CourseTranslation::insert($course_translations);
            }

            DB::commit();

            $course->save();

            // Success message
            Toastr::success(__('GA::courses.update_success_message'), __('toastr.success'));
            return redirect()->route('courses.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::courses.not_found_message'), __('toastr.error'));
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
           
            // return "apagando o curso";
            DB::beginTransaction();

            // Find and delete
            $course = Course::whereId($id)->firstOrFail();

            // Delete all relations
            /*
            $course->department()->dissociate();
            $course->degree()->dissociate();
            $course->course_cycle()->dissociate();
            $course->course_regimes()->sync([]);
            $course->duration_type()->dissociate();
            */
            $course->update([
                'deleted_at' => now(),
                'deleted_by' => auth()->user()->id,
            ]);
            
            DB::commit();

            // Success message
            Toastr::success(__('GA::courses.destroy_success_message'), __('toastr.success'));
            return redirect()->route('courses.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::courses.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function roles($id)
    {
        try {
            //Find
            $course = Course::whereId($id)->firstOrFail();
            $data = [
                'course' => $course
            ];
            return view('Courses::courses.roles')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function rolesAjax($id)
    {

        try {
            // Fetch the course
            $course = Course::whereId($id)->firstOrFail();
            // Get ids
            $roles = $course->roles->pluck('id')->toArray();

            // Build query
            $query = Role::query()->with(['translations' => function ($q) {
                $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            }]);

            // Return the datatable
            return Datatables::eloquent($query)
                // Deal with extra cloumns [not in model]
                ->addColumn('display_name', function ($item) {
                    return $item->translations->first()->display_name ?? '';
                })
                ->addColumn('select', function ($item) use ($roles) {
                    return view('Courses::courses.datatables.select', ['id' => $item->id, 'checked' => in_array($item->id, $roles, true)]);
                })
                ->rawColumns(['select'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function rolesSave(Request $request, $id)
    {
        try {
            // Find
            $course = Course::whereId($id)->firstOrFail();

            $course->syncRoles($request->get('items'));

            // Success message
            Toastr::success(__('GA::courses.roles_success_message'), __('toastr.success'));
            return redirect()->route('courses.roles', $course->id);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::courses.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissions($id)
    {
        try {
            // Find
            $course = Course::whereId($id)->firstOrFail();

            $data = [
                'course' => $course
            ];

            return view('Courses::courses.permissions')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissionsAjax($id)
    {
        try {
            // Fetch the course
            $course = Course::whereId($id)->firstOrFail();

            // Get ids
            $permissions = $course->permissions->pluck('id')->toArray();

            // Build query
            $query = Permission::query()->with(['translations' => function ($q) {
                $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            }]);

            // Return the datatable
            return Datatables::eloquent($query)
                // Deal with extra cloumns [not in model]
                ->addColumn('display_name', function ($item) {
                    return $item->translations->first()->display_name ?? '';
                })
                ->addColumn('select', function ($item) use ($permissions) {
                    return view('Courses::courses.datatables.select', ['id' => $item->id, 'checked' => in_array($item->id, $permissions, true)]);
                })
                ->rawColumns(['select'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function permissionsSave(Request $request, $id)
    {
        try {
            // Find
            $course = Course::whereId($id)->firstOrFail();

            $course->syncPermissions($request->get('items'));

            // Success message
            Toastr::success(__('GA::courses.permissions_success_message'), __('toastr.success'));
            return redirect()->route('courses.permissions', $course->id);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::courses.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
