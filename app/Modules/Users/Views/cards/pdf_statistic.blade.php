<title>{{'Estatística dos cartões de estudantes_'.$lt->display_name}}</title>
@extends('layouts.print')
@section('content')

<head>
    <style>
        table {
            page-break-inside: auto;
            border-collapse: collapse;
            width: 100%;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .titulo {
            margin-bottom: 10px;
        }
        
        .decreto {
            font-size: 0.7rem !important;
            padding-left: 70px !important;
        }
        
        .instituition {
            font-size: 30px !important;
        }

        /* Estilos melhorados para legibilidade */
        .table_te {
            width: 780px !important;
            border: 2px solid #000000;
            font-family: Arial, sans-serif;
        }

        .table_te th,
        .table_te td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
            background-color: #ffffff !important;
            color: #000000 !important;
        }

        .table_te .bg0 {
            background-color: #f8f9fa !important;
            color: #000000 !important;
            font-size: 16px;
            padding: 12px;
        }

        .table_te .bg1 {
            background-color: #e9ecef !important;
            color: #000000 !important;
            font-weight: bold;
            font-size: 14px;
        }

        .table_te .bg2 {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 13px;
        }

        .table_te .bg3 {
            background-color: #f8f9fa !important;
            color: #000000 !important;
            font-weight: bold;
            font-size: 13px;
        }

        .table_te .bg4 {
            background-color: #dee2e6 !important;
            color: #000000 !important;
            font-weight: bold;
            font-size: 14px;
        }

        .line {
            border-bottom: 2px solid #000000;
        }

        .last-line {
            border-top: 2px solid #000000;
        }

        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-white {
            color: #000000 !important;
        }

        .font-weight-bold {
            font-weight: bold !important;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .f1 { font-size: 16px; }
        .f2 { font-size: 14px; }
        .f3 { font-size: 13px; }
        .f4 { font-size: 14px; }

        .pd {
            padding: 8px;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        .signature-forlearn {
            margin-left: 92px;
            width: 810px !important;
        }

        .sign-date p {
            font-size: 17px !important;
        }

        /* Melhor espaçamento */
        .table_te tr {
            line-height: 1.4;
        }

        /* Quebras de página melhoradas */
        @media print {
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<main>
    @php
        $title1 = 'Análise estatística dos cartões de estudantes';
        $title2 = 'Ano lectivo ( ' . $lt->display_name . ' )';
        $area = '';
    @endphp
    @include('Reports::declaration.cabecalho.others')
    <!-- aqui termina o cabeçalho do pdf -->
    
    <div class="">
        <div class="">
            <!-- personalName -->
            <div class="row">
                <div class="col-12">
                    <div class="">
                        <div class="">
                            @php
                                $i = 1;
                            @endphp
                            <center>
                                <table class="table_te">
                                    @php
                                        $t1 = 0;
                                        $t2 = 0;
                                        $t3 = 0;
                                        $t4 = 0;
                                    @endphp

                                    @php
                                        $i = 1;
                                        $m_p = 0;
                                        $t_p = 0;
                                        $n_p = 0;
                                        $count_break = 0
                                    @endphp
                                    
                                    @foreach ($courses as $key => $item)
                                        @php
                                            $sub_t1 = 0;
                                            $sub_t2 = 0;
                                            $sub_t3 = 0;
                                            $sub_t4 = 0;
                                            $count_break = $count_break + 1;
                                        @endphp
                                        
                                        <tr class="line">
                                            <td colspan="5" class="text-left bg0 text-uppercase font-weight-bold f1">
                                                {{ $key }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="text-center bg1 font-weight-bold f2">Turmas</th>
                                            <th class="text-center bg1 font-weight-bold f2">Nº de estudantes</th>
                                            <th class="text-center bg1 font-weight-bold f2">Fotografia</th>
                                            <th class="text-center bg1 font-weight-bold f2">Imprimido</th>
                                            <th class="text-center bg1 font-weight-bold f2">Entregas</th>
                                        </tr>
                                        
                                        @foreach ($item as $turma)
                                            <tr>
                                                <td class="text-center bg2 font-weight-bold f3 pd">{{ $turma['turma'] }}</td>
                                                <td class="text-center bg2 font-weight-bold f3 pd">{{ $turma['total'] }}</td>
                                                <td class="text-center bg2 font-weight-bold f3 pd">{{ $turma['fotografia'] }}</td>
                                                <td class="text-center bg2 font-weight-bold f3 pd">{{ $turma['imprimido'] }}</td>
                                                <td class="text-center bg2 font-weight-bold f3 pd">{{ $turma['entrega'] }}</td>
                                            </tr>
                                            @php
                                                $sub_t1 += $turma['total'];
                                                $sub_t2 += $turma['fotografia'];
                                                $sub_t3 += $turma['imprimido'];
                                                $sub_t4 += $turma['entrega'];

                                                $t1 += $turma['total'];
                                                $t2 += $turma['fotografia'];
                                                $t3 += $turma['imprimido'];
                                                $t4 += $turma['entrega'];
                                            @endphp
                                        @endforeach

                                        <tr class="last-line font-weight-bold">
                                            <td class="bg-white f4"><b>SUB-TOTAL</b></td>
                                            <td class="text-center bg3 f3">{{ $sub_t1 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t2 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t3 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t4 }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="5" class="bg-white" style="height: 10px;"></td>
                                        </tr>
                                        
                                        @if ($count_break == 5)
                                            <tr>
                                                <td colspan="5" class="bg-white" style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="bg-white" style="height: 10px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="bg-white" style="height: 20px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="bg-white" style="height: 10px;"></td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td colspan="5" class="bg-white" style="height: 15px;"></td>
                                    </tr>

                                    <tr class="last-line">
                                        <td class="bg-white f1"><b>TOTAL</b></td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t1 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t2 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t3 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t4 }}</td>
                                    </tr>
                                </table>
                            </center>
                            
                            <br>
                            @if(($card_total - $t3) > 0)
                                <div style="font-size: 17px; margin-left: 108px; color: #000000;">
                                    <b>Nota:</b> A forLEARN detectou ({{ (+$card_total - $t3) }}) cartões imprimidos associados a matrículas anuladas.
                                </div>
                            @endif
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('Reports::pdf_model.signature')
    
@endsection