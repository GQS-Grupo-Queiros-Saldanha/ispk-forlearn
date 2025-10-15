<?php
namespace App\Modules\RH\Controllers;

use App\Helpers\LanguageHelper;
use App\Modules\Cms\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Role;
use App\Modules\GA\Models\DayOfTheWeek;
use App\Modules\GA\Models\Schedule;
use App\Modules\GA\Models\ScheduleType;

use App\Modules\Payments\Models\Bank;
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
use Error;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use PDF;

class RecursoHumanoSalarioController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function anularPagamentoFuncionario()
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

            $userContrato=DB::table('funcionario_with_contrato')
                ->whereNull('funcionario_with_contrato.deleted_at')
            ->get();

            $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
                ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
                })
                ->whereNull('fun_type_contrato.deleted_at')
                ->whereNull('fun_type_contrato.deleted_by')
                ->where('fun_type_contrato.status_contrato','=','ativo')
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
                'action'=>'SALÁRIO E HONORÁRIO',
                'getcontratos'=>$getcontratos,
                'getSalariofuncionario'=>$getSalariofuncionario,
                'userContrato'=>$userContrato,
                'users'=>$users,
                'getLocalizedMonths'=>getLocalizedMonths()
            ];
            return view('RH::salarioHonorario.folhaPagamento.anularSalario')->with($data);
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function ajaxAnulauReciboSalario($getIdRole_idFun)
    {
        try{
            // $explodeId=explode(',',$getIdRole_idFun);
            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->select([
                    'processam_sl.year as year',
                    'processam_sl.nota as nota',
                    'processam_sl.recibo_num as recibo_num',
                    'processam_sl.month as month',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.created_at as created_at',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valor_falta as valor_falta',
                    'fun_with_type_cont.id_cargo as id_cargo',
                    'processam_sl.deleted_by as deleted_by',
                    'processam_sl.deleted_at as deleted_at'
                    
                ])
                ->whereNull('processam_sl.deleted_by')
                ->whereNull('processam_sl.deleted_at')
                ->where('fun_with_cont.id_user', $getIdRole_idFun)
                // ->where('fun_with_type_cont.id_cargo', $explodeId[0])
                ->orderBy('processam_sl.created_at','DESC')  
            ->latest()
            ->take(1)
            ->get();
            // ->last();
            $getFunProcesso=collect($getFunProcessoSalario)->map(function($item){
                    getLocalizedMonths()->map(function ($element)use($item){
                        if ($element['id']==$item->month) {
                            $item->{'year_month'}=$element['display_name']."/".$item->year;
                        }
                    });   
                return $item;
            });
            return response()->json(['data'=>$getFunProcessoSalario]);
        } catch (Exception | Throwable $e) {
            return response()->json(['data'=>$e]);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxListaRecibosAnulados()
    {
        try{
            

            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
                {
                    $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
                })
                ->join('users as use','use.id','=','fun_with_cont.id_user')
                ->leftJoin('user_parameters as full_name',function($join)
                {
                    $join->on('full_name.users_id','=','use.id')
                    ->where('full_name.parameters_id',1); 
                })

                ->join('users as use1','use1.id','=','processam_sl.created_by')
                ->leftJoin('user_parameters as full_name1',function($join)
                {
                    $join->on('full_name1.users_id','=','use1.id')
                    ->where('full_name1.parameters_id',1); 
                })

                ->leftJoin('user_parameters as bi',function($join)
                {
                    $join->on('bi.users_id','=','use.id')
                    ->where('bi.parameters_id',14); 
                })
                ->leftJoin('user_parameters as anulado_por',function($join)
                {
                    $join->on('anulado_por.users_id','=','processam_sl.deleted_by')
                    ->where('anulado_por.parameters_id',1); 
                })
                ->leftJoin('user_parameters as nif',function($join)
                {
                    $join->on('nif.users_id','=','use.id')
                    ->where('nif.parameters_id',49); 
                })

                ->join('role_translations as role_trans',function($join)
                {
                    $join->on('role_trans.role_id','=','fun_with_type_cont.id_cargo')
                    ->where('role_trans.language_id','=',1)
                    ->where('role_trans.active','=',1); 
                })
                ->select([
                    'processam_sl.year as year',
                    'bi.value as bi',
                    'nif.value as nif',

                    'processam_sl.nota as nota',
                    'processam_sl.recibo_num as recibo_num',
                    'processam_sl.month as month',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.created_at as created_at',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valor_falta as valor_falta',
                    'processam_sl.deleted_at as deleted_at',
                    'full_name.value as name_funcionario',
                    'full_name1.value as criado_por',
                    'anulado_por.value as anulado_por',
                    'processam_sl.salario_base as salarioBase',
                    'role_trans.display_name as name_cargo',
                    'use.id as id_user',
                    'role_trans.role_id as id_cargo'
                ])
                ->whereNull('role_trans.deleted_at')
                ->where('processam_sl.deleted_by','!=',null)
                ->where('processam_sl.deleted_at','!=',null)
                
            ->get();

            


            // return response()->json(['data'=>$getFunProcessoSalario]);
            return DataTables::of($getFunProcessoSalario)
            ->addIndexColumn()
            ->toJson();
        } catch (Exception | Throwable $e) {
            return response()->json(['data'=>$e]);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function AnulauReciboVencimentoFunc(Request $request)
    {   
        
        try{

            // return $request;
            
            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')                
                ->whereIn('processam_sl.id',$request->referencia)
            ->update([
                'processam_sl.deleted_by' => Auth::user()->id,
                'processam_sl.deleted_at' => Carbon::Now()
            ]);
                
                  
            Toastr::success(__('O recibo de vencimento foi anulado com sucesso'), __('toastr.success'));
            return redirect()->back();
                
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function folhaPagamentoFuncionario()
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

            $userContrato=DB::table('funcionario_with_contrato')
            ->whereNull('funcionario_with_contrato.deleted_at')
            ->get();
            $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
                ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
                })
                ->whereNull('fun_type_contrato.deleted_at')
                ->whereNull('fun_type_contrato.deleted_by')
                ->where('fun_type_contrato.status_contrato','=','ativo')
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
                'action'=>'SALÁRIO E HONORÁRIO',
                'getcontratos'=>$getcontratos,
                'getSalariofuncionario'=>$getSalariofuncionario,
                'userContrato'=>$userContrato,
                'users'=>$users
            ];
            return view('RH::salarioHonorario.folhaPagamento.folhaPagamentoFuncionario')->with($data);
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function index()
    {
        try {
            $data=[
                'action'=>'SALÁRIO E HONORÁRIO'
            ];
                return view('RH::salarioHonorario.index')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
    * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function processamentoSalario()
    {
        try {
        // return horas_docente(180);
           
            // return (count($d) * 45);           
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
                    $q->whereIn('parameters_id',[1,36,39,5,45,25,14]);
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
                    elseif($paramete->id==14) {
                        $item->{'bi_num'} =$paramete->pivot->value;
                    }
                }
                return $item;
            });
        

            $getcontratos=DB::table('fun_with_type_contrato as fun_type_contrato')
                ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('fun_with_contrato.id','=','fun_type_contrato.id_fun_with_contrato');
                })
                ->whereNull('fun_type_contrato.deleted_at')
                ->whereNull('fun_type_contrato.deleted_by')
                ->where('fun_type_contrato.status_contrato','=','ativo')
                ->select([
                    'fun_type_contrato.id as id_contrato',
                    'fun_type_contrato.id_fun_with_contrato as id_fun_with_contrato',
                    'fun_type_contrato.tipo_presenca as tipo_presenca',
                    'fun_type_contrato.id_cargo as id_cargo',
                    'fun_type_contrato.data_inicio_conrato as data_inicio_conrato',
                    'fun_type_contrato.data_fim_contrato as data_fim_contrato',
                    'fun_type_contrato.status_contrato as status_contrato',
                    'fun_with_contrato.*'
                ])
            ->get();

            $getSalariofuncionario=DB::table('recurso_humano_salario_funcionario as rh_salario_funcionario')
                ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('rh_salario_funcionario.id_fun_with_contrato','=','fun_with_contrato.id');
                })
                ->leftJoin('rh_horario_laboral as rh_hora_laboral','rh_hora_laboral.id','=','rh_salario_funcionario.id_horalaboral')
                ->select([
                    'rh_hora_laboral.dias_trabalho as dias_trabalho',
                    'rh_hora_laboral.total_horas_dia as total_horas_dia',
                    'rh_salario_funcionario.id_horalaboral as id_horalaboral',
                    'rh_salario_funcionario.created_at as created_at',
                    'rh_salario_funcionario.id_cargo  as id_cargo',
                    'rh_salario_funcionario.id_fun_with_contrato as id_fun_with_contrato',
                    'fun_with_contrato.id_user as id_user',
                    'rh_salario_funcionario.salarioBase as salarioBase',
                    'rh_salario_funcionario.id as id'
                ])
                ->orderBy('rh_salario_funcionario.created_at','DESC')
                ->whereNull('rh_salario_funcionario.deleted_by') 
                ->whereNull('rh_hora_laboral.deleted_by')
                ->whereNull('rh_hora_laboral.deleted_at') 
            ->get();
            
            $getProcessoSalario=DB::table('processamento_salarial as processamento_sl')
            ->whereNull('processamento_sl.deleted_at')
            ->whereNull('processamento_sl.deleted_by')
            ->get();
            $getProcessoSalario=collect($getProcessoSalario)->map(function ($item){
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$element['display_name']."/".$item->year;
                    }
                });
              return $item;
            })->groupBy('year_month');
            $getfuncoesFuncionario=DB::table('fun_with_contrato_at_funcao as fun_with_cont_at_funcao')
            ->join('recurso_humano_at_funcao as rh_at_funcao','rh_at_funcao.id','=','fun_with_cont_at_funcao.id_funcao_rh')
            ->join('funcionario_with_contrato as fun_with_cont','fun_with_cont.id','=','fun_with_cont_at_funcao.id_fun_with_contrato')
            ->where('fun_with_cont_at_funcao.status_contrato_at_funcao', '=', 'ativo')
            ->whereNull('rh_at_funcao.deleted_at')
            ->whereNull('fun_with_cont_at_funcao.deleted_at')
            ->whereNull('fun_with_cont_at_funcao.deleted_by')
            ->get();
            $getBancos=DB::table('banks')
            ->whereNull('banks.deleted_at')
            ->whereNull('banks.deleted_by')
            ->where('banks.type_conta_entidade','=','rh')
            ->get();
            
            $data=[
                'action'=>'SALÁRIO E HONORÁRIO',
                'getcontratos'=>$getcontratos,
                'getSalariofuncionario'=>$getSalariofuncionario,
                'getProcessoSalario'=>$getProcessoSalario,
                'users'=>$users,
                'bancos'=>$getBancos,
                'getfuncoesFuncionario'=>$getfuncoesFuncionario,
                // 'getFunProcessoSalario'=>$getFunProcessoSalario,
                // 'getFunProcesso'=>$getFunProcesso
            ];
                return view('RH::salarioHonorario.folhaPagamento.processamentoSalario')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
    // PAGAMENTOS - RECIBOS - MENSAL
    public function folhaPagamentoMes() {
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
                $q->whereIn('parameters_id',[1,36,39,5,45,25,14]);
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
                elseif($paramete->id==14) {
                    $item->{'bi_num'} =$paramete->pivot->value;
                }
            }
            return $item;
        });

        $getProcessoSalario=DB::table('processamento_salarial as processamento_sl')
            ->whereNull('processamento_sl.deleted_at')
            ->whereNull('processamento_sl.deleted_by')
            ->get();
            $getProcessoSalario=collect($getProcessoSalario)->map(function ($item){
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$element['display_name']."/".$item->year;
                    }
                });
              return $item;
            })->groupBy('year_month');


        $data=[
            'action'=>'SALÁRIO E HONORÁRIO',
            'users'=>$users,
            'getProcessoSalario'=>$getProcessoSalario,
        ];

        
        return view('RH::salarioHonorario.folhaPagamento.processamentoSalarioMensal')->with($data);

    }
    
    


    public function ajaxDocentePlanoAula($getIdUser)
    {   
        $disciplinaDocente=false;
        $getSalariofuncionario=DB::table('recurso_humano_salario_funcionario as rh_salario_funcionario')
                ->leftJoin('funcionario_with_contrato as fun_with_contrato',function ($q){
                    $q->on('rh_salario_funcionario.id_fun_with_contrato','=','fun_with_contrato.id');
                })
                ->leftJoin('rh_horario_laboral as rh_hora_laboral','rh_hora_laboral.id','=','rh_salario_funcionario.id_horalaboral')
                ->select([
                    'rh_hora_laboral.dias_trabalho as dias_trabalho',
                    'rh_hora_laboral.total_horas_dia as total_horas_dia',
                    'rh_salario_funcionario.id_horalaboral as id_horalaboral',
                    'rh_salario_funcionario.created_at as created_at',
                    'rh_salario_funcionario.id_cargo  as id_cargo',
                    'rh_salario_funcionario.id_fun_with_contrato as id_fun_with_contrato',
                    'fun_with_contrato.id_user as id_user',
                    'rh_salario_funcionario.salarioBase as salarioBase',
                    'rh_salario_funcionario.id as id'
                ])
                ->where('fun_with_contrato.id_user',$getIdUser)
                ->orderBy('rh_salario_funcionario.created_at','DESC')
                ->whereNull('rh_salario_funcionario.deleted_by') 
                ->whereNull('rh_hora_laboral.deleted_by')
                ->whereNull('rh_hora_laboral.deleted_at') 
            ->get();
       
        $response = horas_docente($getIdUser);
        $totalTemposSemana = horas_docente($getIdUser) * 4;
        $valorSalarioBase = $totalTemposSemana * $getSalariofuncionario[0]->salarioBase; 
        if ($response==0) {
            $disciplinaDocente=false;
        } else {
            $disciplinaDocente=true;
        }
        
        
        
        
        $data = [
            'totalTemposSemana'=>horas_docente($getIdUser),
            'horas_docente'=>$totalTemposSemana,
            'valorSalarioBase'=>$valorSalarioBase,
            'valorTempo'=>$getSalariofuncionario[0]->salarioBase,
            'id'=>$getSalariofuncionario[0]->id,
            'disciplinaDocente'=>$disciplinaDocente
        ];
        return response()->json(['data'=>$data]);
    }
    
    // metodo que monstra o PDF depois de processar salário
    private function PDFprocessar_salario($request) {
        try{

            $expldeMonth=explode('-',$request->refrencia);
            $year= $expldeMonth[0];
            $month=$expldeMonth[1];
            $id_funContrato=$request->funcionario;
            $roles=$request->roles;
            $institution = Institution::latest()->first();
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;
            $dataCreated=null;

             $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
                {
                    $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
                })
                ->join('users as use','use.id','=','fun_with_cont.id_user')
                ->leftJoin('user_parameters as full_name',function($join)
                {
                    $join->on('full_name.users_id','=','use.id')
                    ->where('full_name.parameters_id',1); 
                })

                ->leftJoin('user_parameters as bi',function($join)
                {
                    $join->on('bi.users_id','=','use.id')
                    ->where('bi.parameters_id',14); 
                })
                ->leftJoin('user_parameters as seguranca',function($join)
                {
                    $join->on('seguranca.users_id','=','use.id')
                    ->where('seguranca.parameters_id',53); 
                })
                ->leftJoin('user_parameters as nif',function($join)
                {
                    $join->on('nif.users_id','=','use.id')
                    ->where('nif.parameters_id',49); 
                })

                ->join('role_translations as role_trans',function($join)
                {
                    $join->on('role_trans.role_id','=','fun_with_type_cont.id_cargo')
                    ->where('role_trans.language_id','=',1)
                    ->where('role_trans.active','=',1); 
                })
                ->leftJoin('historic_processamento_salario_subsidio as hist_processo_sl_subsidio',function($join)
                {
                    $join->on('hist_processo_sl_subsidio.id_processamento','=','processam_sl.id'); 
                })
                
                ->leftJoin('funcionario_contrato_subsidio as func_contr_subsidio',function ($join)
                {
                    $join->on('func_contr_subsidio.id_funcionario','=','use.id');
                    $join->on('func_contr_subsidio.id_funcionario_cargo','=','fun_with_type_cont.id_cargo');
                    $join->on('func_contr_subsidio.id','=','hist_processo_sl_subsidio.id_subsidio');
                })
                ->leftJoin('subsidio as subsid','subsid.id','=','func_contr_subsidio.id_subsidio')
                ->select([
                    'processam_sl.year as year',
                    'bi.value as bi',
                    'seguranca.value as seguranca',
                    'nif.value as nif',

                    'processam_sl.nota as nota',
                    'processam_sl.recibo_num as recibo_num',
                    'processam_sl.month as month',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.created_at as created_at',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valor_falta as valor_falta',
                    'full_name.value as name_funcionario',
                    'processam_sl.salario_base as salarioBase',
                    'role_trans.display_name as name_cargo',
                    'subsid.display_name as name_subsidio',
                    'subsid.id as id_subsidio',
                    'func_contr_subsidio.valor as valor_subsidio',
                    'use.id as id_user',
                    'role_trans.role_id as id_cargo'
                ])
                ->where('processam_sl.month','=',$month)
                ->where('processam_sl.year','=',$year)
                ->whereNull('role_trans.deleted_at')
                ->whereNull('processam_sl.deleted_by')
                ->whereNull('processam_sl.deleted_at')
                ->orderBy('role_trans.display_name','ASC')
                ->whereIn('fun_with_cont.id_user', $id_funContrato)
                ->whereIn('fun_with_type_cont.id_cargo', $roles)
                
            ->get()
            ->groupBy(['id_processam_sl','name_funcionario','name_cargo','bi','seguranca','nif']);
           
            $dataCreated=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $getCargo){
                            foreach($getNomeFun as $getCargo){
                                foreach($getCargo as $getbi){
                                    foreach($getbi as $getseguranca){
                                        foreach($getseguranca as $getnif){
                                
                                            return $getnif->created_at;

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            $getFunProcessoSalario=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $getCargo){
                            foreach($getCargo as $getBi){
                                foreach($getBi as $getSeguranca){
                                    foreach($getSeguranca as $getNif){
                                        getLocalizedMonths()->map(function ($element)use($getNif,$item){
                                            if ($element['id']==$getNif->month) {
                                                $getNif->{'year_month'}=$element['display_name']."/".$getNif->year;
                                            }
                                        });

                                    }
                                }
                            }
                        }
                    }
                }
                return $item;
            });
            foreach ($dataCreated as $key => $value) {
                  $dataCreated=Carbon::parse($value)->day.'/'.Carbon::parse($value)->month. '/'.Carbon::parse($value)->year;
            }

            $getFunProcessoImposto=DB::table('imposto_year as impostYear')
                ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
                ->join('taxa_imposto as taxa','taxa.id_impostoYear','=','impostYear.id')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev')
                ->select([
                    'taxa.id as taxa_id',
                    'taxa.parcela_fixa as parcela_fixa',
                    'taxa.valor_inicial as valor_inicial',
                    'taxa.valor_final as valor_final',
                    'code_dev.code as nome_code',
                    'taxa.excesso as excesso',
                    'taxa.taxa as taxa',
                    'impostYear.id as id_impostYear',
                    'impost.display_name as name_imposto',
                    'impost.id as id_imposto'
                ])
                ->whereNull('impost.deleted_by')
                ->whereNull('impostYear.deleted_at')
                ->whereNull('taxa.deleted_by')
                ->orderBy('impost.display_name','ASC')
                ->orderBy('taxa.taxa','ASC')
            ->get();

            $gethistoricProcessoImposto=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
                {
                    $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
                })
                ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
                ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
                ->select([
                    'hist_processo_imposto.id_processamento as id_processamento',
                    'impost.id as id_impost',
                    'code_dev.code as nome_code',
                    'impost.display_name as name_imposto',
                    'impost.discricao as discricao',
                    'hist_processo_imposto.id_impostoYear as id_impostoYear'
                ])
                ->where('processam_sl.month','=',$month)
                ->where('processam_sl.year','=',$year)
                ->whereIn('fun_with_cont.id_user', $id_funContrato)
                ->whereIn('fun_with_type_cont.id_cargo', $roles)
                ->orderBy('impost.display_name','ASC')
            ->get();

            $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
            ->whereNull('sub_impost.deleted_at')
            ->whereNull('sub_impost.deleted_by')
            ->get();

            // return view('RH::salarioHonorario.folhaPagamento.reciboSalario.reciboSalario_singular')->with($data);  
            //  return view('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')->with($data);
            $data=[
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'getFunProcessoSalario'=>$getFunProcessoSalario,
                'dataCreated'=>$dataCreated,
                'getSubsidioImposto'=>$getSubsidioImposto,
                'getFunProcessoImposto'=>$getFunProcessoImposto,
                'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
            ];
            // return $request;
            

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSingular", $data);              
            $pdf->setOption('margin-top', '3mm');
            $pdf->setOption('margin-left', '3mm');
            $pdf->setOption('margin-bottom', '1.5cm');
            $pdf->setOption('margin-right', '3mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            // $pdf->setOption('footer-html', $footer_html);
            $pdf->setPaper('a4','landscape');       
            return $pdf->stream('Forlearn | Recibo de vencimento.pdf');
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
               
    }

    // Metodo que processa salário do funcionário
    public function createProcessoSalario(Request $request)
    {     
        try{
            $expldeMonth=explode('-',$request->refrencia);
            $year= $expldeMonth[0];
            $month=$expldeMonth[1];
            $id_processamentos=[];
            $confirmaboole=false;
            $num_recibo=0;
            $id_funcionario=$request->funcionario;
            $funRH=Auth::user()->name;
            $valorSalarioBase=0;
            $resultadoFalta=0;
            $valorFalta=0;
            $dataIncio=$year.'-'.$month.'-'.'00';
            $dataFim=$year.'-'.$month.'-'.'31';

            if (isset($request->roles)) {
                $di_roles=$request->roles;
                // return $request;
                foreach ($id_funcionario as $key => $id_posicion) {
                    foreach ($di_roles as $key => $id_posicionRoles) {
                        $confirmaboole=false;
                        $valorSalarioBase=0;
                        // consultar o funcionário com seu respentivo cargo.
                        $getFun_type_contrato=DB::table('funcionario_with_contrato as fun_with_contra')
                            ->join('fun_with_type_contrato as fun_type_contra','fun_type_contra.id_fun_with_contrato','=','fun_with_contra.id')
                            ->join('user_parameters as full_name',function($join){
                                    $join->on('full_name.users_id','=','fun_with_contra.id_user')
                                    ->where('full_name.parameters_id',1); 
                            })
                            ->select([
                                'fun_type_contra.id as idfun_type_contra',
                                'fun_with_contra.id as id_fun_with_contra',
                                'full_name.value as nome_user'
                            ])
                            ->whereNull('fun_type_contra.deleted_by')
                            ->whereNull('fun_type_contra.deleted_at')
                            ->where('fun_with_contra.id_user','=',$id_posicion)
                            ->where('fun_type_contra.id_cargo','=',$id_posicionRoles)
                            ->where('fun_type_contra.status_contrato','=','ativo')
                        ->get();
                       
                        if (!$getFun_type_contrato->isEmpty()) {
                            $getProcessamentoMonth=DB::table('processamento_salarial as processamento_sl')
                                ->where('processamento_sl.year','=',$year)
                                ->where('processamento_sl.month','=',$month)
                                ->where('processamento_sl.id_fun_type_contrato','=',$getFun_type_contrato[0]->idfun_type_contra)
                                ->whereNull('processamento_sl.deleted_by')
                                ->whereNull('processamento_sl.deleted_by')
                            ->get();
                           
                            if($getProcessamentoMonth->isEmpty()) {
                     
                                // Consultar os subsídios
                                 $getSubFuncionario=DB::table('funcionario_contrato_subsidio as fun_cont_sub')
                                    ->where('fun_cont_sub.id_funcionario','=',$id_posicion)
                                    ->where('fun_cont_sub.id_funcionario_cargo','=',$id_posicionRoles)
                                    ->whereNull('fun_cont_sub.deleted_by')
                                    ->whereNull('fun_cont_sub.deleted_by')
                                ->get();
                                // Consultar os impostos
                                    $getimpost=DB::table('imposto_year as impost_year')
                                    ->where('impost_year.estado','=',1)
                                    ->whereNull('impost_year.deleted_by')
                                    ->whereNull('impost_year.deleted_by')
                                ->get();

                                 
                                
 
                                // salario do funcionario os calculos são feitos com base ao tipo de cargo (Docente, administrativo)
                                $getRHSalario_fun=DB::table('recurso_humano_salario_funcionario as rh_salario_fun')
                                    ->leftJoin('rh_horario_laboral as rh_hora_laboral','rh_hora_laboral.id','=','rh_salario_fun.id_horalaboral')
                                    ->where('rh_salario_fun.id_fun_with_contrato','=',$getFun_type_contrato[0]->id_fun_with_contra)
                                    ->where('rh_salario_fun.id_cargo','=',$id_posicionRoles)
                                    ->select([
                                        'rh_salario_fun.id as id_rh_salario_fun',
                                        'rh_salario_fun.salarioBase as salarioBase',
                                        'rh_hora_laboral.total_horas_dia as total_horas_dia',
                                        'rh_hora_laboral.dias_trabalho as dias_trabalho',
                                        'rh_hora_laboral.total_minutos_dia as total_minutos_dia',
                                        'rh_salario_fun.id_horalaboral as id_horalaboral',
                                        'rh_salario_fun.created_at as created_at',
                                    ])
                                    ->latest('rh_salario_fun.created_at')
                                    ->whereNull('rh_salario_fun.deleted_at')
                                    ->whereNull('rh_salario_fun.deleted_by')
                                    ->whereNull('rh_hora_laboral.deleted_by')
                                    ->whereNull('rh_hora_laboral.deleted_at')
                                    ->orderBy('rh_salario_fun.created_at','DESC')
                                ->get();
                                
                                

                                if(!$getRHSalario_fun->isEmpty()){
                                    if ($getRHSalario_fun[0]->id_horalaboral!=null) {
                                       

                                        
                                        $qdtFaltaHora=ajaxfuncionarioTotalHoras($id_posicion,$id_posicionRoles,$dataIncio,$dataFim)[0];// quatidade de dias que funcionário docente faltou
                                        $qdtFaltaMin=ajaxfuncionarioTotalHoras($id_posicion,$id_posicionRoles,$dataIncio,$dataFim)[1];// quatidade de dias que funcionário docente faltou
                                        
                                        $qtdHora=(int) (($qdtFaltaHora * 60));
                                        $qtdMin=(int)($qdtFaltaMin);
                                        // TOTAL DE HORAS FALTADAS PELO FUNCIONÁRIO
                                        $resultadoFalta=(int)($qtdHora + $qtdMin) / 60;

                                        $semanas=4;// quantidade de semanas no mês.

                                        // CALCULO DO VALOR POR HORA
                                        $valorSalarioBaseHora = $getRHSalario_fun[0]->salarioBase / (($getRHSalario_fun[0]->total_horas_dia) * $getRHSalario_fun[0]->dias_trabalho); 
                                        
                                        $valorSalarioBase = $getRHSalario_fun[0]->salarioBase;
                                        
                                        $resultadoFalta=(int)($qdtFaltaHora + ($qdtFaltaMin / 60));                                       
                                        // TOTAL DE DESCONTO A SER APLICAD AO FUNCIONÁRIO
                                        $valorFalta=$valorSalarioBaseHora * $resultadoFalta; 
                                        
                                        $confirmaboole= true;

                                    }
                                    else{
                                        // bloco  de codigo que vai fai fazer os calculos do salário base de funcionario Docente.
                                        $totalTemposSemana = horas_docente($id_posicion);
                                        if ($totalTemposSemana==0) {
                                            $confirmaboole=false;
                                        }else{
                                            $qdtFaltaHora=ajaxfuncionarioTotalHoras($id_posicion,$id_posicionRoles,$dataIncio,$dataFim)[0];// quatidade de dias que funcionário docente faltou
                                            $qdtFaltaMin=ajaxfuncionarioTotalHoras($id_posicion,$id_posicionRoles,$dataIncio,$dataFim)[1];// quatidade de dias que funcionário docente faltou
                                            
                                            $qtdHora=(int) (($qdtFaltaHora * 60) / 45);
                                            $qtdMin=(int)($qdtFaltaMin / 45);
                                            $resultadoFalta=(int)($qtdHora + $qtdMin);

                                            $semanas=4;// quantidade de semanas no mês. 
                                            $valorFalta=$resultadoFalta * $getRHSalario_fun[0]->salarioBase;                                          
                                            $totalTemposSemana = (horas_docente($id_posicion) *  $semanas) - $resultadoFalta;
                                            $valorSalarioBase = (horas_docente($id_posicion) *  $semanas) * $getRHSalario_fun[0]->salarioBase;
                                            $confirmaboole= true;
                                        }

                                    }
                                }

                                // verficar os banco que estão associado funcionario com um deternado cargo.
                                $getBanco_contra_funcionario=DB::table('rh_contrato_bank as contrato_with_bank')
                                    ->whereNull('contrato_with_bank.deleted_by')
                                    ->whereNull('contrato_with_bank.deleted_at')
                                    ->where('contrato_with_bank.status','ativo')
                                    ->where('contrato_with_bank.id_fun_with_type_contrato',$getFun_type_contrato[0]->idfun_type_contra)
                                ->first();
                                $lastReciboFun= DB::table('processamento_salarial')
                                ->latest()
                                ->orderBy('processamento_salarial.id','DESC')
                                ->get();

                                if ($lastReciboFun->isEmpty()) {
                                    $num_recibo= (int)1;
                                }else{
                                    $num_recibo= (int)$lastReciboFun[0]->recibo_num + 1;
                                }
                                $getRecibo= DB::table('processamento_salarial')
                                ->where('processamento_salarial.recibo_num','=',$num_recibo)
                                ->get();
                               
                                if ($getRecibo->isEmpty() && $confirmaboole==true) {
                                    $setInsert=DB::table('processamento_salarial')->insertGetId([
                                        'id_fun_type_contrato' =>$getFun_type_contrato[0]->idfun_type_contra,
                                        'id_salariotempo_funcionario' => $getRHSalario_fun[0]->id_rh_salario_fun,
                                        'id_conta_bank_fun_contrato' => isset($getBanco_contra_funcionario->id) ? $getBanco_contra_funcionario->id: null,
                                        'salario_base' => (double)$valorSalarioBase,
                                        'recibo_num' => $num_recibo,
                                        'year' => $year,
                                        'month' => $month,
                                        'qtd_falta' => $resultadoFalta,
                                        'valor_falta' => (double) $valorFalta,
                                        'nota' => $request->nota,
                                        'valorReembolso' => $request->valorReembolso,
                                        'created_at' => Carbon::Now(),
                                        'created_by' => Auth::user()->id,
                                        'update_at' => Carbon::Now(),
                                        'update_by' => Auth::user()->id
                                    ]);
                                    
                                    
                                    
                                    
                                    
                                    // criar notificação do processamento de salário.
                                    $mesSalario="";
                                    foreach (getLocalizedMonths() as $key => $getMonth) {
                                        if ($getMonth['id']==$month) {
                                           $mesSalario=$getMonth['display_name']."/".$year;
                                        }
                                    }

                                    $body="";
                                    $body='<div> <p>Caro(a) Funcionário(a) '.$getFun_type_contrato[0]->nome_user.', o seu pagamento referente a <b>'.$mesSalario .'</b> foi efetuado com sucesso, por favor ve.</p> 
                                    <br><br>
                                    
                                    </div>';
                                    $icon= "fas fa-receipt";
                                    $subjet="[Recursos humanos]-Processamento de salário";
                                    $destinetion=[];
                                    $destinetion[]=$id_posicion;
                                    $file="";
                                    $file="/RH/recurso_humanos_getFolhaSalarioNotificacoes/".$setInsert;
                                    notification($icon,$subjet,$body,$destinetion,$file,null);
                                    
                                    
                                    
                                    
                                    
                                    
                                    $id_processamentos[]=$setInsert;
                                    foreach ($getSubFuncionario as $key => $item_subfun) {
                                        DB::table('historic_processamento_salario_subsidio')->insert([
                                            'id_processamento'=>$setInsert,
                                            'id_subsidio'=>$item_subfun->id
                                        ]);

                                        // atuliazar o subsídio dizer que foi utilizado.
                                        $updateSubsidio = DB::table('subsidio as subsidio')
                                            ->join('funcionario_contrato_subsidio as fun_contr_sub','fun_contr_sub.id_subsidio','=','subsidio.id')
                                            ->where('fun_contr_sub.id_subsidio','=',$item_subfun->id_subsidio)
                                            ->where('fun_contr_sub.id_funcionario','=',$id_posicion)
                                            ->where('fun_contr_sub.id_funcionario_cargo','=',$id_posicionRoles)
                                            ->where('subsidio.status','=','panding')
                                            ->whereNull('subsidio.deleted_by')
                                            ->whereNull('subsidio.deleted_by')
                                            ->update([
                                                'subsidio.status' => 'ativo'
                                            ]);

                                    }
                                    foreach ($getimpost as $chave => $item_impostoYear) {
                                        DB::table('historic_processamento_salario_imposto')->insert([
                                            'id_processamento'=>$setInsert,
                                            'id_impostoYear'=>$item_impostoYear->id
                                        ]);
                                    }

                                    // atualizar o salario quando for processado
                                    DB::table('recurso_humano_salario_funcionario as rh_salario_fun')
                                    ->where('rh_salario_fun.id',$getRHSalario_fun[0]->id_rh_salario_fun)
                                    ->update([
                                        'status_salario' =>"ativo",
                                    ]);

                                    // atualizar o imposto que já foi utilizado.
                                    $updateImposto = DB::table('imposto_year as impost_year')
                                    ->join('imposto as impost','impost.id','=','impost_year.id_imposto')
                                    ->where('impost_year.estado', 1)
                                    ->where('impost_year.status','=','panding')
                                    ->where('impost.status','=','panding')
                                    ->whereNull('impost_year.deleted_by')
                                    ->whereNull('impost_year.deleted_by')
                                    ->update([
                                        'impost_year.status' => 'ativo',
                                        'impost.status' => 'ativo'
                                    ]);

                                    // criar notificação do processamento de salário.
                                    $mesSalario="";
                                    foreach (getLocalizedMonths() as $key => $getMonth) {
                                        if ($getMonth['id']==$month) {
                                           $mesSalario=$getMonth['display_name']."/".$year;
                                        }
                                    }

                                    $body='<div> <p>Caro(a) Funcionário(a) '.$getFun_type_contrato[0]->nome_user.', o seu pagamento referente a <b>'.$mesSalario .'</b> foi efetuado com sucesso, por favor ve.</p> 
                                    <br><br>
                                    
                                    </div>';
                                    $icon= "fas fa-receipt";
                                    $subjet="[Recursos humanos]-Processamento de salário";
                                    $destinetion[]=$id_posicion;
                                    $file="/RH/recurso_humanos_getFolhaSalarioNotificacoes/".$setInsert;
                                    notification($icon,$subjet,$body,$destinetion,$file,null);

                                    
                                   
                                }else{
                                    
                                    Toastr::error(__('Caro gestor RH Sr., <b>'.$funRH.'</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br>Por favor repita a operação.'), __(''));
                                    return redirect()->back();
                                }
                            }
                        }
                    }
                   
                }
                    // if ($confirmaboole==true) {
                        // return $this->PDFprocessar_salario($request);
                        
                         $nomeUser=Auth::user()->name;
                        Toastr::success(__('Caro utilizador <b>'.$nomeUser.'</b>. \n\n  Processamento de salário foi feito com sucesso.'), __('toastr.success'));
                        return redirect()->back();
                        
                    // } else {
                    //     Toastr::error(__('Caro gestor RH Sr., <b>'.$funRH.'</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br>Por favor repita a operação.'), __(''));
                    //     return redirect()->back();
                    // }
                
            }else{
                Toastr::error(__('Caro gestor RH Sr., <b>'.$funRH.'</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br>Por favor repita a operação.'), __(''));
                return redirect()->back();
            }

            
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxGetReciboSalario($getIdRole_idFun)
    {
        try{
            // $explodeId=explode(',',$getIdRole_idFun);
            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->select([
                    'processam_sl.year as year',
                    'processam_sl.nota as nota',
                    'processam_sl.recibo_num as recibo_num',
                    'processam_sl.month as month',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.created_at as created_at',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valorReembolso as valorReembolso',
                    'processam_sl.valor_falta as valor_falta',
                    'fun_with_type_cont.id_cargo as id_cargo'
                    
                ])
                ->whereNull('processam_sl.deleted_by')
                ->whereNull('processam_sl.deleted_at')
                ->where('fun_with_cont.id_user', $getIdRole_idFun)
                // ->where('fun_with_type_cont.id_cargo', $explodeId[0])
                ->orderBy('processam_sl.recibo_num','DESC')
            ->get();
            $getFunProcesso=collect($getFunProcessoSalario)->map(function($item){
                    getLocalizedMonths()->map(function ($element)use($item){
                        if ($element['id']==$item->month) {
                            $item->{'year_month'}=$element['display_name']."/".$item->year;
                        }
                    });   
                return $item;
            });
            return response()->json(['data'=>$getFunProcessoSalario]);
        } catch (Exception | Throwable $e) {
            return response()->json(['data'=>$e]);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    public function PDFpesquisaFolhaSalario(Request $request)
    {   
        
        try{

            $id_funContrato=$request->funcionario;
            $roles=$request->roles;
            $institution = Institution::latest()->first();
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;
            $dataCreated=null;
            // return $request;
            
            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->join('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
                {
                    $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
                })
                ->join('users as use','use.id','=','fun_with_cont.id_user')
                ->leftJoin('user_parameters as full_name',function($join)
                {
                    $join->on('full_name.users_id','=','use.id')
                    ->where('full_name.parameters_id',1); 
                })

                ->leftJoin('user_parameters as bi',function($join)
                {
                    $join->on('bi.users_id','=','use.id')
                    ->where('bi.parameters_id',14); 
                })
                ->leftJoin('user_parameters as seguranca',function($join)
                {
                    $join->on('seguranca.users_id','=','use.id')
                    ->where('seguranca.parameters_id',53); 
                })
                ->leftJoin('user_parameters as nif',function($join)
                {
                    $join->on('nif.users_id','=','use.id')
                    ->where('nif.parameters_id',49); 
                })

                ->join('role_translations as role_trans',function($join)
                {
                    $join->on('role_trans.role_id','=','fun_with_type_cont.id_cargo')
                    ->where('role_trans.language_id','=',1)
                    ->where('role_trans.active','=',1); 
                })
                ->leftJoin('historic_processamento_salario_subsidio as hist_processo_sl_subsidio',function($join)
                {
                    $join->on('hist_processo_sl_subsidio.id_processamento','=','processam_sl.id'); 
                })
                ->leftJoin('funcionario_contrato_subsidio as func_contr_subsidio',function ($join)
                {
                    $join->on('func_contr_subsidio.id_funcionario','=','use.id');
                    $join->on('func_contr_subsidio.id_funcionario_cargo','=','fun_with_type_cont.id_cargo');
                    $join->on('func_contr_subsidio.id','=','hist_processo_sl_subsidio.id_subsidio');
                })
                ->leftJoin('subsidio as subsid','subsid.id','=','func_contr_subsidio.id_subsidio')
                ->select([
                    'processam_sl.year as year',
                    'bi.value as bi',
                    'seguranca.value as seguranca',
                    'nif.value as nif',

                    'processam_sl.nota as nota',
                    'processam_sl.recibo_num as recibo_num',
                    'processam_sl.month as month',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.created_at as created_at',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valorReembolso as valorReembolso',
                    'processam_sl.valor_falta as valor_falta',
                    'full_name.value as name_funcionario',
                    'processam_sl.salario_base as salarioBase',
                    'role_trans.display_name as name_cargo',
                    'subsid.display_name as name_subsidio',
                    'subsid.id as id_subsidio',
                    'func_contr_subsidio.valor as valor_subsidio',
                    'use.id as id_user',
                    'role_trans.role_id as id_cargo'
                ])
                ->whereNull('role_trans.deleted_at')
                ->whereNull('processam_sl.deleted_by')
                ->whereNull('processam_sl.deleted_at')
                ->orderBy('role_trans.display_name','ASC')
                ->where('fun_with_cont.id_user', $id_funContrato)
                ->whereIn('processam_sl.id',$request->referencia)
                
            ->get()
            ->groupBy(['id_processam_sl','name_funcionario','name_cargo','bi','seguranca','nif']);

            $dataCreated=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $getCargo){
                            foreach($getNomeFun as $getCargo){
                                foreach($getCargo as $getbi){
                                    foreach($getbi as $getseguranca){
                                        foreach($getseguranca as $getnif){
                                
                                            return $getnif->created_at;

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            $getFunProcessoSalario=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $getCargo){
                            foreach($getCargo as $getBi){
                                foreach($getBi as $getSeguranca){
                                    foreach($getSeguranca as $getNif){
                                        getLocalizedMonths()->map(function ($element)use($getNif,$item){
                                            if ($element['id']==$getNif->month) {
                                                $getNif->{'year_month'}=$element['display_name']."/".$getNif->year;
                                            }
                                        });

                                    }
                                }
                            }
                        }
                    }
                }
                return $item;
            });
            foreach ($dataCreated as $key => $value) {
                  $dataCreated=Carbon::parse($value)->day.'/'.Carbon::parse($value)->month. '/'.Carbon::parse($value)->year;
            }

            $getFunProcessoImposto=DB::table('imposto_year as impostYear')
                ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
                ->join('taxa_imposto as taxa','taxa.id_impostoYear','=','impostYear.id')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev')
                ->select([
                    'taxa.id as taxa_id',
                    'taxa.parcela_fixa as parcela_fixa',
                    'taxa.valor_inicial as valor_inicial',
                    'taxa.valor_final as valor_final',
                    'code_dev.code as nome_code',
                    'taxa.excesso as excesso',
                    'taxa.taxa as taxa',
                    'impostYear.id as id_impostYear',
                    'impost.display_name as name_imposto',
                    'impost.id as id_imposto'
                ])
                ->whereNull('impost.deleted_by')
                ->whereNull('impostYear.deleted_at')
                ->whereNull('taxa.deleted_by')
                ->orderBy('impost.display_name','ASC')
                ->orderBy('taxa.taxa','ASC')
            ->get();

            $gethistoricProcessoImposto=DB::table('processamento_salarial as processam_sl')
            ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
            })
            ->join('funcionario_with_contrato as fun_with_cont',function($join)
            {
                $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
            })
            ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
            {
                $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
            })
            ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
            ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
            ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
            ->select([
                'hist_processo_imposto.id_processamento as id_processamento',
                'impost.id as id_impost',
                'code_dev.code as nome_code',
                'impost.display_name as name_imposto',
                'impost.discricao as discricao',
                'hist_processo_imposto.id_impostoYear as id_impostoYear'
            ])
            
            ->where('fun_with_cont.id_user', $id_funContrato)
            ->whereIn('processam_sl.id', $request->referencia)
            ->orderBy('impost.display_name','ASC')
            ->get();

            $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
            ->whereNull('sub_impost.deleted_at')
            ->whereNull('sub_impost.deleted_by')
            ->get();
            

             $data=[
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'getFunProcessoSalario'=>$getFunProcessoSalario,
                'dataCreated'=>$dataCreated,
                'getSubsidioImposto'=>$getSubsidioImposto,
                'getFunProcessoImposto'=>$getFunProcessoImposto,
                'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
            ];

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSingular", $data);              
            $pdf->setOption('margin-top', '3mm');
            $pdf->setOption('margin-left', '3mm');
            $pdf->setOption('margin-bottom', '1.5cm');
            $pdf->setOption('margin-right', '3mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            // $pdf->setOption('footer-html', $footer_html);
            $pdf->setPaper('a4','landscape');       
            return $pdf->stream('Forlearn | Recibo de vencimento.pdf');
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function getFolhaSalarioNotificacoes($id_processoSalario)
    {
        // return $id_processoSalario;
        $institution = Institution::latest()->first();
        $titulo_documento = "DOCUMENTO";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 1;
        $dataCreated=null;
        $user_id=Auth::user()->id;

         $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
            ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
            })
            ->join('funcionario_with_contrato as fun_with_cont',function($join)
            {
                $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
            })
            ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
            {
                $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
            })
            ->join('users as use','use.id','=','fun_with_cont.id_user')
            ->leftJoin('user_parameters as full_name',function($join)
            {
                $join->on('full_name.users_id','=','use.id')
                ->where('full_name.parameters_id',1); 
            })

            ->leftJoin('user_parameters as bi',function($join)
            {
                $join->on('bi.users_id','=','use.id')
                ->where('bi.parameters_id',14); 
            })
            ->leftJoin('user_parameters as seguranca',function($join)
            {
                $join->on('seguranca.users_id','=','use.id')
                ->where('seguranca.parameters_id',53); 
            })
            ->leftJoin('user_parameters as nif',function($join)
            {
                $join->on('nif.users_id','=','use.id')
                ->where('nif.parameters_id',49); 
            })

            ->join('role_translations as role_trans',function($join)
            {
                $join->on('role_trans.role_id','=','fun_with_type_cont.id_cargo')
                ->where('role_trans.language_id','=',1)
                ->where('role_trans.active','=',1); 
            })
            ->leftJoin('historic_processamento_salario_subsidio as hist_processo_sl_subsidio',function($join)
            {
                $join->on('hist_processo_sl_subsidio.id_processamento','=','processam_sl.id'); 
            })
            
            ->leftJoin('funcionario_contrato_subsidio as func_contr_subsidio',function ($join)
            {
                $join->on('func_contr_subsidio.id_funcionario','=','use.id');
                $join->on('func_contr_subsidio.id_funcionario_cargo','=','fun_with_type_cont.id_cargo');
                $join->on('func_contr_subsidio.id','=','hist_processo_sl_subsidio.id_subsidio');
            })
            ->leftJoin('subsidio as subsid','subsid.id','=','func_contr_subsidio.id_subsidio')
            ->select([
                'processam_sl.year as year',
                'bi.value as bi',
                'seguranca.value as seguranca',
                'nif.value as nif',

                'processam_sl.nota as nota',
                'processam_sl.recibo_num as recibo_num',
                'processam_sl.month as month',
                'processam_sl.id as id_processam_sl',
                'processam_sl.created_at as created_at',
                'processam_sl.qtd_falta as qtd_falta',
                'processam_sl.valorReembolso as valorReembolso',
                'processam_sl.valor_falta as valor_falta',
                'full_name.value as name_funcionario',
                'processam_sl.salario_base as salarioBase',
                'role_trans.display_name as name_cargo',
                'subsid.display_name as name_subsidio',
                'subsid.id as id_subsidio',
                'func_contr_subsidio.valor as valor_subsidio',
                'use.id as id_user',
                'role_trans.role_id as id_cargo'
            ])
            ->where('processam_sl.id','=',$id_processoSalario)
            ->where('fun_with_cont.id_user','=',$user_id)
            ->whereNull('role_trans.deleted_at')
            ->whereNull('processam_sl.deleted_by')
            ->whereNull('processam_sl.deleted_at')
            ->orderBy('role_trans.display_name','ASC')
           
            
        ->get()
        ->groupBy(['id_processam_sl','name_funcionario','name_cargo','bi','seguranca','nif']);
       
        if ($getFunProcessoSalario->isEmpty()) {
             Toastr::error(__('Caro utilizador Sr., <b>'.Auth::user()->name.'</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br> A folha de vencimento não encontrada.'), __(''));
          return redirect()->back();
        }
        $dataCreated=collect($getFunProcessoSalario)->map(function($item){
            foreach($item as $getIdprocesso){
                foreach($getIdprocesso as $getNomeFun){
                    foreach($getNomeFun as $getCargo){
                        foreach($getNomeFun as $getCargo){
                            foreach($getCargo as $getbi){
                                foreach($getbi as $getseguranca){
                                    foreach($getseguranca as $getnif){
                            
                                        return $getnif->created_at;

                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
        
        $getFunProcessoSalario=collect($getFunProcessoSalario)->map(function($item){
            foreach($item as $getIdprocesso){
                foreach($getIdprocesso as $getNomeFun){
                    foreach($getNomeFun as $getCargo){
                        foreach($getCargo as $getBi){
                            foreach($getBi as $getSeguranca){
                                foreach($getSeguranca as $getNif){
                                    getLocalizedMonths()->map(function ($element)use($getNif,$item){
                                        if ($element['id']==$getNif->month) {
                                            $getNif->{'year_month'}=$element['display_name']."/".$getNif->year;
                                        }
                                    });

                                }
                            }
                        }
                    }
                }
            }
            return $item;
        });
        foreach ($dataCreated as $key => $value) {
              $dataCreated=Carbon::parse($value)->day.'/'.Carbon::parse($value)->month. '/'.Carbon::parse($value)->year;
        }

        $getFunProcessoImposto=DB::table('imposto_year as impostYear')
            ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
            ->join('taxa_imposto as taxa','taxa.id_impostoYear','=','impostYear.id')
            ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev')
            ->select([
                'taxa.id as taxa_id',
                'taxa.parcela_fixa as parcela_fixa',
                'taxa.valor_inicial as valor_inicial',
                'taxa.valor_final as valor_final',
                'code_dev.code as nome_code',
                'taxa.excesso as excesso',
                'taxa.taxa as taxa',
                'impostYear.id as id_impostYear',
                'impost.display_name as name_imposto',
                'impost.id as id_imposto'
            ])
            ->whereNull('impost.deleted_by')
            ->whereNull('impostYear.deleted_at')
            ->whereNull('taxa.deleted_by')
            ->orderBy('impost.display_name','ASC')
        ->get();

        $gethistoricProcessoImposto=DB::table('processamento_salarial as processam_sl')
            ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
            })
            ->join('funcionario_with_contrato as fun_with_cont',function($join)
            {
                $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
            })
            ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
            {
                $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
            })
            ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
            ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
            ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
            ->select([
                'hist_processo_imposto.id_processamento as id_processamento',
                'impost.id as id_impost',
                'code_dev.code as nome_code',
                'impost.display_name as name_imposto',
                'impost.discricao as discricao',
                'hist_processo_imposto.id_impostoYear as id_impostoYear'
            ])
            ->where('processam_sl.id','=',$id_processoSalario)
            ->where('fun_with_cont.id_user','=',$user_id)
            ->orderBy('impost.display_name','ASC')
        ->get();

        $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
        ->whereNull('sub_impost.deleted_at')
        ->whereNull('sub_impost.deleted_by')
        ->get();

        // return view('RH::salarioHonorario.folhaPagamento.reciboSalario.reciboSalario_singular')->with($data);  
        //  return view('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')->with($data);
        $data=[
            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento,
            'getFunProcessoSalario'=>$getFunProcessoSalario,
            'dataCreated'=>$dataCreated,
            'getSubsidioImposto'=>$getSubsidioImposto,
            'getFunProcessoImposto'=>$getFunProcessoImposto,
            'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
        ];
        // return $request;
        

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSingular", $data);              
        $pdf->setOption('margin-top', '3mm');
        $pdf->setOption('margin-left', '3mm');
        $pdf->setOption('margin-bottom', '1.5cm');
        $pdf->setOption('margin-right', '3mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        // $pdf->setOption('footer-html', $footer_html);
        $pdf->setPaper('a4','landscape');       
        return $pdf->stream('Forlearn | Recibo de vencimento.pdf');
    }

    public function getFolhaSalarioRecibosAnulado($id_processoSalario)
    {
        // return $id_processoSalario;
        $institution = Institution::latest()->first();
        $titulo_documento = "DOCUMENTO";
        $documentoGerado_documento = "Documento gerado a";
        $documentoCode_documento = 1;
        $dataCreated=null;
        // $user_id=Auth::user()->id;

        $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
            ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
            })
            ->join('funcionario_with_contrato as fun_with_cont',function($join)
            {
                $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
            })
            ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
            {
                $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
            })
            ->join('users as use','use.id','=','fun_with_cont.id_user')
            ->leftJoin('user_parameters as full_name',function($join)
            {
                $join->on('full_name.users_id','=','use.id')
                ->where('full_name.parameters_id',1); 
            })

            ->leftJoin('user_parameters as bi',function($join)
            {
                $join->on('bi.users_id','=','use.id')
                ->where('bi.parameters_id',14); 
            })
            ->leftJoin('user_parameters as seguranca',function($join)
            {
                $join->on('seguranca.users_id','=','use.id')
                ->where('seguranca.parameters_id',53); 
            })
            ->leftJoin('user_parameters as nif',function($join)
            {
                $join->on('nif.users_id','=','use.id')
                ->where('nif.parameters_id',49); 
            })

            ->join('role_translations as role_trans',function($join)
            {
                $join->on('role_trans.role_id','=','fun_with_type_cont.id_cargo')
                ->where('role_trans.language_id','=',1)
                ->where('role_trans.active','=',1); 
            })
            ->leftJoin('historic_processamento_salario_subsidio as hist_processo_sl_subsidio',function($join)
            {
                $join->on('hist_processo_sl_subsidio.id_processamento','=','processam_sl.id'); 
            })
            
            ->leftJoin('funcionario_contrato_subsidio as func_contr_subsidio',function ($join)
            {
                $join->on('func_contr_subsidio.id_funcionario','=','use.id');
                $join->on('func_contr_subsidio.id_funcionario_cargo','=','fun_with_type_cont.id_cargo');
                $join->on('func_contr_subsidio.id','=','hist_processo_sl_subsidio.id_subsidio');
            })
            ->leftJoin('subsidio as subsid','subsid.id','=','func_contr_subsidio.id_subsidio')
            ->select([
                'processam_sl.year as year',
                'bi.value as bi',
                'seguranca.value as seguranca',
                'nif.value as nif',

                'processam_sl.nota as nota',
                'processam_sl.recibo_num as recibo_num',
                'processam_sl.month as month',
                'processam_sl.id as id_processam_sl',
                'processam_sl.created_at as created_at',
                'processam_sl.qtd_falta as qtd_falta',
                'processam_sl.valorReembolso as valorReembolso',
                'processam_sl.valor_falta as valor_falta',
                'full_name.value as name_funcionario',
                'processam_sl.salario_base as salarioBase',
                'role_trans.display_name as name_cargo',
                'subsid.display_name as name_subsidio',
                'subsid.id as id_subsidio',
                'func_contr_subsidio.valor as valor_subsidio',
                'use.id as id_user',
                'role_trans.role_id as id_cargo'
            ])
            ->where('processam_sl.id','=',$id_processoSalario)
            // ->where('fun_with_cont.id_user','=',$user_id)
            ->whereNull('role_trans.deleted_at')
            ->where('processam_sl.deleted_by','!=',null)
            ->where('processam_sl.deleted_at','!=',null)
            ->orderBy('role_trans.display_name','ASC')
           
            
        ->get()
        ->groupBy(['id_processam_sl','name_funcionario','name_cargo','bi','seguranca','nif']);
       
        if ($getFunProcessoSalario->isEmpty()) {
             Toastr::error(__('Caro utilizador Sr., <b>'.Auth::user()->name.'</b>.<br><br>Houve uma sobreposição momentânea na submissão de dados.<br><br> A folha de vencimento não encontrada.'), __(''));
          return redirect()->back();
        }
        $dataCreated=collect($getFunProcessoSalario)->map(function($item){
            foreach($item as $getIdprocesso){
                foreach($getIdprocesso as $getNomeFun){
                    foreach($getNomeFun as $getCargo){
                        foreach($getNomeFun as $getCargo){
                            foreach($getCargo as $getbi){
                                foreach($getbi as $getseguranca){
                                    foreach($getseguranca as $getnif){
                            
                                        return $getnif->created_at;

                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
        
        $getFunProcessoSalario=collect($getFunProcessoSalario)->map(function($item){
            foreach($item as $getIdprocesso){
                foreach($getIdprocesso as $getNomeFun){
                    foreach($getNomeFun as $getCargo){
                        foreach($getCargo as $getBi){
                            foreach($getBi as $getSeguranca){
                                foreach($getSeguranca as $getNif){
                                    getLocalizedMonths()->map(function ($element)use($getNif,$item){
                                        if ($element['id']==$getNif->month) {
                                            $getNif->{'year_month'}=$element['display_name']."/".$getNif->year;
                                        }
                                    });

                                }
                            }
                        }
                    }
                }
            }
            return $item;
        });
        foreach ($dataCreated as $key => $value) {
              $dataCreated=Carbon::parse($value)->day.'/'.Carbon::parse($value)->month. '/'.Carbon::parse($value)->year;
        }

        $getFunProcessoImposto=DB::table('imposto_year as impostYear')
            ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
            ->join('taxa_imposto as taxa','taxa.id_impostoYear','=','impostYear.id')
            ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev')
            ->select([
                'taxa.id as taxa_id',
                'taxa.parcela_fixa as parcela_fixa',
                'taxa.valor_inicial as valor_inicial',
                'taxa.valor_final as valor_final',
                'code_dev.code as nome_code',
                'taxa.excesso as excesso',
                'taxa.taxa as taxa',
                'impostYear.id as id_impostYear',
                'impost.display_name as name_imposto',
                'impost.id as id_imposto'
            ])
            ->whereNull('impost.deleted_by')
            ->whereNull('impostYear.deleted_at')
            ->whereNull('taxa.deleted_by')
            ->orderBy('impost.display_name','ASC')
        ->get();

        $gethistoricProcessoImposto=DB::table('processamento_salarial as processam_sl')
            ->join('fun_with_type_contrato as fun_with_type_cont',function($join){
                $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
            })
            ->join('funcionario_with_contrato as fun_with_cont',function($join)
            {
                $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
            })
            ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
            {
                $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
            })
            ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
            ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
            ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
            ->select([
                'hist_processo_imposto.id_processamento as id_processamento',
                'impost.id as id_impost',
                'code_dev.code as nome_code',
                'impost.display_name as name_imposto',
                'impost.discricao as discricao',
                'hist_processo_imposto.id_impostoYear as id_impostoYear'
            ])
            ->where('processam_sl.id','=',$id_processoSalario)
            // ->where('fun_with_cont.id_user','=',$user_id)
            ->orderBy('impost.display_name','ASC')
        ->get();

        $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
        ->whereNull('sub_impost.deleted_at')
        ->whereNull('sub_impost.deleted_by')
        ->get();

        // return view('RH::salarioHonorario.folhaPagamento.reciboSalario.reciboSalario_singular')->with($data);  
        //  return view('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')->with($data);
        $data=[
            'institution' => $institution,
            'titulo_documento' => $titulo_documento,
            'documentoGerado_documento' => $documentoGerado_documento,
            'documentoCode_documento' => $documentoCode_documento,
            'getFunProcessoSalario'=>$getFunProcessoSalario,
            'dataCreated'=>$dataCreated,
            'getSubsidioImposto'=>$getSubsidioImposto,
            'getFunProcessoImposto'=>$getFunProcessoImposto,
            'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
        ];
        // return $request;
        

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
        $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSingular", $data);              
        $pdf->setOption('margin-top', '3mm');
        $pdf->setOption('margin-left', '3mm');
        $pdf->setOption('margin-bottom', '1.5cm');
        $pdf->setOption('margin-right', '3mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        // $pdf->setOption('footer-html', $footer_html);
        $pdf->setPaper('a4','landscape');       
        return $pdf->stream('Forlearn | Recibo de vencimento.pdf');
    }



    public function folhaPagamentoBanco(){

        try {       
        
            // return "Folha de Pagamento Banco";folhaPagamentoBanco.blade

           $bancos = Bank::where('banks.type_conta_entidade', '=', 'rh')
           ->get();

            $data=[
                'action' => 'create',
                'bancos'=>$bancos
            ];

            return view('RH::salarioHonorario.banco.folhaPagamentoBanco')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // BANCOS
    public function createBank()
    {
        try {

            //MEU CODIGO
            $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->get();

            $currentData = Carbon::now();
            $lectiveYearSelected = DB::table('lective_years')
                            ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                            ->first();
            $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
            
            //return view("Payments::datatables.bank", compact('lectiveYears', 'lectiveYearSelected'));
            $data = [
                'action' => 'create',
                'languages' => Language::whereActive(true)->get(),
            ];


            return view('RH::salarioHonorario.banco.create_bank')->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajaxBank()
    {
        try {

            $model = Bank::
            // join('users as u1', 'u1.id', '=', 'banks.created_by')
            //     ->leftJoin('users as u2', 'u2.id', '=', 'banks.updated_by')
            //     ->leftJoin('users as u3', 'u3.id', '=', 'banks.deleted_by')
            //     ->select([
            //         'banks.*',
            //         'u1.name as created_by',
            //         'u2.name as updated_by',
            //         'u3.name as deleted_by',
            //     ])
            where('banks.type_conta_entidade', '=', 'rh');

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('RH::salarioHonorario.banco.datatables.actionsBanco')->with('item', $item);
                })
                ->rawColumns(['actions'])
                ->toJson();

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }

    }

    public function storeBank(Request $request)
    {
        try {
           
            DB::beginTransaction();

            // $entidade = "rh";

            // return $request;

            // Create
            $bank = new Bank([
                'code' => $request->get('code'),
                'display_name' => $request->get('display_name'),
                // 'account_number' => $request->get('account_number'),
                // 'iban' => $request->get('iban'),
                'type_conta_entidade' => "rh"
            ]);
 
            // DB::table('banks')->insert([
            //     'code' => $request->get('code'),
            //     'display_name' => $request->get('display_name'),
            //     'account_number' => $request->get('account_number'),
            //     'iban' => $request->get('iban'),
            //     'type_conta_entidade' => $request->get('type_conta_entidade'),
            // ]);

            // return $bank;

            $bank->save();

            DB::commit();

            // Success message
            Toastr::success(__('Payments::banks.store_success_message'), __('toastr.success'));
            return redirect()->route('recurso-humano.create-banco');

        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function editaBanco(Request $request)
    {
        $updateBank = DB::table('banks')
            ->where('id', $request->id_bank)
            ->update([
            'display_name' => $request->nome,
            'code' => $request->code,
            'updated_by'=> Auth::user()->id,
            'updated_at' => Carbon::Now()
        ]);

        Toastr::success(__('Banco editado com sucesso'), __('toastr.success'));
        return redirect()->back();
    }
    
    public function deleteBanco($id)
    {
        $registro = DB::table('rh_users_bank')
            ->where('bank_id', '=',  $id)
            ->whereNull('rh_users_bank.deleted_by')
            ->whereNull('rh_users_bank.deleted_at')
        ->get(); 

            if ($registro->isEmpty()){
                $updateBank = DB::table('banks')
                    ->where('id', $id)
                    ->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' =>  Carbon::Now()
                ]);
                Toastr::success(__('Banco eliminado com sucesso'), __('toastr.success'));
                return redirect()->back();
            }
            else{
                Toastr::error(__('Caro utilizador  este banco não pode ser eliminado, foi usado para contrato de trabalho !'), __('toastr.error'));
                return redirect()->back();
            }

        
    }

    public function userBank()
    {
        try {       
        
            // return "Folha de Pagamento Banco";
            // folhaPagamentoBanco.blade

            $bancos = Bank::where('banks.type_conta_entidade', '=', 'rh')
            ->get();

            $users =  User::
            // with(['roles' => function ($q) {
            //     $q->with(['currentTranslation' ]);
            //         // $q->select([
            //         //     'roles.name as name'
            //         // ]);
            //         // $q->whereNotIn('roles.id', [6,15]);
            //     }], ['parameters' => function ($q) {
            //         $q->whereIn('code', ['nome', 'n_mecanografico']);
            //     }])
                // with(['parameters'=>function ($q)
                // {
                //     $q->whereIn('parameters_id',[1,36,39,5,45,25,14]);
                // }])
                whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("id",[6,15,2]);
                })
            ->get(); 

            $data=[
                'action' => 'GESTÃO DO STAFF',
                'bancos'=>$bancos,
                'users' => $users
            ];

            return view('RH::salarioHonorario.banco.associarBancoUser')->with($data);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    public function storeUserBank(Request $request)
    {
        // return $request;
        try {

            DB::table('rh_users_bank')->insert([
                    'user_id' => $request->get('funcionario'),
                    'bank_id' => $request->get('banco'),
                    'conta' => $request->get('conta'),
                    'iban' => $request->get('iban'),
                    'created_at' => Carbon::Now(),
                    'created_by' => Auth::user()->id,
                ]);

            Toastr::success(__('Associação feita com sucesso'), __('toastr.success'));
            return redirect()->back();
        }
        catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function eliminarBancoFuncionario(Request $request, $id)
    {
        $is_force = isset($request->is_force) && $request->is_force == "on";
        $getExiteBanco_funcionario= DB::table('rh_users_bank as rh_user_bank')
        ->join('rh_contrato_bank as rh_contr_bank', 'rh_contr_bank.id_user_bank', '=', 'rh_user_bank.id')              
        ->join('processamento_salarial as process_salarial', 'process_salarial.id_conta_bank_fun_contrato', '=', 'rh_contr_bank.id')
        ->where('rh_user_bank.id',$id)
        ->whereNull('process_salarial.deleted_at')
        ->whereNull('process_salarial.deleted_by')
        ->get();
        if ($getExiteBanco_funcionario->isEmpty() || $is_force) {

            $updateBanco = DB::table('rh_users_bank')
              ->where('id', $id)
              ->update([
                'deleted_at' => Carbon::Now(),
                'deleted_by' => Auth::user()->id
                ]);

                return response()->json("Banco eliminado com sucesso");
        }
        return response()->json($getExiteBanco_funcionario);
        //return response()->json("Caro utilizador o banco não pode ser eliminado, devido o seu uso no processamento de salário");

    }   
    public function eliminarBancoFunContrato($id)
    {
        $getExiteBanco_funcionario= DB::table('rh_users_bank as rh_user_bank')
        ->join('rh_contrato_bank as rh_contr_bank', 'rh_contr_bank.id_user_bank', '=', 'rh_user_bank.id')              
        ->join('processamento_salarial as process_salarial', 'process_salarial.id_conta_bank_fun_contrato', '=', 'rh_contr_bank.id')
        ->where('rh_contr_bank.id',$id)
        ->whereNull('process_salarial.deleted_at')
        ->whereNull('process_salarial.deleted_by')
        ->get();
        if ($getExiteBanco_funcionario->isEmpty()) {

            $updateContratoBanco = DB::table('rh_contrato_bank')
              ->where('id', $id)
              ->update([
                'deleted_at' => Carbon::Now(),
                'deleted_by' => Auth::user()->id
                ]);

                return response()->json("Banco associado ao contrato eliminado com sucesso");
        }
        return response()->json("Caro utilizador o banco associado ao contrato não pode ser eliminado, devido o seu uso no processamento de salário");
    }

    public function ajaxUserBank()
    {

        try {

            $users = DB::table('rh_users_bank')
                ->join('users', 'users.id', '=', 'rh_users_bank.user_id')
                ->leftJoin('users as users_create', 'users_create.id', '=', 'rh_users_bank.created_by')
                ->leftJoin('users as users_update', 'users_update.id', '=', 'rh_users_bank.update_by')
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('users.id', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })                 
                ->select([
                    'fullName.value as name',
                    'users.email as email',
                    'users_create.name as created_by',
                    'users_update.name as update_by',
                    'rh_users_bank.created_at as created_at',
                    'rh_users_bank.user_id as id_user',
                    'rh_users_bank.id as id'

                ])
            ->groupBy('rh_users_bank.user_id')
            ->whereNull('rh_users_bank.deleted_by')
            ->whereNull('rh_users_bank.deleted_at')
            ->get();

            $banks = DB::table('rh_users_bank')
                ->join('banks', 'banks.id', '=', 'rh_users_bank.bank_id')             
                ->select([
                    'banks.display_name as banco',
                    'banks.code as banco_sigla',
                    'rh_users_bank.user_id as id_user',                    
                    'rh_users_bank.id as id',                    
                    'rh_users_bank.created_at as created_at',
                    'rh_users_bank.conta as conta',
                    'rh_users_bank.iban as iban',
                ])
            ->distinct('fun_with_type_contrato.id_cargo')
            ->whereNull('rh_users_bank.deleted_by')
            ->whereNull('rh_users_bank.deleted_at')
            ->get();


            $contrato = DB::table('funcionario_with_contrato')           
                ->join('fun_with_type_contrato', 'fun_with_type_contrato.id_fun_with_contrato', '=', 'funcionario_with_contrato.id')              
                ->join('role_translations as role_trans',function($join)
                {
                    $join->on('role_trans.role_id','=','fun_with_type_contrato.id_cargo')
                    ->where('role_trans.language_id','=',1)
                    ->where('role_trans.active','=',1); 
                })
                ->select([
                    'funcionario_with_contrato.id_user as contrato_id_user',
                    'fun_with_type_contrato.id_cargo as contrato_id_cargo',
                    'fun_with_type_contrato.id as id',
                    'fun_with_type_contrato.data_inicio_conrato as contrato_data_inicio_conrato',
                    'fun_with_type_contrato.data_fim_contrato as contrato_data_fim_contrato',
                    'fun_with_type_contrato.status_contrato as contrato_status_contrato',
                    'role_trans.display_name as name_cargo'
                ])
            ->where('fun_with_type_contrato.status_contrato', '=', 'ativo')
            ->whereNull('fun_with_type_contrato.deleted_by')
            ->whereNull('fun_with_type_contrato.deleted_at')
            ->distinct()
            ->get();

            // return $model= $banks->merge($contrato);
            // return json_encode(array('model'=>$model));
            // return 2022;

            return Datatables::of($users)
                ->addColumn('actions', function ($item) use ($banks, $contrato) {
                    return view('RH::salarioHonorario.banco.datatables.actions', compact('item', 'banks', 'contrato'));
                })
                ->addColumn('banks', function ($item) use ($banks){
                    return view('RH::salarioHonorario.banco.datatables.banks', compact('item', 'banks'));
                })
                ->rawColumns(['actions', 'banks'])
                ->addIndexColumn()
            ->toJson();

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }

    }

    public function ajaxUserBankContrato($id)
    {
        try {
            $contrato = DB::table('rh_users_bank as rh_users_banco')
                ->join('banks as banco', 'banco.id','=','rh_users_banco.bank_id')   
                ->join('rh_contrato_bank as rh_contrato_banco', 'rh_contrato_banco.id_user_bank','=','rh_users_banco.id')   
                ->join('users as user', 'user.id', '=', 'rh_users_banco.user_id')
                ->leftJoin('user_parameters as fullCreate_name', function ($join) {
                    $join->on('rh_contrato_banco.created_by', '=', 'fullCreate_name.users_id')
                    ->where('fullCreate_name.parameters_id', 1);
                })   
                ->join('fun_with_type_contrato as fun_with_type_contr', 'fun_with_type_contr.id', '=', 'rh_contrato_banco.id_fun_with_type_contrato')              
                ->join('role_translations as role_trans',function($join){
                    $join->on('role_trans.role_id','=','fun_with_type_contr.id_cargo')
                    ->where('role_trans.language_id','=',1)
                    ->where('role_trans.active','=',1); 
                })
                ->select([
                    'user.name as nome_user',
                    'user.id as id_user',
                    'fullCreate_name.value as nome_create',
                    'banco.display_name as nome_banco',
                    'role_trans.display_name as nome_cargo',
                    'rh_contrato_banco.id as id_rh_contrato_banco',
                    'rh_contrato_banco.created_at as created_at',
                    'rh_contrato_banco.status as status'
                ])
                ->where('rh_users_banco.user_id',$id)
                ->whereNull('fun_with_type_contr.deleted_by')
                ->whereNull('rh_users_banco.deleted_by')
                ->whereNull('rh_users_banco.deleted_at')
                ->whereNull('rh_contrato_banco.deleted_at')
                ->whereNull('rh_contrato_banco.deleted_by')
                ->whereNull('fun_with_type_contr.deleted_at')
                ->distinct()
                ->get();

            return Datatables::of($contrato)
                ->addColumn('actions', function ($item) {
                    return view('RH::salarioHonorario.banco.datatables.cargo_bank',compact('item'));
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
            ->toJson();

        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function storeUserBankContrato(Request $request)
    {
        // return $request;
        try {
            $status=null;
            $registro = DB::table('rh_contrato_bank')
                ->where('id_user_bank', '=',  $request->get('banco_data'))
                ->where('id_fun_with_type_contrato', '=', $request->get('contrato_data'))
                ->whereNull('rh_contrato_bank.deleted_by')
                ->whereNull('rh_contrato_bank.deleted_at')
            ->get();

            if (count($registro) < 1)
            {
                $registro = DB::table('rh_contrato_bank')
                ->where('id_fun_with_type_contrato', '=', $request->get('contrato_data'))
                ->whereNull('rh_contrato_bank.deleted_by')
                ->whereNull('rh_contrato_bank.deleted_at')
                 ->get();
                $status= $registro->isEmpty() ? "ativo" : "panding";
                DB::table('rh_contrato_bank')->insert(
                    [
                        'id_user_bank' => $request->get('banco_data'),
                        'id_fun_with_type_contrato' => $request->get('contrato_data'),
                        'status' => $status,
                        'created_at' => Carbon::Now(),
                        'created_by' => Auth::user()->id,
                    ]
                );

                Toastr::success(__('Associação do banco com o contrato, feita com sucesso.'), __('toastr.success'));
                return redirect()->back();
            }
            else {

                Toastr::error(__('Não foi possivél associar o banco com o contrato.'), __('toastr.error'));
                return redirect()->back();

            }
        }
        catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    public function updateAtivarBancoProcessarSalario($id)
    {
        // return $id;
        $getContrato_banco = DB::table('rh_contrato_bank')
                ->where('rh_contrato_bank.id', '=',$id)
                ->whereNull('rh_contrato_bank.deleted_by')
                ->whereNull('rh_contrato_bank.deleted_at')
            ->get();

            /*
             Depois de pesquisar o contrato com banco.
             segundo passo pesquisar todos os funcionários com type de contrato de acordoa a este banco com contrato, a ser ativo.
             terceiro passo atualizar todos os contratos com banco de todo funcionário com tipo de contrato, 
            */ 
            $getFunContrato_bank=DB::table('rh_contrato_bank')
            ->where('id_fun_with_type_contrato', '=', $getContrato_banco[0]->id_fun_with_type_contrato)
            ->whereNull('rh_contrato_bank.deleted_by')
            ->whereNull('rh_contrato_bank.deleted_at')
            ->get();
            foreach ($getFunContrato_bank as $key => $item) {
                $updateAllContrato_bank_fun= DB::table('rh_contrato_bank')
                ->where('id', $item->id)
                ->update([
                  'status' => 'panding'
                  ]);
            }
            $updateAtiveContrato_bank = DB::table('rh_contrato_bank')
            ->where('id', $id)
            ->update([
              'status' => 'ativo'
              ]);
            
            Toastr::success(__('ativo com sucesso.'), __('toastr.success'));
             return redirect()->back();

    }

    public function validationContaBancaria($numero)
    {
         $numero=explode(',',$numero);
        $conta=null;
        $iban=null;
        if ($numero[0]=="conta") {
            $conta=$numero[1];
        }else{
            $iban=$numero[1];  
        }

        $usersBank = DB::table('rh_users_bank')
        ->when($iban!=null, function ($query) use($iban){
            $query->where('rh_users_bank.iban',$iban);
        })
        ->when($conta!=null, function ($query) use($conta){
            $query->where('rh_users_bank.conta',$conta);
        })
        ->whereNull('rh_users_bank.deleted_by')
        ->whereNull('rh_users_bank.deleted_at')
        ->get();
           return  $usersBank->isEmpty() ?  response()->json(true) : response()->json(false);
    }
    
    public function controlePresencaCatraca()
    {
          $usuarios= DB::table('users as usuario')
        ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
        ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
        ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
        ->leftJoin('user_parameters as user_namePar',function($join){
            $join->on('user_namePar.users_id', '=', 'usuario.id')
            ->where('user_namePar.parameters_id',1);
        }) 
        ->leftJoin('user_parameters as foto',function($join){
            $join->on('foto.users_id', '=', 'usuario.id')
            ->where('foto.parameters_id',25);
        }) 
        ->where('cargo_traducao.active',1)
        ->where('cargo_traducao.language_id',1)
        ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
        ->whereNotIn("cargo_traducao.role_id",[6,15,2])
        ->select([
            'user_namePar.value as nome_usuario',
            'usuario.email as email',
            'usuario.id as id_user',
            'foto.value as foto'
            ])
            ->distinct('usuario.id')
        ->orderBy('usuario.id','ASC')
        ->get()
        ->map(function ($q){
            $q->{'full_nameEmail'}=$q->nome_usuario .' ( '. $q->email .' )';

            return $q;
         });

         return view('RH::salarioHonorario.controle-presencas.controle-presenca-catraca.index',compact('usuarios'));
    }
     public function ajaxControlePresencaCatraca($user_id,$data)
    {
        
        try{   
            DB::beginTransaction();
                // buscar a data de entrada e saida do funcionario
                $getAPICatraca=  $this->getAPIcontroloPresecaCatraca($user_id,$data);
            // return $getAPICatraca;
             $getPresenca=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                ->leftJoin('user_parameters as user_parament',function($q){
                    $q->on('user_parament.users_id','=','presenca_catraca.id_funcionario')
                    ->where('user_parament.parameters_id',1);
                })
                ->select([
                    'presenca_catraca.id as id',
                    'user_parament.value as nome_funcionario',
                    'presenca_catraca.id_funcionario as id_funcionario',
                    'presenca_catraca.hora_entrada as hora_entrada',
                    'presenca_catraca.hora_saida as hora_saida',
                    'presenca_catraca.data as data'
                ])
                ->when($user_id==0,function ($q) use($getAPICatraca){
                    $q->whereIn('presenca_catraca.id_funcionario',$getAPICatraca);
                })
                ->when($user_id!=0,function ($q) use($user_id){
                    $q->where('presenca_catraca.id_funcionario',$user_id);
                })
                ->orderBy('presenca_catraca.data','ASC')
                ->orderBy('presenca_catraca.hora_entrada','ASC')
                ->orderBy('user_parament.value','ASC')
                ->where('presenca_catraca.month_year',$data)
                ->whereNull('presenca_catraca.deleted_at')
                ->whereNull('presenca_catraca.deleted_by');
                // ->get();

                return Datatables::of($getPresenca)
                ->addColumn('actions', function ($item) {
                    return view('RH::salarioHonorario.controle-presencas.controle-presenca-catraca.datatables.actions',compact('item'));
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->toJson();
            DB::commit();
            
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    private function getAPIcontroloPresecaCatraca($id_funcionario,$data){
        $id_user=[];
        $month_year=$data;
        $data= explode('-',$data);
        $data=$data[1].'/'.$data[0];
        if ($id_funcionario==0) {
            $usuarios= DB::table('users as usuario')
            ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
            ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
            ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
            ->whereNotIn("cargo.id",[6,15,2])
            ->select([
                'usuario.id as id_user',
                ])
                ->distinct('usuario.id')
            ->orderBy('usuario.id','ASC')
            ->get();
            // foreach ($usuarios as $key => $value) {
                $getInformacionCatraca= getInformationCatraca(0,$data);
                $this->setInsertInformaCatraca($getInformacionCatraca,$month_year);
            // }
            foreach ($usuarios as $key => $item) {
                $id_user[]=$item->id_user;
            }
            
        }else{
            $getInformaCatraca= getInformationCatraca($id_funcionario,$data);
            $this->setInsertInformaCatraca($getInformaCatraca,$month_year);
            $id_user[]=$id_funcionario;
        }
        
        return $id_user;
    }
    
    private function setInsertInformaCatraca($getInformaCatraca,$month_year)
    {
        $getInformation_organize=[];
        $getInformaCatracc=collect($getInformaCatraca)->sortBy('descricao');
         foreach ($getInformaCatraca as $key => $item) {
           if ($item->descricao == 'Entrada') {
                $getInformation_organize[]=(object)[
                    'numInterno'=>$item->numInterno,
                    'nome'=>$item->nome,
                    'descricao'=>$item->descricao,
                    'hora'=>$item->hora,
                    'data'=>$item->data,
                ];
           }
         }
         foreach ($getInformaCatraca as $key => $item) {
            if ($item->descricao != 'Entrada') {
                 $getInformation_organize[]=(object)[
                     'numInterno'=>$item->numInterno,
                     'nome'=>$item->nome,
                     'descricao'=>$item->descricao,
                     'hora'=>$item->hora,
                     'data'=>$item->data,
                 ];
            }
          }
        //   return $getInformation_organize;

        if (count($getInformation_organize)>0) {
            foreach ($getInformation_organize as $key => $item) {
                // consultar de o funcionario já foi cadastrado na DB com uma repectiva data e hora
                // Passo para cadastrar na BD: 1- consulta de tipo se refere a entrada ou saida.

                if ($item->descricao=="Entrada") {
                    $getDB_dataCatraca=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                    ->where('presenca_catraca.id_funcionario',$item->numInterno)
                    ->where('presenca_catraca.data',$item->data)
                    ->where('presenca_catraca.hora_entrada',$item->hora)
                    // ->where('presenca_catraca.hora_saida',$item->hora_saida)
                    ->whereNull('presenca_catraca.deleted_at')
                    ->whereNull('presenca_catraca.deleted_by')
                    ->get();

                     if ($getDB_dataCatraca->isEmpty()) {
                        DB::table('rh_controle_presenca_catraca')->insert([
                            'id_funcionario' => $item->numInterno,
                            'hora_entrada' => $item->hora,
                            'data' => $item->data,
                            'month_year' => $month_year
                        ]);
                    } 

                } else {
                    $getDB_dataCatraca=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                    ->where('presenca_catraca.id_funcionario',$item->numInterno)
                    ->where('presenca_catraca.data',$item->data)
                    ->whereNull('presenca_catraca.hora_entrada')
                    // ->where('presenca_catraca.hora_saida',$item->hora)
                    ->whereNull('presenca_catraca.deleted_at')
                    ->whereNull('presenca_catraca.deleted_by')
                    ->orderBy('presenca_catraca.hora_entrada','DESC')
                    ->get();

                    if (!$getDB_dataCatraca->isEmpty()) {
                        $affected = DB::table('rh_controle_presenca_catraca')
                        ->where('id', $getDB_dataCatraca[0]->id)
                        ->update(['hora_saida' => $item->hora]);

                        
                    }else{


                        $getDB_dataCatraca=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                        ->where('presenca_catraca.id_funcionario',$item->numInterno)
                        ->where('presenca_catraca.data',$item->data)
                        ->where('presenca_catraca.hora_saida','=',$item->hora)
                        ->whereNull('presenca_catraca.deleted_at')
                        ->whereNull('presenca_catraca.deleted_by')
                        ->orderBy('presenca_catraca.hora_entrada','DESC')
                        ->get();
                            if ($getDB_dataCatraca->isEmpty()) {
                                DB::table('rh_controle_presenca_catraca')->insert([
                                    'id_funcionario' => $item->numInterno,
                                    'hora_saida' => $item->hora,
                                    'data' => $item->data,
                                    'month_year' => $month_year
                                ]);
                            }
                    }
                   
                }  
            }   
        
        } 
    }

    public function controlocCatraca_dayFuncionario($id_funcionario,$data)
    {
       
        $explode=explode('-',$data);

        $monthYear=$explode[0].'-'.$explode[1];
        $total_day=0;
        $total_month=0;
        $total_Entrada=null;
            $getPresencamonth=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                ->leftJoin('user_parameters as user_parament',function($q){
                    $q->on('user_parament.users_id','=','presenca_catraca.id_funcionario')
                    ->where('user_parament.parameters_id',1);
                })
                ->select([
                    'presenca_catraca.id as id',
                    'user_parament.value as nome_funcionario',
                    'presenca_catraca.id_funcionario as id_funcionario',
                    'presenca_catraca.hora_entrada as hora_entrada',
                    'presenca_catraca.hora_saida as hora_saida',
                    'presenca_catraca.data as data'
                ])
                ->where('presenca_catraca.id_funcionario',$id_funcionario)
                ->orderBy('presenca_catraca.hora_entrada','ASC')
                ->where('presenca_catraca.month_year',$monthYear)
                ->whereNull('presenca_catraca.deleted_at')
                ->whereNull('presenca_catraca.deleted_by')
            ->get();

                foreach ($getPresencamonth as $key => $q) {
                    $total_Entrada=intervalo_duas_horas($q->hora_entrada, $q->hora_saida);   
                   $q->{'total_Entrada'}=$total_Entrada[0].':'.$total_Entrada[1];
                   $total_month+= $total_Entrada[0];
                    if ($q->data==$data) {
                       $total_day+=$total_Entrada[0];
                    }
                }

            

        return response()->json(['data'=>$getPresencamonth,'day_data'=>$data, 'taotal_month'=>$total_month, 'total_day'=>$total_day]);
    }
    

    public function controlo_torniquePDF($id_funcionario,$data)
    {
        // return $data;
        $explode=explode('-',$data);

        $monthYear=$explode[0].'-'.$explode[1];
        $total_day=0;
        $total_month=0;
        $total_Entrada=null;
             $getPresencamonth=DB::table('rh_controle_presenca_catraca as presenca_catraca')
                ->leftJoin('user_parameters as user_parament',function($q){
                    $q->on('user_parament.users_id','=','presenca_catraca.id_funcionario')
                    ->where('user_parament.parameters_id',1);
                })
                ->select([
                    'presenca_catraca.id as id',
                    'user_parament.value as nome_funcionario',
                    'presenca_catraca.id_funcionario as id_funcionario',
                    'presenca_catraca.hora_entrada as hora_entrada',
                    'presenca_catraca.hora_saida as hora_saida',
                    'presenca_catraca.data as data'
                ])
                ->where('presenca_catraca.id_funcionario',$id_funcionario)
                ->orderBy('presenca_catraca.data','ASC')
                ->orderBy('presenca_catraca.hora_entrada','ASC')
                ->where('presenca_catraca.month_year',$monthYear)
                ->whereNull('presenca_catraca.deleted_at')
                ->whereNull('presenca_catraca.deleted_by')
            ->get();

            foreach ($getPresencamonth as $key => $q) {
                $total_Entrada=intervalo_duas_horas($q->hora_entrada, $q->hora_saida);   
                $q->{'total_Entrada'}=$total_Entrada[0];
                $q->{'total_EntradaMinuto'}=$total_Entrada[1];
                $total_month+= $total_Entrada[0];
                if ($q->data==$data) {
                    $total_day+=$total_Entrada[0];
                }
            }
            // return $getPresencamonth;
           $count=count($getPresencamonth);
            $institution = Institution::latest()->first();
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento = 1;
            $dados=[
                'institution'=>$institution,
                'getPresencamonth'=>$getPresencamonth,
                'total_day'=>$total_day,
                'total_month'=>$total_month,
                'monthYear'=>$monthYear,
                'count'=>$count
            ];
            

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView("RH::salarioHonorario.controle-presencas.controle-presenca-catraca.pdf_presencaTorniquete", $dados);              
            $pdf->setOption('margin-top', '3mm');
            $pdf->setOption('margin-left', '3mm');
            $pdf->setOption('margin-bottom', '1.5cm');
            $pdf->setOption('margin-right', '3mm');
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('debug-javascript', true);
            $pdf->setOption('javascript-delay', 1000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('footer-html', $footer_html);
            // $pdf->setPaper('a4','landscape');       
            return $pdf->stream('Forlearn | Relatorio de presença torniquete.pdf');
    }
}
