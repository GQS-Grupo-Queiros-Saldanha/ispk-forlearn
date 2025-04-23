<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\AccessType;
use App\Modules\GA\Models\AccessTypeTranslation;
use App\Modules\GA\Models\AverageCalculationRule;
use App\Modules\GA\Models\AverageCalculationRuleTranslation;
use App\Modules\GA\Models\DisciplineAbsenceConfiguration;
use App\Modules\GA\Models\DisciplinePeriod;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\DisciplineRegimeTranslation;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\LectiveYearTranslation;
use App\Modules\GA\Models\PeriodType;
use App\Modules\GA\Models\PeriodTypeTranslation;
use App\Modules\GA\Models\Precedence;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanEdition;
use App\Modules\GA\Models\StudyPlanEditionAccessType;
use App\Modules\GA\Models\StudyPlanEditionDiscipline;
use App\Modules\GA\Models\StudyPlanEditionDisciplineDisciplineRegime;
use App\Modules\GA\Models\StudyPlanEditionDisciplineModule;
use App\Modules\GA\Models\StudyPlanEditionDisciplineModuleTranslation;
use App\Modules\GA\Models\StudyPlanEditionOptionalGroup;
use App\Modules\GA\Models\StudyPlanEditionOptionalGroupDiscipline;
use App\Modules\GA\Models\StudyPlanEditionTranslation;
use App\Modules\GA\Models\YearTransitionRule;
use App\Modules\GA\Models\YearTransitionRuleTranslation;
use App\Modules\GA\Requests\DisciplineAbsenceConfigurationRequest;
use App\Modules\GA\Requests\StudyPlanEditionRequest;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\ScheduleType;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\Avaliations;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
// use Request;
use Throwable;
// use App\Modules\GA\Controllers\HttpRequest;
use Toastr;

use function PHPSTORM_META\map;

class StudyPlanEditionsController extends Controller
{

    public function index()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('GA::study-plan-editions.index', compact('lectiveYears', 'lectiveYearSelected'));
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();

            $model = StudyPlanEdition::
            leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'study_plan_editions.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })
            ->leftJoin('lective_year_translations as lyt', function ($join) {
                $join->on('lyt.lective_years_id', '=', 'study_plan_editions.lective_years_id');
                $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyt.active', '=', DB::raw(true));
            })
                ->select([
                    'study_plan_editions.*',
                    'spet.display_name',
                    'lyt.display_name as lective_year'
                ])
            ->where('study_plan_editions.lective_years_id', $lectiveYearSelected->id ?? 6);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::study-plan-editions.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
    
