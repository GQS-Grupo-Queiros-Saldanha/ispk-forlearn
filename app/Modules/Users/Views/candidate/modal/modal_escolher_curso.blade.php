<div class="modal fade" id="modalEscolher" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="{{route('escolher.curso.post')}}" class="modal-dialog modal-lg modal-dialog-centered" method="POST">
        @csrf
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel" id="title-historico">Escolha o curso (padrão)</h3>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="user_id" id="user_id_escolher"/>
                        <table id="table-curso-escolher" class="table table-hover text-center">
                            <thead>
                                <th>curso</th>
                                <th>escolher</th>
                            </thead>
                            <tbody id="tbody-curso-escolher">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="close_modal_create" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </form>
</div>