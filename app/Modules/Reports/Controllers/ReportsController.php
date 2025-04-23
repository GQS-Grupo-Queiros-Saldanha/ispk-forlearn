<?php

namespace App\Modules\Reports\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Throwable;
use timgws\QueryBuilderParser;
use DataTables;
use PDF;
use Carbon\Carbon;
use Toastr;
use App\Model\Institution;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;
use App\Modules\GA\Models\LectiveYear;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\ResumoExport;

class ReportsController extends Controller
{
public function generalReport()
    {
        return view('Reports::general');
    }
    public function generalReportAjax($role, $state, $start_from)
    {

        //25
        $users =  User::whereHas("roles", function ($roles) use ($role) {
            return $roles->where('id', $role);
        })
        ->leftJoin('user_parameters as u_p', function ($join) {
            $join->on('users.id', '=', 'u_p.users_id')
                ->where('u_p.parameters_id', 25);
                //->where('u_p.value', "6506_file_25_Maria Sabina Tomé.JPG");
        })

        ->leftJoin('user_courses as uc', 'uc.users_id','=','users.id')
        ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'uc.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
        ->leftJoin('user_parameters as u_pp', function ($join) {
                $join->on('users.id', '=', 'u_pp.users_id')
                ->where('u_pp.parameters_id', 19);
        })
        ->leftJoin('user_parameters as fullName', function ($join) {
            $join->on('users.id', '=', 'fullName.users_id')
            ->where('fullName.parameters_id', 1);
        })
        ->leftJoin('user_parameters as u_ppp', function ($join) {
                $join->on('users.id', '=', 'u_ppp.users_id')
                ->where('u_ppp.parameters_id', 14);
        })
        ->when($state == "matriculated", function($q){
            //return $q->has('matriculation');
            return $q->join('matriculations', 'matriculations.user_id','=','users.id');
        })->when($state == "non-matriculed", function($q) {
            return $q->doesntHave('matriculation');
        })
        /*->when($photo == "has-photo", function($q){
            //"6506_file_25_Maria Sabina Tomé.JPG"
            return $q->whereNotNull('u_p.value');
        })->when($photo == "non-photo", function($q){
            return $q->whereNull('u_p.value');
        })*/->when($start_from != null, function($q)use($start_from){
            return $q->where('u_pp.value', 'like', $start_from.'%');
        })


        ->select([
            'users.id',
            'fullName.value as name',
            'u_p.value as photo',
            'u_pp.value as mecanografico',
            'users.email',
            'ct.display_name as course_name',
            'u_ppp.value as b_identidade',
            'matriculations.code as matriculation_code'
        ])
        ->distinct()
        ->get();

        return response()->json($users);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

      //Criada pelo Marcos
    public function index(Request $request)
    {
        $lectiveYears = LectiveYear::with(['currentTranslation'])
        ->get();

       $currentData = Carbon::now();
       $lectiveYearSelected = DB::table('lective_years')
                       ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                       ->first();
       $lectiveYearSelected = $lectiveYearSelected->id ?? 6;
        
         return view('Reports::listar_recibo.listas_de_recibo',compact('lectiveYears','lectiveYearSelected'));

        
    }

    

    public function listasRecibos(Request $request)
    {


        switch ($request->submitButton) {
            case 'pdf':



                $DataInicio = $request->dataInicio;
                $DataFim = $request->dataFim;
                $date_from = Carbon::parse($DataInicio)->startOfDay();
                $date_to = Carbon::parse($DataFim)->endOfDay();
                $vetorCreditoAjuste = [];
                $est_vetorCreditoAjuste = [];

                if ($DataInicio > $DataFim) {

                    Toastr::error(__('A data inicial não pode ser maior que a data final.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } elseif ($DataFim > date('Y-m-d') || $DataInicio > date('Y-m-d')) {

                    Toastr::error(__('As datas não podem ser maior que a data de hoje.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');
                } elseif ($DataInicio == "" && $DataFim == "") {

                    Toastr::error(__('Os campos das datas não podem estar vazias.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } else {

                         $recibo = DB::table('transactions as tr')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as u_m', function ($join) {
                            $join->on('art.user_id', '=', 'u_m.users_id')
                                ->where('u_m.parameters_id', 19);
                        })

                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transacion', 'trans.id', 'trans.code', 'trans.path', 'users.name', 'trans.created_at', 'tr.value as valor', 'art.user_id', 'u_p.value as estudante', 'u_m.value as matricula', 'tr.type as type'])

                        ->where('tr.data_from', "!=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->distinct('trans.path')
                        ->get();

                        $est_recibo = DB::table('transactions as tr')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as u_m', function ($join) {
                            $join->on('art.user_id', '=', 'u_m.users_id')
                                ->where('u_m.parameters_id', 19);
                        })
 
                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transacion', 'trans.id', 'trans.code', 'trans.path', 'users.name', 'trans.created_at', 'tr.value as valor', 'art.user_id', 'u_p.value as estudante', 'u_m.value as matricula'])

                        ->where('tr.data_from', "=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->distinct('trans.path')
                        ->get();



                    // Evitando recibos duplicados

                    // $recibo = $recibo->unique('code');




                    $recibo_bancos = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])

                        ->where('tr.data_from', "!=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->whereNull('ban.type_conta_entidade')
                        ->distinct('trans.path')
                        ->get();

                    
                        $est_recibo_bancos = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])

                        ->where('tr.data_from', "=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->whereNull('ban.type_conta_entidade')
                        ->distinct('trans.path')
                        ->get();

                        
                    $entidadeCreditoAjute = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->join('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transation', 'tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])

                        ->where('tr.data_from', "!=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->where('ban.type_conta_entidade', '=', 'creditoAjuste')
                        ->distinct('trans.path')
                        ->get();

                        foreach ($entidadeCreditoAjute as $key => $value) {
                            if (!in_array($value->id_transation, $vetorCreditoAjuste)) {
                                $vetorCreditoAjuste[] = $value->id_transation;
                            }
                        } 

                    $est_entidadeCreditoAjute = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->join('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transation', 'tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])

                        ->where('tr.data_from', "=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->where('ban.type_conta_entidade', '=', 'creditoAjuste')
                        ->distinct('trans.path')
                        ->get();

                        foreach ($est_entidadeCreditoAjute as $key => $value) {
                            if (!in_array($value->id_transation, $est_vetorCreditoAjuste)) {
                                $est_vetorCreditoAjuste[] = $value->id_transation;
                            }
                        } 

                        $recibos_estornados = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])

                        ->where('tr.data_from', "=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->whereNull('ban.type_conta_entidade')
                        ->distinct('trans.path')
                        ->get();
                    

                    // Evitando recibos duplicados

                    // $recibo_bancos = $recibo_bancos->unique('code');

                    // return $vetorCreditoAjuste;


                    // Criação de uma colecção contendo os dados da consulta na base de dados 
                    // Odenamos as chaves em função do nome do banco e cramos uma mapa para agrupar os valor por

                    $cretidoAjuste = collect($entidadeCreditoAjute)->groupBy('display_name')->map(function ($item, $key) {
                        $soma = $item->sum('valor_banco');
                        $count = count($item);
                        $resulatado = $count . "," . $soma;
                        return $resulatado;
                    });

                    $bancos = collect($recibo_bancos)->groupBy('display_name')->map(function ($item, $key) {
                        $soma = $item->sum('valor_banco');
                        $count = count($item);
                        $resulatado = $count . "," . $soma;
                        return $resulatado;
                    });

                    $recibos_estornados = collect($recibos_estornados)->groupBy('code')->map(function ($item, $key) {
                        $soma = $item->sum('valor_banco');
                        $recibo = explode("/",$item[0]->path)[3];
                        $recibo = str_replace("recibo-", "", $recibo);
                        $recibo = str_replace(".pdf", "", $recibo);
                        
                        $resulatado = [$recibo,$soma];
                        return $resulatado;
                    });


                    $tesoureiros = collect($recibo)
                        ->groupBy('name')
                        ->map(function ($item, $key) {

                            $soma = $item->sum('valor');
                            $count = count($item);
                            $resulatado = $count . "," . $soma;
                            return $resulatado;


                        });

                    //dados da instituição
                    $institution = Institution::latest()->first();
                    $titulo_documento = "FOLHA DE CAIXA [ RESUMO ]";
                    $anoLectivo_documento = "Ano Lectivo :";
                    $documentoGerado_documento = "Documento gerado a";
                    $documentoCode_documento = 1;
                    //instaciando o PDF

                    $pdf = PDF::loadView("Reports::listar_recibo.pdf", compact(
                        'recibo',
                        'bancos',
                        'tesoureiros',
                        'DataInicio',
                        'DataFim',
                        'institution',
                        'titulo_documento',
                        'anoLectivo_documento',
                        'documentoGerado_documento',
                        'documentoCode_documento',
                        'cretidoAjuste',
                        'vetorCreditoAjuste',
                        'recibos_estornados',
                        // Recibos estornados logo abaixo
                        'est_vetorCreditoAjuste',
                        'est_recibo'
                    )
                    );
                    //Configuração
                    $pdf->setOption('margin-top', '1mm');
                    $pdf->setOption('margin-left', '1mm');
                    $pdf->setOption('margin-bottom', '12mm');
                    $pdf->setOption('margin-right', '1mm');
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('debug-javascript', true);
                    $pdf->setOption('javascript-delay', 1000);
                    $pdf->setOption('enable-smart-shrinking', true);
                    $pdf->setOption('no-stop-slow-scripts', true);
                    //  $pdf->setPaper('a4', 'portrait');  
                    $pdf->setPaper('a4', 'landscape');

                    //Nome do documento PDF
                    $pdf_name = "Folha de caixa [ Resumo ]";
                    //Rodapé do PDF
                    $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
                    $pdf->setOption('footer-html', $footer_html);
                    //Retornar o PDF
                    return $pdf->stream($pdf_name . '.pdf');


                }
                break;

            case 'excel':



                $DataInicio = $request->dataInicio;
                $DataFim = $request->dataFim;
                $date_from = Carbon::parse($DataInicio)->startOfDay();
                $date_to = Carbon::parse($DataFim)->endOfDay();
                $vetorCreditoAjuste = [];

                if ($DataInicio > $DataFim) {

                    Toastr::error(__('A data inicial não pode ser maior que a data final.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } elseif ($DataFim > date('Y-m-d') || $DataInicio > date('Y-m-d')) {

                    Toastr::error(__('As datas não podem ser maior que a data de hoje.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');
                } elseif ($DataInicio == "" && $DataFim == "") {

                    Toastr::error(__('Os campos das datas não podem estar vazias.'), __('Atenção!'));
                    return redirect()->route('listarRecibo');

                } else {

                    $recibo = DB::table('transactions as tr')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->leftJoin('user_parameters as u_m', function ($join) {
                            $join->on('art.user_id', '=', 'u_m.users_id')
                                ->where('u_m.parameters_id', 19);
                        })

                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transacion', 'trans.id', 'trans.code', 'trans.path', 'users.name', 'trans.created_at', 'tr.value as valor', 'art.user_id', 'u_p.value as estudante', 'u_m.value as matricula'])

                        ->where('tr.data_from', "!=", "Estorno")
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->distinct('trans.path')
                        ->get();


                    $recibo_bancos = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->where('tr.data_from', "!=", "Estorno")
                        ->whereNull('ban.type_conta_entidade')
                        ->distinct('trans.path')
                        ->get();


                    $entidadeCreditoAjute = DB::table('transactions as tr')
                        ->join('transaction_info as tinfo', 'tr.id', '=', 'tinfo.transaction_id')
                        ->join('banks as ban', 'tinfo.bank_id', '=', 'ban.id')
                        ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                        ->leftJoin('users', 'tr.created_by', 'users.id')
                        ->join('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                        ->join('article_requests as art', function ($join) {
                            $join->on('art.id', '=', 'tar.article_request_id');
                        })
                        ->leftJoin('user_parameters as u_p', function ($join) {
                            $join->on('art.user_id', '=', 'u_p.users_id')
                                ->where('u_p.parameters_id', 1);
                        })
                        ->orderBy('code', 'asc')
                        ->select(['tr.id as id_transation', 'tinfo.value as valor_banco', 'ban.display_name', 'trans.code', 'trans.path'])
                        ->WhereDate('trans.created_at', '>=', $DataInicio)
                        ->WhereDate('trans.created_at', '<=', $DataFim)
                        ->where('ban.type_conta_entidade', '=', 'creditoAjuste')
                        ->where('tr.data_from', "!=", "Estorno")
                        ->distinct('trans.path')
                        ->get();

                    foreach ($entidadeCreditoAjute as $key => $value) {
                        if (!in_array($value->id_transation, $vetorCreditoAjuste)) {
                            $vetorCreditoAjuste[] = $value->id_transation;
                        }
                    }


                    $cretidoAjuste = collect($entidadeCreditoAjute)->groupBy('display_name')->map(function ($item, $key) {
                        $soma = $item->sum('valor_banco');
                        $count = count($item);
                        $resulatado = $count . "," . $soma;
                        return $resulatado;
                    });

                    $bancos = collect($recibo_bancos)->groupBy('display_name')->map(function ($item, $key) {
                        $soma = $item->sum('valor_banco');
                        $count = count($item);
                        $resulatado = $count . "," . $soma;
                        return $resulatado;
                    });


                    $tesoureiros = collect($recibo)
                        ->groupBy('name')
                        ->map(function ($item, $key) {

                            $soma = $item->sum('valor');
                            $count = count($item);
                            $resulatado = $count . "," . $soma;
                            return $resulatado;

                        });




                    //dados da instituição
                    $institution = Institution::latest()->first();
                    $titulo_documento = "FOLHA DE CAIXA [ RESUMO ]";
                    $anoLectivo_documento = "Ano Lectivo :";
                    $documentoGerado_documento = "Documento gerado a";
                    $documentoCode_documento = 1;
                    //instaciando o PDF

                    $data = [
                        'recibo' => $recibo,
                        'bancos' => $bancos,
                        'tesoureiros' => $tesoureiros,
                        'DataInicio' => $DataInicio,
                        'DataFim' => $DataFim,
                        'institution' => $institution,
                        'titulo_documento' => $titulo_documento,
                        'anoLectivo_documento' => $anoLectivo_documento,
                        'documentoGerado_documento' => $documentoGerado_documento,
                        'documentoCode_documento' => $documentoCode_documento,
                        'cretidoAjuste' => $cretidoAjuste,
                        'vetorCreditoAjuste' => $vetorCreditoAjuste
                    ];

                    return Excel::download(new ResumoExport($data), 'FC_Resumo' . now() . '.xlsx');


                }

                break;

            default:

                break;
        }
    }

    public function listaAjax() {
            try {
        
                  $recibo = DB::table('transactions as tr')
                ->join('transaction_receipts as trans', 'tr.id', '=', 'trans.transaction_id')
                ->leftJoin('users', 'tr.created_by', 'users.id')
                ->leftJoin('transaction_article_requests as tar', 'tr.id', '=', 'tar.transaction_id')
                ->join('article_requests as art', function ($join) {
                        $join->on('art.id', '=', 'tar.article_request_id');
                        $join->on('art.id', '=', 'tar.article_request_id');
                 })
                 ->leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('art.user_id', '=', 'u_p.users_id')
                    ->where('u_p.parameters_id', 1);
                 })->leftJoin('user_parameters as u_m', function ($join) {
                    $join->on('art.user_id', '=', 'u_m.users_id')
                    ->where('u_m.parameters_id', 19);
                 })
                 ->where("tr.data_from","!=","Estorno")
                ->select(['trans.id', 'trans.code', 'trans.path', 'users.name as criador','trans.created_at as data','tr.value as valor','art.user_id','u_p.value as estudante','u_m.value as matricula','art.id as id_art'])
                ->distinct('trans.path');
                
                //  return $recibo->get();
                
                return Datatables::of($recibo)
                    ->addColumn('actions', function ($item) {
                        return view('Reports::listar_recibo.datatables.actions')->with('item', $item);
                 })
            
                    ->rawColumns(['actions'])
                    ->addIndexColumn()
                    ->toJson();
                
            } catch (Exception | Throwable $e) {
                logError($e);
                return response()->json($e->getMessage(), 500);
            }
    }

    public function getResults(Request $request)
    {
        if ($request->ajax()) {
            $queryBuilderJSON = $request->json()->all();
            // $contaRequest =  count($request['rules']) - 1;
            //return $contaRequest;
            //return $request;
            $convertJSON = json_encode($queryBuilderJSON, true);


            $sub = DB::table('users as users')
                ->join('users as u1', 'u1.id', '=', 'users.created_by')
                ->join('user_parameters', 'user_parameters.users_id', '=', 'users.id')
                ->join('parameters', 'user_parameters.parameters_id', '=', 'parameters.id')
                ->join('parameter_options', 'user_parameters.value', '=', 'parameter_options.id')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('parameter_groups', 'parameter_groups.id', '=', 'user_parameters.parameter_group_id')
                ->leftjoin('user_candidate', 'users.id', '=', 'user_id')

                ->leftjoin('user_classes', 'user_classes.user_id', '=', 'users.id')
                ->leftjoin('classes', 'user_classes.class_id', '=', 'classes.id')


                ->leftJoin('user_courses', 'user_courses.users_id', '=', 'users.id')

                ->leftjoin('user_disciplines', 'users.id', '=', 'user_disciplines.users_id')
                //->leftjoin('disciplines', 'user_disciplines.disciplines_id', '=', 'disciplines.id' )

                /*->leftjoin('disciplines as discipline_code', function ($join) {
                  $join->on('discipline_code.id', '=', 'user_disciplines.discipline_id')
                  ->distinct();
                })*/

                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'user_courses.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })

                ->leftjoin('matriculations', 'matriculations.user_id', '=', 'users.id')
                ->leftjoin('users as u3', 'matriculations.created_by', '=', 'u3.id')

                //->leftjoin('matriculation_classes', 'matriculation_classes.matriculation_id', '=', 'matriculations.id')
                //->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id')

                ->leftJoin('matriculation_classes', function ($join) {
                    $join->on('matriculations.id', '=', 'matriculation_classes.matriculation_id');
                    //->where('u_p.parameters_id', 1);
                   //->distinct('turma_matriculado');
                })

                //->leftjoin('classes as c2', 'matriculation_classes.class_id', '=', 'c2.id')

                ->leftJoin('classes as c2', function ($join) {
                    $join->on('matriculation_classes.class_id', '=', 'c2.id');
                    //->where('u_p.parameters_id', 1);
                   //->distinct('matriculation_classes.matriculation_id');
                })

                ->leftjoin('matriculation_disciplines', function ($join) {
                    $join->on('matriculations.id', '=', 'matriculation_disciplines.matriculation_id');
                })
                ->leftjoin('disciplines', function ($join) {
                    $join->on('matriculation_disciplines.discipline_id', '=', 'disciplines.id');
                })

                ->select(
                    'disciplines.code as code',
                    //'matriculation_classes.matriculation_id as matriculation_id',
                    //'c2.display_name',
                    'c2.code as turma_matriculado',
                    'matriculations.created_at as matriculations_created_at',
                    'matriculations.updated_at as matriculations_updated_at',
                    'user_candidate.created_at as user_candidate_created_at',
                    'u3.name as matriculations_created_by',
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.email as user_email',
                    //, 'role_id as roles_id',
                    'roles.name as role_name',
                    'model_has_roles.role_id as roles_id',
                    'user_courses.courses_id as courses_id',
                    'ct.display_name as course_name',
                    'user_candidate.code as code_candidate',
                    'user_classes.class_id as classes',
                    'classes.display_name as turma_display_name',
                    'u1.name as created_by',
                    //'discipline_code  as d_c',
                    'users.created_at as created_at',
                    'matriculations.code as matricula_numb',
                    'matriculations.course_year as ano_curricular'
                );
            //Percorrer o array (for) baseando-se no tamanho das regras e dentro do for avaliar com um if  se aquele parametro existe

            /*for ($i=0; $i <= $contaRequest ; $i++)
            {

                 if ($request['rules'][$i]['field'] == 'value_sexo')
                     {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 2 and users_id = users.id) as value_sexo');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 2 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_sexo');
                     }

                 if ($request['rules'][$i]['field'] == 'value_civil')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 4 and users_id = users.id) as value_civil');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 4 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_civil');
                    }

                    if ($request['rules'][$i]['field'] == 'value_nacionalidade')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 6 and users_id = users.id) as value_nacionalidade');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 6 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_nacionalidade');
                     }

                    //if ($request['rules'][$i]['field'] == 'value_nascimento')
                    //{
                      //  $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 5 and users_id = users.id) as value_nascimento');
                    //}

                    //if ($request['rules'][$i]['field'] == 'value_altura')
                     //{
                       // $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 30 and users_id = users.id) as value_altura');
                     //}

                     if ($request['rules'][$i]['field'] == 'value_sangue')
                     {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 32 and users_id = users.id) as value_sangue');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 32 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_sangue');
                     }

                     //if ($request['rules'][$i]['field'] == 'value_email')
                      //{
                        //    $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 34 and users_id = users.id) as value_email');
                      //}

                     if ($request['rules'][$i]['field'] == 'value_bacharelato')
                      {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 285 and users_id = users.id) as value_bacharelato');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 285 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_bacharelato');
                      }

                      if ($request['rules'][$i]['field'] == 'value_licenciatura')
                      {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 263 and users_id = users.id) as value_licenciatura');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 263 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_licenciatura');
                      }

                      if ($request['rules'][$i]['field'] == 'value_mestrado')
                      {
                         $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 286 and users_id = users.id) as value_mestrado');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 286 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_mestrado');
                      }

                    if ($request['rules'][$i]['field'] == 'value_doutoramento')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 287 and users_id = users.id) as value_doutoramento');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 287 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_doutoramento');
                    }
                    //if($request['rules'][$i]['field'] == 'value_peso')
                    //{
                      //  $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 31 and users_id = users.id) as value_peso');
                    //}
                    if($request['rules'][$i]['field'] == 'value_provincia_origem')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 150 and users_id = users.id and user_parameters.parameter_group_id = 5) as value_provincia_origem');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 150 AND users_id = users.id and parameter_options.id = user_parameters.value and user_parameters.parameter_group_id = 5) as getcode_value_provincia_origem');
                    }
                    if($request['rules'][$i]['field'] == 'value_provincia_actual')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 150 and users_id = users.id and user_parameters.parameter_group_id = 2) as value_provincia_actual');
                        //$sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 150 AND users_id = users.id and parameter_options.id = user_parameters.value and user_parameters.parameter_group_id = 2) as getcode_value_provincia_actual');
                    }
                    if($request['rules'][$i]['field'] == 'value_trabalhador_estudante')
                    {
                        $sub = $sub ->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 62 and users_id = users.id) as value_trabalhador_estudante');
                    }
                    if ($request['rules'][$i]['field'] == 'value_nec_especiais')
                    {
                        $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 289 and users_id = users.id) as value_nec_especiais');
                    }
            }*/

            //$sub = $sub->selectRaw('(select `courses_id` from user_courses where user_courses.courses_id = courses.id and user_courses.users_id = users.id) as courses_id');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 289 and users_id = users.id) as value_nec_especiais');
            $sub = $sub ->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 62 and users_id = users.id) as value_trabalhador_estudante');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 150 and users_id = users.id and user_parameters.parameter_group_id = 2) as value_provincia_actual');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 150 and users_id = users.id and user_parameters.parameter_group_id = 5) as value_provincia_origem');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 287 and users_id = users.id) as value_doutoramento');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 286 and users_id = users.id) as value_mestrado');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 263 and users_id = users.id) as value_licenciatura');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 285 and users_id = users.id) as value_bacharelato');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 32 and users_id = users.id) as value_sangue');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 6 and users_id = users.id) as value_nacionalidade');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 4 and users_id = users.id) as value_civil');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 2 and users_id = users.id) as value_sexo');
            $sub = $sub->selectRaw('(select `name` from users as usr where user_parameters.created_by = usr.id) as updated_by');

            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 19 and user_parameters.users_id = users.id) as value_mecanografico');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 1 and user_parameters.users_id = users.id) as value_nome');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 31 and users_id = users.id) as value_peso');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 34 and users_id = users.id) as value_email');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 5 and users_id = users.id) as value_nascimento');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 30 and users_id = users.id) as value_altura');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 14 and users_id = users.id) as value_bilhete');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 16 and users_id = users.id) as value_validade_bilhete');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 26 and users_id = users.id) as value_passaporte');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 28 and users_id = users.id) as value_validade_passaporte');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 49 and users_id = users.id) as value_nif');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 53 and users_id = users.id) as value_segsocial');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 55 and users_id = users.id) as value_atestmedico');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 57 and users_id = users.id) as value_regcriminal');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 59 and users_id = users.id) as value_ressmilitar');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 51 and users_id = users.id) as value_cartaconducao');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 308 and users_id = users.id) as value_data_termo_trabalho');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 302 and users_id = users.id) as value_iban');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 38 and users_id = users.id) as value_teleffixo');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 36 and users_id = users.id) as value_telefprincipal');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 37 and users_id = users.id) as value_telefalternativo');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 35 and users_id = users.id) as value_emailalternativo');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 39 and users_id = users.id) as value_whatsapp');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 40 and users_id = users.id) as value_skype');
            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 300 and users_id = users.id) as value_facebook');

            $sub = $sub->selectRaw('(select `value` from user_parameters where user_parameters.parameters_id = 25 and users_id = users.id) as value_fotografia');

            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 2 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_sexo');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 4 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_civil');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 6 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_nacionalidade');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 32 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_sangue');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 285 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_bacharelato');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 263 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_licenciatura');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 286 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_mestrado');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 287 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_doutoramento');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 150 AND users_id = users.id and parameter_options.id = user_parameters.value and user_parameters.parameter_group_id = 5) as getcode_value_provincia_origem');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 150 AND users_id = users.id and parameter_options.id = user_parameters.value and user_parameters.parameter_group_id = 2) as getcode_value_provincia_actual');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 62 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_estudantetrabalhador');
            $sub = $sub->selectRaw('(SELECT `code`  from parameter_options JOIN user_parameters ON `user_parameters`.`value` = `parameter_options`.`id` WHERE user_parameters.parameters_id = 289 AND users_id = users.id and parameter_options.id = user_parameters.value ) as getcode_necespeciais');


            $sub = $sub->whereRaw('parameters.has_options = 1')
                ->whereRaw('model_has_roles.model_type LIKE "%User" ')
                ->whereRaw('parameter_options.id', 'user_parameters.value')
                ->distinct();

            $table = DB::table(DB::raw("({$sub->toSql()}) as sub"));
            //->groupBy('users_id');

            $qbp = new QueryBuilderParser(
                array(
                    'user_id', 'user_name', 'value_sexo',
                    'value_civil', 'value_nacionalidade',
                    'value_nascimento', 'value_provincia_origem', 'getcode_value_provincia_origem',
                    'value_altura', 'value_bilhete', 'user_email',
                    'value_email', 'value_nome', 'roles_id',
                    'getcode', 'getsexcode', 'role_name',
                    'value_licenciatura','value_sangue',
                    'value_bacharelato', 'value_mestrado', 'value_doutoramento',
                    'value_peso', 'value_validade_bilhete', 'value_passaporte', 'value_validade_passaporte',
                    'value_nif', 'value_segsocial',
                    'value_atestmedico', 'value_regcriminal', 'value_ressmilitar',
                    'value_cartaconducao', 'value_trabalhador_estudante', 'value_nec_especiais',
                    'value_data_termo_trabalho', 'value_iban', 'value_teleffixo', 'value_telefprincipal',
                    'value_telefalternativo', 'value_emailalternativo', 'value_whatsapp',
                    'value_skype', 'value_facebook', 'getcode_basico',
                    'getcode_ensino_medio', 'getcode_tipo_ensino_medio', 'getcode_bacharelato',
                    'getcode_licenciatura', 'getcode_mestrado', 'getcode_doutoramento',
                    'getcode_estudantetrabalhador', 'getcode_necespeciais', 'getcode_value_provincia_actual',
                    'value_provincia_origem', 'value_provincia_actual',
                    'value_mecanografico', 'courses_id','course_name',
                    'classes','turma_display_name', 'updated_by',
                   'created_by','created_at','ano_curricular','value_fotografia', 'matricula_numb'
                   //'c2.display_name'
                   ,'code'
                )
            );

            //$convertJSON = str_replace("display_name", "c2.display_name", $convertJSON);

            $query = $qbp->parse($convertJSON, $table);

            $rows = $query->get();

            //return $rows;
            $view = view("Reports::getReports", compact('rows'))->render();
            return response()->json(['html' => $view]);
        }
    }

    public function reportByUsers()
    {
        //$roles = Role::with(['currentTranslation'])->whereIn('id', [7,23,21,26,45,11,17,19,18,12,13,16,8,25,14,22,27,43,41,42,20,24])->get();

        $roles = Role::with(['currentTranslation'])->whereNotIn('roles.id', [6,15])->get();
        return view("Reports::getUsers", compact('roles'));
    }
    public function getUsers()
    {

        //$roles = [7,23,21,26,45,11,17,19,18,12,13,16,8,25,14,22,27,43,41,42,20,24];
        $users =  User::with(['roles' => function ($q) {
            $q->with([
                    'currentTranslation'
                ]);
        }], ['parameters' => function ($q) {
            $q->whereIn('code', ['nome', 'n_mecanografico']);
        }])
        ->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416])

        /*->whereHas("roles", function ($q) use ($roles) {
                $q->whereIn("id", $roles);
            })*/
            ;
        return DataTables::of($users)
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
                ->addIndexColumn()
                ->make(true);
    }

    public function getUserByRoles($keyword)
    {
        //quando pesquisar por cargo se um utilizador tiver mais de um cargo, exibir so o cargo em questao.
        //with -> roles -> where(5)
        $users =  User::with(['roles' => function ($q) {
            $q->with([
                    'currentTranslation'
                ]);
        }])->whereHas("roles", function ($role) use ($keyword) {
            $role->where('id', $keyword);
        })
        ->whereNotIn('users.id', [4362, 4428, 5178, 57, 56, 4125, 4270, 4240, 4266, 4416]);
        //->get()
        /*->map(function($user){
            return ['name' => $user->name, 'email' => $user->email, 'role' => $user->roles->map(function($role){
                return $role->currentTranslation->display_name;
            })->implode(", ")];
        });*/

        return DataTables::eloquent($users)
                ->addColumn('users', function ($user) {
                    $displayName = $this->formatUserName($user);
                    return $displayName;
                })
                ->addColumn('roles', function ($item) use($keyword){
                    return $item->roles->map(function ($role) use($keyword) {
                        if ($role->id == $keyword) {
                            return $role->currentTranslation->display_name;
                            exit;
                        }
                    })->implode("");
                    //return $item->roles->first()->currentTranslation->display_name;
                })
                ->addIndexColumn()
                ->toJson();
        return $users;
    }

    public function getUserDuplicates()
    {
        return view("Reports::getDuplicates");

        /*$results = User::whereIn('name', function ($query) {
           $query->select('name')->from('users')->groupBy('name')->havingRaw('count(*) > 1');
        })->get();

        return $results;*/

       /* $results = User::leftJoin('user_parameters as u_p', function ($join) {
                    $join->on('users.id', '=', 'u_p.users_id')
                        ->where('u_p.parameters_id', 19);
                    })->whereIn('u_p.value', function ($query) {
                    $query->select('u_p.value')->from('users')->groupBy('u_p.value')->havingRaw('count(*) > 1');
        })->get();*/

        //return $results;



       //$collection = collect([1, 2, 3, 3, 4, 4, 4, 5]);

        //return $users->duplicates();
    }

    public function getUserDuplicatesBy($slug)
    {
        $users = User::get();

        $slug = 'user_id';
        //$collection = $users->duplicates("name");
        /*$grouped = $users->groupBy($slug)->map(function ($row) {

            return $row->count();

           });*/

        /*$results = User::whereIn($slug, function ($query) {
            $query->select('name')->from('users')->groupBy('name')->havingRaw('count(*) > 1');
        })->select('name')->get();*/

        $results = Matriculation::whereIn($slug, function ($query) {
            $query->select('user_id')->from('matriculations')->groupBy('user_id')->havingRaw('count(*) > 1');
        })->select('user_id')->get();


        return response()->json($results);
        //dd($grouped);
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

    

}
