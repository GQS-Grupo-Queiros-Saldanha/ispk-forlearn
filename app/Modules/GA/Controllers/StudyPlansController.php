<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplinePeriod;
use App\Modules\GA\Models\DisciplineRegime;
use App\Modules\GA\Models\OptionalGroup;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\GA\Models\StudyPlanHasDiscipline;
use App\Modules\GA\Models\StudyPlanHasDisciplineRegime;
use App\Modules\GA\Models\StudyPlanHasOptionalGroup;
use App\Modules\GA\Models\StudyPlanTranslation;
use App\Modules\GA\Requests\StudyPlanRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use PDF;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Illuminate\Http\Request;
use Throwable;
use Toastr;
use Auth;
use  App\Model\Institution;


class StudyPlansController extends Controller
{

    public function updateHorario(Request $request, $id){
        $study_plan = StudyPlan::whereId($id)->with([
            'study_plans_has_disciplines' => function($q) use ($request){
                $q->where('disciplines_id', $request->discipline_id)
                  ->where('discipline_periods_id',$request->periodo_id)
                  ->where('years', $request->ano)
                  ->first();
            }   
        ])->first();
        
        if(!isset($study_plan->study_plans_has_disciplines[0]->id)){
            Toastr::error('A forLEARN detectou que a disciplina não adicionada no plano de estudo', __('toastr.error'));
            return redirect()->back();
        }
        
        $study_plans_has_disciplines = $study_plan->study_plans_has_disciplines[0]; 
        $regimes = $request->regimes ?? [];
        $horas = $request->horas ?? [];
        
        if(sizeof($regimes) != sizeof($horas)){
            Toastr::error('A forLEARN detectou que o total de regimes é diferente do total de horas', __('toastr.error'));
            return redirect()->back();
        }
        
        $total_horas = 0;
        for($i = 0; $i < sizeof($regimes); $i++){
            if(isset($regimes[$i], $horas[$i])){
        
                $has_discipline_regimes = DB::table('sp_has_discipline_regimes')->where([
                    'sp_has_disciplines_id' => $study_plans_has_disciplines->id,
                    'discipline_regimes_id' => $regimes[$i]
                ])->first();
                
                if(isset($has_discipline_regimes->id_span)){
                    DB::table('sp_has_discipline_regimes')->where('id_span',$has_discipline_regimes->id_span)
                                                          ->update(['hours' => $horas[$i] ]);
                    $total_horas += $horas[$i];
                }
                
            }
        }
        
        if(isset($request->hora_total)){
            $study_plans_has_disciplines->update(["total_hours" => $request->hora_total]);
        }
        
        Toastr::success('Foi realizado com sucesso alteração das horas dos regimes no plano de estudo', __('toastr.success'));
        return redirect()->route('study-plans.edit',$id);
    }    
    
