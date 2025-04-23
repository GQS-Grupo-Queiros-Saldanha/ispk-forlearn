<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use Auth;
use App\Helpers\TimeHelper;
use App\Modules\GA\Models\AccessType;
use App\Modules\GA\Models\Enrollment;
use App\Modules\GA\Models\Student;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Requests\EnrollmentRequest;
use App\Modules\Users\Models\User;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\EnrollmentStateType;
use Log;
use Throwable;
use Toastr;

class EnrollmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('GA::enrollments.index');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = Enrollment::leftJoin('students as s', 's.id', '=', 'enrollments.students_id')
                ->leftJoin('users as u', 'u.id', '=', 's.users_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'enrollments.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'enrollments.updated_by')
                ->leftJoin('study_plan_edition_translations as spet', function($join) {
                    $join->on('spet.study_plan_editions_id', '=', 'enrollments.study_plan_editions_id');
                    $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                })
                ->select([
                    'enrollments.*',
                    'spet.display_name as study_plan_edition',
                    'u.name as student',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::enrollments.datatables.actions')->with('item', $item);
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

            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation'
            ])->get();

            $users = User::whereHas('roles', function($q) {
                $q->where('name', 'student');
            });
            $users = $users->pluck('name', 'id');

            $access_types = AccessType::with([
                'currentTranslation'
            ])->get();
            $access_types = $access_types->pluck('currentTranslation.display_name', 'id');

            $enrollment_state_types = EnrollmentStateType::with([
                'currentTranslation'
            ])->get();
            $enrollment_state_types = $enrollment_state_types->pluck('currentTranslation.display_name', 'id');

            $data = [
                'action' => 'create',
                'access_types' => $access_types,
                'study_plan_editions' => $study_plan_editions,
                'enrollment_state_types' => $enrollment_state_types,
                'users' => $users
            ];
            return view('GA::enrollments.enrollment')->with($data);
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
    public function store(EnrollmentRequest $request)
    {
        // TODO: don't allow same study_plan_edition
        // TODO: don't allow if over the max_enrollments of the study_plan_edition

        try {
            DB::beginTransaction();

            // Create student
            $student = Student::create([
                //'number' => $request->get('number'),
                'users_id' => $request->get('user'),
            ]);

            // Create enrollment
            $enrollment = Enrollment::create([
                'students_id' => $student->id,
                'study_plan_editions_id' => $request->get('study_plan_edition'),
                'access_type_id' => $request->get('access_type'),
                'status' => $request->get('enrollment_state_type'),
                'year' => 0, // TODO
                'candidate_id' => null, // TODO
                'partial_time' => false, // TODO
            ]);

            // State types
            $enrollment->stateTypes()->sync([
                $request->get('enrollment_state_type') => [
                    'explanation' => '', //TODO
                    'created_by' => Auth::user()->id
                ]
            ]);

            // Disciplines
            $disciplines = [];
            if ($request->has('disciplines')) {
                foreach ($request->get('disciplines') as $discipline) {
                    $disciplines[$discipline] = [
                        'status' => $request->get('enrollment_state_type') // TODO: dont know if this is right
                    ];
                }
            }

            // Optional Disciplines
            if ($request->has('optional_disciplines')) {
                foreach ($request->get('optional_disciplines') as $optional_groups_id => $discipline) {
                    $disciplines[$discipline] = [
                        'status' => $request->get('enrollment_state_type'), // TODO: dont know if this is right
                        'optional_groups_id' => $optional_groups_id
                    ];
                }
            }

            if (!empty($disciplines)) {
                $enrollment->disciplines()->sync($disciplines);
            }

            $enrollment->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::enrollments.store_success_message'), __('toastr.success'));
            return redirect()->route('enrollments.index');
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
            $enrollment = Enrollment::whereId($id)->with([
                'accessType',
                'student'
            ])->firstOrFail();

            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation'
            ])->get();

            $users = User::all();
            $users = $users->pluck('name', 'id');

            $user = $enrollment->student; //FIXME: get actual user

            $access_types = AccessType::with([
                'currentTranslation'
            ])->get();
            $access_types = $access_types->pluck('currentTranslation.display_name', 'id');

            $access_type = $enrollment->accessType;

            $enrollment_state_types = EnrollmentStateType::with([
                'currentTranslation'
            ])->get();
            $enrollment_state_types = $enrollment_state_types->pluck('currentTranslation.display_name', 'id');

            $data = [
                'action' => $action,
                'access_type' => $access_type,
                'access_types' => $access_types,
                'enrollment' => $enrollment,
                'enrollment_state_types' => $enrollment_state_types,
                'study_plan_editions' => $study_plan_editions,
                'users' => $users,
                'user' => $user
            ];
            return view('GA::enrollments.enrollment')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::enrollments.not_found_message'), __('toastr.error'));
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
            $enrollment = Enrollment::whereId($id)->firstOrFail();
            $enrollment->stateTypes()->sync([]);
            $enrollment->disciplines()->sync([]);
            $enrollment->student->parameters()->where('enrollments_id', $enrollment->id)->delete();
            $enrollment->save();
            $enrollment->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::enrollments.destroy_success_message'), __('toastr.success'));
            return redirect()->route('enrollments.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::enrollments.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function userAjax($id)
    {
        try {

            $user = User::with([
                'parameters' => function ($q) {
                    $q->with([
                        'roles',
                        'currentTranslation',
                        'groups' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'options'
                    ]);
                }
            ])->findOrFail($id);

            // se esta bosta funcionasse é que era bom -> $parameters = $user->parameters->groupBy(['groups.id']);
            $parameter_groups = [];
            foreach ($user->parameters as $parameter) {
                //foreach ($parameter->groups as $group) {
                // $parameter_groups[$group->id][] = $parameter;
                //}
                $parameter_groups[$parameter->pivot->parameter_group_id][] = $parameter;
            }

            $data = [
                'user' => $user,
                'parameter_groups' => $parameter_groups
            ];

            return view('GA::enrollments.partials.user')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function disciplinesAjax($id)
    {
        try {

            $study_plan_edition = StudyPlanEdition::with([
                'disciplines' => function ($q) {
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'periodType' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                    ])->whereOptional(false);
                },
                'currentTranslation'
            ])->findOrFail($id);

            $data = [
                'study_plan_edition' => $study_plan_edition,
            ];

            return view('GA::enrollments.partials.disciplines')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function optionalDisciplinesAjax($id)
    {
        try {

            $study_plan_edition = StudyPlanEdition::with([
                /* 'disciplines' => function ($q) {
                     $q->with([
                         'discipline' => function ($q) {
                             $q->with([
                                 'currentTranslation'
                             ]);
                         }
                     ])->whereOptional(true);
                 },*/
                'optionalDisciplines',
                'currentTranslation'
            ])->findOrFail($id);

            $data = [
                'study_plan_edition' => $study_plan_edition, //>disciplines->groupBy('year')
            ];

            return view('GA::enrollments.partials.optional-disciplines')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
}
