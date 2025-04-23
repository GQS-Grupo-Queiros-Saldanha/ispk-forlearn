  <div id="modal-pdf" class="modal" tabindex="-1" role="dialog">
      <form class="modal-dialog modal-dialog-centered" action="#" method="get" target="_blank", id='form-modal-pdf'>
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Opções de impressão</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">

                  <div hidden>

                      <input type="hidden" name="user_id" id="user-id-model-pdf">
                      <input type="hidden" name="fase_exists" id="fase_exists">

                      {{ Form::bsNumber('columns-per-group', 7, [], ['label' => __('pdf.columns_per_group')]) }}
                      {{ Form::bsNumber('font-size', 14, [], ['label' => __('pdf.font_size')]) }}
                      {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                      {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}

                  </div>
                  {{ Form::bsSelect('include-attachments', [0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' =>"Incluir documentos"]) }}

              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">
                      <i class="fas fa-file-pdf"></i>
                      Imprimir
                  </button>
              </div>
          </div>
      </form>
  </div>
