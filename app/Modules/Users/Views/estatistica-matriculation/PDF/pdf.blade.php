@extends('layouts.print')
@include('Reports::pdf_model.pdf_header')
<br>
<style>
    #tabela-aprovados {
        margin-bottom: 10px;
        width: 100%;
    }

    #tabela-total {

        width: 22%;
    }

    #tabela-reprovados {
        width: 70%;
    }

    #tabela-aprovados th,
    #tabela-aprovados td,
    #tabela-aprovados {
        text-align: center !important;
        /* background-color: #adadad; */

    }

    #tabela-reprovados th,
    #tabela-reprovados td,
    #tabela-reprovados {
        text-align: center !important;
        /* background-color: #c1c1c1; */

    }

    #tabela-total th,
    #tabela-total td,
    #tabela-total {
        text-align: center !important;
        /* background-color: #a09e9e; */

    }


    #tabela-reprovados {
        float: left;
        margin-right: 8%;
        padding: 2px;

    }


    .titulo {
        text-transform: uppercase;
        /* background-color: #adadad; */
        width: 250px;
        text-indent: 10px;
        padding: 4px;

    }

    #opaco {
        background-color: transparent;
    }

    .tabela-estatistica thead {
        /* display: table-row-group; */
    }

    .tabela-estatistica td {
        text-align: center;
        white-space: pre-wrap;
    }


    #tabela-aprovados th,
    #tabela-reprovados th,
    #tabela-total th,
    #tabela-aprovados td,
    #tabela-reprovados td,
    #tabela-total td {
        padding: 2px 4px 1px 4px;

        border: 1px solid white;
        font-size: 10px;
    }

    #tabela-aprovados thead,
    #tabela-reprovados thead,
    #tabela-total thead {
        width: 100%;
    }

    #tabela-aprovados td,
    #tabela-reprovados td,
    #tabela-total td {
        font-size: 10px;
        border: 1px solid white;
    }

    .titulo-aprovados {
        background-color: #a8d08d !important;
    }

    .titulo-reprovados {
        background-color: #c55a11 !important;
    }

    .titulo-avaliados {
        background-color: #8eaadb !important;

    }

    .escala-135 {
        background-color: #ffe598 !important;
    }

    .escala-24 {
        background-color: #f7caac !important;
    }

    .anodisc {
        background-color: #b4c6e7;
    }

    .t {
        background-color: #aeabab;
    }

    .t-descricao {
        background-color: #ffd965;
    }
