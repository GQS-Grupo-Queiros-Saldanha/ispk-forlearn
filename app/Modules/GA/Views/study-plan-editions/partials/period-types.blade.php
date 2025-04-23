<div class="form-group col">
    <label>Semestre</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('period_type_id', $period_type_id, $action === 'create' ? old('classes') : null ?? null, ['placeholder' => '', 'required']) }}
    @else
        <span>{!! $study_plan_edition->periodTypes->currentTranslation->display_name !!}</span>
    @endcan
</div>
