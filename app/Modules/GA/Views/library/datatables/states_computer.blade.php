

@switch($states->estado_requisicao)
@case('Em curso')
    <p class="estado-andamento requisicao-computador-{{$states->codigo}}">Em curso</p> 
@break

@case('Finalizada')
    <p class="estado-finalizado"> Finalizada </p>
@break

@default
@endswitch 
