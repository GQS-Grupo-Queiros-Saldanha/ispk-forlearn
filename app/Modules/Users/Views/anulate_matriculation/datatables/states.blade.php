

@if ($state->state == "total")
 <span class='bg-success p-1 text-white'>PAGO</span>
@elseif($state->state == "pending")
<span class='bg-info p-1'>PAGAMENTO EM ESPERA</span>
@elseif($state->state == "partial")
<span class='bg-warning p-1'>PARCIAL</span>
@elseif($state->state ==null )
<span class='bg-danger p-1'>EMOLUMENTO NÃO ENCONTRADO</span>
@endif