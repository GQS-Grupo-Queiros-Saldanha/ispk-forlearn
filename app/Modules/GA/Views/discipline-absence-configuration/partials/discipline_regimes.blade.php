<div class="form-group col">
    <label>@lang('GA::discipline-absence-configuration.discipline_regimes')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('discipline_regimes', $discipline_regimes, $action === 'create' ? old('discipline_regimes') : $discipline_absence_configuration->discipline_regime->id ?? null, ['required']) }}
    @else
        @if($discipline_absence_configuration->discipline_regime != null)
            <span>{{ $discipline_absence_configuration->discipline_regime->translation->display_name }}</span>
        @else
            <span>N/A</span>
        @endif
    @endcan
</div>
