<?php
use Illuminate\Support\Facades\DB;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use App\Modules\GA\Models\LectiveYear;
use App\Modules\Cms\Models\Language;
use Illuminate\Http\Response;
use App\Helpers\LanguageHelper;
use App\Modules\Users\Models\User;


use Carbon\Carbon;


function currentLectiveYear(){
    $currentData = Carbon::now();
    return LectiveYear::whereRaw('"'.$currentData.'" between `start_date` and `end_date`')->first();
}

/**
 *    Payments Helper
 */

function getLocalizedMonths()
{
    $months = explode('_', trans('Payments::articles.monthly_charge.months'));
    $monthsArray = array_map(function ($month, $idx) {
        return ['id' => ++$idx, 'display_name' => $month];
    }, $months, array_keys($months));
    return collect($monthsArray);
}

function getLocalizedMonthsPropinas()
{
    $months = explode('_', trans('Payments::articles.monthly_charge.months'));
    $monthsArray = array_map(function ($month, $idx) {
        return ['id' => ++$idx, 'display_name' => $month];
    }, $months, array_keys($months));
    return collect($monthsArray)->filter(function ($value, $key) {
        return $key > 1;
    });
}

function getYearList()
{
    $now = \Carbon\Carbon::now();
    $year = $now->subYears(4)->year;
    return collect([
        ['id' => ++$year, 'display_name' => $year],
        ['id' => ++$year, 'display_name' => $year],
        ['id' => ++$year, 'display_name' => $year],
        ['id' => ++$year, 'display_name' => $year],
        ['id' => ++$year, 'display_name' => $year],
    ]);
}




function createAutomaticArticleRequest($userId, $articleId, $year, $month)
{

    try {
        DB::beginTransaction();

        $article = Article::findOrFail($articleId);

        // Create
        $articleRequest = new ArticleRequest([
            'user_id' => $userId,
            'article_id' => $article->id,
            'year' => $year ?: null,
            'month' => $month ?: null,
            'base_value' => $article->base_value
        ]);

        $articleRequest->save();
        // create debit with article base value
        $transaction = Transaction::create([
            'type' => 'debit',
            'value' => $articleRequest->base_value,
            'notes' => 'Débito inicial do valor base'
        ]);

        $transaction->article_request()
            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
        DB::commit();

        return $articleRequest->id;

    } catch (Exception | Throwable $e) {
        logError($e);
        return false;
    }


}




function createAutomaticArticleRequestExame($userId, $articleId, $year, $month,$discipline,$metric = null)
{

    try {
        DB::beginTransaction();

        $article = Article::findOrFail($articleId);
      
        // Create
        $articleRequest = new ArticleRequest([
            'user_id' => $userId,
            'article_id' => $article->id,
            'year' => $year ?: null,
            'month' => $month ?: null,
            'base_value' => $article->base_value,
            'discipline_id' => $discipline?:null,
            'metric_id' => $metric?:null
        ]);

        
        $articleRequest->save();
        // create debit with article base value
        $transaction = Transaction::create([
            'type' => 'debit',
            'value' => $articleRequest->base_value,
            'notes' => 'Débito inicial do valor base'
        ]);
      
        $transaction->article_request()
            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);
        DB::commit();

        return $articleRequest->id;

    } catch (Exception | Throwable $e) {
        DB::rollBack();
        logError($e);
        return $e;
    }


}








function requestStatusList()
{
    return [
        'pending' => "<span class='badge badge-info text-uppercase'>" . __('Payments::requests.status.pending') . '</span>',
        'canceled' => "<span class='badge badge-danger text-uppercase'>" . __('Payments::requests.status.canceled') . '</span>',
        'error' => "<span class='badge badge-danger text-uppercase'>" . __('Payments::requests.status.error') . '</span>',
        'total' => "<span class='badge badge-success text-uppercase'>" . __('Payments::requests.status.total') . '</span>',
        'partial' => "<span class='badge badge-warning text-uppercase'>" . __('Payments::requests.status.partial') . '</span>',
    ];
}






function createAutomaticArticleRequestCandidate($userId, $articleId, $year, $month, $discipline)
{
    try {
        DB::beginTransaction();

        $article = Article::findOrFail($articleId);

        // Create
        $articleRequest = new ArticleRequest([
            'user_id' => $userId,
            'article_id' => $article->id,
            'year' => $year ?: null,
            'month' => $month ?: null,
            'base_value' => $article->base_value,
            'discipline_id' => $discipline
        ]);

        $articleRequest->save();

        // create debit with article base value
        $transaction = Transaction::create([
            'type' => 'debit',
            'value' => $articleRequest->base_value,
            'notes' => 'Débito inicial do valor base'
        ]);

        $transaction->article_request()
            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);

        DB::commit();

        return $articleRequest->id;
    } catch (Exception | Throwable $e) {
        logError($e);
        return false;
    }


}




