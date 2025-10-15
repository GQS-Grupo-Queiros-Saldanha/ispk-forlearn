 
@section('title', __('RECIBO'))
@extends('layouts.print')
@section('content')

    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <style>
        

        * {}

        body{
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
            mari
        }
        .tabela-livros tbody tr{
            border-bottom:1px solid white;
            font-size:13px;
        }

    </style>


    @include('GA::library.pdf.header')

    <div class="row corpo">

        <div class="col-12">

            <div class="row">


               

            </div>


            <div class="row">

                <div class="titulo-dica">

                  Autores registrados

                </div>
            </div>

            <div class="row">

                <table class=" tabela-livros col-12 mt-2">

                    <thead>
                        <tr>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nome completo</th>
                                <th>Gênero</th>
                                <th>País</th>
                                <th>Descrição</th>
                                <th>Obras</th>
                                {{-- <th>Bibliotecário</th> --}}
                            </tr>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            
                            $i = 1;
                            
                        @endphp

                        @foreach ($autores as $item)
                            <tr>

                                <td class="text-center">{{ $i++ }}</td>
                                <td>{{ $item->name." ".$item->surname }}</td>
                                <td>{{ $item->genre }}</td>
                                <td>{{ $item->country }}</td>
                                <td>{{ $item->others_information }}</td>
                                <td>

                                    {{-- Verificar a quantidade de livros de um determinado autor  --}}
                                        @php $total=0; @endphp


                                    @foreach ($autores_total_livro as $quantidade)
                                        
                                        @php $livro = explode("-",$quantidade); @endphp

                                        @if ($item->id==$livro[0])
                                            
                                            @php $total = $livro[1]; @endphp

                                        @endif

                                    @endforeach
                                    
                                    {{$total}}
                                    
                                </td>

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

                <table class="tabela-estado col-4 mt-3 mb-2 text-left" style="width: 10%; margin-left: 50%;"> 
                    
                    <thead>   
                        
                        <tr>

                            <th>Total</th> 

                        </tr>

                    </thead> 
 
                    <tbody>
 
                        <tr>
                            
                            <td>{{count($autores)}}</td> 
                            
                        </tr>

                    </tbody>

                </table>

            </div>

           

        </div>
    </div>

@endsection
