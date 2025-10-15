<?php

namespace App\Modules\RH\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Users\Enum\ParameterEnum;
use App\Modules\Users\Enum\RoleEnum;
use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

use Exception;
use DB;

class SaftApiController extends Controller{

    private const PAGINATE_TAM = 100;
    private const BAD_REQUEST = 400;
    private const AUTHORIZATION_REQUEST = 401;

    public function loginApi($token, $key){
        if(!isset($token, $key)) return false;
        return DB::table('api_users')->where('token', $token)->where('keey', $key)->exists();
    }

    public function login(Request $request){
        return $this->loginApi($request->header('token'), $request->header('key'));
    }

    public function findAll(Request $request){
        if(!$this->login($request)) return $this->loginFailed(); 
        try{
            return $this->query()->paginate(static::PAGINATE_TAM);
        }catch(Exception $e){
            return $this->exceptionRequest($e);
        }
    }

    public function findAllUpdate(Request $request, $data_update){
        if(!$this->login($request)) return $this->loginFailed();
        try{
            return $this->query()
                ->where('ar.updated_at','like','%'.$data_update.'%')
                ->paginate(static::PAGINATE_TAM);
        }catch(Exception $e){
            return $this->exceptionRequest($e);
        }
    }

    public function findAllBetween(Request $request, $date_start, $date_end){
        if(!$this->login($request)) return $this->problemDetails('Autenticação', 'Falha no processo de autenticação', static::AUTHORIZATION_REQUEST); 
        if(strtotime($date_start) > strtotime($date_end)) return $this->problemDetails('Conflito entre datas', 'A data de começo é maior que a data de termino'); 
        try{
            return $this->query()
                ->whereBetween('ar.created_at', [$date_start, $date_end])
                ->paginate(static::PAGINATE_TAM);
        }catch(Exception $e){
            return $this->exceptionRequest($e);
        }        
    }



    private function problemDetails($title, $detail = '', $status = 400 , $type = 'about:blank', $instance = ''){
        return response()->json([
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => $instance
        ], $status);
    }

    private function loginFailed(){
        return $this->problemDetails('Autenticação', 'Falha no processo de autenticação', static::AUTHORIZATION_REQUEST); 
    }

    private function exceptionRequest($e){
        return $this->problemDetails('Erro', $e->getMessage(), 500); 
    }

