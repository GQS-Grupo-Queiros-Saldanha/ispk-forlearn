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
use App\Modules\GA\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Auth;

use App\Modules\Payments\proxyPay;

use Log;
use PDF;
use Illuminate\Http\Request as HttpRequest;

class proxyPayController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        try {
            
            /*
                1º consulta se está requisição o já exite, ou seja se estão disponíveis como a referênica e muito mais
                2ª Se ainda não foi criado ou se não exite referencia disponível para este pedido criar nova referencia
            */ 
            // $body=[
            //     "reference_id"=>168098940,
            //     "amount"=>"47460"
            // ];

            // return (new proxyPay())->createPamenty($body);
            
            
            
            $referencia = null;
            $montante = 0;
            $resultadoPagar = 0;
            $gravarReferencia=false;
            $id_referencia=null;    
            $calculoMontante=null;
            $saldoCartiera=0;
            $art_tra = explode("@,", $request->referenciaemolument);
            foreach ($art_tra as $item) {
                $item = str_replace('@',"",$item);
                 $articles_transations = explode(",", $item);
                // $articles_transations[0] == Articles
                // $articles_transations[1] == Transation

                $getExiste = $this->getTransaction_articleCalculoMontante($articles_transations[0],$articles_transations[1]);
                if($getExiste['getReferencia_article']->isEmpty()){
                    $calculoMontante = $getExiste['getTransactionEmolument'];
                    $resultadoPagar = $calculoMontante->base_value - $calculoMontante->value;
                    $montante += $resultadoPagar == 0 ? $calculoMontante->base_value  : $calculoMontante->value;
                    $saldoCartiera=$getExiste['getTransactionEmolument']->credit_balance;
                }  
            }
           
            $montante=$montante - $saldoCartiera;
            $montante= $montante<0 ? $montante*(-1) : $montante;
            

            foreach ($art_tra as  $value) {
                $value = str_replace('@',"",$value);
                $articlesTransations = explode(",", $value);
                $getExiste = $this->getExicteReferenciaMulticaixa($articles_transations[0],$articles_transations[1]);

                if ($getExiste['getReferencia_article']->isEmpty()  && $gravarReferencia==false) {
                    
                    
                    $validate = Carbon::now()->addDay("1");
                    $body = [
                        "amount" => $montante,
                        "end_datetime" => $validate,
                        "custom_fields" => [
                            "invoice" => "2018/0333"
                        ]
                    ];
                    $referencia = (new proxyPay())->createReferecia($body);
                    $id_referencia = DB::table('referencia_multicaixa')->insertGetId([
                        'referencia' => $referencia,
                        'entidade' => 99568,
                        'montante' => $body['amount'],
                        'data_expira' => $body['end_datetime'],
                        'created_at' => Carbon::Now(),
                        'update_at' => Carbon::Now(),
                        'update_by' => Auth::user()->id,
                        'created_by' => Auth::user()->id
                    ]);
                    $gravarReferencia=true;
                }

                
                if($getExiste['getReferencia_article']->isEmpty() && $gravarReferencia==true){
                    DB::table('referencia_multicaixa_article_requests')->insert([
                        'id_referencia_multicaixa' => $id_referencia,
                        'id_article_requests' => $articlesTransations[0],
                        'id_transaction' => $articlesTransations[1],
                    ]);
                }
            }
           
            // return (new proxyPay())->getAllReferencia();
            
            $getTableReferencia = $this->getTableReferencia_article($art_tra);
            
            
            $data = [
                'getTableReferencia' => $getTableReferencia
            ];


          
            
            

