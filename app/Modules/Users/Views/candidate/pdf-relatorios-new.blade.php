@extends('layouts.print')
@section('title', __('Relatório de candidatos'))
@section('content')

<head>
    <style>
        /* Typography */
        @font-face {
            font-family: 'Tinos', serif;
            font-style: normal;
            font-weight: 400;
            src: local('Tinos', serif), url('/fonts/times.woff') format('woff');
        }

        .cabecalho {
            font-family: 'Tinos', serif;
            text-transform: uppercase;
            margin-top: 15px;
        }

        .cabecalho>*,
        .titulo>* {
            padding: 0;
            margin: 0;
            padding-top: 3px;
        }

        .cabecalho .instituition,
        .cabecalho .area,
        .titulo p {
            font-size: 1rem;
            font-weight: 700;
        }

        .cabecalho .instituition {
            font-size: 20px !important;
            letter-spacing: 1px;
            padding-bottom: 0px;
            margin-bottom: 0px;
        }

        .cabecalho .area {
            padding-top: 0px;
        }

        .cabecalho .decreto {
            font-size: 0.5rem;
            text-align: left;
            text-indent: 210px;
            padding-top: 0px;
            top: -10;
            position: relative;
        }

        .cabecalho .logotipo {
            width: 76px;
            height: 96px;
        }

        .titulo p {
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .titulo .a {
            padding-top: 30px;
            padding-bottom: 5px;
        }

        .table_te {
            width: 76%;
            margin-left: 12%;
            text-align: right;
            font-family: calibri light;
            margin-bottom: 6px;
        }

        .cor_linha {
            background-color: #999;
            color: #000;
        }

        .table_te th,
        .table_te td {
            border: 1px solid #fff;
            background-color: #F9F2F4;
        }

        .last-line td {
            background-color: #cecece;
        }

        .line td {
            background-color: #ebebeb;
        }

        .assinaturas p,
        .data {
            font-size: 22px;
        }

        .data,
        .assinaturas {
            margin-left: 12%;
            text-align: right;
            margin-right: 100px;
            margin-top: 350px;
        }

        .div-top {
            background-color: #2f5496 !important;
            color: white;
        }

        .div-top>* {
            color: white !important;
        }

        .bg-white {
            background-color: white !important;
        }

        .bg-total {
            background-color: #F9E994 !important;
        }

        .bg-sexo {
            background-color: #fff2ce !important;
        }

        .bg-p_total {
            background-color: #FACBB1 !important;
        }

        /* Grouped Colors */
        .bg1 {
            background-color: #8eaadb !important;
        }

        .bg2 {
            background-color: #d9e2f3 !important;
        }

        /* Font Sizes */
        .f1 {
            font-size: 13pt !important;
        }

        .f2 {
            font-size: 12pt !important;
        }

        .f3 {
            font-size: 11pt !important;
        }

        .f4 {
            font-size: 10pt !important;
        }

        /* Paddings/Margins */
        .pd {
            width: 60px;
        }

        .pd1 {
            width: 70px;
        }

        .margin-new {
            margin-top: 9%;
        }

        /* Text and Color */
        .f-blue {
            color: #243f60 !important;
        }

        .t-color {
            color: #fc8a17;
        }

        .tr-new-line th {
            font-weight: normal;
        }

        .strange {
            color: #1c65e5;
        }

        .title-dom {
            font-size: 80px;
            font-weight: bold;
            text-align: left;
            margin-top: 35px;
            margin-left: 100px;
            margin-bottom: 5px;
            color: #243f60;
        }

        .title-dom1 {
            font-weight: normal;
            font-size: 40px;
            color: #243f60;
            margin-left: 100px;
            margin-bottom: 70px;
            text-transform: uppercase;
        }

        .title-dom2 {
            font-weight: normal;
            font-size: 32px;
            color: #243f60;
            margin-left: 100px;
        }

        .logotipo img {
            width: 400px;
            height: 400px;
            margin-top: 90px !important;
            margin-bottom: 20px !important;
        }

        .row {
            margin-top: -10px !important;
        }

        table,
        tr,
        td {
            break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<main>
    <div class="row">
        <br>
        <div class="col-12">
            <br>
            <center>
                @php $url = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo; @endphp
                <div class="logotipo" style="margin-top:30px;">
                    <img src="{{ $url }}" class="" srcset="">
                </div>
            </center>
            <h3 class="title-dom">
                <br>
                <br>
                RELATÓRIO:
            </h3>
            <h4 class="title-dom1">
                <titles class="t-color"> Evolução das candidaturas</titles>
                <br>
            </h4>
            <h4 class="title-dom2">
                <b>Ano lectivo:</b>
                <titles class="t-color">{{ $lectiveYears[0]->currentTranslation->display_name }}</titles>
                <div style="height:10px;color:white;">f</div>
                <b>Fase:</b>
                <titles class="t-color">{{ $lectiveFase->fase }}ª</titles>
            </h4>
            <br>
            <br>
            <div class="data">
                <span style="text-transform: capitalize;">{{ $institution->municipio }}</span>,
                aos
                @php
                    $meses = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                    echo date('d') . ' de ' . $meses[date('m')] . ' de ' . date('Y');
                @endphp
                <br>
                <titles class="t-color">Powered by</titles> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
            </div>
        </div>
        <br>
        <div class="col-12" style="margin-top: 1100px;height: 500px;width:200px;color:white;">asa</div>
    </div>
    <div class="row">
        <div class="col-12">
            <div>
                <div>
                    <br><br><br><br><br>
                    <table class="table_te margin-new" style="width: 93%;margin-left:4%;margin-right:0;">
                        <br>
                        <tr class="line">
                            <th colspan="23" class="text-left text-BLACK bg-white text-uppercase font-weight-bold f1 t-color">
                                <titles class="f-blue">Quadro 1:</titles> Vagas vs Candidatos
                            </th>
                        </tr>
                    </table>

                    @php
                        $dataTotals = [
                            'vagas' => ['manha' => 0, 'tarde' => 0, 'noite' => 0],
                            'candidaturas' => ['total_m' => 0, 'total_f' => 0, 'manha' => 0, 'tarde' => 0, 'noite' => 0],
                            'exames' => ['total_m' => 0, 'total_f' => 0],
                            'ausentes' => ['total_m' => 0, 'total_f' => 0],
                            'reprovados' => ['total_m' => 0, 'total_f' => 0],
                            'admitidos' => ['total_m' => 0, 'total_f' => 0],
                            'matriculados' => ['total_m' => 0, 'total_f' => 0],
                        ];
                    @endphp

                    @foreach ($vagas as $key => $items)
                        <table class="page-break table_te" style="width: 90%;margin-left:4%;margin-right:0;">
                            <tr class="line">
                                <th colspan="23" class="text-left text-white div-top text-uppercase font-weight-bold f1">
                                    {{ $key }}
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3">#</th>
                                <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3" colspan="2">CURSOS</th>
                                <th class="text-center bg1 font-weight-bold f3 pd" colspan="4">M</th>
                                <th class="text-center bg1 font-weight-bold f3 pd" colspan="4">T</th>
                                <th class="text-center bg1 font-weight-bold f3 pd" colspan="4">N</th>
                                <th class="text-center bg1 font-weight-bold f3 pd1" colspan="5">Total</th>
                            </tr>
                            <tr class="tr-new-line">
                                <th class="text-center bg1 f3 pd" rowspan="2">V</th>
                                <th class="text-center bg1 f3 pd t-color" colspan="3">C</th>
                                <th class="text-center bg1 f3 pd" rowspan="2">V</th>
                                <th class="text-center bg1 f3 pd t-color" colspan="3">C</th>
                                <th class="text-center bg1 f3 pd" rowspan="2">V</th>
                                <th class="text-center bg1 f3 pd t-color" colspan="3">C</th>
                                <th class="text-center bg1 f3 pd" rowspan="2">V</th>
                                <th class="text-center bg1 f3 pd t-color" colspan="3">C</th>
                                <th class="text-center bg1 f3 pd" rowspan=2>%</th>
                            </tr>
                            <tr class="tr-new-line" style="font-weight:bold">
                                <th class="text-center bg1 f3 pd">Total</th>
                                <th class="text-center bg1 f3 pd">m</th>
                                <th class="text-center bg1 f3 pd">f</th>
                                <th class="text-center bg1 f3 pd">Total</th>
                                <th class="text-center bg1 f3 pd">m</th>
                                <th class="text-center bg1 f3 pd">f</th>
                                <th class="text-center bg1 f3 pd">Total</th>
                                <th class="text-center bg1 f3 pd">m</th>
                                <th class="text-center bg1 f3 pd">f</th>
                                <th class="text-center bg1 f3 pd">Total</th>
                                <th class="text-center bg1 f3 pd">m</th>
                                <th class="text-center bg1 f3 pd">f</th>
                            </tr>

                            @foreach ($items as $item_vagas)
                                @php
                                    $candidaturasManha = $candidatos[$item_vagas->courses_id]['manha']['candidaturas'] ?? ['total' => 0, 'm' => 0, 'f' => 0];
                                    $candidaturasTarde = $candidatos[$item_vagas->courses_id]['tarde']['candidaturas'] ?? ['total' => 0, 'm' => 0, 'f' => 0];
                                    $candidaturasNoite = $candidatos[$item_vagas->courses_id]['noite']['candidaturas'] ?? ['total' => 0, 'm' => 0, 'f' => 0];

                                    $totalVagas = $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha;
                                    $totalCandidatos = $candidaturasManha['total'] + $candidaturasTarde['total'] + $candidaturasNoite['total'];
                                    $totalCandidatosM = $candidaturasManha['m'] + $candidaturasTarde['m'] + $candidaturasNoite['m'];
                                    $totalCandidatosF = $candidaturasManha['f'] + $candidaturasTarde['f'] + $candidaturasNoite['f'];
                                    $percentual = $totalVagas > 0 ? round(($totalCandidatos / $totalVagas) * 100) : 0;
                                @endphp
                                <tr class="f2">
                                    <td class="text-center bg2">{{ $loop->iteration }}</td>
                                    <td class="text-left bg2 text-uppercase" style="width:350px" colspan="2">{{ $item_vagas->abbreviation }}</td>
                                    <td class="text-center bg2">{{ $item_vagas->manha ?? '-' }}</td>
                                    <td class="text-center bg-total t-color">{{ $candidaturasManha['total'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasManha['m'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasManha['f'] ?? '-' }}</td>
                                    <td class="text-center bg2">{{ $item_vagas->tarde ?? '-' }}</td>
                                    <td class="text-center bg-total t-color">{{ $candidaturasTarde['total'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasTarde['m'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasTarde['f'] ?? '-' }}</td>
                                    <td class="text-center bg2">{{ $item_vagas->noite ?? '-' }}</td>
                                    <td class="text-center bg-total t-color">{{ $candidaturasNoite['total'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasNoite['m'] ?? '-' }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $candidaturasNoite['f'] ?? '-' }}</td>
                                    <td class="text-center bg2">{{ $totalVagas ?? '-' }}</td>
                                    <td class="text-center bg-total t-color">{{ $totalCandidatos }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $totalCandidatosM }}</td>
                                    <td class="text-center bg-sexo t-color">{{ $totalCandidatosF }}</td>
                                    <td class="text-center bg-p_total">{{ $percentual }}%</td>
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>
@endsection