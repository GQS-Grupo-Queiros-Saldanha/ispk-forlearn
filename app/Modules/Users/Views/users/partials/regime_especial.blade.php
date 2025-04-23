@php
    $currentUserIsAuthorized = auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_inscrições', 'staff_matriculas', 'chefe_tesoureiro']);
    $is_not_teacher = !$user->hasAnyRole(['teacher']);
@endphp

@if ($user->hasAnyRole(['student']))
    <div class="col col-6 form-group">
        @if ($action !== 'show' && $currentUserIsAuthorized)

            <div class="form-check form-check-dflex">
                @if ($regime_especial_status == null)
                    <input name="regime_especial" class="form-check-input" type="hidden" value="0">
                    <input name="regime_especial" class="form-check-input" type="checkbox" value="1"
                        id="regime_especial_check" {{ $regime_especial_status != null ? 'checked' : '' }}>
                @else
                    <input name="regime_especial" class="form-check-input" type="hidden" value="0">
                    <input name="regime_especial" class="form-check-input" type="checkbox" value="1"
                        id="regime_especial_check" {{ $regime_especial_status->are_regime_especial == 1 ? 'checked' : '' }}>
                @endif
                <label class="form-check-label" for="defaultCheck1">
                    Estudante em Regime Especial
                </label>
            </div>
        @else
            <h6 class="card-title mb-3" style="font-size:12pt">Estudante em Regime Especial</h6>
            @if ($regime_especial_status == null)
                N/A
            @else
                {{ $regime_especial_status->are_regime_especial == 0 ? 'Não' : 'Sim' }}
            @endif
        @endif
    </div>

    <div class="modal fade" id="modalRegimeEspecial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Associar estudante ao regime especial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalRegimeEspecial">
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
                        <input type="text" class="form-control" name="rotacao" value="{{ $fullname }}" readonly>
                        <div hidden>
                            <input type="text" value="{{ $user->id }}" name="user_id">
                        </div>
                    </div>
                   

                    <div class="form-group" id="">
                        <label for="exampleFormControlSelect3">Rotação</label>
                        <select name="rotacao" id="rotacao"
                            class="selectpicker form-control form-control-sm" id="exampleFormControlSelect3">

                            <option value="" selected></option>
                            @foreach($rotacoes as $rotacao)
    <option value="{{ $rotacao->id }}" 
        {{ isset($regime_especial_status->rotation_id) && $rotacao->id == $regime_especial_status->rotation_id ? 'selected' : '' }}>
        {{ $rotacao->nome }}
    </option>
@endforeach
                           
                        </select>
                    </div>

                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="cancelModalRegimeEspecial">Cancelar</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
  $('rotacao').append('<option value="" selected></option>');  
    
</script>
