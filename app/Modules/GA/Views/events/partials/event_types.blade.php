<div class="form-group col">
    <label>@lang('GA::event-types.event_types')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('event_type', $event_types, $action === 'create' ? old('event_type') : $event->type->id ?? null, ['required']) }}
    @else
        <span>
            {{ $event->type->currentTranslation->display_name }}
        </span>
    @endif
</div>
