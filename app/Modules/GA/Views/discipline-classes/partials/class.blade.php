<div class="form-group col">
    <label>@lang('GA::classes.class')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('class', $discipline_classes, $action === 'create' ? old('classes') : $class->id, ['required']) }}
    @else
        <span>{{ $discipline_classes->class->translation->display_name  }}</span>
    @endcan
</div>
