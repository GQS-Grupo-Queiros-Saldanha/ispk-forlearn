<div class="form-group col">
    <label>@lang('GA::study-plan-editions.study_plan_editions')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('study_plan_editions', $study_plan_editions, $action === 'create' ? old('study_plan_editions') : $discipline_curricula->study_plan_edition->id, ['required']) }}
    @else
        <span> {{ $discipline_curricula->study_plan_edition->translation->display_name }} </span>
    @endcan
</div>
