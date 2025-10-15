@isset($study_plan->id)
<div id="modal-carga-horario" class="modal" tabindex="-1" role="dialog">
    <form action="{{ route('study-plain.horario', $study_plan->id) }}" class="modal-dialog modal-dialog-centered" role="document" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualização horários</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex">
                    <div class="w-100">Carga de Horário</div>
                    <div class="flex-shrink-1">
                        <input class="form-control rounded w-50 m-1" type="number" id="hora_total" name="hora_total" value="" onChange=""/>   
                    </div>
                </div>                
                <div id="regimes"></div>
                <input type="hidden" id="carga_horario_input" name="carga_horario" value=""/>
                <input type="hidden" id="discipline_input" name="discipline_id" value=""/>
                <input type="hidden" id="periodo_input" name="periodo_id" value=""/>
                <input type="hidden" id="ano_input" name="ano" value=""/>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Actualizar
                </button>
            </div>
        </div>
    </form>
</div>
@endisset