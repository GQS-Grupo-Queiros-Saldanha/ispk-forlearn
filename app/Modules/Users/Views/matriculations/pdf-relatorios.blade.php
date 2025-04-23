@extends('layouts.print')
@section('title', __('Relatório de confirmação de matrículas'))
@section('content')

<head>

  <style>
    @import url('https://fonts.cdnfonts.com/css/times-new-roman');

    .cabecalho {
      font-family: 'Times New Roman';
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

    .tabble_te #table_ti {
      display: table-row-group;
    }


    .cor_linha {
      background-color: #999;
      color: #000;
    }



    .table_te th,
    .table_te td {
      border: 1px solid #fff;
      background-color: #F9F2F4;

      padding: 3px !important;
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

    .bg6 {
      background-color: #F7E6A0 !important;
    }

    .bg7 {
      background-color: #FEF4D1 !important;
    }

    .bg8 {
      background-color: #FEBD02 !important;
    }


    .f1 {
      font-size: 14pt !important;
    }

    .f2 {
      font-size: 11pt !important;
    }

    .f3 {
      font-size: 12pt !important;
    }

    .f4 {
      font-size: 11pt !important;
    }

    .pd {
      width: 64px;

    }

    .w32 {
      width: 32px;
    }

    .w16 {
      width: 25px;
      !important
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
      margin-top: 90px !important;
      margin-bottom: 20px !important;
    }

    .f-blue {
      color: #243f60 !important;
    }

    .t-color {
      color: #fc8a17;
    }

    .tr-new-line th {
      font-weight: none;
    }

    .margin-new {
      margin-top: 9%;
    }

    .row {
      margin-top: -10px !important;
    }

    .ng {
      font-weight: bold !important;
    }

    .page-break {
      page-break-before: always;
    }

    table {
      page-break-inside: avoid;
    }

    tr {
      page-break-inside: avoid;
      page-break-after: auto;
    }

    .bg-admitidos {
      background-color: #BBD8F0 !important;
    }

    @media print {
      .page-break {
        page-break-before: always;
      }
    }
  </style>
</head>
<main>



  <!-- aqui termina o cabeçalho do pdf -->
  <div class="">
    <div class="">

      <!-- personalName -->

      <div class="row" style="margin-bottom:300px!important">

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
            <titles class="t-color"> Confirmação de matrículas</titles>
            <br>

          </h4>
          <h4 class="title-dom2">
            <b>Ano lectivo:</b>
            <titles class="t-color">{{ $lt->display_name }}</titles>
            <div style="height:10px;color:white;">f</div>

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
            <titles class="t-color">Powered by</titles> <b
              style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
          </div>

        </div>
        <br>
        <div class="col-12" style="margin-top: 1100px;height: 500px;width:200px;color:white;">

        </div>

      </div>


      <div class="row">
        <div class="col-12" style="margin-top:10px!important">
          <div class="">
            <div class="">
              @php
        $i = 1;
        @endphp





              @php
        $total_turno_M = 0;
        $total_turno_T = 0;
        $total_turno_N = 0;
        $total_turno_manha_1 = 0;
        $total_turno_manha_2 = 0;
        $total_turno_manha_3 = 0;
        $total_turno_manha_4 = 0;
        $total_turno_manha_5 = 0;
        $total_turno_tarde_1 = 0;
        $total_turno_tarde_2 = 0;
        $total_turno_tarde_3 = 0;
        $total_turno_tarde_4 = 0;
        $total_turno_tarde_5 = 0;
        $total_turno_noite_1 = 0;
        $total_turno_noite_2 = 0;
        $total_turno_noite_3 = 0;
        $total_turno_noite_4 = 0;
        $total_turno_noite_5 = 0;
        $t1 = 0;
        $t2 = 0;
        $t3 = 0;
        $t4 = 0;
        $t5 = 0;
        $total_turno_M_geral = 0;
        $total_turno_T_geral = 0;
        $total_turno_N_geral = 0;
        $total_turno_manha_1_global = 0;
        $total_turno_manha_2_global = 0;
        $total_turno_manha_3_global = 0;
        $total_turno_manha_4_global = 0;
        $total_turno_manha_5_global = 0;
        $total_turno_tarde_1_global = 0;
        $total_turno_tarde_2_global = 0;
        $total_turno_tarde_3_global = 0;
        $total_turno_tarde_4_global = 0;
        $total_turno_tarde_5_global = 0;
        $total_turno_noite_1_global = 0;
        $total_turno_noite_2_global = 0;
        $total_turno_noite_3_global = 0;
        $total_turno_noite_4_global = 0;
        $total_turno_noite_5_global = 0;
      @endphp


              @php
        $i = 1;
        $m_p = 0;
        $t_p = 0;
        $n_p = 0;
        @endphp
              @foreach ($departamentos as $x => $val)
                <table class="table_te" style="width:90%;margin-left:60px;">
                <tr class="line">
                  <td colspan="23" class="text-left text-white bg0 text-uppercase font-weight-bold f1"> {{ $x }}</td>
                </tr>
                @php
            $total_turno_manha_1_geral = 0;
            $total_turno_manha_2_geral = 0;
            $total_turno_manha_3_geral = 0;
            $total_turno_manha_4_geral = 0;
            $total_turno_manha_5_geral = 0;
            $total_turno_tarde_1_geral = 0;
            $total_turno_tarde_2_geral = 0;
            $total_turno_tarde_3_geral = 0;
            $total_turno_tarde_4_geral = 0;
            $total_turno_tarde_5_geral = 0;
            $total_turno_noite_1_geral = 0;
            $total_turno_noite_2_geral = 0;
            $total_turno_noite_3_geral = 0;
            $total_turno_noite_4_geral = 0;
            $total_turno_noite_5_geral = 0;
          @endphp
                <tr>
                  <th class="text-center bg1 font-weight-bold f2" rowspan="3">#</th>
                  <th class="text-center bg1 font-weight-bold f2" rowspan="3">CURSOS</th>

                  <th class="text-center bg1 font-weight-bold f2" colspan="20">Ano de frequência</th>
                  <th class="text-center bg1 font-weight-bold f2" rowspan="2">Total<br>do<br>Curso</th>

                </tr>
                <tr>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">1º</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">2º</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">3º</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">4º</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">5º</th>

                </tr>

                <tr>
                  <th class="text-center bg1 font-weight-bold f3 w32" style="" colspan="2">Total</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">m</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">f</th>
                  <th class="text-center bg1 font-weight-bold f3 w32" style="" colspan="2">Total</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">m</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">f</th>
                  <th class="text-center bg1 font-weight-bold f3 w32" style="" colspan="2">Total</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">m</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">f</th>
                  <th class="text-center bg1 font-weight-bold f3 w32" style="" colspan="2">Total</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">m</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">f</th>
                  <th class="text-center bg1 font-weight-bold f3 w32" style="" colspan="2">Total</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">m</th>
                  <th class="text-center bg1 font-weight-bold f3 w16" style="">f</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="">&nbsp;</th>

                </tr>
                @php
            $sub_t1_geral = 0;
            $sub_t2_geral = 0;
            $sub_t3_geral = 0;
            $sub_t4_geral = 0;
            $sub_t5_geral = 0;
        @endphp
                @foreach ($val as $key => $item)
              @php
        $sub_t1 = 0;
        $sub_t2 = 0;
        $sub_t3 = 0;
        $sub_t4 = 0;
        $sub_t5 = 0;
      @endphp
              <tr class="f2">
                <td class="text-center bg2" rowspan="3" style="width:25px!important">{{ $i++ }}</td>
                <td class="text-left bg2 text-uppercase" rowspan="3">{{ $item["curso"] }}</td>

                <td class="text-center bg2 w16 ng">M</td>
                <td class="text-center bg6 w16 ng">{{ $item["r1"]["m"] != 0 ? $item["r1"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["sm"]["m"] != 0 ? $item["r1"]["sm"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["sm"]["f"] != 0 ? $item["r1"]["sm"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">M</td>
                <td class="text-center bg6 w16 ng">{{ $item["r2"]["m"] != 0 ? $item["r2"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["sm"]["m"] != 0 ? $item["r2"]["sm"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["sm"]["f"] != 0 ? $item["r2"]["sm"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">M</td>
                <td class="text-center bg6 w16 ng">{{ $item["r3"]["m"] != 0 ? $item["r3"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["sm"]["m"] != 0 ? $item["r3"]["sm"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["sm"]["f"] != 0 ? $item["r3"]["sm"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">M</td>
                <td class="text-center bg6 w16 ng">{{ $item["r4"]["m"] != 0 ? $item["r4"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["sm"]["m"] != 0 ? $item["r4"]["sm"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["sm"]["f"] != 0 ? $item["r4"]["sm"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">M</td>
                <td class="text-center bg6 w16 ng">{{ $item["r5"]["m"] != 0 ? $item["r5"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["sm"]["m"] != 0 ? $item["r5"]["sm"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["sm"]["f"] != 0 ? $item["r5"]["sm"]["f"] : '' }}</td>

                <td class="text-center bg3 ng">
                {{ $total_M = $item["r1"]["m"] + $item["r2"]["m"] + $item["r3"]["m"] + $item["r4"]["m"] + $item["r5"]["m"] }}
                </td>
                @php
          $total_turno_M = $total_M;
          $total_turno_manha_1 = $item["r1"]["m"];
          $total_turno_manha_2 = $item["r2"]["m"];
          $total_turno_manha_3 = $item["r3"]["m"];
          $total_turno_manha_4 = $item["r4"]["m"];
          $total_turno_manha_5 = $item["r5"]["m"];

          $total_turno_M_geral += $total_M;
          $total_turno_manha_1_geral += $item["r1"]["m"];
          $total_turno_manha_2_geral += $item["r2"]["m"];
          $total_turno_manha_3_geral += $item["r3"]["m"];
          $total_turno_manha_4_geral += $item["r4"]["m"];
          $total_turno_manha_5_geral += $item["r5"]["m"];

          $total_turno_manha_1_global += $item["r1"]["m"];
          $total_turno_manha_2_global += $item["r2"]["m"];
          $total_turno_manha_3_global += $item["r3"]["m"];
          $total_turno_manha_4_global += $item["r4"]["m"];
          $total_turno_manha_5_global += $item["r5"]["m"];
          @endphp
              </tr>

              <tr class="f2">
                <td class="text-center bg2 w16 ng">T</td>
                <td class="text-center bg6 w16 ng">{{ $item["r1"]["t"] != 0 ? $item["r1"]["t"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["st"]["m"] != 0 ? $item["r1"]["st"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["st"]["f"] != 0 ? $item["r1"]["st"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">T</td>
                <td class="text-center bg6 w16 ng">{{ $item["r2"]["t"] != 0 ? $item["r2"]["t"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["st"]["m"] != 0 ? $item["r2"]["st"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["st"]["f"] != 0 ? $item["r2"]["st"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">T</td>
                <td class="text-center bg6 w16 ng">{{ $item["r3"]["t"] != 0 ? $item["r3"]["t"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["st"]["m"] != 0 ? $item["r3"]["st"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["st"]["f"] != 0 ? $item["r3"]["st"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">T</td>
                <td class="text-center bg6 w16 ng">{{ $item["r4"]["t"] != 0 ? $item["r4"]["t"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["st"]["m"] != 0 ? $item["r4"]["st"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["st"]["f"] != 0 ? $item["r4"]["st"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">T</td>
                <td class="text-center bg6 w16 ng">{{ $item["r5"]["t"] != 0 ? $item["r5"]["t"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["st"]["m"] != 0 ? $item["r5"]["st"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["st"]["f"] != 0 ? $item["r5"]["st"]["f"] : '' }}</td>

                <td class="text-center bg3 ng">
                {{$total_T = $item["r1"]["t"] + $item["r2"]["t"] + $item["r3"]["t"] + $item["r4"]["t"] + $item["r5"]["t"] }}
                </td>
                @php
          $total_turno_T = $total_T;
          $total_turno_tarde_1 = $item["r1"]["t"];
          $total_turno_tarde_2 = $item["r2"]["t"];
          $total_turno_tarde_3 = $item["r3"]["t"];
          $total_turno_tarde_4 = $item["r4"]["t"];
          $total_turno_tarde_5 = $item["r5"]["t"];

          $total_turno_T_geral += $total_T;
          $total_turno_tarde_1_geral += $item["r1"]["t"];
          $total_turno_tarde_2_geral += $item["r2"]["t"];
          $total_turno_tarde_3_geral += $item["r3"]["t"];
          $total_turno_tarde_4_geral += $item["r4"]["t"];
          $total_turno_tarde_5_geral += $item["r5"]["t"];

          $total_turno_tarde_1_global += $item["r1"]["t"];
          $total_turno_tarde_2_global += $item["r2"]["t"];
          $total_turno_tarde_3_global += $item["r3"]["t"];
          $total_turno_tarde_4_global += $item["r4"]["t"];
          $total_turno_tarde_5_global += $item["r5"]["t"];
          @endphp
              </tr>


              <tr class="f2">
                <td class="text-center bg2 w16 ng">N</td>
                <td class="text-center bg6 w16 ng">{{ $item["r1"]["n"] != 0 ? $item["r1"]["n"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["sn"]["m"] != 0 ? $item["r1"]["sn"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r1"]["sn"]["f"] != 0 ? $item["r1"]["sn"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">N</td>
                <td class="text-center bg6 w16 ng">{{ $item["r2"]["n"] != 0 ? $item["r2"]["n"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["sn"]["m"] != 0 ? $item["r2"]["sn"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r2"]["sn"]["f"] != 0 ? $item["r2"]["sn"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">N</td>
                <td class="text-center bg6 w16 ng">{{ $item["r3"]["n"] != 0 ? $item["r3"]["n"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["sn"]["m"] != 0 ? $item["r3"]["sn"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r3"]["sn"]["f"] != 0 ? $item["r3"]["sn"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">N</td>
                <td class="text-center bg6 w16 ng">{{ $item["r4"]["n"] != 0 ? $item["r4"]["n"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["sn"]["m"] != 0 ? $item["r4"]["sn"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r4"]["sn"]["f"] != 0 ? $item["r4"]["sn"]["f"] : '' }}</td>

                <td class="text-center bg2 w16 ng">N</td>
                <td class="text-center bg6 w16 ng">{{ $item["r5"]["n"] != 0 ? $item["r5"]["n"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["sn"]["m"] != 0 ? $item["r5"]["sn"]["m"] : '' }}</td>
                <td class="text-center bg7 w16">{{ $item["r5"]["sn"]["f"] != 0 ? $item["r5"]["sn"]["f"] : '' }}</td>

                <td class="text-center bg3 ng">
                {{ $total_N = $item["r1"]["n"] + $item["r2"]["n"] + $item["r3"]["n"] + $item["r4"]["n"] + $item["r5"]["n"] }}
                </td>
                @php
          $total_turno_N = $total_N;
          $total_turno_noite_1 = $item["r1"]["n"];
          $total_turno_noite_2 = $item["r2"]["n"];
          $total_turno_noite_3 = $item["r3"]["n"];
          $total_turno_noite_4 = $item["r4"]["n"];
          $total_turno_noite_5 = $item["r5"]["n"];

          $total_turno_N_geral += $total_N;
          $total_turno_noite_1_geral += $item["r1"]["n"];
          $total_turno_noite_2_geral += $item["r2"]["n"];
          $total_turno_noite_3_geral += $item["r3"]["n"];
          $total_turno_noite_4_geral += $item["r4"]["n"];
          $total_turno_noite_5_geral += $item["r5"]["n"];

          $total_turno_noite_1_global += $item["r1"]["n"];
          $total_turno_noite_2_global += $item["r2"]["n"];
          $total_turno_noite_3_global += $item["r3"]["n"];
          $total_turno_noite_4_global += $item["r4"]["n"];
          $total_turno_noite_5_global += $item["r5"]["n"];
          @endphp
              </tr>

              @php
        $sub_t1_geral = $total_turno_manha_1_geral + $total_turno_tarde_1_geral + $total_turno_noite_1_geral;
        $sub_t2_geral = $total_turno_manha_2_geral + $total_turno_tarde_2_geral + $total_turno_noite_2_geral;
        $sub_t3_geral = $total_turno_manha_3_geral + $total_turno_tarde_3_geral + $total_turno_noite_3_geral;
        $sub_t4_geral = $total_turno_manha_4_geral + $total_turno_tarde_4_geral + $total_turno_noite_4_geral;
        $sub_t5_geral = $total_turno_manha_5_geral + $total_turno_tarde_5_geral + $total_turno_noite_5_geral;

        $sub_t1 = $total_turno_manha_1 + $total_turno_tarde_1 + $total_turno_noite_1;
        $sub_t2 = $total_turno_manha_2 + $total_turno_tarde_2 + $total_turno_noite_2;
        $sub_t3 = $total_turno_manha_3 + $total_turno_tarde_3 + $total_turno_noite_3;
        $sub_t4 = $total_turno_manha_4 + $total_turno_tarde_4 + $total_turno_noite_4;
        $sub_t5 = $total_turno_manha_5 + $total_turno_tarde_5 + $total_turno_noite_5;

      @endphp

              @php
        $t1 += $sub_t1;
        $t2 += $sub_t2;
        $t3 += $sub_t3;
        $t4 += $sub_t4;
        $t5 += $sub_t5;

      @endphp
              <tr class="last-line font-weight-bold">
                <td class="bg-white f4" colspan="2"><b>SUB-TOTAL</b></td>
                <td class="text-center bg3 f3" colspan="4">
                {{ $sub_t1 }}
                </td>
                <td class="text-center bg3 f3" colspan="4">
                {{ $sub_t2 }}
                </td>
                <td class="text-center bg3 f3" colspan="4">
                {{$sub_t3 }}
                </td>
                <td class="text-center bg3 f3" colspan="4">
                {{ $sub_t4 }}
                </td>
                <td class="text-center bg3 f3" colspan="4">
                {{ $sub_t5 }}
                </td>
                <td class="text-center bg8 f3">{{$sub_t1 + $sub_t2 + $sub_t3 + $sub_t4 + $sub_t5  }}</td>
              </tr>
              <tr>
                <td class="bg-white"></td>
              </tr>

              @if($loop->first)
          <div class="col-12" style="margin-top:100px!important; page-break-before: avoid;"></div>
        @else
        <div class="col-12" style="margin-top:100px!important; page-break-before: avoid;"></div>
      @endif
        @endforeach
                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr class="last-line font-weight-bold">
                  <td class="bg-white f4" colspan="2"><b>SUB-TOTAL GERAL DO DEPARTAMENTO</b></td>
                  <td class="text-center bg3 f3" colspan="4">{{ $sub_t1_geral }}</td>
                  <td class="text-center bg3 f3" colspan="4">{{ $sub_t2_geral }}</td>
                  <td class="text-center bg3 f3" colspan="4">{{ $sub_t3_geral }}</td>
                  <td class="text-center bg3 f3" colspan="4">{{ $sub_t4_geral }}</td>
                  <td class="text-center bg3 f3" colspan="4">{{ $sub_t5_geral }}</td>
                  <td class="text-center bg8 f3">
                  {{$sub_t1_geral + $sub_t2_geral + $sub_t3_geral + $sub_t4_geral + $sub_t5_geral}}
                  </td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                @if($loop->iteration == 5)

          @for($i = 0; $i < 40; $i++)
        <tr>
        <td class="bg-white"></td>
        </tr>
      @endfor
        @endif
                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

      @endforeach

                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr>
                  <td class="bg-white"></td>
                </tr>
                @php 
                    $total_turno = $t1 + $t2 + $t3 + $t4 + $t5;
          $p_turno_manha = round(($total_turno_M_geral / $total_turno) * 100, 0);
          $p_turno_tarde = round(($total_turno_T_geral / $total_turno) * 100, 0);
          $p_turno_noite = round(($total_turno_N_geral / $total_turno) * 100, 0);
          $p_turno_geral = round(($total_turno / $total_turno) * 100, 0);

          $p_turno_1 = round(($t1 / $total_turno) * 100, 0);
          $p_turno_2 = round(($t2 / $total_turno) * 100, 0);
          $p_turno_3 = round(($t3 / $total_turno) * 100, 0);
          $p_turno_4 = round(($t4 / $total_turno) * 100, 0);
          $p_turno_5 = round(($t5 / $total_turno) * 100, 0);
          $p_turno_total = round(($total_turno / $total_turno) * 100, 0);


        @endphp
                <tr>
                  <td class="bg-white"></td>
                </tr>
                <tr class="last-line">
                  <td class="bg-white f1 m-4" colspan="2"><b>M </b></td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_manha_1_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_manha_2_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_manha_3_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_manha_4_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_manha_5_global }}</td>
                  <td class="text-center bg8 f3 font-weight-bold">{{ $total_turno_M_geral}}</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold">{{ $p_turno_manha}}%</td>
                </tr>
                <tr class="last-line">
                  <td class="bg-white f1 m-4" colspan="2"><b>T </b></td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_tarde_1_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_tarde_2_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_tarde_3_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_tarde_4_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_tarde_5_global }}</td>
                  <td class="text-center bg8 f3 font-weight-bold">{{ $total_turno_T_geral }}</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold">{{ $p_turno_tarde }}%</td>
                </tr>
                <tr class="last-line">
                  <td class="bg-white f1 m-4" colspan="2"><b>N </b></td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_noite_1_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_noite_2_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_noite_3_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_noite_4_global }}</td>
                  <td class="text-center bg3 f3 font-weight-bold" colspan="4">{{ $total_turno_noite_5_global }}</td>
                  <td class="text-center bg8 f3 font-weight-bold">{{ $total_turno_N_geral}}</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold">{{ $p_turno_noite}}%</td>
                </tr>
                <tr class="last-line">
                  <td class="bg-white f1" colspan="2"><b>TOTAL</b></td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="4">{{ $t1 }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="4">{{ $t2 }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="4">{{ $t3 }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="4">{{ $t4 }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="4">{{ $t5 }}</td>
                  <td class="text-center bg8 f3 font-weight-bold strange">{{ $total_turno }}</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold strange">{{ $p_turno_geral }}%</td>
                </tr>
                <tr class="last-line">
                  <td class="bg-white f1" colspan="2"><b></b></td>
                  <td class="text-center bg-admitidos f3 font-weight-bold" colspan="4">{{ $p_turno_1 }}%</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold" colspan="4">{{ $p_turno_2 }}%</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold" colspan="4">{{ $p_turno_3 }}%</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold" colspan="4">{{ $p_turno_4 }}%</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold" colspan="4">{{ $p_turno_5 }}%</td>
                  <td class="text-center bg-admitidos f3 font-weight-bold strange">{{ $p_turno_total }}%</td>
                </tr>
              </table>
            </div>
            <br>

            <div class="row margin-new">
              <div class="col-5">

                <table class="table_te ta"
                  style="margin-left: 10%!important;page-break-before: always; width: 80%!important">
                  <thead>
                    @for ($i = 0; $i < 20; $i++)
            <tr>
              <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color" colspan="4">
              </th>
            </tr>
          @endfor

                    <tr>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color" colspan="4">
                        <titles class="f-blue">Quadro 2:</titles> matrículas por dia
                      </th>
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

                    @foreach ($datas_inscricao as $key => $value)
                      @php
              $total_data = $total_data + $value["matriculas"];
            @endphp
                      <tr>
                        <td class="text-left bg2 f3">{{ $a++ }}</td>
                        <td class="text-left bg2 f3">{{ $key }}</td>
                        <td class="text-center bg2 f3">{{ $value["matriculas"] }}</td>
                        <td class="text-center bg2 f3">
                        {{ ($total_data) }}
                        </td>
                      </tr>
          @endforeach
                  </tbody>
                </table>

              </div>



              <div class="col-8">

                <table class="table_te ta" style="left: -1;margin:0%!important;page-break-before: always;">
                  <thead>

                    @for ($i = 0; $i < 20; $i++)
            <tr>
              <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color" colspan="3">
              </th>
            </tr>
          @endfor
                    <tr>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color" colspan="3">
                        <titles class="f-blue">Quadro 3:</titles> matrículas por Staff
                      </th>
                    </tr>
                    <tr>
                      <th class="text-left bg1 font-weight-bold f3 pd" style="width: 20px!IMPORTANT">#</th>
                      <th class="text-left bg1 font-weight-bold f3 pd">STAFF MATRÍCULAS </th>
                      <th class="text-center bg1 font-weight-bold f3 pd">Nº</th>
                      <th class="text-center bg1 font-weight-bold f3 pd">%</th>
                    </tr>
                  </thead>
                  <tbody>

                    @php 
                          $a = 1;
            $p_total = 0;
            @endphp

                    @foreach ($matriculas_staff as $key => $item)
            <tr>
              <td class="text-left bg2 f3">{{ $a++ }}</td>
              @foreach($staff as $s)
          @if($s->users_id == $key)
        <td class="text-left bg2 f3">{{ $s->nome }}</td>
      @endif
        @endforeach
              <td class="text-center bg2 f3">{{ $item['matriculas'] }}</td>
              <td class="text-center bg2 f3">
              @if ($item['matriculas'])
          {{ $item['percentagem'] }}%
        @else
        0%
      @endif
              </td>
              @php  $p_total += $item['percentagem']; @endphp
            </tr>
          @endforeach
                    <tr>

                      <td class="bg-white f1"></td>
                      <td class="bg-white f1"><b>TOTAL</b></td>
                      <td class="text-center bg4 f3 font-weight-bold">{{ $total_matriculas }}
                      </td>
                      <td class="text-center bg4 f3 font-weight-bold">
                        @if($total_matriculas)
              {{ round($p_total, 0) }}%
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
        $user = auth()->user()->id;

        @endphp

            <br><br>
            <br><br>
            <br><br>
            <br>

            <div class="row">
              <div class="col-6">
                <table class="table_te ta" id="table_ti" style="margin-left: 8%!important;width:88%;">
                  <thead>
                    <tr>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 ">#</th>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                        style="font-size: 18px!important;" colspan="3">
                        <titles class="f-blue">Quadro 4:</titles> Resumo das matrículas
                      </th>
                    </tr>
                    <tr>

                    </tr>
                  </thead>

                  <tbody>
                    <tr>
                      <td class="text-left bg2 f3">1</td>
                      <td class="text-left bg2 f3">Matrículas INICIADAS</td>
                      <td class="text-center bg2 f3">{{ $p_matriculas['m_iniciadas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">2</td>
                      <td class="text-left bg2 f3">Matrículas ELIMINADAS</td>
                      <td class="text-center bg2 f3">{{ $p_matriculas['m_eliminadas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">3</td>
                      <td class="text-left bg2 f3">Matrículas NÃO CONCLUIDAS</td>
                      <td class="text-center bg2 f3">{{ $p_matriculas['m_n_concluidas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">4</td>
                      <td class="text-left bg2 f3">Matrículas CONCLUIDAS</td>
                      <td class="text-center bg2 f3">
                        {{ ($p_matriculas['m_iniciadas'] - $p_matriculas['m_eliminadas'] - $p_matriculas['m_n_concluidas']) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">5</td>
                      <td class="text-left bg2 f3">Matrículas NÃO PAGAS</td>
                      <td class="text-center bg2 f3">{{ $p_matriculas['m_n_concluidas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">6</td>
                      <td class="text-left bg2 f3">Matrículas PAGAS</td>
                      <td class="text-center bg2 f3">
                        {{ $p_matriculas['m_iniciadas'] - $p_matriculas['m_eliminadas'] - $p_matriculas['m_n_concluidas'] }}
                      </td>
                    </tr>

                  </tbody>
                </table>

              </div>

              <div class="col-6">
                <table class="table_te ta" style="margin-left: 8%!important;width:88%;">
                  <thead>
                    <tr>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 ">#</th>
                      <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                        style="font-size: 18px!important;" colspan="3">
                        <titles class="f-blue">Quadro 5:</titles> Resumo das confirmações
                      </th>
                    </tr>
                    <tr>

                    </tr>
                  </thead>

                  <tbody>
                    <tr>
                      <td class="text-left bg2 f3">1</td>
                      <td class="text-left bg2 f3">Confirmações INICIADAS</td>
                      <td class="text-center bg2 f3">{{ $cf_matriculas['m_iniciadas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">2</td>
                      <td class="text-left bg2 f3">Confirmações ELIMINADAS</td>
                      <td class="text-center bg2 f3">{{ $cf_matriculas['m_eliminadas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">3</td>
                      <td class="text-left bg2 f3">Confirmações NÃO CONCLUIDAS</td>
                      <td class="text-center bg2 f3">{{ $cf_matriculas['m_n_concluidas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">4</td>
                      <td class="text-left bg2 f3">Confirmações CONCLUIDAS</td>
                      <td class="text-center bg2 f3">
                        {{ ($cf_matriculas['m_iniciadas'] - $cf_matriculas['m_eliminadas'] - $cf_matriculas['m_n_concluidas']) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">5</td>
                      <td class="text-left bg2 f3">Confirmações NÃO PAGAS</td>
                      <td class="text-center bg2 f3">{{ $cf_matriculas['m_n_concluidas'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left bg2 f3">6</td>
                      <td class="text-left bg2 f3">Confirmações PAGAS</td>
                      <td class="text-center bg2 f3">
                        {{ $cf_matriculas['m_iniciadas'] - $cf_matriculas['m_eliminadas'] - $cf_matriculas['m_n_concluidas'] }}
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
</main>

<br>
<br>

{{-- <div class="assinaturas">
  <p><b> O(A) Vice-Director(a) académico:</b></p>
  <p>______________________________</p>
  <p>{{ $cordenador }}/ MSc</p>
</div> --}}
@endsection