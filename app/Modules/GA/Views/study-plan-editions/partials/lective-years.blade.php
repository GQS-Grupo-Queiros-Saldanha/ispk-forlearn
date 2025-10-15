<div class="form-group col">
    <label>@lang('GA::study-plan-editions.lective_year')</label>
    @php
        $options = ['required'];
        if($action === 'show'){
            $options[] = 'disabled';
        }
    @endphp
    @php($selectedLectiveYear = isset($study_plan_edition) && $study_plan_edition->lectiveYear ? $study_plan_edition->lectiveYear->id : null)
    {{ Form::bsLiveSelect('lective_year', $lective_years, $action === 'create' ? old('lective_year') : $selectedLectiveYear, $options) }}
</div>
