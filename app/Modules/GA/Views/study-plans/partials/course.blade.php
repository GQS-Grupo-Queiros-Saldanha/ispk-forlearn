<div class="form-group col">
    <label>@lang('GA::courses.course')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('course', $courses, $action === 'create' ? old('courses') : $study_plan->course->id, ['required']) }}
    @else
        <span>{{ $study_plan->course->translation->display_name }}</span>
    @endcan
</div>