            return view("Payments::referenciaMulticaixa.index")->with($data);

           
            
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
    private function  getTransaction_articleCalculoMontante($article,$transation)
    {   
        $getTransactionEmolument=[];
        $getReferencia_article = DB::table('referencia_multicaixa as referenciaMult')
            ->join('referencia_multicaixa_article_requests  as referenciaMultArticle', function ($join) {
                $join->on('referenciaMultArticle.id_referencia_multicaixa', '=', 'referenciaMult.id');
            })
            ->join('article_requests as articleRequest', function ($join) {
                $join->on('articleRequest.id', '=', 'referenciaMultArticle.id_article_requests');
            })
            ->join('users as user', 'user.id', '=', 'articleRequest.user_id')
            ->join('user_parameters as user_paramtFoto', function ($join) {
                $join->on('user_paramtFoto.users_id', '=', 'user.id')
                    ->where('user_paramtFoto.parameters_id', 25);
            })
            ->join('user_parameters as full_name', function ($join) {
                $join->on('full_name.users_id', '=', 'user.id')
                    ->where('full_name.parameters_id', 1);
            })
            ->join('user_parameters as matricula', function ($join) {
                $join->on('matricula.users_id', '=', 'user.id')
                    ->where('matricula.parameters_id', 19);
            })
            ->select([
                'referenciaMult.referencia',
                'referenciaMult.montante',
                'referenciaMult.data_expira',
                'user.credit_balance',
                'full_name.value as nome',
                'matricula.value as matricula',
                'user_paramtFoto.value as foto'
            ])
            ->whereNull('articleRequest.deleted_by')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.status_referencia')
            ->where('referenciaMultArticle.id_article_requests', '=', $article)
            ->where('referenciaMultArticle.id_transaction', '=', $transation)
        ->get();
        if($getReferencia_article->isEmpty()){
            $getTransactionEmolument = DB::table('transaction_article_requests as transa_articlerequet')
                ->join('article_requests as articleRequest', function ($join) {
                    $join->on('articleRequest.id', '=', 'transa_articlerequet.article_request_id');
                })
                ->join('transactions as transaction', 'transaction.id', '=', 'transa_articlerequet.transaction_id')
                ->join('users as user', 'user.id', '=', 'articleRequest.user_id')
                ->whereNull('articleRequest.deleted_by')
                ->where('transa_articlerequet.article_request_id', '=', $article)
                ->where('transa_articlerequet.transaction_id', '=', $transation)
                ->select([
                    'articleRequest.base_value as base_value',
                    'transa_articlerequet.value as value',
                    'transaction.value as transaction_value',
                    'user.credit_balance'
                ])
            ->first();
        }

        $data = [
            'getTransactionEmolument' => $getTransactionEmolument,
            'getReferencia_article' => $getReferencia_article
        ];
        return  $data;
    }
    private function  getExicteReferenciaMulticaixa($article,$transation)
    {
        $getReferencia_article = DB::table('referencia_multicaixa as referenciaMult')
            ->join('referencia_multicaixa_article_requests  as referenciaMultArticle', function ($join) {
                $join->on('referenciaMultArticle.id_referencia_multicaixa', '=', 'referenciaMult.id');
            })
            ->join('article_requests as articleRequest', function ($join) {
                $join->on('articleRequest.id', '=', 'referenciaMultArticle.id_article_requests');
            })
            ->join('users as user', 'user.id', '=', 'articleRequest.user_id')
            ->join('user_parameters as user_paramtFoto', function ($join) {
                $join->on('user_paramtFoto.users_id', '=', 'user.id')
                    ->where('user_paramtFoto.parameters_id', 25);
            })
            ->join('user_parameters as full_name', function ($join) {
                $join->on('full_name.users_id', '=', 'user.id')
                    ->where('full_name.parameters_id', 1);
            })
            ->join('user_parameters as matricula', function ($join) {
                $join->on('matricula.users_id', '=', 'user.id')
                    ->where('matricula.parameters_id', 19);
            })
            ->select([
                'referenciaMult.referencia',
                'referenciaMult.montante',
                'referenciaMult.data_expira',
                'user.credit_balance',
                'full_name.value as nome',
                'matricula.value as matricula',
                'user_paramtFoto.value as foto'
            ])
            ->whereNull('articleRequest.deleted_by')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.status_referencia')
            ->where('referenciaMultArticle.id_article_requests', '=', $article)
            ->where('referenciaMultArticle.id_transaction', '=', $transation)
        ->get();

       
        $data = ['getReferencia_article' => $getReferencia_article];
        return  $data;
    }
    private function getTableReferencia_article($art_tra){
      
        $article_id=[];
        foreach ($art_tra as $item) {
            $item = str_replace('@',"",$item);
            $articles_transations = explode(",", $item);
            $article_id[]=$articles_transations[0];
        }  
   
             $getAll_article=DB::table('referencia_multicaixa as referenciaMult')
                    ->join('referencia_multicaixa_article_requests  as referenciaMultArticle', function ($join) {
                        $join->on('referenciaMultArticle.id_referencia_multicaixa', '=', 'referenciaMult.id');
                    })
                    ->join('article_requests as articleRequest', function ($join) {
                        $join->on('articleRequest.id', '=', 'referenciaMultArticle.id_article_requests');
                    })
                    ->select([
                        'referenciaMult.id as id_referencia'
                    ])
                    ->whereNull('articleRequest.deleted_by')
                    ->whereNull('referenciaMult.deleted_at')
                    ->whereNull('referenciaMult.deleted_at')
                    ->whereNull('referenciaMult.status_referencia')
                    ->where('referenciaMultArticle.id_article_requests', '=', $article_id)
                    ->whereIn('referenciaMultArticle.id_article_requests',$article_id)
                    ->orderBy('referenciaMult.created_at','DESC')
            ->get();

        return $getReferencia_article = DB::table('referencia_multicaixa as referenciaMult')
            ->join('referencia_multicaixa_article_requests  as referenciaMultArticle', function ($join) {
                $join->on('referenciaMultArticle.id_referencia_multicaixa', '=', 'referenciaMult.id');
            })
            ->join('article_requests as articleRequest', function ($join) {
                $join->on('articleRequest.id', '=', 'referenciaMultArticle.id_article_requests');
            })
            ->join('users as user', 'user.id', '=', 'articleRequest.user_id')
            ->join('user_parameters as user_paramtFoto', function ($join) {
                $join->on('user_paramtFoto.users_id', '=', 'user.id')
                    ->where('user_paramtFoto.parameters_id', 25);
            })
            ->join('user_parameters as full_name', function ($join) {
                $join->on('full_name.users_id', '=', 'user.id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftjoin('user_parameters as matricula', function ($join) {
                $join->on('matricula.users_id', '=', 'user.id')
                    ->where('matricula.parameters_id', 19);
            })
            ->leftJoin('article_translations as article_translat', function ($join) {
                $join->on('articleRequest.article_id', '=', 'article_translat.article_id');
                $join->on('article_translat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('article_translat.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines', 'disciplines.id', '=', 'articleRequest.discipline_id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->select([
                'dcp.display_name as nome_disciplina',
                'disciplines.code as codigo_disciplina',
                'referenciaMult.id as id_referencia',
                'referenciaMult.entidade',
                'referenciaMult.referencia',
                'referenciaMult.montante',
                'referenciaMult.data_expira',
                'user.credit_balance',
                'full_name.value as nome',
                'matricula.value as matricula',
                'user_paramtFoto.value as foto',
                'article_translat.display_name as nomeEmolumento',
                'articleRequest.year',
                'articleRequest.month',
                'articleRequest.status',
                'articleRequest.base_value'
            ])
            ->whereNull('articleRequest.deleted_by')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.status_referencia')
            ->where('referenciaMult.id', '=', $getAll_article[0]->id_referencia) 
        ->get();

    }

    public function noticationReferrencia($id_referencia)
    {
        try{
            $refereDisponivel=null;
             $getReferencia_article = DB::table('referencia_multicaixa as referenciaMult')
            ->join('referencia_multicaixa_article_requests  as referenciaMultArticle', function ($join) {
                $join->on('referenciaMultArticle.id_referencia_multicaixa', '=', 'referenciaMult.id');
            })
            ->join('article_requests as articleRequest', function ($join) {
                $join->on('articleRequest.id', '=', 'referenciaMultArticle.id_article_requests');
            })
            ->join('users as user', 'user.id', '=', 'articleRequest.user_id')
            ->join('user_parameters as user_paramtFoto', function ($join) {
                $join->on('user_paramtFoto.users_id', '=', 'user.id')
                    ->where('user_paramtFoto.parameters_id', 25);
            })
            ->join('user_parameters as full_name', function ($join) {
                $join->on('full_name.users_id', '=', 'user.id')
                    ->where('full_name.parameters_id', 1);
            })
            ->join('user_parameters as matricula', function ($join) {
                $join->on('matricula.users_id', '=', 'user.id')
                    ->where('matricula.parameters_id', 19);
            })
            ->leftJoin('article_translations as article_translat', function ($join) {
                $join->on('articleRequest.article_id', '=', 'article_translat.article_id');
                $join->on('article_translat.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('article_translat.active', '=', DB::raw(true));
            })
            ->leftJoin('disciplines', 'disciplines.id', '=', 'articleRequest.discipline_id')
            ->leftJoin('disciplines_translations as dcp', function ($join) {
                $join->on('dcp.discipline_id', '=', 'disciplines.id');
                $join->on('dcp.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                $join->on('dcp.active', '=', DB::raw(true));
            })
            ->select([
                'dcp.display_name as nome_disciplina',
                'disciplines.code as codigo_disciplina',
                'referenciaMult.id as id_referencia',
                'referenciaMult.status_referencia as status_referencia',
                'referenciaMult.entidade',
                'referenciaMult.referencia',
                'referenciaMult.montante',
                'referenciaMult.data_expira',
                'user.credit_balance',
                'full_name.value as nome',
                'matricula.value as matricula',
                'user_paramtFoto.value as foto',
                'article_translat.display_name as nomeEmolumento',
                'articleRequest.year',
                'articleRequest.month',
                'articleRequest.status',
                'articleRequest.base_value',
                'user.id as user_id'
            ])
            ->whereNull('articleRequest.deleted_by')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.deleted_at')
            ->whereNull('referenciaMult.status_referencia')
            ->where('referenciaMult.id', '=', $id_referencia)
        ->first();
       
            $referencia=rtrim(chunk_split($getReferencia_article->referencia, 3, '-'), '-');
                if ($getReferencia_article->status_referencia==null) {
                    $refereDisponivel="disponivel";
                }else{
                    $refereDisponivel="indisponível";
                }
            $body='<div class="alert alert-success rounded pr-0  pl-0" role="alert">
                        <p style="border-bottom: #c7dbd0 0.1px solid; font-size:0.9pc" class="mb-0  mb-4 pr-2 pl-2">Caro estudante tem disponivel a referência multicaixa</p>
                        <h1 style="font-size:1.3pc" class="m-0 p-0 text-dark pr-2 pl-2">REFERÊNCIA MULTICAIXA</h1>
                        <div  style="background:transparent" class="card rounded">
                            <small class="col-12 pt-2 pb-2 border-bottom">Informações sobre referência</small>
                            <div class="card-body">
                                <h6 class="card-title"> Entidade: <small>'.$getReferencia_article->entidade.'</small></h6>
                                <h5 class="card-title mt-2"> Referência: <small>'.$referencia.'</small></h5>
                                <h5 class="card-title mt-2"> Montante: <small>'.number_format($getReferencia_article->montante,2,",",".") .'</small></h5>
                                <h5  style="font-size:0.8pc" class="card-title mt-4 mb-2"> Expira a: <small style="font-size: 0.9pc" class="card-text">'.$getReferencia_article->data_expira.'</small></h5>  
                            </div>
                        </div>
                    </div>';
                        
            $icon= "fas fa-bank";
            $subjet="[Tesouraria] Referência multicaixa";
            $destinetion[]=$getReferencia_article->user_id;
            // $file=$caminhRecibo;
            notification($icon,$subjet,$body,$destinetion,null,null);
           
            return response()->json(['data'=>'1']);
        } catch (Exception | Throwable $e) {
            return $e;
            logError($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }
}

