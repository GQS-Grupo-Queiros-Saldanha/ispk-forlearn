<div class="form-group col">
    <label>@lang('GA::course-regimes.course_regime')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('course_regimes[]', $course_regimes,$action === 'create' ? old('course_regimes') : $course->course_regime->id ?? null, ['required', 'multiple']) }}
    @else
        <span>
            {!! implode(', ', $course->course_regimes->pluck('translation.display_name')->toArray()) !!}
        </span>
    @endcan
</div>
