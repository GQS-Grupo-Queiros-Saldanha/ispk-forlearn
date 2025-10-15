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
        padding: 0px;
        padding-left: 6pc; 
        margin-left: 5pc;
        margin-top: 18px;
        margin-bottom: 25px;
        text-align: center;
        font-size: 1.2pc;
    }
    .text-doc-data{
        float: right;
        position: relative;
        z-index: 999999;
        margin-top: -1.6pc;
        margin-right: 0pc;
        font-size: 0.9pc
    }
</style>
<div class="m-0 p-0 row div-container">
    <div  class="reciboGeral">
        @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')
        <p class="text-doc-data" >&nbsp;&nbsp;<small style="font-size: 0.9pc">DOCUMENTO GERADO A </small><strong style="font-size: 1pc" >{{$dataCreated}}</strong></p>
        <div style="display: flex;justify-content: center; align-items: center;" class="col-md-12 text-center">
            <p class="text-doc-original "><i> FOLHA DE SALÁRIO DOS FUNCIONÁRIOS: <b> {{$dataPagamentobanco}} </b></i></p>
        </div>
        
        <div class="m-0 p-0 mt-1">
            <style>
                .table-salario td{
                    border: 0.8px solid;
                }
                .table-salario th{
                    border: 1px solid;
                }
                .table-salario{
                    width: 100%;
                    border-collapse: collapse;
                 
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
            <div>
                <table class="table-salario">
                    {{-- cabeçalho dentro cabeçalho verifica-se quantos subsídios existem --}}
                    <thead>
                        <tr style="background: rgb(248, 248, 248);font-size: 12pt">
                            <th class="text-center" style="border-bottom:none;" >Nº</th>
                            <th class="text-center"  style="font-size: 13pt;border-bottom:none;" >Nome</th>
                            <th class="text-center"  style="font-size: 13pt;border-bottom:none;" >Categoria</th>
                            <th class="text-center"  style="font-size: 11pt;" >Conta bancária</th>
                            {{-- verficar quantos subsídios existem --}}
                            @php $qdtColumSB=0; $qtdColum=0; $nome_banco=""; @endphp
                            @foreach ($getFunProcessoSalario as $key => $getFunProcessoSl)
                                @foreach ($getFunProcessoSl as $id_processam_sl => $getFunProcessoSalari)
                                    @foreach ($getFunProcessoSalari as $name_funcionario => $getFunProcessoSalar)
                                        @foreach ($getFunProcessoSalar as $name_cargo => $item)
                                        @php $nome_banco=$getFunProcessoSalar[0]->nome_banco @endphp
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
                            <th class="text-center" style="font-size: 13pt;border-bottom:none;"  >Salário líquido</th>
                        </tr>
                    </thead>
                   
                    <tbody>
                        <tr style="background: rgb(248, 248, 248)">
                            <th class="text-center"style="border-top:none;" ></th>
                            <th class="text-center"  style="border-top:none; " ></th>
                            <th class="text-center"  style="border-top:none; " ></th>
                            <th class="text-center"  style="border-top:none; " >{{$nome_banco}} <i style="font-weight: normal">( IBAN )</i></th>
                            <th class="text-center" style="border-top:none; "  ></th>
                        </tr>
                        <tr>
                        

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

                                        <tr>
                                            {{-- inicio listar os dados princípais como nome, cargo, salario --}}
                                                <td class="text-center" style="font-size: 12pt;">{{$i}}</td>
                                                <td class="pl-2" style="font-size: 13pt;">{{$name_funcionario}}</td>
                                                <td class=" text-center pl-2" style="font-size: 13pt;">{{$name_cargo}}</td>
                                                @php
                                                    $salario_base=$getFunProcessoSalar[0]->salarioBase;
                                                @endphp
                                            {{-- fim --}}
                                            @foreach ($getFunProcessoSalar as $key => $item)
                                                    @php $qtd_falta=$item->valor_falta;  $tataolSubsidio+=$item->valor_subsidio @endphp  
                                            @endforeach

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
                                                                            $resultadoINS= ($subsidoImpostoINSS + $salario_base) * ($item->taxa / 100)
                                                                        @endphp
                                                                        @if ($valorINS_fun==null || $resultadoINS<$valorINS_fun)
                                                                            @php 
                                                                                $valorINS_fun=$resultadoINS; 
                                                                            @endphp
                                                                        @else
                                                                            @php $totalEmpregador+=$resultadoINS;@endphp
                                                                        @endif
                                                                @endif  
                                                            @endforeach
                                                        @endif 
                                                    @endif
                                                @endforeach
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
                                                                        $totalExec = $mc - $excesso; 
                                                                        $resultadoIRT = $totalExec * ($value->taxa / 100) + $value->parcela_fixa;
                                                                        $variavel=($tataolSubsidio + $salario_base ) *($value->taxa / 100);
                                                                        $totalgeralIRT+=$resultadoIRT;
                                                                        $totalEmpregado+=$valorINS_fun;
                                                                    @endphp
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            {{-- fim --}}
                                            
                                            {{-- Calcular total descontos --}}
                                                @php $totalDescontos=0; @endphp
                                                {{-- @if ($resultadoIRT!=0) --}}
                                                    @php  $totalDescontos= $valorINS_fun + $resultadoIRT + $qtd_falta @endphp
                                                {{-- @endif --}}
                                            {{-- fim --}}

                                            {{-- calculo salário liquido --}}
                                                @php
                                                    $calculoSalárioLiquido=0;
                                                    $calculoSalárioLiquido= ($tataolSubsidio +  $salario_base) - $totalDescontos;
                                                    $totalSalario+=$calculoSalárioLiquido;
                                                    // tatalSalariopago
                                                @endphp
                                                @php $tataolSubsidio=0 @endphp
                                                @php  $subsidoImpostoIRT=0; $subsidoImpostoINSS=0 @endphp
                                            {{-- fim --}}
                                            <td class="text-center" style="font-size: 12pt;" >{{$getFunProcessoSalar[0]->iban_banco}}</td>
                                            <td class="text-center" style="font-size: 12pt;"> <b> {{number_format($calculoSalárioLiquido, 2, ',', '.') }}</b> <small>Kz</small> </td>
                                        </tr>
                                        
                                    @endforeach
                                @endforeach
                            @endforeach
                            <tr style="background: rgb(248, 248, 248)">
                                <td style="border-right:none;"></td>
                                <td  class="text-right" style="font-size: 13pt; border-right:none;border-left:none;">TOTAL</td>
                                <td style="border-right:none; border-left:none;"></td>
                                <td style="border-right:none; border-left:none;"></td>
                                <td class="text-center" style="font-weight: bold; font-size: 14pt">{{number_format($totalSalario, 2, ',', '.') }} <small>Kz</small></td>
                            </tr> 
                        {{-- fim --}}

                    </tbody>
                </table>
            </div>
            <div style="padding-left: 3pc; padding-right: 3pc" class="mt-5 d-flex justify-content-between">
                <div class="pr-3 pl-3 text-center">
                   <div style="border-bottom: black 1px solid;font-size:11pt" class="pb-5 pr-3 pl-3 text-center">
                        DEPTO DE RECURSOS HUMANOS
                   </div>
                   @if ($institution->directorGeral!="")
                        <p>{{$institution->directorGeral}}</p>
                   @else
                        <p>{{$institution->directorGeralName}}</p> 
                   @endif
                </div>
                <div  class="pr-3 pl-3 text-center">
                    <div class="pb-5 pr-3 pl-3" style="border-bottom: black 1px solid;font-size:11pt">ADMINISTRADOR GERAL</div>
                    <p>{{$institution->nome_dono}}</p>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection