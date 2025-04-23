<div class="modal fade" role="dialog" id="modal_type_4">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_4" onsubmit="dt2.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plan-editions.study_plan_edition_discipline_regimes')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label>@lang('GA::disciplines.disciplines')</label>
                            {{ Form::bsLiveSelect('dr_disciplines', $disciplines, $action === 'create' ? old('discipline') : '', ['required']) }}
                        </div>
                        <div class="col-6">
                            <label>@lang('GA::discipline-periods.discipline_periods')</label>
                            {{ Form::bsLiveSelect('dr_period_types', $period_types, $action === 'create' ? old('period_type') : '', ['required']) }}
                        </div>
                        {{--<div class="col-6">
                            <h5 class="card-title mb-3">@lang('common.year')</h5>
                            {{ Form::bsNumber('dr_years', null, ['placeholder' => __('common.year'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.year')]) }}
                        </div>--}}
                        <div class="col-6">
                            {{ Form::bsCheckbox('dr_optional', null, false, ['disabled' => $action === 'show'], ['label' => __('GA::study-plan-editions.optional')]) }}
                        </div>
                        <div class="col-12 collapse" id="collapse-regimes">
                            <label>@lang('GA::study-plans.discipline_regimes')</label>
                            <div id="dd_extra_modal_4"></div>
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
