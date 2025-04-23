

  <div class="modal fade" id="CreateCursoChange" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel"></h3> 
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h3 id="TituloCreatechange"></h3>
                                  <div class="alert-warning p-2" id="AlertaModa">
                                    <label for="" id="alertMessage">Esta associação irá transferir no processo de matrícula os alunos do <b>Curso inicial</b> para o outro curso selecionado. se o estado da mudança estiver activo. </label>
                                  </div>
                                </div>
                        </div>

                        <br>

                        {!! Form::open(array('route' => 'courses_change.store','files' => true,'id'=>'formChangeCourse')) !!}

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Curso Inicial</label>
                                    {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso','required'=>'required','id'=>'coursePrimary']) }}
                                    <input type="hidden" id="InputYear" readonly name="anoLective">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Curso à transferir</label>
                                    {{ Form::bsLiveSelect('courseNew', $courses, null, ['placeholder' => 'Selecione o curso','required'=>'required','id'=>'courseNew']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group col">
                                    <label>Descrição</label>
                                    <textarea class="form-control" name="Descricao" id="" cols="15" rows="6" placeholder="Escreve uma descrição do motivo da mudança de curso"></textarea>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-6">
                                
                                <input type="checkbox" name="estado" value="1">
                                <label>Estado da definição (Marcar para activação imediata da mudança de curso)</label>
                             
                            </div>
                            
                        </div>

                        {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">    
          <button type="button" class="btn btn-success" id="modal_create_save">Guardar</button>
          <button type="button" class="btn btn-primary" id="close_modal_create">Fechar</button>
        </div>
      </div>
    </div>
  </div>