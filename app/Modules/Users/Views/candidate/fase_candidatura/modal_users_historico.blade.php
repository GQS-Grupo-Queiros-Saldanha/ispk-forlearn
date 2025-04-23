<div class="modal fade" id="modalHistorico" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    {!! Form::open(['route' => ['fase.candidatura.gerar'], 'method' => 'get', 'target' => '_blank', 'id' => 'form-historico']) !!}
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel" id="title-historico"></h3> 
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label for="include-attachments">Incluir anexos</label>
                        <select class="form-control form-control-sm" id="include-attachments" name="include-attachments">
                            <option value="0" selected="">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </div>
                    <table id="table-historico" class="table table-hover text-center">
                        <thead>
                            <th>Ano</th>
                            <th>Fase</th>
                            <th>curso</th>
                            <th>turma</th>
                            <th>pdf</th>
                        </thead>
                        <tbody id="tbody-historico">

                        </tbody>
                    </table>
                    <div hidden>
                    {{ Form::bsNumber('columns-per-group', 7, [], ['label' => __('pdf.columns_per_group')]) }}
                    {{ Form::bsNumber('font-size', 14, [], ['label' => __('pdf.font_size')]) }}
                    {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                    {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}
                    {{ Form::bsSelect('include-attachments', [ 0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' => __('pdf.include_attachments')]) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">    
          <button type="submit" class="btn btn-success" id="btn-submit" name="user_id">Guardar</button>
          <button type="button" class="btn btn-primary" id="close_modal_create" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
    {{ Form::close() }}
  </div>