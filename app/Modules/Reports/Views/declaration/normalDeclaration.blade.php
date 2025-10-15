@extends('layouts.print')
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
            text-decoration:none;
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

        p {
            /*margin-top:50px;*/
            /*font-size:1.5rem;*/
            font-size: 11pt;
            margin-left: 80px;
            margin-right: 80px;
            color: black;
            text-align: justify;
        
        }

        /*.paragrafo1{*/
        /*    margin-top:100px; */
        /*}*/

        .dados_pessoais {
            margin-bottom: -5;
        }
        .conteudo{
            margin-left:50px;
            margin-right:50px;
            
        }
        .conteudo p {
            font-size:11px!important;
        }
        .visto{
            margin-left:120px;
            text-align: center;
            width:180px;
            font-size:11px;
            margin-top:60px;
            font-weight: 600;
        }
        .watermark{
            background-position-x: 50px!important;
        }
    </style>
    <main>


      
        @include('Reports::declaration.cabecalho.cabecalho_forLEARN')
        <!-- aqui termina o cabeçalho do pdf -->
        <div>
            <img src="" alt="">
            <div class="row">
                <div class="col-12 ">

 @php
                            $efeito == '' ? ' ' : $efeito;
                        @endphp
                    <div class="conteudo"><p>O<b> {{$institution->nome}}</b>, declara para @if($efeito=="Diversos")efeitos<b> {{$efeito}}</b>@else efeito de<b> {{$efeito}}</b>@endif, que<b> {{ $studentInfo->name }}</b>, filho de {{$studentInfo->dad}} e de {{$studentInfo->mam}}, Natural de {{str_replace('Município de ', '',$studentInfo->municipio)}}, Província de {{ $studentInfo->province }}, Nascido(a) aos {{ $nascimento }}, Portador(a) do B.I nº {{ $studentInfo->bi }}, passado pelo Arquivo de Identificação de {{ $studentInfo->province }}, aos {{ \Carbon\Carbon::parse($studentInfo->emitido)->format('d/m/Y') }}, frequentou, no ano académico {{ $lectivo->ano }}, o<b> {{ $studentInfo->year }}º Ano</b>, no curso de Licenciatura em<b> {{ $studentInfo->course }},</b> matriculado(a) com o nº<b> {{ $studentInfo->number }}.</b></p>
               
                        <br>
                                    @php 
                                    use App\Modules\Avaliations\Controllers\RequerimentoController; 
                                    
                                    if(isset($requerimento->code) && isset($requerimento->year)){
                                        
                                        $referencia = RequerimentoController::get_code_doc($requerimento->code,$requerimento->year,$requerimento->codigo_documento,2);
                                        
                                        }
                                    @endphp
                        <p>

                            Por ser verdade e ter sido solicitado, passou-se a presente declaração que será assinada e autenticada com o carimbo a óleo em uso nesta instituição.

                        </p>
                        <br>
                     

                         <p style="17pt;text-align:left;">
                           {{-- $institution->nome --}} <br> {{ $institution->provincia }} aos {{ $dataActual }}
                        </p>
                        <br><br>
                     


                        <div style="width: 500px;float:left;">
                            
                                <p style="font-size:10pt;text-align: center;margin-left: 50px;">
                                    O Chefe do Dpto Académico
                                </p>
                                <p style="font-size:10pt;text-align: center;margin-left: 50px;">
                                    ________________________
                                    <br>
                                    
                                        {{isset($secretario->value) ? $secretario->value : "" }}
                                    

                                </p>
                        

                        </div> 
                        <div class="">
                        
                                <p style="font-size:10pt;text-align: center;">
                                    O Vice-P/Área Académica
                                </p>
                                <p style="font-size:10pt;text-align: center;">
                                    ________________________
                                    <br>

                                    
                                    <as style="">
                                         {{isset($direitor->value) ?"Professor Doutor ".$direitor->value : ""}}
                                    </as>

                                </p>


                        </div>
                        
                        <br
                    </div>
                    <div class="watermark"></div>
                </div>


            </div>
        </div> 
    </main>
@endsection

<script></script>
