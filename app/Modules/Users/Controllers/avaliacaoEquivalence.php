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
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Throwable;
use App\Modules\GA\Models\LectiveYear;

use Illuminate\Support\Facades\Auth;
use App\Model\Institution;

class avaliacaoEquivalence extends Controller
{
    



    public function index()
    {
        try {
               //Pegar ano lectivo corrente.
          $lectiveYears = LectiveYear::with(['currentTranslation'])
         ->get();

         $currentData = Carbon::now();

         $lectiveYearSelected = DB::table('lective_years')
         ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
         ->first();

         $courses = Course::with([
            'currentTranslation'
         ])->get();
          
        $data = [
                     'action' => 'create',
                     'languages' => Language::whereActive(true)->get(),
                     'courses' => $courses,
                     'lectiveYears'=>$lectiveYears,
                     'lectiveYearSelected'=>$lectiveYearSelected->id
              
                  ];
         
       
        return view('Users::equivalence.avaliacaoEquivalence')->with($data);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }







    public function getStudents($course_id,$lective)
    {

        $article="pedido_t_entrada";
        $emoItem = EmolumentCodevLective($article,$lective)->first();
        if(isset($emoItem))
        $emolumento_pedido[0]=$emoItem->id_emolumento;

        $article='mudanca_curso';
        $emoItem = EmolumentCodevLective($article,$lective)->first();
        if(isset($emoItem))
        $emolumento_pedido[1]=$emoItem->id_emolumento;
         
           $lectiveYears = LectiveYear::with(['currentTranslation'])
           ->get();
  
           $currentData = Carbon::now();
  
           $lectiveYearSelected = DB::table('lective_years')
           ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
           ->first();

            $students = User::query()
             ->whereHas('roles', function($q) {
                 // $q->where('id', '!=', 15);
                 $q->where('id', '=', 6);
             })
             ->join('tb_transference_studant as tb_st', function($join) use ($lectiveYearSelected){
                $join->on('tb_st.user_id','=','users.id')
                ->whereBetween('tb_st.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
             })
                 ->leftJoin('users as u1', 'u1.id', '=', 'users.created_by')
                 ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                 ->join('courses_translations as ct', function ($join) {
                         $join->on('ct.courses_id', '=', 'uc.courses_id');
                         $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                         $join->on('ct.active', '=', DB::raw(true));
                     })
                 
                 ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                 ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
      
                 ->leftJoin('user_parameters as full_name', function ($join) {
                     $join->on('users.id', '=', 'full_name.users_id')
                     ->where('full_name.parameters_id', 1);
                 })
                  ->leftJoin('user_parameters as up_meca', function ($join) {
                     $join->on('users.id', '=', 'up_meca.users_id')
                     ->where('up_meca.parameters_id', 19);
                 })
                 ->leftJoin('user_parameters as up_bi', function ($join) {
                     $join->on('users.id', '=', 'up_bi.users_id')
                     ->where('up_bi.parameters_id', 14);
                 })
                 ->leftJoin('article_requests as art_requests',function ($join) use( $emolumento_pedido)
                 {
                     $join->on('art_requests.user_id', '=','users.id')
                     ->whereIn('art_requests.article_id',$emolumento_pedido)
                     ->whereNull('art_requests.deleted_by') 
                     ->whereNull('art_requests.deleted_at');
                 })
                 ->where('uc.courses_id',$course_id)
                 ->where('art_requests.status','=','total')
                 ->select([
                     'users.*',
                     'full_name.value as nome_student',
                     'up_meca.value as matricula',
                     'art_requests.status as state',
                     'u1.name as created_by',
                     'u2.name as updated_by',
                     'u3.name as deleted_by',
                     'up_bi.value as n_bi',
                     'ct.display_name as curso'
                 ])
                 ->distinct('id')
                 ->get();    
                   

            return response()->json($students);
    }



    public function getStudentsDsiciplines($student_id){

        $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
    
             $currentData = Carbon::now();
    
             $lectiveYearSelected = DB::table('lective_years')
             ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
             ->first();
    
        $data=DB::table('tb_equivalence_studant_discipline as eqDisc')
        ->join('tb_transference_studant as trans', function($join) use ($lectiveYearSelected){
            $join->on('trans.id','=','eqDisc.id_transference_user')
                ->whereBetween('trans.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
        })
        ->join('users as u','u.id','=','trans.user_id')
        ->join('disciplines', 'disciplines.id', '=', 'eqDisc.id_discipline_equivalence')
    
    
        ->join('disciplines_translations as dt', function ($join) {
            $join->on('dt.discipline_id', '=', 'disciplines.id');
            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dt.active', '=', DB::raw(true));
        })
        ->leftJoin('article_requests as art_requests',function ($join) use($student_id)
        {
            $join->on('art_requests.user_id', '=','u.id');
            $join->on('art_requests.discipline_id','=','disciplines.id') 
            ->whereNull('art_requests.deleted_by') 
            ->whereNull('art_requests.deleted_at');
        })
        ->leftJoin('new_old_grades as percurso',function ($join) use($student_id)
        {
            $join->on('percurso.user_id', '=','u.id');
            $join->on('percurso.discipline_id','=','disciplines.id') ;
        })
        ->select([
            'disciplines.id as disc_id',
            'dt.display_name as disciplina',
            'disciplines.code as codigo',
            'art_requests.status as state',
            'percurso.grade as nota',
            'trans.type_transference as type'
        ])
        ->where('u.id',$student_id)
        ->get();
    
       return response()->json($data);
    
    
    }





public function edit($id){

    try {
        //Pegar ano lectivo corrente.
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

        $consulta = DB::table('tb_transference_studant as transf')
        ->join('users as u0', 'u0.id', '=', 'transf.user_id')
        ->join('users as u1', 'u1.id', '=', 'transf.created_by')
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
        ->join('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'uc.courses_id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
        ->leftJoin('user_parameters as u_p', function ($join) {
                $join->on('u0.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 1);
        })
        ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('u0.id', '=', 'up_meca.users_id')
                ->where('up_meca.parameters_id', 19);
        })
        ->where('transf.id',$id)
        ->where('transf.status_disc',1)
       
    
        ->select([
            'transf.*',
            'u0.id as id_usuario',
            'u_p.value as student',
            'u0.email as email',
            'u1.name as criado_por',
            'ct.display_name as course',
            'uc.courses_id as course_id',
        ])
    
    
    ->groupBy('u_p.value')
    ->distinct('id')
    ->first(); 
    



        if(!$consulta){
            Toastr::warning(__('A forLEARN não detectou um argumento invalido , por favor tente novamente'), __('toastr.warning'));
            return redirect()->back();
        }
        

        //Pegar equivalência já marcadas
        $Disci_Eq=DB::table('tb_equivalence_studant_discipline')
        ->where('id_transference_user',$consulta->id)->whereNull('deleted_by')->get()
        ;
        $currentData = Carbon::now();

        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();

         $data = [
                    'action' => 'create',
                    'languages' => Language::whereActive(true)->get(),
                    'dados_geral'=>$consulta,
                    'dados_discipline'=>$Disci_Eq,
                    'lectiveYears'=>$lectiveYears,
                    'lectiveYearSelected'=>$lectiveYearSelected->id
            
                ];
        

        return view('Users::equivalence.discipline_student_equivalence')->with($data);

        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }


}



















    public function create(LectiveYear $lective_year)
    {
       

            try {
            
            // $data = [
            //          'action' => 'create',
            //          'languages' => Language::whereActive(true)->get(),
            //          'lective_year'=>$lective_year,
            //          'users' => $this->studentsWithCourseAndMatriculationSelectList()
            //       ];
         

        // return view('Users::confirmations-matriculations.confirmation')->with($data);
      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }





  
     
     
    public function ajaxUserData($studentId)
    {       
     try{

 

        return response()->json(array('html' => $view));
    
      } catch (Exception | Throwable $e) {
         return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
      }
        


    }







    public function store(Request $request)
    {
      try{
          //Tabela
        
        $currentData = Carbon::now();

        $data = [
            'disciplines'=> $request->discipline_id,
            'user_student' => $request->Studants,
            'nota' => $request->nota
           ];
        //tb_transference_studant
         $validar=DB::table('tb_transference_studant')
         ->where('user_id',$data['user_student'])
         ->whereIn('type_transference',[1, 3])
         ->get();
         
        //  tb_avaliation_equivalence

        if(!$validar->isEmpty()){

         $lectiveYearPercurso = DB::table('lective_years as lt')
            ->join('lective_year_translations as yearLEctive', function ($join) {
                $join->on('yearLEctive.lective_years_id', '=', 'lt.id');
                $join->on('yearLEctive.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('yearLEctive.active', '=', DB::raw(true));
            })
            ->where('lt.id',$validar[0]->lectiveYear)
            ->first(); 
          
            
            for($i=0 ; $i<count( $data['nota']);$i++){
                //não gurdar nota nula
                if($data['nota'][$i]!=null){
                    //Guardar nota na tabla suplente.   
                    $suplente= DB::table('tb_avaliation_equivalence')
                    ->updateOrInsert(
                        [
                            'id_discipline' =>  $data['disciplines'][$i],
                            'id_transference_user' =>  $validar[0]->id
                        ],
                        [
                            'grade' =>  $data['nota'][$i],
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id
                        ]
                    );


                    //Actualizar o percurso

                     //Condicao para eliminar a mesma disciplina no percurso >2
                    $consulta=DB::table('new_old_grades')
                    ->where('user_id',$data['user_student'])
                    ->where('discipline_id',$data['disciplines'][$i])
                    ->get();
                    
                    if(count($consulta)>1){
                        $consulta=DB::table('new_old_grades')
                        ->where('user_id',$data['user_student'])
                        ->where('discipline_id',$data['disciplines'][$i])
                        ->delete();
                    }    

                    $Percurso = DB::table('new_old_grades')->updateOrInsert(
                        [
                            'user_id' => $data['user_student'],
                            'discipline_id' => $data['disciplines'][$i],
                        
                        ]
                        ,
                        [
                            'lective_year' =>  $lectiveYearPercurso->display_name,
                            'grade' => $data['nota'][$i],
                            'created_at' => $currentData,
                            'updated_at' => $currentData
                            
                            ]
                
                        ); 

             }
             //Fim do if de verificação se é null
             
             
            }
            
            //Fim do for
            



        }
        else{
          Toastr::warning(__('A forLEARN detectou uma anomalia ao localizar o dado de pedido de transferência do usuário selecionario! Tente novamente.'), __('toastr.warning'));
          return redirect()->back();
        }
        
         


          Toastr::success(__('As nota(s) foram gravadas com sucesso!'), __('toastr.success'));
          return redirect()->back();

      } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
      }
        

    }





    public function update($id)
    {
       
    }

            



}