</style>
<br>
<br>
@section('content')
    <style>
        .tabela-estatistica thead {
            display: table-row-group;
        }

        .tabela-estatistica td {
            text-align: center;
            white-space: pre-wrap;
        }

        th,
        td {
            padding: 2px 4px 1px 4px;

            border: 1px solid white;
            font-size: 10px;
        }

        thead {
            width: 100%;
        }

        thead tr th,
        tbody tr th {
            font-size: 12pt !important;
        }

        thead tr th {
            font-weight: 500 !important;
        }

        tbody tr th {
            font-weight: 400 !important;
        }

        td {
            font-size: 10px;
            border: 1px solid white;
        }

        #tabela-aprovados {
            float: left;
        }

        #tabela-reprovados {
            float: left;
            margin-right: 34px
        }

        #tabela-total {}

        .bnone {
            border: none !important;
        }


        h4 {
            text-transform: uppercase;
        }

        .titulo-aprovados {
            background-color: #a8d08d;
        }

        .titulo-reprovados {
            background-color: #c55a11;
        }

        .titulo-avaliados {
            background-color: #8eaadb;

        }

        .escala-135 {
            background-color: #ffe598;
        }

        .escala-24 {
            background-color: #f7caac;
        }

        .escala-22 {
            background-color: #8eaadb;
        }

        .anodisc {
            background-color: #b4c6e7;
        }

        .t {
            background-color: #aeabab;
            font-weight: 700 !important;
        }

        .t-descricao {
            background-color: #ffd965;
        }
        .tr-linha th{
            font-weight: 900!important; 
        }
        .h1-title{
            padding-left: 0%!important;
            transform: translateX(-100px);
            font-size: 1.40rem; 
        }
    </style>

    <br>
    <br>
    <main>
        <table class="tabela-estatistica" cellspacing="0" style="width: 60% !important;margin-left: 20%!important;">
            <thead style="">
                <tr>
                    <th class="bnone" style="text-align:left;font-size: 40px!|important" colspan="50">

                        Curso: Licenciatura em

                        {{ $curso . ' - ' .$anos. ' ano' }}

                    </th>
                </tr>

                <tr>
                    <th colspan="1" class="bnone" style="text-align: left"></th>
                    <th colspan="1" class="bnone" style="text-align: left"></th>
                    <th colspan="5" class="bnone titulo-aprovados">Matriculados</th>
                </tr>
                <tr class="tr-linha">
                    <th colspan="1" class="titulo-aprovados">Ano</th>
                    <th colspan="1" class="escala-135">Turma</th>
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                </tr>
            </thead>


            <tbody>

                @php
                    
                    // Matriculados ( Sexo )
                    
                    $t_M = 0;
                    $t_F = 0;
                    $T_MF = 0;
                @endphp

                @foreach ($ano as $item_ano)
                    
               
                @foreach ($matriculados[$item_ano] as $item)
                    @php
                        $M = isset($item['masculino']) ? $item['masculino'] : 0;
                        $F = isset($item['femenino']) ? $item['femenino'] : 0;
                        
                        // Total de Matriculados em turmas
                        
                        $MF = $M + $F;
                        
                        // Total de Matriculados Masculino
                        
                        $t_M = $t_M + $M;
                        
                        // Total de Matriculados Masculino
                        
                        $t_F = $t_F + $F;
                        
                        // Total de Matriculados
                        
                        $T_MF = $T_MF + $MF;
                    @endphp

                    <tr>
                        <th colspan="1" class="titulo-aprovados">{{ $item['ano']}}º</th>
                        <th colspan="1" class="escala-135">{{ $item['turma'] }}</th>
                        <th class="escala-22">
                            {{ $M }}
                        </th>
                        <th class="escala-22">
                            @if ($MF != 0)
                                {{ (int) round(($M / $MF) * 100, 0) }}%
                            @else
                                0%
                            @endif
                        </th>
                        <th class="escala-24">
                            {{ $F }}
                        </th>
                        <th class="escala-24">
                            @if ($MF != 0)
                                {{ (int) round(($F / $MF) * 100, 0) }}%
                            @else
                                0%
                            @endif
                        </th>

                        <th class="t">
                            {{ $MF }}
                        </th>

                    </tr>
                @endforeach
                
                {{-- @if(count($ano)>1)
                    <tr>
                        <td></td>
                    </tr>
                @endif --}}
                
                @endforeach
                <tr>
                    <th colspan="100" style="color: transparent;font-size:1px!important;">
                </tr>
                </tr>
                <tr>
                    <th colspan="1" class="bnone"></th>
                    <th colspan="1" class="bnone"></th>
                    <th class="escala-22">{{ $t_M }}</th>
                    <th class="escala-22">
                        @if ($T_MF != 0)
                            {{ (int) round(($t_M / $T_MF) * 100, 0) }}%
                        @else
                            0%
                        @endif
                    </th>
                    <th class="escala-24">{{ $t_F }}</th>
                    <th class="escala-24">
                        @if ($T_MF != 0)
                            {{ (int) round(($t_F / $T_MF) * 100, 0) }}%
                        @else
                            0%
                        @endif
                    </th>
                    <th class="t">{{ $T_MF }}</th>

                </tr>
            </tbody>
        </table>


        <br>
    </main>
@endsection
