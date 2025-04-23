<div class="form-group col">
    <label>@lang('GA::discipline-curricula.disciplines')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('discipline', $disciplines, $action === 'create' ? old('disciplines') : $discipline_curricula->discipline->id, ['required']) }}
    @else
        <span>{{ $discipline_curricula->discipline->translation->display_name }}</span>
    @endcan
</div>
