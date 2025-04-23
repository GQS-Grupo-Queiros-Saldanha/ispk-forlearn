<?php

Route::group(
    [
    'module' => 'Lessons',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Lessons\Controllers',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'auth',
    ]],
    function () {
        Route::middleware(['role_or_permission:superadmin|teacher|view-lessons-menu'])->group(function () {
            Route::resource('lessons', 'LessonsController');
            Route::get('lessons_ajax/{lective_year}', 'LessonsController@ajax')->name('lessons.ajax');
            Route::get('lessons_disciplines', 'LessonsController@ajaxDisciplines')->name('lessons.disciplines');
            Route::get('lessons_discipline_class', 'LessonsController@ajaxDisciplineClassData')->name('lessons.discipline-class');
            Route::get('lessons_pdf/{id}', 'LessonsController@generatePDF')->name('lessons.pdf');
            Route::post('lessons_save','LessonsController@store')->name('lessons.save'); 
            Route::get('lessons/delete/{id}','LessonsController@delete')->name('lessons.delete');

            //Biblioteca
                //auth
            Route::get('libray/auth','SedracController@lib_auth')->name('lib.auth');
                //areas
             Route::get('libray/areas','SedracController@lib_areas')->name('lib.areas');
                //computadores
            Route::get('libray/computer','SedracController@lib_computer')->name('lib.computer');
            

            //Sedrac
            Route::get('lessons_hello','SedracController@hello')->name('lessons.hello');
            Route::get('lessons_hello/get_all','SedracController@get_all')->name('lessons.hello.get_all');
            Route::get('lessons_hello/show','SedracController@show')->name('lessons.hello.show');
            Route::get('lessons_hello/edit/{id}','SedracController@edit')->name('lessons.hello.edit');

            Route::get('lessons_hello/delete/{id}','SedracController@delete')->name('lessons.hello.delete');

            Route::post('lessons_hello','SedracController@store')->name('lessons.hello.store');
            Route::put('lessons_hello/{id}','SedracController@update')->name('lessons.hello.update');

            //Joaquim
            Route::get('lessons_teste','AulasController@teste')->name('lessons.teste');
            Route::get('lessons_teste/show','AulasController@index')->name('lessons.index-teste');
            Route::get('lessons_teste/show/{id}','AulasController@show')->name('lessons.teste-show');
            Route::get('lessons_teste/edit/{id}','AulasController@edit')->name('lessons.teste-edit');
            Route::get('lessons_ajaxx/','AulasController@ajax')->name('lessons.index-ajax');

                //Relatório
            Route::get('lessons_teste/relatorio','AulasController@relatorio')->name('lessons.teste-relatorio');
                //Editora
            Route::get('lessons_editora','AulasController@editora')->name('lessons.editora');

            Route::post('lessons_edtStore','AulasController@edtStore')->name('lessons.edtStore');
                //Delete
            Route::get('lessons_teste/delete/{id}','AulasController@delete')->name('lessons.teste-delete');
                //Store
            Route::post('lessons_teste','AulasController@store')->name('lessons.store');        
                //Update
            Route::put('lessons_teste/edit/{id}','AulasController@update')->name('lessons.teste.update');

        });
        Route::middleware(['role_or_permission:superadmin|menu_gestao_faltas'])->group(function () {
            Route::get('check_user', 'LessonsController@checkUser')->name('check.user');
            Route::get('lessons_attendance', 'LessonsController@attendance')->name('attendance');
            Route::get('lesson_by_teacher', 'LessonsController@attendanceByTeacher')->name('attendanceByTeacher');
            Route::get('classes/{id}', 'LessonsController@getClasses')->name('getclasses.ajax');
            Route::get('students/{discipline_id}/{class_id}', 'LessonsController@getStudents');
            Route::get('lessons_by_staff', 'LessonsController@attendanceByStaff')->name('attendanceByStaff');
            Route::get('courses_ajax', 'LessonsController@getAllCourses')->name('getCourseAjax');
            Route::get('disciplines_ajax/{course}', 'LessonsController@getAllDisciplines')->name('getdisciplinesAjax');
            Route::get('students_ajax/{discipline}/{lective_year}', 'LessonsController@getAllStudents')->name('getStudentsAjax');
            Route::get('attendance_ajax/{student}/{discipline}/{lective_year}', 'LessonsController@getAttendance')->name('getAttendance');
        });

        
    }
);
