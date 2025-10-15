<?php

use App\Modules\Users\Models\User;
use App\Helpers\LanguageHelper;
use App\Modules\RH\Controllers\RhApiController;

Route::group(
    [
    'module' => 'RH',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\RH\Controllers',
    'middleware' => [
        // 'web',
        'localeSessionRedirect',
        'localizationRedirect',
        // 'auth',
    ]
],

    function () {
        Route::group(['prefix'=>'api'],function () {
            //Route da App SAF-T AGT
            //Author: Sedrac Calupeteca
            //Data: 06/09/2024
            Route::get('students', 'SaftApiController@findAll'); 
            Route::get('students-update/{data_update}', 'SaftApiController@findAllUpdate'); 
            // Route::get('students-between/{date_start}/{date_end}', 'SaftApiController@findAllBetween');
            Route::get('company', 'SaftApiController@getCompanyData'); 
            Route::get('students-between/{start_date?}/{end_date?}', 'SaftApiController@findStudentsBetween'); 
            Route::get('document-between/{start_date?}/{end_date?}', 'SaftApiController@documentBetween'); 
            Route::get('emolumento-between/{start_date?}/{end_date?}', 'SaftApiController@emolumentoBetween');
            
            //Routa da App Mobile -API
            //Author: Cláudio Fernando
            //Data:25/07/2023
           
            Route::get('app-user-data/{id}', 'MobileApiController@getUserData')->name('app.getUserData'); 
            Route::post('app-login', 'MobileApiController@login')->name('app.login'); 
            Route::post('app-student-matriculations', 'MobileApiController@matriculations_student')->name('app.matriculations.estudant'); 
            Route::post('Teste', 'testeController@index')->name('basico-teste'); 
            
            //Rout da api WHATSAPPBOT`
            //Autor:Cláudio Fernando
            //Data:07/02/2024
            
            Route::post('whatsappbot-login', 'WhatsappBotApi@login')->name('app.login'); 
            Route::get('get_current_account/{id}', 'WhatsappBotApi@getCurrentAccount')->name('app.getCurrentAccount'); 
            Route::get('get_matriculations/{id}', 'WhatsappBotApi@getMatriculations')->name('app.getMatriculations'); 
            Route::get('get_schedules/{id}', 'WhatsappBotApi@getSchedules')->name('app.getSchedules');   
            Route::get('get_grades/{id}', 'WhatsappBotApi@getGrades')->name('app.getGrades'); 

            

            
            
            
            
            
            
            
            
            
            
            
            
            // Rota para consumir dados.
            Route::get('get-information-emissor/{body}', function ($body){
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];

                    $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                        return app('App\Modules\RH\Controllers\RhApiController')->getAll_student();
                    }
                    else {
                        // ACESSO NEGAD
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   

                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }

            });

            Route::get('get-student-photo/{body}', function ($body){
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];
                    $user_photo_id =$vetorBody[2];
                   $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                        return app('App\Modules\RH\Controllers\RhApiController')->get_student_photo($user_photo_id);
                    }
                    else {
                        // ACESSO NEGAD
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   

                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }

            });
            
            
            Route::get('get-information-staff/{body}',function ($body){
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];
                    
                    $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                        return app('App\Modules\RH\Controllers\RhApiController')->getStaff();
                    }
                    else {
                        // ACESSO NEGAD
                        
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   

                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }
            });
            Route::get('get-information-docente/{body}',function($body){
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];
                   $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                        return app('App\Modules\RH\Controllers\RhApiController')->getDocente();
                    }
                    else {
                        // ACESSO NEGAD
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   

                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }
            });

           Route::get('get-status-student/{body}', function ($body) {
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];
                    $matriculation = $vetorBody[2];

                   $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                         
                        return app('App\Modules\RH\Controllers\RhApiController')->getStatus_Student($matriculation);
                    }
                    else {
                        // ACESSO NEGAD
                        
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   
 
                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }
            });

            Route::get('sendNewMatriculation/{id_estudante}', function ($id_estudante){

              return  getApiWebhookNewMatriculation($id_estudante);
            });
             Route::get('sendMensagem', 'RhApiController@sendMatricula');
             Route::post('recebeMensagem', 'RhApiController@recebeMensagem');

            Route::get('catraca/{id_funcionario}/{data}', 'RhApiController@catracaSimulacaoAIP');

            Route::get('get-curso-forlearmIspm/{body}', function ($body) {
                
                try {
                    $vetorBody=explode(',',$body);
                    $token = $vetorBody[0];
                    $key =$vetorBody[1];
                   $validation = app('App\Modules\RH\Controllers\RhApiController')->loginApi($token,$key);
                    if ($validation===true) {   
                        //  ACESSO CONCEDIDO
                        return app('App\Modules\RH\Controllers\RhApiController')->getCursoForlearn_ispm();
                    }
                    else {
                        // ACESSO NEGAD
                        return response()->json(['Erro' => 'Access denied'], 404);
                    }                   

                }catch (Exception | Throwable $e) {
                    Log::error($e);
                    return response()->json($e->getMessage(), 500);
                }
            });
        }); 

        Route::get('configuracao-api-client', 'RhApiController@configuracaoApiClient');
        
    }
);

Route::group(
    [
        'module' => 'RH',
        'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
        'namespace' => 'App\Modules\RH\Controllers',
        'middleware' => [
            'web',
            'localeSessionRedirect',
            'localizationRedirect',
            'auth',
        ]
    ],
    function () {
        Route::group(['prefix'=>'config-api-client'],function () {
            Route::middleware(['role:superadmin'])->group(function () {
                Route::get('configuracao-api-client', 'RhApiController@configuracaoApiClient');
                Route::post('criar-webhook-servico-entidade', 'RhApiController@criarWebhookServico_entidade')->name('criar.webhook-servico-entidade');
                Route::get('ajax-table-client-webhook', 'RhApiController@ajax_table_client_webhook');
                Route::post('delete-config-client-webhook', 'RhApiController@delete_config_client_webhook')->name('delete.config-client-webhook');
                Route::post('editar-configuracao-cliente', 'RhApiController@editar_configuracao_cliente')->name('editar.configuracao-cliente');
            });
           
        });  
    }
);



