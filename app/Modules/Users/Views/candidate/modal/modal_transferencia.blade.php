<div class="modal fade" id="modalTransferencia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{route('fase.transfer.user')}}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius: 10px;">
                <div class="modal-header">
                    <h3 class="modal-title" id="staticBackdropLabel" id="">Escolha o curso e a turma</h3>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="user" id="user" />
                            <div>
                                <label for="curso">Escolha o curso</label>
                                <select class="form-control" name="curso" id="curso"></select>
                            </div>
                            <div class="mt-2">
                                <label for="turma">Escolha a turma</label>
                                <select class="form-control" name="turma" id="turma"></select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="btn-submit" name="user_id">Guardar</button>
                        <button type="button" class="btn btn-primary" id="close_modal_create" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>