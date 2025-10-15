@if ($item->states_id == 2) {{-- Falecido --}}
   <span class="badge badge-pill badge-secondary rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 3) {{-- Prescrito --}}
   <span class="badge badge-pill badge-danger rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 4) {{-- Aguardar Matrícula --}}
   <span class="badge badge-pill badge-warning rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 5) {{-- Aguardar Pagamento --}}
   <span class="badge badge-pill badge-warning rounded"> {{ $item->state }} </span>
@endif  

@if ($item->states_id == 6) {{-- Concluído --}}
   <span class="badge badge-pill badge-success rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 7) {{-- Frequentar --}}
   <span class="badge badge-pill badge-success rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 8) {{-- Interropido --}}
   <span class="badge badge-pill badge-danger rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 9) {{-- Mudança de curso --}}
   <span class="badge badge-pill badge-warning rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 10) {{-- Suspensão da Matrícula --}}
   <span class="badge badge-pill badge-danger rounded"> {{ $item->state }} </span>
@endif

@if ($item->states_id == 11) {{-- Não Inscrito --}}
   <span class="badge badge-pill badge-danger rounded"> {{ $item->state }} </span>
@endif

@if($item->states_id == 12) {{-- Pedido de transferencia (sair)--}}
   <span class="badge badge-pill badge-warning rounded"> {{ $item->state}} </span>
@endif

@if($item->states_id == 13) {{-- Inactivo --}}
   <span class="badge badge-pill badge-danger rounded"> {{ $item->state }} </span>
@endif 

@if($item->states_id == 14) {{-- Pedido de transferencia (entrar)--}}
   <span class="badge badge-pill badge-warning rounded"> {{ $item->state}} </span>
@endif

@if($item->states_id == 15) {{-- Finalista --}}
   <span class="badge badge-pill badge-success rounded"> {{ $item->state}} </span>
@endif