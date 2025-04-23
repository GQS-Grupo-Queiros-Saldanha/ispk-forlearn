<!DOCTYPE html>
<html lang="pt">

<head>
 
    <style>
        .tr td{
            border-bottom: 1px solid white!important;
            border-right: 1px solid white!important;
            padding: 3px;
        }
    </style>
</head>
@php 
    $doc_name = "FOLHA DE SALÁRIO REFERENTE AO MÊS DE: ".$dataPagamento;
@endphp
<body>
    <div class="bg0">
        @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')

    </div>
    <div class="m-0 p-0 row div-container">
        <div class="reciboGeral " >
           

            {{-- <div class="col-sm-12 text-center">
                <p class="text-center" style="font-size: 1.3pc; font-weight: normal"><i>FOLHA DE SALÁRIO REFERENTE AO MÊS
                        DE:</i> <b> {{ $dataPagamento }}</b></p> --}}
                        <br>
            </div>
            <div class="m-0 p-0 mt-1">
              
                @php
                    $vetorSub = [];
                    $vetorImp = [];
                    $qdtColumSB = 0;
                    $totalgeralIRT = 0;
                    $totalEmpregado = 0;
                    $totalEmpregador = 0;
                    $totalSalario = 0;
                @endphp

                <div style="margin-bottom: 5px;" class="">

                    <table style="width: 100%" class="table table-striped table-sm table-main"  cellspacing="0" >
                        {{-- Cabeçalho principal --}}
                        <thead>
                            <tr id="cabecalho1" style="page-break-after: auto;" class="bg2">
                                <th class="text-center" style="">Nº</th>
                                <th class="text-center" style="">Nome</th>
                                <th class="text-center" style="">Dep/Secção</th>
                                <th class="text-center" style="">Categoria</th>
                                <th class="text-center" style="">Início de Função</th>
                                <th class="text-center" style="">Conta Bancaria</th>
                                <th class="text-center" style="">Salário base</th>
                                <th class="text-center" style="" colspan="1">Bónus</th>
                                <th class="text-center" style="">Nº Faltas</th>
                                <th class="text-center" style="">Total liquido</th>
                                <th class="text-center" style="" colspan="5">Descontos</th>
                                <th class="text-center" style="">Salário líquido</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr id="cabecalho2" style="">
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                {{-- BONUS --}}
                                {{--
                            <th class="text-center" style="">Retroactivo</th>
                            --}}
                                <th class="text-center" style="">Subsídio</th>
                                {{--
                            <th class="text-center" style="">13º</th>
                            --}}
                                <th class="text-center" style="border: none"></th>
                                <th class="text-center" style="border: none"></th>
                                {{-- DESCONTOS --}}
                                @foreach ($getFunProcessoImposto as $item)
                                    @if (!in_array($item->id_imposto, $vetorImp))
                                        @php $vetorImp[]=$item->id_imposto @endphp
                                        @if ($item->nome_code == 'inss')
                                            <th class="text-center" style="">{{ $item->name_imposto }}
                                            </th>
                                        @elseif($item->nome_code == 'irt')
                                            <th class="text-center" style="">{{ $item->name_imposto }}
                                            </th>
                                        @endif
                                    @endif
                                @endforeach
                                <th class="text-center" style="">Faltas</th>
                                <th class="text-center" style="">Reembolso</th>
                                <th class="text-center" style="">Total KZ</th>
                            </tr>

                            {{-- LISTAGEM DOS DAOS --}}
                            @php
                                $subsidoImpostoIRT = 0;
                                $subsidoImpostoINSS = 0;
                            @endphp
                            @php
                                $pesquisarImposto = false;
                                $qtd_falta = 0;
                                $id_processoSalario;
                                $i = 0;
                                $idProcefunSalario = [];
                                $qdtColumSB = $qdtColumSB - 1;
                                $coluSub = 0;
                                $salario_base = 0;
                                $tataolSubsidio = 0;
                            @endphp
                            @foreach ($getFunProcessoSalario as $id_processam_sl => $getFunProcessoSl)
                                @php
                                    $pesquisarImposto = false;
                                    $qtd_falta = 0;
                                    $i++;
                                    $coluSub = 0;
                                    $id_processoSalario = $id_processam_sl;
                                @endphp
                                @foreach ($getFunProcessoSl as $name_funcionario => $getFunProcessoSalari)
                                    @foreach ($getFunProcessoSalari as $name_cargo => $getFunProcessoSalar)
                                        {{-- subsidio com imposto --}}
                                        @foreach ($getFunProcessoSalar as $item)
                                            @if ($item->id_subsidio != null)
                                                @foreach ($gethistoricProcessoImposto as $key => $getImpoto)
                                                    @if ($getImpoto->id_processamento == $id_processam_sl)
                                                        @foreach ($getSubsidioImposto as $valueElement)
                                                            @if (
                                                                $valueElement->imposto_id == $getImpoto->id_impost &&
                                                                    $valueElement->subsidio_id == $item->id_subsidio &&
                                                                    $getImpoto->nome_code == 'inss')
                                                                @php
                                                                    $subsidoImpostoINSS += $item->valor_subsidio;
                                                                @endphp
                                                            @elseif(
                                                                $valueElement->imposto_id == $getImpoto->id_impost &&
                                                                    $valueElement->subsidio_id == $item->id_subsidio &&
                                                                    $getImpoto->nome_code == 'irt')
                                                                @php
                                                                    $subsidoImpostoIRT += $item->valor_subsidio;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        <tr class="tr" style="background: #f0f2f5;border-bottom:2px solid white!important;">
                                            {{-- inicio listar os dados princípais como nome, cargo, salario, banco --}}
                                            <td class="text-center" >{{ $i }}</td>
                                            <td class="pl-2" >{{ $name_funcionario }}</td>
                                            <td class="pl-2" >{{ $name_cargo }}</td>
                                            <td class="pl-2" ></td>
                                            <td class="pl-2" >
                                                {{ $getFunProcessoSalar[0]->data_inicio_conrato }}</td>
                                            <td class="pl-2" >
                                                {{ $getFunProcessoSalar[0]->conta_banco }}</td>
                                            <td class="text-center" style="">
                                                <b>{{ number_format($getFunProcessoSalar[0]->salarioBase, 1, ',', '.') }}</b>
                                            </td>
                                            @php
                                                $salario_base = $getFunProcessoSalar[0]->salarioBase;
                                            @endphp
                                            {{-- fim --}}

                                            {{-- inicio da criação para listar o valor do subsídio do funcionario  --}}
                                            @foreach ($getFunProcessoSalar as $key => $item)
                                                @php
                                                    $qtd_falta = $item->valor_falta;
                                                    $coluSub = $key;
                                                    $tataolSubsidio += $item->valor_subsidio;
                                                @endphp
                                                {{-- TEM SUBSIDIOS --}}
                                                {{-- 
                                                    <td class="text-center" >{{number_format(0, 0, ',', '.') }}</td>    
                                                 --}}
                                            @endforeach
                                            {{-- logica que permite que as outras colunas sejam criadas, quando o funcionario não tem subsídio --}}
                                            @for ($a = 1; $a <= $qdtColumSB; $a++)
                                                @php $coluSub++ @endphp
                                                @if ($coluSub <= $qdtColumSB)
                                                    {{-- NÃO TEM SUBSIDIOS --}}
                                                    {{-- 
                                                        <td class="text-center" >{{number_format(0, 0, ',', '.') }}</td>    
                                                     --}}
                                                @endif
                                            @endfor

                                            {{-- RETROACTIVO --}}
                                            {{--
                                            <td class="text-center" >{{number_format(0, 0, ',', '.') }}</td> 
                                            --}}
                                            <td class="text-center" ><b>
                                                    {{ number_format($tataolSubsidio, 0, ',', '.') }}</b> </td>
                                            {{-- 13º --}}
                                            {{--
                                            <td class="text-center" style=""><b> {{number_format($salario_base, 1, ',', '.') }}</b></td>
                                            --}}
                                            <td class="text-center" ><b> 0</b> </td>
                                            {{-- TOTAL LIQUIDO $getFunProcessoSalar[0]->valorReembolso --}}
                                            <td class="text-center" style=""><b>
                                                    {{ number_format($tataolSubsidio + $salario_base, 1, ',', '.') }}</b>
                                            </td>

                                            {{-- fim --}}

                                            {{-- calculo do imposto INS e sobre as suas percentagens -----......... --}}
                                            @php
                                                $insTaxa = null;
                                                $vetorImpotosYear = [];
                                                $resultadoINS = 0;
                                                $valorINS_fun = null;
                                                $totalSubsidio = null;
                                            @endphp
                                            @foreach ($gethistoricProcessoImposto as $element)
                                                @if ($element->id_processamento == $id_processam_sl)
                                                    @php  $pesquisarImposto=true; @endphp
                                                    @if (!in_array($element->id_impostoYear, $vetorImpotosYear))
                                                        @php $vetorImpotosYear[]=$element->id_impostoYear @endphp
                                                        @foreach ($getFunProcessoImposto as $item)
                                                            @if (
                                                                $element->id_impostoYear == $item->id_impostYear &&
                                                                    $element->id_impost == $item->id_imposto &&
                                                                    $item->nome_code == 'inss')
                                                                @php
                                                                    $resultadoINS = ($subsidoImpostoINSS + $salario_base + $tataolSubsidio) * ($item->taxa / 100);
                                                                @endphp
                                                                @if ($valorINS_fun == null || $resultadoINS < $valorINS_fun)
                                                                    @php
                                                                        $valorINS_fun = $resultadoINS;
                                                                    @endphp
                                                                @else
                                                                    @php $totalEmpregador+=$resultadoINS;@endphp
                                                                @endif
                                                                @php $totalSubsidio+=$resultadoINS;@endphp
                                                                {{-- 
                                                                    <td class="text-center">&nbsp;A {{number_format($resultadoINS, 0, ',', '.') }}&nbsp;</td>
                                                                    --}}
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endforeach
                                            {{-- SEGURANÇA SOCIAL FUNCIONÁRIO --}}
                                            <td class="text-center">
                                                &nbsp;{{ number_format($valorINS_fun, 0, ',', '.') }}&nbsp;</td>

                                            @if ($pesquisarImposto == false)
                                                @for ($a = 0; $a <= $qtdColunaImp; $a++)
                                                    <td class="text-center">{{ number_format(0, 0, ',', '.') }}</td>
                                                    @php $i++ @endphp
                                                @endfor
                                            @endif
                                            {{-- fim --}}

                                            {{-- calculo do imposto IRT e sobre as suas percentagens e parcelea fixa   --}}
                                            @php
                                                $variavel = 0;
                                                $pesquisaTaxa = false;
                                                $vetorImpotosYear = [];
                                                $resultadoIRT = 0;
                                                $mc = 0;
                                                $excesso = 0;
                                                $totalExec = 0;
                                            @endphp
                                            @foreach ($getFunProcessoSalario as $id_processo => $item)
                                                @foreach ($gethistoricProcessoImposto as $element)
                                                    @if (
                                                        !in_array($element->id_processamento, $vetorImpotosYear) &&
                                                            $id_processo == $id_processoSalario &&
                                                            $element->id_processamento == $id_processoSalario)
                                                        @php  $vetorImpotosYear[]=$element->id_processamento; @endphp
                                                        @foreach ($getFunProcessoImposto as $key => $value)
                                                            @if (
                                                                $pesquisaTaxa == false &&
                                                                    $value->nome_code == 'irt' &&
                                                                    $subsidoImpostoIRT + $salario_base >= $value->valor_inicial &&
                                                                    $subsidoImpostoIRT + $salario_base <= $value->valor_final)
                                                                @php $pesquisaTaxa = true;
                                                                    $mc = $subsidoImpostoIRT + $salario_base + $tataolSubsidio - $valorINS_fun;
                                                                    $excesso = $value->excesso;
                                                                    $totalExec = $mc - $excesso;
                                                                    $resultadoIRT = $totalExec * ($value->taxa / 100) + $value->parcela_fixa;
                                                                    $variavel = ($tataolSubsidio + $salario_base) * ($value->taxa / 100);
                                                                    $totalgeralIRT += $resultadoIRT;
                                                                    $totalEmpregado += $valorINS_fun;
                                                                @endphp
                                                                {{-- 
                                                                <td class="text-center">&nbsp;{{number_format($value->parcela_fixa, 0, ',', '.') }}&nbsp;</td>
                                                                <td class="text-center">&nbsp;{{$value->taxa}}%&nbsp;</td>
                                                                <td class="text-center">&nbsp;{{number_format($variavel, 0, ',', '.') }}&nbsp;</td>
                                                                --}}
                                                            @endif
                                                        @endforeach
                                                        @php
                                                            $valorReembolso = $getFunProcessoSalar[0]->valorReembolso;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            {{-- TOTAL IRT FUNCIONÁRIO --}}
                                            @if ($pesquisarImposto == true)
                                                <td class="text-center">
                                                    &nbsp;{{ number_format($resultadoIRT, 1, ',', '.') }}&nbsp;</td>
                                            @endif
                                            @if ($pesquisarImposto == false)
                                                <td class="text-center">&nbsp;0 & 0%&nbsp;</td>
                                                <td class="text-center">{{ number_format(0, 0, ',', '.') }}</td>
                                            @endif
                                            {{-- fim --}}

                                            <td class="text-center"> {{ number_format($qtd_falta, 1, ',', '.') }}</td>
                                            {{-- valor da falta  --}}

                                            {{-- Calcular total descontos --}}
                                            @php $totalDescontos=0; @endphp
                                            {{-- @if ($resultadoIRT != 0) --}}
                                            @php  $totalDescontos= $valorINS_fun + $resultadoIRT + $qtd_falta @endphp
                                            {{-- @endif --}}
                                            {{-- REEMBLOSO --}}
                                            <td class="text-center">
                                                {{ number_format($getFunProcessoSalar[0]->valorReembolso, 0, ',', '.') }}
                                            </td>
                                            {{-- KZ --}}
                                            <td class="text-center" >
                                                <b>{{ number_format($totalDescontos, 2, ',', '.') }}</b></td>
                                            {{-- fim --}}

                                            {{-- calculo salário liquido --}}
                                            @php
                                                $calculoSalárioLiquido = 0;
                                                $calculoSalárioLiquido = $tataolSubsidio + $salario_base - $totalDescontos - $getFunProcessoSalar[0]->valorReembolso;
                                                $totalSalario += $calculoSalárioLiquido;
                                            @endphp
                                            <td class="text-center" >
                                                <b>{{ number_format($calculoSalárioLiquido, 2, ',', '.') }}</b></td>
                                            @php $tataolSubsidio=0 @endphp
                                            @php
                                                $subsidoImpostoIRT = 0;
                                                $subsidoImpostoINSS = 0;
                                            @endphp
                                            {{-- fim --}}

                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach


                        </tbody>
                    </table>

                </div>
                <br><br><br>
                <div style="" class="mt-5 d-flex justify-content-between" >
              
                    <div class="assinaturas">
                        <p><b> O/A CHEFE DO DEPTO. DOS RECURSOS HUMANOS</b></p>
                        <p>______________________________________________</p>
                        @if ($institution->recursosHumano != '')
                        <p>{{ $institution->recursosHumano }}</p>
                        @else
                            <p>{{ $institution->recursos_humano }}</p>
                        @endif
                    </div>
                    <div class="assinaturas">
                        <b> O/A CHEFE DO DEPTO. ADMNIST. FINANÇAS</b>
                        <p>______________________________________________</p>
                       
                        @if ($institution->recursosHumano != '')
                        <p>{{ $institution->recursosHumano }}</p>
                        @else
                            <p>{{ $institution->recursos_humano }}</p>
                        @endif
                    </div>
                    <div class="assinaturas">
                        <b>O/A CHEFE DO DEPTO. ADMNIST. FINANÇAS</b>
                        <p>______________________________________________</p>
                    </div>
                    <div class="assinaturas">
                        <b> DIRECTOR GERAL</b>
                            <p>______________________________________________</p>
                            @if ($institution->directorGeral != '')
                            <p>{{ $institution->directorGeral }}</p>
                        @else
                            <p>{{ $institution->directorGeralName }}</p>
                        @endif
                    </div>
                    <div class="assinaturas">
                        <b> ADMINISTRADOR GERAL</b>
                        <p>______________________________________________</p>
                        <p>{{$institution->nome_dono}}</p>
                    </div>

              
                   
                   
                </div>
            </div>
        </div>
    </div>
</body>
