@php
    $currentUserIsAuthorized = auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas', 'chefe_tesoureiro']);
    $is_not_teacher = !$user->hasAnyRole(['teacher']);
@endphp

@if ($user->hasAnyRole(['student']))
    <div class="col col-6 form-group">
        @if ($action !== 'show' && $currentUserIsAuthorized)

            <div class="form-check form-check-dflex">
                @if ($scholarship_status == null)
                    <input name="are_scholarship" class="form-check-input" type="hidden" value="0">
                    <input name="are_scholarship" class="form-check-input" type="checkbox" value="1"
                        id="scholarship_check" {{ $scholarship_status != null ? 'checked' : '' }}>
                @else
                    <input name="are_scholarship" class="form-check-input" type="hidden" value="0">
                    <input name="are_scholarship" class="form-check-input" type="checkbox" value="1"
                        id="scholarship_check" {{ $scholarship_status->are_scholarship_holder == 1 ? 'checked' : '' }}>
                @endif
                <label class="form-check-label" for="defaultCheck1">
                    Estudante: Bolseiro / Protocolo
                </label>
            </div>
        @else
            <h6 class="card-title mb-3" style="font-size:12pt">Estudante: Bolseiro / Protocolo</h6>
            @if ($scholarship_status == null)
                N/A
            @else
                {{ $scholarship_status->are_scholarship_holder == 0 ? 'Não' : 'Sim' }}
            @endif
        @endif
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Associar estudante a uma entidade bolseira / protocolo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Estudante</label>
                    @php
                        foreach($user->parameters as $item){
                        
             if($item->pivot->parameters_id == 1 && $item->pivot->parameter_group_id == 2)           
            $fullname = $item->pivot->value;
            
            
            }
            
            @endphp
                        <input type="text" class="form-control" name="company" value="{{ $fullname }}" readonly>
                        <div hidden>
                            <input type="text" value="{{ $user->id }}" name="user_id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Entidate</label>
                        {{ Form::bsLiveSelect('entity', $entitys, $scholarship_status->scholarship_entity_id ?? null, ['id' => 'entity', 'class' => 'form-control']) }}
                    </div>

                    {{-- seletor de descontos --}}
                    <div class="form-group" id="normal_desconto">
                        <label for="exampleFormControlSelect2">Desconto</label>
                        <select name="desconto_bolseiro" id="desconto_bolseiro"
                            class="selectpicker form-control form-control-sm" id="exampleFormControlSelect2">
                            <option selected></option>
                            @php($i = 5)
                            @while ($i <= 100)
                                <option value="{{ $i }}" @if( isset($scholarship_status->id) && $scholarship_status->desconto_scholarship_holder == $i ) selected @endif>
                                    {{ $i }} %
                                </option>
                                @php($i += 5)
                            @endwhile
                        </select>
                    </div>
                    
                    <div class="form-group d-none" id="outro_desconto">
                        <label for="exampleFormControlSelect2">Desconto</label>
                        <input type="number" step="any" class="form-control" name="desconto_bolseiro" value="{{ $scholarship_status->desconto_scholarship_holder ?? null }}"/>
                    </div>
                    
                    <!--<div class="form-group d-flex gap-1 align-items-center">-->
                    <!--    <input id="check_desconto" class="form-check" type="checkbox"/>-->
                    <!--    <label for="check_desconto" class="form-label mt-2">Outro desconto</label>-->
                    <!--</div>-->
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="cancelModalScholarship">Cancelar</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
@endif


@if (!$user->hasAnyRole(['student']) && $is_not_teacher)
    <div class="col col-6 form-group">

        @if ($action !== 'show' && $currentUserIsAuthorized)
            <div class="form-check form-check-dflex">
                @if ($staff_status_studant == null)
                    <input name="are_scholarship" class="form-check-input" type="hidden" value="0">
                    <input name="are_scholarship" class="form-check-input openModalStaff" type="checkbox" value="1"
                        id="scholarship_check_staff" {{ $staff_status_studant != null ? 'checked' : '' }}>
                @else
                    <input name="are_scholarship" class="form-check-input" type="hidden" value="0">
                    <input name="are_scholarship" class="form-check-input openModalStaff" type="checkbox" value="1"
                        id="scholarship_check_staff" {{ $staff_status_studant->status > 0 ? 'checked' : '' }}>
                @endif
                <label class="form-check-label openModalStaff" for="scholarship_check_staff">
                    Staff-Estudante
                </label>
            </div>
        @else
            <h5 class="card-title mb-3">Staff-Estudante</h5>
            @if ($staff_status_studant == null)
                N/A
            @else
                {{ $staff_status_studant->status == 0 ? 'Não' : 'Sim' }}
            @endif
        @endif
    </div>

    <div class="modal fade" id="exampleModalStaff" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalStaffLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ATENÇÃO!</h5>
                    <button type="button" class="close staffClose" data-dismiss="modal" aria-label="Close"
                        id="closeModalStaff">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        @if (isset($staff_status_studant->status) && $staff_status_studant->status > 0)
                            @if ($staff_status_studant->is_candidato == 1)
                                <label for="">
                                    Esta opção não poderá ser desabilitada, a forLEARN detectou que já existe um
                                    candidato a estudante associado a este utlizador.
                                    <br>
                                </label>
                            @else
                                <label for="">
                                    Tens a certeza que pretendes desabilitar esta opção ?
                                    <br>
                                </label>
                            @endif
                        @else
                            <label for="">
                                Caro funcionário(a) <strong>{{ Auth::user()->name }}</strong>, após confirmar esta ação
                                o sistema vai habilitar a possibilidade de criar um candidato à estudante com o utilizador
                                staff <strong>{{ $user->name }}</strong> usando o mesmo número de Bilhete para
                                carregar os dados.
                            </label>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary staffClose" data-dismiss="modal"
                        id="cancelModalStaff">Cancelar</button>
                    @if (isset($staff_status_studant->status) && $staff_status_studant->status > 0)
                        @if ($staff_status_studant->is_candidato != 1)
                            <button type="button" class="btn btn-danger" id="btn-docente-estudante-eliminar"
                                name="btn_eliminar">Confirmar</button>
                        @endif
                    @else
                        <button type="button" class="btn btn-success" id="btn-docente-estudante"
                            name="btn_confirmar">Confirmar</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    
    const checkedOther = document.querySelector('#check_desconto');
    
    function verifyCheckDesconto(){
        const outro_desconto = document.querySelector('#outro_desconto');
        const normal_desconto = document.querySelector('#normal_desconto');
        const label_desconto = document.querySelector('label[for="check_desconto"]');
        
        const input_desconto = document.querySelector('input[name="desconto_bolseiro"]');
        const select_desconto = document.querySelector('select[name="desconto_bolseiro"]');
        
        if(checkedOther.checked){
            normal_desconto.classList.add('d-none');
            outro_desconto.classList.remove('d-none');
            
            input_desconto.removeAttribute('disabled');
            select_desconto.setAttribute('disabled', true);
            
            label_desconto.innerHTML = "Descontos padrão";
        }else{
            outro_desconto.classList.add('d-none');
            normal_desconto.classList.remove('d-none');
            
            select_desconto.removeAttribute('disabled');
            input_desconto.setAttribute('disabled', true);
            
            label_desconto.innerHTML = "Outras desconto";
        }
    }
    
    checkedOther.addEventListener('change',()=>{
        verifyCheckDesconto();
    });
    
</script>
