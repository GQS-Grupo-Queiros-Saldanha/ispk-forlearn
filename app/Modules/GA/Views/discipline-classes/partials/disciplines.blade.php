<div class="form-group col">
    <label>@lang('GA::discipline-classes.discipline')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('discipline', $disciplines, $action === 'create' ? old('disciplines') : $discipline_classes->discipline->id, ['required']) }}
    @else
        <span>{{ $discipline_classes->discipline->translation->display_name }}</span>
    @endcan
</div>
