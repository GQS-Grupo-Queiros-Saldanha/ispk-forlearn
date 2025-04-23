<?php

Route::group([
    'module' => 'Grade',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\Grades\Controllers',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'auth',
    ]],

    function () {
        Route::group(['prefix' => 'grades'], function () {
            Route::middleware(['role_or_permission:superadmin|teacher|student|view-grades-menu|staff_candidaturas|staff_candidaturas_chefe|coordenador-curso'])->group(function () {
                Route::middleware(['role_or_permission:superadmin|teacher|manage-grades|staff_candidaturas|coordenador-curso|staff_candidaturas_chefe'])->group(function () {
                    Route::resource('teacher', 'GradesController', ['names' => 'grade_teacher']);
                    Route::get('teacher_disciplines/{id}/{anoLectivo}', 'GradesController@ajaxDisciplines')->name('grade_teacher.disciplines');
                    Route::get('teacher_students/{id}', 'GradesController@ajaxStudents')->name('grade_teacher.students');
                    Route::get('teacher_discipline/{id}', 'GradesController@ajaxDisciplineGrades')->name('grade_teacher.discipline');
                    Route::post('teacher_student_discipline', 'GradesController@ajaxStudentGrade')->name('grade_teacher.student_grade');
                });

                Route::middleware(['role_or_permission:superadmin|teacher|manage-grades|coordenador-curso-profissional'])->group(function () {
                    Route::resource('special-course-grades', 'SpecialCourseGradesController', ['names' => 'scg']);
                    Route::get('get_editions/{course_id}/{anoLectivo}', 'SpecialCourseGradesController@getEditions')->name('scg.editions');
                    Route::get('get_students_grades/{edition_id}', 'SpecialCourseGradesController@getStudentsGrades');
                    Route::get('generate-pdf/{edition_id}', 'SpecialCourseGradesController@generatePDF')->name('scg.pdf');
                
                });

                Route::middleware(['role_or_permission:superadmin|teacher|manage-grades|coordenador-curso'])->group(function () {
                    Route::get('melhoria-notas', 'MelhoriaNotasController@index');
                    Route::get('student_grades/{discipline_id}/{lective_year}/{type}','MelhoriaNotasController@studentsGrades');
                    Route::get('generate-pdf-grades/{discipline_id}/{lective_year}/{type}','MelhoriaNotasController@generatePDFGrades')->name('mn.pdf');
                    Route::post('melhoria-notas/store', 'MelhoriaNotasController@store')->name('melhoria-notas.store');
                
                    Route::get('exame-extraordinario-notas', 'MelhoriaNotasController@index');
                });

                Route::get('student', 'GradesController@indexStudent')->name('grade_student.index');
                
                
           
                     
                     
                Route::get('student_ajax/{id}', 'GradesController@ajaxStudent')->name('grade_student.ajax');
            
                //------COMEÇA GQS-------
                Route::get('reports', 'GradesReportsController@index')->name('index');
                Route::post('getResults', 'GradesReportsController@getResults')->name('gradesgetResults');

                //Route::get('getResults/getPDF', 'PaymentsReportsController@generatePDF')->name('getResults.getPDF');
                //-------TERMINA GQS--------

                Route::get('curricular_plan', 'GradesController@curricularPlan')->name('curricular_plan');
                Route::get('staff_curricular_plan', 'GradesController@staffCurricularPlan')->name('staff_curricular_plan');
                Route::get('curricular_plan_student_ajax/{id}', 'GradesController@ajaxStudentForStaff')->name('curricular_plan_student.ajax');
                Route::get('staff_curricular_plan_ed/{id}', 'GradesController@staffCurricularPlanPDF')->name('staff_curricular_plan_ed');  

                // Route::get('show_student_grades/{id}', 'GradesController@showStudentGrades')->name('show_student_grades');
                Route::get('show_student_grades/{id}/{anoLectivoId}/{cursoId}/{turma}', 'GradesController@showStudentGrades')->name('show_student_grades');

                Route::get('show_student_list/{cursoId}/{anoLectivoId}/{turma}', 'GradesController@showStudentList')->name('show_student_list');
             
                Route::get('usuario_Pega', 'GradesController@usuario');
               
                Route::get('show_student_estatistica/{cursoId}/{anoLectivoId}/{turma}', 'GradesController@showStudentEstatistic')->name('show_student_estatistic');
                
                Route::post('show_student_excel', 'GradesController@exportListaExcel')->name('show_student_excel');

                Route::get('grades_candidate/getStudentsBy/{lective_year}/{courseId}/{id_disciplina}/{turma}', 'GradesController@getStudentsBy')->name('candidateGrade.get_students');


            });
        });
    }
);
