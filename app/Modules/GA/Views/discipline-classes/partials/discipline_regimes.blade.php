<div class="form-group col">
    <label>@lang('GA::discipline-classes.discipline_regime')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('discipline_regime', $discipline_regimes, $action === 'create' ? old('discipline_regimes') : $discipline_classes->discipline_regime->id, ['required']) }}
    @else
        <span>{{ $discipline_classes->discipline_regime->translation->display_name }}</span>
    @endcan
</div>
