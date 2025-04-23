<?php

use App\Modules\GA\Controllers\ScholarshipHolderController;

Route::group(
    [
        'module' => 'GA',
        'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
        'namespace' => 'App\Modules\GA\Controllers',
        'middleware' => [
            'web',
            'localeSessionRedirect',
            'localizationRedirect',
            'auth',
        ],
    ],
    function () {


            // Admin
            // Note: if changing prefix please change it as well in the database (menu_items)
            Route::group(['prefix' => 'gestao-academica'], function () {
        
            //sedrac
            Route::post('mudanca-curso','settingCourseCurricularController@students_course_curricular_block_change')->name('mudanca.curso');
            Route::post('updateHorario/{id}', 'StudyPlansController@updateHorario')->name('study-plain.horario');

            //retiro 2
            Route::resource('buildings', 'BuildingsController');
            Route::get('buildings_ajax', 'BuildingsController@ajax')->name('buildings.ajax');
            Route::get('buildings/rooms/{id}', 'BuildingsController@rooms')->name('buildings.rooms');

            // Days of the week
            Route::middleware(['role_or_permission:superadmin|manage-days-of-the-week'])->group(function () {
                Route::resource('days-of-the-week', 'DaysOfTheWeekController');
                Route::get('days-of-the-week1', 'DaysOfTheWeekController@ajax')->name('days-of-the-week.ajax');
                Route::get('days-of-the-week/{id}/is_start_of_week', 'DaysOfTheWeekController@start_of_week')->name('days-of-the-week.start_of_week');
            });
            // Events
            Route::middleware(['role_or_permission:manage-events|view-event'])->group(function () {
                Route::resource('events', 'EventsController');
                Route::get('events_ajax', 'EventsController@ajax')->name('events.ajax');
                Route::get('ajax_users/{id_role}', 'EventsController@ajax_users')->name('events.ajax_users');
                Route::get('my_events/{id_user}', 'EventsController@my_events')->name('events.my_events');
            });

            // Orçamentos
            Route::middleware(['role_or_permission:manage-budget|view-budget'])->group(function () {
                Route::resource('budget', 'BudgetController');
                Route::get('budget_ajax', 'BudgetController@ajax')->name('budget.ajax');
                Route::get('budget/create', 'BudgetController@create')->name('budget.create');
                Route::get('budget/edit/{id}', 'BudgetController@edit')->name('budget.edit');
                Route::post('budget/store', 'BudgetController@store')->name('budget.store');
                Route::post('budget/update/{id}', 'BudgetController@update')->name('budget.update');
                Route::get('budget/show/{id}', 'BudgetController@show')->name('budget.show');
                Route::get('budget/reports/{id}', 'BudgetController@reports')->name('budget.reports');
                Route::post('budget/delete', 'BudgetController@destroy')->name('budget.delete');
            });

            // Orçamentos tipos
            Route::middleware(['role_or_permission:manage-budget'])->group(function () {
                Route::resource('budget_type', 'BudgetTypeController');
                Route::get('budget_type_ajax', 'BudgetTypeController@ajax')->name('budget_type.ajax');
                Route::get('budget_type/create', 'BudgetTypeController@create')->name('budget_type.create');
                Route::get('budget_type/edit/{id}', 'BudgetTypeController@edit')->name('budget_type.edit');
                Route::post('budget_type/store', 'BudgetTypeController@store')->name('budget_type.store');
                Route::post('budget_type/update/{id}', 'BudgetTypeController@update')->name('budget_type.update');
                Route::get('budget_type/show/{id}', 'BudgetTypeController@show')->name('budget_type.show');
                Route::post('budget_type/delete', 'BudgetTypeController@destroy')->name('budget_type.delete');
            });

            //  Capítulos de Orçamentos
            Route::middleware(['role_or_permission:manage-budget'])->group(function () {
                Route::resource('budget_chapter', 'BudgetChaptersController');
                Route::get('budget_chapter/index/{id}', 'BudgetChaptersController@budget_chapter')->name('budget_chapter.budget');
                Route::get('budget_chapter_ajax/{id}', 'BudgetChaptersController@ajax')->name('budget_chapter.ajax');
                Route::get('budget_chapter/create/{id}', 'BudgetChaptersController@create')->name('budget_chapter.create');
                Route::get('budget_chapter/edit/{id}', 'BudgetChaptersController@edit')->name('budget_chapter.edit');
                Route::post('budget_chapter/store', 'BudgetChaptersController@store')->name('budget_chapter.store');
                Route::post('budget_chapter/update/{id}', 'BudgetChaptersController@update')->name('budget_chapter.update');
                Route::get('budget_chapter/show/{id}', 'BudgetChaptersController@show')->name('budget_chapter.show');
                Route::post('budget_chapter/delete', 'BudgetChaptersController@destroy')->name('budget_chapter.delete');
            });

            //  Artigos de Orçamentos
            Route::middleware(['role_or_permission:manage-budget'])->group(function () {
                Route::resource('budget_articles', 'BudgetArticlesController');
                Route::get('budget_articles/index/{id}', 'BudgetArticlesController@budget_articles')->name('budget_articles.budget');
                Route::get('budget_articles_ajax/{id}', 'BudgetArticlesController@ajax')->name('budget_articles.ajax');
                Route::get('budget_articles/create/{id}', 'BudgetArticlesController@create')->name('budget_articles.create');
                // Route::get('budget_articles/create/{id}', 'BudgetArticlesController@create_articles')->name('budget_articles.create_id');
                Route::get('budget_articles/edit/{id}', 'BudgetArticlesController@edit')->name('budget_articles.edit');
                Route::post('budget_articles/store', 'BudgetArticlesController@store')->name('budget_articles.store');
                Route::post('budget_articles/update/{id}', 'BudgetArticlesController@update')->name('budget_articles.update');
                Route::get('budget_articles/show/{id}', 'BudgetArticlesController@show')->name('budget_articles.show');
                Route::post('budget_articles/delete', 'BudgetArticlesController@destroy')->name('budget_articles.delete');
            });


            // Calendar event
            Route::middleware(['role_or_permission:superadmin|manage-events'])->group(function () {
                Route::resource('calendar-event', 'CalendarEventController');
                Route::get('calendar-event_ajax', 'CalendarEventController@ajax')->name('calendar-event.ajax');
            });

            // Library  = Biblioteca   


            Route::middleware(['role_or_permission:superadmin|library_manage_item|library_manage_request|library_view'])->group(function () {
                Route::resource('library', 'LibraryController');
                Route::post('library-create-author', 'LibraryController@new_author')->name('create-author');
                Route::post('library/library-create-item', 'LibraryController@store')->name('library-create-item');
                Route::get('library/new-item/{type}', 'LibraryController@new')->name('library-new');
                Route::get('library-library-loan', 'LibraryController@loan')->name('library-loan');
                Route::get('library-computer-loan', 'LibraryController@loan_computer')->name('library-computer-loan');
                Route::get('library-create-item/{array}', 'LibraryController@create_item');
                Route::post('library-edit-item/{array}', 'LibraryController@edit_item');
                Route::post('library-delete-item/{array}', 'LibraryController@delete_item');
                Route::post('library-get-item/{array}', 'LibraryController@get_item');
                Route::post('library-recycle-item/{array}', 'LibraryController@recycle_item');
                Route::get('library-get-loan/{id}', 'LibraryController@get_user_loan');
                Route::get('library-get_filter-loan/{estado}', 'LibraryController@get_states_loan');
                Route::get('library-get_filter-loan-computer/{estado}', 'LibraryController@get_states_loan_computer');
                Route::get('library-get_book-loan/{id}', 'LibraryController@get_book_loan');

                Route::get('library-create', 'LibraryController@library_create')->name('library-create');
                Route::get('library-searchBooks', 'LibraryController@searchBooks')->name('library-searchBooks');
                Route::get('library-searchLoan', 'LibraryController@searchLoan')->name('library-searchLoan');
                Route::get('library-bin', 'LibraryController@bin')->name('library-bin');

                // Routas para criar os PDF

                Route::get('library-create-pdf/{id}', 'LibraryController@library_create_pdf')->name('library-create-pdf');
                Route::get('library-computer-pdf/{id}', 'LibraryController@library_computer_pdf')->name('library-computer-pdf');
                Route::get('library-reports-pdf/{inicio}/{fim}/{estado}', 'LibraryController@library_reports_pdf')->name('library-reports-pdf');
                Route::get('library-report-item-pdf/{item}', 'LibraryController@library_report_item_pdf')->name('library-report-item-pdf');
                Route::get('library-reports-computer-pdf/{inicio}/{fim}/{estado}', 'LibraryController@library_reports_computer_pdf')->name('library-reports-computer-pdf');
            });

            // Event types
            Route::middleware(['role_or_permission:manage-events'])->group(function () {
                Route::resource('event-types', 'EventTypesController');
                Route::get('event-types_ajax', 'EventTypesController@ajax')->name('event-types.ajax');
            });



            // Rooms
            Route::middleware(['role_or_permission:superadmin|manage-rooms'])->group(function () {
                Route::resource('rooms', 'RoomsController');
                Route::get('rooms_ajax', 'RoomsController@ajax')->name('rooms.ajax');
            });
            // Summaries
            Route::middleware(['role_or_permission:superadmin|manage-summaries'])->group(function () {
                Route::resource('summaries', 'SummariesController');
                Route::get('summaries_ajax/{lective_year}', 'SummariesController@ajax')->name('summaries.ajax');
                Route::get('summaries/disciplines_ajax/{id}', 'SummariesController@ajaxDisciplines')->name('summaries.disciplines-ajax');
                Route::get('summaries/discipline_regimes_ajax/{studyPlanId}/{disciplineId}', 'SummariesController@ajaxDisciplineRegimes')->name('summaries.discipline-regimes-ajax');
                // Route::get('summaries/disciplines/{study_plan_edition_id}', 'SummariesController@disciplinesAjax')->name('summaries.disciplines.ajax');
                // Route::get('summaries/modules/{spe_discipline_id}', 'SummariesController@modulesAjax')->name('summaries.modules.ajax');
                // Route::get('summaries/module/{module_id}', 'SummariesController@moduleAjax')->name('summaries.module.ajax');
                Route::get('summary_student', 'SummariesController@summaryByStudent')->name('summaryStudent');
                Route::get('summary_discipline_ajax/{disciplineId}/{lective_year}', 'SummariesController@getSummary');
                Route::get('summary_info/{summaryId}', 'SummariesController@summaryInfo');
                Route::get('summary_pdf/{summaryPDF}', 'SummariesController@ajaxSummaryArchive')->name('summary.archive');
            });
            // Student Summaries
            Route::middleware(['role_or_permission:superadmin|student-manage-summaries'])->group(function () {
                /* Route::get('summary_student', 'SummariesController@summaryByStudent')->name('summaryStudent');
                 Route::get('summary_discipline_ajax/{disciplineId}', 'SummariesController@getSummary');
                 Route::get('summary_info/{summaryId}', 'SummariesController@summaryInfo');*/
            });
            // Schedule Types
            /*Route::middleware(['role_or_permission:superadmin|manage-schedule-types'])->group(function () {
                Route::resource('schedule-types', 'ScheduleTypesController');
                Route::get('schedule-types_ajax', 'ScheduleTypesController@ajax')->name('schedule-types.ajax');
                Route::get('schedule-types/times/{id}', 'ScheduleTypesController@times')->name('schedule-types.times');
            });*/
            //Retiro 3
            Route::resource('schedule-types', 'ScheduleTypesController');
            Route::get('schedule-types_ajax', 'ScheduleTypesController@ajax')->name('schedule-types.ajax');
            Route::get('schedule-types/times/{id}', 'ScheduleTypesController@times')->name('schedule-types.times');
            Route::middleware(['role_or_permission:superadmin|manage-schedules'])->group(function () {
                Route::resource('schedules', 'SchedulesController');
            });
            Route::get('schedule_student_pdf/{lective_year}', 'SchedulesController@generate_student_pdf')->name('schedules.student.pdf');
            Route::get('schedules', 'SchedulesController@index')->name('schedules.index');
            Route::get('schedules_ajax/{lective_year}', 'SchedulesController@ajax')->name('schedules.ajax');
            Route::get('schedule_pdf/{id}', 'SchedulesController@generate_pdf')->name('schedules.pdf');
            Route::resource('schedules', 'SchedulesController');
            Route::get('schedules_ajax/{lective_year}', 'SchedulesController@ajax')->name('schedules.ajax');
            Route::get('schedule_pdf/{id}', 'SchedulesController@generate_pdf')->name('schedules.pdf');

            Route::get('schedule_teacher', 'SchedulesController@getCurriculerPlanTeacher')->name('schedules.teacher');
            Route::get('print_schedule_teacher/{lective_year}', 'SchedulesController@printCurriculerPlanTeacher')->name('schedules.teacher.print');
            Route::get('print_schedule_student/{lective_year}', 'SchedulesController@printCurriculerPlanStudent')->name('schedules.student.print');
            // Courses group
            // Note: if changing prefix please change it as well in the database (menu_items)
            Route::group(['prefix' => 'courses'], function () {

                // Courses
                Route::middleware(['role_or_permission:superadmin|manage-courses'])->group(function () {
                    Route::resource('courses', 'CoursesController');
                    Route::get('courses_ajax', 'CoursesController@ajax')->name('courses.ajax');

                    Route::resource('special-courses', 'SpecialCoursesController');
                    Route::get('special-courses-ajax', 'SpecialCoursesController@ajax')->name('special-courses.ajax');
                
                    
                    Route::resource('special-course-editions','SpecialCourseEditionsController');
                    Route::get('special-course-editions-list/{course}', 'SpecialCourseEditionsController@list')->name('sce_list');
                    Route::get('spce_ajax/{course}/{lective_year}', 'SpecialCourseEditionsController@ajax')->name('sce_ajax');
                    Route::post('sce_storeEdition', 'SpecialCourseEditionsController@storeEdition')->name('sce_storeEdition');
                    Route::post('sce_updateEdition/{id}', 'SpecialCourseEditionsController@updateEdition')->name('sce_updateEdition');
                });

                // Duration types
                Route::middleware(['role_or_permission:superadmin|manage-duration-types'])->group(function () {
                    Route::resource('duration-types', 'DurationTypesController');
                    Route::get('duration-types_ajax', 'DurationTypesController@ajax')->name('duration-types.ajax');
                });

                // Degrees
                Route::middleware(['role_or_permission:superadmin|manage-degrees'])->group(function () {
                    Route::resource('degrees', 'DegreesController');
                    Route::get('degrees_ajax', 'DegreesController@ajax')->name('degrees.ajax');
                });

                // Departments
                Route::middleware(['role_or_permission:superadmin|manage-departments'])->group(function () {
                    Route::resource('departments', 'DepartmentsController');
                    Route::get('departments_ajax', 'DepartmentsController@ajax')->name('departments.ajax');
                });

                // Course cycles
                Route::middleware(['role_or_permission:superadmin|manage-course-cycles'])->group(function () {
                    Route::resource('course-cycles', 'CourseCyclesController');
                    Route::get('course-cycles_ajax', 'CourseCyclesController@ajax')->name('course-cycles.ajax');
                });



                // Course regimes
                Route::middleware(['role_or_permission:superadmin|manage-course-regimes'])->group(function () {
                    Route::resource('course-regimes', 'CourseRegimesController');
                    Route::get('course-regimes_ajax', 'CourseRegimesController@ajax')->name('course-regimes.ajax');
                });

                // Course Reports
                Route::middleware(['role_or_permission:superadmin|manage-course-regimes'])->group(function () {
                    Route::get('courses-reports', 'ReportsCoursesController@index')->name('courses.reports-index');
                    Route::post('getCoursesResults', 'ReportsCoursesController@getCoursesResults')->name('couses.getCoursesResults');
                });
            });

            // Disciplines
            // Note: if changing prefix please change it as well in the database (menu_items)
            Route::group(['prefix' => 'disciplines'], function () {

                // Disciplines
                Route::middleware(['role_or_permission:superadmin|manage-disciplines'])->group(function () {
                    Route::resource('disciplines', 'DisciplinesController');
                    Route::get('disciplines_ajax', 'DisciplinesController@ajax')->name('disciplines.ajax');
                     Route::get('discipline_pdf/{id}', 'DisciplinesController@fetchPDF')->name('discipline.pdf');
                     Route::get('update_name/{year}', 'DisciplinesController@update_name');
                });

                // Discipline Areas
                Route::middleware(['role_or_permission:superadmin|manage-discipline-areas'])->group(function () {
                    Route::resource('discipline-areas', 'DisciplineAreasController');
                    Route::get('discipline-areas_ajax', 'DisciplineAreasController@ajax')->name('discipline-areas.ajax');
                });

                // Discipline Periods
                Route::middleware(['role_or_permission:superadmin|manage-discipline-periods'])->group(function () {
                    Route::resource('discipline-periods', 'DisciplinePeriodsController');
                    Route::get('discipline-periods_ajax', 'DisciplinePeriodsController@ajax')->name('discipline-periods.ajax');
                });

                // Discipline Profiles
                Route::middleware(['role_or_permission:superadmin|manage-discipline-profiles'])->group(function () {
                    Route::resource('discipline-profiles', 'DisciplineProfilesController');
                    Route::get('discipline-profiles_ajax', 'DisciplineProfilesController@ajax')->name('discipline-profiles.ajax');
                });

                // Discipline Regimes
                Route::middleware(['role_or_permission:superadmin|manage-discipline-regimes'])->group(function () {
                    Route::resource('discipline-regimes', 'DisciplineRegimesController');
                    Route::get('discipline-regimes_ajax', 'DisciplineRegimesController@ajax')->name('discipline-regimes.ajax');
                });

                // Optional Groups
                Route::middleware(['role_or_permission:superadmin|manage-optional-groups'])->group(function () {
                    Route::resource('optional-groups', 'OptionalGroupsController');
                    Route::get('optional-groups_ajax', 'OptionalGroupsController@ajax')->name('optional-groups.ajax');
                });

                //------COMEÇA GQS-------
                Route::get('reports', 'DisciplineReportsController@index')->name('index');
                Route::post('getResults', 'DisciplineReportsController@getResults')->name('disciplinegetResults');
                //-------TERMINA GQS--------
            });

            // Study plans
            // Note: if changing prefix please change it as well in the database (menu_items)
            Route::middleware(['role_or_permission:superadmin|manage-study-plans'])->group(function () {
                Route::resource('study-plans', 'StudyPlansController');
                Route::get('study-plan_ajax', 'StudyPlansController@ajax')->name('study-plans.ajax');
                Route::get('study-plans/fetch/{id}', 'StudyPlansController@fetchAjax')->name('study-plans.fetch');
                Route::get('study-plans/pdf/{id}', 'StudyPlansController@generate_pdf')->name('study-plans.pdf');
            });

            // Study Plan Edition
            // Note: if changing prefix please change it as well in the database (menu_items)
            Route::group(['prefix' => 'study-plan-editions'], function () {

                // Study Plan Edition
                Route::middleware(['role_or_permission:superadmin|manage-study-plan-editions|gerir_horários'])->group(function () {
                    Route::resource('study-plan-editions', 'StudyPlanEditionsController');
                    Route::get('study-plan-editions_avaliacao/{id_plano}/{id_disciplina}', 'StudyPlanEditionsController@avaliacaoAdd');


                    Route::get('study-plan-editions_ajax', 'StudyPlanEditionsController@ajax')->name('study-plan-editions.ajax');
                    Route::get('study-plan-editions-by-year/{id}', 'StudyPlanEditionsController@studyPlanEditionBy');
                    Route::get('study-plan-editions/classes/{id}', 'StudyPlanEditionsController@classes')->name('study-plan-editions.classes');
                    Route::get('study-plan-editions/disciplines/{id}', 'StudyPlanEditionsController@disciplines')->name('study-plan-editions.disciplines');
                    Route::get('study-plan-editions/schedule_type/{id}', 'StudyPlanEditionsController@scheduleTypes')->name('study-plan-editions.schedule_type');
                    Route::get('/duplicate_list_item/{id}', 'StudyPlanEditionsController@duplicateListItem');
                    Route::post('duplicate_study_plan', 'StudyPlanEditionsController@duplicateStudyPlan')->name('duplicate.study_plan');
                    Route::post('study-plan-editions_avaliacao', 'StudyPlanEditionsController@consultAdd_avalicao')->name('study_plan_avaliacao');
                });

                // Study Plan Edition - Absences
                Route::middleware(['role_or_permission:superadmin|manage-absences'])->group(function () {
                    Route::get('study-plan-editions/{id}/absences', 'StudyPlanEditionsController@absences')->name('study-plan-editions.absences');
                    Route::put('study-plan-editions/{id}/update_absences', 'StudyPlanEditionsController@update_absences')->name('study-plan-editions.update_absences');
                    // Route::get('study-plan-editions/{id}/absences/ajax', 'StudyPlanEditionsController@absencesAjax')->name('study-plan-editions.absences.ajax');
                });

                // Average Calculation Rules
                Route::middleware(['role_or_permission:superadmin|manage-average-calculation-rules'])->group(function () {
                    Route::resource('average-calculation-rules', 'AverageCalculationRulesController');
                    Route::get('average-calculation-rules_ajax', 'AverageCalculationRulesController@ajax')->name('average-calculation-rules.ajax');
                });

                // Year Transition Rules
                Route::middleware(['role_or_permission:superadmin|manage-year-transition-rules'])->group(function () {
                    Route::resource('year-transition-rules', 'YearTransitionRulesController');
                    Route::get('year-transition-rules_ajax', 'YearTransitionRulesController@ajax')->name('year-transition-rules.ajax');
                });

                // Lective Years
                Route::middleware(['role_or_permission:superadmin|manage-lective-years'])->group(function () {
                   
                    Route::resource('lective-years', 'LectiveYearsController');
                    Route::get('lective-years_ajax', 'LectiveYearsController@ajax')->name('lective-years.ajax');
                    //Definição de mudança de ano curricular
                    Route::resource('lective-years-course-curricular', 'settingCourseCurricularController');
                    
                    // Bloqueio de cursos em anos lectivos
                    Route::get('lective-years-course-curricular-block', 'settingCourseCurricularController@list')->name('course-curricular-year-block.list');  
                    Route::get('lective-years-course-curricular-block-ajax', 'settingCourseCurricularController@ListAjax')->name('course-curricular-year-block.ajax');  
                    Route::get('lective-years-course-curricular-block-change/{id}', 'settingCourseCurricularController@change_state')->name('course-curricular-year-block.change_state');  

                    // Mostra a listagem dos estudantes bloqueados
                    Route::get('matriculation-students-course-curricular-change', 'settingCourseCurricularController@students_list_change')->name('matriculation-students.list-change');
                    Route::get('students-course-curricular-change-ajax/{id}', 'settingCourseCurricularController@students_list_ajax')->name('students-course-curricular-change.ajax');  

                });



                // Period Type
                Route::middleware(['role_or_permission:superadmin|manage-period-types'])->group(function () {
                    Route::resource('period-types', 'PeriodTypesController');
                    Route::get('period-types_ajax', 'PeriodTypesController@ajax')->name('period-types.ajax');
                });

                // Access Types
                Route::middleware(['role_or_permission:superadmin|manage-access-types'])->group(function () {
                    Route::resource('access-types', 'AccessTypesController');
                    Route::get('access-types_ajax', 'AccessTypesController@ajax')->name('access-types.ajax');
                });

                // Classes
                Route::middleware(['role_or_permission:superadmin|manage-classes'])->group(function () {
                    Route::resource('classes', 'ClassesController');
                    Route::get('classes_ajax', 'ClassesController@ajax')->name('classes.ajax');
                    
                    //anchor
                    Route::get('classes-by-year/{id}', 'ClassesController@classesBy');
                    Route::post('classes_duplicate', 'ClassesController@Duplicar_Turma')->name('classes.Duplicar_Turma');
                     Route::get('classes_pdf/{id}', 'ClassesController@gerarPDF')->name('classes.gerarPDF');
                });

                // Discipline Classes
                Route::middleware(['role_or_permission:superadmin|manage-discipline-classes'])->group(function () {
                    Route::resource('discipline-classes', 'DisciplineClassesController');
                    Route::get('discipline-classes_ajax', 'DisciplineClassesController@ajax')->name('discipline-classes.ajax');
                });

                // Discipline Curricula
                Route::middleware(['role_or_permission:superadmin|manage-discipline-curricula'])->group(function () {
                    Route::resource('discipline-curricula', 'DisciplineCurriculaController');
                    Route::get('discipline-curricula_ajax', 'DisciplineCurriculaController@ajax')->name('discipline-curricula.ajax');
                });

                // Discipline Absence Configuration
                Route::middleware(['role_or_permission:superadmin|manage-discipline-absence-configuration'])->group(function () {
                    Route::resource('discipline-absence-configuration', 'DisciplineAbsenceConfigurationController');
                    Route::get('discipline-absence-configuration_ajax', 'DisciplineAbsenceConfigurationController@ajax')->name('discipline-absence-configuration.ajax');
                });

                // Enrollment State Types
                Route::middleware(['role_or_permission:superadmin|manage-enrollment-state-types'])->group(function () {
                    Route::resource('enrollment-state-types', 'EnrollmentStateTypesController');
                    Route::get('enrollment-state-types_ajax', 'EnrollmentStateTypesController@ajax')->name('enrollment-state-types.ajax');
                });

                // Enrollments
                Route::middleware(['role_or_permission:superadmin|manage-enrollments'])->group(function () {
                    Route::resource('enrollments', 'EnrollmentsController');
                    Route::get('enrollments_ajax', 'EnrollmentsController@ajax')->name('enrollments.ajax');
                    Route::get('enrollments_disciplines_ajax/{id}', 'EnrollmentsController@disciplinesAjax')->name('enrollments.disciplines.ajax');
                    Route::get('enrollments_optional_disciplines_ajax/{id}', 'EnrollmentsController@optionalDisciplinesAjax')->name('enrollments.optional-disciplines.ajax');
                    Route::get('enrollments_user_ajax/{id}', 'EnrollmentsController@userAjax')->name('enrollments.user.ajax');
                });
            });

            //Scholarship Holder
            Route::middleware(['role_or_permission:superadmin|gerir_bolsas_estudo'])->group(function () {
                Route::get('scholarship-holder', 'ScholarshipHolderController@index')->name('scholarship.index');
                Route::get('scholarship-holder/ajax', 'ScholarshipHolderController@ajax')->name('scholarship.ajax');
                Route::get('scholarship-holder/ajax_entity', 'ScholarshipHolderController@ajax_entity')->name('scholarship.ajax_entity');
                
                Route::get('create-entity', 'ScholarshipHolderController@createScholarship')->name('create.entity');

                Route::post('store-entity', 'ScholarshipHolderController@store')->name('store.entity');
                Route::get('student-entity/{id}', 'ScholarshipHolderController@associateStudentEntity')->name('associate.student.entity');
                Route::post('student-entity', 'ScholarshipHolderController@storeAssociateStudent')->name('store.associate.student');
                Route::get('remove-student-entity/{id}', 'ScholarshipHolderController@removeAssociateStudent')->name('remove.associate.student');
                Route::get('scholarship-generate-invoice', 'ScholarshipHolderController@generateInvoice')->name('scholarship.generate.invoice');
                Route::get('scholarship-generate-receipt', 'ScholarshipHolderController@generateReceipt')->name('scholarship.generate.receipt');
                Route::get('list-scholarship', 'ScholarshipHolderController@listScholarship')->name('list.scholarship');
                
                Route::get('scholarship-rules', 'ScholarshipHolderController@listRules')->name('scholarship.rules');
                Route::get('getImplemtRulesAjax/{id_anolectivo}', 'ScholarshipHolderController@getImplemtRulesAjax');
                Route::get('getImplemtRulesAjax-pdf', 'ScholarshipHolderController@pdf')->name('pdf.scholarship_entity');
                Route::post('createRegraScholarshipNew', 'ScholarshipHolderController@createRegraScholarshipNew')->name('createRegraScholarship');

                Route::get('show-scholarship/{id}', 'ScholarshipHolderController@showScholarship')->name('show.scholarship');
                Route::get('edit-scholarship/{id}', 'ScholarshipHolderController@editScholarship')->name('edit.scholarship');
                Route::put('update-scholarship/{id}', 'ScholarshipHolderController@updateScholarship')->name('update.scholarship');
                Route::get('delete-scholarship/{id}', 'ScholarshipHolderController@deleteScholarship')->name('delete.scholarship');
                Route::get('ajax-users', 'ScholarshipHolderController@ajaxUsers')->name('ajax.users');
                Route::get('student-scholarship/{id}', 'ScholarshipHolderController@get_student')->name('get_student.scholarship');
                
                Route::get('pdf-scholarship-holder', 'ScholarshipHolderController@pdf_scholarship_holder')->name('pdf.scholarship-holder');
                Route::get('pdf_scholarship_entity', 'ScholarshipHolderController@pdf_scholarship_entity')->name('pdf.scholarship_entity');
            });

            // Routas configuração dos documentos
            Route::middleware(['role_or_permission:superadmin'])->group(function () {
                // Route::get('declaration-Without-note','DeclarationController@generatePdfDeclaracao');
                Route::get('Configurate-documentation-studant', 'configurationDocumentation@index')->name('documentation.index');
                Route::get('document_type', 'configurationDocumentation@type_document');
                // Route::get('documentation_students/{id}','DeclarationController@studants_course_documentation');
                Route::post('configurate-documente-type', 'configurationDocumentation@store')->name('document.generate-configuration');
            });

            // Route::middleware(['role_or_permission:student'])->group(function () {

            //     Route::get('document_type', 'configurationDocumentation@type_document');

            // });
        });
    }
);


