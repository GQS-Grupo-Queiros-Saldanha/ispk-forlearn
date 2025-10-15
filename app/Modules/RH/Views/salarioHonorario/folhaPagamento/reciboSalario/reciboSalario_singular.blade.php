<style>
    .container-user{
        border-bottom: 1.8px solid;
        width: 100%;
        margin: 0px;
        padding: 0px;
        padding-top: 15px;
        
    }
    .rodepe-assinatura{
        margin: 5px;
        padding: 0px;
        padding-top: 15px;
        margin-bottom: 10px;
    }
    .assinatura{
        border-bottom: 1px solid;
        font-size: 0.9pc;
    }
    .observacao{
        margin-left: 6pc;
        font-size: 0.9pc;
    }
    .text-doc-original{
        float: right;
        position: relative;
        z-index: 999999;
        margin-top: -5.8pc;
        margin-right: 6pc;
        padding-right: 0px;
        font-size: 0.9pc;
    }
    .recibo-rodape{
        /* position: absolute; */
        font-size: 0.8pc;
        padding: 15px;
        width: 100%;
	      bottom: 0
    }
</style>
@foreach ($getFunProcessoSalario as $id_processam_sl => $get_id_processam_sl)
  @foreach ($get_id_processam_sl as $name_funcionario => $get_name_funcionario)
    @foreach ($get_name_funcionario as $name_cargo => $itget_name_cargo)
      @foreach ($itget_name_cargo as $bi => $itget_name_bi)
        @foreach ($itget_name_bi as $seguranca => $itget_name_seguranca)
          @foreach ($itget_name_seguranca as $nif => $item)

            <div style="page-break-before: always;" class="">
                @include('RH::salarioHonorario.folhaPagamento.reciboSalario.pdf_header')
                {{-- <p class="text-doc-original">Original{{$key}}</p> --}}
                <p class="text-doc-original" ></p>

                {{-- div para informar os dados do funcionario --}}
                <div class="container-user  mt-1 mb-0 row col-md-12" style="width: 100%;">
                    <div class="bg2" style="width: 100%;">
                      <p style="font-size: 1pc;" class="m-0 ml-3 p-0" >Nome: <strong style="font-size: 1.1pc">{{$name_funcionario}}</strong>
                       <br>Categoria: <strong style="font-size: 1.1pc">{{$name_cargo}}</strong>
                        </p>
                    {{-- </div>
                    <div class="ml-4"> --}}

                        {{-- <p style="font-size: 1pc;" class="m-0  p-0" class="bg2"></p> --}}
                    </div>
                </div>

                {{-- div para informar os dados do funcionario sobre contrato--}}
                <div class="ml-3 mb-3">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Referente a: {{$item[0]->year_month}}</th>
                                <th>Vencimento:<b style="font-weight: bold; font-size: 1pc">{{number_format($item[0]->salarioBase, 2, ',', '.') }}</b> <small>Kz</small></th>
                               <th>N.º BI.: {{$bi}}</th>
                              
                            </tr>
                            <tr>
                                   <th>Faltas: <small>{{$item[0]->qtd_falta}} Horas - </small> (<strong>{{number_format($item[0]->valor_falta, 2, ',', '.') }} kz</strong>)</th>
                                   <th>N.º Benef:<b style="font-weight: bold; font-size: 1pc">{{$seguranca}}</b> <small></small></th>
                                   <th>N.º Contrib.: <small>{{$nif}} </small> </th>
                                
                            </tr>
                            
                            <tr style="opacity: 1;">
                                
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- div que agrupa os dados sobre o salario do funcionario --}}
                <div class="row ml-3 mb-3" style="padding-bottom: 7pc">
                    <div class="pr-3" style="width: 100%; font-size: 1pc;">
                        <table style="width: 100%" class="table table-striped table-sm">  
                          <thead>
                              <tr class="bg2">
                                <th scope="col">Cód</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Remuneração</th>
                                <th scope="col">Descontos</th>
                              </tr>
                            </thead>
                            <tbody>

                            {{-- salário base --}}
                              @php $salarioBase=$item[0]->salarioBase; $totalVencimento=$item[0]->salarioBase;  @endphp
                              <tr class="table-white" >
                                <td scope="row">R01</td>
                                <td scope="row">Salário base</td>
                                <td>{{number_format($item[0]->salarioBase, 2, ',', '.') }}</td>
                                <td> </td>
                              </tr>

                              {{-- subsidio de trabalho.  getSubsidioImposto--}}
                              @php  $subsidoImpostoIRT=0; $subsidoImpostoINSS=0 @endphp
                              @foreach ($item as $key=> $element)
                                @php $key=$key+1;  $totalVencimento+=$element->valor_subsidio;@endphp
                                <tr>
                                  <td scope="row">S0{{$key}}</td>
                                  <td scope="row"> {{$element->name_subsidio}}</td>
                                  <td>{{number_format($element->valor_subsidio, 2, ',', '.') }}</td>
                                  <td></td>
                                </tr>
                                    @foreach ($gethistoricProcessoImposto as $key=> $getImpoto)
                                        @if ($getImpoto->id_processamento==$id_processam_sl)
                                            @foreach ($getSubsidioImposto as $valueElement)
                                                @if ($valueElement->imposto_id==$getImpoto->id_impost && $valueElement->subsidio_id==$element->id_subsidio && $getImpoto->nome_code=="inss")
                                                    @php
                                                        $subsidoImpostoINSS+=$element->valor_subsidio;
                                                    @endphp
                                                @elseif($valueElement->imposto_id==$getImpoto->id_impost && $valueElement->subsidio_id==$element->id_subsidio && $getImpoto->nome_code=="irt")
                                                    @php
                                                         $subsidoImpostoIRT+=$element->valor_subsidio  
                                                    @endphp
                                                @endif

                                            @endforeach
                                        @endif
                                    @endforeach
                              @endforeach
                            

                              @php
                                  $resultadoINSS=0;
                                  $resultadoIRT=0;
                                  $percentaINSS=null;
                                  $percentaIRT=null;
                                  $mc=0;
                                  $totalEx=0;
                                  $totalDesconto=0;
                                  $i=1;
                              @endphp

                              <tr>
                                <td scope="row">D01</td>
                                <td scope="row">Reembolso</td>
                                <td></td>
                                <td>{{number_format($item[0]->valorReembolso, 2, ',', '.') }}</td>
                              </tr>
                              
                              @foreach ($gethistoricProcessoImposto as $key=> $getImpoto)
                                @if ($getImpoto->id_processamento==$id_processam_sl)
                                  @php $i++;@endphp
                                  <tr>
                                    <td scope="row">D0{{$i}}</td>
                                    @if ($getImpoto->nome_code=="inss")
                                      @foreach ($getFunProcessoImposto as $getINSS)
                                           @if ($getINSS->nome_code=="inss")
                                                @if ($percentaINSS==null || $percentaINSS>$getINSS->taxa)
                                                    @php $percentaINSS=  $getINSS->taxa @endphp
                                                @endif
                                            
                                           @endif
                                      @endforeach
                                     
                                      <td scope="row">{{$getImpoto->name_imposto}} ({{$percentaINSS}}%)</td>
                                      <td></td>
                                       @php $resultadoINSS= ($subsidoImpostoINSS + $salarioBase + $element->valor_subsidio) * ($percentaINSS / 100);
                                       @endphp
                                      <td>{{number_format($resultadoINSS, 2, ',', '.') }}</td>
                                    @endif
                                    @if($getImpoto->nome_code=="irt")
                                      @foreach ($getFunProcessoImposto as $getIRT)
                                            @if ($getIRT->nome_code=="irt" && ($subsidoImpostoIRT + $salarioBase)>=$getIRT->valor_inicial  && ($subsidoImpostoIRT + $salarioBase)<=$getIRT->valor_final)
                                              @php
                                                $percentaIRT= $getIRT->taxa;
                                                $mc= ($subsidoImpostoIRT + $salarioBase + $element->valor_subsidio) - $resultadoINSS;
                                                $totalEx=$mc - $getIRT->excesso;
                                                $resultadoIRT=$totalEx * ($percentaIRT / 100) + $getIRT->parcela_fixa;
                                              @endphp
                                            @endif
                                      @endforeach
                                      <td scope="row">{{$getImpoto->name_imposto}} ({{$percentaIRT}}%)</td>
                                      <td></td>
                                      <td>{{number_format($resultadoIRT, 2, ',', '.') }}</td>
                                    @endif
                                    
                                  </tr>
                                 @endif
                              @endforeach

                              @php 
                                  $falta=$item[0]->valor_falta;
                                  $totalDesconto= $falta + $resultadoIRT + $resultadoINSS + $item[0]->valorReembolso;
                              @endphp
                              <tr>
                                <td scope="row">F01</td>
                                <td scope="row">Faltas</td>
                                <td></td>
                                <td>{{number_format($item[0]->valor_falta, 2, ',', '.') }}</td>
                              </tr>
                              
                              
                            </tbody>
                          </table>
                    </div>
                </div>

                {{-- div que agrupa a remunerações --}}
                <div class="ml-3 mb-5 mt-2" style="font-size: 1pc; padding-bottom: 0.8pc">
                    <table class="table table-striped table-sm">
                        <thead class="border-0">
                          <tr style="border-top:none" class="bg1">
                            <th style="visibility: hidden" scope="col" colspan="2"> </th>
                            <th style="visibility: hidden" scope="col" > 21rewr</th>
                            <th style="visibility: hidden" scope="col" > 212rew</th>
                            <td scope="col text-center"><strong>Total</strong> {{number_format($totalVencimento, 2, ',', '.') }}  <small>Kz</small> </td>
                            <td scope="col"><strong>Total Desconto:</strong> {{number_format($totalDesconto, 2, ',', '.') }} <small>Kz</small></td>
                          </tr><br>
                        </thead>
                        <thead>
                            <tr>
                                <td scope="row" colspan="2">Forma de Pagamento</td>                   
                                <td scope="row"> </td>                   
                                <th scope="row"> </th>                   
                                <th scope="row"> </th>                   
                                <th scope="row"> </th>                   
                            </tr>
                        </thead>
                        <tbody>
                          @php
                              $totalPago=$totalVencimento - $totalDesconto;
                          @endphp
                          <tr class="bg2">
                            <td scope="row">100,00%</td>
                            <td>Transferência</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td>Total pago: <b style="font-weight: bold;font-size: 1pc">{{number_format($totalPago, 2, ',', '.') }}</b> <small>Kz</small></td>
                          </tr>
                        </tbody>
                      </table>
                </div>

                {{-- assinatura do funcionario --}}
                <div class="ml-3 mb-5 mt-1 pb-5 rodepe-assinatura row">
                    <div class="assinatura">
                      <p class="pb-3">Declaro que recebi a quantia constante neste dia</p> 
                    </div>
                    <div class="observacao">
                        <p><strong>Obs.: {{$item[0]->nota}}</strong></p> 
                    </div>
                </div>

                {{-- cabeçalho --}}
                <div class="recibo-rodape">
                  @include('Reports::pdf_model.pdf_footer')
                </div>
            </div>
          @endforeach
        @endforeach
      @endforeach
    @endforeach
  @endforeach
@endforeach