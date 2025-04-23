@if (!isset($hidden_btn))
    <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal-pdf"
        onclick="$('#modal-pdf').modal()">
        <i class="fas fa-plus-square"></i>
        @isset($message_ficha) {{ $message_ficha}} @else Ficha do CE @endisset
    </a>
@endif

@if (auth()->user()->hasRole('candidado-a-estudante'))
    <div id="modal-pdf" class="modal" tabindex="-1" role="dialog">
        {!! Form::open(['route' => ['candidate.generate_pdf', $user->id], 'method' => 'get', 'target' => '_blank']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Opções de impressão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div hidden>

                        {{ Form::bsNumber('columns-per-group', 7, [], ['label' => __('pdf.columns_per_group')]) }}
                        {{ Form::bsNumber('font-size', 14, [], ['label' => __('pdf.font_size')]) }}
                        {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                        {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}

                    </div>
                    {{ Form::bsSelect('include-attachments', [0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' => 'Incluir documentos']) }}

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@else
    <div id="modal-pdf" class="modal" tabindex="-1" role="dialog">
        {!! Form::open(['route' => ['users.generatePDF', $user->id], 'method' => 'get', 'target' => '_blank']) !!}
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Opções de impressão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div >

                        {{ Form::bsNumber('columns-per-group', 7, [], ['label' => __('pdf.columns_per_group')]) }}
                        {{ Form::bsNumber('font-size', 14, [], ['label' => __('pdf.font_size')]) }}
                        {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                        {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}

                    </div>
                    {{ Form::bsSelect('include-attachments', [0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' => 'Incluir documentos']) }}

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endif