////================================================================================
//// F4k3
////================================================================================
//
//// Student
//Route::group(['prefix' => 'student'], function () {
//
////    Route::get('/payments', function () {
////        return view('GA::payments.index');
////    })->name('payments.index');
//
//    Route::get('/schedule', function () {
//        return view('GA::schedule.index');
//    })->name('schedule.index');
//
//    Route::get('/enrollment', function () {
//        return view('GA::enrollment.index');
//    })->name('enrollment.index');
//
//    Route::get('/evaluations', function () {
//        return view('GA::evaluations.index');
//    })->name('evaluations.index');
//
//    Route::get('/calendar', function () {
//        return view('GA::calendar.index');
//    })->name('calendar.index');
//
//    Route::get('/attendance', function () {
//        return view('GA::attendance.index');
//    })->name('attendance.index');
//
//    Route::get('/requests', function () {
//        return view('GA::requests.index');
//    })->name('requests.index');
//
//    Route::get('/library', function () {
//        return view('GA::library.index');
//    })->name('library.index');
//});
//
//// Teacher
//Route::group(['prefix' => 'teacher'], function () {
//
//    Route::get('/teacher-calendar', function () {
//        return view('GA::teacher-calendar.index');
//    })->name('teacher-calendar.index');
//
//    Route::get('/teacher-class', function () {
//        return view('GA::teacher-class.index');
//    })->name('teacher-class.index');
//
//    Route::get('/summaries', function () {
//        return view('GA::summaries.index');
//    })->name('summaries.index');
//
//    Route::get('/documents', function () {
//        return view('GA::documents.index');
//    })->name('documents.index');
//
//    Route::get('/teacher-evaluations', function () {
//        return view('GA::teacher-evaluations.index');
//    })->name('teacher-evaluations.index');
//
//    Route::get('/schedules_old', function () {
//        return view('GA::schedules_old.index');
//    })->name('schedules_old.index');
//
//});
