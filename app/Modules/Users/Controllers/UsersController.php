<?php

namespace App\Modules\Users\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Department;
use App\Modules\GA\Models\Discipline;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Users\Models\ParameterGroup;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\TeacherClass;
use App\Modules\Users\Models\User;
use App\Modules\Users\Models\UserCandidate;
use App\Modules\Users\Models\UserParameter;
use App\Modules\Users\Requests\UserRequest;
use Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Log;
use PdfMerger;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Storage;
use Throwable;
use Toastr;
use App\Model\Institution;
use App\Modules\Users\util\EnumVariable;
use App\Modules\Users\util\UserUtil;
use App\Modules\Users\util\GrauAcademicoUtil;
use App\Modules\Users\util\CategoriaProfissionalUtil;
use App\Modules\Users\Enum\ParameterEnum;
use PDF;
use App\Modules\Users\Exports\RecursosHumanosExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Model\checked;

class UsersController extends Controller
{
    public $user_fase = null;
    private $userUtil;

    public function __construct()
    {
        $this->authorizeResource(User::class, null, ['except' => ['update']]);
        $this->userUtil = new UserUtil();
    }

    public function getStudent($id_curso)
    {
        try {
            $model = User::query()
            ->whereHas('roles', function($q) {
                // $q->where('id', '!=', 15);
                 $q->where('id', '=', 6);
            })
             /*->with(['roles' => function ($q) {
                 $q->with([
                    'currentTranslation'
                ]);
             }])*/
                ->join('users as u1', 'u1.id', '=', 'users.created_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                
                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                // ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                // ->leftJoin('states', 'us.state_id', '=', 'states.id')
                // ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                // ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')
               /* ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function($join){
                    $join->on('model_has_roles.role_id', '=', 'roles.id');
                })*/
                //->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
                
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
                    'ct.display_name as course'
                    // 'states.name as state_name',
                    // 'scholarship_entity.company as company'
                    //'roles.name as roles'
                ])
                ->where('uc.courses_id',$id_curso);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::users.datatables.actions')->with('item', $item);
                })
                ->addColumn('states', function ($item) {
                    return view('Users::users.datatables.states')->with('item', $item);
                })
                ->addColumn('scholarship-entity', function($item) {
                    return view('Users::users.datatables.scholarship-entity')->with('item', $item);
                })

                // ->addColumn('roles', function ($item) {
                //     return $item->roles->map(function ($role) {
                //         return $role->currentTranslation->display_name;
                //     })->implode(", ");
                //     //return $item->roles->first()->currentTranslation->display_name;
                // })
                ->rawColumns(['actions'])
                 ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

     public function getDocente()
     {
        try {
    
            return view('Users::users.user_docente');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
     }

     public function getStaff()
     {
        try {
    
            return view('Users::users.user_staff');
        } catch (Exception | Throwable $e) {
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
     }

    public function ajaxGetDocente()
    {
        try {

            $user = auth()->user();
            $isCoordenador = is_coordenador(auth()->user());
    
           
            $model = User::query()
            ->whereHas('roles', function($q) {
                // $q->where('id', '!=', 15);
                $q->where('id', '=', 1);
            })
                ->leftJoin('users as u1', 'u1.id', '=', 'users.created_by')
                ->when($isCoordenador, function($q)use($user){
                    $courses = $this->getcoordenadorCourses($user->id);
                    
                    $q->join('user_courses as uc', 'uc.users_id', '=', 'users.id');
                    $q->whereIn('uc.courses_id',$courses);
                })
                ->when(!$isCoordenador, function($q)use($user){
                    $q->leftjoin('user_courses as uc', 'uc.users_id', '=', 'users.id');
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                
                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                ->leftJoin('states', 'us.state_id', '=', 'states.id')
                ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')

                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
                })
                 ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
                })
                ->whereNotIn("users.id",users_exemplo())
                ->select([
                    'users.id',
                    'users.email',
                    'full_name.value as nome_student',
                    'up_meca.value as matricula',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    // 'ct.display_name as course'
                    // 'states.name as state_name',
                    // 'scholarship_entity.company as company'
                    // 'roles.name as roles'
                ])
                  ->distinct()
                ;

            return Datatables::of($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::users.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    //BUSCA O DOCENTE DE ACORDO AO CURSO
    public function getDocenteCourse($id_curso)
    {
        try {
            $model = User::query()
            ->whereHas('roles', function($q) {
                // $q->where('id', '!=', 15);
                $q->where('id', '=', 1);
            })
             /*->with(['roles' => function ($q) {
                 $q->with([
                    'currentTranslation'
                ]);
             }])*/
                ->leftJoin('users as u1', 'u1.id', '=', 'users.created_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                
                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                ->leftJoin('states', 'us.state_id', '=', 'states.id')
                ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')

                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
                })
                 ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
                })
                ->select([
                    'users.*',
                    'full_name.value as nome_student',
                    'up_meca.value as matricula',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    // 'ct.display_name as course'
                    // 'states.name as state_name',
                    // 'scholarship_entity.company as company'
                    // 'roles.name as roles'
                ])                
                ->where('uc.courses_id',$id_curso);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::users.datatables.actions')->with('item', $item);
                })
                ->rawColumns(['actions'])
                 ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxGetStaff()
    {
        try {
       
            //$exemplo_ids = DB::table('view_exemplos_user')->get()->map(function($item){return $item->id;});
          
            $model = User::query()
            ->whereHas('roles', function($q) {
                //$q->where('id', '!=', 15);
                //$q->whereNotIn('id', [6,1,15,2]);
                $q->whereNotIn('id', [1,6,15]);
            })
             /*->with(['roles' => function ($q) {
                 $q->with([
                    'currentTranslation'
                ]);
             }])*/
                ->join('users as u1', 'u1.id', '=', 'users.created_by')
                // ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                // ->join('courses_translations as ct', function ($join) {
                //         $join->on('ct.courses_id', '=', 'uc.courses_id');
                //         $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                //         $join->on('ct.active', '=', DB::raw(true));
                //     })
                
               
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                ->leftJoin('states', 'us.state_id', '=', 'states.id')
                ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')
        
                
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('users.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('users as u2', 'u2.id', '=', 'full_name.created_by')
                
                
                 ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('users.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_phone', function ($join) {
                    $join->on('users.id', '=', 'up_phone.users_id')
                    ->where('up_phone.parameters_id',36);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('users.id', '=', 'up_bi.users_id')
                    ->where('up_bi.parameters_id',14);
                })                 
                ->whereNotIn("users.id",users_exemplo())
                ->select([
                    'users.*',
                    'full_name.value as nome_student',
                    'up_meca.value as matricula',
                    'up_phone.value as telefone',
                    'up_bi.value as bilhete',                    
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                     'full_name.updated_at as updated_at'
                 
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::users.datatables.actions_staff')->with('item', $item);
                })
                ->addColumn('roles', function ($item) {
                    return $item->roles->map(function ($role) {
                        return $role->currentTranslation->display_name;
                    })->implode(", ");
                    //return $item->roles->first()->currentTranslation->display_name;
                })

              
                ->rawColumns(['actions'])
                 ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }





    public function updateParametro ()
    {
        //Rotina para actualizar todos os email de indentificação institucional
       return DB::transaction(function () {
               $retur= DB::table('user_parameters')
                ->where('parameters_id',312)
                ->select(['users_id','value'])
                ->get();

            return $data=collect($retur)->map(function($item){
                      $item->users_id;
                      $email_antigo=explode("@",$item->value);
                      $mail=count($email_antigo)>1?$email_antigo:"notUp";
                      if($mail!="notUp"){
                          $dominio="@forlearn.ao";

                          $affected = DB::table('user_parameters')
                          ->where('users_id', $item->users_id)
                          ->where('parameters_id',312)
                          ->update(['value' => $mail[0].$dominio]);
                          if($affected){
                                return  $item->value."  || Bazou agora" ;

                            }else{
                                return $item->value."  || Já estava actualizado";
                            }
                        }
            });
        });
           
    }



    public function actualizarWhatsapp(Request $request)
    {
        // 1. Validação simples
        $dados = $request->validate([
            'id'        => 'required|integer|exists:users,id',
            'criterio'  => 'required|digits_between:8,12', // 945347861, etc.
        ]);

        // 2. Actualiza o campo na base de dados
        DB::table('users')
            ->where('id', $dados['id'])
            ->update(['user_whatsapp' => $dados['criterio']]);

        // 3. Redirecciona ou devolve view/JSON
        return redirect()->route('main.index')->with('sucesso', 'Whatsapp actualizado com êxito.');
    }



    public function updateUsuario()
    {
        //Rotina para actualizar todos os email de indentificação institucional
       

        try{

    

//    return  $trans=DB::transaction(function () {

          
 $retur= DB::table('users')
             ->select(['id','email','password'])
             ->where('email', 'like', '%ispm.ao%')
             ->whereNull('deleted_by')
             ->whereNull('deleted_at')
             ->get();


               $data=collect($retur)->map(function($item){
                

                      $email_antigo=explode("@",$item->email);
                      $mail=count($email_antigo)>1?$email_antigo:"notUp";
                      if($mail!="notUp"){
                        $dominio="@forlearn.ao";
                        if ($email_antigo[1]=="ispm.ao") {
                            echo $mail[0].$dominio ."</br>";
                            $consulta = DB::table('users')
                            ->where(['email' => $mail[0].$dominio])->first();

                            if(!$consulta){
                                
                                                            $affected = DB::table('users')
                                                            ->where('id', $item->id)
                                                            ->update(['email' => $mail[0].$dominio]);
                                                           
                                
                                                            if($affected){return  $item->email." || Bazou agora" ;}
                                                            else{ return $item->email." || Já estava actualizado";}
                                                          

                            }else{
                                echo "Já existe";
                            }

                        }
                        else{
                            return "não encontrou";
                        }
                     
                   }

                });
     

        // });
           
        
        

    }

        catch (Exception | Throwable $e) {
          
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
      
    }




public function getcursoIndex()
{
    try {
        $user = auth()->user();
        $isCoordenador = is_coordenador($user); 

        
        $data = Course::when($isCoordenador, function($q)use($user){
            $coordenador_id = $user->id;
            return $q->join('coordinator_course','coordinator_course.courses_id','courses.id')
                    ->where('coordinator_course.user_id', $coordenador_id);
        })
        ->join('users as u1', 'u1.id', '=', 'courses.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'courses.updated_by')
        ->leftJoin('users as u3', 'u3.id', '=', 'courses.deleted_by')
        ->leftJoin('duration_type_translations as dtt', function ($join) {
            $join->on('dtt.duration_types_id', '=', 'courses.duration_types_id');
            $join->on('dtt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dtt.active', '=', DB::raw(true));
        })
        ->leftJoin('courses_translations as ct', function ($join) {
            $join->on('ct.courses_id', '=', 'courses.id');
            $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('ct.active', '=', DB::raw(true));
        })
                ->select([
                    'courses.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'ct.display_name as nome_curso',
                    'ct.abbreviation as conta',
                    DB::raw('CONCAT(ct.display_name," ",courses.duration_value, " ", dtt.display_name) as duration')
                ])
            ->get();
      

        return response()->json(array('data'=>$data));
    } catch (Exception | Throwable $e) {
        logError($e);
        return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
    }
}




    public function index()
    {
        try {
    
            return view('Users::users.new_index');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create_user()
    {
        // return $roles = Role::with([
        //     'currentTranslation'
        // ])->where('id', '=', 6)->get();

        try {
            /*$parameters = Parameter::with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            },'options' => function ($q) { $q->with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
             }  ]); }])->get();*/  /*$parameter_groups = ParameterGroup::with(['translations' => function ($q) { $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            } ])->orderBy('order')->get();*/
            $roles = Role::with([
                'currentTranslation'
            ])->where('id', '=', 6)->first();

            $data = [
                    'action' => 'create',
                    'roles' => $roles,
                    ];
            return view('Users::users.user_create_student')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function create_user_docente()
    {
        // return $roles = Role::with([
        //     'currentTranslation'
        // ])->where('id', '=', 6)->get();

        try {
            /*$parameters = Parameter::with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            },'options' => function ($q) { $q->with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
             }  ]); }])->get();*/  /*$parameter_groups = ParameterGroup::with(['translations' => function ($q) { $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            } ])->orderBy('order')->get();*/
            $roles = Role::with([
                'currentTranslation'
            ])->where('id', '=', 1)->first();

            $data = [
                    'action' => 'create',
                    'roles' => $roles,
                    ];
            return view('Users::users.user_create_docente')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }   
    
    

    public function create_user_staff()
    {   
        // return $roles = Role::with([
        //     'currentTranslation'
        // ])->where('id', '=', 6)->get();

        try {
            /*$parameters = Parameter::with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            },'options' => function ($q) { $q->with([
            'translation' => function ($q) {
            $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
             }  ]); }])->get();*/  /*$parameter_groups = ParameterGroup::with(['translations' => function ($q) { $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            } ])->orderBy('order')->get();*/
            $hidden = [1, 6, 15];
            if(!auth()->user()->hasRole('superadmin')) array_push($hidden,2);
            $roles = Role::with([
                'currentTranslation'
            ])
            ->whereNotIn('id', $hidden)->get();
            //->where('id', '=', 1)->get();

            $data = [
                    'action' => 'create',
                    'roles' => $roles,
                    ];
            return view('Users::users.user_create_staff')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function create()
    {
        try {
                /*$parameters = Parameter::with([
                'translation' => function ($q) {
                $q->whereActive(true)->whereLangu
                ageId(LanguageHelper::getCurrent
                Language())324321232LA1233243212;
                },'options' => function ($q) { $q
                ->with(['translation' => function
                ($q) {->whereLanguageId(LanguageH
                elper::getCurren->whereLanguageId
                tLanguage())324321232LA123324321-
                }]); }])->get();*/  /*$parameterb
                _groups = ParameterGroup::with([
                'translations' =>->whereLanguage
                //function ($q) { $q->whereActiv
                e(true):getCurrentLanguage());
                }])->orderBy('order')->get();*/


            $roles = Role::with([
                'currentTranslation'
            ])->where('id', '!=', 15)->get();

            $data = [
                     'action' => 'create',
                     'roles' => $roles,
                     ];
            return view('Users::users.user')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function createCandidate()
    {
        try {
            $roles = Role::with([
                'currentTranslation'
            ])->where('id', 15)->first();

            $data = [
                'action' => 'create',
                //'parameters' => $parameters,
                //'parameter_groups' => $parameter_groups,
                'roles' => $roles,
            ];
            return view('Users::users.candidate')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }

    public function store(UserRequest $request)
    {
  
        try {
            
            DB::beginTransaction();

            // Check if it was deleted
            $user = User::withTrashed()->where('email', $request->get('email'))->first();
            if ($user) {

                // Update 
                $user->name = $request->get('name');
                $user->email = $request->get('email');
                $user->password = bcrypt($request->get('id_number'));
                $user->updated_by = auth()->user()->id;
                $user->deleted_at = null;
                $user->save();
            } else {

                // Create
                $user = User::create([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->get('id_number')),
                    'created_by' => auth()->user()->id
                ]);
                $user->save();
            }

            //******************************************************************************************************//
            // full_name
            $user_parameters[] = [
                'parameters_id' => 1,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => 2,
                'value' => $request->get('full_name')
            ];
            // id_number
            $user_parameters[] = [
                'parameters_id' => 14,
                'created_by' => auth()->user()->id ?? 0,
                'parameter_group_id' => 3,
                'value' => $request->get('id_number')
            ];

            $user->parameters()->sync($user_parameters);

            // Roles
            $user->syncRoles($request->get('roles'));

            if ((int)$request->get('roles') === 15) {
                // if user has the student candidate role

                if (!createAutomaticArticleRequest($user->id, 6, null, null)) {
                    throw new Exception('Could not create automatic access exam article request payment for new candidate');
                }

                $latestsCandidate = UserCandidate::latest()->first();
                if ($latestsCandidate && Carbon::parse($latestsCandidate->created_at)->year === Carbon::now()->year) {
                    $nextCode = 'CE' . ((int)ltrim($latestsCandidate->code, 'CE') + 1);
                } else {
                    $nextCode = 'CE' . Carbon::now()->format('y') . '0001';
                }

                UserCandidate::create([
                    'user_id' => $user->id,
                    'code' => $nextCode,
                    'created_at' => Carbon::now()
                ]);

                //candidate_email
                $user_email[] = [
                    'parameters_id' => 312,
                    'created_by' => 1 ?? 0,
                    'parameter_group_id' => 11,
                    'value' => $request->get('email')
                ];
                $user->parameters()->attach($user_email);

            }

            DB::commit();


            if ((int)$request->get('roles') === 15) {
                // Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
                $user = User::whereId($user->id)->first();
                return $this->fetch($user, 'edit');
            }
            // Success message
            Toastr::success(__('Users::users.store_success_message'), __('toastr.success'));
            if ((int)$request->roles === 6) {                
                return redirect()->route('users.index');
            } 
            if ((int)$request->roles === 1) {                
                // return redirect()->route('users.user_docente');
                return view('Users::users.user_docente');
            } 
            if (((int)$request->roles != 6) && ((int)$request->roles != 1)) {
                return view('Users::users.user_staff');
                //return redirect()->route('users.show_user_staff');
            }
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        try {
            return $this->fetch($user, 'show');
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(User $user)
    {
        try {
            return $fetch=$this->fetch($user, 'edit');
        } catch (Exception | Throwable $e) {
            dd($e);
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {

        try {
         
            if ($request->has('attachment_parameters')) {
                $files = $request->file('attachment_parameters');
                if (is_array($files)) {
                    $extensions = ["JPG","PNG"];
                    foreach ($files as $index_parameter_group => $parameter) {
                        foreach ($parameter as $index_parameter => $file) {
                            $filename = $file->getClientOriginalName();
                            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                            // return $index_parameter;
                           // return
                           if( $index_parameter == 25)
                            {
                                if(!in_array(strtoupper($fileExtension), $extensions))
                                {
                                    Toastr::error("Erro com o arquivo, formato de imagem inválido, utiliza apenas os formatos (jpg,png)", __('toastr.error'));
                                    return redirect()->back();
                                }
                            }
                           
                           
                           if($index_parameter == 50 && $fileExtension != "pdf")
                            {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }

                            if($index_parameter == 54 && $fileExtension != "pdf")
                            {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }
                            if ($index_parameter == 60 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }
                            if ($index_parameter == 63 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }
                            if ($index_parameter == 61 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }
                            if ($index_parameter == 298 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }


                            if($index_parameter == 29 && $fileExtension != "pdf")
                            {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 56 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 17 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }

                            if ($index_parameter == 202 && $fileExtension != "pdf") {
                                Toastr::error("Erro com o arquivo, utiliza apenas arquivos com o formato pdf", __('toastr.error'));
                                return redirect()->back();
                            }
                        }
                    }
                }
            }

            $isCandidate = User::whereId($id)->firstOrFail();
            if (!$isCandidate->hasAnyRole(['candidado-a-estudante'])) {
                if(!$this->matriculationNumberValidation($request, $id)->isEmpty()){
                    Toastr::error("Há dados introduzidos em conflito(Contacto) com outro utilizador", __('toastr.error'));
                    return redirect()->back();
                }
                $bilheUser = $this->BINumberValidation($request, $id);
                if(!$bilheUser->isEmpty()){
                    if($bilheUser[0]->is_duplicate == 0){
                        // if(!DB::table('staff_student')->where(['id_user' => $id, 'status' => 1])->exists()){
                        //     Toastr::error("Há dados introduzidos em conflito(Bilhete de identidade) com outro utilizador", __('toastr.error'));
                        //     return redirect()->back();
                        // }else{
                          
                        // }
                        DB::table('staff_student')->updateOrInsert(['id_user' => $id ],['status' => 0]);
                    }
                } 
            }
            DB::beginTransaction();

            // Current user
            $current_user = Auth::user();

            // Find
            $user = User::whereId($id)->firstOrFail();

            // Update
            $data = [];
            
            if (!empty($request->get('name'))) {
                $data['name'] = $request->get('name');
            } 
            
            if (!empty($request->get('password'))) {
               $data['password'] = bcrypt($request->get('password'));
            }
            
            $data['updated_by'] = auth()->user()->id;
            $user->update($data);

            //Updat ve Or Create (Join) Teacher with departments
            if ($user->hasAnyRole(['staff_forlearn','teacher','coordenador-curso'])) {
                if(!empty($request->get('departments')))
                    $user->departments()->sync($request->get('departments'));
            }
            
            //Associate Coordinator to a Course
            if($user->hasAnyRole(['coordenador-curso']) && $request->has('coodinator-course')) {
                $coodinatorCourse = $request->get('coodinator-course');
                if(is_array($coodinatorCourse)){
                    DB::table('coordinator_course')->where('user_id',$user->id)->delete();
                    foreach($coodinatorCourse as $course_coor_id){
                        DB::table('coordinator_course')
                            ->updateOrInsert(
                                ['user_id' => $user->id, 'courses_id' => $course_coor_id],
                                ['courses_id' => $course_coor_id, 'courses_id' => $course_coor_id]
                            );
                    }
                }else{
                    DB::table('coordinator_course')->updateOrInsert(
                        ['user_id' => $user->id],
                        ['courses_id' => $coodinatorCourse]
                    );
                }
            }

            if($user->hasAnyRole(['coordenador-curso-profissional']) && $request->has('coodinator-special-course')) {
                $coodinatorSpecialCourse = $request->get('coodinator-special-course');
                if(is_array($coodinatorSpecialCourse)){
                  
                    DB::table('coordinator_special_course')->where('user_id',$user->id)->delete();
                    foreach($coodinatorSpecialCourse as $course_coor_id){
                        DB::table('coordinator_special_course')
                            ->updateOrInsert(
                                ['user_id' => $user->id, 'courses_id' => $course_coor_id],
                                ['courses_id' => $course_coor_id, 'courses_id' => $course_coor_id]
                            );
                    }
                }else{
                  
                    DB::table('coordinator_special_course')->updateOrInsert(
                        ['user_id' => $user->id],
                        ['courses_id' => $coodinatorSpecialCourse]
                    );
                }
            }

            
            // Parameters
            $user_parameters = [];
            if ($request->has('attachment_parameters')) {
                $updated_parameters = [];

                $files = $request->file('attachment_parameters');
                if (is_array($files)) {
                    foreach ($files as $index_parameter_group => $parameter) {
                        foreach ($parameter as $index_parameter => $file) {
                            $filename = $user->id . '_file_' . $index_parameter . '_' . $file->getClientOriginalName();

                            $file->storeAs('attachment', $filename);

                            $user_parameters[$index_parameter] = [
                                'parameters_id' => $index_parameter,
                                'created_by' => $current_user->id ?? 0,
                                'parameter_group_id' => $index_parameter_group,
                                'value' => $filename
                            ];

                            // Skip this parameter/group combination, since it was updated
                            $updated_parameters[] = [
                                'parameters_id' => $index_parameter,
                                'parameter_group_id' => $index_parameter_group,
                            ];
                            
                            if($index_parameter == 25){
                                $user->update(["image" => $filename]);
                            }                            
                            
                        }
                    }
                }
            }

            if ($request->has('parameters')) {
                foreach ($request->get('parameters') as $index_parameter_group => $parameters) {
                    foreach ($parameters as $index_parameter => $parameter) {
                        $value = is_array($parameter) ? implode(',', $parameter) : $parameter ?? '';

                        // If it was an updated file, delete it and skip this parameter
                        if (!empty($updated_parameters)) {
                            foreach ($updated_parameters as $updated_parameter) {
                                if ($index_parameter_group === $updated_parameter['parameter_group_id'] && $index_parameter === $updated_parameter['parameters_id']) {
                                    continue 2;
                                }
                            }
                        }

                        $user_parameters[$index_parameter]= [
                            'parameters_id' => $index_parameter,
                            'created_by' => $current_user->id ?? 0,
                            'parameter_group_id' => $index_parameter_group,
                            'value' => $value
                        ];


                        // if (!$isCandidate->hasAnyRole(['candidado-a-estudante'])) {
                        //     if ($index_parameter === 19) {
                        //         $findDuplicateMechanographic = UserParameter::where('parameters_id', 19)
                        //         ->where('value', $value)
                        //         ->count();

                        //         if ($findDuplicateMechanographic) {
                        //             return redirect()->back()->withErrors(['Nº de: Matrícula | Mecanográfico já existe'])->withInput();
                        //         }
                        //     }
                        // }
                    }
                }
            }
            
            // Save parameters
            if (!empty($user_parameters)) {
                $user->parameters()->syncWithoutDetaching($user_parameters);
            }
              
            $courses = [];
            // single course
            if ($request->get('course') && $user->hasAnyRole(['student'])) {
                $courses[] = (int)$request->get('course');
            }

            // multi courses
            if ($request->has('course') && $user->hasAnyRole(['teacher', 'candidado-a-estudante'])) {
                $c = $request->get('course');
                if (is_array($c)) {
                    foreach ($c as $course) {
                        $courses[] = (int)$course;
                    }
                }
            }

            if (!empty($courses)) {
                $user->courses()->sync($courses);
            }

            $classes = [];
            if ($request->has('classes') && $user->hasAnyRole(['candidado-a-estudante', 'student', 'teacher'])) {
                $cl = $request->get('classes');
                if ($cl) {
                    foreach ($cl as $class) {
                        if($class > 0) $classes[] = (int)$class;
                    }
                }
            }

            if ($request->has('classes') && $user->hasAnyRole(['teacher'])) {
                if (!empty($classes)) {
                    TeacherClass::where('user_id', $user->id)->delete();
                    for ($i=0; $i < count($classes); $i++) {
                        TeacherClass::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'class_id' => $classes[$i]
                            ],
                            [
                            'user_id' => $user->id,
                            'class_id' => $classes[$i],
                            //IMPORTANTE aqui ao editar turma tenho
                            //que passar / editar o ano civil nao o ano lectivo
                            //(2020/2021)
                            //'lective_year' => Date('Y')
                            ]
                        );
                    }
                }
            }

            if (!empty($classes)) {
                $user->classes()->sync($classes);
            }

            $disciplines = [];
            if ($request->has('disciplines')) {
                $d = $request->get('disciplines');
                if ($d) {
                    foreach ($d as $discipline) {
                        $disciplines[] = (int)$discipline;
                    }
                }
            }

            if (!empty($disciplines)) {
                $user->disciplines()->sync($disciplines);
            }
           
            //Update Or Create (Join) Students to scholarship holders
            if ($user->hasAnyRole(['student'])) {
                   
                    DB::table('scholarship_holder')
                    ->updateOrInsert(
                        ['user_id' => $id],
                        ['are_scholarship_holder' => $request->get('are_scholarship') == 1 ? 1 : 0,
                        'scholarship_entity_id' => $request->get('entity'),
                        'desconto_scholarship_holder'=>$request->get('desconto_bolseiro')
                        ]);

                        $rg = DB::table('regime_especial')
                                ->where('user_id',$id)
                                ->exists();

                        if($request->get('regime_especial') == 1 || $rg)
                        {
                            DB::table('regime_especial')
                            ->updateOrInsert(
                                ['user_id' => $id],
                                ['are_regime_especial' => $request->get('regime_especial') == 1 ? 1 : 0,
                                'rotation_id'=>$request->get('rotacao')
                                ]);
                        }

                       
                
            }
            $user_id = auth()->user()->id;
            if($request->has('email')){  
             DB::table('user_parameters')
                   ->updateOrInsert(
                        ['parameter_group_id' => 1, 'parameters_id' => 312, 'users_id' => $user->id],
                        ['value' => $request->get('email'),'created_by' => $user_id, 'updated_by' => $user_id]
                    );
                } 
            // Grau academico    
            if($request->has('id_grau_academico')){  
             DB::table('user_parameters')
                    ->updateOrInsert(
                         ['parameters_id' => ParameterEnum::GRAU_ACADEMICO, 'users_id' => $user->id],
                         ['value' => $request->get('id_grau_academico'),'created_by' => $user_id, 'updated_by' => $user_id]
                     );
            }

                     if($request->has('main-role')){
                        DB::table('user_parameters')
                        ->updateOrInsert(
                             ['parameters_id' => ParameterEnum::CARGO_PRINCIPAL, 'users_id' => $user->id],
                             ['value' => $request->get('main-role'),'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]
                         );
                     }
           

            // Categoria Profissional    
            if($request->has('id_categoria_profissional')){
                DB::table('user_parameters')
                ->updateOrInsert(
                     ['parameters_id' => ParameterEnum::CATEGORIA_PROFISSIONAL, 'users_id' => $user->id],
                     ['value' => $request->get('id_categoria_profissional'),'created_by' => $user_id, 'updated_by' => $user_id]
                 );
            }
             


            DB::commit();
            // Success message
            Toastr::success(__('Users::users.update_success_message'), __('toastr.success'));
            return redirect()->route('users.show', $id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            logError($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            
            return $e;
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Find and delete
            $user->syncPermissions([]);
            $user->syncRoles([]);

            $user->courses()->delete();
            $user->disciplines()->delete();

            $user->delete();

            $user->deleted_by = auth()->user()->id;
            $user->save();

            DB::commit();

            // Success message
            Toastr::success(__('Users::users.destroy_success_message'), __('toastr.success'));
            return redirect()->route('users.index');
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax()
    {
        try {
            $model = User::query()
            ->whereHas('roles', function($q) {
                // $q->where('id', '!=', 15);
                $q->where('id', '=', 6);
            })
             /*->with(['roles' => function ($q) {
                 $q->with([
                    'currentTranslation'
                ]);
             }])*/
                ->leftJoin('users as u1', 'u1.id', '=', 'users.created_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
                ->leftJoin('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                
                ->leftJoin('users as u2', 'u2.id', '=', 'users.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'users.deleted_by')
                // ->leftJoin('users_states as us', 'users.id', '=', 'us.user_id')
                // ->leftJoin('states', 'us.state_id', '=', 'states.id')
                // ->leftJoin('scholarship_holder', 'scholarship_holder.user_id','=','users.id')
                // ->leftJoin('scholarship_entity','scholarship_entity.id','=','scholarship_holder.scholarship_entity_id')
               /* ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function($join){
                    $join->on('model_has_roles.role_id', '=', 'roles.id');
                })*/
                //->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
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
                ->whereNotIn('users.id',users_exemplo())
                ->select([
                    'users.*',
                    'full_name.value as nome_student',
                    'up_meca.value as matricula',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'up_bi.value as n_bi',
                    // 'ct.display_name as course'
                    // 'states.name as state_name',
                    // 'scholarship_entity.company as company'
                    // 'roles.name as roles'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::users.datatables.actions')->with('item', $item);
                })
                // ->addColumn('states', function ($item) {
                //     return view('Users::users.datatables.states')->with('item', $item);
                // })
                // ->addColumn('scholarship-entity', function($item) {
                //     return view('Users::users.datatables.scholarship-entity')->with('item', $item);
                // })

                // ->addColumn('roles', function ($item) {
                //     return $item->roles->map(function ($role) {
                //         return $role->currentTranslation->display_name;
                //     })->implode(", ");
                //     //return $item->roles->first()->currentTranslation->display_name;
                // })

               /* ->addColumn('cargo', function ($item) {
                    return Str::limit(implode(', ', $item->rt->pluck('display_name')->toArray()), 20);
                })
                 ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })*/
                ->rawColumns(['actions'])
                 ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function fetch($user, $action)
    {
        try {
            // return $user;
            $parameter_groups = ParameterGroup::with([
                'currentTranslation',
                'roles',
                'parameters' => function ($q) {
                    $q->with([
                        'currentTranslation',
                        'roles',
                        'options' => function ($q) {
                            $q->with([
                                'currentTranslation',
                                'relatedParametersRecursive'
                            ]);
                        }
                    ]);
                }
            ])->orderBy('order')->get();

            // Find
          
            $user = $user->whereId($user->id)->with(
                [
                    /*'parameters.parameter.options',*/
                    'roles' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    },
                    'parameters' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'groups',
                        ]);
                    },
                    'courses',
                    'disciplines',
                    'candidate'
                ]
            )->firstOrFail();
            
           
            $user_cargo = DB::table('user_parameters as up')
            ->where('users_id','=', $user->id)
            ->where('parameters_id','=', ParameterEnum::CARGO_PRINCIPAL)
            ->join('role_translations as rt',function($join){
                    $join->on('rt.role_id','up.value')
                    ->where('active',1)
                   ;
            })
            ->select(['up.*',
            'rt.display_name',
            ])
            ->first();
            
            $userGA = DB::table('user_parameters as up')
                    ->where('users_id','=', $user->id)
                    ->where('parameters_id','=', ParameterEnum::GRAU_ACADEMICO)
                    ->join('grau_academico as ga', 'up.value', '=', 'ga.id')
                    ->first();

            $userCP = DB::table('user_parameters as up')
                    ->where('users_id','=', $user->id)
                    ->where('parameters_id','=',ParameterEnum::CATEGORIA_PROFISSIONAL)
                    ->join('categoria_profissional as cp', 'up.value', '=', 'cp.id')
                    ->first();

            $graus_academicos = DB::table('grau_academico')->get();
            $categorias_profissionais = DB::table('categoria_profissional')->get();

            $isCoordenador = is_coordenador(auth()->user()); 
            $courses = Course::when($isCoordenador, function($q){
                $c_courses = $this->getcoordenadorCourses(auth()->user()->id);
                $q->whereIn('id',$c_courses);
            })
            ->with([
                'currentTranslation'
            ])->get();

            $special_courses = DB::table('special_courses')
            ->whereNull('deleted_at')
            ->get();

           $departments = Department::with([
                'currentTranslation'
            ])->get();


            $userDepartment = Department::join('users_departments', 'users_departments.departments_id', '=', 'departments.id')
                                            ->leftJoin('department_translations as dpt', function ($join) {
                                                $join->on('dpt.departments_id', '=', 'departments.id');
                                                $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                                $join->on('dpt.active', '=', DB::raw(true));
                                            })
                                            ->where('users_departments.user_id', $user->id)
                                            ->get();

            $coordinatorCourse = DB::table('coordinator_course')
                                            ->join('courses_translations as ct', function ($join) {
                                                $join->on('ct.courses_id', '=', 'coordinator_course.courses_id');
                                                $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                                                $join->on('ct.active', '=', DB::raw(true));
                                            })
                                            ->where('user_id', $user->id)
                                            ->get();

            $coordinatorSpecialCourse = DB::table('coordinator_special_course')
                                            ->join('special_courses as sc','sc.id', '=', 'coordinator_special_course.courses_id')
                                            ->where('user_id', $user->id)
                                            ->whereNull('sc.deleted_at')
                                            ->get();
            
            $scholarship_status = DB::table('scholarship_holder')
                                            ->where('user_id', $user->id)
                                            ->first();

            $regime_especial_status = DB::table('regime_especial')
                                            ->where('user_id', $user->id)
                                            ->first();
                                            
                     $staff_status_studant = DB::table('staff_student')
                                            ->where('id_user', $user->id)
                                            ->first();

            $entitys = DB::table('scholarship_entity')
                ->get()
                ->map(function ($q) {
                    return ['id' => $q->id, 'display_name' => $q->company];
                });

                $rotacoes = DB::table('rotacao_regime_especial')
                ->get();
                
               
                
           
          $data = [
                'action' => $action,
                'user' => $user,
                'parameter_groups' => $parameter_groups,
                'courses' => $courses,
                'disciplines' => disciplinesSelect(),
                /*'roles' => $roles*/
                'departments' => $departments,
                'userDepartment' => $userDepartment,
                'scholarship_status' => $scholarship_status,
                'regime_especial_status' => $regime_especial_status,
                  'staff_status_studant' => $staff_status_studant,
                'entitys' => $entitys,
                'rotacoes' => $rotacoes,
                'coordinatorCourse' => $coordinatorCourse,
                'graus_academicos' => $graus_academicos,
                'categorias_profissionais' => $categorias_profissionais,
                'userCP' => $userCP,
                'userGA' => $userGA,
                'user_cargo' => $user_cargo,
                'coordinatorSpecialCourse' => $coordinatorSpecialCourse,
                'special_courses' => $special_courses
            ];

            return view('Users::users.user')->with($data);
        } catch (ModelNotFoundException $e) {
            return $e;
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return abort(500);
        }
    }

    private function getcoordenadorCourses($user_id){
        return DB::table('coordinator_course')
                    ->where('user_id',$user_id)
                    ->pluck('courses_id')
                    ->toArray();
    }

    public function roles($id)
    {
        try {
            // Find
            $user = User::whereId($id)->firstOrFail();

            $data = [
                'user' => $user
            ];

            return view('Users::users.roles')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function rolesAjax($id)
    {
        try {
            // Fetch the user
            $user = User::whereId($id)->firstOrFail();

            // Get ids
            $roles = $user->roles->pluck('id')->toArray();

            $model = Role::join('users as u1', 'u1.id', '=', 'roles.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'roles.updated_by')
                ->leftJoin('role_translations as rt', function ($join) {
                    $join->on('rt.role_id', '=', 'roles.id');
                    $join->on('rt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('rt.active', '=', DB::raw(true));
                })
                ->where('roles.id','!=', 2)
                ->where('roles.id', '!=',15)
                ->select([
                    'roles.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'rt.display_name'
                ]);

            // Return the datatable
            return Datatables::eloquent($model)
                ->addColumn('select', function ($item) use ($roles) {
                    return view('Users::users.datatables.select', [
                        'id' => $item->id,
                        'checked' => in_array($item->id, $roles, true),
                    ]);
                })
                ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })
                ->rawColumns(['select'])
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function rolesSave(Request $request, $id)
    {
        try {
            // Find
            $user = User::whereId($id)->firstOrFail();

            $user->syncRoles($request->get('items'));
            // $user->syncRoles(2); sempre que se precisar voltar no cargo superADMIN

            // Success message
            Toastr::success(__('Users::users.roles_success_message'), __('toastr.success'));
            return redirect()->route('users.roles', $user->id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissions($id)
    {
        try {
            // Find
            $user = User::whereId($id)->firstOrFail();

            $data = [
                'user' => $user
            ];

            return view('Users::users.permissions')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function permissionsAjax($id)
    {
        try {
            // Fetch the user
            $user = User::whereId($id)->firstOrFail();

            // Permissions
            $permissions = $user->getDirectPermissions()->pluck('id')->toArray();
            $inherited_permissions = $user->getPermissionsViaRoles()->pluck('id')->toArray();

            $model = Permission::join('users as u1', 'u1.id', '=', 'permissions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'permissions.updated_by')
                ->leftJoin('permission_translations as pt', function ($join) {
                    $join->on('pt.permission_id', '=', 'permissions.id');
                    $join->on('pt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('pt.active', '=', DB::raw(true));
                })
                ->select([
                    'permissions.*',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'pt.display_name'
                ]);

            // Return the datatable
            return Datatables::eloquent($model)
                ->addColumn('select', function ($item) use ($permissions, $inherited_permissions) {
                    return view('Users::users.datatables.select', [
                        'id' => $item->id,
                        'checked' => in_array($item->id, $permissions, true) || in_array($item->id, $inherited_permissions, true),
                        'disabled' => in_array($item->id, $inherited_permissions, true),
                        'title' => __('Users::permissions.inherited_permission'),
                    ]);
                })
                ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->updated_at);
                })
                ->editColumn('deleted_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->deleted_at);
                })
                ->rawColumns(['select'])
                ->toJson();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function permissionsSave(Request $request, $id)
    {
        try {
            // Find
            $user = User::whereId($id)->firstOrFail();

            if ($request->has('items')) {
                $user->syncPermissions($request->get('items'));
            } else {
                $user->syncPermissions([]);
            }

            // Success message
            Toastr::success(__('Users::users.permissions_success_message'), __('toastr.success'));
            return redirect()->route('users.permissions', $user->id);
        } catch (ModelNotFoundException $e) {
            Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
            Log::error($e);
            return redirect()->back() ?? abort(500);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function update_avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        //TODO: falta eliminar
        $avatarName = $user->id . '_profile_image' . '.' . request()->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('attachment', $avatarName);

        $user->image = $avatarName;
        $user->save();

        return back()
            ->with('success', 'You have successfully upload image.');
    }

    public function candidaturaswhatsapp($whatsapp){
        
        try{/*
            $isApiRequest = request()->header('X-From-API') === 'flask';
            $tokenRecebido = request()->bearerToken();
            if($isApiRequest){
                if($tokenRecebido!== env('FLASK_API_TOKEN')){
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }*/
            $UserId = DB::table('users')
                ->where('users.user_whatsapp', $whatsapp)
                ->select('users.id')
                ->first();
            $id = $UserId->id;
            if (is_null($id))  {
                    return response()->json([
                        'error' => 'Candidatura não encontrada para este número de WhatsApp.'
                    ], 404);
                }
            $request = new Request([
                'include-attachments' => true,
                'font-size' => '12',
                'view' => true,  // ou 'view' => false, conforme queres
            ]);

            $api = $isApiRequest ? 'flask' : null;

            return $this->generatePDF($id, $request, $api);
        }
        catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function generatePDF($id, Request $request, $api = null)
    {
        
         $userDepartment = Department::join('users_departments', 'users_departments.departments_id', '=', 'departments.id')
        ->leftJoin('department_translations as dpt', function ($join) {
            $join->on('dpt.departments_id', '=', 'departments.id');
            $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->on('dpt.active', '=', DB::raw(true));
        })
        ->where('users_departments.user_id', $id)
        ->first();
        
        $Departamento = isset($userDepartment->display_name)?$userDepartment->display_name:"N/A";
        
        $user = User::whereId($id)->with([
            'roles' => function ($q) {
                $q->with([
                    'currentTranslation'
                ]);
            },
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'groups'
                ]);
            },
            'courses',
            'disciplines',
            'classes' => function ($q) {
                $q->with([
                    'room' => function ($q) {
                        $q->with([
                            'currentTranslation'
                        ]);
                    }
                    ]);
            }
        ])->firstOrFail();
        $courses = Course::with([
            'currentTranslation'
        ])->get();

             //Funcionario 
       $Funcionario=DB::table('users')->where('id',$user->created_by)->first();  
        // Options
        $measurement = 'cm';
        $font_measurement = 'px';
        // $options = [
        //     'columns_per_group' => $request->get('columns-per-group'),
        //     'extension' => '.pdf',
        //     'filename' => $user->id,
        //     'margins' => [
        //         'top' => $request->get('margin-top') . $measurement,
        //         'bottom' => $request->get('margin-bottom') . $measurement,
        //         'left' => $request->get('margin-left') . $measurement,
        //         'right' => $request->get('margin-right') . $measurement,
        //     ],
        //     'orientation' => $request->get('orientation'),
        //     'paper' => $request->get('paper-size'),
        //     'font-size' => $request->get('font-size') . $font_measurement,
        // ];

         $options = [
            'columns_per_group' => "7",
            'extension' => '.pdf',
            'filename' => $user->id,
            'margins' => [
                'top' => '2mm',
                'bottom' => '12mm',
                'left' => '2mm',
                'right' => '2mm',
            ],

            'orientation' => "portrait",
            'paper' => "a4",
            'font-size' => $request["font-size"]. $font_measurement,
        ];


        $parameter_groups = ParameterGroup::with([
            'currentTranslation',
            'roles',
            'parameters' => function ($q) {
                $q->with([
                    'currentTranslation',
                    'roles',
                    'options' => function ($q) {
                        $q->with([
                            'currentTranslation',
                            'relatedParametersRecursive'
                        ]);
                    }
                ]);
            }
        ])->orderBy('order')->get();

        $institution = Institution::latest()->first();
        $titulo_documento = "FICHA DE";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 5;
        
        $foto = DB::table('user_parameters')
        ->where("users_id",$user->id)
        ->where("parameters_id",25) 
        ->where("parameter_group_id", 1) 
        ->select(["value"])
        ->first();
        if (isset($foto->value)) {
            $user->image = $foto->value;
        }
        $userApi = auth()->user();
        if ($userApi == null) {$userApi = User::find($id);}
   
      
         $data = [
            'action' => 'print',
            'user' => $user ?? $userApi,
             'Departamento'=>$Departamento,
             'Funcionario' => $Funcionario,
            'parameter_groups' => $parameter_groups,
            'date_generated' => date('d/m/Y'),
            'include_attachments' => $request->get('include-attachments'),
            'courses' => $courses,
            'options' => $options,
            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento,
            'user_fase' => $this->user_fase,
            'logotipo' => "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo,
            'fotografia' => "https://" . $_SERVER['HTTP_HOST'] . "/storage/attachment/" .  $user->image, 
        ];   

        if ($request->get('view')) {
            return view('Users::users.pdf', $data);
        }

        //return view('Users::users.pdf', $data);

        $pdf = SnappyPDF::loadView('Users::users.pdf', $data);

        $pdf->setOption('margin-top', $options['margins']['top']);
        $pdf->setOption('margin-left', $options['margins']['left']);
        $pdf->setOption('margin-bottom', $options['margins']['bottom']);
        $pdf->setOption('margin-right', $options['margins']['right']);
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);

        // Footer
        // $footer_html = view()->make('Users::users.partials.pdf_footer', compact('institution'))->render();
        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf->setOption('footer-html', $footer_html);

        $pdf->setPaper($options['paper'], $options['orientation']);

        // If we want to add attachments
        if ($request->get('include-attachments')) {
            $temp_filename = $options['filename'] . $options['extension'];

            // Create temporary pdf
            Storage::put($temp_filename, $pdf->output());

            // Merge all pdfs
            $merger = PdfMerger::init();
            $merger->addPDF(storage_path('app/public/' . $temp_filename));

            // Search all attachments
            $attachments = $user->parameters->filter(function ($item) {
                return in_array($item->type, ['file_pdf'], true);
            });
            
            // Merge all attachments
            if (count($attachments) > 0) {
                foreach ($attachments as $attachment) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);

                    $allowed_pdf_mime_types = ['application/pdf'];
                    $allowed_image_mime_types = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                    try {
                        $filename = storage_path('app/public/attachment/' . $attachment->pivot->value);
                        $mime_type = finfo_file($finfo, $filename);

                        // If its PDF
                        if (in_array($mime_type, $allowed_pdf_mime_types, true)) {
                            $merger->addPDF($filename);
                        } elseif (in_array($mime_type, $allowed_image_mime_types, true)) {
                            //TODO: convert image to pdf
                        }
                    } catch (Exception | Throwable $e) {
                        //dump($e);
                    }
                }
            }
           
            $merger->merge();

            Storage::delete($temp_filename);
           
            return $merger->save($temp_filename, 'browser');
        }
        if($api != null){

            return $pdf->stream($options['filename'] . $options['extension']);
    
        }
        
        return $pdf->stream($options['filename'] . $options['extension']);
    }

    public function exists(Request $request)
    {   
        // return $request;
        $json = [];

        if ($request->has('field') && $request->has('value')) {
            $parameter = User::where($request->get('field'), '=', $request->get('value'));

            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json(array('data'=>$json) );
    }

    public function existsParameter(Request $request)
    {
        $json = [];
        if ($request->has('field') && $request->has('value')) {
            $parameter = User::leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 14);
            })->where('u_p.value', '=', $request->get('value'));
            //anchor
            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }

    
    public function existsMecanNumber(Request $request)
    {
        $json = [];
        if ($request->has('field') && $request->has('value')) {
            $parameter = User::leftJoin('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                                        ->where('u_p.parameters_id', 19);
            })
                                 ->where('u_p.value', '=', $request->get('value'));
            //anchor
            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }


    public function coursesDisciplinesAjax(Request $request)
    {
        try {
            $courses = $request->get('courses');

            $disciplines = disciplinesSelect($courses);

            // $disciplines = disciplinesSelect($courses);

            // $classes = classesSelect($courses);
            
             $classes=DB::table('classes as t')
            ->join('lective_years as a','a.id','t.lective_year_id')
            ->leftJoin('lective_year_translations as l', function ($join) {
                $join->on('l.lective_years_id', '=', 'a.id');
                $join->on('l.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('l.active', '=', DB::raw(true));
              })

            ->whereIn('t.courses_id', $courses)
            ->select(['t.display_name','t.id','l.display_name as anoLective'])
            ->distinct()
            ->get();


            return response()->json(['disciplines' => $disciplines, 'classes' => $classes], 200);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function matriculationNumberValidation($request, $id)
    {   $contacto = isset($request->parameters[1][19]) ? $request->parameters[1][19] : ($request->parameters[6][36] ?? 0);
        if($contacto == 0) return collect([]);
        $matriculationNumber = User::leftJoin('user_parameters as u_p', function ($join) use($id){
                $join->on('users.id', '=', 'u_p.users_id')
                                        ->where('u_p.parameters_id', 19)
                                        ->where('u_p.users_id','!=', $id);
                                        // ->orWhere('u_p.parameters_id',14);
            })
                ->where('u_p.value', '=', $contacto )
                // ->orWhere('u_p.value', '=', $request->parameters[3][14])
                ->get();

            if (!empty($request->get('ignored_id'))) {
                $matriculationNumber = $matriculationNumber->whereKeyNot($request->get('ignored_id'));
            }

            return $matriculationNumber;


    }

    private function BINumberValidation($request, $id)
    {    if(!isset($request->parameters[3][14])) return collect([]);
         $biNumber = User::leftJoin('user_parameters as u_p', function ($join) use($id) {
                $join->on('users.id', '=', 'u_p.users_id')
                                         ->where('u_p.parameters_id',14)
                                         ->where('u_p.users_id','!=', $id);
            })
                ->where('u_p.value', '=', $request->parameters[3][14])
                ->get();

            if (!empty($request->get('ignored_id'))) {
                $biNumber = $biNumber->whereKeyNot($request->get('ignored_id'));
            }

            return $biNumber;
    }














    public function convertToEmail($name){
        return $this->userUtil->convertToEmail($name);
        /*
        $pieces = explode(",", $name);
        //count the quantity of name to use in email
        $lenght = strlen($pieces[0]);

        //return first and last name
        $nameLenght = count($pieces);
        $firstAndLastName = $pieces[0] ." ".  $pieces[$nameLenght - 1];

        //checar caracteres com acentuacao
        //IMPORTANTE

        $specialCharacters = [
                 "á" => "a", "à" => "a", "â" => "a", "ã" => "a" ,"Á" => "A", "À" => "A", "Â" => "A", "Ã" => "A",
                 "È" => "E", "É" => "E", "è" => "e", "é" => "e", "Ê" => "E", "ê" => "e",
                 "Ì" => "I", "Í" => "I", "ì" => "i", "í" => "i", "Î" => "I", "î" => "i",
                 "Ç" => "c", "ç" => "c",
                 "ó" =>"o", "ò" =>"o", "Ó" => "O", "Ò" => "O", "Ô" => "O", "Õ" => "O", "õ" => "o", "ô" => "o",
                 "Ù" => "U", "Ú" => "U", "ù" =>"u", "ú" =>"u", "û" => "u", "Û" => "U" ];
  
        //Ultimo email  da cadeia de palavras
        $lastEmail= strtolower(strtr($pieces[0], $specialCharacters).".". strtr($pieces[$nameLenght - 1], $specialCharacters) . EnumVariable::$CONVERT_TO_EMAIL);
   

        for ($i=0; $i <= $lenght; $i++) {
                $letter = strtr($pieces[0], $specialCharacters);
                $lastNameWithoutSpecialCharacters = strtr($pieces[$nameLenght - 1], $specialCharacters);
                $email = strtolower(substr($letter, 0, $i + 1) .".". $lastNameWithoutSpecialCharacters . EnumVariable::$CONVERT_TO_EMAIL);
    
                $checkEmail = User::where('users.email', '=', $email)->get();
    
                if ($checkEmail->isEmpty()) {
                    $email = $email;
                    break;
                }else if($lastEmail== $email){
                    //se o email for o último e já existe.
                    $checkEmail_Point = User::where('users.email', '=', $email)->get();
                    $rand=rand(10,1000);
                    $novoEmail= strtolower(strtr($pieces[0], $specialCharacters).".". strtr($pieces[$nameLenght - 1], $specialCharacters) .$rand. EnumVariable::$CONVERT_TO_EMAIL);
                    if(!$checkEmail_Point->isEmpty()){ $email=$novoEmail ; }
                }
                
        }
        $data = ['name' => $firstAndLastName, 'email' => $email ];
        return  response()->json($data);
        */
    }
    
    


    private function verifyInStaffStudentByBi($bi){
        $element = DB::table('staff_student as ss')
                 ->join('user_parameters as up','up.users_id','=','ss.id_user')
                 ->where('ss.status',1)
                 ->where('ss.is_candidato',0)
                 ->whereNull('ss.id_candidato')
                 ->where('up.parameters_id',14)
                 ->where('up.value',$bi)
                 ->select('ss.id_user')
                 ->first(); 
        return $element;
    }

    private function getUserStaffBi($id){
        $query= DB::table('users as u')
                  ->join('user_parameters as up','up.users_id','=','u.id')
                  ->where('u.id',$id)
                  ->where('up.parameters_id',1)
                  ->whereNull('u.deleted_by')->whereNull('up.deleted_by')
                  ->select('up.value','u.name')
                  ->first();
         return (Object)[
            "status" => 1,
            "object" => (Object)[
                "nome_longo" => $query->value,
                "nome_curto" => $query->name
            ]
        ];                 
    }

    public function getValidationNewNumberBI($valorBi)
    {   try{
            
            $status = $this->verifyInStaffStudentByBi($valorBi);
            //dd($status);
            if(isset($status->id_user)) 
                return response()->json($this->getUserStaffBi($status->id_user));
            
            $getNumberBi = User::join('user_parameters as u_p', function ($join) {
                $join->on('users.id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id',14);
            })->where('u_p.value', '=', $valorBi)
              ->get();
                $sit = (Object)[
                    "status" => !$getNumberBi->isEmpty()  ?  2 : 3
                ];
            return response()->json($sit);
            //return    !$getNumberBi->isEmpty()  ? response()->json(2) :  response()->json(3);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
        
        
    }

    public function create_docente_student(Request $request){
        try{
            $obj = DB::table('staff_student')->where('id_user',$request->user_id)->first();
            if(!isset($obj->id))
                DB::insert('insert into staff_student values (DEFAULT,?, ?, ?,DEFAULT)', [$request->user_id, 1,false]);
            else
                DB::update('update staff_student set status=? where id_user=? limit 1', [$obj->is_candidato ? 2 : 1, $request->user_id]);  
            return response()->json(true);
        }catch(Exception $err){ 
            return response()->json(false);
        }
    }

    public function deleta_docente_student(Request $request){
        try{
            $rec = false;
            $obj = DB::table('staff_student')->where('id_user',$request->user_id)->first();
            if(isset($obj->id))
                 $rec = DB::update('update staff_student set status=? where id_user=? limit 1', [0, $request->user_id]);
            return response()->json($rec);
        }catch(Exception $err){ 
            return response()->json(false);
        }
    }

    public function update_docente_student(Request $request){
        try{
            $obj = DB::table('staff_student')->where('id_user',$request->user_id)->first();
            if($obj->status > 1){
                DB::update('update staff_student set status=? where id_user=?', [0, $request->user_id]);
            }
            return response()->json(true);
        }catch(Exception $err){
            return response()->json(false);
        }
    }

    public function verifyUserInChangeCourse($id){

        $obj = DB::table('tb_user_change_course as tucc')
                    ->join('matriculations as mt', 'mt.user_id', '=', 'tucc.user_id')
                    ->join('user_courses as uc','tucc.user_id','=','uc.users_id')
                    ->join('courses_translations as ct','uc.courses_id',"=",'ct.courses_id')
                    ->where('ct.active',1)
                    ->whereNull('ct.deleted_at')
                    ->where('mt.user_id',$id)
                    ->select('tucc.id','uc.courses_id','ct.display_name')
                    ->first();

        if(isset($obj->id)) return response()->json((Object)["status"  => true,"body" => $obj]);

            $obj = DB::table('user_courses as uc')
            ->join('matriculations as mt', 'mt.user_id', '=', 'uc.users_id')
            ->join('courses_translations as ct','uc.courses_id',"=",'ct.courses_id')
            ->where('ct.active',1)
            ->whereNull('ct.deleted_at')
            ->where('uc.users_id',$id)
            ->select('uc.courses_id','ct.display_name')
            ->first();

        return response()->json((Object)["status"  => false, "body" => $obj]);
    }


  public function generateDocentePDF()
  {
    try {
      $model = User::query()
        ->whereHas('roles', function ($q) {
          $q->where('id', '=', 1);  // Assumindo que 1 é o ID do papel de docente
        })
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
        ->leftJoin('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'uc.courses_id')
            ->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
            ->on('ct.active', '=', DB::raw(true));
        })
        ->leftJoin('user_parameters as full_name', function ($join) {
          $join->on('users.id', '=', 'full_name.users_id')
            ->where('full_name.parameters_id', 1);
        })
        ->leftJoin('user_parameters as up_meca', function ($join) {
          $join->on('users.id', '=', 'up_meca.users_id')
            ->where('up_meca.parameters_id', 19);
        })
        ->select([
          'users.id',
          'users.email',
          'up_meca.value as matricula',
          'full_name.value as nome',
          'ct.display_name as course_name',
          'uc.courses_id'
        ])
        ->orderByRaw('uc.courses_id IS NULL') // Coloca os usuários sem curso por último
        ->orderBy('uc.courses_id')
        ->orderBy('full_name.value')
        ->get()
        ->groupBy('id');  // Agrupa os resultados pelo ID do docente

      // Consulta para obter a lista de todos os cursos
      $cursos = Course::join('courses_translations as ct', function ($join) {
        $join->on('ct.courses_id', '=', 'courses.id')
          ->where('ct.language_id', LanguageHelper::getCurrentLanguage())
          ->where('ct.active', true);
      })
        ->pluck('ct.abbreviation', 'courses.id');


      // Obtém as informações da instituição (ou pode ser removido se não for necessário)
      $institution = DB::table('institutions')->latest()->first();

      // Título e documento gerado
      $titulo_documento = "Relatório de Docentes";
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      // Gerando o PDF
      $pdf = PDF::loadView(
        'Users::users.pdf.relatorio-docente-pdf', // Certifique-se de criar essa view com o layout adequado
        compact(
          'cursos',
          'model',
          'institution',
          'titulo_documento',
          'documentoGerado_documento'
        )
      );

      // Definindo as opções do PDF
      $pdf->setOption('margin-top', '2.3mm');
      $pdf->setOption('margin-left', '5mm');
      $pdf->setOption('margin-bottom', '6.4mm');
      $pdf->setOption('margin-right', '5mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'landscape');

      $pdf_name = "Lista_de_Docentes_" . date("Y-m-d");

      // Retornando o PDF para ser visualizado
      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception | Throwable $e) {
    
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }


  public function generateUserPDF($id_curso = null)
  {
    try {
      // If no course is selected, set a default course ID
      if (is_null($id_curso)) {
        Toastr::error("Selecione um curso");
        return redirect()->back(); // Default to course ID 11, or set another default as needed
      } else {
        $id_curso = intval($id_curso);

      }

      // Building the query
      $users = User::query()
        ->whereHas('roles', function ($q) {
          $q->where('id', '=', 6); // Assuming 6 is the role ID for students
        })
        ->join('users as u1', 'u1.id', '=', 'users.created_by')
        ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'users.id')
        ->join('courses_translations as ct', function ($join) {
          $join->on('ct.courses_id', '=', 'uc.courses_id')
            ->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()))
            ->on('ct.active', '=', DB::raw(true));
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
          'up_bi.value as n_bi', // Adding course name to the PDF data
          'ct.display_name as course_name'
        ])
        ->where('uc.courses_id', $id_curso) // Filtering by selected course ID
        ->orderBy('nome_student', 'ASC')
        ->get();

      // Get the course name from the first user


      // Check if any records are returned
      if ($users->isEmpty()) {
        Toastr::error("Curso sem dados");
        return redirect()->back();
      }

      // Fetch the institution's details
      $institution = DB::table('institutions')->latest()->first();

      // Defining data for the PDF
      $titulo_documento = "" . $users->first()->course_name . "";
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      // Generating the PDF
      $pdf = PDF::loadView(
        'Users::users.pdf.relatorio-pdf',
        compact(
          'users',
          'institution',
          'titulo_documento',
          'documentoGerado_documento'
        )
      );

      $pdf->setOption('margin-top', '2.3mm');
      $pdf->setOption('margin-left', '5mm');
      $pdf->setOption('margin-bottom', '6.4mm');
      $pdf->setOption('margin-right', '5mm');
      $pdf->setOption('enable-javascript', true);
      $pdf->setOption('debug-javascript', true);
      $pdf->setOption('javascript-delay', 1000);
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'portrait');

      $pdf_name = "Tabela dos Estudantes_" . "_" . date("Y-m-d");

      return $pdf->stream($pdf_name . '.pdf');
    } catch (Exception | Throwable $e) {
      Log::error($e);
      return response()->json($e->getMessage(), 500);
    }
  }


  public function generateDocenteCursoPDF($id)
  {
    try {
      // return $user;
      $user = User::whereId($id)->with([
        'user_parameters',
        'courses',
        'disciplines',
        'classes'
      ])->firstOrFail();

      $userDepartment = Department::join('users_departments', 'users_departments.departments_id', '=', 'departments.id')
        ->leftJoin('department_translations as dpt', function ($join) {
          $join->on('dpt.departments_id', '=', 'departments.id');
          $join->on('dpt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
          $join->on('dpt.active', '=', DB::raw(true));
        })
        ->where('users_departments.user_id', $user->id)
        ->get();

      // Obtém as informações da instituição (ou pode ser removido se não for necessário)
      $institution = DB::table('institutions')->latest()->first();

      // Título e documento gerado
      $titulo_documento = "Relatório de Docentes";
      $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

      // Gerando o PDF
      $pdf = PDF::loadView(
        'Users::users.pdf.relatorio-docente-curso-pdf', // Certifique-se de criar essa view com o layout adequado
        compact(
          'user',
          'userDepartment',
          'institution',
          'titulo_documento',
          'documentoGerado_documento'
        )
      );

      // Definindo as opções do PDF
      $pdf->setOption('margin-top', '1mm');
      $pdf->setOption('margin-left', '1mm');
      $pdf->setOption('margin-bottom', '5mm');
      $pdf->setOption('margin-right', '1mm');
      $pdf->setOption('enable-smart-shrinking', true);
      $pdf->setOption('no-stop-slow-scripts', true);
      $pdf->setPaper('a4', 'portrait');

      $pdf_name = "HdD_Docente_" . $user->name;

      // Retornando o PDF para ser visualizado
      return $pdf->stream($pdf_name . '.pdf');

    } catch (ModelNotFoundException $e) {
      return $e;
      Toastr::error(__('Users::users.not_found_message'), __('toastr.error'));
      Log::error($e);
      return redirect()->back() ?? abort(500);
    } catch (Exception | Throwable $e) {
      return $e;
      Log::error($e);
      return abort(500);
    }
  }

  public function update_password()
    {
        
        try {
            
            DB::beginTransaction();

            $users = DB::table('users as u')
                        ->whereNull('u.deleted_at')
                        ->whereNull('u.deleted_by')
                        ->join('model_has_roles as mr','mr.model_id','u.id')
                        ->where('mr.role_id',6)
                        ->join('user_parameters as up','up.users_id','u.id')
                        ->where('up.parameters_id',14)
                        ->join('user_courses as uc','uc.users_id','u.id')
                        ->where('uc.courses_id',8)
                        ->select(['u.id as id','up.value as bi'])
                        ->distinct('u.id')
                        ->get();
                        
        
            $users->each(function($user){
                    DB::table('users')
                    ->where('id',$user->id)
                    ->update([
                        'password' => bcrypt($user->bi),
                        'updated_at' => Carbon::now(),
                        'updated_by' => 845
                    ]);
            });

            

            DB::commit();

            dd('sucesso');

        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    
    }

    public function generateRhRpa(){
        try{
        
          return Excel::download(new RecursosHumanosExport(), 'rh-rpa.xlsx');
        
        } catch (Exception | Throwable $e) {    
            dd($e);
            Log::error($e);
            Toastr::error($e->getMessage(), __('toastr.error'));
            return redirect()->back();
        }
    }


}

