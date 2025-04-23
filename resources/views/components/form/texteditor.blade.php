<div class="form-group col">
    @if(!isset($label) || (isset($label) && $label))
        {{ Form::label($name, $extra['label'] ?? $name) }}
    @endif
    <div class="texteditor form-control" ></div>
    @if ($errors->has($name))
        <div class="invalid-feedback">
            <strong>{{ $errors->first($name) }}</strong>
        </div>
    @endif
</div>
