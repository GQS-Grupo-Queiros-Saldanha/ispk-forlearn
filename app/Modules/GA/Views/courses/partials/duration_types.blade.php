<div class="form-group col" style="margin-bottom: 24px;">
    <label>@lang('GA::duration-types.duration_types')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('duration_type', $duration_types, $action === 'create' ? old('duration_type') : $course->duration_type->id ?? null, ['required']) }}
    @else
        <span>{!! $course->duration_type->currentTranslation->display_name !!}</span>
    @endcan
</div>
