<?php
namespace App\Modules\Grades\Controllers;
use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Grades\Models\Grade;
use App\Modules\Grades\Requests\GradeRequest;
use App\Modules\Users\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;
use Toastr;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use Auth;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\GA\Models\LectiveYear;

use App\Exports\CandidateExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Model\Institution;
use App\Modules\Grades\Util\GradesUtil;

class GradesController extends Controller

{
    private $gradeUtil;

    function __construct()
    {
        $this->gradeUtil = new GradesUtil();
    }
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {   
        if (auth()->user()->hasRole('teacher')) {
            $teacherCourses = auth()->user()->courses()->pluck('courses.id')->all();
            $courses = Course::with(['currentTranslation'])->select(['courses.*'])->whereIn('courses.id', $teacherCourses);
        }else{
            $courses = Course::with(['currentTranslation'])
                ->select(['courses.*'])
                ->join('coordinator_course as co','co.courses_id','courses.id')
                ->where('co.user_id',Auth::user()->id) ;            
        }

        $currentData = Carbon::now();
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

       $data = [
            'courses' => $courses->get(),
            'lectiveYearSelected'=>$lectiveYearSelected,
            'lectiveYears'=>$lectiveYears
        ];

        return view("Grades::index")->with($data);
    }

    public function ajaxDisciplines($id,$anoLectivo)
    {
        try {
                // return response()->json($anoLectivo);
                $disciplines = Discipline::leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
                    ->select('disciplines.id as id', 'disciplines.code as code', 'dt.display_name as display_name', 'disciplines.percentage')
                    ->leftJoin('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
                    ->leftJoin('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
                    ->where('disciplines.courses_id', $id)
                    ->where('discipline_area_id', 18);
                
           $turma = DB::table('classes as turma')
           ->where( [
 
            ['turma.courses_id', '=', $id],
            ['turma.lective_year_id', '=', $anoLectivo],
            ['turma.year', '=', 1],
            ['turma.deleted_at', '=', null]
           
            ])
            ->get();

            if (auth()->user()->hasRole('teacher')) {
                $teacherDisciplines = auth()->user()->disciplines()->pluck('disciplines.id')->all();
                $disciplines = $disciplines->whereIn('disciplines.id', $teacherDisciplines);
            }
            
            $disciplines = $disciplines->get();

            return response()->json(['disciplines'=>$disciplines,'turma'=>$turma]);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxStudents($id)
    {
        try {
            $students = studentsSelectList([6, 15], $id);

            return response()->json($students->values()->all());
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxStudentGrade(Request $request)
    {
        try {
            $grade = Grade::where('student_id', $request->get('student_id'))
                ->where('discipline_id', $request->get('discipline_id'))
                ->latest()
                ->first();

                
                
            return response()->json([
                'id' => $grade ? $grade->id : null,
                'value' => $grade ? $grade->value : null
            ], 200);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }



    public function ajaxDisciplineGrades($id)
    {
        try {
            $students = User::leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                     ->where('u_p.parameters_id', 1);
            })
            ->join('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->join('user_disciplines as ud', 'ud.users_id', '=', 'users.id')
            ->join('disciplines as dp', 'dp.id', '=', 'ud.disciplines_id')

            ->leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'dp.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
            ->leftJoin('user_parameters as u_n', function ($join) {
                $join->on('users.id', '=', 'u_n.users_id')
                        ->where('u_n.parameters_id', 19);
            })
            ->leftJoin('grades', 'grades.student_id', '=', 'users.id')
            ->select([
                'users.id as user_id',
                'users.email as email',
                'dp.id as dc_id',
                'u_n.value as number',
                'u_p.value as name',
                'grades.value as value'
            ])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'candidado-a-estudante');
            })
            ->whereYear('users.created_at', date('Y'))
            ->where('ud.disciplines_id', $id)
            ->distinct()
            ->get(); 

            return json_encode(array('student' => $students));
                
            
         
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function exameCandidatesStatus($course, $id_turma, $id_fase, $user_id, $lective_year){
        
        $ajaxDisciplines = $this->ajaxDisciplines($course, $lective_year)->getData();
        $disciplines = $ajaxDisciplines->disciplines;
        
        $data = [ 'curso_id' => $course, 'turma_id' => $id_turma, 'user_id' => $user_id,'fase_id' => $id_fase,];
        $obj = DB::table("exame_candidates_status")->where($data)->first();
        
        
        //$tam = sizeof($disciplines);
        
        $join = "";
        $notas = 0;
        $media = 0;
        $tam = 0;
        $peloMenosUmENegativo = false;
        $description = "Não admitido(a)";
        $status = 0;
        
        foreach($disciplines as $discipline){
            
            $grade = Grade::where([ 
                'course_id' => $course, 
                'discipline_id' => $discipline->id,
                'student_id' => $user_id,
                'id_fase' => $id_fase                
            ])->first();
                
            if(isset($grade->id)){
                $tam++;
                $notas += $grade->value;
                if($join == ""){
                    $join = $join . "[d={$discipline->id},n={$grade->value}]";   
                }else{
                    $join = $join . ";[d={$discipline->id},n={$grade->value}]";
                }  
            }
            
        }
        
        if($tam > 0)  $media = round( $notas / $tam);
        
        if($media > 9){
            $description = "Admitido(a)";
            $status = 1;
        }
        
        if(!isset($obj->id)){
            $data["media"] = $media;
            $data["status"] = $status;
            $data["disciplina_nota"] = $join;
            $data["description"] = $description;
            $data['created_by'] = auth()->user()->id;
            $data['created_at'] = Carbon::now();
            DB::table("exame_candidates_status")->insert($data);            
        }else{
     
            DB::table("exame_candidates_status")
                ->where('id',$obj->id)
                ->update([
                    "media" => $media,
                    "status" => $status,
                    "description" => $description,
                    "updated_by" => auth()->user()->id,
                    "updated_at" => Carbon::now(),
                    "disciplina_nota" => $join
                ]);
                
        }
        
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    { 
        
        try {
            
            $course = $request->get('course');
            $discipline = $request->get('discipline');
            $id_fase=$request->get('fase');
            $classe= $request->get('classe');
            $lective_year = $request->get('lective_year');
           
            $data = [
                'grades' => $request->get('grades'),
                'students' => $request->get('students'),
                'users' => $request->get('users')
            ];
            
            DB::transaction(function () use ($data, $course, $discipline, $id_fase, $classe, $lective_year) {
                for ($i=0; $i < count($data['grades']); $i++) {
                    Grade::updateOrInsert([
                        'course_id' => $course,
                        'discipline_id' => $discipline,
                        'student_id' => $data['users'][$i],
                        'id_fase' => $id_fase
                    ],[
                        'value' => $data['grades'][$i],
                        'updated_at'=>Carbon::now(),
                        'updated_by'=> Auth::user()->id,
                        'id_fase' => $id_fase
                    ]);
                    
                    $this->exameCandidatesStatus(
                        $course, 
                        $classe, 
                        $id_fase, 
                        $data['users'][$i],
                        $lective_year
                    );
                }
            });
            
            // Success message
            Toastr::success(__('Grades::grades.store_success_message'), __('toastr.success'));

            return redirect()->back();
            //return response()->json(['grade' => $grade], 201);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GradeRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(GradeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $grade = Grade::findOrFail($id);

            $grade->value = $request->get('value');

            $grade->save();

            DB::commit();

            // Success message

            Toastr::success(__('Grades::grades.store_success_message'), __('toastr.success'));

            return response()->json(['grade' => $grade], 201);

        } catch (Exception | Throwable $e) {

            Log::error($e);

            Toastr::error(__('Grades::grades.store_error_message'), __('toastr.error'));

            return response()->json(['error' => $e], 500);
        }
    } 


    /*Methodo de apagar um registro */
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */







    public function exportListaExcel(Request $request){
    
        return Excel::download(new CandidateExport, 'Lista_de_candidatos.xlsx');
        try{
        }
        catch (Exception | Throwable $e) {
          return redirect()->back();
        }

    }






    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $grade = Grade::whereId($id)->firstOrFail();

            $grade->delete();

            $grade->save();

            DB::commit();

            // Success message
            Toastr::success(__('Grades::grades.destroy_success_message'), __('toastr.success'));
            return response()->json(['grade' => $grade], 201);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Grades::grades.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
























    public function getStudentsBy(Request $request, $lectiveYears,$courseId,$id_disciplina,$turma){
        Log::info("Entrou no getStudentsBy:".$lectiveYears."-".$courseId."-".$id_disciplina."-".$turma);
         //return response()->json($request->all());

        try {
          
            if(!isset($request->fase)){
                Toastr::warning(__('Seleciona uma fase'), __('toastr.warning'));
                return redirect()->back();
              }
        
            $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();
            $lectiveCandidate = DB::table('lective_candidate')->find($request->fase);


        //     $query = $this->gradeUtil->getUserInFaseStepOne($lectiveCandidate,$lectiveYear,$courseId,$id_disciplina,$turma)
        //                             ->join('user_candidate as uca','uca.user_id','=','users.id')
        //                             ->where('uca.year_fase_id',$lectiveCandidate->id);
                                    
        // if(isset($lectiveCandidate->id)){
        //     $model = $query->where('notas.id_fase',$lectiveCandidate->id)->get();
        // }
        
         //if(count($model)==0){
            $query = $this->gradeUtil->getUserInFaseStepTwo($lectiveCandidate,$lectiveYear,$courseId,$id_disciplina,$turma)
                ->join('user_candidate as uca','uca.user_id','=','users.id')
                ->where('uca.year_fase_id',$lectiveCandidate->id);
        
                $model = $query->get();                                     
                               
                $notas=[];           
                for($i=0;$i< count($model);$i++){       
                    $model[$i]["value"]= $this->gradeUtil->getGrades(
                        $model[$i]["user_id"],
                        $id_disciplina,
                        $courseId,
                        $lectiveCandidate->id
                    );         
                }
         //}   
         Log::info($model);
       return response()->json(['studant'=>$model]);
            // return response()->json($model);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }










    public function indexStudent()
    {
        try {
            $users = auth()->user()->can('manage-payments-others') ? studentsSelectList() : null;

            $data = compact('users');

            return view("Grades::student-grades")->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }














































    public function ajaxStudent($id)
    {
        $id = auth()->user()->can('view-grades-others') ? $id : auth()->user()->id;

        try {
            $model = Grade::where('grades.student_id', $id)
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'grades.course_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'grades.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->join('users as u0', 'u0.id', '=', 'grades.student_id')
                ->join('users as u1', 'u1.id', '=', 'grades.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'grades.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'grades.deleted_by')
                ->select([
                    'grades.*',
                    'ct.display_name as course',
                    'dt.display_name as discipline',
                    'u0.name as student',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by'
                ]);

            return Datatables::eloquent($model) 
               // Juadilson Perdão!
              /*  ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at ? TimeHelper::time_elapsed_string($item->updated_at) : null;
                })
                ->editColumn('deleted_at', function ($item) {
                    return $item->deleted_at ? TimeHelper::time_elapsed_string($item->deleted_at) : null;
                })*/
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }













































    public function curricularPlan()
    {
        //for example 2392
        $user = User::whereId(2392)->with([
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'groups',
                ]);
            },
            'matriculation' => function ($q) {
                $q->with([
                    'disciplines' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'grades'
                        ]);
                    }
                ]);
            },
            'courses' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'disciplines' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'study_plans_has_disciplines' => function ($q) {
                                $q->with([
                                    'discipline_period' => function ($q) {
                                        $q->with([
                                            'currentTranslation'
                                        ]);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            }
        ])->firstOrFail();

        /*$data = [
            'user' => $user
        ];*/

        /*$footer_html = view()->make('Users::matriculations.partials.pdf_footer')->render();
        $pdf = PDF::loadView('Grades::curricular_plan_pdf', compact('user'))
            ->setOption('margin-top', '10')
            ->setOption('header-html', '<header></header>')
            ->setOption('footer-html', $footer_html)
            ->setPaper('a4');
        return $pdf->stream('ficha_curricular.pdf');*/
        return view('Grades::curricular_plan_pdf', compact('user'));
    }



















































    public function staffCurricularPlan()
    {
        try {
            $users = auth()->user()->can('manage-payments-others') ? studentsSelectList() : null;

            $data = compact('users');

            return view('Grades::staff_curricular_plan')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





















































    public function ajaxStudentForStaff($id)
    {
        $id = auth()->user()->can('view-grades-others') ? $id : auth()->user()->id;

        try {
            $model = Grade::where('grades.student_id', $id)
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'grades.course_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->join('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'grades.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })
                ->join('users as u0', 'u0.id', '=', 'grades.student_id')
                ->join('users as u1', 'u1.id', '=', 'grades.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'grades.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'grades.deleted_by')
                ->select([
                    'grades.*',
                    'ct.display_name as course',
                    'dt.display_name as discipline',
                    'u0.name as student',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by'
                ]);
            return Datatables::eloquent($model)
              /*  ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at ? TimeHelper::time_elapsed_string($item->updated_at) : null;
                })
                ->editColumn('deleted_at', function ($item) {
                    return $item->deleted_at ? TimeHelper::time_elapsed_string($item->deleted_at) : null;
                })*/
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }













































    public function staffCurricularPlanPDF($id)
    {

        //for example 2392
        $user = User::whereId($id)->with([
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'groups',
                ]);
            },
            'matriculation' => function ($q) {
                $q->with([
                    'disciplines' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'grades'
                        ]);
                    }
                ]);
            },
            'courses' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'disciplines' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'study_plans_has_disciplines' => function ($q) {
                                $q->with([
                                    'discipline_period' => function ($q) {
                                        $q->with([
                                            'currentTranslation'
                                        ]);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            }
        ])->firstOrFail();

        /*$data = [
            'user' => $user
        ];*/

        $footer_html = view()->make('Users::matriculations.partials.pdf_footer')->render();
        $pdf = PDF::loadView('Grades::curricular_plan_pdf', compact('user'))
            ->setOption('margin-top', '10')
            ->setOption('header-html', '<header></header>')
            ->setOption('footer-html', $footer_html)
            ->setPaper('a4');
        return $pdf->stream('ficha_curricular.pdf');
        //return view('Grades::curricular_plan_pdf', compact('user'));
    }



























public function usuario(){
    

//  DB::transaction(function () {
     
//      User::chunk(1000, function ($users){ 
//     
//      foreach ($users as $user) {
    
//             $a = explode('@',$user->email); 
//             $b = $a[0]."@forlearn.ao";
            
//             if(!str_contains($b, "@forlearn.ao"))
//             {
//                 $newUser = User::find($user->id); 
//                 $nome_email = explode('@',$user->email); 
//                 $newUser->email = $nome_email[0]."@forlearn.ao";
//                 $newUser->save(); 
//              
//             }
//           }
//   });


// });


// return "ola ";

// DB::transaction(function () {
    
    $count=0;
    $users = User::select('id', 'email')->get();
        foreach ($users as $user) 
        { 
            
            $a = explode('@',$user->email); 
            $b = $a[0]."@forlearn.ao";
            if($user->email!=$b)
            {
                $newUser = User::whereId($user->id)->firstOrFail(); 
                $nome_email = explode('@',$user->email); 
                $newUser->email = $nome_email[0]."@forlearn.ao";
                $newUser->save(); 
              echo "Dentro do if". $count++."</br>";
            }
            else{
              echo "-Else: ". $count++."</br>";  
              
                 $newUser = User::whereId($user->id)->firstOrFail(); 
                $nome_email = explode('@',$user->email); 
                $newUser->email = $nome_email[0]."@forlearn.ao";
                $newUser->save(); 
              
            }
        
        } 
//  });


    
    
    }
























    public function showStudentGrades_EXEMplo($id,$anoLectivoId)
    {
    return   $discipline_grades = Discipline::whereId($id)->with([
            'grades' => function ($q) {
                $q->with([
                    'student' => function ($q) {
                        $q->orderBy('name', 'ASC');
                        $q->with([
                            'classes',
                            'candidate',
                            'parameters' => function ($q) {
                                $q->with([
                                    'currentTranslation',
                                    'groups',
                                ]);
                                $q->orderBy('pivot_value', 'DESC');
                            },
                        ]);
                        //$q->orderBy('id', 'ASC');
                    }
                ]);//->orderBy('student_id', 'ASC');
            },
            'course' => function ($q) {
                $q->with([
                    'classes',
                    'currentTranslation'
                ]);
            }
        ])->firstOrFail();


       $estado = Discipline::whereId($id)->with([
            'grades' => function ($q) {
                $q->with([
                    'student' => function ($q) {
                        $q->with([
                            'candidate',
                            
                        ]);
                        
                    }
                ])->orderBy('value', 'DESC');
            },
          
        ])->firstOrFail();

        return view('Grades::show-students-grades', compact('discipline_grades', 'estado'));
    }






























    
// Lista dos candidatos a estudante
    function showStudentList(Request $request, $courseId,$lectiveYears,$turma)
    {

        
        try {
            
                
             $disciplines = Discipline::leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
             })
            ->leftJoin('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
            ->leftJoin('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
            ->where('disciplines.courses_id', $courseId)
            ->where('discipline_area_id', 18)
            ->select('disciplines.id as id_disciplina', 'dt.abbreviation as abb', 'dt.display_name as nome_disciplina')
            ->orderBy("disciplines.id")
            ->get();
            
            
            if(!isset($request->fase)){
                Toastr::warning(__('Seleciona uma fase'), __('toastr.warning'));
                return redirect()->back();
             }
            
          $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

            $curso="";

            $lectiveCandidate = DB::table('lective_candidate')->find($request->fase);

            $model = $this->gradeUtil->getUserInFaseStepTwo($lectiveCandidate,$lectiveYear,$courseId,true,$turma)
                                     ->join('user_candidate as uca','uca.user_id','=','users.id')
                                     ->where('uca.year_fase_id',$lectiveCandidate->id)
                                     ->get();
              
                                 
               
            
             $notas = collect($model)->groupBy('id')->map(function ($item, $key) use($request){
                     
                    $array = [];
                    foreach($item as $value){
                    $array[] = $turma = DB::table('user_classes as turma')
                    ->leftJoin('grades as notas', 'notas.student_id', '=', 'turma.user_id')
                    ->where('turma.user_id', $item[0]->id)
                    ->where('notas.id_fase',$request->fase)
                    ->select([
                        'turma.class_id as id_turma', 
                        'turma.user_id as usuario_id',
                        'notas.course_id as curso_id',
                        'notas.discipline_id as disciplina_id',
                        'notas.value as nota',
                        'notas.id_fase as fase',
                    ])
                    ->orderBy("notas.discipline_id")
                    ->get(); 
                    }
                    
                    return $array;
                    
             });
             
             
            
            $tam = sizeof($model);
            if($tam > 0){
            foreach($model[0]->courses as $cursoD){  
                $curso = $cursoD->currentTranslation->display_name;
                
              }
              $turmaC = $model[0]->turma;
            }
            else{
                $turmaC = $turma;
            }


           # Listagem das turmas onde os estudantes pertencem em tempo real

            if(isset($turma)){
                $nome_turma= DB::table('classes')
                ->where('classes.id',$turma)
                ->select(["display_name"])
                ->first();
            }
          
            
          $turmaC = $nome_turma->display_name;
          //dados da instituição
          $institution = Institution::latest()->first();  
         

         
     

    $pdf = PDF::loadView("Grades::exame.list_candidate", compact(
        'model',
        'curso',
        'turmaC',
        'institution',
        'lectiveCandidate',
        'disciplines',
        'notas'
    ));


    
    $pdf->setOption('margin-top', '1mm');
    $pdf->setOption('margin-left', '1mm');
    $pdf->setOption('margin-bottom', '12mm');
    $pdf->setOption('margin-right', '1mm');
    $pdf->setOption('enable-javascript', true);
    $pdf->setOption('debug-javascript', true);
    $pdf->setOption('javascript-delay', 1000);
    $pdf->setOption('enable-smart-shrinking', true);
    $pdf->setOption('no-stop-slow-scripts', true);
    $pdf->setPaper('a4', 'landscape');
    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
    
    $pdf_name="Lista_candidatos";
    //$footer_html = view()->make('Users::users.partials.pdf_footer')->render();
    //$pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');
   
   
          

      } catch (Exception | Throwable $e) {
          Log::error($e);
          return response()->json($e->getMessage(), 500);
      }


    }



























    



    
// Lista dos candidatos a estudante
    function showStudentGrades(Request $request,$id,$lectiveYears,$courseId,$turma)
    {

       
        try {

            if(!isset($request->fase)){
                Toastr::warning(__('Seleciona uma fase'), __('toastr.warning'));
                return redirect()->back();
              }
           
            $lectiveCandidate = DB::table('lective_candidate')->find($request->fase);
            $disciplines = Discipline::leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
                    ->select('disciplines.id as id', 'disciplines.code as code', 'dt.display_name as disciplina','dt.abbreviation as abb')
                    ->leftJoin('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
                    ->leftJoin('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
                    ->where('disciplines.courses_id', $courseId )
                    ->where('discipline_area_id', 18);

            // if (auth()->user()->hasRole('teacher')) {
            //     $teacherDisciplines = auth()->user()->disciplines()->pluck('id')->all();
            //     $disciplines = $disciplines->whereIn('id', $teacherDisciplines);
            // }
             $disciplines = $disciplines->get();
            
            if(count($disciplines)>1){
               //ShowTwoNotaPDF($disciplines,$lectiveYears,$courseId); 
               $grade = Grade::whereIn('discipline_id',array($disciplines[0]->id,$disciplines[1]->id))
                 ->where('course_id', $courseId)
                 ->orderBy('value','DESC')
                 ->get();
                 $folha=2;

                }

            else{      
                $grade = Grade::where('discipline_id', $id)
                   ->where('course_id', $courseId)
                   ->orderBy('value','DESC')
                   ->get();
                    $folha=1; 
               }
               
               
               
           if($disciplines->isEmpty()){
                
                  Toastr::warning(__('Houve algum erro ao localizar a(s) disciplina(s) de exame de acesso do curso selecionado, por favor tente novamente.'), __('toastr.warning'));

                 return redirect()->back();
           }
            
            //Acina tem o código de pegar as disciplinas 
             foreach($disciplines as $dis){
                $discip[]=$dis->id;
             }
             
           
           $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

       

             $query = $this->gradeUtil->getUserInFaseStudentGrades($lectiveCandidate,$lectiveYears,$courseId,$turma,$discip);
             if($lectiveCandidate->fase > 1){
                $model =  $query->where('notas.id_fase',$lectiveCandidate->id)->get();
            }else {
               $model =  $query->orderBy("name_completo","desc")->get(); 
            }
            

            if($model->isEmpty()){
                
                  Toastr::warning(__('Não foi possivel gerar a pauta de exame de acesso desta turma, verifique se as notas estão devidamente lançadas.'), __('toastr.warning'));

                 return redirect()->back();
            }


            $curso="";
            $cursoID="";
            //$Estatistica=0;
            $notas=$grade;
            $negativas=[];
        
            $contadorNotas=0;
            $geral=[];

    
           

   
                $cursoGet =DB::table('courses as c')
                ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'c.id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                })
                ->select(['c.id as id_curso','ct.display_name as nome_curso'])
                 ->where('c.id','=',$courseId)->first();
       
                $curso = $cursoGet->nome_curso; 
                $cursoID = $cursoGet->id_curso; 
           
      

            $TurmaL=DB::table('classes')
            ->join('schedule_types as periodo','periodo.id','=', 'classes.schedule_type_id')
            ->join('schedule_type_translations as lyt', function ($join) {
                    $join->on('lyt.schedule_type_id', '=', 'periodo.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
            })
            ->select(['classes.id as id_turma','classes.display_name as turma','lyt.description as periodo'])
            ->where('classes.id',$model[0]->id_turma)
            ->first();
            // dd($TurmaL);

           $turmaC=$TurmaL->turma;

             $data = [
                     'model' => $model,
                     'curso'=>$curso,
                     'notas'=>$grade,
                     'disciplines'=>$disciplines,
                     'turmaC'=>$turmaC,
                    ]; 

              $notas=$grade;
        


             $qtdVagas= DB::table('anuncio_vagas')
            ->where('lective_year',$lectiveYears)
            ->where('course_id',$courseId)
            ->whereNull('deleted_at')
            ->get(); 
            
            $vagas_number=0;
            if($TurmaL->periodo=="Manhã"){  $vagas_number=$qtdVagas[0]->manha; }
            if($TurmaL->periodo=="Tarde"){  $vagas_number=$qtdVagas[0]->tarde; }
            if($TurmaL->periodo=="Noite"){  $vagas_number=$qtdVagas[0]->noite; }

     if($folha>1){

        $estudantes=collect($model)->sortBy('name_completo')->sortBy('discipline_id');

    // return $sorted->values()->all();
       $estudantes=collect($estudantes)->sortBy('name_completo')->groupBy('name')
       
        ->map(function ($item, $key) {
                $id_discip1= $item[0]->discipline_id;
                $id_discip2= $item[1]->discipline_id ?? 0;
                
                $email= $item[0]->email;
                $cand= $item[0]->cand_number;
                $Nome= $item[0]->name_completo;
                $nota= $item[0]->nota."-".($item[1]->nota ?? 0);
                if(isset($item[0]->nota, $item[1]->nota)){
                    $resultado = round( ( $item[0]->nota * ($item[0]->percentage / 100) ) + ( $item[1]->nota * ($item[1]->percentage / 100) )  );
                }else if(isset($item[0]->nota) && !isset($item[1]->nota)){
                    $resultado = $item[0]->nota;
                }else{
                    $resultado = round ( ( ($item[0]->nota ?? 0) + ($item[1]->nota ?? 0) ) / 2);
                }
                $estado = $resultado < 10 ? "Não admitido(a)": "Admitido(a)";
            
            return   $data = [
                'nome'=>$Nome,
                'email'=>$email,
                'cand'=>$cand,
                'id_disciplina_a'=>$item[0]->discipline_id,
                'id_disciplina_b'=> ($item[1]->discipline_id ?? 0),
                'nota_a'=>$item[0]->nota,
                'nota_b'=>$item[1]->nota ?? null,
                'resultado'=>$resultado,
                'estado'=>$estado
                ];
        })->sortBy('nome');
        $posit=[];
        $negat=[];
       
        foreach($estudantes as $dados){

            if($dados['resultado']>0){
                $posit[]=$dados['resultado'];
            }elseif($dados['resultado']==0){
                $negat[]=$dados['resultado'];      
            }
        }
        // return count($posit);
        // return count($estudantes);
        // return count($negat);

        if($TurmaL->periodo=="Manhã"){
            $esta= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha' =>count($estudantes),
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

             $estaADP= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha'=>count($posit),
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

              $estaADPS= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha' =>count($negat),
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  
                    
             }
           if($TurmaL->periodo=="Tarde"){
                    // return $courseId;
               $esta=DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId ,'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'tarde' =>count($estudantes),
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

                 $estaADPsR= DB::table('estatistica')->updateOrInsert(
                        [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                        [ 
                        'tarde' =>count($posit),
                        'id_anoLectivo' =>$lectiveYears
                        ]
                        );  

                  $estaADPP = DB::table('estatistica')->updateOrInsert(
                            [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                            [ 
                            'tarde' =>count($negat),
                            'id_anoLectivo' =>$lectiveYears
                            ]
                    );  
            }
           if($TurmaL->periodo=="Noite"){
             $esta=  DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'noite' =>count($estudantes),
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

                    $estaADP= DB::table('estatistica')->updateOrInsert(
                        [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                        [ 
                        'noite' =>count($posit),
                        'id_anoLectivo' =>$lectiveYears
                        ]
                        );  
                        $estaADPD= DB::table('estatistica')->updateOrInsert(
                            [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                            [ 
                            'noite' =>count($negat),
                            'id_anoLectivo' =>$lectiveYears
                            ]
                            );  
            }    
        

       //return view("Grades::exame.list_note_two")->with($data);
       $institution = Institution::latest()->first();
        $pdf = PDF::loadView("Grades::exame.list_note_alfa", compact(
            'estudantes',
            'model',
            'vagas_number',
            'curso',
            'disciplines',
            'turmaC',
            'lectiveCandidate',
            'institution'
        ));

       }
       else{
        // return view("Grades::exame.list_note")->with($data);
           $countP=0;
           $countN=0;
           $qtdPagos=0;
           $geralNota=[];
           $geralNota1=[];
           $geralNotaNegativa=[];
           foreach($model as $studant){
               $geralNota1[]=$studant->nota;
               $qtdPagos++;
               if($studant->nota>9){
                  // $countP++;
                  //    $geralNota=$studant->nota;
                    if($model[0]->periodo=="Manhã"){ $geralNota[]=$studant->nota;}
                    if($model[0]->periodo=="Tarde"){ $geralNota[]=$studant->nota;}
                    if($model[0]->periodo=="Noite"){ $geralNota[]=$studant->nota;}    
                }
                if($studant->nota<=9){
                     $negativas[]=$studant->nota;
                     if($model[0]->periodo=="Manhã"){ $geralNotaNegativa[]=$studant->nota;}
                     if($model[0]->periodo=="Tarde"){ $geralNotaNegativa[]=$studant->nota;}
                     if($model[0]->periodo=="Noite"){ $geralNotaNegativa[]=$studant->nota;}  
                                
                 }     
                }
             
             
         
        //    return $turmaL;
            $geralNotaNegativa =count($geralNotaNegativa);
            $geralNota =count($geralNota);
           
            //  return  $qtdPagos;
            //Pega e nao pagos admitidos          
           if($TurmaL->periodo=="Manhã"){
            $esta= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha' =>$qtdPagos,
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

             $estaADP= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha' =>$geralNota,
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

              $estaADPS= DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'manha' =>$geralNotaNegativa,
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  
                    
             }
           if($TurmaL->periodo=="Tarde"){
                    // return $courseId;
               $esta=DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId ,'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'tarde' =>$qtdPagos,
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

                 $estaADPsR= DB::table('estatistica')->updateOrInsert(
                        [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                        [ 
                        'tarde' =>$geralNota,
                        'id_anoLectivo' =>$lectiveYears
                        ]
                        );  

                  $estaADPP = DB::table('estatistica')->updateOrInsert(
                            [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                            [ 
                            'tarde' =>$geralNotaNegativa,
                            'id_anoLectivo' =>$lectiveYears
                            ]
                    );  
            }
           if($TurmaL->periodo=="Noite"){
             $esta=  DB::table('estatistica')->updateOrInsert(
                    [ 'id_curso' => $courseId, 'categoria'=>'NP','id_anoLectivo'=>$lectiveYears],                      
                    [ 
                    'noite' =>$qtdPagos,
                    'id_anoLectivo' =>$lectiveYears
                    ]
                    );  

                    $estaADP= DB::table('estatistica')->updateOrInsert(
                        [ 'id_curso' => $courseId, 'categoria'=>'ADP','id_anoLectivo'=>$lectiveYears],                      
                        [ 
                        'noite' =>$geralNota,
                        'id_anoLectivo' =>$lectiveYears
                        ]
                        );  
                        $estaADPD= DB::table('estatistica')->updateOrInsert(
                            [ 'id_curso' => $courseId, 'categoria'=>'ND','id_anoLectivo'=>$lectiveYears],                      
                            [ 
                            'noite' =>$geralNotaNegativa,
                            'id_anoLectivo' =>$lectiveYears
                            ]
                            );  
            }    
        
           
        $pdf = PDF::loadView("Grades::exame.list_note", compact(
            'model',
            'curso',
            'notas',
            'vagas_number',
            'disciplines',
            'turmaC'
        ));
       } 



    
    $pdf->setOption('margin-top', '1mm');
    $pdf->setOption('margin-left', '1mm');
    $pdf->setOption('margin-bottom', '12mm');
    $pdf->setOption('margin-right', '1mm');
    $pdf->setOption('enable-javascript', true);
    $pdf->setOption('debug-javascript', true);
    $pdf->setOption('javascript-delay', 1000);
    $pdf->setOption('enable-smart-shrinking', true);
    $pdf->setOption('no-stop-slow-scripts', true);
    $pdf->setPaper('a4', 'landscape');
    
    
        $pdf_name="PdEA_" . $turmaC;
       // $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
        //$pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');
   
   
          

      } catch (Exception | Throwable $e) {
          Log::error($e);
          return response()->json($e->getMessage(), 500);
      }


    }



















































// estatística dos candidatos a estudante
    function showStudentEstatistic($courseId,$lectiveYears)
    {
 
  
        try {

            //   $grade = Grade::where('discipline_id', $id_disciplina)
            // //   ->where('discipline_id', $request->get('discipline_id'))
            //->get();
            // Realidade Veritual
            // 
            // return response()->json($grade);
            $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

          $model = User::query()
            ->whereHas('roles', function ($q)  {0- 
                  $q->whereIn('id', [15]);
              })->with(['courses' => function ($q) use ($courseId) {
                  $q->with([
                      'currentTranslation'
                      
                  ]);

              }])
              ->join('users as u1', 'u1.id', '=', 'users.created_by')
              ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
              ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
              ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
              ->leftJoin('article_requests', 'article_requests.user_id', '=', 'users.id')
              
             
              ->leftJoin('user_parameters as candidate', function ($join) {
                  $join->on('users.id', '=', 'candidate.users_id')
                  ->where('candidate.parameters_id', 311);
              })

              ->join('user_courses as uc', function ($join) use($courseId)  {
                  $join->on('users.id', '=', 'uc.users_id')
                  ->where('uc.courses_id', $courseId);
              })

              ->leftJoin('lective_years', function ($join) {
                  $join->whereRaw('users.created_at between `start_date` and `end_date`');
              })
              ->join('lective_year_translations as lyt', function ($join) {
                  $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                  $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('lyt.active', '=', DB::raw(true));
              })->where('lective_years.id',$lectiveYears)
              ->select([
                  'users.*',
                  'users.id',
                  'u1.name as us_created_by',
                  'u2.name as us_updated_by',
                  'full_name.value as name_completo',
                  // 'u3.name as deleted_by',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code',
                  
              ])

              

              ->distinct('article_requests.status')
              ->get();

            //   $model->courses->map(function ($course) {
            // })->implode(", ");
            $curso="";
            foreach($model[0]->courses as $cursoD){  
                $curso = $cursoD->currentTranslation->display_name;
            }

          $data = [
                'model' => $model,
                'curso'=>$curso
            ];           
           
     view("Grades::exame.estatistica")->with($data);

    $pdf = PDF::loadView("Grades::exame.estatistica", compact(
        'model',
        'curso'
    ));     


    
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

    $pdf_name="Estatística_candidatos";
    $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
    $pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');
   
   
          

      } catch (Exception | Throwable $e) {
        Log::error($e);
        return response()->json($e->getMessage(), 500);
      }


    }
    





}


