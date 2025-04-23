<div class="form-group col">
    <label>@lang('GA::degrees.degree')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('degree', $degrees, $action === 'create' ? old('degree') : $course->degree->id ?? null, ['required']) }}
    @else
        <span>{!! $course->degree->currentTranslation->display_name !!}</span>
    @endcan
</div>
