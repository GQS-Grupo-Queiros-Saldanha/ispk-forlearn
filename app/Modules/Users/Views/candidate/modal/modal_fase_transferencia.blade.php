<div class="modal" id="modalFase" tabindex="-1" role="dialog">
    <form class="modal-dialog modal-lg modal-dialog-centered" role="document" id="form" action="#" method="GET">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transferência de fase.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span id="message"></span>
                <input type="hidden" name="user" id="user" value=""/>
                <input type="hidden" name="ano_lective" id="ano_lective" @isset($lectiveYearSelected) value="{{$lectiveYearSelected}}" @endisset/>
                <input type="hidden" name="fase_nova" id="fase_nova" @isset($lectiveCandidateNext->fase) value="{{$lectiveCandidateNext->fase}}" @endisset/>
                <input type="hidden" name="lective_candidate_id" id="lective_candidate_id" @isset($lectiveCandidate->id) value="{{$lectiveCandidate->id}}" @endisset>
                @isset($lectiveCandidate->id) @else
                    <input type="hidden" name="back" value="back"/>
                @endisset
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="btn-fase-action">confirma</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">cancelar</button>
            </div>
        </div>
        @isset($lectiveCandidate->id)
            <input type="hidden" name="url" id="url"  value="{{route('fase.candidatura.ajax.list.users',$lectiveCandidate->id)}}"  disabled>
        @endisset
    </form>
</div>