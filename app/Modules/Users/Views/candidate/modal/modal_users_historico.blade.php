<div class="modal fade" id="modalHistorico" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    {!! Form::open(['route' => ['fase.candidatura.gerar'],'method' => 'get','target' => '_blank','id' => 'form-historico',]) !!}
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <h3 class="modal-title" id="staticBackdropLabel" id="title-historico">Histórico de candidaturas</h3>
            </div>
            <div class="modal-body">
                <div class="">
                    <div class="">
                        <div class="form-group mb-2 d-none">
                            <label for="include-attachments">Incluir anexos</label>
                            <select class="form-control form-control-sm" id="include-attachments"
                                name="include-attachments">
                                <option value="0" selected="">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <table id="table-historico" class="table table-hover text-center">
                            <thead>
                                <th>ANO</th>
                                <th>CURSO</th>
                                <th>TURMA</th>
                                <th>FASE</th>
                                <th>PDF</th>
                            </thead>
                            <tbody id="tbody-historico">
                            </tbody>
                        </table>
                        
                        <input type="hidden" name="user_id" id="user-pdf"/>
                        <div hidden>
                            
                            <input type="hidden" name="curso" id="curso-pdf"/>
                            <input type="hidden" name="turma" id="turma-pdf"/>

                            {{ Form::bsNumber('columns-per-group', 7, [], ['label' => __('pdf.columns_per_group')]) }}
                            {{ Form::bsNumber('font-size', 14, [], ['label' => __('pdf.font_size')]) }}
                            {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                            {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}
                            {{ Form::bsSelect('include-attachments', [0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' => __('pdf.include_attachments')]) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="close_modal_create" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
     {!! Form::close() !!}
</div>

