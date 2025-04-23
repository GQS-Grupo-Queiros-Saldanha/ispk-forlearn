@extends('layouts.print')
@section('content')
<style>
    .reciboGeral{
        width: 100%;
        height: auto;
        padding-right: 7px;
        font-size: 0.9em;
    }
    .text-doc-original{
        /* width: 100%; */
        float: right;
        position: relative;
        z-index: 999999;
        margin-top: -5.7pc;
        margin-right: 0.5pc;
        padding-right: 0px;
        padding-left: 0px;
        font-size: 0.9pc
    }
    .text-doc-data{
        float: right;
        position: relative;
        z-index: 999999;
        margin-top: -2.9pc;
        margin-right: 0.5pc;
        font-size: 0.9pc;
        padding: 0px
    }
</style>
<div class="m-0 p-0 row div-container">
    <div  class="reciboGeral ">
        @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')
        <p class="text-doc-original">QUADRO RESUMO DE VENCIMENTOS E IMPOSTOS</p><br>
        <p class="text-doc-data" >&nbsp;&nbsp;<small style="font-size: 0.9pc">DOCUMENTO GERADO A </small><strong style="font-size: 1pc" >{{$dataCreated}}</strong></p>

        <div class="col-sm-12 text-center">
            <p class="text-center" style="font-size: 1.3pc; font-weight: normal"><i>FOLHA DE SALÁRIO DOS FUNCIONÁRIOS:</i> <b> {{$dataPagamento}}</b></p>
        </div>
        <div class="m-0 p-0 mt-1">
            <style>

                .table-salario td{
                    border: black 1.4pt solid;
                }
                .table-salario{
                    width: 100%;
                    border-collapse: collapse;
                 
                }
                #cabecalho1 > th{
                    background: #c3d291;
                    border: #777e5e 1.4pt solid;
                    font-family: 'Times New Roman';
                    font-weight: normal;
                }
                #cabecalho2 > th{
                    border:#61644f 1.4pt solid;
                }
            </style>
            @php
                $vetorSub=[];
                $vetorImp=[];
                $qdtColumSB=0;
                $totalgeralIRT=0;
                $totalEmpregado=0;
                $totalEmpregador=0;
                $totalSalario=0;
            @endphp
            <div style="margin-bottom: 5pc" class="">
            
                <table class="table-salario">
                    {{-- cabeçalho dentro cabeçalho verifica-se quantos subsídios existem --}}
                    <thead>
                        <tr id="cabecalho1" style="font-size: 12pt">
                            <th class="text-center" style="font-size: 9pt;">Nº</th>
                            <th class="text-center"  style="font-size: 12pt;" >Nome</th>
                            <th class="text-center"  style="font-size: 11pt;" >Categoria</th>
                            <th class="text-center"  style="font-size: 11pt;" >Salário base</th>
                            {{-- verficar quantos subsídios existem --}}
                            @php $qdtColumSB=0; $qtdColum=0; @endphp
                            @foreach ($getFunProcessoSalario as $key => $getFunProcessoSl)
                                @foreach ($getFunProcessoSl as $id_processam_sl => $getFunProcessoSalari)
                                    @foreach ($getFunProcessoSalari as $name_funcionario => $getFunProcessoSalar)
                                        @foreach ($getFunProcessoSalar as $name_cargo => $item)
                                            @if ($item->id_subsidio!=null)
                                                @if (!in_array($item->id_subsidio,$vetorSub))
                                                    @php $vetorSub[]=$item->id_subsidio @endphp 
                                                    @php $qdtColumSB++;
                                                        $qtdColum=$qdtColumSB;
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                            <th class="text-center"  style="font-size: 12pt;" colspan="{{$qdtColumSB}}">Subsídios</th>
                            <th class="text-center"  style="font-size: 11pt;" >T. subsídios</th>
                            <th class="text-center"  style="font-size: 11pt;" >T. ilíquido</th>
                            <th class="text-center"  style="font-size: 11pt;" colspan="7">Descontos</th>
                            <th class="text-center"  style="font-size: 11pt;" >T. descontos</th>
                            <th class="text-center" style="font-size: 11pt;"  >Salário líquido</th>
                        </tr>
                    </thead>
                   
                    <tbody>
                        {{-- criar o sub cabeçalho dos impostos e subsídios  --}}
                        <tr id="cabecalho2"  style="">
                            <th class="text-center" style="font-size: 13pt;border: none"> </th>
                            <th class="text-center" style="font-size: 13pt;border: none"> </th>
                            <th class="text-center" style="font-size: 13pt;border: none"> </th>
                            <th class="text-center" style="font-size: 13pt;border: none"> </th>

                            {{-- listar os nome dos subsídios --}}
                            @php $semSubsidio=false; $vetorSub=[]; @endphp
                            @foreach ($getFunProcessoSalario as $key => $getFunProcessoSl)
                                @foreach ($getFunProcessoSl as $id_processam_sl => $getFunProcessoSalari)
                                    @foreach ($getFunProcessoSalari as $name_funcionario => $getFunProcessoSalar)
                                        @foreach ($getFunProcessoSalar as $name_cargo => $item)
                                            @if ($item->id_subsidio!=null)
                                                @if (!in_array($item->id_subsidio,$vetorSub))
                                                
                                                    @php $vetorSub[]=$item->id_subsidio;
                                                    $sub_name_subsidio=substr($item->name_subsidio, 0, 5);
                                                    $semSubsidio=true;
                                                    @endphp 
                                                    <th class="text-center" style="font-size: 10pt;background: #74c8e1;">{{$item->name_subsidio}}</th>
                                                @endif
                                            
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach

                            @if ($semSubsidio==false)
                                <th class="text-center" style="font-size: 13pt; border: none"></th>
                            @endif
                            <th class="text-center" style="font-size: 13pt; border: none"></th>
                            <th class="text-center" style="font-size: 13pt; border: none"></th>
                            
                            {{-- listar nome dos impostos  --}}
                            @foreach ($getFunProcessoImposto as $item)
                                @if (!in_array($item->id_imposto,$vetorImp))
                                    @php $vetorImp[]=$item->id_imposto @endphp
                                    @if ($item->nome_code=="inss")
                                        <th class="text-center" style="font-size: 13pt;background: #74c8e1;" colspan="2">{{$item->name_imposto}}</th>
                                    @elseif($item->nome_code=="irt")
                                        <th class="text-center" style="font-size: 13pt;background: #74c8e1;" colspan="4">{{$item->name_imposto}}</th>
                                    @endif
                                @endif
                            @endforeach
                            <th class="text-center" style="font-size: 13pt;background: #74c8e1;">Faltas</th>
                            <th class="text-center" style="font-size: 13pt; border: none"></th>
                            <th class="text-center" style="font-size: 13pt; border: none"></th>
                        </tr>

                        {{-- criar o sub cabeçalho dos impostos e listar as suas percentagens  --}}
                            <tr>
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>
                                @for ($i = 1; $i <=$qdtColumSB; $i++)
                                    <td class="text-center" style="border: none"></td>
                                @endfor

                                @if ( $semSubsidio==false)
                                 <th class="text-center" style="font-size: 13pt; border: none"></th>
                                @endif
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>

                                @php $qtdColunaImp=0;  $vetorImpotosYear=[] @endphp
                                @foreach ($gethistoricProcessoImposto as $element)
                                    @if (!in_array($element->id_impostoYear,$vetorImpotosYear))
                                        @php $vetorImpotosYear[]=$element->id_impostoYear @endphp
                                        @foreach ($getFunProcessoImposto as $chave => $item)
                                            @if ($element->id_impostoYear==$item->id_impostYear && $element->id_impost==$item->id_imposto  && $item->nome_code=="inss")
                                                @php $qtdColunaImp=$chave;  @endphp
                                                <td class="text-center">&nbsp; {{$item->taxa}}% &nbsp;</td>
                                            @endif  
                                        @endforeach
                                    @endif
                                @endforeach

                               
                                <td class="text-center">PF</td>
                                <td class="text-center" colspan="2">%Taxa</td>
                                <td class="text-center" >&nbsp;Total IRT&nbsp;</td>
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>
                                <td class="text-center" style="border: none"></td>
                            </tr>
                        {{-- fim --}}
                        

                        {{-- listar os dados na tabela tbody  --}}
                            
                            @php  $subsidoImpostoIRT=0; $subsidoImpostoINSS=0 @endphp
                            @php $pesquisarImposto=false; $qtd_falta=0; $id_processoSalario; $i=0;$idProcefunSalario=[] ;$qdtColumSB=$qdtColumSB-1; $coluSub=0; $salario_base=0; $tataolSubsidio=0; @endphp
                            @foreach ($getFunProcessoSalario as  $id_processam_sl => $getFunProcessoSl)
                                @php  $pesquisarImposto=false; $qtd_falta=0; $i++; $coluSub=0; $id_processoSalario=$id_processam_sl @endphp
                                @foreach ($getFunProcessoSl as $name_funcionario => $getFunProcessoSalari)
                                    @foreach ($getFunProcessoSalari as $name_cargo => $getFunProcessoSalar)

                                        {{-- subsidio com imposto --}}
                                        @foreach ($getFunProcessoSalar as $item)
                                            @if ($item->id_subsidio!=null)
                                                @foreach ($gethistoricProcessoImposto as $key=> $getImpoto)
                                                    @if ($getImpoto->id_processamento==$id_processam_sl)
                                                        @foreach ($getSubsidioImposto as $valueElement)
                                                            @if ($valueElement->imposto_id==$getImpoto->id_impost && $valueElement->subsidio_id==$item->id_subsidio && $getImpoto->nome_code=="inss")
                                                                @php
                                                                    $subsidoImpostoINSS+=$item->valor_subsidio;
                                                                @endphp
                                                            @elseif($valueElement->imposto_id==$getImpoto->id_impost && $valueElement->subsidio_id==$item->id_subsidio && $getImpoto->nome_code=="irt")
                                                                @php
                                                                    $subsidoImpostoIRT+=$item->valor_subsidio  
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endforeach 
                                            @endif
                                        @endforeach
                                        <tr class="" style="background: #f0f2f5">
                                            {{-- inicio listar os dados princípais como nome, cargo, salario --}}
                                                <td class="text-center" style="font-size: 12pt;">{{$i}}</td>
                                                <td class="pl-2" style="font-size: 10pt;">{{$name_funcionario}}</td>
                                                <td class="pl-2" style="font-size: 10pt;">{{$name_cargo}}</td>
                                                <td class="text-center" style="font-size: 11pt;"><b>{{number_format($getFunProcessoSalar[0]->salarioBase, 1, ',', '.') }}</b></td>
                                                @php
                                                    $salario_base=$getFunProcessoSalar[0]->salarioBase;
                                                @endphp
                                            {{-- fim --}}

                                            {{-- inicio da criação para listar o valor do subsídio do funcionario  --}}
                                                @foreach ($getFunProcessoSalar as $key => $item)
                                                    @php $qtd_falta=$item->valor_falta;  $coluSub=$key;  $tataolSubsidio+=$item->valor_subsidio @endphp
                                                    <td class="text-center" style="font-size: 10pt;">{{number_format($item->valor_subsidio, 0, ',', '.') }}</td>    
                                                @endforeach
                                                {{-- logica que permite que as outras colunas sejam criadas, quando o funcionario não tem subsídio --}}
                                                @for ($a = 1; $a <= $qdtColumSB; $a++)
                                                    @php $coluSub++ @endphp
                                                    @if ($coluSub<=$qdtColumSB)
                                                        <td class="text-center" style="font-size: 10pt;">{{number_format(0, 0, ',', '.') }}</td>    
                                                    @endif
                                                @endfor
                                            
                                                <td class="text-center" style="font-size: 10pt;"><b> {{number_format($tataolSubsidio, 0, ',', '.') }}</b> </td>
                                                <td class="text-center" style="font-size: 11pt;"><b> {{number_format($tataolSubsidio +$salario_base, 1, ',', '.') }}</b></td>

                                            {{-- fim --}}

                                            {{-- calculo do imposto INS e sobre as suas percentagens -----......... --}}
                                                @php  $insTaxa=null; $vetorImpotosYear=[];$resultadoINS=0; $valorINS_fun=null;@endphp
                                                @foreach ($gethistoricProcessoImposto as $element)
                                                    @if ($element->id_processamento == $id_processam_sl)
                                                    @php  $pesquisarImposto=true; @endphp
                                                        @if (!in_array($element->id_impostoYear,$vetorImpotosYear))
                                                            @php $vetorImpotosYear[]=$element->id_impostoYear @endphp
                                                            @foreach ($getFunProcessoImposto as $item)
                                                                @if ($element->id_impostoYear==$item->id_impostYear && $element->id_impost==$item->id_imposto && $item->nome_code=="inss")
                                                                        @php
                                                                            if($tataolSubsidio > 0){
                                                                                $resultadoINS= ($subsidoImpostoINSS + $salario_base + $tataolSubsidio) * ($item->taxa / 100);
                                                                            }else{
                                                                                $resultadoINS= ($subsidoImpostoINSS + $salario_base) * ($item->taxa / 100);
                                                                            }
                                                                        @endphp
                                                                        @if ($valorINS_fun==null || $resultadoINS<$valorINS_fun)
                                                                            @php 
                                                                                $valorINS_fun=$resultadoINS; 
                                                                            @endphp
                                                                        @else
                                                                            @php $totalEmpregador+=$resultadoINS;@endphp
                                                                        @endif
                                                                        <td class="text-center">&nbsp;{{number_format($resultadoINS, 0, ',', '.') }}&nbsp;</td>
                                                                @endif  
                                                            @endforeach
                                                        @endif 
                                                    @endif
                                                @endforeach

                                                @if ($pesquisarImposto==false)
                                                    @for ($a = 0; $a <= $qtdColunaImp; $a++)
                                                        <td class="text-center">{{number_format(0, 0, ',', '.') }}</td>  
                                                        @php $i++ @endphp
                                                    @endfor
                                                @endif
                                            {{-- fim --}}

                                            {{-- calculo do imposto IRT e sobre as suas percentagens e parcelea fixa   --}}
                                                @php $variavel=0; $pesquisaTaxa=false;  $vetorImpotosYear=[];$resultadoIRT=0; $mc=0; $excesso=0; $totalExec=0; @endphp
                                                @foreach ($getFunProcessoSalario as $id_processo => $item)
                                                    @foreach ($gethistoricProcessoImposto as $element)
                                                        @if (!in_array($element->id_processamento,$vetorImpotosYear) && $id_processo==$id_processoSalario && $element->id_processamento==$id_processoSalario)
                                                            @php  $vetorImpotosYear[]=$element->id_processamento; @endphp
                                                            @foreach ($getFunProcessoImposto as $key => $value)
                                                                @if ($pesquisaTaxa==false && $value->nome_code=="irt" && ($subsidoImpostoIRT+$salario_base)>=$value->valor_inicial  && ($subsidoImpostoIRT+$salario_base)<=$value->valor_final)
                                                                    @php  $pesquisaTaxa=true; 
                                                                        $mc = ($subsidoImpostoIRT + $salario_base ) - $valorINS_fun;
                                                                        $excesso = $value->excesso;
                                                                        $totalExec = ($mc + $tataolSubsidio) - $excesso; 
                                                                        $variavel=($totalExec) *($value->taxa / 100);
                                                                        $resultadoIRT = $variavel + $value->parcela_fixa;
                                                                        $totalgeralIRT+=$resultadoIRT;
                                                                        $totalEmpregado+=$valorINS_fun;
                                                                        // echo "mc=(".($subsidoImpostoIRT+$tataolSubsidio)."+".$salario_base.")-".$valorINS_fun."<br>".$value->parcela_fixa."+(".($mc+$tataolSubsidio)."-".$excesso.")*".$value->taxa."/100 <br>";
                                                                    @endphp
                                                                    <td class="text-center">&nbsp;{{number_format($value->parcela_fixa, 0, ',', '.') }}&nbsp;</td>
                                                                    <td class="text-center">&nbsp;{{$value->taxa}}%&nbsp;</td>
                                                                    <td class="text-center">&nbsp;{{number_format($variavel, 0, ',', '.') }}&nbsp;</td>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @if($pesquisarImposto==true)
                                                    <td class="text-center">&nbsp;{{number_format($resultadoIRT, 1, ',', '.') }}&nbsp;</td> 
                                                @endif
                                                @if($pesquisarImposto==false)
                                                    <td class="text-center">&nbsp;0 & 0%&nbsp;</td> 
                                                    <td class="text-center">{{number_format(0, 0, ',', '.') }}</td>  
                                                @endif
                                            {{-- fim --}}
                                                
                                            <td class="text-center"> {{number_format($qtd_falta, 1, ',', '.') }}</td> {{-- valor da falta  --}}
                                            
                                            {{-- Calcular total descontos --}}
                                                @php $totalDescontos=0; @endphp
                                                {{-- @if ($resultadoIRT!=0) --}}
                                                    @php  $totalDescontos= $valorINS_fun + $resultadoIRT + $qtd_falta @endphp
                                                {{-- @endif --}}
                                                <td class="text-center" style="font-size: 10pt;"><b>{{number_format($totalDescontos, 2, ',', '.') }}</b></td>
                                            {{-- fim --}}

                                            {{-- calculo salário liquido --}}
                                                @php
                                                    $calculoSalárioLiquido=0;
                                                    $calculoSalárioLiquido= ($tataolSubsidio +  $salario_base) - $totalDescontos;
                                                    $totalSalario+=$calculoSalárioLiquido;
                                                @endphp
                                                <td class="text-center" style="font-size: 10pt;"><b>{{number_format($calculoSalárioLiquido, 2, ',', '.') }}</b></td>
                                                @php $tataolSubsidio=0 @endphp
                                                @php  $subsidoImpostoIRT=0; $subsidoImpostoINSS=0 @endphp
                                            {{-- fim --}}

                                        </tr> 
                                    @endforeach
                                @endforeach
                            @endforeach
                        {{-- fim --}}

                        {{-- total geral --}}
                            {{-- <tr>
                                <td class="pb-2 pt-1" style="border: none"></td>
                            </tr> --}}

                       
                            <tr >
                                <td class="text-center"  style="border: none"></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                @for ($a = 1; $a <= $qtdColum; $a++)
                                    @if ($a<=$qtdColum)
                                        <td class="text-center" style="border: none"></td>    
                                    @endif
                                @endfor
                                @if ($qtdColum==0)
                                    <td class="text-center" style="border: none"></td>    
                                @endif
                                @php
                                    $otalEmpregadores=$totalEmpregado+$totalEmpregador;
                                @endphp
                               
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                
                                <td class="text-center" style="font-size: 10.4pt;background: #74c8e1;" > <b>{{number_format($totalEmpregado, 2, ',', '.') }} </b> </td>
                                <td class="text-center" style="font-size: 10.4pt;background: #74c8e1;" > <b>{{number_format($totalEmpregador, 2, ',', '.') }} </b> </td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center" style="font-size: 10.4pt;background: #74c8e1;" > <b>{{number_format($totalgeralIRT, 2, ',', '.') }}</b></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center"  style="border: none" ></td>
                                <td class="text-center" style="font-size: 10.4pt;background: #74c8e1;" > <b>{{number_format($totalSalario, 2, ',', '.') }}</b></td>
                            </tr>
                            
                        {{-- fim total  --}}

                        <tr >
                            <td class="text-center"  style="border: none"></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            @for ($a = 1; $a <= $qtdColum; $a++)
                                @if ($a<=$qtdColum)
                                    <td class="text-center" style="border: none"></td>    
                                @endif
                            @endfor
                            @if ($qtdColum==0)
                                <td class="text-center" style="border: none"></td>    
                            @endif
                            @php
                                $otalEmpregadores=$totalEmpregado+$totalEmpregador;
                            @endphp
                           
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td  style="border:  black 1.4pt solid;font-size: 11.4pt;background: #f0f2f5;" class="text-center" colspan="2"> <b> {{number_format($otalEmpregadores, 2, ',', '.') }}</b><small>Kz</small></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                            <td class="text-center"  style="border: none" ></td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <div style="" class="mt-5 d-flex justify-content-between">
                <div style="padding-left: 1pc; padding-right: 2pc" class="col text-center">
                   <div style="border-bottom: black 0.1pc solid;font-size:8pt" class="pb-5 pr-1 pl-1 text-center">
                      <b>O/A CHEFE DO  DEPTO. DOS RECURSOS HUMANOS</b>
                   </div>
                    @if ($institution->recursosHumano!="")
                        <p>{{$institution->recursosHumano}}</p>
                    @else
                        <p>{{$institution->recursos_humano}}</p> 
                    @endif
                </div>
                <div style="padding-left: 2pc; padding-right: 2pc" class="col text-center">
                    <div style="border-bottom: black 0.1pc solid;font-size:8pt" class="pb-5 pr-1 pl-1 text-center">
                        <b>O/A CHEFE DO  DEPTO. ADMNIST. FINANÇAS</b>
                    </div>
                 </div>
                <div style="padding-left: 3pc; padding-right: 3pc"  class="col  text-center">
                    <div class="pb-5 pr-1 pl-1" style="border-bottom: black 0.1pc solid;font-size:8pt"><b>O DIRECTOR GERAL</b></div>
                    @if ($institution->directorGeral!="")
                        <p>{{$institution->directorGeral}}</p>
                    @else
                        <p>{{$institution->directorGeralName}}</p> 
                    @endif
                </div>
                <div style="padding-left: 2pc; padding-right: 1pc"  class="col  text-center">
                    <div class="pb-5 pr-1 pl-1" style="border-bottom: black 0.1pc solid;font-size:8pt"><b>ADMINISTRADOR GERAL</b></div>
                    <p>{{$institution->nome_dono}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection