@section('title', __('Folha de caixa [ Resumo ]'))
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
        background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{ $institution->logotipo }}');
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        background-position: 100%;
        background-repeat: no-repeat;
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
            $doc_name = "FOLHA DE CAIXA [ RESUMO ]";
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
                background-image: url('https://forlearn.ispm.ao/instituicao-arquivo/{{ $institution->logotipo }}');
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
            .assinaturas p,.data{ 
                font-size: 17px;
            }
            .data,.assinaturas{
                margin-left: 12%;
            }   
       
    .t-color{
            color:#fc8a17;
        }
          </style>
        
        <div class="div-top" style="">
            <div class="div-top">
        
        
                <table class="table table-main m-0 p-1 bg0 " style="border:none;">
                    <tr>
        
                        <td class="td-fotografia " rowspan="12"
                            style="background-image:url('{{ $logotipo }}');width:120px; height:68px; background-size:80px 100px;
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
                                        <th class="text-center" style="font-size: 11pt; padding: 0px; ">#</th>
                                        {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                        <th class="text-center" style="font-size: 11pt;">Nº de matrícula</th>
                                        <th class="text-center" style="font-size: 11pt;">Nome do estudante</th>
                                        <th class="text-center" style="font-size: 11pt;">Factura/Recibo nº</th>
                                        <th class="text-center" style="font-size: 11pt;">Data</th>
                                        <th class="text-center" style="font-size: 11pt;">Tesoureiro</th>
                                        <th class="text-center" style="font-size: 11pt;">Valor</th>
                                        <th class="text-center" style="font-size: 11pt;">Tipo transacção</th>


                                        {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                    </tr>
                                    @php
                                        $i = 1;
                                        $v = 0;
                                        $ta = 0;
                                    @endphp

                                    @foreach ($recibo as $item)
                                        <tr>
                                            @if (!in_array($item->id_transacion,$vetorCreditoAjuste))
                                             @php $ta += $item->valor; @endphp
                                            @endif
                                            @php
                                                $data = $item->created_at;
                                                $code = substr($item->created_at, 2, 2);
                                            @endphp
                                            <td class="text-center" style="font-size: 11pt;">{{ $i++ }}</td>
                                            <td class="text-center" style="font-size: 11pt;">{{ $item->matricula }}</td>
                                            <td class="text-left" style="font-size: 11pt;width: 300px">
                                                {{ $item->estudante }}</td>
                                            <td class="text-center" style="font-size: 11pt;">{{ $code . '-' . $item->code }}
                                            </td>
                                            <td class="text-center" style="font-size: 11pt;">{{ $data }}</td>

                                            <td class="text-left" style="font-size: 11pt;width: 160px;">{{ $item->name }}
                                            </td>
                                            <td class="text-center" style="font-size: 11pt;">
                                                {{ number_format($item->valor, 2, ',', '.') . ' kz' }}</td>
                                            @if (in_array($item->id_transacion,$vetorCreditoAjuste))
                                                <td class="text-center" style="font-size: 10pt">Crédito - Ajuste</td>
                                            @else
                                                <td class="text-center" style="font-size: 10pt">Pagamento</td>
                                            @endif

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
                                        <td class="text-center bg2" style="font-weight: bold;">TOTAL</td>
                                        <td class="text-center bg2" style="font-weight: bold;">
                                            {{ number_format($ta, 2, ',', '.') . ' kz' }}</td>
                                        {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                    </tr>

                                </table>

                                <br>







                                {{-- Início --}}


                                <div style="margin-left:7%;page-break-before: always;">
                                    <br>
                                    <table class="table_te" style="width: 1180px">

                                        <tr style="">
                                            <th class="text-center bg0" style="font-size: 16pt;" colspan="2">QUADRO RESUMO
                                            </th>
                                        </tr>

                                    </table>



                                    {{-- Tabela para o total de cada banco --}}

                            

                                    <div class="row" >
                            


                                    <table class="table_te" style="width:420px;margin-right: 10px;margin-left: 15px;">
 
                                        <tr>
                                            <th class="text-center bg1" style="font-size: 16pt;" colspan="2">FINANCEIRO</th>

                                        </tr>

                                        <tr>
                                            <th class="text-center bg2" style="font-size: 16pt;">Banco</th>
                                            <th class="text-center bg2" style="font-size: 16pt;">Valor</th>
                                        </tr>
                                        @foreach ($bancos as $key => $value)
                                            @php
                                                $dados = explode(',', $value);
                                                $valor = $dados[1];
                                                $quantidade = $dados[0];
                                            @endphp

                                            <tr>

                                                {{-- <td class="text-center" style="">{{$quantidade}}</td> --}}
                                                <td class="text-left" style="">{{ $key }}</td>
                                                <td class="text-center">{{ number_format($valor, 2, ',', '.') . ' kz' }}
                                                </td>

                                            </tr>
                                            @php
                                                $v += $valor;
                                            @endphp
                                        @endforeach
                                        <tr style="background-color: white">

                                            <th class="text-center" style="font-size: 14pt; padding: 0px; "></th>
                                            {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                            <td class="text-center" style="font-weight: bold;background-color: white;"></td>
                                            <td class="text-center" style="font-weight: bold;background-color: white;">
                                            </td>
                                            <td class="text-center" style="font-weight: bold;background-color: white;">
                                            </td>
                                            {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                        </tr>
                                        <tr style="background-color: white">

                                            <th class="text-center" style="font-size: 14pt; padding: 0px; "></th>
                                            {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                            <td class="text-center" style="font-weight: bold;background-color: white;">
                                            </td>
                                            <td class="text-center" style="font-weight: bold;background-color: white;">
                                            </td>
                                            <td class="text-center" style="font-weight: bold;background-color: white;">
                                            </td>
                                            {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                        </tr>
                                        <tr style="margin-top: 5%">
                                            {{-- <th class="text-center" style="font-size: 16pt;"></th> --}}
                                            <td class="text-right bg2" style="font-weight: bold">TOTAL</td>
                                            <td class="text-center bg2" style="font-weight:bold">
                                                {{ number_format($v, 2, ',', '.') . ' kz' }}</td>
                                        </tr>

                                    </table>





                                    {{-- Tabela de resumos dos cadastros feitos pelos usuários --}}

                                        {{-- <table class="table_te" style="width: 600px;">
                                    <tr style="width: 100%">
                                        <th class="text-center" style="font-size: 16pt;width: 100%;" >Tesoureiro</th>
                                    </tr>
                                </table> --}}





                                    <table class="table_te" style="width:420px;margin-right: 10px;">

                                        <tr>
                                            <th class="text-center bg1" style="font-size: 16pt;" colspan="3">RECURSOS
                                                HUMANOS</th>
                                        </tr>

                                        <tr class="bg2">

                                            {{-- <th class="text-center" style="font-size: 16pt; ">Quantidade</th> --}}
                                            <th class="text-center" style="font-size: 16pt;">Tesoureiro</th>
                                            <th class="text-center" style="font-size: 16pt;">Nº de Factura/Recibo(s)</th>
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

                            
                                
                                <table class="table_te" style="width:320px;">

                                        <tr class="bg1"><th class="text-center " style="font-size: 16pt;" colspan="2">NÃO FINANCEIRO</th></tr>
                                        <tr class="bg2">

                                            {{-- <th class="text-center" style="font-size: 16pt; ">Quantidade</th> --}}
                                            <th class="text-center" style="font-size: 16pt;">Tipo de transacção</th>
                                            <th class="text-center" style="font-size: 16pt;">Valores</th>
                                        </tr>
                                      <tr>
                                   
                                        </tr>
                                        @php $total=0; $valor=0; $quantidade=0 @endphp
                                        @foreach ($cretidoAjuste as $key => $value)
                                            @php
                                                $dados = explode(',', $value);
                                                $valor = $dados[1];
                                                $quantidade = $dados[0];
                                            @endphp

                                            <tr>
                                                <td class="text-left" style="">{{ $key }}</td>
                                                <td class="text-center">{{ number_format($valor, 2, ',', '.') . ' kz' }}</td>
                                            </tr>
                                            @php
                                                $total += $valor;
                                            @endphp
                                        @endforeach
                                        <tr style="margin-top: 5%">
                                            <td class="text-right  bg2" style="font-weight: bold">TOTAL</td>
                                            <td class="text-center bg2" style="font-weight:bold">
                                                {{ number_format($total, 2, ',', '.') . ' kz' }}</td>
                                        </tr>

                                    </table>
       
                                </div>
                            </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="">
                            <br>
                            <br>
                            <div style="page-break-before: always;">
                                <br>
                                <table class="table_te" style="width: 100%">

                                    <tr style="">
                                        <th class="text-center bg0" style="font-size: 16pt;" colspan="2">RECIBOS ESTORNADOS
                                        </th>
                                    </tr>

                                </table>
                            </div>
                            <table class="table_te" >

                                <tr class="bg1">
                                    <th class="text-center" style="font-size: 11pt; padding: 0px; ">#</th>
                                    {{-- <th class="text-center" style="font-size: 14pt; padding: 0px;">Id</th> --}}
                                    <th class="text-center" style="font-size: 11pt;">Nº de matrícula</th>
                                    <th class="text-center" style="font-size: 11pt;">Nome do estudante</th>
                                    <th class="text-center" style="font-size: 11pt;">Factura/Recibo nº</th>
                                    <th class="text-center" style="font-size: 11pt;">Data</th>
                                    <th class="text-center" style="font-size: 11pt;">Tesoureiro</th>
                                    <th class="text-center" style="font-size: 11pt;">Valor</th>
                                    <th class="text-center" style="font-size: 11pt;">Tipo transacção</th>


                                    {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                </tr>
                                @php
                                    $i = 1;
                                    $v = 0;
                                    $ta = 0;
                                @endphp

                                @foreach ($est_recibo as $item)
                                    <tr>
                                        @if (!in_array($item->id_transacion,$est_vetorCreditoAjuste))
                                         @php $ta += $item->valor; @endphp
                                        @endif
                                        @php 
                                            $data = $item->created_at;
                                            $code = substr($item->created_at, 2, 2);
                                        @endphp
                                        <td class="text-center" style="font-size: 11pt;">{{ $i++ }}</td>
                                        <td class="text-center" style="font-size: 11pt;">{{ $item->matricula }}</td>
                                        <td class="text-left" style="font-size: 11pt;width: 300px">
                                            {{ $item->estudante }}</td>
                                        <td class="text-center" style="font-size: 11pt;">{{ $code . '-' . $item->code }}
                                        </td>
                                        <td class="text-center" style="font-size: 11pt;">{{ $data }}</td>

                                        <td class="text-left" style="font-size: 11pt;width: 160px;">{{ $item->name }}
                                        </td>
                                        <td class="text-center" style="font-size: 11pt;">
                                            {{ number_format($item->valor, 2, ',', '.') . ' kz' }}</td>
                                        @if (in_array($item->id_transacion,$est_vetorCreditoAjuste))
                                            <td class="text-center" style="font-size: 10pt">Crédito - Ajuste</td>
                                        @else
                                            <td class="text-center" style="font-size: 10pt">Pagamento</td>
                                        @endif

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
                                    <td class="text-center bg2" style="font-weight: bold;">TOTAL</td>
                                    <td class="text-center bg2" style="font-weight: bold;">
                                        {{ number_format($ta, 2, ',', '.') . ' kz' }}</td>
                                    {{-- <th class="text-center" style="font-size: 16pt;">Banco</th> --}}
                                </tr>

                            </table>

                            <br>
                            <br>
                            <br>
                            <br>
                            <P class="data" style="margin-top:50px;width:720px;font-size:16px;">
                                <b>Observações:</b><br> De acordo a folha de caixa resumo no período solicitado, foram gerados um total de <a style="color:white;text-decoration:none;">.</a> <b>{{ (count($recibo)+count($est_recibo))}}</b> recibos na plataforma forLEARN, dos quais: <b>{{count($recibo)}}</b> encontram-se activos e <b>{{count($est_recibo)}}</b> foram estornados.
                            </P>
                            <br>
                            <br>
                            <br>
                            <br>
                           {{-- <div class="data" style="text-align: left; font-size: 12pt;">

                                <as style="text-transform: capitalize;"> {{ $institution->municipio }}</as>,
                                aos
                                @php
                                    $m = date('m');
                                    $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                                    echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
                                @endphp



                                <br>
                                <titles class="t-color">Powered by</titles> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
                            </div>--}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

@endsection

<script></script>
