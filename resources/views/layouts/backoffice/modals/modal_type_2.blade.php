<div class="modal fade" role="dialog" id="modal_type_2">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_2" onsubmit="dt2.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plans.study_plans_discipline_regimes')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col" hidden>
                            <input class="form-control" name="rowNumber" type="number" id="rowNumber" value="#">
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::disciplines.disciplines')</label>
                                 <select data-live-search="true" required class="selectpicker form-control form-control-sm"id="dr_disciplines" data-actions-box="true" data-selected-text-format="values" name="dr_disciplines" tabindex="-98">
                                    @foreach($disciplines_course as $item)
                                     <option value="{{$item->id}}">#{{$item->code}} - {{$item->currentTranslation['display_name']}}</option>
                                    @endforeach
                                </select>
                                <!--{{ Form::bsLiveSelect('dr_disciplines', $disciplines, $action === 'create' ? old('discipline') : '', ['required']) }}-->
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col">
                                <label>@lang('GA::discipline-periods.discipline_periods')</label>
                                {{ Form::bsLiveSelect('dr_discipline_periods', $discipline_periods, $action === 'create' ? old('discipline_period') : '', ['required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            {{ Form::bsNumber('dr_years', null, ['min' => '0','placeholder' => __('common.year'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.year')]) }}
                        </div>
                        <div class="col-6">
                            {{ Form::bsFloat('dr_total_hours', null, ['min' => '0', 'placeholder' => __('common.hours'), 'disabled' => $action === 'show', 'required'], ['label' => __('common.hours')]) }}
                        </div>
                        <div class="col-12">
                            <div style="padding: 15px;">
                                <label>@lang('GA::study-plans.discipline_regimes')</label>
                                <div id="dd-extra"></div>
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
