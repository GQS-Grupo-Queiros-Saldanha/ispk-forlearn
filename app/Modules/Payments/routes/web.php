<?php

use Illuminate\Http\Request;

Route::get('/api/getContacorrentWhatsapp/{whatsapp}', 'App\Modules\Payments\Controllers\TransactionsController@getContacorrentWhatsapp');


Route::group(
    [
    'module' => 'Payments',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Payments\Controllers',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'auth',
    ]],
    function () {
        Route::group(['prefix' => 'payments'], function () {

            //emarq  
            Route::get("view-file/receipts/{filename}", 'FileController@receipts')->name('view-file.receipts');
            Route::get("receipts/{filename}", 'FileController@receipts2')->name('view-file.receipts2');
            Route::get("view-file/attachment/{filename}", 'FileController@attachment')->name('view-file.attachment');
            Route::get("view-file/historic_credit/{filename}", 'FileController@historic_credit')->name('view-file.historic_credit');
            //fim - emarq 

            Route::middleware(['role_or_permission:superadmin|view-payments-menu|view-tesouraria-estudante'])->group(function () {
                Route::middleware(['role_or_permission:superadmin|view-tesouraria-estudante|manage-article-requests'])->group(function () {
                    Route::post('getProxypay', 'proxyPayController@index')->name('getProxypay.referrencia');
                    Route::get('tesouraria-notication_referencia/{id}', 'proxyPayController@noticationReferrencia');
                });
                
                
                Route::middleware(['role_or_permission:superadmin|regra_bloqueio'])->group(function () {  
                        Route::get('config-divida', 'ArticlesController@confiDivida')->name('configuracao.divida');
                        Route::post('config-divida-create', 'ArticlesController@confDividaCreate')->name('config.divida.create');
                        Route::get('config-divida-ajax', 'ArticlesController@configDividaAjax')->name('config_divida.ajax');
                        Route::get('ativar-config-divida/{id}', 'ArticlesController@ativarConfigDivida')->name('ativar.config_divida');
                        Route::post('delete-divida-configuracao', 'ArticlesController@deleteDividaConfiguracao')->name('delete-divida.configuracao');
                });
                
                         Route::get('company', 'SaftApiController@getCompanyData'); 
                         Route::get('students-between/{start_date?}/{end_date?}', 'SaftApiController@findStudentsBetween'); 
                         Route::get('document-between/{start_date?}/{end_date?}', 'SaftApiController@documentBetween'); 
                         Route::get('emolumento-between/{start_date?}/{end_date?}', 'SaftApiController@emolumentoBetween');

                Route::middleware(['role_or_permission:superadmin|manage-payments'])->group(function () {
                    // Route::resource('account', 'PaymentsController');
                    // Route::get('account_ajax/{userId}', 'PaymentsController@ajax')->name('account.ajax');
                    // Route::get('account_ajax_articles/{userId}', 'PaymentsController@ajaxArticliesPerUser')->name('account.ajax_articles');
                    // Route::post('account_manual_update/{id}', 'PaymentsController@paymentManualUpdate')->name('account.manual_update');
                    

                    //------COMEÇA GQS-------
                    Route::get('reports', 'PaymentsReportsController@index')->name('index');
                    Route::post('getResults', 'PaymentsReportsController@getResults')->name('paymentgetResults');

                    Route::get('getResults/getPDF', 'PaymentsReportsController@generatePDF')->name('getResults.getPDF');
                    //-------TERMINA GQS--------
                });

                Route::middleware(['role_or_permission:superadmin|manage-articles'])->group(function () {
                    Route::resource('articles', 'ArticlesController');

                     // route que trata sobre as regras de emolumento por cada ano.
                    Route::get('/implementar_regra/{id_letivo}','ArticlesController@implementar_regra');
                    Route::get('/updateTransain','ArticlesController@updateTransain')->name('update.Transion');
                    Route::post('/updateTransainEstorno','ArticlesController@updateTransainEstorno')->name('updateTransainEstorno');
                    
                    
                    Route::get('/createEmolimento/{id_letivo}','ArticlesController@createEmolimento');

                    Route::post('createRegraEmolumento/{id_anlectivo}', 'ArticlesController@createRegraEmolumento')->name('createRegra.emolumento'); 
                    Route::get('getImplemtRules/{id_anolectivo}', 'ArticlesController@getImplemtRules');
                    Route::get('getImplemtRulesAnoLectivo/{id_anolectivo}', 'ArticlesController@getImplemtRulesAnoLectivo');
                    Route::get('getEmoluAnoletivo/{id_anolectivo}', 'ArticlesController@getEmoluAnoletivo');
                    Route::get('updateDeleteRules/{id_articlRules}', 'ArticlesController@updateDeleteRules');

                    //new ruleArticle
                    Route::get('getImplemtRulesAjax/{id_anolectivo}', 'ArticlesController@getImplemtRulesAjax');
                    Route::post('createRegraEmolumentoNew/{id_anlectivo}', 'ArticlesController@createRegraEmolumentoNew')->name('createRegraNew.emolumento');
                    Route::delete('createRegraEmolumentoDel/{id}', 'ArticlesController@delRegraEmolumento')->name('del.RegraEmolumento');

                    //rota criada pelo Marcos
                    Route::get('getEmolumento/{emolu}/{lective_year}', 'ArticlesController@getEmolumentoValor')->name('getEmolumento'); 
                    Route::post('getEmolumentoUpdate', 'ArticlesController@getEmolumentoValorUpdate')->name('getEmolumentoUpdate'); 


                    Route::get('/duplicate_list_item/{id}','ArticlesController@duplicateListItem');
                    Route::get('articles_ajax', 'ArticlesController@ajax')->name('articles.ajax');
                    Route::post('article_duplicar', 'ArticlesController@article_duplicar')->name('articles.duplicar');
                    Route::post('duplicate_article','ArticlesController@duplicateArticle')->name('duplicate.articles');
                    Route::get('articles-by-year/{id}', 'ArticlesController@ArticleBy');
                    
                    //Trio Maravilha
                    Route::get('/arcticle-pdf', 'ArticlesController@gerarPDF')->name('articles.gerarPDF');
                    Route::get('/arcticle_categoria', 'ArticlesController@categoria')->name('articles.categoria');
                    Route::get('/article_categoria_ajax', 'ArticlesCategoriaController@ajax_list')->name('articles.categoria.ajax_list');
                    Route::delete('/article_categoria_destroy/{id}/', 'ArticlesCategoriaController@destroy')->name('articles.categoria.destroy');
                    Route::put('/article_categoria_update', 'ArticlesCategoriaController@update')->name('articles.categoria.update');
                    Route::post('/article_categoria_store', 'ArticlesCategoriaController@store')->name('articles.categoria.store');

                   
                });

                Route::middleware(['role_or_permission:superadmin|manage-banks'])->group(function () {
                    Route::resource('banks', 'BanksController');
                    Route::get('banks_ajax', 'BanksController@ajax')->name('banks.ajax');
                    Route::get('pega_emonumento/{id_anolectivo}', 'BanksController@pegaEmonumento');
                    Route::get('banks_pdf', 'BanksController@generatePDF')->name('banks.pdf');
                });

                Route::middleware(['role_or_permission:superadmin|manage-article-requests|view-tesouraria-estudante'])->group(function () {
                    Route::resource('requests', 'ArticleRequestsController');

                    Route::get('user_requests_create/{id}', 'ArticleRequestsController@createUserArticle')->name('user_requests_create');
                    Route::get('user_requests/{id_matricula}', 'ArticleRequestsController@user_requests')->name('user_requests');

                    Route::get('user_requestsDisciplina/{semestre_disciplina}', 'ArticleRequestsController@user_requestsDisciplina')->name('user_requestsDisciplina.month');
                    

                    Route::post('update_credit', 'ArticleRequestsController@updateCredit')->name('update.credet');

                    // Route::get('request-transactions', 'ArticleRequestsController@update');
                    Route::get('requests_ajax/{userId}', 'ArticleRequestsController@ajax')->name('requests.ajax');
                    Route::get('requests_ajax_articles/{userId}', 'ArticleRequestsController@ajaxArticliesPerUser')->name('requests.ajax_articles');

                    Route::get('requests_ajax_disciplines/{userId}', 'ArticleRequestsController@ajaxDisciplinesPerUser')->name('requests.ajax_disciplines');
                    Route::get('request_transaction_by/{userId}/{anoLectivo}', 'ArticleRequestsController@transactionsBy');

                    // get Candidatura
                    Route::get('getOutrosEmolumentoRequerido/{userId}/{anoLectivoSem_matricula}', 'ArticleRequestsController@getOutrosEmolumentoRequerido');
                    
                    // get finalista emolumentos
                    Route::get('getEmolumentoFinalista/{userId}/{anoLectivoSem_matricula}', 'ArticleRequestsController@getEmolumentoFinalista');

                    Route::get('getFiltroEmolumento_student/{selectedUserId}', 'ArticleRequestsController@getFiltroEmolumento_student');
                    Route::get('filtroEmolumento_student/{id_art}/{selectedUserId}/{ano_lectivo}', 'ArticleRequestsController@filtroEmolumento_student');
                    Route::get('getAnolectivo_student/{userId}', 'ArticleRequestsController@getAnolectivo_student');
                    Route::get('getConsultaPropina_apagar/{selectedUserId}/{anolectivo_ativo}', 'ArticleRequestsController@getConsultaPropina_apagar');
                    Route::get('getConsultMonth/{anletivo}', 'ArticleRequestsController@getConsultMonth');
                    Route::post('requests_create', 'TransactionsArticleRequestController@create')->name('request_create');
                    Route::post('estornar', 'ArticleRequestsController@update')->name('estornar');
                    Route::get('deleteArticleRequest/{articleRequestId}', 'ArticleRequestsController@deleteArticleRequest')->name('delete_article_request');

                    Route::resource('transactions', 'TransactionsController');
                    Route::get('transactions_ajax/{userId}', 'TransactionsController@ajax')->name('transactions.ajax');
                    Route::get('transactions_ajax_balance/{userId}', 'TransactionsController@ajaxUserBalance')->name('transactions.ajax_balance');
                    Route::get('transactions_ajax_email/{userId}', 'TransactionsController@emailTransaction')->name('transactions.email.receipt');
                    Route::post('transactions_pdf', 'TransactionsController@transactionPDF')->name('transactions.pdf');
                    Route::get('Rotina_transation/{idCurso}', 'TransactionsController@Rotina_transation')->name('Rotina');
                    Route::resource('transaction_observations', 'CurrentAccountObservationsController');
                    Route::get('transaction_observations_file/{id}', 'CurrentAccountObservationsController@downloadFile')->name('transactions.file');
                    Route::get('transactions_show_observation/{id}', 'CurrentAccountObservationsController@observation')->name('transactions.showObservation');
                    Route::delete('transactions_delete_observation/{id}', 'CurrentAccountObservationsController@destroyObservation')->name('transaction_observations.destroyObservation');
                    Route::get('count_Observations_by/{userId}', 'CurrentAccountObservationsController@countObservationsBy')->name('transaction_observations.countByUser');
                });

                Route::group(['prefix' => '{userId}'], function () {
                    Route::middleware(['role_or_permission:superadmin|manage-article-requests'])->group(function () {
                    Route::resource('transaction-request', 'TransactionsArticleRequestController');

                    });
                });

                Route::middleware(['role_or_permission:superadmin|gerir_bolseiros|reembolso_manage'])->group(function () {
                      // Gestão de bolsas de estudo, depósitos e reembolso
 
                Route::get('bolseiros/reembolsos/create/{id}', 'BolseirosController@create')->name('reembolsos.create');
                Route::post('bolseiros/reembolsos/reembolsos_store', 'BolseirosController@reembolsos_store')->name('reembolsos.store');
                Route::get('bolseiros/reembolsos/destroy', 'BolseirosController@reembolsos_destroy')->name('reembolsos.destroy');
                Route::get('bolseiros/reembolsos/pdf/{id}', 'BolseirosController@reembolsos_pdf')->name('reembolsos.pdf');
                Route::get('bolseiros/reembolsos', 'BolseirosController@reembolsos')->name('bolseiros.reembolsos');
                Route::get('bolseiros/depositos', 'BolseirosController@depositos')->name('bolseiros.depositos');
                Route::get('bolseiros/ajax_reembolso/{id}', 'BolseirosController@ajax_reembolso')->name('bolseiros.ajax_reembolso');
                Route::get('bolseiros/ajax_reembolso_all/{id}', 'BolseirosController@ajax_all')->name('bolseiros.ajax_reembolso_all');
                Route::get('bolseiros/reembolsos/report', 'BolseirosController@report')->name('bolseiros.report_reembolsos');
                Route::post('bolseiros/reembolsos/report_pdf', 'BolseirosController@report_pdf')->name('bolseiros.report_pdf');

                });

                Route::get('/receipt/{id}', 'TransactionsController@ajaxTransactionReceiptFile')->name('transactions.receipt');
                Route::post('/requests_ajax/reference_exists/', 'TransactionsController@referenceExists')->name('transactions.reference_exists');
                Route::post('/requests_ajax/reference1_exists', 'TransactionsController@referenceExists1')->name('transactions.reference1_exists');
                Route::post('/requests_ajax/reference2_exists', 'TransactionsController@referenceExists2')->name('transactions.reference2_exists');
                Route::post('/requests_ajax/reference3_exists', 'TransactionsController@referenceExists3')->name('transactions.reference3_exists');
                Route::post('/requests_ajax/reference4_exists', 'TransactionsController@referenceExists4')->name('transactions.reference4_exists');
                
                // Verificar a origem da referência

                Route::get('reference_get_origem/{referencia}', 'TransactionsController@referenceGetOrigem')->name('transactions.reference_get_origem');
               
                // Verificar a origem do saldo em carteira

                Route::get('historico_saldo/{id}', 'TransactionsController@saldoGetOrigem')->name('transactions.historico_saldo');
                Route::get('historico_pagamentos_diario', 'TransactionsController@pagamentoGetOrigem')->name('transactions.historico_pagamento_dia');
                Route::get('pagamentos_dia/{date}', 'TransactionsController@getPagamentoDayAjax')->name('transactions.lista_pagamentos_dia');

                // Route::get('/exmp', 'TransactionsController@callBtn');
                // Route::get('/recibo/{id}', 'TransactionsController@test')->name('transactions.recibo');
                // Route::get('/receipt-test/{id}', 'ArticleRequestsController@generateReceipt');
            });
        });
    }
);
