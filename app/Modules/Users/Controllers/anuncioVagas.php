<?php
namespace App\Modules\Users\Controllers;
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
use App\Modules\Users\Enum\ParameterEnum;


class anuncioVagas extends Controller

{

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        
        $courses = Course::with([
            'currentTranslation'
        ])
         ->where('id','!=',22)
         ->where('id','!=',18)
        ;

        // if (auth()->user()->hasRole('teacher')) {
        //     $teacherCourses = auth()->user()->courses()->pluck('id')->all();
        //     $courses = $courses->whereIn('id', $teacherCourses);
        // }

        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;

       $data = [
            'courses' => $courses->get(),
            'lectiveYearSelected'=>$lectiveYearSelected,
            'lectiveYears'=>$lectiveYears
        ];

        return view("Users::candidate.anuncio_vaga")->with($data);
    }








private function dados ($id_curso,$categoria){
  
   $courses = Course::with([
        'currentTranslation'
    ])
     ->where('id','!=',22)
     ->where('id','!=',18) ;
    $courses= $courses->get();
    $data = [
        'manha' => 1,
        'tarde' => 1,
        'noite' => 1,
        'total' => 1+1+1
    ];
    
    DB::transaction(function () use ($data, $courses,$categoria) {
          $i=0;
          foreach ( $courses as $course ) {   
            $i++;
            DB::table('estatistica')->updateOrInsert(

                [ 'id_curso' => $course->id, 'categoria'=>$categoria]
                , 
                [
                // 'id_curso' => $course->id,
                'manha' => (double)$data['manha'][$i],
                'tarde' => (double)$data['tarde'][$i],
                'noite' => (double)$data['noite'][$i],
                'total' => (double)$data['total'][$i],
                'categoria' => $categoria
                
                ]
            );
        }
    });


     
}


















































public function estatisticaPDF(Request $request,$anoletivo){
    $lectiveYears = LectiveYear::with(['currentTranslation'])
    ->where('id',$anoletivo)
    ->select('*')
    ->get();

    if(!isset($request->fase)){
        Toastr::warning(__('Seleciona uma fase'), __('toastr.warning'));
        return redirect()->back();
      }
    
    //  $this->dados(1,"ND");
     
    //Pega o Direitor da escola
     $direitor = User::whereHas('roles', function ($q) {
        $q->whereIn('id', [9]);
      }) ->leftJoin('user_parameters as u_p9', function ($q) {
               $q->on('users.id', '=', 'u_p9.users_id')
              ->where('u_p9.parameters_id', 1);
       })
       ->first();      
    $cordenador=$direitor->value;
    //Fim direitor
    
    $lectiveFase = DB::table('lective_candidate')->find($request->fase);

    //Pega Vaga de todos os cursos por ano lectivo
    $vagas = DB::table('anuncio_vagas  as vaga')
    ->join('courses as c', 'c.id', '=', 'vaga.course_id')
    ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'c.id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
    })
     ->where([
      ['vaga.lective_year', '=', $anoletivo],
      ['vaga.id_fase', '=', $request->fase],
      ['vaga.deleted_at', '=', null]
     ])
     ->select(['vaga.id as id_vaga','ct.display_name','ct.courses_id','vaga.manha as manha','vaga.tarde as tarde','vaga.noite as noite','vaga.lective_year as id_ano_lectivo','vaga.course_id'])
     ->orderBy('ct.display_name')
     ->get();
//Fim Vaga dos Cursos 




