<div class="card">
    <div class="card-body row">
        <h5 class="card-title mb-3">@lang('GA::discipline-periods.discipline_period')</h5>
        @if(in_array($action, ['create','edit'], true))
            {{ Form::bsLiveSelect('period', $periods, $action === 'create' ? old('period') : $period_type->disciplinePeriod->id, ['required']) }}
        @else
            {{ $period_type->disciplinePeriod->translation->display_name }}
        @endcan
    </div>
</div>
