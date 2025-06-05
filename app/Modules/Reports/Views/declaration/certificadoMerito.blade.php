@extends('layouts.print')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Rubik' rel='stylesheet'>


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        @import url('http://fonts.cdnfonts.com/css/book-antiqua');
        @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        @import url(' http://fonts.cdnfonts.com/css/eb-garamond-2 ');         
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
            display: block!important;
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
            font-weight: bold;
            margin-left: 130px;
            margin-right: 130px;
            margin-top: 20px;
            font-family: 'EB Garamond'!important;
            
            
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

            font-size: {{ $config->tamanho_fonte }}pt;
            margin-left: 80px;
            margin-right: 80px;
            color: black;
            text-align: justify;
        }


        .dados_pessoais {
            margin-bottom: -5;
        }

        body{
            padding: 10px;
        }
    </style>
      <div class="watermark"></div>
    <main>

        <!-- aqui termina o cabeçalho do pdf -->
        <div class="linha">
            @include('Reports::declaration.cabecalho.cabecalhoCertificadoSimples')
            <div class="row">
                <div class="col-12 ">


                    <div class="conteudo">

                    </div class="row">
                    <div class="">

                        <br><br>
                        <br>
                        <p style="text-align: center;font-size: 25px;">
                            O {{$iniciais}} confere o presente Diploma de Mérito a(o) Sr(a).
                        </p>
                    
                        <center> <br> 
                        <h1 style="text-align: center;font-size: 60px;font-family:'Book Antiqua';font-style:italic;width: 80%;">
                            {{ $studentInfo->name }}, 
                        </h1>
                        <br>


                        @switch($tipo)
                             
                            @case(1)
                             
                            <p style="text-align: center!important;font-weight: 700;font-size: 25px;margin-left: 80px!important;margin-right: 80px!important;"> 
                            Estudante do Curso de Licenciatura em <b
                                style="color:rgba(255, 0, 0, 0.815)">{{ $studentInfo->course }}</b>, pelo seu empenho, zelo
                            e
                            dedicação, destacando-se como Estudante com maior nota no {{substr($iniciais, 0, 6) }}Maravilha no Curso de
                            Licenciatura em 
                            <b>{{ $studentInfo->course }}</b>, no ano académico de
                            {{ $anos[0]->ano_lectivo }}, com a média final de 
                            <b style="color:red">{{ round($media) }}</b> valores.

                        </p> 
                                @break
                            @case(2)
                            
                             <p style="text-align: center!important;font-weight: 700;font-size: 25px;margin-left: 80px!important;margin-right: 80px!important;"> 
                                pelo seu empenho, zelo e dedicação no desenvolvimento da actividade docente no ano académico {{$anos}} destacando-se como, o melhor
                                Funcionário da seccção {{$departamento->display_name}}
                                {{-- {{str_replace("Departamento","Departamento de ",$departamento->display_name)}} --}}
                            </p>
                            <br><br> 
                            <br><br>
                                @break
                            @case(3)
                            
                             <p style="text-align: center!important;font-weight: 700;font-size: 25px;margin-left: 80px!important;margin-right: 80px!important;"> 
                                pelo seu empenho, zelo e dedicação no desenvolvimento da actividade administrativa no ano académico {{$anos}} destacando-se como, o melhor
                                Funcionário da secção de {{$seccao}}
                            </p> 
                            <br><br>
                            <br><br>
                                @break
                            @default
                                
                        @endswitch

                        </center>  
                            <br><br>
                            <br><br>
                            <br><br>
                            <br><br>
                            <br><br>
                            <br><br>
                        <div style="text-align:center;">
                            <p style="font-size: 28px;; text-align:center;">
                                Benguela, {{ $dataActual }}
                            </p>
                            <br> 
                            <br><br>
                            <p style="font-size:{{ $config->tamanho_fonte }}pt;text-align:center;">
                                {{-- O DIRECTOR GERAL --}}
                            </p>
                            <br>
                            <br>
                            <p style="font-size:{{ $config->tamanho_fonte }}pt;text-align:center;color:#606467!important;font-family: 'EB Garamond'!important;font-weight: 700; ">
                                
                           <asa style="text-transform: uppercase;"> {{ $direitor->value == '' ? ' ' : $direitor->value }}</asa>, PhD.
                            </p>
                            
                        </div>
 
                    </div>

                  
                </div>

            </div>
        </div>
        <br>

    </main>
@endsection 

<script></script>