// Pega candidatos a estudantes 
 $lectiveYear = LectiveYear::where('id', $anoletivo)->first();    
  
 $candidatos = User::query()
    ->whereHas('roles', function ($q) {
        $q->whereIn('id', [15]);
    })->with(['courses' => function($q) {
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
    ->leftJoin('user_parameters as full_name', function ($join) {
        $join->on('users.id', '=', 'full_name.users_id')
                ->where('full_name.parameters_id', 1);
    })
    ->leftJoin('lective_years', function($join){
        $join->whereRaw('users.created_at between `start_date` and `end_date`');
    })
    ->join('user_classes as ut', function ($join)  {
        $join->on('users.id', '=', 'ut.user_id');
      
    }) 
    ->join('classes as tur', function ($join)   {
        $join->on('tur.id', '=', 'ut.class_id');   
    }) 
    ->join('lective_year_translations as lyt', function ($join) {
        $join->on('lyt.lective_years_id', '=', 'lective_years.id');
        $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('lyt.active', '=', DB::raw(true));
    })
    // ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u1.id')

    ->join('user_candidate as uca','uca.user_id','=','u1.id')
    ->where('uca.year_fase_id','=',$request->fase)

    ->select([
        'users.*',
        'full_name.value as name_name',
         'u1.name as us_created_by',
        'u2.name as us_updated_by',
        'tur.display_name as turma',
        // 'u3.name as deleted_by',
        'article_requests.status as state',
        'candidate.value as cand_number',
        'lyt.display_name as lective_year_code'
    ])
    ->whereBetween('users.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])
    ->whereNull('article_requests.deleted_at')
    ->wherenotNull('article_requests.status')
    ->distinct();

  $model=$candidatos->get();
//Fim Candidatos a estudante
//Pagos e não pagos
$todos=collect($candidatos->get());

 $grupo=$todos->groupBy('turma')
->map(function ($item, $key) {
        $turmaL= substr($item[0]->turma, -3,1);
        $id_curso= $item[0]->courses[0]->id;
        $qtd = count($item)  ;
        return $dados=$turmaL.",".$qtd.",".$id_curso;
});
 foreach($grupo as $tudo){
  
    $var=explode(",",$tudo);
    if($var[0]=="M"){
          
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'NC'],                      
                [ 
                'manha' =>$var[1]
                ]
                );  
        }
       if($var[0]=="T"){
        //    var_dump($var[0].$var[1].$var[2]);
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'NC'],                      
                [ 
                'tarde' =>$var[1]
                ]
                );  
        }
       if($var[0]=="N"){
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'NC'],                      
                [ 
                'noite' =>$var[1]
                ]
                );  
        }
 }
//  return "";
   $periodo=[];
   $i=0;
   $pago= [];
   $Npago= [];
   foreach($model as $item){
       if($item->state=="total") {   
        $pago[]=$item;
        }
        if($item->state=="pending")  {   
            $Npago[]=$item;
        }
    }  
 
//Fim Pago e não pagos
//Pagos
  $pagoCollection=collect($pago)->groupBy('turma')
  ->map(function ($item, $key) {
          $turmaL= substr($item[0]->turma, -3,1);
          $id_curso= $item[0]->courses[0]->id;
          $qtd = count($item)  ;
          return $dados=$turmaL.",".$qtd.",".$id_curso;
  });
  foreach($pagoCollection as $tudo){
  
    $var=explode(",",$tudo);
    if($var[0]=="M"){
          
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'AD'],                      
                [ 
                'manha' =>$var[1]
                ]
                );  
        }
       if($var[0]=="T"){
        //    var_dump($var[0].$var[1].$var[2]);
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'AD'],                      
                [ 
                'tarde' =>$var[1]
                ]
                );  
        }
       if($var[0]=="N"){
            $esta=  DB::table('estatistica')->updateOrInsert(
                [ 'id_curso' => $var[2], 'categoria'=>'AD'],                      
                [ 
                'noite' =>$var[1]
                ]
                );  
        }
 }


//Pegar as disciplinas de exame de acesso 
//notas dos estudantes para comparar com os mais  pagos e ver a Positiva e negativas


//  $grade = Grade::whereIn('discipline_id',array($disciplines[0]->id,$disciplines[1]->id))
//  ->where('course_id', $courseId)
//  ->orderBy('value','DESC')
//  ->get();

 //nao pagos
