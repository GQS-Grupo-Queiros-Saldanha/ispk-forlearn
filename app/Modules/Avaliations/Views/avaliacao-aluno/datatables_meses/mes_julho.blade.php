@php $vetorArticl=[]; @endphp
@php $articlAtivo=null; @endphp
@foreach ($getArtclis_estudent as $element)
    @if ($element->user_id==$item->user_id)
        @if (empty($vetorArticl))
            @php $vetorArticl[]=$element->article_req_id; $articlAtivo=true @endphp
        @else
            @if (in_array($element->article_req_id, $vetorArticl) && $element->data_from=="Estorno")
                @php $articlAtivo=false @endphp
            @else
                @php $vetorArticl[]=$element->article_req_id; $articlAtivo=true @endphp
            @endif   
        @endif
        
        {{-- permitir na leistagem somemnte o emolumento --}}
        @if ($articlAtivo==true)
            @switch($element->article_month)
                @case(7)
                    @if ($element->status == "total")PAGO
                    @elseif($element->status == "pending")ESPERA
                    @elseif($element->status == "partial")PARCIAL
                    @elseif($element->status ==null)ESPERA
                    @endif
                @break    
            @endswitch
        @endif
        
    @endif
@endforeach