@include('Lessons::pdf.header')

@extends('layouts.print')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    @php $url = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo; @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        body {
            font-family: 'Calibri Light', sans-serif;
        }

        html,
        body {
            padding: 0;
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;

        }

        .form-group,
        .card,
        label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

        .h1-title {

            padding: 0;
            margin-bottom: 0;
            font-size: 3.3em;
            font-weight: bold;
            margin-left: 130px;
            margin-right: 130px;
            margin-top: 100px;

        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
        }

        .table-parameter-group {
            page-break-inside: avoid;
        }

        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }

        .tbody-parameter-group {
            border-top: 0;
            padding: 0;
            margin: 0;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }

        .div-top {
            height: 99px;
            text-transform: uppercase;
            position: relative;
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('{{ $url }}');
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 7%;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: justify;
        }

        .td-institution-logo {
            vertical-align: middle !important;

        }

        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }

        input,
        textarea,
        select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 85px;
            height: 100%;
            margin-bottom: 5px;
            background-position: 50%;
            margin-right: 8px;
        }

        .td-declaracao {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 85px;
            height: 100%;
            margin-bottom: 5px;
            text-align: 30px;
            background-position: 50%;
            margin-right: 8px;
            padding-top: 3000px;
        }

        .mediaClass td {
            border: 1px solid #fff;


        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        table {
            page-break-inside: auto;

        }

        .table-orcamento tr {
            page-break-inside: avoid;
            page-break-after: auto;
            background-color: rgba(0, 0, 0, 0.03);
        }


        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        p {

            font-size: 17pt;
            margin-left: 80px;
            margin-right: 80px;
            color: black;
            text-align: justify;
        }

        .dados_pessoais {
            margin-bottom: -5;
        }

        .corpo-element {
            margin-left: 15px;
            margin-right: 15px;
        }

        .table-orcamento {
            page-break-inside: auto;
            padding: 5px;
            /* background-color: #3D3C3C; */
            width: 100%;
            font-size: 18px;
        }

        .table-orcamento tr {
            padding: 8px;
        }

        .table-orcamento tr th {
            text-align: center;
            padding: 8px;
            border: 2px solid white;
        }

        .th-preco {
            text-align: center !important;
            background-color: #c3c3c3;
            padding: 4px;
            padding-top: 1px !important;
            padding-bottom: 1px !important;
        }

        .table-orcamento tr {
            page-break-inside: avoid;
            page-break-after: auto;
            background-color: rgba(0, 0, 0, 0.03);
        }

        .th-valor {
            text-align: center !important;
            background-color: #eeeeee;
            padding: 4px;
            padding-top: 1px !important;
            padding-bottom: 1px !important;
        }

        .table-orcamento thead tr {
            text-align: center !important;
            background-color: #c4c1c1;
        }

        .f-white {
            background-color: white !important;
        }

        .font-20 {
            font-size: 20px;
        }

        .f-small th {
            font-size: 8px !important;
        }

        .t-cabecalho {
            background-color: rgba(0, 0, 0, 0.2) !important;
        }
    </style>
    <main>

        {{-- @include('GA::budget.pdf.cabecalho') --}}

        <div class="corpo-element">

            <div class="row">
                <div class="col-12 ">
                    
                    <table class="table mt-2 text-center">
                        <thead>
                            <th>ID</th>
                            <th>Ano</th>
                            <th>Cadeira</th>
                            <th>Professor</th>
                            <th>Horario</th>
                        </thead>
                        <tbody>
                            @foreach($aulas  as $aula)
                                <tr>
                                    <td>{{$aula->id}}</td>
                                    <td>{{$aula->ano}}</td>
                                    <td>{{$aula->cadeira}}</td>
                                    <td>{{$aula->professor}}</td>
                                    <td>{{$aula->horario}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 text-center ">
                        <div>{{$dataActual}}</div>
                        <div class="mt-3">O Funcionário em exercicío</div>
                        <div class="mt-2">____________________________________________</div>
                        <div>{{$userLogado->name}}</div>
                    </div>
                </div>

            </div>


        </div>

    </main>
@endsection
