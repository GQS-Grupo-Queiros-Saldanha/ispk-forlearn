<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Building;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleEvent;
use App\Modules\GA\Models\ScheduleTime;
use App\Modules\GA\Models\ScheduleTimeTranslation;
use App\Modules\GA\Models\ScheduleTranslation;
use App\Modules\GA\Models\ScheduleType;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Requests\ScheduleRequest;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Toastr;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Room;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\StudyPlan;
use PDF;
use App\Modules\GA\Models\PeriodType;
use App\Modules\GA\Models\ScheduleTypeTime;
use App\Modules\Users\Models\User;
use App\Model\Institution;
use App\Modules\GA\Enum\PeriodTypeEnum;

class SchedulesController extends Controller
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
            // dd($lectiveYears);
            
            
            $auth = auth()->user();
            
            if ($auth->hasRole('student')) {
                
                $user = User::whereId(Auth::user()->id)->with([
                    'classes',
                    'matriculation' => function ($q) {
                        $q->with([
                            'disciplines',
                            'classes'
                        ]);
                    }
                ])->firstOrFail();
                
                
                // Verifica se existe matrícula para o estudante
                if ($user->matriculation == null) {
                    
                    return redirect()->back();
                    
                    //return view('GA::schedules.index')->with($data);
                }
                
                return $this->fetchForStudent('show',$lectiveYearSelected);
            }
            else {
                return view('GA::schedules.index')->with($data);
            }
            
            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // generate_pdf

    public function ajax($lective_year)
    {
        try {
            $lectiveYearSelected = DB::table('lective_years')
                ->where('id', $lective_year)
            // ->first()
            ->get();

           

            $model = Schedule::join('users as u1', 'u1.id', '=', 'schedules.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'schedules.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'schedules.deleted_by')
                ->leftJoin('schedule_translations as st', function ($join) {
                    $join->on('st.schedule_id', '=', 'schedules.id');
                    $join->on('st.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('st.active', '=', DB::raw(true));
                })
                //---------------Começa GQS----------------
                ->leftJoin('period_types as pt', function ($join) {
                    $join->on('pt.id', '=', 'schedules.period_type_id');
                })
                ->leftJoin('period_type_translations as ptt', function ($join) {
                    $join->on('ptt.period_types_id', '=', 'pt.id');
                    $join->on('ptt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ptt.active', '=', DB::raw(true));
                })

                ->leftJoin('schedule_types as sc', function ($join) {
                    $join->on('sc.id', '=', 'schedules.schedule_type_id');
                })
                ->leftJoin('schedule_type_translations as sct', function ($join) {
                    $join->on('sct.schedule_type_id', '=', 'sc.id');
                    $join->on('sct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('sct.active', '=', DB::raw(true));
                })

                ->leftJoin('classes as cl', function ($join) use ($lective_year) {
                    $join->on('cl.id', '=', 'schedules.discipline_class_id');
                })
                //------------Termina GQS-------------------------
                ->select([
                    'schedules.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'st.display_name',
                    //--------começa GQS-----------
                    'ptt.display_name as semestre',
                    'sct.display_name as turno',
                    'cl.display_name as cl_turma'
                    //------------Termina GQS-------------------------
                ])
                ->whereBetween('schedules.created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date]);  
                

                if(auth()->user()->hasRole('teacher') && !auth()->user()->hasRole('coordenador-curso')) {
                    $data = DB::table("user_classes")
                    ->leftJoin('classes as cl', function ($join){
                       $join->on('cl.id', '=', 'user_classes.class_id'); // o id da tabela classes é o class_id da tabela user_classes
                   })
                   ->where("user_classes.user_id", auth()->user()->id) // a tabela user_classes tem o id do usuário logado
                   ->where('cl.lective_year_id', '=', $lective_year) // a tabela classes tem o id do ano letivo
                   ->whereNull('cl.deleted_at')
                   ->get();
                  
                   $model->whereIn('schedules.discipline_class_id', $data->pluck("class_id"));
                }


            return (DataTables::of($model->get())
                ->addColumn('actions', function ($item) {
                    return view('GA::schedules.datatables.actions')->with('item', $item);
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make('true'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation',
            ])->get();

            $schedule_types = ScheduleType::with([
                'currentTranslation'
            ])->get();

            $days_of_the_week = DayOfTheWeek::with([
                'currentTranslation'
            ])
            ->WhereNull("deleted_at")
            ->get();

            $buildings = Building::with([
                'currentTranslation'
            ])->get();


            $classes = Classes::with([
                'course'
            ])->get();

            $rooms = Room::with([
                'currentTranslation'
            ])->get();

            $discipline_regime = DisciplineRegime::with([
                'currentTranslation'
            ])->get();

            $disciplines = disciplinesSelect();

            $period_types_id = PeriodType::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => 'create',
                'buildings' => $buildings,
                'study_plan_editions' => $study_plan_editions,
                'schedule_types' => $schedule_types,
                'days_of_the_week' => $days_of_the_week,
                'languages' => Language::whereActive(true)->get(),
                'classes' => $classes,
                'rooms' => $rooms,
                'disciplines' => $disciplines,
                'discipline_regime' => $discipline_regime,
                'period_type_id' => $period_types_id
            ];
            return view('GA::schedules.schedule')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(ScheduleRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $schedule = Schedule::create([
                'code' => $request->get('code'),
                'start_at' => $request->get('start_at'),
                'end_at' => $request->get('end_at'),
                'spe_id' => $request->get('study_plan_edition'),
                'schedule_type_id' => $request->get('schedule_type'),
                'discipline_class_id' => $request->get('classes'),
                'period_type_id' => $request->get('period_type_id'),
                'created_by' => auth()->user()->id
            ]);
            $schedule->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $item_translations[] = [
                    'schedule_id' => $schedule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }
            if (!empty($item_translations)) {
                ScheduleTranslation::insert($item_translations);
            }

            // Events
            if ($request->has('disciplines') && $request->has('rooms')) {
                $schedule_events = [];

                foreach ($request->get('disciplines') as $day_of_the_week_id => $disciplines) {
                    foreach ($disciplines as $schedule_type_time_id => $spe_discipline_id) {

                        // If empty then we skip it
                        if (empty($spe_discipline_id)) {
                            continue;
                        }

                        $schedule_events[] = [
                            'schedule_id' => $schedule->id,
                            'schedule_type_time_id' => $schedule_type_time_id,
                            'day_of_the_week_id' => $day_of_the_week_id,
                            'spe_discipline_id' => $spe_discipline_id,
                            'room_id' => $request->get('rooms')[$day_of_the_week_id][$schedule_type_time_id],
                            'created_by' => Auth::user()->id,
                        ];
                    }
                }

                if (!empty($schedule_events)) {
                    ScheduleEvent::insert($schedule_events);
                }
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedules.store_success_message'), __('toastr.success'));
            //return redirect()->route('schedules.index');
            return redirect()->route('schedules.index');
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
            $schedule = Schedule::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'period_type' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'events' => function ($q) {
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
                'class'
            ])->firstOrFail();




            $schedule->start_at = Carbon::parse($schedule->start_at)->format('Y-m-d\TH:i:s');
            $schedule->end_at = Carbon::parse($schedule->end_at)->format('Y-m-d\TH:i:s');

            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation',
            ])->get();

            $schedule_types = ScheduleType::with([
                'currentTranslation'
            ])->get();

            $days_of_the_week = DayOfTheWeek::with([
                'currentTranslation'
            ])
            ->WhereNull("deleted_at")
            ->get();

            $buildings = Building::with([
                'currentTranslation'
            ])->get();

            //adicionei as turmas

            $classes = Classes::with([
                'course'
            ])->get();

            $period_type_id = PeriodType::with([
                'currentTranslation'
            ])->get();
            $list_period = PeriodType::whereId($schedule->period_type_id)->with([
                'currentTraslation'
            ]);




            //adicionei as disciplinas
            $disciplines = disciplinesSelect();

            $data = [
                'action' => $action,
                'schedule' => $schedule,
                'study_plan_editions' => $study_plan_editions,
                'schedule_types' => $schedule_types,
                'days_of_the_week' => $days_of_the_week,
                'buildings' => $buildings,
                'translations' => $schedule->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get(),
                'classes' => $classes,
                'disciplines' => $disciplines,
                'period_type_id' => $period_type_id,
                'list_period' => $list_period
            ];
            return view('GA::schedules.schedule')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
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

    public function update(ScheduleRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();
            $languages = Language::whereActive(true)->get();

            // Find and update
            $schedule = Schedule::whereId($id)->with([
                'events'
            ])->firstOrFail();
            $schedule->code = $request->get('code');
            $schedule->start_at = $request->get('start_at');
            $schedule->end_at = $request->get('end_at');
            $schedule->spe_id = $request->get('study_plan_edition');
            $schedule->schedule_type_id = $request->get('schedule_type');
            $schedule->discipline_class_id = $request->get('classes');
            $schedule->period_type_id = $request->get('period_type_id');

            $schedule->updated_by = Auth::user()->id;
            $schedule->save();

            // Disable previous translations
            ScheduleTranslation::where('schedule_id', $schedule->id)->update(['active' => false]);
            $version = ScheduleTranslation::where('schedule_id', $schedule->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            foreach ($languages as $language) {
                $schedule_translations[] = [
                    'schedule_id' => $schedule->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($schedule_translations)) {
                ScheduleTranslation::insert($schedule_translations);
            }

            // Events
            if ($request->has('disciplines') && $request->has('rooms')) {
                $schedule_events = [];
                $schedule_event_ids_found = [];

                foreach ($request->get('disciplines') as $day_of_the_week_id => $disciplines) {
                    foreach ($disciplines as $schedule_type_time_id => $spe_discipline_id) {

                        // If empty then we skip it
                        if (empty($spe_discipline_id)) {
                            continue;
                        }

                        // Check if it exists
                        $found = false;
                        foreach ($schedule->events as $event) {
                            if ($event->schedule_type_time_id === $schedule_type_time_id && $event->day_of_the_week_id === $schedule_type_time_id) {

                                // If it has differences then we update it
                                if ($event->spe_discipline_id != $spe_discipline_id || $event->room_id != $request->get('rooms')[$day_of_the_week_id][$schedule_type_time_id]) {
                                    @dump($event);
                                    $event->spe_discipline_id = $spe_discipline_id;
                                    $event->room_id = $request->get('rooms')[$day_of_the_week_id][$schedule_type_time_id];
                                    $event->save();
                                }

                                $schedule_event_ids_found[] = $event->id;

                                $found = true;
                                break;
                            }
                        }
                        if($request->get('rooms')[$day_of_the_week_id][$schedule_type_time_id] == null)
                        Toastr::warning(__('Verifique se todas salas estão definidas'), __('toastr.warning'));
                        return redirect()->route('schedules.edit', $id);
                        // If it doesn't exist
                        if (!$found) {
                            $schedule_events[] = [
                                'schedule_id' => $schedule->id,
                                'schedule_type_time_id' => $schedule_type_time_id,
                                'day_of_the_week_id' => $day_of_the_week_id,
                                'spe_discipline_id' => $spe_discipline_id,
                                'room_id' => $request->get('rooms')[$day_of_the_week_id][$schedule_type_time_id],
                                'created_by' => Auth::user()->id,
                            ];
                        }
                    }
                }

                // Delete events not found
                $schedule_events_not_found = $schedule->events->filter(function ($event, $key) use ($schedule_event_ids_found) {
                    return !in_array($event->id, $schedule_event_ids_found);
                });
                if (!empty($schedule_events_not_found)) {
                    ScheduleEvent::whereIn('id', $schedule_events_not_found->pluck('id'))->delete();
                }

                // Insert new events
                if (!empty($schedule_events)) {
                    ScheduleEvent::insert($schedule_events);
                }
            } else {
                $schedule->events()->delete();
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedules.update_success_message'), __('toastr.success'));
            return redirect()->route('schedules.edit', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
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
            $schedule = Schedule::whereId($id)->firstOrFail();
            $schedule->delete();

            $schedule->deleted_by = Auth::user()->id;
            $schedule->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::schedules.destroy_success_message'), __('toastr.success'));
            return redirect()->route('schedules.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    private function fetchForPDF($id, $action)
    {
        try {
            // Find
            $schedule = Schedule::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'events' => function ($q) {
                    $q->with([
                        'discipline' => function ($q) {
                            
                            $q->with([
                                'currentTranslation',
                                'study_plans_has_disciplines' => function ($q) {
                                    $q->with([
                                        'study_plans_has_discipline_regimes' => function ($q) {
                                            $q->with([
                                                'discipline_regime'
                                            ]);
                                        }
                                    ]);
                                },
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation',
                            ]);
                        }
                    ]);
                },
                'class',
                'period_type' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'type' => function ($q) {
                    $q->with([
                        'times' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
                'schedule_type' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }
            ])->firstOrFail();
            
            

            $schedule->start_at = Carbon::parse($schedule->start_at)->format('Y-m-d\TH:i:s');
            $schedule->end_at = Carbon::parse($schedule->end_at)->format('Y-m-d\TH:i:s');

            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation',
            ])->get();

            $schedule_types = ScheduleType::with([
                'currentTranslation'
            ])->get();

            $days_of_the_week = DayOfTheWeek::with([
                'currentTranslation'
            ])
            ->WhereNull("deleted_at")
            ->get();

            $buildings = Building::with([
                'currentTranslation'
            ])->get();

            $classe = Classes::where('id', $schedule->discipline_class_id)->firstOrFail();


            $lectiveYears = lectiveYear::whereId($schedule->study_plan_edition->lective_years_id)->with([
                'currentTranslation'
            ])->firstOrFail();

            $studyPlans = StudyPlan::whereId($schedule->study_plan_edition->study_plans_id)->with([
                'course' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }
            ])->firstOrFail();


            //adicionei as disciplinas
            $disciplines = disciplinesSelect();

            $institution = Institution::latest()->first();
            $titulo_documento = "HORÁRIO DA TURMA";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 2;

            $a =  [];
            $nome = "";

            foreach ($schedule->events as $event) {

                if (isset($event->discipline->currentTranslation->id) && ($nome !== $event->discipline->currentTranslation->id)) {
                    $nome = $event->discipline->currentTranslation->id;
                    if(isset($event->discipline->id)){
                       $teacher_discipline = DB::table('user_disciplines')            
                            ->leftJoin('user_parameters as fullName', function ($join) {
                                $join->on('user_disciplines.users_id', '=', 'fullName.users_id')
                                ->where('fullName.parameters_id', 1);
                            })

                        ->leftJoin('funcionario_with_contrato as fun_with_cont', function ($join) {
                            $join->on('fun_with_cont.id_user', '=', 'user_disciplines.users_id');
                        })

                        ->leftJoin('fun_with_type_contrato as fun_with_type_cont', function ($join) {
                            $join->on('fun_with_type_cont.id_fun_with_contrato', '=', 'fun_with_cont.id');
                        })

                        ->leftJoin('role_translations as role_trans',function ($join)
                        {
                            $join->on('role_trans.role_id', '=', 'fun_with_type_cont.id_cargo')
                            ->where('role_trans.language_id',1)
                            ->where('role_trans.active',1);
                        })
                        ->join('teacher_classes as tc','tc.user_id','user_disciplines.users_id')
                        ->where('tc.class_id',$schedule->discipline_class_id)
                        ->where('disciplines_id', '=', $event->discipline->id)
                        ->whereNotIn('user_disciplines.users_id',["24","23"])
                        ->get();

                        $index_teacher = count($teacher_discipline)-1;
                        if(isset($teacher_discipline[$index_teacher])){

 
                            $parts = explode(" ", $teacher_discipline[$index_teacher]->{'value'});

                            $a[$teacher_discipline[$index_teacher]->{'disciplines_id'}] = [$parts[0]." ".end($parts)];
                        }
                    }
                }
            };
    
            $data = [
                'action' => $action,
                'schedule' => $schedule,
                'study_plan_editions' => $study_plan_editions,
                'schedule_types' => $schedule_types,
                'days_of_the_week' => $days_of_the_week,
                'buildings' => $buildings,
                'translations' => $schedule->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get(),
                'classe' => $classe,
                'disciplines' => $disciplines,
                'lectiveYears' => $lectiveYears,
                'studyPlans' => $studyPlans,
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'teacher_discipline' => $a

            ];
            // return view('GA::schedules.partials.schedule_pdf_partial')->with($data);
            // $footer_html = view()->make('GA::schedules.partials.pdf_footer', compact('institution'))->render();
            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView('GA::schedules.partials.schedule_pdf_partial', $data)
                ->setOption('margin-top', '2mm')
                ->setOption('margin-left', '2mm')
                ->setOption('margin-bottom', '13mm')
                ->setOption('margin-right', '2mm')
                ->setOption('footer-html', $footer_html)
                ->setPaper('a4', 'landscape');
            return $pdf->stream('Horario_'.$classe->code.'_'.$lectiveYears->currentTranslation->display_name.'.pdf');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
        }
    }


    private function get_discipline_obs($id) {
        $schedule = Schedule::whereId($id)->with([
            'translations' => function ($q) {
                $q->whereActive(true);
            },
            'events' => function ($q) {
                $q->with([
                    'discipline' => function ($q) {
                        $q->with([
                            'currentTranslation',                            
                            'study_plans_has_disciplines'
                        ]);
                    },
                    'room' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }
                ]);
            },
            'class',
            'period_type' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            },
            'type' => function ($q) {
                $q->with([
                    'times' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }
                ]);
            },
            'schedule_type' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            }
        ])->firstOrFail();

        return $schedule;
    }

    public function generate_pdf($id)
    {
        try {
            return $this->fetchForPDF($id, 'pdf');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function get_horario_lectiveyear($whatsapp){
        
        try {
            $isApiRequest = request()->header('X-From-API') === 'flask';
            $tokenRecebido = request()->bearerToken();

            if ($isApiRequest) {
                if ($tokenRecebido !== env('FLASK_API_TOKEN')) {
                    abort(403, 'Unauthorized');
                }

                $api = DB::table('users')->where('user_whatsapp', $whatsapp)->value('id');

                $lective_year_api = DB::table('users')
                    //->join('user_classes', 'user_classes.user_id', '=', 'users.id')
                    ->where('users.user_whatsapp', $whatsapp)->select('id')->get();
                    //->join('classes', 'classes.id', '=', 'user_classes.class_id')
                    //->select('classes.lective_year_id')
                    //->first();

                return $lective_year_api;
                if (!$lective_year_api || !isset($lective_year_api->lective_year_id)) {
                    return response()->json([
                        'error' => 'Ano lectivo não encontrado para este número de WhatsApp.'
                    ], 404);
                }

                $lective_year = $lective_year_api->lective_year_id;

                //return $this->fetchForStudent('pdf', $lective_year, $api);
                //return $this->fetchForPDF($lective_year, 'pdf');
            }

            return "Acesso Negado!";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }


    public function generate_student_pdf($lective_year)
    {        

        try {
            return $this->fetchForStudent('pdf',$lective_year);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function fetchForTeacher($action)
    {
        try {
            // return $id_lective_year;
           
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
              
            ->get();
           
            if (count($lectiveYearSelected) > 0) {

                // $lectiveYearSelected = $lectiveYearSelected[0]->id ?? 6;
                
                $user = User::whereId(Auth::user()->id)->with([
                    'classes',
                    'disciplines' => function ($q) {
                        $q->with([
                            'course' => function ($q) {
                                $q->with([
                                    'classes'
                                ]);
                            },
                        ]);
                    }
                ])->firstOrFail();

                // Find
                //$schedule = Schedule::whereId(14)->with([
                $discipline_list = $user->disciplines->pluck('id')->all();
                // return $lectiveYearSelected[0]->start_date;
                $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                    $q->whereIn('id', $discipline_list);
                })->with([
                    'events' => function ($q) use ($discipline_list) {
                        $q->whereHas('discipline', function ($q) use ($discipline_list) {
                            $q->whereIn('id', $discipline_list);
                        });
                        $q->with([
                            'discipline' => function ($q) {
                                $q->with([
                                    'course' => function ($q) {
                                        $q->with([
                                            'currentTranslation'
                                        ]);
                                    }
                                ]);
                                $q->with([
                                    'currentTranslation'
                                ]);
                            },
                            'room' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    },
                ])
                // ->whereYear('schedules.created_at', Date('Y'))
                ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                ->orderBy('schedule_type_id', 'ASC')->get();

                if (count($schedule_id) > 0) {

                    $events_by_type = [];
                    $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
                        $events = collect([]);

                        $item->each(function ($item, $key) use (&$events) {
                            $events = $events->merge($item->events);
                        });
                        $events_by_type[$key] = $events;
                    });



                    $schedule = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                        $q->whereIn('id', $discipline_list);
                    })->with([
                        'translations' => function ($q) {
                            $q->whereActive(true);
                        },
                        'events' => function ($q) use ($discipline_list) {
                            $q->whereHas('discipline', function ($q) use ($discipline_list) {
                                $q->whereIn('id', $discipline_list);
                            });
                            $q->with([
                                'discipline' => function ($q) {
                                    $q->with([
                                        'currentTranslation'
                                    ]);
                                },
                                'room' => function ($q) {
                                    $q->with([
                                        'currentTranslation'
                                    ]);
                                }
                            ]);
                        },
                    ])
                    // ->whereYear('created_at', Date('Y'))
                    ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                    ->firstOrFail();

                    $days_of_the_week = DayOfTheWeek::with([
                        'currentTranslation'
                    ])
                    ->WhereNull("deleted_at")
                    ->get();

                    $schedule_types = ScheduleType::with([
                        'times',
                        'currentTranslation'
                    ])->get();

                    $data = [
                        'schedule' => $schedule,
                        'action' => $action,
                        'days_of_the_week' => $days_of_the_week,
                        'schedule_types' => $schedule_types,
                        'user' => $user,
                        'events_by_type' => $events_by_type,
                        'schedule_id' => $schedule_id,
                        'lectiveYearSelected' => $lectiveYearSelected[0]->id,
                        'lectiveYears' => $lectiveYears
                    ];

                    return view('GA::schedules.teachers.schedule_teacher_partial')->with($data);
                

                }
                else {
                   
                    Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
                    return redirect()->back();
                }


            }
            else {
                Toastr::error(__('Verifica o ano lectivo.'), __('toastr.error'));
                return redirect()->back();
            }


        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            // return $e;
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            // return $e;
            return abort(500);
        }
    }

    public function getCurriculerPlanTeacher()
    {
        try {
            // return $lective_year;
            
            
            return $this->fetchForTeacher('show');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function printCurriculerPlanTeacher($lective_year)
    {        
        try {

            $lectiveYearSelected = DB::table('lective_years')
                // ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->where('id', $lective_year)
            ->get();
            
            $user = User::whereId(Auth::user()->id)->with([
                'classes',
                'courses' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'disciplines' => function ($q) {
                    $q->with([
                        'course' => function ($q) {
                            $q->with([
                                'classes'
                            ]);
                        },
                    ]);
                }
            ])->firstOrFail();

            // Find
            //$schedule = Schedule::whereId(14)->with([
            $discipline_list = $user->disciplines->pluck('id')->all();
            $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                $q->whereIn('id', $discipline_list);
            })->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'events' => function ($q) use ($discipline_list) {
                    $q->whereHas('discipline', function ($q) use ($discipline_list) {
                        $q->whereIn('id', $discipline_list);
                    });
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'course' => function ($q) {
                                    $q->with([
                                        'currentTranslation'
                                    ]);
                                }
                            ]);
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
            ])
            // ->whereYear('created_at', Date('Y'))
            ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
            ->orderBy('schedule_type_id', 'ASC')->get();

            $events_by_type = [];
            $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
                $events = collect([]);

                $item->each(function ($item, $key) use (&$events) {
                    $events = $events->merge($item->events);
                });
                $events_by_type[$key] = $events;
            });

            $schedule = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                $q->whereIn('id', $discipline_list);
            })->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'events' => function ($q) use ($discipline_list) {
                    $q->whereHas('discipline', function ($q) use ($discipline_list) {
                        $q->whereIn('id', $discipline_list);
                    });
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
            ])
            // ->whereYear('created_at', Date('Y'))
            ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
            ->firstOrFail();



            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation',
            ])->get();

            $schedule_types = ScheduleType::with([
                'times',
                'currentTranslation'
            ])->get();
            //dd($schedule_types);

            $days_of_the_week = DayOfTheWeek::with([
                'currentTranslation'
            ])
            ->WhereNull("deleted_at")
            ->get();
            $lectiveYears = lectiveYear::whereId($schedule->study_plan_edition->lective_years_id)->with([
                'currentTranslation'
            ])->firstOrFail();

            $institution = Institution::latest()->first();
            $titulo_documento = "HORÁRIO DA TURMA";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 2;

            $data = [
                'schedule' => $schedule,
                'schedule_types' => $schedule_types,
                'days_of_the_week' => $days_of_the_week,
                'languages' => Language::whereActive(true)->get(),
                'translations' => $schedule->translations->keyBy('language_id')->toArray(),
                'lectiveYears' => $lectiveYears,
                'user' => $user,
                'events_by_type' => $events_by_type,
                'schedule_id' => $schedule_id,
                'institution' => $institution,
                // 'titulo_documento' => $titulo_documento,
                // 'documentoGerado_documento' => $documentoGerado_documento,
                // 'documentoCode_documento' => $documentoCode_documento
            ];
            //return view('GA::schedules.teachers.print_schedule')->with($data);
            $footer_html = view()->make('GA::schedules.partials.pdf_footer')->with($data)->render();

            $pdf = PDF::loadView('GA::schedules.teachers.print_schedule', $data)
                ->setOption('margin-top', '10')
                ->setOption('footer-html', $footer_html)
                ->setPaper('a4', 'landscape');
            return $pdf->stream($user->name . '.pdf');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return $e;
            return redirect()->back() ?? abort(500);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return abort(500);
        }
    }

    //Student
    public function fetchForStudent($action, $lective_year, $api=null)
    {
        
        try {
            
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = LectiveYear::where('id', $lective_year)
                ->with(['currentTranslation'])
            ->get();

            if (count($lectiveYearSelected) > 0) {

                if ($api != null) {
                    $user = User::whereId($api)->with([
                    'classes',
                    'matriculation' => function ($q) use ($lectiveYearSelected) {
                        $q->where('lective_year', $lectiveYearSelected[0]->id);
                        $q->with([
                            'disciplines',
                            'classes'
                        ]);
                    }
                ])->firstOrFail();

                    if (!$user->matriculation) {
                            return response()->json([
                                'error' => 'Este aluno não está matriculado no ano lectivo seleccionado.'
                            ], 404);
                    }

                } else {
                    $user = User::whereId(Auth::user()->id)->with([
                        'classes',
                        'matriculation' => function ($q) use ($lectiveYearSelected) {
                            $q->where('lective_year', $lectiveYearSelected[0]->id);
                            $q->with([
                                'disciplines',
                                'classes'
                            ]);
                        }
                    ])->firstOrFail();
                }

                switch(date('m')){
                    case 10:
                    case 11:
                    case 12:
                    case 1:
                    case 2:
                        $period = PeriodTypeEnum::PRIMEIRO_SEMESTRE;
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        $period = PeriodTypeEnum::SEGUNDO_SEMESTRE;
               }

               

                $classes = $user->matriculation->classes->pluck('id')->all();

                $discipline_list = $user->matriculation->disciplines->pluck('id')->all();
               
                $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                    $q->whereIn('id', $discipline_list);
                })->with([
                    'translations' => function ($q) {
                        $q->whereActive(true);
                    },
                    'events' => function ($q) use ($discipline_list) {
                        $q->whereHas('discipline', function ($q) use ($discipline_list) {
                            $q->whereIn('id', $discipline_list);
                        });
                        $q->with([
                            'discipline' => function ($q) {
                                $q->with([
                                    'course' => function ($q) {
                                        $q->with([
                                            'currentTranslation'
                                        ]);
                                    }
                                ]);
                                $q->with([
                                    'currentTranslation'
                                ]);
                            },
                            'room' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    },
                ])
                ->whereIn('discipline_class_id', $classes)
                ->where('period_type_id',$period)
                ->whereBetween('start_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
                ->orderBy('schedule_type_id', 'ASC')->get();

                if (count($schedule_id) > 0) {
                
                    $events_by_type = [];
                    $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
                        $events = collect([]);

                        $item->each(function ($item, $key) use (&$events) {
                            $events = $events->merge($item->events);
                        });
                        $events_by_type[$key] = $events;
                    });


                    $days_of_the_week = DayOfTheWeek::with([
                        'currentTranslation'
                    ])
                    ->WhereNull("deleted_at")
                    ->get();

                    $schedule_types = ScheduleType::with([
                        'times',
                        'currentTranslation'
                    ])->get();

                    $teacher_discipline = $this->get_teacher_discipline($schedule_id);
                    
                    switch($action)
                    {
                        case 'show':
                                $data = [
                                    'action' => $action,
                                    'schedule_types' => $schedule_types,
                                    'days_of_the_week' => $days_of_the_week,
                                    'user' => $user,
                                    'events_by_type' => $events_by_type,
                                    'schedule_id' => $schedule_id,
                                    'lectiveYearSelected' => $lectiveYearSelected[0],
                                    'lectiveYears' => $lectiveYears,
                                    'teacher_discipline' => $teacher_discipline
            
                                ];
                               return view('GA::schedules.students.schedule_student_partial')->with($data);

                        case 'pdf':
                            $institution = Institution::latest()->first();
                            $titulo_documento = "HORÁRIO";
                            $documentoGerado_documento = "Documento gerado a";
                            $documentoCode_documento = 2;
                            $student_info =mainController::get_matriculation_student($lective_year);
                           
                            $data = [
                                'action' => $action,
                                'schedule_types' => $schedule_types,
                                'days_of_the_week' => $days_of_the_week,
                                'user' => $user,
                                'events_by_type' => $events_by_type,
                                'schedule_id' => $schedule_id,
                                'lectiveYearSelected' => $lectiveYearSelected[0],
                                'lectiveYears' => $lectiveYears,
                                'teacher_discipline' => $teacher_discipline,
                                'institution' => $institution,
                                'titulo_documento' => $titulo_documento,
                                'documentoGerado_documento' => $documentoGerado_documento,
                                'documentoCode_documento' => $documentoCode_documento,
                                'student_info' => $student_info
                            ];
                
                            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                            $pdf = PDF::loadView('GA::schedules.students.schedule_student_partial_pdf', $data)
                                ->setOption('margin-top', '2mm')
                                ->setOption('margin-left', '2mm')
                                ->setOption('margin-bottom', '13mm')
                                ->setOption('margin-right', '2mm')
                                ->setOption('footer-html', $footer_html)
                                ->setPaper('a4', 'landscape');
                            
                            if($api != null){

                                $matriculationCode = optional($user->matriculation)->code ?? 'indefinido';
                                return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="Horario_'.$matriculationCode.'.pdf"');
                            }

                            return $pdf->stream('Horario_'.$user->matriculation->code.'.pdf');              
                    }
              
                
                }
                else {
                    Toastr::error(__('Este horário não existe'), __('toastr.error'));
                    return redirect()->back();
                }
                
            
            }
            else {
                if($api != null){

                    $matriculationCode = optional($user->matriculation)->code ?? 'indefinido';
                    return response($pdf->output(), 200)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="Horario_'.$matriculationCode.'.pdf"');
                }
                Toastr::error(__('Verifica o ano lectivo.'), __('toastr.error'));
                return redirect()->back();
            }
            
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return $e;
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return abort(500);
        }
    }

    public function get_teacher_discipline($schedule){
        $a =  [];
        $nome = "";
        foreach ($schedule as $schedule_obs){
        foreach ($schedule_obs->events as $event) {

            if (isset($event->discipline->currentTranslation->id) && ($nome !== $event->discipline->currentTranslation->id)) {
                $nome = $event->discipline->currentTranslation->id;
                if(isset($event->discipline->id)){
                   $teacher_discipline = DB::table('user_disciplines')            
                        ->leftJoin('user_parameters as fullName', function ($join) {
                            $join->on('user_disciplines.users_id', '=', 'fullName.users_id')
                            ->where('fullName.parameters_id', 1);
                        })

                    ->leftJoin('funcionario_with_contrato as fun_with_cont', function ($join) {
                        $join->on('fun_with_cont.id_user', '=', 'user_disciplines.users_id');
                    })

                    ->leftJoin('fun_with_type_contrato as fun_with_type_cont', function ($join) {
                        $join->on('fun_with_type_cont.id_fun_with_contrato', '=', 'fun_with_cont.id');
                    })

                    ->leftJoin('role_translations as role_trans',function ($join)
                    {
                        $join->on('role_trans.role_id', '=', 'fun_with_type_cont.id_cargo')
                        ->where('role_trans.language_id',1)
                        ->where('role_trans.active',1);
                    })
                    ->join('teacher_classes as tc','tc.user_id','user_disciplines.users_id')
                   ->where('tc.class_id',$schedule_obs->discipline_class_id)
                    ->where('disciplines_id', '=', $event->discipline->id)
                    ->whereNotIn('user_disciplines.users_id',["23","24"])
                    ->get();
                    
                    $index_teacher = count($teacher_discipline)-1;
                    if(isset($teacher_discipline[$index_teacher])){

                        

                        $parts = explode(" ", $teacher_discipline[$index_teacher]->{'value'});

                        $a[$teacher_discipline[$index_teacher]->{'disciplines_id'}] = [$parts[0]." ".end($parts)];
                    }
                }
            }
        };
       }
    
    return $a;

    }

    public function printCurriculerPlanStudent($lective_year)
    {
        try {
            
            $institution = Institution::latest()->first();
            $titulo_documento = "HORÁRIO DA TURMA";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 2;
            

            $lectiveYearSelected = DB::table('lective_years')
                // ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->where('id', $lective_year)
            ->get();

            $user = User::whereId(Auth::user()->id)->with([
                'classes',
                'matriculation' => function ($q) {
                    $q->with([
                        'disciplines'
                    ]);
                },
                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                        'groups'
                    ]);
                }
            ])->firstOrFail();

            // Find
            //$schedule = Schedule::whereId(14)->with([
            $discipline_list = $user->matriculation->disciplines->pluck('id')->all();
            $schedule_id = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                $q->whereIn('id', $discipline_list);
            })->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'events' => function ($q) use ($discipline_list) {
                    $q->whereHas('discipline', function ($q) use ($discipline_list) {
                        $q->whereIn('id', $discipline_list);
                    });
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
            ])
            // ->whereYear('created_at', Date('Y'))
            ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
            ->orderBy('schedule_type_id', 'ASC')->get();

            $events_by_type = [];
            $schedule_id->groupBy('type.id')->each(function ($item, $key) use (&$events_by_type) {
                $events = collect([]);

                $item->each(function ($item, $key) use (&$events) {
                    $events = $events->merge($item->events);
                });
                $events_by_type[$key] = $events;
            });

            $schedule = Schedule::whereHas('events.discipline', function ($q) use ($discipline_list) {
                $q->whereIn('id', $discipline_list);
            })->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'events' => function ($q) use ($discipline_list) {
                    $q->whereHas('discipline', function ($q) use ($discipline_list) {
                        $q->whereIn('id', $discipline_list);
                    });
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'room' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
            ])
            // ->whereYear('created_at', Date('Y'))
            ->whereBetween('created_at', [$lectiveYearSelected[0]->start_date, $lectiveYearSelected[0]->end_date])
            ->firstOrFail();

            $study_plan_editions = StudyPlanEdition::with([
                'currentTranslation',
            ])->get();

            $schedule_types = ScheduleType::with([
                'times',
                'currentTranslation'
            ])->get();

            $days_of_the_week = DayOfTheWeek::with([
                'currentTranslation'
            ])
            ->WhereNull("deleted_at")
            ->get();

            $lectiveYears = lectiveYear::whereId($schedule->study_plan_edition->lective_years_id)->with([
                'currentTranslation'
            ])->firstOrFail();
            $parameterNome = $user->parameters->where('code', 'nome')->first();
            $personalName = $parameterNome ? $parameterNome->pivot->value : '';

            $parameterMecanografico = $user->parameters->where('code', 'n_mecanografico')->first();
            $personalMecanografico = $parameterMecanografico ? $parameterMecanografico->pivot->value : '';

            $data = [
                'schedule' => $schedule,
                'schedule_types' => $schedule_types,
                'days_of_the_week' => $days_of_the_week,
                'translations' => $schedule->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get(),
                'lectiveYears' => $lectiveYears,
                'user' => $user,
                'events_by_type' => $events_by_type,
                'schedule_id' => $schedule_id,
                'personal' => [
                    'name' => $personalName,
                    'n_mecanografico' => $personalMecanografico
                ],
                'institution' => $institution,
            ];
            //return view('GA::schedules.students.print_schedule')->with($data);
            $footer_html = view()->make('GA::schedules.partials.pdf_footer')->with($data)->render();
            $pdf = PDF::loadView('GA::schedules.students.print_schedule', $data)
                ->setOption('margin-top', '10')
                ->setOption('footer-html', $footer_html)
                ->setPaper('a4', 'landscape');

            return $pdf->stream($user->name . '.pdf');

        } catch (ModelNotFoundException $e) {
            
            Toastr::error(__('GA::schedules.not_found_message'), __('toastr.error'));
            Log::error($e);
            return $e;
            return redirect()->back() ?? abort(500);

        } catch (Exception | Throwable $e) {            
            Log::error($e);
            return $e;
            return abort(500);
        }
    }
}