//    $pagoCollectionN=collect($Npago)->groupBy('turma')
//    ->map(function ($item, $key) {
//            $turmaL= substr($item[0]->turma, -3,1);
//            $id_curso= $item[0]->courses[0]->id;
//            $qtd = count($item)  ;
//            return $dados=$turmaL.",".$qtd.",".$id_curso;
//    });
// return $pagoCollection;
// view("Grades::exame.list_candidate")->with($data);





   $estatistica = DB::table('estatistica  as esta')
   ->join('courses as c', 'c.id', '=', 'esta.id_curso')
   ->leftJoin('courses_translations as ct', function ($join) {
       $join->on('ct.courses_id', '=', 'c.id');
       $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
       $join->on('ct.active', '=', DB::raw(true));
   })->select(['esta.*'])->get();



    $pdf = PDF::loadView("Users::candidate.estatistica", compact(
        'vagas',
        'pago',
        'Npago',
        'model',
        'estatistica',
        'cordenador',
        'lectiveYears',
        'lectiveFase'
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
    
    $pdf_name="estatisticas_candidatos_a_estudante";
    $institution = Institution::latest()->first();
    $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
    $pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');



    
}
















































public function anuncioPDF(Request $request, $anoletivo){
  //dd($request->all());

  if(!isset($request->fase)){
    Toastr::warning(__('Seleciona uma fase'), __('toastr.warning'));
    return redirect()->back();
  }

    $lectiveYears = LectiveYear::with(['currentTranslation'])
    ->where('id',$anoletivo)
    ->select('*')
    ->get(); 

    

    
     $lectiveFase = DB::table('lective_candidate')->find($request->fase);
     
    $vagas = DB::table('anuncio_vagas  as vaga')
    ->join('courses as c', 'c.id', '=', 'vaga.course_id')
    ->leftjoin('department_translations as dpt', 'dpt.departments_id', '=', 'c.departments_id')
    ->leftJoin('lective_candidate as lc','lc.id','=','vaga.id_fase') // sedrac
    ->leftJoin('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'c.id');
        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
        $join->on('ct.active', '=', DB::raw(true));
    })
     ->where([
      ['vaga.lective_year', '=', $anoletivo],
      ['vaga.id_fase', '=', $request->fase],
      ['vaga.deleted_at', '=', null]
     ])
     ->where([
      ['dpt.language_id', '=',1],
      ['dpt.active', '=', 1],
     ])
     ->whereNull("c.deleted_at")
     ->select(['dpt.display_name as departamento','c.departments_id as departamento_id','vaga.id as id_vaga','ct.display_name','ct.courses_id','vaga.manha as manha','vaga.tarde as tarde','vaga.noite as noite','vaga.lective_year as id_ano_lectivo','lc.fase'])
     ->orderBy('ct.display_name')
     ->get();

    $vagas = $vagas->groupBy('departamento'); 
     
    // view("Grades::exame.list_candidate")->with($data);
    $institution = Institution::latest()->first();
    
        $cordenador =DB::table('user_parameters as up')
        ->join("institutions","institutions.vice_director_academica","up.users_id")
        ->join("user_parameters as grau","up.users_id","grau.users_id")
        ->join("user_parameters as categ","up.users_id","categ.users_id")
        ->join("grau_academico as grauTable","grauTable.id","grau.value")
        ->join("categoria_profissional as categoriaProfissional","categoriaProfissional.id","categ.value")
        ->where("up.parameters_id",ParameterEnum::NOME)
        ->where("grau.parameters_id",ParameterEnum::GRAU_ACADEMICO)
        ->where("categ.parameters_id",ParameterEnum::CATEGORIA_PROFISSIONAL)
        ->select(["up.value as nome","grauTable.abreviacao","categoriaProfissional.nome as categoria"])
       
    ->first(); 
    
    $role_id = 9;
    $vd_academica_role_name = DB::table('roles')
                                 ->join('role_translations as rt', 'roles.id', '=', 'rt.role_id')
                                 ->where('rt.role_id', $role_id)
                                 ->where('rt.active', 1)
                                 ->pluck('display_name')
                                 ->first();

    $cordenador = $cordenador;
    $titulo_documento = "Anúncio de vaga";
    $anoLectivo_documento = "Ano ACADÊMICO: ";
    $documentoGerado_documento = "Documento gerado a";
    $documentoCode_documento = 3;

    $pdf = PDF::loadView("Users::candidate.pdf-vagas", compact(
        'vagas',
        'cordenador',
        'lectiveFase',
        'lectiveYears',
        'institution',
        'titulo_documento',
        'anoLectivo_documento',
        'documentoGerado_documento',
        'documentoCode_documento',
        'vd_academica_role_name'
    ));

    // $pdf->setOption('margin-top', '1mm');
    // $pdf->setOption('margin-left', '1mm');
    $pdf->setOption('margin-bottom', '12mm');
    // $pdf->setOption('margin-right', '1mm');
    $pdf->setOption('enable-javascript', true);
    $pdf->setOption('debug-javascript', true);
    $pdf->setOption('javascript-delay', 1000);
    $pdf->setOption('enable-smart-shrinking', true);
    $pdf->setOption('no-stop-slow-scripts', true);
    $pdf->setPaper('a4', 'portrait');
    
    $pdf_name = "Anuncio_vagas_" . $lectiveYears[0]->currentTranslation->display_name . "(" . $lectiveFase->fase . "ª Fase)";

    // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
    $pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');



    
}














    public function ajaxVagas(Request $request,$id,$anoLectivo)
    {
        try {
        
            $count=0;

            $id_fase = $request->fase;
            $lectiveFase = DB::table('lective_candidate')->find($id_fase);
            
            $select = [
                'vaga.id_fase','vaga.id as id_vaga','ct.display_name',
                'vaga.manha as manha','vaga.tarde as tarde','vaga.noite as noite',
                'vaga.lective_year as id_ano_lectivo', 'lc.fase as fase_num'
            ];

            $vagas = DB::table('anuncio_vagas  as vaga')
            ->join('courses as c', 'c.id', '=', 'vaga.course_id')
            ->leftjoin('lective_candidate as lc','lc.id','=','vaga.id_fase')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'c.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })->where([
              ['vaga.course_id', '=', $id],
              ['vaga.lective_year', '=', $anoLectivo],
              ['vaga.id_fase', '=', $id_fase],
              ['vaga.deleted_at', '=', null],
             ])
             ->select($select)
             ->get();
            
            if(count($vagas) == 0 ){

                $userId = auth()->user()->id;
      
              


            $vagas = DB::table('anuncio_vagas  as vaga')
                 ->join('courses as c', 'c.id', '=', 'vaga.course_id')
                 ->leftjoin('lective_candidate as lc','lc.id','=','vaga.id_fase')
                 ->leftJoin('courses_translations as ct', function ($join) {
                     $join->on('ct.courses_id', '=', 'c.id');
                     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                     $join->on('ct.active', '=', DB::raw(true));
                 })
                  ->where([
                //    ['vaga.id', '=', $id_Vaga],
                   ['vaga.course_id', '=', $id],
                   ['vaga.lective_year', '=', $anoLectivo],
                   ['vaga.deleted_at', '=', null]
                  ])
                  ->select($select)
                 ->get();
                
                if(sizeof($vagas) == 0){
                    $vagas = [$this->firstAnuncioVaga($id,$id_fase,$anoLectivo)];
                }else{
                    $vagas = $this->vagasFases($vagas,$lectiveFase);
                }
                
            }
           
            
            return response()->json(['course_vagas'=>$vagas]);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function firstAnuncioVaga($id_curso, $id_fase, $anoLectivo){
       $curso = DB::table('courses_translations as ct')
        ->where('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
        ->where('ct.active', '=', DB::raw(true))
        ->where('ct.courses_id',$id_curso)
        ->first();
       return (object)[
            "id_fase" => $id_fase, "id_vaga" => 0,
            "display_name" => $curso->display_name,
            "manha" => 0, "tarde" => 0, "noite" => 0, 
            "id_ano_lectivo" => $anoLectivo, "fase_num" => 1
       ];
    }

    //novo codigo sedrac
    private function vagasFases($vagas, $lectiveFase){
        $model = [];
        
        foreach($vagas as $vaga)      
            if($vaga->id_fase == $lectiveFase->id)
                array_push($model, $vaga);
            
        if(sizeof($model) == 0 && sizeof($vagas) > 0){
            $vaga = $vagas[0];
            $vaga->fase_num = $lectiveFase->fase;
            $vaga->id_vaga= null;
            $vaga->manha = 0;
            $vaga->tarde = 0;
            $vaga->noite = 0;
            $vaga->id_fase = $lectiveFase->id;
            array_push($model, $vaga);
        }

        return $model;
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



    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        //  return $request->all();
       
        try {
            $course = $request->get('course');
            $id = $request->get('id_vaga');
            $userId = Auth::user()->id;
            $faseId = $request->get('fase') ?? "";
            $yearId = $request->get('year') ?? "";
           
            $data = [
                'id' => $request->get('id_vaga'),
                'manha' => $request->get('manha'),
                'tarde' => $request->get('tarde'),
                'noite' => $request->get('noite'),
            ];
              
              DB::transaction(function () use ($data, $course, $id,$userId, $faseId, $yearId) {
                  for ($i=0; $i < count($data['manha']); $i++) {
                    DB::table('anuncio_vagas')->updateOrInsert([ 'id' => $id[0], 'id_fase' => $faseId],[
                        'course_id' => $course,
                        'manha' => (double)$data['manha'][$i],
                        'tarde' => (double)$data['tarde'][$i],
                        'noite' => (double)$data['noite'][$i],
                        'id_fase' => $faseId,
                        'lective_year' => $yearId,
                        'updated_by' => $userId,
                        'created_at' => Carbon::now(),
                        ]
                    );
                }
            });
            

            Toastr::success(__('Os dados foram actualizado com sucesso!'), __('toastr.success'));

            return redirect()->back();
            //return response()->json(['grade' => $grade], 201);
        } 
        catch (Exception | Throwable $e) {
            dd($e);
            Toastr::error(__('Erro ao actualizar as vagas do curso'), __('toastr.error'));
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
























    public function getStudentsBy(Request $request, $lectiveYears,$courseId,$id_disciplina,$turma)
    {

        try {
          
            $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();
            $model = User::query()
              ->whereHas('roles', function ($q)  {
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
                ->leftJoin('grades as notas', 'student_id', '=', 'users.id')


                          //->leftJoin('user_classes as uc', 'uc.user_id', '=', 'u1.id')


                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                            ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as candidate', function ($join) {
                    $join->on('users.id', '=', 'candidate.users_id')
                    ->where('candidate.parameters_id', 311);
                })

                ->join('user_courses as uc', function ($join) use($courseId)  {
                    $join->on('users.id', '=', 'uc.users_id')
                    ->where('uc.courses_id', $courseId);
                })
                ->join('user_classes as ut', function ($join) use($turma)  {
                    $join->on('users.id', '=', 'ut.user_id')
                    ->where('ut.class_id', $turma);
                }) 
                ->join('classes as tur', function ($join) use($turma)  {
                    $join->on('tur.id', '=', 'ut.class_id')
                    ->where('tur.id', $turma);
                }) 

                //  ->join('classes as tur', 'tur.id', '=', 'ut.class_id')

                ->leftJoin('lective_years', function ($join) {
                    $join->whereRaw('users.created_at between `start_date` and `end_date`');
                })
                ->join('lective_year_translations as lyt', function ($join) {
                    $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                    $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('lyt.active', '=', DB::raw(true));
                })->where('lective_years.id',$lectiveYears)
                 ->where('notas.course_id',$courseId)
                 ->where('notas.discipline_id',$id_disciplina)
                 ->where('tur.id', $turma)

                ->select([
                    'users.*',
                    'users.id',
                    'ut.*',
                    'tur.display_name',
                    'notas.value',
                    'u1.name as us_created_by',
                    'full_name.value as name_completo',
                    'u2.name as us_updated_by',
                    // 'u3.name as deleted_by',
                    'article_requests.status as state',
                    'candidate.value as cand_number',
                    'lyt.display_name as lective_year_code'
                ])

                //->whereBetween('users.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])

                ->distinct('article_requests.status')
                 ->orderBy('name_completo')
                ->get();



         if(count($model)==0){


         $model = User::query()
            ->whereHas('roles', function ($q)  {
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
              
              
              ->leftJoin('user_parameters as full_name', function ($join) {
                  $join->on('users.id', '=', 'full_name.users_id')
                          ->where('full_name.parameters_id', 1);
              })
              ->leftJoin('user_parameters as candidate', function ($join) {
                  $join->on('users.id', '=', 'candidate.users_id')
                  ->where('candidate.parameters_id', 311);
              })

              ->join('user_courses as uc', function ($join) use($courseId)  {
                  $join->on('users.id', '=', 'uc.users_id')
                  ->where('uc.courses_id', $courseId);
              })
            ->join('user_classes as ut', function ($join) use($turma)  {
                $join->on('users.id', '=', 'ut.user_id')
                ->where('ut.class_id', $turma);
            }) 
             ->join('classes as tur', function ($join) use($turma)  {
                $join->on('tur.id', '=', 'ut.class_id')
                ->where('tur.id', $turma);
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
                  'full_name.value as name_completo',
                  'u2.name as us_updated_by',
                  'u3.name as deleted_by',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code'
              ])

              //->whereBetween('users.created_at', [$lectiveYear->start_date, $lectiveYear->end_date])

              ->distinct('article_requests.status')
               ->orderBy('name_completo')
              ->get();
            
       $notas=[];           
        for($i=0;$i< count($model);$i++){
            
                
             // echo $aluno->name_completo."  Nota:".$nota->value ."</br>";
            //    return $model[$i];         
            $model[$i]["value"]=null;         
        
            
           }
             

           
            // return "Vazia".$model;

         }           
        // $notas=[];           
        // foreach($model as $aluno){
        //     foreach($grade as $nota){
        //         if($aluno->id==$nota->student_id)
        //         // echo $aluno->name_completo."  Nota:".$nota->value ."</br>";
        //         $notas[]=['value'=>$nota->value ];         
        //     }
            
        // }
 
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
    function showStudentList($courseId,$lectiveYears,$turma)
    {


        try {
            
          $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

          $model = User::query()
            ->whereHas('roles', function ($q)  {
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
              
              ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
            })
              ->leftJoin('user_parameters as candidate', function ($join) {
                  $join->on('users.id', '=', 'candidate.users_id')
                  ->where('candidate.parameters_id', 311);
              })

              ->join('user_courses as uc', function ($join) use($courseId)  {
                  $join->on('users.id', '=', 'uc.users_id')
                  ->where('uc.courses_id', $courseId);
              })
              ->join('user_classes as ut', function ($join) use($turma)  {
                $join->on('users.id', '=', 'ut.user_id')
                ->where('ut.class_id', $turma);
            }) 
            ->join('classes as tur', function ($join) use($turma)  {
                $join->on('tur.id', '=', 'ut.class_id')
                ->where('tur.id', $turma);
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
                  'tur.display_name as turma',
                  'u1.name as us_created_by',
                  'u2.name as us_updated_by',
                  'full_name.value as name_completo',
                  // 'u3.name as deleted_by',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code',
                  
              ])

              
              ->where('tur.id', $turma)
              ->distinct('article_requests.status')
               ->orderBy('name_completo')
              ->get();

            //   $model->courses->map(function ($course) {
            // })->implode(", ");
            $curso="";
          
            foreach($model[0]->courses as $cursoD){  
                $curso = $cursoD->currentTranslation->display_name;
                
            }
       $turmaC=$model[0]->turma;
          $data = [
                'model' => $model,
                'curso'=>$curso,
                'turmaC'=>$turmaC
            ];           
           
   view("Grades::exame.list_candidate")->with($data);

    $pdf = PDF::loadView("Grades::exame.list_candidate", compact(
        'model',
        'curso',
        'turmaC'
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
    
    
        $pdf_name="Lista_candidatos";
        $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
        $pdf->setOption('footer-html', $footer_html);
    return $pdf->stream($pdf_name.'.pdf');
   
   
          

      } catch (Exception | Throwable $e) {
          Log::error($e);
          return response()->json($e->getMessage(), 500);
      }


    }































    
// Lista dos candidatos a estudante
    function showStudentGrades($id,$lectiveYears,$courseId,$turma)
    {


        try {


            $disciplines = Discipline::leftJoin('disciplines_translations as dt', function ($join) {
                $join->on('dt.discipline_id', '=', 'disciplines.id');
                $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dt.active', '=', DB::raw(true));
            })
                    ->select('disciplines.id as id', 'disciplines.code as code', 'dt.display_name as disciplina')
                    ->leftJoin('discipline_has_areas', 'discipline_has_areas.discipline_id', '=', 'disciplines.id')
                    ->leftJoin('discipline_areas', 'discipline_areas.id', '=', 'discipline_has_areas.discipline_area_id')
                    ->where('disciplines.courses_id', $courseId )
                    ->where('discipline_area_id', 18);

            if (auth()->user()->hasRole('teacher')) {
                $teacherDisciplines = auth()->user()->disciplines()->pluck('id')->all();
                $disciplines = $disciplines->whereIn('id', $teacherDisciplines);
            }
 
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
            //Acina tem o código de pegar as disciplinas 
  
            $lectiveYear = LectiveYear::where('id', $lectiveYears)->first();

       $model = User::query()
            ->whereHas('roles', function ($q)  { 
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
              
              ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
            })
              ->leftJoin('user_parameters as candidate', function ($join) {
                  $join->on('users.id', '=', 'candidate.users_id')
                  ->where('candidate.parameters_id', 311);
              })

              ->join('user_courses as uc', function ($join) use($courseId)  {
                  $join->on('users.id', '=', 'uc.users_id')
                  ->where('uc.courses_id', $courseId);
              })

              ->leftJoin('grades as notas', function ($join) use ($courseId,$disciplines) {
                $join->on('uc.users_id', '=', 'notas.student_id')
                ->where('notas.course_id', $courseId)
                ->where('notas.discipline_id',655);
             })

              ->join('user_classes as ut', function ($join) use($turma)  {
                $join->on('users.id', '=', 'ut.user_id')
                ->where('ut.class_id', $turma);
            }) 
            ->join('classes as tur', function ($join) use($turma)  {
                $join->on('tur.id', '=', 'ut.class_id')
                ->where('tur.id', $turma);
            }) 

              ->leftJoin('lective_years', function ($join) {
                  $join->whereRaw('users.created_at between `start_date` and `end_date`');
              })
              ->join('lective_year_translations as lyt', function ($join) {
                  $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                  $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                  $join->on('lyt.active', '=', DB::raw(true));
              })->where('lective_years.id',$lectiveYears)
              ->where('tur.id', $turma)
              ->where('article_requests.status', 'total')
              ->select([
                  'users.*',
                  'users.id',
                  'tur.display_name as turma',
                  'u1.name as us_created_by',
                  'u2.name as us_updated_by',
                  'full_name.value as name_completo',
                  'article_requests.status as state',
                  'candidate.value as cand_number',
                  'lyt.display_name as lective_year_code'
              ])
             ->distinct('article_requests.status')
           //->orderBy('name_completo')
              ->get();

            //   $model->courses->map(function ($course) {
            // })->implode(", ");
            $curso="";
            foreach($model[0]->courses as $cursoD){  
                $curso = $cursoD->currentTranslation->display_name;
             }

             $turmaC=$model[0]->turma;
             $data = [
                     'model' => $model,
                     'curso'=>$curso,
                     'notas'=>$grade,
                     'disciplines'=>$disciplines,
                     'turmaC'=>$turmaC,
                    ];                  
       $notas=$grade;
       if($folha>1){
        //    return view("Grades::exame.list_note_two")->with($data);
        $pdf = PDF::loadView("Grades::exame.list_note_two", compact(
            'model',
            'curso',
            'notas',
            'disciplines',
            'turmaC'
        ));

       }else{
        // return view("Grades::exame.list_note")->with($data);

        $pdf = PDF::loadView("Grades::exame.list_note", compact(
            'model',
            'curso',
            'notas',
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
    $pdf->setPaper('a4', 'portrait');
    
    
        $pdf_name="Lista_candidatos";
        $footer_html = view()->make('Users::users.partials.pdf_footer')->render();
        $pdf->setOption('footer-html', $footer_html);
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
            ->whereHas('roles', function ($q)  {
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
              
              ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('users.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
            })
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


