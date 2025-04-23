@php use App\Modules\Reports\Controllers\DocsReportsController; @endphp
@section('title', 'Extracto')
@extends('layouts.printForSchedule')
@section('content')



    
    @php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    $documentoCode_documento = 50;
    $doc_name = 'Extracto de conta'; 
    $discipline_code = '';
    @endphp
<main>
    @include('Reports::pdf_model.forLEARN_header')
        <div class="container-fluid" style="padding:0px; margin: 0px">
            <div class="row">
                <div class="col-md-12">
                    @php
                        $subtotal = 0;
                        $ano = date('Y');
                    @endphp
                    @php
                        $count = 1;
                        $total = 0;
                        $totalSaldoUtilizado = 0;
                        $subTotalSaldo = 0;
                    @endphp
                    <br>
                    <table>
                        <thead>
                            <tr class="bg1">
                                <th style="width:10px;"></th>
                                <th style="width:200px;">Nº de Matrícula: </th>
                                <th style="width:500px;">Estudante: </th>
                                <th style="width:300px;">email: </th>
                                <th style="width:500px;">Curso: </th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>{{ $students->meca }}</td>
                                <td> {{ $students->display_name }}</td>
                                <td>{{ $students->email }}</td>
                                <td>{{ $students->course }}</td>

                            </tr>
                            <tr class="bg1">
                                <th></th>
                                <th>Turma: </th>
                                <th>Ano Curricular: </th>
                                <th>Ano Lectivo: </th>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>{{ DocsReportsController::getTurma($students->mt_id, $students->lective_year, $students->year) }}
                                </td>
                                <td>{{ $students->year }}ª </td>
                                <td>
                                    @php
                                        $ano_final = $lectiveYears;
                                        switch ($ano_final) {
                                            case '20/21':
                                                $ano_final = '2020/2021';
                                                break;
                                            case '21/22':
                                                $ano_final = '2021/2022';
                                                break;
                                            case '22/23':
                                                $ano_final = '2022/2023';
                                                break;
                                            case '23/24':
                                                $ano_final = '2023/2024';
                                                break;
                                        }
                                        
                                        echo $ano_final;
                                    @endphp

                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <br>
                    <h4 class="bg1 " style="padding-top: 6px;">EMOLUMENTOS PAGOS</h4>
                    <br>
                    <table class="container-fluid w-100 table table-parameter-group  tabela_principal">
                        <thead>
                            <tr class="bg1" style="font-family: calibri light;">
                                <th style="font-size:10pt; width:100px;">#</th>
                                <th style="font-size:10pt; width:1000px;">Emolumentos</th>
                                <th style="font-size:10pt;">Banco</th> 
                                <th style="font-size:10pt;  width:300px">Referência</th>
                                <th style="font-size:10pt;width:350px;">Data</th>
                                <th style="font-size:10pt;width:500px;">Tesoureio</th>
                                <th style="font-size:10pt;width:300px;">Recibo</th>
                                <th style="font-size:10pt;width:300px;">S. carteira</th>
                                <th style="font-size:10pt;width:300px;">Valor</th>
                            </tr>
                        </thead>


                        @foreach ($getTransaction as $course => $emoluments)
                            @foreach ($emoluments as $course_id => $emoluments)
                                @php
                                    $subtotal = 0;
                                    $subTotalSaldo = 0;
                                @endphp



                                <style>
                                    .tabela_principal {
                                        height: 30px;
                                        padding: 0;
                                        margin: 0;
                                        margin-bottom: 1px;
                                    }

                                    .tabela_principal td {
                                        background-color: transparent;
                                        font-family: calibri light;
                                    }

                                    .tabela_principal .thead {
                                        border: 1px solid #2c2c2c;
                                        padding: 0;
                                        color: #fff;
                                    }

                                    .tabela_principal .thead th {
                                        
                                        border: 1px solid #fff;
                                        width: 1000px;
                                        text-align: center;
                                        padding: 0px;
                                        padding-left: 1px;
                                    }
                                </style>

                                <tbody class="corpo_td">
                                    <style>
                                        .corpo_td td {
                                            font-family: calibri light;
                                            font-size: 80%;
                                            color: #444;
                                            border: none;
                                            padding: 0;
                                            background-color: transparent;
                                        }

                                        .cor_linha {
                                            background-color: #f2f2f3;
                                            color: #000;
                                        }
                                    </style>

                                    @php
                                        $i = 1;
                                        
                                    @endphp

                                    @foreach ($emoluments as $emolument)
                                        @php $cor = $i++ % 2 === 0 ? 'cor_linha':''; @endphp
                                        @php
                                            $somaTotalSaldo = 0;
                                            $somarSubTotal = 0;
                                        @endphp
                                        @php $valorEmoluTrans =0; @endphp
                                        @php
                                            $resultado = 0;
                                            $saldoResultado = 0;
                                        @endphp
                                        @php $ResultadoValorEmolu =0; @endphp
                                        @php $saldoUtilizado =0; @endphp
                                        <tr class="{{ $cor }}">
                                            <td style="text-align: center;" class="td-parameter-column"> {{ $count++ }}
                                            </td>
                                            <td style="text-align: left;" class="td-parameter-column"> {{ $course }}   {{ isset($emolument->code_discipline) ?" ( #".$emolument->code_discipline." - ".$emolument->name_discipline." ) ": '' }}
                                                @isset($emolument->article_month)
                                                    @if ($emolument->article_month == 1)
                                                        (Janeiro {{ $emolument->article_year }})
                                                    @elseif($emolument->article_month == 2)
                                                        ( Fevereiro {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 3)
                                                        ( Março {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 4)
                                                        ( Abril {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 5)
                                                        ( Maio {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 6)
                                                        ( Junho {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 7)
                                                        ( Julho {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 8)
                                                        ( Agosto {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 9)
                                                        ( Setembro {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 10)
                                                        ( Outubro {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 11)
                                                        ( Novembro {{ $emolument->article_year }} )
                                                    @elseif ($emolument->article_month == 12)
                                                        ( Dezembro {{ $emolument->article_year }} )
                                                    @endif
                                                @endisset
                                            </td>
                                            <td style="width: 20pc" class="column">
                                                @foreach ($getInfornBanco as $item)
                                                    @if (
                                                        $item->transaction_id == $emolument->transaction_id &&
                                                            $item->id_article_requests == $emolument->id_article_requests)
                                                        @if ($item->id_bank === 16)
                                                            <b class="text-left">- -</b>
                                                        @else
                                                            &nbsp;{{ $item->bank_name }}, &nbsp; &nbsp;&nbsp;
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>

                                            <td tyle="width: 30pc" class="column">
                                                @foreach ($getInfornBanco as $item)
                                                    @if (
                                                        $item->transaction_id == $emolument->transaction_id &&
                                                            $item->id_article_requests == $emolument->id_article_requests)
                                                        @if ($item->id_bank === 16)
                                                            <b class="text-left">- -</b>
                                                        @else
                                                            &nbsp;{{ $item->reference }}&nbsp;/&nbsp;
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>


                                           
                                            @foreach ($objetoValorTransacao as $item)
                                              
                                                @if ($item->transaction_id == $emolument->transaction_id && $item->valorTotal_trans == 0)
                                                    @foreach ($objetoSaldo_cartera as $element)
                                                        @if ($element->transaction_id == $emolument->transaction_id)
                                                            @php  $saldoResultado=$element->valor_saldo - $emolument->price; @endphp
                                                          
                                                            @if ($saldoResultado > 0 || $saldoResultado == 0)
                                                             
                                                                @php $valorEmoluTrans=$somarSubTotal= $item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans; @endphp
                                                                @php
                                                                    $saldoResultado = $element->valor_saldo - $emolument->price;
                                                                    $element->valor_saldo = $saldoResultado;
                                                                    $saldoUtilizado = $emolument->price;
                                                                @endphp
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                    
                                                @else
                                                    @if ($item->transaction_id == $emolument->transaction_id)
                                                        @php $resultado=$item->valorTotal_trans - $emolument->price; @endphp
                                                        @if ($resultado > 0 || $resultado == 0)
                                                            
                                                            @php
                                                                $valorEmoluTrans = $somarSubTotal = $item->valorTotal_trans >= $emolument->price ? $emolument->price : $item->valorTotal_trans;
                                                                $item->valorTotal_trans = $resultado;
                                                            @endphp
                                                        @else
                                                            @foreach ($objetoSaldo_cartera as $element)
                                                                @if ($element->transaction_id == $emolument->transaction_id)
                                                                    @php
                                                                        $saldoResultado = $element->valor_saldo + $item->valorTotal_trans;
                                                                        $ResultadoValorEmolu = $saldoResultado;
                                                                    @endphp
                                                                    @php $resultado=$saldoResultado - $emolument->price; @endphp

                                                                    @if ($resultado > 0 || $resultado == 0)
                                                                       
                                                                        @php $valorEmoluTrans=$somarSubTotal=$saldoResultado >= $emolument->price ? $item->valorTotal_trans : $emolument->price; @endphp
                                                                        @php  $element->valor_saldo= $resultado; @endphp
                                                                        @php
                                                                            $saldoUtilizado = $emolument->price - $item->valorTotal_trans;
                                                                            $item->valorTotal_trans = 0;
                                                                        @endphp
                                                                    @else
                                                                        @php  $saldoResultado=$element->valor_saldo + $resultado;@endphp
                                                                        @php  $ResultadoValorEmolu=-($ResultadoValorEmolu);@endphp
                                                                        @php  $element->valor_saldo=$saldoResultado;@endphp
                                                                        @php  $saldoUtilizado=-($resultado);@endphp
                                                                        {{ number_format($somarSubTotal = $ResultadoValorEmolu, 2, ',', '.') }}
                                                                        kz

                                                                        @php $valorEmoluTrans=$somarSubTotal=$ResultadoValorEmolu; @endphp
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach




                                            <td class="" style="text-align: center;">
                                                {{ date('d-m-Y', strtotime($emolument->created_atranst)) }}</td>
                                            <td class=""> {{ $emolument->created_by_user }}</td>
                                            <td class="" style="text-align: center;"> {{ $emolument->recibo }}</td>

                                            <td style="text-align: right;">
                                                {{ number_format($somaTotalSaldo = $saldoUtilizado, 2, ',', '.') }} kz
                                            </td>

                                            <td style="text-align: right" class="td-parameter-column">
                                                {{ number_format($valorEmoluTrans, 2, ',', '.') }} kz

                                                {{-- Os calculo de subTotal, total e saldoTatoal são na estrutura a baixo --}}
                                                @php
                                                    $subtotal += $somarSubTotal;
                                                    $total += $somarSubTotal;
                                                    $subTotalSaldo += $somaTotalSaldo;
                                                    $totalSaldoUtilizado += $somaTotalSaldo;
                                                @endphp
                                            </td>

                                        </tr>
                                    @endforeach
                            @endforeach
                        @endforeach













                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;"><b>Total</b></td>
                            <td style="text-align: right;"><b>{{ number_format($totalSaldoUtilizado, 2, ',', '.') }} </b>Kz
                            </td>
                            <td style="text-align: right;"><b>{{ number_format($total, 2, ',', '.') }} </b>Kz</td>
                        </tr>

                        </tbody>
                    </table>
                    <br>
                    
                    <div class="row">
                        <div class="col-5" style="">
                            <div class="row">
                                <h4 class="bg1 " style="width: 100%; text-align: center;padding-top:6px;">EMOLUMENTOS
                                    PENDENTES</h4>
                            </div>
                            <div class="row">
                                <br>
                                <table class="container-fluid table table-parameter-group  tabela_principal"
                                    style="widht: 700px!important;margin:0 auto;">
                                    <thead>
                                        <tr class="thead bg1" style="font-family: calibri light;">
                                            <th style="font-size:10pt; text-align: center;width: 200px">#</th>
                                            <th style="font-size:10pt; text-align: center;">Emolumentos</th>
                                            <th style="font-size:10pt; text-align: center;width: 200px;">Estado</th>
                                            {{-- <th style="font-size:1pc;width:20pc;">Estado do Pagamento</th> --}}
                                            <th style="font-size:10pt; text-align: center;width: 200px;">Valor a
                                                pagar</th>

                                        </tr>
                                    </thead>
                                    <tbody class="tbody-parameter-group">
                                        @php
                                            $count_p = 1;
                                            $total_p = 0;
                                        @endphp
                                        @foreach ($emolumento as $item)
                                            @php
                                                $total_p = $total_p + $item->price;
                                            @endphp
                                            <tr>
                                                <td style="text-align: center;" class="td-parameter-column">{{ $count_p++ }}</td>
                                                <td style="text-align: left;" class="td-parameter-column">
                                                    {{ $item->article_name }}
                                                    @isset($item->article_month)
                                                        @if ($item->article_month == 1)
                                                            ( Janeiro {{ $item->article_year }} )
                                                        @elseif($item->article_month == 2)
                                                            ( Fevereiro {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 3)
                                                            ( Março {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 4)
                                                            ( Abril {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 5)
                                                            ( Maio {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 6)
                                                            ( Junho {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 7)
                                                            ( Julho {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 8)
                                                            ( Agosto {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 9)
                                                            ( Setembro {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 10)
                                                            ( Outubro {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 11)
                                                            ( Novembro {{ $item->article_year }} )
                                                        @elseif ($item->article_month == 12)
                                                            ( Dezembro {{ $item->article_year }} )
                                                        @endif
                                                    @endisset
                                                </td>
                                                <td style="text-align: center">Pendente</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($item->price, 2, ',', '.') }} kz </td>

                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: right"><b>Total</b></td>
                                            <td style="text-align: right"> <b>{{ number_format($total_p, 2, ',', '.') }}
                                                    kz</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-2">
                        </div>
                        <div class="col-5">
                            <div class="row">
                                <h4 class="bg1" style="width: 100%; text-align: center;padding-top:6px;">RESUMO
                                    FINANCEIRO</h4>
                            </div>
                            <div class="row"> 
                          

                                <br>
                                <table class="container-fluid table table-parameter-group  tabela_principal"
                                    style="widht: 700px!important;margin:0 auto;">
                                    <thead>
                                        <tr class="thead bg1" style="font-family: calibri light;">
                                            <th colspan="4" style="font-size:10pt;">Emolumentos</th>
                                        </tr>
                                       
                                    </thead>
                                    <tbody class="tbody-parameter-group">
                                            <tr class="bg1">
                                                <td style="text-align: center">Pagos</td>   
                                                <td style="text-align: center">Pendentes</td>
                                                <td style="text-align: center;">Total</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center">{{ $count-1}}</td>   
                                                <td style="text-align: center">{{ $count_p-1 }}</td>
                                                <td style="text-align: center;">{{ ($count_p-1) + ($count-1) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: center">Total <b>  
                                                     {{ number_format(($total+$totalSaldoUtilizado), 2, ',', '.') }} kz</b> </td>
                                                <td style="text-align: center">Total <b>  
                                                     {{ number_format(($total_p), 2, ',', '.') }} kz</b></td>
                                                <td style="text-align: center;"></td>
                                            </tr>
                                    </tbody> 
                                </table>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>
        </div>
        {{-- @include('Reports::partials.enrollment-income-footer') --}}
    </main>
@endsection
