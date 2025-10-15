<div class="modal fade" role="dialog" id="modal_type_5">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_5" onsubmit="dt3.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plan-editions.precedence')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
{{--                            <label>@lang('GA::disciplines.discipline')</label>--}}
                            {{ Form::bsLiveSelectEmpty('discipline_precedence', [], null, ['required', 'disabled', 'placeholder' => '']) }}
                        </div>
                        <div class="col-6">
{{--                            <label>@lang('GA::study-plan-editions.precedence')</label>--}}
                            {{ Form::bsLiveSelectEmpty('precedence_precedence', [], null, ['required', 'disabled', 'placeholder' => '']) }}
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
<script>
    // function preventDupes(select, index) {
    //     let options = select.options;
    //
    //     let len = options.length;
    //     while (len--) {
    //         options[len].disabled = false;
    //     }
    //     select.options[index].disabled = true;
    //
    //     if (index === select.selectedIndex) {
    //         this.selectedIndex = 0;
    //     }
    // }
    //
    // window.onload = function() {
    //     let select1 = document.querySelector('select[name=discipline_precedence]');
    //     let select2 = document.querySelector('select[name=precedence_precedence]');
    //
    //     if (select1 !== null && select2 !== null) {
    //         select1.onchange = function () {
    //             preventDupes.call(this, select2, this.selectedIndex);
    //         };
    //         select2.onchange = function () {
    //             preventDupes.call(this, select1, this.selectedIndex);
    //         };
    //     } else {
    //         console.error('Unable to perform validation');
    //     }
    // }
</script>
