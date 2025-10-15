@extends('layouts.print')
@section('content')
<style>
    html,
    body {}

    body {
        font-family: Montserrat, sans-serif;
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
        font-size: 1.75rem;
    }

    .h1-title {
        padding: 0;
        margin-bottom: 0;
        font-size: 1.5em;
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
        font-size: 0.8rem;
    }

    .div-top {
        text-transform: uppercase;
        position: relative;
        /* border-top: 1px solid #000; */
        /* border-bottom: 1px solid #000; */
        /* margin-bottom: 25px; */
        margin-bottom: 2px;
        background-color: rgb(240, 240, 240);
        background-image: url('https://dev.forlearn.ao/instituicao-arquivo/{{ $institution->logotipo }}');
        /* background-image: url('https://dev.forlearn.ao/storage/; */
        /* background-image: url('/img/CABECALHO_CINZA01GRANDE.png'); */
        background-position: 100%;
        background-repeat: no-repeat;
        background-size: 9%;
        /* padding-left:10px;
        padding-right:10px;
        padding-top:10px;
        padding-bottom:8px; */
        margin-bottom: 1pc;
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
        font-size: 0.7rem;
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
        width: 70px;
        height: 100%;
        margin-bottom: 5px;
    }

    .pl-1 {
        padding-left: 1rem !important;
    }
    
</style>
<main>
    <div class="div-top" style="height:80px;">
        <table class="table m-0 p-0">
            <tr>
                @if ($stundet_finalist->fotografia)
                    <td class="td-fotografia" rowspan="12"
                        style="background-image: url('{{ asset('storage/attachment/'.$stundet_finalist->fotografia) }}'); width:100px;height:76px; background-position:50%;">
                    </td>
                @endif
                <td class="" style=" padding-top:16px;">
                    <h1 class="h1-title">
                        @if (isset($titulo_documento))
                            {{ $titulo_documento }}
                        @else
                            Sem título
                        @endif
                    </h1>
                </td>
            </tr>
            <tr>
                <td class="data_bMatricula" rowspan="4">
                    <style>
                        .data_bMatricula {
                            background-color: transparent;
                        }
                    </style>
                    @if (isset($documentoGerado_documento))
                        {{ $documentoGerado_documento }}
                    @else
                        Documento sem titulo
                    @endif
                    <b>{{ Carbon\Carbon::Now()->format('d/m/Y') }}</b>
                    {{-- <b>{{ Carbon\Carbon::parse($stundet_finalist->created_at)->format('d/m/Y') }}</b> --}}
                </td>
            </tr>
        </table>
        <div style="position: absolute; top: 8px; right: 100px; width: 310px; font-family: Impact; padding-top: 15px;">
            <h4><b>
                @if (isset($institution->nome))
                    {{ $institution->nome }}
                @else
                    Instituição sem nome
                @endif
            </b></h4>
        </div>
    </div>

