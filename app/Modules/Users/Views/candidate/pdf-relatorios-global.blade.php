@extends('layouts.print')
@section('title', __('Relatório de candidatos'))
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
      page-break-inside: avoid;
      /*padding: 1px !important;*/
    }

    #acomula thead {
      display: table-header-group;
      break-inside: avoid;
    }

    .last-line td {
      background-color: #cecece;
    }

    .line td {
      background-color: #ebebeb;
    }

    .tabble_te thead {
      display: table-header-group;
    }
    
    table {
    page-break-inside: avoid;
    break-inside: avoid;
}

thead {
    display: table-header-group;
}

tfoot {
    display: table-footer-group;
}

tr {
    page-break-inside: avoid;
    break-inside: avoid;
}



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

    .page-break {
      page-break-after: always;
    }

    .bg-total {
      background-color: #F9E994 !important;
    }

    .bg-sexo {
      background-color: #fff2ce !important;
    }

    .bg-candidatos {
      background-color: #10AAD0 !important;
    }

    .bg-exames {
      background-color: #8DAADB !important;
    }

    .bg-ausentes {
      background-color: #FFE699 !important;
    }

    .bg-reprovados {
      background-color: #F3B086 !important;
    }

    .bg-admitidos {
      background-color: #BBD8F0 !important;
    }

    .bg-matriculados {
      background-color: #C3DFB4 !important;
    }

    .bg-p_total {
      background-color: #FACBB1 !important;
    }



    .vertical-text {
      writing-mode: vertical-rl;
      /* Orientação vertical de baixo para cima */
      transform: rotate(180deg);
      /* Rotaciona o texto para ficar de baixo para cima */
      text-align: center;
      /* Centraliza o texto */
      font-size: 20px;
      /* Tamanho da fonte (opcional) */
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
              candidaturas (Novo)</titles>
            <br>

          </h4>
          <h4 class="title-dom2">
            <b>Ano lectivo:</b>
            <titles class="t-color">{{ $lectiveYears[0]->currentTranslation->display_name }}</titles>
            <div style="height:10px;color:white;">f</div>
            <b>Fase:</b>
            <titles class="t-color">Global</titles>
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
              <table class="table_te margin-new" style="width: 93%;margin-left:4%;margin-right:0;">

                <br>

                <tr class="line">
                  <th colspan="23" class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color">
                    <titles class="f-blue">Quadro 1:</titles> Vagas vs Candidatos
                  </th>
                </tr>
              </table>
              @php
$m = 0;
$money = 0;
$t = 0;
$n = 0;
$m_c = 0;
$t_c = 0;
$n_c = 0;
$candidatos_global_exame_m_m = 0;
$candidatos_global_exame_m_f = 0;
$candidatos_global_exame_m = 0;
$candidatos_global_exame_t_m = 0;
$candidatos_global_exame_t_f = 0;
$candidatos_global_exame_t = 0;
$candidatos_global_exame_n_m = 0;
$candidatos_global_exame_n_f = 0;
$candidatos_global_exame_n = 0;
$candidatos_global_ausente_m_m = 0;
$candidatos_global_ausente_m_f = 0;
$candidatos_global_ausente_m = 0;
$candidatos_global_ausente_t_m = 0;
$candidatos_global_ausente_t_f = 0;
$candidatos_global_ausente_t = 0;
$candidatos_global_ausente_n_m = 0;
$candidatos_global_ausente_n_f = 0;
$candidatos_global_ausente_n = 0;
$candidatos_global_reprovado_m_m = 0;
$candidatos_global_reprovado_m_f = 0;
$candidatos_global_reprovado_m = 0;
$candidatos_global_reprovado_t_m = 0;
$candidatos_global_reprovado_t_f = 0;
$candidatos_global_reprovado_t = 0;
$candidatos_global_reprovado_n_m = 0;
$candidatos_global_reprovado_n_f = 0;
$candidatos_global_reprovado_n = 0;
$candidatos_global_admitido_m_m = 0;
$candidatos_global_admitido_m_f = 0;
$candidatos_global_admitido_m = 0;
$candidatos_global_admitido_t_m = 0;
$candidatos_global_admitido_t_f = 0;
$candidatos_global_admitido_t = 0;
$candidatos_global_admitido_n_m = 0;
$candidatos_global_admitido_n_f = 0;
$candidatos_global_admitido_n = 0;
$candidatos_global_matriculado_m_m = 0;
$candidatos_global_matriculado_m_f = 0;
$candidatos_global_matriculado_m = 0;
$candidatos_global_matriculado_t_m = 0;
$candidatos_global_matriculado_t_f = 0;
$candidatos_global_matriculado_t = 0;
$candidatos_global_matriculado_n_m = 0;
$candidatos_global_matriculado_n_f = 0;
$candidatos_global_matriculado_n = 0;
$manha_global = 0;
$tarde_global = 0;
$noite_global = 0;
$vaga_manha_global = 0;
$vaga_tarde_global = 0;
$vaga_noite_global = 0;
$vaga_manha_m_global = 0;
$vaga_manha_f_global = 0;
$vaga_tarde_m_global = 0;
$vaga_tarde_f_global = 0;
$vaga_noite_m_global = 0;
$vaga_noite_f_global = 0;
$candidatos_m_global = 0;
$candidatos_t_global = 0;
$candidatos_n_global = 0;
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
                <table class="page-break table_te" style="width: 90%;margin-left:4%;margin-right:0;">
                <tr class="line">
                  <th colspan="23" class="text-left text-white bg0 text-uppercase font-weight-bold f1">
                  {{ $key }}
                  </th>
                </tr>

                <tr>
                  <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3">
                  #</th>
                  <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3"
                  colspan="2">
                  CURSOS</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">M</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">T</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">N</th>
                  <th class="text-center bg1 font-weight-bold f3 pd1" style="" colspan="5">Total</th>


                </tr>
                <tr class="tr-new-line">

                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan=2>%</th>

                </tr>
                <tr class="tr-new-line" style="font-weight:bold">


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>
                </tr>
                @php
  $manha_geral = 0;
  $tarde_geral = 0;
  $noite_geral = 0;
  $vaga_manha_geral = 0;
  $vaga_tarde_geral = 0;
  $vaga_noite_geral = 0;

  $vaga_manha_m_geral = 0;
  $vaga_manha_f_geral = 0;
  $vaga_tarde_m_geral = 0;
  $vaga_tarde_f_geral = 0;
  $vaga_noite_m_geral = 0;
  $vaga_noite_f_geral = 0;

  $total_exames_geral = 0;
  $total_ausentes_geral = 0;
  $total_admitidos_geral = 0;
  $total_reprovados_geral = 0;
  $total_matriculados_geral = 0;
  $candidatos_m_geral = 0;
  $candidatos_t_geral = 0;
  $candidatos_n_geral = 0;
  $candidatos_geral_exame_m = 0;
  $candidatos_geral_exame_m_m = 0;
  $candidatos_geral_exame_m_f = 0;
  $candidatos_geral_exame_t = 0;
  $candidatos_geral_exame_t_m = 0;
  $candidatos_geral_exame_t_f = 0;
  $candidatos_geral_exame_n = 0;
  $candidatos_geral_exame_n_m = 0;
  $candidatos_geral_exame_n_f = 0;
  $candidatos_geral_exame_total = 0;
  $candidatos_geral_exame_total_m = 0;
  $candidatos_geral_exame_total_f = 0;
  $candidatos_geral_ausente_m = 0;
  $candidatos_geral_ausente_m_m = 0;
  $candidatos_geral_ausente_m_f = 0;
  $candidatos_geral_ausente_t = 0;
  $candidatos_geral_ausente_t_m = 0;
  $candidatos_geral_ausente_t_f = 0;
  $candidatos_geral_ausente_n = 0;
  $candidatos_geral_ausente_n_m = 0;
  $candidatos_geral_ausente_n_f = 0;
  $candidatos_geral_ausente_total = 0;
  $candidatos_geral_ausente_total_m = 0;
  $candidatos_geral_ausente_total_f = 0;
  $candidatos_geral_reprovado_m = 0;
  $candidatos_geral_reprovado_m_m = 0;
  $candidatos_geral_reprovado_m_f = 0;
  $candidatos_geral_reprovado_t = 0;
  $candidatos_geral_reprovado_t_m = 0;
  $candidatos_geral_reprovado_t_f = 0;
  $candidatos_geral_reprovado_n = 0;
  $candidatos_geral_reprovado_n_m = 0;
  $candidatos_geral_reprovado_n_f = 0;
  $candidatos_geral_reprovado_total = 0;
  $candidatos_geral_reprovado_total_m = 0;
  $candidatos_geral_reprovado_total_f = 0;
  $candidatos_geral_admitido_m = 0;
  $candidatos_geral_admitido_m_m = 0;
  $candidatos_geral_admitido_m_f = 0;
  $candidatos_geral_admitido_t = 0;
  $candidatos_geral_admitido_t_m = 0;
  $candidatos_geral_admitido_t_f = 0;
  $candidatos_geral_admitido_n = 0;
  $candidatos_geral_admitido_n_m = 0;
  $candidatos_geral_admitido_n_f = 0;
  $candidatos_geral_admitido_total = 0;
  $candidatos_geral_admitido_total_m = 0;
  $candidatos_geral_admitido_total_f = 0;
  $candidatos_geral_matriculado_m = 0;
  $candidatos_geral_matriculado_m_m = 0;
  $candidatos_geral_matriculado_m_f = 0;
  $candidatos_geral_matriculado_t = 0;
  $candidatos_geral_matriculado_t_m = 0;
  $candidatos_geral_matriculado_t_f = 0;
  $candidatos_geral_matriculado_n = 0;
  $candidatos_geral_matriculado_n_m = 0;
  $candidatos_geral_matriculado_n_f = 0;
  $candidatos_geral_matriculado_total = 0;
  $candidatos_geral_matriculado_total_m = 0;
  $candidatos_geral_matriculado_total_f = 0;

  $p_m_ausentes_geral = 0;
  $p_t_ausentes_geral = 0;
  $p_n_ausentes_geral = 0;
  $p_total_ausentes_geral = 0;
  $p_m_exames_geral = 0;
  $p_t_exames_geral = 0;
  $p_n_exames_geral = 0;
  $p_total_exames_geral = 0;
  $p_m_reprovados_geral = 0;
  $p_t_reprovados_geral = 0;
  $p_n_reprovados_geral = 0;
  $p_total_reprovados_geral = 0;
  $p_m_admitidos_geral = 0;
  $p_t_admitidos_geral = 0;
  $p_n_admitidos_geral = 0;
  $p_total_admitidos_geral = 0;
  $p_m_matriculados_geral = 0;
  $p_t_matriculados_geral = 0;
  $p_n_matriculados_geral = 0;
  $p_total_matriculados_geral = 0;

  $p_m_ausentes = 0;
  $p_t_ausentes = 0;
  $p_n_ausentes = 0;
  $p_total_ausentes = 0;
  $p_m_exames = 0;
  $p_t_exames = 0;
  $p_n_exames = 0;
  $p_total_exames = 0;
  $p_m_reprovados = 0;
  $p_t_reprovados = 0;
  $p_n_reprovados = 0;
  $p_total_reprovados = 0;
  $p_m_admitidos = 0;
  $p_t_admitidos = 0;
  $p_n_admitidos = 0;
  $p_total_admitidos = 0;
  $p_m_matriculados = 0;
  $p_t_matriculados = 0;
  $p_n_matriculados = 0;
  $p_total_matriculados = 0;
          @endphp
                @foreach ($item as $item_vagas)
              <tr class="f2">
                <td class="text-center bg2 ">{{ $i++ }}</td>

                <td class="text-left bg2 text-uppercase " style="width:350px" colspan="2">
                {{ $item_vagas->abbreviation }}
                </td>
                <td class="text-center bg2 ">
                {{ $item_vagas->manha != 0 ? $item_vagas->manha : '-' }}
                </td>
                <td class="text-center bg-total t-color ">
                {{ $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'] ?? "-" }}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['m'] ?? "-" }}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['f'] ?? "-" }}
                </td>
                <td class="text-center bg2 ">
                {{ $item_vagas->tarde != 0 ? $item_vagas->tarde : '-' }}
                </td>
                <td class="text-center bg-total t-color">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] ?? '-' }}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['m'] ?? "-" }}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['f'] ?? "-" }}
                </td>
                <td class="text-center bg2 ">
                {{ $item_vagas->noite != 0 ? $item_vagas->noite : '-' }}
                </td>
                <td class="text-center bg-total t-color">
                {{ $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] ?? '-'}}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['m'] ?? "-" }}
                </td>
                <td class="text-center bg-sexo t-color ">
                {{ $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['f'] ?? "-" }}
                </td>
                <td class="text-center bg2 ">
                {{ $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha != 0 ? $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha : '-' }}
                </td>
                @php 
            $total = $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'];

          @endphp
                <td class="text-center bg-total t-color">
                {{ $total }}
                </td>



                <td class="text-center bg-sexo t-color">{{  $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['m'] }}
                </td>


                <td class="text-center bg-sexo t-color">{{
      $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['f']
              }}</td>
                <td class="text-center bg-p_total">
                @if ($total != 0)
          {{ (int) round(($total / ($item_vagas->noite + $item_vagas->tarde + $item_vagas->manha)) * 100, 0) }}%
        @else
      0%
    @endif
                </td>
              </tr>
              @php


    //Turnos
    $manha_global += $item_vagas->manha;
    $tarde_global += $item_vagas->tarde;
    $noite_global += $item_vagas->noite;


    //Vagas
    $vaga_manha_global += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'];
    $vaga_tarde_global += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'];
    $vaga_noite_global += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'];

    $vaga_manha_m_global += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['m'];
    $vaga_manha_f_global += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['f'];
    $vaga_tarde_m_global += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['m'];
    $vaga_tarde_f_global += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['f'];
    $vaga_noite_m_global += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['m'];
    $vaga_noite_f_global += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['f'];

    //Candidaturas

    $candidatos_m_global += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'];
    $candidatos_t_global += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'];
    $candidatos_n_global += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'];

    //Exames

    $candidatos_global_exame_m_m += $candidatos[$item_vagas->courses_id]['manha']['exames']['m'];
    $candidatos_global_exame_m_f += $candidatos[$item_vagas->courses_id]['manha']['exames']['f'];
    $candidatos_global_exame_m += $candidatos[$item_vagas->courses_id]['manha']['exames']['total'];

    $candidatos_global_exame_t_m += $candidatos[$item_vagas->courses_id]['tarde']['exames']['m'];
    $candidatos_global_exame_t_f += $candidatos[$item_vagas->courses_id]['tarde']['exames']['f'];
    $candidatos_global_exame_t += $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'];

    $candidatos_global_exame_n_m += $candidatos[$item_vagas->courses_id]['noite']['exames']['m'];
    $candidatos_global_exame_n_f += $candidatos[$item_vagas->courses_id]['noite']['exames']['f'];
    $candidatos_global_exame_n += $candidatos[$item_vagas->courses_id]['noite']['exames']['total'];

    //Ausentes

    $candidatos_global_ausente_m_m += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['m'];
    $candidatos_global_ausente_m_f += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['f'];
    $candidatos_global_ausente_m += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['total'];

    $candidatos_global_ausente_t_m += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['m'];
    $candidatos_global_ausente_t_f += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['f'];
    $candidatos_global_ausente_t += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['total'];

    $candidatos_global_ausente_n_m += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['m'];
    $candidatos_global_ausente_n_f += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['f'];
    $candidatos_global_ausente_n += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['total'];

    //Reprovados

    $candidatos_global_reprovado_m_m += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['m'];
    $candidatos_global_reprovado_m_f += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['f'];
    $candidatos_global_reprovado_m += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['total'];

    $candidatos_global_reprovado_t_m += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['m'];
    $candidatos_global_reprovado_t_f += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['f'];
    $candidatos_global_reprovado_t += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['total'];

    $candidatos_global_reprovado_n_m += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['m'];
    $candidatos_global_reprovado_n_f += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['f'];
    $candidatos_global_reprovado_n += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['total'];

    //Admitidos

    $candidatos_global_admitido_m_m += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['m'];
    $candidatos_global_admitido_m_f += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['f'];
    $candidatos_global_admitido_m += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['total'];

    $candidatos_global_admitido_t_m += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['m'];
    $candidatos_global_admitido_t_f += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['f'];
    $candidatos_global_admitido_t += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'];

    $candidatos_global_admitido_n_m += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['m'];
    $candidatos_global_admitido_n_f += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['f'];
    $candidatos_global_admitido_n += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'];

    //Matriculados

    $candidatos_global_matriculado_m_m += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['m'];
    $candidatos_global_matriculado_m_f += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['f'];
    $candidatos_global_matriculado_m += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['total'];

    $candidatos_global_matriculado_t_m += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['m'];
    $candidatos_global_matriculado_t_f += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['f'];
    $candidatos_global_matriculado_t += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['total'];

    $candidatos_global_matriculado_n_m += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['m'];
    $candidatos_global_matriculado_n_f += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['f'];
    $candidatos_global_matriculado_n += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['total'];
      @endphp
              @php
    // Total para o número de candidatos

    $m_c += ($candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'] ?? 0);
    $money = ($candidatos[$item_vagas->courses_id]['money'] ?? 0);
    $t_c += ($candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] ?? 0);
    $n_c += ($candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] ?? 0);
    $m_p_c += ($candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'] ?? 0);
    $t_p_c += ($candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] ?? 0);
    $n_p_c += ($candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] ?? 0);

    // Total para o número de vagas

    $m += $item_vagas->manha;
    $t += $item_vagas->tarde;
    $n += $item_vagas->noite;
    $m_p += $item_vagas->manha;
    $t_p += $item_vagas->tarde;
    $n_p += $item_vagas->noite;

    // totais

    $total_exames = $candidatos[$item_vagas->courses_id]['noite']['exames']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['exames']['total'];

    $total_ausentes = $candidatos[$item_vagas->courses_id]['noite']['ausentes']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['ausentes']['total'];

    $total_admitidos = $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['admitidos']['total'];

    $total_reprovados = $candidatos[$item_vagas->courses_id]['noite']['reprovados']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['reprovados']['total'];

    $total_matriculados = $candidatos[$item_vagas->courses_id]['noite']['matriculados']['total'] +
      $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['total'] +
      $candidatos[$item_vagas->courses_id]['manha']['matriculados']['total'];


    // Cálculo de ausentes
    // Cálculo de ausentes
    $p_m_ausentes = $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['manha']['ausentes']['total'] / $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';
    $p_t_ausentes = $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['tarde']['ausentes']['total'] / $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';
    $p_n_ausentes = $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['noite']['ausentes']['total'] / $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';

    $p_total_ausentes = $total != 0
      ? round(($total_ausentes / $total) * 100, 0) . '%'
      : '0%';

    // Cálculo de exames
    $p_m_exames = $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['manha']['exames']['total'] / $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';
    $p_t_exames = $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['tarde']['exames']['total'] / $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';
    $p_n_exames = $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['noite']['exames']['total'] / $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total']) * 100, 0) . '%'
      : '0%';

    $p_total_exames = $total != 0
      ? round(($total_exames / $total) * 100, 0) . '%'
      : '0%';

    // Cálculo de reprovados
    $p_m_reprovados = $candidatos[$item_vagas->courses_id]['manha']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['manha']['reprovados']['total'] / $candidatos[$item_vagas->courses_id]['manha']['exames']['total']) * 100, 0) . '%'
      : '0%';
    $p_t_reprovados = $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['tarde']['reprovados']['total'] / $candidatos[$item_vagas->courses_id]['tarde']['exames']['total']) * 100, 0) . '%'
      : '0%';
    $p_n_reprovados = $candidatos[$item_vagas->courses_id]['noite']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['noite']['reprovados']['total'] / $candidatos[$item_vagas->courses_id]['noite']['exames']['total']) * 100, 0) . '%'
      : '0%';



    $p_total_reprovados = $total_exames != 0
      ? round(($total_reprovados / $total_exames) * 100, 0) . '%'
      : '0%';

    // Cálculo de admitidos
    $p_m_admitidos = $candidatos[$item_vagas->courses_id]['manha']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['manha']['admitidos']['total'] / $candidatos[$item_vagas->courses_id]['manha']['exames']['total']) * 100, 0) . '%'
      : '0%';
    $p_t_admitidos = $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'] / $candidatos[$item_vagas->courses_id]['tarde']['exames']['total']) * 100, 0) . '%'
      : '0%';
    $p_n_admitidos = $candidatos[$item_vagas->courses_id]['noite']['exames']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'] / $candidatos[$item_vagas->courses_id]['noite']['exames']['total']) * 100, 0) . '%'
      : '0%';

    $p_total_admitidos = $total_exames != 0
      ? round(($total_admitidos / $total_exames) * 100, 0) . '%'
      : '0%';

    // Cálculo de matriculados
    $p_m_matriculados = $candidatos[$item_vagas->courses_id]['manha']['admitidos']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['manha']['matriculados']['total'] / $candidatos[$item_vagas->courses_id]['manha']['admitidos']['total']) * 100, 0) . '%'
      : '0%';
    $p_t_matriculados = $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['tarde']['matriculados']['total'] / $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total']) * 100, 0) . '%'
      : '0%';
    $p_n_matriculados = $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'] != 0
      ? round(($candidatos[$item_vagas->courses_id]['noite']['matriculados']['total'] / $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total']) * 100, 0) . '%'
      : '0%';

    $p_total_matriculados = $total_admitidos != 0
      ? round(($total_matriculados / $total_admitidos) * 100, 0) . '%'
      : '0%';

      @endphp
              <tr style="height:8px !important">
                <td class="bg-white"></td>
              </tr>

              <tr>
                <center>
                <td class="bg-white" style="color: white;height:5px!important;" rowspan="5"></td>
                <td rowspan="5" style="text-transform:uppercase">
                </td>
                @php

    //Turnos
    $manha_geral += $item_vagas->manha;
    $tarde_geral += $item_vagas->tarde;
    $noite_geral += $item_vagas->noite;


    //Vagas
    $vaga_manha_geral += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'];
    $vaga_tarde_geral += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'];
    $vaga_noite_geral += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'];

    $vaga_manha_m_geral += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['m'];
    $vaga_manha_f_geral += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['f'];
    $vaga_tarde_m_geral += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['m'];
    $vaga_tarde_f_geral += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['f'];
    $vaga_noite_m_geral += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['m'];
    $vaga_noite_f_geral += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['f'];

    //Candidaturas

    $candidatos_m_geral += $candidatos[$item_vagas->courses_id]['manha']['candidaturas']['total'];
    $candidatos_t_geral += $candidatos[$item_vagas->courses_id]['tarde']['candidaturas']['total'];
    $candidatos_n_geral += $candidatos[$item_vagas->courses_id]['noite']['candidaturas']['total'];

    //Exames

    $candidatos_geral_exame_m_m += $candidatos[$item_vagas->courses_id]['manha']['exames']['m'];
    $candidatos_geral_exame_m_f += $candidatos[$item_vagas->courses_id]['manha']['exames']['f'];
    $candidatos_geral_exame_m += $candidatos[$item_vagas->courses_id]['manha']['exames']['total'];

    $candidatos_geral_exame_t_m += $candidatos[$item_vagas->courses_id]['tarde']['exames']['m'];
    $candidatos_geral_exame_t_f += $candidatos[$item_vagas->courses_id]['tarde']['exames']['f'];
    $candidatos_geral_exame_t += $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'];

    $candidatos_geral_exame_n_m += $candidatos[$item_vagas->courses_id]['noite']['exames']['m'];
    $candidatos_geral_exame_n_f += $candidatos[$item_vagas->courses_id]['noite']['exames']['f'];
    $candidatos_geral_exame_n += $candidatos[$item_vagas->courses_id]['noite']['exames']['total'];

    //Ausentes

    $candidatos_geral_ausente_m_m += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['m'];
    $candidatos_geral_ausente_m_f += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['f'];
    $candidatos_geral_ausente_m += $candidatos[$item_vagas->courses_id]['manha']['ausentes']['total'];

    $candidatos_geral_ausente_t_m += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['m'];
    $candidatos_geral_ausente_t_f += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['f'];
    $candidatos_geral_ausente_t += $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['total'];

    $candidatos_geral_ausente_n_m += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['m'];
    $candidatos_geral_ausente_n_f += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['f'];
    $candidatos_geral_ausente_n += $candidatos[$item_vagas->courses_id]['noite']['ausentes']['total'];

    //Reprovados

    $candidatos_geral_reprovado_m_m += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['m'];
    $candidatos_geral_reprovado_m_f += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['f'];
    $candidatos_geral_reprovado_m += $candidatos[$item_vagas->courses_id]['manha']['reprovados']['total'];

    $candidatos_geral_reprovado_t_m += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['m'];
    $candidatos_geral_reprovado_t_f += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['f'];
    $candidatos_geral_reprovado_t += $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['total'];

    $candidatos_geral_reprovado_n_m += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['m'];
    $candidatos_geral_reprovado_n_f += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['f'];
    $candidatos_geral_reprovado_n += $candidatos[$item_vagas->courses_id]['noite']['reprovados']['total'];

    //Admitidos

    $candidatos_geral_admitido_m_m += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['m'];
    $candidatos_geral_admitido_m_f += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['f'];
    $candidatos_geral_admitido_m += $candidatos[$item_vagas->courses_id]['manha']['admitidos']['total'];

    $candidatos_geral_admitido_t_m += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['m'];
    $candidatos_geral_admitido_t_f += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['f'];
    $candidatos_geral_admitido_t += $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'];

    $candidatos_geral_admitido_n_m += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['m'];
    $candidatos_geral_admitido_n_f += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['f'];
    $candidatos_geral_admitido_n += $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'];

    //Matriculados

    $candidatos_geral_matriculado_m_m += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['m'];
    $candidatos_geral_matriculado_m_f += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['f'];
    $candidatos_geral_matriculado_m += $candidatos[$item_vagas->courses_id]['manha']['matriculados']['total'];

    $candidatos_geral_matriculado_t_m += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['m'];
    $candidatos_geral_matriculado_t_f += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['f'];
    $candidatos_geral_matriculado_t += $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['total'];

    $candidatos_geral_matriculado_n_m += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['m'];
    $candidatos_geral_matriculado_n_f += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['f'];
    $candidatos_geral_matriculado_n += $candidatos[$item_vagas->courses_id]['noite']['matriculados']['total'];
          @endphp
                <td class="text-left bg-exames">Exames</td>
                <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_m_exames }}</td>
                <td class="text-center bg-exames font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['manha']['exames']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{$candidatos[$item_vagas->courses_id]['manha']['exames']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['exames']['f'] }}
                </td>

                <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_t_exames }}</td>
                <td class="text-center bg-exames font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['exames']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['exames']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['exames']['f'] }}
                </td>

                <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_n_exames }}</td>
                <td class="text-center bg-exames font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['noite']['exames']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['exames']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['exames']['f'] }}
                </td>

                <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_total_exames }} </td>



                <td class="text-center bg-exames font-weight-bold">{{ $total_exames}} </td>

                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['exames']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['exames']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['exames']['m']
              }}</td>
                <td class="text-center bg-sexo">{{ 
               $candidatos[$item_vagas->courses_id]['noite']['exames']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['exames']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['exames']['f']
              }}</td>

                <td class="bg-white" style="color: white;font-size: 15px;" rowspan="5">as</td>
                </center>
              </tr>
              <tr>
                <td class="text-right bg-ausentes" style="font-size: 12px;">Ausentes</td>
                <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_m_ausentes }}</td>
                <td class="text-center bg-ausentes font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['manha']['ausentes']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['ausentes']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['ausentes']['f'] }}
                </td>

                <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_t_ausentes }}</td>
                <td class="text-center bg-ausentes font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['f'] }}
                </td>

                <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_n_ausentes }}</td>
                <td class="text-center bg-ausentes font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['noite']['ausentes']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['ausentes']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['ausentes']['f'] }}
                </td>

                <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_total_ausentes }} </td>

                <td class="text-center bg-ausentes font-weight-bold">{{ $total_ausentes}} </td>

                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['ausentes']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['ausentes']['m']
              }}</td>
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['ausentes']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['ausentes']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['ausentes']['f']
              }}</td>
              </tr>

              <tr>
                <td class="text-right bg-reprovados" style="font-size: 12px;">Reprovados</td>
                <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_m_reprovados }}</td>
                <td class="text-center bg-reprovados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['manha']['reprovados']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['reprovados']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['reprovados']['f'] }}
                </td>

                <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_t_reprovados }}</td>
                <td class="text-center bg-reprovados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['f'] }}
                </td>

                <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_n_reprovados }}</td>
                <td class="text-center bg-reprovados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['noite']['reprovados']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['reprovados']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['reprovados']['f'] }}
                </td>


                <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_total_reprovados }} </td>

                <td class="text-center bg-reprovados font-weight-bold">{{ $total_reprovados}} </td>
                </td>
                <!--COMEÇA AQUI!!!-->
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['reprovados']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['reprovados']['m']
              }}</td>
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['reprovados']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['reprovados']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['reprovados']['f']
              }}</td>
              </tr>

              <tr>
                <td class="text-left bg-admitidos" style="font-size: 15px;">Admitidos</td>
                <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_m_admitidos }}</td>
                <td class="text-center bg-admitidos font-weight-bold">
                {{ $candidatos_geral_admitido_m }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['admitidos']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['manha']['admitidos']['f'] }}
                </td>

                <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_t_admitidos }}</td>
                <td class="text-center bg-admitidos font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['f'] }}
                </td>

                <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_n_admitidos }}</td>
                <td class="text-center bg-admitidos font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['noite']['admitidos']['total'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['admitidos']['m'] }}
                </td>
                <td class="text-center bg-sexo">{{ $candidatos[$item_vagas->courses_id]['noite']['admitidos']['f'] }}
                </td>


                <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_total_admitidos }} </td>

                <td class="text-center bg-admitidos font-weight-bold">{{ $total_admitidos}} </td>
                <!--COMEÇA AQUI!!!-->
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['admitidos']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['admitidos']['m']
              }}</td>
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['admitidos']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['admitidos']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['admitidos']['f']
              }}</td>
              </tr>


              <tr>
                <td class="text-left bg-matriculados text-uppercase font-weight-bold" style="font-size: 15px;">
                Matriculados</td>
                <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_m_matriculados }}</td>
                <td class="text-center bg-matriculados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['manha']['matriculados']['total'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['manha']['matriculados']['m'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['manha']['matriculados']['f'] }}
                </td>

                <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_t_matriculados }}</td>
                <td class="text-center bg-matriculados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['total'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['m'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['f'] }}
                </td>

                <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_n_matriculados }}</td>
                <td class="text-center bg-matriculados font-weight-bold">
                {{ $candidatos[$item_vagas->courses_id]['noite']['matriculados']['total'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['noite']['matriculados']['m'] }}
                </td>
                <td class="text-center bg-sexo">
                {{ $candidatos[$item_vagas->courses_id]['noite']['matriculados']['f'] }}
                </td>


                <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_total_matriculados }} </td>

                <td class="text-center bg-matriculados font-weight-bold">{{ $total_matriculados}} </td>
                <!--COMEÇA AQUI!!!-->
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['matriculados']['m'] +
      $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['m'] +
      $candidatos[$item_vagas->courses_id]['manha']['matriculados']['m']
              }}</td>
                <td class="text-center bg-sexo">{{ 
              $candidatos[$item_vagas->courses_id]['noite']['matriculados']['f'] +
      $candidatos[$item_vagas->courses_id]['tarde']['matriculados']['f'] +
      $candidatos[$item_vagas->courses_id]['manha']['matriculados']['f']
              }}</td>
              </tr>
              <tr style="height:24px !important">
                <td class="bg-white"></td>
              </tr>

              <tr style="height:8px !important">
                <td class="bg-white"></td>
              </tr>
        @endforeach
                <tr style="height:8px !important">
                  <td class="bg-white"></td>
                </tr>
                <tr style="height:28px !important">
                  <td class="bg-white"></td>
                </tr>
                </tr>
                <tr class="line">
                  <th colspan="23" class="text-left text-white bg0 text-uppercase font-weight-bold f1">
                  QUADRO RESUMO DO {{ $key }}
                  </th>
                </tr>
                <tr class="f2">
                  <td class="text-center bg-white"></td>
                  <td class="text-left text-uppercase bg-white" style="width:350px" colspan="2">
                  </td>
                  <td class="text-center bg2 ">
                  {{ $manha_geral != 0 ? $manha_geral : '-' }}
                  </td>
                  <td class="text-center bg-total t-color ">
                  {{ $vaga_manha_geral ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_manha_m_geral ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_manha_f_geral ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                  {{$tarde_geral != 0 ? $tarde_geral : '-' }}
                  </td>
                  <td class="text-center bg-total t-color">
                  {{$vaga_tarde_geral ?? '-' }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_tarde_m_geral ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_tarde_f_geral ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                  {{ $noite_geral != 0 ? $noite_geral : '-' }}
                  </td>
                  <td class="text-center bg-total t-color">
                  {{ $vaga_noite_geral ?? '-'}}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_noite_m_geral ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                  {{ $vaga_noite_f_geral ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                  {{ $noite_geral + $tarde_geral + $manha_geral != 0 ? $noite_geral + $tarde_geral + $manha_geral : '-' }}
                  </td>
                  @php 
              $total_geral = $vaga_noite_geral +
    $vaga_manha_geral +
    $vaga_tarde_geral;

          @endphp
                  <td class="text-center bg-total t-color">
                  {{ $total_geral }}
                  </td>



                  <td class="text-center bg-sexo t-color">{{  
                $vaga_noite_m_geral +
    $vaga_tarde_m_geral +
    $vaga_manha_m_geral  
                }}
                  </td>


                  <td class="text-center bg-sexo t-color">{{
    $vaga_noite_f_geral +
    $vaga_tarde_f_geral +
    $vaga_manha_f_geral  
                }}</td>
                  <td class="text-center bg-p_total">
                  @if ($total_geral != 0)
            {{ (int) round(($total_geral / ($noite_geral + $manha_geral + $tarde_geral)) * 100, 0) }}%
          @else
        0%
      @endif
                  @php

  //Totais 
  $total_exames_geral = $candidatos_geral_exame_m +
    $candidatos_geral_exame_t +
    $candidatos_geral_exame_n;

  $total_ausentes_geral = $candidatos_geral_ausente_m +
    $candidatos_geral_ausente_t +
    $candidatos_geral_ausente_n;

  $total_admitidos_geral = $candidatos_geral_admitido_m +
    $candidatos_geral_admitido_t +
    $candidatos_geral_admitido_n;

  $total_reprovados_geral = $candidatos_geral_reprovado_m +
    $candidatos_geral_reprovado_t +
    $candidatos_geral_reprovado_n;

  $total_matriculados_geral = $candidatos_geral_matriculado_m +
    $candidatos_geral_matriculado_t +
    $candidatos_geral_matriculado_n;

  // Cálculo de ausentes
  $p_m_ausentes_geral = $candidatos_m_geral != 0
    ? round(($candidatos_geral_ausente_m / $candidatos_m_geral) * 100, 0) . '%'
    : '0%';
  $p_t_ausentes_geral = $candidatos_t_geral != 0
    ? round(($candidatos_geral_ausente_t / $candidatos_t_geral) * 100, 0) . '%'
    : '0%';
  $p_n_ausentes_geral = $candidatos_n_geral != 0
    ? round(($candidatos_geral_ausente_n / $candidatos_n_geral) * 100, 0) . '%'
    : '0%';

  $p_total_ausentes_geral = $total_geral != 0
    ? round(($total_ausentes_geral / $total_geral) * 100, 0) . '%'
    : '0%';

  // Cálculo de exames
  $p_m_exames_geral = $candidatos_m_geral != 0
    ? round(($candidatos_geral_exame_m / $candidatos_m_geral) * 100, 0) . '%'
    : '0%';
  $p_t_exames_geral = $candidatos_t_geral != 0
    ? round(($candidatos_geral_exame_t / $candidatos_t_geral) * 100, 0) . '%'
    : '0%';
  $p_n_exames_geral = $candidatos_n_geral != 0
    ? round(($candidatos_geral_exame_n / $candidatos_n_geral) * 100, 0) . '%'
    : '0%';

  $p_total_exames_geral = $total_geral != 0
    ? round(($total_exames_geral / $total_geral) * 100, 0) . '%'
    : '0%';

  // Cálculo de reprovados
  $p_m_reprovados_geral = $candidatos_geral_exame_m != 0
    ? round(($candidatos_geral_reprovado_m / $candidatos_geral_exame_m) * 100, 0) . '%'
    : '0%';
  $p_t_reprovados_geral = $candidatos_geral_exame_t != 0
    ? round(($candidatos_geral_reprovado_t / $candidatos_geral_exame_t) * 100, 0) . '%'
    : '0%';
  $p_n_reprovados_geral = $candidatos_geral_exame_n != 0
    ? round(($candidatos_geral_reprovado_n / $candidatos_geral_exame_n) * 100, 0) . '%'
    : '0%';



  $p_total_reprovados_geral = $total_exames_geral != 0
    ? round(($total_reprovados_geral / $total_exames_geral) * 100, 0) . '%'
    : '0%';

  // Cálculo de admitidos
  $p_m_admitidos_geral = $candidatos_geral_exame_m != 0
    ? round(($candidatos_geral_admitido_m / $candidatos_geral_exame_m) * 100, 0) . '%'
    : '0%';
  $p_t_admitidos_geral = $candidatos_geral_exame_t != 0
    ? round(($candidatos_geral_admitido_t / $candidatos_geral_exame_t) * 100, 0) . '%'
    : '0%';
  $p_n_admitidos_geral = $candidatos_geral_exame_n != 0
    ? round(($candidatos_geral_admitido_n / $candidatos_geral_exame_n) * 100, 0) . '%'
    : '0%';

  $p_total_admitidos_geral = $total_exames_geral != 0
    ? round(($total_admitidos_geral / $total_exames_geral) * 100, 0) . '%'
    : '0%';

  // Cálculo de matriculados
  $p_m_matriculados_geral = $candidatos_geral_exame_m != 0
    ? round(($candidatos_geral_matriculado_m / $candidatos_geral_exame_m) * 100, 0) . '%'
    : '0%';
  $p_t_matriculados_geral = $candidatos_geral_exame_t != 0
    ? round(($candidatos_geral_matriculado_t / $candidatos_geral_exame_t) * 100, 0) . '%'
    : '0%';
  $p_n_matriculados_geral = $candidatos_geral_exame_n != 0
    ? round(($candidatos_geral_matriculado_n / $candidatos_geral_exame_n) * 100, 0) . '%'
    : '0%';

  $p_total_matriculados_geral = $total_admitidos_geral != 0
    ? round(($total_matriculados_geral / $total_admitidos_geral) * 100, 0) . '%'
    : '0%';

          @endphp
                  </td>
                </tr>
                <tr>
                  <center>
                  <td class="bg-white" style="color: white;height:5px!important;" rowspan="5"></td>
                  <td rowspan="5" style="text-transform:uppercase">
                  </td>

                  <td class="text-left bg-exames">Exames</td>
                  <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_m_exames_geral }}</td>
                  <td class="text-center bg-exames font-weight-bold">
                    {{ $candidatos_geral_exame_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_exame_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_exame_m_f }}
                  </td>

                  <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_t_exames_geral }}</td>
                  <td class="text-center bg-exames font-weight-bold">
                    {{ $candidatos_geral_exame_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_exame_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_exame_t_f }}
                  </td>

                  <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_n_exames_geral }}</td>
                  <td class="text-center bg-exames font-weight-bold">
                    {{ $candidatos_geral_exame_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_exame_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_exame_n_f}}
                  </td>

                  <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_total_exames_geral }} </td>


                  <td class="text-center bg-exames font-weight-bold">
                    {{  $candidatos_geral_exame_n + $candidatos_geral_exame_m + $candidatos_geral_exame_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_geral_exame_m_m + $candidatos_geral_exame_n_m + $candidatos_geral_exame_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_geral_exame_m_f + $candidatos_geral_exame_n_f + $candidatos_geral_exame_t_f
                }}</td>

                  <td class="bg-white" style="color: white;font-size: 15px;" rowspan="5">as</td>
                  </center>
                </tr>
                <tr>
                  <td class="text-right bg-ausentes" style="font-size: 12px;">Ausentes</td>
                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_m_ausentes_geral }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                  {{ $candidatos_geral_ausente_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_ausente_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_ausente_m_f }}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_t_ausentes_geral }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                  {{ $candidatos_geral_ausente_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_ausente_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_ausente_t_f }}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_n_ausentes_geral }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                  {{ $candidatos_geral_ausente_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_ausente_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_ausente_n_f}}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_total_ausentes_geral }} </td>


                  <td class="text-center bg-ausentes font-weight-bold">
                  {{  $candidatos_geral_ausente_n + $candidatos_geral_ausente_m + $candidatos_geral_ausente_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_geral_ausente_m_m + $candidatos_geral_ausente_n_m + $candidatos_geral_ausente_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_geral_ausente_m_f + $candidatos_geral_ausente_n_f + $candidatos_geral_ausente_t_f
                }}</td>
                </tr>

                <tr>
                  <td class="text-right bg-reprovados" style="font-size: 12px;">Reprovados</td>
                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_m_reprovados_geral }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                  {{ $candidatos_geral_reprovado_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_reprovado_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_reprovado_m_f }}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_t_reprovados_geral }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                  {{ $candidatos_geral_reprovado_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_reprovado_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_reprovado_t_f }}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_n_reprovados_geral }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                  {{ $candidatos_geral_reprovado_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_reprovado_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_reprovado_n_f}}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_total_reprovados_geral }} </td>


                  <td class="text-center bg-reprovados font-weight-bold">
                  {{  $candidatos_geral_reprovado_n + $candidatos_geral_reprovado_m + $candidatos_geral_reprovado_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_geral_reprovado_m_m + $candidatos_geral_reprovado_n_m + $candidatos_geral_reprovado_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_geral_reprovado_m_f + $candidatos_geral_reprovado_n_f + $candidatos_geral_reprovado_t_f
                }}</td>
                </tr>

                <tr>
                  <td class="text-left bg-admitidos" style="font-size: 15px;">Admitidos</td>
                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_m_admitidos_geral }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                  {{ $candidatos_geral_admitido_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_admitido_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_admitido_m_f }}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_t_admitidos_geral }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                  {{ $candidatos_geral_admitido_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_admitido_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_admitido_t_f }}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_n_admitidos_geral }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                  {{ $candidatos_geral_admitido_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_admitido_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_admitido_n_f}}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_total_admitidos_geral }} </td>


                  <td class="text-center bg-admitidos font-weight-bold">
                  {{  $candidatos_geral_admitido_n + $candidatos_geral_admitido_m + $candidatos_geral_admitido_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_geral_admitido_m_m + $candidatos_geral_admitido_n_m + $candidatos_geral_admitido_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_geral_admitido_m_f + $candidatos_geral_admitido_n_f + $candidatos_geral_admitido_t_f
                }}</td>
                </tr>


                <tr>
                  <td class="text-center bg-matriculados text-uppercase font-weight-bold" style="font-size: 15px;">
                  Matriculados</td>
                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_m_matriculados_geral }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                  {{ $candidatos_geral_matriculado_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_matriculado_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_matriculado_m_f }}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_t_matriculados_geral }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                  {{ $candidatos_geral_matriculado_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_matriculado_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_geral_matriculado_t_f }}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_n_matriculados_geral }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                  {{ $candidatos_geral_matriculado_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_matriculado_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_geral_matriculado_n_f}}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_total_matriculados_geral }}
                  </td>


                  <td class="text-center bg-matriculados font-weight-bold">
                  {{  $candidatos_geral_matriculado_n + $candidatos_geral_matriculado_m + $candidatos_geral_matriculado_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_geral_matriculado_m_m + $candidatos_geral_matriculado_n_m + $candidatos_geral_matriculado_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_geral_matriculado_m_f + $candidatos_geral_matriculado_n_f + $candidatos_geral_matriculado_t_f
                }}</td>
                </tr>
                <tr style="height:30px !important">
                  <td class="bg-white"></td>
                </tr>
      @endforeach
              </table>


              <!-- Quadro Geral ✍️✍️✍️✍️✍️✍️✍️✍️✍️✍️✍️ de Estatísticas Global -->

              <br>
              <br>
              <br>
              <table class="table_te margin-new" style="width: 93%;margin-left:4%;margin-right:0;">

                <br>

                <tr class="line">
                  <th colspan="23" class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color">
                    <titles class="f-blue">Quadro 2:</titles> Estatísticas Global dos Departamentos
                  </th>
                </tr>
              </table>

              <table class="table_te" style="width: 90%;margin-left:4%;margin-right:0;">
                <tr class="line">
                  <th colspan="23" class="text-left text-white bg0 text-uppercase font-weight-bold f1">
                    QUADRO RESUMO DOS DEPARTAMENTOS
                  </th>
                </tr>

                <tr>
                  <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3">
                    #</th>
                  <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;" rowspan="3"
                    colspan="2">
                  </th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">M</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">T</th>
                  <th class="text-center bg1 font-weight-bold f3 pd" style="" colspan="4">N</th>
                  <th class="text-center bg1 font-weight-bold f3 pd1" style="" colspan="5">Total</th>


                </tr>
                <tr class="tr-new-line">

                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan="2">V</th>
                  <th class="text-center bg1  f3 pd t-color" style="" colspan="3">C</th>
                  <th class="text-center bg1  f3 pd" style="" rowspan=2>%</th>

                </tr>
                <tr class="tr-new-line" style="font-weight:bold">


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>


                  <th class="text-center bg1  f3 pd" style="">Total</th>
                  <th class="text-center bg1  f3 pd" style="">m</th>
                  <th class="text-center bg1  f3 pd" style="">f</th>
                </tr>
                <tr style="height:8px !important">
                  <td class="bg-white"></td>
                </tr>
                </tr>
                <tr class="f2">
                  <td class="text-center bg-white"></td>
                  <td class="text-left text-uppercase bg-white" style="width:350px" colspan="2">
                  </td>
                  <td class="text-center bg2 ">
                    {{ $manha_global != 0 ? $manha_global : '-' }}
                  </td>
                  <td class="text-center bg-total t-color ">
                    {{ $vaga_manha_global ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_manha_m_global ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_manha_f_global ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                    {{$tarde_global != 0 ? $tarde_global : '-' }}
                  </td>
                  <td class="text-center bg-total t-color">
                    {{$vaga_tarde_global ?? '-' }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_tarde_m_global ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_tarde_f_global ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                    {{ $noite_global != 0 ? $noite_global : '-' }}
                  </td>
                  <td class="text-center bg-total t-color">
                    {{ $vaga_noite_global ?? '-'}}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_noite_m_global ?? "-" }}
                  </td>
                  <td class="text-center bg-sexo t-color ">
                    {{ $vaga_noite_f_global ?? "-" }}
                  </td>
                  <td class="text-center bg2 ">
                    {{ $noite_global + $tarde_global + $manha_global != 0 ? $noite_global + $tarde_global + $manha_global : '-' }}
                  </td>
                  @php 
                    $total_global = $vaga_noite_global +
  $vaga_manha_global +
  $vaga_tarde_global;

          @endphp
                  <td class="text-center bg-total t-color">
                    {{ $total_global }}
                  </td>



                  <td class="text-center bg-sexo t-color">{{  
                  $vaga_noite_m_global +
  $vaga_tarde_m_global +
  $vaga_manha_m_global  
                }}
                  </td>


                  <td class="text-center bg-sexo t-color">{{
  $vaga_noite_f_global +
  $vaga_tarde_f_global +
  $vaga_manha_f_global  
                }}</td>
                  <td class="text-center bg-p_total">
                    @if ($total_global != 0)
            {{ (int) round(($total_global / ($noite_global + $manha_global + $tarde_global)) * 100, 0) }}%
          @else
      0%
    @endif
                    @php
//Totais 
$total_exames_global = $candidatos_global_exame_m +
  $candidatos_global_exame_t +
  $candidatos_global_exame_n;

$total_ausentes_global = $candidatos_global_ausente_m +
  $candidatos_global_ausente_t +
  $candidatos_global_ausente_n;

$total_admitidos_global = $candidatos_global_admitido_m +
  $candidatos_global_admitido_t +
  $candidatos_global_admitido_n;

$total_reprovados_global = $candidatos_global_reprovado_m +
  $candidatos_global_reprovado_t +
  $candidatos_global_reprovado_n;

$total_matriculados_global = $candidatos_global_matriculado_m +
  $candidatos_global_matriculado_t +
  $candidatos_global_matriculado_n;

// Cálculo de ausentes
$p_m_ausentes_global = $candidatos_m_global != 0
  ? round(($candidatos_global_ausente_m / $candidatos_m_global) * 100, 0) . '%'
  : '0%';
$p_t_ausentes_global = $candidatos_t_global != 0
  ? round(($candidatos_global_ausente_t / $candidatos_t_global) * 100, 0) . '%'
  : '0%';
$p_n_ausentes_global = $candidatos_n_global != 0
  ? round(($candidatos_global_ausente_n / $candidatos_n_global) * 100, 0) . '%'
  : '0%';

$p_total_ausentes_global = $total_global != 0
  ? round(($total_ausentes_global / $total_global) * 100, 0) . '%'
  : '0%';

// Cálculo de exames
$p_m_exames_global = $candidatos_m_global != 0
  ? round(($candidatos_global_exame_m / $candidatos_m_global) * 100, 0) . '%'
  : '0%';
$p_t_exames_global = $candidatos_t_global != 0
  ? round(($candidatos_global_exame_t / $candidatos_t_global) * 100, 0) . '%'
  : '0%';
$p_n_exames_global = $candidatos_n_global != 0
  ? round(($candidatos_global_exame_n / $candidatos_n_global) * 100, 0) . '%'
  : '0%';

$p_total_exames_global = $total_global != 0
  ? round(($total_exames_global / $total_global) * 100, 0) . '%'
  : '0%';

// Cálculo de reprovados
$p_m_reprovados_global = $candidatos_global_exame_m != 0
  ? round(($candidatos_global_reprovado_m / $candidatos_global_exame_m) * 100, 0) . '%'
  : '0%';
$p_t_reprovados_global = $candidatos_global_exame_t != 0
  ? round(($candidatos_global_reprovado_t / $candidatos_global_exame_t) * 100, 0) . '%'
  : '0%';
$p_n_reprovados_global = $candidatos_global_exame_n != 0
  ? round(($candidatos_global_reprovado_n / $candidatos_global_exame_n) * 100, 0) . '%'
  : '0%';



$p_total_reprovados_global = $total_exames_global != 0
  ? round(($total_reprovados_global / $total_exames_global) * 100, 0) . '%'
  : '0%';

// Cálculo de admitidos
$p_m_admitidos_global = $candidatos_global_exame_m != 0
  ? round(($candidatos_global_admitido_m / $candidatos_global_exame_m) * 100, 0) . '%'
  : '0%';
$p_t_admitidos_global = $candidatos_global_exame_t != 0
  ? round(($candidatos_global_admitido_t / $candidatos_global_exame_t) * 100, 0) . '%'
  : '0%';
$p_n_admitidos_global = $candidatos_global_exame_n != 0
  ? round(($candidatos_global_admitido_n / $candidatos_global_exame_n) * 100, 0) . '%'
  : '0%';

$p_total_admitidos_global = $total_exames_global != 0
  ? round(($total_admitidos_global / $total_exames_global) * 100, 0) . '%'
  : '0%';

// Cálculo de matriculados
$p_m_matriculados_global = $candidatos_global_exame_m != 0
  ? round(($candidatos_global_matriculado_m / $candidatos_global_exame_m) * 100, 0) . '%'
  : '0%';
$p_t_matriculados_global = $candidatos_global_exame_t != 0
  ? round(($candidatos_global_matriculado_t / $candidatos_global_exame_t) * 100, 0) . '%'
  : '0%';
$p_n_matriculados_global = $candidatos_global_exame_n != 0
  ? round(($candidatos_global_matriculado_n / $candidatos_global_exame_n) * 100, 0) . '%'
  : '0%';

$p_total_matriculados_global = $total_admitidos_global != 0
  ? round(($total_matriculados_global / $total_admitidos_global) * 100, 0) . '%'
  : '0%';

            @endphp
                  </td>
                </tr>
                <tr>
                  <center>
                    <td class="bg-white" style="color: white;height:5px!important;" rowspan="5"></td>
                    <td rowspan="5" style="text-transform:uppercase">
                    </td>

                    <td class="text-left bg-exames">Exames</td>
                    <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_m_exames_global }}</td>
                    <td class="text-center bg-exames font-weight-bold">
                      {{ $candidatos_global_exame_m }}
                    </td>
                    <td class="text-center bg-sexo">{{ $candidatos_global_exame_m_m }}
                    </td>
                    <td class="text-center bg-sexo">{{ $candidatos_global_exame_m_f }}
                    </td>

                    <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_t_exames_global }}</td>
                    <td class="text-center bg-exames font-weight-bold">
                      {{ $candidatos_global_exame_t }}
                    </td>
                    <td class="text-center bg-sexo">{{ $candidatos_global_exame_t_m }}
                    </td>
                    <td class="text-center bg-sexo">{{ $candidatos_global_exame_t_f }}
                    </td>

                    <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_n_exames_global }}</td>
                    <td class="text-center bg-exames font-weight-bold">
                      {{ $candidatos_global_exame_n }}
                    </td>
                    <td class="text-center bg-sexo">{{  $candidatos_global_exame_n_m }}
                    </td>
                    <td class="text-center bg-sexo">{{  $candidatos_global_exame_n_f}}
                    </td>

                    <td class="text-center bg-exames" style="font-size: 15px;">{{ $p_total_exames_global }} </td>


                    <td class="text-center bg-exames font-weight-bold">
                      {{  $candidatos_global_exame_n + $candidatos_global_exame_m + $candidatos_global_exame_t}}
                    </td>
                    <td class="text-center bg-sexo">{{ 
                $candidatos_global_exame_m_m + $candidatos_global_exame_n_m + $candidatos_global_exame_t_m
                }}</td>
                    <td class="text-center bg-sexo">{{ 
                 $candidatos_global_exame_m_f + $candidatos_global_exame_n_f + $candidatos_global_exame_t_f
                }}</td>

                    <td class="bg-white" style="color: white;font-size: 15px;" rowspan="5">as</td>
                  </center>
                </tr>
                <tr>
                  <td class="text-right bg-ausentes" style="font-size: 12px;">Ausentes</td>
                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_m_ausentes_global }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                    {{ $candidatos_global_ausente_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_ausente_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_ausente_m_f }}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_t_ausentes_global }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                    {{ $candidatos_global_ausente_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_ausente_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_ausente_t_f }}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_n_ausentes_global }}</td>
                  <td class="text-center bg-ausentes font-weight-bold">
                    {{ $candidatos_global_ausente_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_ausente_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_ausente_n_f}}
                  </td>

                  <td class="text-center bg-ausentes" style="font-size: 15px;">{{ $p_total_ausentes_global }} </td>


                  <td class="text-center bg-ausentes font-weight-bold">
                    {{  $candidatos_global_ausente_n + $candidatos_global_ausente_m + $candidatos_global_ausente_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_global_ausente_m_m + $candidatos_global_ausente_n_m + $candidatos_global_ausente_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_global_ausente_m_f + $candidatos_global_ausente_n_f + $candidatos_global_ausente_t_f
                }}</td>
                </tr>

                <tr>
                  <td class="text-right bg-reprovados" style="font-size: 12px;">Reprovados</td>
                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_m_reprovados_global }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                    {{ $candidatos_global_reprovado_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_reprovado_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_reprovado_m_f }}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_t_reprovados_global }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                    {{ $candidatos_global_reprovado_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_reprovado_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_reprovado_t_f }}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_n_reprovados_global }}</td>
                  <td class="text-center bg-reprovados font-weight-bold">
                    {{ $candidatos_global_reprovado_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_reprovado_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_reprovado_n_f}}
                  </td>

                  <td class="text-center bg-reprovados" style="font-size: 15px;">{{ $p_total_reprovados_global }} </td>


                  <td class="text-center bg-reprovados font-weight-bold">
                    {{  $candidatos_global_reprovado_n + $candidatos_global_reprovado_m + $candidatos_global_reprovado_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_global_reprovado_m_m + $candidatos_global_reprovado_n_m + $candidatos_global_reprovado_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_global_reprovado_m_f + $candidatos_global_reprovado_n_f + $candidatos_global_reprovado_t_f
                }}</td>
                </tr>

                <tr>
                  <td class="text-left bg-admitidos" style="font-size: 15px;">Admitidos</td>
                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_m_admitidos_global }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                    {{ $candidatos_global_admitido_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_admitido_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_admitido_m_f }}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_t_admitidos_global }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                    {{ $candidatos_global_admitido_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_admitido_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_admitido_t_f }}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_n_admitidos_global }}</td>
                  <td class="text-center bg-admitidos font-weight-bold">
                    {{ $candidatos_global_admitido_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_admitido_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_admitido_n_f}}
                  </td>

                  <td class="text-center bg-admitidos" style="font-size: 15px;">{{ $p_total_admitidos_global }} </td>


                  <td class="text-center bg-admitidos font-weight-bold">
                    {{  $candidatos_global_admitido_n + $candidatos_global_admitido_m + $candidatos_global_admitido_t}}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_global_admitido_m_m + $candidatos_global_admitido_n_m + $candidatos_global_admitido_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_global_admitido_m_f + $candidatos_global_admitido_n_f + $candidatos_global_admitido_t_f
                }}</td>
                </tr>


                <tr>
                  <td class="text-center bg-matriculados text-uppercase font-weight-bold" style="font-size: 15px;">
                    Matriculados</td>
                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_m_matriculados_global }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                    {{ $candidatos_global_matriculado_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_matriculado_m_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_matriculado_m_f }}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_t_matriculados_global }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                    {{ $candidatos_global_matriculado_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_matriculado_t_m }}
                  </td>
                  <td class="text-center bg-sexo">{{ $candidatos_global_matriculado_t_f }}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_n_matriculados_global }}</td>
                  <td class="text-center bg-matriculados font-weight-bold">
                    {{ $candidatos_global_matriculado_n }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_matriculado_n_m }}
                  </td>
                  <td class="text-center bg-sexo">{{  $candidatos_global_matriculado_n_f}}
                  </td>

                  <td class="text-center bg-matriculados" style="font-size: 15px;">{{ $p_total_matriculados_global }}
                  </td>


                  <td class="text-center bg-matriculados font-weight-bold">
                    {{  $candidatos_global_matriculado_n + $candidatos_global_matriculado_m + $candidatos_global_matriculado_t }}
                  </td>
                  <td class="text-center bg-sexo">{{ 
                $candidatos_global_matriculado_m_m + $candidatos_global_matriculado_n_m + $candidatos_global_matriculado_t_m
                }}</td>
                  <td class="text-center bg-sexo">{{ 
                 $candidatos_global_matriculado_m_f + $candidatos_global_matriculado_n_f + $candidatos_global_matriculado_t_f
                }}</td>
                </tr>
                <tr style="height:30px !important">
                  <td class="bg-white"></td>
                </tr>

                <tr>
                  <td class="bg-white"></td>
                </tr>
                <!-- <tr class="last-line">
                  <td class="bg-white"></td>
                  <td class="bg-white f1" colspan="2"><b>TOTAL</b></td>
                  <td class="text-center bg4 f3 font-weight-bold">{{ $m }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="3">{{ $m_c }}</td>

                  <td class="text-center bg4 f3 font-weight-bold">{{ $t }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="3">{{ $t_c }}</td>

                  <td class="text-center bg4 f3 font-weight-bold">{{ $n }}</td>
                  <td class="text-center bg4 f3 font-weight-bold" colspan="3">{{ $n_c }}</td>

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

                </tr> -->
              </table>


              <!-- ✍️✍️✍️✍️✍️✍️✍️✍️✍️✍️ -->




              <div class="row margin-new">
                <div class="col-5">

                  <table id="acomula" class="table_te ta"
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
                          colspan="4">
                          <titles class="f-blue">Quadro 3:</titles> candidaturas por dia
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
                  <br><br><br><br><br><br>
                </div>
                <div class="col-8">

                  <table class="table_te ta" style="left: -1;margin:0%!important;page-break-before: always;">
                    <thead>

                      @for ($i = 0; $i < 20; $i++)
              <tr>
              <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                colspan="3"></th>
              </tr>
            @endfor
                      <tr>
                        <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                          colspan="3">
                          <titles class="f-blue">Quadro 4:</titles> candidaturas por Staff
                        </th>
                      </tr>
                      <tr>
                        <th class="text-left bg1 font-weight-bold f3 pd" style="width: 20px!IMPORTANT">#</th>
                        <th class="text-left bg1 font-weight-bold f3 pd">STAFF CANDIDATURAS </th>
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
          {{ round(($item['inscricao'] / $total_staff) * 100, 0) }}%
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
                        <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 ">#</th>
                        <th class="text-left text-BLACK bg-white  text-uppercase font-weight-bold f1 t-color"
                          style="font-size: 18px!important;" colspan="3">
                          <titles class="f-blue">Quadro 5:</titles> Resumo das candidaturas
                        </th>
                      </tr>
                      <tr>

                      </tr>
                    </thead>
                    @php
$p_total = isset($emolumentos_espera['pending']['total']) ? $emolumentos_espera['pending']['total'] : 0;
$c_iniciadas = $todos_candidatos + 0;
$c_eliminadas = $todos_candidatos - $total_staff;
$e_eliminadas = $p_total;
$e_lancados = isset($emolumentos['total']) ? $emolumentos['total'] : 0;


$total = isset($emolumentos['total']) ? $emolumentos['total'] : 0;
$pending = isset($emolumentos['pending']) ? $emolumentos['pending'] : 0;
$total_money = isset($emolumentos['total_money']) ? $emolumentos['total_money'] : 0;
$pending_money = isset($emolumentos['espera_money']) ? $emolumentos['espera_money'] : 0;

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
                        <td class="text-center bg2 f3">{{ ($total + $pending) }}</td>
                      </tr>
                      <!-- <tr>
                        <td class="text-left bg2 f3">2</td>
                        <td class="text-left bg2 f3">Candidaturas NÃO CONCLUIDAS</td>
                        <td class="text-center bg2 f3">{{ ($c_iniciadas - ($total + $pending)) }}</td>
                      </tr> -->
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
                        <td class="text-center bg2 f3">
                          {{ $twoCourseUsers}}
                        </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
                <div class="col-6">
                  <table class="table_te ta" style="left: -20;margin:0%!important;">
                    <thead>
                      <tr>
                        <th class="text-left  bg-white font-weight-bold f1">#</th>
                        <th class="text-left  bg-white  text-uppercase font-weight-bold f1 t-color" colspan="3">
                          <titles class="f-blue">Quadro 6:</titles> Resumo financeiro
                        </th>
                      </tr>
                      <tr>

                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left bg2 f3">1</td>
                        <td class="text-left bg2 f3">Emolumentos LANÇADOS</td>
                        <td class="text-center bg2 f3">{{ ($total + $pending)  }}</td>
                        <td class="text-center bg2 f3">

                          {{ number_format(($total_money + $pending_money), 0, ',', '.') . ' kz' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left bg2 f3">2</td>
                        <td class="text-left bg2 f3">Emolumentos LIQUIDADOS</td>
                        <td class="text-center bg2 f3">{{ $total }}</td>
                        <td class="text-center bg2 f3">
                          {{ number_format($total_money, 0, ',', '.') . ' kz' }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left bg2 f3">3</td>
                        <td class="text-left bg2 f3">Emolumentos EM ESPERA</td>
                        <td class="text-center bg2 f3">{{ $pending }}</td>
                        <td class="text-center bg2 f3">
                          {{ number_format($pending_money, 0, ',', '.') . ' kz' }}
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
</div> --}}@endsection