<?php

namespace App\Modules\Users\Controllers;

use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\LectiveYear;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use Auth;
use DB;
use Log;
use Exception;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use Toastr;
use App\Modules\Users\util\CandidatesUtil;
use App\Modules\Users\util\FaseCandidaturaUtil;
use App\Modules\Users\Controllers\UsersController;

class FaseCandidaturaController extends Controller
{
    private $candidateUtil;
    private $faseCandidateUtil;
    private $candidateController;
    
    function __construct() {
        $this->candidateUtil = new CandidatesUtil();
        $this->faseCandidateUtil = new FaseCandidaturaUtil();
        $this->candidateController = new CandidatesController();
    }


    public function generatePDF(Request $request){
      
        $userController = new UsersController();
        if(isset($request->lective_history_id)){
          $userController->user_fase = DB::table('lective_candidate_historico_fase as lchf')
            ->join('historic_classe_candidate as hcc','lchf.id','=','hcc.id_historic_user_candidate')
            ->join('lective_candidate as lc','lchf.id_fase','=','lc.id')
            ->join('classes as c','hcc.id_classe','=','c.id')
            ->join('courses_translations as ct','ct.courses_id','=','c.courses_id')
            ->join('room_translations as rt','c.room_id','=','rt.room_id')
            ->select('c.display_name as turma','ct.display_name as curso','lchf.user_id','rt.display_name  as sala')
            ->where('ct.active',1)
            ->where('rt.active',1)
            ->where('lchf.id',$request->lective_history_id)
            ->first();
            return $userController->generatePDF($request->user_id,$request);
        }
        return $userController->generatePDF($request->user_id,$request);
    }

    public function index()
    {   
        $this->candidateUtil->actualizarDatasCalendariosPassaram();
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $lectiveYearSelected = FaseCandidaturaController::calculateLective(Carbon::now());
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        $lectiveYearCandidatura = DB::table('lective_candidate')->where('id_years', $lectiveYearSelected)->first();
        return  view("Users::candidate.fase_candidatura.index", compact('lectiveYears', 'lectiveYearSelected', 'lectiveYearCandidatura'));
    }

    private function insertLectiveCandidate($lective_year,$data_start,$data_end, $fase, $lective_calendario = null){
        $auth = Auth::user()->id;
      
        DB::table('lective_candidate')->insert(['id_years' => $lective_year],
            [
             'data_inicio' => $data_start, 
             'data_fim' => $data_end,
             'lective_calendario' => $lective_calendario,
             'fase' => $fase , 
             'created_by' => $auth, 
             'updated_by' => $auth
            ]
        );
    }

    public function anolectivoFase($id)
    {   
        $only=true;
        $this->candidateUtil->actualizarDatasCalendariosPassaram();
        $lectiveYears = LectiveYear::with(['currentTranslation'])->find($id)->get();
        $lectiveYearSelected = $id;
        $lectiveYearCandidatura = DB::table('lective_candidate')->where('id_years', $lectiveYearSelected)->orderBy('fase','DESC')->first();
        if(!isset($lectiveYearCandidatura->id)){
            $lectiveYearCandidatura = (object)[ "fase" => 0, "data_fim" => $lectiveYears[0]->start_date, "first" => true  ];
        }
        return  view("Users::candidate.fase_candidatura.index", compact('lectiveYears', 'lectiveYearSelected', 'lectiveYearCandidatura','only'));
    }

    public function users($id)
    {
        $lectiveCandidate = DB::table('lective_candidate')->find($id);
        $lectiveYear = DB::table('lective_year_translations')
            ->where('lective_years_id', '=', $lectiveCandidate->id_years)
            ->where('active', 1)
            ->first();
        return  view("Users::candidate.fase_candidatura.users", compact('lectiveCandidate', 'lectiveYear'));
    }   

