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

class ConfiguracoesRHController extends Controller
{
    public function controlePresenca()
    {
        // MOSTRA OS FUNCIONÁRIOS
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
        ->get();

        // return $getcontratos;


        // MOSTRA OS SUBSÍDIOS
        // $getSubsidio=DB::table('subsidio as subsid')
            // ->select([
            //     'subsid.id as subsidio_id',
            //     'subsid.display_name as display_name',
            //     'subsid.discricao as descricao',
            //     'subsid.status as status',
            //     'subsid.year as year',
            //     'subsid.month as month'
            // ])
            // ->whereNull('subsid.deleted_by')
            // ->whereNull('subsid.deleted_at')        
            // // ->distinct('subsid.id')
            // ->groupBy(('subsid.id'))
        // ->get();
  
        $data=[
            'action'=>'SALÁRIO E HONORÁRIO',
            'section'=>'criar',
            'users' => $users,
            // 'getSubsidio' => $getSubsidio,
            'getcontratos' => $getcontratos,
        ];

        return view('RH::salarioHonorario.controle-presencas.index')->with($data);
        // ,compact('data'));
    }

    public function ajaxcontrolePresenca($id_func)
    {   
        
        $presenca_func = DB::table('rh_controle_presenca as controle_presenca')        
            ->leftJoin('fun_with_type_contrato as fun_type_contrato',function ($q){
                $q->on('controle_presenca.id_fun_with_contrato','=','fun_type_contrato.id_cargo');
            })
            ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('controle_presenca.id_funcionario', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
            ->leftJoin('user_parameters as fullName2', function ($join) {
                $join->on('controle_presenca.created_by', '=', 'fullName2.users_id')
                ->where('fullName2.parameters_id', 1);
            })
            ->leftJoin('user_parameters as fullName1', function ($join) {
                $join->on('controle_presenca.update_by', '=', 'fullName1.users_id')
                ->where('fullName1.parameters_id', 1);
            })
            ->Join('roles as role',function ($q){
                $q->on('role.id','=','fun_type_contrato.id_cargo');
            })
            ->join('role_translations as role_trans', function ($join) {
                $join->on('role_trans.role_id', '=', 'fun_type_contrato.id_cargo')
                ->where('role_trans.language_id', 1)
                ->where('role_trans.active', 1);
            })
            // ->join('rh_controle_presenca as rh_presencas',function ($q){
            //     $q->on('rh_presencas.id_fun_with_contrato', '=', 'controle_presenca.id_fun_with_contrato');
            // })
            ->leftJoin('documento_user_recurso_humano as doc_user_rh',function ($q){
                $q->on('doc_user_rh.id_presenca','=','controle_presenca.id');
            })
        ->select([
            'controle_presenca.id as id',
            'controle_presenca.id_funcionario as funcionario_id',
            'fullName.value as fullName',
            'controle_presenca.id_fun_with_contrato as contrato_id',
            'controle_presenca.data as data', 
            'controle_presenca.entrada as entrada', 
            'controle_presenca.saida as saida',
            'fullName2.value as created_by',
            'controle_presenca.created_at as created_at',
            'fullName1.value as update_by',
            'controle_presenca.update_at as update_at',
            // 'role.name as contrato',
            'role_trans.display_name as contrato',
            'doc_user_rh.arquivo as arquivo',
            'controle_presenca.falta as falta'
        ])                    
        ->whereNull('controle_presenca.deleted_at')
            ->whereNull('controle_presenca.deleted_by')
            ->where('controle_presenca.id_funcionario', '=', $id_func)
            ->where('fullName.users_id', '=', $id_func)
        // ->groupBy('controle_presenca.id')
        // ->distinct('controle_presenca.id')
        ->distinct()
        ->get();

        return DataTables::of($presenca_func)
        ->addColumn('actions', function ($item) {
            return view('RH::salarioHonorario.controle-presencas.datatables.actions',compact('item'));
        })
        ->addColumn('time', function ($item) {
            return $item->{'totalHoras'} = intervalo_duas_horas($item->entrada, $item->saida);
        })
        ->rawColumns(['actions', 'time'])
        ->addIndexColumn()
        ->toJson();
        // ->make(true);

    }


    
    
    

    public function store_controlePresenca (Request $request) {
        try {

            // return $request;

            DB::table('rh_controle_presenca')->insert([
                'id_funcionario' => $request->funcionario,
                'id_fun_with_contrato' => $request->funcionario_contrato, 
                'data' => $request->data,
                'entrada' => $request->entrada, 
                'saida' => $request->saida,
                'created_by' => Auth::user()->id,
                'created_at' =>Carbon::Now()
            ]);
            
            Toastr::success(__('Ausência criada com sucesso'), __('toastr.success'));
            return redirect()->back();

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }


    public function delete_controlePresenca (Request $request) {
        try {

            // return $request;
             $select=DB::table('rh_controle_presenca')
                ->where('id',$request->getId)
                ->first();
                $expldeMonth=explode('-',$select->data);
                $year= $expldeMonth[0];
                $month=$expldeMonth[1];
                
                $getProcessamentoMonth=DB::table('processamento_salarial as processamento_sl')
                ->join('fun_with_type_contrato as fun_type_contra','fun_type_contra.id','=','processamento_sl.id_fun_type_contrato')
                ->join('funcionario_with_contrato as fun_with_contra','fun_with_contra.id','=','fun_type_contra.id_fun_with_contrato')
                ->where('processamento_sl.year','=',$year)
                ->where('processamento_sl.month','=',$month)
                ->where('fun_with_contra.id_user','=',$select->id_funcionario)
                ->where('fun_type_contra.id_cargo','=',$select->id_fun_with_contrato)
                ->whereNull('processamento_sl.deleted_by')
                ->whereNull('processamento_sl.deleted_by')
            ->get();
            if ($getProcessamentoMonth->isEmpty()) {

                DB::table('rh_controle_presenca as rh_presencas')
                    ->where('rh_presencas.id', '=', $request->getId)
                ->update([
                    'rh_presencas.deleted_by' => Auth::user()->id,
                    'rh_presencas.deleted_at' =>Carbon::Now()
                ]);
                
                Toastr::success(__('Falta  eliminada com sucesso'), __('toastr.success'));
                return redirect()->back();
            }else{
                Toastr::error(__('Falta não pode ser eliminada devido a sua utilização no processamento de salário!'), __('toastr.error'));
                return redirect()->back();
            }
            

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }


    public function edit_controlePresenca(Request $request)
    {
        try{ 
            
            // return $request;

            $select=DB::table('rh_controle_presenca')
            ->where('id',$request->id_presence)
            ->first();
            $expldeMonth=explode('-',$select->data);
            $year= $expldeMonth[0];
            $month=$expldeMonth[1];
            
            $getProcessamentoMonth=DB::table('processamento_salarial as processamento_sl')
            ->join('fun_with_type_contrato as fun_type_contra','fun_type_contra.id','=','processamento_sl.id_fun_type_contrato')
            ->join('funcionario_with_contrato as fun_with_contra','fun_with_contra.id','=','fun_type_contra.id_fun_with_contrato')
            ->where('processamento_sl.year','=',$year)
            ->where('processamento_sl.month','=',$month)
            ->where('fun_with_contra.id_user','=',$select->id_funcionario)
            ->where('fun_type_contra.id_cargo','=',$select->id_fun_with_contrato)
            ->whereNull('processamento_sl.deleted_by')
            ->whereNull('processamento_sl.deleted_by')
            ->get();
            
           
            if ($getProcessamentoMonth->isEmpty()) {

                $rh_presencas = DB::table('rh_controle_presenca as rh_presencas')
                    ->where('rh_presencas.id','=',$request->id_presence)
                ->update([
                    'rh_presencas.falta' => $request->falta,
                    'rh_presencas.data' => $request->data,
                    'rh_presencas.entrada' => $request->entrada,
                    'rh_presencas.saida' => $request->saida,                             
                    'rh_presencas.update_by' => Auth::user()->id,
                    'rh_presencas.update_at' =>Carbon::Now()
                ]);

                $upload=false;

                if ($request->arquivo!=null) {
                    $user = User::whereId($request->funcionario_id)->firstOrFail();
                    $file=$request->file('arquivo');
                    $filename = $file->getClientOriginalName();
                    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    if($fileExtension!="pdf" && $fileExtension!="jpg" && $fileExtension!="png")
                    {
                        // Toastr::error("Erro com o arquivo, anexa arquivo com as seguintes fromatações (PDF,PNG,JPG).", __('toastr.error'));
                        // return redirect()->back();
                    }else{
                        $hear=time(); 
                        $filename =$user->name.'_jusificacao_'.$hear.'.'.$fileExtension;

                        //  gravar arquivo no servidor
                        $request->file('arquivo')->storeAs('documento_userRH', $filename);
                        $upload=true;
                    }
                }


                if ($upload==true) {
                    DB::table('documento_user_recurso_humano')->insert(
                        [
                        'id_presenca' => $request->id_presence, 
                        'arquivo' => $filename,
                        'created_at' => Carbon::Now(),
                        'created_by' => Auth::user()->id,
                        'update_at' => Carbon::Now(),
                        'update_by' =>  Auth::user()->id
                        ]);
                }

                Toastr::success(__('Ausência editada com sucesso.'), __('toastr.success'));
                return redirect()->back();
            }else{
                Toastr::error(__('Falta não pode ser eliminada devido a sua utilização no processamento de salário!'), __('toastr.error'));
                return redirect()->back();
            }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }




    public function index()
    {
        try {
      
            $data=[
                'action'=>'CONFIGURAÇÕES RH'
            ];
                return view('RH::configuracoes.index',compact('data'));
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function impostos()
    {
        try {
      
            $data=[
                'action'=>'CONFIGURAÇÕES RH',
                'id_imposto'=>0,
                'section'=>"created_imposto",
                'getyearImposto'=>0
            ];
                return view('RH::configuracoes.imposto.index')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }
    
    public function createImpostoRH(Request $request)
    {
            
            try{
                $getImposto=DB::table('imposto')
                    ->where('imposto.display_name','=',$request->nameImposto)
                    // ->where('imposto.yearMonth','=',$request->yearImposto)
                    ->whereIn('imposto.status',['panding','active'])
                    ->whereNull('imposto.deleted_by')
                    ->whereNull('imposto.deleted_at')
                ->get();
                $count=count($getImposto);
                if ($count==0) {
                        DB::table('imposto')->insert([
                            'display_name' =>$request->nameImposto,
                            // 'yearMonth' =>$request->yearImposto,
                            'discricao' =>$request->descricaoImposto,
                            'status' =>"panding",
                            'update_at' =>Carbon::Now(),
                            'update_by' =>Auth::user()->id,
                            'created_by' =>Auth::user()->id,
                            'created_at' =>Carbon::Now()
                        ]);
                        Toastr::success(__('Imposto criado com sucesso'), __('toastr.success'));
                        return redirect()->back();
                }else{
                        Toastr::error(__('Imposto já exite no sistema ou encontra-se activo'), __('toastr.error'));
                        return redirect()->back();
                    }
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }  
        
    }
    
    
    public function ajaxImposto()
    {       
            try{
                $getyearImposto=DB::table('imposto_year as impost_year')
                ->whereNull('impost_year.deleted_at')
                ->whereNull('impost_year.deleted_at')
                ->get();
                $getImposto=DB::table('imposto as impost')
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('impost.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->select([
                    'impost.display_name as display_name',
                    'impost.discricao as descricao',
                    'impost.status as status',
                    'impost.id as id_imposto',
                    'fullName.value as created_by',
                    'impost.created_at as created_at'
                ])
                ->whereNull('impost.deleted_by')
                ->whereNull('impost.deleted_at');
                // ->get();

                return DataTables::of($getImposto)
                ->addColumn('actions', function ($item) {
                    return view('RH::configuracoes.imposto.datatables.actions',compact('item'));
                })
                ->addColumn('years', function ($item) use($getyearImposto) {
                    return view('RH::configuracoes.imposto.datatables.year_imposto',compact('item','getyearImposto'));
                })
                ->rawColumns(['actions','years'])
                ->addIndexColumn()
                // ->toJson();
                ->make(true);
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }  

    }

    public function createdYear_imposto($id_imposto)
    {
        try {
            $nameImposto=null;
            $getyearImposto=DB::table('imposto  as impost')
            ->leftJoin('imposto_year as impost_year', function ($join) {
                $join->on('impost.id', '=', 'impost_year.id_imposto');
            })
            ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('impost.created_by', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
            ->select([
                'impost_year.id as id_impostoYear',
                'impost_year.year as year',
                'impost_year.month as month',
                'impost_year.status as status',
                'fullName.value as name_created_at',
                'impost.discricao as discricao',
                'impost.display_name as display_name',
                'impost.id as id'
            ])
            ->where('impost.id','=',$id_imposto)
            ->whereNull('impost.deleted_at')
            ->whereNull('impost.deleted_at')
            ->get();
            $getyearImposto=collect($getyearImposto)->map(function($item)use($nameImposto){
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$item->year." (".$element['display_name'].")";
                    }
                });
            $item->{'name_imposto'}=$item->discricao."($item->display_name)";
            $nameImposto=$item->name_imposto;
            return $item;
            });

             $data=[
                    'action'=>'CONFIGURAÇÕES RH',
                    'section'=>"created_yearImposto",
                    'nameImposto'=>$nameImposto,
                    'id_imposto'=>$id_imposto,
                    'getyearImposto'=>$getyearImposto
                ];
                    return view('RH::configuracoes.imposto.index')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    public function ajaxYearImposto($id_imposto)
    {
        try{
            $getTaxaYearImposto=DB::table('taxa_imposto as taxa')
                ->leftJoin('imposto_year as impost_year', function ($join) {
                    $join->on('impost_year.id', '=', 'taxa.id_impostoYear');
                })
                ->leftJoin('imposto as impost', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->select([
                    'impost_year.id as id_impostoYear',
                    'impost.id as id_imposto',
                    'taxa.id_impostoYear as id_impostoYear'
                ])
                ->whereNull('taxa.deleted_by')
                ->whereNull('impost.deleted_by')
                ->whereNull('impost_year.deleted_at')
                ->where('impost_year.id_imposto','=',$id_imposto)
            ->get();

            $getImpostoYear=DB::table('imposto as impost')
                ->leftJoin('imposto_year as impost_year', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('impost_year.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->select([
                    'impost.display_name as display_name',
                    'impost.discricao as descricao',
                    'impost.id as id_imposto',
                    'impost_year.year as year',
                    'impost_year.month as month',
                    'impost_year.estado as estado',
                    'impost_year.status as status',
                    'impost_year.id as id_impostoYear',
                    'fullName.value as created_by',
                    'impost_year.created_at as created_at'
                ])
                ->orderBy('impost_year.year','DESC')
                ->orderBy('impost_year.month','DESC')
                
                ->whereNull('impost.deleted_by')
                ->whereNull('impost.deleted_at')
                ->whereNull('impost_year.deleted_by')
            ->where('impost_year.id_imposto','=',$id_imposto);
            // ->get();

         return DataTables::of($getImpostoYear)
            ->addColumn('year_months', function ($item) {
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$item->year." (".$element['display_name'].")";
                    }
                });
                $display_month=$item->year_month;
                return $display_month;
            })
            ->addColumn('actions', function ($item) use($getTaxaYearImposto) {
                return view('RH::configuracoes.imposto.datatables.actions_year',compact('item','getTaxaYearImposto'));
            })
            ->rawColumns(['actions'])
            ->addIndexColumn()
            ->toJson();
            // ->make(true);
        } catch (Exception | Throwable $e) {
            return response()->json($e);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }    
    }
    
    public function createYearImposto(Request $request)
    {
            try{
                // return $request;
                    $explo=explode('-',$request->yearImposto);
                    
                    $getImposto=DB::table('imposto_year as impost_year')
                    ->where('impost_year.id_imposto','=',$request->id_imposto)
                    ->where('impost_year.year','=',$explo[0])
                    ->where('impost_year.month','=',$explo[1])
                    ->whereNull('impost_year.deleted_by')
                    ->whereNull('impost_year.deleted_at')
                    ->get();

                $count=count($getImposto);
                if ($count==0) {
                     $getUpdateEstadoImp=DB::table('imposto_year as impost_year')
                    ->where('impost_year.id_imposto','=',$request->id_imposto)
                    ->where('impost_year.estado','=',1)
                    ->whereNull('impost_year.deleted_by')
                    ->whereNull('impost_year.deleted_at')
                    ->get();
                     $count=count($getUpdateEstadoImp);
                    if ($count>0) {
                        foreach ($getUpdateEstadoImp as $key => $value) {
                            $updateEstado = DB::table('imposto_year as impost_year')
                            ->where('impost_year.id','=',$value->id)
                            ->update([
                                'impost_year.estado' => 0
                                ]);
                        } 
                    }

                    DB::table('imposto_year')->insert([
                        'id_imposto' =>$request->id_imposto,
                        'year' =>$explo[0],
                        'month' =>$explo[1],
                        'created_at' =>Carbon::Now(),
                        'created_by' =>Auth::user()->id,
                        'estado' =>1,
                        'status' =>'panding'
                    ]);
                    Toastr::success(__('Data de imposto criado com sucesso'), __('toastr.success'));
                    return redirect()->back();
                }else{
                        Toastr::error(__('Este imposto já está associado a esta data'), __('toastr.error'));
                        return redirect()->back();
                    }
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }   
    }
    
    public function impostoYearCopy(Request $request)
    {
            try{
                // return $request; 
                $getImpostoYear=DB::table('taxa_imposto as taxa')
                    ->where('taxa.id_impostoYear','=',$request->idImpostoCopy)
                    ->whereNull('taxa.deleted_by')
                    ->get();
                    
                $getImposto=DB::table('taxa_imposto as taxa')
                ->where('taxa.id_impostoYear','=',$request->selectYearImposto)
                ->whereNull('taxa.deleted_by')
                ->get();
                $count=count($getImposto);
                if ($count==0) {
                    
                    foreach ($getImpostoYear as $key => $item) {
                        $taxa_imposto = DB::table('taxa_imposto')->insert([
                            'id_impostoYear' => $request->selectYearImposto,
                            'taxa' => $item->taxa!=null ? $item->taxa:0,
                            'parcela_fixa' => $item->parcela_fixa!=null ? $item->parcela_fixa:0,
                            'valor_inicial' => $item->valor_inicial!=null ? $item->valor_inicial:0,
                            'valor_final' => $item->valor_final!=null ? $item->valor_final:0,
                            'excesso' => $item->excesso!=null ? $item->excesso:0,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::Now()
                        ]);
                    }
                
                    Toastr::success(__('Copia do dados creado com sucesso.'), __('toastr.success'));
                    return redirect()->back();
                }
                else {
                    $updateYearImposto = DB::table('taxa_imposto as taxa')
                    ->where('taxa.id_impostoYear','=',$request->selectYearImposto)
                    ->update(['taxa.deleted_by' =>Auth::user()->id]);



                    if ($updateYearImposto != null) {
                        foreach ($getImpostoYear as $key => $item) {
                            $taxa_imposto = DB::table('taxa_imposto')->insert([
                                'id_impostoYear' => $request->selectYearImposto,
                                'taxa' => $item->taxa,
                                'parcela_fixa' => $item->parcela_fixa,
                                'valor_inicial' => $item->valor_inicial,
                                'valor_final' => $item->valor_final,
                                'excesso' => $item->excesso,
                                'created_by' => Auth::user()->id,
                                'created_at' => Carbon::Now()
                            ]);
                        }
                    }

                    Toastr::success(__('Copia do dados creado com sucesso.'), __('toastr.success'));
                    return redirect()->back();
                }
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            }   
        
    }
    
    public function taxa_impostos($id_impostoYear)
    {
        
        
        try {
            $id_impostoYear=explode(',',$id_impostoYear);
             $getTaxaYearImposto=DB::table('taxa_imposto as taxa')
                ->leftJoin('imposto_year as impost_year', function ($join) {
                    $join->on('impost_year.id', '=', 'taxa.id_impostoYear');
                })
                ->leftJoin('imposto as impost', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->select([
                    'impost_year.id as id_impostoYear',
                    'impost_year.status as status',
                    'impost.id as id_imposto',
                    'taxa.id_impostoYear as id_impostoYear',
                    'taxa.taxa as taxa',
                    'taxa.id as id_taxa',
                    'taxa.parcela_fixa as parcela_fixa',
                    'taxa.valor_inicial as valor_inicial',
                    'taxa.valor_final as valor_final',
                    'taxa.excesso as excesso'
                ])
                ->whereNull('taxa.deleted_by')
                ->whereNull('impost.deleted_by')
                ->whereNull('impost_year.deleted_at')
                ->where('impost_year.id','=',$id_impostoYear[0])
                ->where('impost.id','=',$id_impostoYear[1])
                ->orderBy('taxa.valor_final','ASC')
            ->get();



            $getyearImposto=DB::table('imposto_year as impost_year')
                ->leftJoin('imposto as impost', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('impost.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->select([
                    'impost_year.id as id_impostoYear',
                    'impost_year.year as year',
                    'impost_year.month as month',
                    'impost_year.status as status',
                    'fullName.value as name_created_at',
                    'impost.discricao as discricao',
                    'impost.display_name as display_name',
                    'impost.id as id'
                ])
                ->whereNull('impost_year.deleted_at')
                ->where('impost.id','=',$id_impostoYear[1])
                ->whereNull('impost_year.deleted_at')
            ->get();

             $getyear=DB::table('imposto_year as impost_year')
                ->leftJoin('imposto as impost', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('impost.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->select([
                    'impost_year.id as id_impostoYear',
                    'impost_year.year as year',
                    'impost_year.month as month',
                    'impost_year.status as status',
                    'fullName.value as name_created_at',
                    'impost.discricao as discricao',
                    'impost.display_name as display_name',
                    'impost.id as id'
                ])
                ->where('impost_year.id','=',$id_impostoYear[0])
                ->where('impost.id','=',$id_impostoYear[1])
                ->whereNull('impost_year.deleted_at')
                ->whereNull('impost_year.deleted_at')
            ->get();

            $getyearImposto=collect($getyearImposto)->map(function($item){
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$item->year." (".$element['display_name'].")";
                    }
                });
            $item->{'name_imposto'}=$item->discricao."($item->display_name)";
            return $item;
            });
            $getyear=collect($getyear)->map(function($item){
                getLocalizedMonths()->map(function ($element)use($item){
                    if ($element['id']==$item->month) {
                        $item->{'year_month'}=$item->year." (".$element['display_name'].")";
                    }
                });
            $item->{'name_imposto'}=$item->discricao."($item->display_name)";
            return $item;
            });

             $data=[
                'action'=>'CONFIGURAÇÕES RH',
                'getyearImposto'=>$getyearImposto,
                'getTaxaYearImposto'=>$getTaxaYearImposto,
                'id_impostoYear'=>$id_impostoYear[0],
                'getyear'=>$getyear

            ];
                return view('RH::configuracoes.imposto.taxa_imposto')->with($data);
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }
    
    public function createTaxaImposto(Request $request)
    {   
        try{
            // return $request;
            $taxa_imposto = DB::table('taxa_imposto')->insert([
                'id_impostoYear' => $request->id_impostoYear,
                'taxa' => $request->taxa!=null ? $request->taxa :0,
                'parcela_fixa' => $request->parcela!=null ? $request->parcela : 0,
                'valor_inicial' => $request->valorIncial!=null ? $request->valorIncial : 0,
                'valor_final' => $request->valorFinal!=null ? $request->valorFinal : 0,
                'excesso' => $request->exacesso!=null ? $request->exacesso : 0,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::Now()
            ]);
            
            if ($taxa_imposto === True) {
                Toastr::success(__('A taxa do imposto foi criado com sucesso'), __('toastr.success'));
                return redirect()->back();
            }
            else {
                Toastr::success(__('Não foi possivel criar taxa do imposto.'), __('toastr.error'));
                return redirect()->back();
            }
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function ajaxTaxa_impostos($id_impostoYear)
    {
         try{
            $getTaxaYearImposto=DB::table('taxa_imposto as taxa')
                ->leftJoin('imposto_year as impost_year', function ($join) {
                    $join->on('impost_year.id', '=', 'taxa.id_impostoYear');
                })
                ->leftJoin('imposto as impost', function ($join) {
                    $join->on('impost.id', '=', 'impost_year.id_imposto');
                })
                ->select([
                    'impost_year.id as id_impostoYear',
                    'impost_year.status as status',
                    'impost.id as id_imposto',
                    'taxa.id_impostoYear as id_impostoYear',
                    'taxa.taxa as taxa',
                    'taxa.id as id_taxa',
                    'taxa.parcela_fixa as parcela_fixa',
                    'taxa.valor_inicial as valor_inicial',
                    'taxa.valor_final as valor_final',
                    'taxa.excesso as excesso'
                ])
                ->whereNull('taxa.deleted_by')
                ->whereNull('impost.deleted_by')
                ->whereNull('impost_year.deleted_at')
                ->where('impost_year.id','=',$id_impostoYear)
                ->orderBy('taxa.valor_final','ASC')
                ->get();
        return response()->json(['data'=>$getTaxaYearImposto]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function deleteTaxaImposto(Request $request)
    {
        try{ 
            // return $request;
            $updateYearImposto = DB::table('taxa_imposto as taxa')
                ->where('taxa.id','=',$request->idTaxa)
                ->update(['taxa.deleted_by' =>Auth::user()->id]);
            
            Toastr::success(__('Taxa eliminada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function editarTaxa_imposto(Request $request)
    {
        try{ 
        // return $request;
            $updateYearImposto = DB::table('taxa_imposto as taxa')
            ->where('taxa.id','=',$request->id_taxa)
            ->update([
                'taxa' => $request->taxa!=null ? $request->taxa :0,
                'parcela_fixa' => $request->parcela!=null ? $request->parcela : 0,
                'valor_inicial' => $request->valorIncial!=null ? $request->valorIncial : 0,
                'valor_final' => $request->valorFinal!=null ? $request->valorFinal : 0,
                'excesso' => $request->exacesso!=null ? $request->exacesso : 0,
                'created_by' => Auth::user()->id
            ]);
            Toastr::success(__('Taxa editada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function deleteImposto(Request $request)
    {
        try{
            $upImposto = DB::table('imposto as impost')
            ->where('impost.id','=',$request->getId)
            ->update([
                'impost.deleted_by' =>Auth::user()->id,
                'impost.deleted_at' =>Carbon::Now()
            ]);
        
            Toastr::success(__('Imposto eliminada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function deleteImpostoYear(Request $request)
    {
        try{
            $imposto_year = DB::table('imposto_year as impost_year')
            ->where('impost_year.id','=',$request->getId)
            ->update([
                'impost_year.deleted_by' =>Auth::user()->id,
                'impost_year.deleted_at' =>Carbon::Now()
            ]);
        
            Toastr::success(__('Imposto associado ao ano eliminada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function editarImposto(Request $request)
    {
        try{
            try{ 
                    // return $request;
                    $imposto = DB::table('imposto as impost')
                    ->where('impost.id','=',$request->idImposto)
                    ->update([
                        'display_name'=>$request->nameImposto,
                        'discricao'=>$request->descricaoImposto,
                        'update_at'=>Carbon::Now(),
                        'update_by'=>Auth::user()->id
                    ]);
                    Toastr::success(__('Imposto editado com sucesso.'), __('toastr.success'));
                    return redirect()->back();
            } catch (Exception | Throwable $e) {
                Log::error($e);
                return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
            } 

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }
    
    public function editarImpostoYear(Request $request)
    {
        try{ 
                // return $request;
                $explo=explode('-',$request->yearImposto);
                $imposto = DB::table('imposto_year as impost_year')
                ->where('impost_year.id','=',$request->idyearImposto)
                ->update([
                    'year'=>$explo[0],
                    'month'=>$explo[1]
                ]);
                Toastr::success(__('Data que entrou em invigor o imposto editado com sucesso.'), __('toastr.success'));
                return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }   
    }




    public function subsidios()
    {
        try {
            $getImposto=DB::table('imposto as impost')
                ->leftJoin('user_parameters as fullName', function ($join) {
                    $join->on('impost.created_by', '=', 'fullName.users_id')
                    ->where('fullName.parameters_id', 1);
                })
                ->select([
                    'impost.display_name as display_name',
                    'impost.discricao as descricao',
                    'impost.status as status',
                    'impost.id as id_imposto',
                    'fullName.value as created_by',
                    'impost.created_at as created_at'
                ])
                ->whereNull('impost.deleted_by')
                ->whereNull('impost.deleted_at')
                ->get();
      
            $data=[
                'action'=>'CONFIGURAÇÕES RH',
                'id_imposto'=>0,
                'section'=>"created_imposto",
                'getyearImposto'=>0,
                'getImposto'=>$getImposto
            ];
                return view('RH::configuracoes.subsidio.index')->with($data);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }


    public function createSubsidioRH(Request $request)
    {        

        $getSubsidio=DB::table('subsidio')
        ->where('subsidio.display_name','=',$request->nameSubsidio)
        ->whereIn('subsidio.status',['panding','active'])
        ->whereNull('subsidio.deleted_by')
        ->whereNull('subsidio.deleted_at')
       ->get();

       $count=count($getSubsidio);
       if ($count==0) {
            $explo=explode('-',$request->yearSubsidio);

            $subsidiosID = DB::table('subsidio')->insertGetId([
                'display_name' =>$request->nameSubsidio,
                'discricao' =>$request->descricaoSubsidio,
                'status' =>"panding",
                'update_at' =>Carbon::Now(),
                'update_by' =>Auth::user()->id,
                'created_by' =>Auth::user()->id,
                'created_at' =>Carbon::Now()
            ]);
            
            // foreach ($request->listmonth as $countElement){
            //     DB::table('subsidio_imposto')->insert([
            //         'subsidio_id' =>$subsidiosID,
            //         'imposto_id' =>$countElement
            //     ]);
            // }

            Toastr::success(__('Subsídio criado com sucesso'), __('toastr.success'));
            return redirect()->back();
       }else{
            Toastr::error(__('Subsídio já exite no sistema ou encontra-se activo'), __('toastr.error'));
            return redirect()->back();
        }
    }


    public function ajaxSubsidio()
    {   
        
        $subsidiosImposto = DB::table('subsidio_imposto as sub_imp')
        ->leftJoin('imposto as impost', function ($join) {
            $join->on('sub_imp.imposto_id', '=', 'impost.id');
        })
        ->leftJoin('user_parameters as fullName', function ($join) {
            $join->on('impost.created_by', '=', 'fullName.users_id')
            ->where('fullName.parameters_id', 1);
        })        
        ->select([
            'sub_imp.imposto_id as id_imposto',
            'sub_imp.subsidio_id as id_subsidio',
            'impost.display_name as display_name'
        ])        
        ->whereNull('sub_imp.deleted_by')
        ->whereNull('sub_imp.deleted_at')
        ->get();

        
        


        $getSubsidio=DB::table('subsidio as subsid')
            ->leftJoin('subsidio_imposto as sub_imp', function ($join) {
                $join->on('sub_imp.subsidio_id', '=', 'subsid.id');
            })
            ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('subsid.created_by', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
            ->select([
                'subsid.id as subsidio_id',
                'subsid.display_name as display_name',
                'subsid.discricao as descricao',
                'subsid.status as status',
                // 'subsid.imposto as id_imposto',
                'fullName.value as created_by',
                'subsid.created_at as created_at',
                'sub_imp.imposto_id as id_imposto'
            ])
            ->whereNull('subsid.deleted_by')
            ->whereNull('subsid.deleted_at')        
            ->distinct('subsid.id')
            ->groupBy(('subsid.id'))
        ->get();

        return DataTables::of($getSubsidio)
        ->addColumn('actions', function ($item) {
            return view('RH::configuracoes.subsidio.datatables.actions',compact('item'));
        })
        ->addColumn('imposto', function ($item) use($subsidiosImposto) {
            return view('RH::configuracoes.subsidio.datatables.nome_imposto',compact('item','subsidiosImposto'));
        })
        ->rawColumns(['actions', 'imposto'])
        ->addIndexColumn()
        // ->toJson();
        ->make(true);

    }

    public function deletedSubsidio_withImposto($id_subsidio)
    {
        try{ 
            
            $getIds=explode(',',$id_subsidio);
            
            $getFunProcessoSalario=DB::table('funcionario_contrato_subsidio as fun_cont_subsidio')
                ->join('historic_processamento_salario_subsidio as histo_pro_salario_subsidio',function ($join)
                {
                    $join->on('histo_pro_salario_subsidio.id_subsidio','=','fun_cont_subsidio.id');
                })
                ->join('historic_processamento_salario_imposto as hist_pro_salario_imposto',function ($join)
                {
                    $join->on('hist_pro_salario_imposto.id_processamento','=','histo_pro_salario_subsidio.id_processamento');
                })
                ->whereNull('fun_cont_subsidio.deleted_at')
                ->whereNull('fun_cont_subsidio.deleted_by')
                ->where('fun_cont_subsidio.id_subsidio','=',$getIds[0])
                ->where('hist_pro_salario_imposto.id_impostoYear','=',$getIds[1])
            ->get();
            $count=count($getFunProcessoSalario);
            
            if ($count==0) {
                $delete_inmposto_sub = DB::table('subsidio_imposto as subsidio_impost')
                    ->where('subsidio_impost.subsidio_id','=',$getIds[0])
                    ->where('subsidio_impost.imposto_id','=',$getIds[1])
                    ->update([                  
                        'subsidio_impost.deleted_at' =>Carbon::Now(),
                        'subsidio_impost.deleted_by' =>Auth::user()->id
                    ]);
                $confirme=1;
            } else {
                $confirme=0;
            }
            
            return response()->json(['data'=>$getIds]);
        } catch (Exception | Throwable $e) {
            return response()->json(['data'=>$e]);

            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }

    public function editarSubsidio(Request $request) {
        try{ 
            // return $request;
           $valido=false;
            if (isset($request->subsidio_imposto)) {
                foreach ($request->subsidio_imposto as $key => $item) {
                    $dados_subsidios = DB::table('subsidio_imposto')
                    ->where('subsidio_id', "=", $request->idSubsidio)                
                    ->where('imposto_id', "=", $item)                
                    ->whereNull('deleted_at')
                    ->whereNull('deleted_by')
                    ->get();
                    $count=count($dados_subsidios);
                    if ($count==0) {
                    $valido=true;
                    DB::table('subsidio_imposto')->insert(['subsidio_id' => $request->idSubsidio, 'imposto_id' => $item ]);
                    } 
                }
            }
            
             $updateSub = DB::table('subsidio as subsid')
                ->where('subsid.id','=',$request->idSubsidio)
                ->update([                  
                    'subsid.display_name' =>$request->nameSubsidio,
                    'subsid.discricao' =>$request->descricaoSubsidio,
                    'subsid.update_at' =>Carbon::Now(),
                    'subsid.update_by' =>Auth::user()->id,
                ]);
            
            if($valido==true) {
                Toastr::success(__('Imposto/os ao  associado subsídio com sucesso.'), __('toastr.success'));
                return redirect()->back();
            }else {
                Toastr::success(__('Subsídio editado com sucesso.'), __('toastr.success'));
                return redirect()->back();
            }
        } catch (Exception | Throwable $e) {
            // return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }



    public function deleteSubsidio(Request $request) {
        // return $request->getId;
        try{ 
            $updateSubsidio = DB::table('subsidio as subsid')
                ->where('subsid.id','=',$request->getId)
                ->update(['subsid.deleted_by' =>Auth::user()->id,
                    'subsid.deleted_at' =>Carbon::Now()
                ]);
            
            Toastr::success(__('Subsidio eliminada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }



    public function createFuncao()
    {
        
        
        return view('RH::configuracoes.funcao.create');
    }

    public function indexFuncao()
    {               
        return view('RH::configuracoes.funcao.index');
    }

    public function ajaxFuncao()
    {   
        $getFuncao=DB::table('recurso_humano_at_funcao as rh_funcao')
            ->leftJoin('user_parameters as fullName', function ($join) {
                $join->on('rh_funcao.created_by', '=', 'fullName.users_id')
                ->where('fullName.parameters_id', 1);
            })
            ->select([
                'rh_funcao.id as id',
                'rh_funcao.display_name as display_name',
                'rh_funcao.descricao as descricao',
                'rh_funcao.created_at as created_at',
                'fullName.value as created_by',
            ])
            ->whereNull('rh_funcao.deleted_at')
            ->whereNull('rh_funcao.deleted_by')
            ->get();

        // $SubsidiosImposto = DB::table('subsidio_imposto as sub_imp')
            // ->leftJoin('imposto as impost', function ($join) {
            //     $join->on('sub_imp.imposto_id', '=', 'impost.id');
            // })
            // ->leftJoin('user_parameters as fullName', function ($join) {
            //     $join->on('impost.created_by', '=', 'fullName.users_id')
            //     ->where('fullName.parameters_id', 1);
            // })        
            // ->select([
            //     'sub_imp.imposto_id as id_imposto',
            //     'sub_imp.subsidio_id as id_subsidio',
            //     'impost.display_name as display_name'
            // ])
            // ->get();

            
            // $getImposto=DB::table('imposto as impost')
            // ->leftJoin('user_parameters as fullName', function ($join) {
            //     $join->on('impost.created_by', '=', 'fullName.users_id')
            //     ->where('fullName.parameters_id', 1);
            // })
            // ->select([
            //     'impost.display_name as display_name',
            //     'impost.discricao as descricao',
            //     'impost.status as status',
            //     'impost.id as id_imposto',
            //     'fullName.value as created_by',
            //     'impost.created_at as created_at'
            // ])
            // ->whereNull('impost.deleted_by')
            // ->whereNull('impost.deleted_at')
            // ->get();


            // $getSubsidio=DB::table('subsidio as subsid')
            // ->leftJoin('user_parameters as fullName', function ($join) {
            //     $join->on('subsid.created_by', '=', 'fullName.users_id')
            //     ->where('fullName.parameters_id', 1);
            // })
            // ->select([
            //     'subsid.id as subsidio_id',
            //     'subsid.display_name as display_name',
            //     'subsid.discricao as descricao',
            //     'subsid.status as status',
            //     'subsid.year as year',
            //     'subsid.month as month',
            //     // 'subsid.imposto as id_imposto',
            //     'fullName.value as created_by',
            //     'subsid.created_at as created_at'
            // ])
            // ->whereNull('subsid.deleted_by')
            // ->whereNull('subsid.deleted_at')
            // ->get();

        // return $getSubsidio;


        return DataTables::of($getFuncao)
        ->addColumn('actions', function ($item) {
            return view('RH::configuracoes.funcao.datatables.actions',compact('item'));
        })
        ->rawColumns(['actions'])
        ->addIndexColumn()
        // ->toJson();
        ->make(true);

    }

    public function editarFuncao(Request $request) {

        try{ 
            // return $request;
            $updateFuncao = DB::table('recurso_humano_at_funcao as rh_funcao')
                ->where('rh_funcao.id','=',$request->idSubsidio)
                // ->get();
                ->update([
                    'display_name' =>$request->display_name,
                    'descricao' =>$request->descricao,
                    'update_at' =>Carbon::Now(),
                    'update_by' =>Auth::user()->id,
                ]);  

            // return $updateFuncao;                   
            
            Toastr::success(__('Função editada com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }

    public function deleteFuncao(Request $request) {

        try{ 
            // return $request;
            $updateFuncao = DB::table('recurso_humano_at_funcao as rh_funcao')
                ->where('rh_funcao.id','=',$request->getId)
                ->update([                  
                    'deleted_at' =>Carbon::Now(),
                    'deleted_by' =>Auth::user()->id,
                ]); 
                
            // return $updateFuncao;
            
            Toastr::success(__('Função elimida com sucesso.'), __('toastr.success'));
            return redirect()->back();
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
        //
    }




    // Zacarias
    public function addSubsidioFuncionario()
    {
        // return "Angola";
        try {

            // MOSTRA OS FUNCIONÁRIOS
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
            ->get();


            // MOSTRA OS SUBSÍDIOS
            $getSubsidio=DB::table('subsidio as subsid')
                ->select([
                    'subsid.id as subsidio_id',
                    'subsid.display_name as display_name',
                    'subsid.discricao as descricao',
                    'subsid.status as status',
                    'subsid.year as year',
                    'subsid.month as month'
                ])
                ->whereNull('subsid.deleted_by')
                ->whereNull('subsid.deleted_at')        
                // ->distinct('subsid.id')
                ->groupBy(('subsid.id'))
            ->get();
      
            $data=[
                'action'=>'SALÁRIO E HONORÁRIO',
                'section'=>'criar',
                'users' => $users,
                'getSubsidio' => $getSubsidio,
                'getcontratos' => $getcontratos,
            ];
            
            // return view('RH::configuracoes.imposto.index')->with($data);

            return view('RH::salarioHonorario.salario-subsidios.add-subsidios.add_subsidio_contrato')->with($data);

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }


    public function createContratoSubsidioFuncionario(Request $request) {
        
        // return $request;
        $inserir=false;
        try{
            foreach ($request->subsidio as $key => $value) {
               $getSubsidioFun=DB::table('funcionario_contrato_subsidio as func_cont_subs')
                ->where('func_cont_subs.id_funcionario','=',$request->funcionario)
                ->where('func_cont_subs.id_funcionario_cargo','=', $request->funcionario_contrato)
                ->where('func_cont_subs.id_subsidio','=',$value)
                ->whereNull('func_cont_subs.deleted_by')
                ->whereNull('func_cont_subs.deleted_at') 
               ->get();
                if ($getSubsidioFun->isEmpty()) {
                    $inserir=true;
                    $create_func_cont_sub = DB::table('funcionario_contrato_subsidio')->insert([
                        'id_funcionario' => $request->funcionario,
                        'id_funcionario_cargo' => $request->funcionario_contrato,
                        'id_subsidio' => $value,
                        'valor' => $request->valor,
                        'created_by' => Auth::user()->id,
                        'update_by' => Auth::user()->id,
                        'created_at' => Carbon::Now(),
                        'update_at' => Carbon::Now()
                    ]);
                }
            } 
            if ($inserir==true) {
               Toastr::success(__('O(s) subsídio(s) do funcionário foram atribuido com sucesso'), __('toastr.success'));
               return redirect()->back();
            } else{
               return redirect()->back();
            }
            

        } catch (Exception | Throwable $e) {
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }

    }

    public function ajaxContratoSubsidioFuncionario() {

        try{
            $getSubsidioCargoFuncionario=DB::table('funcionario_contrato_subsidio as func_cont_subs')
                ->join('users as use','use.id','=','func_cont_subs.id_funcionario')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('func_cont_subs.id_funcionario', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
                })
                ->select([
                    'func_cont_subs.id_funcionario',
                    'full_name.value as name_funcionario',
                    'use.email as email_funcionario'
                ])
                ->whereNull('func_cont_subs.deleted_by')
                ->whereNull('func_cont_subs.deleted_at')        
                ->distinct('func_cont_subs.id_funcionario')
            ->get();

            // return response()->json(['data'=>$getSubsidioCargoFuncionario]);
            
            return DataTables::of($getSubsidioCargoFuncionario)
            ->addColumn('actions', function ($item) {
                return view('RH::salarioHonorario.salario-subsidios.add-subsidios.datatables.actions',compact('item'));
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


    public function getSubsidiosContrato($id_user)
    {   
        try{
            $getSubsidioCargoFun=DB::table('funcionario_contrato_subsidio as fun_cont_sub')
            ->join('role_translations as role_trans',function ($join)
            {
                    $join->on('role_trans.role_id', '=', 'fun_cont_sub.id_funcionario_cargo');
                    $join->on('role_trans.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('role_trans.active', '=', DB::raw(true));
            })
            ->whereNull('fun_cont_sub.deleted_at')
            ->whereNull('fun_cont_sub.deleted_by')
            ->where('fun_cont_sub.id_funcionario',$id_user)
            ->get();
            return response()->json($getSubsidioCargoFun);
        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }  
    }
    
    public function recuros_ajaxSubsidioFuncionario($id_funcionario)
    {
        $getSubsidioCargoFun=DB::table('funcionario_contrato_subsidio as func_cont_subs')
            ->join('role_translations as role_trans', function ($join) {
                $join->on('role_trans.role_id', '=', 'func_cont_subs.id_funcionario_cargo')
                ->where('role_trans.language_id', 1)
                ->where('role_trans.active', 1);
            })
            ->leftJoin('funcionario_with_contrato as fun_with_cont', function ($join) {
                $join->on('fun_with_cont.id_user', '=', 'func_cont_subs.id_funcionario');
            })
            ->leftJoin('fun_with_type_contrato as fun_with_type_cont', function ($join) {
                $join->on('fun_with_type_cont.id_fun_with_contrato', '=', 'fun_with_cont.id');
            })

            ->select([
                'func_cont_subs.id_funcionario_cargo',
                'role_trans.display_name as name_role',
                'fun_with_type_cont.status_contrato as status_contrato'
            ])
            ->whereNull('func_cont_subs.deleted_by')
            ->whereNull('func_cont_subs.deleted_at')        
            ->whereNull('fun_with_type_cont.deleted_at')        
            ->where('fun_with_type_cont.status_contrato','=','ativo')        
            ->where('func_cont_subs.id_funcionario','=',$id_funcionario)        
            ->distinct('func_cont_subs.id_funcionario_cargo') 
        ->get();
       

        $getSubsidio=DB::table('funcionario_contrato_subsidio as func_cont_subs')
        ->leftJoin('subsidio as sub', function ($join) {
            $join->on('sub.id', '=', 'func_cont_subs.id_subsidio');
        })     
        ->whereNull('func_cont_subs.deleted_by')
        ->whereNull('func_cont_subs.deleted_at')     
        ->where('func_cont_subs.id_funcionario','=',$id_funcionario)
        ->get();

        // return response()->json($getSubsidio);

        return DataTables::of($getSubsidioCargoFun)
        // ->addColumn('actions', function ($item)  {
        //     return view('RH::salarioHonorario.salario-subsidios.add-subsidios.datatables.actions_subsidios',compact('item'));
        // })
        ->addColumn('subsidios', function ($item) use($getSubsidio,$id_funcionario)  {
            return view('RH::salarioHonorario.salario-subsidios.add-subsidios.datatables.subsidios',compact('item','getSubsidio','id_funcionario'));
        })
        ->rawColumns(['actions','subsidios'])
        ->addIndexColumn()
        ->make(true);

    }

    public function deleteSubsidioFuncionario($getIdSubsidio)
    {
        try{
            $getIdSubsidio=explode(',',$getIdSubsidio);
            $getFunProcessoSalario=DB::table('funcionario_contrato_subsidio as fun_cont_subsidio')
            ->join('historic_processamento_salario_subsidio as histo_pro_salario_subsidio',function ($join)
            {
                $join->on('histo_pro_salario_subsidio.id_subsidio','=','fun_cont_subsidio.id');
            })
            ->whereNull('fun_cont_subsidio.deleted_at')
            ->whereNull('fun_cont_subsidio.deleted_by')
            ->where('fun_cont_subsidio.id_funcionario_cargo','=',$getIdSubsidio[1])
            ->where('fun_cont_subsidio.id_funcionario','=',$getIdSubsidio[0])
            ->where('fun_cont_subsidio.id_subsidio','=',$getIdSubsidio[2])
            ->get();
            $coint=count($getFunProcessoSalario);
            if ($coint==0) {
                $confirme=1;
            }else{
                $confirme=0;
            }
            $delete_func_cont_sub = DB::table('funcionario_contrato_subsidio as func_cont_subs')
                ->where('func_cont_subs.id_funcionario','=',$getIdSubsidio[0])
                ->where('func_cont_subs.id_funcionario_cargo','=',$getIdSubsidio[1])
                ->where('func_cont_subs.id_subsidio','=',$getIdSubsidio[2])
            ->update([                  
                'deleted_at' =>Carbon::Now(),
                'deleted_by' =>Auth::user()->id,
            ]);

            
            
            return response()->json(['response'=>$confirme]);
        } catch (Exception | Throwable $e) {
            // return response()->json(['response'=>$e]);
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        } 

    }


    public function getProcessoSalario(Request $request)
    {
        // return $request;
        /**
         * consultar processamento de salário dos funcinarios.
         * Conslta por banco e outros.
         */
        
        try{
            
            $vetorMonth=explode('/',$request->vencimentoMonth);
            $id_funContrato=[];
            $id_funcionario=$request->idFuncionario;
            $id_cargo=$request->cargoModal;
            $month=$vetorMonth[0];
            $year=$vetorMonth[1];
            $banco_indisponivel=$request->bancos[0];
            $bancos=$request->bancos;
            $dataPagamento='';
            $dataPagamentobanco='';
             getLocalizedMonths();
             foreach (getLocalizedMonths() as $key => $value) {
                if ($value['display_name'] == $month) {
                     $month=$value['id'];
                }
             }
           
             $getContratoFun=DB::table('funcionario_with_contrato as fun_with_cont') 
                ->leftJoin('fun_with_type_contrato as fun_with_type_cont','fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato')
                ->whereNull('fun_with_type_cont.deleted_at')
                ->whereNull('fun_with_type_cont.deleted_by')
                // ->where('fun_with_type_cont.status_contrato','=','ativo')
                ->when($request->idFuncionario != null, function ($q) use($id_funcionario) {
                    return $q->whereIn('fun_with_cont.id_user', $id_funcionario);
                })
                ->when($request->cargoModal != null, function ($q) use($id_cargo) {
                    return $q->whereIn('fun_with_type_cont.id_cargo', $id_cargo);
                })
            ->get();
            foreach ($getContratoFun as $key => $item) {
               $id_funContrato[]=$item->id;
            }
    
            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->leftJoin('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->leftJoin('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
                {
                    $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
                })
                ->leftJoin('users as use','use.id','=','fun_with_cont.id_user')
                ->leftJoin('user_parameters as full_name',function($join)
                {
                    $join->on('full_name.users_id','=','use.id')
                    ->where('full_name.parameters_id',1); 
                })
                ->leftJoin('role_translations as role_trans',function($join)
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
                

                ->leftJoin('rh_contrato_bank as contrato_with_bank',function ($join)
                {
                   $join->on('contrato_with_bank.id','=','processam_sl.id_conta_bank_fun_contrato')
                   ->where('contrato_with_bank.status','ativo');
                })
                ->leftJoin('rh_users_bank as rh_users_banks',function ($join)
                {
                   $join->on('rh_users_banks.id','=','contrato_with_bank.id_user_bank');
                })
                ->leftJoin('banks as bank',function ($join)
                {
                   $join->on('bank.id','=','rh_users_banks.bank_id');
                })
                ->select([
                    'bank.display_name as nome_banco',
                    'rh_users_banks.iban as iban_banco',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.month as month',
                    'processam_sl.year as year',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valorReembolso as valorReembolso',
                    'processam_sl.valor_falta as valor_falta',
                    'processam_sl.created_at as created_at',
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
                // ->when($id_funContrato != null, function ($q) use($id_funContrato) {
                //     return $q->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato);
                // })
                ->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato)

                ->when($banco_indisponivel != null, function ($q) use($bancos) {
                    return $q->whereIn('bank.id', $bancos);
                })
            ->get()
            ->groupBy(['id_processam_sl','name_funcionario','name_cargo']);
            
            
            

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
                ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
                {
                    $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
                })
                ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
                ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
               

                ->leftJoin('rh_contrato_bank as contrato_with_bank',function ($join)
                {
                   $join->on('contrato_with_bank.id','=','processam_sl.id_conta_bank_fun_contrato')
                   ->where('contrato_with_bank.status','ativo');
                })
                ->leftJoin('rh_users_bank as rh_users_banks',function ($join)
                {
                   $join->on('rh_users_banks.id','=','contrato_with_bank.id_user_bank');
                })
                ->leftJoin('banks as bank',function ($join)
                {
                   $join->on('bank.id','=','rh_users_banks.bank_id');
                })
                ->select([
                    'hist_processo_imposto.id_processamento as id_processamento',
                    'impost.id as id_impost',
                    'code_dev.code as nome_code',
                    'impost.display_name as name_imposto',
                    'impost.discricao as discricao',
                    'hist_processo_imposto.id_impostoYear as id_impostoYear'
                ])

                ->when($banco_indisponivel != null, function ($q) use($bancos) {
                    return $q->whereIn('bank.id', $bancos);
                })
                ->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato)

                // ->when($id_funContrato != null, function ($q) use($id_funContrato) {
                //     return $q->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato);
                // })
                ->orderBy('impost.display_name','ASC')
            ->get();
            $dataCreated=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $item){
                            return $item->created_at;
                        }
                    }
                }
            });  
            // foreach ($dataCreated as $key => $value) {
                $dataCreated=Carbon::parse()->day.'/'.Carbon::parse()->month. '/'.Carbon::parse()->year;
            // } 
            
            foreach(getLocalizedMonths() as $item){
                if ($item['id']==$month && $banco_indisponivel!=null) {
                    $dataPagamentobanco=$item['display_name']."/".$year;
                }else if($item['id']==$month && $banco_indisponivel ==null){
                     $dataPagamento=$item['display_name']."/".$year;
                }
            }
            
            

            $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
            ->whereNull('sub_impost.deleted_at')
            ->whereNull('sub_impost.deleted_by')
            ->get();

             $institution = Institution::
            // latest()

                leftJoin('user_parameters as directorg',function($join)
                {
                    $join->on('directorg.users_id','=','institutions.director_geral')
                    ->where('directorg.parameters_id',1); 
                })
                ->leftJoin('user_parameters as recursos_humano',function($join)
                {
                    $join->on('recursos_humano.users_id','=','institutions.recursos_humanos')
                    ->where('recursos_humano.parameters_id',1); 
                })
                ->join('users as user','user.id','=','institutions.director_geral')
                ->join('users as us','us.id','=','institutions.recursos_humanos')
                ->select([
                    'directorg.value as directorGeral',
                    'recursos_humano.value as recursosHumano',
                    'user.name as directorGeralName',
                    'us.name as recursos_humano',

                    'institutions.id as id',
                    'institutions.nome as nome',
                    'institutions.morada as morada',
                    'institutions.provincia as provincia',
                    'institutions.municipio as municipio',
                    'institutions.contribuinte as contribuinte',
                    'institutions.capital_social as capital_social',
                    'institutions.registro_comercial_n as registro_comercial_n',
                    'institutions.registro_comercial_de as registro_comercial_de',
                    'institutions.dominio_internet as dominio_internet',
                    'institutions.telefone_geral as telefone_geral',
                    'institutions.telemovel_geral as telemovel_geral',
                    'institutions.email as email',
                    'institutions.whatsapp as whatsapp',
                    'institutions.facebook as facebook',
                    'institutions.instagram as instagram',
                    'institutions.director_geral as director_geral',
                    'institutions.vice_director_academica as vice_director_academica',
                    'institutions.vice_director_cientifica as vice_director_cientifica',
                    'institutions.daac as daac',
                    'institutions.gabinete_termos as gabinete_termos',
                    'institutions.secretaria_academica as secretaria_academica',
                    'institutions.director_executivo as director_executivo',
                    'institutions.recursos_humanos as recursos_humanos',
                    'institutions.nome_dono as nome_dono',
                    'institutions.nif as nif',
                    'institutions.logotipo as logotipo',
                    'institutions.instituicao_arquivo as instituicao_arquivo',
                    'institutions.cursos_arquivo as cursos_arquivo',
                    'institutions.decreto_instituicao as decreto_instituicao',
                    'institutions.decreto_cursos as decreto_cursos',
                    'institutions.created_at as created_at',
                    'institutions.updated_at as updated_at',
                    // 'institutions.users_id as users_id',
                    // 'institutions.parameters_id as parameters_id',
                    // 'institutions.description as description',
                    // 'institutions.created_by as created_by',
                    // 'institutions.updated_by as updated_by',
                    // 'institutions.deleted_by as deleted_by',
                    // 'institutions.deleted_at as deleted_at',
                ])
            ->get();
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento =   2;
            $tam = count($institution);
            if($tam == 0){
                 Toastr::warning("A forLEARN não detectou a existência do director geral.", __('toastr.warning'));
                 return back();
            }
             $data=[
                'institution' => $institution[count($institution)-1],
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'getFunProcessoSalario'=>$getFunProcessoSalario,
                'getFunProcessoImposto'=>$getFunProcessoImposto,
                'dataCreated'=>$dataCreated,
                'dataPagamento'=>$dataPagamento,
                'dataPagamentobanco'=>$dataPagamentobanco,
                'getSubsidioImposto'=>$getSubsidioImposto,
                'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
            ];
        //   return view("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_geral", $data);
            $institution=$institution[count($institution)-1];
            if ($banco_indisponivel != null) {
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_geralBanco", $data);              
                $pdf->setOption('margin-top', '3mm');
                $pdf->setOption('margin-left', '3mm');
                $pdf->setOption('margin-bottom', '2cm');
                $pdf->setOption('margin-right', '3mm');
                $pdf->setOption('enable-javascript', true);
                $pdf->setOption('debug-javascript', true);
                $pdf->setOption('javascript-delay', 1000);
                $pdf->setOption('enable-smart-shrinking', true);
                $pdf->setOption('no-stop-slow-scripts', true);
                $pdf->setOption('footer-html', $footer_html);
                $pdf->setPaper('a4');       
                // return $pdf->download('RH[processamento salário]'.'.pdf');
                return $pdf->stream('Forlearn | estado-mensalidade.pdf');
            }else{
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_geral", $data);              
                $pdf->setOption('margin-top', '3mm');
                $pdf->setOption('margin-left', '3mm');
                $pdf->setOption('margin-bottom', '2cm');
                $pdf->setOption('margin-right', '3mm');
                $pdf->setOption('enable-javascript', true);
                $pdf->setOption('debug-javascript', true);
                $pdf->setOption('javascript-delay', 1000);
                $pdf->setOption('enable-smart-shrinking', true);
                $pdf->setOption('no-stop-slow-scripts', true);
                $pdf->setOption('footer-html', $footer_html);
                $pdf->setPaper('a4','landscape');   
                // return $pdf->download('RH[processamento salário]'.'.pdf');
                return $pdf->stream('Forlearn | estado-mensalidade.pdf');

            }

        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    
    
    // PAGAMENTOS - RECIBOS - MENSAL
    
    public function getProcessoSalarioMes(Request $request)
    {
       

        /**
         * consultar processamento de salário dos funcinarios.
         * Conslta por banco e outros.
         */
        
        try{
            
            $vetorMonth=explode('/',$request->vencimentoMonth);
            $id_funContrato=[];
            $id_funcionario=$request->idFuncionario;
            $id_cargo=$request->cargoModal;
            $month=$vetorMonth[0];
            $year=$vetorMonth[1];
            //$banco_indisponivel=$request->bancos[0];
            $banco_indisponivel=[];
            $bancos=$request->bancos;
            $dataPagamento='';
            $dataPagamentobanco='';
             getLocalizedMonths();
             foreach (getLocalizedMonths() as $key => $value) {
                if ($value['display_name'] == $month) {
                     $month=$value['id'];
                }
             }
           
            $getContratoFun=DB::table('funcionario_with_contrato as fun_with_cont') 
                ->leftJoin('fun_with_type_contrato as fun_with_type_cont','fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato')
                ->whereNull('fun_with_type_cont.deleted_at')
                ->whereNull('fun_with_type_cont.deleted_by')
                // ->where('fun_with_type_cont.status_contrato','=','ativo')
                ->when($request->idFuncionario != null, function ($q) use($id_funcionario) {
                    return $q->whereIn('fun_with_cont.id_user', $id_funcionario);
                })
                ->when($request->cargoModal != null, function ($q) use($id_cargo) {
                    return $q->whereIn('fun_with_type_cont.id_cargo', $id_cargo);
                })
            ->get();
            foreach ($getContratoFun as $key => $item) {
               $id_funContrato[]=$item->id;
            }

            $getFunProcessoSalario=DB::table('processamento_salarial as processam_sl')
                ->leftJoin('fun_with_type_contrato as fun_with_type_cont',function($join){
                    $join->on('fun_with_type_cont.id','=','processam_sl.id_fun_type_contrato');
                })
                ->leftJoin('funcionario_with_contrato as fun_with_cont',function($join)
                {
                    $join->on('fun_with_cont.id','=','fun_with_type_cont.id_fun_with_contrato');
                })
                ->leftJoin('recurso_humano_salario_funcionario as rh_salario_funcionario',function($join)
                {
                    $join->on('rh_salario_funcionario.id','=','processam_sl.id_salariotempo_funcionario');
                })
                ->leftJoin('users as use','use.id','=','fun_with_cont.id_user')
                ->leftJoin('user_parameters as full_name',function($join)
                {
                    $join->on('full_name.users_id','=','use.id')
                    ->where('full_name.parameters_id',1); 
                })
                ->leftJoin('role_translations as role_trans',function($join)
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
                

                ->leftJoin('rh_contrato_bank as contrato_with_bank',function ($join)
                {
                   $join->on('contrato_with_bank.id','=','processam_sl.id_conta_bank_fun_contrato')
                   ->where('contrato_with_bank.status','ativo');
                })
                ->leftJoin('rh_users_bank as rh_users_banks',function ($join)
                {
                   $join->on('rh_users_banks.id','=','contrato_with_bank.id_user_bank');
                })
                ->leftJoin('banks as bank',function ($join)
                {
                   $join->on('bank.id','=','rh_users_banks.bank_id');
                })
                ->select([
                    'bank.display_name as nome_banco',
                    'rh_users_banks.iban as iban_banco',
                    'rh_users_banks.conta as conta_banco',
                    'processam_sl.id as id_processam_sl',
                    'processam_sl.month as month',
                    'processam_sl.year as year',
                    'processam_sl.qtd_falta as qtd_falta',
                    'processam_sl.valorReembolso as valorReembolso',
                    'processam_sl.valor_falta as valor_falta',
                    'processam_sl.created_at as created_at',
                    'full_name.value as name_funcionario',
                    'processam_sl.salario_base as salarioBase',
                    'role_trans.display_name as name_cargo',
                    'subsid.display_name as name_subsidio',
                    'subsid.id as id_subsidio',
                    'func_contr_subsidio.valor as valor_subsidio',
                    'use.id as id_user',
                    'role_trans.role_id as id_cargo',
                    'fun_with_type_cont.data_inicio_conrato as data_inicio_conrato',
                    'fun_with_type_cont.data_fim_contrato as data_fim_contrato',
                    'fun_with_type_cont.status_contrato as status_contrato'
                ])
                ->where('processam_sl.month','=',$month)
                ->where('processam_sl.year','=',$year)
                ->whereNull('role_trans.deleted_at')
                ->whereNull('processam_sl.deleted_by')
                ->whereNull('processam_sl.deleted_at')
                ->orderBy('role_trans.display_name','ASC')
                // ->when($id_funContrato != null, function ($q) use($id_funContrato) {
                //     return $q->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato);
                // })
                ->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato)

                ->when($banco_indisponivel != null, function ($q) use($bancos) {
                    return $q->whereIn('bank.id', $bancos);
                })
            ->get()
            ->groupBy(['id_processam_sl','name_funcionario','name_cargo']);
                        
            
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
                ->join('historic_processamento_salario_imposto as hist_processo_imposto',function($join)
                {
                    $join->on('hist_processo_imposto.id_processamento','=','processam_sl.id'); 
                })
                ->join('imposto_year as impostYear','impostYear.id','=','hist_processo_imposto.id_impostoYear')
                ->join('imposto as impost','impost.id','=','impostYear.id_imposto')
                ->leftJoin('code_developer as code_dev','code_dev.id','=','impost.id_code_dev') 
               

                ->leftJoin('rh_contrato_bank as contrato_with_bank',function ($join)
                {
                   $join->on('contrato_with_bank.id','=','processam_sl.id_conta_bank_fun_contrato')
                   ->where('contrato_with_bank.status','ativo');
                })
                ->leftJoin('rh_users_bank as rh_users_banks',function ($join)
                {
                   $join->on('rh_users_banks.id','=','contrato_with_bank.id_user_bank');
                })
                ->leftJoin('banks as bank',function ($join)
                {
                   $join->on('bank.id','=','rh_users_banks.bank_id');
                })
                ->select([
                    'hist_processo_imposto.id_processamento as id_processamento',
                    'impost.id as id_impost',
                    'code_dev.code as nome_code',
                    'impost.display_name as name_imposto',
                    'impost.discricao as discricao',
                    'hist_processo_imposto.id_impostoYear as id_impostoYear'
                ])

                ->when($banco_indisponivel != null, function ($q) use($bancos) {
                    return $q->whereIn('bank.id', $bancos);
                })
                ->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato)

                // ->when($id_funContrato != null, function ($q) use($id_funContrato) {
                //     return $q->whereIn('processam_sl.id_fun_type_contrato', $id_funContrato);
                // })
                ->orderBy('impost.display_name','ASC')
            ->get();
            
            $dataCreated=collect($getFunProcessoSalario)->map(function($item){
                foreach($item as $getIdprocesso){
                    foreach($getIdprocesso as $getNomeFun){
                        foreach($getNomeFun as $item){
                            return $item->created_at;
                        }
                    }
                }
            });  
            // foreach ($dataCreated as $key => $value) {
                $dataCreated=Carbon::parse()->day.'/'.Carbon::parse()->month. '/'.Carbon::parse()->year;
            // } 
            
            foreach(getLocalizedMonths() as $item){
                if ($item['id']==$month && $banco_indisponivel!=null) {
                    $dataPagamentobanco=$item['display_name']."/".$year;
                }else if($item['id']==$month && $banco_indisponivel ==null){
                     $dataPagamento=$item['display_name']."/".$year;
                }
            }
            
            

            $getSubsidioImposto=DB::table('subsidio_imposto as sub_impost')
            ->whereNull('sub_impost.deleted_at')
            ->whereNull('sub_impost.deleted_by')
            ->get();

             $institution = Institution::
                leftJoin('user_parameters as directorg',function($join)
                {
                    $join->on('directorg.users_id','=','institutions.director_geral')
                    ->where('directorg.parameters_id',1); 
                })
                ->leftJoin('user_parameters as recursos_humano',function($join)
                {
                    $join->on('recursos_humano.users_id','=','institutions.recursos_humanos')
                    ->where('recursos_humano.parameters_id',1); 
                })
                ->leftjoin('users as user','user.id','=','institutions.director_geral')
                ->leftjoin('users as us','us.id','=','institutions.recursos_humanos')
                ->select([
                    'directorg.value as directorGeral',
                    'recursos_humano.value as recursosHumano',
                    'user.name as directorGeralName',
                    'us.name as recursos_humano',

                    'institutions.id as id',
                    'institutions.nome as nome',
                    'institutions.morada as morada',
                    'institutions.provincia as provincia',
                    'institutions.municipio as municipio',
                    'institutions.contribuinte as contribuinte',
                    'institutions.capital_social as capital_social',
                    'institutions.registro_comercial_n as registro_comercial_n',
                    'institutions.registro_comercial_de as registro_comercial_de',
                    'institutions.dominio_internet as dominio_internet',
                    'institutions.telefone_geral as telefone_geral',
                    'institutions.telemovel_geral as telemovel_geral',
                    'institutions.email as email',
                    'institutions.whatsapp as whatsapp',
                    'institutions.facebook as facebook',
                    'institutions.instagram as instagram',
                    'institutions.director_geral as director_geral',
                    'institutions.vice_director_academica as vice_director_academica',
                    'institutions.vice_director_cientifica as vice_director_cientifica',
                    'institutions.daac as daac',
                    'institutions.gabinete_termos as gabinete_termos',
                    'institutions.secretaria_academica as secretaria_academica',
                    'institutions.director_executivo as director_executivo',
                    'institutions.recursos_humanos as recursos_humanos',
                    'institutions.nome_dono as nome_dono',
                    'institutions.nif as nif',
                    'institutions.logotipo as logotipo',
                    'institutions.instituicao_arquivo as instituicao_arquivo',
                    'institutions.cursos_arquivo as cursos_arquivo',
                    'institutions.decreto_instituicao as decreto_instituicao',
                    'institutions.decreto_cursos as decreto_cursos',
                    'institutions.created_at as created_at',
                    'institutions.updated_at as updated_at',
                    // 'institutions.users_id as users_id',
                    // 'institutions.parameters_id as parameters_id',
                    // 'institutions.description as description',
                    // 'institutions.created_by as created_by',
                    // 'institutions.updated_by as updated_by',
                    // 'institutions.deleted_by as deleted_by',
                    // 'institutions.deleted_at as deleted_at',
                ])
            ->first();
            
            $titulo_documento = "DOCUMENTO";
            $documentoGerado_documento = "Documento gerado a";
            $documentoCode_documento =   2;
           
            $data=[
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'getFunProcessoSalario'=>$getFunProcessoSalario,
                'getFunProcessoImposto'=>$getFunProcessoImposto,
                'dataCreated'=>$dataCreated,
                'dataPagamento'=>$dataPagamento,
                'dataPagamentobanco'=>$dataPagamentobanco,
                'getSubsidioImposto'=>$getSubsidioImposto,
                'gethistoricProcessoImposto'=>$gethistoricProcessoImposto
            ];
    
        //   return view("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_geral", $data);
            
            $institution=$institution;
            
            if ($banco_indisponivel != null) {
                
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_mes", $data);              
                $pdf->setOption('margin-top', '3mm');
                $pdf->setOption('margin-left', '3mm');
                $pdf->setOption('margin-bottom', '2cm');
                $pdf->setOption('margin-right', '3mm');
                $pdf->setOption('enable-javascript', true);
                $pdf->setOption('debug-javascript', true);
                $pdf->setOption('javascript-delay', 1000);
                $pdf->setOption('enable-smart-shrinking', true);
                $pdf->setOption('no-stop-slow-scripts', true);
                $pdf->setOption('footer-html', $footer_html);
                $pdf->setPaper('a4');       
                // return $pdf->download('RH[processamento salário]'.'.pdf');
                return $pdf->stream('Forlearn | estado-mensalidade.pdf');
            }else{
                
                $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                $pdf = PDF::loadView("RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_reciboSalario_mes", $data);              
                $pdf->setOption('margin-top', '3mm');
                $pdf->setOption('margin-left', '3mm');
                $pdf->setOption('margin-bottom', '2cm');
                $pdf->setOption('margin-right', '3mm');
                $pdf->setOption('enable-javascript', true);
                $pdf->setOption('debug-javascript', true);
                $pdf->setOption('javascript-delay', 1000);
                $pdf->setOption('enable-smart-shrinking', true);
                $pdf->setOption('no-stop-slow-scripts', true);
                $pdf->setOption('footer-html', $footer_html);
                $pdf->setPaper('a4','landscape');   
                // return $pdf->download('RH[processamento salário]'.'.pdf');
                return $pdf->stream('Forlearn | estado-mensalidade.pdf');

            }
            

        } catch (Exception | Throwable $e) {
            return $e;
            Log::error($e);
            return \Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

}


