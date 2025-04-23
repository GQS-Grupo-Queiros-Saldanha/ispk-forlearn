
@if ($state->state == "concluido")
<span class='bg-success p-1 text-white'>CONCLUIDO</span>
@elseif($state->state == "espera")
<span class='bg-info p-1'>ESPERA</span>
@endif   