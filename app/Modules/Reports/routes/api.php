<?php

Route::group(['module' => 'Reports', 'middleware' => ['api'], 'namespace' => 'App\Modules\Reports\Controllers'], function() {

    Route::resource('Reports', 'ReportsController');

});
