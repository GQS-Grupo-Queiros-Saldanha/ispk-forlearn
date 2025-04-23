<div class="form-group col">
    <label>Turno</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('schedule_type', $schedule_types, $action === 'create' ? old('schedule_type') : $schedule->type->id ?? null, ['required', 'placeholder' => '']) }}
    @else
        <span>{!! $schedule->type->currentTranslation->display_name !!}</span>
    @endcan
</div>
