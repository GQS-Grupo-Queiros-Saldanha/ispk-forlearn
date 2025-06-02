<?php

//rota fora do auth
Route::get('api/boletim_pdf/{whatsapp}', 'App\Modules\Cms\Controllers\mainController@boletim_pdf');
Route::get('matriculation_id/', 'App\Modules\Cms\Controllers\mainController@get_matriculation_id');



// ENTIRE MODULE ROUTE GROUP
Route::group([
    'module' => 'Cms',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Cms\Controllers',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'auth'
    ]],
    function () {
        // Linguas
        Route::middleware(['role_or_permission:superadmin|manage-languages'])->group(function () {
            Route::resource('languages', 'LanguagesController');
            Route::get('languages_ajax', 'LanguagesController@ajax')->name('languages.ajax');
            Route::get('languages/{id}/default', 'LanguagesController@default')->name('languages.default');
        });

        // Menus
        Route::middleware(['role_or_permission:superadmin|manage-menus'])->group(function () {
            Route::resource('menus', 'MenusController');
            Route::get('menus_ajax', 'MenusController@ajax')->name('menus.ajax');
            Route::get('menus_items', 'MenusController@items')->name('menus.items.ajax');
        });

        // Menu Items
        Route::middleware(['role_or_permission:superadmin|manage-menu-items'])->group(function () {
            Route::resource('menu-items', 'MenuItemsController');
            Route::get('menu-items_ajax', 'MenuItemsController@ajax')->name('menu-items.ajax');
            Route::get('menu-items_save_order', 'MenuItemsController@saveOrder')->name('menu-items.save_order');
        });
        
         Route::middleware(['role_or_permission:superadmin|configur_codeDev'])->group(function () {
            Route::get('configur_codeDev', 'ConfingDevController@configCode');
            Route::get('getCodeInCategoria/{nome_tabela}', 'ConfingDevController@getCodeInCategoria')->name("getCodeInCategoria");
            Route::get('getCodeCategoria/{id_categoria}', 'ConfingDevController@getCodeCategoria');
            Route::post('created_categoria', 'ConfingDevController@created_categoria')->name('created_categoria');
            Route::post('created_codigoInCategory', 'ConfingDevController@created_codigoInCategory')->name('created_codigoInCategory');
            Route::post('created_categoria_save', 'ConfingDevController@created_categoria_save')->name('created_categoria_save');
        });
        
        
             // access-control
        Route::middleware(['role_or_permission:superadmin|acess-control-log'])->group(function () {
           Route::resource('controlodeacessos', 'accessLogController');
           Route::get('access-control-ajax', 'accessLogController@ajax')->name('access-control.ajax');
        });
        
        Route::middleware(['role_or_permission:superadmin|student'])->group(function () {
        
            Route::get('get_boletim_student/{lective_year}', 'mainController@get_boletim_student')->name('main.get_boletim_student');
            Route::get('get_schedule_student/{lective_year}', 'mainController@get_schedule_student')->name('main.get_schedule_student');
        });
        
        Route::get('repair', 'accessLogController@repair')->name('repair');
        
        Route::get('painelinicial', 'mainController@index')->name('main.index');
        Route::get('boletim_pdf/{matriculation}', 'mainController@boletim_pdf')->name('main.boletim_pdf');
        Route::get('forTEST', 'mainController@test');
        Route::get('verificar_pauta/{turma}/{disciplina}/{lective}/{pauta}', 'mainController@verificar_pauta')->name('main.verificar_pauta');

        
    });
