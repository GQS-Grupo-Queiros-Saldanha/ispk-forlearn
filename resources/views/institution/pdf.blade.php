@extends('layouts.print')
<title>Instituição | forLEARN</title>
@section('content')

    <link href="http://fonts.cdnfonts.com/css/calibri-light" rel="stylesheet">
    <title>Instituição | forLEARN</title>
    <style>
        @import url('http://fonts.cdnfonts.com/css/calibri-light');


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
            font-size: 2em;
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
            /* border-left: 1px solid #BCBCBC;
            border-right: 1px solid #BCBCBC; */
            /* border-bottom: 1px solid #BCBCBC; */
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
            background-position: right;
            background-repeat: no-repeat;
            background-size: 10%;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: justify;
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

        .mediaClass td {
            border: 1px solid #fff;


        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }
    </style>

    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'INSTITUIÇÃO DE ENSINO';
        $discipline_code = '';
    @endphp
    @include('Reports::pdf_model.forLEARN_header')


    <main>

        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te">
                            <style>
                                .table_te {
                                    background-color: #F5F3F3;
                                    !important;
                                    width: 100%;
                                    text-align: left;
                                    font-family: calibri light;
                                    margin-bottom: 6px;
                                    font-size: 14pt;
                                }

                                .cor_linha {
                                    background-color: #999;
                                    color: #000;
                                }

                                .table_te th {
                                    border-left: 1px solid #fff;
                                    border-bottom: 1px solid #fff;
                                    padding: 4px;
                                    !important;
                                    text-align: left;
                                    font-size: 18pt;
                                    font-weight: bold;
                                }

                                .table_te td {
                                    border-left: 1px solid #fff;
                                    background-color: rgb(240 240 240);
                                    border-bottom: 1px solid white;
                                    font-size: 14pt;
                                }

                                .tabble_te thead {}
                            </style>
                            <tr>

                                @php
                                    $count = 0;
                                @endphp

                                {{-- @foreach ($model as $curso)
                                @if ($curso->state == 'total')
                                    @php
                                    $count++;
                                    @endphp
                                @endif
                                @endforeach   --}}
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- personalName -->
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                @endphp
                                <table class="table_te">
                                    <tr>
                                        <th class="bg1" colspan="3" style="background-color:white; padding: 0px;">
                                            <b>DADOS GERAIS</b>
                                        </th>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Nome:</td>
                                        <td><b>{{ $institution->nome }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Morada:</td>
                                        <td><b>{{ $institution->morada }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Província:</td>
                                        <td><b>{{ $institution->provincia }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Município:</td>
                                        <td><b>{{ $institution->municipio }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Contribuinte:</td>
                                        <td><b>{{ $institution->contribuinte }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Capital social:</td>
                                        <td><b>{{ $institution->capital_social }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Registro comercial nº:</td>
                                        <td><b>{{ $institution->registro_comercial_n }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Conservatória do registro comercial:</td>
                                        <td><b>{{ $institution->registro_comercial_de }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Domínio de internet:</td>
                                        <td><b>{{ $institution->dominio_internet }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Decreto da Instituição:</td>
                                        <td><b>{{ $institution->decreto_instituicao }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Decreto dos Cursos:</td>
                                        <td><b>{{ $institution->decreto_cursos }}</b></td>
                                    </tr>
                                </table>
                                <br>

                                <table class="table_te">
                                    <tr>
                                        <th class="bg1" colspan="3" style="background-color:white; padding: 0px;">
                                            CONTACTOS GERAIS</th>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Telefone geral:</td>
                                        <td><b>{{ $institution->telefone_geral }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Telemóvel geral:</td>
                                        <td><b>{{ $institution->telemovel_geral }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>E-mail:</td>
                                        <td><b>{{ $institution->email }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Whatsapp:</td>
                                        <td><b>{{ $institution->whatsapp }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Facebook:</td>
                                        <td><b>{{ $institution->facebook }}</b></td>
                                    </tr>
                                    <tr class="bg1">
                                        <td>Instagram:</td>
                                        <td><b>{{ $institution->instagram }}</b></td>
                                    </tr>
                                </table>
                                <br>

                                <table class="table_te">
                                    <tr>
                                        <th class="bg1" colspan="3" style="background-color:white; padding: 0px;">
                                            <b>DIRECÇÃO ACADÉMICA</b></th>
                                    </tr>
                                    <tr>
                                        <td class="">Descrição</td>
                                        <td class="">Nome completo</td>
                                        <td class="">E-mail</td>
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[0])
                                            @foreach ($institution_cargos[0] as $dir_geral)
                                                @if (isset($director->id) && $director->id == $dir_geral->users_id)
                                                    <td>Presidente:</td>
                                                    <td><b>{{ $dir_geral->value }}</b></td>
                                                    <td><b>{{ $dir_geral->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[1])
                                            @foreach ($institution_cargos[1] as $vd_acad)
                                                @if (isset($institution->vice_director_academica) && $institution->vice_director_academica == $vd_acad->users_id)
                                                    <td>Vice-director(a) área académica:</td>
                                                    <td><b>{{ $vd_acad->value }}</b></td>
                                                    <td><b>{{ $vd_acad->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[2])
                                            @foreach ($institution_cargos[2] as $vd_cient)
                                                @if (isset($institution->vice_director_cientifica) && $institution->vice_director_cientifica == $vd_cient->users_id)
                                                    <td>Vice-director(a) área científica:</td>
                                                    <td><b>{{ $vd_cient->value }}</b></td>
                                                    <td><b>{{ $vd_cient->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[3])
                                            @foreach ($institution_cargos[3] as $daac)
                                                @if (isset($institution->daac) && $institution->daac == $daac->users_id)
                                                    <td>DAAC:</td>
                                                    <td><b>{{ $daac->value }}</b></td>
                                                    <td><b>{{ $daac->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[4])
                                            @foreach ($institution_cargos[4] as $gab_ter)
                                                @if (isset($institution->gabinete_termos) && $institution->gabinete_termos == $gab_ter->users_id)
                                                    <td>Gabinete de termos:</td>
                                                    <td><b>{{ $gab_ter->value }}</b></td>
                                                    <td><b>{{ $gab_ter->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[5])
                                            @foreach ($institution_cargos[5] as $sec_acad)
                                                @if (isset($institution->secretaria_academica) && $institution->secretaria_academica == $sec_acad->users_id)
                                                    <td>Secretaria académica:</td>
                                                    <td><b>{{ $sec_acad->value }}</b></td>
                                                    <td><b>{{ $sec_acad->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                </table>
                                <br>
                                <table class="table_te">
                                    <tr>
                                        <th class="bg1" colspan="3" style="background-color:white; padding: 0px;">
                                            <b>DIRECÇÃO EXECUTIVA</th>
                                    </tr>
                                    <tr>
                                        <td>Descrição</td>
                                        <td>Nome completo</td>
                                        <td>E-mail</td>
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[6])
                                            @foreach ($institution_cargos[6] as $director_executivo)
                                                @if (isset($institution->director_executivo) && $institution->director_executivo == $director_executivo->users_id)
                                                    <td>Director(a) executivo:</td>
                                                    <td><b>{{ $director_executivo->value }}</b></td>
                                                    <td><b>{{ $director_executivo->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                    <tr>
                                        @isset($institution_cargos[7])
                                            @foreach ($institution_cargos[7] as $rh)
                                                @if (isset($institution->recursos_humanos) && $institution->recursos_humanos == $rh->users_id)
                                                    <td>Recursos Humanos:</td>
                                                    <td><b>{{ $rh->value }}</b></td>
                                                    <td><b>{{ $rh->email }}</b></td>
                                                @endif
                                            @endforeach
                                        @endisset
                                    </tr>
                                </table>
                                <br>
                                <table class="table_te">
                                    <tr>
                                        <th class="bg1" colspan="3" style="background-color:white; padding: 0px;">
                                            <b>PROPRETÁRIO DA IE</b></th>
                                    </tr>
                                    <tr>
                                        <td>Nome do propriétário:</td>
                                        <td><b>{{ $institution->nome_dono }}</b></td>
                                    </tr>
                                    <tr>
                                        <td>NIF do propriétário:</td>
                                        <td><b>{{ $institution->nif }}</b></td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="">
                                <br>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
