<div class="form-group col">
    @if(!isset($label) || (isset($label) && $label))
        {{ Form::label($name, $extra['label'] ?? $name) }}
    @endif
    {{ Form::select($name, $values, $selected, array_diff_key(array_merge_recursive(['class' => 'form-control form-control-sm'.($errors->has($name) ? ' is-invalid' : '')], $attributes), ['placeholder' => ''])) }}
    @if ($errors->has($name))
        <div class="is-invalid">
            <strong>{{ $errors->first($name) }}</strong>
        </div>
    @endif
</div>
