<?php

namespace App\Modules\RH\Controllers;


use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;


use Cache;
use App\Helpers\LanguageHelper;
use App\Modules\Cms\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Role;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleType;

use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use Auth;
use App\Model\Institution;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;
use Illuminate\Support\Facades\Storage;

class RhApiController extends Controller
{


    public function lista_user_api()
    {
        // VALIDAÇÃO DO SUSÁRIO
        $api_user = DB::table('api_users')
            ->leftJoin('users as user1', 'user1.id', '=', 'api_users.created_by')
            ->leftJoin('user_parameters as full1', function ($join) {
                $join->on('user1.id', '=', 'full1.users_id')
                    ->where('full1.parameters_id', 1);
            })
            ->leftJoin('users as user2', 'user2.id', '=', 'api_users.update_by')
            ->leftJoin('user_parameters as full2', function ($join) {
                $join->on('user2.id', '=', 'full2.users_id')
                    ->where('full2.parameters_id', 1);
            })
            ->whereNull('api_users.deleted_at')
            ->whereNull('api_users.deleted_by')
            ->select([
                'api_users.id as id',
                'api_users.token as token',
                'api_users.keey as keey',
                'api_users.name as name',
                'api_users.email as email',
                'api_users.telefone_principal as telefone_principal',
                'api_users.telefone_altenativo as telefone_altenativo',
                'api_users.created_at as created_at',
                'full1.value as created_by',
                'api_users.update_at as update_at',
                'full2.value as update_by'
            ])

            ->get();

        return DataTables::of($api_user)
            ->addColumn('actions', function ($item) {
                return view('RH::api_user.datatables.actions', compact('item'));
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->make(true);

        // return response()->json($api_user);
    }


    public function delete_user_api(Request $request)
    {

        try {

            $delete_user_api = DB::table('api_users as api_user')
                ->where('api_user.id', '=', $request->id_api_user)
                ->whereNull('api_user.deleted_at')
                ->whereNull('api_user.deleted_by')
                // ->get();
                ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => Carbon::Now()
                ]);

            Toastr::success(__('O usuário da API foi eliminado com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function loginApi($token, $key)
    {
        // VALIDAÇÃO DO SUSÁRIO
        $api_user = DB::table('api_users')
            ->where('token', $token)
            ->where('keey', $key)
            ->get();
        return $api_user->isEmpty() ? false : true;
    }


    public function register()
    {

        return view('RH::api_user.api_users');
    }

    public function store(Request $request)
    {

        DB::table('api_users')->insertGetId([
            'name' => $request->full_name,
            'email' => $request->email,
            'state' => 1,
            'telefone_principal' => $request->telefone_principal,
            'telefone_altenativo' => $request->telefone_altenativo,
            'token' => bcrypt($request->full_name),
            'keey' => bcrypt(Carbon::Now()),
            'expires_at' => Carbon::Now(),
            'created_at' => Carbon::Now(),
            'created_by' => auth()->user()->id
        ]);

        // return view('RH::api_user.api_users');
        return redirect()->back();
    }

    public function RHupdate(Request $request)
    {
        try {
            $verificacao = DB::update(
                'update api_users set name = ? , email = ?, state = ?, telefone_principal = ?, telefone_altenativo = ?, update_at = ?, update_by = ? where id = ?',
                [
                    $request->full_name,
                    $request->email,
                    $request->estado,
                    $request->telefone_principal,
                    $request->telefone_altenativo,
                    Carbon::Now(),
                    auth()->user()->id,
                    $request->chave
                ]
            );
            if (!$verificacao)
                return redirect()->route('api.register')->with('error', 'Erro ao Actualizar!');
            return redirect()->route('api.register')->with('sucess', 'Actualizado com sucesso!');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    public function index()
    {
        $email = "zacaju@forlearn.ao";
        $password = "002600188BA037";
        $data = ["email" => $email, "password" => $password];
        return response()->json(['data' => $data]);
        if (Auth::attempt($data)) {
            return response()->json(['data' => $data]);
        } else {
            return response()->json(['data' => 123]);
        }
    }
    
      public function getAll_student()
    {
        try {
            
            
            
            if (Cache::has('api_users_data')) { 
            
                
                $model =  Cache::get('api_users_data');
                
                return response()->json(['data' => $model]);
            }
            
            
            else {
                
                $model = Cache::remember('api_users_data', 10, function() {
                
                    $currentData = Carbon::now();
                    $Proprina_month = Carbon::now()->format('m');
                    $Proprina_year =  Carbon::now()->format('Y');
                    $Proprina_dia =  Carbon::now()->format('d');
                    $dataAtual = 0;
                    $i = 0;
                    $id_estudent = [];
                    $resultData = "";
                    $qtd_divida = 0;
                    $dados_e = ["id","qtd"];
                    DB::beginTransaction();
        
                    $user_data = DB::table('users as usuario')
                    ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                    ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                    ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id')
                    ->leftJoin('user_parameters as user_namePar', function ($join) {
                        $join->on('user_namePar.users_id', '=', 'usuario.id')
                            ->where('user_namePar.parameters_id', 1);
                    })
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'usuario.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('usuario.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })
           
                    
                    ->where('cargo_traducao.active', 1)
                    ->where('cargo_traducao.language_id', 1)
                    ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                    ->where("cargo_traducao.role_id", 6)
                    ->select([
                            'usuario.name as name',
                            'user_namePar.value as full_name',
                            'up_meca.value as matriculation',
                            'usuario.email as email',
                            'usuario.id as id_user',
                            'ct.display_name as course'
                    ])
                    ->orderBy('usuario.name', 'ASC')
                    ->whereNull('usuario.deleted_by')
                    ->whereNull('usuario.deleted_at')
                    
                    ->get();

                        $getUser_article = DB::table("article_requests")
                        
                        ->where("status","pending")
                        ->whereNotNull("month")
                        ->whereNotNull("year") 
                        ->where("discipline_id","")
                        ->whereNull('deleted_at')
                        ->whereNull('deleted_by')
                        ->select([
                            'user_id as user_id',
                            'status as estado_do_mes',
                            'month as month',
                            'year as year',
                        ])
                        ->get();
        
        
                        
        
        
                        $select = DB::table('config_divida_instituicao as config_divida')
                        ->select([
                            'config_divida.qtd_divida',
                            'config_divida.dias_exececao'
                        ])
                        ->where('config_divida.status', 'ativo')
                        ->whereNull('config_divida.deleted_at')
                        ->whereNull('config_divida.deleted_by')
                        ->first();
                    if (isset($select->qtd_divida)) {
                        $qtd_divida = $select->qtd_divida;
                        $Proprina_dia = $select->dias_exececao;
                    }
            
                    
                     $dataAtual = $Proprina_year . '-' . $Proprina_month . '-' . date('d');
                     
                    foreach ($getUser_article as $key => $item) {        
                        $resultData = $item->year . '-0' . $item->month . '-' . $Proprina_dia;
                   
                        if ($resultData < $dataAtual) {

                            if (isset($id_estudent[$item->user_id])) {
                                $id_estudent[$item->user_id] = $id_estudent[$item->user_id] +1;
                            }else{
                                $id_estudent[$item->user_id] = 1;
                            }
                        }
                    }

                    
                   

                    foreach ($user_data as $key => $item) {
                        
                        if((isset($id_estudent[$item->id_user]) && $id_estudent[$item->id_user]>= $qtd_divida)){
                            $item->{'bloqueado'} = 1;
                        } else {
                            $item->{'bloqueado'} = 0;
                        }
                    }
        
                    DB::commit();
                    
                    
                    return $user_data;
                    
                
                });
            
            
                return response()->json(['data' => $model]);
                
                
            }
            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
        public function get_student_status($matriculation)
    {
        try {
                  
                    // return response()->json('Acessado com sucesso !');
                    $currentData = Carbon::now();
                    $Proprina_month = Carbon::now()->format('m');
                    $Proprina_year =  Carbon::now()->format('Y');
                    $id_estudent = [];
                    DB::beginTransaction();
                    
                    $user_data = DB::table('users as usuario')
                    ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                    ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                    ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id')
                    ->leftJoin('user_parameters as user_namePar', function ($join) {
                        $join->on('user_namePar.users_id', '=', 'usuario.id')
                            ->where('user_namePar.parameters_id', 1);
                    })
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'usuario.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('usuario.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })
           
                    ->where('up_meca.value', $matriculation)
                    ->where('cargo_traducao.active', 1)
                    ->where('cargo_traducao.language_id', 1)
                    ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                    ->where("cargo_traducao.role_id", 6)
                    ->select([
                            'usuario.name as name',
                            'user_namePar.value as full_name',
                            'up_meca.value as matriculation',
                            'usuario.email as email',
                            'usuario.id as id_user',
                            'ct.display_name as course'
                    ])
                    ->orderBy('usuario.name', 'ASC')
                    ->whereNull('usuario.deleted_by')
                    ->whereNull('usuario.deleted_at')
                    ->limit(1)
                    ->get();

                        $getUser_article = DB::table("article_requests")
                        ->where("user_id",$user_data[0]->id_user)
                        ->where("status","pending")
                        ->whereNotNull("month")
                        ->whereNotNull("year") 
                        ->where("discipline_id","")
                        ->whereNull('deleted_at')
                        ->whereNull('deleted_by')
                        ->select([
                            'user_id as user_id',
                            'status as estado_do_mes',
                            'month as month',
                            'year as year',
                        ])
                        ->get();
                   
        
                    
                    foreach ($getUser_article as $key => $item) {
                        $resultMonth = $Proprina_month - $item->month;
                        $resultyear = $Proprina_year - $item->year;
                        // if ($resultyear > 0 || $resultMonth > 1) {
                        //     in_array($item->user_id, $id_estudent) ? 0 : $id_estudent[] = $item->user_id;
                        // }
                        if ($resultyear > 0 || $resultMonth > 1) {
                            in_array($item->user_id, $id_estudent) ? 0 : $id_estudent[] = $item->user_id;
                        }
                    }

                   
        
                    foreach ($user_data as $key => $item) {
                        if (in_array($item->id_user, $id_estudent)) {
                            $item->{'bloqueado'} = 1;                
                        } else {
                            $item->{'bloqueado'} = 0;
                        }
                    }
        
                 
               
                    
                    return [$user_data,$getUser_article];            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    
    
    public function get_student_photo($user_id){
        
        try {
            
                
            $model =  DB::table('users as usuario')
                    ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                    ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                    ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id')
                    ->leftJoin('user_parameters as user_namePar', function ($join) {
                        $join->on('user_namePar.users_id', '=', 'usuario.id')
                            ->where('user_namePar.parameters_id', 1);
                    })
                    ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'usuario.id')
                    ->join('courses_translations as ct', function ($join) {
                        $join->on('ct.courses_id', '=', 'uc.courses_id');
                        $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('ct.active', '=', DB::raw(true));
                    })
                    ->leftJoin('user_parameters as up_meca', function ($join) {
                        $join->on('usuario.id', '=', 'up_meca.users_id')
                            ->where('up_meca.parameters_id', 19);
                    })
                    ->leftJoin('user_parameters as up_foto', function ($join) {
                        $join->on('usuario.id', '=', 'up_foto.users_id')
                            ->where('up_foto.parameters_id', 25);
                    })
                    ->where('cargo_traducao.active', 1)
                    ->where('cargo_traducao.language_id', 1)
                    ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                    ->where("cargo_traducao.role_id", 6)
                    ->select([
                        'user_namePar.value as full_name',
                        'usuario.email as email',
                        //'usuario.name as first_last_name_user',
                        'usuario.id as user_id',
                        'up_meca.value as matriculation_number',
                        'up_foto.value as fotografia',
                        'ct.display_name as course'
                    ])
            ->orderBy('usuario.name', 'ASC')
            ->whereNull('usuario.deleted_by')
            ->whereNull('usuario.deleted_at')
            ->where('usuario.id', '=', $user_id)
            ->limit(1)
            ->first();
                    
                    
            foreach ($model as $key => $item) {
                
                $item->{'fotografia'} = $this->get_file_binay($item->fotografia); 
                    
            }
            
        
            return response()->json(['data' => $model]);
            
                    
                    
        } catch (Exception | Throwable $e) {
            
            Log::error($e);
            return response()->json($e->getMessage(), 500);
            
        }
        
    }
    
    
    
    // Retorna o binario de um arquivo
    private function get_file_binay($fotografia) {
        // Verifica se o ficheiro exite e a variavél não está null
        if (Storage::disk('public')->exists('attachment/'.$fotografia) and (isset($fotografia))) {
            
            $path_tabela = storage_path('app/public/attachment/');
            $path= "https://".$_SERVER['HTTP_HOST']."/users/avatar/";     
            
            //$file = rawurlencode($fotografia);
            
            
            // Converte a imagem para binario
            return  base64_encode(file_get_contents($path_tabela.$fotografia));
            
        }
        else {

            return null;

        }
    }


    public function getStaff()
    {

        try {
            DB::beginTransaction();
            $model = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id')
                ->leftJoin('user_parameters as user_namePar', function ($join) {
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                        ->where('user_namePar.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_foto', function ($join) {
                    $join->on('usuario.id', '=', 'up_foto.users_id')
                        ->where('up_foto.parameters_id', 25);
                })
                ->where('cargo_traducao.active', 1)
                ->where('cargo_traducao.language_id', 1)
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->whereNotin('cargo_traducao.role_id', [6, 1, 15, 2])
                ->select([
                    'user_namePar.value as full_name_user',
                    'usuario.name as first_last_name_user',
                    'usuario.email as email',
                    'usuario.id as id_user',
                    'up_foto.value as fotografia'
                ])
                ->orderBy('usuario.id', 'ASC')
                ->whereNull('usuario.deleted_by')
                ->whereNull('usuario.deleted_at')
                ->distinct('usua vrio.id')
                ->get();

            foreach ($model as $key => $item) {
                $item->{'acessivel'} = 0;
                // Converte a imagem para binario
                $item->fotografia = base64_encode($item->fotografia);
            }

            return response()->json($model);
            DB::commit();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getDocente()
    {
        try {
            DB::beginTransaction();

            $model = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id')
                ->leftJoin('user_parameters as user_namePar', function ($join) {
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                        ->where('user_namePar.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_foto', function ($join) {
                    $join->on('usuario.id', '=', 'up_foto.users_id')
                        ->where('up_foto.parameters_id', 25);
                })
                ->where('cargo_traducao.active', 1)
                ->where('cargo_traducao.language_id', 1)
                ->where('usuario_cargo.model_type', "App\Modules\Users\Models\User")
                ->whereIn('cargo_traducao.role_id', [1])
                ->select([
                    'user_namePar.value as full_name_user',
                    'usuario.name as first_last_name_user',
                    'usuario.email as email',
                    'usuario.id as id_user',
                    'up_foto.value as fotografia',
                ])
                ->orderBy('usuario.id', 'ASC')
                ->whereNull('usuario.deleted_by')
                ->whereNull('usuario.deleted_at')
                ->distinct('usuario.id')
                ->get();

            foreach ($model as $key => $item) {
                $item->{'acessivel'} = 0;
                // Converte a imagem para binario
                $item->fotografia = base64_encode($item->fotografia);
            }

            return response()->json($model);
            DB::commit();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getStatus_Student($matriculation)
    {
        try {
            
            DB::beginTransaction();
           
            
            
            return $getInformation_student = $this->get_information_student($matriculation);
            return response()->json($getInformation_student);
            DB::commit();
        } catch (Exception | Throwable $e) {
            return response()->json($e);
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    private function get_information_student($matriculation)
    {
 
        $currentData = Carbon::now();
        $Proprina_month = Carbon::now()->format('m');
        $Proprina_year =  Carbon::now()->format('Y');
        $Proprina_dia =  Carbon::now()->format('d');
        $dataAtual = 0;
        $i = 0;
        $id_estudent = [];
        $resultData = "";
        // strtotime
        $data = $this->get_student_status($matriculation);
        
        $model = $data[0];
         $getUser_article = $data[1];
        $qtd_divida = 0;

      

        $select = DB::table('config_divida_instituicao as config_divida')
            ->select([
                'config_divida.qtd_divida',
                'config_divida.dias_exececao'
            ])
            ->where('config_divida.status', 'ativo')
            ->whereNull('config_divida.deleted_at')
            ->whereNull('config_divida.deleted_by')
            ->first();
        if (isset($select->qtd_divida)) {
            $qtd_divida = $select->qtd_divida;
            $Proprina_dia = $select->dias_exececao;
        }

        
         $dataAtual = $Proprina_year . '-' . $Proprina_month . '-' . date('d');
         
        foreach ($getUser_article as $key => $item) {        
            $resultData = $item->year . '-0' . $item->month . '-' . $Proprina_dia;
       
            if ($resultData < $dataAtual) {
                $i = $i+1;
            }
        } 
        
        foreach ($model as $key => $item) {
            if ($i>= $qtd_divida) {
                $item->{'bloqueado'} = 1;
            } else {
                $item->{'bloqueado'} = 0;
            }
        }
 

       return $model;
    }


    public function sendMatricula()
    {
        try {
            $url = "https://dev.forlearn.ao/pt/api/recebeMensagem";
            $data = [
                'status_code' => 200,
                'status' => 'success',
                'message' => 'webhook send successfully',
                'extra_data' => [
                    'first_name' => 'Gelson',
                    'last_name' => 'Matias',
                ],
            ];

            $json_array = json_encode($data);
            $curl = curl_init();
            $headers = ['Content-Type: application/json'];


            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_array);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($http_code == 200) {
                return "mensagem enviada com sucesso.";
            } else {
                return "Erro no envio.";
            }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }


    public function recebeMensagem(Request $request)
    {
        try {
            $data = [
                'responsse' => (object)[
                    'request' => $request->model,
                    'status' => 200
                ]
            ];
            return response()->json($data);
        } catch (\Throwable $e) {
            return 500;
        }
    }

    public function catracaSimulacaoAIP($id_funcionario, $data)
    {
        try {
            $getpresencaCatraca = DB::table('rh_controle_presenca_catraca as presenca_catraca')
                ->where('presenca_catraca.id_funcionario', $id_funcionario)
                ->where('presenca_catraca.month_year', $data)
                ->get();

            return response()->json($getpresencaCatraca, 200);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json(500);
        }
    }


    public function configuracaoApiClient()
    {

        $getEntidade = DB::table('api_users as client')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->get()->map(function ($q) {
                $q->{'client'} = $q->name . ' (' . $q->email . ')';
                return $q;
            });
        $data = [
            'getEntidade' => $getEntidade
        ];
        return view('RH::api_user.config_client_webhook.config_client_webhook')->with($data);
    }

    public function criarWebhookServico_entidade(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('client_webhook')->insert([
                'id_api_user' => $request->entidade,
                'endpoint' => $request->endpoint,
                'servico' => $request->servico,
                'status' => 'ativo',
                'created_at' => Carbon::Now(),
                'created_by' => Auth::user()->id
            ]);
            DB::commit();

            Toastr::success(__('Serviço de notificação criado ao cliente com sucesso'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax_table_client_webhook()
    {

        $getclientes = DB::table('client_webhook as client_hook')
            ->join('api_users as api_user', function ($q) {
                $q->on('api_user.id', '=', 'client_hook.id_api_user');
            })
            ->leftJoin('user_parameters as user_parament', function ($q) {
                $q->on('user_parament.users_id', '=', 'client_hook.created_by')
                    ->where('user_parament.parameters_id', 1);
            })
            ->select([
                'client_hook.id as id_webhook',
                'client_hook.servico as servico',
                'client_hook.endpoint as endpoint',
                'client_hook.status as status',
                'client_hook.created_at as created_at ',
                'user_parament.value as created_by',
                'api_user.name as cliente',
            ])
            ->whereNull('client_hook.deleted_at')
            ->whereNull('client_hook.deleted_by');
        // ->get();


        return Datatables::of($getclientes)
            ->addColumn('actions', function ($item) {
                return view('RH::api_user.config_client_webhook.datatables.actions', compact('item'));
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
    }

    public function delete_config_client_webhook(Request $request)
    {
        $affected = DB::table('client_webhook')
            ->where('id', $request->id_webhook_cliente)
            ->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => Carbon::Now()
            ]);

        Toastr::success(__('Configuração do cliente foi eliminado com sucesso.'), __('toastr.success'));
        return redirect()->back();
    }
    public function editar_configuracao_cliente(Request $request)
    {
        try {
            DB::beginTransaction();
            $affected = DB::table('client_webhook')
                ->where('id', $request->id_webhook_cliente_edit)
                ->update([
                    'endpoint' => $request->endpoint,
                    'servico' => $request->servico_edit,
                    'status' => isset($request->status) ? 'ativo' : 'desativado'
                ]);

            Toastr::success(__('Configuração do cliente foi editada com sucesso.'), __('toastr.success'));
            return redirect()->back();
            DB::commit();
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
    public function getCursoForlearn_ispm()
    {   try{
            $getAllCurso=DB::table('courses as curso')
            ->join('courses_translations as curso_trans',function ($q)
            {
            $q->on('curso_trans.courses_id','=','curso.id')
            ->where('curso_trans.language_id',1)
            ->where('curso_trans.active',1);
            })
            ->whereNull('curso.deleted_by')
            ->whereNUll('curso.deleted_at')
            ->get();
            return response()->json($getAllCurso);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
}