    public function index()
    {
        try {
            return view('GA::study-plans.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {

            $model = StudyPlan::join('users as u1', 'u1.id', '=', 'study_plans.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'study_plans.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'study_plans.deleted_by')
                ->leftJoin('courses as c', 'c.id', '=', 'study_plans.courses_id')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'c.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('study_plan_translations as spt', function ($join) {
                    $join->on('spt.study_plans_id', '=', 'study_plans.id');
                    $join->on('spt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('spt.active', '=', DB::raw(true));
                })
                ->select([
                    'ct.display_name as course_name',
                    'study_plans.id',
                    'study_plans.code',
                    'study_plans.created_at',
                    'study_plans.updated_at',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'spt.display_name',
                    'spt.abbreviation'
                ]);

            return Datatables::eloquent($model)->addColumn('actions', function ($item) {
                return view('GA::study-plans.datatables.actions')->with('item', $item);
            })
                /*
                 ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                 ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                 ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })
                */

                ->rawColumns(['actions'])
                ->toJson();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $disciplines = disciplinesSelect();

            $discipline_periods = DisciplinePeriod::with([
                'currentTranslation'
            ])->get();

            $discipline_regimes = DisciplineRegime::with([
                'currentTranslation'
            ])->get();


            $data = [
                'action' => 'create',
                'courses' => $courses,
                'discipline_periods' => $discipline_periods,
                'disciplines_course' => $disciplines,
                'disciplines' => $disciplines,
                'discipline_regimes' => $discipline_regimes,
                'languages' => Language::whereActive(true)->get()
            ];

            return view('GA::study-plans.study-plan')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StudyPlanRequest $request
     * @return Response
     */
    public function store(StudyPlanRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create
            $study_plan = new StudyPlan([
                'code' => $request->get('code')
            ]);

            // Course
            $study_plan->course()->associate($request->get('course'));
            $study_plan->save();

            //            // Create Study Plans Optional Groups
            //            if ($request->has('dp_optional_groups')) {
            //
            //                foreach ($request->get('dp_optional_groups') as $index => $val) {
            //
            //                    $study_plan_optional_groups[] = [
            //                        'study_plans_id' => $study_plan->id,
            //                        'optional_groups_id' => $val, // or $request->get('dp_optional_groups')[$counter],
            //                        'discipline_periods_id' => $request->get('dp_discipline_periods')[$index],
            //                        'year' => $request->get('dp_years')[$index]
            //                    ];
            //                }
            //
            //                if (!empty($study_plan_optional_groups)) {
            //                    StudyPlanHasOptionalGroup::insert($study_plan_optional_groups);
            //                }
            //            }

            $reIndexUniquesRegimes = $this->reIndexDynamicTablesUniqueElemetsRowIds($request->all(), 'dr_discipline_regimes_');
            $reIndexUniquesRegimesHours = $this->reIndexDynamicTablesUniqueElemetsRowIds($request->all(), 'dr_discipline_regimes_hours_');

            // Create Study Plans Disciplines Regimes
            if ($request->has('dr_discipline_periods')) {
                foreach ($request->get('dr_discipline_periods') as $index => $val) {
                    $study_plan_has_disciplines = new StudyPlanHasDiscipline([
                        'study_plans_id' => $study_plan->id,
                        'discipline_periods_id' => $val, // or $request->get('dr_discipline_periods')[$counter],
                        'disciplines_id' => $request->get('dr_disciplines')[$index],
                        'years' => $request->get('dr_years')[$index],
                        'total_hours' => $request->get('dr_total_hours')[$index],
                    ]);
                    $study_plan_has_disciplines->save();

                    // insert study plan disciplines
                    if ($request->has('dr_discipline_regimes_' . $reIndexUniquesRegimes[$index] . '_')) {
                        $study_plan_has_discipline_regimes = [];
                        foreach ($request->get('dr_discipline_regimes_' . $reIndexUniquesRegimes[$index] . '_') as $index2 => $val2) {
                            $study_plan_has_discipline_regimes[] = [
                                'sp_has_disciplines_id' => $study_plan_has_disciplines->id,
                                'discipline_regimes_id' => $val2, // or $request->get('dr_discipline_regimes_' . $index . '_')[$index2],
                                'hours' => $request->get('dr_discipline_regimes_hours_' . $reIndexUniquesRegimesHours[$index] . '_')[$index2],
                            ];
                        }

                        //insert study plan discipline regimes
                        if (!empty($study_plan_has_discipline_regimes)) {
                            StudyPlanHasDisciplineRegime::insert($study_plan_has_discipline_regimes);
                        }
                    }
                }
            }

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $study_plan_translations[] = [
                    'study_plans_id' => $study_plan->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'abbreviation' => $request->get('abbreviation')[$language->id],
                    'description' => $request->get('description')[$language->id],
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($study_plan_translations)) {
                StudyPlanTranslation::insert($study_plan_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plans.store_success_message'), __('toastr.success'));
            return redirect()->route('study-plans.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    protected function reIndexDynamicTablesUniqueElemetsRowIds($requestArray, $searchString)
    {
        $indexMap = [];
        $searchPieces = explode('_', $searchString);

        foreach (array_keys($requestArray) as $key) {
            $keyPieces = explode('_', $key);
            if (count($searchPieces) + 1 === count($keyPieces)) {
                $compareIndex = count($searchPieces) - 2;
                $foundMatch = true;
                while ($compareIndex >= 0 && $foundMatch) {
                    $foundMatch = $searchPieces[$compareIndex] === $keyPieces[$compareIndex];
                    $compareIndex--;
                }

                if ($foundMatch) {
                    $indexMap[] = (int)$keyPieces[count($searchPieces) - 1];
                }
            }
        }

        return $indexMap;
    }

    public function fetchAjax($id)
    {
        return $this->fetch($id, 'ajax');
    }

    private function fetch($id, $action)
    {
        try {
            // Find study plan
            $study_plan = StudyPlan::whereId($id)->with([
                'course' => function ($q) {
                    $q->with([
                        'currentTranslation'
                    ]);
                }, 'study_plans_has_disciplines' => function ($q) {
                    $q->with([
                        'discipline' => function ($q) {
                            $q->with([
                                'currentTranslation',
                                'disciplineAreas' => function ($q) {
                                    $q->with([
                                        'translations'
                                    ]);
                                }
                            ]);
                        },
                        'study_plans_has_discipline_regimes' => function ($q) {
                            $q->with([
                                'discipline_regime' => function ($q) {
                                    $q->with([
                                        'currentTranslation'
                                    ]);
                                }
                            ]);
                        },
                        'discipline_period' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ])
                                ->orderBy("code");
                        }
                    ])

                        ->orderBy('years')
                        ->orderBy('discipline_periods_id');
                }
            ])
                ->with('course')->firstOrFail();

            // If its an ajax request
            if ($action === 'ajax') {
               
                return $study_plan;
            }

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $discipline_periods = DisciplinePeriod::with([
                'currentTranslation'
            ])
                ->get();

            $discipline_regimes = DisciplineRegime::with([
                'currentTranslation'
            ])->get();

            $disciplines = disciplinesSelect();
            $curso_id[] = $study_plan->courses_id ?? "";
            $disciplines_course = disciplinesSelect($curso_id, null);


            $data = [
                'action' => $action,
                'study_plan' => $study_plan,
                'courses' => $courses,
                'discipline_periods' => $discipline_periods,
                'discipline_regimes' => $discipline_regimes,
                'disciplines' => $disciplines,
                'disciplines_course' => $disciplines_course,
                'translations' => $study_plan->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];


            return view('GA::study-plans.study-plan')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plans.not_found_message'), __('toastr.error'));
            logError($e);
            return $e;
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
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
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StudyPlanRequest $request
     * @param int $id
     * @return Response
     */
    public function update(StudyPlanRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $study_plan = StudyPlan::whereId($id)->firstOrFail();

            // Update course
            $study_plan->course()->dissociate();
            $study_plan->course()->associate($request->get('course'));

            // Delete optional groups
            $study_plan->study_plans_has_optional_groups()->delete();

            // Update optional groups
            if ($request->has('dp_optional_groups')) {
                foreach ($request->get('dp_optional_groups') as $index => $val) {
                    $study_plan_optional_groups[] = [
                        'study_plans_id' => $study_plan->id,
                        'optional_groups_id' => $val, // or $request->get('dp_optional_groups')[$index],
                        'discipline_periods_id' => $request->get('dp_discipline_periods')[$index],
                        'year' => $request->get('dp_years')[$index]
                    ];
                }
            }
            if (!empty($study_plan_optional_groups)) {
                StudyPlanHasOptionalGroup::insert($study_plan_optional_groups);
            }

            // Remove all study plan disciplines regimes
            $sp_disciplines = $study_plan->study_plans_has_disciplines;
            foreach ($sp_disciplines as $sp_discipline) {
                $sp_discipline->study_plans_has_discipline_regimes()->delete();
                $sp_discipline->delete();
            }

            $reIndexUniquesRegimes = $this->reIndexDynamicTablesUniqueElemetsRowIds($request->all(), 'dr_discipline_regimes_');
            $reIndexUniquesRegimesHours = $this->reIndexDynamicTablesUniqueElemetsRowIds($request->all(), 'dr_discipline_regimes_hours_');

            // Create Study Plans Disciplines Regimes
            // return $study_plan->id;
            if ($request->has('dr_discipline_periods')) {
                $arrayKeys = array_keys($reIndexUniquesRegimes);
                foreach ($request->get('dr_discipline_periods') as $index => $val) {
                    if (in_array($index, $arrayKeys)) {
                        $study_plan_has_disciplines = new StudyPlanHasDiscipline([
                            'study_plans_id' => $study_plan->id,
                            'discipline_periods_id' => $val, // or $request->get('dr_discipline_periods')[$index],
                            'disciplines_id' => $request->get('dr_disciplines')[$index],
                            'years' => $request->get('dr_years')[$index],
                            'total_hours' => $request->get('dr_total_hours')[$index],
                        ]);
                        $study_plan_has_disciplines->save();

                        // Insert study plan disciplines
                        if ($request->has('dr_discipline_regimes_' . $reIndexUniquesRegimes[$index] . '_')) {
                            // return $request;
                            $study_plan_has_discipline_regimes = [];
                            foreach ($request->get('dr_discipline_regimes_' . $reIndexUniquesRegimes[$index] . '_') as $index2 => $val2) {
                                // dd($reIndexUniquesRegimes[$index]);

                                $study_plan_has_discipline_regimes[] = [
                                    'sp_has_disciplines_id' => $study_plan_has_disciplines->id,
                                    'discipline_regimes_id' => $val2, //or $request->get('dr_discipline_regimes_' . $index . '_')[$regimes_counter],
                                    'hours' => $request->get('dr_discipline_regimes_hours_' . $reIndexUniquesRegimesHours[$index] . '_')[$index2],
                                ];
                            }
                            //Insert study plan discipline regimes ------>>--->>
                            if (!empty($study_plan_has_discipline_regimes)) {
                                StudyPlanHasDisciplineRegime::insert($study_plan_has_discipline_regimes);
                            }
                        }
                    }
                }
            }

            $study_plan->code = $request->get('code');
            $study_plan->save();

            // Disable previous translations
            StudyPlanTranslation::where('study_plans_id', $study_plan->id)->update(['active' => false]);

            $version = StudyPlanTranslation::where('study_plans_id', $study_plan->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $study_plan_translations[] = [
                    'study_plans_id' => $study_plan->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($study_plan_translations)) {
                StudyPlanTranslation::insert($study_plan_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plans.update_success_message'), __('toastr.success'));
            return redirect()->route('study-plans.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plans.not_found_message'), __('toastr.error'));
            logError($e);
            return $e;
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function generate_pdf($id)
    {



        $plano = DB::table('study_plans as plano')
            ->join("study_plan_translations as p_t", "p_t.study_plans_id", "=", "plano.id")
            ->join("courses as curso", "curso.id", "=", "plano.courses_id")
            ->join("study_plans_has_disciplines as st_has_d", "st_has_d.study_plans_id", "=", "plano.id")
            ->join("disciplines as disci", "st_has_d.disciplines_id", "=", "disci.id")
            ->join("disciplines_translations as dt", "dt.discipline_id", "=", "disci.id")
            ->join("discipline_periods as dp", "st_has_d.discipline_periods_id", "=", "dp.id")
            ->join("discipline_period_translations as prd_t", "dp.id", "=", "prd_t.discipline_periods_id")
            ->where("plano.id", $id)
            ->where("p_t.active", 1)
            ->where("prd_t.active", 1)
            ->where("dt.active", 1)
            ->where("prd_t.language_id", 1)
            ->select([
                'plano.id as plano_id',
                'plano.courses_id as curso_id',
                'p_t.display_name as nome_plano',
                'dt.display_name as nome_disciplina',
                'curso.code as curso_code',
                'disci.code as code_disci',
                'disci.id as id_disci',
                'st_has_d.id as st_has_d_id',
                'st_has_d.discipline_periods_id as period_id',
                'st_has_d.years as ano',
                'st_has_d.total_hours as total',
                'prd_t.display_name as period_nome',
            ])

            ->orderBy('st_has_d.years', 'asc')
            ->orderBy('prd_t.display_name')
            ->get();

        $plano_regime = DB::table('study_plans_has_disciplines as sthd')
            ->join("sp_has_discipline_regimes as sthdr", "sthdr.sp_has_disciplines_id", "=", "sthd.id")
            ->join("discipline_regimes as dr", "dr.id", "=", "sthdr.discipline_regimes_id")
            ->where("sthd.study_plans_id", $id)
            ->select([
                'sthd.id as id',
                'sthdr.discipline_regimes_id as regime',
                'sthdr.hours as horas',
                'dr.code as codigo',
            ])
            ->get();

        // $corpo = $request->get("corpo-pdf");
        // $corpo = str_replace('"table table-hover"', '"table table-striped table-hover"', $corpo);




        //dados da instituição  
        $institution = Institution::latest()->first();
        $titulo_documento = "Plano de estudo";
        $documentoGerado_documento = "Documento gerado a " . (date('d-m-Y'));
        $documentoCode_documento = 1;



        //instaciando o PDF  

        $pdf = PDF::loadView("GA::study-plans.pdf.pdf", compact(
            'institution',
            'plano',
            'plano_regime',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));

        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = " ";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $study_plan = StudyPlan::whereId($id)->firstOrFail();

            // Delete translations
            $study_plan->translations()->delete();

            // Delete optional groups
            $study_plan->study_plans_has_optional_groups()->delete();

            // Remove all study plan disciplines regimes
            $sp_disciplines = $study_plan->study_plans_has_disciplines;
            foreach ($sp_disciplines as $sp_discipline) {
                $sp_discipline->study_plans_has_discipline_regimes()->delete();
                $sp_discipline->delete();
            }

            //$study_plan->delete();
            $study_plan->deleted_at = now();
            $study_plan->deleted_by = Auth::user()->id;
            $study_plan->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::study-plans.destroy_success_message'), __('toastr.success'));
            return redirect()->route('study-plans.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::study-plans.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (QueryException $e) {
            // Integrity violation
            if ($e->getCode() === '23000') {
                Toastr::error(__('GA::study-plans.destroy_integrity_violation_message'), __('toastr.error'));
            }
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