function createAutomaticArticleRequestFrequencia($userId, $articleId, $year, $month, $discipline)
{
    try {
        DB::beginTransaction();

        $article = Article::findOrFail($articleId);

        // Create
        $articleRequest = new ArticleRequest([
            'user_id' => $userId,
            'article_id' => $article->id,
            'year' => $year ?: null,
            'month' => $month ?: null,
            'base_value' => $article->base_value,
            'discipline_id' => $discipline
        ]);

        $articleRequest->save();

        // create debit with article base value
        $transaction = Transaction::create([
            'type' => 'debit',
            'value' => $articleRequest->base_value,
            'notes' => 'Débito inicial do valor base'
        ]);

        $transaction->article_request()
            ->attach($articleRequest->id, ['value' => $articleRequest->base_value]);

        DB::commit();

        return $articleRequest->id;
    } catch (Exception | Throwable $e) {
        logError($e);
        return false;
    }


}






function getStatusPagamento($id,$matriculation,$email,$inscriFre)
{       try{
    
        DB::beginTransaction();
            $getUser_article=DB::table('article_requests as artR')
                ->join('articles as art', function ($join) {
                        $join->on('artR.article_id', '=', 'art.id');
                }) 
                ->join('article_translations as at', function ($join) {
                        $join->on('art.id', '=', 'at.article_id');
                        $join->on('at.language_id', '=', DB::raw(LanguageHelper::getCurrentLanguage()));
                        $join->on('at.active', '=', DB::raw(true));
                })
                ->join('user_parameters as up_meca', function ($join) {
                    $join->on('artR.user_id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->select([
                    'artR.user_id as user_id',
                    'artR.status as estado_do_mes',
                    'artR.month as month',
                    'at.display_name as display_name',
                    'artR.year as year',
                ])
                ->leftJoin('code_developer as code_dev','code_dev.id','art.id_code_dev')
                ->where('artR.status','!=','total') 
                ->when($inscriFre!=false, function($q)use($inscriFre)
                {
                    if ($inscriFre!=false) {
                        $q->whereIn('code_dev.code', ["propina","in_fre"]); 
                    }else{
                        $q->where('code_dev.code', "propina"); 

                    }
                   
                })
                ->when($matriculation!=false, function($q) use($matriculation)
                {
                    return $q->where('up_meca.value',$matriculation);
                })
                ->when($id!=false, function($q)use($id)
                {
                    return $q->where('artR.user_id',$id);
                })
               
                ->whereNull('artR.deleted_at') 
                ->whereNull('artR.deleted_by')   
            ->get()
            ->map(function($query){
                $query->{'day'}=15;
                return $query;
            });
            
            $model = DB::table('users as usuario')
                ->join('model_has_roles as usuario_cargo', 'usuario.id', '=', 'usuario_cargo.model_id')  
                ->join('roles as cargo', 'usuario_cargo.role_id', '=', 'cargo.id')  
                ->join('role_translations as cargo_traducao', 'cargo_traducao.role_id', '=', 'cargo.id') 
                ->leftJoin('user_parameters as user_namePar',function($join){
                    $join->on('user_namePar.users_id', '=', 'usuario.id')
                    ->where('user_namePar.parameters_id',1);
                }) 
                ->join('user_parameters as up_meca', function ($join) {
                    $join->on('usuario.id', '=', 'up_meca.users_id')
                        ->where('up_meca.parameters_id', 19);
                })
                ->where('cargo_traducao.active',1)
                ->where('cargo_traducao.language_id',1)
                ->where('usuario_cargo.model_type',"App\Modules\Users\Models\User")
                ->where("cargo_traducao.role_id",6)
                ->when($matriculation!=false, function($q)use($matriculation)
                {
                   return $q->where('up_meca.value',$matriculation);
                })
                ->when($id!=false, function($q)use($id)
                {
                   return $q->where('usuario.id',$id);
                })
                ->select([
                    'user_namePar.value as full_nome_usuario',
                    'usuario.email as email',
                    'usuario.name as first_last_name_user',
                    'usuario.id as id_user',
                    'up_meca.value as number_matriculation',
                ])
                ->orderBy('usuario.id','ASC')
                ->whereNull('usuario.deleted_by') 
                ->whereNull('usuario.deleted_at') 
            ->get();
            
           return $data=[$model,$getUser_article];
        DB::commit();
    }catch (Exception | Throwable $e) {
        return $e;
        Log::error($e);
        return response()->json($e->getMessage(), 500);
    } 
}


function getstatesPayment($getEmolument,$user){

    // return "123";
    foreach ($getEmolument as $key => $item) {
        $getStates=DB::table('states')
        ->where('states.id_code_dev',$item->id_code_dev)
        ->first();

        if(isset($getStates->id)){
            $getStudentSates=DB::table('users_states')
            ->where('users_states.user_id',$user->id)
            ->first();
            if (isset($getStudentSates->user_id)) {
                DB::table('users_states_historic')->insert([
                    'user_id' => $user->id,
                    'state_id' => $getStudentSates->state_id,
                    'occurred_at' =>  $getStudentSates->created_at,
                    'created_at' => Carbon::Now(),
                    'updated_at' =>  Carbon::Now()
                ]);

                $updateSates_user = DB::table('users_states')
                ->where('id', $getStudentSates->id)
                ->update([
                    'state_id' => $getStates->id,
                    'occurred_at' => Carbon::Now(),
                    'updated_by' =>  Auth::user()->id,
                    'updated_at' =>  Carbon::Now()
                ]);
            }else{
                DB::table('users_states')->insert([
                    'user_id' => $user->id,
                    'state_id' => $getStates->id,
                    'created_by' => Auth::user()->id,
                    'updated_by' =>  Auth::user()->id,
                    'occurred_at' => Carbon::Now(),
                    'created_at' => Carbon::Now(),
                    'updated_at' =>  Carbon::Now()
                ]);

            }
            
        }

        
    }    
}




function Anular_matricula($id_articles,$id_user){
   

//   $first = $collection->firstWhere('code_dev', 'anul_matric');

  $dados=collect($id_articles);

  $article =  $dados->firstWhere('code_dev', 'anul_matric');
 
if($article!=null){
  
    $ConfirmaEmolumentoExist= DB::table('article_requests')
    ->join('articles as art', 'art.id', '=', 'article_requests.article_id')
    ->where('art.id',$article->id_article)
    ->where('article_requests.user_id',$id_user)
    ->whereNull('article_requests.deleted_at')
    ->first();

   
    
    if($ConfirmaEmolumentoExist){
        
     
        $anoLEctivo=$ConfirmaEmolumentoExist->anoLectivo;

        $getMatric= DB::table('matriculations')
        ->where('lective_year',$anoLEctivo)
        ->where('user_id',$id_user)
        ->whereNull('deleted_at')
        ->first();

        if($getMatric){
            $currentData = Carbon::now();

            $ApagarMatriculation = DB::table('matriculations')
            ->where('id', $getMatric->id)
            ->where('user_id', $id_user)
            ->update(['deleted_at' => $currentData,'deleted_by'=> Auth::user()->id ]);  
        }

    }

}else{

    // se for nulo
    //não faz nada na anulação

    // return "Sem makas";
}


}


function validar_mudanca_curso($id_articles,$id_user){
   
//   $first = $collection->firstWhere('code_dev', 'anul_matric');

  $dados=collect($id_articles);

  $article =  $dados->firstWhere('code_dev', 'mudanca_curso');
 
if($article!=null){
  
    $ConfirmaEmolumentoExist= DB::table('article_requests')
    ->join('articles as art', 'art.id', '=', 'article_requests.article_id')
    ->where('art.id',$article->id_article)
    ->where('article_requests.user_id',$id_user)
    ->whereNull('article_requests.deleted_at')
    ->first();

   
    
    if($ConfirmaEmolumentoExist){
  
        $anoLEctivo=$ConfirmaEmolumentoExist->anoLectivo;

        $getChange_course= DB::table('tb_change_course_normal')
        ->where('id_student_user',$id_user)
        ->whereNull('status')
        ->first();

        if($getChange_course){

            $user = User::whereId($id_user)->firstOrFail();
            $user->courses()->sync($getChange_course->id_new_course);
       

            $currentData = Carbon::now();

            $validarMudanca = DB::table('tb_change_course_normal')
            ->where('id',$getChange_course->id)
            ->where('id_student_user',$id_user)
            ->update(['updated_at' => $currentData,'updated_by'=> Auth::user()->id,'status'=>1 ]);  
      
        }

    }

}else{


}


}

