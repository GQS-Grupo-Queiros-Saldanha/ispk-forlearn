<div class="modal fade" id="exampleModalJuris" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form class="modal-dialog" method="POST" action="{{ route('juri.delete') }}">
        @csrf
        @method("DELETE")
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel" id="title-historico">Júris</h3>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <table id="table-juris" class="table table-hover">
                            <thead>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Acção</th>
                            </thead>
                            <tbody id="tbody-juris">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </form>
</div>
