@extends('layouts.print')
@section('title',__('Pedido de equivalência (de entrada no ISPK)'))
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
            font-size: 14.5pt;
            margin-left: 80px;
            margin-right: 80px;
            color: black;
            text-align: justify;
            /*letter-spacing: 0.1px;*/
        }

        /*.paragrafo1{*/
        /*    margin-top:100px; */
        /*}*/

        .dados_pessoais {
            margin-bottom: -5;
        }

        .conteudo {
            margin-left: 50px;
            margin-right: 50px
       

        }

        .conteudo p {
            font-size: 22px !important;
        }

        .visto {
            margin-left: 120px;
            text-align: center;
            width: 180px;
            font-size: 15px;
            margin-top: 60px;
            font-weight: 600;
        }

        .tb-notas {
            margin-left: 80px;
            font-size: 17pt !important;
            width: 740px;
            margin-bottom: 13px !important;
           
        }

        .tb-notas  th, td {
            padding: 12px;
            border: 1px solid #000;
        }

        .tb-notas  th, td {
            padding-top:1px;
            padding-bottom:1px;
        }

      
    
      


    
    </style>
    <main>




        @include('Reports::declaration.cabecalho.cabecalho_forLEARN')

        <div style"margin-top:0px!important;">
            <div class="row">
                <div class="col-12 ">
                     
        <div class="conteudo">
                        @php
                           
                            $province = null;
                            $province = matchProvince(substr($studentInfo->bi,-5,2));
                        @endphp
                             <p> 
                    {{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>,{{ $direitor->cargo ?? 'cargo' }}  do <b>{{ $institution->nome }}</b>,
                    declara que
                   <b style="">
                        {{ $studentInfo->name }}</b>, filho(a) de {{ $studentInfo->dad }} e de
                    {{ $studentInfo->mam }}, nascido(a) aos
                    {{ $nascimento }}, portador(a) do B.I nº {{ $studentInfo->bi }}, passado pelo Arquivo de Identificação de {{ $province }}, aos
                    {{ \Carbon\Carbon::parse($studentInfo->emitido)->format('d/m/Y') }},
                    solicitou o pedido de equivalência (de entrada no {{$institution->abrev}}), no curso de {{ $studentInfo->course ?? '(nome do curso)' }}, tendo como instituição de origem 
                    o(a) {{ $transference->school_name ?? '(nome da instituição)' }}.
                   

                </p>
                

                
                 <p style="margin-top:-10px!important;"><b>Documentação entregue:</b></p>
                 <ul style="font-size: 14pt !important;margin-top:-20px!important;">
                 @foreach($documentation as $doc)
                    
                         <style>.li{ list-style-type: none; margin-left:40px; font-weight:200; margin-bottom:-10px!important} </style>
                        <li class="li">{{$doc}}</li>
                   
                 @endforeach
                   </ul>
           
                        
            <div><br><br><br>
                <p> 
                    
                    Passa-se a presente declaração nº {{$requerimento->code ?? 'código doc'}}, liquidada no CP nº {{$recibo ?? 'recibo'}},
                    assinada e autenticada com o carimbo a óleo em uso no {{$institution->abrev}}.
                </p><br>
                
                <!--<p style="font-size:12pt !important">Documento gerado automaticamente pela <b style="color:#243f60;margin-top:10px;font-size:inherit !important">forLEARN<sup style="font-size: 11px">®</sup></b></p>-->
               
                 <p style="font-size: 16pt !important;text-align:left;font-weight:bolder !important;margin-bottom:100px !important;margin-top:-10px">
           
                 {{ $institution->provincia }}, aos {{ $dataActual }}
                </p>
                <p>_______________________________________</p>
                          <p style="font-size: 14pt !important;margin-top:-18px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                          <p style="font-size:11pt !important;margin-top:-24.5px">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                          <p style="font-size:11pt !important;margin-top:-28.5px">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>

                
            </div>

                 
                <div class="watermark" style="left: -3px;"></div>
                
            </div>
            </div>


          </div>
        </div>
    </main>
@endsection

<script></script>
