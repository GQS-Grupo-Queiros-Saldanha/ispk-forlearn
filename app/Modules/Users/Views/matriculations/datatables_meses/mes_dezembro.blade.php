@php $vetorArticl=[]; @endphp
@php $articlAtivo=null; @endphp
@foreach ($getArtclis_estudent as $element)
    @if ($element->user_id==$item->user_id)
        @if (empty($vetorArticl))
            @php $vetorArticl[]=$element->article_req_id; $articlAtivo=true @endphp
        @else
            @if (in_array($element->article_req_id, $vetorArticl))
                @php $articlAtivo=false @endphp
            @else
                @php $vetorArticl[]=$element->article_req_id; $articlAtivo=true @endphp
            @endif   
        @endif




        {{-- permitir na leistagem somemnte o emolumento --}}
        @if ($articlAtivo==true)
            @switch($element->article_month)
                @case(12)
                    @if ($element->status == "total")
                    <span class='bg-success p-1 text-white'>PAGO</span>
                    @elseif($element->status == "pending")
                        <span class='bg-info p-1'>ESPERA</span>
                    @elseif($element->status == "partial")
                        <span class='bg-warning p-1'>PARCIAL</span>
                    @elseif($element->status ==null)
                        <span class='bg-info p-1'>ESPERA</span>
                    @endif
                @break    
            @endswitch
        @endif
        
    @endif
@endforeach