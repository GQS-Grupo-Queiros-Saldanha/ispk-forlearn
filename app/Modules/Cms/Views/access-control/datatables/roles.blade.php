@php
$cargo =  array();

@endphp



@foreach ($cargos as $cg)
    @if ($cg->id_usuario == $item->id_user)
        @php
            $cargo[] = " ".$cg->cargo_usuario;
        @endphp
    @endif
@endforeach


@php
echo implode(',', $cargo);
@endphp
