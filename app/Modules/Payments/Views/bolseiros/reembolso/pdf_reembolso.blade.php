@php use App\Modules\Payments\Controllers\BolseirosController; @endphp
@section('title', __('Folha de caixa [ Reembolsos ]'))
@extends('layouts.print')
@section('content')



    <style>

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
            /* border-top: 1px solid #000;
                border-bottom: 1px solid #000; */
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');
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
        .bg0,.div-top{
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
        td,th{
            font-size: 12px!important;
        }
        .h1-title,.data-generate{
            text-align:right;
            padding-right: 10px;
        }

    </style>
    <main>
        @php
            $logotipo = "https://".$_SERVER['HTTP_HOST']."/instituicao-arquivo/".$institution->logotipo;
            $doc_name = "FOLHA DE CAIXA [ Reembolsos ]";
        @endphp
        <style>
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
                /* border-top: 1px solid #000;
                    border-bottom: 1px solid #000; */
                margin-bottom: 15px;
                background-color: rgb(240, 240, 240);
                /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}'); */
                background-image: url('{{ $logotipo }}');
                /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
                background-position: 100%;
                background-repeat: no-repeat;
                background-size: 8%;
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
        
            .h1-title {
        
                padding: 0;
                margin-bottom: 0;
                /* font-size: 14pt; */
                font-size: 1.50rem;
                padding-top: 20px;
                /* background-color:red; */
                /* font-weight: bold; */
                width: 100%;
        
            }
        
            .table_te {
                background-color: #F5F3F3;
                ;
                width: 100%;
                text-align: right;
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
                text-align: center;
                font-size: 18pt;
                font-weight: bold;
            }
        
            .table_te td {
                border-left: 1px solid #fff;
                background-color: #F9F2F4;
                border-bottom: 1px solid white;
                font-size: 12pt;
            }
        
            .tabble_te thead {}
        
            .bg0 {
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
        
            .div-top,
            .div-top table {
                height: 130px;
            }
        
            .table-main{
                padding-left: 10px;
            }
            th,td{
                padding-top: 4px; 
                padding-bottom: 4px; 
                font-size: 15px!important;
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
        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te tdata">
                            <style>
                                .table_te {
                                    background-color: #F5F3F3;
                                     !important;
                                    width: 100%;
                                    text-align: right;
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
                                    text-align: center;
                                    font-size: 18pt;
                                    font-weight: bold;
                                }

                                .table_te td {
                                    border-left: 1px solid #fff;
                                    background-color: #F9F2F4;
                                    border-bottom: 1px solid white;
                                    font-size: 14pt;
                                }

                                .tabble_te thead {}

                                .tr-normal {}
                            </style>
                            <tr class="bg1">
                                <th class="text-center">Data de início</th>
                                <th class="text-center">Data de fim</th>
                            </tr>
                            <tr class="bg1">
                                <td class="text-center bg2" >{{ $DataInicio }}</td>
                                <td class="text-center bg2" >{{ $DataFim }}</td>
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

                                    <tr class="bg1">
                                   
                                             <th class="text-center">#</th>
                                             <th class="text-center">Estudante</th> 
                                             <th class="text-center">email</th> 
                                             <th class="text-center">Data</th>
                                             <th class="text-center">Método de devolução</th>
                                             {{-- <th class="text-center">Nº da conta / IBAN</th> --}}
                                             <th class="text-center">Recibo nº</th>
                                             <th class="text-center">Banco</th>
                                             <th class="text-center">Valor</th> 
                                             <th class="text-center">Tesoureiro</th>
                                    </tr>
                                    @php
                                        $i = 1;
                                        $v = 0;
                                        $total = 0;
                                    @endphp

                                    @foreach ($balance as $item)
                                        <tr>
                                          
                                            <td class="text-center" style="font-size: 11pt;width:40px;">{{ $i++ }}</td>
                                            <td class="text-left" style="font-size: 11pt;width:300px;">{{ $item->name }}</td>
                                            <td class="text-left" style="font-size: 11pt;width:250px;">{{ $item->email }}</td>
                                            <td class="text-center" style="font-size: 11pt;width:110px;">{{ $item->date }}</td>
                                            <td class="text-center" style="font-size: 11pt;width:210px;">
                                                    @php
                                                    if($item->mode=="1"){
                                                        echo $item->mode="Tranferência";
                                                    }
                                                    if($item->mode=="2"){
                                                        echo  $item->mode="Depósito";
                                                    }
                                                    @endphp 
                                            </td>
                                            {{-- <td class="text-center" style="font-size: 11pt;width:230px;">{{ $item->iban }}</td> --}}
                                            <td class="text-center" style="font-size: 11pt;width:110px;;">
                                                
                                                @if (isset($item->code) && isset($item->year))    
                                                    {{ BolseirosController::get_code_doc($item->code,$item->year) }}
                                                @endif
                                            </td>
                                            <td class="text-center" style="font-size: 11pt;">{{ $item->bank }}</td>
                                            <td class="text-center" style="font-size: 11pt;width:130px;">
                                                @if (isset($item))
                                                    @php 
                                                        $total = $total + $item->value;
                                                    @endphp
                                                    {{ number_format($item->value, 2, ',', '.') }} kz
                                                @endif
                                            </td>
                                            <td class="text-center" style="font-size: 11pt;width:220px;">{{ $item->created_by }}</td>
                                           
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: white">

                                        <th class="text-center" style="font-size: 14pt; padding: 0px; "></th>
                                        {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                        <th class="text-center" style="font-size: 16pt;"></th>
                                        <th class="text-center" style="font-size: 16pt;"></th>
                                        <th class="text-center" style="font-size: 16pt;"></th>
                                        <th class="text-center" style="font-size: 16pt;"></th>
                                        <td class="text-center" style="font-weight: bold;background-color: white;"></td>
                                        <td class="text-center" style="font-weight: bold;background-color: white;"></td>
                                        {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                    </tr>
                                    <tr>

                                        <th class="text-center"
                                            style="font-size: 14pt; padding: 0px;background-color: white "></th>
                                        {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                        <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        <td class="text-right bg2" style="font-weight: bold;">TOTAL</td>
                                        <td class="text-center bg2" style="font-weight: bold;">
                                            {{ number_format($total, 2, ',', '.') . ' kz' }}</td>
                                            <th class="text-center" style="font-size: 16pt;background-color: white"></th>
                                        
                                    </tr>

                                </table>

                               

                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            
                            <table class="table_te" style="width:420px;margin-left: 500px;">

                                <tr>
                                    <th class="text-center bg1" style="font-size: 16pt;" colspan="3">RECURSOS
                                        HUMANOS</th>
                                </tr>

                                <tr class="bg2">

                                    {{-- <th class="text-center" style="font-size: 16pt; ">Quantidade</th> --}}
                                    <th class="text-center" style="font-size: 16pt;">Tesoureiro</th>
                                    <th class="text-center" style="font-size: 16pt;">Nº de Recibo</th>
                                    <th class="text-center" style="font-size: 16pt;">%</th>
                                </tr>

                                @php
                                    $quantidade = 0;
                                    
                                    $v = 0;
                                @endphp


                                @foreach ($tesoureiros as $key => $value)
                                    @php
                                        $dados = explode(',', $value);
                                        
                                        $quantidade = $dados[0];
                                        $v += $quantidade;
                                    @endphp
                                @endforeach


                                @foreach ($tesoureiros as $key => $value)
                                    @php
                                        $dados = explode(',', $value);
                                        
                                        $quantidade = $dados[0];
                                        $percentagem = ($quantidade * 100) / $v;
                                        
                                    @endphp

                                    <tr>

                                        <td class="text-left" style="">{{ $key }}</td>
                                        <td class="text-center" style="">{{ $quantidade }}</td>
                                        <td class="text-center" style="">{{ round($percentagem) . '%' }}
                                        </td>



                                    </tr>
                                @endforeach
                                <tr style="background-color: white">

                                    <td class="text-left" style="background-color: white"></td>
                                    <td class="text-center" style="background-color: white"></td>
                                    <td class="text-center" style="background-color: white"></td>



                                </tr>

                                <tr style="font-weight:bold">

                                    <td class="text-right bg2" style="">TOTAL</td>
                                    <td class="text-center bg2">{{ $v }}</td>
                                    <td></td>



                                </tr>

                            </table>

                            <br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

@endsection

<script></script>
