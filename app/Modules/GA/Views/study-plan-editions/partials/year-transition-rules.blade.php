<div class="form-group col">
    <label>@lang('GA::study-plan-editions.year_transition_rule')</label>
    @php
        $options = ['placeholder' => ''];
        if ($action === 'show'){
            $options[] = 'disabled';
        }
    @endphp
    {{ Form::bsLiveSelect('year_transition_rule', $year_transition_rules, $action === 'create' ? old('year_transition_rule') : $study_plan_edition->yearTransitionRule->id, $options) }}
</div>
