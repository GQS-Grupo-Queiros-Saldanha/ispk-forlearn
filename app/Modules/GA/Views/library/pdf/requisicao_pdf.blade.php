
@section('title', __('RECIBO'))
@extends('layouts.print')
@section('content')

    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <style>
        

        * {}

        body {
            margin: 10px;

        }

        .tabela-dados,
        .tabela-data,
        .tabela-estado,
        .tabela-livros {}

        .tabela-dados th,
        .tabela-data th,
        .tabela-estado th,
        .tabela-livros th {

            padding: 6px;
            border-bottom: 1px solid E7E9EB;

            border: none;
        }

        .tabela-dados td,
        .tabela-data td,
        .tabela-estado td,
        .tabela-livros td {
            padding: 6px;
        }

        .tabela-dados thead,
        .tabela-data thead,
        .tabela-estado thead,
        .tabela-livros thead {
            background-color: rgba(128, 128, 128, 0.315);
        }


        .tabela-livros tbody tr {

            background-color: #E7E9EB;
            border-bottom: 1px solid white;

        }

        .tabela-dados {}

        .tabela-data {
            width: 40%;

        }

        .tabela-estado {
            margin-left: 50%;
            width: 10%;
            float: right;
        }

        .tabela-dados thead th {
            padding-bottom: 3px;
            background-color: white;
            font-size: 15px;

        }

        .corpo {
            margin-left: 0px;
            margin-right: 0px;
        }

        .leitor,
        .bibliotecario {
            border-top: 1px solid black;
            text-align: left;
            padding-left: 0px;
            padding-top: 6px;
        }

        .assinatura {

            margin-top: 200px;
        }

        th,
        td {
            border: none !important;
        }

        .titulo-dica {
            background-color: rgba(128, 128, 128, 0.315);
            width: 100%;
            padding: 6px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .img-profile {

            width: 120px;
            height: 100px;
            padding: 0px;

        }

    </style>


    @include('GA::library.pdf.header')

    <div class="row corpo">

        <div class="col-12">


            <div class="row" style="font-weight: bold; padding-left: 10px;">

                <h5>RECIBO Nº: <b>{{ $requisicao[0]->referencia }}</b></h5>

            </div>

            <div class="row">


                <div class="titulo-dica">
                    Dados do requerente
                </div>


            </div>

            {{-- <div class="row mt-2">

                

                    @if (isset($requisicao[0]->fotografia))

                    <img src={{"https://forlearn.ao/storage/attachment/".$requisicao[0]->fotografia}} class="rounded img-profile "
                    alt="Foto do leitor" id="imgLeitor">   
                    @else
                        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" class="rounded img-profile"
                        alt="Foto do leitor" id="imgLeitor">
                    @endif
                

            </div> --}}

            <div class="row">



                <table class="tabela-dados col-6 mt-2" style="border:none;">

                    {{-- Se for um visitante / Utilizador --}}

                    @if ($requisicao[0]->nome_visitante == '')

                        <thead>

                            <tr>
                                <th>Nome completo</th>
                                <th>Bilhete de identidade</th>
                                <th>email</th>
                                <th>Telefone principal</th>
                                <th>Telefone alternativo</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($requisicao as $item)
                                <tr>
                                    <td>{{ $item->leitor_nome }}</td>

                                    @if (isset($bilhete[0]->bi_leitor))
                                        <td>{{ $bilhete[0]->bi_leitor }}</td>
                                    @else
                                        <td>Documento sem data</td>
                                    @endif

                                    <td>{{ $item->email }}</td>

                                    @if (isset($telefone[0]->telefone_leitor))
                                        <td>{{ $telefone[0]->telefone_leitor }}</td>
                                    @else
                                        <td>
                                            < Sem telefone>
                                        </td>
                                    @endif 

                                    @if (isset($telefone2[0]->telefone_leitor))
                                        <td>{{ $telefone2[0]->telefone_leitor }}</td>
                                    @else
                                        <td>
                                            < Sem telefone>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach

                        </tbody>
                    @else
                        <thead>

                            <tr>
                                <th>Nome completo</th>
                                <th>Telefone</th>
                                <th>Instituição de ensino</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($requisicao as $item)
                                <tr>
                                    <td>{{ $item->nome_visitante }}</td>

                                    @if (isset($item->telefone_visitante))
                                        <td>{{ $item->telefone_visitante }}</td>
                                    @else
                                        <td>
                                            < Sem telefone>
                                        </td>
                                    @endif

                                    @if (isset($item->instituicao_visitante))
                                        <td>{{ $item->instituicao_visitante }}</td>
                                    @else
                                        <td>
                                            < Sem Instituição>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach

                        </tbody>

                    @endif


                </table>


            </div>

            <div class="row">


                <table class="tabela-data col-4 mt-3 mb-2">

                    {{-- <thead>

                        <tr>
                            <th>Data da requisição</th>
                            <th>Data da devolução</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($requisicao as $item)
                            <tr>
                                <td>{{ $item->data_inicio }}</td>
                                <td>{{ $item->data_fim }}</td>
                            </tr>
                        @endforeach

                    </tbody> --}}

                </table>
                <table class="tabela-estado col-4 mt-3 mb-2">

                    <thead>

                        <tr>
                            <th>Estado</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($requisicao as $item)
                            <tr>
                                <td>{{ $item->estado }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

            <div class="row">

                <div class="titulo-dica">

                    @if (count($livros) > 1)
                        Livros requeridos
                    @else
                        Livro requerido
                    @endif

                </div>
            </div>

            <div class="row">

                <table class=" tabela-livros col-12 mt-2">

                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            {{-- <th>Código</th> --}}
                            <th>Título</th>
                            <th>Subtítulo</th>
                            <th>Autor(es)</th>
                            <th>Editora</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            
                            $i = 1;
                            
                        @endphp

                        @foreach ($livros as $item)
                            <tr>

                                <td class="text-center">{{ $i++ }}</td>
                                {{-- <td>{{ $item[0] }}</td> --}}
                                <td>{{ $item[1] }}</td>
                                <td>{{ $item[2] }}</td>
                                <td>{{ $item[4] }}</td>
                                <td>{{ $item[5] }}</td>
                                <td>{{ $item[6] }}</td>

                            </tr>
                        @endforeach


                    </tbody>

                </table>

            </div>

            <div class="row">


                <table class="tabela-data col-4 mt-1 mb-2 ">

                    <thead>



                    </thead>


                </table>
                <table class="tabela-estado col-4 mt-1 mb-2 text-left">

                    <thead>

                        <tr>
                            <th>Total: {{ count($livros) }}</th>

                        </tr>

                    </thead>

                </table>

            </div>

            <div class="col-12 text-center assinatura position-absolute bottom-50 end-50">


                <div class="row mb-2">

                  
                    <div class="col-4 text-left" style="padding: 0px!important">

                        Data da requisição: <b>
                            @foreach ($requisicao as $item)
                                {{ $item->data_inicio }}
                            @endforeach
                        </b>

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 text-left" style="padding: 0px!important">



                    </div>

                    <div class="col-2">

                    </div>

                </div>


                <div class="row">

                  
                    <div class="col-4 text-left" style="padding: 0px!important">

                        O Requerente

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 text-left" style="padding: 0px!important">

                        O Bibliotecário(a)

                    </div>

                    <div class="col-2">

                    </div>
                </div>

                <div class="row mt-4 mb-4">

                   
                    <div class="col-4 leitor ">

                        @if ($item->nome_visitante == '')
                        {{ $item->leitor_nome }}
                    @else
                        {{ $item->nome_visitante }}
                    @endif

                 

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 bibliotecario">

                        ( {{ $requisicao[0]->bibliotecario }} )

                    </div>

                    <div class="col-2">

                    </div>

                </div>

                <div class="row mb-2  mt-4">


                    <div class="col-4 text-left" style="padding: 0px!important">

                        Data da devolução: <b>
                            @foreach ($requisicao as $item)
                                {{ $item->data_fim }}
                            @endforeach
                        </b>

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 text-left" style="padding: 0px!important">



                    </div>

                    <div class="col-2">

                    </div>

                </div>
                <div class="row">

                    <div class="col-4 text-left" style="padding: 0px!important">

                        O Requerente

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 text-left" style="padding: 0px!important">

                        O Bibliotecário(a)

                    </div>

                    <div class="col-2">

                    </div>
                </div>

                <div class="row mt-4">

                    <div class="col-4 leitor ">

                        @if ($item->nome_visitante == '')
                        {{ $item->leitor_nome }}
                        @else
                            {{ $item->nome_visitante }}
                        @endif

                    </div>

                    <div class="col-2">

                    </div>

                    <div class="col-4 bibliotecario">

                        ({{ $bibliotecario[0]->nome_bibliotecario }})

                    </div>

                    <div class="col-2">

                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection
