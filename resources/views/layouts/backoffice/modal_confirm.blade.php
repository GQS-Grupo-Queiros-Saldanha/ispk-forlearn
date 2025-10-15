<div class="modal fade" id="modal_confirm">
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
                <form method="POST" action="" accept-charset="UTF-8" class="d-inline" id="form_modal_confirm">
                    <input name="_method" type="hidden" value="">
                    @csrf
                    <button type="submit" class="btn forlearn-btn" id="delete-btn">
                        <i class="far fa-check-square"></i>@lang('modal.confirm_button')
                    </button>
                    <button type="button" class="btn forlearn-btn" data-dismiss="modal">
                        <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>




