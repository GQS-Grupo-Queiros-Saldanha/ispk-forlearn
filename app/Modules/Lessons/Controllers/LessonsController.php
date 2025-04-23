<?php

namespace App\Modules\Lessons\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleEvent;
use App\Modules\GA\Models\ScheduleTypeTime;
use App\Modules\GA\Models\Summary;
//use App\Modules\GA\Models\Discipline;
use App\Modules\Lessons\Models\Lessons;
use App\Modules\Lessons\Requests\LessonRequest;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Users\Models\Matriculation;
use App\Modules\GA\Models\LectiveYear;
use Carbon\Traits\Date;
use Exception;
use Illuminate\Support\Facades\Auth;
use Toastr;
use Illuminate\Support\Facades\Validator;
use PDF;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use App\Model\Institution;

class LessonsController extends Controller
{
    public function index()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];
            
            
            $auth = auth()->user();
            
            if ($auth->hasRole('student')) {
                return redirect()->route('summaryStudent');
                
                //return view("Lessons::index")->with($data);
            }
            else {
                return view("Lessons::index")->with($data);
            }
            
        } catch (\Exception $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create()
    {
        try {
            $teachers = User::whereHas('roles', function ($q) {
                $q->where('id', 1);
            })
                ->with(['parameters' => function ($q) {
                    $q->whereIn('code', ['nome', 'n_mecanografico']);
                }])
                ->get()
                ->map(function ($user) {
                    $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
                    $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
                        $fullNameParameter->pivot->value : $user->name;

                    $teacherNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
                    $teacherNumber = $teacherNumberParameter && $teacherNumberParameter->pivot->value ?
                        $teacherNumberParameter->pivot->value : "000";

                    $displayName = "$fullName #$teacherNumber ($user->email)";
                    return ['id' => $user->id, 'display_name' => $displayName];
                })
                ->sortBy(function ($item) {
                    return strtr(
                        utf8_decode($item['display_name']),
                        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
                    );
                })
                ->values();

            $data = [
                'action' => 'create',
                'teachers' => $teachers,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Lessons::lesson-create')->with($data);
        } catch (\Exception  $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxDisciplines()
    {
        try {
            $teacherId = request()->has('teacher') ? (int)request()->get('teacher') : null;
            $date = request()->has('date') ? Carbon::createFromTimestamp(request()->get('date')) : null;

            $teacher = User::where('id', $teacherId)
                ->with('disciplines')
                ->firstOrFail();

            // return $teacher;
            // return response()->json($teacher);

            $teacherDisciplines = $teacher->disciplines->pluck('id')->all();

            $date = $date ?: Carbon::now();

            $lowerLimit = $date->subMinutes(45)->format('H:i:s');
            $upperLimit = $date->addMinutes(100)->format('H:i:s');
            
            //return [$date->dayOfWeekIso, $teacherDisciplines];
            
            $year = Date('Y') - 1;

            $events = ScheduleEvent::where('day_of_the_week_id', $date->dayOfWeekIso)
                // ->whereHas('time', function ($q) use ($lowerLimit, $upperLimit) {
                //     $q->where('start', '>=', $lowerLimit)->where('end', '<', $upperLimit);
                // })
                ->whereHas('discipline', function ($q) use ($teacherDisciplines) {
                    $q->whereIn('id', $teacherDisciplines);
                })
                ->with([
                    'discipline' => function ($q) {
                        $q->with(['currentTranslation']);
                    },
                    'schedule' => function ($q) {
                        $q->with([
                            'class'
                            // => function ($q){
                            //     $q->whereYear('classes.created_at', Date('Y'));
                            // }
                        ]);
                    },
                ])
                ->whereYear('created_at', $year)
                ->get();
                
            //return $events;

            $possibleDisciplines = $events->map(function ($item, $key) {
                $d = $item->discipline;
                return [
                    'id' => $d->id . '-' . $item->schedule->discipline_class_id,
                    'display_name' => $d->code . ' - ' .
                        $d->currentTranslation->display_name .
                        ' (' . $item->schedule->class->display_name . ')'
                ];
            });

            $data = [
                'disciplines' => $possibleDisciplines
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxDisciplineClassData()
    {
        try {
            //return request()->get('discipline');  //request()->has('discipline') ? (int)request()->get('discipline') : null;
            
            $disciplineId = request()->has('discipline') ? (int)request()->get('discipline') : null;
            $classId = request()->has('class') ? (int)request()->get('class') : null;

            if (!$disciplineId || !$classId) {
                return null;
            }


            $disciplineClassStudents = Classes::where('id', $classId)
                ->with([
                    'matriculations' => function ($q) use ($disciplineId) {
                        $q
                            ->whereHas('disciplines', function ($q) use ($disciplineId) {
                                $q->where('id', $disciplineId);
                            })
                            ->with([
                                'user' => function ($q) {
                                    $q->with(['parameters' => function ($q) {
                                        $q->whereIn('code', ['nome', 'n_mecanografico']);
                                    }]);
                                }
                            ]);
                        // ->where('matriculations.lective_year', 7);
                        // ->whereYear('matriculations.created_at', Date('Y'));
                    }
                ])
                // ->where('classes.lective_year_id', 7)
            ->first();

            $students = $disciplineClassStudents ?
                $disciplineClassStudents->matriculations
                ->map(function ($item, $key) {
                    return $item->user;
                })
                ->sortBy(function ($item) {
                    return strtr(
                        utf8_decode($item['name']),
                        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
                    );
                })
                ->values() : null;

            // return $students[0];
            
            
            

            $alreadyGivenSummariesForDisciplineClass = Discipline::where('id', $disciplineId)
            //->where('class_id', $classId)
            ->with([
                    'study_plans_has_disciplines' => function ($q) {
                        $q->with([
                            'study_plans_has_discipline_regimes'=> function ($q) {
                                $q->with([
                                    'discipline_regime'
                                ])->get()->last();
                            }
                        ]);
                    }
                ])
            
            ->get();
            
            
            
            /*
            $alreadyGivenSummariesForDisciplineClass = Lessons::where('discipline_id', $disciplineId)
                ->where('class_id', $classId)
                ->pluck('summary_id')
                ->all();
            */

            //return Summary::where('discipline_id', $disciplineId)
            //->get();
            
            
            $ungivenDisciplineSummaries = Summary::where('discipline_id', $disciplineId)
                ->whereNotIn('id', $alreadyGivenSummariesForDisciplineClass)
                ->with([
                    'currentTranslation',
                    'regime' => function ($q) {
                        $q->with(['currentTranslation']);
                    },
                ])
                ->get();
                
                
                
            

            $data = [
                'students' => $students,
                'regimes' => [],
                'summaries' => []
            ];
            
            $data['regimes'] = $alreadyGivenSummariesForDisciplineClass[0]->study_plans_has_disciplines[0]->study_plans_has_discipline_regimes; 
            
            
            
            

            $ungivenDisciplineSummaries
                ->sortBy('order')
                ->groupBy('discipline_regime_id')
                ->each(function ($item, $key) use (&$data) {
                    $summary = $item->first();
                    $summary->{'regime_id'} = $summary->regime->id;

                    $data['regimes'][] = $summary->regime;
                    $data['summaries'][] = $summary->unsetRelation('regime');
                })
                ->values();

            return response()->json($data);
        } catch (\Exception $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function store(LessonRequest $request)
    {
        try {
            DB::beginTransaction();

            $disciplineClass = explode('-', $request->get('discipline'));

            $lesson = new Lessons([
                'teacher_id' => $request->get('teacher'),
                'discipline_id' => $disciplineClass[0],
                'class_id' => $disciplineClass[1],
                'regime_id' => $request->get('regime'),
                'summary_id' => $request->get('summary'),
                'occured_at' => Carbon::parse($request->get('occured_at')),
                'observations' => $request->get('observation')
            ]);

            $lesson->save();

            $attendance = $request->get('attendance');
            if (is_array($attendance) && count($attendance)) {
                $lesson->students()->sync($attendance);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Lessons::lessons.store_success_message'), __('toastr.success'));
            return redirect()->route('lessons.index');
        } catch (\Exception $e) {
            logError($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }

    public function fetch($id, $action)
    {
        try {
            $lesson = Lessons::where('id', $id)
                ->with([
                    'teacher',
                    'discipline' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'class',
                    'regime' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'summary' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'students'
                ])
                ->firstOrFail();

            if (!auth()->user()->can('manage-lessons-others') && (int)$lesson->teacher_id !== (int)auth()->id()) {
                return redirect()->back();
            }

            $data = [
                'lesson' => $lesson,
                'attendance' => $lesson->students->pluck('id'),
                'action' => $action,
            ];

            return view('Lessons::lesson')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (\Exception $e) {
            logError($e);
            return abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (\Exception $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function edit($id)
    {
        try {
            return $this->fetch($id, 'edit');
        } catch (\Exception $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'summary' => 'required|numeric',
            'attendance' => 'sometimes|required|array',
        ]);

        try {
            DB::beginTransaction();

            $lesson = Lessons::findOrFail($id);

            $attendance = $request->get('attendance');
            if (is_array($attendance) && count($attendance)) {
                $lesson->students()->sync($attendance);
            } else {
                $lesson->students()->sync([]);
            }

            DB::commit();

            // Success message
            Toastr::success(__('Lessons::lessons.update_success_message'), __('toastr.success'));
            return redirect()->route('lessons.show', $id);
        } 
        // catch (ModelNotFoundException $e) {
        //     Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
        //     logError($e);
        //     return redirect()->back() ?? abort(500);
        // } 
        catch (\Exception $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function delete($id)
    {
        //
        try{ 
            // DB::table('lessons')->delete($id);
            
            Toastr::success(__('Lessons::lessons.destroy_success_message'), __('toastr.success'));
            return redirect()->route('lessons.index');

        } catch (\Exception $e) {
            logError($e);
            Toastr::error(__('Grades::grades.destroy_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }

    public function ajax($lective_year)
    {
        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $lective_year)
            // ->first()
            ->get();

            $model = Lessons::join('users as teacher', 'teacher.id', '=', 'lessons.teacher_id')
                ->join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'lessons.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->join('classes as c', 'c.id', '=', 'lessons.class_id')
                ->join('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'lessons.regime_id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->join('summary_translations as st', function ($join) {
                    $join->on('st.summaries_id', '=', 'lessons.summary_id');
                    $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('st.active', '=', DB::raw(true));
                })
                ->select([
                    'lessons.*',
                    'teacher.name as teacher',
                    'dt.display_name as discipline',
                    'c.display_name as class',
                    'drt.display_name as regime',
                    'st.display_name as summary',
                ])
                ->whereBetween('lessons.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);

            if (!auth()->user()->can('manage-lessons-others')) {
                $model = $model->where('lessons.teacher_id', auth()->id());
            }

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Lessons::datatables.actions')->with('item', $item);
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
        } catch (\Exception $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function checkUser()
    {

        $user = User::whereId(Auth::user()->id)->firstOrFail();

        if ($user->hasAnyRole(['teacher'])) {

            return redirect()->route('attendanceByTeacher');
        } elseif ($user->hasAnyRole(['student'])) {

            return redirect()->route('attendance');
        } elseif ($user->hasAnyRole([
            'superadmin',
            'staff_forlearn',
            'staff_gabinete_termos',
            'chefe-departamento-staff'
        ])) {

            return redirect()->route('attendanceByStaff');
        } else {
            return redirect()->back();
        }
    }

    public function attendanceByTeacher()
    {
        $user = User::whereId(Auth::user()->id)->firstOrFail();

        if (!$user->hasAnyRole(['teacher'])) {
            return redirect()->back();
        }

        $teacher_id = Auth::user()->id; //57;

        $user = User::whereId($teacher_id)->with([
            'disciplines',
            'classes'
        ])->firstOrFail();

        $discipline_pluck = $user->disciplines->pluck('id');

        $disciplines = Discipline::join('disciplines_translations as dt', function ($join) {
            $join->on('dt.discipline_id', '=', 'disciplines.id');
            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dt.active', '=', DB::raw(true));
        })
            ->whereIn('disciplines.id', $discipline_pluck)
            ->select('disciplines.id as id', 'dt.display_name as display_name')
            ->get()
            ->map(function ($discipline) {
                return ['id' => $discipline->id, 'display_name' => $discipline->display_name];
            });

        return view('Lessons::presenceByTeacher', compact('user', 'disciplines'));
    }

    public function getClasses($discipline_id)
    {
        $teacher_id = Auth::user()->id; //57;

        $discipline = Discipline::whereId($discipline_id)->firstOrFail();

        $classes = Classes::join('user_classes as uc', 'uc.class_id', '=', 'classes.id')
            ->where('uc.user_id', $teacher_id)
            ->where('classes.courses_id', $discipline->courses_id)
            ->get()
            ->map(function ($class) {
                return ['id' => $class->id, 'display_name' => $class->display_name];
            });

        return response()->json($classes);
    }

    public function getStudents($discipline_id, $classes)
    {
        $discipline = Discipline::whereId($discipline_id)->firstOrFail();
        $students = Discipline::leftJoin('matriculation_disciplines as md', 'md.discipline_id', '=', 'disciplines.id')
            ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'md.matriculation_id')
            ->leftJoin('matriculations as mt', 'mt.id', '=', 'mc.matriculation_id')
            ->leftJoin('users', 'users.id', '=', 'mt.user_id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
            })
            ->leftJoin('lesson_attendance', 'lesson_attendance.student_id', '=', 'users.id')
            ->select('users.*', 'u_p.value as name', DB::raw('count(*) as total, lesson_attendance.student_id'))
            ->where('mc.class_id', $classes)
            ->where('disciplines.id', $discipline_id)
            ->whereYear('mt.created_at', Date('Y'))
            ->orderBy('u_p.value', 'ASC')
            ->groupBy('users.id')
            ->get();


        $totalLessons =  Lessons::select('lessons.id as id', 'lessons.discipline_id as discipline_id', DB::raw('count(*) as total'))
            ->where('lessons.discipline_id', $discipline_id)
            ->where('lessons.class_id', $classes)
            ->whereYear('lessons.created_at', Date('Y'))
            ->firstOrFail();

        $totalAbsence = Lessons::leftJoin('lesson_attendance', 'lesson_attendance.lesson_id', '=', 'lessons.id')
            ->select('id', 'student_id', 'discipline_id')
            ->where('lessons.discipline_id', $discipline_id)
            ->where('lessons.class_id', $classes)
            ->whereYear('lessons.created_at', Date('Y'))
            ->distinct()
            ->get();

        $data = [
            'discipline' => $discipline,
            'students' => $students,
            'totalLessons' => $totalLessons,
            'totalAbsence' => $totalAbsence
        ];
        return response()->json($data);
    }

    public function attendance()
    {
        $user = User::whereId(Auth::user()->id)->firstOrFail();
        if (!$user->hasAnyRole(['student'])) {
            return redirect()->back();
        }

        $id = Auth::user()->id; //4579;

        $user = User::whereId($id)->with([
            'matriculation' => function ($q) {
                $q->with([
                    'disciplines'
                ]);
            },
            'courses' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            },
            'classes',
            'parameters'
        ])->firstOrFail();

        if (!$user->matriculation) {
            Toastr::error(__('Infelizmente o discente não possui uma matrícula para este ano lectivo'), __('toastr.error'));
            return redirect()->back();
        }
        else {
            // return 3525;
            $discipline = $user->matriculation->disciplines->pluck('id')->all();
        }

        $totalLessons =  Lessons::join('disciplines_translations as dt', function ($join) {
            $join->on('dt.discipline_id', '=', 'lessons.discipline_id');
            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dt.active', '=', DB::raw(true));
        })
            ->select('lessons.id as id', 'lessons.discipline_id as discipline_id', 'dt.display_name as name', DB::raw('count(*) as total'), 'lessons.regime_id as regime_id')
            ->groupBy('discipline_id')
            ->whereIn('lessons.discipline_id', $discipline)
            ->whereYear('lessons.created_at', Date('Y'))
            ->get();

        $totalPresences = Lessons::leftJoin('lesson_attendance', 'lesson_attendance.lesson_id', '=', 'lessons.id')
            ->select('id', 'discipline_id', DB::raw('count(*) as total'))
            ->where('student_id', $id)
            ->whereYear('lessons.created_at', Date('Y'))
            ->groupBy('discipline_id')
            ->groupBy('regime_id')
            ->get();

        $totalByRegimes = Lessons::join('discipline_regimes as dr', 'dr.id', '=', 'lessons.regime_id')
            ->select('lessons.id as id', 'lessons.discipline_id as discipline_id', DB::raw('count(*) as total'), 'lessons.regime_id as regime_id', 'dr.code as code')
            ->whereIn('lessons.discipline_id', $discipline)
            ->whereYear('lessons.created_at', Date('Y'))
            ->groupBy('discipline_id')
            ->groupBy('regime_id')
            ->get();

        return view('Lessons::presence', [
            'user' => $user,
            'totalLessons' => $totalLessons,
            'totalPresences' => $totalPresences,
            'totalByRegimes' => $totalByRegimes
        ]);
    }

    public function attendanceByStaff()
    {
        try {
            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            $data = [
                'lectiveYearSelected' => $lectiveYearSelected,
                'lectiveYears' => $lectiveYears
            ];
            $user = User::whereId(Auth::user()->id)->firstOrFail();
            if (!$user->hasAnyRole([
                'superadmin',
                'staff_forlearn',
                'staff_gabinete_termos',
                'chefe-departamento-staff'
            ])) {
                return redirect()->back();
            }
            return view('Lessons::presenceByStaff')->with($data);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
        }
    }

    public function getAllCourses()
    {
        $courses = Course::with([
            'currentTranslation'
        ])->get();

        return response()->json($courses);
    }

    public function getAllDisciplines($course_id)
    {
        try {
            $disciplines = Discipline::with([
                'currentTranslation'
            ])->whereCoursesId($course_id)
                ->get()
                ->map(function ($discipline) {
                    return ['id' => $discipline->id, 'display_name' => '#' . $discipline->code . ' - ' . $discipline->currentTranslation->display_name];
                });

            return response()->json($disciplines);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
        }
    }

    public function getAllStudents($discipline_id, $lective_year)
    {
        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $lective_year)
            // ->first()
            ->get();

            $students = User::join('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->join('matriculation_disciplines', 'matriculations.id', '=', 'matriculation_disciplines.matriculation_id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->select('users.id as id', 'u_p.value as user_name')
                ->where('matriculation_disciplines.discipline_id', $discipline_id)
                // ->whereYear('matriculations.created_at', Date('Y'))
                // ->whereBetween('matriculations.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])  
                ->where('matriculations.lective_year', $lectiveYearSelected[0]->id)
                ->orderBy('user_name')
                ->get()
                ->map(function ($student) {
                    return ['id' => $student->id, 'display_name' => $student->user_name];
                });

            return response()->json($students);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
        }
    }

    public function getAttendance($student_id, $discipline_id, $lective_year)
    {
        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $lective_year)
            // ->first()
            ->get();
            
            // return $lectiveYearSelected[0]->start_date.date('Y');

            $user = User::whereId($student_id)->with([
                'matriculation' => function ($q) {
                    $q->with([
                        'disciplines'
                    ]);
                },
                'courses' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'classes',
                'parameters'
            ])->firstOrFail();

            $totalLessons =  Lessons::join('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'lessons.discipline_id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })->join('disciplines', 'disciplines.id', '=', 'lessons.discipline_id')
                ->select('lessons.id as id', 'lessons.discipline_id as discipline_id', 'dt.display_name as name', DB::raw('count(*) as total'), 'lessons.regime_id as regime_id', 'disciplines.maximum_absence as maximum_absence')
                ->where('lessons.discipline_id', $discipline_id)
                // ->whereYear('lessons.created_at', Date('Y'))
                ->whereBetween('lessons.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])  
                ->groupBy('discipline_id')
                ->get();

            $totalPresences = Lessons::leftJoin('lesson_attendance', 'lesson_attendance.lesson_id', '=', 'lessons.id')
                ->select('id', 'discipline_id', DB::raw('count(*) as total'))
                ->where('student_id', $student_id)
                // ->whereYear('lessons.created_at', Date('Y'))
                ->whereBetween('lessons.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]) 
                ->groupBy('discipline_id')
                ->groupBy('regime_id')
                ->get();

            $totalByRegimes = Lessons::join('discipline_regimes as dr', 'dr.id', '=', 'lessons.regime_id')
                ->select('lessons.id as id', 'lessons.discipline_id as discipline_id', DB::raw('count(*) as total'), 'lessons.regime_id as regime_id', 'dr.code as code')
                ->where('lessons.discipline_id', $discipline_id)
                // ->whereYear('lessons.created_at', Date('Y'))
                ->whereBetween('lessons.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]) 
                ->groupBy('discipline_id')
                ->groupBy('regime_id')
                ->get();

            $data = [
                'user' => $user,
                'totalLessons' => $totalLessons,
                'totalPresences' => $totalPresences,
                'totalByRegimes' => $totalByRegimes
            ];
            return response()->json($data);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
        }
    }

    public function generatePDF($id)
    {
        try {
            $lesson = Lessons::where('id', $id)
                ->with([
                    'teacher',
                    'discipline' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'class',
                    'regime' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'summary' => function ($q) {
                        $q->with('currentTranslation');
                    },
                    'students'
                ])
                ->firstOrFail();

            if (!auth()->user()->can('manage-lessons-others') && (int)$lesson->teacher_id !== (int)auth()->id()) {
                return redirect()->back();
            }

            $institution = Institution::latest()->first();

            $students = $this->GetStudentListAtendence($lesson);

            $data = [
                'lesson' => $lesson,
                'attendance' => $lesson->students->pluck('id'),
                'institution' => $institution,
                'students' => $students
            ];

            // return $data;            

            // return view('Lessons::pdf')->with($data);

            $pdf = PDF::loadView('Lessons::pdf', $data);

            $pdf->setOption('margin-top', '2mm');
            $pdf->setOption('margin-left', '2mm');
            $pdf->setOption('margin-bottom', '13mm');
            $pdf->setOption('margin-right', '2mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('header-html', '<header></header>');
            $pdf->setPaper('a4');

            $footer_html = view()->make('Reports::partials.enrollment-income-footer')->with($data)->render();
            $pdf->setOption('footer-html', $footer_html);
            return $pdf->stream($lesson->id . '.pdf');
        } catch (ModelNotFoundException $e) {
            //Toastr::error(__('Users::matriculations.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (\Exception $e) {
            logError($e);
            return $e;
            return abort(500);
        }
    }

    private function GetStudentListAtendence($lesson)
    {
        $classId = $lesson->class_id;
        $disciplineId = $lesson->discipline_id;

        $disciplineClassStudents = Classes::where('id', $classId)
            ->with([
                'matriculations' => function ($q) use ($disciplineId) {
                    $q
                        ->whereHas('disciplines', function ($q) use ($disciplineId) {
                            $q->where('id', $disciplineId);
                        })
                        ->with([
                            'user' => function ($q) {
                                $q->with(['parameters' => function ($q) {
                                    $q->whereIn('code', ['nome', 'n_mecanografico']);
                                }]);
                            }
                        ]);
                    // ->where('matriculations.lective_year', 7);
                    // ->whereYear('matriculations.created_at', Date('Y'));
                }
            ])
            // ->where('classes.lective_year_id', 7)
            ->first();

        $students = $disciplineClassStudents ?
            $disciplineClassStudents->matriculations
            ->map(function ($item, $key) {
                return $item->user;
            })
            ->sortBy(function ($item) {
                return strtr(
                    utf8_decode($item['name']),
                    utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
                );
            })
            ->values() : null;


        $existe = [];

        $students_list = $students->map(function ($item, $key) use ($lesson, $existe) {
            if ($item <> null) {
                foreach ($lesson->students as $students) {
                    if ($students->name === $item->name) {
                        $item->{'presenca'} = "Presente";
                        $existe[] = $item->name;
                        return $item;
                    }
                }
                if (!in_array($item->name, $existe)) {
                    $item->{'presenca'} = "Falta";
                    return $item;
                }
            }
        })->values();

        return $students_list;
    }
}
