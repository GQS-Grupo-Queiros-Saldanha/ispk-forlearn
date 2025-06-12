<?php

namespace App\Modules\Payments\Controllers;
use Illuminate\Http\Request;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\TransactionInfo;
use App\Modules\Payments\Models\TransactionReceipt;
use App\Modules\Payments\Requests\PaymentRequest;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Request;
use Mail;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use App\Modules\Payments\Models\DisciplineArticle;
use App\Model\Institution;
use App\Modules\GA\Models\LectiveYear;

//Models Instanciado para Usar a API
use App\Modules\Cms\Models\Language;
use App\Modules\Payments\Models\Bank;
use App\Modules\Payments\Models\Payment;
use App\Modules\Payments\Requests\ArticleRequestRequest;
use App\Modules\Payments\Util\ArticlesUtil;
use Brian2694\Toastr\Facades\Toastr;

class TransactionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        try {
            $users = auth()->user()->can('manage-requests-others') ? studentsSelectList() : null;

            $data = compact('users');
            
            return view("Payments::transactions.index")->with($data);
        } catch (Exception | Throwable $e) {
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    public function ajax($userId)
    {
        $userId = auth()->user()->can('manage-payments-others') ? $userId : auth()->user()->id;

        try {
            $model = ArticleRequest::whereUserId($userId)
                ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'article_requests.article_id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->join('users as u0', 'u0.id', '=', 'article_requests.user_id')
                ->join('transaction_article_requests as tar', 'tar.article_request_id', '=', 'article_requests.id')
                ->join('transactions', 'transactions.id', '=', 'tar.transaction_id')
                ->join('users as u1', 'u1.id', '=', 'transactions.created_by')
                ->leftJoin('users as u2', 'u2.id', '=', 'transactions.updated_by')
                ->leftJoin('users as u3', 'u3.id', '=', 'transactions.deleted_by')
                ->leftJoin('transaction_info as ti', 'ti.transaction_id', '=', 'transactions.id')
                ->leftJoin('banks as b', 'b.id', '=', 'ti.bank_id')

                //relacao entre a disciplines_translations e o articles_requests.meta
                //para retornar o nome da disciplina.

                ->leftJoin('disciplines_translations as dt', function ($join) {
                    $join->on('dt.discipline_id', '=', 'article_requests.discipline_id');
                    $join->on('dt.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dt.active', '=', DB::raw(true));
                })

                ->select([
                    'article_requests.article_id as article',
                    'article_requests.year as article_year',
                    'article_requests.month as article_month',
                    'at.display_name as article_name',
                    'u0.name as user',
                    'tar.value as transaction_value',
                    'transactions.*',
                    'ti.fulfilled_at',
                    'ti.reference',
                    'b.display_name as bank',
                    'u1.name as created_by',
                    'u2.name as updated_by',
                    'u3.name as deleted_by',
                    'dt.display_name as discipline_name'
                ]);

            return Datatables::eloquent($model)
                ->addColumn('actions', function ($item) {
                    return view('Payments::transactions.datatables.actions')->with('item', $item);
                })
                ->addColumn('article', function ($item) {
                    $columnValue = $item->article_name;
                    if ($item->article_month) {
                        $month = getLocalizedMonths()[$item->article_month - 1]["display_name"];
                        $columnValue .= " ($month $item->article_year)";
                    }
                    return $columnValue;
                })
                ->filterColumn('article', function ($query, $keyword) {
                    // TODO: how to filter by month name?
                    $query
                        ->where('at.display_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('article_requests.year', 'LIKE', '%' . $keyword . '%');
                })
                ->orderColumn('article', function ($query, $order) {
                    $query
                        ->orderBy('at.display_name', $order)
                        ->orderBy('article_requests.year', $order)
                        ->orderBy('article_requests.month', $order);
                })
                ->editColumn('type', function ($item) {
                    return $item->type === 'debit' ? 'Débito' : 'Crédito';
                })
                ->editColumn('fulfilled_at', function ($item) {
                    return $item->fulfilled_at ? TimeHelper::time_elapsed_string($item->fulfilled_at) : null;
                })
               /* ->editColumn('created_at', function ($item) {
                    return TimeHelper::time_elapsed_string($item->created_at);
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at ? TimeHelper::time_elapsed_string($item->updated_at) : null;
                })
                ->editColumn('deleted_at', function ($item) {
                    return $item->deleted_at ? TimeHelper::time_elapsed_string($item->deleted_at) : null;
                })*/
                ->order(function ($query) {
                    $query->orderBy('id', 'desc');
                })
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
            $user = User::findOrFail($id)->load('courses');
            $userCourses = $user->courses->pluck('id');

            $articles = Article::with([
                'currentTranslation',
                'extra_fees',
                'monthly_charges'
            ])
                ->doesntHave('monthly_charges')
                ->orWhereHas('monthly_charges', function ($q) use ($userCourses) {
                    $q->whereIn('course_id', $userCourses);
                })
                ->get();

            $articles->each(function ($item) {
                $item->{'extraFeesAsText'} = $item->extraFeesAsText();
            });

            return $articles->sortBy('currentTranslation.display_name')->values();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxUserBalance($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $balance = 0;

            $requests = ArticleRequest::whereUserId($user->id)
                ->with('transactions')
                ->get();

            $requests->each(function ($request) use (&$balance) {
                $request->transactions->each(function ($transaction) use (&$balance) {
                    $op = $transaction->type === 'debit' ? -1 : 1;
                    $balance += $transaction->pivot->value * $op;
                });
            });

            return response()->json([
                'balance' => $balance,
                'personal' => $user->credit_balance
            ]);
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    public function ajaxTransactionReceiptFile($id)
    {
        $receipt = TransactionReceipt::where('transaction_id', $id)
            ->first();

        return $receipt->path;
    }

    

    public function referenceExists(\Illuminate\Http\Request $request)
    {
        try{
            $json = [];
            if ($request->get('field') === 'transaction_reference' && $request->has('value')) {
                $parameter = TransactionInfo::
                leftJoin('transactions as trasn',function ($join)
                {
                    $join->on('trasn.id','=','transaction_info.transaction_id');
                    // $join->where('trasn.data_from','=','Estorno');
                })
                ->where('trasn.data_from','!=','Estorno')
               ->where('reference', '=', $request->get('value'));

                if (!empty($request->get('ignored_id'))) {
                    $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
                }
                $json['success'] = !$parameter->exists(); 
            }
            return response()->json($json);

            return response()->json($json);
        } catch (Exception | Throwable $e) {
            // logError($e);
            return response()->json($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    public function referenceExists1(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->get('field') === 'reference_1' && $request->has('value')) {
            $parameter = TransactionInfo::
            leftJoin('transactions as trasn',function ($join)
            {
                $join->on('trasn.id','=','transaction_info.transaction_id');
                // $join->where('trasn.data_from','=','Estorno');
            })
            ->where('trasn.data_from','!=','Estorno')
           ->where('reference', '=', $request->get('value'));
            
            
            // where('reference', '=', $request->get('value'));


            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }
    public function referenceExists2(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->get('field') === 'reference_2' && $request->has('value')) {
            $parameter = TransactionInfo::
            leftJoin('transactions as trasn',function ($join)
            {
                $join->on('trasn.id','=','transaction_info.transaction_id');
                // $join->where('trasn.data_from','=','Estorno');
            })
            ->where('trasn.data_from','!=','Estorno')
           ->where('reference', '=', $request->get('value'));

            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }

    public function referenceExists3(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->get('field') === 'reference_3' && $request->has('value')) {
            $parameter = TransactionInfo::
            leftJoin('transactions as trasn',function ($join)
            {
                $join->on('trasn.id','=','transaction_info.transaction_id');
                // $join->where('trasn.data_from','=','Estorno');
            })
            ->where('trasn.data_from','!=','Estorno')
           ->where('reference', '=', $request->get('value'));

            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }
    public function referenceExists4(\Illuminate\Http\Request $request)
    {
        $json = [];
        if ($request->get('field') === 'reference_4' && $request->has('value')) {
            $parameter = TransactionInfo::
            leftJoin('transactions as trasn',function ($join)
            {
                $join->on('trasn.id','=','transaction_info.transaction_id');
                // $join->where('trasn.data_from','=','Estorno');
            })
            ->where('trasn.data_from','!=','Estorno')
           ->where('reference', '=', $request->get('value'));

            if (!empty($request->get('ignored_id'))) {
                $parameter = $parameter->whereKeyNot($request->get('ignored_id'));
            }

            $json['success'] = !$parameter->exists();
        }
        return response()->json($json);
    }
    
    # Verificar a origem de uma referência já usada

    public function referenceGetOrigem($reference)
    {
        // Pegando os dados completos da referência
        
        
        
         $transacao=DB::table('transaction_info as tinfo')
        ->leftJoin('transaction_receipts as tr','tr.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('transaction_article_requests as tar','tar.transaction_id',"=","tinfo.transaction_id")
        ->leftJoin('banks as bk','bk.id',"=","tinfo.bank_id")
        ->leftJoin('article_requests as ar','ar.id',"=","tar.article_request_id")
        ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
        ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
        ->leftJoin('articles as art','art.id',"=","ar.article_id")
        ->where('tinfo.reference',$reference)
        ->where('up.parameters_id',1)
        ->where('at.active',1)
        ->select([
            "tinfo.reference as referencia",
            "ar.id as article_request_id",
            "tr.path as recibo",
            /*"tinfo.fulfilled_at as dia"
             "bk.display_name as banco",
             "ar.user_id as estudante_id",
             "ar.article_id as article_id",
             "ar.year as ano",
             "ar.month as mes",
             "up.value as estudante_nome",
             "art.code as code_emolumento", 
             */
             "at.display_name as emolumento" 
        ]) 
        ->get();
        
        $usuario = DB::table('users')->select(['name as nome'])->where('id',auth()->user()->id)->get();

        // $json['success'] = $transacao;
        $n_recibo = $transacao[0]->recibo;
        $rt = explode('-',explode('/',$n_recibo )[3]);
        

        $recibo = $rt[1]."-".explode('.',$rt[2])[0];

        $json=[
            'info'=>$transacao[0],
            'nome'=>$usuario[0]->nome,
            'recibo'=>$recibo 
        ];
        
        return response()->json($json);

    }
    # Verificar a origem do saldo em carteira 

    public function saldoGetOrigem($user)
    {
        
         $estudante = DB::table('users as usuario')
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
        ->where('usuario.id',$user)
        ->orderBy('usuario.name','ASC')
        ->get();

        $historico_credito =DB::table('historic_user_credit_balance as hucb')
       ->leftJoin('transaction_info as tinfo','tinfo.transaction_id',"=","hucb.id_transaction")
       ->leftJoin('transaction_receipts as tr','tr.transaction_id',"=","tinfo.transaction_id")
       ->leftJoin('transaction_article_requests as tar','tar.transaction_id',"=","tinfo.transaction_id")
       ->leftJoin('banks as bk','bk.id',"=","tinfo.bank_id")
       ->leftJoin('article_requests as ar','ar.id',"=","tar.article_request_id")
       ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
       ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
       ->leftJoin('articles as art','art.id',"=","ar.article_id")
       ->where('up.parameters_id',1)
       ->where('at.active',1)
       ->select([
            "hucb.*",
        //    "tinfo.reference as referencia",
        //    "ar.id as article_request_id",
            "tr.path as recibo",
        //    "tinfo.fulfilled_at as dia",
        //     "bk.display_name as banco",
        //     "ar.user_id as estudante_id",
        //     "ar.article_id as article_id",
        //     "ar.year as ano",
        //     "ar.month as mes",
        //     "up.value as estudante_nome",
        //     "art.code as code_emolumento", 
            "at.display_name as emolumento"
       ]) 
        ->where('hucb.user_id',$user)
        ->get();

        $historico_credito = collect($historico_credito)->groupBy('recibo')
        ->map(function($item,$key){
            
         
            $n_recibo = $item[0]->recibo;
            $rt = explode('-',explode('/',$n_recibo )[3]);
            $recibo = $rt[1]."-".explode('.',$rt[2])[0];

            $dados = [
              "emolumento"=>"",
              "valor"=>$item[0]->valor,
              "data_from"=>$item[0]->data_from,
              "recibo"=>$key,
              "recibo_n"=>$recibo
            ];
            
            $i = 0;

            foreach ($item as $value) {
                
                $i++;
            }

            if ($i>1) {
                $dados["emolumento"] = "Diversos emolumentos";
            }else{
                $dados["emolumento"]=$item[0]->emolumento;
            }
            return $dados;
            
        })
        ;

        
        $historico_debito =DB::table('historic_user_balance as hub')
        ->leftJoin('transaction_info as tinfo','tinfo.transaction_id',"=","hub.id_transaction")
       ->leftJoin('transaction_receipts as tr','tr.transaction_id',"=","tinfo.transaction_id")
       ->leftJoin('transaction_article_requests as tar','tar.transaction_id',"=","tinfo.transaction_id")
       ->leftJoin('banks as bk','bk.id',"=","tinfo.bank_id")
       ->leftJoin('article_requests as ar','ar.id',"=","tar.article_request_id")
       ->leftJoin('user_parameters as up','up.users_id',"=","ar.user_id")
       ->leftJoin('article_translations as at','at.article_id',"=","ar.article_id")
       ->leftJoin('articles as art','art.id',"=","ar.article_id")
       ->where('up.parameters_id',1)
       ->where('at.active',1)
       ->select([
           "hub.*",
        //    "tinfo.reference as referencia",
        //    "ar.id as article_request_id",
           "tr.path as recibo",
        //    "tinfo.fulfilled_at as dia",
        //     "bk.display_name as banco",
        //     "ar.user_id as estudante_id",
        //     "ar.article_id as article_id",
        //     "ar.year as ano",
        //     "ar.month as mes",
        //     "up.value as estudante_nome",
        //     "art.code as code_emolumento", 
            "at.display_name as emolumento"
       ]) 
        ->where('hub.id_user',$user)
        ->get();

        $historico_debito = collect($historico_debito)->groupBy('recibo')
        ->map(function($item,$key){
            
         
            $n_recibo = $item[0]->recibo;
            $rt = explode('-',explode('/',$n_recibo )[3]);
            $recibo = $rt[1]."-".explode('.',$rt[2])[0];

            $dados = [
              "emolumento"=>"",
              "valor"=>$item[0]->valor_credit,
              "data_from"=>$item[0]->data_from,
              "recibo"=>$key,
              "recibo_n"=>$recibo
            ];
            
            $i = 0;

            foreach ($item as $value) {
                
                $i++;
            }

            if ($i>1) {
                $dados["emolumento"] = "Diversos emolumentos";
            }else{
                $dados["emolumento"]=$item[0]->emolumento;
            }
            return $dados;
            
        })
        ;

        $data = [
            "credito" => $historico_credito,
            "debito" => $historico_debito,
            "estudante" => $estudante[0],
        ];
        // return $data; 
        return view("Payments::transactions.historic_credit")->with($data);

       
    }

    public function pagamentoGetOrigem()
    {
        
        try {

            $date = '2023-02-06';
            
            $recipt1 = DB::table('transaction_receipts as transaction_receipt')
                ->leftJoin('transactions as transaction', 'transaction.id', '=', 'transaction_receipt.transaction_id')
                ->leftJoin('transaction_article_requests as transaction_article_request', 'transaction_article_request.transaction_id', '=', 'transaction_receipt.transaction_id')
                ->leftJoin('article_requests as article_request', 'article_request.id', '=', 'transaction_article_request.article_request_id')
                ->leftJoin('articles as article', 'article.id', '=', 'article_request.article_id')
                ->leftJoin('users as user', 'user.id', '=', 'article_request.user_id')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('user.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('user.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('historic_user_credit_balance as historic_user_credit', function ($join) {
                    $join->on('historic_user_credit.user_id', '=', 'article_request.user_id');
                    $join->on('historic_user_credit.id_transaction', '=', 'transaction.id');
                })

                ->leftJoin('user_parameters as tesoureiro', function ($join) {
                    $join->on('transaction.created_by', '=', 'tesoureiro.users_id')
                        ->where('tesoureiro.parameters_id', 1);
                })
                /*
                ->leftJoin('historic_user_balance as historic_user_balance', function ($join) {
                    $join->on('historic_user_balance.id_user', '=', 'article_request.user_id');
                    $join->on('historic_user_balance.id_transaction', '=', 'transaction.id');
                })
                */

                ->leftJoin('transaction_info', 'transaction_info.transaction_id', '=', 'transaction_receipt.transaction_id')
                ->leftJoin('code_developer as code_dev', 'code_dev.id', '=', 'article.id_code_dev')

                /*
                ->leftJoin('disciplines', 'disciplines.id', '=', 'article_request.discipline_id')
                ->leftJoin('disciplines_translations as dcp', function ($join) {
                    $join->on('dcp.discipline_id', '=', 'disciplines.id');
                    $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('dcp.active', '=', DB::raw(true));
                })
                ->leftJoin('courses_translations as ct', function ($join) {
                    $join->on('ct.courses_id', '=', 'disciplines.courses_id');
                    $join->on('ct.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('ct.active', '=', DB::raw(true));
                })
                */


                ->select([
                    'transaction_receipt.transaction_id as transactions_id',
                    'transaction_receipt.code as code',
                    'transaction_receipt.path as path',
                    'transaction_receipt.created_at as receipt_createdat',
                    'transaction.id as transaction_id',
                    'transaction.type as transaction_type',
                    'transaction.value as transaction_value_pagou',
                    'transaction.created_by as transaction_created_by',
                    'transaction.updated_by as transaction_updated_by',
                    'transaction.created_at as transaction_created_at',
                    'transaction.updated_at as transaction_updated_at',
                    'transaction_article_request.transaction_id as transaction_article_request',
                    'transaction_article_request.article_request_id as article_request_id',
                    'transaction_article_request.value as transaction_article_request_value_total',
                    'article_request.id as article_req_id',
                    'article_request.article_id as article_id',
                    'article_request.user_id as article_user_id',
                    'article_request.year as article_year',
                    'article_request.month as article_month',
                    'article_request.base_value as article_base_value_custou',
                    'article_request.extra_fees_value as article_extra_fees_value_multa',
                    'article_request.status as article_status',
                    //'article.base_value',
                    'transaction_info.bank_id as transaction_info_bank_id',
                    'transaction_info.reference as transaction_info_reference',
                    'tesoureiro.value as tesoureiro_nome',
                    'user.id as user_id',
                    'user.name as user_name',
                    'user.email as user_email',
                    'full_name.value as user_fullName',
                    'up_meca.value as user_matriculation',
                    'historic_user_credit.user_id as historic_user_credit_user_id',
                    'historic_user_credit.id_transaction as historic_user_credit_transaction_id',
                    'historic_user_credit.valor as historic_user_credit_value',
                    'code_dev.code as code_emolumento1',
                    /*
                    'historic_user_balance.id_user as historic_user_balance_user_id',
                    'historic_user_balance.id_transaction as historic_user_balance_id_transaction',
                    'historic_user_balance.valor_credit as historic_user_balance_valor_credit'
                    */ 
                    //'article_request.id as article_req_id',
                    //'dcp.display_name as discipline_name',
                    //'disciplines.code as codigo_disciplina',
                    'article_request.discipline_id as discipline_id',
                    //'ct.display_name as course_name',
                    //'dcp.abbreviation as abbreviation',
                    //'dcp.display_name as discipline_name',
                    //'disciplines.code as codigo_disciplina',
                ])
                ->whereDate('transaction_receipt.created_at', $date)                
                //->where('transaction_receipt.transaction_id', '=', 173505)  
                ->whereNull('article_request.deleted_at')
                ->whereNull('article_request.deleted_by')                
                ->where('article_request.discipline_id','!=',null)
                //->groupBy('transaction_article_request.article_request_id')            
                //->distinct()
                ->orderBy('transaction.id', 'ASC')
                //->whereNull('historic_user_credit.user_id')
            ->get();

            $recipt = Article::with(['currentTranslation'])
                ->leftJoin('article_requests as article_request', 'article_request.article_id', '=', 'articles.id')
                ->leftJoin('transaction_article_requests as transaction_article_request', 'transaction_article_request.article_request_id', '=', 'article_request.id')

                ->leftJoin('transaction_receipts as transaction_receipt', 'transaction_receipt.transaction_id', '=', 'transaction_article_request.transaction_id')

                ->leftJoin('transactions as transaction', 'transaction.id', '=', 'transaction_receipt.transaction_id')
                
                ->leftJoin('users as user', 'user.id', '=', 'article_request.user_id')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('user.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('user.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('historic_user_credit_balance as historic_user_credit', function ($join) {
                    $join->on('historic_user_credit.user_id', '=', 'article_request.user_id');
                    $join->on('historic_user_credit.id_transaction', '=', 'transaction.id');
                })

                ->leftJoin('user_parameters as tesoureiro', function ($join) {
                    $join->on('transaction.created_by', '=', 'tesoureiro.users_id')
                        ->where('tesoureiro.parameters_id', 1);
                })                

                ->leftJoin('transaction_info', 'transaction_info.transaction_id', '=', 'transaction_receipt.transaction_id')                

                ->select([
                    
                    'transaction_receipt.transaction_id as transactions_id',
                    'transaction_receipt.code as code_recib',
                    'transaction_receipt.path as path',
                    'transaction_receipt.created_at as receipt_createdat',
                    'transaction.id as transaction_id',
                    'transaction.type as transaction_type',
                    'transaction.value as transaction_value_pagou',
                    'transaction.created_by as transaction_created_by',
                    'transaction.updated_by as transaction_updated_by',
                    'transaction.created_at as transaction_created_at',
                    'transaction.updated_at as transaction_updated_at',
                    'transaction_article_request.transaction_id as transaction_article_request',
                    'transaction_article_request.article_request_id as article_request_id',
                    'transaction_article_request.value as transaction_article_request_value_total',
                    'article_request.id as article_req_id',
                    'article_request.article_id as article_id',
                    'article_request.user_id as article_user_id',
                    'article_request.year as article_year',
                    'article_request.month as article_month',
                    'article_request.base_value as article_base_value_custou',
                    'article_request.extra_fees_value as article_extra_fees_value_multa',
                    'article_request.status as article_status',
                    
                    'articles.*',
                    
                    'transaction_info.bank_id as transaction_info_bank_id',
                    'transaction_info.reference as transaction_info_reference',
                    'tesoureiro.value as tesoureiro_nome',
                    'user.id as user_id',
                    'user.name as user_name',
                    'user.email as user_email',
                    'full_name.value as user_fullName',
                    'up_meca.value as user_matriculation',
                    'historic_user_credit.user_id as historic_user_credit_user_id',
                    'historic_user_credit.id_transaction as historic_user_credit_transaction_id',
                    'historic_user_credit.valor as historic_user_credit_value',
                    'article_request.discipline_id as discipline_id',                  
                ])
                ->whereDate('transaction_receipt.created_at', $date)    
                ->whereNull('article_request.deleted_at')
                ->whereNull('article_request.deleted_by')         
                //->groupBy('transaction_article_request.article_request_id')            
                //->distinct()
                ->orderBy('transaction.id', 'ASC')
                //->whereNull('historic_user_credit.user_id')
            ->get();
            

            /*
            $data = [
                'recipt' => $recipt                
            ];
            */

            return view("Payments::transactions.historic_payment_day", compact('recipt'));

        } catch (Exception $e) {    // metodo merger em que estou a trabalhar
            dd($e);
            //logError($e);
            Log::error($e);
            //return response()->json($e);
        }
        
    }

    public function getPagamentoDayAjax($date)
    {        

        try {
            
            $recipt = Article::with(['currentTranslation'])
                ->leftJoin('article_requests as article_request', 'article_request.article_id', '=', 'articles.id')
                ->leftJoin('transaction_article_requests as transaction_article_request', 'transaction_article_request.article_request_id', '=', 'article_request.id')

                ->leftJoin('transaction_receipts as transaction_receipt', 'transaction_receipt.transaction_id', '=', 'transaction_article_request.transaction_id')

                ->leftJoin('transactions as transaction', 'transaction.id', '=', 'transaction_receipt.transaction_id')
                
                ->leftJoin('users as user', 'user.id', '=', 'article_request.user_id')
                ->leftJoin('user_parameters as full_name', function ($join) {
                    $join->on('user.id', '=', 'full_name.users_id')
                        ->where('full_name.parameters_id', 1);
                })
                ->leftJoin('user_parameters as up_meca', function ($join) {
                    $join->on('user.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })

                ->leftJoin('historic_user_credit_balance as historic_user_credit', function ($join) {
                    $join->on('historic_user_credit.user_id', '=', 'article_request.user_id');
                    $join->on('historic_user_credit.id_transaction', '=', 'transaction.id');
                })

                ->leftJoin('user_parameters as tesoureiro', function ($join) {
                    $join->on('transaction.created_by', '=', 'tesoureiro.users_id')
                        ->where('tesoureiro.parameters_id', 1);
                })                

                ->leftJoin('transaction_info', 'transaction_info.transaction_id', '=', 'transaction_receipt.transaction_id')                

                ->select([
                    
                    'transaction_receipt.transaction_id as transactions_id',
                    'transaction_receipt.code as code_recib',
                    'transaction_receipt.path as path',
                    'transaction_receipt.created_at as receipt_createdat',
                    'transaction.id as transaction_id',
                    'transaction.type as transaction_type',
                    'transaction.value as transaction_value_pagou',
                    'transaction.created_by as transaction_created_by',
                    'transaction.updated_by as transaction_updated_by',
                    'transaction.created_at as transaction_created_at',
                    'transaction.updated_at as transaction_updated_at',
                    'transaction_article_request.transaction_id as transaction_article_request',
                    'transaction_article_request.article_request_id as article_request_id',
                    'transaction_article_request.value as transaction_article_request_value_total',
                    'article_request.id as article_req_id',
                    'article_request.article_id as article_id',
                    'article_request.user_id as article_user_id',
                    'article_request.year as article_year',
                    'article_request.month as article_month',
                    'article_request.base_value as article_base_value_custou',
                    'article_request.extra_fees_value as article_extra_fees_value_multa',
                    'article_request.status as article_status',
                    
                    'articles.*',
                    
                    'transaction_info.bank_id as transaction_info_bank_id',
                    'transaction_info.reference as transaction_info_reference',
                    'tesoureiro.value as tesoureiro_nome',
                    'user.id as user_id',
                    'user.name as user_name',
                    'user.email as user_email',
                    'full_name.value as user_fullName',
                    'up_meca.value as user_matriculation',
                    'historic_user_credit.user_id as historic_user_credit_user_id',
                    'historic_user_credit.id_transaction as historic_user_credit_transaction_id',
                    'historic_user_credit.valor as historic_user_credit_value',
                    'article_request.discipline_id as discipline_id',                  
                ])
                ->whereDate('transaction_receipt.created_at', $date)    
                ->whereNull('article_request.deleted_at')
                ->whereNull('article_request.deleted_by')         
                //->groupBy('transaction_article_request.article_request_id')            
                //->distinct()
                ->orderBy('transaction.id', 'ASC')
                //->whereNull('historic_user_credit.user_id')
            ->get();
                
                        
            
            $data = [
                'recipt' => $recipt,                
            ];
            

            return response()->json(array('data'=>$recipt));

        } catch (Exception $e) {    // metodo merger em que estou a trabalhar
            dd($e);
            //logError($e);
            Log::error($e);
            //return response()->json($e);
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentRequest $request
     * @return void
     */
    public function store(PaymentRequest $request)
    {
        //
    }

    public function fetch($id, $action)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function emailTransaction($transactionId)
    {
        $receipt = TransactionReceipt::where('transaction_id', $transactionId)
            ->first();

        $transaction = Transaction::where('id', $transactionId)
            ->with(['article_request' => function ($q) {
                $q->with('user');
            }])
            ->first();

        if ($receipt && $transaction) {
            try {
                $user = $transaction->article_request->first()->user;

                Mail::to($user->email)
                    ->send(new \App\Mail\TransactionReceipt($receipt, $user));
                return response()->json(['sent' => true]);
            } catch (\Exception $e) {
                return response()->json(['sent' => true, 'error' => $e->getMessage()]);
            }
        }

        return response()->json(['sent' => false]);
    }

    public function Rotina_transation($id){

        $Consulta=DB::select("SELECT trans.id, trans.type,trans.value as valor,trans.notes, art.article_id ,u.name, trs.transaction_id ,art.base_value,trs.value FROM article_requests as art JOIN transaction_article_requests as trs Join users u JOIN transactions as trans on trans.id=trs.transaction_id and u.id=art.user_id and art.id=trs.article_request_id where trans.type!='payment' and art.month and trans.notes='Débito inicial do valor base' and art.article_id=$id",[1]);


        return  $collect=collect($Consulta) 
        ->map(function($item){
            $currentData = Carbon::now();
    
            DB::transaction(function  () use($item,$currentData) { 
        
                $transacao=DB::table('transactions  as ART')
                ->where('ART.id',$item->id)
                ->whereNotNull('ART.type')
                ->update(['value' =>$item->base_value ,'updated_at'=> $currentData]);
            });
            return $item->id." Actualizado com sucesso";
            
        });


    }
    /*Zona API Whatsapp*/

    public function getContacorrentWhatsapp()
    {
        $userId = 616;
        $anoLectivo = 9;

       
        $articleRequest = new ArticleRequestController();
        $isApi = true;

        $htmlContaCorrente = $articleRequest->transactionsBy($userId, $anoLectivo, $isApi);
       
        $data = [
            'id_userContaCorrente' => $userId,
            'htmlContaCorrente' => $htmlContaCorrente,
            'ano_lectivo_estudante' => $anoLectivo  
        ];
        
        // Use a classe Request já importada (sem "\Illuminate\Http\")
       
        return $this->transactionPDF($data);
    }






    public function transactionPDF(\Illuminate\Http\Request $request, $api = null)
    {
        try{
        //    return $request;
            $userId=$request->id_userContaCorrente;
            $html=$request->htmlContaCorrente;
            $institution = Institution::latest()->first(); //Esta linha obtém o último registo criado na tabela institutions e guarda-o na variável $institution.
            $titulo_documento = "Conta corrente";
            $documentoGerado_documento = "Data: ";
            $documentoCode_documento = 9;

            $anoLectivo=0;
            if ($request->ano_lectivo_estudante==null) {
                // $currentData = Carbon::now();
                // $lectiveYear = DB::table('lective_years')
                // ->whereRaw('"'.$currentData.'" between `start_date` and `end_date`')
                // ->first();
                // $anoLectivo=$lectiveYear->id;
            }else{
                $anoLectivo=$request->ano_lectivo_estudante;
            }
            
             $lectiveYears = LectiveYear::with(['currentTranslation'])
            ->whereId($anoLectivo)
            ->get();
            
    
            // return view('Payments::transactions.pdf_contacorrente.contaCorrente', compact('html'));
            $count = 1;
            $meses=[1=>"Janeiro",2=>"Fevereiro",3=>"Março",4=>"Abril",5=>"Maio",6=>"Junho",7=>"Julho",8=>"Agosto",9=>"Setembro",10=>"Outubro",11=>"Novembro",12=>"Dezembro"];
        

            $user_requests = ArticleRequest::leftJoin('transaction_article_requests','transaction_article_requests.article_request_id','=','article_requests.id')
                ->leftJoin('transactions', 'transactions.id','=','transaction_article_requests.transaction_id')
                ->leftJoin('transaction_receipts','transaction_receipts.transaction_id','=','transactions.id')
                ->leftJoin('articles','articles.id','=','article_requests.article_id')
                    ->leftJoin('article_translations as at', function ($join) {
                    $join->on('at.article_id', '=', 'articles.id');
                    $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                    $join->on('at.active', '=', DB::raw(true));
                })
                ->where('article_requests.user_id', $userId)
                ->select([
                    'transactions.created_at as created_at',
                    'transactions.type as type',
                    'transactions.data_from as from',
                    'at.display_name as article_name',
                    'article_requests.id as article_requests_id',
                    'article_requests.month as mes',
                    'article_requests.year as ano',
                    'transaction_receipts.code as receipt_code',
                    'transactions.value as value'
                ])
                ->orderBy('transactions.created_at')
            ->get();
            /*$user_requests = ArticleRequest::whereUserId($userId)->with([
                'user' => function ($q) {
                    $q->with([
                        'parameters' => function ($q) {
                            $q->with([
                                'currentTranslation',
                                'groups'
                            ]);
                        },
                        'courses' => function ($q) {
                            $q->with([
                                'currentTranslation'
                            ]);
                        }
                    ]);
                },
                'transactions' => function ($q) {
                    $q->with([
                        'transaction_receipts',
                        'article_request' => function ($q) {
                            $q->with([
                                'article' => function ($q) {
                                    $q->with([
                                        'currentTranslation',
                                    ]);
                                },

                            ]);
                        }
                    ])->orderBy('created_at', 'ASC');
                }
            ])->orderBy('updated_at')->get();*/

            $getDisciplines = DB::table('disciplines_articles')
                ->join('disciplines_translations', 'disciplines_translations.discipline_id', '=', 'disciplines_articles.discipline_id')
                ->where('disciplines_articles.user_id', $userId)
                ->where('disciplines_translations.active', true)
                ->select('disciplines_translations.display_name', 'disciplines_articles.article_request_id')
                //->groupBy('disciplines_articles.discipline_id')
            ->get();
            //return $getDisciplines;
            $user = User::findOrFail($userId);
            
            $deletedArticlesRequested = DB::table('article_requests')
                    ->leftJoin('article_translations as at', function ($join) {
                        $join->on('at.article_id', '=', 'article_requests.article_id');
                        $join->on('at.language_id', '=', \DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', \DB::raw(true));
                    })
                    ->where('user_id', $userId)
                    ->whereNotNull('article_requests.deleted_at')
                    ->get();

            $balance = 0;

            $requests = ArticleRequest::whereUserId($user->id)
                        ->with('transactions')
                        ->get();

            $requests->each(function ($request) use (&$balance) {
                $request->transactions->each(function ($transaction) use (&$balance) {
                    $op = $transaction->type === 'debit' ? -1 : 1;
                    $balance += $transaction->pivot->value * $op;
                });
            });

            $getUserInfo = User::with(['parameters'])->findOrFail($userId);


            /*return response()->json([
                'balance' => $balance,
                'personal' => $user->credit_balance
            ]);*/



            $parameterNome = $getUserInfo->parameters->where('code', 'nome')->first();
            $personalName = $parameterNome ? $parameterNome->pivot->value : '';

            $parameterMecanografico = $getUserInfo->parameters->where('code', 'n_mecanografico')->first();
            $personalMecanografico = $parameterMecanografico ? $parameterMecanografico->pivot->value : '';

            $path_contaCorrente = storage_path('app/public/contaCorrente/contaCorrente.blade.php');
            file_put_contents($path_contaCorrente,$request->htmlContaCorrente);
            $data = [
                'user_requests' => $user_requests,
                'count' => $count,
                'meses'=>$meses,
                'balance' => $balance,
                'user' => $user,
                'getUserInfo' => $getUserInfo,
                'personal' => $user->credit_balance,
                'personal' => [
                    'name' => $personalName,
                    'n_mecanografico' => $personalMecanografico
                ],
                'getDisciplines' => $getDisciplines,
                'deletedArticlesRequested' => $deletedArticlesRequested,
                'institution' => $institution,
                'titulo_documento' => $titulo_documento,
                'documentoGerado_documento' => $documentoGerado_documento,
                'documentoCode_documento' => $documentoCode_documento,
                'html'=>$html,
                'lectiveYears'=>$lectiveYears
            ];
            //{{$personal['name']}}  #{{$personal['n_mecanografico']}}  ({{$user->email}})


           

            $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();
            $pdf = PDF::loadView('Payments::transactions.pdf_contacorrente.pdf', $data)
                    ->setOption('margin-top', '2mm')
                    ->setOption('margin-left', '2mm')
                    ->setOption('margin-bottom', '13mm')
                    ->setOption('margin-right', '2mm')
                    ->setOption('footer-html', $footer_html)
                        ->setPaper('a4');   
                    // ->setPaper('a4','landscape');
                    // $pdf->setOption('margin-top', '3mm');
                    // $pdf->setOption('margin-left', '3mm');
                    // $pdf->setOption('margin-bottom', '1.5cm');
                    // $pdf->setOption('margin-right', '3mm');
                    // $pdf->setOption('enable-javascript', true);
                    // $pdf->setOption('debug-javascript', true);
                    // $pdf->setOption('javascript-delay', 1000);
                    // $pdf->setOption('enable-smart-shrinking', true);
                    // $pdf->setOption('no-stop-slow-scripts', true);
                    // $pdf->setOption('footer-html', $footer_html);

            if($api != null){
                return $pdf->stream('conta_corrente.pdf');        
            }
            return $pdf->stream('conta_corrente.pdf');
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}
