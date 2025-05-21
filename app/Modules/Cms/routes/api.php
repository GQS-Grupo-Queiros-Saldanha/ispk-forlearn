<?php
Route::group(
    [
    'module' => 'Cms',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Cms\Controllers',
    'middleware' => [
        // 'web',
        'localeSessionRedirect',
        'localizationRedirect',
        // 'auth',
    ]
],

    function () {
        Route::post('update-percurso-grades','mainController@update_percurso_grades')->middleware([]);;
        Route::get('get-classes-grades/{class_id}/{lectivo}','mainController@get_classes_grades')->middleware([]);
 });
Route::group(['module' => 'Cms', 'middleware' => ['api'], 'namespace' => 'App\Modules\Cms\Controllers'], function() {


});
