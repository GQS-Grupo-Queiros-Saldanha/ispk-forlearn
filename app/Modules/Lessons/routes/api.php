<?php

Route::group(['module' => 'Lessons', 'middleware' => ['api'], 'namespace' => 'App\Modules\Lessons\Controllers'], function() {

    Route::resource('Lessons', 'LessonsController');

});
