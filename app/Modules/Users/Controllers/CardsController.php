<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\StudyPlan;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use App\Modules\Users\Requests\MatriculationRequest;
use DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use PDF;
use App\Modules\GA\Models\LectiveYear;
use App\Model\Institution;
use Toastr;
use App\Modules\Users\Events\PaidStudentCardEvent;

class CardsController extends Controller
{
  
    public function index()
    {
        try {
         
           $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            //Curso 
        $courses = Course::with([
                'currentTranslation'
            ])->whereNull('deleted_by')
            ->get();

            return view('Users::list-class-matriculation.index', compact('lectiveYears', 'lectiveYearSelected','courses'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    
   
    private function pre_matricula_confirma_emolumento($lectiveYearSelected){
     
        $confirm=EmolumentCodevLective("confirm",$lectiveYearSelected)->first();
        $Prematricula=EmolumentCodevLective("p_matricula",$lectiveYearSelected)->first() ;   
        $emolumentos=[];

        if($confirm!=null){
            $emolumentos[]=$confirm->id_emolumento;
        }
        if($Prematricula!=null){
            $emolumentos[]=$Prematricula->id_emolumento;
        }
        return $emolumentos;


    }


    public function student_pdf($request)
    {
        

     
            
            $data = explode(",",$request);
            $id = $data[0];
            $model = $data[1];
           
            
                $currentData = Carbon::now();
                    $lectiveYearSelected = DB::table('lective_years')
                    ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                    ->first();
                    
                $lt = DB::table('lective_year_translations')
                ->where("lective_years_id",$lectiveYearSelected->id)
                ->first();
                
                
        
        $emolumento_confirma_prematricula= $this->pre_matricula_confirma_emolumento($lectiveYearSelected->id);
        
         $student = Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                    ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')     
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })

                    ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
                    ->join('classes as cl', function ($join)  {
                        $join->on('cl.id', '=', 'mc.class_id');
                        $join->on('mc.matriculation_id', '=', 'matriculations.id');
                        $join->on('matriculations.course_year', '=', 'cl.year');
                    })                             
                                        

                    ->leftJoin('user_parameters as u_p', function ($join) {
                         $join->on('u0.id', '=', 'u_p.users_id')
                         ->where('u_p.parameters_id', 1);
                    })
                    ->leftJoin('user_parameters as picture', function ($join) {
                         $join->on('u0.id', '=', 'picture.users_id')
                         ->where('picture.parameters_id', 25);
                    })

                    ->leftJoin('user_parameters as up_meca', function ($join) {
                         $join->on('u0.id','=','up_meca.users_id')
                         ->where('up_meca.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as up_bi', function ($join) {
                        $join->on('u0.id','=','up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                   })

                  ->leftJoin('article_requests as art_requests',function ($join) use($emolumento_confirma_prematricula)
                    {
                        $join->on('art_requests.user_id','=','u0.id')
                        ->whereIn('art_requests.article_id', $emolumento_confirma_prematricula);
                    })
                    ->leftJoin('card_student_status as card', 'card.matriculation_id', '=', 'matriculations.id')
                    ->select([
                        'matriculations.*',
                        'u0.id as id_usuario',
                        'matriculations.code as code_matricula',
                        'up_meca.value as matricula',
                        'art_requests.status as state' ,
                        'up_bi.value as n_bi',
                        'cl.display_name as classe',
                        'u_p.value as student',
                        'u0.email as email',
                        'u1.name as criado_por',
                        'u2.name as actualizado_por',
                        'u3.name as deletador_por',
                        'ct.display_name as course',
                        //sedrac
                        'uc.courses_id as id_course',
                        'picture.value as photo',
                        'card.valido_ate as card_validity'
                    ])
                    ->where("u0.id",$id)
                    ->where('art_requests.deleted_by', null) 
                    ->where('art_requests.deleted_at', null)
                    ->groupBy('u_p.value')
                    
                    ->where('matriculations.lective_year', $lectiveYearSelected->id)
                    
                    ->distinct('id')
                    ->first();

                    if(!isset($student->card_validity)){
                         //gerar validade 
                        event(new PaidStudentCardEvent($student->id_usuario));
                    
                    }
                    if(isset($student->$student->id)){
                       $this->verify_status_cards($student->id);
                    }
                    
                    if(!isset($student->photo)){
                        Toastr::warning(__('Foto de perfil do(a) estudante em falta.'), __('toastr.warning'));
                        return back();
                    }
                 
               
                 
                $student->photo = str_replace(" ", "%20",'https://'.$_SERVER['HTTP_HOST'] . '/users/avatar/'.$student->photo);
        
                    
        
          

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE MATRICULADOS POR TURMA";
        $anoLectivo_documento = "Ano Lectivo :";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 1;

        if($model=="1"){
            $pdf = PDF::loadView("Users::cards.model1", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'student',
             'lt'
            ));     
        }
        
        if($model=="2"){
            $pdf = PDF::loadView("Users::cards.model2", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'student',
             'lt'
            ));     
        }
        
         if($model=="3"){
            $pdf = PDF::loadView("Users::cards.model3", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'student',
             'lt'
            ));     
        }
        
          if($model=="4"){
            $pdf = PDF::loadView("Users::cards.model4", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'student',
             'lt'
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
        
        $pdf_name= $student->student."_".$student->matricula;
        // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name.'.pdf');

    }
    
    public function all_student()
    {
        try {
           
           $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            //Curso 
        $courses = Course::with([
                'currentTranslation'
            ])->whereNull('deleted_by')
            ->get();

            return view('Users::cards.all_student', compact('lectiveYears', 'lectiveYearSelected','courses'));
        }
        
        catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    public function all_student_ajax($classe,$course,$AnoLectivo,$anoCurricular)
    {
  
            try{
                
                

               if(empty($classe)){
                      Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
                      return redirect()->back() ;
                } 
               $courses=DB::table('courses as curso')
                  ->join('courses_translations as ct', function ($join) {
                      $join->on('ct.courses_id', '=', 'curso.id');
                      $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                      $join->on('ct.active', '=', DB::raw(true));
                  })
                  ->where('curso.id',$course)
                  ->get();
          
          
                  //Consulta do Ano Lectivo
                  $lectiveYearSelectedP = DB::table('lective_years')
                  ->where('id',$AnoLectivo)
                  ->get();
          
                //   $cartao_pago = $this->verify_status_all_cards($classe);
                    
                  
                   $model=DB::table('matriculation_classes as mat_class')
                  ->join("matriculations as mat",'mat.id','mat_class.matriculation_id')
                  ->join("matriculation_disciplines as mat_disc",'mat.id','mat_disc.matriculation_id')
                  ->join("classes as turma",'mat_class.class_id','turma.id')
                  ->join("users as user",'mat.user_id','user.id')
                  ->leftJoin('user_parameters as u_p', function ($join) {
                      $join->on('user.id', '=', 'u_p.users_id')
                      ->where('u_p.parameters_id', 1);
                  })
                 ->leftJoin('user_parameters as up_meca', function ($join) {
                      $join->on('user.id','=','up_meca.users_id')
                      ->where('up_meca.parameters_id', 19);
                 }) 
           
                ->leftJoin('user_parameters as up_bi', function ($join) {
                  $join->on('user.id','=','up_bi.users_id')
                ->where('up_bi.parameters_id', 14);
                 })
                 ->join("study_plans_has_disciplines as st_has_d", "st_has_d.disciplines_id", "=", "mat_disc.discipline_id")
                ->join("article_requests as user_emolumento",'user_emolumento.user_id','user.id')
                ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
                ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'turma.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('user_parameters as picture', function ($join) {
                    $join->on('user.id', '=', 'picture.users_id')
                    ->where('picture.parameters_id', 25);
               })
                ->whereIn('code_dev.code', ["confirm","p_matricula"])
                ->where('user_emolumento.status', "total")
                ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
                ->select([
                      'user_emolumento.status as pago',
                      'article_emolumento.id as id_article',
                      'article_emolumento.code as code_article',
                      'turma.display_name as turma',
                      'ct.display_name as course_name',
                      'user.email',
                      'user.id',
                      'mat.code',
                      'up_meca.value as matricula',
                      'up_bi.value as n_bi',
                      'u_p.value as student',
                      'turma.lective_year_id as id_anoLectivo',
                      'mat_disc.matriculation_id',
                      'mat_disc.exam_only',
                      'picture.value as photo'
                      ]) 
           
                  ->orderBy('student','ASC')
                  ->distinct(['up_bi.value','mat.code','u_p.value'])
                  ->where('mat.lective_year', $lectiveYearSelectedP[0]->id)
                  ->where("turma.lective_year_id",$AnoLectivo)    
                  ->where("turma.id",$classe)   
                  ->where('ct.courses_id',$course) 
                  ->whereNull('mat.deleted_at')  
                //   ->whereIn('user.id',$cartao_pago)    
                  ->where("st_has_d.years",$anoCurricular) 
                  ->get(); 
                  
                  return Datatables::of($model)
                  ->addColumn('actions', function ($item) {
                        return view('Users::cards.datatable.actions', compact('item'));      
                    })
                  ->addColumn('photo', function ($item) {
                        return view('Users::cards.datatable.photo', compact('item'));      
                    })
                  ->addColumn('impressao', function ($item) {
                        return view('Users::cards.datatable.state_print', compact('item'));      
                    })
                  ->addColumn('entrega', function ($item) { 
                        return view('Users::cards.datatable.entrega', compact('item'));      
                    })
                        ->rawColumns(['actions','photo','impressao','entrega'])
                        ->addIndexColumn()
                        ->toJson(); 

            }catch (Exception | Throwable $e) {
                      return $e;
                      logError($e);
                      return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
                  }
                 
            
    }

  
    public function edit($matriculation,$lectiveyear){
        try {
             $cards = $this->verificar_cards($matriculation,$lectiveyear);
             return view('Users::cards.edit', compact('matriculation','lectiveyear','cards'));  
         }  
         
         catch (Exception | Throwable $e) {
             return $e;
             logError($e);
             return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
         }
    }
    
 
    public function verificar(Request $request){
       
    
       if(
        DB::table("card_student_status")
        ->where("matriculation_id",$request->get('matriculation'))
        ->where("lective_year",$request->get('lectiveyear'))
        ->exists()){
            
            // Se existir actualiza os dados
           
            DB::table('card_student_status')
            ->where("matriculation_id",$request->get('matriculation'))
            ->where("lective_year",$request->get('lectiveyear'))
            ->update([ 
                'matriculation_id' => $request->get('matriculation'),
                'lective_year' => $request->get('lectiveyear'),
                'impressao' => $request->get('impressao'),
                'data_impressao' => $request->get('data_impressao'),
                'entrega' => $request->get('entrega'),
                'data_entrega' => $request->get('data_entrega'),
                'updated_by' => auth()->user()->id
            ]);


       }else{

            // Se não existir actualiza os dados cadastra os 

            $card_status = DB::table('card_student_status')->insertGetId(
            [ 
                'matriculation_id' => $request->get('matriculation'),
                'lective_year' => $request->get('lectiveyear'),
                'impressao' => $request->get('impressao'),
                'data_impressao' => $request->get('data_impressao'),
                'entrega' => $request->get('entrega'),
                'data_entrega' => $request->get('data_entrega'),
                'created_by' => auth()->user()->id
            ]
             ); 
       }
  

    Toastr::success(__('Estado actualizado com sucesso'), __('toastr.success'));
    return redirect()->route('cards.all_student'); 
        
    }
    

    public static function verificar_cards($matriculation,$lective_year){
        
        return DB::table("card_student_status")
        ->where("matriculation_id",$matriculation)
        ->where("lective_year",$lective_year)
        ->first();
    }
    
    public function report(Request $request)
    {
            
            
            if(empty($request->classe)){
                Toastr::error(__('Verifique se selecionou uma turma antes de gerar o PDF.'), __('toastr.error'));
                return redirect()->back() ;
          } 
         $courses=DB::table('courses as curso')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'curso.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->where('curso.id',$request->course)
            ->get();
    
    
            //Consulta do Ano Lectivo
            $lectiveYearSelectedP = DB::table('lective_years')
            ->where('id',$request->AnoLectivo)
            ->get();
    
            // $cartao_pago = $this->verify_status_all_cards($request->classe);

            // if(!$cartao_pago){
            //     Toastr::warning(__('Nenhum estudante fez o pagamento do emolumento cart���o de estudante'), __('toastr.warning'));
            //     return back();
            // }
            
             $model=DB::table('matriculation_classes as mat_class')
            ->join("matriculations as mat",'mat.id','mat_class.matriculation_id')
            ->join("matriculation_disciplines as mat_disc",'mat.id','mat_disc.matriculation_id')
            ->join("classes as turma",'mat_class.class_id','turma.id')
            ->join("users as user",'mat.user_id','user.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
            })
           ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id','=','up_meca.users_id')
                ->where('up_meca.parameters_id', 19);
           }) 
          ->leftJoin('user_parameters as up_bi', function ($join) {
            $join->on('user.id','=','up_bi.users_id')
          ->where('up_bi.parameters_id', 14);
           })
           ->join("study_plans_has_disciplines as st_has_d", "st_has_d.disciplines_id", "=", "mat_disc.discipline_id")
          ->join("article_requests as user_emolumento",'user_emolumento.user_id','user.id')
          ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
          ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
          ->join('courses_translations as ct', function ($join) {
              $join->on('ct.courses_id', '=', 'turma.courses_id');
              $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('ct.active', '=', DB::raw(true));
          })
          ->leftJoin('user_parameters as picture', function ($join) {
              $join->on('user.id', '=', 'picture.users_id')
              ->where('picture.parameters_id', 25);
            })
            
          ->whereIn('code_dev.code', ["confirm","p_matricula"])
          ->where('user_emolumento.status', "total")
          ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
          ->select([
                'user_emolumento.status as pago',
                'article_emolumento.id as id_article',
                'article_emolumento.code as code_article',
                'turma.display_name as turma',
                'ct.display_name as course_name',
                'user.email',
                'mat.code',
                'up_meca.value as matricula',
                'up_bi.value as n_bi',
                'u_p.value as student',
                'turma.lective_year_id as id_anoLectivo',
                'mat_disc.matriculation_id',
                'mat_disc.exam_only',
                'picture.value as photo'
                ]) 
     
            ->orderBy('student','ASC')
            ->distinct(['up_bi.value','mat.code','u_p.value'])
            ->where('mat.lective_year', $lectiveYearSelectedP[0]->id)
            ->where("turma.lective_year_id",$request->AnoLectivo)    
            ->where("turma.id",$request->classe)   
            ->where('ct.courses_id',$request->course) 
            ->whereNull('mat.deleted_at')    
            // ->whereIn('user.id',$cartao_pago)    
            ->where("st_has_d.years",$request->curricular_year) 
            
            ->get(); 
            
            // Cart���es eliminados
        
            
             $cartoes_eliminados = DB::table('matriculation_classes as mat_class')
            ->join("matriculations as mat",'mat.id','mat_class.matriculation_id')
            ->join("matriculation_disciplines as mat_disc",'mat.id','mat_disc.matriculation_id')
            ->join("classes as turma",'mat_class.class_id','turma.id')
            ->join("users as user",'mat.user_id','user.id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
            })
            ->leftjoin("card_student_status as cards",'cards.matriculation_id','mat_class.matriculation_id')
           ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id','=','up_meca.users_id')
                ->where('up_meca.parameters_id', 19);
           }) 
          ->leftJoin('user_parameters as up_bi', function ($join) {
            $join->on('user.id','=','up_bi.users_id')
          ->where('up_bi.parameters_id', 14);
           })
           ->join("study_plans_has_disciplines as st_has_d", "st_has_d.disciplines_id", "=", "mat_disc.discipline_id")
          ->join("article_requests as user_emolumento",'user_emolumento.user_id','user.id')
          ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
          ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
          ->join('courses_translations as ct', function ($join) {
              $join->on('ct.courses_id', '=', 'turma.courses_id');
              $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('ct.active', '=', DB::raw(true));
          })
          ->leftJoin('user_parameters as picture', function ($join) {
              $join->on('user.id', '=', 'picture.users_id')
              ->where('picture.parameters_id', 25);
            })
            