    private function query(){
        return User::query()->whereHas('roles', function ($q) {
            $q->where('id', '=', RoleEnum::STUDENT);
        })
        ->join('user_parameters as full_name', function ($join) {
            $join->on('users.id', '=', 'full_name.users_id')
                 ->where('full_name.parameters_id', ParameterEnum::NOME);
        })
        ->join('user_parameters as up_bi', function ($join) {
            $join->on('users.id', '=', 'up_bi.users_id')
                 ->where('up_bi.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
        })
        ->join('article_requests as ar', function ($join) {
            $join->on('users.id', '=', 'ar.user_id');
            $join->where('ar.status','<>','pending');
            $join->whereNull('ar.deleted_at');
            $join->whereNull('ar.deleted_by');
        })
        ->join('articles as a', function ($join) {
            $join->on('a.id', '=', 'ar.article_id');
            $join->whereNull('a.deleted_at');
            $join->whereNull('a.deleted_by');
        })     
        ->join('article_translations as at', function ($join) {
            $join->on('at.id', '=', 'ar.article_id');
            $join->where('at.language_id', DB::raw(LanguageHelper::getCurrentLanguage()));
            $join->where('at.active', 1);
            $join->whereNull('at.deleted_at');
        })      
        ->where('full_name.value','<>','')
        ->where('up_bi.value','<>','')
        ->whereNull('users.deleted_at')
        ->whereNull('users.deleted_by')
        ->select([
            'users.id as user_id',
            'full_name.value as nome',
            'up_bi.value as bilhete',
            'at.display_name as emolumento',
            'ar.id as operacacao',
            'a.base_value as valor',
            'ar.base_value as pago',
            'ar.status as estado',
            'ar.updated_at as data_hora'
        ]);
    }

    #Manuel guengui
    public function getCompanyData() {
      
        $companyData = DB::table('institutions')->first();
       
        if (!$companyData) {
            return $this->problemDetails('Instituição não encontrada', 'Não foi possível localizar os dados da empresa', static::BAD_REQUEST);
        }
     
        return response()->json([
            'firma' => $companyData->nome,
            'nif' => $companyData->contribuinte,
            'endereco' => $companyData->morada,
            'telefone' => $companyData->telefone_geral,
            'email' => $companyData->email,
            'site' => $companyData->dominio_internet,
        ]);
    }
    



        #Manuel guengui
     public function findStudentsBetween($start_date=null, $end_date=null) {
       
        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->problemDetails('Conflito entre datas', 'A data de começo é maior que a data de término', static::BAD_REQUEST);
        }
        try {
            
            $students = DB::table('users')
            ->whereNull("users.deleted_by")
            ->whereNull("users.deleted_at")
                ->join('model_has_roles as mr', function($join){
                    $join->on('mr.model_id', '=', 'users.id')
                            ->whereIn('mr.role_id', [6, 15]);
                })
                ->join('article_requests as ar', function($join){
                    $join->on('ar.user_id', '=', 'users.id')
                    ->whereNull('ar.deleted_by')
                    ->whereNull('ar.deleted_at');
                })
                ->join('articles as a', 'a.id', '=', 'ar.article_id')
                ->join('transaction_article_requests as tar', function($join){
                    $join->on('tar.article_request_id', '=', 'ar.id');
                })
                ->join('transactions as trans', function($join){
                    $join->on('trans.id', '=', 'tar.transaction_id')
                    ->whereNull('trans.deleted_by')
                    ->whereNull('trans.deleted_at')
                    ->where('trans.type', '!=', 'adjust');
                })
                ->join('transaction_receipts as tr', function($join) use ($start_date, $end_date){
                    $join->on('tr.transaction_id', '=', 'trans.id')
                    ->WhereDate('tr.created_at', '>=', $start_date,)
                    ->WhereDate('tr.created_at', '<=', $end_date);
                })
                ->join('user_parameters as user_p1', function($join){
                    $join->on('user_p1.users_id','users.id')
                    ->where('user_p1.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
                })
                ->join('user_parameters as user_p2', function($join){
                    $join->on('user_p2.users_id','users.id')
                    ->where('user_p2.parameters_id', ParameterEnum::NOME);
                })
                ->leftJoin('user_parameters as up_bairro', function ($join) {
                    $join->on('users.id', '=', 'up_bairro.users_id')
                         ->where('up_bairro.parameters_id', ParameterEnum::BAIRRO);
                })
                ->leftJoin('user_parameters as up_rua', function ($join) {
                    $join->on('users.id', '=', 'up_rua.users_id')
                         ->where('up_rua.parameters_id', ParameterEnum::RUA);
                })
                ->leftJoin('user_parameters as up_porta', function ($join) {
                    $join->on('users.id', '=', 'up_porta.users_id')
                         ->where('up_porta.parameters_id', ParameterEnum::NUMERO_DE_PORTA);
                })
               
                 ->select([
                    
                    'users.id as id',
                    'user_p1.value as bi',
                    'user_p2.value as nome',
                    'up_bairro.value as bairro',
                    'up_rua.value as rua',
                    'up_porta.value as porta'
                ])
                ->distinct()
                ->get();
              
            return response()->json($students);
            
            
        } catch (Exception $e) {
            return $this->exceptionRequest($e);
        }
    }
    



    public function documentBetween($start_date=null, $end_date=null){
        
        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->problemDetails('Conflito entre datas', 'A data de começo é maior que a data de término', static::BAD_REQUEST);
        }

        try {
            $receipt = DB::table("transaction_receipts as tra_receipt")
            ->WhereDate('tra_receipt.created_at', '>=', $start_date)
            ->WhereDate('tra_receipt.created_at', '<=', $end_date)
            ->join("transactions", function($join){
                $join->on("tra_receipt.transaction_id", "transactions.id")
                ->whereNull('transactions.deleted_by')
                ->whereNull('transactions.deleted_at')
                ->where('transactions.type','payment');
            })
            ->join("transaction_article_requests as tras_article", function($join){
                $join->on("tras_article.transaction_id", "transactions.id"); 
            })
            ->join("article_requests", function($join){
                $join->on("tras_article.article_request_id", "article_requests.id")
                ->whereNull('article_requests.deleted_by')
                ->whereNull('article_requests.deleted_at');
            })
            ->join('articles as a', 'a.id', '=', 'article_requests.article_id')
            ->join('users', function($join){
                $join->on('users.id', 'article_requests.user_id')
                ->whereNull("users.deleted_by")
                ->whereNull("users.deleted_at");
            })
            ->select([
                DB::raw("CONCAT(YEAR(tra_receipt.created_at), '/', tra_receipt.code) as documento_numero"),
                'transactions.value as total',
                'tra_receipt.created_at as data',
                'article_requests.user_id as aluno_id'
            ])
            ->distinct('tra_receipt.id')
            ->get();



        //    dd($receipt->where('documento_numero', '24/000265'));
            return response()->json($receipt);


                
            
        } catch (Exception $e) {
            return $this->exceptionRequest($e);
        }



    }


    public function emolumentoBetween($start_date=null, $end_date=null){
        if (strtotime($start_date) > strtotime($end_date)) {
            return $this->problemDetails('Conflito entre datas', 'A data de começo é maior que a data de término', static::BAD_REQUEST);
        }

        try {
            $emulumento = DB::table("transaction_article_requests as tras_article")
           ->join("article_requests as ar","tras_article.article_request_id","=",  "ar.id")
           ->whereNull("ar.deleted_by")
           ->whereNull("ar.deleted_at")
           ->join("transactions as trans", function($join){
            $join->on("tras_article.transaction_id", "trans.id")
            ->whereNull("trans.deleted_by")
            ->whereNull("trans.deleted_at")
                ->where('trans.type', 'payment');
           })
	   ->join("transaction_receipts as tra_receipt", function($join) use ($start_date, $end_date){
                $join->on("tra_receipt.transaction_id","=",  "trans.id")
                ->WhereDate('tra_receipt.created_at', '>=', $start_date)
                ->WhereDate('tra_receipt.created_at', '<=', $end_date);
       })
 	   ->join("article_translations as article_tras", function($join){
                $join->on("ar.article_id", "=", "article_tras.article_id")
                ->where('article_tras.active', 1);
       })
       ->join('users', function($join){
        $join->on('users.id', 'ar.user_id')
        ->whereNull("users.deleted_by")
        ->whereNull("users.deleted_at");
    })
            ->select([
                'article_tras.display_name as descricao',
		'ar.article_id as emolumento_id',
        'tras_article.value as valor',
		 DB::raw("CONCAT(YEAR(tra_receipt.created_at), '/', tra_receipt.code) as documento_numero")
            ])
          
            ->get();

                   

         
            return response()->json($emulumento);
            
            
        } catch (Exception $e) {
            return $this->exceptionRequest($e);
        }

    }
    //Existe uma routa que foi usada para fim de teste (Route::get('RH/api', 'SaftApiController@findStudentsBetween');)




   
}