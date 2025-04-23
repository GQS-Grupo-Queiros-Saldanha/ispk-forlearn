<div class="form-group col">
    <label>@lang('GA::buildings.buildings')</label>
    @if(in_array($action, ['create', 'edit'], true))
        {{ Form::bsLiveSelect('building', $buildings, $action === 'create' ? old('regime') : $room->building->id ?? null, ['required']) }}
    @else
        <span>
            {{ $room->building->currentTranslation->display_name }}
        </span>
    @endif
</div>
