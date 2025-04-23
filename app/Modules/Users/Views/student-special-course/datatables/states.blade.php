@if (isset($item->states))
@if ($item->states == "total")
<span class='bg-success p-1 text-white'>PAGO</span>
@elseif($item->states == "pending")
<span class='bg-info p-1'>EM ESPERA</span>
@elseif($item->states == "partial")
<span class='bg-warning p-1'>PARCIAL</span>
@elseif($item->states ==null)
<span class='bg-info p-1'>EM ESPERA</span>
@endif
@else
N/A
@endif