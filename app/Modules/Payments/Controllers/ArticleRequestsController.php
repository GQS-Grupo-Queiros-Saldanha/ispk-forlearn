<?php

namespace App\Modules\Payments\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Bank;
use App\Modules\Payments\Models\Payment;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\TransactionInfo;
use App\Modules\Payments\Requests\ArticleRequestRequest;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserState;
use App\Modules\Users\Models\UserStateHistoric;
use Brian2694\Toastr\Facades\Toastr;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Users\Models\Matriculation;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Payments\Util\ArticlesUtil;
use Auth;


class ArticleRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    private $articlesUtil;

    function __construct(){
        $this->articlesUtil = new ArticlesUtil();
    }

    public function index(){
        try { 
            $auth_student=null;
            if(auth()->user()->hasRole('candidado-a-estudante') || !auth()->user()->can('manage-requests-others')) {
                 $auth_student=auth()->user()->id;
            } else {}
            
            if(auth()->user()->hasRole('chefe_tesoureiro') || auth()->user()->hasRole('tesoureiro')){
                $auth_student=null;
            }

              $usuarios_cargos = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
                ->leftJoin('user_parameters as user_namePar',function($join){
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                    ->where('user_namePar.parameters_id',1);
                }) 
                ->leftJoin('user_parameters as numb_mecanografico',function($join){
                    $join->on('numb_mecanografico.users_id', '=', 'usuario.id')
                    ->where('numb_mecanografico.parameters_id',19);
                }) 
                ->leftJoin('user_candidate as uca',function($join){
                    $join->on('uca.user_id', '=', 'usuario.id');
                })
            ->whereNotIn('usuario.id',users_exemplo(false))
            ->where('cargo_traducao.active',1)
            ->where('cargo_traducao.language_id',1)
            ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
            ->whereIn("cargo_traducao.role_id",[6,15])
            ->select([
                'usuario.name as nome',
                'user_namePar.value as nome_usuario',
                'numb_mecanografico.value as numb_mecanografico',
                'usuario.email as email',
                'usuario.id as id',
                'cargo_traducao.role_id',
                'uca.code as ce',

                ])
                ->when($auth_student!=null, function($query)use($auth_student){
                    $query->where('usuario.id',$auth_student);
                })
            ->orderBy('usuario.name','ASC')
            ->get()
            ->map(function ($student)
            {
                $name_student = $student->nome_usuario== ""  ? $student->nome  : $student->nome_usuario;
                
                
                $n_mecanografico = "0000";
                
                if(isset($student->ce)){
                    $n_mecanografico = $student->ce;
                } 
                
                if(isset($student->numb_mecanografico)){
                    $n_mecanografico = $student->numb_mecanografico;
                } 
                
                $student->{'display_name'} = $name_student . " #". $n_mecanografico . " " ."(".$student->email .")";
                strtr(
                    utf8_decode( $student->display_name),
                    utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
                return $student;
            }); 
             
            $users = auth()->user()->can('manage-requests-others') ? $usuarios_cargos : $usuarios_cargos;
            
            $data = compact('users');
            return view("Payments::requests.index")->with($data);

        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function updateCredit(\Illuminate\Http\Request $request)
    {   
        //    return $request;
       $affected = DB::table('users')
              ->where('id', '=',$request->id_aluno)
              ->update(['credit_balance' =>$request->valor ]);
              return redirect()->back();
       
    }
    
    public function ajax($userId)
    {
        $userId = auth()->user()->can('manage-requests-others') ? $userId : auth()->user()->id;

        try {
            $model = ArticleRequest::whereUserId($userId)
                ->with(['article' => function ($q) {
                    $q->with([
                        'extra_fees',
                        'currentTranslation'
                    ]);
                }])
                ->join('users as u0', 'u0.id', '=', 'article_requests.user_id')
                ->join('users as u1', 'u1.id', '=', 'article_requests.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'article_requests.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'article_requests.deleted_by')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'article_requests.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })

                //relacao entre a disciplines_translations e o articles_requests.meta
                //para retornar o nome da disciplina.
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'article_requests.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })

                ->select([
                    'article_requests.*',
                    'u0.name as user',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name as discipline_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('status', function ($item) {
                    return $this->requestStatus($item);
                })
                ->addColumn('actions', function ($item) {
                    return view('Payments::requests.datatables.actions')->with('item', $item);
                })
                ->addColumn('article', function ($item) {
                    $columnValue = $item->article->currentTranslation->display_name;
                    if ($item->month) {
                        $month = getLocalizedMonths()[$item->month - 1]["display_name"];
                        $columnValue .= " ($month $item->year)";
                    }
                    return $columnValue;
                })
                ->filterColumn('article', function ($query, $keyword) {
                    // TODO: how to filter by month name?
                    $query
                        ->where('at.display_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('year', 'LIKE', '%' . $keyword . '%');
                })
                ->orderColumn('article', function ($query, $order) {
                    $query
                        ->orderBy('at.display_name', $order)
                        ->orderBy('year', $order)
                        ->orderBy('month', $order);
                })
              /*  ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at ? TimeHelper::time_elapsed_string($item->updated_at) : null;
                })
                ->editColumn('deleted_at', function ($item) {
                    return $item->deleted_at ? TimeHelper::time_elapsed_string($item->deleted_at) : null;
                })*/
                ->rawColumns(['actions'])
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxArticliesPerUser($id)
    {
        try {
            $dados=explode(',',$id);
            $qdt_dados=count($dados);
            if ($qdt_dados>1) {

                 $lectiveYearSelected = LectiveYear::whereId($dados[0])->first();
                //enviar apenas emolumentos do ano lectivo em vigor
          
                     
                $user = User::findOrFail($dados[1])->load('courses');
                $userCourses = $user->courses->pluck('id');

                // copiar somente este codígo
                 $articles = Article::with([
                    'currentTranslation',
                    'extra_fees',
                    'monthly_charges'
                ])
                ->whereNull('articles.deleted_by')
                ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);
                
                if ($user->hasRole('candidado-a-estudante')) {

                   return $articles = $articles->whereIn('id', [6])
                   ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                   ->get();
                } else {
                return $articles = $articles->doesntHave('monthly_charges')
                    ->orWhereHas('monthly_charges', function ($q) use ($userCourses) {
                        $q->whereIn('course_id', $userCourses);
                    })
                    ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->get();
                }

                $articles = $articles->get();
                $articles->each(function ($item) {
                    $item->{'extraFeesAsText'} = $item->extraFeesAsText();
                });

               
                // return $articles->sortBy('currentTranslation.display_name')->values();

            }else{
                $currentData = Carbon::now();
                $lectiveYearSelected = DB::table('lective_years')
                    ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                    ->first();

                $user = User::findOrFail($id)->load('courses');
                $userCourses = $user->courses->pluck('id');

                 $articles = Article::with([
                'currentTranslation',
                'extra_fees',
                'monthly_charges'
                ])->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date]);

                if ($user->hasRole('candidado-a-estudante')) {
                $articles = $articles->whereIn('id', [6]);
                } else {
                $articles = $articles->doesntHave('monthly_charges')
                    ->orWhereHas('monthly_charges', function ($q) use ($userCourses) {
                        $q->whereIn('course_id', $userCourses);
                    });
                }

                $articles = $articles->get();

                $articles->each(function ($item) {
                     $item->{'extraFeesAsText'} = $item->extraFeesAsText();
                });

                return $articles->sortBy('currentTranslation.display_name')->values();

            }
            
            
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxDisciplinesPerUser($id)
    {
      $array=explode(",",$id);
      $tipo_inscr=null;
        try {
            $id_codeDev = $array[1];
            $user_id=$array[2];
            $article=['in_exa','in_fre','exame','exame_recurso'];
            
            $lectiveYearSelected = DB::table('lective_years')
            ->where('id', $array[3])
            ->first();

            $getCode_dev=DB::table('code_developer as code_dev')
            ->where('code_dev.id','=',$id_codeDev)
            ->select([
                'code_dev.code as code'
            ])
            ->first();
            $setCode_dev=$getCode_dev->code;
            if (in_array($getCode_dev->code,$article)) {
              
            if ($getCode_dev->code == "in_exa" ) {
                $filter =1;
            } elseif($getCode_dev->code == "in_fre") {
                $filter =0;
            }else{
                $filter =2;
            }
            


                $students_disciplina =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                    ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                    ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                    ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                    // ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                    // ->join('courses_translations as ct', function ($join) {
                    //     $join->on('ct.courses_id', '=', 'uc.courses_id');
                    //     $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    //     $join->on('ct.active', '=', DB::raw(true));
                    // })
                    ->join('matriculation_disciplines as mat_disciplina',function($join)
                    {  
                        $join->on('mat_disciplina.matriculation_id','=','matriculations.id');
                        // $join->whereIn('mat_disciplina.matriculation_id');
                    })
                    ->join('disciplines as dc', 'dc.id', '=', 'mat_disciplina.discipline_id')
                    ->leftJoin('disciplines_translations as dt', function ($join) {
                        $join->on('dt.discipline_id', '=', 'dc.id');
                        $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dt.active', '=', DB::raw(true));
                    })
                
                    ->select([
                        'dt.display_name as nome_disciplina',
                        'mat_disciplina.exam_only as exam_only',
                        'dc.code as disciplines_code',
                        'u0.id as id',
                        'dc.id  as id_disciplina'
                    ])
                    ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->distinct('mat_disciplina.discipline_id')
                    ->where('matriculations.user_id', $user_id)
                    ->where('matriculations.deleted_at',null)
                    ->when($filter!= 2, function ($q) use ($filter) {
                        return $q->where('mat_disciplina.exam_only',$filter);
                    })
                    
                ->get();
                return response()->json(['data'=>$students_disciplina,'setCode_dev'=>$setCode_dev]);
            }else{
                return response()->json(['data'=>"N_dis",'setCode_dev'=>"N_dis"]);

            }
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        try {
            $articles = Article::with([
                'currentTranslation', 'extra_fees', 'monthly_charges'
            ])->get();

            $articles->each(function ($item) {
                $item->{'extraFeesAsText'} = $item->extraFeesAsText();
            });

            $data = [
                'action' => 'create',
                'users' => auth()->user()->can('manage-requests-others') ? studentsSelectList() : null,
                'articles' => $articles,
                'years' => getYearList(),
                'months' => getLocalizedMonthsPropinas(),
                'languages' => Language::whereActive(true)->get()
            ];

            return view('Payments::requests.request-create')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function createUserArticle($id)
    {
        //TODO: avaliar para quando o select estiver vazio
        try {
            
            $dados=explode(',',$id);
            $qdt_dados=count($dados);
            if ($qdt_dados>1) {
                
                $lectiveYears = LectiveYear::with(['currentTranslation'])
             ->get();
     
                 $currentData = Carbon::now();
                $lective = DB::table('lective_years')
                 ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                 ->first();
                 $lective = $lective->id ?? 6;

                $lista_Month=[];
                $ordem_Month=[];
                $desor_Month=[];
                $getLocalizedMonths=getLocalizedMonths();
                foreach ($getLocalizedMonths as $key => $value) {
                    if ($value['id']>7 && $value['id']<10) {
                    }else{
                        $lista_Month[]=$value;
                    }
                }
                foreach ($lista_Month as $index => $item) {
                    if ($item['id']>9) {
                        $ordem_Month[]=$item;
                    } else {
                        $desor_Month[]=$item;
                    }
                }
                foreach ($desor_Month as $indexInArray => $element) {
                    $ordem_Month[]=$element;
                }
                





                
                $lectiveYearSelected = LectiveYear::whereId($dados[0])->first();

                 $articles = Article::with([
                    'currentTranslation', 'extra_fees', 'monthly_charges'
                  ]) 
                  ->whereBetween('articles.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                  ->whereNull('articles.deleted_by')
                ->get();


                
                $articles->each(function ($item) {
                    $item->{'extraFeesAsText'} = $item->extraFeesAsText();
                });
                
                    $students_disciplina =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
                        ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
                        ->leftJoin('users as u2', 'u2.id', '=', 'matriculations.updated_by')
                        ->leftJoin('users as u3', 'u3.id', '=', 'matriculations.deleted_by')
                        ->join('matriculation_disciplines as mat_disciplina',function($join)
                        {  
                            $join->on('mat_disciplina.matriculation_id','=','matriculations.id');
                            // $join->whereIn('mat_disciplina.matriculation_id');
                        })
                        ->join('disciplines as dc', 'dc.id', '=', 'mat_disciplina.discipline_id')
                        ->leftJoin('disciplines_translations as dt', function ($join) {
                            $join->on('dt.discipline_id', '=', 'dc.id');
                            $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dt.active', '=', DB::raw(true));
                        })
                    
                        ->select([
                            'dt.display_name as nome_disciplina',
                            'mat_disciplina.exam_only as exam_only',
                            'dc.code as disciplines_code',
                            'u0.id as id',
                            'dc.id  as id_disciplina'
                        ])
                        ->whereBetween('matriculations.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->distinct('mat_disciplina.discipline_id')
                        ->where('matriculations.user_id', $dados[1])
                        ->where('matriculations.deleted_at',null)
                    ->get();

                
                $data = [
                    'userSelected' => $dados[1],
                    'action' => 'create',
                    'users' => auth()->user()->can('manage-requests-others') ? studentsSelectList() : null,
                    'articles' => $articles,
                    'lective'=>$lective,
                    'ordem_Month'=>$ordem_Month,
                    'lectiveYears'=>$lectiveYears,
                    'years' => getYearList(),
                    'students_disciplina'=>$students_disciplina,
                    'months' => getLocalizedMonths(),
                    'languages' => Language::whereActive(true)->get(),
                    'seletor'=>$dados[0]
                ];
                return view('Payments::requests.request-create')->with($data);
            }else{
               
                 $articles = Article::with([
                    'currentTranslation', 'extra_fees', 'monthly_charges'
                ])->get();
                
                $articles->each(function ($item) {
                    $item->{'extraFeesAsText'} = $item->extraFeesAsText();
                });
                // return count($articles);
                $data = [
                    'userSelected' => $id,
                    'action' => 'create',
                    'users' => auth()->user()->can('manage-requests-others') ? studentsSelectList() : null,
                    'articles' => $articles,
                    'years' => getYearList(),
                    'months' => getLocalizedMonthsPropinas(),
                    'languages' => Language::whereActive(true)->get(),
                    'seletor'=>0
                ];
                return view('Payments::requests.request-create')->with($data);
            }
           
            

           
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ArticleRequestRequest $request
     * @return void
     */


    public function store(\Illuminate\Http\Request $request)
    {
        try {
            
            // return $request; 
             
            $array=explode(",",$request->article);
            // return $request;
            if (isset($request->listmonth)) {
                $lective_year=explode("/",$request->lective_year);
                $data1="20".$lective_year[0];
                $data2="20".$lective_year[1];
                $array_disciplina=explode(",",$request->discipline);
                $year=null;

                if (count($request->listmonth)>1) {
                    DB::beginTransaction();
                    
                    foreach ($request->listmonth as $key => $value) {
                        // return $value;
                        $article = Article::findOrFail($array[0]);
                        $userId = $request->user()->can('create-requests-others') && $request->get('user') ?
                        $request->get('user') : $request->user()->id;

                        // este consulta é temporia serve para consultamos se o mes de marco se encontra disponivel para o usuraio.
                        if ($data1=='2020') {
                            $getMonth=DB::table('article_requests')
                            ->where('article_requests.user_id','=',$userId)
                            ->whereNull('article_requests.deleted_at')
                            ->where('article_requests.month','=',3)
                            ->where('article_requests.year','=',2020)
                            ->get();
                            if ($getMonth->isEmpty() && !isset($request->discipline)) {
                                $articleRequestMonth = new ArticleRequest([
                                    'user_id' => $userId,
                                    'article_id' => $article->id,
                                    'year' => 2020 ,
                                    'month' => 3 ,
                                    'base_value' => $article->base_value,
                                    'meta' => isset($request->discipline)?$array_disciplina[0]: " ",
                                    'discipline_id'=>isset($request->discipline)?$array_disciplina[0]:""
                                ]);
                                $articleRequestMonth->save();
                                $transaction = Transaction::create([
                                    'type' => 'debit',
                                    'value' => $articleRequestMonth->base_value,
                                    'notes' => 'Débito inicial do valor base'
                                ]);
        
                                $transaction->article_request() ->attach($articleRequestMonth->id, ['value' => $articleRequestMonth->base_value]);
                            }
                        }
                        
                        
                        if ($value>=10 && $value<=12) {
                            $year=$data1;
                        }else{
                            $year=$data2;
                        }
                        //   $retVal = isset(discipline) ? a : b ;
                        // Create
                        $articleRequest = new ArticleRequest([
                            'user_id' => $userId,
                            'article_id' => $article->id,
                            'year' => $year ?: null,
                            'month' => $value ?: null,
                            'base_value' => $article->base_value,
                            'meta' => isset($request->discipline)?$array_disciplina[0]: " ",
                            'discipline_id'=>isset($request->discipline)?$array_disciplina[0]:""
                        ]);

                        $articleRequest->save();

                        //$this->changeState($article->id, $userId);

                        //avaliar se o article_id do Article Request foi 41 ou 42, para depois inserir na tabela disciplines_articles
                        if (isset($request->discipline)) {
                            $periodo=substr($array_disciplina[1],-3, 1);
                            $semetre=null;
                            if($periodo=="1"){$semetre=1;}
                            if($periodo=="2"){$semetre=2;}
                            if($periodo=="A"){$semetre=3;}
                            else{$semetre=0;}
        
        
                            if ($semetre== 1 || $semetre== 2 || $semetre== 3 ) {
                                //Adicionar novo registo na tabela disciplines_articles
                                $disciplineArticle = new DisciplineArticle([
                                    'user_id' => $userId,
                                    'article_request_id' => $articleRequest->id,
                                    'discipline_id' => $array_disciplina[0]
                                ]);
        
                                $disciplineArticle->save();
                            }
        
                        }
            



                        // create debit with article base value
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base'
                        ]);

                        $transaction->article_request()
                            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);

                        DB::commit();
                }
            
            } else {
                $lective_year=explode("/",$request->lective_year);
                $data1="20".$lective_year[0];
                $data2="20".$lective_year[1];
                $array_disciplina=explode(",",$request->discipline);
                $year=null;
                $getConsultMonth=[];
                DB::beginTransaction();
                
                $article = Article::findOrFail($array[0]);
    
                $userId = $request->user()->can('create-requests-others') && $request->get('user') ?
                    $request->get('user') : $request->user()->id;
                   
                    if ($data1=='2020') {
                        $getMonth=DB::table('article_requests')
                        ->where('article_requests.user_id','=',$userId)
                        ->whereNull('article_requests.deleted_at')
                        ->where('article_requests.month','=',3)
                        ->where('article_requests.year','=',2020)
                        ->get();
                        if ($getMonth->isEmpty() && !isset($request->discipline)) {
                            // return "123";
                            $articleRequestMonth = new ArticleRequest([
                                'user_id' => $userId,
                                'article_id' => $article->id,
                                'year' => 2020,
                                'month' => 3,
                                'base_value' => $article->base_value,
                                'meta' => isset($request->discipline)?$array_disciplina[0]: " ",
                                'discipline_id'=>isset($request->discipline)?$array_disciplina[0]:""
                            ]);
                            $articleRequestMonth->save();
                            // create debit with article base value
                            $transaction = Transaction::create([
                                'type' => 'debit',
                                'value' => $articleRequestMonth->base_value,
                                'notes' => 'Débito inicial do valor base'
                            ]);
    
                            $transaction->article_request() ->attach($articleRequestMonth->id, ['value' => $articleRequestMonth->base_value]);
                        }
                    }
                    // return "12300";


                    if ($request->listmonth[0]>=10 && $request->listmonth[0]<=12) {
                        $year=$data1;
                    }else{
                        $year=$data2;
                    }
    
                    // sconsukta somente nestes anos
                    if ($data1=="2020" && $data2=="2021" && $request->listmonth[0]=="3" ) {
                        $getConsultMonth=DB::table('article_requests')
                        ->where('article_requests.user_id','=',$userId)
                        ->whereNull('article_requests.deleted_at')
                        ->where('article_requests.month','=',3)
                        ->where('article_requests.year','=',2021)
                        ->whereNull('article_requests.deleted_by')
                        ->get();
                    }
                
                    

                    $articleRequest = new ArticleRequest([
                        'user_id' => $userId,
                        'article_id' => $article->id,
                        'year' => $year ?: null,
                        'month' => $request->listmonth[0] ?: null,
                        'base_value' => $article->base_value,
                        'meta' => $array_disciplina[0] ?: " " ,
                        'discipline_id'=>isset($request->discipline)?$array_disciplina[0]:""
                    ]);
                    if (count($getConsultMonth)==0 && $request->listmonth[0]=="3") {
                        $articleRequest->save();
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base'
                        ]);
                        $transaction->article_request()->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    }elseif($data1!="2020" && $request->listmonth[0]!="3"){
                        $articleRequest->save();
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base'
                        ]);
                        $transaction->article_request()->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    } 
                    else if($request->listmonth[0]!="3"){
                        $articleRequest->save();
                        $transaction = Transaction::create([
                            'type' => 'debit',
                            'value' => $articleRequest->base_value,
                            'notes' => 'Débito inicial do valor base'
                        ]);
                        $transaction->article_request()->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
                    }
               
                   
                
    
                //$this->changeState($article->id, $userId);
                //avaliar se o article_id do Article Request foi 41 ou 42, para depois inserir na tabela disciplines_articles
                
                if (isset($request->discipline)) {
                    $periodo=substr($array_disciplina[1],-3, 1);
                    $semetre=null;
                    if($periodo=="1"){$semetre=1;}
                    if($periodo=="2"){$semetre=2;}
                    if($periodo=="A"){$semetre=3;}
                    else{$semetre=0;}


                    if ($semetre== 1 || $semetre== 2 || $semetre== 3 ) {
                        //Adicionar novo registo na tabela disciplines_articles
                        $disciplineArticle = new DisciplineArticle([
                            'user_id' => $userId,
                            'article_request_id' => $articleRequest->id,
                            'discipline_id' => $array_disciplina[0]
                        ]);

                        $disciplineArticle->save();
                    }

                }
    
    
                // create debit with article base value
              
    
                
    
                DB::commit();
            }
        }else{
            // return $request;
            DB::beginTransaction();
            $lective_year=explode("/",$request->lective_year);
            $data1="20".$lective_year[0];
            $data2="20".$lective_year[1];
            $array_disciplina=explode(",",$request->discipline);
            $year=null;
                $article = Article::findOrFail($request->article);
    
                $userId = $request->user()->can('create-requests-others') && $request->get('user') ?
                    $request->get('user') : $request->user()->id;
    
    
                // Create
                $articleRequest = new ArticleRequest([
                    'user_id' => $userId,
                    'article_id' => $article->id,
                    'year' => $request->year ?: null,
                    'month' => null,
                    'base_value' => $article->base_value,
                    'meta' => $request->discipline ?: " ",
                    'discipline_id' => $array_disciplina[0] ?$array_disciplina[0] :""

                ]);
    
                $articleRequest->save();
    
                //$this->changeState($article->id, $userId);
    
                //avaliar se o article_id do Article Request foi 41 ou 42, para depois inserir na tabela disciplines_articles
    
                if (isset($request->discipline)) {
                    $periodo=substr($array_disciplina[1],-3, 1);
                    $semetre=null;
                    if($periodo=="1"){$semetre=1;}
                    if($periodo=="2"){$semetre=2;}
                    if($periodo=="A"){$semetre=3;}
                    else{$semetre=0;}


                    if ($semetre== 1 || $semetre== 2 || $semetre== 3 ) {
                        //Adicionar novo registo na tabela disciplines_articles
                        $disciplineArticle = new DisciplineArticle([
                            'user_id' => $userId,
                            'article_request_id' => $articleRequest->id,
                            'discipline_id' => $array_disciplina[0]
                        ]);

                        $disciplineArticle->save();
                    }

                }
    
    
    
                // create debit with article base value
                $transaction = Transaction::create([
                    'type' => 'debit',
                    'value' => $articleRequest->base_value,
                    'notes' => 'Débito inicial do valor base'
                ]);
    
                $transaction->article_request()
                    ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
    
                DB::commit();

        }
            
            

            // Success message
            Toastr::success(__('Payments::requests.store_success_message'), __('toastr.success'));
            return redirect()->route('requests.index');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function paymentManualUpdate(\Illuminate\Http\Request $request, $id)
    {
        try {
            /** @var Payment $payment */
            $payment = Payment::findOrFail($id);
            $checkPermission = auth()->user()->can('manage-manual-payments');
            $newValue = $payment->total_paid + (double)$request->get('manual_value');
            $checkOverPaid = $newValue <= $payment->total_value;

            if ($checkPermission) {
                if ($payment->free_text !== $request->get('free_text')) {
                    $payment->free_text = $request->get('free_text');
                }

                if ($checkOverPaid) {
                    $payment->total_paid = $newValue;

                    if ($payment->total_paid >= $payment->total_value) {
                        $payment->fulfilled_at = Carbon::now();
                    }
                } else {
                    Toastr::error(__('Payments::payments.update_error_message'), __('toastr.error'));
                    return redirect()->back();
                }

                $payment->save();

                Toastr::success(__('Payments::payments.update_success_message'), __('toastr.success'));
            } else {
                Toastr::error(__('Payments::payments.update_error_message'), __('toastr.error'));
            }

            return redirect()->back();
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    protected function getCalculatePaidValue(ArticleRequest $articleRequest)
    {
        $valueOffsetToZero = $articleRequest->base_value + $articleRequest->extra_fees_value;

        $calculatedValue = 0;

        if ($articleRequest) {
            if (!$articleRequest->transactions) {
                $articleRequest->load('transactions');
            }

            foreach ($articleRequest->transactions as $transaction) {
                $operation = $transaction->type === 'debit' ? -1 : 1;
                $calculatedValue += $operation * $transaction->pivot->value;
            }

            return $calculatedValue ; 
            // += $valueOffsetToZero;
        }

        return $calculatedValue;
    }
    protected function requestStatus(ArticleRequest $articleRequest)
    {
        return $paidValue = (float)$this->getCalculatePaidValue($articleRequest);

        if ($paidValue === 0.0) {
            $status = 'pending';
            $type = 'info';
        } elseif ($paidValue === ($articleRequest->base_value + $articleRequest->extra_fees_value)) {
            $status = 'total';
            $type = 'success';
        } elseif ($paidValue > 0.0 && $paidValue < ($articleRequest->base_value + $articleRequest->extra_fees_value)) {
            $status = 'partial';
            $type = 'warning';
        } else {
            $status = 'error';
            $type = 'danger';
        }

        if ($status !== $articleRequest->status) {
            $articleRequest->status = $status;
            $articleRequest->save();
        }

        // $text = __("Payments::payments.status.$status");

        // return "<span class='badge badge-$type text-uppercase'>$text</span>";
    }

    public function fetch($id, $action)
    {
        try {
            $articleRequest = ArticleRequest::whereId($id)
                ->with([
                    'article' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'extra_fees'
                        ]);
                    },
                    'transactions' => function ($q) {
                        $q->with(['transaction_info' => function ($q) {
                            $q->with('bank');
                        }]);
                    },
                    'user'
                ])
                ->firstOrFail();

                $userInfo = User::whereId($articleRequest->user->id)
                            ->with(['parameters' => function($q) {
                               return $q->where('parameters.id', 1);
                            }])
                            ->first();

            $banks = Bank::get();
           

            // return $status = $this->requestStatus($articleRequest);
            
            $creditTypes = collect([
                ['id' => 'payment', 'display_name' => __('Payments::requests.transactions.credit-payment')],
                ['id' => 'adjust', 'display_name' => __('Payments::requests.transactions.credit-adjust')],
            ]);



            //carregar disciplinas de um determinado estudante
            //Disciplina em que ele esta matriculado
            //disciplinas do ano curricular e as em atraso.
            $userDisciplines = User::with([
                    'matriculation' => function ($q) {
                        $q->with([
                            'disciplines' => function ($q) {
                                $q->with([
                                    'currentTranslation'
                                ]);
                            }
                        ]);
                    }
                ])->findOrFail($articleRequest->user->id);

            $data = [
                'action' => $action,
                'request' => $articleRequest,
                'status_list' => requestStatusList(),
                'banks' => $banks,
                // 'payment_status' => $status,
                'credit_types' => $creditTypes,
                'article_extra_fees' => $articleRequest->article && $articleRequest->article->extraFeesAsText() ?
                    $articleRequest->article->extraFeesAsText() : null,
                'userDisciplines' => $userDisciplines,
                'userInfo' => $userInfo
            ];

            return view('Payments::requests.request')->with($data);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::requests.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id){
        try {
            return $this->fetch($id, 'show');
        } catch (Exception | Throwable $e) {
            // return $e;
            logError($e);
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
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

//    public function generateReceipt($transactionId)
        //    {
        //
        //        $transaction = Transaction::findOrFail($transactionId);
        //
        //        if ($transaction->type === 'payment') {
        //
        //            $transaction = $transaction
        //                ->where('id', $transaction->id)
        //                ->with([
        //                    'article_request' => function ($q) {
        //                        $q->with([
        //                            'user' => function ($q) {
        //                                $q->with([
        //                                    'courses' => function ($q) {
        //                                        $q->with('currentTranslation');
        //                                    },
        //                                    'classes' => function ($q) {
        //                                        $q->with([
        //                                            'room' => function ($q) {
        //                                                $q->with('currentTranslation');
        //                                            }
        //                                        ]);
        //                                    },
        //                                    'parameters' => function ($q) {
        //                                        $q->where('code', 'n_mecanografico');
        //                                    }
        //                                ]);
        //                            },
        //                            'article'
        //                        ]);
        //                    },
        //                    'transaction_info' => function ($q) {
        //                        $q->with(['bank']);
        //                    },
        //                    'createdBy'
        //                ])
        //                ->first();
        //
        //            $nextCode = '000001';
        //            $latestReceipt = TransactionReceipt::latest()->first();
        //            if ($latestReceipt && Carbon::parse($latestReceipt->created_at)->year === Carbon::now()->year) {
        //                $nextCode = str_pad((int)$latestReceipt->code + 1, 6, '0', STR_PAD_LEFT);
        //            }
        //
        //            // create receipt
        //            $receipt = new TransactionReceipt();
        //            $receipt->transaction_id = $transaction->id;
        //            $receipt->code = $nextCode;
        //            $receipt->created_at = Carbon::now();
        //            $receipt->save();
        //
        //            $data = [
        //                'transaction' => $transaction,
        //                'receipt' => $receipt
        //            ];
        //
        //            // return view('Payments::transactions.pdf_recibo', $data);
        //            // Footer
        //            $footer_html = view()->make('Payments::transactions.partials.pdf_footer', ['user' => $transaction->createdBy])->render();
        //
        //            $fileName = 'recibo-' . Carbon::now()->format('y') . '-' . $receipt->code . '.pdf';
        //
        //            $pdf = PDF::loadView('Payments::transactions.pdf_recibo', $data)
        //                ->setOption('margin-top', '10')
        //                ->setOption('header-html', '<header></header>')
        //                ->setOption('footer-html', $footer_html)
        //                ->setPaper('a5')
        //                ->save(storage_path('app/public/receipts-temp/' . $fileName));
        //
        //            $merger = PDFMerger::init();
        //
        //            $merger->addPDF(storage_path('app/public/receipts-temp/' . $fileName));
        //            $merger->addPDF(storage_path('app/public/receipts-temp/' . $fileName));
        //            $merger->merge();
        //
        //            Storage::delete('receipts-temp/' . $fileName);
        //
        //            $merger->save(storage_path('app/public/receipts/' . $fileName), 'file');
        //
        //            $receipt->path = '/storage/receipts/' . $fileName;
        //            $receipt->save();
        //
        //            // return 'ok';
        //            return true;
        //        }
        //
        //        return false;
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|void
     */
    public function update(\Illuminate\Http\Request $request){
       
        try {
            $transaction_id = $request->transaction_id;
            $a=[];
            //$articleRequest = ArticleRequest::findOrFail($id);
            if ($request->has("disciplines")) {
                
                $articleRequest = ArticleRequest::whereId($request->article_req_id)
                                  ->first();
                $articleRequest->discipline_id = $request->disciplines;
                $articleRequest->save();

                // Success message
                Toastr::success(__('Artigo editado com sucesso'), __('toastr.success'));
                return redirect()->route('requests.index');
            } else {
                
                DB::beginTransaction();
                            $transactionRequest = DB::table('transaction_article_requests')
                                ->join('article_requests', 'article_requests.id', '=', 'transaction_article_requests.article_request_id')
                                ->where('transaction_article_requests.transaction_id', $transaction_id)
                                ->where('article_requests.status', '!=', 'pending')
                                ->select(
                                    'transaction_article_requests.value as value',
                                    'transaction_id',
                                    'article_requests.id as article_request_id'
                                )
                            ->get();
                    
                        // code que consulta e retorna o valor do saldo em carteira.
                        $historic_user_balance = DB::table('historic_user_balance')
                        ->where('historic_user_balance.id_transaction',$transaction_id)
                        ->get();

                        // code que consulta o sado que foi gerado na transação...
                         $historic_user_credit_balance = DB::table('historic_user_credit_balance')
                         ->where('historic_user_credit_balance.id_transaction', $transaction_id)
                         ->get();
                         if (!$historic_user_credit_balance->isEmpty()) {
                             foreach ($historic_user_credit_balance as $key => $item) {
                                $users = DB::table('users')
                                ->where('users.id', $item->user_id)
                                ->first();
                                $saldoAtual=$users->credit_balance - $item->valor;
                                DB::table('users')
                                ->updateOrInsert(
                                    ['id' => $item->user_id],
                                    ['credit_balance' =>  $saldoAtual < 0 ? 0: $saldoAtual]
                                );
                                DB::table('historic_user_credit_balance')
                                ->where('historic_user_credit_balance.id_transaction', '=',$transaction_id)
                                    ->update(
                                        ['data_from' =>'estorno']
                                    );
                             }
                           
                         }

                        if (!$historic_user_balance->isEmpty()) {
                            foreach ($historic_user_balance as $key => $value) {
                                /*
                                    *****retornar o valor em carteira que foi usado por esta transação
                                */
                                DB::table('users')
                                    ->updateOrInsert(
                                        ['id' => $value->id_user],
                                        ['credit_balance' => $value->valor_credit]
                                    );
                                DB::table('historic_user_balance')
                                ->where('historic_user_balance.id_transaction', $transaction_id)
                                    ->update(
                                        ['data_from' =>'estorno']
                                    );
                                
                            }
                        }
                   
                         
                        //  return "12323";

                // return $transactionRequest;
                foreach ($transactionRequest as $key => $value) {
                    // ////// tenho que verificar aaqui este cadastramento.
                      $articleRequest = ArticleRequest::whereId($value->article_request_id)
                        ->with([
                            'article' => function ($q) {
                                $q->with([
                                    'currentTranslation',
                                    'extra_fees'
                                ]);
                            },
                            'transactions' => function ($q) {
                                $q->where('data_from','=','');
                                $q->with(['transaction_info' => function ($q) {
                                    $q->with('bank');
                                }]);
                            },
                            'user'
                        ])
                        ->firstOrFail();

                        $data = $this->calculatePaidValue($articleRequest,$transaction_id );
                        $qtd_transacion=$data['qtdTrans_info'];
                        $paidValue =$data['calculatedValue'];
                            
                        if($qtd_transacion<2){
                            $paidValue = $paidValue< $articleRequest->base_value ? $articleRequest->base_value : $paidValue ;
                            $articleRequest->extra_fees_value=0;
                            $articleRequest->estado_extra_fees=0;
                        }
                      $extra_fees_value= $articleRequest->extra_fees_value;
                        if ($paidValue === 0.0) {
                            $extra_fees_value=0;
                            $status='pending';
                            $type = 'info';
                        }
                        elseif ($paidValue === ($articleRequest->base_value + $articleRequest->extra_fees_value)) {
                            $extra_fees_value=0;
                            $status ='pending';
                            $type = 'success';
                        } 
                        elseif ($paidValue > 0.0 && $paidValue < ($articleRequest->base_value + $articleRequest->extra_fees_value)) {
                            $status ='partial';
                            $type = 'warning';
                        } else {
                            $status='pending';
                            $type = 'danger';
                        }
                        if ($status !== $articleRequest->status) {
                            $a[]=$status;
                            $articleRequest->status = $status;
                            
                            $articleRequest->extra_fees_value = $extra_fees_value;
                            $articleRequest->save();
                        } 
                        // return $status;

                        DB::table('transactions')
                        ->where('id', $transaction_id)
                        ->update([
                            'type' => 'credit',
                            'notes' => $request->motivo_estorno,
                            'data_from' => "Estorno",
                            'updated_by' => Auth::user()->id,
                            'updated_at'=>Carbon::Now()
                        ]);
                       
                        // $transactionId = Transaction::create([
                        //     'type' => 'debit',
                        //     'value' => $value->value, //$request->balanceValue, //$articleRequest->base_value,
                        // ]);

                    //     $transaction_articleReq=DB::table('transaction_article_requests')
                    //     ->insert([
                    //         'transaction_id'=>$transactionId->id,
                    //         'article_request_id'=>$value->article_request_id,
                    //         'value'=>$value->value,
                    //     ]);

                                    // $transactionId->article_request()->attach($value->article_request_id, ['value' => $value->value]);


                           
                }

                DB::commit();
                // Success message
                
                Toastr::success(__('Estorno efetuado com sucesso'), __('toastr.success'));
                return redirect()->route('requests.index');
            }
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    protected function calculatePaidValue(ArticleRequest $articleRequest, $transaction_id )
    {
        $valueOffsetToZero = $articleRequest->base_value + $articleRequest->extra_fees_value; //
        $trans_info=[];
        $data=[];
        $qtdTrans_info=0;
        $calculatedValue = 0;
        if ($articleRequest) {
            if (!$articleRequest->transactions) {
                 $articleRequest->load('transactions');
            }
            
            foreach ($articleRequest->transactions as $transaction) {
                if ($transaction->transaction_info==null) {
                    $trans_info[]='N-info';
                }else{
                    $trans_info[]='S_info';
                    $qtdTrans_info+=1;
                }
            }
            if ($qtdTrans_info<2 && in_array('S_info',$trans_info)) {
                foreach ($articleRequest->transactions as $transaction) {
                    if ($transaction_id == $transaction->id ) {
                        $operation = $transaction->type === 'debit' ? -1 : 1;
                        $calculatedValue += $operation * $transaction->pivot->value; // 
                    }   
                
                }   
            }else{
                foreach ($articleRequest->transactions as $transaction) {
                    if ($transaction_id == $transaction->id ) {
                        $operation = $transaction->type === 'debit' ? -1 : 1;
                        $calculatedValue += $operation * $transaction->pivot->value; // 
                    }   
                
                }
            }

            

            // return $calculatedValue ;
            // +=  $articleRequest->base_value;
        }

        return $data=[
            'calculatedValue'=>$calculatedValue,
            'qtdTrans_info'=>$qtdTrans_info
        ] ;
    }

    protected function calculateArticleRequestExtraFee(ArticleRequest $articleRequest)
    {
        $extraFeesValue = 0;

        if ($articleRequest) {
            $extraFees = $articleRequest->article->extra_fees;

            if ($extraFees->count()) {
                $articleRequestDate = $articleRequest->year && $articleRequest->month ?
                    Carbon::parse($articleRequest->year . '-' . $articleRequest->month . '-' . 1)->startOfDay() :
                    Carbon::parse($articleRequest->created_at)->startOfDay();

                $extraFeesValue = 0;
                $highestDayDiff = 0;

                foreach ($articleRequest->transactions as $transaction) {
                    if ($transaction->type === 'payment' && $transaction->transaction_info) {
                        $transactionDate = Carbon::parse($transaction->transaction_info->fulfilled_at)->startOfDay();
                        $diffInDays = $articleRequestDate->diffInDays($transactionDate);
                        $highestDayDiff = $diffInDays > $highestDayDiff ? $diffInDays : $highestDayDiff;
                    }
                }

                if ($highestDayDiff) {
                    $normalPaymentDays = 0;
                    foreach ($extraFees as $extra) {
                        if ((int)$extra->fee_percent === 0) {
                            $normalPaymentDays = $extra->max_delay_days;
                        }
                    }

                    $extraFeePercent = 0;
                    if ($highestDayDiff > $normalPaymentDays) {
                        foreach ($extraFees as $extra) {
                            $percent = (int)$extra->fee_percent;
                            if ($percent !== 0 && $extra->max_delay_days <= ($highestDayDiff - $normalPaymentDays)) {
                                $extraFeePercent = $percent > $extraFeePercent ? $percent : $extraFeePercent;
                            }
                        }
                    }

                    if ($extraFeePercent) {
                        $extraFeesValue = $articleRequest->base_value * ($extraFeePercent / 100);
                    }
                }
            }
        }

        return $extraFeesValue;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|void
     */
    public function destroy($id)
    {
        try {

            // $checkPermission = auth()->user()->can('manage-article-requests');
            $articleRequest = ArticleRequest::findOrFail($id);

            if ($articleRequest->status !== 'pending') {
                Toastr::error(__('Payments::requests.destroy_error_message'), __('toastr.error'));
                return redirect()->back();
            }

            DB::beginTransaction();

            // delete relations
            $articleRequest->transactions()->delete();

            // Delete translations
            $articleRequest->delete();

            $articleRequest->deleted_by = auth()->user()->id;

            // update DB row to force update to delete_by
            $articleRequest->save();

            DB::commit();

            // Success message
            Toastr::success(__('Payments::payments.destroy_success_message'), __('toastr.success'));
            return redirect()->route('requests.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::payments.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function deleteArticleRequest($articleRequestId)
    {
        try {
            $articles=explode(',',$articleRequestId);
                       

            DB::beginTransaction(); 
                foreach ($articles as $key => $item) {

                    $updateArticleRequest=DB::table('article_requests')
                    ->where('id', '=',$item)
                    ->update([
                        'deleted_by' =>auth()->user()->id,
                        'deleted_at' => Carbon::Now()
                    ]);
                        
                     $arti =DB::table('article_requests as ar')
                    ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
                    ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
                    ->leftJoin('articles as art','art.id',"=","ar.article_id")
                    ->where('up.parameters_id',1)
                    ->where('at.active',1)
                    ->select([
                    "ar.id",  
                    "ar.user_id",
                    "at.display_name as emolumento"])
                    ->where('ar.id',$item)
                    ->first();
                    
                    
                    $obs = "O emolumento '".$arti->emolumento."' foi eliminado.";
                    $Observation = DB::table('current_account_observations')
                    ->insert([
                        'user_id' =>$arti->user_id,
                        'observation' => $obs,
                        'file' => "Sem arquivo anexado...",
                    ]);
                      
                   

                }
                
            DB::commit();

            // Success message
            Toastr::success(__('Payments::payments.destroy_success_message'), __('toastr.success'));
            return redirect()->route('requests.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Payments::payments.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function changeState($articleId, $userId)
    {
        if (in_array($articleId, [23,24,27,21,22,25,26,6,41,42,36,8])) { //caso o artigo for aguardar pagamento por - declaracao, inscricao e confirmacao de matricula
            UserState::updateOrCreate(
                ['user_id' => $userId],
                ['state_id' => 17, 'courses_id' => null] //17 => aguardar pagamento
            );
            UserStateHistoric::create([
                        'user_id' => $userId,
                        'state_id' => 17
                    ]);
        }
    }

    public function getArticleRequests($userId)
    {
        $userId = auth()->user()->can('manage-requests-others') ? $userId : auth()->user()->id;

        try {
            $model = ArticleRequest::whereUserId($userId)
                ->with(['article' => function ($q) {
                    $q->with([
                        'extra_fees',
                        'currentTranslation'
                    ]);
                }])
                ->join('users as u0', 'u0.id', '=', 'article_requests.user_id')
                ->join('users as u1', 'u1.id', '=', 'article_requests.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'article_requests.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'article_requests.deleted_by')
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'article_requests.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })

                //relacao entre a disciplines_translations e o articles_requests.meta
                //para retornar o nome da disciplina.
                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'article_requests.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })

                ->select([
                    'article_requests.*',
                    'u0.name as user',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name as discipline_name'
                ])
                ->get();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    
    private function orderPay($model){
        $organizado = ['partial' => [],'pending' => [],'total' => [],];
        foreach ($model as $objeto) {
            switch ($objeto->status) {
                case 'partial':
                    $organizado['partial'][] = $objeto;
                    break;
                case 'pending':
                    $organizado['pending'][] = $objeto;
                    break;
                case 'total':
                    $organizado['total'][] = $objeto;
                break;
            }
        }
        $resultado = array_merge($organizado['partial'], $organizado['pending'], $organizado['total']);
        return $resultado;
    }    

    public function transactionsBy($userId,$anoLectivo, $userApi = false){
        try{ 
            $getRegraImplementEmolu=null;
            $object=[];
            $data_anolectivo=null;
            $totalValorTrans=0;
            $arrayMonth_getRegraImplementada=[];
            $arrayMonth_getRegraImplementEmolu=[];
            $lectiveYearSelected = LectiveYear::whereId($anoLectivo)->first();
            

                    // consultar criada para os estorno, que sera mostrado no modal.
                    $modelo = DB::table('articles as art')
                        ->leftJoin('article_translations as at', function ($join) {
                            $join->on('art.id', '=', 'at.article_id');
                            $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('at.active', '=', DB::raw(true));
                        })
                        ->join('article_requests as article_ret', function ($join) {
                            $join->on('art.id', '=', 'article_ret.article_id');
                        })
                        ->join('transaction_article_requests as trans_artic_req', function ($join) {
                            $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                        })
                        ->join('transactions as tran', function ($join) {
                            $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                        })
                        ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                            $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                        })
                        ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                            $join->on('tran.id','=','historic_saldo.id_transaction');
                        })
                        ->leftJoin('user_parameters as up',function ($join){
                            $join->on('up.users_id','=','tran.updated_by')
                            ->where('up.parameters_id','=',1);
                        })
                        ->leftJoin('disciplines', 'disciplines.id', '=', 'article_ret.discipline_id')
                        ->leftJoin('disciplines_translations as dcp', function ($join) {
                            $join->on('dcp.discipline_id', '=', 'disciplines.id');
                            $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                            $join->on('dcp.active', '=', DB::raw(true));
                        })
                        ->select([
                            'dcp.display_name as discipline_name',
                            'disciplines.code as codigo_disciplina',
                            'article_ret.discipline_id as discipline_id',
                            'article_ret.id as article_req_id',
                            'tran.value as value',
                            'tran.id as transaction_id',
                            'up.value as nome_creador',
                            'historic_saldo.valor_credit as valor_credit',
                            'at.display_name as article_name',
                            'article_ret.year as article_year',
                            'article_ret.month as article_month',
                            'article_ret.base_value as base_value',
                            'article_ret.extra_fees_value as extra_fees_value',
                            'article_ret.status as status',
                            'article_ret.discipline_id as art_idDisciplina',
                            'article_ret.meta as meta',
                            'trant_receipts.created_at as created_at_arti',
                            'tran.data_from as data_from',
                            'tran.updated_at as updated_at',
                            'trant_receipts.path as path',
                            'trant_receipts.code as code'
                        ])
                        ->where('article_ret.user_id', $userId)
                        ->where('trant_receipts.code', '!=', null)
                        ->where('tran.data_from', '=','Estorno')
                        ->orderBy('article_ret.year', 'ASC')
                        ->orderBy('article_ret.month', 'ASC')
                        ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                    ->get()
                    ->groupBy('transaction_id');

                    $modelo=collect($modelo)->map(function ($item) 
                    { 
                        foreach ($item as $key => $value) {
                            $array=null;
                            $code=null;
                            if ($value->path!=null) {
                                $array=explode("-",$value->path);
                                $code=explode(".",$array[2]);
                                $value->{'code_recibo'} = $array[1].'-'.$code[0];
                            }
                            else{
                              $value->{'code_recibo'} ="Erro criação";  
                            }
                        }
                        return $item;
                    });
                    $totalValorTrans=collect($modelo)->map(function ($item) use($object,$totalValorTrans)
                    { 
                        foreach ($item as $key => $value) {
                            if (empty($object)){
                                $object []= $value->code_recibo; 
                                $totalValorTrans+=$value->value;

                            }
                            elseif(in_array($value->code_recibo,$object)){

                            }
                            else{
                                $object []= $value->code_recibo;
                                $totalValorTrans+=$value->value;
                            }
                        }
                        return $totalValorTrans;
                    });
                    $data_anolectivo=$lectiveYearSelected->start_date.' - '.$lectiveYearSelected->end_date;

                        $disciplines = DB::table('articles as art')
                            ->join('article_requests','article_requests.article_id','=','art.id')
                            ->join('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                            ->leftJoin('disciplines_translations as dcp', function ($join) {
                                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('dcp.active', '=', DB::raw(true));
                            })
                            ->join('courses_translations as ct', function ($join) {
                                $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                $join->on('ct.active', '=', DB::raw(true));
                            })
                            ->select([
                                'article_requests.id as article_req_id',
                                'dcp.display_name as discipline_name',
                                'disciplines.code as codigo_disciplina',
                                'article_requests.discipline_id as discipline_id',
                                'ct.display_name as course_name',
                                'dcp.abbreviation as abbreviation'
                            ])
                            ->whereNull('article_requests.deleted_at')
                            ->whereNull('article_requests.deleted_by')
                            ->whereNull('art.deleted_by')
                            ->where('article_requests.user_id',$userId)
                            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->get();

                        $qdt_disciplines=count($disciplines);

                        $metrics = DB::table('articles as art')
                            ->join('article_requests','article_requests.article_id','=','art.id')
                            ->join('metricas', 'metricas.id', '=', 'article_requests.metric_id')
                            ->select([
                                'article_requests.id as article_req_id',
                                'article_requests.metric_id as metric_id',
                                'metricas.nome as nome'
                            ])
                            ->whereNull('article_requests.deleted_at')
                            ->whereNull('article_requests.deleted_by')
                            ->whereNull('art.deleted_by')
                            ->where('article_requests.user_id',$userId)
                            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->get();

                            $consultArt = DB::table('articles as art')
                                ->leftJoin('article_translations as at', function ($join) {
                                    $join->on('art.id', '=', 'at.article_id');
                                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                    $join->on('at.active', '=', DB::raw(true));
                                })
                                ->join('article_requests as article_ret', function ($join) {
                                    $join->on('art.id', '=', 'article_ret.article_id');
                                })
                                ->join('transaction_article_requests as trans_artic_req', function ($join) {
                                    $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                                })

                                ->join('transactions as tran', function ($join) {
                                    $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                                })

                                ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                                    $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                                })
                                ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                                    $join->on('tran.id','=','historic_saldo.id_transaction');
                                })
                                ->select([
                                    'article_ret.id as article_req_id',
                                    'tran.id as transaction_id',
                                    'tran.type as trans_type',
                                    'historic_saldo.valor_credit as valor_credit',
                                    'at.display_name as article_name',
                                    'article_ret.year as article_year',
                                    'article_ret.month as article_month',
                                    'article_ret.base_value as base_value',
                                    'article_ret.extra_fees_value as extra_fees_value',
                                    'article_ret.status as status',
                                    'article_ret.discipline_id as art_idDisciplina',
                                    'article_ret.meta as meta',
                                    'trant_receipts.created_at as created_at_arti',
                                    'tran.data_from as data_from',
                                    'trant_receipts.code as code'
                                ])
                                ->where('article_ret.user_id', $userId)
                                ->whereNull('article_ret.deleted_at')
                                ->whereNull('article_ret.deleted_by')
                                ->whereNull('tran.deleted_at')
                                ->where('tran.type','!=','debit')
                                ->orderBy('article_ret.year', 'ASC')
                                ->orderBy('article_ret.month', 'ASC')
                                ->where('tran.data_from', '!=','Estorno')
                                // ->orderBy('tran.id', 'ASC')
                                ->orderBy('trant_receipts.code', 'ASC')
                                ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                            ->get();
                            $i=0;
                            $collet=collect($consultArt)->map(function($item){
                                return $item->article_req_id;
                              
                            });
                
                        
                            
                            $consultRecibos = DB::table('articles as art')
                                ->leftJoin('article_translations as at', function ($join) {
                                    $join->on('art.id', '=', 'at.article_id');
                                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                    $join->on('at.active', '=', DB::raw(true));
                                })
                                ->join('article_requests as article_ret', function ($join) {
                                    $join->on('art.id', '=', 'article_ret.article_id');
                                })
                                ->join('transaction_article_requests as trans_artic_req', function ($join) {
                                    $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                                })

                                ->join('transactions as tran', function ($join) {
                                    $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                                })

                                ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                                    $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                                })
                                ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                                    $join->on('tran.id','=','historic_saldo.id_transaction');
                                })
                                ->select([
                                    'article_ret.id as article_req_id',
                                    'tran.id as transaction_id',
                                    'tran.type as trans_type',
                                    'historic_saldo.valor_credit as valor_credit',
                                    'at.display_name as article_name',
                                    'article_ret.year as article_year',
                                    'article_ret.month as article_month',
                                    'article_ret.base_value as base_value',
                                    'article_ret.discipline_id as art_idDisciplina',
                                    'article_ret.meta as meta',
                                    'article_ret.extra_fees_value as extra_fees_value',
                                    'article_ret.status as status',
                                    'tran.data_from as data_from',
                                    'trant_receipts.code as code'
                                ])
                                ->where('article_ret.user_id', $userId)
                                ->where('tran.type', '=', 'debit')
                                ->whereNull('article_ret.deleted_at')
                                ->whereNull('article_ret.deleted_by')
                                ->whereNull('tran.deleted_at')
                                ->whereNotin('trans_artic_req.article_request_id',$collet) 
                                ->orderBy('article_ret.year', 'ASC')
                                ->orderBy('article_ret.month', 'ASC')
                                // ->orderBy('tran.id', 'ASC')
                                ->orderBy('trant_receipts.code', 'ASC')
                                ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                            ->get();
                            
                            $model= $consultArt->merge($consultRecibos);
                            
                            $model = $this->orderPay($model);

                            $qdt_model=count($model);

                            $classe = $this->articlesUtil->getMatriculationClass($anoLectivo, $userId);
                            
                                // esta consulta vai verificar se este alguma regra implementada, tanto faze regra por curso o no ambito geral por anolectivo.

                                $getRegraImplementada =  $this->articlesUtil->getRegraImplementada($anoLectivo, $userId);

                                if (count($getRegraImplementada)>0) {
                                    foreach ($getRegraImplementada as $key => $value) {
                                        $arrayMonth_getRegraImplementada[]=$value->mes;
                                    }
                                }else{

                                    
                                    $getRegraImplementEmolu =  $this->articlesUtil->getRegraImplementEmolu($anoLectivo, $userId);
                                  
                                    if (count($getRegraImplementEmolu)>0) {
                                        foreach ($getRegraImplementEmolu as $key => $value) {
                                            $arrayMonth_getRegraImplementEmolu[]=$value->mes;
                                        }   
                                    }
                                } 
                            
                            $user = User::whereId($userId)->first();

                            $data=[
                                'arrayMonth_getRegraImplementada'=>$arrayMonth_getRegraImplementada,
                                'arrayMonth_getRegraImplementEmolu'=>$arrayMonth_getRegraImplementEmolu,
                                'getRegraImplementEmolu'=>$getRegraImplementEmolu,
                                'getRegraImplementada'=>$getRegraImplementada,
                                'disciplines'=>$disciplines,
                                'model'=>$model,
                                'modelo'=>$modelo,
                                'metrics'=>$metrics,
                                'user'=> auth()->user() ?? $user

                            ];
                            // if(auth()->user()->id == 845)dd($data);
                            
                            
                            $detalheEstorno=[
                                'totalValorTrans'=>$totalValorTrans,
                                'data_anolectivo'=>$data_anolectivo,
                            ];

                            if ($qdt_model>0 || $qdt_disciplines>0) {
                                $view = view("Payments::requests.table")->with($data)->render();
                                $html_view = view("Payments::requests.table-estorno")->with($data)->render();
                     
                                return $userApi ? $view : response()->json(['html'=>$view,'data_html'=>$html_view,'detalheEstorno'=>$detalheEstorno,'data'=>$data]);
                            } else {
                                return $userApi ? null :response()->json(array('data'=>false));
                            }
            } catch (Exception | Throwable $e) {
                // return $e;
                logError($e);
                return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    
    }
    
    private function chooseClass(){
        //ssss
        return "<div>O estudante deve pertence a uma turma</div>";
    }

    public function getAnolectivo_student($userId){
        $display_nameAnolectivo=[];
        $anolectivoSem_matricula=[];
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
      
        $getInformalionUser=DB::table('user_parameters as user_paramet')
        ->where('user_paramet.parameters_id',25)
        ->where('user_paramet.users_id',$userId)
        ->get();

        $getConfirmation_finalista=DB::table('matriculation_finalist as matricula_finalist')
        ->join('lective_year_translations as lective_year_translation',function($q){
            $q->on('lective_year_translation.lective_years_id','=','matricula_finalist.year_lectivo')
            ->where('lective_year_translation.language_id',1)
            ->where('lective_year_translation.active',1);
        })
        ->select([
            'matricula_finalist.id as id_matriculation_finalist',
            'matricula_finalist.num_confirmaMatricula as num_confirmaMatricula',
            'matricula_finalist.year_curso as year_curso',
            'matricula_finalist.created_at as created_at',
            'matricula_finalist.updated_at as updated_at',
            'lective_year_translation.display_name as anoLectivo',
            'lective_year_translation.lective_years_id as lective_years_id'
        ])
        ->where('matricula_finalist.user_id',$userId)
        ->whereNull('matricula_finalist.deleted_by')
        ->whereNull('matricula_finalist.deleted_at')
        ->get();

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        
        foreach ($lectiveYears as $chave => $item) {
            $students =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
            ->select([
                    'u0.id as id',
                    'u1.name as created_by'
                ]) 
            ->whereNull('matriculations.deleted_at')
            ->where('matriculations.user_id',$userId)
            ->where('matriculations.lective_year',$item->id)
            ->get();
            $qdtConsulta=count($students);
            if ($qdtConsulta>0) {
                $display_nameAnolectivo[]=$item;
            }else{
                $anolectivoSem_matricula[]=$item;
            }
        }
        $getSaldoCarteira=DB::table('users')->whereId($userId)->get();

        $data = [
            'getConfirmation_finalista'=>$getConfirmation_finalista,
            'display_nameAnolectivo'=>$display_nameAnolectivo,
            'anolectivoSem_matricula'=>$anolectivoSem_matricula,
            'getSaldoCarteira'=>$getSaldoCarteira
        ];
       

        return response()->json([
            'data' =>$data,
            'anoativo'=>$lectiveYearSelected,
            'getInformalionUser'=>$getInformalionUser
        ]);
    }

    public function getConsultaPropina_apagar($userId,$anolectivo_ativo){
        
        $lectiveYearSelected = LectiveYear::whereId($anolectivo_ativo)->first();
        $consultRecibos = DB::table('articles as art')
        ->join('article_requests as article_ret', function ($join) {
            $join->on('art.id', '=', 'article_ret.article_id');
        })
        ->join('transaction_article_requests as trans_artic_req', function ($join) {
            $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
        })
        ->join('transactions as tran', function ($join) {
            $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
        })
        ->join('code_developer as codev',function ($join)
        {
            $join->on('art.id_code_dev','=','codev.id');
        })
        ->select([
            'article_ret.id as article_req_id',
            'tran.id as transaction_id',
            'tran.type as trans_type',
            'article_ret.year as article_year',
            'article_ret.month as article_month',
            'article_ret.base_value as base_value',
            'article_ret.discipline_id as art_idDisciplina',
            'article_ret.meta as meta',
            'article_ret.extra_fees_value as extra_fees_value',
            'article_ret.status as status',
            'tran.data_from as data_from',
        ])
        ->where('article_ret.user_id', $userId)
        ->whereNull('article_ret.deleted_at')
        ->whereNull('article_ret.deleted_by')
        ->whereNull('tran.deleted_at')
        ->orderBy('tran.id', 'DESC')
        ->whereIn('codev.code',['propina','propina_finalista'])
        ->where('article_ret.status','!=','total') 
        ->whereNull('article_ret.deleted_at')  
        ->where('article_ret.month','!=',null) 
        ->where('article_ret.year','!=',null)  
        ->orderBy('article_ret.year', 'ASC')
        ->orderBy('article_ret.month', 'DESC')
        ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
        ->get();
        return response()->json(['data'=>$consultRecibos]);
    }
    
     public function getFiltroEmolumento_student($selectedUserId){
        $display_nameAnolectivo=[];
        $filtroEmolumento=[];

        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();
      

        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
        ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
        ->first();
        $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        
        foreach ($lectiveYears as $chave => $item) {
            $students =  Matriculation::join('users as u0', 'u0.id', '=', 'matriculations.user_id')
            ->join('users as u1', 'u1.id', '=', 'matriculations.created_by')
            ->where('matriculations.deleted_at',null)
            ->where('matriculations.user_id',$selectedUserId)
            ->select([
                'u0.id as id',
                'u1.name as created_by',
                ]) 
            ->whereBetween('matriculations.created_at', [$item->start_date, $item->end_date])
            ->get();
                $qdtConsulta=count($students);
            if ($qdtConsulta>0) {
                $display_nameAnolectivo[]=$item;
            }
        }
    
        foreach ($display_nameAnolectivo as $key => $value) {
            $lectiveYearSelected = LectiveYear::whereId($value->id)->first();
            $consultRecibos = DB::table('articles as art')
                ->join('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->join('article_requests as article_ret', function ($join) {
                    $join->on('art.id', '=', 'article_ret.article_id');
                })
                ->select([
                'art.id as art_id',
                'at.display_name as article_name'
                ])
                ->where('article_ret.user_id', $selectedUserId) 
                ->whereNull('article_ret.deleted_at') 
                ->distinct('art.id')
                ->whereBetween('art.created_at', [$value->start_date, $value->end_date])
            ->get();
            $qdt_get=count($consultRecibos);
            if ($qdt_get>0) {
                $filtroEmolumento[]=collect([$consultRecibos,$value->id]) ;
            }
           
        }

        return response()->json(['data'=>$filtroEmolumento]);
    }

    public function filtroEmolumento_student($id_art,$selectedUserId,$ano_lectivod){
        $getRegraImplementEmolu=null;
        $arrayMonth_getRegraImplementada=[];
        $arrayMonth_getRegraImplementEmolu=[];
        try{
            $disciplines = DB::table('articles as art')
                ->join('article_requests','article_requests.article_id','=','art.id')
                ->join('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                ->join('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->select([
                    'article_requests.id as article_req_id',
                    'dcp.display_name as discipline_name',
                    'disciplines.code as codigo_disciplina',
                    'article_requests.discipline_id as discipline_id',
                    'ct.display_name as course_name',
                    'dcp.abbreviation as abbreviation'
                ])
                ->whereNull('article_requests.deleted_at')
                ->whereNull('article_requests.deleted_by')
                ->where('article_requests.user_id',$selectedUserId)
                ->where('article_requests.article_id', $id_art)
            ->get();

            $metrics = DB::table('articles as art')
                            ->join('article_requests','article_requests.article_id','=','art.id')
                            ->join('metricas', 'metricas.id', '=', 'article_requests.metric_id')
                            ->select([
                                'article_requests.id as article_req_id',
                                'article_requests.metric_id as metric_id',
                                'metricas.nome as nome'
                            ])
                            ->whereNull('article_requests.deleted_at')
                            ->whereNull('article_requests.deleted_by')
                            ->whereNull('art.deleted_by')
                            ->where('article_requests.user_id',$selectedUserId)
                            ->where('article_requests.article_id', $id_art)
                        ->get();

            
            $consultArt = DB::table('articles as art')
                ->join('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->join('article_requests as article_ret', function ($join) {
                    $join->on('art.id', '=', 'article_ret.article_id');
                })
                ->join('transaction_article_requests as trans_artic_req', function ($join) {
                    $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                })

                ->join('transactions as tran', function ($join) {
                    $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                })

                ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                    $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                })
                ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                    $join->on('tran.id','=','historic_saldo.id_transaction');
                })
                ->select([
                    'article_ret.id as article_req_id',
                    'tran.id as transaction_id',
                    'tran.type as trans_type',
                    'historic_saldo.valor_credit as valor_credit',
                    'at.display_name as article_name',
                    'article_ret.year as article_year',
                    'article_ret.month as article_month',
                    'article_ret.base_value as base_value',
                    'article_ret.extra_fees_value as extra_fees_value',
                    'article_ret.status as status',
                    'article_ret.discipline_id as art_idDisciplina',
                    'article_ret.meta as meta',
                    'trant_receipts.created_at as created_at_arti',
                    'tran.data_from as data_from',
                    'trant_receipts.code as code'
                ])
                ->where('article_ret.user_id', $selectedUserId)
                ->where('article_ret.article_id', $id_art)
                ->whereNull('article_ret.deleted_at')
                ->whereNull('article_ret.deleted_by')
                ->whereNull('tran.deleted_at')
                ->where('tran.type','!=','debit')
                ->orderBy('article_ret.year', 'ASC')
                ->orderBy('article_ret.month', 'ASC')
                ->orderBy('tran.id', 'DESC')
            ->get();

            $collet=collect($consultArt)->map(function($item)
            {
                return $item->article_req_id;
            });
            
            $consultRecibos = DB::table('articles as art')
                ->join('article_translations as at', function ($join) {
                    $join->on('art.id', '=', 'at.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->join('article_requests as article_ret', function ($join) {
                    $join->on('art.id', '=', 'article_ret.article_id');
                })
                ->join('transaction_article_requests as trans_artic_req', function ($join) {
                    $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                })

                ->join('transactions as tran', function ($join) {
                    $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                })

                ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                    $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                })
                ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                    $join->on('tran.id','=','historic_saldo.id_transaction');
                })
                ->select([
                    'article_ret.id as article_req_id',
                    'tran.id as transaction_id',
                    'tran.type as trans_type',
                    'historic_saldo.valor_credit as valor_credit',
                    'at.display_name as article_name',
                    'article_ret.year as article_year',
                    'article_ret.month as article_month',
                    'article_ret.base_value as base_value',
                    'article_ret.discipline_id as art_idDisciplina',
                    'article_ret.meta as meta',
                    'article_ret.extra_fees_value as extra_fees_value',
                    'article_ret.status as status',
                    'tran.data_from as data_from',
                    'trant_receipts.code as code'
                ])
                ->where('article_ret.user_id', $selectedUserId)
                ->where('article_ret.article_id', $id_art)
                ->where('tran.type', '=', 'debit')
                ->whereNull('article_ret.deleted_at')
                ->whereNull('article_ret.deleted_by')
                ->whereNull('tran.deleted_at')
                ->whereNotin('trans_artic_req.article_request_id',$collet) 
                ->orderBy('article_ret.year', 'ASC')
                ->orderBy('article_ret.month', 'ASC')
                ->orderBy('tran.id', 'DESC')
            ->get();

            $model= $consultArt->merge($consultRecibos);


            // esta consulta vai verificar se este alguma regra implementada, tanto faze regra por curso o no ambito geral por anolectivo.
            $getRegraImplementada=DB::table('artcles_rules as art_rule')
                ->join('articles as art','art.id','=','art_rule.id_articles')
            
                ->join('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'art.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->where('art_rule.id_articles','=',null)
                ->where('art_rule.deleted_by','=',null)
                ->where('art_rule.ano_lectivo','=',$ano_lectivod)
                ->select([
                    'art_rule.id as id_ruleArtc',
                    'art_rule.valor as valor',
                    'art_rule.mes as mes',
                    'art_rule.ano_lectivo as ano_lectivo',
                    'art_rule.created_at as created_at',
                    'at.display_name as display_name'
                ])
            ->get();
            if (count($getRegraImplementada)>0) {
                foreach ($getRegraImplementada as $key => $value) {
                    $arrayMonth_getRegraImplementada[]=$value->mes;
                }
            }else{
                 $getRegraImplementEmolu =  $this->articlesUtil->getRegraImplementEmolu($ano_lectivod, $selectedUserId,$id_art);
                if (count($getRegraImplementEmolu)>0) {
                    foreach ($getRegraImplementEmolu as $key => $value) {
                        $arrayMonth_getRegraImplementEmolu[]=$value->mes;
                    }   
                }
            }
            $user = User::whereId($userId)->first();
            $data=[
                'arrayMonth_getRegraImplementada'=>$arrayMonth_getRegraImplementada,
                'arrayMonth_getRegraImplementEmolu'=>$arrayMonth_getRegraImplementEmolu,
                'getRegraImplementada'=>$getRegraImplementada,
                'getRegraImplementEmolu'=>$getRegraImplementEmolu,
                'disciplines'=>$disciplines,
                'model'=>$model,
                'metrics'=>$metrics,
                'user'=> auth()->user() ?? $user

            ];
            
            $view = view("Payments::requests.table")->with($data)->render();
            return response()->json(['html'=>$view]);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
      
    }
    
    
    public function getOutrosEmolumentoRequerido($userId,$anoLectivoSem_matricula){
        
        try{
            
            $vetorAnoLectivo=explode(",",$anoLectivoSem_matricula);
            $getRegraImplementEmolu=null;
            $object=[];
            $totalValorTrans=0;
            $arrayMonth_getRegraImplementada=[];
            $arrayMonth_getRegraImplementEmolu=[];
            $getgetRegraImplementada=[];
            $getdisciplines=[];
            $getmodel=[];
            $getmodelo=[];

            foreach ($vetorAnoLectivo as $key => $valueAno) {
                $lectiveYearSelected = DB::table('lective_years')
                ->where('lective_years.id',$valueAno)
                ->first();
               
            
                // consultar criada para os estorno, que sera mostrado no modal.
                $modelo= DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })
                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })
                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    ->leftJoin('user_parameters as up',function ($join){
                        $join->on('up.users_id','=','tran.updated_by')
                        ->where('up.parameters_id','=',1);
                    })
                    ->leftJoin('disciplines', 'disciplines.id', '=', 'article_ret.discipline_id')
                    ->leftJoin('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    // ->leftjoin('code_developer as codev',function ($join)
                    // {
                    //     $join->on('art.id_code_dev','=','codev.id');
                    // })
                    ->select([
                        'dcp.display_name as discipline_name',
                        'disciplines.code as codigo_disciplina',
                        'article_ret.discipline_id as discipline_id',
                        'article_ret.id as article_req_id',
                        'tran.value as value',
                        'tran.id as transaction_id',
                        'up.value as nome_creador',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'trant_receipts.created_at as created_at_arti',
                        'tran.data_from as data_from',
                        'tran.updated_at as updated_at',
                        'trant_receipts.path as path',
                        'trant_receipts.code as code',
                        'art.created_at as data_at'
                    ])
                    //->whereNotIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->where('trant_receipts.code', '!=', null)
                    ->where('tran.data_from', '=','Estorno')
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get()
                ->groupBy('transaction_id');
                
                $modelo=collect($modelo)->map(function ($item){ 
                    foreach ($item as $key => $value) {
                        $array=null;
                        $code=null;
                        if ($value->path!=null) {
                            $array=explode("-",$value->path);
                             $code=explode(".",$array[2]);
                             $value->{'code_recibo'} = $array[1].'-'.$code[0];
                         }
                         else{
                           $value->{'code_recibo'} ="Erro criação";  
                         }
                    }
                    return $item;
                });
                if (!$modelo->isEmpty()) {
                    $totalValorTrans=collect($modelo)->map(function ($item) use($object,$totalValorTrans)
                    { 
                        foreach ($item as $key => $value) {
                            if (empty($object)){
                                $object []= $value->code_recibo; 
                                $totalValorTrans+=$value->value;

                            }
                            elseif(in_array($value->code_recibo,$object)){

                            }
                            else{
                                $object []= $value->code_recibo;
                                $totalValorTrans+=$value->value;
                            }
                        }
                        return $totalValorTrans;
                    });
                }else{}
               

                $disciplines = DB::table('articles as art')
                    ->join('article_requests','article_requests.article_id','=','art.id')
                    ->join('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                    ->join('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->select([
                        'article_requests.id as article_req_id',
                        'dcp.display_name as discipline_name',
                        'disciplines.code as codigo_disciplina',
                        'article_requests.discipline_id as discipline_id',
                        'ct.display_name as course_name',
                        'dcp.abbreviation as abbreviation'
                    ])
                    ->whereNull('article_requests.deleted_at')
                    ->whereNull('article_requests.deleted_by')
                    ->whereNull('art.deleted_by')
                    ->where('article_requests.user_id',$userId)
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();

                $qdt_disciplines=count($disciplines);

                $metrics = DB::table('articles as art')
                            ->join('article_requests','article_requests.article_id','=','art.id')
                            ->join('metricas', 'metricas.id', '=', 'article_requests.metric_id')
                            ->select([
                                'article_requests.id as article_req_id',
                                'article_requests.metric_id as metric_id',
                                'metricas.nome as nome'
                            ])
                            ->whereNull('article_requests.deleted_at')
                            ->whereNull('article_requests.deleted_by')
                            ->whereNull('art.deleted_by')
                            ->where('article_requests.user_id',$userId)
                            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->get();


                
                 $consultArt= DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })

                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })

                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    // ->leftjoin('code_developer as codev',function ($join)
                    // {
                    //     $join->on('art.id_code_dev','=','codev.id');
                    // })
                    ->select([
                        'article_ret.id as article_req_id',
                        'tran.id as transaction_id',
                        'tran.type as trans_type',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'trant_receipts.created_at as created_at_arti',
                        'tran.data_from as data_from',
                        'trant_receipts.code as code'
                    ])
                    // ->whereNotIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->whereNull('article_ret.deleted_at')
                    ->whereNull('article_ret.deleted_by')
                    ->whereNull('tran.deleted_at')
                    ->where('tran.type','!=','debit')
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    // ->where('art.anoLectivo',"=",$valueAno)
                    // ->orderBy('tran.id', 'ASC')
                    // ->orderBy('trant_receipts.code', 'ASC')
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
                $i=0;
                $collet=collect($consultArt)->map(function($item){
                    return $item->article_req_id;
                    
                });
        
                    
                        
                 $consultRecibos = DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })

                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })

                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    // ->leftjoin('code_developer as codev',function ($join)
                    // {
                    //     $join->on('art.id_code_dev','=','codev.id');
                    // })
                    ->select([
                        'article_ret.id as article_req_id',
                        'tran.id as transaction_id',
                        'tran.type as trans_type',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'tran.data_from as data_from',
                        'trant_receipts.code as code'
                    ])
                    // ->whereNotIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->where('tran.type', '=', 'debit')
                    ->whereNull('article_ret.deleted_at')
                    ->whereNull('article_ret.deleted_by')
                    ->whereNull('tran.deleted_at')
                    ->whereNotin('trans_artic_req.article_request_id',$collet) 
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    // ->orderBy('tran.id', 'ASC')
                    // ->orderBy('trant_receipts.code', 'ASC')
                    // ->where('art.anoLectivo',"=",$valueAno)
                    // ->where('art.anoLectivo',$valueAno)
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
                //   $model= $consultRecibos->merge($consultArt);
                $model= $consultArt->merge($consultRecibos);

               

                // esta consulta vai verificar se este alguma regra implementada, tanto faze regra por curso o no ambito geral por anolectivo.
                $getRegraImplementada=DB::table('artcles_rules as art_rule')
                    ->join('articles as art','art.id','=','art_rule.id_articles')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'art.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->where('art_rule.id_articles','=',null)
                    ->where('art_rule.deleted_by','=',null)
                    ->where('art_rule.ano_lectivo','=',$lectiveYearSelected->id)
                    ->select([
                        'art_rule.id as id_ruleArtc',
                        'art_rule.valor as valor',
                        'art_rule.mes as mes',
                        'art_rule.ano_lectivo as ano_lectivo',
                        'art_rule.created_at as created_at',
                        'at.display_name as display_name'
                    ])
                ->get();
                
                $getgetRegraImplementada[]=$getRegraImplementada;
                $getdisciplines[]=$disciplines;
                $getmodel[]=$model;
                $getmodelo[]=$modelo;

            }       

            // return $getmodelo;
                
            $getRegraImplementada=[];
            $disciplines=[];
            $modelo=[];
            $model=[];
            $array=[];
            $arrayArticle=[];
            foreach ($getmodel as $key => $item) {
                foreach ($item as $key => $value) {
                    if (!in_array($value->article_req_id,$arrayArticle)) {
                        $model[]=$value;
                        $arrayArticle[]=$value->article_req_id;
                    }
                       
                }    
            }

              $array=[];
            foreach ($getmodelo as $key => $item) {
                foreach ($item as $id_transation => $value) {
                    if (!in_array($id_transation,$array)) {
                        $modelo[]=$value;
                        $array[]=$id_transation;
                    }
                       
                }    
            }

            $array=[];
            foreach ($getdisciplines as $key => $item) {
                foreach ($item as $key => $value) {
                    if (!in_array($value->article_req_id,$array)) {
                        $disciplines[]=$value;
                        $array[]=$value->article_req_id;
                    }
                       
                }    
            }

            $array=[];
            foreach ($getgetRegraImplementada as $key => $item) {
                foreach ($item as $key => $value) {
                    if (!in_array($value->id_ruleArtc,$array)) {
                        $getRegraImplementada[]=$value;
                        $array[]=$value->id_ruleArtc;
                    }
                       
                }    
            }


            


            if (count($getRegraImplementada)>0) {
                foreach ($getRegraImplementada as $key => $value) {
                    $arrayMonth_getRegraImplementada[]=$value->mes;
                }
            }else{

                $getRegraImplementEmolu=DB::table('artcles_rules as art_rule')
                    ->join('articles as art','art.id','=','art_rule.id_articles')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'art.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as art_req',function($join){
                        $join->on('art_req.article_id','=','art_rule.id_articles');
                        $join->on('art_req.month','=','art_rule.mes');
                    })
                    ->where('art_rule.id_articles','!=',null)
                    ->where('art_req.user_id', $userId)
                    ->where('art_rule.deleted_by','=',null)
                    ->whereNull('art_req.deleted_at')
                    ->whereIn('art_rule.id',$arrayArticle)
                    // ->where('art_rule.ano_lectivo','=',$lectiveYearSelected->id)
                    ->select([
                        'art_req.id as id_art_req',
                        'art_rule.id_articles as id_articles',
                        'art_rule.id as id_ruleArtc',
                        'art_rule.valor as valor',
                        'art_rule.mes as mes',
                        'art_rule.ano_lectivo as ano_lectivo',
                        'art_rule.created_at as created_at',
                        'at.display_name as display_name'
                    ])
                ->get();

                if (count($getRegraImplementEmolu)>0) {
                    foreach ($getRegraImplementEmolu as $key => $value) {
                        $arrayMonth_getRegraImplementEmolu[]=$value->mes;
                    }   
                }
            }  
            $data=[
                'arrayMonth_getRegraImplementada'=>$arrayMonth_getRegraImplementada,
                'arrayMonth_getRegraImplementEmolu'=>$arrayMonth_getRegraImplementEmolu,
                'getRegraImplementEmolu'=>$getRegraImplementEmolu,
                'getRegraImplementada'=>$getRegraImplementada,
                'disciplines'=>$disciplines,
                'model'=>$model,
                'modelo'=>$modelo,
                'metrics'=>$metrics
            ];

            $detalheEstorno=[
                'totalValorTrans'=>$totalValorTrans,
                'data_anolectivo'=>"Outros emolumentos",
            ];
            $qdt_model=count($model);
            if ($qdt_model>0 || $qdt_disciplines>0) {
                $view = view("Payments::requests.table")->with($data)->render();
                $html_view = view("Payments::requests.table-estorno")->with($data)->render();
                // return response()->json($data);
                return response()->json(['html'=>$view,'data_html'=>$html_view,'detalheEstorno'=>$detalheEstorno,'data'=>$data]);
            } else {
                return response()->json(array('data'=>false));
            }
        } catch (Exception | Throwable $e) {
            // logError($e);
            return Request::ajax() ? response()->json(['error'=>$e->getMessage()], 500) : abort(500);
        }
    }
    
     public function getEmolumentoFinalista($userId,$anoLectivoSem_matricula){
        try{
            
            
            $vetorAnoLectivo=explode(",",$anoLectivoSem_matricula);
            $getRegraImplementEmolu=null;
            $object=[];
            $totalValorTrans=0;
            $arrayMonth_getRegraImplementada=[];
            $arrayMonth_getRegraImplementEmolu=[];
            $getgetRegraImplementada=[];
            $getdisciplines=[];
            $getmodel=[];
            $getmodelo=[];

            foreach ($vetorAnoLectivo as $key => $valueAno) {
                $lectiveYearSelected = DB::table('lective_years')
                ->where('lective_years.id',$valueAno)
                ->first();
                
            
                // consultar criada para os estorno, que sera mostrado no modal.
                $modelo= DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })
                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })
                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    ->leftJoin('user_parameters as up',function ($join){
                        $join->on('up.users_id','=','tran.updated_by')
                        ->where('up.parameters_id','=',1);
                    })
                    ->leftJoin('disciplines', 'disciplines.id', '=', 'article_ret.discipline_id')
                    ->leftJoin('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->join('code_developer as codev',function ($join)
                    {
                        $join->on('art.id_code_dev','=','codev.id');
                    })
                    ->select([
                        'dcp.display_name as discipline_name',
                        'disciplines.code as codigo_disciplina',
                        'article_ret.discipline_id as discipline_id',
                        'article_ret.id as article_req_id',
                        'tran.value as value',
                        'tran.id as transaction_id',
                        'up.value as nome_creador',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'trant_receipts.created_at as created_at_arti',
                        'tran.data_from as data_from',
                        'tran.updated_at as updated_at',
                        'trant_receipts.path as path',
                        'trant_receipts.code as code'
                    ])
                    ->whereIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->where('trant_receipts.code', '!=', null)
                    ->where('tran.data_from', '=','Estorno')
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get()
                ->groupBy('transaction_id');

                $modelo=collect($modelo)->map(function ($item){ 
                    foreach ($item as $key => $value) {
                        $array=null;
                        $code=null;
                        if ($value->path!=null) {
                            $array=explode("-",$value->path);
                             $code=explode(".",$array[2]);
                             $value->{'code_recibo'} = $array[1].'-'.$code[0];
                         }
                         else{
                           $value->{'code_recibo'} ="Erro criação";  
                         }
                    }
                    return $item;
                });
                if (!$modelo->isEmpty()) {
                    $totalValorTrans=collect($modelo)->map(function ($item) use($object,$totalValorTrans)
                    { 
                        foreach ($item as $key => $value) {
                            if (empty($object)){
                                $object []= $value->code_recibo; 
                                $totalValorTrans+=$value->value;

                            }
                            elseif(in_array($value->code_recibo,$object)){

                            }
                            else{
                                $object []= $value->code_recibo;
                                $totalValorTrans+=$value->value;
                            }
                        }
                        return $totalValorTrans;
                    });
                }else{}
               

                $disciplines = DB::table('articles as art')
                    ->join('article_requests','article_requests.article_id','=','art.id')
                    ->join('disciplines', 'disciplines.id', '=', 'article_requests.discipline_id')
                    ->join('disciplines_translations as dcp', function ($join) {
                        $join->on('dcp.discipline_id', '=', 'disciplines.id');
                        $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('dcp.active', '=', DB::raw(true));
                    })
                    ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->select([
                        'article_requests.id as article_req_id',
                        'dcp.display_name as discipline_name',
                        'disciplines.code as codigo_disciplina',
                        'article_requests.discipline_id as discipline_id',
                        'ct.display_name as course_name',
                        'dcp.abbreviation as abbreviation'
                    ])
                    ->whereNull('article_requests.deleted_at')
                    ->whereNull('article_requests.deleted_by')
                    ->whereNull('art.deleted_by')
                    ->where('article_requests.user_id',$userId)
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();

                $qdt_disciplines=count($disciplines);

                $metrics = DB::table('articles as art')
                            ->join('article_requests','article_requests.article_id','=','art.id')
                            ->join('metricas', 'metricas.id', '=', 'article_requests.metric_id')
                            ->select([
                                'article_requests.id as article_req_id',
                                'article_requests.metric_id as metric_id',
                                'metricas.nome as nome'
                            ])
                            ->whereNull('article_requests.deleted_at')
                            ->whereNull('article_requests.deleted_by')
                            ->whereNull('art.deleted_by')
                            ->where('article_requests.user_id',$userId)
                            ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                        ->get();

                
                  $consultArt= DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })

                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })

                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    // ->join('code_developer as codev',function ($join)
                    // {
                    //     $join->on('art.id_code_dev','=','codev.id');
                    // })
                    ->select([
                        'article_ret.id as article_req_id',
                        'tran.id as transaction_id',
                        'tran.type as trans_type',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'trant_receipts.created_at as created_at_arti',
                        'tran.data_from as data_from',
                        'trant_receipts.code as code'
                    ])
                    // ->whereIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->whereNull('article_ret.deleted_at')
                    ->whereNull('article_ret.deleted_by')
                    ->whereNull('tran.deleted_at')
                    ->where('tran.type','!=','debit')
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    // ->orderBy('tran.id', 'ASC')
                    // ->orderBy('trant_receipts.code', 'ASC')
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
                $i=0;
                $collet=collect($consultArt)->map(function($item){
                    return $item->article_req_id;
                    
                });
        
                    
                        
                 $consultRecibos = DB::table('articles as art')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as article_ret', function ($join) {
                        $join->on('art.id', '=', 'article_ret.article_id');
                    })
                    ->join('transaction_article_requests as trans_artic_req', function ($join) {
                        $join->on('article_ret.id', '=', 'trans_artic_req.article_request_id');
                    })

                    ->join('transactions as tran', function ($join) {
                        $join->on('trans_artic_req.transaction_id', '=', 'tran.id');
                    })

                    ->leftJoin('transaction_receipts as trant_receipts', function ($join) {
                        $join->on('tran.id', '=', 'trant_receipts.transaction_id');
                    })
                    ->leftJoin('historic_user_balance as historic_saldo',function ($join){
                        $join->on('tran.id','=','historic_saldo.id_transaction');
                    })
                    // ->join('code_developer as codev',function ($join)
                    // {
                    //     $join->on('art.id_code_dev','=','codev.id');
                    // })
                    ->select([
                        'article_ret.id as article_req_id',
                        'tran.id as transaction_id',
                        'tran.type as trans_type',
                        'historic_saldo.valor_credit as valor_credit',
                        'at.display_name as article_name',
                        'article_ret.year as article_year',
                        'article_ret.month as article_month',
                        'article_ret.base_value as base_value',
                        'article_ret.discipline_id as art_idDisciplina',
                        'article_ret.meta as meta',
                        'article_ret.extra_fees_value as extra_fees_value',
                        'article_ret.status as status',
                        'tran.data_from as data_from',
                        'trant_receipts.code as code'
                    ])
                    // ->whereIn('codev.code',['propina_finalista','confirm','trabalho_fim_curso'])
                    ->where('article_ret.user_id', $userId)
                    ->where('tran.type', '=', 'debit')
                    ->whereNull('article_ret.deleted_at')
                    ->whereNull('article_ret.deleted_by')
                    ->whereNull('tran.deleted_at')
                    ->whereNotin('trans_artic_req.article_request_id',$collet) 
                    ->orderBy('article_ret.year', 'ASC')
                    ->orderBy('article_ret.month', 'ASC')
                    // ->orderBy('tran.id', 'ASC')
                    // ->orderBy('trant_receipts.code', 'ASC')
                    ->whereBetween('art.created_at', [$lectiveYearSelected->start_date, $lectiveYearSelected->end_date])
                ->get();
                //   $model= $consultRecibos->merge($consultArt);
                $model= $consultArt->merge($consultRecibos);

                $qdt_model=count($model);

                // esta consulta vai verificar se este alguma regra implementada, tanto faze regra por curso o no ambito geral por anolectivo.
                $getRegraImplementada=DB::table('artcles_rules as art_rule')
                    ->join('articles as art','art.id','=','art_rule.id_articles')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'art.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->where('art_rule.id_articles','=',null)
                    ->where('art_rule.deleted_by','=',null)
                    ->where('art_rule.ano_lectivo','=',$lectiveYearSelected->id)
                    ->select([
                        'art_rule.id as id_ruleArtc',
                        'art_rule.valor as valor',
                        'art_rule.mes as mes',
                        'art_rule.ano_lectivo as ano_lectivo',
                        'art_rule.created_at as created_at',
                        'at.display_name as display_name'
                    ])
                ->get();
                
                $getgetRegraImplementada[]=$getRegraImplementada;
                $getdisciplines[]=$disciplines;
                $getmodel[]=$model;
                $getmodelo[]=$modelo;

            }       

            // return $getmodelo;
                
            $getRegraImplementada=[];
            $disciplines=[];
            $modelo=[];
            $model=[];
            $array=[];
            $arrayArticle=[];
            foreach ($getmodel as $key => $item) {
                foreach ($item as $key => $value) {
                    // if (!in_array($value->article_req_id,$arrayArticle)) {
                        $model[]=$value;
                        $arrayArticle[]=$value->article_req_id;
                    // }
                       
                }    
            }

              $array=[];
            foreach ($getmodelo as $key => $item) {
                foreach ($item as $id_transation => $value) {
                    if (!in_array($id_transation,$array)) {
                        $modelo[]=$value;
                        $array[]=$id_transation;
                    }
                       
                }    
            }

            $array=[];
            foreach ($getdisciplines as $key => $item) {
                foreach ($item as $key => $value) {
                    if (!in_array($value->article_req_id,$array)) {
                        $disciplines[]=$value;
                        $array[]=$value->article_req_id;
                    }
                       
                }    
            }

            $array=[];
            foreach ($getgetRegraImplementada as $key => $item) {
                foreach ($item as $key => $value) {
                    if (!in_array($value->id_ruleArtc,$array)) {
                        $getRegraImplementada[]=$value;
                        $array[]=$value->id_ruleArtc;
                    }
                       
                }    
            }


            


            if (count($getRegraImplementada)>0) {
                foreach ($getRegraImplementada as $key => $value) {
                    $arrayMonth_getRegraImplementada[]=$value->mes;
                }
            }else{

                $getRegraImplementEmolu=DB::table('artcles_rules as art_rule')
                    ->join('articles as art','art.id','=','art_rule.id_articles')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'art.id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                    })
                    ->join('article_requests as art_req',function($join){
                        $join->on('art_req.article_id','=','art_rule.id_articles');
                        $join->on('art_req.month','=','art_rule.mes');
                    })
                    ->where('art_rule.id_articles','!=',null)
                    ->where('art_req.user_id', $userId)
                    ->where('art_rule.deleted_by','=',null)
                    ->whereNull('art_req.deleted_at')
                    ->whereIn('art_rule.id',$arrayArticle)
                    // ->where('art_rule.ano_lectivo','=',$lectiveYearSelected->id)
                    ->select([
                        'art_req.id as id_art_req',
                        'art_rule.id_articles as id_articles',
                        'art_rule.id as id_ruleArtc',
                        'art_rule.valor as valor',
                        'art_rule.mes as mes',
                        'art_rule.ano_lectivo as ano_lectivo',
                        'art_rule.created_at as created_at',
                        'at.display_name as display_name'
                    ])
                ->get();

                if (count($getRegraImplementEmolu)>0) {
                    foreach ($getRegraImplementEmolu as $key => $value) {
                        $arrayMonth_getRegraImplementEmolu[]=$value->mes;
                    }   
                }
            }  
            $user = User::whereId($userId)->first();

            $data=[
                'arrayMonth_getRegraImplementada'=>$arrayMonth_getRegraImplementada,
                'arrayMonth_getRegraImplementEmolu'=>$arrayMonth_getRegraImplementEmolu,
                'getRegraImplementEmolu'=>$getRegraImplementEmolu,
                'getRegraImplementada'=>$getRegraImplementada,
                'disciplines'=>$disciplines,
                'model'=>$model,
                'modelo'=>$modelo,
                'metrics'=>$metrics,
                'user'=> auth()->user() ?? $user
            ];

            $detalheEstorno=[
                'totalValorTrans'=>$totalValorTrans,
                'data_anolectivo'=>"Outros emolumentos",
            ];
            $qdt_model=count($model);
            if ($qdt_model>0 || $qdt_disciplines>0) {
                $view = view("Payments::requests.table")->with($data)->render();
                $html_view = view("Payments::requests.table-estorno")->with($data)->render();
                // return response()->json($data);
                return response()->json(['html'=>$view,'data_html'=>$html_view,'detalheEstorno'=>$detalheEstorno,'data'=>$data]);
            } else {
                return response()->json(array('data'=>false));
            }
        } catch (Exception | Throwable $e) {
            // logError($e);
            return $e;
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }
    
     public function user_requests($id_matricula)
    {
        // return $id_student;
        try {
            $auth_student=null;
            if (auth()->user()->hasRole('candidado-a-estudante') || !auth()->user()->can('manage-requests-others')) {
                 $auth_student=auth()->user()->id;
            } else {}

             $usuarios_cargos = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
                ->leftJoin('user_parameters as user_namePar',function($join){
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                    ->where('user_namePar.parameters_id',1);
                }) 
                ->leftJoin('user_parameters as numb_mecanografico',function($join){
                    $join->on('numb_mecanografico.users_id', '=', 'usuario.id')
                    ->where('numb_mecanografico.parameters_id',19);
                }) 
            ->where('cargo_traducao.active',1)
            ->where('cargo_traducao.language_id',1)
            ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
            ->whereIn("cargo_traducao.role_id",[6,15])
            ->select([
                'usuario.name as nome',
                'user_namePar.value as nome_usuario',
                'numb_mecanografico.value as numb_mecanografico',
                'usuario.email as email',
                'usuario.id as id'
                ])
                ->when($auth_student!=null, function($query)use($auth_student){
                    $query->where('usuario.id',$auth_student);
                })
            ->orderBy('usuario.name','ASC')
            ->get()
            ->map(function ($student)
            {
                $name_student = $student->nome_usuario== ""  ? $student->nome  : $student->nome_usuario;
                $n_mecanografico = $student->numb_mecanografico== ""  ? "0000"   : $student->numb_mecanografico;
                $student->{'display_name'} = $name_student . " #". $n_mecanografico . " " ."(".$student->email .")";
                strtr(
                    utf8_decode( $student->display_name),
                    utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
                return $student;
            }); 
             

            $id_student=DB::table('matriculations')
            ->select([
                'matriculations.user_id as id_student'])
            ->where('matriculations.id','=',$id_matricula)
            ->first();
            $id_student=$id_student->id_student;
            
            $users = auth()->user()->can('manage-requests-others') ? $usuarios_cargos : $usuarios_cargos;
            $data = compact('users','id_matricula','id_student');

            return view("Payments::requests.index")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
        
    }
    
    public function user_requestsDisciplina($semestre_disciplina)
    {
        $lista_Month=[];
        $ordem_Month=[];
        $desor_Month=[];
        $getLocalizedMonths=getLocalizedMonths();
        foreach ($getLocalizedMonths as $key => $value) {
            if ($value['id']>7 && $value['id']<10) {
            }else{
                $lista_Month[]=$value;
            }
        }
        foreach ($lista_Month as $index => $item) {
            if ($item['id']>9) {
                $ordem_Month[]=$item;
            } else {
                $desor_Month[]=$item;
            }
        }
        foreach ($desor_Month as $indexInArray => $element) {
            $ordem_Month[]=$element;
        }
        return response()->json(['data'=>$ordem_Month]);
    }
    
    
     
}


