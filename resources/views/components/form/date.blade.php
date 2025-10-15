<div class="form-group col" style="position: relative;">
    @if(!isset($label) || (isset($label) && $label))
        {{ Form::label($name, $extra['label'] ?? $name) }}
    @endif
    {{ Form::date($name, $value, array_merge_recursive(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')], $attributes)) }}
    @if ($errors->has($name))
        <div class="invalid-feedback">
            <strong>{{ $errors->first($name) }}</strong>
        </div>
    @endif
</div>
