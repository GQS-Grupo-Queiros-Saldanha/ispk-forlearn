@php $yearVetor=[]; @endphp
[@foreach ($getyearImposto as $element)
    @if (!in_array($element->year,$yearVetor))        
        @php $yearVetor[]=$element->year; @endphp
        @if ($element->id_imposto==$item->id_imposto)
            {{$element->year}},
        @endif
    @endif
@endforeach]