</main>
<main>

    <table class="table table-parameter-group">

        <thead class="thead-parameter-group">
        <th class="th-parameter-group">DADOS PESSOAIS</th>
        </thead>

    </table>

    <table class="table table-parameter-group" width="100%" style="border: 0 !important;">
        <thead>
        <th style="font-size: 10pt; border: 0 !important;">
            Nome
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Nº Matrícula
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Bilhete de Identidade
        </th>
        </thead>
        <tbody class="">
        <tr>
            <td width="25%" style="font-size: 10pt;">
                {{ $stundet_finalist->name_full }}
            </td>
            <td width="25%" style="font-size: 10pt;">
            {{$stundet_finalist->matricula }}
            </td>
            <td width="50%" style="font-size: 10pt;">
                {{ $stundet_finalist->num_bi }}
            </td>
        </tr>
        </tbody>
    </table>

    <table class="table table-parameter-group" width="100%" style="border: 0 !important;">
        <thead>
        <th style="font-size: 10pt; border: 0 !important;">
            Telémovel
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Telémovel Alternativo
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Telefone
        </th>
        <th style="font-size: 10pt; border: 0 !important;"></th>
        </thead>
        <tbody class="">
        <tr>
            <td width="25%" style="font-size: 10pt;">
                {{ $stundet_finalist->telemovel_principal }}
            </td>
            <td width="25%" style="font-size: 10pt;">
                {{ $stundet_finalist->telemovel_alternativo }}
            </td>
            <td width="25%" style="font-size: 10pt;">
                {{ $stundet_finalist->telefone_fixo }}
            </td>
            <td width="25%" style="font-size: 10pt;"></td>
        </tr>
        </tbody>
    </table>

    <table class="table table-parameter-group" width="100%" style="border: 0 !important;">
        <thead>
        <th style="font-size: 10pt; border: 0 !important;">
            Email
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Email Pessoal
        </th>
        <tbody class="">
        <tr>
            <td width="50%" style="font-size: 10pt;">
                {{ $stundet_finalist->email }}
            </td>
            <td width="50%" style="font-size: 10pt;">
                 {{ $stundet_finalist->e_mail_alternativo }}
            </td>
        </tr>
        </tbody>
    </table>

    <br>

    <table class="table table-parameter-group">

        <thead class="thead-parameter-group">
        <th class="th-parameter-group">DADOS CURRICULARES</th>
        </thead>

    </table>

    <table class="table table-parameter-group" width="100%" style="border: 0 !important;">
        <thead>
        <th style="font-size: 10pt; border: 0 !important;">
            Curso
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Ano Curricular
        </th>
        <th style="font-size: 10pt; border: 0 !important;">
            Código da Matrícula
        </th>
        {{-- <th style="font-size: 10pt; border: 0 !important;">
            Turno
        </th> --}}
        <tbody class="">
        <tr>
            <td width="25%" style="font-size: 10pt;">
                {{ $stundet_finalist->display_name }}
            </td>
            <td width="25%" style="font-size: 10pt;">
                 {{ $stundet_finalist->duration_value }}
            </td>
            <td width="25%" style="font-size: 10pt;">
                 {{ $stundet_finalist->num_confirmaMatricula }}
            </td>
            {{-- @foreach($disciplines as $d)--}}
            {{-- <td width="25%" style="font-size: 10pt;">
                @if($loop->first)
                    @if( (substr($d['class'], -2, 1) == "M" ) )
                        Manhã
                    @elseif((substr($d['class'], -2, 1) == "T" ))
                        Tarde
                    @elseif((substr($d['class'], -2, 1) == "N" ))
                        Noite
                @endif
                @endif
            </td>  --}}
        </tr>
        </tbody>
    </table>

    <br><br><br>

    <table class="table table-parameter-group">

        <thead class="thead-parameter-group">
        <th class="th-parameter-group">FREQUÊNCIA A DISCIPLINAS DO ANO CURRICULAR</th>
        </thead>

    </table>


    <table class="table table-parameter-group" width="100%">
        <thead>
        <th style="font-size: 10pt;">DISCIPLINA</th>
        {{-- <th style="font-size: 10pt;">REGIME</th> --}}
        <th style="font-size: 10pt;">ANO</th>
        {{-- <th style="font-size: 10pt;">TURMA</th> --}}
        </thead>
        <tbody class="tbody-parameter-group">
        {{-- @foreach($disciplines as $d)@endforeach --}}
            <tr>
                <td width="50%" style="font-size: 10pt;">Trabalho de fim de cursoo</td>
                {{-- <td width="20%" style="font-size: 10pt;">{{ $d['regime'] }}</td> --}}
                <td width="10%" style="font-size: 10pt;">{{ $stundet_finalist->duration_value }}</td>
                {{-- <td width="20%" style="font-size: 10pt;">{{ $d['class'] }}</td> --}}
            </tr>
        
        </tbody>
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>

    <table class="table table-parameter-group">
        <thead class="">
        <tr>
            <th style="font-size: 10pt; border: 0 !important;">Assinatura do Estudante</th>
            <th style="font-size: 10pt; border: 0 !important;">O Funcionario(a)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="font-size: 10pt;"><br>_________________________________________________________________</td>
            <td style="font-size: 10pt;"><br>_________________________________________________________________ <br>
                {{-- ({{$created_by}}) --}}
            </td>
        </tr>
        </tbody>
    </table>
    <br><br>

</main>

@endsection