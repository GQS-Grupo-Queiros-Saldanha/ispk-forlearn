<div class="modal fade" id="modal_delete_simple">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('modal.confirm_title')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p>
                    <span>@lang('modal.confirm_text')</span>&nbsp;<span class="modal-confirm-text"></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="submit" id="button_simple_modal_delete" data-dismiss="modal" class="btn forlearn-btn" id="delete-btn">
                    <i class="far fa-check-square"></i>@lang('modal.confirm_button')
                </button>
                <button type="button" class="btn forlearn-btn" data-dismiss="modal">
                    <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                </button>
            </div>
        </div>
    </div>
</div>


