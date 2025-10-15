[@foreach ($getyearImposto as $element)
    @if ($element->id_imposto==$item->id_imposto)
        {{$element->year}},
    @endif
@endforeach]


@foreach ($getfun_with_type_contrato as $contrato_activo) {