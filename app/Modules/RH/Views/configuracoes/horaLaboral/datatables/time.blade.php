
{{$item->total_horas_dia}}h:
@if($item->total_minutos_dia < 10)
    0{{$item->total_minutos_dia}}m 
@else
    {{$item->total_minutos_dia}}m 
@endif
