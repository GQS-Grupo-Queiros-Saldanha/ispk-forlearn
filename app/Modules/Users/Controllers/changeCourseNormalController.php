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

class changeCourseNormalController extends Controller
{
    



    public function studentchangeCourse()
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();

            $courses = Course::with([
                'currentTranslation'
            ])->get();

            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
                ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')
                ->first();
            $data = [
                'action' => 'create',
                'courses' => $courses,
                'lectiveYears' => $lectiveYears,
                'lectiveYearSelected' => $lectiveYearSelected->id
            ];

            return view('Users::change-course-normal.Listar-estudantes')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }




    
    public function studentChangeCourseAjax($lective_year)
    {
        try {

            $lectiveYears = LectiveYear::with(['currentTranslation'])
                ->get();
          // return $lective_year;

            $dados = DB::table('tb_change_course_normal as tccn')
                ->join('users as u', 'u.id', '=', 'tccn.id_student_user')
                ->join('users as u1', 'u1.id', '=', 'tccn.created_by')
                ->join('users as u2', 'u2.id', '=', 'tccn.updated_by')
                ->join('courses_translations as ct', 'ct.courses_id', "=", 'tccn.id_old_course')
                ->join('courses_translations as ct1', 'ct1.courses_id', "=", 'tccn.id_new_course')
                ->join('matriculations as m', 'm.user_id', "=", "tccn.id_student_user")
                ->leftJoin('article_requests as art', function($join){
                    $join->on('art.user_id','u.id')
                    ->whereNull('art.deleted_by')
                    ->whereNull('art.deleted_at');
                })
                ->leftJoin('articles as artG','artG.id','art.article_id')
                ->leftJoin('code_developer as codev','codev.id','artG.id_code_dev')
                ->where('tccn.lectiveYear', $lective_year)
                ->where('ct.active', 1)->where('ct1.active', 1)
                ->whereNull('ct.deleted_at')->whereNull('ct1.deleted_at')->whereNull('m.deleted_at')
                ->whereNull('m.deleted_by')
                ->whereNull('tccn.deleted_by')

                ->select(
                    'art.status as state',
                    'u.name as name',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'ct.display_name as curso_velho',
                    'ct1.display_name as curso_novo',
                    'tccn.id_old_course as id_curso_velho',
                    'tccn.id_new_course as id_curso_novo',
                    'tccn.id as id',
                    'tccn.description as description',
                    'tccn.created_at',      
                    'tccn.updated_at',  
                     'm.id as id_matricula',
                     'tccn.status as status_disc',
                     'u.id as student'
                )
                ->where('codev.code','mudanca_curso')
                ->get();

                return Datatables::of($dados)
                ->addColumn('actions', function ($item) {
                      return view('Users::change-course-normal.datatables.actions')->with('item', $item);
                })
                
                ->addColumn('status', function($state){
                        if ($state->state == "total"){return  " <span class='bg-success p-1 text-white'>PAGO</span>";}
                        else if($state->state == "pending"){return "<span class='bg-info p-1'>EM ESPERA</span>";}
                        else if($state->state == "partial"){ return  " <span class='bg-warning p-1'>PARCIAL</span>";}
                    
                })
                ->rawColumns(['status','actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function transferenceRequest()
    {
        try {
                //Pegar ano lectivo corrente.
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();
                
            $currentData = Carbon::now();

            $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
            ->first();
            
            $institution = Institution::latest()->first();

            $data = [
                        'action' => 'create',
                        'languages' => Language::whereActive(true)->get(),
                        'lectiveYears'=>$lectiveYears,
                        'lectiveYearSelected'=>$lectiveYearSelected->id,
                        'institution'=>$institution->nome
                  ];


                  
        return view('Users::change-course-normal.requerimento')->with($data);

      
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function getStudentsWhereHasCourse($course_id)
    {



            $students = User::query()
             ->whereHas('roles', function($q) {
                 $q->where('id', '=', 6);
             })
             
             ->whereHas('matriculation')
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
                 ->select([
                     'users.*',
                     'full_name.value as nome_student',
                     'up_meca.value as matricula',
                     'u1.name as created_by',
                     'u2.name as updated_by',
                     'u3.name as deleted_by',
                     'up_bi.value as n_bi',
                     'ct.display_name as curso',
                 ])
                 
                 ->get();    


            return response()->json($students);
    }



public function requerir_mudanca_de_course_student_store(Request $request){

    try{

        $courses=$request->courses;
        $students=$request->students;
        $anoLectivo=$request->anoLectivo;
        $courses_new=$request->courses_new;
        $description=$request->description;

        $emolumento_equivalence = EmolumentCodevLective(['mudanca_curso'],$request->anoLectivo)->first();
        if(!$emolumento_equivalence){
            Toastr::warning(__('A forLEARN não encontrou um emolumento de Pedido de mudança de curso  [ configurado no ano lectivo selecionado].'), __('toastr.warning'));
            return redirect()->back();
        }
        
            
        $currentData = Carbon::now();

        //Emolumento
        $Percurso_dados=DB::table('new_old_grades')
        ->where('user_id',$students)
        ->get(); 

        //Emolumento
        $consulta=DB::table('tb_change_course_normal')
        ->where('id_student_user',$students)
        ->whereNull('deleted_at')
        ->whereNull('deleted_by')
        ->get(); 

        if($consulta->isEmpty()){

                $student_insert_id=DB::table('tb_change_course_normal')->insertGetId([
                    'id_student_user' => $students,
                    'id_old_course' => $courses,
                    'id_new_course' => $courses_new,
                    'description' => $description,
                    'lectiveYear' =>  $anoLectivo,
                    'created_by' => Auth::user()->id, 
                    'updated_by' => Auth::user()->id, 
                    'created_at' =>$currentData,
                ]);

                $institution = Institution::latest()->first();
                DB::table('tb_transference_studant')->insert([
                    'user_id' => $students,
                    'lectiveYear' => $anoLectivo,
                    'school_name' =>$institution->nome,
                    'change_course' => $student_insert_id,
                    'type_transference' =>3,
                    'created_by' => Auth::user()->id
                 ]);

                foreach($Percurso_dados as $item){
                    // echo $item;

                     DB::table('percurso_mudanca_curso_student')
                    ->updateOrInsert(
                        [
                          'user_id' => $students,
                          'discipline_id' => $item->discipline_id
                        ],
                        [
                         'lective_year' => $item->lective_year,
                         'grade' => $item->grade,
                         'created_at' => $item->created_at,
                         'updated_at' => $item->updated_at,
                         'created_by' => $item->created_by,
                         'type'       => $item->type,
                         'tfc_trabalho' => $item->tfc_trabalho,
                         'tfc_defesa' =>   $item->tfc_defesa,
                         'iduser_staff_change'=> Auth::user()->id, 
                         'created_at_change'=> $currentData, 
                        ]
                    );


                }
           
                if(createAutomaticArticleRequest($students, $emolumento_equivalence->id_emolumento, null, null)){
                    Toastr::success(__('Pedido de mudança de curso foi efectuado com sucesso.'), __('toastr.success'));
                    return back();
                }
          }   else if(!$consulta->isEmpty()) {

            Toastr::warning(__('Atenção,a forLEARN detectou a existência de um pedido de mudança de curso para este estudante, verifica a lista de estudantes. Caso persistir a situação, contactar o Apoio a forLEARN'), __('toastr.warning'));
            return back();

        }

        
        Toastr::warning(__('Atenção, não foi possível requerer o pedido de mudança de curso, tente novamente. Caso persistir o erro, contactar o Apoio a forLEARN'), __('toastr.warning'));
        return back();


} 
catch (Exception | Throwable $e) {
    return $e;
    Toastr::error($e->getMessage(), __('toastr.error'));
    logError($e);
    return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
}


}

function delete($id){
    try{

        DB::beginTransaction();

        DB::table('tb_change_course_normal')
        ->where('id', $id)
        ->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => Carbon::now()
        ]);

        DB::table('tb_transference_studant')
        ->where('change_course', $id)
        ->update([
            'deleted_by' => auth()->user()->id,
            'deleted_at' => Carbon::now()
        ]);
        
        Toastr::success(__('Pedido de mudança de curso eliminado com sucesso!'), __('toastr.success'));
        DB::commit();
    }
    catch (Exception | Throwable $e) {
        
        DB::rollBack();
        logError($e);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
}

}