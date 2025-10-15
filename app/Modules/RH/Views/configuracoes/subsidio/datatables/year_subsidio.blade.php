[
@foreach ($SubsidiosImposto as $element)
    @if ($element->id_subsidio==$item->subsidio_id)
        {{$item->year}},{{$item->month}}        
        @break
    @endif
@endforeach
]