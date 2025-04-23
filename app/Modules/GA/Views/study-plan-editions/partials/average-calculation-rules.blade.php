<div class="form-group col">
    <label>@lang('GA::study-plan-editions.average_calculation_rule')</label>
    @php
        $options = ['required'];
        if ($action === 'show') {
            $options[] = 'disabled';
        }
    @endphp
    {{ Form::bsLiveSelect('average_calculation_rule', $average_calculation_rules, $action === 'create' ? old('average_calculation_rule') : $study_plan_edition->averageCalculationRule->id, $options) }}
</div>
