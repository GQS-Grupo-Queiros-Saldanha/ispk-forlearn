<div class="form-group col">
    <label>@lang('GA::study-plan-editions.study_plan_edition')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('study_plan_edition', $study_plan_editions, $action === 'create' ? old('study_plan_edition') : $schedule->study_plan_edition->id ?? null, ['required']) }}
    @else
        <span>{!! $schedule->study_plan_edition->currentTranslation->display_name !!}</span>
    @endcan
</div>
