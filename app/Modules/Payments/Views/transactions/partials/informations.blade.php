                        <div class="row" hidden id="group1">
                            <div class="col-3 pb-3">
                                {{ Form::bsText('transaction_value_1', null, ['onkeypress'=>'somenteNumeros(this);', 'onkeydown'=>'somenteNumeros(this);', 'onkeyup'=>'somenteNumeros(this);', 'placeholder' => 'Digite o montante'], ['label' => __('Payments::requests.value')]) }}
                                <small style="font-weight: bold" id="valor2" class="form-text text-muted pt-0 mt-0 pl-3"></small>
                                
                            </div>
                            <div class="col-3">
                                {{ Form::bsDate('transaction_fulfilled_at_1', null, ["id" => "date_1",'placeholder' => __('Payments::requests.fulfilled_at'), 'max' => date('Y-m-d')], ['label' => "Pago a"]) }}
                            </div>
                            <div class="col-3">
                                <div class="form-group col mb-2">
                                    <label>@lang('Payments::banks.bank')</label>
                                    {{ Form::bsLiveSelect('bank_1', $banks, null, ['id'=> "bank_1", 'placeholder' => '']) }}
                                </div>
                            </div>
                            <div class="col-3">
                                {{ Form::bsText('reference_1', null, ['id' => "reference_1", 'class'=>'referencia-config'],['label' => 'Referência']) }}
                            </div>
                            </div>

                            <div class="row" hidden id="group2">
                                <div class="col-3 pb-3">
                                    {{ Form::bsText('transaction_value_2', null, ['onkeypress'=>'somenteNumeros(this);', 'onkeydown'=>'somenteNumeros(this);', 'onkeyup'=>'somenteNumeros(this);','placeholder' => 'Digite o montante'], ['label' => __('Payments::requests.value')]) }}
                                    <small style="font-weight: bold" id="valor3" class="form-text text-muted pt-0 mt-0 pl-3"></small>
                                    
                                </div>
                                <div class="col-3">
                                    {{ Form::bsDate('transaction_fulfilled_at_2', null, ['id' => 'data_2', 'placeholder' => "Pago a", 'max' => date('Y-m-d')], ['label' => "Pago a"]) }}
                                </div>
                                <div class="col-3">
                                    <div class="form-group col mb-2">
                                        <label>@lang('Payments::banks.bank')</label>
                                        {{ Form::bsLiveSelect('bank_2', $banks, null, ['id' => "bank_2",'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-3">
                                    {{ Form::bsText('reference_2', null,["id" => "reference_2", 'class'=>'referencia-config'], ['label' => 'Referência ']) }}
                                </div>
                            </div>


                            <div class="row" hidden id="group3">
                                <div class="col-3 pb-3">
                                    {{ Form::bsText('transaction_value_3', null, ['onkeypress'=>'somenteNumeros(this);', 'onkeydown'=>'somenteNumeros(this);', 'onkeyup'=>'somenteNumeros(this);','placeholder' => 'Digite o montante', ], ['label' => __('Payments::requests.value')]) }}
                                    <small style="font-weight: bold" id="valor4" class="form-text text-muted pt-0 mt-0 pl-3"></small>
                                    
                                </div>
                                <div class="col-3">
                                    {{ Form::bsDate('transaction_fulfilled_at_3', null, ['id' => 'data_3', 'placeholder' => "Pago a", 'max' => date('Y-m-d')], ['label' => "Pago a"]) }}
                                </div>
                                <div class="col-3">
                                    <div class="form-group col mb-2">
                                        <label>@lang('Payments::banks.bank')</label>
                                        {{ Form::bsLiveSelect('bank_3', $banks, null, ['id' => "bank_3",'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-3">
                                    {{ Form::bsText('reference_3', null,["id" => "reference_3", 'class'=>'referencia-config'], ['label' => 'Referência ']) }}
                                </div>
                            </div>

                            <div class="row" hidden id="group4">
                                <div class="col-3 pb-3">
                                    {{ Form::bsText('transaction_value_4', null, ['onkeypress'=>'somenteNumeros(this);', 'onkeydown'=>'somenteNumeros(this);', 'onkeyup'=>'somenteNumeros(this);','placeholder' => 'Digite o montante', ], ['label' => __('Payments::requests.value')]) }}
                                    <small style="font-weight: bold" id="valor5" class="form-text text-muted pt-0 mt-0 pl-3"></small>
                                    
                                </div>
                                <div class="col-3">
                                    {{ Form::bsDate('transaction_fulfilled_at_4', null, ['id' => 'data_4', 'placeholder' => "Pago a", 'max' => date('Y-m-d')], ['label' => "Pago a"]) }}
                                </div>
                                <div class="col-3">
                                    <div class="form-group col mb-2">
                                        <label>@lang('Payments::banks.bank')</label>
                                        {{ Form::bsLiveSelect('bank_4', $banks, null, ['id' => "bank_4",'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-3">
                                    {{ Form::bsText('reference_4', null,["id" => "reference_4", 'class'=>'referencia-config'], ['label' => 'Referência ']) }}
                                </div>
                        </div>
                        
                         <div class="form-group col-12" id="addBank">
                            <button style="border-radius: 6px; background:#2aceff" class="btn btn-lg text-white btn-submeter"  type="button">
                                <i class="fas fa-plus"></i> Adicionar transacção
                            </button>
                        </div>
                        <div class="form-group col-12" id="removeBank" hidden>
                            <button style="border-radius: 6px; background:#38c172"  class="btn btn-lg text-white btn-submeter"  type="button">
                                <i class="fas fa-times"></i> Remover transacção
                            </button>
                        </div>