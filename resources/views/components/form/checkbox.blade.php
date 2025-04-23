<div class="form-check-input-center-container">
    <div class="form-check col">
        <label class="form-check-label">
            {{ Form::checkbox($name, $value, $checked, array_merge_recursive(['class' => 'form-check-input-center'.($errors->has($name) ? ' is-invalid' : ''), 'id' => $name ?? ''], $attributes)) }}
            {{ $extra['label'] ?? $name }}
            {{--{{ Form::label($name, $extra['label'] ?? $name, ['class' => 'form-check-label']) }}--}}
        </label>
        @if ($errors->has($name))
            <div class="invalid-feedback">
                <strong>{{ $errors->first($name) }}</strong>
            </div>
        @endif
    </div>
</div>
