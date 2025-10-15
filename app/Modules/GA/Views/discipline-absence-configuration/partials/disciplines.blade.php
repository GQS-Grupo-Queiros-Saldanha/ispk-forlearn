<div class="form-group col">
    <label>@lang('GA::discipline-absence-configuration.disciplines')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('disciplines', $disciplines, $action === 'create' ? old('disciplines') : $discipline_absence_configuration->discipline->id, ['required']) }}
    @else
        <span>{{ $discipline_absence_configuration->discipline->translation->display_name }}</span>
    @endcan
</div>
