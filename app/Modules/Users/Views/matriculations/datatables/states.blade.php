{{-- {{$state->state}} --}}
@if ($state->state == "total")
 <span class='bg-success p-1 text-white'>PAGO</span>
@elseif($state->state == "pending")
<span class='bg-info p-1'>EM ESPERA</span>
@elseif($state->state == "partial")
<span class='bg-warning p-1'>PARCIAL</span>
@elseif($state->state ==null)
<span class='bg-warning p-1'>EM ESPERA</span>
@endif