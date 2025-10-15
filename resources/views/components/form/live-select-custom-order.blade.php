@php $attributes = array_merge_recursive(['data-live-search' => 'true', 'class' => 'selectpicker form-control form-control-sm'.($errors->has($name) ? ' is-invalid' : '')],$attributes); @endphp
@php $attributes['id'] = $attributes['id'] ?? $name; @endphp
@php $attributes['data-actions-box'] = in_array('multiple', $attributes, true) ? 'true' : 'false'; @endphp
@php $attributes['data-selected-text-format'] = in_array('multiple', $attributes, true) ? 'count > 3' : 'values'; @endphp

@php
$new_method = !empty($values->pluck('currentTranslation.display_name'));
$old_method = !$new_method && !empty($values->pluck('translation.display_name')[0]);
@endphp

{{ 'old_method ' . ($old_method ? 'true' : 'false') }}
{{ 'new_method ' . ($new_method ? 'true' : 'false') }}

@if ($old_method || $new_method)
    <select name="{{ $name }}"

        @foreach($attributes as $key=>$val)
            @if(in_array($val, ['required', 'disabled', 'readonly', 'multiple']))
                {{ $val }}
            @else
            {{ $key }}="{{ $val }}"
            @endif
        @endforeach
        >

        @if(!empty($selected))

            @foreach($selected as $sel)
                <option value="{{ $sel }}" selected>
                @if ($old_method)
                    {{ $values->find($sel)->translation->display_name ?? '' }}
                @elseif($new_method)
                    {{ $values->find($sel)->currentTranslation->display_name ?? '' }}
                @endif
            </option>
        @endforeach
        @foreach($values as $value)
            @if(!in_array($value->id, $selected->toArray(), true))
                <option value="{{ $value->id }}">
                    @if ($old_method)
                        {{ $value->translation->display_name ?? '' }}
                    @elseif($new_method)
                        {{ $value->currentTranslation->display_name ?? '' }}
                    @endif
                </option>
            @endif
        @endforeach
    @else
        @foreach($values as $value)
                <option value="{{ $value->id }}">
                    @if ($old_method)
                        {{ $value->translation->display_name ?? '' }}
                    @elseif($new_method)
                        {{ $value->currentTranslation->display_name ?? '' }}
                    @endif
                </option>
        @endforeach
    @endif
</select>
@else
{{ Form::select(
    $name,
    $values !== null ? $values->pluck('display_name', 'id') : [],
    $selected,
    $attributes
)}}
@endif

@if ($errors->has($name))
<div class="is-invalid">
    <strong>{{ $errors->first($name) }}</strong>
</div>
@endif
