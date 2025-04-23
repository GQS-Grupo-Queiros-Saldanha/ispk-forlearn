@section('title', "Folha de caixa [Detalhada]")
@extends('layouts.printForSchedule')
@section('content')


                    
    <main>         
        @php
        $logotipo = "https://".$_SERVER['HTTP_HOST']."/instituicao-arquivo/".$institution->logotipo;
        $doc_name = "FOLHA DE CAIXA [ DETALHADA ]";
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
        /*page-break-inside: avoid;*/
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
        /*page-break-inside: auto*/
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
        /* padding-right: 10px; */
        padding-top: 20px;
    }

</style>
    
    <div class="div-top" style="">
        <div class="div-top">
    
    
            <table class="table table-main m-0 p-1 bg0 " style="border:none;">
                <tr>
    
                    <td class="td-fotografia " rowspan="12"
                        style="background-image:url('{{ $logotipo }}');width:120px; height:68px; background-size:80px 100px;
                            background-repeat:no-repeat;Background-position:center center;
                            border:none;padding-right:12px!important;top:10px;position:relative;left: 10px;
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
        
        {{-- @include('Reports::partials.enrollment-income') --}}
        <div class="container-fluid" style="padding:0px; margin: 0px">
            <div class="row">
                <div class="col-md-12">
                    @php $subtotal = 0; $ano=date('Y'); @endphp
                    @php $total = 0; $totalSaldoUtilizado = 0; $subTotalSaldo = 0; @endphp 
                    
        
                    @foreach ($getTransaction as $course => $emoluments)
                    @foreach ($emoluments as $course_id => $emoluments)
                    @php $subtotal = 0; $subTotalSaldo = 0;   @endphp  

                    <table class="tabela_emolumento" >
                             
                        <tr class="bg1">
                            <td >Emolumento</td>
                            <td >Curso</td>                             
                        </tr>
                        <tr>
                            <td   style="width: 300px;">{{ $course }}</td>
                            <td   style="width: 300px;">{{ $course_id }}</td>
                        </tr>
                    </table>


                    <table class="container-fluid w-100 table table-parameter-group  tabela_principal" >
                       
        
                        <tr class="thead bg1" style="font-family: calibri light;">
                            <th style="">#</th>
                            <th style="">Matricula</th>
                            <th style="">Nome do aluno</th>
                            {{-- <th style=" width:300px;">Ano</th> --}}
                            <th style="">Banco</th>
                            <th style="">Referência</th>
                            <th style="">Data</th>
                            <th style="">Utilizador</th>
                            <th style="">Nº de recibo</th>
                            <th style="">Saldo em carteira</th>
                            <th style="">Valor</th>
                        </tr>

                        <tbody class="corpo_td">
                            <style>.corpo_td td{font-family: calibri light;font-size:80%; color:#444; border:none; padding: 0; background-color:transparent;}
                                .cor_linha{background-color:#f2f2f3;color:#000;}
                            </style>
                   
                            @php $i=1; $count=1;  @endphp
        
                            @foreach ($emoluments as $emolument)
                                    @php $cor = $i++ % 2 === 0 ? 'cor_linha':''; @endphp 
                                     @php $somaTotalSaldo = 0; $somarSubTotal =0;@endphp
                                     @php $valorEmoluTrans =0; @endphp   
                                     @php $resultado =0; $saldoResultado =0; @endphp     
                                     @php $ResultadoValorEmolu =0; @endphp   
                                     @php $saldoUtilizado =0; @endphp   
                                    <tr class="{{$cor}}">
                                        <td style="text-align: center;" class="td-parameter-column"> {{ $count++}} </td>
                                        <td class=""> {{ $emolument->matriculation_number }} </td>
                                       
                                        <td class="" >{{ $emolument->full_name }}</td>
                                        {{-- <td style="text-align: center;" class="td-parameter-column"> {{ $emolument->ano_curso }} º </td> --}}
                                        <td style="" class="column">
                                            @foreach ($getInfornBanco as $item)
                                                @if ($item->transaction_id==$emolument->transaction_id && $item->id_article_requests==$emolument->id_article_requests   )
                                                    @if ($item->id_bank===16)
                                                               <b class="text-center">- -</b>
                                                    @else
                                                    &nbsp;{{$item->bank_name}}, &nbsp; &nbsp;&nbsp;
                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>

                                        <td tyle="" class="column">
                                            @foreach ($getInfornBanco as $item)
                                                @if ($item->transaction_id==$emolument->transaction_id && $item->id_article_requests==$emolument->id_article_requests   )
                                                    @if ($item->id_bank===16)
                                                    <b class="text-center">- -</b>
                                                    @else
                                                        &nbsp;{{$item->reference }}&nbsp;/&nbsp;
                                                        

                                                    @endif
                                                @endif
                                            @endforeach
                                        </td>


                                     
                                        @foreach ($objetoValorTransacao as $item)
                                     
                                                @if ($item->transaction_id == $emolument->transaction_id && $item->valorTotal_trans==0)
                                                    @foreach ($objetoSaldo_cartera as $element)
                                                        @if ($element->transaction_id == $emolument->transaction_id)
                                                            @php  $saldoResultado=$element->valor_saldo - $emolument->price; @endphp
                                                            {{-- condicionar se saldo e carteira foi suficiente para pagar o emolumento --}}
                                                            @if ($saldoResultado>0 || $saldoResultado==0)
                                                                {{-- {{number_format($somarSubTotal= $item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans, 2, ",", ".") }} --}}
                                                                @php $valorEmoluTrans=$somarSubTotal= $item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans; @endphp
                                                                @php 
                                                                    $saldoResultado=$element->valor_saldo - $emolument->price; 
                                                                    $element->valor_saldo= $saldoResultado;
                                                                    $saldoUtilizado=$emolument->price;
                                                                @endphp
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                    {{-- caso a transação não foi totalmento com saldo em carteira entras nesta estrutura de condição --}}
                                                @else
                                                    @if ($item->transaction_id == $emolument->transaction_id)
                                                        @php $resultado=$item->valorTotal_trans - $emolument->price; @endphp
                                                        @if ($resultado>0 || $resultado==0)
                                                                 {{-- {{number_format($somarSubTotal=$item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans, 2, ",", ".") }}  --}}
                                                            @php $valorEmoluTrans=$somarSubTotal=$item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans; $item->valorTotal_trans=$resultado; @endphp      
                                                        @else
                                                            @foreach ($objetoSaldo_cartera as $element)
                                                                @if ($element->transaction_id == $emolument->transaction_id)
                                                                    @php  $saldoResultado=$element->valor_saldo + $item->valorTotal_trans; $ResultadoValorEmolu=$saldoResultado; @endphp
                                                                    @php $resultado=$saldoResultado - $emolument->price; @endphp 
                                                                    
                                                                    @if ($resultado>0 || $resultado==0)
                                                                       {{-- {{number_format($somarSubTotal=$saldoResultado >= $emolument->price ? $item->valorTotal_trans : $emolument->price, 2, ",", ".") }} --}}
                                                                        @php $valorEmoluTrans=$somarSubTotal=$saldoResultado >= $emolument->price ? $item->valorTotal_trans : $emolument->price; @endphp
                                                                        @php  $element->valor_saldo= $resultado; @endphp
                                                                        @php  $saldoUtilizado=$emolument->price -$item->valorTotal_trans; $item->valorTotal_trans=0;@endphp
                                                                    @else
                                                                        @php  $saldoResultado=$element->valor_saldo + $resultado;@endphp
                                                                        @php  $ResultadoValorEmolu=-($ResultadoValorEmolu);@endphp
                                                                        @php  $element->valor_saldo=$saldoResultado;@endphp
                                                                        @php  $saldoUtilizado=-($resultado);@endphp
                                                                        {{number_format($somarSubTotal=$ResultadoValorEmolu, 2, ",", ".") }}

                                                                        @php $valorEmoluTrans=$somarSubTotal=$ResultadoValorEmolu; @endphp
                                                                       
                                                                    @endif
                                                                @endif
                                                            @endforeach              
                                                        @endif
                                                    @endif
                                                @endif
                                        @endforeach



                                        
                                        <td class="" style="text-align: center;"> {{date('d-m-Y', strtotime($emolument->created_atranst))}}</td>
                                        <td class=""> {{ $emolument->created_by_user }}</td>
                                        <td class="" style="text-align: center;"> {{ $emolument->recibo }}</td>

                                        <td style="text-align: center;">{{number_format($somaTotalSaldo=$saldoUtilizado, 2, ",", ".") }} </td>

                                        <td style="text-align: right" class="td-parameter-column">
                                            {{number_format($valorEmoluTrans, 2, ",", ".") }}
                                            
                                            {{-- Os calculo de subTotal, total e saldoTatoal são na estrutura a baixo --}}
                                            @php $subtotal +=$somarSubTotal; $total +=$somarSubTotal; $subTotalSaldo+=$somaTotalSaldo;$totalSaldoUtilizado+=$somaTotalSaldo @endphp 
                                        </td>
                                                                                      
                                    </tr>

                            @endforeach
                            <tr style="border:none;">
                                <td></td><td></td><td></td>
                                <td></td> <td></td> <td ></td><td ></td><td style="text-align: right;font-size: 1pc">Sub-totais</td>
                                <td  style="border-bottom: 1px solid black;border-right: 5px solid #fff;text-align: right"><b>{{ number_format($subTotalSaldo, 2, ",", ".") }}</b>Kz</td>
                                <td style="border-bottom: 1px solid black;border-right: 5px solid #fff;text-align: right">&nbsp;&nbsp; <b>{{ number_format($subtotal, 2, ",", ".") }}</b>Kz</td>
                            </tr>                    
                        </tbody>                                      
                    </table>
               
                    @endforeach
                    @endforeach
               

                    <br>









                    
                        <table class="table table-parameter-group"  cellspacing="2">
                            <thead class="thead-parameter-group">
                                
                            </thead>
                            <tbody >
                                <tr>
                                    <td style="width: 1020px;"></td>
                                    <td style="text-align: right">Totais:</td>
                                    <td style="text-align: right; border-bottom:1px solid !important;"><b>{{number_format($totalSaldoUtilizado, 2, ",", ".") }} </b>Kz</td>
                                    <td style="text-align: right; border-bottom:1px solid !important;"><b>{{number_format($total, 2, ",", ".") }} </b>Kz</td>  
                                </tr>  
                            </tbody>
                        </table>
                </div>        
            </div>
        </div>
        {{-- @include('Reports::partials.enrollment-income-footer') --}}
    </main>
@endsection

