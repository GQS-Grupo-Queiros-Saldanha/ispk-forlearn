<div class="form-group col">
    <label>@lang('GA::departments.department')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('department', $departments, $action === 'create' ? old('department') : $course->department->id ?? null, ['required']) }}
    @else
        <span>{!! $course->department->currentTranslation->display_name !!}</span>
    @endcan
</div>
