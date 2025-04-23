

@switch($states->estado)
    @case('Em curso')
        <p class="estado-andamento requisicao-{{$states->codigo}}">Em curso</p> 
    @break

    @case('Finalizada')
        <p class="estado-finalizado"> Finalizada </p>
    @break

    @default
@endswitch 



 