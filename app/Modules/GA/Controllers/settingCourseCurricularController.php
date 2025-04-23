<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\GA\Models\LectiveYearTranslation;
use App\Modules\GA\Requests\LectiveYearRequest;
use Carbon\Carbon;
use DataTables;
use App\Modules\GA\Models\Course;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Toastr;
use App\Modules\Users\util\MatriculationUtil;
use App\Modules\Users\AnulateMatriculationController;

class settingCourseCurricularController extends Controller
{

    public function list() {        

        try {

            // $courses=Course::with(['currentTranslation'])->get();
            // return view('GA::setting-course-curricular.list',compact('courses'));
            
            $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('GA::setting-course-curricular.list', compact('lectiveYears', 'lectiveYearSelected'));


        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }

    public function ListAjax()
    {
        try {

        //    return 
           $model = LectiveYear::join('course_curricular_block as course_year_block', 'course_year_block.id_lective_year', '=', 'lective_years.id')
            ->join('users as u1', 'u1.id', '=', 'course_year_block.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'course_year_block.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'course_year_block.deleted_by')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })

                ->leftJoin('courses as crs', 'crs.id', '=', 'course_year_block.id_course')
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'crs.id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->select([
                    'lective_years.*',
                    'course_year_block.id as course_year_block',
                    // 'course_year_block.id_course as id_course',
                    'ct.display_name as course_name',
                    'course_year_block.id as course_year_block_id',
                    'course_year_block.curricular_year as curricular_year',
                    'course_year_block.state as state',
                    'course_year_block.created_at as created_at',
                    'course_year_block.updated_at as updated_at',
                    'course_year_block.deleted_at as deleted_at',                    
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'lyt.display_name',
                ])
                // ->get()
                ;


            return Datatables::eloquent($model)

                ->addColumn('actions', function ($item) {
                    return view('GA::setting-course-curricular.datatables.actions')->with('item', $item);
                })
                ->addColumn('state', function ($item) {
                    return view('GA::setting-course-curricular.datatables.curso_lectiveyear_state')->with('item', $item);
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
                ->rawColumns(['actions', 'state'])
                ->addIndexColumn()

            ->toJson();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }



    public function change_state($id) {        

        $change_state = DB::table('course_curricular_block')
        ->where('id', $id)
        ->get()
        ->first();
        
        if ($change_state->state == 0){
            //$change_state->state = 1;

            //$change_state->update(['state' => 1]);

            DB::table('course_curricular_block')
            ->where('id', $id)
            ->update([
                'state' => 1,                
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }
        else {
            //$change_state->state = 0;
            //$change_state->update(['state' => 0]);

            DB::table('course_curricular_block')
            ->where('id', $id)
            ->update([
                'state' => 0,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }

        // Success message
        Toastr::success(__('O estado foi actualizado com sucesso!'), __('toastr.success'));
        return redirect()->route('course-curricular-year-block.list');
        
        return $change_state->state;
        
        // try {

            // $courses=Course::with(['currentTranslation'])->get();
            // return view('GA::setting-course-curricular.list',compact('courses'));
            return view('GA::setting-course-curricular.list');

        // } catch (Exception | Throwable $e) {
        //     Log::error($e);
        //     return $e;
        //     return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        // }

    }
    
    
    public function index()
    {
        try {

            $courses=Course::with(['currentTranslation'])->get();
            return view('GA::setting-course-curricular.index',compact('courses'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


   

    public function ajax()
    {
        try {

            $model = LectiveYear::join('users as u1', 'u1.id', '=', 'lective_years.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'lective_years.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'lective_years.deleted_by')
                ->leftJoin('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })
                ->select([
                    'lective_years.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'lyt.display_name',
                ]);
            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('GA::lective-years.datatables.actions')->with('item', $item);
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

    public function create()
    {
        try {
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
            ];
            return view('GA::lective-years.lective-year')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function store(Request $request)
    {
        try {
            
            DB::beginTransaction();
                foreach($request->curricular_year as $item){
                    
                    if ($item == 1) {
                        DB::table('course_curricular_block')
                        ->updateOrInsert(
                            [
                                'id_lective_year' => $request->AnoLectivo, 
                                'id_course' => $request->course,
                                'curricular_year'=>$item],
                            [
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::now(),
                                'updated_by'=> Auth::user()->id,
                                'updated_at' => Carbon::now(),
                                'state'=>1
                            ]
                        );
                    }
                    else {
                        Toastr::Warning(__('A forLEARN detectou uma selecção de ano curricular inválido, tente novamente, no caso do erro persistir, contacte o apoio à forLEARN!'), __('toastr.warning'));
                        return redirect()->back();
                    }
                }
                            
            DB::commit();

            // Success message
            Toastr::success(__('Bloqueio foi realizado com sucesso!'), __('toastr.success'));
            return redirect()->route('course-curricular-year-block.list');
            //return redirect()->route('lective-years.index');

        } catch (Exception | Throwable $e) {
            return dd($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function show($id)
    {
        try {
          
            $LectiveYear=LectiveYear::with(['currentTranslation'])->find($id);
         
            $courses=Course::with(['currentTranslation'])->get();
            return view('GA::setting-course-curricular.index',compact('courses','LectiveYear'));
        } 
        catch (Exception | Throwable $e) {
           Log::error($e);
           return $e;
           return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
       }
    }

    private function fetch($id, $action)
    {
        try {
            // Find
            $lective_year = LectiveYear::whereId($id)->with([
                'translations' => function ($q) {
                    $q->whereActive(true);
                },
            ])->firstOrFail();

            $data = [
                'action' => $action,
                'lective_year' => $lective_year,
                'translations' => $lective_year->translations->keyBy('language_id')->toArray(),
                'languages' => Language::whereActive(true)->get()
            ];
            return view('GA::lective-years.lective-year')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return abort(500);
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


    
    public function update(LectiveYearRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the default language
            $default_language = Language::whereDefault(true)->firstOrFail();

            // Find and update
            $lective_year = LectiveYear::whereId($id)->firstOrFail();
            $lective_year->code = $request->get('code');
            $lective_year->start_date = $request->get('start_date');
            $lective_year->end_date = $request->get('end_date');
            $lective_year->save();

            // Disable previous translations
            LectiveYearTranslation::where('lective_years_id', $lective_year->id)->update(['active' => false]);

            $version = LectiveYearTranslation::where('lective_years_id', $lective_year->id)->whereLanguageId($default_language->id)->count() + 1;

            // Associated translations
            $languages = Language::whereActive(true)->get();
            foreach ($languages as $language) {
                $lective_year_translations[] = [
                    'lective_years_id' => $lective_year->id,
                    'language_id' => $language->id,
                    'display_name' => $request->get('display_name')[$language->id] ?? null,
                    'description' => $request->get('description')[$language->id] ?? null,
                    'abbreviation' => $request->get('abbreviation')[$language->id] ?? null,
                    'created_at' => Carbon::now(),
                    'version' => $version,
                    'active' => true,
                ];
            }

            if (!empty($lective_year_translations)) {
                LectiveYearTranslation::insert($lective_year_translations);
            }

            DB::commit();

            // Success message
            Toastr::success(__('GA::lective-years.update_success_message'), __('toastr.success'));
            return redirect()->route('lective-years.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
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
            $lective_year = LectiveYear::whereId($id)->firstOrFail();
            $lective_year->delete();

            DB::commit();

            // Success message
            Toastr::success(__('GA::lective-years.destroy_success_message'), __('toastr.success'));
            return redirect()->route('lective-years.index');

        } catch (ModelNotFoundException $e) {
            Toastr::error(__('GA::lective-years.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // Lista de estudantes que mudaram de curso por não abertura do curso    
    public function students_course_curricular_block_change(Request $request) {

        try {

            
            DB::beginTransaction();            

            //Ano lectivo 
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
            ->where('id',$request->id_lective_year)
            ->get();

            // Verifica se o curso encontra-se bloqueado neste ano lectivo
            if(MatriculationUtil::verificarCursoBloquedo($request->id_lective_year, $request->id_course_mudanca, $request->course_year)){
                Toastr::Warning(__('A forLEARN detectou que o curso e o ano curricular deste estudante encontra-se bloqueiado neste ano lectivo, contacte o apoio à forLEARN.'), __('toastr.warning'));
                return redirect()->route('matriculations.index');
            }

            // Verifica se o ano lectivo é válido
            if($lectiveYearSelected->isEmpty()){
                Toastr::Warning(__('A forLEARN detectou uma selecção de ano lectivo inválido, tente novamente, no caso do erro persistir, contacte o apoio à forLEARN!'), __('toastr.warning'));
                return redirect()->route('matriculations.index');
            }

            
            if(isset($request->id_course_mudanca) and ($request->id_course_mudanca != 0)) {     
                
                //return [$request->id_user, $request->id_course_mudanca];

                // Procura pelo último número de matrícula de acordo ao curso e ano lectivo
                $user_code_matricula = DB::select('CALL proc_last_code_matricula(?,?)',[$request->id_lective_year,$request->id_course_mudanca]);
                
                $user_next_code_matricula = $user_code_matricula[0]->next_code_matricula;

                //return $user_next_code_matricula;
                
                //Muda de curso                 
                DB::table('user_courses')
                ->where(
                    'users_id', '=', $request->id_user,                  
                )
                ->where(                 
                    'courses_id', '=', $request->id_course,
                )
                ->update([
                    'courses_id' => $request->id_course_mudanca,
                ]);     

                // Actualiza o novo número de matrícula 
                DB::table('users')
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
                })
                ->where(
                    'users.id', '=', $request->id_user,                  
                )
                ->update([
                    'up_meca.value' => $user_next_code_matricula,
                ]);
                                
                
                DB::table('students_course_curricular_block_change')->updateorInsert(
                    [
                        'id_user' => $request->id_user,
                        'id_matricula' => $request->id_matricula,                   
                        'id_lective_year' => $request->id_lective_year,
                        'id_course'=> $request->id_course,
                        'course_year' => $request->course_year,
                    ],
                    [                        
                        'num_matricula' => $request->num_matricula,
                        'id_course_new' => $request->id_course_mudanca,
                        'course_year_new' => $request->course_year,
                        'created_by' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                        'updated_by'=> Auth::user()->id,
                        'updated_at' => Carbon::now(),
                    ]
                ); 
                            
                DB::commit();
                
                Toastr::success(__('A mudança de curso foi realizado com sucesso! Por favor verifique se as disciplinas correspondem ao curso, caso não volte a carregar as disciplinas do estudante para atualizar as mesma.'), __('toastr.success'));
                return redirect()->route('matriculations.edit',$request->id_matricula);
            
            }
            else {
                Toastr::Warning(__('A forLEARN detectou que não foi selecionado um curso em na qual sera feita a mudança, por favor informe um curso!'), __('toastr.warning'));
                return redirect()->route('matriculations.index');
            }             

            // Success message
            //Toastr::success(__('A mudança de curso foi realizado com sucesso!'), __('toastr.success'));
            //return redirect()->route('matriculations.index');

        } catch (Exception | Throwable $e) {
            dd($e);
            return $e;
            Toastr::error($e->getMessage(), __('toastr.error'));
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function students_list_change(){
        //return 2023;

        try {

            // $courses=Course::with(['currentTranslation'])->get();
            // return view('GA::setting-course-curricular.list',compact('courses'));
            
            $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                ->first();

            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

            return view('GA::setting-course-curricular.matriculation_students_course_change', compact('lectiveYears', 'lectiveYearSelected'));


        } catch (Exception | Throwable $e) {
            Log::error($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function students_list_ajax($id) {

        try {

            //    return 
            
            //return $id;
            
            $model = LectiveYear::join('students_course_curricular_block_change as students_course_change', 'students_course_change.id_lective_year', '=', 'lective_years.id')
                ->join('users as u1', 'u1.id', '=', 'students_course_change.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'students_course_change.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'students_course_change.deleted_by')
                    ->leftJoin('users as student', 'student.id', '=', 'students_course_change.id_user')
                    ->join('user_parameters as full_name', function ($join) {
                        $join->on('students_course_change.id_user', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                    })

                    ->leftJoin('lective_year_translations as lyt', function ($join) {
                        $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                        $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('lyt.active', '=', DB::raw(true));
                    })
    
                    ->leftJoin('courses as crs', 'crs.id', '=', 'students_course_change.id_course')
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'crs.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })

                    ->leftJoin('courses as crs_new', 'crs_new.id', '=', 'students_course_change.id_course_new')
                    ->leftJoin('courses_translations as ct1', function ($join) {
                        $join->on('ct1.courses_id', '=', 'crs_new.id');
                        $join->on('ct1.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct1.active', '=', DB::raw(true));
                    })
                    ->where('students_course_change.id_lective_year', $id)
    
                    ->select([
                        'lective_years.*',
                        'students_course_change.id as students_course_change_id',
                        'students_course_change.id_user as id_user',
                        'students_course_change.id_matricula as id_matricula',
                        'students_course_change.id_course as id_course',
                        'students_course_change.course_year as course_year',
                        'students_course_change.id_lective_year as id_lective_year',
                        'students_course_change.num_matricula as num_matricula',

                        'students_course_change.created_at as created_at',
                        'students_course_change.updated_at as updated_at',
                        'students_course_change.deleted_at as deleted_at',                    
                        'u1.name as created_by',
                        'u2.name as updated_by',
                        'u3.name as deleted_by',
                        //'student.name as student',
                        'full_name.value as student',
                        'lyt.display_name as display_name',
                        'ct.display_name as course_name',
                        'ct1.display_name as coursename',
                        'students_course_change.course_year_new as courseyear',
                    ])
                    // ->get()
                    ;
    
    
                return Datatables::eloquent($model)
    
                    ->addColumn('actions', function ($item) {
                        return view('GA::setting-course-curricular.datatables.actions_matriculation')->with('item', $item);
                    })
                    
                    ->rawColumns(['actions'])
                    ->addIndexColumn()
    
                ->toJson();
    
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return response()->json($e->getMessage(), 500);
            }

    }
}
