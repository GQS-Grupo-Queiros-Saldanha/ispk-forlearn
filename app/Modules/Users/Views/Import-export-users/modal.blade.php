<div class="modal fade" id="CreateCursoChange" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel"></h3>
            </div>
            <div class="modal-body">
                <div class="">
                    <div class="">
                        <div class="row">
                            <div class="col-12">
                                <h3 id="TituloCreatechange"></h3>
                                <div class="alert-warning p-2" id="AlertaModa">
                                    <label for="" id="alertMessage">Atenção no processo a ser feito, essa acção
                                        pode ser irreversível!</label>
                                </div>
                            </div>

                        </div>

                        <br>

                        {!! Form::open(['route' => 'courses_change.store', 'files' => true, 'id' => 'formChangeCourse']) !!}

                        <div class="row">
                            <div class="col-12">

                                <div class="form-group col-8">
                                    <select name=""  class="selectpicker" id="userTipSelect">
                                        <option >Seleciona o tipo de usuário</option>
                                        <option value="1">Estudante</option>
                                        <option value="2">Docente - Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6" id="GeralTip">
                                <div class="form-group col" id="DIVcourse">
                                    <label>Curso </label>
                                    {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required' => 'required', 'id' => 'courseImportusers']) }}
                                </div>
                                <div class="form-group col" id="DIVcourse">
                                    <label>Ano Curricular</label>
                                    <select name="" id="anoCurricular" class="form-control" required>
                                        <option value="">Selecione um ano curricular</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <div class="form-group col" id="DIVRole">
                                    <label>Cargo(s) </label>
                                    {{ Form::bsLiveSelect('roles', $roles, null, ['placeholder' => 'Selecione um cargo', 'required' => 'required', 'id' => 'rolesImportusers']) }}
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Arquivo de importação à transferir</label>
                                    <input type="file" id="jsonFileforlearn" name="jsonFileforLEARN"
                                        accept=".json, .xlsx, .xls">
                                </div>
                            </div>
                        </div>
                        <div class="row">

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
