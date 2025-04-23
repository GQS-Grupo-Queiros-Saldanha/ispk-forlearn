<div class="modal fade" role="dialog" id="modal_type_1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_1" onsubmit="dt.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plans.study_plan_optional_group')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::discipline-periods.discipline_periods')</label>
                                {{ Form::bsLiveSelect('dp_discipline_periods', $discipline_periods, $action === 'create' ? old('discipline_period') : '', ['required']) }}
                            </div>
                        </div>
{{--                        <div class="col-6">--}}
{{--                            <div class="form-group col">--}}
{{--                                <label>@lang('GA::optional-groups.optional_group')</label>--}}
{{--                                {{ Form::bsLiveSelect('dp_optional_groups', $optional_groups, $action === 'create' ? old('optional_group') : '', ['required']) }}--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-12">
                            {{ Form::bsNumber('dp_years', null, ['placeholder' => __('common.year'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.year')]) }}
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
