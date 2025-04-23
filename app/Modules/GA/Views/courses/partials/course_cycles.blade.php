<div class="form-group col">
    <label>@lang('GA::course-cycles.course_cycle')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('course_cycle', $course_cycles, $action === 'create' ? old('duration_type') : $course->course_cycle->id ?? null, ['required']) }}
    @else
        <span> {!!$course->course_cycle->translation->display_name  !!} </span>
    @endcan
</div>
