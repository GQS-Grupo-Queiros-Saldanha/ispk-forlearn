<div class="form-group col">
    <label>Semestre</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('period_type_id', $period_type_id, $action === 'create' ? old('classes') : null ?? null, ['required', 'placeholder' => '']) }}
    @else
        <span>{!! $schedule->period_type->currentTranslation->display_name !!}</span>
    @endcan
</div>
