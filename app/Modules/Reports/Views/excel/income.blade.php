<table>
    <thead>
        <tr>
            <th colspan="6" rowspan="2" style="text-align: center!important;">{{ $titulo_documento }}
                <br> DATA: {{ $date1 }} - {{ $date2 }}
            </th>
            <th colspan="2" rowspan="2">{{ $institution->nome }}</th>
        </tr>

    </thead>
    <tbody>
        <tr>
            <td></td>
        </tr>

    </tbody>
</table>
@php
    $subtotal = 0;
    $ano = date('Y');
@endphp
@php
    $total = 0;
    $totalSaldoUtilizado = 0;
    $subTotalSaldo = 0;
@endphp


@foreach ($getTransaction as $course => $emoluments)
    @foreach ($emoluments as $course_id => $emoluments)
        @php
            $subtotal = 0;
            $subTotalSaldo = 0;
        @endphp


        <table>

            <thead>
                <tr>
                    <th>#</th>
                    <th>Matricula</th>
                    <th>Nome do aluno</th>
                    <td>Curso</td>
                    <td>Emolumento</td>
                    <th>Banco</th>
                    <th>Referência</th>
                    <th>Data</th>
                    <th>Utilizador</th>
                    <th>Factura/Recibo nº</th>
                    <th>Saldo em carteira</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $i = 1;
                    $count = 1;
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
                        <td> {{ $count++ }} </td>
                        <td class=""> {{ $emolument->matriculation_number }} </td>

                        <td class="">{{ $emolument->full_name }}</td>
                        <td>{{ $emolument->course_name }}</td>
                        <td>{{ $emolument->article_name }}</td>

                        <td>
                            @foreach ($getInfornBanco as $item)
                                @if (
                                    $item->transaction_id == $emolument->transaction_id &&
                                        $item->id_article_requests == $emolument->id_article_requests)
                                    @if ($item->id_bank === 16)
                                        <b class="text-center">- -</b>
                                    @else
                                        &nbsp;{{ $item->bank_name }}, &nbsp; &nbsp;&nbsp;
                                    @endif
                                @endif
                            @endforeach
                        </td>

                        <td>
                            @foreach ($getInfornBanco as $item)
                                @if (
                                    $item->transaction_id == $emolument->transaction_id &&
                                        $item->id_article_requests == $emolument->id_article_requests)
                                    @if ($item->id_bank === 16)
                                        <b class="text-center">- -</b>
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

                                                    @php $valorEmoluTrans=$somarSubTotal=$ResultadoValorEmolu; @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            @endif
                        @endforeach




                        <td>
                            {{ date('d-m-Y', strtotime($emolument->created_atranst)) }}</td>
                        <td class=""> {{ $emolument->created_by_user }}</td>
                        <td> {{ $emolument->recibo }}</td>

                        <td>
                            {{ number_format($somaTotalSaldo = $saldoUtilizado, 2, ',', '.') }} </td>

                        <td>
                            {{ number_format($valorEmoluTrans, 2, ',', '.') }}
                            @php
                                $subtotal += $somarSubTotal;
                                $total += $somarSubTotal;
                                $subTotalSaldo += $somaTotalSaldo;
                                $totalSaldoUtilizado += $somaTotalSaldo;
                            @endphp
                        </td>

                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Sub-totais</b></td>
                    <td>
                        <b>{{ number_format($subTotalSaldo, 2, ',', '.') }}</b> Kz
                    </td>
                    <td class="tfoot">&nbsp;&nbsp; <b>{{ number_format($subtotal, 2, ',', '.') }}</b>Kz</td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endforeach


<table class="table table-parameter-group" cellspacing="2">
    <thead class="thead-parameter-group">

    </thead>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Totais</b></td>
            <td>
                <b>{{ number_format($totalSaldoUtilizado, 2, ',', '.') }} </b>Kz
            </td>
            <td>
                <b>{{ number_format($total, 2, ',', '.') }} </b>Kz
            </td>
        </tr>
    </tbody>
</table>
