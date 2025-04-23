<?php
namespace App\Modules\RH\Controllers;


use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\User;
use App\Modules\GA\Models\LectiveYear;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Storage;
use Log;
use Throwable;
use Toastr;
use Auth;
use PDF;





class RecursoHumanoController extends Controller
{   
    public function rescisaoContratoAutomatico($dataAtual)
    {
        try{
            $upload=false;
            $getfun_with_type_contrato=DB::table('fun_with_type_contrato as fun_with_type_cont')
                ->join('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato')
                ->select([
                    'fun_with_type_cont.id as id_fun_with_type_cont',
                    'fun_with_cont.id as id_fun_with_cont',
                    'fun_with_cont.id_user as id_user'
                ])
                ->where('fun_with_type_cont.data_fim_contrato','=',$dataAtual)
                ->where('fun_with_type_cont.status_contrato','=','ativo')
                ->whereNull('fun_with_type_cont.deleted_by')
                ->whereNull('fun_with_cont.deleted_by')
                ->whereNull('fun_with_cont.deleted_at')
            ->get();

            if (!$getfun_with_type_contrato->isEmpty()) {
            foreach ($getfun_with_type_contrato as $key => $item) {
                    $getfun_with_contrato=DB::table('rescisoes_contrato as rescisoes')
                    ->join('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','rescisoes.id_fun_with_contrato')
                    ->where('rescisoes.id_fun_with_contrato','=',$item->id_fun_with_cont)
                    ->select([
                        'rescisoes.id as id_rescisoes'
                        ])
                    ->whereNull('fun_with_cont.deleted_by')
                    ->whereNull('fun_with_cont.deleted_at')
                    ->get();
                

                    if ($getfun_with_contrato->isEmpty()) {
                            $getid_rescisao =DB::table('rescisoes_contrato')->insertGetId([
                                'id_fun_with_contrato' => $item->id_fun_with_cont,
                                'created_at' => Carbon::Now(),
                                'created_by' => Auth::user()->id,
                                'update_at' => Carbon::Now(),
                                'update_by' =>  Auth::user()->id
                            ]);
                            $fun_with_type_cont= $item->id_fun_with_type_cont;
                            $this->updateRescisaoContratoAutomatico($getid_rescisao,$upload,$fun_with_type_cont);
                    }else{
                            $getid_rescisao=$getfun_with_contrato[0]->id_rescisoes;
                            $fun_with_type_cont= $item->id_fun_with_type_cont;
                            $this->updateRescisaoContratoAutomatico($getid_rescisao,$upload,$fun_with_type_cont);
                    }
                }
                return response()->json(['data'=>$getfun_with_type_contrato,'data1'=>$getfun_with_contrato]);
            }

                
            
        } catch (Exception | Throwable $e) {
            // return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
        
    }

    private function updateRescisaoContratoAutomatico($getid_rescisao,$upload,$fun_with_type_cont)
    {

         // gravar a rescisão do contrato com tipo de contrato
         DB::table('rescisoes_contrato_tipo_trabalho')->insert(
            [
            'id_rescisao' => $getid_rescisao, 
            'id_fun_type_contrato'=> $fun_with_type_cont,
            'nota' =>null,
            'created_by' => Auth::user()->id,
            'update_at' => Carbon::Now(),
            ]
        );

        // Atualizar Contrato tabela fun with_type_contrato
        DB::table('fun_with_type_contrato as fun_with_type_cont')
            ->where('fun_with_type_cont.id', $fun_with_type_cont)
            ->update(['status_contrato'=>'panding']);
    }


    public function rescisoses()
    {
        try {
            $users =  User::with(['roles' => function ($q) {
                   $q->with(['currentTranslation' ]);
                   // $q->select([
                   //     'roles.name as name'
                   // ]);
                   // $q->whereNotIn('roles.id', [6,15]);
               }], ['parameters' => function ($q) {
                   $q->whereIn('code', ['nome', 'n_mecanografico']);
               }])
               ->with(['parameters'=>function ($q)
               {
                   $q->whereIn('parameters_id',[1,36,39,5,45,25]);
               }])
               ->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
               ->whereHas("roles", function ($q) {
                   $q->whereNotIn("id",[6,15,2]);
               })
           ->get();
               

           $users=collect($users)->map(function($item){
               foreach ($item->parameters as $key => $paramete) {
                   if ($paramete->id==1) {
                       $item->{'full_name'} =$paramete->pivot->value;
                   }elseif($paramete->id==36) {
                       $item->{'telefone'} =$paramete->pivot->value;
                   }
                   elseif($paramete->id==39) {
                       $item->{'whatApp'} =$paramete->pivot->value;
                   }
                   elseif($paramete->id==5) {
                       $getIdade= explode('-',$paramete->pivot->value);
                       $item->{'idade'} = (int)date('Y') -(int)$getIdade[0];
                   }
                   elseif($paramete->id==45) {
                       $item->{'bairro'} =$paramete->pivot->value;
                   }
                   elseif($paramete->id==25) {
                       $item->{'fotografia'} =$paramete->pivot->value;
                   }
               }
               return $item;
           });

           $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
           ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
               $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
           })
           ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('fun_type_contrato.created_by', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
           ->whereNull('fun_type_contrato.deleted_at')
           ->whereNull('fun_type_contrato.deleted_by')
           ->where('fun_type_contrato.status_contrato','=','ativo')
           ->get();

           $getfuncaoUsers=DB::table('recurso_humano_at_funcao as rh_funcao')
           ->join('fun_with_contrato_at_funcao as fun_with_cont_funcao',function ($q){
               $q->on('fun_with_cont_funcao.id_funcao_rh','=','rh_funcao.id');
           })
           ->join('funcionario_with_contrato as fun_with_contrato',function ($q){
               $q->on('fun_with_cont_funcao.id_fun_with_contrato','=','fun_with_contrato.id');
           })
           ->whereNull('fun_with_cont_funcao.deleted_at')
           ->whereNull('rh_funcao.deleted_by')
           ->whereNull('fun_with_contrato.deleted_by')
           ->get();

           $getSalariofuncionario=DB::table('recurso_humano_salario_funcionario as rh_salario_funcionario')
           ->join('funcionario_with_contrato as fun_with_contrato',function ($q){
               $q->on('rh_salario_funcionario.id_fun_with_contrato','=','fun_with_contrato.id');
           })
           ->select([
               'rh_salario_funcionario.created_at as created_at',
               'rh_salario_funcionario.id_cargo  as id_cargo',
               'rh_salario_funcionario.id_fun_with_contrato as id_fun_with_contrato',
               'fun_with_contrato.id_user as id_user',
               'rh_salario_funcionario.salarioBase as salarioBase',
               'rh_salario_funcionario.id as id'
           ])
           ->orderBy('rh_salario_funcionario.created_at','DESC')
           ->whereNull('rh_salario_funcionario.deleted_by')  
           ->get();
           
           $data=[
               'action'=>'GESTÃO DO STAFF',
               'users'=>$users,
               'getfuncaoUsers'=>$getfuncaoUsers,
               'getSalariofuncionario'=>$getSalariofuncionario,
               'getcontratos'=>$getcontratos
           ];
               return view('RH::gestaoStaff.rescisoes.index')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }

    public function createRescisao(Request $request)
    {
        try{
            $upload=false;
            $filename=null;
            $user = User::whereId($request->funcionario)->firstOrFail();
            $role= Role::with(['currentTranslation' ])
            ->whereId($request->roles)->firstOrFail();
            if ($request->arquivo!=null) {
                    $file=$request->file('arquivo');
                    $filename = $file->getClientOriginalName();
                    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                    
                if($fileExtension!="pdf" && $fileExtension!="jpg" && $fileExtension!="png")
                {
                    Toastr::error("Erro com o arquivo, anexa arquivo com as seguintes fromatações (PDF,PNG,JPG).", __('toastr.error'));
                    return redirect()->back();
                }else{
                    $hear=time(); 
                    $filename =$user->name.'_rescisaoContrato_'.$hear.'.'.$fileExtension;

                    //  gravar arquivo no servidor
                    $request->file('arquivo')->storeAs('documento_userRH', $filename);
                    $upload=true;
                }
            }

            $getfun_with_contrato=DB::table('rescisoes_contrato as rescisoes')
            ->join('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','rescisoes.id_fun_with_contrato')
            ->where('fun_with_cont.id_user','=',$request->funcionario)
            ->select([
                'rescisoes.id as id_rescisoes'
                ])
            ->whereNull('fun_with_cont.deleted_by')
            ->whereNull('fun_with_cont.deleted_at')
            ->get();

            $getfun_contrato=DB::table('funcionario_with_contrato as fun_with_cont')
            ->where('fun_with_cont.id_user','=',$request->funcionario)
            ->whereNull('fun_with_cont.deleted_by')
            ->whereNull('fun_with_cont.deleted_at')
            ->first();

            $getfun_with_type_contrato=DB::table('fun_with_type_contrato as fun_with_type_cont')
            ->join('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato')
            ->select([
                'fun_with_type_cont.id as id_fun_with_type_cont'
            ])
            ->where('fun_with_type_cont.id_fun_with_contrato','=',$getfun_contrato->id)
            ->where('fun_with_type_cont.id_cargo','=',$request->roles)
            ->where('fun_with_type_cont.status_contrato','=','ativo')
            ->whereNull('fun_with_type_cont.deleted_by')
            ->whereNull('fun_with_cont.deleted_by')
            ->whereNull('fun_with_cont.deleted_at')
            ->first();

            if ($getfun_with_contrato->isEmpty()) {
                // gravar a rescisão pela primeira vez
                $getid_rescisao =DB::table('rescisoes_contrato')->insertGetId([
                    'id_fun_with_contrato' => $getfun_contrato->id,
                    'created_at' => Carbon::Now(),
                    'created_by' => Auth::user()->id,
                    'update_at' => Carbon::Now(),
                    'update_by' =>  Auth::user()->id
                ]);

                $this->insertRescisao_fun_type_contrato($getid_rescisao,$upload,$getfun_with_type_contrato,$filename,$request);
                Toastr::success(__('Contrato rescindido com sucesso'), __('toastr.success'));
                return redirect()->back();
            }else{
               $getid_rescisao=$getfun_with_contrato[0]->id_rescisoes;
               $this->insertRescisao_fun_type_contrato($getid_rescisao,$upload,$getfun_with_type_contrato,$filename,$request);
               Toastr::success(__('Contrato rescindido com sucesso'), __('toastr.success'));
               return redirect()->back();

            }
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }

    private function insertRescisao_fun_type_contrato($getid_rescisao,$upload,$getfun_with_type_contrato,$filename,$request)
    {
         // gravar a rescisão do contrato com tipo de contrato
        DB::table('rescisoes_contrato_tipo_trabalho')->insert(
            [
            'id_rescisao' => $getid_rescisao, 
            'id_fun_type_contrato'=> $getfun_with_type_contrato->id_fun_with_type_cont,
            'nota' =>$request->nota,
            'created_by' => Auth::user()->id,
            'update_at' => Carbon::Now(),
            ]
        );
        
        // insercir nome arquivo base  de dado nome do arquivo
        if ($upload==true) {
            DB::table('documento_user_recurso_humano')->insert(
                [
                'id_rescisao_fun_type_contrato' => $getfun_with_type_contrato->id_fun_with_type_cont, 
                'arquivo' => $filename,
                'created_at' => Carbon::Now(),
                'created_by' => Auth::user()->id,
                'update_at' => Carbon::Now(),
                'update_by' =>  Auth::user()->id
                ]);
        }
        // Atualizar Contrato tabela fun with_type_contrato
        DB::table('fun_with_type_contrato as fun_with_type_cont')
            ->where('fun_with_type_cont.id', $getfun_with_type_contrato->id_fun_with_type_cont)
            ->update(['status_contrato'=>'panding']);
    }
    public function ajaxRescisaoContrato()
    {
         try{

            $getfun_with_contrato=DB::table('rescisoes_contrato as rescisoes')
            ->leftJoin('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','rescisoes.id_fun_with_contrato')
            ->leftJoin('users as user','user.id','=','fun_with_cont.id_user')
            ->leftJoin('user_parameters as full', function ($join) {
                $join->on('user.id', '=', 'full.users_id')
                ->where('full.parameters_id', 1);
            })
            ->select([
                'rescisoes.id as id_rescisoes',
                'full.value as nome_funcionario',
                'user.email as email'
            ])
            ->whereNull('fun_with_cont.deleted_by')
            ->whereNull('fun_with_cont.deleted_at')
            ->get();

            return DataTables::of($getfun_with_contrato)
            ->addColumn('actions', function ($item) {
                return view('RH::gestaoStaff.rescisoes.datatables.actions',compact('item'));
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->make(true); 

        } catch (Exception | Throwable $e) {
            // return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }

    public function ajaxCargoRescisaoContrato($getid_rescisoes)
    {
        try{
            $getCargofun_with_contrato=DB::table('rescisoes_contrato as rescisoes')
                ->leftJoin('rescisoes_contrato_tipo_trabalho as res_cont_tipo_trab','res_cont_tipo_trab.id_rescisao','=','rescisoes.id')
                ->leftJoin('fun_with_type_contrato as fun_with_type_cont','fun_with_type_cont.id','=','res_cont_tipo_trab.id_fun_type_contrato')
                ->leftJoin('role_translations as role_trans',function ($join)
                {
                    $join->on('role_trans.role_id', '=', 'fun_with_type_cont.id_cargo')
                    ->where('role_trans.language_id',1)
                    ->where('role_trans.active',1);
                })
                ->leftJoin('documento_user_recurso_humano as doc_recurso_humano', function ($join) {
                    $join->on('doc_recurso_humano.id_rescisao_fun_type_contrato', '=', 'res_cont_tipo_trab.id_fun_type_contrato');
                })
                ->leftJoin('user_parameters as full_created', function ($join) {
                    $join->on('fun_with_type_cont.created_by', '=', 'full_created.users_id')
                    ->where('full_created.parameters_id', 1);
                })
                ->leftJoin('user_parameters as full_rescindido_por', function ($join) {
                    $join->on('res_cont_tipo_trab.created_by', '=', 'full_rescindido_por.users_id')
                    ->where('full_rescindido_por.parameters_id', 1);
                })
                ->select([
                    'rescisoes.id as id_rescisoes',
                    'role_trans.display_name as cargo',
                    'fun_with_type_cont.data_inicio_conrato as data_inicio_conrato',
                    'fun_with_type_cont.data_fim_contrato as data_fim_contrato',
                    'full_created.value as criado_por',
                    'full_rescindido_por.value as rescindido_por',
                    'doc_recurso_humano.arquivo as arquivo',
                    'rescisoes.update_at as rescindido_ao'
                ])
                ->where('rescisoes.id',$getid_rescisoes)
                ->whereNull('rescisoes.deleted_by')
                ->whereNull('rescisoes.deleted_at')
            ->get();
            
            // return response()->json($getCargofun_with_contrato);
            return DataTables::of($getCargofun_with_contrato)
            ->addIndexColumn()
            ->make(true); 

        } catch (Exception | Throwable $e) {
            // return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 


    }

    public function contratoUser($user_id){
        $data = [];
        
        $fwc = DB::table('funcionario_with_contrato as fwc')
            ->join('fun_with_type_contrato as fwtype','fwtype.id_fun_with_contrato','fwc.id')
            ->join('model_has_roles as mhr','mhr.model_id','fwc.id_user')
            ->select([
                'fwtype.tipo_presenca',
                'fwtype.data_inicio_conrato',
                'fwtype.data_fim_contrato',
                'fwtype.status_contrato',
                'fwtype.tipo_irt',
                'mhr.role_id',
                'fwtype.id_fun_with_contrato',
            ])
            ->where('mhr.model_type','App\Modules\Users\Models\User')
            ->where('fwc.id_user',$user_id)
            ->first();
        
        if(isset($fwc->id_fun_with_contrato)) $data['funcionario'] = $fwc;
            
       $rhaf = isset($fwc->id_fun_with_contrato, $fwc->role_id) ? 
            DB::table('recurso_humano_salario_funcionario as rhaf')
            ->where([
                'rhaf.id_fun_with_contrato' => $fwc->id_fun_with_contrato,
                'id_cargo' => $fwc->role_id
            ])->first() : null;
       
        if(isset($rhaf->id)) $data['recurso'] = $rhaf;
        
        return response()->json($data);
    }

    public function contratoTrabalho()
    {
        
        try {
             $users =  User::with(['roles' => function ($q) {
                    $q->with(['currentTranslation' ]);
                    // $q->select([
                    //     'roles.name as name'
                    // ]);
                    // $q->whereNotIn('roles.id', [6,15]);
                }], ['parameters' => function ($q) {
                    $q->whereIn('code', ['nome', 'n_mecanografico']);
                }])
                ->with(['parameters'=>function ($q)
                {
                    $q->whereIn('parameters_id',[1,36,39,5,45,25]);
                }])
                ->whereNotIn('users.id', users_exemplo())
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("id",[6,15,2]);
                })
            ->get();


            $users=collect($users)->map(function($item){
                foreach ($item->parameters as $key => $paramete) {
                    if ($paramete->id==1) {
                        $item->{'full_name'} =$paramete->pivot->value;
                    }elseif($paramete->id==36) {
                        $item->{'telefone'} =$paramete->pivot->value;
                    }
                    elseif($paramete->id==39) {
                        $item->{'whatApp'} =$paramete->pivot->value;
                    }
                    elseif($paramete->id==5) {
                        $getIdade= explode('-',$paramete->pivot->value);
                        $item->{'idade'} = (int)date('Y') -(int)$getIdade[0];
                    }
                    elseif($paramete->id==45) {
                        $item->{'bairro'} =$paramete->pivot->value;
                    }
                    elseif($paramete->id==25) {
                        $item->{'fotografia'} =$paramete->pivot->value;
                    }
                }
                return $item;
            });

            $getfuncoes=DB::table('recurso_humano_at_funcao as rh_funcao')
            ->whereNull('rh_funcao.deleted_at')
            ->whereNull('rh_funcao.deleted_by')
            ->get();

            $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
            ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
            })
            ->whereNull('fun_type_contrato.deleted_at')
            ->whereNull('fun_type_contrato.deleted_by')
            ->where('fun_type_contrato.status_contrato','=','ativo')
            ->get();

            $getfuncaoUsers=DB::table('recurso_humano_at_funcao as rh_funcao')
            ->join('fun_with_contrato_at_funcao as fun_with_cont_funcao',function ($q){
                $q->on('fun_with_cont_funcao.id_funcao_rh','=','rh_funcao.id');
            })
            ->join('funcionario_with_contrato as fun_with_contrato',function ($q){
                $q->on('fun_with_cont_funcao.id_fun_with_contrato','=','fun_with_contrato.id');
            })
            ->select([
                'fun_with_cont_funcao.id as id_fun_with_cont_funcao',
                'fun_with_cont_funcao.nota as nota',
                'fun_with_cont_funcao.data_fim_contrato_at_funcao as data_fim_contrato_at_funcao',
                'fun_with_cont_funcao.data_inicio_contrato_at_funcao as data_inicio_contrato_at_funcao',
                'fun_with_cont_funcao.status_contrato_at_funcao as status_contrato_at_funcao',
                'fun_with_cont_funcao.created_at as created_at',
                'fun_with_cont_funcao.status_contrato_at_funcao as status_contrato_at_funcao',
                'rh_funcao.display_name as display_name',
                'rh_funcao.descricao as descricao',
                'fun_with_contrato.id_user as id_user',
            ])
            ->where('fun_with_cont_funcao.status_contrato_at_funcao','=','ativo')
            ->whereNull('fun_with_cont_funcao.deleted_at')
            ->whereNull('rh_funcao.deleted_by')
            ->whereNull('fun_with_contrato.deleted_by')
            ->get();

            $getSalariofuncionario=DB::table('recurso_humano_salario_funcionario as rh_salario_funcionario')
            ->join('funcionario_with_contrato as fun_with_contrato',function ($q){
                $q->on('rh_salario_funcionario.id_fun_with_contrato','=','fun_with_contrato.id');
            })
            ->select([
                'rh_salario_funcionario.created_at as created_at',
                'rh_salario_funcionario.id_cargo  as id_cargo',
                'rh_salario_funcionario.id_fun_with_contrato as id_fun_with_contrato',
                'fun_with_contrato.id_user as id_user',
                'rh_salario_funcionario.salarioBase as salarioBase',
                'rh_salario_funcionario.id as id'
            ])
            ->orderBy('rh_salario_funcionario.created_at','DESC')
            ->whereNull('rh_salario_funcionario.deleted_by')  
            ->get();

            $horas_laboral = DB::table('rh_horario_laboral as rh_horario_lab')
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('rh_horario_lab.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->leftJoin('user_parameters as fullName1', function ($join) {
                    $join->on('rh_horario_lab.update_by', '=', 'fullName1.users_id')
                    ->where('fullName1.parameters_id', 1);
                })
                ->select([
                    'rh_horario_lab.id as id',
                    'rh_horario_lab.dias_trabalho as dias_trabalho',
                    'rh_horario_lab.entrada_1 as entrada_1', 
                    'rh_horario_lab.saida_1 as saida_1',
                    'rh_horario_lab.entrada_2 as entrada_2', 
                    'rh_horario_lab.saida_2 as saida_2',
                    'rh_horario_lab.total_horas_dia as total_horas_dia',
                    'rh_horario_lab.total_minutos_dia as total_minutos_dia',
                    'fullName.value as created_by',
                    'rh_horario_lab.created_at as created_at',
                    'fullName1.value as update_by',
                    'rh_horario_lab.update_at as update_at'
                ])                    
                ->whereNull('rh_horario_lab.deleted_at')
                ->whereNull('rh_horario_lab.deleted_by')
            ->get();
            
            $data=[
                'action'=>'GESTÃO DO STAFF',
                'users'=>$users,
                'getfuncoes'=>$getfuncoes,
                'getfuncaoUsers'=>$getfuncaoUsers,
                'getSalariofuncionario'=>$getSalariofuncionario,
                'getcontratos'=>$getcontratos,
                'horas_laboral'=>$horas_laboral
            ];
                return view('RH::gestaoStaff.contrato.index')->with($data);
            } catch (Exception | Throwable $e) {
                // return $e;
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }   
    }


    public function createFuncaoRH_contrato (Request $request)
    {
        DB::table('recurso_humano_at_funcao')->insert([
            'display_name' =>$request->display_name,
            'descricao' =>$request->descricao,
            'created_by' =>Auth::user()->id,
            'created_at' =>Carbon::Now(),
            'update_by' =>Auth::user()->id
            
        ]);

        Toastr::success(__('Função criado com sucesso'), __('toastr.success'));
        return redirect()->back();
    }
    public function contrato_funcionario(Request $request)
    { 
        try{
            
            $upload=false;
            $getcontratoFuncionario=[];
            $request->dataFinal= $request->dataFinal==null ? '2100-01-01' : $request->dataFinal;
            if (strtotime($request->dataIncial)<=strtotime($request->dataFinal)) {
                // return $request;
                if ($request->dataFinal==null || $request->dataIncial==null  || $request->presenca==null) {
                    $salarioRenovado=$this->createSalarioWithContrato($request);
                    if ($salarioRenovado) {
                        Toastr::success(__('Salário Renovado com sucesso'), __('toastr.success'));
                        return redirect()->back();
                    }else{
                        Toastr::error(__('Caro utilizador este mês atribuído o salário já foi processado o salário, por favor digite um outro mês para atribuir o salário.'), __('toastr.error'));
                        return redirect()->back(); 
                    }
                    
                }else{
                    $getId_user=DB::table('funcionario_with_contrato as contrato')
                    ->whereNull('contrato.deleted_at')
                    ->whereNull('contrato.deleted_by')
                    ->where('contrato.id_user','=',$request->funcionario)
                    ->get();
                        foreach ($getId_user as $key => $item) {
                            $getcontratoFuncionario=DB::table('fun_with_type_contrato as contrato_type')
                            ->whereNull('contrato_type.deleted_at')
                            ->whereNull('contrato_type.deleted_by')
                            ->where('contrato_type.id_fun_with_contrato','=',$item->id)
                            ->where('contrato_type.id_cargo','=',$request->roles)
                            ->where('contrato_type.status_contrato','=','ativo')
                            ->get();
                        }
                        $count=count($getcontratoFuncionario);
                    if ($count!=0) {
                            Toastr::error(__('Este funcionario encontra-se neste cargo com contrato ativo'), __('toastr.error'));
                            return redirect()->back();
                    }
                        else {
                            if ($getId_user->isEmpty()) {
                                $getUser=DB::table('funcionario_with_contrato')->insertGetId([
                                    'id_user' =>$request->funcionario,
                                    'created_by' =>Auth::user()->id,
                                    'created_at' =>Carbon::Now(),
                                    'update_by' =>Auth::user()->id,
                                    'update_at' =>Carbon::Now() 
                                ]);

                                DB::table('fun_with_type_contrato')->insert([
                                    'id_fun_with_contrato' =>$getUser,
                                    'tipo_presenca' =>$request->presenca,
                                    'id_cargo' =>$request->roles,
                                    'data_inicio_conrato' =>$request->dataIncial,
                                    'data_fim_contrato' =>$request->dataFinal,
                                    'status_contrato' =>"ativo",
                                    'tipo_irt' => $request->tipo_irt ?? null,
                                    'created_at' =>Carbon::Now(),
                                    'created_by' =>Auth::user()->id,
                                    'update_at' =>Carbon::Now()
                                ]);
                            }else{
                             $fun_type_contrato=DB::table('fun_with_type_contrato')->insertGetId([
                                    'id_fun_with_contrato' =>$getId_user[0]->id,
                                    'tipo_presenca' =>$request->presenca,
                                    'id_cargo' =>$request->roles,
                                    'data_inicio_conrato' =>$request->dataIncial,
                                    'data_fim_contrato' =>$request->dataFinal,
                                    'status_contrato' =>"ativo",
                                    'tipo_irt' => $request->tipo_irt ?? null,
                                    'created_at' =>Carbon::Now(),
                                    'created_by' =>Auth::user()->id,
                                    'update_at' =>Carbon::Now()
                                ]);
                            }
                            if ($request->arquivo!=null) {
                                $user = User::whereId($request->funcionario)->firstOrFail();
                                $file=$request->file('arquivo');
                                $filename = $file->getClientOriginalName();
                                $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                                
                                if($fileExtension!="pdf" && $fileExtension!="jpg" && $fileExtension!="png")
                                {
                                    Toastr::error("Erro com o arquivo, anexa arquivo com as seguintes fromatações (PDF,PNG,JPG).", __('toastr.error'));
                                    return redirect()->back();
                                }else{
                                    $hear=time(); 
                                    $filename =$user->name.'_contratoTrabalho_'.$hear.'.'.$fileExtension;
                
                                    //  gravar arquivo no servidor
                                    $request->file('arquivo')->storeAs('documento_userRH', $filename);
                                    $upload=true;
                                }
                            }

                        if ($upload==true) {
                            DB::table('documento_user_recurso_humano')->insert(
                                [
                                'id_fun_type_contrato' => $fun_type_contrato, 
                                'arquivo' => $filename,
                                'created_at' => Carbon::Now(),
                                'created_by' => Auth::user()->id,
                                'update_at' => Carbon::Now(),
                                'update_by' =>  Auth::user()->id
                                ]);
                        }


                       $salario= $this->createSalarioWithContrato($request);
                       if ($salario) {
                            Toastr::success(__('Contrato criado com sucesso'), __('toastr.success'));
                            return redirect()->back();
                       }else{
                            Toastr::success(__('O contrato foi criado com sucesso, o salário do funcionário não pode ser atualizado de acordo o mês inserido!'), __('toastr.success'));
                            return redirect()->back();
                       }
                        
                    }
            }
            }else{
                Toastr::error(__('Interlavo de contrato incorreto, data de inicio do contrato tem que ser igual ou menor que data final do contrato'), __('toastr.error'));
                return redirect()->back();  
            }
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  

    }
    private function createSalarioWithContrato($request)
    {   
        try{
            // return $request;
            $valor=$request->valorSalario!=null ?  (float)$request->valorSalario :  (float)$request->valorSalarioDia;
            
            $getuser_contrato=DB::table('funcionario_with_contrato as fun_with_contrato')
            ->whereNull('fun_with_contrato.deleted_at')
            ->whereNull('fun_with_contrato.deleted_by')
            ->where('fun_with_contrato.id_user','=',$request->id_funSalario)
            ->get();

            
            $getSalarioMonth=DB::table('recurso_humano_salario_funcionario as rh_salario_func')
            ->whereNull('rh_salario_func.deleted_at')
            ->whereNull('rh_salario_func.deleted_by')
            ->where('rh_salario_func.id_fun_with_contrato','=',$getuser_contrato[0]->id)
            ->where('rh_salario_func.id_cargo','=',$request->roles)
            ->where('rh_salario_func.status_salario','!=','panding')
            ->where('rh_salario_func.dataSalario','=',$request->dataSalario)
            ->get();
                $count=count($getSalarioMonth);
                if ($count==0) {
                    DB::table('recurso_humano_salario_funcionario')->insert([
                        'id_fun_with_contrato' =>$getuser_contrato[0]->id,
                        'id_cargo' =>$request->roles,
                        'salarioBase' =>$valor,
                        'dataSalario' =>$request->dataSalario,
                        'id_horalaboral' =>isset($request->horaLaboral) ? $request->horaLaboral : null,
                        'status_salario' =>"panding",
                        'created_at' =>Carbon::Now(),
                        'created_by' =>Auth::user()->id,
                        'update_at' =>Carbon::Now()
                    ]);
                    return true;
                    // Toastr::success(__('Salário criado com sucesso'), __('toastr.success'));
                    // return redirect()->back();
                } else {
                    return false;
                    // Toastr::error(__('Caro utilizador este mês atribuído o salário já foi processado o salário, por favor digite um outro mês para atribuir o salário.'), __('toastr.error'));
                    // return redirect()->back(); 
                }
        
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 

    }
    public function add_funcao_funcionario(Request $request)
    {
        // return $request;
        if (strtotime($request->dataIncial)<=strtotime($request->dataFinal)) {
            $getuser_contrato=DB::table('funcionario_with_contrato as fun_with_contrato')
            ->whereNull('fun_with_contrato.deleted_at')
            ->whereNull('fun_with_contrato.deleted_by')
            ->where('fun_with_contrato.id_user','=',$request->idFuncionario)
            ->get();
            if ($getuser_contrato->isEmpty()) {
                Toastr::error(__('Este funcionario encontra-se sem menhum contrato'), __('toastr.error'));
                return redirect()->back();
            } else {
                foreach ($request->funcao as $key => $valor) {
                    $getfuncoes=DB::table('fun_with_contrato_at_funcao as funcao')
                    ->whereNull('funcao.deleted_at')
                    ->whereNull('funcao.deleted_by')
                    ->where('funcao.status_contrato_at_funcao','=','ativo')
                    ->where('funcao.id_fun_with_contrato','=',$getuser_contrato[0]->id)
                    ->where('funcao.id_funcao_rh','=',$valor)
                    ->get();
                    if ($getfuncoes->isEmpty()) {
                        DB::table('fun_with_contrato_at_funcao')->insert([
                            'id_fun_with_contrato' =>$getuser_contrato[0]->id,
                            'id_funcao_rh' =>$valor,
                            'nota' =>$request->nota,
                            'data_inicio_contrato_at_funcao' =>$request->dataIncial,
                            'data_fim_contrato_at_funcao' =>$request->dataFinal,
                            'status_contrato_at_funcao' =>"ativo",
                            'created_by' =>Auth::user()->id,
                            'created_at' =>Carbon::Now(),
                            'update_by' =>Auth::user()->id
                            
                        ]);
                        Toastr::success(__('Função atribuída com sucesso'), __('toastr.success'));
                        return redirect()->back();
                    }
                }
                return redirect()->back();
            }
        }else{
            Toastr::error(__('Interlavo de contrato para exercer função incorreta, data de inicio do contrato tem que ser igual ou menor que data final do contrato'), __('toastr.error'));
            return redirect()->back();
        }
            
    }
    public function deleteFuncaoFuncionario($getIdFuncao)
    {  
         try{
            $affected = DB::table('fun_with_contrato_at_funcao as fun_with_cont_at_funcao')
              ->where('fun_with_cont_at_funcao.id', $getIdFuncao)
              ->update(['fun_with_cont_at_funcao.status_contrato_at_funcao' => 'panding']);

            return response()->json($affected);
            
        } catch (Exception | Throwable $e) {
            // return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }

    


  


    public function RecursogestaoSatffLista()
    {
        try {
            
            //$roles = Role::with(['currentTranslation'])->whereIn('id', [7,23,21,26,45,11,17,19,18,12,13,16,8,25,14,22,27,43,41,42,20,24])->get();
            if(auth()->user()->hasRole(['coordenador-curso'])){
                return redirect()->intended('/pt/users/getDocente');
             }else{
                $roles = Role::with(['currentTranslation'])->whereNotIn('roles.id', [6, 15, 2])->get();

                $getfun_with_type_contrato = DB::table('fun_with_type_contrato as fun_with_type_cont')
                    ->join('funcionario_with_contrato as fun_with_cont', 'fun_with_cont.id', '=', 'fun_with_type_cont.id_fun_with_contrato')
                    ->select([
                        'fun_with_type_cont.id as id_fun_with_type_cont',
                        'fun_with_cont.id as id_fun_with_cont',
                        'fun_with_cont.id_user as id_user',
                        'fun_with_type_cont.id_cargo as id_cargo'
                    ])
                    // ->where('fun_with_type_cont.data_fim_contrato','=',$dataAtual)
                    ->where('fun_with_type_cont.status_contrato', '=', 'ativo')
                    ->whereNull('fun_with_type_cont.deleted_by')
                    ->whereNull('fun_with_cont.deleted_by')
                    ->whereNull('fun_with_cont.deleted_at')
                    ->get();


                // return [$getfun_with_type_contrato, $roles];

                $data = [
                    'action' => 'GESTÃO DO STAFF',
                    // 'roles'=>$roles
                ];

                return view('RH::gestaoStaff.listagem', compact('data'));
            }
            
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxRecursogestaoSatffLista()
    {
        try {
            $qtdcontratos = 1;

            $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
                ->join('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
                })
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('fun_type_contrato.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })


                ->join('recurso_humano_salario_funcionario as rh_salario_funcionario',function ($q){
                    $q->on('rh_salario_funcionario.id_fun_with_contrato','=','fun_with_contrato.id');
                })

                ->leftJoin('documento_user_recurso_humano as contrato_documento',function ($q){
                    $q->on('contrato_documento.id_fun_type_contrato','=','fun_type_contrato.id');
                })
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'fun_type_contrato.id_cargo') 
                ->where('cargo_traducao.active',1)
                ->where('cargo_traducao.language_id',1)
                ->whereNull('fun_type_contrato.deleted_at')
                ->whereNull('fun_type_contrato.deleted_by')
                ->where('fun_type_contrato.status_contrato','=','ativo')
                // ->where('rh_salario_funcionario.status_salario','=','panding')
                ->select([
                    'fun_type_contrato.id_cargo as cargo_id',
                    'fun_type_contrato.id as fun_type_contrato_id',
                    'fun_type_contrato.data_inicio_conrato as inicio_contrato',
                    'fun_type_contrato.data_fim_contrato as fim_contrato',
                    'cargo_traducao.display_name as nome_cargo',
                    'fun_type_contrato.status_contrato as status_contrato',
                    'fun_type_contrato.id_fun_with_contrato as id_fun_with_contrato',
                    'fun_with_contrato.id_user as user_id',
                    'rh_salario_funcionario.salarioBase as salarioBase',
                    'rh_salario_funcionario.dataSalario as dataSalario',
                    'rh_salario_funcionario.id_cargo as sb_id_cargo',
                    'rh_salario_funcionario.status_salario as status_salario',
                    'rh_salario_funcionario.id as id',
                    'rh_salario_funcionario.created_at as created_at',
                    'fullName.value as created_by',
                    'contrato_documento.arquivo as contratoPDF'
                ])
                ->distinct('rh_salario_funcionario.id_cargo')
                // ->distinct('rh_salario_funcionario.id')
                ->orderBy('rh_salario_funcionario.created_at','DESC')
            ->get();
           
            $usuarios_cargos = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
                ->leftJoin('user_parameters as user_namePar',function($join){
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                    ->where('user_namePar.parameters_id',1);
                }) 
              ->where('cargo_traducao.active',1)
              ->where('cargo_traducao.language_id',1)
              ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
              ->whereNotIn("cargo_traducao.role_id",[6,15,2])
              ->whereNotIn("usuario.id",users_exemplo())
              ->select([
                'user_namePar.value as nome_usuario',
                'usuario.email as email',
                'usuario.id as id_user',
                ])
            ->distinct('usuario.id')
            ->orderBy('usuario.id','ASC')
            ->get(); 


            
            return Datatables::of($usuarios_cargos)
                ->addColumn('roles', function ($item) use($getcontratos) {
                    return view('RH::gestaoStaff.datatables.roles',compact('item', 'getcontratos'));
                })
                ->rawColumns(['roles'])
                ->addIndexColumn()
            ->make(true);

            
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function RecursogestaoSatff()
    {
        try {
            
            //$roles = Role::with(['currentTranslation'])->whereIn('id', [7,23,21,26,45,11,17,19,18,12,13,16,8,25,14,22,27,43,41,42,20,24])->get();
               $roles = Role::with(['currentTranslation'])->whereNotIn('roles.id', [6,15,2])->get();
            $data=[
                'action'=>'GESTÃO DO STAFF',
                'roles'=>$roles
            ];
                return view('RH::gestaoStaff.index',compact('data'));
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    }


    public function add_funcionario()
    {
        try {
            // $data=[
            //     'action'=>'GESTÃO DO STAFF'
            // ];
            // return view('RH::gestaoStaff.criar_funcionario',compact('data'));
            return view('Users::users.user_staff');

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function add_colaborador()
    {
        try {
            $data=[
                'action'=>'GESTÃO DO STAFF'
            ];
                return view('RH::gestaoStaff.criar_colaborador',compact('data'));
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }
    }

    public function getUsersRecurso()
    {
        //$roles= [7,23,21,26,45,11,17,19,18,12,13,16,8,25,14,22,27,43,41,42,20,24];
        $users =  User::with(['roles' => function ($q) {
            $q->with(['currentTranslation' ]);
            // $q->whereNotIn('roles.id', [6,15]);
        }], ['parameters' => function ($q) {
            $q->whereIn('code', ['nome', 'n_mecanografico']);
        }])
        // ->whereNotIn('id', [6,15])
        //->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
        ->whereNotIn("users.id",users_exemplo())
        ->whereHas("roles", function ($q) {
             $q->whereNotIn("id",[6,15,2]);
        });


        return DataTables::eloquent($users)
                // ->addColumn('actions', function ($item) {
                //     return view('RH::gestaoStaff.datatables.actions',compact('item'));
                // })
                ->addColumn('users', function ($user) {
                    $displayName = $this->formatUserName($user);
                    return $displayName;
                })
                ->addColumn('roles', function ($item) {
                    return $item->roles->map(function ($role) {
                        return $role->currentTranslation->display_name;
                    })->implode(", ");
                    //return $item->roles->first()->currentTranslation->display_name;
                })
                // ->rawColumns(['actions'])
                ->addIndexColumn()
                ->make(true);

    }
    
    
    public function getUserByRoles($getRoles)
    {
        try{
        //quando pesquisar por cargo se um utilizador tiver mais de um cargo, exibir so o cargo em questao.
        $users =  User::with(['roles' => function ($q) {
            $q->with([
                    'currentTranslation'
                ]);
        }])->whereHas("roles", function ($role) use ($getRoles) {
            $role->where('id', $getRoles);
        })
        //->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416]);
        ->whereNotIn('users.id', users_exemplo());
        // ->get();
        // return response()->json($users);

        return DataTables::eloquent($users)
                // ->addColumn('actions', function ($item) {
                //     return view('RH::gestaoStaff.datatables.actions',compact('item'));
                // })
                ->addColumn('users', function ($user) {
                    $displayName = $this->formatUserName($user);
                    return $displayName;
                })
                ->addColumn('roles', function ($item) use($getRoles){
                    return $item->roles->map(function ($role) use($getRoles) {
                        if ($role->id == $getRoles) {
                            return $role->currentTranslation->display_name;
                            exit;
                        }
                    })->implode(" ");
                    //return $item->roles->first()->currentTranslation->display_name;
                })
                // ->rawColumns(['actions'])
                ->addIndexColumn()
                ->make(true);
        } catch (Exception | Throwable $e) {
            // return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    
    
    public function Getperfil_func($id_user)
    {
        try {
            $dataUser=DB::table('users as user')
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('user.id', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->leftJoin('user_parameters as telefone', function ($join) {
                    $join->on('user.id', '=', 'telefone.users_id')
                    ->where('telefone.parameters_id', 36);
                })
                ->leftJoin('user_parameters as whatsapp', function ($join) {
                    $join->on('user.id', '=', 'whatsapp.users_id')
                    ->where('whatsapp.parameters_id', 39);
                })
                ->leftJoin('user_parameters as anoAniversario', function ($join) {
                    $join->on('user.id', '=', 'anoAniversario.users_id')
                    ->where('anoAniversario.parameters_id', 5);
                })
                ->leftJoin('users_departments as departament', function ($join) {
                    $join->on('departament.user_id', '=', 'user.id');
                })
                ->leftJoin('department_translations as trad_departament', function ($join) {
                    $join->on('trad_departament.departments_id', '=', 'departament.departments_id');
                    $join->on('trad_departament.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('trad_departament.active', '=', DB::raw(true));
                })
                ->where('user.id','=',$id_user)
                ->select([
                    'user.image as image',
                    'fullName.value as name',
                    'telefone.value as telefone',
                    'whatsapp.value as whatsapp',
                    'anoAniversario.value as anoAniversario',
                    'trad_departament.display_name as name_departamento',
                    'user.id as id_user'
                ])
            ->first();
            // ->get();
            
            $getIdade= explode('-',$dataUser->anoAniversario);
            $dataUser->{'idade'}=(int)date('Y') -(int)$getIdade[0];


            $roleUser =  User::with(['roles' => function ($q) {
                $q->with(['currentTranslation' ]);
                }], ['parameters' => function ($q) {
                    $q->whereIn('code', ['nome', 'n_mecanografico']);
                }])
                ->whereIn('users.id', [$id_user])
            ->get();
 
             $roleUser=collect($roleUser)->map(function ($item) use($dataUser) { 
                 $dataUser->{'cargos'}=$item->roles->map(function ($role) {
                    return $role->currentTranslation->display_name;
                })->implode(", ");
                return $item;
            });

            // return $dataUser;


            $roles = Role::with(['currentTranslation'])->whereNotIn('roles.id', [6,15])->get();
            $data=[
                'action'=>'GESTÃO DO STAFF',
                'roles'=>$roles
            ];

                return view('RH::gestaoStaff.index',compact('data','dataUser','roleUser'));
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
    
    protected function formatUserName($user)
    {
        $fullNameParameter = $user->parameters->firstWhere('code', 'nome');
        $fullName = $fullNameParameter && $fullNameParameter->pivot->value ?
            $fullNameParameter->pivot->value : $user->name;

        $studentNumberParameter = $user->parameters->firstWhere('code', 'n_mecanografico');
        $studentNumber = $studentNumberParameter && $studentNumberParameter->pivot->value ?
            $studentNumberParameter->pivot->value : "000";

        return "$fullName";
    }



    public function create_horas_laroral() {
        // return 123;

        $action = 'CONFIGURAÇÕES RH';

        return view('RH::configuracoes.horaLaboral.index',compact('action'));
    }

    public function ajaxHoraLaboral()
    {   
        
        $horas_laboral = DB::table('rh_horario_laboral as rh_horario_lab')
        ->leftJoin('user_parameters as fullName', function ($join) {
            $join->on('rh_horario_lab.created_by', '=', 'fullName.users_id')
            ->where('fullName.parameters_id', 1);
        })
        ->leftJoin('user_parameters as fullName1', function ($join) {
            $join->on('rh_horario_lab.update_by', '=', 'fullName1.users_id')
            ->where('fullName1.parameters_id', 1);
        })
        ->select([
            'rh_horario_lab.id as id',
            'rh_horario_lab.dias_trabalho as dias_trabalho',
            'rh_horario_lab.entrada_1 as entrada_1', 
            'rh_horario_lab.saida_1 as saida_1',
            'rh_horario_lab.entrada_2 as entrada_2', 
            'rh_horario_lab.saida_2 as saida_2',
            'rh_horario_lab.total_horas_dia as total_horas_dia',
            'rh_horario_lab.total_minutos_dia as total_minutos_dia',
            'fullName.value as created_by',
            'rh_horario_lab.created_at as created_at',
            'fullName1.value as update_by',
            'rh_horario_lab.update_at as update_at'
        ])                    
        ->whereNull('rh_horario_lab.deleted_at')
        ->whereNull('rh_horario_lab.deleted_by')
        ->get();

        return DataTables::of($horas_laboral)
        ->addColumn('actions', function ($item) {
            return view('RH::configuracoes.horaLaboral.datatables.actions',compact('item'));
        })
        ->addColumn('time', function ($item) {
            return view('RH::configuracoes.horaLaboral.datatables.time',compact('item'));
        })
        ->rawColumns(['actions', 'time'])
        ->addIndexColumn()
        ->toJson();
        // ->make(true);

    }

    
       public function ajaxHoraLaboralContrato($contrato)
    {   

        $data = explode(",",$contrato);
        
        return DB::table('funcionario_with_contrato as ftc')
        ->leftjoin("fun_with_type_contrato as fc","fc.id_fun_with_contrato","=","ftc.id")        
        ->leftjoin("rh_horario_laboral as hl","hl.id","=","fc.tipo_presenca")
        ->where('ftc.id_user',$data[0])
        ->whereNull('ftc.deleted_at')
        ->select(["entrada_1 as entrada","saida_1 as saida"])
        ->get();

        // $horas_laboral = DB::table('rh_horario_laboral as rh_horario_lab')
        // ->leftjoin("")         
        // ->whereNull('rh_horario_lab.deleted_by')
        // ->get();

       

    }
    
    // MÉTODO QUE RETORNA O INTERVALO DE HORAS ENTRE DOIS PERÍODOS
    private function intervalo_duas_horas($hora_entrada, $hora_saida) {
        if (strtotime($hora_entrada) <= strtotime($hora_saida)){
            // dd("A hora final é maior.");
            // TOTAL DE HORAS MANHÃ
            $entrada = explode(':', $hora_entrada);
            $saida = explode(':', $hora_saida);
            $intervalo_horas = ($saida[0] - $entrada[0]);
            $intervalo_minutos = ($saida[1] - $entrada[1]);
            
        }
        else{
            // dd("A hora inicial é maior.");
            // TOTAL DE HORAS MANHÃ
            $entrada = explode(':', $hora_entrada);
            $saida = explode(':', $hora_saida);
            $intervalo_horas = ($entrada[0] - $saida[0]);
            $intervalo_minutos = ($entrada[1] - $saida[1]);
        }

        return [$intervalo_horas, $intervalo_minutos];
    }


    public function store_horas_laroral(Request $request) {

        // return $request;

        try {
            
            $total_dias_trabalho = $request->dias_trabalho;

            $total_horas = ($this->intervalo_duas_horas($request->entrada_1, $request->saida_1)[0]) + ($this->intervalo_duas_horas($request->entrada_2, $request->saida_2)[0]);
            $total_minutos = ($this->intervalo_duas_horas($request->entrada_1, $request->saida_1)[1]) + ($this->intervalo_duas_horas($request->entrada_2, $request->saida_2)[1]);                

            if ($total_minutos < 0) {
                $total_horas = $total_horas  - 1;
                $total_minutos = $total_minutos * (-1);
            }

            if ($total_horas < 0) {
                $total_horas = ($total_horas  + 1) * (-1);
                // $total_minutos = $total_minutos * (-1);
            }

            DB::table('rh_horario_laboral')->insert([
                'dias_trabalho' => $total_dias_trabalho,
                'entrada_1' => $request->entrada_1, 
                'saida_1' => $request->saida_1,
                'entrada_2' => $request->entrada_2, 
                'saida_2' => $request->saida_2,
                'total_horas_dia' => $total_horas,
                'total_minutos_dia' => $total_minutos,
                'created_by' => Auth::user()->id,
                'created_at' =>Carbon::Now()
            ]);
            
            Toastr::success(__('Horário laboral criado com sucesso'), __('toastr.success'));
            return redirect()->back();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }


    public function edit_horas_laboral(Request $request) {
        
        try{ 
            // return $request;

            $total_horas = ($this->intervalo_duas_horas($request->entrada_1, $request->saida_1)[0]) + ($this->intervalo_duas_horas($request->entrada_2, $request->saida_2)[0]);
            $total_minutos = ($this->intervalo_duas_horas($request->entrada_1, $request->saida_1)[1]) + ($this->intervalo_duas_horas($request->entrada_2, $request->saida_2)[1]);                

            if ($total_minutos < 0) {
                $total_horas = $total_horas  - 1;
                $total_minutos = $total_minutos * (-1);
            }

            if ($total_horas < 0) {
                $total_horas = ($total_horas  + 1) * (-1);
                // $total_minutos = $total_minutos * (-1);
            }

            $horas_laboral = DB::table('rh_horario_laboral as horario_laboral')
            ->where('horario_laboral.id','=',$request->idHorasLaboral)
            ->whereNull('horario_laboral.deleted_at')
            ->whereNull('horario_laboral.deleted_by')            
            ->update([
                'horario_laboral.dias_trabalho' => $request->dias_trabalho,
                'horario_laboral.entrada_1' => $request->entrada_1, 
                'horario_laboral.saida_1' => $request->saida_1,
                'horario_laboral.entrada_2' => $request->entrada_2, 
                'horario_laboral.saida_2' => $request->saida_2,
                'horario_laboral.total_horas_dia' => $total_horas,
                'horario_laboral.total_minutos_dia' => $total_minutos,
                'horario_laboral.update_by' => Auth::user()->id,
                'horario_laboral.update_at' =>Carbon::Now()
            ]);
            Toastr::success(__('Horário laboral editada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }


    public function delet_horas_laboral($request) {
        
        try{ 
            
            // return $request;

            $horas_laboral = DB::table('rh_horario_laboral as horario_laboral')
            ->where('horario_laboral.id','=',$request)            
            ->whereNull('horario_laboral.deleted_at')
            ->whereNull('horario_laboral.deleted_by')  
            ->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' =>Carbon::Now()
            ]);
            Toastr::success(__('Horário laboral eliminado com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
        
    }
    
    
        public function generateUserByRolePDF($getRoles)
    {
        try {
            // Fetching users with the specified role, including matricula
            $users = User::query()
                ->with([
                    'roles' => function ($q) {
                        $q->with(['currentTranslation']);
                    }
                ])
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
                ->whereHas('roles', function ($role) use ($getRoles) {
                    $role->where('id', $getRoles);
                })
                ->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
                ->select([
                    'users.*',
                    'up_bi.value as n_bi',
                    'up_meca.value as matricula',
                    'full_name.value as nome_completo',

                    // Adding matricula to the select statement
                ])
                ->orderBy('users.name', 'ASC')
                ->get();


            if ($users->isEmpty()) {
                Toastr::error("Cargo sem dados");
                return redirect()->back();
            }

            // Grouping users by their role's translation (if necessary)
            $model = $users->groupBy(function ($user) use ($getRoles) {
                return $user->roles->firstWhere('id', $getRoles)->currentTranslation->display_name;
            });

            // Fetching institution details for the PDF header
            $institution = DB::table('institutions')->latest()->first();

            // Defining PDF metadata
            $titulo_documento = "Lista de " . $model->keys()->first();
            $documentoGerado_documento = "Documento gerado em " . date("Y/m/d");

            // Generating the PDF
            $pdf = PDF::loadView(
                'RH::gestaoStaff.pdf.relatorio-pdf', // Create a view for the PDF
                compact('model', 'institution', 'titulo_documento', 'documentoGerado_documento')
            );

            // Setting PDF options
            $pdf->setOption('margin-top', '3.2mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-bottom', '7.4mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setPaper('a4', 'portrait');

            $pdf_name = "Lista_de_" . $model->keys()->first() . "_" . date("Y-m-d");

            // Returning the PDF for download or display
            return $pdf->stream($pdf_name . '.pdf');
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return response()->json($e->getMessage(), 500);
        }
    }
    
}


