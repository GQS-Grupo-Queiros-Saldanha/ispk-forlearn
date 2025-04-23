<div class="form-group col">

    @if(!isset($label) || (isset($label) && $label))
        {{ Form::label($name, $extra['label'] ?? $name) }}
    @endif

    @if(stripos($value, 'jpg') !== false || stripos($value, 'png') !== false || stripos($value,'jpeg') !== false || stripos($value,'bmp') !== false)
        <img class="user-profile-image" style="width:100px;" src="{{ URL::to('/') }}/storage/attachment/{{ $value }}">
    @else
        <a target="_blank" href="{{ URL::to('/') }}/storage/attachment/{{ $value }}"><span> {{ $value }} </span></a>
    @endif

    <div class="custom-file-upload">

        @if($value)
            {{ Form::hidden($name, $value) }}
        @endif
        <input type="file" class="attachment" id="id_attachment_{{ $name }}" name="attachment_{{ $name }}" value="{{ $value }}"/>
        <input type="text" class="attachment" style="visibility: hidden;" id="id_helper_attachment_{{ $name }}" name="attachment_{{ $name }}" value="{{ $value }}"/>
        {{--<button type="button" data-type="remove" class="btn forlearn-btn remove-input-attachment">
            <i class="fas fa-trash"></i>Eliminar
        </button>--}}
    </div>

    @if ($errors->has($name))
        <div class="invalid-feedback">
            <strong>{{ $errors->first($name) }}</strong>
        </div>
    @endif
</div>
