@extends('layouts.printForSchedule')
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
        margin-bottom: 5px;
        color: white;
        width: 290px;
        padding:1px;
        text-align: left;
     }
     .rec_title{
        margin-left: 223px;
        padding-top: 28px;
     }
</style>

@php $now = \Carbon\Carbon::now(); @endphp

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


    @section('content')

            {{-- Main content --}}
            <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col" style="margin-top: -25px; !important">

                        <div class="row">
                            <div class="col-6">
                                <span style="font-size: 10pt;"> {{$institution->nome}}</span> <br>
                                <span style="font-size: 10pt;"> {{$institution->morada}}, -
                                    {{$institution->provincia}}</span> <br>
                                <span style="font-size: 10pt;">NIF: {{$institution->contribuinte}}</span> <br>
                                <span style="font-size: 10pt;">Email: {{$institution->email}} | Telemóvel:
                                    {{$institution->telefone_geral}}</span> <br>
                                {{-- <span style="font-size: 10pt;">Criado pelo Decreto Presidencial nº168/12, em 24 de Julho de 2012</span> --}}
                            </div>
                            <div class="col-6">
                                <span><b>Entidade Bolseira</b></span>
                                <br>
                                <span style="font-size: 10pt;"><b>EMPRESA:</b> {{ $entityInfo->company }}</span> <br>
                                <span style="font-size: 10pt;"><b>SEDE SOCIAL:</b>
                                    {{ $entityInfo->registered_office}}</span> <br>
                                <span style="font-size: 10pt;"><b>ESCRITÓRIOS:</b> {{$entityInfo->offices}}</span> <br>
                                <span style="font-size: 10pt;"><b>NIF:</b> {{$entityInfo->NIF}}</span> <br>
                                <span style="font-size: 10pt;"><b>TELEF:</b> {{$entityInfo->telf}}</span>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                @php($user = $transaction->article_request->first()->user)

                <table class="table table-parameter-group">
                    <thead class="">
                        <th class="titulos_emulumentos bg1">IDENTIFICAÇÃO NA INSTITUIÇÃO</th>
                        </thead>
                    <table width="100%">
                        <thead>
                            <th class="th-parameter-group header-user bg2">Matrícula</th>
                            <th class="th-parameter-group header-user bg2">NOME</th>
                            <th class="th-parameter-group header-user bg2">Curso</th>
                            <th class="th-parameter-group header-user bg2">Turma(s)</th>
                            <th class="th-parameter-group header-user bg2">Sala(s)</th>
                            <th class="th-parameter-group header-user bg2">Ano académico</th>
                        </thead>
                        <tbody>
                            <tr>
                                @php($nMecanografico = $user->parameters->first() ? $user->parameters->first()->pivot->value
                                : 'N/A')
                                <td style="font-size: 11px">{{ $nMecanografico }}</td>
                                <td style="font-size: 11px">{{ $user->user_parameters->first()->value}}</td>
                                @php($curso = $user->courses->first() ?
                                $user->courses->first()->currentTranslation->display_name : 'N/A')
                                <td style="font-size: 11px">{{ $curso }}</td>
                                <?php 
                                    $turma = $user->matriculation ?
                                        $user->matriculation->classes->pluck('display_name')->implode(', ') :
                                        null;

                                    $turmaInUser = $user->classes->first() ? $user->classes->first()->display_name : null;
                                    $turma = $turma ?: $turmaInUser;

                                    $turma = $turma ?: 'N/A';
                                ?>
                                <td style="font-size: 11px">{{ $turma }}</td>
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

                                <td style="font-size: 11px">{{ $sala }}</td>
                                <td style="font-size: 11px">
                                    @php ($course_year =
                                    $transaction->article_request->first()->user->matriculation->course_year)
                                    {{ $course_year }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <table class="table table-parameter-group">
                        <thead>
                            <th class="titulos_emulumentos bg1">DADOS DA FACTURA/RECIBO</th>
                            </thead>
                        <table width="100%">
                            <thead>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-size: 12px; line-height: 24px;">
                                        Recebemos a quantia de <b>{{ number_format($transaction->value, 2, ',', '.') }} </b>Kz referente a:
                                    </td>
                                </tr>
                                @php( $nome_disciplina=null) 
                                @foreach($transaction->article_request as $ar)
                                        @foreach ($disciplines as $discipline)
                                            @if ($discipline->article_req_id == $ar->id)
                                                @if ($discipline->discipline_id!=null)
                                                    @php  ($nome_disciplina = " ($discipline->discipline_name - [$discipline->codigo_disciplina])") 
                                                @endif
                                            @endif
                                        @endforeach


                                <tr>
                                    <td style="font-size: 11px">
                                        {{ $ar->article->currentTranslation->display_name }} {{$nome_disciplina}}
                                        @if ($ar->month)
                                        @php($monthName = getLocalizedMonths()[$ar->month - 1]["display_name"])
                                        ({{ $monthName }} {{ $ar->year }})
                                        @endif
                                        - <b>{{ number_format($ar->pivot->value, 2, ',', '.') }} Kz</b>
                                    </td>

                                    @if($ar->discipline_id==null &&  $ar->year!=null && $ar->month!=null && $ar->extra_fees_value!=0 && $ar->estado_extra_fees==1 || $ar->estado_extra_fees==2  && $ar->discipline_id==null)  
                                        <td style="font-size: 10px; padding-left:7px ; padding-top:3px; padding-bottom:3px;">Taxa de atraso ({{$monthName}}  {{ $ar->year }}) - <b> {{ number_format($ar->extra_fees_value, 2, ',', '.') }} Kz</b>   </td>
                                    @else
                                        @if ($cancelarMulta==0)
                                                
                                        @else
                                            @if (in_array($ar->id, $cancelarMulta)) 
                                                <td style="font-size: 10px; padding-left:7px ; padding-top:3px; padding-bottom:3px;"><b>OBS:</b> Multa anulada.</td>
                                            @else
                                                
                                            @endif
                                        @endif
                                    @endif 


                                </tr>
                                <tr>
                                    <td style="font-size: 11px">
                                        @if(isset($entityInfo->desconto_scholarship_holder) && $entityInfo->desconto_scholarship_holder>0)
                                            Desconto - {{$entityInfo->desconto_scholarship_holder}}%
                                        @else
                                            Sem Desconto 
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </table>

                    <br>
                    <table class="table table-parameter-group">
                        <thead>
                            <th class="titulos_emulumentos bg1">MODO DE PAGAMENTO</th>
                            </thead>
                        <table width="100%">
                            <thead>
                            </thead>
                            <tbody>
                                @foreach ($transactionInfo as $info)
                                    <tr>
                                        <td style="font-size: 11px">
                                            <b>Valor: </b>
                                            {{ number_format($info->value, 2, ',', '.') }} Kz

                                        </td>
                                        <td style="font-size: 11px">
                                            <b>Data: </b>
                                            {{ \Carbon\Carbon::parse($info->fulfilled_at)->format('d/m/Y')}}
                                        </td>
                                        <td style="font-size: 11px">
                                            <b>Banco: </b>
                                            {{ $info->bank->display_name }}
                                        </td>
                                        <td style="font-size: 11px">
                                            <b>Referência: </b>
                                            {{ $info->reference }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                    </table>
                        <br>
                        {{-- <table class="table table-parameter-group">
                        <thead class="thead-parameter-group">
                            <th class="th-parameter-group" style="text-align: center">CONTAS BANCÁRIAS</th>
                        </thead>
                        <table width="100%">
                            <thead>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-size: 11px">
                                        <b>BPC:</b> 0455-G58843-011 | <b>IBAN:</b> AO06 001004550165884301169
                                    </td>
                                    <td style="font-size: 11px">
                                        <b>MILLENIUM ATLÂNTICO:</b> 1030376101 | <b>IBAN:</b> AO06 005500000103037610136
                                        </td>
                                    <td style="font-size: 11px">
                                        <b>KEVE: </b>9484640101 | <b>IBAN:</b> AO06 004700000948464010103
                                    </td>
                                </tr>
                        </table>
                    </table> --}}

            </div>
        </div>
        </div>
        </div>
        </div>
        </div>
        </div>

    @endsection
    @section('scripts')

    @endsection
