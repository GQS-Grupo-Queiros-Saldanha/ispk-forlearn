@php

$data_title = "Pendentes_".date("Y-m-d");
@endphp

@section('title', "".$data_title)
@extends('layouts.printForSchedule')
@section('content')

<style>
    .div-top {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        background-size: 90px !important;
    }
</style>

<main>
    <main>
        @php
        $logotipo = "https://".$_SERVER['HTTP_HOST']."/instituicao-arquivo/".$institution->logotipo;
        $doc_name = "LISTA DE PENDENTES";
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
                /* border-top: 1px solid #000;
                border-bottom: 1px solid #000; */
                margin-bottom: 15px;
                background-color: rgb(240, 240, 240);

                /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
                background-position: 100%;
                background-repeat: no-repeat;
                background-size: 63%;
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

            tfoot {
                display: table-footer-group
            }

            .tdata {
                width: 350px !important;
            }

            .tdata tr th {

                font-size: 20px;
            }

            .bg0,
            .div-top {
                background-color: #2f5496 !important;
                color: white;
            }



            .bg1 {
                background-color: #8eaadb !important;
            }

            .bg2 {
                background-color: #d9e2f3 !important;
            }

            .bg3 {
                background-color: #fbe4d5 !important;
            }

            .bg4 {
                background-color: #f4b083 !important;
            }

            td,
            th {
                font-size: 12px !important;
            }

            .h1-title,
            .data-generate {
                text-align: right;
                /* padding-right: 10px; */
                padding-top: 20px;
            }
        </style>

        <div class="div-top" style="">
            <div class="div-top">


                <table class="table table-main m-0 p-1 bg0 " style="border:none;">
                    <tr>

                        <td class="td-fotografia " rowspan="12"
                            style="background-image:url('{{ $logotipo }}');width:110px; height:78px; background-size:100%;
                                background-repeat:no-repeat;Background-position:center center;
                                border:none;padding-right:12px!important;top:-5px;position:relative;left: 10px;
                                ">
                        </td>

                    </tr>

                    <tr>
                        <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>

                    </tr>
                    <tr>
                        <td class="td-fotografia " rowspan="12" style=" width:200px; height:78px;border:none;"></td>
                    </tr>





                    <tr>
                        <td class="bg0" style="border:none;padding-right:3px;">
                            <h1 class="h1-title" style="">
                                <b> {{$doc_name}}</b>
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="data-generate" style="border:none;padding-right:3px;">

                            <span class="" rowspan="1" style="">
                                Documento gerado a
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </span>

                        </td>
                    </tr>

                </table>





                <div class="instituto"
                    style="position: absolute; top: 8px; left: 130px; width: 440px; font-family: Impact; padding-top: 20px;color:white;">
                    <h4><b>
                            @if (isset($institution->nome))
                            @php
                            $institutionName = mb_strtoupper($institution->nome, 'UTF-8');
                            $new_name = explode(" ",$institutionName);
                            foreach( $new_name as $key => $value ){
                            if($key==1){
                            echo $value."<br>";
                            }else{
                            echo $value." ";
                            }
                            }
                            @endphp
                            @else
                            Nome da instituição não encontrado
                            @endif
                        </b></h4>
                </div>

            </div>

        </div>


        @if(isset($finalist))
        @include('Reports::partials.pending-articles-finalist')
        @else
        @include('Reports::partials.pending-articles')
        @endif


    </main>

    @endsection

    <script>

    </script>