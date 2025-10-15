<div class="modal fade" role="dialog" id="modal_type_8">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_8" onsubmit="dt8.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Payments::articles.monthly_charge.monthly_charge')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::courses.course')</label>
                                {{ Form::bsLiveSelect('monthly_charge_course', \App\Modules\GA\Models\Course::whereNull('deleted_at')->get(), ['required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                {{ Form::bsNumber('monthly_charge_course_year', null, ['placeholder' => 'Ano', 'required', 'min' => 1, 'max' => 5], ['label' => __('Payments::articles.monthly_charge.course_year')]) }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group col">
                                <label>@lang('Payments::articles.monthly_charge.start_month')</label>
                                {{ Form::bsLiveSelect('monthly_charge_start_month', getLocalizedMonths(), 0, ['required']) }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group col">
                                <label>@lang('Payments::articles.monthly_charge.end_month')</label>
                                {{ Form::bsLiveSelect('monthly_charge_end_month', getLocalizedMonths(), 0, ['required']) }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group col">
                                {{ Form::bsNumber('monthly_charge_charge_day', 1, ['placeholder' => 'Dia', 'required', 'min' => 1, 'max' => 31], ['label' => __('Payments::articles.monthly_charge.charge_day')]) }}
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
