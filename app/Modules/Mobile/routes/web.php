<?php

Route::group([
    'module' => 'Mobile',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Mobile\Controllers',
    'middleware' => [
        // 'web',
        // 'localeSessionRedirect',
        // 'localizationRedirect',
        // 'auth',
    ]],


    function () {
        Route::group(['prefix' => 'mobile'], function () {
            
            Route::resource('/', 'AppController')->names([
                'index' => 'app.index',
                'create' => 'app.create',
                'show' => 'app.show',
                'update' => 'app.update',
                'destroy' => 'app.destroy'
            ]);

            Route::post('auth-app', 'AppController@login')->name('login-app');
            Route::get('menu/{id}', 'AppController@menu')->name('menu-app');
            Route::get('notification/{id}', 'AppController@notification')->name('notification-app');
            Route::get('single_notification/{id}/{id_notify}', 'AppController@single_notification')->name('notification_single-app');
            Route::get('detail/{id}', 'AppController@detalhes')->name('detalhes-app');
            Route::get('matricula/{id}', 'AppController@matricula')->name('matricula-app');
            Route::get('perfil', 'AppController@perfil')->name('perfil-app');
            Route::get('finance', 'AppController@propina')->name('propina-app');
            Route::get('avaliacao', 'AppController@avaliacao')->name('avaliacao-app');
            Route::get('finance/{type}/{lectivo}/{id}', 'AppController@finance')->name('finance-app');
            Route::get('matricula-dados/{lectivo}/{id}', 'AppController@dadosMatricula')->name('matriculaDados-app');
            Route::middleware(['role_or_permission:superadmin|student'])->group(function () {
                
            Route::middleware(['role_or_permission:superadmin|teacher|manage-grades|staff_candidaturas'])->group(function () {

            });


            });
        });
    }
);
