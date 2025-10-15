<?php

Route::group(['module' => 'Payments', 'middleware' => ['api'], 'namespace' => 'App\Modules\Payments\Controllers'], function() {

    Route::resource('Payments', 'PaymentsController');

});
