                        <table class="table_te tdata">
                          
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">Data de início</th>
                                <th class="text-center">Data de fim</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-center" style="background-color: #F9F2F4;">{{ $DataInicio }}</td>
                                <td class="text-center" style="background-color: #F9F2F4;">{{ $DataFim }}</td>
                            </tr>
                        </table>

                        @php
                            $i = 1;
                        @endphp 




                        <table class="table_te">

                            <tr>
                                <th class="text-center" >#</th>
                                <th>Nº de matrícula</th>
                                <th>Nome do estudante</th>
                                <th>Factura/Recibo nº</th>
                                <th>Data</th>
                                <th>Tesoureiro</th>
                                <th>Valor</th>
                                <th>Tipo transacção</th>



                            </tr>
                            @php
                                $i = 1;
                                $v = 0;
                                $ta = 0;
                            @endphp

                            @foreach ($recibo as $item)
                                <tr>
                                    @if (!in_array($item->id_transacion, $vetorCreditoAjuste))
                                        @php $ta += $item->valor; @endphp
                                    @endif
                                    @php
                                        $data = $item->created_at;
                                        $code = substr($item->created_at, 2, 2);
                                    @endphp
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item->matricula }}</td>
                                    <td >
                                        {{ $item->estudante }}</td>
                                    <td>{{ $code . '-' . $item->code }}
                                    </td>
                                    <td>{{ $data }}</td>

                                    <td>{{ $item->name }}
                                    </td>
                                    <td>
                                        {{ number_format($item->valor, 2, ',', '.') . ' kz' }}</td>
                                    @if (in_array($item->id_transacion, $vetorCreditoAjuste))
                                        <td>Crédito por ajuste</td>
                                    @else
                                        <td>Pagamento</td>
                                    @endif

                                </tr>
                            @endforeach
                     
                            <tr>

                                <th >
                                </th>
                                <th >
                                </th>
                                <th >
                                </th>
                                <th >
                                </th>
                                <th >
                                </th>
                               
                                <td class="text-center" style="font-weight: bold;">TOTAL</td>
                                <td class="text-center" style="font-weight: bold;">
                                    {{ number_format($ta, 2, ',', '.') . ' kz' }}</td>
                                
                            </tr>

                        </table>
                        <table>

                            <tr style="">
                                <th></th>
                                <th></th>
                                <th class="text-center" style="font-size: 14pt;">QUADRO RESUMO
                                </th>
                            </tr>

                        </table>

                        <table >

                            <tr>
                                <th></th>
                                <th class="text-center" style="font-size: 16pt;" colspan="1">FINANCEIRO</th>

                            </tr>

                            <tr>
                                <th></th>
                                <th class="text-center" style="font-size: 16pt;">Banco</th>
                                <th class="text-center" style="font-size: 16pt;">Valor</th>
                            </tr>
                            @foreach ($bancos as $key => $value)
                                @php
                                    $dados = explode(',', $value);
                                    $valor = $dados[1];
                                    $quantidade = $dados[0];
                                @endphp

                                <tr>

                                    <td></td>
                                    <td >{{ $key }}</td>
                                    <td>{{ number_format($valor, 2, ',', '.') . ' kz' }}
                                    </td>

                                </tr>
                                @php
                                    $v += $valor;
                                @endphp
                            @endforeach
                         
                            <tr style="margin-top: 5%"><td></td>
                                <td class="text-right" style="font-weight: bold">TOTAL</td>
                                <td class="text-center" style="font-weight:bold">
                                    {{ number_format($v, 2, ',', '.') . ' kz' }}</td>
                            </tr>

                        </table>








                        <table class="table_te" >

                            <tr>
                                <th></th>
                                <th >RECURSOS
                                    HUMANOS</th>
                            </tr>

                            <tr>

                                <th></th>
                                <th class="text-center" style="font-size: 16pt;">Tesoureiro</th>
                                <th class="text-center" style="font-size: 16pt;">Nº de Factura/Recibo</th>
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
                                    <td></td>
                                    <td class="text-left" style="">{{ $key }}</td>
                                    <td class="text-center" style="">{{ $quantidade }}</td>
                                    <td class="text-center" style="">{{ round($percentagem) . '%' }}
                                    </td>



                                </tr>
                            @endforeach
                           

                            <tr style="font-weight:bold">
                                <th></th>
                                <td class="text-right" style="">TOTAL</td>
                                <td class="text-center">{{ $v }}</td>



                            </tr>

                        </table>

                        <table class="table_te" style="width:420px;margin-left:17.5pc;">

                            <tr>
                                <th></th>
                                <th  colspan="1">NÃO FINANCEIRO</th>
                            </tr>

                            <tr>
                                <th></th>
                                <th >Banco</th>
                                <th >Valor</th>
                            </tr>
                            @php
                                $total = 0;
                                $valor = 0;
                                $quantidade = 0;
                            @endphp
                            @foreach ($cretidoAjuste as $key => $value)
                                @php
                                    $dados = explode(',', $value);
                                    $valor = $dados[1];
                                    $quantidade = $dados[0];
                                @endphp

                                <tr>
                                    <td></td>
                                    <td >{{ $key }}</td>
                                    <td >{{ number_format($valor, 2, ',', '.') . ' kz' }}</td>
                                </tr>
                                @php
                                    $total += $valor;
                                @endphp
                            @endforeach
                            <tr >
                                <td></td>
                                <td >TOTAL</td>
                                <td >
                                    {{ number_format($total, 2, ',', '.') . ' kz' }}</td>
                            </tr>

                        </table>