    public function store(Request $request)
    {
        try {
            $currentData = Carbon::now();
            $data_start = strtotime($request->data_start);
            $data_end = strtotime($request->data_end);
            
            if ($data_start > $data_end) {
                Toastr::warning(_('Conflito entre as datas: a data de inicio não pode ser maior que a data de termino, por favor verifica o intervalo entre as datas'), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }

            $calendarioProva = DB::table("lective_candidate_calendarie")
                ->whereRaw('"' . $currentData . '" between `data_inicio` and `data_fim`')
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->first();
                
            if (!isset($calendarioProva->id)){
                Toastr::warning(_('Não foi criado o calendário de candidatura este ano'), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }
            
            $calendario_start = strtotime($calendarioProva->data_inicio);
            $calendario_end = strtotime($calendarioProva->data_fim);
            $calendario_data = "[".$calendarioProva->data_inicio." à ".$calendarioProva->data_fim."]";
            
            if ($calendario_start > $data_start && $data_start <  $calendario_end) {
                Toastr::warning(_('A data de incio informada não pertence ao intervalo de datas do calendário de candidaturas '.$calendario_data), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }
            
            if ($calendario_start > $data_end && $data_end <  $calendario_end) {
                Toastr::warning(_('A data de termino informada não pertence ao intervalo de datas do calendário de candidaturas '.$calendario_data), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }   
            
            if(isset($request->first)){
                $this->insertLectiveCandidate($request->lective_year,$request->data_start,$request->data_end,1, $calendarioProva->id);
            }else{
                $this->insertLectiveCandidate($request->lective_year,$request->data_start,$request->data_end,$request->fase_num, $calendarioProva->id);
            }

            $this->candidateUtil->actualizarDatasCalendariosPassaram();

            Toastr::success(_('A criação da fase foi realizado com successo.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function update(Request $request)
    { 
        try {
            $currentData = Carbon::now();
            $data_start = strtotime($request->data_start);
            $data_end = strtotime($request->data_end);
            
            if ($data_start > $data_end) {
                Toastr::warning(_('Conflito entre as datas: a data de inicio não pode ser maior que a data de termino, por favor verifica o intervalo entre as datas'), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }

            $calendarioProva = DB::table("lective_candidate_calendarie")
                ->whereRaw('"' . $currentData . '" between `data_inicio` and `data_fim`')
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->first();
           
            if (!isset($calendarioProva->id)){
                Toastr::warning(_('Não foi criado o calendário de candidatura este ano'), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }
            
            $calendario_start = strtotime($calendarioProva->data_inicio);
            $calendario_end = strtotime($calendarioProva->data_fim);
            $calendario_data = "[".$calendarioProva->data_inicio." à ".$calendarioProva->data_fim."]";
            
            if ($calendario_start > $data_start && $data_start <  $calendario_end) {
                Toastr::warning(_('A data de incio informada não pertence ao intervalo de datas do calendário de candidaturas '.$calendario_data), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }
            
            if ($calendario_start > $data_end && $data_end <  $calendario_end) {
                Toastr::warning(_('A data de termino informada não pertence ao intervalo de datas do calendário de candidaturas '.$calendario_data), __('toastr.warning'));
                return redirect()->route('fase-candidatura');
            }            
            
            $auth = Auth::user()->id;
            DB::update('UPDATE lective_candidate SET id_years=?, data_inicio=?, data_fim=?, fase=?, updated_by=?  WHERE id = ?', [
                $request->lective_year,
                $request->data_start,
                $request->data_end,
                $request->fase_num,
                $auth,
                $request->chave
            ]);
            $this->candidateUtil->actualizarDatasCalendariosPassaram();
            Toastr::success(_('Actualização foi realizada com successo'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }


    public function ajax_list(Request $request)
    {
        try {
            $model = DB::table('lective_candidate as lc')
                ->join('lective_year_translations as lyt', 'lc.id_years', '=', 'lyt.lective_years_id')
                ->where('lyt.active', 1)
                ->whereNull('lyt.deleted_at')
                ->whereNull('lc.deleted_at')
                ->whereNull('lc.deleted_by')
                ->select('lc.id', 'lc.data_inicio', 'lc.data_fim', 'lc.fase', 'lyt.display_name','lc.id_years')
                ->orderBy('lc.id_years','DESC');
            if(isset($request->lective_year)){
                $model->where('lc.id_years',$request->lective_year);
            }
            $model = $model->get();
            return Datatables::of($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::candidate.fase_candidatura.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();

           
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    public function ajax_list_lective( $anoLEctive)
    {

        $fazes_candidate = DB::table('lective_candidate as lc')
            ->join('lective_years as ly', 'ly.id', '=', 'lc.id_years')
            ->where('lc.id_years', $anoLEctive)
            ->orderBy('lc.fase', 'ASC')
            ->select('lc.id','lc.data_inicio', 'lc.data_fim', 'lc.is_termina', 'lc.fase', 'ly.start_date', 'ly.end_date')
            ->get();

        return response()->json( $fazes_candidate);
    }






    private function preparName($value){
        $names = explode(" ",$value);
        $join = "";
        foreach($names as $name){
            $join .= $join == "" ? trim($name) : ",".trim($name);
        }
        return $join;       
    }



   public function ajax_list_users($id){
        try {

        $lectiveCandidate = DB::table('lective_candidate')->find($id);
        $lectiveYear = LectiveYear::where('id', $lectiveCandidate->id_years)->first();
        
        $cursos = $this->candidateUtil->cursoQueryGet($lectiveYear,$lectiveCandidate);
        $model = $this->candidateUtil->modelQueryGet($lectiveYear,$id);
        
        $faseNext = FaseCandidaturaUtil::faseActual();

        return $this->candidateUtil->addColumnCheckBox($model) 
            ->addColumn('actions', function ($item) use ($faseNext) {
                return view('Users::candidate.fase_candidatura.datatables.actions_users',compact('faseNext'))->with('item', $item);
            })
            ->addColumn('states', function ($state) use ($cursos) {
                return view('Users::candidate.datatables.states', compact('cursos', 'state'));
            })
            ->addColumn('cursos', function ($cadidate) use ($cursos, $lectiveCandidate) {
                return view('Users::candidate.fase_candidatura.datatables.courses_states', compact('cursos', 'cadidate', 'lectiveCandidate'));
            })
            ->rawColumns(['actions', 'states', 'cursos','foto','diploma','bi_doc'])
            ->addIndexColumn()  
            ->toJson(); 

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    
    
    //novo codigo sedrac
    private function userCourses($users,$id_fase){
        $model = []; 
        $users_list_id = [];
        foreach($users as $user){
            $join = "";
            $objs = $this->queryCourse($user->user_id,$id_fase);
            foreach($objs as $obj)
                $join .= $join == "" ? $obj->curso : ','.$obj->curso;
            $user->cursos = $join;
            if(!in_array($user->user_id, $users_list_id)){
                array_push($model, $user);
                array_push($users_list_id,$user->user_id);
            }
        }
        return $model;
    }

    private function queryCourse($user_id,$fase_id){
       return DB::table('lective_candidate_historico_fase as lchf')
                        ->join('courses_translations as ct','ct.courses_id','=','lchf.id_curso')
                        ->where('user_id',$user_id)
                        ->where('id_fase',$fase_id)
                        ->where('ct.active',1)
                        ->whereNull('ct.deleted_at')
                        ->select('ct.display_name as curso','lchf.id_curso as curso_id','lchf.id_fase')
                        ->get();
    }
    
    

    public function ajax_history_users($id)
    {
        try {
            $users = DB::table('lective_candidate_historico_fase as lchf')
                ->join('lective_candidate as lc','lc.id','=','lchf.id_fase')
                ->join('lective_year_translations as lyt','lyt.lective_years_id','=','lc.id_years')
                ->join('courses_translations as ct','ct.courses_id','=','lchf.id_curso')
                ->leftjoin('classes as c','c.id','=','lchf.id_turma')
                ->where('lyt.active',1)
                ->where('ct.active',1)
                ->where('lchf.user_id', $id)
                ->select('ct.display_name as curso', 'c.code','lc.fase','lyt.display_name as ano','lchf.user_id','lchf.id')
                ->get();
            return response()->json($users);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }    







    public function ajax_candidate_year(Request $request)
    {

        $obj = DB::table('lective_candidate as lc')
            ->join('lective_years as ly', 'ly.id', '=', 'lc.id_years')
            ->where('lc.id_years', $request->year)
            ->orderBy('lc.fase', 'DESC')
            ->select('lc.data_inicio', 'lc.data_fim', 'lc.is_termina', 'lc.fase', 'ly.start_date', 'ly.end_date')
            ->first();

        if (isset($obj->fase))  return response()->json((object)["status" => 1, "body" => $obj]);

        $obj = DB::table('lective_years')
            ->where('id', $request->year)
            ->select('id', 'start_date', 'end_date')
            ->first();

        if (isset($obj->id))  return response()->json((object)["status" => 2, "body" => $obj]);

        return response()->json((object)["status" => 3]);
    }



    private static function calculateLective($data){
        return DB::table('lective_years')->whereRaw('"' . $data. '" between `start_date` and `end_date`')->first();
    }


    public function getCourse(){
        $courses = Course::with([
            'currentTranslation'
        ])->get();
        return response()->json($courses);
    }


    public function getCourseTurma($id){
        $turmas = DB::table('classes')->where('courses_id',$id)->where('year',1)->get();

        return response()->json($turmas);
    }

    public function getStudentsByFase($fase_id){
         try {

            //Pegar todos que estão nesta fase
             $CandidatosFases=DB::table('lective_candidate_historico_fase')
            ->where('id_fase',$fase_id)
            ->get();
            //Pegar  fase
             $FasesCandidate=DB::table('lective_candidate')
            ->where('id',$fase_id)
            ->first();

            $ids_user_fase=collect($CandidatosFases)->map(function($item){return $item->user_id;});
            $ids_curso_fase=collect($CandidatosFases)->map(function($item){return $item->id_curso;});


            $cursos = DB::table('articles as art')
            ->leftjoin('article_requests as ar', 'art.id', '=', 'ar.article_id')
            ->join('disciplines as disciplina', 'disciplina.id', '=', 'ar.discipline_id')
            ->join('courses as curso', 'disciplina.courses_id', '=', 'curso.id')
            ->join('courses_translations as ct', 'ct.courses_id', '=', 'curso.id')
            ->join('users as usuario', 'ar.user_id', '=', 'usuario.id')
            ->where("art.id_code_dev",6)
            ->where("ct.active",1)
            ->where('usuario.name',"!=","") 
            ->whereNull('ar.deleted_at') 
            ->whereNull('disciplina.deleted_at') 
            ->whereNull('ct.deleted_at') 
            ->whereNull('art.deleted_at') 
            ->whereNull('curso.deleted_at') 
            ->whereNull('usuario.deleted_at') 
            ->where('ar.discipline_id',"!=",null) 
            ->select([
                "art.id as articles",
                "ar.id as articles_req",
                "ar.discipline_id as discipline",
                "curso.id as course",
                "ct.display_name as nome_curso",
                "ar.user_id as usuario_id",
                "ar.status as state",
                "usuario.name as usuario"
                ])
            ->orderBy('ar.id','desc')
            ->whereIn('ar.user_id',$ids_user_fase)
            ->whereIn('curso.id',$ids_curso_fase)
            ->get(); 
            
            

         $model = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('id', [15,6]);
            })
            // ->with(['courses' => function($q) {
            //     $q->with([
            //         'currentTranslation'
            //     ]);
            // }])

            ->join('users as u1', 'u1.id', '=', 'users.created_by')

            // base da alteração pesquisar courso.
            ->join('user_candidate as uca','uca.user_id','=','users.id')
             ->leftJoin('lective_candidate as lc',function($join){
                 $join->on('lc.id','=','uca.year_fase_id')
                 ->limit(1);
             })

            ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
            ->leftJoin('courses_translations as ct', function ($join) {
                $join->on('ct.courses_id', '=', 'uc.courses_id');
                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('ct.active', '=', DB::raw(true));
            })

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
            ->leftJoin('lective_years', function ($join) {
                $join->whereRaw('users.created_at between `start_date` and `end_date`');
            })
            ->join('lective_year_translations as lyt', function ($join) {
                $join->on('lyt.lective_years_id', '=', 'lective_years.id');
                $join->on('lyt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('lyt.active', '=', DB::raw(true));
            })
            // ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u1.id')
            ->select([
                'users.*',
                'full_name.value as name_name',
                'u1.name as us_created_by',
                'u2.name as us_updated_by',
                // SELECT QUE PEGA TODOS OS CURSO E A SUA TRADUCAO
                'ct.display_name as nome_course',
                'ct.courses_id as id_curso',
                'article_requests.status as state',
                'article_requests.id as art',
                'candidate.value as cand_number',
                'lyt.display_name as lective_year_code',
               
            ])
           
            ->whereNull('article_requests.deleted_at')
            ->whereNotNull('candidate.value')
            ->whereIn('users.id',$ids_user_fase)
            ->groupBy('full_name.users_id')
            ->distinct('full_name.value');
            // ->get();  
            
         
         

            return Datatables::of($model)
                ->addColumn('fase', function ($item) use($FasesCandidate){
                    return $FasesCandidate->fase;
                })
              
                ->addColumn('actions', function ($item) {
                    return view('Users::candidate.datatables.actions')->with('item', $item);
                })
              
                ->addColumn('states', function ($state) use ($cursos) {
                    return view('Users::candidate.datatables.states', compact('cursos', 'state'));
                })
                ->addColumn('cursos', function ($cadidate) use ($cursos) {
                    return view('Users::candidate.datatables.courses_states', compact('cursos', 'cadidate'));
                })
                ->rawColumns(['actions', 'states', 'cursos'])
                ->addIndexColumn()  
                ->toJson();
        } catch (Exception | Throwable $e) {
            // Log::error($e);
            return response()->json($e->getMessage(), 500);
        }


    }


}
