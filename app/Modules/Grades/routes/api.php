<?php

Route::group(['module' => 'Grade', 'middleware' => ['api'], 'namespace' => 'App\Modules\Grades\Controllers'], function() {

    Route::resource('Grade', 'GradesController');

});
