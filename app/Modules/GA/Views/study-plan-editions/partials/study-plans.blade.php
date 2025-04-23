<div class="col-6">
    <div class="form-group col">
        <label>@lang('GA::study-plans.study_plan')</label>
        @if ($action === 'create')
            @php
                $options = ['required', 'placeholder' => ''];
                if ($action === 'show'){
                    $options[] = 'disabled';
                }
            @endphp
            {{ Form::bsLiveSelect('study_plan', $study_plans, $action === 'create' ? null : $study_plan_edition->studyPlan->id, $options) }}
        @else
            <span>
                {{ $study_plan_edition->studyPlan->currentTranslation->display_name }}
            </span>
        @endif
    </div>
</div>
<div class="col-6">
    <div class="form-group col">
        <label>@lang('GA::study-plans.year')</label>
        @if ($action === 'create')
            @php
                $options = ['required', 'disabled', 'placeholder' => ''];
                if ($action === 'show'){
                    $options[] = 'disabled';
                }
            @endphp
            {{ Form::bsLiveSelectEmpty('course_year', [], $action === 'create' ? old('study_plan_edition_year') : $study_plan_edition->studyPlan->id, $options) }}
        @else
            <span>
                {{ $study_plan_edition->course_year }}
            </span>
        @endif
    </div>
</div>
