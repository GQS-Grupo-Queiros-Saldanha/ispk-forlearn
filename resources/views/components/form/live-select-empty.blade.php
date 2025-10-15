@php $attributes = array_merge_recursive(['data-live-search' => 'true', 'class' => 'selectpicker form-control form-control-sm'.($errors->has($name) ? ' is-invalid' : '')],$attributes); @endphp
@php $attributes['id'] = $attributes['id'] ?? $name; @endphp
@php $attributes['data-actions-box'] = in_array('multiple', $attributes, true) ? 'true' : 'false'; @endphp
@php $attributes['data-selected-text-format'] = in_array('multiple', $attributes, true) ? 'count > 3' : 'values'; @endphp

{{ Form::select(
    $name,
    $values,
    $selected,
    $attributes
)}}

@if ($errors->has($name))
    <div class="is-invalid">
        <strong>{{ $errors->first($name) }}</strong>
    </div>
@endif
