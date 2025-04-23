@extends('layouts.print')
@section('content')
<title>Reembolsos | forLEARN® by GQS</title>

    <style>
        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 3px 5px !important;
            font-size: .625rem;
            width: 180px;

        }

        .td-parameter-column {
            padding-left: 5px !important;
            margin-bottom: 4px;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        .header-user {
            padding: 0 !important;
            text-align: left !important;
        }

        td {
            background-color: rgb(240, 240, 240);
            /* margin-bottom: 2px; */
            border-left: 2px solid white;
            font-size: 15px;
        }


        .titulos_emulumentos {
            margin-bottom: 2pc;
            color: white;
            width: 290px;
            padding: 1px;
            text-align: left;
        }
        body{
            transform: scale(2,2); 
        }
    </style>

    @php $now = \Carbon\Carbon::now(); @endphp
    @php $multa_total = 0 ; @endphp
    @php $saldo_usado = 0 ;  @endphp
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'Comprovativo de reembolso';
    @endphp
    


    @php use App\Modules\Payments\Controllers\BolseirosController; @endphp
    @php if(isset($balance->code) && isset($balance->year)){
         $code = BolseirosController::get_code_doc($balance->code,$balance->year);
        }
    @endphp

    @include('Reports::pdf_model.header_reembolso')
    <br>
    <main>
        <table>
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
                    <th class=" header-user bg2">Ano Lectivo</th>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 14px">#{{$balance->matriculation}}</td>
                        <td style="font-size: 14px">{{isset($balance->full_name)?$balance->full_name:$balance->name}}</td>
                        <td style="font-size: 14px">{{$balance->email}}</td>
                        <td style="font-size: 14px">{{$balance->course}}</td>
                        <td style="font-size: 14px">{{$balance->turma}}</td>
                        <td style="font-size: 14px">{{$lective}}</td>
                    </tr>
                </tbody>
            </table>
        </table>

        <br>
        <table>
            <thead>
                <th class="titulos_emulumentos bg1">DADOS DO RECIBO</th>
            </thead>

            <table width="100%" style="margin-top: 7px;">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 12px; padding:2px;line-height: 24px; ">
                            Reembolsamos a quantia de <b> {{ number_format($balance->value, 2, ',', '.') }} kz</b>
                        </td>
                    </tr>
                </tbody>
            </table>

        </table>

        <br>

        <table>
            <thead>
                <th class="titulos_emulumentos bg1">DADOS DO REEMBOLSO</th>
            </thead>
            <table width="100%" style="margin-top: 7px; ">
                <thead>
                </thead>
                <tbody>
                    <tr style="">
                        <td colspan=""
                            style="font-size: 14px; line-height: 24px; margin-bottom: 6px; padding-left:2px; ">
                            <b>Saldo em carteira inícial:</b> {{ number_format($balance->credit_balance, 2, ',', '.') }} kz
                        </td>
                    </tr>
                </tbody>
            </table>

            <table width="100%" style="margin-top: 5px">
                <thead>
                </thead>
                <tbody>

                    <tr>
                        <td style="font-size: 14px;padding-left:4px; padding-top: 7px;">
                            <b>Valor: </b>
                             {{ number_format($balance->value, 2, ',', '.') }} kz
                        </td>
                        <td style="font-size: 14px; padding-top: 7px;">
                            <b>Data: </b>
                            {{$balance->date}}
                        </td>

                        <td style="font-size: 14px;padding-left:4px; padding-top: 7px;">
                            <b>Método: </b>
                            {{$balance->mode==1?"Transferência":""}}
                            {{$balance->mode==2?"Depósito":""}}
                        </td>

                        <td style="font-size: 14px;padding-left:4px; padding-top: 7px;">
                            <b>IBAN / nº de conta: </b>
                            {{$balance->iban}}

                        </td>
                        <td style="font-size: 14px;padding-left:4px; padding-top: 7px;">
                            <b>Referência: </b>
                            {{$balance->reference}}

                        </td>
                    </tr>


                </tbody>
            </table>



            <table width="100%" style=" margin-top: 6px;">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="font-size: 14px ; line-height: 24px; margin-top: 10px; padding-left:2px;">
                            <b>Saldo em carteira final :</b>  <b> {{ number_format($balance->credit_balance_final, 2, ',', '.') }} kz</b> Kz
                        </td>
                    </tr>
                </tbody>
            </table>






            <table width="100%" style="margin-top: 10px;">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="font-size: 14px"><b>Notas: </b> {{$balance->observation}}</td>
                    </tr>
                </tbody>
            </table>

        </table>

        <style>
            .table-assinaturas tbody,.table-assinaturas tfoot,.table-assinaturas tr,.table-assinaturas td,.table-assinaturas th{
                background-color: white !important;
                text-align: center;
            }
            .table-assinaturas{
                width: 700px;
                margin-top: 320px;
            }
        </style>
        
        <center>
        <table class="table-assinaturas">
            <thead>
                <tr>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Estudante</td>
                    <td></td>
                    <td>Tesoureiro(a)</td>
                </tr>
                <tr>
                    <td>______________________________________</td>
                    <td></td>
                    <td>______________________________________</td>
                </tr>
                <tr>
                    <td>{{isset($balance->full_name)?$balance->full_name:$balance->name}}</td>
                    <td></td>
                    <td>{{$balance->created_by}}</td> 
                </tr>
            </tbody>
        </table>
    </center>

    </main>
