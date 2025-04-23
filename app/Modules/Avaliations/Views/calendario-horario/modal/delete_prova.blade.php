<div class="modal fade" id="exampleModalProva" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form class="modal-dialog modal-dialog-centered" method="POST"
        action="{{ route('calendario_prova_horario.delete') }}">
        @csrf
        @method('DELETE')
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel"><strong>Confirmar eliminação</strong></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="prova_horario" name="prova_horario" />
                <span>Tens a certeza que desejas eliminar esse registo?</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirma</button>
            </div>
        </div>
    </form>
</div>
