<div class="col-6 data-field">
    <div>
        <label>{{ array_values($array)[0]['text'] }}</label>
    </div>
    <div>
        @foreach ($array as $key => $item)
            <span>
                    @if($key > 0)
                    {!!", "!!}
                @endif
                {{$item['value']}}
                </span>
        @endforeach
    </div>
</div>
