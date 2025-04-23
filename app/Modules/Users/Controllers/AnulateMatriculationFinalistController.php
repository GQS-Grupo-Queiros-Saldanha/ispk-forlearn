<?php

namespace App\Modules\Users\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Brian2694\Toastr\Facades\Toastr;
use App\Modules\GA\Models\LectiveYear;
use App\Http\Controllers\Controller;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Auth;
use DB;

class AnulateMatriculationFinalistController extends Controller
{

    public function index()
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])->get();
        $currentData = Carbon::now();
        $lectiveYearSelected = DB::table('lective_years')
            ->whereRaw('"' . $currentData . '" between `start_date` and `end_date`')->first();
        $data = ['lectiveYears' => $lectiveYears, 'lectiveYearSelected' => $lectiveYearSelected->id];
        return view('Users::anulate_matriculation_finalist.index')->with($data);
    }

    private function anular($dados, $article, $matriculation)
    {

        if ($article) {
            $ConfirmaAnulateMatriculation = DB::table('anulate_matriculation_finalist')
                ->where('id_matricula', $matriculation->id)
                ->whereNull('deleted_at')
                ->first();
            if (!$ConfirmaAnulateMatriculation) {
                //verificar existencia já de emolumento 
                $ConfirmaEmolumentoExist = DB::table('article_requests')
                    ->where('user_id', $matriculation->user_id)
                    ->where('article_id', $article[0]->id_emolumento)
                    ->whereNull('deleted_at')
                    ->get();
                //Anular matricula 
             //   dd('asai',$dados->all());
                DB::table('anulate_matriculation_finalist')->updateOrInsert(
                    ['id_matricula' => $matriculation->id],
                    [
                        "mode_anulate" => isset($dados->admin_anulate) ? 1 : 0,
                        "description" => $dados->anulate_observetion != "" ? $dados->anulate_observetion : "Nenhuma",
                        "created_by" => Auth::user()->id,
                        "updated_by" => Auth::user()->id,
                    ]
                );
               // dd('aai');
                if (isset($dados->admin_anulate) != null) {
                    //return 1;
                    $this->pendingDelete($matriculation->user_id,$matriculation->year_lectivo);
                    $currentData = Carbon::now();
                    //Anular sem gerar emolumento
                    DB::table('matriculation_finalist')
                        ->where('id', $matriculation->id)
                        ->where('user_id', $matriculation->user_id)
                        ->update(['deleted_at' => $currentData, 'deleted_by' => Auth::user()->id]);
                 
                    Toastr::success(__('A anulação de matricula "ADMINISTRATIVA" foi efectuada com sucesso!'), __('toastr.success'));
                    return redirect()->route('matriculations.index');
                }
                //Criar emolumento na tesouraria
                else if ($ConfirmaEmolumentoExist->isEmpty()) {
                    //return 1;
                    
                    createAutomaticArticleRequest($matriculation->user_id, $article[0]->id_emolumento, null, null);
                    //Rertanar para validar 
                    
                    Toastr::success(__('o pedido de anulação de matricula foi efectuada com sucesso, efectue o pagamento do emolumento "Anulação de matrícula" para validar!'), __('toastr.success'));
                    return redirect()->back();
                }
            } else {
                Toastr::warning(__('A forLEARN não  detectou um emolumento de anulação de matrícula configurado no CODDEV para o ano selecionado na matrícula que se predente anular, Verifica se existe este emolumento criado nas configurações da tesouraria, caso contrário faça a duplicação do mesmo!'), __('toastr.warning'));
                return redirect()->back();
            }
        }
    }

    public function store(Request $request)
    {
        try {
            
            $matriculationExiste = DB::table('matriculation_finalist')->where('id', $request->matricula_id)->first();
           
            if (isset($matriculationExiste->id)) {
                //pegar o emolumento de anulação de matrícula e anular
                $article = EmolumentCodevLective("anul_matric", $matriculationExiste->year_lectivo);
                //Anular a matricula
                return  $this->anular($request, $article, $matriculationExiste);
            } else {
                Toastr::warning(__('A matrícula que tentou anular não foi detectada, tente novamente, caso o erro persistir contacte o apoio a forLEARN!'), __('toastr.warning'));
                return redirect()->back();
            }
        } catch (Exception $e) {
            dd($e);
            logError($e);
            return request()->ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function getAnulateMatriculation($anoLective)
    {
        try {
            $lectiveYearSelected = DB::table('lective_years')->where('id', $anoLective)->first();
            $EmolumentoAnulacao = EmolumentCodevLective("anul_matric", $lectiveYearSelected->id);
            $model = DB::table('matriculation_finalist')
                ->join('anulate_matriculation_finalist as anulate_m', 'anulate_m.id_matricula', '=', 'matriculation_finalist.id')
                ->join('users as u0', 'u0.id', '=', 'matriculation_finalist.user_id')
                ->join('users as u1', 'u1.id', '=', 'anulate_m.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'anulate_m.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'matriculation_finalist.deleted_by')
                ->leftJoin('user_courses as uc', 'uc.users_id', '=', 'u0.id')
                ->join('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                ->leftJoin('matriculation_classes as mc', 'mc.matriculation_id', '=', 'matriculation_finalist.id')
                ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('u0.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('u0.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->leftJoin('user_parameters as up_bi', function ($join) {
                    $join->on('u0.id', '=', 'up_bi.users_id')
                        ->where('up_bi.parameters_id', 14);
                })

                ->leftJoin('article_requests as art_requests', function ($join) use ($EmolumentoAnulacao) {
                    $join->on('art_requests.user_id', '=', 'u0.id')
                        ->where('art_requests.article_id', $EmolumentoAnulacao[0]->id_emolumento)
                        ->whereNull('art_requests.deleted_by')
                        ->whereNull('art_requests.deleted_at');
                })
                ->select([
                    'matriculation_finalist.id',
                    'matriculation_finalist.num_confirmaMatricula as code_matricula',
                    'matriculation_finalist.user_id',
                    'anulate_m.created_at as criado_em',
                    'matriculation_finalist.year_curso as year_curso',
                    'up_meca.value as matricula',
                    'u0.id as id_usuario',
                    'art_requests.status as state',
                    'up_bi.value as num_bi',
                    'u_p.value as name_full',
                    'u0.email as email',
                    'u1.name as criado_por',
                    'u2.name as actualizado_por',
                    'u3.name as deletador_por',
                    'ct.display_name as course',
                    'anulate_m.id as id_anulate_matriculation',
                    'anulate_m.mode_anulate',
                    'anulate_m.description as descricao',
                ])
                ->where('matriculation_finalist.year_lectivo', $lectiveYearSelected->id)
                ->groupBy('u_p.value')
                ->distinct('id')
                ->get();
            return Datatables::of($model)
                ->addColumn('actions', function ($item) {
                    return view('Users::anulate_matriculation.datatables.action')->with('item', $item);
                })
                ->addColumn('states', function ($state) {
                    return view('Users::anulate_matriculation.datatables.states')->with('state', $state);
                })
                ->rawColumns(['actions', 'states'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function pendingDelete($id,$year)
    {

       
        
         $lective = LectiveYear::whereId($year)
        // ->select(['end_date'])
        ->first();
        
         $emolumentos = DB::table("article_requests")
        ->where("user_id",$id)
        ->where("status","pending")
        // ->where("month",">",date("m"))
        ->whereNotNull("month")
        ->whereNotNull("year") 
        ->where("discipline_id","")
        ->whereNull("deleted_at")   
        ->select(['id','month','year'])
        // ->whereBetween('article_requests.created_at', [$lective->start_date, $lective->end_date])
        ->get(); 


        $final = $lective->end_date;
        
         $emo = collect($emolumentos)->groupBy('id')->map(function ($item,$key)use($final) {
            
            $mes = $item[0]->month;
            $ano = $item[0]->year;
            $qtd_mes = strlen($mes);

            if (($qtd_mes==1) && ($mes<10)) {
                $mes = "0".$mes;
            }
            
            
            $dataactual = date("Y-m");
            $dataemo = $ano."-".$mes;
            $final = explode("-",$final);
            $final = $final[0]."-".$final[1];

            if(($dataemo>$dataactual) && ($dataemo<=$final)) {
                return $item[0]->id;
            }            
        });

        
        foreach ($emo as $item) {   
            $deletar = DB::table('article_requests')
            ->where('id', "=",$item)
            ->update(
                [
                    "deleted_by" => auth()->user()->id,
                    "deleted_at" => Carbon::now()
                ]
            );

            if ($item=="" || $item==0 || $item==null) {
                
            }else{
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

                    $obs = "O emolumento '".$arti->emolumento."' foi eliminado automáticamente por razões de
                     anulação de matrícula...";
                    $Observation = DB::table('current_account_observations')
                    ->insert([
                        'user_id' =>$arti->user_id,
                        'observation' => $obs,
                        'file' => "Sem arquivo anexado...",
                    ]); 
            }
                    
        }

        


        
    }
}
