
@extends('layouts.print')
@section('content')
    <link href="http://fonts.cdnfonts.com/css/calibri-light" rel="stylesheet">
 
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        body{
            font-family: 'Tines' ,serif;
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
        height: 100px;
        background-position: center;
        color: black;
        background-repeat: no-repeat;
        margin-top:30px;
        font-size:23px;
        letter-spacing:1px;
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
            background-image: url('https://forlearn.ao/storage/attachment/{{ $institution->logotipo }}');
            /* background-image: url('{{ asset('$logotipo') }}'); */
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 7%;
            /*text-align:center;*/
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
            font-size: 17pt;
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


        .destinario p {
            margin: 0px !important;
            font-style: italic;
        }



        .destinario {
            position: relative;

            padding: 0px 30px;
            padding-bottom: 10px;
            font-size: 20px;
            margin-right: 90px;
            
        }

        .destinario:before,
        .destinario:after {
            content: '';
            display: block;
            position: absolute;
            border: 1px solid black;
            top: 8px;
            bottom: 8px;
            /* espaço no topo e embaixo */
            width: 18px;
            /* largura das "chaves"     */

        }

        .destinario:before {
            left: 2px;
            border-right: none;
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;

           
        }

        .destinario:after {
            right: 2px;
            border-left: none;
            border-top-right-radius: 30px;
            border-bottom-right-radius: 30px;
        }
    </style>
    <main>




        <br>
<br>
 
<style>
    .div-top {
        height: 150px;
        text-transform: none;
        background-color: white;
        background-image: url("{{'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo}}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 130px;
        
    }

    .watermark {
        opacity: 0.2;
        color: BLACK;
        position: fixed;
        top: 280px;
        background-image: url("{{'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo}}");
        background-position: center;
        background-position-x: 100px;
        background-repeat: no-repeat;
        background-size: 800px; 
        height: 800;
        width: 100%;

    }
    
    .conteudo{
        margin-left:60px;
        margin-right:60px;
    }
    
</style>

<div class="div-top" style="">
    <table class="table m-0 p-0">  
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:96px;">

                <p style="text-align: center;margin-top: 130px;font-size: 24.5px;text-transform: uppercase!important;">
                    <b>{{$institution->nome}}</b>
                </p>
                <p style="text-align: left;margin-top: 10px;margin-left:120px;font-weight: 600;font-size: 14px;">
                    Criado pelo {{$institution->decreto_instituicao}}
                </p>


            </td>

        </tr>


    </table>

</div>
<br>


        <!-- aqui termina o cabeçalho do pdf -->
        <div>
            <img src="" alt="">
            <div class="row">
                <div class="col-12 ">
                    <br>

                    <p style="margin-top:50px;margin-left:130px;">
                            Assunto: <b>Anulação de Matrícula</b>

                    </p>
                        <br>
                    <div class="div-top_f" >
                        <table class="table m-0 p-0">
                            <tr>
                             
                                <td class="td-declaracao" rowspan="12"
                                    style="background-color: transparent; text-align:center;">
                    
                                    <h1 class="h1-title">
                                        <b>DECLARAÇÃO</b>
                                    </h1>
                    
                    
                                </td>
                    
                            </tr>
                    
                    
                        </table>
                    
                    </div>


                    <div class="conteudo">

                        <p style="">
                            O <b>{{$institution->nome}}</b>, comunica a(o) Senhor(a) <b>{{$studentInfo->name}}</b> que o seu pedido de anulação de matrícula no curso de <b>{{$studentInfo->course}}</b> foi aceite.

                        </p>

                        <br>

                        <p style="">

                            Sem outro assunto de momento,<br>
                            As nossas cordiais saudações,

                        </p>
                        <br>
                        <br>
                        <p style="17pt;text-align:left;">
                           {{-- $institution->nome --}} <br> {{ $institution->provincia }} aos {{ $dataActual }}
                        </p>
                        <br>
                            <div style="width: 400px;">
                                <br><br>
                                <br><br>
                                <br><br>
                                <p style="font-size:16pt;text-align: left;">
                                    A Secretaria Geral
                                </p>
                                <p style="font-size:16pt;text-align: left;">
                                    ________________________
                                    <br>
                                    @if (isset($secretario->value))
                                        {{ $secretario->value }}
                                    @endif

                                </p>
                                </b>

                            </div>

                    </div>
                        <div class="watermark"></div>
                </div>


            </div>
        </div>
    </main>
@endsection

<script></script>