          ->whereIn('code_dev.code', ["confirm","p_matricula"])
          ->where('user_emolumento.status', "total")
          ->whereBetween('article_emolumento.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
          ->select([
                'user_emolumento.status as pago',
                'article_emolumento.id as id_article',
                'article_emolumento.code as code_article',
                'turma.display_name as turma',
                'ct.display_name as course_name',
                'user.email',
                'mat.code',
                'up_meca.value as matricula',
                'up_bi.value as n_bi',
                'u_p.value as student',
                'turma.lective_year_id as id_anoLectivo',
                'mat_disc.matriculation_id',
                'mat_disc.exam_only',
                'picture.value as photo'
                ]) 
     
            ->orderBy('student','ASC')
            ->distinct(['up_bi.value','mat.code','u_p.value'])
            ->where('mat.lective_year', $lectiveYearSelectedP[0]->id)
            ->where("turma.lective_year_id",$request->AnoLectivo)    
            ->where("turma.id",$request->classe)  
            ->whereNotNull("cards.data_impressao")
            ->where('ct.courses_id',$request->course) 
            ->whereNotNull('mat.deleted_at')    
                
            ->where("st_has_d.years",$request->curricular_year) 
            
            ->get(); 
            
        
        
        
            
        
        
            
                 
            $lt = DB::table('lective_year_translations')
            ->where("lective_years_id",$request->AnoLectivo)
            ->first();
            $ano = $request->curricular_year;
            $institution = Institution::latest()->first();
            $titulo_documento = "LISTA DE MATRICULADOS POR TURMA";
            $anoLectivo_documento = "Ano Lectivo :";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;

       
            $pdf = PDF::loadView("Users::cards.pdf_report", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'model',
             'ano',
             'lt',
             'cartoes_eliminados'
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
        
        $pdf_name= "RC_".$model[0]->course_name."_".$lt->display_name."_".$ano."_".$model[0]->turma;
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name.'.pdf');

    }


