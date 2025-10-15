@extends('layouts.print')
@section('title',__('Relatório de candidatos'))
@section('content')

    <head>
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

            .cabecalho {
                font-family: 'Tinos' , serif;
                text-transform: uppercase;
                margin-top: 15px;
            }

            .cabecalho>*,
            .titulo>* {
                padding: 0;
                margin: 0;
                padding-top: 3px;
            }

            .cabecalho .instituition,
            .cabecalho .area,
            .titulo p {
                font-size: 1rem;
                font-weight: 700;
            }

            .cabecalho .instituition {
                font-size: 20px !important;
                letter-spacing: 1px;
                padding-bottom: 0px;
                margin-bottom: 0px;

            }

            .cabecalho .area {
                padding-top: 0px;
            }

            .cabecalho .decreto {
                font-size: 0.5rem;
                text-align: left;
                text-indent: 210px;
                padding-top: 0px;
                top: -10;

                position: relative;
            }

            .cabecalho .logotipo {
                width: 76px;
                height: 96px;
            }

            .titulo p {
                font-size: 1rem;
                font-weight: 700;
                text-transform: uppercase;
            }

            .titulo .a {
                padding-top: 30px;
                padding-bottom: 5px;
            }

            .table_te {


                width: 76%;
                margin-left: 12%;
                text-align: right;
                font-family: calibri light;
                margin-bottom: 6px;

            }
            
         
            .cor_linha {
                background-color: #999;
                color: #000;
            }



            .table_te th,
            .table_te td {
                border: 1px solid #fff;
                background-color: #F9F2F4;

                /*padding: 1px !important;*/
            }

            .last-line td {
                background-color: #cecece;
            }

            .line td {
                background-color: #ebebeb;
            }

            .tabble_te thead {}



            .assinaturas p,
            .data {
                font-size: 22px;
            }

            .data,
            .assinaturas {
                margin-left: 12%;
                text-align: right;
                margin-right: 100px;
                margin-top: 350px;
            }

            .bg0,
            .div-top {
                background-color: #2f5496 !important;
                color: white;
            }

            .div-top>* {
                color: white !important;
            }

            .bg1 {
                background-color: #8eaadb !important;
            }

            .bg2 {
                background-color: #d9e2f3 !important;
            }

            .bg3 {
                background-color: #fbe4d5 !important;
            }

            .bg4 {
                background-color: #f4b083 !important;
            }


            .f1 {
                font-size: 13pt !important;
            }

            .f2 {
                font-size: 12pt !important;
            }

            .f3 {
                font-size: 11pt !important;
            }

            .f4 {
                font-size: 10pt !important;
            }

            .pd {
                width: 60px;

            }

            .pd1 {
                width: 70px;
            }

            .strange {
                color: #1c65e5;
            }

            .title-dom {
                font-size: 80px;
                font-weight: bold;
                text-align: left;
                margin-top: 35px;
                margin-left: 100px;
                margin-bottom: 5px;
                color: #243f60;
            }

            .title-dom1 {
                font-weight: none;
                font-size: 40px;
                color: #243f60;
                margin-left: 100px;
                margin-bottom: 70px;
                text-transform: UPPERCASE;
            }

            .title-dom2 {
                font-weight: none;
                font-size: 32px;
                color: #243f60;
                margin-left: 100px;
            }


            .logotipo img {
                width: 400px;
                height: 400px;
                margin-top: 90px!important;
                margin-bottom: 20px!important;
            }
            .f-blue{
                color:#243f60!important;
            }
            .t-color{
                color:#fc8a17;
            }
            .tr-new-line th{
                font-weight:none;
            }
            
            .margin-new{
                margin-top: 9%;
            }
            
            .row {
                margin-top: -10px!important;
            }
            
            .page-break {
               page-break-after: always;
            }
        </style>
    </head>
    <main>



        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
            <div class="">
                
                <!-- personalName -->

                <div class="row">

                    <br>
                    <div class="col-12" style="">

                        <br>
                        <center>
                            @php $url = "https://" . $_SERVER['HTTP_HOST'] . "/instituicao-arquivo/" . $institution->logotipo; @endphp
                            <div class="logotipo" style="margin-top:30px;">
                                <img src="{{ $url }}" class="" srcset="">
                            </div>
                        </center>
                        <h3 class="title-dom"> <br>

                            <br>
                            RELATÓRIO:



                        </h3>

                        <h4 class="title-dom1">
                           <titles class="t-color"> Evolução das
                            candidaturas</titles>
                            <br>

                        </h4>
                        <h4 class="title-dom2">
                            <b>Ano lectivo:</b> <titles class="t-color">{{ $lectiveYears[0]->currentTranslation->display_name }}</titles>
                            <div style="height:10px;color:white;">f</div>
                            <b>Fase:</b> <titles class="t-color">{{ $lectiveFase->fase }}ª</titles>
                        </h4>
                        <br>
                        <br>
                        <div class="data">

                            <as style="text-transform: capitalize;"> {{ $institution->municipio }}</as>,
                            aos
                            @php
                                $m = date('m');
                                $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                                echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
                            @endphp



                            <br>
                            <titles class="t-color">Powered by</titles> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
                        </div>

                    </div>
                    <br>
                    <div class="col-12" style="margin-top: 1100px;height: 500px;width:200px;color:white;">
                        asa
                    </div>

                </div>
                
                <div class="row">


                    <div class="col-12">
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                @endphp
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <table class="table_te margin-new" style=" width: 93%;margin-left:4%;margin-right:0;">
                                    <br>
                                     <tr class="line">
                                            <td colspan="19"
                                                class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color">
                                                    <titles class="f-blue">Quadro 1:</titles> Vagas vs Candidatos
                                               </td>
                                        </tr>
                                    @php
                                        
                                        $m = 0;
                                        $money = 0;
                                        $t = 0;
                                        $n = 0;
                                        $m_c = 0;
                                        $t_c = 0;
                                        $n_c = 0;
                                    @endphp
                                    @foreach ($vagas as $key => $item)
                                        @php
                                            $i = 1;
                                            $m_p = 0;
                                            $t_p = 0;
                                            $n_p = 0;
                                            $m_p_c = 0;
                                            $t_p_c = 0;
                                            $n_p_c = 0;
                                        @endphp

                                        <tr class="line">
                                            <td colspan="19"
                                                class="text-left text-white bg0 text-uppercase font-weight-bold f1" >
                                                {{ $key }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-center bg-white " style="vertical-align:bottom;" rowspan="3">
                                            </th>
                                            <th class="text-center bg-white " style="vertical-align:bottom;"></th>
                                            <th class="text-center bg-white font-weight-bold f2" colspan="17" style="color:white;font-size:1px!important">Vagas vs
                                                Candidatos</th>

                                        </tr>
                                        <tr>
                                          
                                            <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;"
                                                rowspan="3">
                                                CURSOS</th>
                                            <th class="text-center bg1 font-weight-bold f3 pd" style=""
                                                colspan="4">M</th>
                                            <th class="text-center bg1 font-weight-bold f3 pd" style=""
                                                colspan="4">T</th>
                                            <th class="text-center bg1 font-weight-bold f3 pd" style=""
                                                colspan="4">N</th>
                                            <th class="text-center bg1 font-weight-bold f3 pd1" style=""
                                                colspan="5">Total</th>


                                        </tr>
                                        <tr class="tr-new-line">
 
                                            <th class="text-center bg1  f3 pd" style="">V</th>
                                            <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                                            <th class="text-center bg1  f3 pd" style="">V</th>
                                            <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                                            <th class="text-center bg1  f3 pd" style="">V</th>
                                            <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                                            <th class="text-center bg1  f3 pd" style="">V</th>
                                            <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                                            <th class="text-center bg1  f3 pd" style="">%</th>

                                        </tr>
                                        <tr class="tr-new-line" style="font-weight:bold">
 
                                            <td class="text-center bg1  f3 pd " style="">#</td>
                                            <td class="text-center bg1  f3 pd" style="" ></td>
                                            <td class="text-center bg1  f3 pd" style="">Total</td>
                                             <td class="text-center bg1  f3 pd" style="">m</td>
                                              <td class="text-center bg1  f3 pd" style="">f</td>
                                             
                                              <td class="text-center bg1  f3 pd" style=""></td>
                                            <td class="text-center bg1  f3 pd" style="" >Total</td>
                                            <td class="text-center bg1  f3 pd" style="">m</td>
                                             <td class="text-center bg1  f3 pd" style="">f</td>
                                             
                                              <td class="text-center bg1  f3 pd" style=""></td>
                                            <td class="text-center bg1  f3 pd" style="" >Total</td>
                                            <td class="text-center bg1  f3 pd" style="">m</td>
                                             <td class="text-center bg1  f3 pd" style="">f</td>
                                             
                                              <td class="text-center bg1  f3 pd" style=""></td>
                                            <td class="text-center bg1  f3 pd" style="" >Total</td>
                                            <td class="text-center bg1  f3 pd" style="">m</td>
                                             <td class="text-center bg1  f3 pd" style="">f</td>
                                             
                                              <td class="text-center bg1  f3 pd" style=""></td>
                                        

                                        </tr>
                                        @foreach ($item as $item_vagas)
                                            <tr class="f2">
                                                <td class="text-center bg2 ">{{ $i++ }}</td>
                                                <td class="text-left bg2 text-uppercase " style="width:350px">{{ $item_vagas->display_name }}
                                                </td>
                                                <td class="text-center bg2 ">
                                                    {{ $item_vagas->manha != 0 ? $item_vagas->manha : '-' }}</td>
                                                <td class="text-center bg2 t-color ">
                                                    {{ $candidatos[$item_vagas->courses_id]['manha'] ?? "-" }}</td>
                                                <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['sm']['m'] ?? "-" }}</td>
                                                <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['sm']['f'] ?? "-" }}</td>
                                                <td class="text-center bg2 ">
                                                    {{ $item_vagas->tarde != 0 ? $item_vagas->tarde : '-' }}</td>
                                                <td class="text-center bg2 t-color">
                                                    {{ $candidatos[$item_vagas->courses_id]['tarde'] ?? '-' }}</td>
                                                    <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['st']['m'] ?? "-" }}</td>
                                                <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['st']['f'] ?? "-" }}</td>
                                                <td class="text-center bg2 ">
                                                    {{ $item_vagas->noite != 0 ? $item_vagas->noite : '-' }}</td>
                                                <td class="text-center bg2 t-color">
                                                    {{ $candidatos[$item_vagas->courses_id]['noite'] ?? '-'}}</td>
                                                <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['sn']['m'] ?? "-" }}</td>
                                                <td class="text-center bg2 t-color ">{{ $candidatos[$item_vagas->courses_id]['sn']['f'] ?? "-" }}</td>
                                                <td class="text-center bg2 ">
                                                    {{ $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha != 0 ? $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha : '-' }}
                                                </td>
                                                <td class="text-center bg2 t-color">{{ $candidatos[$item_vagas->courses_id]['total'] ?? '-' }}</td>
                                                <td class="text-center bg2 t-color">{{ $candidatos[$item_vagas->courses_id]['sn']['m'] +  $candidatos[$item_vagas->courses_id]['st']['m'] + $candidatos[$item_vagas->courses_id]['sm']['m'] ?? "-" }}</td>
                                                <td class="text-center bg2 t-color">{{ $candidatos[$item_vagas->courses_id]['sn']['f'] +  $candidatos[$item_vagas->courses_id]['st']['f'] + $candidatos[$item_vagas->courses_id]['sm']['f'] ?? "-" }}</td>
                                                <td class="text-center bg2 ">
                                                    @if (($candidatos[$item_vagas->courses_id]['total'] ?? 0) != 0)
                                                        {{ (int) round(($candidatos[$item_vagas->courses_id]['total'] / ($item_vagas->noite + $item_vagas->tarde + $item_vagas->manha)) * 100, 0) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                // Total para o número de candidatos
                                                
                                                $m_c += ($candidatos[$item_vagas->courses_id]['manha'] ?? 0);
                                                $money = ($candidatos[$item_vagas->courses_id]['money'] ?? 0);
                                                $t_c += ($candidatos[$item_vagas->courses_id]['tarde'] ?? 0);
                                                $n_c += ($candidatos[$item_vagas->courses_id]['noite'] ?? 0);
                                                $m_p_c += ($candidatos[$item_vagas->courses_id]['manha'] ?? 0);
                                                $t_p_c += ($candidatos[$item_vagas->courses_id]['tarde'] ?? 0);
                                                $n_p_c += ($candidatos[$item_vagas->courses_id]['noite'] ?? 0);
                                                
                                                // Total para o número de vagas
                                                
                                                $m += $item_vagas->manha;
                                                $t += $item_vagas->tarde;
                                                $n += $item_vagas->noite;
                                                $m_p += $item_vagas->manha;
                                                $t_p += $item_vagas->tarde;
                                                $n_p += $item_vagas->noite;
                                                
                                            @endphp
                                        @endforeach
                                        <tr class="last-line font-weight-bold">
                                            <td class="bg-white f4" colspan="2"><b>SUB-TOTAL</b></td>

                                            <td class="text-center bg3 f3">{{ $m_p }}</td>
                                            <td class="text-center bg3 f3 t-color" colspan="3">{{ $m_p_c }}</td>

                                            <td class="text-center bg3 f3">{{ $t_p }}</td>
                                            <td class="text-center bg3 f3 t-color" colspan="3">{{ $t_p_c }}</td>

                                            <td class="text-center bg3 f3">{{ $n_p }}</td>
                                            <td class="text-center bg3 f3 t-color" colspan="3">{{ $n_p_c }}</td>

                                            <td class="text-center bg3 f3">{{ $m_p + $t_p + $n_p }}</td>
                                            <td class="text-center bg3 f3 t-color" colspan="3">{{ $m_p_c + $t_p_c + $n_p_c }}</td>
                                            <td class="text-center  f3 bg-white"></td>

                                        </tr>
                                        <tr>
                                            <td class="bg-white" style="color: white;font-size: 15px;">as</td>
                                        </tr>
                                        <!-- @if($loop->iteration == 5)-->
                                       
                                        <!--@for($i = 0; $i < 40; $i++)-->
                                        <!-- <tr>-->
                                        <!--    <td class="bg-white"></td>-->
                                        <!--</tr>-->
                                        <!--@endfor-->
                                        <!--@endif-->
                                    @endforeach
                                    <tr>
                                        <td class="bg-white"></td>
                                    </tr>
                                    <tr class="last-line">
                                        <td class="bg-white f1" colspan="2"><b>TOTAL</b></td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $m }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold t-color" colspan="3">{{ $m_c }}</td>

                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold t-color" colspan="3">{{ $t_c }}</td>

                                        <td class="text-center bg4 f3 font-weight-bold">{{ $n }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold t-color" colspan="3">{{ $n_c }}</td>

                                        <td class="text-center bg4 f3 font-weight-bold strange">{{ $m + $t + $n }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold strange" colspan="3">{{ $m_c + $t_c + $n_c }}
                                        </td>
                                        <td class="text-center bg4 f3 font-weight-bold strange ">
                                            @if ($m_c + $t_c + $n_c != 0)
                                                {{ (int) round((($m_c + $t_c + $n_c) / ($m + $t + $n)) * 100, 0) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>

                                    </tr>
                                </table>

                                <br>
                                <div class="row margin-new">
                                    <div class="col-5">
                                        
                                        <table class="table_te ta"
                                            style="margin-left: 10%!important;page-break-before: always; width: 80%!important">
                                            <thead>
                                                 @for ($i = 0; $i < 20; $i++)
                                  <tr>
                                         <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                                          colspan="4"></th>
                                    </tr>    
                                    @endfor
                                                
                                                <tr>
                                                    <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                                                        colspan="4"><titles class="f-blue">Quadro 2:</titles> candidaturas por dia</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left bg1 font-weight-bold f3 pd" style="width: 20px;text-align: center;">#</th>
                                                    <th class="text-left bg1 font-weight-bold f3 pd">Dia</th>
                                                    <th class="text-center bg1 font-weight-bold f3 pd">Novos</th>
                                                    <th class="text-center bg1 font-weight-bold f3 pd">Acumulados</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_data = 0;
                                                    $a = 1;
                                                @endphp

                                                @foreach ($datas_inscricao as $key => $item)
                                                    @php
                                                        $total_data = $total_data + $item;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-left bg2 f3">{{ $a++ }}</td>
                                                        <td class="text-left bg2 f3">{{ $key }}</td>
                                                        <td class="text-center bg2 f3">{{ $item }}</td>
                                                        <td class="text-center bg2 f3">
                                                            {{ ($total_data) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="col-8">

                                        <table class="table_te ta"
                                            style="left: -1;margin:0%!important;page-break-before: always;">
                                            <thead>
                                                
                                                 @for ($i = 0; $i < 20; $i++)
                                                    <tr>
                                                        <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                                                            colspan="3"></th>
                                                    </tr>    
                                                @endfor
                                                <tr>
                                                    <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                                                        colspan="3"><titles class="f-blue">Quadro 3:</titles> candidaturas por Staff</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-left bg1 font-weight-bold f3 pd" style="width: 20px!IMPORTANT">#</th>
                                                    <th class="text-left bg1 font-weight-bold f3 pd">STAFF CANDIDATURAS                                                    </th>
                                                    <th class="text-center bg1 font-weight-bold f3 pd">Nº</th>
                                                    <th class="text-center bg1 font-weight-bold f3 pd">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @php
                                                    $total_staff = 0;
                                                    $total_cd = 0;
                                                    $a = 1;
                                                    
                                                    foreach ($staff as $item) {
                                                        $total_staff = $total_staff + $item['inscricao'];
                                                        $total_cd = $total_cd + $item['inscricao'];
                                                    }
                                                    
                                                @endphp

                                                @foreach ($staff as $key => $item)
                                                    <tr>
                                                        <td class="text-left bg2 f3">{{ $a++ }}</td>
                                                        <td class="text-left bg2 f3">{{ $key }}</td>
                                                        <td class="text-center bg2 f3">{{ $item['inscricao'] }}</td>
                                                        <td class="text-center bg2 f3">
                                                            @if ($item['inscricao'])
                                                                {{ round(($item['inscricao'] / $total_staff) * 100, 2) }}%
                                                            @else
                                                                0%
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>

                                                    <td class="bg-white f1"></td>
                                                    <td class="bg-white f1"><b>TOTAL</b></td>
                                                    <td class="text-center bg4 f3 font-weight-bold">{{ ($total_staff) }}
                                                    </td>
                                                    <td class="text-center bg4 f3 font-weight-bold">
                                                        @if ($total_staff)
                                                            {{ (int) round(($total_staff / $total_cd) * 100, 0) }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                @php
                                    $lancada = 0;
                                    $lancada_t = 0;
                                    $liquidada = 0;
                                    $liquidada_t = 0;
                                    $value_base = 0;
                                    foreach ($emolumentos as $key => $item) {
                                        if ($key == 'total') {
                                            $liquidada = $item['total_valor'] + $liquidada;
                                            $liquidada_t = $item['total'];
                                        }
                                        $value_base = $item['value'] != 0 ? $item['value'] : 0;
                                        $lancada = $item['total_valor'] + $lancada;
                                        $lancada_t = $lancada_t + $item['total'];
                                    }
                                     
                                @endphp
                                <br>
                                <div class="row">
                                    <div class="col-5">
                                        <table class="table_te ta" style="margin-left: 8%!important;width:88%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 "
                                                       >#</th>
                                                    <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                                                      style="font-size: 18px!important;"  colspan="3"><titles class="f-blue">Quadro 4:</titles> Resumo das candidaturas</th>
                                                </tr>
                                                <tr>

                                                </tr>
                                            </thead> 
                                            @php
                                                $p_total = isset($emolumentos_espera['pending']['total'])?$emolumentos_espera['pending']['total']:0;
                                                $c_iniciadas = $todos_candidatos + 0;
                                                $c_eliminadas = $todos_candidatos - $total_staff;
                                                $e_eliminadas = $p_total;
                                                $e_lancados =  isset($emolumentos['total'])?$emolumentos['total']:0;
                                                
                                               
                                                $total = isset($emolumentos['total'])?$emolumentos['total']:0;
                                                $pending = isset($emolumentos['pending'])?$emolumentos['pending']:0;
                                                $total_money = isset($emolumentos['total_money'])?$emolumentos['total_money']:0;
                                                $pending_money = isset($emolumentos['espera_money'])?$emolumentos['espera_money']:0;
                                                
                                            @endphp
                                            <tbody>
                                                <tr>
                                                    <td class="text-left bg2 f3">1</td>
                                                    <td class="text-left bg2 f3">Candidaturas INICIADAS</td>
                                                    <td class="text-center bg2 f3">{{ $c_iniciadas }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left bg2 f3">2</td>
                                                    <td class="text-left bg2 f3">Candidaturas CONCLUIDAS</td>
                                                    <td class="text-center bg2 f3">{{ ($total+$pending) }}</td>
                                                </tr>
                                                 <tr>
                                                    <td class="text-left bg2 f3">2</td>
                                                    <td class="text-left bg2 f3">Candidaturas NÃO CONCLUIDAS</td>
                                                    <td class="text-center bg2 f3">{{ ($c_iniciadas - ($total+$pending)) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left bg2 f3">3</td>
                                                    <td class="text-left bg2 f3">Candidaturas PAGAS</td>
                                                    <td class="text-center bg2 f3">{{ $total }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left bg2 f3">4</td>
                                                    <td class="text-left bg2 f3">Candidaturas ELIMINADAS</td>
                                                    <td class="text-center bg2 f3">{{ $c_eliminadas }}</td>
                                                </tr>
                                               
                                                <tr>
                                                    <td class="text-left bg2 f3">6</td>
                                                    <td class="text-left bg2 f3">Candidaturas POR 2 CURSOS</td>
                                                    <td class="text-center bg2 f3"></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="col-6">
                                        <table class="table_te ta" style="left: -20;margin:0%!important;">
                                            <thead>
                                                <tr>
                                                    <th class="text-left  bg-white font-weight-bold f1">#</th>
                                                    <th class="text-left  bg-white  text-uppercase font-weight-bold f1 t-color"
                                                        colspan="3"><titles class="f-blue">Quadro 5:</titles> Resumo financeiro</th>
                                                </tr>
                                                <tr>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-left bg2 f3">1</td>
                                                    <td class="text-left bg2 f3">Emolumentos LANÇADOS</td>
                                                    <td class="text-center bg2 f3">{{ ($total +  $pending)  }}</td>
                                                    <td class="text-center bg2 f3">

                                                        {{ number_format(($total_money+$pending_money), 2, ',', '.') . ' kz' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left bg2 f3">2</td>
                                                    <td class="text-left bg2 f3">Emolumentos LIQUIDADOS</td>
                                                    <td class="text-center bg2 f3">{{ $total }}</td>
                                                    <td class="text-center bg2 f3">
                                                        {{ number_format($total_money, 2, ',', '.') . ' kz' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left bg2 f3">3</td>
                                                    <td class="text-left bg2 f3">Emolumentos EM ESPERA</td>
                                                    <td class="text-center bg2 f3">{{ $pending }}</td>
                                                    <td class="text-center bg2 f3">
                                                        {{ number_format($pending_money, 2, ',', '.') . ' kz' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <br>
    <br>

    {{-- <div class="assinaturas">
        <p><b> O(A) Vice-Director(a) académico:</b></p>
        <p>______________________________</p>
        <p>{{ $cordenador }}/ MSc</p>
    </div> --}}
@endsection
