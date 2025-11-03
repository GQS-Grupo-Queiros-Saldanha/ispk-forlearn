<?php



use App\Modules\Avaliations\Controllers\RequerimentoController;

Route::group(
    [
        'module' => 'Avaliations',
        'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
        'namespace' => 'App\Modules\Avaliations\Controllers',
        'middleware' => [
            'web',
            'localeSessionRedirect',
            'localizationRedirect',
            'auth',
        ]
    ],
    function () {
        Route::group(['prefix' => 'avaliations'], function () {

            //Emarq
            Route::get("view-file/pautas-frequencia/{filename}", "FileController@pautas_frequencia")->name('viewFile.pautas_frequencia');
            Route::get("view-file/pautas-recurso/{filename}", "FileController@pautas_recurso")->name('viewFile.pautas_recurso');
            Route::get("view-file/pautas-exame/{filename}", "FileController@pautas_exame")->name('viewFile.pautas_exame');
            Route::get("view-file/pautas-exame-especial/{filename}", "FileController@pautas_exame_especial")->name('viewFile.pautas_exame_especial');
            Route::get("view-file/pautas-exame-oral/{filename}", "FileController@pautas_exame_oral")->name('viewFile.pautas_exame_oral');
            Route::get("view-file/pautas-seminario/{filename}", "FileController@pautas_seminario")->name('viewFile.pautas_seminario');
            Route::get("view-file/pauta-final/{filename}", "FileController@pautas_final")->name('viewFile.pautas_final');
            Route::get("view-file/pautas-mac/{filename}", "FileController@pautas_mac")->name('viewFile.pautas_mac');
            Route::get("view-file/pautas-exame-extraordinario/{filename}", "FileController@exame_extraordinario")->name('viewFile.exame_extraordinario');
            Route::get("view-file/pautas-exame-tfc/{filename}", "FileController@pautas_tfc")->name('viewFile.pautas_tfc');

            //fim - Emarq

            Route::get("ajax-config", "ConfigurationController@getConfigurations")->name("avaliacao.config.ajax");

            Route::get("configuration", "ConfigurationController@index")->name("avaliacao.config");
            Route::post("config-store", "ConfigurationController@store")->name("avaliacao.config.store");
            Route::put("config-update/{id}", "ConfigurationController@update")->name("avaliacao.config.update");
            Route::delete("config-destroy/{id}", "ConfigurationController@destroy")->name("avaliacao.config.destroy");


            Route::get("calendario-folha", "CalendarioProvaHorarioController@calendario")->name("calendario.folha");

            Route::get('ajax_juris', 'CalendarioProvaHorarioController@ajax_juris')->name('ajax.juris');
            Route::get('ajax_class', 'CalendarioProvaHorarioController@ajax_class')->name('ajax.class');
            Route::get('ajax_discipline', 'CalendarioProvaHorarioController@ajax_discipline')->name('ajax.discipline');
            Route::get('ajax_calendario_prova', 'CalendarioProvaHorarioController@ajax_calendario_prova')->name('ajax.calendario_prova');
            Route::get('ajax_calendario_horario', 'CalendarioProvaHorarioController@ajax_calendario_horario')->name('ajax.calendario_horario');

            Route::delete('juri_delete', 'CalendarioProvaHorarioController@juri_delete')->name('juri.delete');
            Route::delete('calendario_prova_delete', 'CalendarioProvaHorarioController@delete')->name("calendario_prova_horario.delete");

            Route::get('search_prova', 'CalendarioProvaHorarioController@search_prova')->name('calendario_prova_horario.search');
            Route::post('search_prova_pdf', 'CalendarioProvaHorarioController@search_prova_post')->name('calendario_prova_horario.search.post');
            Route::get('search_prova_pdf', 'CalendarioProvaHorarioController@search_prova_post');

            Route::resource('calendario_prova_horario', "CalendarioProvaHorarioController");

            Route::middleware([
                'role_or_permission:superadmin|staff_forlearn|satff_gabinete_termos|manage_grade_reports|teacher|student|coordenador-curso|id_user|av_gerir_percurso_academico|av_exibir_mac|av_exibir_classificacao|
            av_exibir_exame|av_exibir_recurso|av_exibir_especial|av_exibir_tfc|av_publicar_mac|av_publicar_classificacao|
            av_publicar_exame|av_publicar_recurso|av_publicar_especial|av_publicar_tfc|av_c_lista_pauta'
            ])->group(function () {

                Route::get('pauta-geral/{pauta_id}', 'AvaliacaoController@generalPautePDF');
                Route::get('panel_avaliation', 'AvaliacaoController@showPainelAvaliation')->name('panel_avaliation');

                Route::get('panel_avaliation_tabela/{lective_year}', 'AvaliacaoController@showPainelAvaliationTabela')->name('panel_avaliation.table');
                Route::get('pauta_student_ajax/{lective_year}', 'AvaliacaoController@pautaAvaliationStudentConfigAjax')->name('pauta_student.ajax');

                Route::post('imprimirPDF_Grades', 'PautaGeralAvaliacoesController@imprimirPDF_Grades')->name('imprimirPDF_Grades');

                //ZACARIAS
                Route::get('discipline_grades_st', 'PautaGeralAvaliacoesController@discipline_grades_st')->name('discipline_grades_st');
                Route::get('discipline_grades_mac/{code}', 'PautaGeralAvaliacoesController@discipline_grades_mac')->name('discipline_grades_mac');
                Route::get('discipline_grades_coordenador', 'PautaGeralAvaliacoesController@discipline_grades_coordenador')->name('discipline_grades_coordenador');
                Route::get('getCursoCoordenador/{id_anoLectivo}/{whoIs}', 'PautaGeralAvaliacoesController@getCursoCoordenador');
                // PAUTA EXAME - RECURSO
                Route::get('discipline_exame_grades/{code}', 'PautaGeralAvaliacoesController@discipline_exame_grades')->name('discipline_exame_grades');
                Route::get('getStudentMatriculation/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{pub_print}/{code_exame}', 'PautaGeralAvaliacoesController@getStudentMatriculation');
                Route::get('getStudentGradesRecurso/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{pub_print}', 'PautaGeralAvaliacoesController@getStudentGradesRecurso');
                Route::get('discipline_recurso_grades/{code}', 'PautaGeralAvaliacoesController@discipline_recurso_grades')->name('discipline_recurso_grades');

                Route::get('getStudentGradesTFC/{id_anoLectivo}/{id_curso}/{pub_print}', 'PautaGeralAvaliacoesController@getStudentGradesTFC');
                Route::get('discipline_tfc_grades/{code}', 'PautaGeralAvaliacoesController@discipline_tfc_grades')->name('discipline_tfc_grades');

                Route::get('getStudentGradesExameEspecial/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{pub_print}', 'PautaGeralAvaliacoesController@getStudentGradesExameEspecial');
                Route::get('discipline_exame_especial_grades/{code}', 'PautaGeralAvaliacoesController@discipline_exame_especial_grades')->name('discipline_exame_especial_grades');

                Route::get('getStudentGradesExameExtraordinario/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}', 'PautaGeralAvaliacoesController@getStudentGradesExameExtraordinario');
                Route::get('discipline_exame_extraordinario_grades', 'PautaGeralAvaliacoesController@discipline_exame_extraordinario_grades');
            });

            Route::middleware(['role_or_permission:superadmin|av_config_limite'])->group(function () {


                //REcovery MAC
                Route::get('recovery-mac', 'RecoveryMacController@index')->name('recovery_mac');
                Route::get('recovery_mac_course_disciplina', 'RecoveryMacController@StudantMacDispenseOnDisciplina')->name('recovery_course_mac');
                // Fim REcovery MAC

                Route::get('pauta_student_config', 'AvaliacaoController@pautaAvaliationStudentConfig')->name('pauta_student.config');
                Route::get('pauta_student_config_create', 'AvaliacaoController@pautaAvaliationStudentConfigCreate')->name('pauta_student_config.create');
                Route::post('pauta_student_config_store', 'AvaliacaoController@pautaAvaliationStudentConfigStore')->name('pauta_student_config.store');
                Route::get('pauta_student_config_edit/{id}', 'AvaliacaoController@pautaAvaliationStudentConfigEdit')->name('pauta_student_config.edit');
                Route::post('pauta_student_config_update', 'AvaliacaoController@pautaAvaliationStudentConfigUpdate')->name('pauta_student_config.update');
                Route::get('pauta_student_config_destroy/{id}', 'AvaliacaoController@pautaAvaliationStudentConfigDestroy')->name('pauta_student_config.destroy');
            });


            Route::middleware(['role_or_permission:superadmin|av_notas_efetuar_transicao_notas|av_gerir_percurso_academico|staff_gabinete_termos|av_lancar_notas_transicao'])->group(function () {
                //Visualizar Percurso academico (notas anteriores)
                Route::resource('old_student', 'OldGradesController');

                Route::get('academic-path/{studentId}', 'OldGradesController@studentAcademicPath')->name('academic-path.percurso');
                Route::get('academic-path-imported/{studentId}', 'OldGradesController@studentAcademicPathImported')->name('academic-path-imported.percurso');

                Route::get('old_student_add_grade/{id}', 'OldGradesController@storeGrade')->name('old_student.add');
                Route::get('print/{id}', 'OldGradesController@print')->name('old_student.print');
                // Route::post('old_student','OldGradesController@store')->name('old_student.store');
                Route::post('store_past_student', 'OldGradesController@storePastStudent')->name('old_student.store_past_student');
                Route::get('old_student_get_list', 'OldGradesController@list')->name('old_student.list');
                Route::get('old_student_get_list_with_grades', 'OldGradesController@listWithGrades')->name('old_student.listWithGrades');
                Route::get('old_student_get_list_without_grades', 'OldGradesController@listWithoutGrades')->name('old_student.listWithoutGrades');


                Route::get('old_student_final_get_list', 'OldGradesController@finalList')->name('old_student_final.list');
                Route::get('old_student_final_get_list_with_grades', 'OldGradesController@finalListWithGrades')->name('old_student_final.listWithGrades');
                Route::get('old_student_final_get_list_without_grades', 'OldGradesController@finalListWithoutGrades')->name('old_student_final.listWithoutGrades');


                Route::get('past_student_add_grade', 'OldGradesController@createPastStudent')->name('old_student.pastStudent');
                Route::get('past_student_not_matriculed', 'OldGradesController@studentsNotMatriculed')->name('old_student.students_not_matriculed');
                Route::get('past_student_get_discipline/{id}', 'OldGradesController@getDisciplinesByCourse')->name('old_student.getDisciplineByCourse');
                Route::get('old_student_final_grade', 'OldGradesController@studentWithFinalCourse')->name('old_student.finalGrade');
                Route::post('old_student_store_final_grade', 'OldGradesController@storeFinalCourse')->name('old_student.storeFinalGrade');
                Route::get('old_student_get_discipline', 'OldGradesController@getDisciplinesFinalCourseByStudent')->name('old_student.getFinalCourse');
                Route::get('old_student_final_grade_store/{id}', 'OldGradesController@callViewFinalGrades')->name('old_student.call_final_grade');
            });

            Route::middleware(['role_or_permission:superadmin|av_notas_tipo_avaliacao|av_config_t_avaliacao'])->group(function () {
                //Tipo de Avaliacao
                Route::resource('tipo_avaliacao', 'TipoAvaliacaoController');
                Route::get('tipo_avaliacao_ajax', 'TipoAvaliacaoController@ajax')->name('tipo_avaliacao.ajax');
                Route::get('tipo_avaliacao_ajax_anoLestivo/{anoLectivo}', 'TipoAvaliacaoController@ajax_anoLectivo')->name('tipo_avaliacao.ajax_anoLectivo');
                Route::get('create-type_avaliation/{anoLectivo}', 'TipoAvaliacaoController@create_type');
                Route::get('tipo_avaliacao_fetch', 'TipoAvaliacaoController@fetch')->name('tipo_avaliacao.fetch');
            });

            Route::middleware(['role_or_permission:superadmin|reparacao_de_pa'])->group(function () {
                Route::post('store_grade_student_repair', 'AvaliacaoAlunoControllerNew@StoreGradePercurso')->name('store_grade_student_repair');
                Route::get('repair-academic-path', 'AvaliacaoAlunoControllerNew@addAcademicPathRepair')->name('repair-academic-path.add');
                Route::get('get_student_repair_ajax', 'AvaliacaoAlunoControllerNew@GetStudentAcademicPathRepair')->name('get_student_repair.ajax');
                Route::get('get_student_disciplines_repair_ajax', 'AvaliacaoAlunoControllerNew@GetStudentDisciplineAcademicPathRepair')->name('get_student_discipline_repair.ajax');
                Route::get('get_student_disciplines_grade_ajax', 'AvaliacaoAlunoControllerNew@GetGradePercurso')->name('get_student_disciplines_grade_ajax.ajax');
                Route::get('get_student_data', 'AvaliacaoAlunoControllerNew@get_student_data')->name('get_student_data'); //MG (Manuel Guengui)

            });



            Route::middleware(['role_or_permission:superadmin|av_notas_metricas|av_config_t_metricas'])->group(function () {
                //Duplicar as configurações das avaliações
                Route::post('duplicar-Config', 'AvaliacaoController@duplicar_avaliacao')->name('avaliation.duplicar');
                //Fim método duplicação

                //Tipo de Metrica
                Route::resource('tipo_metrica', 'TipoMetricaController');
                Route::get('tipo_metrica_ajax_anoLestivo/{anoLectivo}', 'TipoMetricaController@ajax_anoLectivo')->name('tipo_metrica.ajax_anoLectivo');
                Route::get('create-type_metrica/{anoLectivo}', 'TipoMetricaController@create_type');
                Route::get('tipo_metrica_ajax', 'TipoMetricaController@ajax')->name('tipo_metrica.ajax');
                //Metrica
                Route::resource('metrica', 'MetricaController');
                Route::get('metrica_fetch', 'MetricaController@fetch')->name('metrica.fetch');
                Route::get('tipo_metrica_fetch/{anoLectivo}', 'AvaliacaoController@fetch_tipo_metrica')->name('tipo_metrica.fetch');
                Route::delete('delete_metrica/{id}', 'MetricaController@delete_metrica')->name('delete_metrica');

                //Duplicar as configurações das avaliações
                Route::post('duplicar-Config', 'AvaliacaoController@duplicar_avaliacao')->name('avaliation.duplicar');
                //Fim método duplicação


                // Route associar a metrica com o calendario da avaliacao e o seu semestre.
                Route::get('Avaliaca_metricaCalendario/{id_semestre}/{id_metrica}/{id_avaliacao}', 'MetricaController@metricaCalendario')->name('metrica_calendario.fetch');
                // Marcia
                Route::get('Avaliaca_metricaCalendario_edit/{id_metrica}', 'MetricaController@metrica_edit')->name('metrica_edit');
                Route::post('Avaliaca_metricaCalendario_metrica_actualizar', 'MetricaController@metrica_actualizar')->name('metrica_actualizar');
                Route::post('add_metricaCalendario', 'MetricaController@ad_metricaCalendario')->name('add_metricaCalendario.registo');
                Route::get('delete_calendMetrica/{id_calendMetrica}', 'MetricaController@delete_calendMetrica')->name('calendMetrica.delete');
            });

            Route::middleware(['role_or_permission:superadmin|av_notas_avaliacao|av_config_avaliacao|av_config_calendario|'])->group(function () {
                //Avaliacao
                Route::resource('avaliacao', 'AvaliacaoController');

                Route::post('avaliacao_open', 'AvaliacaoController@avaliacaoOpen')->name('avaliacao.open');
                Route::get('avaliacao_open', 'AvaliacaoController@avaliacaoOpen');

                //Dev. Gelson Matias routa criada 04/11/2021
                Route::get('avaliacaos/school-exam-calendar/{avaliacao_id}', 'AvaliacaoController@cadastroProva_Avariocao')->name('avaliacao_data.cadastro');
                Route::get('avaliacao_av_ajax/{anoLectivo}', 'AvaliacaoController@ajax');
                Route::get('create-type/{anoLectivo}', 'AvaliacaoController@create');

                Route::get('avaliacao_metrica_fetch/{id}/{id_anoLectivo}', 'AvaliacaoController@fetch_metrica')->name('avaliacao_metrica.fetch');

                Route::get('editarMetrica_calendarizada/{id_avaliacao}/{id_metrica}/{semestre}', 'AvaliacaoController@editarMetrica_calendarizada')->name('editarMetrica_calendarizada.edit');
                Route::get('get_metrica_calendarizada_segunda_chamada/{id_metrica}/{semestre}', 'AvaliacaoController@get_calendMetrica_segundaChamada');
                Route::get('avaliacao_fetch_metricaSemestre/{id}/{id_semestre}', 'AvaliacaoController@fetch_metricaSemestre')->name('avaliacao_metrica.fetch_metricaSemestre');
                Route::get('avaliacao_metricaSemestre_calendario/{id_avaliacao}/{id_semestre}', 'AvaliacaoController@avaliacao_metricaSemestre_calendario')->name('metricaSemestre_calendario.fetch');


                Route::post('concluir_avaliacao', 'AvaliacaoController@concluir_avaliacao')->name('avaliacao.concluir_avaliacao');
                Route::get('fetch_single_avaliacao/{id}', 'AvaliacaoController@fetch_single_avaliacao')->name('avaliacao.single_fetch');
                Route::post('atualizar_avaliacao', 'AvaliacaoController@atualizar_avaliacao')->name('avaliacao.atualizar');
                Route::post('editar_metrica', 'MetricaController@editarMetrica')->name('avaliacao.editarMetrica');
                Route::post('store_metrica_sc', 'MetricaController@calendMetrica_segundaChamada')->name('avaliacao.storeCalendMetricaSC');
            });
            Route::middleware(['role_or_permission:superadmin|av_notas_planoestudo_avaliacao'])->group(function () {
                //Plano Estudo Avaliacao
                Route::resource('plano_estudo_avaliacao', 'PlanoEstudoAvaliacaoController');
                Route::get('plano_estudo_avaliacao_ajax', 'PlanoEstudoAvaliacaoController@ajax')->name('plano_estudo_avaliacao.ajax');
                Route::post('editar_spa', 'PlanoEstudoAvaliacaoController@editar_spa')->name('spa.edit');
                Route::get('study_plans_ajax/{id}', 'PlanoEstudoAvaliacaoController@studyPlanAjax')->name('study_plan.ajax');
            });
            Route::middleware([
                'role_or_permission:superadmin|av_notas_atribuir_notas|boletim_notas_aluno|av_lancar_notas|av_lancar_oas|av_lancar_notas_finalista|av_exibir_avaliacao|av_exibir_tesp|
            av_publicar_tesp|av_c_lista_pauta'
            ])->group(function () {
                //Avaliacao Aluno
                Route::resource('avaliacao_aluno', 'AvaliacaoAlunoController');

                //ZACARIAS
                //Route::get('discipline_grades_st', 'AvaliacaoAlunoController@discipline_grades_st')->name('discipline_grades_st');

                Route::get('plano_estudo_ajax', 'AvaliacaoAlunoController@studyPlanEditionAjax')->name('study_plan.ajax');
                Route::get('disciplines_ajax/{id}', 'AvaliacaoAlunoController@disciplineAjax')->name('disciplines.ajax');
                Route::get('disciplines_ajax_uc/{id}', 'AvaliacaoAlunoController@disciplineAjaxUC')->name('disciplines_uc.ajax');
                Route::get('avaliacao_ajax/{id}', 'AvaliacaoAlunoController@avaliacaoAjax')->name('avaliacao.ajax');
                Route::get('avaliacao_ajax_oa/{id}', 'AvaliacaoAlunoController@avaliacaoAjaxOA')->name('avaliacao.ajax.oa');
                Route::get('avaliacao_ajax_uc/{id}', 'AvaliacaoAlunoController@avaliacaoAjaxUC')->name('avaliacao_uc.ajax');
                Route::get('metrica_ajax/{avaliacion_id}/{discipline_id}/{course_id}', 'AvaliacaoAlunoController@metricaAjax')->name('metrica.ajax');
                Route::get('metrica_ajax_coordenador/{avaliation_id}/', 'AvaliacaoAlunoControllerNew@metricaAjaxCoordenador')->name('metrica.ajax');
                Route::post('store_final_grade', 'AvaliacaoAlunoController@storeFinalGrade')->name('store_final_grade');

                Route::get('open-pauta/{pauta_id}', 'AvaliacaoAlunoControllerNew@openPauta');
                Route::post('lock-pauta', 'AvaliacaoAlunoControllerNew@lockPauta')->name('lock-pauta');
                Route::get('historico-pauta-ajax/{pauta_id}', 'AvaliacaoAlunoControllerNew@historico_pauta_ajax')->name('historic-ajax');
                //-------------------------------------------------------------------------------------------------------------------------------//  
                Route::get('disciplines_teacher/{anolectivo}', 'AvaliacaoAlunoControllerNew@disciplina_teacher')->name('disciplines.ajax');
                Route::get('turma_teacher/{id_edicao_plain}/{anoLectivo}', 'AvaliacaoAlunoControllerNew@getTurmasDisciplina')->name('disciplinesTurmas.ajax');
                Route::get('turma_teacher_oa/{id_edicao_plain}/{anoLectivo}', 'AvaliacaoAlunoControllerNew@getTurmasDisciplinaOA')->name('disciplinesTurmasOA.ajax');
                //-------------------------------------------------------------------------------------------------------------------------------//
                // Route::get('student_ajax/{id}/{metrica_id}/{study_plan_id}/{avaliacao_id}/{class_id}', 'AvaliacaoAlunoController@studentAjax')->name('student.ajax');

                Route::get('student_ajax/{id}/{metrica_id}/{study_plan_id}/{avaliacao_id}/{class_id}/{id_anoLectivo}', 'AvaliacaoAlunoControllerNew@studentAjax')->name('student.ajax');
                Route::get('student_ajax_oa_new/{id}/{metrica_id}/{study_plan_id}/{avaliacao_id}/{class_id}/{id_anoLectivo}/{numero_prova}', 'AvaliacaoAlunoControllerNew@studentAjaxOA_new')->name('studentAjaxOA_new.ajax');

                Route::get('mac_pdf/{id}/{metrica_id}/{study_plan_id}/{avaliacao_id}/{class_id}/{id_anoLectivo}', 'PautaMacController@mac_pdf')->name('mac_pdf');
                Route::get('seg', 'PautaMacController@sc');
                Route::get('student_ajax_oa/{id}/{study_plan_id}/{avaliacao_id}/{class_id}/{oa}', 'AvaliacaoAlunoController@studentOAAjax')->name('student.oa.ajax');
                Route::get('metrica_ajax_oa/{id}', 'AvaliacaoAlunoController@metricaAjaxOA')->name('metrica.ajax');
                Route::get('student_grades_ajax/{id}', 'AvaliacaoAlunoController@studentGradesAjax')->name('student_grades.ajax');
                Route::get('student_ajax_uc/{discipline_id}/{class_id}', 'AvaliacaoAlunoController@studentAjaxUC')->name('student_ajax.uc');
                //Visualizar Avaliação Aluno
                Route::get('show_grades_ajax/{avaliacao_id}/{discipline_id}/{stdp_edition}/{classes}', 'AvaliacaoAlunoController@showStudentGradesAjax')->name('show_grades.ajax');
                Route::post('concluir_notas', 'AvaliacaoAlunoHistoricoController@store')->name('concluir.notas');
                Route::get('gerar_classificacao', 'AvaliacaoAlunoHistoricoController@gerarClassificacao')->name('gerar.classificacao');
                Route::post('gerar_classificacao', 'AvaliacaoAlunoHistoricoController@storeClassificacao')->name('store.gerarClassificacao');
                Route::get('study_plan_edition_closed', 'AvaliacaoAlunoHistoricoController@studyPlanEditionClosedAjax');

                //Visualizar Nota Final do Estudante
                //Routa pauta de cada avaliacao com suas metricas.
                Route::get('show_final_grades', 'PautaFinalController@getStudentFinalGrades');
                Route::get('getDocenteDisciplina/{anoLectivo}', 'PautaFinalController@getDocenteDisciplina');
                Route::get('pautaTurma_teacher/{id_edicao_plain}/{anoLectivo}', 'PautaFinalController@getTurmasDisciplina')->name('disciplinesTurmas.ajax');
                Route::get('pautaTurma_teacher_metricas/{id_edicao_plain}/{anoLectivo}', 'PautaFinalController@getTurmasDisciplina_metricas')->name('disciplinesTurmas_.ajax');
                Route::get('show_final_grades_ajax_pdf/{stdp_edition}/{class_id}/{discipline_id}/{id_metrica}/{anolectivo}', 'PautaFinalController@generatePDF');
                Route::get('show_final_grades_ajax_pdf_OA/{stdp_edition}/{class_id}/{discipline_id}/{id_metrica}/{anolectivo}/{valor_oa}', 'PautaFinalController@generatePDF_Oas');
                Route::get('pautaTurma_teacher_getStudent/{id_plano}/{id_turma}/{anolectivo}/{id_metrica}', 'PautaFinalController@getStudentNotas');
                Route::get('pautaTurma_getAvaliacaoAO/{id_metrica}/{id_turma}/{id_plano}/{ano_lectivo}/{valor_oa}', 'PautaFinalController@getAvaliacaoAo_student');



                // Routa para avaliação para os finalistas
                Route::get('avaliacao-finalista', 'AvaliacaoAlunoFinalistaController@index')->name('avaliacao.finalista');
                Route::get('avaliacao-getFinalistas/{id_curso}/{id_anolectivo}/{id_metrica}', 'AvaliacaoAlunoFinalistaController@getEstudent_finalist_courso');
                Route::get('avaliacao-getMetrica/{id_anolectivo}', 'AvaliacaoAlunoFinalistaController@getMetrica_lective_year');
                Route::post('nota-avaliacao-finalista', 'AvaliacaoAlunoFinalistaController@notaAvaliacaoFinalista')->name('nota.avaliacaoFinalista');




                // Routa da pauta final Geral das avaliacoes
                Route::get('Show_pautaGeralFinal', 'PautaGeralAvaliacoesController@index');
                Route::get('gerentePDF_pautaFinal', 'PautaFinalController@gerentePDF_pautaFinal');
                Route::get('getCurso/{id_anoLectivo}/{whoIs}', 'PautaGeralAvaliacoesController@getCurso');
                Route::get('getDiscipline/{id_anoLectivo}/{anoCurso_id_Select}/{arrayCurso}/{whoIs}', 'PautaGeralAvaliacoesController@getDiscipline');
                Route::get('getTurma/{id_anoLectivo}/{id_curso}/{whoIs}', 'PautaGeralAvaliacoesController@getTurma');
                //Routa para publicar a Pauta pelo coordenador e quem tiver a permissão.
                Route::post('publisher_final_grade', 'PautaGeralAvaliacoesController@publisher_final_grade')->name('publisher_final_grade');
                Route::post('publisher_final_grade_tfc', 'PautaGeralAvaliacoesController@publisher_final_grade_tfc')->name('publisher_final_grade_tfc');
                //Fim comentário C.Kaizer
                Route::get('getMenuAvaliacoesDisciplina/{id_turma}/{ano_lectivo}/{id_curso}/{id_disciplina}/{ano_curso}/{periodo_disciplina}', 'PautaGeralAvaliacoesController@getMenuAvaliacoesDisciplina');
                Route::get('getStudentNotasPautaFinal/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{tipo_pauta}/{pub_print}', 'PautaGeralAvaliacoesController@getStudentNotasPautaFinal');
                Route::get('getPautaPublicar/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{tipo_pauta}', 'PautaGeralAvaliacoesController@getPautaPublicar');


                Route::get('discipline_grades_seminario/{code}', 'PautaGeralAvaliacoesController@discipline_grades_seminario')->name('discipline_grades_seminario');
                Route::get('getStudentNotasPautaSeminario/{id_anoLectivo}/{id_curso}/{Turma_id_Select}/{id_disciplina}/{tipo_pauta}/{pub_print}', 'PautaGeralAvaliacoesController@getStudentNotasPautaSeminario');
                Route::get('getStudentCourse/{id_curso}/{id_lectiveyear}', 'PautaGeralAvaliacoesController@getStudentCourse');


                Route::get('show_pea_with_grades', 'AvaliacaoAlunoController@getPEAWithGrades');
                Route::get('show_final_grades_ajax/{stdp_edition}/{class_id}/{discipline_id}', 'AvaliacaoAlunoController@getFinalGrades')->name('show_final_grades.ajax');
                Route::get('show_partial_grades_ajax_pdf/{avaliacao_id}/{discipline_id}/{stdp_edition}/{classes}', 'AvaliacaoAlunoController@generatePartialPDF');
                //Visualizar Nota Final Sumario
                Route::get('show_summary_grades', 'AvaliacaoAlunoController@getStudentSummaryGrades');
                Route::get('show_summary_grades_ajax/{stdp_edition}/{discipline_id}', 'AvaliacaoAlunoController@getSummaryGrades')->name('show_summary_grades.ajax');
                //Atribuir notas para OA
                Route::get('oa', 'AvaliacaoAlunoController@setPautaOA');
                Route::get('other_avaliations', 'AvaliacaoAlunoController@AddOAGrades');
                Route::post('other_avaliations', 'AvaliacaoAlunoControllerNew@StoreOAGrades')->name('other_avaliations.store');
                //Publicar notas das metricas
                Route::get('publish_metric', 'AvaliacaoAlunoController@showPublishMetricForm');
                Route::post('publish_metric', 'AvaliacaoAlunoController@publishMetricGrade')->name('publish.metric');
                //Atribuir notas de disciplinas sem avaliacoess
                Route::get('atribuir_notas_uc', 'AvaliacaoAlunoController@addUCGrades')->name('UCGrades.create');
                Route::post('atribuir_notas_uc', 'AvaliacaoAlunoController@storeUCGrades')->name('UCGrades.store');
            });

            //Avaliacao Aluno
            Route::resource('avaliacao_aluno_new', 'AvaliacaoAlunoControllerNew');


            //Avaliacao estatistica
            Route::middleware(['role_or_permission:superadmin|av_gerir_percurso_academico|staff_gabinete_termos|Chefe_do_gabinete_de_termos|coordenador-curso|av_estatisticas_candidatos|av_estatisticas_relatorios|av_estatisticas_geral|av_estatisticas_graduados|av_estatisticas_anual'])->group(function () {
                Route::resource('avaliacao-estatistica', 'AvaliacaoEstatisticaController');

                Route::get('estatistica-geral-percurso', 'AvaliacaoEstatisticaController@filter_Pauta');

                Route::get('PegarDisciplina/{id_cursos}', 'AvaliacaoEstatisticaController@PegarDisciplina');
                Route::get('PegarAnoCurricular/{id_cursos}', 'AvaliacaoEstatisticaController@PegarAnoCurricular');
                Route::get('PegarDisciplinaAnoCurricular/{id_cursos}/{anosCurricular}/{anoLectivo}', 'AvaliacaoEstatisticaController@PegarDisciplinasAnoCurricular');
                Route::post('Generate_estatistic', 'AvaliacaoEstatisticaController@generateEstatistic')->name('generate_estatistic');

                Route::post('Generate_estatistic_geral', 'AvaliacaoEstatisticaController@generateEstatistic_geral')->name('generate_estatistic_geral');
                Route::post('Generate_estatistic_anual', 'AvaliacaoEstatisticaController@generateEstatisticAnual')->name('generate_estatistic_anual');
                Route::post('Generate_estatistic_candidato', 'AvaliacaoEstatisticaController@generateEstatistiCandidato')->name('generate_estatistic_candidato');

                //graduados
                Route::get('estatistica-anual', 'AvaliacaoEstatisticaController@anual');
                Route::get('estatistica-candidato', 'AvaliacaoEstatisticaController@candidato');
                Route::get('estatistica-graduado', 'AvaliacaoEstatisticaController@graduado');
                Route::get('PegarDisciplinasAnoCurricularGraduado/{id_cursos}/{anoLectivo}', 'AvaliacaoEstatisticaController@PegarDisciplinaGraduado');
                Route::post('Generate_estatistic_graduado', 'AvaliacaoEstatisticaController@generateEstatistic_graduado')->name('generate_estatistic.graduado');
                //fim graduados

            });

            // Requerimento
            Route::middleware(['role_or_permission:gerir_requerimento'])->group(function () {

                // Route::get('requerimento_article', 'RequerimentoController@request_articles'); 


                Route::get('requerimento', 'RequerimentoController@index')->name('requerimento.index');
                Route::get('requerimento_doc', 'RequerimentoController@doc')->name("requerimento_doc");
                Route::get('requerimento_ajax/{id}', 'RequerimentoController@ajax')->name("requerimento_ajax");
                Route::get('requeriment_discipline/{id_anoLectivo}/{anoCurso_id_Select}/{arrayCurso}', 'PautaGeralAvaliacoesController@getDiscipline');
                Route::get('requerimento_articles/{id}', 'RequerimentoController@getUserArticle');
                Route::get('requerimento_articles_cerimonia/{id}', 'RequerimentoController@getUserArticleCerimonia');
                Route::get('get_exam_info/{exam}/{student_id}', 'RequerimentoController@getExamInfoBy');
                Route::get('my_articles/{type}', 'RequerimentoController@my_articles')->name('my_articles');
                Route::get('matriculation_requerimento/{ano}', 'RequerimentoController@matriculation')->name('requerimento.matriculation');
                Route::get('store_doc/{dados}', 'RequerimentoController@store_doc')->name('requerimento.store_doc');
                Route::get('requerimento_merito', 'RequerimentoController@merito')->name("requerimento_merito");
                Route::get('requerimento_cerimonia', 'RequerimentoController@cerimonia')->name("requerimento_cerimonia");
                Route::get('store_doc_merito/{dados}', 'RequerimentoController@store_doc_merito')->name('requerimento.store_doc_merito');
                Route::post('store_doc_cerimonia', 'RequerimentoController@store_doc_cerimonia')->name('requerimento.store_doc_cerimonia');
                // Route::get('index', 'RequerimentoController@filter_Pauta')->name('generate_estatistic_geral'); 
                Route::post('delete_doc', 'RequerimentoController@destroy')->name('requerimento.delete_doc');
                Route::post('updated_word', 'RequerimentoController@updated_word')->name('requerimento.updated_word');
                Route::get('get_word/{word}', 'RequerimentoController@get_word')->name('requerimento.get_word');
                Route::get('requerimento_folha/{number}', 'RequerimentoController@get_folha')->name("requerimento_folha");

                // Routas ara o registro de outorga
                Route::get('requerimento/registo_outorga', 'RequerimentoController@registo_outorga')->name("registo_outorga");
                Route::get('requerimento_registo/{number}', 'RequerimentoController@get_registo')->name("requerimento_registo");
                Route::post('requerimento_registo/store', 'RequerimentoController@requerimento_registo_store')->name('requerimento_registo_store');

                // Mundança de turma
                Route::get('requerimento/mudanca_turma', 'RequerimentoController@mudanca_turma')->name("mudanca_turma");
                Route::get('requerimento/get_students_matriculation/{lective_year}', 'RequerimentoController@get_students_matriculation')->name("get_students_matriculation");
                Route::get('requerimento/studant_get_year/{matriculation_id}', 'RequerimentoController@studant_get_year')->name("studant_get_year");
                Route::get('requerimento/get_classes/{year}/{lective_year}/{matriculation_id}/{turno}', 'RequerimentoController@get_classes')->name("get_classes");
                Route::post('requerimento/mudanca_turma_store', 'RequerimentoController@mudanca_turma_store')->name('mudanca_turma_store');





                //Defesa
                Route::get('requerimento/create_defesa/{type}', 'RequerimentoController@create_defesa')->name("create_defesa");
                Route::post('requerimento_defesa/store', 'RequerimentoController@defesa_store')->name('defesa_store');
                Route::get('requerimento/getFinalists/{course_id}/{lective_year_matriculation}', 'RequerimentoController@get_finalists');

                //Cartão de estudante
                Route::get('requerimento/create_student_card', 'RequerimentoController@createStudentCard')->name("create_student_card");
                Route::post('requerimento_student_card/store', 'RequerimentoController@student_card_store')->name('student_card_store');


                // geral matriculados
                Route::get('requerimento/create_requerimento/{codev}', 'RequerimentoController@createRequerimento')->name("create_requerimento");
                Route::post('student_requerimento_store', 'RequerimentoController@student_requerimento_store')->name('student_requerimento_store');

                //Solicitação de horário
                Route::get('requerimento/create_student_schedule', 'RequerimentoController@createStudentSchedule')->name("create_student_schedule");
                Route::post('requerimento_student_schedule/store', 'RequerimentoController@student_schedule_store')->name('student_schedule_store');
                
                //Solicitação de Revisão de Prova
                Route::get('/requerimento/solicitacao_revisao_prova', [RequerimentoController::class, 'solicitacao_revisao_prova'])->name('requerimento.solicitacao_revisao_prova');
                Route::get('/requerimento/getEstudante/{course_id}/{lective_year}', [RequerimentoController::class, 'getEstudante'])->name('requerimento.getEstudante');
                Route::get('/requerimento/getDisciplinas/{student_id}/{lective_year}/{course_id}',[RequerimentoController::class, 'getDisciplinas'])->name('requerimento.getDisciplinas');
                Route::post('/requerimento/solicitacao_revisao_prova_store', [RequerimentoController::class, 'solicitacao_revisao_prova_store'])->name('requerimento.solicitacao_revisao_prova_store');
                
                //Defesa extraordinaria
                Route::get('/requerimento/solicitacao_defesa_extraordinaria', [RequerimentoController::class, 'solicitacao_defesa_extraordinaria'])->name('requerimento.solicitacao_defesa_extraordinaria');
                Route::get('/requerimento/getEstudante_extraordinario/{course_id}/{lective_year}', [RequerimentoController::class, 'getEstudante_extraordinario'])->name('requerimento.getEstudante_extraordinario');
                Route::get('/requerimento/getDisciplinas_extraordinaria/{student_id}/{lective_year}/{course_id}',[RequerimentoController::class, 'getDisciplinas_extraordinaria'])->name('requerimento.getDisciplinas_extraordinaria');
                Route::post('/requerimento/solicitacao_solicitacao_defesa_extraordinaria_store', [RequerimentoController::class, 'solicitacao_solicitacao_defesa_extraordinaria_store'])->name('requerimento.solicitacao_solicitacao_defesa_extraordinaria_store');
                
                //Convite
                Route::get('/requerimento/solicitacao_convite', [RequerimentoController::class, 'solicitacao_convite'])->name('requerimento.solicitacao_convite');
                Route::get('/requerimento/create_convite', [RequerimentoController::class, 'create_convite'])->name('requerimento.create_convite');
                
                //TFC
                Route::get('requerimento/create_student_tfc', 'RequerimentoController@createStudentTfc')->name("create_student_tfc");
                Route::post('requerimento_student_tfc/store', 'RequerimentoController@student_tfc_store')->name('student_tfc_store');

                Route::get('pp', 'RequerimentoController@two_c');
            });
            //Etianete Reepson
            //Solicitação de estágio
            Route::middleware(['auth'])->group(function () {
                Route::get('requerimento/solicitacao/{type}', 'RequerimentoController@solicitacao_estagio')->name('solicitacao_estagio');
                Route::post('requerimento/solicitacao_estagio', 'RequerimentoController@solicitacao_estagio_store')->name('solicitacao_estagio_store');
                Route::get('requerimento/solicitacao/get-students-by-course/{id}', 'RequerimentoController@getStudentsByCourse')->name('getStudentsByCourse');
            });

            Route::middleware(['role_or_permission:superadmin|student|teacher|staff_forlearn'])->group(function () {
                Route::resource('avaliations', 'AvaliationsController');
                //Route::get('tipo_avaliacao_ajax, TipoAvaliacaoController@ajax')->name('tipo_avaliacao.ajax');
                //Estudante visualizar notas
                Route::get('grade', 'AvaliacaoAlunoController@studentGrade');
                Route::get('show_grade/{class_id}/{discipline_id}', 'AvaliacaoAlunoController@showGrade')->name('show.grade');
                Route::get('getStudentByCourse/{course_id}/{id_anolectivo}', 'AvaliacaoAlunoController@getStudentsByCourse');

                Route::get('getGradesByStudent/{student_id}/{id_anolectivo}/{course_id}', 'AvaliacaoAlunoController@getGradeByStudent');
                Route::get('getDisciplinesByStudent/{student_id}', 'AvaliacaoAlunoController@getDisciplinesByStudent');
                Route::get('getGradesByDiscipline/{student_id}/{discipline_id}', 'AvaliacaoAlunoController@getGradesByDiscipline');
                Route::get('print-grades-student/{student_id}', 'AvaliacaoAlunoController@printGradeStudent')->name('student.generatePDF');
                Route::get('show_grade_by_year/{year}', 'AvaliacaoAlunoController@showGradeByYear')->name('show.grade.year');
            });


            //Relatorios de Notas (verificar qual professor lançou ou nao nota)
            Route::middleware(['role_or_permission:superadmin|manage_grade_reports'])->group(function () {
                Route::get('grade_reports', 'ReportsController@index');
                Route::get('get_teachers_with_grades', 'ReportsController@allTeachersWithGrades')->name('getTeachers.ajax');
                Route::get('get_all_courses', 'ReportsController@getAllCourses');
                Route::get('get_all_departments', 'ReportsController@getAllDepartments');
                Route::get('get_teachers_by_course/{id}', 'ReportsController@searchByCourse')->name('getTeachersBy.course');
                Route::get('get_teachers_by_departments/{id}', 'ReportsController@searchByDepartments')->name('getTeachersBy.departments');

                Route::get('back', function () {
                    return redirect()->route('users.index');
                })->name('back.page');
            });

            //marcacao de exames
            Route::middleware(['role_or_permission:superadmin|staff_gabinete_termos|gerir_requerimento|av_requerer_exame'])->group(function () {
                Route::get('schedule_exam', 'ScheduleExamController@index')->name('schedule_exam.index');
                Route::get('list_courses', 'ScheduleExamController@listCourses');
                Route::get('get_students_where_has/{exam}/{course_id}/{lective_year}', 'ScheduleExamController@getStudentsWhereHas');
                Route::get('get_exam_info_by/{exam}/{student_id}/{lectiveYear}', 'ScheduleExamController@getExamInfoBy');
                Route::post('schedule_exam_store', 'ScheduleExamController@store')->name('schedule_exam.store');
            });
            Route::get('fix', 'ScheduleExamController@fix');
            Route::get('get-metricas-segunda-chamada/{lective_year}','ScheduleExamController@getMetricasSegundaChamada');

            //Rota que return o percurso academico / criado pelo Marcos
            Route::get('percursoAcademico/{id}', 'AvaliacaoAlunoHistoricoController@show')->name('percursoAcademico');
            //Route::get('percursoPDF', 'AvaliacaoAlunoHistoricoController@percursoAjax')->name('percursoPDF');
            Route::get('percursoAcademico/academic-path/{id}', 'OldGradesController@studentAcademicPercurso')->name('academic-path');


            Route::middleware(['role_or_permission:superadmin|av_gerir_percurso_academico|staff_gabinete_termos'])->group(function () {
                Route::get('curricular_path', 'AvaliacaoAlunoHistoricoController@curricularPath');
                Route::get('curricular_path_course/{id_aluno}', 'AvaliacaoAlunoHistoricoController@curricularPathGetCourses');
                Route::get('curricular_path_students/{id}', 'AvaliacaoAlunoHistoricoController@curricularPathGetStudents');

                //ZACARIAS LOCALIZAR PERCUSO 
                Route::get('curricular_path_grade', 'AvaliacaoAlunoHistoricoController@curricularPathGrade');
                Route::get('curricular_path_pauta', 'AvaliacaoAlunoHistoricoController@curricularPathGetPauta');
                Route::get('curricular_path_pauta_students/{id}', 'AvaliacaoAlunoHistoricoController@curricularPathGetPautaStudents');
                Route::get('student_academic_path/{id}', 'AvaliacaoAlunoHistoricoController@getStudentPercursoAcademicNotas');
            });
            Route::middleware(['role_or_permission:superadmin|av_gerir_percurso_academico'])->group(function () {
                Route::resource('school-exam-calendar', 'CalendarioProvaController');
                Route::get('calendarie/getCreate/{id_anoLectivo}', 'CalendarioProvaController@getCreate')->name('getCreate.create');
                Route::get('calendarie/getSCalendarie/{lectiveYear}', 'CalendarioProvaController@getCalendarieYear');
                Route::get('calendarie', 'CalendarioProvaController@ajaxCalendarie')->name('calendarie.ajax');
            });

            Route::middleware(['role_or_permission:superadmin|av_gerir_percurso_academico'])->group(function () {
                Route::get('percurso_task', 'TaskPercursoController@index')->name('percurso_task.index');
                Route::get('percurso_painel', 'TaskPercursoController@painel')->name('percurso_task.painel_task');
                Route::get('percurso_task/estudantes_ajax/{id}', 'TaskPercursoController@ajax');
                Route::get('percurso_task/estudantes_ajax_last/{id}', 'TaskPercursoController@ajax_last');
                Route::post('percurso_task/show', 'TaskPercursoController@show')->name("percurso_task.show");
                Route::get('percurso_task/recicle/{id}', 'TaskPercursoController@recicle')->name("percurso_task.recicle");
                Route::post('percurso_task/restaurar', 'TaskPercursoController@restaurar')->name("percurso_task.restaurar");
                Route::post('percurso_task/delete', 'TaskPercursoController@destroy')->name("percurso_task.delete");
            });

            Route::middleware(['role_or_permission:superadmin|student|boletim_notas_aluno'])->group(function () {
                Route::get('discipline_boletimNotas', 'PautaGeralAvaliacoesController@discipline_boletimNotas');
                Route::get('getStudent_boletimNotas/{id_anoLectivo}/{id_curso}/{student}', 'PautaGeralAvaliacoesController@getStudent_boletimNotas');
                Route::get("unpub-grades", "PautaGeralAvaliacoesController@unpublishedGrades");
            });


            Route::middleware(['role_or_permission:teacher_matriculation'])->group(function () {
                Route::get('matriculations_student', 'PautaFinalController@docent_disciplines')->name('metrica.ajax');
            });
        });
    }
);