    public function verify_status_cards($smatriculation_id){
         $matriculation = DB::table("matriculations")
        ->where("id",$smatriculation_id)
        ->whereNull("deleted_at")
        ->select(["lective_year","user_id"])
        ->first();

        if(!isset($matriculation)){
            Toastr::warning(__('Nenhuma matr���cula foi detectada no corrente ano lectivo'), __('toastr.warning'));
            return back();
        }
        
        $payment = DB::table("articles")
        ->join("article_requests as user_emolumento",'user_emolumento.article_id','=','articles.id')
        ->where("articles.id_code_dev",14)
        ->where('user_emolumento.user_id','=',$matriculation->user_id)
        ->whereNull('user_emolumento.deleted_at')
        ->whereNull('user_emolumento.deleted_by')
        ->where('user_emolumento.status',"total")
        ->count();

        if(!$payment){
           
            Toastr::warning(__('O estudante não fez o pagamento do cartão de estudante'), __('toastr.warning'));
            return redirect()->route('cards.all_student');
        }


    }

    public function verify_status_all_cards($class){
          $matriculation = DB::table("matriculations")
        ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculations.id')
        ->where("mc.class_id",$class)
        // ->select('matriculation_id') 
        ->get(); 
        

        $all = array();

        foreach($matriculation as $item){
            array_push($all,$item->user_id);
        }

     
        $payment = DB::table("articles")
        ->join("article_requests as user_emolumento",'user_emolumento.article_id','=','articles.id')
        ->where("articles.id_code_dev",14)
        ->whereIn('user_emolumento.user_id',$all)
        ->whereNull('user_emolumento.deleted_at')
        ->where('user_emolumento.status',"total")
        ->get();

        
        $all = array();
        foreach($payment as $item){
            array_push($all,$item->user_id);
        }

        return $all;

    }
    
