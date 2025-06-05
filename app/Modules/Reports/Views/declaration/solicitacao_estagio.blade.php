@extends('layouts.print')
@section('title', __('Solicitação de Estágio'))
@section('content')

@php

@endphp
<style>

    body {
        font-family: 'Tinos', serif;
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
        font-size: 4.3em;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 40px;
        letter-spacing: 5px;
        text-decoration: none;
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

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }

    p {
        font-size: 11.5pt;
        margin-left: 80px;
        margin-right: 80px;
        color: black;
        text-align: justify;
        line-height: 1.2;
        /* Adicionando o controle de espaçamento entre linhas */
    }

    .dados_pessoais {
        margin-bottom: -5;
    }

    .conteudo {
        margin-left: 50px;
        margin-right: 50px;
    }

    .conteudo p {
        font-size: 19px !important;
    }

    .visto {
        margin-left: 120px;
        text-align: center;
        width: 180px;
        font-size: 15px;
        margin-top: 60px;
        font-weight: 600;
    }

    .text-right {
        text-align: right;
    }

   
</style>




<main>

    @include('Reports::declaration.cabecalho.cabecalho_forLEARN')
    <div>
        <div class="row">
            <div class="col-12 ">
                <div class="conteudo">
                    
                    <br>
                    <p><b>ASSUNTO:</b> SOLICITAÇÃO DE ESTÁGIO</p>

                    <p>Melhores comprimentos,</p>
                    <p>
                        O <b>{{ $institution->nome }} ({{$institution->abrev}})</b> é uma instituição de Ensino
                        Superior, aprovada pelo Decreto Presidencial
                        nº 168/12 de 24 Julho, localizada no Bairro Benfica, junto á Avenida Comandande Fidel Castro,
                        dispõe de instalações modernas e acolhedoras, capazes de fazer
                        face as exigências do Ministério de tutela, garantindo deste modo a qualidade. É a primeira e
                        única Instituição de Ensino Privado no País que lecciona exclusivamente cursos
                        nas áreas das engenharias, nomeadamente:
                        <b>{{ $courses->pluck('course_name')->join(', ') }} e {{ $courses->last()->course_name }}</b>.
                    </p>

                    <p>Com a intenção em associar o conteúdo teórico aprendido com a prática, vimos por este meio
                        solicitar a vossa
                        autorização no sentido de acolherem o estudante, <b>{{ $studentInfo->name }}</b>, que frequenta
                        na nossa instituição o <b>{{ $studentInfo->year }}º ano </b>do curso de <b>{{ $studentInfo->course }}</b>,
                        necessidade
                        de um estágio Profissional, com vista aprimorar o seu conhecimento na referida área.
                    </p>

                    <p>
                        Na expectativa de que a nossa intenção seja de interesse comum, remetemos os nossos contactos
                        telefónicos e reiteramos os nossos melhores cumprimentos.
                    </p>


                    <p hidden="true">GABINETE DA VICE-PRESIDENTE PARA ASSUNTOS CIENTIFICOS E POS-GRADUACAO.

                    </p>

                    <div>

                        <p style="font-size: 16pt !important;text-align:left;font-weight:bolder !important;margin-bottom:40px !important;
                        margin-top:60px !important;">
           
           {{ $institution->provincia }}, aos {{ $dataActual }}
          </p>
          <p>_______________________________________</p>
                    <p style="font-size: 16pt !important;margin-top:-10px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                    <p style="font-size:12pt !important;margin-top:-18px">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                    <p style="font-size:12pt !important;margin-top:-16px">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>



                    </div>
                    <div class="watermark" style="left: -3px;"></div>
                </div>


            </div>
        </div>
    </div>
</main>
@endsection

<script></script>