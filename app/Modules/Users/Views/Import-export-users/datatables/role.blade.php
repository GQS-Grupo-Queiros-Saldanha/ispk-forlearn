@if ($item->nome_cargo == null)
    <span class="badge bg-success">Estudante</span></h1>
@else
    <span class="badge bg-secondary"> {{ $item->nome_cargo }}</span></h1>
   
@endif
