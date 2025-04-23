<?php
Route::group(
    [
    'module' => 'RH',
    'prefix' => config('app.env') === 'testing' ? 'en' : LaravelLocalization::setLocale(),
    'namespace' => 'App\Modules\RH\Controllers',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'auth',
    ]],
    function () {
        Route::group(['prefix'=>'RH'],function () {
            
                //emarq   
                Route::get("view-file/documento_userRH/{filename}", 'FileController@documento_userRH')->name('view-file.documento_userRH');
                //fim - emarq
                Route::middleware(['role_or_permission:colaborador|user_colaborador'])->group(function () {
                    Route::get('recurso_folhaPagamentoFuncionario', 'RecursoHumanoSalarioController@folhaPagamentoFuncionario'); 
                });

                Route::middleware(['role_or_permission:superadmin|gestorRH|listagem_staff'])->group(function () {
                    
                        Route::get('contrato-info/{user_id}', 'RecursoHumanoController@contratoUser')->name('contrato.info');
                        
                        Route::get('recursohumanos', 'RecursoHumanoController@index');
                        Route::get('recurso_gestaoStaff_listagem', 'RecursoHumanoController@RecursogestaoSatffLista')->name('recurso_humano.home');
                        
                        Route::get('ajax_gestaoStaff_listagem', 'RecursoHumanoController@ajaxRecursogestaoSatffLista')->name('recurso_humano.ajax_listagem');


                        Route::get('recurso_gestaoStaff', 'RecursoHumanoController@RecursogestaoSatff')->name('recurso_humano.gestaoStaff');
                        Route::get('add_funcionario', 'RecursoHumanoController@add_funcionario')->name('add_funcionario');
                        Route::get('add_colaborador', 'RecursoHumanoController@add_colaborador')->name('add_colaborador');
                        Route::get('recurso_humano-contratoTrabalho', 'RecursoHumanoController@contratoTrabalho')->name('recurso_humano.contratoTrabalho');                
                        Route::post('recurso_humano-contrato-funcionario', 'RecursoHumanoController@contrato_funcionario')->name('recurso.contrato-funcionario');
                        Route::post('recurso_humano-add-funcao-funcionario', 'RecursoHumanoController@add_funcao_funcionario')->name('recurso.add-funcao-funcionario');
                        Route::get('recurso_delete_funcaoFuncionario/{getIdFuncao}', 'RecursoHumanoController@deleteFuncaoFuncionario');

                        
                        // Route::post('recurso_humano-create-salario-fun-with-contrato', 'RecursoHumanoController@createSalarioWithContrato')->name('recurso_humano.create-salario-fun-with-contrato');     
                        
                        
                        // RESCISÂO DO CONTRATO DE TRABALHO
                        Route::get('recurso_humano-rescisoses', 'RecursoHumanoController@rescisoses')->name('recurso_humano.rescisoses');  
                        Route::post('recurso-humanos-create-rescisao', 'RecursoHumanoController@createRescisao')->name('recurso-humanos.create-rescisao');     
                        Route::get('recurso_humano-ajaxRescisao-contrato', 'RecursoHumanoController@ajaxRescisaoContrato')->name('recurso.ajaxRescisao-contrato');  
                        Route::get('recurso_ajaxCargo_rescisao_contrato/{getid_rescisoes}', 'RecursoHumanoController@ajaxCargoRescisaoContrato')->name('recurso.ajaxCargo-rescisao-contrato');  
                        Route::get('recurso_humanoRescisaoContratoAutomatico/{dataAtual}', 'RecursoHumanoController@rescisaoContratoAutomatico');  
                        
                        Route::get('recurso_rescisaoBaixando_arquivos/{arquivo}',function($arquivo){
                        return response()->download(storage_path("app/public/documento_userRH/".$arquivo));
                            //  redirect()->back();
                        });  
                        
                        Route::get('recurso_humano-funcao', 'ConfiguracoesRHController@indexFuncao')->name('recurso.funcao');
                        Route::get('recurso_humano-ajaxFunca', 'ConfiguracoesRHController@ajaxFuncao')->name('recurso.ajaxFuncao');
                        Route::get('recurso_humano-funcaoCreate', 'ConfiguracoesRHController@createFuncao')->name('recurso.Createfuncao');
                        Route::post('recurso_humano-createFuncaoRH_contrato', 'RecursoHumanoController@createFuncaoRH_contrato')->name('recurso.create-FuncaoRH_contrato');
                        Route::post('configuracoes-Edita-funcao', 'ConfiguracoesRHController@editarFuncao')->name('recurso.Edita-funcao');                
                        Route::post('configuracoes-deleteFuncao', 'ConfiguracoesRHController@deleteFuncao')->name('recurso.deleteFuncao');
                        
                        
                        // CONFIGURAÇÃO
                        Route::get('configuracoes', 'ConfiguracoesRHController@index')->name('config.recurso_humanoEvetos');
                        Route::get('configuracoes-Imposto', 'ConfiguracoesRHController@impostos')->name('config.recurso_humanoImposto');
                        Route::post('configuracoes-createImpostoRH', 'ConfiguracoesRHController@createImpostoRH')->name('create.impostoRH');
                        Route::get('configuracoes-ajaxImposto', 'ConfiguracoesRHController@ajaxImposto')->name('recurso.ajaxImposto');
                        Route::get('configuracoes-plus-imposto/{id_imposto}', 'ConfiguracoesRHController@createdYear_imposto')->name('recurso.plus-imposto');
                        Route::get('configuracoes-plus-imposto/configuracoes-ajaxYearImposto/{id_imposto}', 'ConfiguracoesRHController@ajaxYearImposto');
                        Route::post('configuracoes-createYearImposto', 'ConfiguracoesRHController@createYearImposto')->name('recurso.createYearImposto');
                        Route::post('configuracoes-impostoYearCopy', 'ConfiguracoesRHController@impostoYearCopy')->name('recurso.impostoYearCopy');

                        Route::get('configuracoes-taxa_impostos/{id_impostoYear}', 'ConfiguracoesRHController@taxa_impostos')->name('recurso.taxa_impostos');
                        Route::post('configuracoes-create-taxaImposto', 'ConfiguracoesRHController@createTaxaImposto')->name('create.taxaImposto');
                        Route::get('configuracoes-taxa_impostos/configuracoes-ajaxTaxa_impostos/{getId}', 'ConfiguracoesRHController@ajaxTaxa_impostos');
                        Route::post('configuracoes-deleteTaxaImposto', 'ConfiguracoesRHController@deleteTaxaImposto')->name('recurso.deletetaxaImposto');
                        Route::post('configuracoes-editarTaxa_imposto', 'ConfiguracoesRHController@editarTaxa_imposto')->name('recurso.editarTaxa_imposto');
                        Route::post('configuracoes-deleteImposto', 'ConfiguracoesRHController@deleteImposto')->name('recurso.deleteImposto');
                        Route::post('configuracoes-deleteImpostoYear', 'ConfiguracoesRHController@deleteImpostoYear')->name('recurso.deleteImpostoYear');
                        Route::post('configuracoes-Edita-imposto', 'ConfiguracoesRHController@editarImposto')->name('recurso.Edita-imposto');
                        Route::post('configuracoes-Edita-impostoYear', 'ConfiguracoesRHController@editarImpostoYear')->name('recurso.Edita-impostoYear');

                        // AJUDA SOBRE O RH
                        Route::get('configuracoes-recurso-humano-ajuda',function(){
                            return view('RH::configuracoes.ajudaRH.index');
                        })->name('config.recurso-humano-ajuda');

                        Route::get('ajax_recurso_users', 'RecursoHumanoController@getUsersRecurso')->name('recurso.getUsers');
                        Route::get('Getperfil_func/{id_user}', 'RecursoHumanoController@Getperfil_func')->name('recurso.Getperfil_func');
                        Route::get('ajax_users_by_role/{getRoles}', 'RecursoHumanoController@getUserByRoles');
                        Route::get('generateUserPDF/{id}', 'RecursoHumanoController@generateUserByRolePDF')->name('recurso_humano.gestaoStaff.pdf');

                        // SUBSÍDIOS
                        Route::get('configuracoes-Subsidio', 'ConfiguracoesRHController@subsidios')->name('config.recurso_humanoSubsidio');
                        Route::post('configuracoes-createSubsidioRH', 'ConfiguracoesRHController@createSubsidioRH')->name('create.subsidioRH');
                        Route::get('configuracoes-ajaxSubsidio', 'ConfiguracoesRHController@ajaxSubsidio')->name('recurso.ajaxSubsidio');
                        Route::get('configuracoes-deletedSubsidio_withImposto/{id_subsidio}', 'ConfiguracoesRHController@deletedSubsidio_withImposto')->name('recurso.deletedSubsidio_withImposto');


                        
                        Route::post('configuracoes-deleteSubsidio', 'ConfiguracoesRHController@deleteSubsidio')->name('recurso.deleteSubsidio');
                        Route::post('configuracoes-Edita-subsidio', 'ConfiguracoesRHController@editarSubsidio')->name('recurso.Edita-subsidio');



                        // CONFIGURAÇÃO -> HORAS LABORAL
                        Route::get('ajax-horas-laboral', 'RecursoHumanoController@ajaxHoraLaboral')->name('config.ajaxHoraLaboral');
                        Route::get('ajax-horas-laboral-contrato/{contrato}', 'RecursoHumanoController@ajaxHoraLaboralContrato')->name('config.ajaxHoraLaboralcontrato');
                        Route::get('create-horas-laboral', 'RecursoHumanoController@create_horas_laroral')->name('config.create_horas_laroral');
                        Route::post('store-horas-laboral', 'RecursoHumanoController@store_horas_laroral')->name('config.store_horas_laroral');
                        Route::post('edit-horas-laboral', 'RecursoHumanoController@edit_horas_laboral')->name('config.edit_horas_laboral');
                        Route::post('delet-horas-laboral/{id}', 'RecursoHumanoController@delet_horas_laboral')->name('config.delet_horas_laboral');

                });
  

            // processamento de salário
            Route::middleware(['role_or_permission:superadmin|gestorRH'])->group(function () {
                // PAGAMENTOS - RECIBOS - MENSAL
                Route::get('recurso_humano-folha_salarial_mes', 'RecursoHumanoSalarioController@folhaPagamentoMes')->name('recurso-humano.folha-pagamento-mes');   
                Route::post('recurso_humano-get-processoSalarioMes', 'ConfiguracoesRHController@getProcessoSalarioMes')->name('recurso_humano.get-processoSalarioMes');
                
                Route::resource('recurso_humano', 'RecursoHumanoSalarioController');
                Route::get('recurso_humano-processamentoSalario', 'RecursoHumanoSalarioController@processamentoSalario')->name('recurso-humano.processamentoSalario');   
                Route::get('recurso_humano-folha-pagamento-funcionario', 'RecursoHumanoSalarioController@folhaPagamentoFuncionario')->name('recurso-humano.folha-pagamento-funcionario'); 
                Route::get('recurso_humano-anular-pagamento-funcionario', 'RecursoHumanoSalarioController@anularPagamentoFuncionario')->name('recurso-humano.anular-pagamento-funcionario'); 
                Route::get('recurso_humano-folha-pagamento-banco', 'RecursoHumanoSalarioController@folhaPagamentoBanco')->name('recurso-humano.folha-pagamento-banco'); 
                
                Route::get('recurso_humano-create-banco', 'RecursoHumanoSalarioController@createBank')->name('recurso-humano.create-banco');
                Route::get('recurso_humano-get-banco', 'RecursoHumanoSalarioController@ajaxBank')->name('recurso-humano.ajax-banco'); 
                Route::post('recurso_humano-save-banco', 'RecursoHumanoSalarioController@storeBank')->name('recurso-humano.store-banco'); 
                
                
                Route::post('recurso_humano-editar-banco', 'RecursoHumanoSalarioController@editaBanco')->name('banco.editar');
                Route::get('recurso_humano-delete-banco/{id}', 'RecursoHumanoSalarioController@deleteBanco')->name('recursoHumano.deleteBanco');
                
                // criação de banco para funcionario
                Route::get('recurso_humano-user-banco', 'RecursoHumanoSalarioController@userBank')->name('recurso-humano.user-banco');
                Route::get('recurso_humano-get-user-banco', 'RecursoHumanoSalarioController@ajaxUserBank')->name('recurso-humano.ajax-user-banco'); 
                Route::post('recurso_humano-save-user-banco', 'RecursoHumanoSalarioController@storeUserBank')->name('recurso-humano.store-user-banco'); 
                Route::post('recurso_humano-save-user-banco-contrato', 'RecursoHumanoSalarioController@storeUserBankContrato')->name('recurso-humano.store-user-banco-contrato'); 
                Route::get('recurso_humano-eliminar-banco-funcionario/{id}', 'RecursoHumanoSalarioController@eliminarBancoFuncionario')->name('recurso.eliminar-banco-funcionario'); 
                Route::get('recurso_humanoa-jaxUserBankContrato/{id}', 'RecursoHumanoSalarioController@ajaxUserBankContrato')->name('recurso.jaxUserBankContrato'); 
                Route::get('recurso_humano-eliminar-banco-funcionario-contrato/{id}', 'RecursoHumanoSalarioController@eliminarBancoFunContrato')->name('recurso.eliminar-banco-funcionario-contrato'); 
                Route::get('recurso_humanos_updateAtivarBancoProcessarSalario/{id}', 'RecursoHumanoSalarioController@updateAtivarBancoProcessarSalario'); 
                Route::get('recuso-humano-validationContaBancaria-Or-validationIBAN/{numero}', 'RecursoHumanoSalarioController@validationContaBancaria'); 
               
                
                
                Route::post('recurso_humano-create-processoSalario', 'RecursoHumanoSalarioController@createProcessoSalario')->name('recurso_humano.create-processoSalario');   
                Route::post('recurso_humano-get-processoSalario', 'ConfiguracoesRHController@getProcessoSalario')->name('recurso_humano.get-processoSalario');  
                
                //Routas criadas pelo Zacario mudar  
                Route::get('recurso_humano-addSubsidioFuncionario', 'ConfiguracoesRHController@addSubsidioFuncionario')->name('recurso-humano.add-subsidio-funcionario');
                Route::post('recurso-humano-create-subsidio-contrato-func', 'ConfiguracoesRHController@createContratoSubsidioFuncionario')->name('recurso-humano.create-subsidio-contrato-func');
                Route::get('rh-ajaxContratoSubsidioFuncionario', 'ConfiguracoesRHController@ajaxContratoSubsidioFuncionario')->name('recurso.ajaxContratoSubsidioFuncionario');
                
                // getSubsidiosContrato
                Route::get('getSubsidiosContrato/{id_user}', 'ConfiguracoesRHController@getSubsidiosContrato');

                
                Route::get('rh-ajaxListaRecibosAnulados', 'RecursoHumanoSalarioController@ajaxListaRecibosAnulados')->name('recurso.ajaxListaRecibosAnulados');
                Route::get('recuros_ajaxSubsidioFuncionario/{id_funcionario}', 'ConfiguracoesRHController@recuros_ajaxSubsidioFuncionario');
                Route::get('recurso_deleteSubsidioFuncionario/{getIdSubsidio}', 'ConfiguracoesRHController@deleteSubsidioFuncionario');                
                Route::get('recuso-humano-ajaxDocentePlanoAula/{getIdUser}', 'RecursoHumanoSalarioController@ajaxDocentePlanoAula');

               
                Route::get('recurso-humano_controlePresenca', 'ConfiguracoesRHController@controlePresenca')->name('recurso-humanos.controle-presenca');
                Route::get('ajax-controlePresenca/{id}', 'ConfiguracoesRHController@ajaxcontrolePresenca')->name('config.ajaxcontrolePresenca');
                // Route::get('ajax-funcionarioTotalHoras/{id}/{contrato}', 'ConfiguracoesRHController@ajaxfuncionarioTotalHoras')->name('config.ajaxfuncionarioTotalHoras');
                Route::post('store_controlePresenca', 'ConfiguracoesRHController@store_controlePresenca')->name('config.store_controlePresenca');                
                Route::post('edit_controlePresenca', 'ConfiguracoesRHController@edit_controlePresenca')->name('config.edit_controlePresenca');
                Route::post('delete_controlePresenca/{id}', 'ConfiguracoesRHController@delete_controlePresenca');
                
                // Controle de presencia pelo catraca.
                Route::get('recurso-humano_controlePresenca_catraca', 'RecursoHumanoSalarioController@controlePresencaCatraca')->name('recurso-humanos.controle-presenca-catraca');
                Route::get('recurso-humano_ajax_controlePresenca-catraca/{user_id}/{data}', 'RecursoHumanoSalarioController@ajaxControlePresencaCatraca');
                Route::get('controlo-catraca-day-funcionario/{id_funcionario}/{data}', 'RecursoHumanoSalarioController@controlocCatraca_dayFuncionario');
                Route::get('recurso-humano-controlo-tornique-PDF/{id_funcionario}/{data}', 'RecursoHumanoSalarioController@controlo_torniquePDF');
                
                
                Route::get('intervalo-duas-horas/{hora_entrada}/{hora_saida}','RecursoHumanoSalarioController@intervalo_duas_horas');
            
            
                
                
                // Route::post('edit-horas-laboral', 'RecursoHumanoController@edit_horas_laboral')->name('config.edit_horas_laboral');
                // Route::post('delet-horas-laboral/{id}', 'RecursoHumanoController@delet_horas_laboral')->name('config.delet_horas_laboral');

            });

            // ajax ver folha de pagamento de salário.
            Route::post('recurso-humanos-pesquisaFolha-salario', 'RecursoHumanoSalarioController@PDFpesquisaFolhaSalario')->name('recurso-humanos.pesquisaFolha-salario');
            Route::post('recurso-humanos-anular_reciboVencimento', 'RecursoHumanoSalarioController@AnulauReciboVencimentoFunc')->name('recurso-humanos.anular_reciboVencimento');
            Route::get('recuso-humano-ajaxGetReciboSalario/{getIdRole_idFun}', 'RecursoHumanoSalarioController@ajaxGetReciboSalario');
            Route::get('recuso-humano-ajaxAnulauReciboSalario/{getIdRole_idFun}', 'RecursoHumanoSalarioController@ajaxAnulauReciboSalario');
            Route::get('recurso_humanos_getFolhaSalarioNotificacoes/{id_processoSalario}', 'RecursoHumanoSalarioController@getFolhaSalarioNotificacoes'); 
            Route::get('recurso_humanos_getFolhaSalarioRecibosAnulado/{id_processoSalario}', 'RecursoHumanoSalarioController@getFolhaSalarioRecibosAnulado'); 
        });


           // estatística dos recursos humano
           Route::middleware(['role_or_permission:superadmin|gerir_estatistica_rh'])->group(function () { 
            Route::get('RH/estatistica', 'EstatisticasController@index')->name('recurso-humanos.estatistica.index');
            Route::post('RH/estatistica/pdf', 'EstatisticasController@generateEstatistic')->name('recurso-humanos.estatistica.generate');
         });
         
         
            

        Route::group(['prefix' =>'api'], function() {
                Route::get('api_users/api-register', 'RhApiController@register')->name('api.register');
                Route::post('api_users/api-form-send', 'RhApiController@store')->name('api.store');
                Route::get('api_users/api-index', 'RhApiController@index')->name('api.index');
                Route::get('api_users/lista_user', 'RhApiController@lista_user_api')->name('api.lista_user');
                Route::post('api_users/api-user-delete', 'RhApiController@delete_user_api')->name('api.delete_user');
                

                //joaquim e Sedrac
                Route::put('api_users/api-update','RhApiController@RHupdate')->name('api.RHupdate');
            });
    }
);

