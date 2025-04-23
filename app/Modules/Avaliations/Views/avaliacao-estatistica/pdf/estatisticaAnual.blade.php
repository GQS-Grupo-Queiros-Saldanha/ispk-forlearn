@extends('layouts.print')

@php
    $doc_name = 'ANÁLISE ESTATÍSTICO ANUAL';
    $discipline_code = '';
@endphp
@include('Reports::pdf_model.forLEARN_header')
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
    </style>

    <br>
    <br>
    <main>
        <table class="tabela-estatistica" cellspacing="0" style="width: 100% !important">
            <thead style="">
                <tr>
                    <th class="bnone" style="text-align:left;" colspan="50">

                        Curso: Licenciatura em

                        {{ $curso[0]->nome . ' - ' . $curso[0]->cg . ' - ' . $ano }}

                    </th>
                </tr>
                <tr>
                    <th colspan="2" class="" style="text-align: left"></th>
                    <th colspan="5" class="bnone"></th>
                    <th colspan="50" class="bnone t-descricao">RESULTADO FINAL - QUANTITATIVO</th>

                </tr>
                <tr>
                    <th colspan="1" class="bnone" style="text-align: left"></th>
                    <th colspan="3" class="bnone escala-24">Matriculados</th>
                    <th colspan="6" class="bnone escala-24">Nº Avaliados</th>
                    <th colspan="6" class="bnone titulo-reprovados">Reprovados</th>
                    <th colspan="6" class="bnone titulo-aprovados">Aprovados. C-CA</th>
                    <th colspan="6" class="bnone titulo-aprovados">Aprovados. S-CA</th>
                    <th colspan="6" class="bnone titulo-aprovados">TOTAL APROVADOS</th>
                    <th colspan="3" class="bnone escala-24">Aproveitamento (%)</th>

                </tr>

                {{-- <tr>
                    <th colspan="50" class="bnone" style="color:rgba(0,0,0,0)">.</th>
                </tr> --}}
                <tr>


                    <th colspan="1" class="t-descricao">Ano Curricular</th>
                    <th class="escala-22">M</th>
                    
                    <th class="escala-24">F</th>
                    
                    <th class="t">T</th>
                    
                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="escala-22">M</th>
                    <th class="escala-22">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>
                    
                    <th class="escala-22">M</th>
                    <th class="escala-24">F</th>
                    <th class="t">T</th>

                </tr>
            </thead>


            <tbody>

                @php
                    
                    // Matriculados ( Sexo )
                    
                    $t_M = 0;
                    $t_F = 0;
                    
                    // Avaliados ( Sexo )
                    
                    $t_AM = 0;
                    $t_AF = 0;
                    $total_AVA = 0;
                    
                    // Reprovados ( Sexo )
                    
                    $t_RM = 0;
                    $t_RF = 0;
                    
                    // Total aprovados sem cadeira em atraso ( Sexo )
                    
                    $t_APM = 0;
                    $t_APF = 0;
                    $total_APSc = 0;
                    
                    // Total aprovados com cadeira em atraso ( Sexo )
                    
                    $t_APMC = 0;
                    $t_APFC = 0;
                    $total_APC = 0;
                    
                    // Reprovados por ano ( Sexo )
                    
                    $RM = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    $RF = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    
                    // Aprovados por ano sem cadeira em atraso ( Sexo )
                    
                    $AM = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    $AF = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    
                    // Aprovados por ano com cadeira em atraso ( Sexo )
                    
                    $AMC = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    $AFC = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];
                    
                @endphp
                @foreach ($curricular as $anos)
                    @php
                        
                        // Alunos matriculados
                        
                        if (isset($total_matriculados[$anos]['masculino'])) {
                            $t_M = $t_M + $total_matriculados[$anos]['masculino'];
                        }
                        if (isset($total_matriculados[$anos]['femenino'])) {
                            $t_F = $t_F + $total_matriculados[$anos]['femenino'];
                        }
                        
                        // Aproveitamentos (Reprovados e aprovados)
                        
                        foreach ($estudantes as $item) {
                            if ($item['ano'] == $anos) {
                                switch ($Cursos) {
                                    case '23':
                                        // ===================================================== Reprovados =====================================================
                        
                                        // Reprovados do sexo Masculino
                        
                                        if ($item['sexo'] == 'Masculino' && $item['negativa'] > 5) {
                                            $t_RM = $t_RM + 1;
                                            $RM[$anos] = $RM[$anos] + 1;
                                        }
                        
                                        // Reprovados do sexo Femenino
                        
                                        if ($item['sexo'] == 'Feminino' && $item['negativa'] > 5) {
                                            $t_RF = $t_RF + 1;
                                            $RF[$anos] = $RF[$anos] + 1;
                                        }
                        
                                        // ===================================================== Aprovados =====================================================
                        
                                        // Aprovados do sexo Masculino
                        
                                        if ($item['sexo'] == 'Masculino' && $item['negativa'] == 0) {
                                            $t_APM = $t_APM + 1;
                                            $total_APSc = $total_APSc + 1;
                                            $AM[$anos] = $AM[$anos] + 1;
                                        }
                        
                                        // Aprovados do sexo Femenino
                        
                                        if ($item['sexo'] == 'Feminino' && $item['negativa'] == 0) {
                                            $t_APF = $t_APF + 1;
                                            $total_APSc = $total_APSc + 1;
                                            $AF[$anos] = $AF[$anos] + 1;
                                        }
                        
                                        // ===================================================== Aprovados com cadeiras em atraso =====================================================
                        
                                        // Aprovados do sexo Masculino
                                        
                                        
                                        if ($item['sexo'] == 'Masculino' && ($item['negativa'] > 0 && $item['negativa'] < 6)) {
                                                    if($anos>3){
                                                        if ($item['sexo'] == 'Masculino') {
                                                            $t_RM = $t_RM + 1;
                                                            $RM[$anos] = $RM[$anos] + 1;
                                                        }
                                                    }else{   
                                                        $t_APMC = $t_APMC + 1;
                                                        $total_APC = $total_APC + 1;
                                                        $AMC[$anos] = $AMC[$anos] + 1;
                                                    }
                                            }
                            
                                            // Aprovados do sexo Femenino
                                            

                                            if ($item['sexo'] == 'Feminino' && ($item['negativa'] > 0 && $item['negativa'] < 6)) {

                                                if($anos>3){
                                                    if ($item['sexo'] == 'Feminino') {
                                                        $t_RF = $t_RF + 1;
                                                        $RF[$anos] = $RF[$anos] + 1; 
                                                    }
                                                    }else{   
                                                        $t_APFC = $t_APFC + 1;
                                                        $total_APC = $total_APC + 1;
                                                        $AFC[$anos] = $AFC[$anos] + 1;
                                                    }
                                            }
                                   
                                        break;
                                    case '25':
                                        if ($item['ano'] != 1) {
                                            // ===================================================== Reprovados =====================================================
                        
                                            // Reprovados do sexo Masculino
                        
                                            if ($item['sexo'] == 'Masculino' && $item['negativa'] > 0) {
                                                $t_RM = $t_RM + 1;
                                                $RM[$anos] = $RM[$anos] + 1;
                                            }
                        
                                            // Reprovados do sexo Femenino
                        
                                            if ($item['sexo'] == 'Feminino' && $item['negativa'] > 0) {
                                                $t_RF = $t_RF + 1;
                                                $RF[$anos] = $RF[$anos] + 1;
                                            }
                        
                                            // ===================================================== Aprovados =====================================================
                        
                                            // Aprovados do sexo Masculino
                        
                                            if ($item['sexo'] == 'Masculino' && $item['negativa'] == 0) {
                                                $t_APM = $t_APM + 1;
                                                $total_APSc = $total_APSc + 1;
                                                $AM[$anos] = $AM[$anos] + 1;
                                            }
                        
                                            // Aprovados do sexo Femenino
                        
                                            if ($item['sexo'] == 'Feminino' && $item['negativa'] == 0) {
                                                $t_APF = $t_APF + 1;
                                                $total_APSc = $total_APSc + 1;
                                                $AF[$anos] = $AF[$anos] + 1;
                                            }
                                        } else {
                                            // =====================================================Reprovados =====================================================
                        
                                            // Reprovados do sexo Masculino
                        
                                            if ($item['sexo'] == 'Masculino' && $item['negativa'] > 4) {
                                                $t_RM = $t_RM + 1;
                                                $RM[$anos] = $RM[$anos] + 1;
                                            }
                        
                                            // Reprovados do sexo Femenino
                        
                                            if ($item['sexo'] == 'Feminino' && $item['negativa'] > 4) {
                                                $t_RF = $t_RF + 1;
                                                $RF[$anos] = $RF[$anos] + 1;
                                            }
                        
                                            // ===================================================== Aprovados =====================================================
                        
                                            // Aprovados do sexo Masculino
                        
                                            if ($item['sexo'] == 'Masculino' && $item['negativa'] == 0) {
                                                $t_APM = $t_APM + 1;
                                                $total_APSc = $total_APSc + 1;
                                                $AM[$anos] = $AM[$anos] + 1;
                                            }
                        
                                            // Aprovados do sexo Femenino
                        
                                            if ($item['sexo'] == 'Feminino' && $item['negativa'] == 0) {
                                                $t_APF = $t_APF + 1;
                                                $total_APSc = $total_APSc + 1;
                                                $AF[$anos] = $AF[$anos] + 1;
                                            }
                        
                                            // ===================================================== Aprovados com cadeira =====================================================
                        
                                            // Aprovados do sexo Masculino
                        
                                            if ($item['sexo'] == 'Masculino' && ($item['negativa'] > 0 && $item['negativa'] < 5)) {
                                                $t_APMC = $t_APMC + 1;
                                                $total_APC = $total_APC + 1;
                                                $AMC[$anos] = $AMC[$anos] + 1;
                                            }
                        
                                            // Aprovados do sexo Femenino
                        
                                            if ($item['sexo'] == 'Feminino' && ($item['negativa'] > 0 && $item['negativa'] < 5)) {
                                                $t_APFC = $t_APFC + 1;
                                                $total_APC = $total_APC + 1;
                                                $AFC[$anos] = $AFC[$anos] + 1;
                                            }
                                        }
                        
                                        break;
                        
                                    default:
                                        // ===================================================== Reprovados =====================================================
                        
                                        // Reprovados do sexo Masculino
                        
                                        if ($item['sexo'] == 'Masculino' && $item['negativa'] > 4) {
                                            $t_RM = $t_RM + 1;
                                            $RM[$anos] = $RM[$anos] + 1;
                                        }
                        
                                        // Reprovados do sexo Femenino
                        
                                        if ($item['sexo'] == 'Feminino' && $item['negativa'] > 4) {
                                            $t_RF = $t_RF + 1;
                                            $RF[$anos] = $RF[$anos] + 1;
                                        }
                        
                                        // ===================================================== Aprovados =====================================================
                        
                                        // Aprovados do sexo Masculino
                        
                                        if ($item['sexo'] == 'Masculino' && $item['negativa'] == 0) {
                                            $t_APM = $t_APM + 1;
                                            $total_APSc = $total_APSc + 1;
                                            $AM[$anos] = $AM[$anos] + 1;
                                        }
                        
                                        // Aprovados do sexo Femenino
                        
                                        if ($item['sexo'] == 'Feminino' && $item['negativa'] == 0) {
                                            $t_APF = $t_APF + 1;
                                            $total_APSc = $total_APSc + 1;
                                            $AF[$anos] = $AF[$anos] + 1;
                                        }
                        
                                        // ===================================================== Aprovados com cadeiras em atraso =====================================================
                        
                                        // Aprovados do sexo Masculino


                                            if ($item['sexo'] == 'Masculino' && ($item['negativa'] > 0 && $item['negativa'] < 5)) {
                                                    if($anos>3){
                                                        if ($item['sexo'] == 'Masculino') {
                                                            $t_RM = $t_RM + 1;
                                                            $RM[$anos] = $RM[$anos] + 1;
                                                        }
                                                    }else{   
                                                        $t_APMC = $t_APMC + 1;
                                                        $total_APC = $total_APC + 1;
                                                        $AMC[$anos] = $AMC[$anos] + 1;
                                                    }
                                            }
                            
                                            // Aprovados do sexo Femenino
                                            

                                            if ($item['sexo'] == 'Feminino' && ($item['negativa'] > 0 && $item['negativa'] < 5)) {

                                                if($anos>3){
                                                    if ($item['sexo'] == 'Feminino') {
                                                        $t_RF = $t_RF + 1;
                                                        $RF[$anos] = $RF[$anos] + 1; 
                                                    }
                                                    }else{   
                                                        $t_APFC = $t_APFC + 1;
                                                        $total_APC = $total_APC + 1;
                                                        $AFC[$anos] = $AFC[$anos] + 1;
                                                    }
                                            }
                                    
                                        break;
                                }
                            }
                        }
                        
                    @endphp
                    @php
                        $estatistica_avalidos = $AF[$anos] + $AM[$anos] + ($AFC[$anos] + $AMC[$anos]) + $RF[$anos] + $RM[$anos];
                        $estatistica_avaliados_masculino = $AM[$anos] + $AMC[$anos] + $RM[$anos];
                        $estatistica_avaliados_femenino = $AF[$anos] + $AFC[$anos] + $RF[$anos];
                        
                        $t_AM = $t_AM + $estatistica_avaliados_masculino;
                        $t_AF = $t_AF + $estatistica_avaliados_femenino;
                        $total_AVA = $total_AVA + ($t_AF + $t_AM);
                    @endphp
                    <tr>
                        <th colspan="1" class="t-descricao">{{ $anos }}º Ano</th>
                       
                        <th class="escala-22">
                            {{ isset($total_matriculados[$anos]['masculino']) ? $total_matriculados[$anos]['masculino'] : 0 }}
                        </th>
                        <th class="escala-24">
                            {{ isset($total_matriculados[$anos]['femenino']) ? $total_matriculados[$anos]['femenino'] : 0 }}
                        </th>
                        <th class="t">
                            {{ isset($total_matriculados[$anos]['total']) ? $total_matriculados[$anos]['total'] : 0 }}</th>

                            <th class="escala-22">
                                {{ isset($estatistica_avaliados_masculino) ? $estatistica_avaliados_masculino : 0 }}
                            </th>
                            <th class="escala-22">
                                {{($total_AV)!=0? round((($estatistica_avaliados_masculino)/( $total_AV))*100,2):0}}%
                            </th>
                            <th class="escala-24">
                                {{ isset($estatistica_avaliados_femenino) ? $estatistica_avaliados_femenino : 0 }}</th>
                            <th class="escala-24">
                                {{($total_AV)!=0? round((($estatistica_avaliados_femenino)/( $total_AV))*100,2):0}}%
                            </th>
                            <th class="t">
                                {{ isset($estatistica_avalidos) ? $estatistica_avalidos : 0 }}
                            </th>
                            <th class="t">
                                {{($total_AV)!=0? round((($estatistica_avalidos)/( $total_AV))*100,2):0}}%
                            </th>
                            
                         
                        <th class="escala-22">{{ $RM[$anos] }}</th>
                        <th class="escala-22">
                            {{($total_AV)!=0? round((($RM[$anos])/( $total_AV))*100,2):0}}%

                        </th>
                        <th class="escala-24">{{ $RF[$anos] }}</th>
                        <th class="escala-24">{{($total_AV)!=0? round((($RF[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="t">{{ $RF[$anos] + $RM[$anos] }}</th>
                        <th class="t">
                           {{($total_AV)!=0? round((($RF[$anos] + $RM[$anos])/( $total_AV))*100,2):0}}%
                        </th>


                        <th class="escala-22">{{ $AMC[$anos] }}</th>
                        <th class="escala-22">{{($total_AV)!=0? round((($AMC[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="escala-24">{{ $AFC[$anos] }}</th>
                        <th class="escala-24">{{($total_AV)!=0? round((($AFC[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="t">{{ $AFC[$anos] + $AMC[$anos] }}</th>
                        <th class="t">{{($total_AV)!=0? round((($AFC[$anos] + $AMC[$anos])/( $total_AV))*100,2):0}}%</th>

                        <th class="escala-22">{{ $AM[$anos] }}</th>
                        <th class="escala-22">{{($total_AV)!=0? round((($AM[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="escala-24">{{ $AF[$anos] }}</th>
                        <th class="escala-24">{{($total_AV)!=0? round((($AF[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="t">{{ $AF[$anos] + $AM[$anos] }}</th> 
                        <th class="t">{{($total_AV)!=0? round((($AF[$anos] + $AM[$anos])/( $total_AV))*100,2):0}}%</th>

 
                        <th class="escala-22">{{ $AM[$anos] + $AMC[$anos] }}</th>
                        <th class="escala-22">{{($total_AV)!=0? round((($AM[$anos] + $AMC[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="escala-24">{{ $AF[$anos] + $AFC[$anos] }}</th> 
                        <th class="escala-24">{{($total_AV)!=0? round((($AF[$anos] + $AFC[$anos])/( $total_AV))*100,2):0}}%</th>
                        <th class="t">{{ $AF[$anos] + $AM[$anos] + ($AFC[$anos] + $AMC[$anos]) }}</th>
                        <th class="t">{{($total_AV)!=0? round((($AF[$anos] + $AM[$anos] + ($AFC[$anos] + $AMC[$anos]))/( $total_AV))*100,2):0}}%</th>


                        <th class="escala-22">
                            @if (isset($estatistica_avalidos))
                                @if ($estatistica_avalidos != 0)
                                    {{ round((($AM[$anos] + $AMC[$anos]) / $estatistica_avalidos) * 100, 2) }}%
                                @else
                                    0%
                                @endif
                            @else
                                0%
                            @endif
                        </th>
                        <th class="escala-24">
                            @if (isset($estatistica_avalidos))
                                @if ($estatistica_avalidos != 0)
                                    {{ round((($AF[$anos] + $AFC[$anos]) / $estatistica_avalidos) * 100, 2) }}%
                                @else
                                    0%
                                @endif
                            @else
                                0%
                            @endif
                        </th>
                        <th class="t">
                            @if (isset($estatistica_avalidos))
                                @if ($estatistica_avalidos != 0)
                                    {{ round((($AF[$anos] + $AM[$anos] + ($AFC[$anos] + $AMC[$anos])) / $estatistica_avalidos) * 100, 2) }}%
                                @else
                                    0%
                                @endif
                            @else
                                0%
                            @endif
                        </th>

                    </tr>
                @endforeach
                <tr>
                    <th colspan="100" style="color: transparent;font-size:1px!important;">
                </tr>
                </tr>
                <tr>
                    <th colspan="1" class="t-descricao">Total</th>
                    <th class="escala-22">{{ $t_M }}</th>
                    <th class="escala-24">{{ $t_F }}</th>
                    <th class="t">{{ $t_M + $t_F }}</th>

                    <th class="escala-22">{{ $t_AM }}</th>
                    <th class="escala-22"> {{($total_AV)!=0? round((($t_AM)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-24">{{ $t_AF }}</th>
                    <th class="escala-24">{{($total_AV)!=0? round((($t_AF)/( $total_AV))*100,2):0}}%</th>
                    <th class="t">{{ $t_AM + $t_AF }}</th>
                    <th class="t">{{($total_AV)!=0? round((($t_AM+$t_AF)/( $total_AV))*100,2):0}}%</th>

                    <th class="escala-22">{{ $t_RM }}</th>
                    <th class="escala-22">{{($total_AV)!=0? round((($t_RM)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-24">{{ $t_RF }}</th>
                    <th class="escala-24">{{($total_AV)!=0? round((($t_RF)/( $total_AV))*100,2):0}}%</th>
                    <th class="t">{{ $t_RM + $t_RF }}</th> 
                    <th class="t">{{($total_AV)!=0? round((($t_RM + $t_RF)/( $total_AV))*100,2):0}}%</th>

                    <th class="escala-22">{{ $t_APMC }}</th>
                    <th class="escala-22">{{($total_AV)!=0? round((($t_APMC)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-24">{{ $t_APFC }}</th>
                    <th class="escala-24">{{($total_AV)!=0? round((($t_APFC)/( $total_AV))*100,2):0}}%</th>
                    <th class="t">{{ $total_APC }}</th>
                    <th class="t">{{($total_AV)!=0? round((($total_APC)/( $total_AV))*100,2):0}}%</th>

                    <th class="escala-22">{{ $t_APM }}</th>
                    <th class="escala-22">{{($total_AV)!=0? round((($t_APM)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-24">{{ $t_APF }}</th>
                    <th class="escala-24">{{($total_AV)!=0? round((($t_APF)/( $total_AV))*100,2):0}}%</th>
                    <th class="t">{{ $total_APSc }}</th>
                    <th class="t">{{($total_AV)!=0? round((($total_APSc)/( $total_AV))*100,2):0}}%</th>

                    <th class="escala-22">{{ $t_APM + $t_APMC }}</th>
                    <th class="escala-22">{{($total_AV)!=0? round((($t_APMC+$t_APM)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-24">{{ $t_APF + $t_APFC }}</th>
                    <th class="escala-24">{{($total_AV)!=0? round((($t_APFC + $t_APF)/( $total_AV))*100,2):0}}%</th>
                    <th class="t">{{ $total_APSc + $total_APC }}</th>
                    <th class="t">{{($total_AV)!=0? round((($total_APSc + $total_APC)/( $total_AV))*100,2):0}}%</th>
                    <th class="escala-22">
                        @if (isset($t_AM))
                            @if ($t_AM != 0)
                                {{ round((($t_APM + $t_APMC) / ($t_AM + $t_AF)) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        @else
                            0%
                        @endif
                    </th>
                    <th class="escala-24">
                        @if (isset($t_AF))
                            @if ($t_AF != 0)
                                {{ round((($t_APF + $t_APFC) / ($t_AM + $t_AF)) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        @else
                            0%
                        @endif
                    </th>

                    <th class="t">
                        @if (isset($total_AVA))
                            @if ($total_AVA != 0)
                                {{ round((($total_APSc + $total_APC) / ($t_AM + $t_AF)) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        @else
                            0%
                        @endif
                    </th>

                </tr>
            </tbody>
        </table>


        <br>
    </main>

    <ul type="none">
        <li>C-C.A - COM CADEIRA EM ATRASO</li>
        <li>S-C.A - SEM CADEIRA EM ATRASO</li>
    </ul>
@endsection
