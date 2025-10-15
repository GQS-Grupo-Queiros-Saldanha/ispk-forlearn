<?php

Route::group(['module' => 'Avaliations', 'middleware' => ['api'], 'namespace' => 'App\Modules\Avaliations\Controllers'], function() {

    Route::resource('Avaliations', 'AvaliationsController');

});
