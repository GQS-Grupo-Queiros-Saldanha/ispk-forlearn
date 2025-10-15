@section('title',__(' Percurso Académico')) 
@extends('layouts.print')
@section('content')

    <style>
        html, body {

        }

        body {
            font-family: Montserrat, sans-serif;
        }

        .table td,
        .table th {
            padding: 2px;
        }

        .form-group, .card, label {
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
            border-left: 1px solid #BCBCBC;
            border-right: 1px solid #BCBCBC;
            border-bottom: 1px solid #BCBCBC;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .425rem;
        }

        .div-top {
            text-transform: uppercase;
            position: relative;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
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

        input, textarea, select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

    </style>
    <main>
        <div class="div-top" style="height:120px; padding:20px;">
            <table class="table m-0 p-0">
                <tr>

                    <td class="pl-1">
                        <h3 class="h1-title">
                            Percurso Académico
                        </h3>
                    </td>
                    <td class="td-institution-name" rowspan="2">
                        Instituto Superior<br>Universitário Maravilha
                    </td>
                    <td class="td-institution-logo" rowspan="2">
                        <img class="img-institution-logo" src="{{ asset('img/logo.jpg') }}" alt="">
                    </td>
                </tr>
                <tr>
                    <td class="pl-1 pt-3">
                        <span style="font-size: 10pt; !important">
                            Documento gerado a
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
         <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nº de matrícula</th>
                                <th class="text-center">Nome completo</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Número</th>
                                <th class="text-center">Curso</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td class="text-center">{{ $studentInfo->matriculation->code}}</td>
                            <td class="text-center">{{ $personalName }}</td>
                            <td class="text-center">{{ $studentInfo->email }}</td>
                            <td class="text-center">{{ $matriculationCode }}</td>
                            <td class="text-center">
                                @foreach ($studentInfo->courses as $course)
                                    {{$course->currentTranslation->display_name}}
                                 @endforeach
                            </td>
                            <td class="text-center"> --- </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <th class="text-center">Ano</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Disciplina</th>
                                    <th class="text-center ">Ano lectivo</th>
                                    <th class="text-center ">Nota</th>
                                </thead>
                                <tbody>
                                    @foreach ($disciplines as $discipline)
                                        <tr>
                                            <td class="text-center">{{ $discipline->year}} º</td>
                                            <td class="text-center">{{ $discipline->code }}</td>
                                            <td class="pl-3">{{ $discipline->name }}</td>
                                            <td class="text-center ">{{ $discipline->lective_year}}</td>
                                            <td class="text-center ">{{ $discipline->grade}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         </div>
    </main>

@endsection

<script>
    window.print();
</script>
