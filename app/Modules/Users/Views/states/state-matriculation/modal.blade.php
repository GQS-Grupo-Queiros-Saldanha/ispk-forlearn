
  <!-- Modal -->
  <div class="modal fade" id="historicStateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel"></h3> 
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">

                    <table id="states-table" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            {{--<th>#</th>--}}
                            <th>Sigla</th>
                            <th>Estudante</th>
                            <th>Estado</th>
                            <th>Tipo</th>
                            <th>Data</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
        <div class="modal-footer">
          
          <a href="#" target="_blank" type="button" class="btn btn-success  rounded" id="GerarPdf_estado">Gerar PDF</a>
          <button type="button" class="btn btn-primary rounded" id="close_modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>