        try {
         

            $study_plan = StudyPlan::with([
                'currentTranslation'
            ])->get();

            $period_types = PeriodType::with([
                'translation' => function ($q) {
                    /** @var PeriodTypeTranslation $q */
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $year_transition_rule = YearTransitionRule::with([
                'translation' => function ($q) {
                    /** @var YearTransitionRuleTranslation $q */
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $average_calculation_rule = AverageCalculationRule::with([
                'translation' => function ($q) {
                    /** @var AverageCalculationRuleTranslation $q */
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $lective_year = LectiveYear::with([
                'translation' => function ($q) {
                    /** @var LectiveYearTranslation $q */
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $access_types = AccessType::with([
                'translation' => function ($q) {
                    /** @var AccessTypeTranslation $q */
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

             $period_types_id = PeriodType::with([
               'currentTranslation'
           ])->get();
            
            $data = [
                'action' => 'create',
                'access_types' => $access_types,
                'study_plans' => $study_plan,
                'discipline_periods' => DisciplinePeriod::all(),
                'lective_years' => $lective_year,
                'year_transition_rules' => $year_transition_rule!=""? $year_transition_rule: 1,
                'period_types' => $period_types,
                'average_calculation_rules' => $average_calculation_rule,
                'languages' => Language::whereActive(true)->get(),
                'period_type_id' => $period_types_id
            ];

            return view('GA::study-plan-editions.study-plan-edition')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(StudyPlanEditionRequest $request)
    {
        try {
            DB::beginTransaction();

            $languages = Language::whereActive(true)->get();

            // Create
            $study_plan_edition = new StudyPlanEdition([
                'block_enrollments' => $request->has('block_enrollments'),
                'max_enrollments' => $request->get('max_enrollments'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'course_year' => $request->get('course_year'),
                'period_type_id' => $request->get('period_type_id'),
            ]);
            
                $year_transition_rule_Validated=$request->get('year_transition_rule')!=""?$request->get('year_transition_rule'):1;

            $year_transition_rule_Validated=$request->get('year_transition_rule')!=""?$request->get('year_transition_rule'):1;
            // Foreign keys
            $study_plan_edition->studyPlan()->associate($request->get('study_plan'));
            $study_plan_edition->lectiveYear()->associate($request->get('lective_year'));
              $study_plan_edition->yearTransitionRule()->associate($year_transition_rule_Validated);
            $study_plan_edition->averageCalculationRule()->associate($request->get('average_calculation_rule'));
            $study_plan_edition->save();

            ########################
            # Create - Disciplines #
            ########################
            if ($request->get('dr_checked_disciplines') && count($request->get('dr_checked_disciplines'))) {
                $checkedDisciplinesArray = array_map(function ($a) {
                    return $a['id'];
                }, $request->get('dr_checked_disciplines'));
                $study_plan_edition->disciplines()->sync($checkedDisciplinesArray);
            }

            ########################
            # Create - Precedences #
            ########################
            if ($request->has(['discipline_precedence', 'precedence_precedence'])) {
                foreach ($request->get('discipline_precedence') as $index => $val) {

                    $precedence = new Precedence();

                    // Foreign keys
                    $precedence->discipline()->associate($request->get('discipline_precedence')[$index]);
                    $precedence->parent()->associate($request->get('precedence_precedence')[$index]);
                    $precedence->studyPlanEdition()->associate($study_plan_edition->id);

                    $precedence->save();
                }
            }

            #########################
            # Create - Access Types #
            #########################
            if ($request->has('access_types')) {

                $study_plan_editions_has_access_types = [];
                foreach ($request->get('access_types') as $access_type) {

                    if (isset($access_type['access_type_id'], $access_type['max_enrollments'])) {

                        $study_plan_editions_has_access_types[] = [
                            'spe_id' => $study_plan_edition->id,
                            'access_type_id' => $access_type['access_type_id'],
                            'max_enrollments' => $access_type['max_enrollments'],
                        ];

                    }
                }

                if (!empty($study_plan_editions_has_access_types)) {
                    StudyPlanEditionAccessType::insert($study_plan_editions_has_access_types);
                }
            }

            #########################
            # Create - Translations #
            #########################
            foreach ($languages as $language) {
                $study_plan_edition_translations[] = [
                    'study_plan_editions_id' => $study_plan_edition->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($study_plan_edition_translations)) {
                StudyPlanEditionTranslation::insert($study_plan_edition_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plan-editions.store_success_message'), __('toastr.success'));
            return redirect()->route('study-plan-editions.index');

        } catch (Exception | Throwable $e) {
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function show($id)
    {
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            
          $areas = [13, 14, 15];
        $study_plan_edition = StudyPlanEdition::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
                'studyPlan' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                },
                'disciplines' => function ($q) {
                    $q->with([
                        'study_plans_has_disciplines',
                        'currentTranslation',
                        'disciplineAreas' =>function($q){
                            $q->with([
                            'translations'
                        ]);
                        }
                    ]);
                },
               
                
                'precedences' => function ($q) {
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'translation' => function ($q) {
                                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                                }
                            ]);
                        },
                        'parent' => function ($q) {
                            $q->with([
                                'translation' => function ($q) {
                                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                                }
                            ]);
                        },
                    ]);
                },
                'accessTypes' => function ($q) {
                    $q->with([
                        'accessType'
                    ]);
                },
                'periodTypes' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }
            ])  
            ->firstOrFail();


           

           $study_plans = StudyPlan::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $discipline_periods = DisciplinePeriod::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

          $lective_years = LectiveYear::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $sped = StudyPlanEditionDiscipline::all();

            $year_transition_rules = YearTransitionRule::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $period_types = PeriodType::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $acr = AverageCalculationRule::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $access_types = AccessType::with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->get();

            $period_type_id = PeriodType::with([
               'currentTranslation'
           ])->get();


           
         $avalicaes = Avaliacao::join('users as u1', 'u1.id', '=', 'avaliacaos.created_by')
           ->leftJoin('users as u2', 'u2.id', '=', 'avaliacaos.updated_by')
           ->leftJoin('users as u3', 'u3.id', '=', 'avaliacaos.deleted_by')
           ->leftJoin('calendario_prova as calend', 'calend.id_avaliacao', '=', 'avaliacaos.id')
           ->join('tipo_avaliacaos as ta', 'ta.id', '=', 'avaliacaos.tipo_avaliacaos_id')
           ->select([
               'avaliacaos.id as avaliacao_id',
               'avaliacaos.lock as avaliacao_lock',
               'avaliacaos.nome',
               'u1.name as created_by',
               'u2.name as updated_by',
               'ta.nome as ta_nome',
               'avaliacaos.created_at as created_at',
               'avaliacaos.updated_at as updated_at',
               'calend.id_avaliacao as calend_id_avaliacao',
               'calend.deleted_at as deleted_at'
           ])
           ->where('avaliacaos.anoLectivo',7)
           ->where('ta.anoLectivo',7)
           ->get();



        $avaliacaoAssociada=DB::table('plano_estudo_avaliacaos as pea')
          ->join('avaliacaos as avl','avl.id','=','pea.avaliacaos_id')
          ->where('pea.study_plan_editions_id',$id)
          ->where('pea.deleted_by',null)
          ->where('pea.deleted_at',null)
          ->select(['avl.id','avl.nome','pea.disciplines_id'])
          ->orderBy('avl.id')
          ->get();
        
        $avaliacaoAssociada=collect($avaliacaoAssociada)
            ->groupBy('disciplines_id')
            ->map(function($item,$key){
                $i=count($item);
                $dado=[];
                    if($i==1){
                    $dado[]="[".$item[$i-1]->nome."]";
                    }
                    if($i==2){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome ."]";
                    }
                    if($i==3){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome ."]";
                    }
                    if($i==4){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome ."]";
                    }
                    if($i==5){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome ."]";
                    }
                    if($i==6){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome.",".$item[$i-6]->nome ."]";
                    }
                    if($i==7){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome.",".$item[$i-6]->nome.",".$item[$i-7]->nome ."]";
                    }
                    if($i==8){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome.",".$item[$i-6]->nome.",".$item[$i-7]->nome.",".$item[$i-8]->nome ."]";
                    }
                    if($i==9){
                    $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome.",".$item[$i-6]->nome.",".$item[$i-7]->nome.",".$item[$i-8]->nome.",".$item[$i-9]->nome ."]";
                    }
                    if($i==10){
                 $dado[]="[".$item[$i-1]->nome.",".$item[$i-2]->nome.",".$item[$i-3]->nome.",".$item[$i-4]->nome.",".$item[$i-5]->nome.",".$item[$i-6]->nome.",".$item[$i-7]->nome.",".$item[$i-8]->nome.",".$item[$i-9]->nome." ".$item[$i-10]->nome ."]";
                 }
                return $dado;
            });

             $falta_disciplina= DB::table('disciplines_has_falt')
                ->get();
               
            $data = [
                'falta_disciplina' =>$falta_disciplina,
                'access_types' => $access_types,
                'action' => $action,
                'average_calculation_rules' => $acr,
                'discipline_periods' => $discipline_periods,
                'languages' => Language::whereActive(true)->get(),
                'lective_years' => $lective_years,
                'period_types' => $period_types,
                'study_plan_edition' => $study_plan_edition,
                'study_plan_edition_disciplines' => $sped,
                'study_plans' => $study_plans,
                'avaliacaoPlanos' => $avaliacaoAssociada,
                'translations' => $study_plan_edition->translations->keyBy('language_id')->toArray(),
                'year_transition_rules' => $year_transition_rules,
                'period_type_id' => $period_type_id,
                //'list_period' => $list_period
            ];
            
            return view('GA::study-plan-editions.study-plan-edition')->with($data);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-editions.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }




    
    public function edit($id)

    {
        try {
            return $this->fetch($id, 'edit');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update(StudyPlanEditionRequest $request, $id)
    {
        try {
            if ($request->dr_checked_disciplines!=null) {
                for ($i=0; $i < count($request->dr_disciplines); $i++) { 
                    foreach($request->dr_checked_disciplines as $item_check){
                        if ( $request->dr_disciplines[$i]['id']=== $item_check['id']) {
                                DB::table('disciplines_has_falt')
                                    ->updateOrInsert(
                                        [
                                        'disciplines_id' => $item_check['id'],
                                        'id_plain_study' => $id
                                            ],
                                        [
                                        // 'number_falt' => $request->dr_falta[$i]
                                        'number_falt' => 0
                                        ] 
                                    ); 
                                }
        
                        }
                   }
            }
           

                            
            

            DB::beginTransaction();

            // Find and update
            $study_plan_edition = StudyPlanEdition::whereId($id)->firstOrFail();
            $languages = Language::whereActive(true)->get();

            // Dissociate relations
            // $study_plan_edition->studyPlan()->dissociate();
            $study_plan_edition->lectiveYear()->dissociate();
            $study_plan_edition->yearTransitionRule()->dissociate();
            $study_plan_edition->averageCalculationRule()->dissociate();

            $study_plan_edition->precedences()->forceDelete();
            $study_plan_edition->accessTypes()->forceDelete();
            // $study_plan_edition->translations()->forceDelete();
            // Update
            $study_plan_edition->block_enrollments = $request->has('block_enrollments');
            $study_plan_edition->max_enrollments = $request->get('max_enrollments');
            $study_plan_edition->start_date = $request->get('start_date');
            $study_plan_edition->end_date = $request->get('end_date');
            $study_plan_edition->period_type_id = $request->get('period_type_id');
            // Foreign keys
            // $study_plan_edition->studyPlan()->associate($request->get('study_plan'));
            $study_plan_edition->lectiveYear()->associate($request->get('lective_year'));
            $study_plan_edition->yearTransitionRule()->associate($request->get('year_transition_rule'));
            $study_plan_edition->averageCalculationRule()->associate($request->get('average_calculation_rule'));
            ######################
            # Sync - Disciplines #
            ######################
            if ($request->get('dr_checked_disciplines') && count($request->get('dr_checked_disciplines'))) {


                $checkedDisciplinesArray = array_map(function ($a) {
                    return $a['id'];
                }, $request->get('dr_checked_disciplines'));
                $study_plan_edition->disciplines()->sync($checkedDisciplinesArray);

            }
            ########################
            # Create - Precedences #
            ########################
            if ($request->has(['discipline_precedence', 'precedence_precedence'])) {
                foreach ($request->get('discipline_precedence') as $index => $val) {

                    $precedence = new Precedence();

                    // Foreign keys
                    $precedence->discipline()->associate($request->get('discipline_precedence')[$index]);
                    $precedence->parent()->associate($request->get('precedence_precedence')[$index]);
                    $precedence->studyPlanEdition()->associate($study_plan_edition->id);
                    $precedence->save();
                }
            }

            #########################
            # Create - Access Types #
            #########################
            if ($request->has('access_types')) {

                $study_plan_editions_has_access_types = [];
                foreach ($request->get('access_types') as $access_type) {
                    if (isset($access_type['access_type_id'], $access_type['max_enrollments'])) {
                        $study_plan_editions_has_access_types[] = [
                            'spe_id' => $study_plan_edition->id,
                            'access_type_id' => $access_type['access_type_id'],
                            'max_enrollments' => $access_type['max_enrollments'],
                        ];
                    }
                }

                if (!empty($study_plan_editions_has_access_types)) {
                    StudyPlanEditionAccessType::insert($study_plan_editions_has_access_types);
                }
            }

            $study_plan_edition->save();
            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();
            // Disable previous translations
            StudyPlanEditionTranslation::where('study_plan_editions_id', $study_plan_edition->id)->update(['active' => false]);
            $version = StudyPlanEditionTranslation::where('study_plan_editions_id', $study_plan_edition->id)->whereLanguageId($default_language->id)->count() + 1;
            // Associated translations
            foreach ($languages as $language) {
                $study_plan_edition_translations[] = [
                    'study_plan_editions_id' => $study_plan_edition->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }
            if (!empty($study_plan_edition_translations)) {
                StudyPlanEditionTranslation::insert($study_plan_edition_translations);
            }

            

            DB::commit();
            // Success message
            Toastr::success(__('GA::study-plan-editions.update_success_message'), __('toastr.success'));
            return redirect()->route('study-plan-editions.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-editions.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            // return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            return response()->json($e->getMessage(), 500);
        }
    }





    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find
            $study_plan_edition = StudyPlanEdition::whereId($id)->firstOrFail();

            // Dissociate
            $study_plan_edition->studyPlan()->dissociate();
            $study_plan_edition->lectiveYear()->dissociate();
            $study_plan_edition->yearTransitionRule()->dissociate();
            $study_plan_edition->averageCalculationRule()->dissociate();
            $study_plan_edition->precedences()->forceDelete();
            $study_plan_edition->translations()->forceDelete();
            $study_plan_edition->accessTypes()->delete();
            $study_plan_edition->absences()->delete();

            // Delete disciplines
            /*foreach ($study_plan_edition->disciplines as $spe_discipline) {
                $spe_discipline->regimes()->delete();

                foreach ($spe_discipline->modules as $module) {
                    $module->translations()->delete();
                    $module->delete();
                }

                $spe_discipline->delete();
            }

            // Delete optional groups
            $spe_optional_groups = $study_plan_edition->optionalGroups;
            foreach ($spe_optional_groups as $spe_optional_group) {
                $spe_optional_group->disciplines()->delete();
                $spe_optional_group->delete();
            }*/

            // Delete
            $study_plan_edition->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plan-editions.destroy_success_message'), __('toastr.success'));
            return redirect()->route('study-plan-editions.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-editions.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (QueryException $e) {
            // Integrity violation
            if ($e->getCode() === '23000') {
                echo $e->getMessage();
                die();
                Toastr::error(__('GA::study-plan-editions.destroy_integrity_violation_message'), __('toastr.error'));
            }
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    
    ###################################
    # DISCIPLINE ABSENCE CONFIGURATION
    ###################################
    public function absences($id)
    {
        try {

            $disciplines_absences = StudyPlanEdition::join('study_plans as sp', 'sp.id', '=', 'study_plan_editions.study_plans_id')
                ->leftJoin('study_plans_has_disciplines as spd', 'spd.study_plans_id', '=', 'sp.id')
                ->leftJoin('sp_has_discipline_regimes as sdr', 'spd.id', '=', 'sdr.sp_has_disciplines_id')
                ->leftJoin('discipline_regimes as dr', 'dr.id', '=', 'sdr.discipline_regimes_id')
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'dr.id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines as d', 'd.id', '=', 'spd.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_absence_configuration as dac', function ($join) {
                    $join->on('dac.study_plan_editions_id', '=', 'study_plan_editions.id');
                    $join->on('dac.disciplines_id', '=', 'd.id');
                    $join->on(DB::raw('( dac.discipline_regimes_id = dr.id or dac.discipline_regimes_id is null )'), DB::raw('1'), DB::raw('1'));
                })
                ->where([
                    ['study_plan_editions.id', '=', $id]
                ])
                ->select(
                    [
                        'study_plan_editions.id as study_plan_editions_id',
                        'dr.id as discipline_regimes_id',
                        'd.id as discipline_id',
                        'dac.max_absences',
                        'dac.is_total',
                        'dt.display_name as discipline',
                        'drt.display_name as discipline_regime',
                        'dr.id as regime_id'
                    ])->orderBy('d.id', 'asc');

            $optional_disciplines_absences = StudyPlanEdition::leftJoin('spe_has_disciplines as shd', 'shd.study_plan_editions_id', '=', 'study_plan_editions.id')
                ->leftJoin('spe_has_disciplines_has_dr as shdd', 'shdd.spe_has_disciplines_id', '=', 'shd.id')
                ->leftJoin('discipline_regimes as dr', 'dr.id', '=', 'shdd.discipline_regimes_id')
                ->leftJoin('discipline_regime_translations as drt', function ($join) {
                    $join->on('drt.discipline_regimes_id', '=', 'dr.id');
                    $join->on('drt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('drt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines as d', 'd.id', '=', 'shd.disciplines_id')
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'd.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_absence_configuration as dac', function ($join) {
                    $join->on('dac.study_plan_editions_id', '=', 'study_plan_editions.id');
                    $join->on('dac.disciplines_id', '=', 'd.id');
                    $join->on(DB::raw('( dac.discipline_regimes_id = dr.id or dac.discipline_regimes_id is null )'), DB::raw('1'), DB::raw('1'));
                })
                ->where([
                    ['study_plan_editions.id', '=', $id],
                    ['shd.optional', '=', 1]
                ])
                ->select(
                    [
                        'study_plan_editions.id as study_plan_editions_id',
                        'dr.id as discipline_regimes_id',
                        'd.id as discipline_id',
                        'dac.max_absences',
                        'dac.is_total',
                        'dt.display_name as discipline',
                        'drt.display_name as discipline_regime',
                        'dr.id as regime_id'
                    ]);

            //union das duas queries e ordenação
            $result = $optional_disciplines_absences->union($disciplines_absences)->orderBy('discipline_id', 'asc');
            $result = $result->get();

            $study_plan_edition = StudyPlanEdition::whereId($id)->with([
                'translation' => function ($q) {
                    $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                }
            ])->firstOrFail();

            $data = [
                'action' => 'show',
                'study_plan_edition' => $study_plan_edition,
                'disciplines_absences' => $result
            ];

            return view('GA::study-plan-editions.absences')->with($data);

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-edition.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }
    }

    public function update_absences(DisciplineAbsenceConfigurationRequest $request, $id)
    {
        try {

            //dd($request->request);
            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Elimina os registos anteriores
            DisciplineAbsenceConfiguration::where('study_plan_editions_id', '=', $id)->delete();

            if ($request->has(['ab_discipline'])) {

                //percorre todas as disciplinas
                foreach ($request->get('ab_discipline') as $index => $val) {

                    //se o is_total = 0 então é porque é regime
                    if (!isset($request->get('ab_total')[$val])) {
                        if ($request->has('ab_max_absence_regime')) {
                            if (isset($request->get('ab_max_absence_regime')[$val])) {

                                //percorre todos os regimes e insere as Absences para cada um
                                foreach ($request->get('ab_max_absence_regime')[$val] as $index_regime => $val_regime) {
                                    if ($val_regime != null) {
                                        $absence = new DisciplineAbsenceConfiguration();

                                        //discipline
                                        $absence->discipline()->associate($request->get('ab_discipline')[$index]);

                                        //study_plan_edition
                                        $absence->study_plan_edition()->associate($id);

                                        //regime
                                        $absence->discipline_regime()->associate($request->get('ab_regime')[$val][$index_regime]);
                                        $absence->max_absences = $val_regime;
                                        $absence->is_total = 0;
                                        $absence->save();
                                    }
                                }
                            }
                        }
                        //se o is_total = 1, então percorre apenas as max_absences relacionadas com a disciplina
                    } else if (isset($request->get('ab_total')[$val])) {
                        if (isset($request->get('ab_max_absence')[$val][0])) {
                            $absence = new DisciplineAbsenceConfiguration();

                            //discipline
                            $absence->discipline()->associate($request->get('ab_discipline')[$index]);

                            //study_plan_edition
                            $absence->study_plan_edition()->associate($id);

                            $absence->discipline_regime()->associate(null);

                            //regime
                            $absence->max_absences = $request->get('ab_max_absence')[$val][0];
                            $absence->is_total = 1;
                            $absence->save();
                        }
                    }
                }
            }

            // Success message
            Toastr::success(__('GA::discipline-absence-configuration.update_success_message'), __('toastr.success'));
            return redirect()->route('study-plan-editions.absences', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::discipline-absence-configuration.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function classes($id)
    {
        $study_plan_edition = StudyPlanEdition::with([
            'disciplines' => function ($q) {
                $q->with([
                    'currentTranslation'
                    /*'discipline' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }*/
                ]);
            },
            'studyPlan'
        ])->findOrFail($id);

        $course_year =  $study_plan_edition->course_year;
        $ab = $study_plan_edition->study_plans_id;

        /*$study_plan_edition = StudyPlanEdition::with([
            'classes'
        ])->findOrFail($id);*/

        $course_id = StudyPlan::with([
            'course'
        ])->findOrFail($ab);

        $cou_id =  $course_id->courses_id;

        $currentData = Carbon::now();
        $currentLective = DB::table('lective_years')
                        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                        ->first();

                        
        $classe = Classes::where('courses_id',$cou_id)
        ->where('lective_year_id',$currentLective->id)
        ->whereNull('deleted_at')
        ->whereNull('deleted_by')
        ->where('year', $course_year)->get();

        return $classe;
    }

    public function disciplines($id)
    {
        $study_plan_edition = StudyPlanEdition::with([
            'disciplines' => function ($q) {
                $q->with([
                    'currentTranslation'
                    /*'discipline' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }*/
                ]);
            }
        ])->findOrFail($id);

        return $study_plan_edition->disciplines;
    }

    public function periodTypes($id)
    {
        $study_plan_edition = StudyPlanEdition::with([
            'studyPlan'
        ])->findOrFail($id);

        $period_type =  $study_plan_edition->period_type_id;

        $get_period_type = PeriodType::with([
            'currentTranslation'
        ])->findOrFail($period_type);

        return $get_period_type;
    }

    public function scheduleTypes($id)
    {
        /*$study_plan_edition = StudyPlanEdition::with([
            'disciplines' => function ($q) {
                $q->with([
                    'currentTranslation'

                ]);
            },
            'studyPlan'
        ])->findOrFail($id);

        $course_year =  $study_plan_edition->course_year;
        $ab = $study_plan_edition->study_plans_id;


        $course_id = StudyPlan::with([
            'course'
        ])->findOrFail($ab);

        $cou_id =  $course_id->courses_id;

        $classe = Classes::where('courses_id',$cou_id)->where('year', $course_year)->get();*/

        $classe_schedule_type = Classes::where('id', $id)->firstOrFail();

        $schedule_type = ScheduleType::with(['currentTranslation'])->where('id', $classe_schedule_type->schedule_type_id)->get();
        return $schedule_type;
    }

    public function duplicateListItem($id)
    {
        try {
            $action = "edit";
            // Find
            $study_plan_edition = StudyPlanEdition::whereId($id)->with([
                        'translations' => function ($q) {
                            $q->whereActive(true);
                        },
                        'studyPlan' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        },
                        'disciplines' => function ($q) {
                            $q->with([
                                'study_plans_has_disciplines',
                                'currentTranslation'
                            ]);
                        },
                        'precedences' => function ($q) {
                            $q->with([
                                'discipline' => function ($q) {
                                    $q->with([
                                        'translation' => function ($q) {
                                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                                        }
                                    ]);
                                },
                                'parent' => function ($q) {
                                    $q->with([
                                        'translation' => function ($q) {
                                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                                        }
                                    ]);
                                },
                            ]);
                        },
                        'accessTypes' => function ($q) {
                            $q->with([
                                'accessType'
                            ]);
                        },
                        'periodTypes' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ])->firstOrFail();

            $lective_years = LectiveYear::with([
                        'translation' => function ($q) {
                            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                        }
                    ])->get();

            $data = [
                        //'access_types' => $access_types,
                        'action' => $action,
                        //'average_calculation_rules' => $acr,
                        //'discipline_periods' => $discipline_periods,
                        'languages' => Language::whereActive(true)->get(),
                        'lective_years' => $lective_years,
                        //'period_types' => $period_types,
                        'study_plan_edition' => $study_plan_edition,
                        //'study_plan_edition_disciplines' => $sped,
                        //'study_plans' => $study_plans,
                        'translations' => $study_plan_edition->translations->keyBy('language_id')->toArray(),
                        //'year_transition_rules' => $year_transition_rules,
                        //'period_type_id' => $period_type_id,
                        //'list_period' => $list_period
                    ];

            return response()->json($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plan-editions.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return abort(500);
        }

    }

    public function duplicateStudyPlan(Request $request)

    {

      
        try {
            DB::beginTransaction();

            $study_plan_edition_data = StudyPlanEdition::whereId($request->get('id'))->firstOrFail();
            $disciplines = [];

            $study_plan_edition_data->disciplines->map(function($item) use(&$disciplines)
            {
                $disciplines[] = $item->id;
            });

            $languages = Language::whereActive(true)->get();

            // Create
            $study_plan_edition = new StudyPlanEdition([
                'block_enrollments' => $study_plan_edition_data->block_enrollments,
                'max_enrollments' => $study_plan_edition_data->max_enrollments,
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'course_year' => $study_plan_edition_data->course_year,
                'period_type_id' => $study_plan_edition_data->period_type_id,
            ]);

            // Foreign keys
            $study_plan_edition->studyPlan()->associate($study_plan_edition_data->study_plans_id);
            $study_plan_edition->lectiveYear()->associate($request->get('lective_year'));
            $study_plan_edition->yearTransitionRule()->associate($study_plan_edition_data->year_transition_rules_id);
            $study_plan_edition->averageCalculationRule()->associate($study_plan_edition_data->average_calculation_rules_id);
            $study_plan_edition->save();

            ########################
            # Create - Disciplines #
            ########################
            //if ($request->get('dr_checked_disciplines') && count($request->get('dr_checked_disciplines'))) {
                // $checkedDisciplinesArray = array_map(function ($a) {
                //     return $a['id'];
                // }, $request->get('dr_checked_disciplines'));
                $study_plan_edition->disciplines()->sync($disciplines);
            //}

            ########################
            # Create - Precedences #
            ########################
            //if ($request->has(['discipline_precedence', 'precedence_precedence'])) {
                if ($study_plan_edition_data->precedences != null) {

                    foreach ($study_plan_edition_data->precedences as $index => $val) {
                        $precedence = new Precedence();

                        // Foreign keys
                        $precedence->discipline()->associate($study_plan_edition_data->precedences[$index]);
                        $precedence->parent()->associate($study_plan_edition_data->precedences[$index]);
                        $precedence->studyPlanEdition()->associate($study_plan_edition->id);

                        $precedence->save();
                    }

                }
            //}

            #########################
            # Create - Access Types #
            #########################

            if ($study_plan_edition_data->access_types != null) {

                $study_plan_editions_has_access_types = [];
                foreach ($$study_plan_edition_data->access_types as $access_type) {

                    if (isset($access_type['access_type_id'], $access_type['max_enrollments'])) {

                        $study_plan_editions_has_access_types[] = [
                            'spe_id' => $study_plan_edition->id,
                            'access_type_id' => $access_type['access_type_id'],
                            'max_enrollments' => $access_type['max_enrollments'],
                        ];

                    }
                }

                if (!empty($study_plan_editions_has_access_types)) {
                    StudyPlanEditionAccessType::insert($study_plan_editions_has_access_types);
                }
            }

            #########################
            # Create - Translations #
            #########################
            foreach ($languages as $language) {
                $study_plan_edition_translations[] = [
                    'study_plan_editions_id' => $study_plan_edition->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('language_display_name'),
                    'abbreviation' => $request->get('language_abreviation'),
                    'description' => $request->get('language_description'),
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($study_plan_edition_translations)) {
                StudyPlanEditionTranslation::insert($study_plan_edition_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plan-editions.store_success_message'), __('toastr.success'));
            return redirect()->route('study-plan-editions.index');

        } catch (Exception | Throwable $e) {
            return $e;
            Toastr::error($e->getMessage(), __('toastr.error'));
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }


    public function studyPlanEditionBy($lective_year)
    {
        try {

            $model = StudyPlanEdition::
            leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'study_plan_editions.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })
            ->leftJoin('lective_year_translations as lyt', function ($join) {
                $join->on('lyt.lective_years_id', '=', 'study_plan_editions.lective_years_id');
                $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyt.active', '=', DB::raw(true));
            })
            ->select([
                'study_plan_editions.*',
                'spet.display_name',
                'lyt.display_name as lective_year'
            ])
            ->where('study_plan_editions.lective_years_id', $lective_year);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::study-plan-editions.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }



    public function avaliacaoAdd($id_plano,$id_disciplina)
    {           

            //As consideradas checadas
            $avaiacaoChecada=DB::table('plano_estudo_avaliacaos as planoCheck')
            ->where('planoCheck.study_plan_editions_id',$id_plano)
            ->where('planoCheck.disciplines_id',$id_disciplina)
            ->where('planoCheck.disciplines_id',$id_disciplina)
            ->where('planoCheck.deleted_at',null)
            ->where('planoCheck.deleted_by',null)
            ->join('avaliacaos as avl', 'avl.id', '=','planoCheck.avaliacaos_id')
            ->select(['avl.id as id_avaliacao','avl.nome','planoCheck.id','planoCheck.disciplines_id as id_disciplina'])
            ->get();
            //fim

            //Pegar o ano lectivo
            $model = StudyPlanEdition::
            leftJoin('study_plan_edition_translations as spet', function ($join) {
                $join->on('spet.study_plan_editions_id', '=', 'study_plan_editions.id');
                $join->on('spet.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('spet.active', '=', DB::raw(true));
            })->leftJoin('lective_year_translations as lyt', function ($join) {
                $join->on('lyt.lective_years_id', '=', 'study_plan_editions.lective_years_id');
                $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyt.active', '=', DB::raw(true));
            })
            ->select([
                'lyt.lective_years_id'
            ])
            ->where('study_plan_editions_id', $id_plano)
            ->first();
        // fim do methodo
        //avalicao todas
        $avaliacao=DB::table('avaliacaos')
        ->where('anoLectivo',$model->lective_years_id)
        ->get()
        ;

        //Pegar se tem exame obrigatório ou não
        $consulta_exame=DB::table('discipline_has_exam')
        ->where('discipline_id',$id_disciplina)
        ->where('id_plain_study',$id_plano)
        ->first();
        // dd($consulta_exame);
         $exame= $consulta_exame!= null ? $consulta_exame->has_mandatory_exam:0;

        try {
            return response()->json(["avaiacaoChecada"=>$avaiacaoChecada, "avaliacaoGeral"=>$avaliacao,"exame_obrigatorio"=>$exame]); 
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }












    public function consultAdd_avalicao(Request $request)
    {

        if(!isset($request->exame_obriga)){
            $exame_obriga=0;  
        }else{ $exame_obriga=1;}
        try {
        $id_user=Auth::user()->id;
        $currentData = Carbon::now();
        $avl=null;
        if(isset($request->checadas)){$avl=$request->get('checadas');}else{$avl=0; }

        $data = [
            'discipline_id' => $request->get('id_disciplina'),
            'avalicoes'=> $avl,
            'exame_obriga'=> $exame_obriga,
            'id_plano' => $request->get('id_plano'),
            //'tipo_avaliacaos_id' => $request->get('avaliations_type')
        ];
        
        if($data['avalicoes']>0){

            DB::table('plano_estudo_avaliacaos')
            ->where('disciplines_id',$data['discipline_id'])
            ->where('study_plan_editions_id',$data['id_plano'])
            ->whereNotIn('avaliacaos_id', $data['avalicoes'])
            ->update(
             [
              'updated_by' => $id_user,
              'deleted_by' => $id_user,
              'updated_at' => $currentData,
              'deleted_at' => $currentData
             ]);


             DB::table('discipline_has_exam')
             ->updateOrInsert(
                 [
                     'discipline_id' => $data['discipline_id'],
                     'id_plain_study' => $data ['id_plano'] 
                ],
                 [
                'discipline_id' => $data['discipline_id'],
                'id_plain_study' => $data ['id_plano'],
                'has_mandatory_exam' => $data['exame_obriga']
                ] 
             ); 

            for ($i=0; $i < count($data['avalicoes']); $i++) {
                

                   $teste= DB::table('plano_estudo_avaliacaos')->updateOrInsert(
                        [
                        'disciplines_id' => $data['discipline_id'],
                        'study_plan_editions_id' => $data['id_plano'],
                        'avaliacaos_id'=> $data['avalicoes'][$i]
                        ],
        
                        [
                            'avaliacaos_id'=> $data['avalicoes'][$i],
                            'created_by' => $id_user,
                            'updated_by' => $id_user,
                            'deleted_by' => null,
                            'created_at' => $currentData,
                            'updated_at' => $currentData,
                            'deleted_at' => null
                        ]
                    );
                    DB::table('discipline_has_exam')
                    ->updateOrInsert(
                        [
                        'discipline_id' => $data['discipline_id'],
                        'id_plain_study' => $data ['id_plano'] 

                    ],
                    [
                    'discipline_id' => $data['discipline_id'],
                    'id_plain_study' => $data ['id_plano'],
                    'has_mandatory_exam' => $data['exame_obriga']
                    ] 
                );
                }
             }

        else{   
            DB::table('plano_estudo_avaliacaos')
            ->where('disciplines_id', $data['discipline_id'])
            ->where('study_plan_editions_id',  $data['id_plano'])
            ->update(

             [
              'updated_by' => $id_user,
              'deleted_by' => $id_user,
              'updated_at' => $currentData,
              'deleted_at' => $currentData

             ] ); 
             DB::table('discipline_has_exam')
             ->updateOrInsert(
                [
                    'discipline_id' => $data['discipline_id'],
                    'id_plain_study' => $data ['id_plano'] 

               ],
                [
               'discipline_id' => $data['discipline_id'],
               'id_plain_study' => $data ['id_plano'],
               'has_mandatory_exam' => $data['exame_obriga']
               ]  
             );

        }

        Toastr::success(__('Os dados foram salvos com sucesso!'), __('toastr.success'));
        return redirect()->back();
       // return view('GA::study-plan-editions.study-plan-edition.blade.php');
        } catch (Exception | Throwable $e) {
            // return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    

}
