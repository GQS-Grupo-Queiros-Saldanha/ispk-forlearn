
@switch($item->tipo)
    @case(1)
        Estudante
    @break

    @case(2)
        Docente
    @break

    @case(3)
        Administrativo
    @break

    @default
@endswitch
