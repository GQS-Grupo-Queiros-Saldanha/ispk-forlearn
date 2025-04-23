


    {{-- Se for uma requisição de Computadores  --}}


@if (isset($nome->requerente))

    {{-- Se for um utilizador interno --}}

    @if($nome->nome_requerente=="")

    {{$nome->requerente}}

    @else

    {{$nome->nome_requerente}}

    @endif


    {{-- Se for uma requisição de livro  --}}
    
@else
    

    {{-- Se for um utilizador interno --}}
    
    @if($nome->nome_requerente=="")

    {{$nome->leitor_nome}}

    @else

    {{$nome->nome_requerente}}

    @endif





@endif