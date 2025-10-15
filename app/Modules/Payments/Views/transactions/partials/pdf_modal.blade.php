<!-- Button trigger modal -->
<a href="#" class="btn btn-sm btn-primary mb-3" data-toggle="modal" data-target="#modal-pdf" onclick="$('#modal-pdf').modal()">
    <i class="fas fa-expand"></i>
    @lang('Users::users.generate_pdf')
</a>

<div id="modal-pdf" class="modal" tabindex="-1" role="dialog">
    {!! Form::open(['route' => ['users.generatePDF', $user->id], 'method' => 'get']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('pdf.options')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
                            <label for="margin-left">@lang('pdf.margin_left')</label>
                            <div class="input-group mb-3">
                                <input class="form-control" placeholder="@lang('pdf.margin_left')" name="margin-left" type="number" min="0.00" value="1.00" step="any" id="margin-left">
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="margin-right">@lang('pdf.margin_right')</label>
                            <div class="input-group mb-3">
                                <input class="form-control" placeholder="@lang('pdf.margin_right')" name="margin-right" type="number" min="0.00" value="1.00" step="any" id="margin-right">
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="margin-top">@lang('pdf.margin_top')</label>
                            <div class="input-group mb-3">
                                <input class="form-control" placeholder="@lang('pdf.margin_top')" name="margin-top" type="number" min="0.00" value="1.00" step="any" id="margin-top">
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="margin-bottom">@lang('pdf.margin_bottom')</label>
                            <div class="input-group mb-3">
                                <input class="form-control" placeholder="@lang('pdf.margin_bottom')" name="margin-bottom" type="number" min="0.00" value="1.50" step="any" id="margin-bottom">
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{ Form::bsNumber('columns-per-group', 5, [], ['label' => __('pdf.columns_per_group')]) }}
                {{ Form::bsNumber('font-size', 16, [], ['label' => __('pdf.font_size')]) }}
                {{ Form::bsSelect('paper-size', \App\Helpers\PDFHelper::getTranslatedPaperSizes(), 'a4', [], ['label' => __('pdf.paper_size')]) }}
                {{ Form::bsSelect('orientation', \App\Helpers\PDFHelper::getTranslatedOrientations(), 'portrait', [], ['label' => __('pdf.paper_orientation')]) }}

                {{ Form::bsSelect('include-attachments', [ 0 => __('common.no'), 1 => __('common.yes')], false, [], ['label' => __('pdf.include_attachments')]) }}

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i>
                    @lang('Users::users.generate_pdf')
                </button>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</div>


