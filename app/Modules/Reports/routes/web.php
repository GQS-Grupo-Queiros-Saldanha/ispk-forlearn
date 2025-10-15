<?php

Route::group(
    [
   'module' => 'Reports',
   'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
   'namespace' => 'App\Modules\Reports\Controllers',
   'middleware' => [
       'web',
       'localeSessionRedirect',
       'localizationRedirect',
       'auth',
   ]],
    function () {
        Route::group(['prefix' => 'reports'], function () {
            Route::middleware(['role_or_permission:superadmin|manage-reports'])->group(function () {
                //Route::get('reports', 'ReportsController@index')->name('index');
                
                
                Route::get('listarRecibo','ReportsController@index')->name('listarRecibo');
                Route::get('tabelaRecibo','ReportsController@listaAjax')->name('tabelaRecibo');
                Route::post('listasRecibos', 'ReportsController@listasRecibos')->name('listasRecibos');

                //testando
                // Route::get('talao','ReportsController@index')->name('talao');
                // Route::get('talaoAjax','ReportsController@mostrarAjax')->name('talaoAjax');
                // Route::post('talaos','ReportsController@talao')->name('talaos');

                Route::post('getResults', 'ReportsController@getResults')->name('getResults');
                Route::get('getResults/getPDF', 'ReportsController@generatePDF')->name('getResults.getPDF');
                //Route::resource('account', 'PaymentsController');
                //Route::get('account_ajax/{userId}', 'PaymentsController@ajax')->name('account.ajax');
                //Route::post('account_manual_update/{id}', 'PaymentsController@paymentManualUpdate')->name('account.manual_update');
                Route::get('report_users', 'ReportsController@reportByUsers')->name('reportByusers');
                Route::get('ajax_report_users', 'ReportsController@getUsers')->name('reports.getUsers');
                Route::get('ajax_report_users_by_role/{keyword}', 'ReportsController@getUserByRoles')->name('reports.getUsersByRole');
                Route::get('get_user_duplicates', 'ReportsController@getUserDuplicates')->name('reports.getUserDuplicates');
                Route::get('get_user_duplicates_by_slug/{slug}', 'ReportsController@getUserDuplicatesBy')->name('reports.getUserDuplicatesBySlug');
                Route::get('reports', 'ReportsController@generalReport')->name('general.reports');
                Route::get('general-ajax/{role}/{state}/{start_from}', 'ReportsController@generalReportAjax')->name('general.report.ajax');
            });

            Route::middleware(['role_or_permission:superadmin|relatorios-gerir-folha-caixa'])->group(function () {
                Route::get('generate-enrollment-income','DocsReportsController@generateEnrollmentIncame')->name('generate-enrollment-income');

                Route::get('generate-enrollment-incomeEmulomento/{elemento}','DocsReportsController@generateEnrollmentIncameEmolumento');
                Route::get('generate-enrollment-incomeStudent/{curso}/{classes}','DocsReportsController@getStudent');
                Route::get('get-article-rules/{lectivo}/{classes}/{mes}/{article}','DocsReportsController@getArticleRules');
                Route::get('getClasses/{curso}/{year}','DocsReportsController@getClasses');

                
                Route::get('getStudentFinalist/{curso}/{year}','DocsReportsController@getStudentFinalist');
                Route::get('generate-enrollment-pending-finalist','DocsReportsController@generateEnrollmentPendingFinalist')->name('generate-enrollment-pending-finalist');
                Route::get('generate-enrollment-incomeEmulomento-finalist/{elemento}','DocsReportsController@generateEnrollmentIncameEmolumentofinalist');
                Route::post('send-enrollment-parameters-finalist', 'DocsReportsController@enrollmentPendingWithParametersfinalist')->name('send.enrollment-parameters-finalist');
                Route::get('generate-enrollment-total','DocsReportsController@generateEnrollmentTotal')->name('generate-enrollment-total');
                Route::post('send-enrollment-parameters-total', 'DocsReportsController@enrollmentTotalWithParameters')->name('send.enrollment-parameters-total');

                Route::post('send-enrollment-parameters', 'DocsReportsController@enrollmentIncomeWithParameters')->name('send.enrollment-parameters');
                Route::get('enrollment-income', 'DocsReportsController@enrollmentIncome');
                Route::post('income-export-excel', 'DocsReportsController@incomeExport')->name('submit.excel');
                //Route::resource('articles', 'ArticlesController');
                //Route::get('articles_ajax', 'ArticlesController@ajax')->name('articles.ajax');
            });
            Route::middleware(['role_or_permission:superadmin|relatorios-gerir-folha-caixa'])->group(function () {
                Route::get('generate-enrollment-pending','DocsReportsController@generateEnrollmentPending')->name('generate-enrollment-pending');
                
                
                Route::get('extract/{students}/{year}','DocsReportsController@extract')->name('extract');
                
                Route::post('send-enrollment-pending-parameters', 'DocsReportsController@enrollmentPendingWithParameters')->name('send.enrollment-pending-parameters');
                Route::get('pending-article', 'DocsReportsController@pendingArticles');
                
                // The routes down is for test 
                Route::get('pending-anulate', 'DocsReportsController@pendinganulate');
                Route::get('pending-anulate-delete/{id}/{year}', 'DocsReportsController@pendingDelete')->name("anulate.pendingDelete");
            });
            
              // Routas das declaraçoes
            Route::middleware(['role_or_permission:gerir_requerimento'])->group(function () {
            // Route::get('declaration-Without-note','DeclarationController@generatePdfDeclaracao');
            Route::get('generate-declaration-note','DeclarationController@create');
            Route::get('documentation_course','DeclarationController@course_documentation');
            Route::get('documentation_students/{id}','DeclarationController@studants_course_documentation');
            Route::get('retornar_emolumento','DocsReportsController@retormar_emolumento')->name('retornar_emolumento');
            Route::post('documentation-generate','DeclarationController@generatePdfDeclaracao')->name('document.generate-documentation');

            });

        });
    }
);
