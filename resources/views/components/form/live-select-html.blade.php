@php $attributes = array_merge_recursive(['data-live-search' => 'true', 'class' => 'selectpicker form-control form-control-sm'.($errors->has($name) ? ' is-invalid' : '')],$attributes); @endphp
<select id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" @foreach($attributes as $key => $val) @if($val){{$key}}="{{ $val }}@endif" @endforeach>

    @if(empty($attributes['required']) || (isset($attributes['required']) && !in_array($attributes['required'], ['true', 'required'])))
        <option value=""></option>
    @endif

    @foreach($values as $key=>$value)
        <option data-content="{{ $value }} {{ $key }}" value="{{ $key }}" @if($selected) selected @endif>{{ $value }}</option>
    @endforeach
</select>

@if ($errors->has($name))
    <div class="is-invalid">
        <strong>{{ $errors->first($name) }}</strong>
    </div>
@endif