<div class="modal fade" role="dialog" id="modal_type_3">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_3" onsubmit="dt.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plan-editions.study_plan_edition_optional_group')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::disciplines.disciplines')</label>
                                {{ Form::bsLiveSelect('og_disciplines', $disciplines, $action === 'create' ? old('discipline') : '', ['required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::study-plan-editions.period_types')</label>
                                {{ Form::bsLiveSelect('og_period_types', $period_types, $action === 'create' ? old('period_type') : '', ['required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::study-plan-editions.period_types')</label>
                                <select name="og_optional_groups" id="select_optional_groups">

                                </select>
                            </div>
                        </div>


                        <div class="col-6">
                            {{ Form::bsNumber('og_year', null, ['placeholder' => __('common.year'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.year')]) }}
                        </div>
                        <div class="col-12" id="collapse-regimes">
                            <label>@lang('GA::study-plans.discipline_regimes')</label>
                            <div id="dd_extra_modal_3"></div>
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
