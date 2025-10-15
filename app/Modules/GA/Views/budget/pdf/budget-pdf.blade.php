{{-- @include('GA::budget.pdf.cabecalho') --}}



@extends('layouts.print')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">


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
            background-image: url('https://forlearn.ao/storage/attachment/{{ $institution->logotipo }}');
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

        @include('GA::budget.pdf.cabecalho')

        <!-- aqui termina o cabeçalho do pdf -->
        <div class="corpo-element">


            @php
                $soma_capitulo = 0;
                $soma_orcamento = 0;
            @endphp


            <div class="row">
                <div class="col-12 ">
                    <br><br>
                    <table class="table-orcamento table dtr-inline table">
                        <thead>
                            <tr class="font-20">
                                <th colspan="4" class="bg-white"></th>
                                <th class="th-preco" colspan="2">Valor</th>
                            </tr>


                            <tr class="font-20 t-cabecalho">
                                <th class="text-left">Nº</th>
                                <th class="text-left">Designação do trabalho</th>
                                <th>Un.</th>
                                <th>Quantidade</th>
                                <th>Unitário</th>
                                <th>Total</th>
                            </tr>

                            <tr class="f-small">
                                <th colspan="6" class="bg-white"
                                    style="font-size: 1px;padding: 1px;color: white!important;">1</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($capitulo as $cap)
                                <tr class="font-20">
                                    <th class="text-left" style="background-color: #eeeeee;"><b>{{ $cap->code }}.</b>
                                    </th>
                                    <th class="text-left" style="background-color: #eeeeee;"><b>{{ $cap->name }}</b></th>
                                    <th colspan="4" class="bg-white"></th>

                                </tr>


                                @foreach ($artigo as $art)
                                    @if ($art->id_capitulo == $cap->id)
                                        <tr>
                                            <th class="text-right">{{ $cap->code }}.{{ $art->code_artigo }}</th>
                                            <th class="text-left">{{ $art->nome_artigo }}</th>
                                            <th>{{ $art->unidade_artigo }}</th>
                                            <th>{{ $art->quantidade_artigo }}</th>
                                            <th class="text-right">
                                                {{ number_format($art->money_artigo, 2, ',', '.') . ' kz' }}</th>
                                            <th class="text-right">
                                                {{ number_format($art->money_artigo * $art->quantidade_artigo, 2, ',', '.') . ' kz' }}
                                            </th>

                                        </tr>
                                        @php
                                            $soma_capitulo = $soma_capitulo + $art->money_artigo * $art->quantidade_artigo;
                                        @endphp
                                    @endif
                                @endforeach
                                <tr class="f-small">
                                    <th colspan="6" class="bg-white" style="font-size: 1px;padding: 1px;color: white;">1
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="bg-white"></th>
                                    <th class="th-preco" style="text-align: right!important;"><b>Sub total</b></th>
                                    <th class="text-right th-valor" style="text-align: right!important;">
                                        <b>{{ number_format($soma_capitulo, 2, ',', '.') . ' kz' }}</b>
                                    </th>

                                    @php
                                        $soma_orcamento = $soma_orcamento + $soma_capitulo;
                                        $soma_capitulo = 0;
                                    @endphp
                                </tr>
                            @endforeach
                            <tr class="f-small">
                                <th colspan="6" class="bg-white"
                                    style="font-size: 0px;padding:0px;color: white!important;">1</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="bg-white"></th>
                                <th class="text-right th-preco font-20"><b>Total</b></th>
                                <th class="text-right th-valor font-20" style="text-align: right!important;">
                                    <b>{{ number_format($soma_orcamento, 2, ',', '.') . ' kz' }}</b>
                                </th>
                            </tr>

                        </tbody>
                    </table>
                    <br> <br><br> <br> <br> <br>
                    <div style="text-align:left;page-break-before:inherit;margin-left: 0px;">
                        <p style="font-size:16pt; text-align:left;margin-left: 7px;">
                            Benguela, {{ $dataActual }}.
                        </p>
                        <br>

                        <p style="font-size:16pt;text-align:left;margin-left: 7px;">
                            {{ $funcionario->value == '' ? ' ' : $funcionario->value }}
                        </p>
                        <p style="font-size:16pt;text-align:left;margin-left: 7px;">
                            _________________________
                        </p>
                    </div>

                </div>

            </div>


        </div>

    </main>
@endsection
