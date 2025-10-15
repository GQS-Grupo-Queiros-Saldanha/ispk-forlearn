
@if ($state->status == "total")
<span class='bg-success p-1 text-white'>PAGO</span>
@elseif($state->status == "pending")
<span class='bg-info p-1'>EM ESPERA</span>
@elseif($state->status == "partial")
<span class='bg-warning p-1'>PARCIAL</span>
@elseif($state->status ==null)
<span class='bg-info p-1'>EM ESPERA</span>
@endif