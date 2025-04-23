<div class="form-group col">
    <label>@lang('GA::discipline-classes.class')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('class', $classes , $action === 'create' ? old('classes') :$discipline_classes->classes->id, ['required']) }}
    @else
        <span>{{ $discipline_classes->classes->display_name }}</span>
    @endcan
</div>
