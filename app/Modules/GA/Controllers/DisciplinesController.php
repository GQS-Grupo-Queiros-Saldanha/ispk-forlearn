<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\DisciplineArea;
use App\Modules\GA\Models\DisciplineProfile;
use App\Modules\GA\Models\DisciplineTranslation;
use App\Modules\GA\Requests\DisciplineRequest;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;
use App\Model\Institution;
use PDF;
use Illuminate\Http\Request;

class DisciplinesController extends Controller
{
    public function index()
    {
        try {
            return view('GA::disciplines.index');
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
            $model = Discipline::join('users as u1', 'u1.id', '=', 'disciplines.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'disciplines.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'disciplines.deleted_by')
                ->leftJoin('discipline_has_areas as dha', 'disciplines.id', '=', 'dha.discipline_id')
                ->leftJoin('discipline_areas_translations as dat', function ($join) {
                    $join->on('dat.discipline_areas_id', '=', 'dha.discipline_area_id');
                    $join->on('dat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dat.active', '=', DB::raw(true));
                })
                ->leftJoin('discipline_profiles as dp', 'dp.id', '=', 'disciplines.discipline_profiles_id')
                ->leftJoin('discipline_profile_translations as dpt', function ($join) {
                    $join->on('dpt.discipline_profiles_id', '=', 'dp.id');
                    $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dpt.active', '=', DB::raw(true));
                })
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'disciplines.id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'disciplines.id',
                    'disciplines.code',
                    'disciplines.uc',
                    'disciplines.percentage', // Percentagem adicionada
                    'disciplines.mandatory_discipline',
                    'disciplines.created_at',
                    'disciplines.updated_at',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name',
                    DB::raw('GROUP_CONCAT(dat.display_name) as areas'),
                    'dpt.display_name as profile',
                    'ct.display_name as course_name'
                ])
                ->groupBy([
                    'disciplines.id',
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::disciplines.datatables.actions')->with('item', $item);
                })
                ->editColumn('mandatory_discipline', function ($item) {
                    return $item->mandatory_discipline !== null ? "sim" : "Não";
                })

                /*   ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions', 'mandatory_discipline'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            $areas = DisciplineArea::with([
                'currentTranslation'
            ])->get();

            $profiles = DisciplineProfile::with([
                'currentTranslation'
            ])->get();

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $data = [
                'action' => 'create',
                'courses' => $courses,
                'areas' => $areas,
                'profiles' => $profiles,
                'languages' => Language::whereActive(true)->get(),
                'percentage' => 'percentage',
            ];
            return view('GA::disciplines.discipline')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisciplineRequest $request
     * @return Response
     */
    public function store(DisciplineRequest $request)
    {
        // return "wewqe";
        try {
            if ($request->get('course') == null) {
                Toastr::warning(__('Atenção!! não foi possivel criar uma nova disciplina porque nenhum curso foi selecionado; Tente novamente.'), __('toastr.warning'));
                return redirect()->route('disciplines.index');
            }

            $isAdmissionExam = $request->input('profile') == 8;

            // Verifica se o campo percentage é obrigatório com base na seleção
            if ($isAdmissionExam && !isset($request->percentage)) {
                Toastr::warning(__('Atenção!! O campo percentual não foi preenchido corretamente. Tente novamente.'), __('toastr.warning'));
                //return redirect()->route('disciplines.index');
                return back();
            }

            DB::beginTransaction();
            // Create 
            $discipline = new Discipline([
                'code' => $request->get('code'),
                'uc' => $request->get('uc') ?: null,
                'courses_id' => $request->get('course') ?: null,
                'percentage' => $request->get('percentage') ?: null, 
                'mandatory_discipline' => $request->input('mandatory_discipline')?? null,
                'tfc' => $request->input('tfc') ? 1:0
            ]);


            // Associations
            $discipline->disciplineProfile()->associate($request->get('profile'));
            $discipline->save();
            $discipline->disciplineAreas()->sync($request->get('discipline_areas'));
            $discipline->save();

            // Create translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                
                 $topics = $request->get('topics')[$language->id] ?? null;
        $topicsArray = $topics ? explode(':', $topics) : [];

        $bibliography = $request->get('bibliography')[$language->id] ?? null;
        $bibliographyArray = $bibliography ? explode(':', $bibliography) : [];
                
                $discipline_translations[] = [
                    'discipline_id' => $discipline->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id],
                    'description' => $request->get('description')[$language->id],
                   'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
          'objectives' => $request->get('objectives')[$language->id] ?? null,
          'learning_outcomes' => $request->get('learning_outcomes')[$language->id] ?? null,
          'topics' => json_encode($topicsArray),
          'bibliography' => json_encode($bibliographyArray),
          'teaching_methods' => $request->get('teaching_methods')[$language->id] ?? null,
          'assessment_strategy' => $request->get('assessment_strategy')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => 1,
                    'active' => true
                ];
            }

