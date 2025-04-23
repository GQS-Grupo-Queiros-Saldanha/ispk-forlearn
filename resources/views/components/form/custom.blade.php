@php
    $attributes = array_merge_recursive(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')], $attributes);
@endphp

<div class="form-group col">
    @if(!isset($label) || (isset($label) && $label))
        {{ Form::label($name, $extra['label'] ?? $name) }}
    @endif

    <input name="{{ $name }}" value="{{ $value }}"
    @foreach($attributes as $attribute => $value)
        @if(in_array($attribute, ['disabled', 'required']))
            @if($value === true)
                {{ $attribute }}
            @endif
        @else
            {{ $attribute }}="{{ $value }}"
        @endif
    @endforeach
    >

    @if ($errors->has($name))
        <div class="invalid-feedback">
            <strong>{{ $errors->first($name) }}</strong>
        </div>
    @endif
</div>