    public function report_all($lective_year,$year)
    {
             
                 

    
            //Consulta do Ano Lectivo
            $lectiveYearSelectedP = DB::table('lective_years')
            ->where('id',$lective_year)
            ->get();
    
            // $cartao_pago = $this->verify_status_all_cards($request->classe);

            if(!$year){
                Toastr::warning(__('Selecione o ano curricular'), __('toastr.warning'));
                return back();
            }
            if(!$lective_year){
                Toastr::warning(__('Selecione o ano curricular'), __('toastr.warning'));
                return back();
            }
            
            $model=DB::table('matriculation_classes as mat_class')
            ->join("matriculations as mat",'mat.id','mat_class.matriculation_id')
            ->join("matriculation_disciplines as mat_disc",'mat.id','mat_disc.matriculation_id')
            ->join("classes as turma",'mat_class.class_id','turma.id')
            ->join("users as user",'mat.user_id','user.id')
            ->leftjoin("card_student_status as cards",'cards.matriculation_id','mat_class.matriculation_id')
            ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('user.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
            })

           ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id','=','up_meca.users_id')
                ->where('up_meca.parameters_id', 19);
           }) 
          ->leftJoin('user_parameters as up_bi', function ($join) {
            $join->on('user.id','=','up_bi.users_id')
          ->where('up_bi.parameters_id', 14);
           })
           ->join("study_plans_has_disciplines as st_has_d", "st_has_d.disciplines_id", "=", "mat_disc.discipline_id")
          ->join("article_requests as user_emolumento",function($join){
            $join->on('user_emolumento.user_id','user.id')
            ->whereNull('user_emolumento.deleted_by')
          ->whereNull('user_emolumento.deleted_at');
          })
          
          ->join('transaction_article_requests as tar','tar.article_request_id','user_emolumento.id')
          ->join("transactions as trans", function($join)use ($lectiveYearSelectedP){
            $join->on("tar.transaction_id", "trans.id")
            ->whereBetween('trans.created_at', [$lectiveYearSelectedP[0]->start_date, $lectiveYearSelectedP[0]->end_date])
            ->where('trans.data_from','!=','estorno')
            ->where('trans.type', 'payment')
            ->whereNull("trans.deleted_by")
            ->whereNull("trans.deleted_at")
            ;
               
           })
	   ->leftJoin("transaction_receipts as tra_receipt", function($join) {
                $join->on("tra_receipt.transaction_id","=",  "trans.id")
                ->where('tra_receipt.path','!=',null);
       })
       ->leftJoin('historic_user_balance as historic_saldo', function ($join) {
        $join->on('historic_saldo.id_transaction', '=', 'trans.id')
        ->where('historic_saldo.data_from','=',null);
    })
          ->join("articles as article_emolumento",'user_emolumento.article_id','article_emolumento.id')
         
          ->join("code_developer as code_dev",'code_dev.id','article_emolumento.id_code_dev')
          ->join('courses_translations as ct', function ($join) {
              $join->on('ct.courses_id', '=', 'turma.courses_id');
              $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
              $join->on('ct.active', '=', DB::raw(true));
          })
          ->leftJoin('user_parameters as picture', function ($join) {
              $join->on('user.id', '=', 'picture.users_id')
              ->where('picture.parameters_id', 25);
            })
       
          ->whereIn('code_dev.code', ["cartao_estudante"])
            
          ->select([
                'user_emolumento.status as pago',
                'user_emolumento.id as emolumento',
                'article_emolumento.id as id_article',
                'article_emolumento.code as code_article',
                'turma.display_name as turma',
                'ct.display_name as course_name',
                'user.email',
                'user.id',
                'mat.code',
                'up_meca.value as matricula',
                'up_bi.value as n_bi',
                'u_p.value as student',
                'turma.lective_year_id as id_anoLectivo',
                'mat_disc.matriculation_id',
                'mat_disc.exam_only',
                'picture.value as photo',
                'cards.data_impressao',
                'cards.data_entrega',
                'turma.courses_id',
                'turma.id as id_turma'
                ]) 
     
            ->orderBy('student','ASC')
            ->distinct(['up_bi.value','mat.code','u_p.value','tra_receipt.id'])
            ->where('mat.lective_year', $lective_year)
            ->where("turma.lective_year_id",$lective_year)  
                  
            ->get()
            ->unique('emolumento');

            $card_total = DB::table("card_student_status")
            ->where("lective_year",$lective_year)
            ->whereNotNull("data_impressao")
            ->count();

            $turmas = collect($model)->groupBy('turma')->map(function ($item, $key) {

                $cartao  = ["fotografia" => 0, "imprimido" => 0, "entrega"=>0,"total"=>0,"course_id"=>$item[0]->courses_id,"turma"=>$item[0]->turma];
                
                // $pagos = $this->verify_status_all_cards($item[0]->id_turma);

                foreach ($item as $estudante) {

                    // if (in_array($estudante->id, $pagos, true)) {

                        if(isset($estudante->photo) && $estudante->photo!=""){
                            $cartao["fotografia"] = $cartao["fotografia"]+1;
                        }
                        if(isset($estudante->data_impressao) && $estudante->data_impressao!=""){
                            $cartao["imprimido"] = $cartao["imprimido"]+1;
                        }
                        if(isset($estudante->data_entrega) && $estudante->data_entrega!=""){
                            $cartao["entrega"] = $cartao["entrega"]+1;
                        } 
                        $cartao["total"] = $cartao["total"]+1;

                    // }
                }
               
                return $cartao;
            });
            
             $courses = DB::table('courses as curso')
            ->join('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'curso.id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })
            ->whereNull('curso.deleted_at')
            ->select(["curso.id","ct.display_name"])
            ->whereNotIn('curso.id',[8])
            ->get();

            $courses = collect($courses)->groupBy('display_name')->map(function ($item, $key) use($turmas) {
                
                $all_turmas = [];

                foreach ($turmas as $turmas_item) {
                    if(isset($turmas_item["course_id"]) && $turmas_item["course_id"]==$item[0]->id){
                        array_push($all_turmas,$turmas_item);        
                    }
                }

                return $all_turmas;
            });

            $courses = $courses->map(function($course){
               
                return $this->ordena_turma($course);
            });



                 
            $lt = DB::table('lective_year_translations')
            ->where("lective_years_id",$lective_year)
            ->first();
           
            $institution = Institution::latest()->first();
            $titulo_documento = "An���lise estat���stica dos cart���es";
            $anoLectivo_documento = "Ano Lectivo :";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;


       
            $pdf = PDF::loadView("Users::cards.pdf_statistic", compact(
             'institution',
             'titulo_documento',
             'anoLectivo_documento',
             'documentoGerado_documento',
             'documentoCode_documento',
             'model',
             'lt',
             'courses',
             'card_total'
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
        
        $pdf_name= "AE_cart���es_".$lt->display_name;
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);
        return $pdf->stream($pdf_name.'.pdf');

    }

    private function ordena_turma($plano)
    {

        for ($i = 0; $i < count($plano); $i++) {

            for ($j = $i + 1; $j < count($plano); $j++) {


                $min = $i;
               
                // pegar os códigos dos objecto
                $objA = $plano[$i]['turma'];
                $objB = $plano[$j]['turma'];

                // pegar a substring apartir do 4 caractere
                $subA = substr($objA, 2);
                $subB = substr($objB, 2);
                
                //verificar se a sub-string contém a letra 
              

                    // substituir o A por 0

                    $subA = str_replace('M', '0', $subA);
                    $subA = str_replace('T', '1', $subA);
                    $subA = str_replace('N', '2', $subA);


                    $subB = str_replace('M', '0', $subB);
                    $subB = str_replace('T', '1', $subB);
                    $subB = str_replace('N', '2', $subB);

                    // convertendo em inteiros
                    $subA = intval($subA);
                    $subB = intval($subB);
                    
                    // comparando
                    if ($subB < $subA) {
                        // Ordenar
                        $min = $j;

                    }

                    $aux = $plano[$min];
                    $plano[$min] = $plano[$i];
                    $plano[$i] = $aux;
                    continue;

              
            }


        }

        return $plano;
    }


    public function testEventListener(){

        
        event(new PaidStudentCardEvent($user_id));


    }
    
 
    }

