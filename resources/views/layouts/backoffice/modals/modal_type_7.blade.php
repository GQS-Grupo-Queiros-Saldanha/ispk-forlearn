<div class="modal fade" role="dialog" id="modal_type_7">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_7" onsubmit="dt7.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Payments::articles.extra_fees.extra_fee')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                {{ Form::bsNumber('extra_fees_percent', null, ['placeholder' => '%', 'required', 'min' => 0, 'max' => 100], ['label' => __('Payments::articles.extra_fees.percent')]) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                {{ Form::bsNumber('extra_fees_delay', null, ['placeholder' => __('Payments::articles.extra_fees.days'), 'required', 'min' => 1, 'max' => 100], ['label' => __('Payments::articles.extra_fees.delay')]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn forlearn-btn add">
                        <i class="far fa-plus"></i>@lang('modal.confirm_button')
                    </button>
                    <button type="button" class="btn forlearn-btn cancel" data-dismiss="modal">
                        <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
