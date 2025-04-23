<div class="modal fade" role="dialog" id="modal_transaction">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_transaction" onsubmit="transactionDT.formSubmit(); typeof newRowFromModal !== 'undefined' ? newRowFromModal('form_modal_transaction') : null; return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Payments::requests.transactions.transaction')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="transaction_id" value="" />
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('Payments::requests.transactions.type')</label>
                                {{ Form::bsLiveSelect('transaction_type', $credit_types, null, ['required', 'placeholder' => '']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            {{ Form::bsNumber('transaction_value', null, ['placeholder' => 0, 'disabled', 'required'], ['label' => __('Payments::requests.value')]) }}
                        </div>
                    </div>
                    <div id="transaction-info-container" hidden>
                        <hr>
                        <h6>@lang('Payments::requests.transactions.info')</h6>
                        <div class="row">
                            <div class="col-6">
                                {{ Form::bsDate('transaction_fulfilled_at', null, ['placeholder' => __('Payments::requests.fulfilled_at'), 'required'], ['label' => __('Payments::requests.fulfilled_at')]) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>@lang('Payments::banks.bank')</label>
                                    {{ Form::bsLiveSelect('transaction_bank', $banks, null, ['required', 'placeholder' => '']) }}
                                </div>
                            </div>
                            <div class="col-6">
                                {{ Form::bsText('transaction_reference', null, ['required'], ['label' => __('Payments::requests.reference')]) }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                {{ Form::bsTextArea('transaction_notes', null, ['disabled'], ['label' => __('Payments::requests.transactions.notes')]) }}
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