            if (!empty($discipline_translations)) {
                DisciplineTranslation::insert($discipline_translations);
            }

            DB::commit();
            // Success message
            Toastr::success(__('GA::disciplines.store_success_message'), __('toastr.success'));
            return redirect()->route('disciplines.index');
        } catch (Exception $e) {
            logError($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
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
      $discipline = Discipline::whereId($id)->with([
        'translations' => function ($q) {
          $q->whereActive(true);
        },
        'disciplineAreas' => function ($q) {
          $q->with([
            'currentTranslation'
          ]);
        },
        'disciplineProfile' => function ($q) {
          $q->with([
            'currentTranslation'
          ]);
        },
        'course' => function ($q) {
          $q->with([
            'currentTranslation'
          ]);
        },
      ])->firstOrFail();

      $courses = Course::with([
        'currentTranslation'
      ])->get();

      $areas = DisciplineArea::with([
        'currentTranslation'
      ])->get();

      $profiles = DisciplineProfile::with([
        'currentTranslation'
      ])->get();

      $hasMandatoryExam = DB::table('discipline_has_exam')
        ->where('discipline_id', $id)
        ->get();
      // //obter as disciplinas traduzidas
      //             $disciplines = DisciplineTranslate::pluck('display_name', 'discipline_id');                    
      //dd($discipline->maximum_absence);
      $translations = $discipline->translations->keyBy('language_id')->toArray();
      foreach ($translations as &$translation) {
        $topics = json_decode($translation['topics'], true);
        if (is_array($topics)) {
          $translation['topics'] = implode(":\n", array_map('html_entity_decode', $topics));
        } else {
          $translation['topics'] = ''; // Ou qualquer valor padrão apropriado
        }

        $bibliography = json_decode($translation['bibliography'], true);
        if (is_array($bibliography)) {
          $translation['bibliography'] = implode(":\n", array_map('html_entity_decode', $bibliography));
        } else {
          $translation['bibliography'] = ''; // Ou qualquer valor padrão apropriado
        }
      }
      $data = [
        'action' => $action,
        'discipline' => $discipline,
        'translations' => $translations,
        'courses' => $courses,
        'areas' => $areas,
        'profiles' => $profiles,
        'languages' => Language::whereActive(true)->get(),
        'hasMandatoryExam' => $hasMandatoryExam,
        'percentage' => 'percentage',
        // '0disciplines' => $discipline    
      ];

      return view('GA::disciplines.discipline')->with($data);
    } catch (ModelNotFoundException $e) {
      Toastr::error(__('GA::disciplines.not_found_message'), __('toastr.error'));
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

    /**
     * Update the specified resource in storage.
     *
     * @param DisciplineRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(DisciplineRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $discipline = Discipline::whereId($id)->firstOrFail();
            $discipline->code = $request->get('code');
            $discipline->courses_id = $request->get('course') ?: null;
            $discipline->maximum_absence = $request->get('absence');
            $discipline->uc = $request->get('uc') ?: null;
            $discipline->percentage = $request->get('percentage'); 
            $discipline->mandatory_discipline = $request->get('mandatory_discipline')??null; 
           
            $discipline->tfc = $request->get('tfc')? 1:0;
            //Associations
            $discipline->disciplineAreas()->sync($request->get('discipline_areas'));
            $discipline->disciplineProfile()->dissociate();
            $discipline->disciplineProfile()->associate($request->get('profile'));

            $discipline->save();

            // Disable previous translations
            DisciplineTranslation::where('discipline_id', $discipline->id)->update(['active' => false]);

            $version = DisciplineTranslation::where('discipline_id', $discipline->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                
                 $topics = $request->get('topics')[$language->id] ?? null;
        $cleanedTopics = str_replace(["\r", "\n"], '', $topics);
        $topicsArray = $cleanedTopics ? explode(':', $cleanedTopics) : [];

        $bibliography = $request->get('bibliography')[$language->id] ?? null;
        $cleanedBibliography = str_replace(["\r", "\n"], '', $bibliography);
        $bibliographyArray = $cleanedBibliography ? explode(':', $cleanedBibliography) : [];
                
                $discipline_translations[] = [
                    'discipline_id' => $discipline->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'objectives' => $request->get('objectives')[$language->id] ?? null,
          'learning_outcomes' => $request->get('learning_outcomes')[$language->id] ?? null,
          'topics' => json_encode($topicsArray),
          'bibliography' => json_encode($bibliographyArray),
          'teaching_methods' => $request->get('teaching_methods')[$language->id] ?? null,
          'assessment_strategy' => $request->get('assessment_strategy')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($discipline_translations)) {
                DisciplineTranslation::insert($discipline_translations);
            }

            // DB::table('discipline_has_exam')
            //     ->updateOrInsert(
            //         ['discipline_id' => $discipline->id],
            //         ['has_mandatory_exam' => $request->get('mandatory_exam')]
            //     );

            DB::commit();

            // Success message
            Toastr::success(__('GA::disciplines.update_success_message'), __('toastr.success'));
            return redirect()->route('disciplines.show', $id);
        } catch (ModelNotFoundException $e) {
            logError($e);
            Toastr::error(__('GA::disciplines.not_found_message'), __('toastr.error'));
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $discipline = Discipline::whereId($id)->firstOrFail();
            $discipline->disciplineAreas()->sync([]);
            $discipline->disciplineProfile()->dissociate();
            //$discipline->delete();
            $discipline->deleted_at = now();
            $discipline->deleted_by = Auth::user()->id;
            $discipline->save();

            DB::commit();

            // Success message
            Toastr::success(__('GA::disciplines.destroy_success_message'), __('toastr.success'));
            return redirect()->route('disciplines.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::disciplines.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
      public function fetchPDF($id)
  {
    try {
      // Obtendo os dados da disciplina e seus relacionamentos
      $discipline = Discipline::whereId($id)->with([
        'translations' => function ($q) {
          $q->whereActive(true);
        },
        'disciplineAreas' => function ($q) {
          $q->with(['currentTranslation']);
        },
        'disciplineProfile' => function ($q) {
          $q->with(['currentTranslation']);
        },
        'course' => function ($q) {
          $q->with(['currentTranslation']);
        },
        'study_plans_has_disciplines' => function ($q) {
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
        },
      ])->firstOrFail();

      $courses = Course::with(['currentTranslation'])->get();
      $areas = DisciplineArea::with(['currentTranslation'])->get();
      $profiles = DisciplineProfile::with(['currentTranslation'])->get();

      $hasMandatoryExam = DB::table('discipline_has_exam')
        ->where('discipline_id', $id)
        ->get();

      // Verifique se há dados suficientes para gerar o PDF
      if (!$discipline || $courses->isEmpty() || $areas->isEmpty() || $profiles->isEmpty()) {
        return abort(404, 'Dados insuficientes para gerar o PDF.');
      }

      $plano_regime = DB::table('study_plans_has_disciplines as sthd')
        ->join("sp_has_discipline_regimes as sthdr", "sthdr.sp_has_disciplines_id", "=", "sthd.id")
        ->join("discipline_regimes as dr", "dr.id", "=", "sthdr.discipline_regimes_id")
        ->where("sthd.disciplines_id", $id)
        ->select([
          'sthd.id as id',
          'sthdr.discipline_regimes_id as regime',
          'sthdr.hours as horas',
          'dr.code as codigo',

        ])
        ->get();

      // Obtendo a instituição
      $institution = Institution::latest()->first();
      $languages = Language::whereActive(true)->get();
      $translations = $discipline->translations->keyBy('language_id')->toArray();

      // Definindo os dados para o PDF
      $titulo_documento = "Relatório de Disciplina " . date("Y/m/d");
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      // Gerando o PDF
      $pdf = PDF::loadView(
        'GA::disciplines.pdf.relatorio-pdf',
        compact(
          'translations',
          'languages',
          'discipline',
          'courses',
          'areas',
          'profiles',
          'hasMandatoryExam',
          'institution',
          'titulo_documento',
          'documentoGerado_documento'
        )
      );

      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '4mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 3000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'portrait');

      $pdf_name = "PCdD_" . $discipline->code;

      return $pdf->stream($pdf_name . '.pdf');

    } catch (Exception $e) {
      // Trate a exceção adequadamente
      logError($e);
      return response()->json($e->getMessage(), 500);
    }
  }

  public function update_name($course)
    {
        
        try {
            
            DB::beginTransaction();
            $string = '%Ingês%';
            $disciplines = DB::table('disciplines as u')
                        ->whereNull('u.deleted_at')
                        ->whereNull('u.deleted_by')
                        ->join('disciplines_translations as dt','dt.discipline_id','u.id')
                        ->where('dt.active',1)
                        ->join('courses as uc','uc.id','u.courses_id')
                        ->where('uc.id',$course)
                        ->where('dt.display_name','like',$string)
                        ->select(['u.id as id','dt.display_name as old'])
                        ->distinct('u.id')
                        ->get();

        
                    
            $disciplines->each(function($discipline){

              $new = str_replace('Ingês','Inglês',$discipline->old);

                    DB::table('disciplines_translations')
                    ->where('discipline_id',$discipline->id)
                    ->update([
                        'display_name' => $new,
                        'description' => $new,
                        'updated_at' => Carbon::now()
                    ]);
            });

            

            DB::commit();

            dd('sucesso',$course);

        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
}

}