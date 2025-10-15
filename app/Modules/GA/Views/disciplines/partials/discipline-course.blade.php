<div class="form-group col">
    <label>@lang('GA::disciplines.course')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('course', $courses, isset($discipline) ? $discipline->courses_id : null, ['required', 'placeholder' => '']) }}
    @else
        {{ $discipline->course ? $discipline->course->currentTranslation->display_name : 'N/A' }}
    @endcan
</div>
