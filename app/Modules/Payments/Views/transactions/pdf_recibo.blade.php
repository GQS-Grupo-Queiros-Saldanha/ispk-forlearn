@extends('layouts.print')
@section('content')

    <style>
      
        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 3px 5px !important;
            font-size:.625rem;
            width: 180px;

        }
      
 
        .td-parameter-column {
            padding-left: 5px !important;
            margin-bottom:4px;
        }
        
        .pl-1 {
            padding-left: 1rem !important;
        }

        .header-user {
            padding: 0 !important;
            text-align: left !important;
        }
         td{
             background-color:rgb(240, 240, 240);
             /* margin-bottom: 2px; */
             border-left: 2px solid white;
           }

        
         .titulos_emulumentos{
            margin-bottom: 2pc;
            color: white;
            width: 290px;
            padding:1px;
            text-align: left;
         }
    </style>

    @php $now = \Carbon\Carbon::now(); @endphp
    @php $multa_total = 0 ; @endphp
    @php $saldo_usado = 0 ;  @endphp
          @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'INSTITUIÇÃO DE ENSINO';
        $discipline_code = '';
    @endphp
    @include('Reports::pdf_model.header_recibo')
    <main>
       
        @php($user = isset($model->id) ? $model : $transaction->article_request->first()->user)
        @php ($totalEmolumento=0)
        @php ($totalTransSaoldo=0) 
        @php ($totalSaldoCarteira=0)
        <table >
            <thead class="">
            <th class="titulos_emulumentos bg1">IDENTIFICAÇÃO NA INSTITUIÇÃO</th>
            </thead>
            <table width="100%" style="margin-top: 6px">
                <thead style="margin-top: 10px">
                    <th class=" header-user bg2">Matrícula </th>
                    <th class=" header-user bg2">Nome </th>
                    <th class=" header-user bg2">e-mail</th>
                    <th class=" header-user bg2">Curso</th>
                    <th class=" header-user bg2">Turma(s)</th>
                </thead>
                <tbody>
                    <tr>
                        @php($nMecanografico = $user->parameters->first() ? $user->parameters->first()->pivot->value : 'N/A')
                        @php($full_nameStudent= $user->user_parameters->first()->value)
                        <td style="font-size: 11px">{{ $nMecanografico }}</td>
                        <td style="font-size: 11px">{{$full_nameStudent}}</td>
                        <?php
                            $turma = $user->matriculation ?
                                $user->matriculation->classes->pluck('display_name')->implode(', ') :
                                null;
                            $turmaInUser = $user->classes->first() ? $user->classes->first()->display_name : null;
                            $turma = $turma ?: $turmaInUser;
                            $turma = $turma ?: 'N/A';
                             if ($matricula_finalista==true) {
                                $turma='N/A';
                            }
                        ?>
                        <td style="font-size: 11px">{{$user->email}}</td>
                        @php($curso = $user->courses->first() ? $user->courses->first()->currentTranslation->display_name : 'N/A')
                        @if(count($disciplines)==1 && $disciplines[0]->perfil_disciplina==8) 
                        <td style="font-size: 11px">{{ $disciplines[0]->course_name }}</td>
                        @else 
                        <td style="font-size: 11px">{{ $curso }}</td>
                        @endif
                        <?php
                        $sala = $user->matriculation ?
                            $user->matriculation->classes->pluck('room.currentTranslation.display_name')->implode(', ') :
                            null;
                        $salaInUser = $user->classes->first() ?
                            $user->classes->first()->room->currentTranslation->display_name :
                            null;
                        $sala = $sala ?: $salaInUser;
                        $sala = $sala ?: 'N/A';
                        ?>
                        <td style="font-size: 11px">{{ $turma }}</td>
                    </tr>
                </tbody>
            </table>
        </table>
        <br>
        <table>
            <thead>
            <th class="titulos_emulumentos bg1">DADOS DA FACTURA/RECIBO</th>
            </thead>






            <table width="100%" style="margin-top: 7px;">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 12px; padding:2px;line-height: 24px; ">
                            Recebemos a quantia de <b>{{ number_format($transaction->value, 2, ',', '.') }} Kz</b> referente a: 
                        </td>
                    </tr>
                </tbody>
            </table>



            <table width="100%" style="margin-top: 5px">
                <thead>
                </thead>
                <tbody>
                    @php( $nome_disciplina=null) 
                        @foreach($transaction->article_request as $ar)
                                @foreach ($disciplines as $discipline)
                                    @if ($discipline->article_req_id == $ar->id)
                                        @if ($discipline->discipline_id!=null)
                                            
                                            @php  ($nome_disciplina = " (#$discipline->codigo_disciplina - $discipline->discipline_name)") 
                                        @endif
                                    @endif
                                @endforeach
                                <tr> 
                                @php($totalEmolumento+=$ar->pivot->value)
                                   
                                    <td style="font-size: 10.3px; padding-left:14px ; padding-top:3px; padding-bottom:3px;border-bottom: white 0.1pc solid;">
                                        {{-- @php ($explode=strops($ar->article->currentTranslation->display_name,'Pro')) --}}

                                        {{$ar->article->currentTranslation->display_name }} {{$nome_disciplina}}
                                        @if ($ar->month)

                                            @php($monthName = getLocalizedMonths()[$ar->month - 1]["display_name"])
                                           ({{ $monthName }} {{ $ar->year }})

                                        @endif

                                        @if ($ar->base_value>$ar->article->base_value || $ar->base_value<$ar->article->base_value)
                                        - <b>{{$ar->base_value}},00 Kz</b> &nbsp; <s><b>{{$ar->article->base_value}},00 Kz</b></s>
                                        
                                        @else
                                        
                                            - <b>{{ number_format($ar->base_value, 2, ',', '.') }} Kz</b>
                                        @endif
                                        
                                    </td>
                                   
                                    @if($ar->year!=null && $ar->month!=null && $ar->extra_fees_value!=0 && $ar->estado_extra_fees==1 || $ar->estado_extra_fees==2  && strpos($ar->article->currentTranslation->display_name,'Propina')!==false )
                                         @php($totalEmolumento+=$ar->extra_fees_value)  
                                         @php($multa_total +=$ar->extra_fees_value)  
                                    <td style="font-size: 10px; padding-left:7px ; padding-top:3px; padding-bottom:3px;border-bottom: white 0.1pc solid;">Taxa de atraso ({{$monthName}}  {{ $ar->year }}) - <b> {{ number_format($ar->extra_fees_value, 2, ',', '.') }} Kz</b>   </td>
                                    @else
                                    @if ($cancelarMulta==0 && $ar->extra_fees_value>0)
                                    <td style="font-size: 10px; padding-left:7px ; padding-top:3px; padding-bottom:3px;border-bottom: white 0.1pc solid;">Taxa de atraso <b> {{ number_format($ar->extra_fees_value, 2, ',', '.') }} Kz</b>   </td>
                                        
                                        @endif
                                        @if ($cancelarMulta==0)
                                                
                                        @else
                                            @if (in_array($ar->id, $cancelarMulta)) 
                                                <td style="font-size: 10px; padding-left:7px ; padding-top:3px; padding-bottom:3px;border-bottom: white 0.1pc solid;"><b>OBS:</b> Multa anulada.</td>
                                            @else
                                                
                                            @endif
                                        @endif
                                    @endif 
                                      
                            </tr>
                            {{-- for upload - 2 --}} 
                            <tr>
                                <td colspan="2" style="font-size: 8px; padding-top:3px; padding-bottom:3px;border-bottom: white 0.1pc solid;text-align:right!important;">
                                    Valor pago nesta transacção: <b> {{number_format($articleResume[$ar->id]["paid"], 2, ',', '.') }} kz </b> | Valor que fica pendente: <b>{{isset($articleResume[$ar->id]["pending"])? number_format($articleResume[$ar->id]["pending"] * -1, 2, ',', '.') :number_format(0, 2, ',', '.')}} kz</b></b> | Estado do emolumento: <b>{{$articleResume[$ar->id]["state"]=="total"?"Total":"Parcial"}}</b>
                                </td>
                            </tr>
                        @endforeach
                   
                </tbody>
            </table>
        </table>

        <br>

        <table >
            <thead>
            <th class="titulos_emulumentos bg1">MODO DE PAGAMENTO</th>
            </thead>
            <table width="100%" style="margin-top: 7px; ">
                <thead>
                </thead>
                <tbody>
                    <tr style="">
                        @php ($qtdLinha= $cretidoUser==true ? 2 :4 )
                        <td colspan="{{$qtdLinha}}" style="font-size: 11px; line-height: 24px; margin-bottom: 6px; padding-left:2px; ">
                           <b>Saldo em carteira inícial:</b>  {{number_format($user->credit_balance, 2, ',', '.') }} Kz
                        </td>
                        @if ($cretidoUser==true)
                            @php($saldo_usado = true)
                          <td colspan="2" style="font-size: 10px ; line-height: 24px; margin-top: 10px; padding-left:2px;">
                            <b>OBS:</b> Saldo em carteira não utilizado.
                          </td> 
                       @endif 
                    </tr>
                </tbody>
            </table>

            <table width="100%" style="margin-top: 5px" >
                <thead>
                </thead>
                <tbody>
                    @foreach ($transactionInfo as $info)
                        <tr>
                            <td style="font-size: 11px;padding-left:4px; padding-top: 7px;">
                                <b>Valor: </b>
                                    {{ number_format($info->value, 2, ',', '.') }} Kz
                                </td>
                                    <td style="font-size: 11px; padding-top: 7px;">
                                        <b>Data: </b>
                                        {{ \Carbon\Carbon::parse($info->fulfilled_at)->format('d/m/Y')}}
                                    </td>
                                    @isset($info->bank->type_conta_entidade)
                                        @if($info->bank->type_conta_entidade=="creditoAjuste")
                                            <td style="font-size: 11px;padding-left:4px; padding-top: 7px;">
                                                <b>Crédito - Ajuste</b>
                                            </td>
                                        @endif
                                    @else
                                        @isset($info->bank->display_name)
                                            <td style="font-size: 11px;padding-left:4px; padding-top: 7px;">
                                                <b>Banco: </b>
                                                {{ $info->bank->display_name }}
                                            </td>
                                            <td style="font-size: 11px;padding-left:4px; padding-top: 7px;">
                                                <b>Referência: </b>
                                                {{ $info->reference }}
                                            </td>                                             
                                        @endisset
                                    @endisset
                                </tr>
                    @endforeach
                                 
            </tbody>
            </table>



            <table width="100%" style=" margin-top: 6px;">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        @php($totalTransSaldo=$transaction->value + $user->credit_balance)
                        @php($totalSaldoCarteira=$totalTransSaldo - $totalEmolumento)
                       <td colspan="4" style="font-size: 11px ; line-height: 24px; margin-top: 10px; padding-left:2px;">
                           @if ($totalSaldoCarteira>=0)
                               {{-- <b>Saldo em carteira final :</b> {{number_format($totalSaldoCarteira, 2, ',', '.') }} Kz    --}}
                               

                               @if (isset($multa_total))
                                @if ($saldo_usado==true)
                                <b>Saldo em carteira final :</b> {{number_format($user->credit_balance, 2, ',', '.') }} Kz   
                                @else    
                                <b>Saldo em carteira final :</b> {{number_format($totalSaldoCarteira+$multa_total, 2, ',', '.') }} Kz   
                                @endif
                               @else    
                               <b>Saldo em carteira final :</b> {{number_format($totalSaldoCarteira, 2, ',', '.') }} Kz   
                               @endif
                               
                               
                               
                           @else
                                @if ($userValor_credit_balance>0 && $user->credit_balance==0)
                                        <b>Saldo em carteira final  :</b>{{number_format($userValor_credit_balance, 2, ',', '.') }} Kz 
                                @else
                                        <b>Saldo em carteira final :</b> 0.00 Kz 
                                @endif
                                    
                           @endif
                       </td>
                       
                        
                   </tr>
                </tbody>
            </table>
            
            



            @if($transaction->notes)
                <table width="100%" style="margin-top: 10px;">
                    <thead>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="4" style="font-size: 11px"><b>Notas: </b>{{ $transaction->notes }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
        </table>
    </main>

    <br>
 
 