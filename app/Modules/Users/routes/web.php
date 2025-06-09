<?php

// ENTIRE MODULE ROUTE GROUP
use App\Modules\Users\Controllers\UserAvatarController;
use App\Modules\Users\Controllers\centralNotification;
use App\Modules\Users\Controllers\UsersController;
use App\Modules\Users\Controllers\MatriculationController;

Route::post('/actualizar‑whatsapp', [UsersController::class, 'actualizarWhatsapp'])->name('actualizar-whatsapp');
Route::post('api/getwhatsapp/{whatsapp}', [MatriculationController::class, 'getWhatsapp']);



Route::group(
    [
        'module' => 'Users',
        'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
        'namespace' => 'App\Modules\Users\Controllers',
        'middleware' => [
            'web',
            'localeSessionRedirect',
            'localizationRedirect',
            'auth',
        ]
    ],
    function () {

        // Route::get('users/testeParametro', 'UsersController@updateParametro');
        // Route::get('users/testeParametro', 'UsersController@updateUsuario');
        //Emarq
        Route::get('users/avatar/{filename}', 'FileController@getAvatar')->name('user.avatar');
        Route::get('users/avatar/', 'FileController@getAvatar')->name('user.avatar.default');
        //fim - Emarq

        Route::get('users_create_docente_student', 'UsersController@create_docente_student')->name('users.create_docente_student');
        Route::get('users_update_docente_student', 'UsersController@update_docente_student')->name('users.update_docente_student');
        Route::get('users_deleta_docente_student', 'UsersController@deleta_docente_student')->name('users.deleta_docente_student');

        //new
        Route::middleware(['role_or_permission:superadmin'])->group(function () {
            Route::get('storage-link', function () {
                Artisan::call('storage:link');
                return "link criado com sucesso";
            });

        });




        Route::middleware(['role_or_permission:superadmin|manage-enrollments'])->group(function () {

            

            #Route::resource('candidates', 'CandidatesController');
            Route::get('candidaturas', 'CandidatesController@index')->name('candidates.index');
            Route::get('candidaturas/relatorios', 'CandidatesController@relatorios')->name('candidaturas.relatorios');
            Route::get('candidaturas/relatorios/pdf/{anoletivo}', 'CandidatesController@relatoriosPDF')->name('candidaturas.relatorios.pdf');
            Route::get('candidaturas/relatorios/pdf-global/{anoletivo}', 'CandidatesController@relatoriosPDFGlobal')->name('candidaturas.relatorios.global.pdf');
            Route::get('candidaturas/create', 'CandidatesController@create')->name('candidates.create');
            Route::post('candidaturas', 'CandidatesController@store')->name('candidates.store');
            Route::get('candidaturas/{id}', 'CandidatesController@show')->name('candidates.show');
            Route::get('candidaturas/{id}/edit', 'CandidatesController@edit')->name('candidates.edit');
            Route::put('candidaturas/{id}', 'CandidatesController@update')->name('candidates.update');
            Route::delete('candidaturas/{id}', 'CandidatesController@destroy')->name('candidates.destroy');

            /* candidatura - finalista */
            Route::get('candidaturas-graduado', 'GraduadoCandidaturaController@index')->name('candidatura.graduado');
            Route::get('ajax-users-graduado', 'GraduadoCandidaturaController@ajax_graduado')->name('ajax.finalista.graduado');
            Route::get('candidatura-copy-graduado/{id}', 'GraduadoCandidaturaController@copy_graduado')->name('copy.graduado');

        });

        Route::resource('profile', 'ProfileController');
        //Routa das notificações
        Route::resource('central-notification', 'centralNotification');

        Route::get('generator_ticker', 'centralNotification@generator_ticker')->name('generator_ticker');
        Route::get('central-notification-apoio', 'centralNotification@apoio_notification')->name('apoio.notification');

        Route::get('central-notification/{id}', 'centralNotification@singleSms')->name('smsSingle');

        Route::post('apagar_notificacao', 'centralNotification@apagar_notificacao')->name('apagar_notificacao');
        Route::get('pesquisar_notificacao', 'centralNotification@pesquisar_notificacao')->name('pesquisar_notificacao');
        Route::get('marcar_estrela', 'centralNotification@marcar_estrela')->name('marcar_estrela');


        Route::group(['prefix' => 'users'], function () {
            Route::get('/link', function () {
                // echo"ola";
                Artisan::call('storage:link');
            });

            //Estudantes - Curso Especial
            Route::get('student-special-course/report/{id}','StudentSpecialCourseController@openReport')->name('student-special-course.report');
            Route::get('student-special-course/destroy/{id}','StudentSpecialCourseController@destroy')->name('student-special-course.destroy');
            Route::get('student-special-course/getStudentsBy/{lective_year}/{course}/{edition}','StudentSpecialCourseController@getStudentsBy');
            Route::get('student-special-course','StudentSpecialCourseController@index')->name('student-special-course.index');
            Route::get('student-special-course/create','StudentSpecialCourseController@create')->name('student-special-course.create');
            Route::post('student-special-course/store','StudentSpecialCourseController@store')->name('student-special-course.store');
            Route::get('student-special-course/get-classes/{course_id}/{lective_year}','StudentSpecialCourseController@getClasses');
            Route::get('student-special-course/get-students','StudentSpecialCourseController@getStudents');
            Route::get('student-special-course/list-students','StudentSpecialCourseController@listStudents');
            Route::get('student-special-course/pdf-list/{edition_id}','StudentSpecialCourseController@listPDF')->name('student-special-course.pdf');
            // Categoria Profissional
            Route::resource('categoria-profissional', 'CategoriaProfissionalController');
            Route::get('cp_ajax', 'CategoriaProfissionalController@ajax')->name('categoria-profissional.ajax');


                //Márcia
                Route::resource('regime_especial','RegimeEspecialController');
                Route::get('regime_especial_ajax','RegimeEspecialController@ajax')->name('regime_especial.ajax');
                Route::get('pdf_regime_especial','RegimeEspecialController@pdfRegimeEspecial')->name('rg-pdf');

                Route::resource('rotacao-regime-especial','RotacaoRegimeEspecialController');

                Route::get('rotacao-regime-especial-ajax','RotacaoRegimeEspecialController@ajax')->name('rotacao-regime-especial.ajax');


                Route::get('update_password', 'UsersController@update_password');

            //Grau Académico
            Route::resource('grau-academico', 'GrauAcademicoController');
            Route::get('grau-academico_ajax', 'GrauAcademicoController@ajax')->name('grau-academico.ajax');


            // Users
            Route::middleware(['role_or_permission:superadmin|manage-users|edit-own-profile'])->group(function () { //'role_or_permission:superadmin|manage-users'
                Route::resource('users', 'UsersController');
                Route::get('rh_rpa','UsersController@generateRhRpa')->name('users.rpa');
                Route::get('getCurso', 'UsersController@getcursoIndex');
                Route::get('getStudent/{id_curso}', 'UsersController@getStudent');
                Route::get('getDocente', 'UsersController@getDocente')->name('user.docents');
                Route::get('getDocenteCourse/{id_curso}', 'UsersController@getDocenteCourse');
                Route::get('getStaff', 'UsersController@getStaff')->name('users.show_user_staff');

                Route::get('user_create', 'UsersController@create_user')->name('users.create_user');
                Route::get('docente_create', 'UsersController@create_user_docente')->name('users.create_user_docente');
                Route::get('staff_create', 'UsersController@create_user_staff')->name('users.create_user_staff');
                Route::get('user_email_convert/{name}', 'UsersController@convertToEmail');

                Route::get('user_verify_change_course/{id}', 'UsersController@verifyUserInChangeCourse')->name('user.verify.change.course');

                // validar BI usuario
                Route::get('get_validation_bi/{valorBi}', 'UsersController@getValidationNewNumberBI');


                Route::get('users_ajax', 'UsersController@ajax')->name('users.ajax');
                Route::get('users_getDocente', 'UsersController@ajaxGetDocente')->name('users.getDocente');
                Route::get('users_getStaff', 'UsersController@ajaxGetStaff')->name('users.getStaff');
                Route::get('users/{id}/roles', 'UsersController@roles')->name('users.roles');
                Route::put('users/{id}/roles', 'UsersController@rolesSave')->name('users.saveRoles');
                Route::get('users/{id}/roles/ajax', 'UsersController@rolesAjax')->name('users.roles.ajax');
                Route::get('users/{id}/permissions', 'UsersController@permissions')->name('users.permissions');
                Route::put('users/{id}/permissions', 'UsersController@permissionsSave')->name('users.savePermissions');
                Route::get('users/{id}/permissions/ajax', 'UsersController@permissionsAjax')->name('users.permissions.ajax');
                Route::post('users_avatar', 'UsersController@update_avatar')->name('users.update_avatar');
                Route::get('users/{id}/generate_pdf', 'UsersController@generatePDF')->name('users.generatePDF');
                Route::get('generateUserPDF/{id}', 'UsersController@generateUserPDF')->name('users.generate.pdf');
                Route::get('generateDocentePDF', 'UsersController@generateDocentePDF')->name('users.generate.docente.pdf');
                Route::get('generateDocentePDF/{id}', 'UsersController@generateDocenteCursoPDF')->name('users.generate.docente.curso.pdf');
                Route::post('users/exists', 'UsersController@exists')->name('users.exists');
                Route::post('users/existsParameter', 'UsersController@existsParameter')->name('users.existsParameter');
                Route::post('users/existsMecanNumber', 'UsersController@existsMecanNumber')->name('users.existsMecanNumber');
                Route::post('users/disciplines', 'UsersController@coursesDisciplinesAjax')->name('users.disciplines');
            });


            // create confirmation matriculaion finalist
            Route::middleware(['role_or_permission:superadmin|manage-users'])->group(function () {
                Route::get('create-matriculation-finalist/{getyear_lectiv}', 'MatriculationConfirmationFinalitController@newConfirmation_forFinalist')->name('new.confirmation');
                Route::get('show-matriculation-listaFinalista', 'MatriculationConfirmationFinalitController@listaFinalista')->name('index.matriculation-finalista');
                Route::post('create-New-matriculation-finalist', 'MatriculationConfirmationFinalitController@createConfirmation_Matriculation')->name('create.New-matriculation-finalist');

                Route::get('ajaxListaFinalista/{id_anoLective}', 'MatriculationConfirmationFinalitController@ajaxListaFinalista');
                Route::get('ajaxListaFinalista_forYear/{year_lectivo}', 'MatriculationConfirmationFinalitController@ajaxListaFinalista_forYear');
                Route::get('delete-matriculation_finalista/{id}', 'MatriculationConfirmationFinalitController@deleteMatriculation_finalista')->name('delete.matriculation_finalista');
                Route::get('boletim-finalista/{id}', 'MatriculationConfirmationFinalitController@boletin_finalista')->name('boletim.finalista');



            });

            Route::get('candidatura_fases_ajax_lective/{id}', 'FaseCandidaturaController@ajax_list_lective')->name('fase.ajax.list.users');

            //candidates
            Route::middleware(['role_or_permission:superadmin|manage-users|manage-enrollments|candidaturas-lista-candidatos|candidaturas-anuncio'])->group(function () {


                Route::get('candidates-update/{id}', 'CandidatesController@actualizar')->name('list_candidates_ajax');


                Route::get('list-candidates', 'CandidatesController@listaCandidate')->name('list_candidates_ajax');
                Route::get('candidates_ajax', 'CandidatesController@ajax')->name('candidates.ajax');
                Route::get('candidates/validation/{passwWord}', 'CandidatesController@validate_PassWord')->name('validarpass.ajax');
                //up[1] Route::resource('candidates', 'CandidatesController');
                Route::get('candidates/email_convert/{name}', 'CandidatesController@convertToEmail');
                Route::post('candidates/disciplines', 'CandidatesController@coursesDisciplinesAjax')->name('candidates.disciplines');
                Route::get('generate-pdf/{id}', 'CandidatesController@generatePDFForCandidate')->name('candidate.generate_pdf');
                Route::get('candidates/getStudentsBy/{lective_year}', 'CandidatesController@getStudentsBy')->name('candidate.get_students');
                Route::get('candidates/get_validation_bi/{valorBi}', 'UsersController@getValidationNewNumberBI');

                // courses defaut joaquim e sedrac
                Route::post('candidates/{courses_default}', 'CandidatesController@StoreDefaultCourseCandidate')->name('candidate.courses_default');

                Route::get('excel-candidatos/{ano_lectivo}','CandidatesController@generateCandidatesGep')->name('candidatura.gep');
                //start fases candidaturas rotas sedrac
                Route::get('fases-candidaturas', 'FaseCandidaturaController@index')->name('fase-candidatura');
                Route::get('fases-candidaturas/{id}/anolectivo', 'FaseCandidaturaController@anolectivoFase')->name('fase.anolectivo');
                Route::get('lective-years-status', 'FaseCandidaturaController@index');
                Route::get('candidatura_fases_ajax_list', 'FaseCandidaturaController@ajax_list')->name('fase.candidatura.ajax.list');
                Route::get('candidatura_fases_ajax_list_users/{id}', 'FaseCandidaturaController@ajax_list_users')->name('fase.candidatura.ajax.list.users');
                Route::get('candidatura_fases_ajax_list_history/{id}', 'FaseCandidaturaController@ajax_history_users')->name('fase.candidatura.ajax.history.users');
                Route::get('lective_year_candidatura', 'FaseCandidaturaController@ajax_candidate_year')->name('fase.candidatura.ajax.get.year');
                Route::post('fases-candidaturas', 'FaseCandidaturaController@store')->name('fase.candidatura.store');
                Route::put('fases-candidaturas', 'FaseCandidaturaController@update')->name('fase.candidatura.update');


                Route::get('fases-candidaturas/{id}', 'FaseCandidaturaController@users')->name('fase.candidatura.users');
                Route::get('pdf-candidatura-historico', 'FaseCandidaturaController@generatePDF')->name('fase.candidatura.gerar');


                Route::get('/fase/curso', 'FaseCandidaturaController@getCourse')->name('fase.course');
                Route::get('/fase/turma/{id}', 'FaseCandidaturaController@getCourseTurma')->name('fase.turma');


                /* transferencia */
                Route::get('fases-candidaturas-trans', 'TransferenciaController@userTrans')->name('fase.candidatura.trans.user');
                Route::post('/fase/tranfes', 'TransferenciaController@transferencia')->name('fase.transfer.user');
                Route::post('/fase/update/{id}', 'TransferenciaController@update')->name('transferencia.up');
                Route::get('/transferencia/fase/{user_id}', 'TransferenciaController@historico')->name('transferencia.historico');

                Route::get('/escolher/cursos/{user_id}', 'TransferenciaController@course')->name('escolher.curso');
                Route::post('/definir/cursos', 'TransferenciaController@defaultCurso')->name('escolher.curso.post');

                //Claudio fase-routa
                Route::get('candidates/getStudentsByFase/{fase_id}', 'FaseCandidaturaController@getStudentsByFase')->name('candidate.get_studentsFases');
                //Fim claudio fase routa
    
                //sedrac e joaquim
    
                Route::get('matricula-incorrecta', 'MatriculationIncorretaController@index')->name('matricula.incorreta');
                Route::get('matricula-incorrecta/ajax', 'MatriculationIncorretaController@ajax')->name('matricula.incorreta.ajax');

                Route::get('matricula-number-last', 'MatriculationIncorretaController@numberMatriculation')->name('matricula.last');

                // validar BI usuario
                Route::get('candidates/get_validation_bi/{valorBi}', 'UsersController@getValidationNewNumberBI');

                //sedrac e joaquim
                Route::get('candidates/validation_candidatura/{id}', 'CandidatesController@validate_ano_candidato');
                Route::get('candidates/validation_ano/{id}', 'CandidatesController@validate_ano_lectivo');

                //ajax candidato list
                Route::get('candidates_ajax_list', 'CandidatesController@ajax_list')->name('candidates.ajax.list');

                //Ano lectivo
                Route::get('ano_candidatura', 'CandidatesController@anoLectivo')->name('candidate.ano_lectivo');
                // listar candidatura ( ano lectivo )
                Route::get('candidatura-listar', 'CandidatesController@listCandidatura')->name('candidate.list_candidatura');
                //view
                Route::get('candidatura-view/{id}', 'CandidatesController@viewCandidatura')->name('candidate.view_candidatura');
                //edit
                Route::get('candidatura-edit/{id}', 'CandidatesController@editCandidatura')->name('candidate.edit_candidatura');
                Route::put('candidatura-edit/{id}', 'CandidatesController@editStoreCandidatura')->name('candidate.edit_store_candidatura');
                //Ano lectivo Store
                Route::post('ano_lectivo-store', 'CandidatesController@anoLectivoStore')->name('candidate.anoLectivoStore');



                /* sedrac transferência */
                Route::post('fases-transferencia', 'TransferenciaController@transferenciaFase')->name('fase.transferencia');
            });
            //anuncio de vagas
            Route::middleware(['role_or_permission:superadmin|manage-enrollments|candidaturas-anuncio'])->group(function () {

                Route::get('candidates_ajax', 'CandidatesController@ajax')->name('candidates.ajax');
                Route::get('vagas/{id}/{anoLectivo}', 'anuncioVagas@ajaxVagas')->name('grade_teacher.disciplines');
                Route::resource('anuncio-vagas', 'anuncioVagas');
                Route::get('anuncio-vagas/pdf/{anoletivo}', 'anuncioVagas@anuncioPDF')->name('anunciopdf');
                Route::get('anuncio-vagas/estatistica/{anoletivo}', 'anuncioVagas@estatisticaPDF')->name('estatistica');
                Route::post('candidates/disciplines', 'CandidatesController@coursesDisciplinesAjax')->name('candidates.disciplines');
                Route::get('generate-pdf/{id}', 'CandidatesController@generatePDFForCandidate')->name('candidate.generate_pdf');
                Route::get('candidates/getStudentsBy/{lective_year}', 'CandidatesController@getStudentsBy')->name('candidate.get_students');
            });

            // Roles
            Route::middleware(['role_or_permission:superadmin|manage-roles'])->group(function () {
                Route::resource('roles', 'RolesController');
                Route::get('roles_ajax', 'RolesController@ajax')->name('roles.ajax');
                Route::get('roles/{id}/permissions', 'RolesController@permissions')->name('roles.permissions');
                Route::put('roles/{id}/permissions', 'RolesController@permissionsSave')->name('roles.savePermissions');
                Route::get('roles/{id}/permissions/ajax', 'RolesController@permissionsAjax')->name('roles.permissions.ajax');
            });

            // Permissions
            Route::middleware(['role_or_permission:superadmin|manage-permissons'])->group(function () {
                Route::resource('permissions', 'PermissionsController');
                Route::get('permissions_ajax', 'PermissionsController@ajax')->name('permissions.ajax');
            });

            // Parameters
            Route::middleware(['role_or_permission:superadmin|manage-parameters'])->group(function () {
                Route::resource('parameters', 'ParametersController');
                Route::get('parameters_ajax', 'ParametersController@ajax')->name('parameters.ajax');
                Route::get('parameters_exists', 'ParametersController@exists')->name('parameters.exists');
                Route::post('parameters_option_exists', 'ParametersController@optionExists')->name('parameters.option_exists');
            });

            // Professional States
            Route::middleware(['role_or_permission:superadmin|manage-professional-states'])->group(function () {
                Route::resource('professional-states', 'ProfessionalStatesController');
                Route::get('professional-states_ajax', 'ProfessionalStatesController@ajax')->name('professional-states.ajax');
            });



            // Professions
            Route::middleware(['role_or_permission:superadmin|manage-professions|staff_recursos_humanos'])->group(function () {
                Route::resource('professions', 'ProfessionsController');
                Route::get('professions_ajax', 'ProfessionsController@ajax')->name('professions.ajax');
            });

            // Degree Levels
            Route::middleware(['role_or_permission:superadmin|manage-degree-levels'])->group(function () {
                Route::resource('degree-levels', 'DegreeLevelsController');
                Route::get('degree-levels_ajax', 'DegreeLevelsController@ajax')->name('degree-levels.ajax');
            });

            // Parameter groups
            Route::middleware(['role_or_permission:superadmin|manage-parameter-groups'])->group(function () {
                Route::resource('parameter-groups', 'ParameterGroupsController');
                Route::get('parameter-groups_ajax', 'ParameterGroupsController@ajax')->name('parameter-groups.ajax');
                Route::get('parameter-groups/{id}/parameter_order', 'ParameterGroupsController@parameterOrder')->name('parameter-groups.parameter_order');
                Route::put('parameter-groups/{id}/save_order', 'ParameterGroupsController@saveParameterOrder')->name('parameter-groups.save_parameter_order');
                Route::get('parameter-group/order', 'ParameterGroupsController@order')->name('parameter-groups.order');
                Route::put('parameter-group/order', 'ParameterGroupsController@saveOrder')->name('parameter-groups.save_order');
            });

            // estado da mensalidade
            Route::middleware(['role_or_permission:superadmin|view_estado_mensalidade'])->group(function () {
                Route::get('matriculations-payments', 'MatriculationController@matriculationspayments')->name('matriculations-payments');
                Route::get('getMatriculations-paymentsAlectivo/{anolectivo}', 'MatriculationController@getMatriculations_paymentsAlectivo');
                Route::get('getMatriculations-paymentsgerarPdf/{anolectivo}', 'MatriculationController@getMatriculations_paymentsgerarPdf');


            });




            // Anulação de matricula
            Route::middleware(['role_or_permission:superadmin|Anular_matricula'])->group(function () {

                //feitas por Joaquim e actualizada por Kaizer
                Route::get('matriculations/anulate_matriculation', 'AnulateMatriculationController@index')->name('anulate.matriculation.index');
                Route::post('matriculations/anulate_matriculation', 'AnulateMatriculationController@store')->name('anulate.matriculation.store');

                //Route::get('matriculations/anulate_matriculation_request', 'AnulateMatriculationController@store');
                Route::get('matriculations/anulate_matriculation_ajax/{anoLective}', 'AnulateMatriculationController@getAnulateMatriculation')->name('anulate.matriculation.ajax');

                Route::get('matriculations/anulate_matriculation_restaure/{id_anulate}', 'AnulateMatriculationController@restoure_anulate')->name('anulate.matriculation.restaure');

                //sedrac - routa(nova)
                Route::get('matriculations/anulate_matriculation_finalist', 'AnulateMatriculationFinalistController@index')->name('anulate.matriculation_finalist.index');
                Route::post('matriculations/anulate_matriculation_finalist', 'AnulateMatriculationFinalistController@store')->name('anulate.matriculation_finalist.store');
                Route::get('matriculations/anulate_matriculation_finalist_ajax/{anoLective}', 'AnulateMatriculationFinalistController@getAnulateMatriculation')->name('anulate.matriculation_finalist.ajax');



            });


            // estatística dos matriculados
            Route::middleware(['role_or_permission:superadmin|estatistica_matriculados'])->group(function () {


                Route::get('turma_estatistica/{curso}/{id_anoLectivo}/{anoCurrucular}', 'estatisticaMatriculationController@turma_estatistica');
                Route::get('matriculations/estatistica', 'estatisticaMatriculationController@index')->name('estatistica.matriculation.index');
                Route::post('matriculations/estatistica/pdf', 'estatisticaMatriculationController@generateEstatistic')->name('estatistica.matriculation.generate');
                Route::get('matriculas/relatorios', 'estatisticaMatriculationController@relatorios')->name('matriculas.relatorios');
                Route::post('matriculas/relatorios/pdf', 'estatisticaMatriculationController@relatoriosPDF')->name('matriculas.relatorios.pdf');
            });


            //Import-data-user-forLEARN
            Route::middleware(['role_or_permission:superadmin'])->group(function () {

                Route::get('import-data-forlearn', 'ImportExportDataController@listSetting')->name('import-export-data');
                Route::post('send-import-data-forlearn', 'ImportExportDataController@store')->name('send-import-data');
                Route::get('import-user-ajax', 'ImportExportDataController@ListImportDataAjax')->name('import.user');

            });



            //strategy matriculations routes
            Route::middleware(['role_or_permission:superadmin'])->group(function () {

                Route::resource('configuracao-de-matricula', 'ConfigMatriculationStrategyController');
                Route::post("save-strategy-matriculation", "ConfigMatriculationStrategyController@store")->name("save-strategy-matriculation");
                Route::get("ajax-config", "ConfigMatriculationStrategyController@getStrategyMatriculationAjax")->name("matriculation.config.ajax");

                Route::get('/get-numSelected', 'ConfigMatriculationStrategyController@numDisciplinasSelected')->name('get.numSelected');
                Route::get('/get-numSelectedActive/{id}', 'ConfigMatriculationStrategyController@activar')->name('numSelectedActive');

            });






            //mundaça de curso e equivalência de curso
            Route::middleware(['role_or_permission:superadmin|gerir_equivalência|requererMudancadeCurso'])->group(function () {

                Route::resource('matriculations-equivalence', 'EquivalenceController');
                Route::resource('avaliations-equivalence', 'avaliacaoEquivalence');
                Route::get('change-course-setting', 'EquivalenceController@changeCourse')->name('setting-change-course');


                Route::get('get_students_equivalence/{course_id}/{lectiveYear}', 'avaliacaoEquivalence@getStudents');
                Route::get('get_students_disciplines/{student_id}', 'avaliacaoEquivalence@getStudentsDsiciplines');
                Route::post('equivalence-student-grade-store', 'avaliacaoEquivalence@store')->name('equivalence_student_grade.store');
                Route::post('equivalencia/apagar', 'EquivalenceController@anulate_equivalence')->name('anulate.equivalence.store');


                Route::get('get_students_course/{course_id}/{type_transference}', 'EquivalenceController@getStudentsWhereHasCourse');
                Route::get('ajaxTransferenceStudant/{lective}', 'EquivalenceController@ajaxTransfereStudent');
                Route::post('equivalence-student-store', 'EquivalenceController@equivalence_student_store')->name('equivalence_student.store');

                Route::get('transference-request', 'EquivalenceController@transferenceRequest');

                //Mudança de curso
                Route::get('requerer-mudanca-de-curso', 'changeCourseNormalController@transferenceRequest')->name('requerer_mudanca_curso.normal');
                Route::get('get_students_course_normal/{course_id}', 'changeCourseNormalController@getStudentsWhereHasCourse');
                Route::post('requerir-mudanca-de-curso-store', 'changeCourseNormalController@requerir_mudanca_de_course_student_store')->name('requerir_mudanca_course_student.store');
                Route::get('estudantes-com-pedido-de-mudanca-de-curso', 'changeCourseNormalController@studentchangeCourse')->name('estudants_change_course.index');
                Route::get('requerir-estudantes-ajax/{lective_year}', 'changeCourseNormalController@studentChangeCourseAjax')->name('requerir_student_normal.courses.change');
                Route::get('delete-students-change-course-normal/{id}', 'changeCourseNormalController@delete')->name('student_normal_courses_change.delete');
                //Fim Mudança de curso

                Route::get('equivalence-discipline-edit/{id}', 'EquivalenceController@edit')->name('EquivalenceController.edit');
                Route::post('transference-student-store', 'EquivalenceController@transference_studant_store')->name('transference_studant.store');

                Route::get('student-change', 'Equivalence2Controller@studentchangeCourse')->name('change_course.index');
                Route::get('student-change-ajax/{lective_year}', 'Equivalence2Controller@studentChangeCourseAjax')->name('student.courses.change');
                Route::get('curso-name/{id}', 'Equivalence2Controller@courseNamejax')->name('courses.name');
                Route::get('curso-disciplina', 'Equivalence2Controller@disciplinesSelect')->name('courses.disciplina.name');

                Route::post('curso-disciplina-store', 'Equivalence2Controller@disciplinaStore')->name('courses.disciplina.store');
                Route::post('curso-change-disciplina-store', 'Equivalence2Controller@changeDisciplinaStoreAjax')->name('courses.change.disciplina.store');

                Route::get('curso-change-disciplina/{id}', 'Equivalence2Controller@disciplinasChangeCourse')->name('change.courses.disciplina.list');
                Route::get('curso-change-disciplina-ajax/{course_change_id}', 'Equivalence2Controller@disciplinasChangeCourseAjax')->name('change.courses.disciplina.list.ajax');

                Route::get('courses-change-ajax/{LectiveYear}', 'EquivalenceController@changeCourseAjax')->name('courses.chage');
                Route::post('courses-change-store', 'EquivalenceController@courses_change_store')->name('courses_change.store');
                Route::delete('curso-change-disciplina/{id}', 'Equivalence2Controller@disciplinasChangeCourseDel')->name('change.courses.disciplina.del');



            });



            // Matriculation
            Route::middleware(['role_or_permission:superadmin|view-menu-matriculations'])->group(function () {
                Route::resource('matriculations', 'MatriculationController');

                Route::get('user_matriculations', 'MatriculationController@index')->name('user_matriculations');

                Route::get('matriculations_user/{id_matricula}/{id_anolectivo}', 'MatriculationController@matricula_anolectivo')->name('user_matriculations.show');

                Route::get('matriculations_ajax', 'MatriculationController@ajax')->name('matriculations.ajax');
                Route::get('matriculations/ajax_user_data/{id}', 'MatriculationController@ajaxUserData')->name('matriculations.user.ajax');
                Route::get('matriculations/ajax_pdf/{id}', 'MatriculationController@ajaxUserPdf')->name('matriculations.user.pdf');
                Route::get('matriculations/report/{id}', 'MatriculationController@openReport')->name('matriculations.report');
                Route::get('get_matriculation_list_by/{id}', 'MatriculationController@getMatriculationBy');
                Route::resource('matriculations-point', 'MatriculatioPointController');

                Route::get('excel-matriculados/{ano_lectivo}','MatriculationController@generateMatriculationGep');

                Route::get('getCursoAno/{id_curso}/{lective_year}', 'MatriculationController@getCursoAno');
                Route::get('getMatriculasCourse/{id_curso}/{lective_year}', 'MatriculationController@getMatriculasCourse');
                Route::get('getMatriculasCourseAno/{id_curso}/{id_curso_years}/{lective_year}', 'MatriculationController@getMatriculasCourseAno');
            });

            // States
            Route::middleware(['role_or_permission:superadmin|ver_menu_estado'])->group(function () {

                Route::get('states-matriculation', 'StatesController@state_matriculation')->name('states.matriculation');
                Route::get('state_matriculations_ajax/{ano}', 'StatesController@ajax_matriculation')->name('matriculation_state.ajax');


                Route::resource('states', 'StatesController');
                Route::get('states_ajax', 'StatesController@ajax')->name('states.ajax');
                Route::get('states_type', 'StatesController@types')->name('states.type');
                Route::get('states_type_index', 'StatesController@typeIndex')->name('types.index');
                Route::get('types_ajax', 'StatesController@typeAjax')->name('types.ajax');
                Route::get('state_type_create', 'StatesController@typeCreate')->name('types.create');
                Route::post('type', 'StatesController@typeStore')->name('types.store');
                Route::get('type/{id}', 'StatesController@typeShow')->name('types.show');
                Route::get('type/{id}/edit', 'StatesController@typeEdit')->name('types.edit');
                Route::put('type', 'StatesController@typeUpdate')->name('types.update');
                //Route::delete('type/{id}', 'StatesController@typeDestroy')->name('types.destroy');
                Route::resource('student_state', 'StudentStatesController');
                Route::get('student_state_ajax', 'StudentStatesController@ajax')->name('student_state.ajax');
                Route::get('states_by_id/{id}', 'StudentStatesController@getStudentState')->name('states_by_id');
                Route::get('generate_payment/{id}', 'StudentStatesController@generatePayment')->name('generatePayment');
                Route::get('schedulingState', 'StudentStatesController@indexSchedulingState')->name('indexScheduling.state');
                Route::get('schedulingState/{id}/edit', 'StudentStatesController@editSchedulingState')->name('editScheduling.state');
                Route::put('schedulingState/{id}', 'StudentStatesController@updateSchedulingState')->name('updateScheduling.state');

                Route::get('states_historic', 'StatesController@stateHistoric')->name('state_historic');
                Route::get('states_historic_ajax/{id_user}', 'StatesController@stateHistoricAjax')->name('state_historic.ajax');

                Route::get('generate-emolument/{course}/{month}/{year}', 'StatesController@generateEmolument');

                Route::get('pdf-states_historic/{user_id}', 'StatesController@pdfStates_historic');



            });



            Route::middleware(['role_or_permission:superadmin|ver_menu_estado|staff_matriculas|staff_matriculas_assistente'])->group(function () {
                Route::resource('confirmation_matriculation', 'MatriculationConfirmationController');
                Route::get('confirmation_matriculation/create/{lective_year}', 'MatriculationConfirmationController@create')->name('confirmation_matriculation.create');
                Route::get('confirmation_matriculation/apagarDadosAlunos/{idusurio}', 'MatriculationConfirmationController@apagar')->name('apagarDadosAlunos');
                Route::get('confirmations/ajax_user_data/{id}', 'MatriculationConfirmationController@ajaxUserData')->name('confirmations.user.ajax');
                Route::get('testeMatriculaUser', 'MatriculationConfirmationController@testeMatriculationBasico')->name('basico');
                
                Route::get('testeAlunos', 'MatriculationConfirmationController@testeAlunos')->name('testelunos');
                Route::get('colocar/{id_user}', 'MatriculationConfirmationController@colocar_emolumento')->name('colocar_emulumento');
                Route::get('form_actualizar', 'MatriculationConfirmationController@formulario_rotina')->name('formulario_rotina');
                Route::get('actualizar_emulumento', 'MatriculationConfirmationController@actualizar_emulumento')->name('emulumento_update');
                


                //Equivalência Matriculation Confirmation
                Route::post('confirmation_matriculation_equivalence','MatriculationConfirmationEquivalenceController@store')->name('confirmation_matriculation_equivalence.store');
                Route::get('confirmations-equivalence/{lective_year}','MatriculationConfirmationEquivalenceController@create')->name('confirmation_matriculation_equivalence.create');

                Route::get('confirmations-equivalence/ajax_user_data/{id}', 'MatriculationConfirmationEquivalenceController@ajaxUserData')
                ->name('confirmations.equivalence.ajax');

            });

          


            //studio de fotografia
            Route::middleware(['role_or_permission:superadmin|studio-photo'])->group(function () {
                Route::resource('studio-photo', 'StudioPhotografyController');
                Route::get('grade-image', 'StudioPhotografyController@grade_images_user')->name('grade_image');
                Route::get('getPicture/{id_user}', 'StudioPhotografyController@grade_images')->name('getPicture');
                Route::get('deletePicture/{id_foto}', 'StudioPhotografyController@delete_photo')->name('deletePicture');
                Route::post('Save-photo', 'StudioPhotografyController@savePhoto')->name('save_photografy_user');
            });

        


            Route::middleware(['role_or_permission:superadmin|ver_menu_estado|lista_de_matriculados|teacher_matriculation'])->group(function () {
                Route::resource('listagem-d-matriculation', 'MatriculationDisciplineListController');
                Route::post('Listagem-alunos-discipline', 'MatriculationDisciplineListController@ajaxUserDataPDF')->name('ajax_dados');
                Route::get('turma/{curso}/{id_anoLectivo}/{anoCurrucular}', 'MatriculationDisciplineListController@turma');

                 //Listagem avaliação
                 Route::get('student-evaluation-list/{type}', 'MatriculationDisciplineListController@studentEvaluationList');
                 Route::post('student-evaluation-list-pdf/{type}', 'MatriculationDisciplineListController@studentEvaluationListPdf')->name('student_evaluation_list_pdf');
                 Route::get('get_avaliacoes/{id_disciplina}/{anoLectivo}', 'MatriculationDisciplineListController@avaliacoes');
                //Listagem segunda chamada
                Route::get('listagem-s-chamada', 'MatriculationDisciplineListController@list_s_chamada');
                Route::post('listagem-s-chamada-pdf', 'MatriculationDisciplineListController@s_chamada_pdf')->name('s_chamada_pdf');
                //Listagem por turma
                Route::resource('listagem-classe-matriculation', 'MatriculationClasseListController');
                Route::post('Listagem-alunos-turma', 'MatriculationClasseListController@ajaxUserDataPDF')->name('ajax_dados_turma');
                //Seminario
                //Emarque -> Listagem por Entidade de bolsas
                Route::get('listagem-alunos-entidade', 'ScholarshipEntityClasseListController@index')->name('scholarship_entity');
                Route::post('Listagem-alunos-bolseiros', 'ScholarshipEntityClasseListController@ajaxUserDataPDF')->name('ajax_dados_turma_bolseiros');
                
                //Emarque -> Listagem de Alunos(Pagamentos por Turno)
                Route::get('alunos/taxa-por-turma', 'ListStudentPaymentTurn@index')->name('ListStudentPaymentTurn');
                Route::post('Listagem-alunos', 'ListStudentPaymentTurn@ajaxClassDataPDF')->name('ajax_dados_turma_turn');


                Route::get('getDisciplina/{id_curso}/{anoCurricular}', 'MatriculationDisciplineListController@PegarDisciplina');
                // Route::post('pegar-dados/{id_disciplina}/{id_anoLectivo}/{id_id_turma}', 'MatriculationDisciplineListController@ajaxUserDataPDF')->name('ajax_dados');
    
            });
            Route::get('cards/student/pdf/{id}', 'CardsController@student_pdf')->name('cards.student');

            Route::get('cards/all_student', 'CardsController@all_student')->name('cards.all_student');
            Route::get('cards/all_student_ajax/{class}/{curso}/{id_anoLectivo}/{anoCurricular}/', 'CardsController@all_student_ajax')->name('cards.all_student_ajax');
            Route::get('cards/edit/{data}/{lectiveyear}', 'CardsController@edit')->name('cards.edit');
            Route::post('cards/verificar', 'CardsController@verificar')->name('cards.verificar');
            Route::post('cards/report', 'CardsController@report')->name('cards.report');
            

            Route::get('cards/report_all/{lective_year}/{year}', 'CardsController@report_all')->name('cards.report_all');



        });
    }
);
