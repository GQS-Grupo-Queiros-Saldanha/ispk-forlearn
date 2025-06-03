
@extends('layouts.print')
<title>
    PLANO CURRICULAR DAS DISCIPLINAS | forLEARN
</title>
@section('content')


<style>

  .td-parameter-column {
    padding-left: 1px !important;
  }

  label {
    font-weight: bold;
    font-size: .75rem;
    color: #000;
    margin-bottom: 0;
  }


  table {
    page-break-inside: auto;

  }

  #table-study thead {
    display: table-row-group;
  }

  thead {
    display: table-header-group;

    border: 1px solid rgba(0, 0, 0, 0);
  }

  tfoot {
    display: table-footer-group
  }

  .corpo-element {
    margin-left: 15px;
    margin-right: 15px;
  }

  p {
    width: 100%;
    margin: 0px;
    padding: 0px;

  }

  tbody tr td {
    vertical-align: middle !important;
    font-weight: 700;
  }

  #table-study tbody tr:nth-child(2n+1) {
    background-color: rgb(241, 241, 241);
  }

  /* .f_td{
            text-align: center;
            width: 80px;
        } */


  /* ///// */

  th,
  td {
    padding: 1px;
    text-align: left;
    border: 5px solid white;
    font-family: 'IBM Plex Mono', monospace;
    white-space: nowrap;
  }

  .container {
    width: 100%;
  }

  th {
    background-color: #8eaadb !important;
    color: white;
    font-size: 1.1em;
    text-align: center;
    font-weight: bold;
  }

  span {
    color: white;
    font-size: 1.1em;
    text-align: center;
    font-weight: bold;
  }

  td.number-cell {
    background-color: #2f5496;
    color: white;
    font-weight: bold;
    text-align: center;
    font-size: 1.5em;
    font-family: 'Bebas Neue', sans-serif;
    padding: 1px;
    width: 0.5em;
    min-width: 0.5em;
    white-space: nowrap;
    line-height: 1;
  }

  .bg-table tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  .invisible {
    visibility: hidden;
    border: none;
  }

  .header-title {
    background-color: #2f5496 !important;
    color: white;
    font-size: 1.5em;
    text-align: center;
    font-weight: bold;
    padding: 1px;
    border: 10px solid white;
    text-transform: uppercase;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    page-break-inside: auto;
    overflow: hidden;
    table-layout: auto;
  }

  thead {
    display: table-header-group;
  }

  tbody {
    display: table-row-group;
  }

  th,
  td {
    padding: 0px;
    /* Diminui o espaçamento */
    text-align: left;
    border: 8px solid #ffffff;
    font-size: 1.5em;
    /* Diminui o tamanho da fonte */
  }

  th {
    background-color: #2f5496;
    color: white;
    font-size: 1em;
    /* Diminui o tamanho da fonte */
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  td {
    font-size: 1.01em;
    /* Diminui o tamanho da fonte */
    color: #333;
  }

  .header {
    font-weight: bold;
    background-color: #e1f0ff;
    color: #2f5496;
    font-size: 1em;
    /* Diminui o tamanho da fonte */
  }

  .section-title {
    background-color: #2f5496;
    color: white;
    font-size: 1.1em;
    font-weight: bold;
    /* Diminui o tamanho da fonte */
    text-align: center;
    padding: 4px;
    /* Diminui o padding */
    margin-top: 5px;
    border-radius: 5px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .section-content {
    padding: 3px;
    /* Diminui o padding */
    background-color: #f4f7fa;
    margin-bottom: 1px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 0.85em;
    /* Diminui o tamanho da fonte */
  }

  ol,
  ul {
    margin: 0;
    padding-left: 20px;
    /* Diminui o espaçamento */
  }

  p {
    margin: 0 0 1px;
    font-size: 1.40em;
    /* Diminui o espaçamento */
  }


  li {
    margin: 0 0 1px;
    font-size: 1.40em;
    /* Diminui o espaçamento */
  }

  .section-title {
    background-color: #2f5496;
    color: white;
    font-size: 1.1em;
    font-weight: bold;
    text-align: center;
    padding: 4px;
    margin-top: 5px;
    border-radius: 5px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .section-content {
    padding: 3px;
    background-color: #f4f7fa;
    margin-bottom: 1px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 0.85em;
  }

  ol {
    margin: 0;
    padding-left: 20px;
    counter-reset: item;
  }

  li {
    list-style-type: none;
    margin: 0 0 1px;
    font-size: 1.2em;
    color: #333;
    position: relative;
    padding-left: 25px;
  }

  li:before {
    content: "•";
    /* Substitua por qualquer símbolo desejado, como "•", "→", "★", etc. */
    position: absolute;
    left: 0;
    color: #2f5496;
    /* Cor do símbolo */
    font-weight: bold;
    font-size: 1.5em;
    /* Ajuste o tamanho conforme necessário */
  }

  .left-column,
  .right-column {
    width: 48%;
    display: inline-block;
    vertical-align: top;
  }

  .page-break {
      page-break-before: always;
    }

</style>
@php
  $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
  $documentoCode_documento = 50;
  $doc_name = 'Plano Curricular das Disciplinas';
  $discipline_code = '';
@endphp

@include('Reports::pdf_model.forLEARN_header')
<!-- aqui termina o cabeçalho do pdf -->
<main id="content">
    <div class="corpo-element">
    <div class="table-wrapper">

    @foreach($disciplines as $discipline)
   
    

    
    <table>

        @foreach ($languages as $language)
      <tr>
        <td class="header text-center">
        Codigo:
        </td>
        <td>
        {{ $discipline->code }}
        </td>
        <td class="header text-center">
        Disciplina:
        </td>
        <td colspan="12" class="text-center">
        {{ $discipline->translations->firstWhere('language_id', $language->id)->display_name }}
        </td>
      </tr>
    @endforeach
        @foreach ($discipline->study_plans_has_disciplines as $studyPlanHasDiscipline)
      <tr>
        <td class="header text-center">Ano de estudo:</td>
        <td>{{$studyPlanHasDiscipline->years}}º ano</td>
        @foreach ($discipline->study_plans_has_disciplines as $studyPlanHasDiscipline)
        <td class="header text-center">Regime:</td>
        @if ($studyPlanHasDiscipline->discipline_period->currentTranslation->display_name == 'Anual')
      <td>{{ $studyPlanHasDiscipline->discipline_period->currentTranslation->display_name }}</td>
    @else
    <td>Semestral</td>
  @endif

        <td colspan="1" class="header text-center">Semestre:</td>
        @if ($studyPlanHasDiscipline->discipline_period->currentTranslation->display_name == 'Anual')
      <td>1º Semestre e 2º Semestre</td>
    @else
    <td>{{ $studyPlanHasDiscipline->discipline_period->currentTranslation->display_name }}</td>
  @endif
        </tr>
      @endforeach
      <tr>
      <tr>
        @foreach ($studyPlanHasDiscipline->study_plans_has_discipline_regimes as $discipline_regime)

      <td class="header text-center">{{  $discipline_regime->discipline_regime->currentTranslation->display_name}}:
      </td>
      <td>{{ $discipline_regime->hours }}</td>

    @endforeach
        <td class="header text-center">Total</td>
        <td>{{ $studyPlanHasDiscipline->total_hours }}</td>
      </tr>
      </tr>
    @endforeach
      </table>

      <div class="section-title">Objectivos</div>
      <div class="section-content">
        <p>{{ $discipline->currentTranslation->objectives }}</p>
      </div>

      <div class="section-title">Resultados de Aprendizagem</div>
      <div class="section-content">
        <p>{{ $discipline->currentTranslation->learning_outcomes }} </p>
      </div>

      <div class="section-title">Temas</div>
      <div class="section-content">
        <div class="left-column">
          <ol>
            @if ($discipline->currentTranslation->topics)
        @foreach (json_decode($discipline->currentTranslation->topics) as $index => $topic)
      @if ($index % 2 == 0) <!-- Exibe temas na coluna da esquerda -->
      <li>{{ $topic }}</li>
    @endif
    @endforeach
      @else
    <p>Sem temas</p>
  @endif
          </ol>
        </div>

        <div class="right-column">
          @if ($discipline->currentTranslation->topics)
        <ol start="{{ ceil(count(json_decode($discipline->currentTranslation->topics)) / 2) + 1 }}">
        @foreach (json_decode($discipline->currentTranslation->topics) as $index => $topic)
      @if ($index % 2 != 0) <!-- Exibe temas na coluna da direita -->
      <li>{{ $topic }}</li>
    @endif
    @endforeach
    @else
    <p>Sem temas</p>
  @endif
          </ol>
        </div>
      </div>


      <div class="section-title">Bibliografia</div>
      <div class="section-content">
        <ul>
          @if ($discipline->currentTranslation->bibliography)
        @foreach (json_decode($discipline->currentTranslation->bibliography) as $bibliography)
      <li>{{ $bibliography }}</li>
    @endforeach
      @else
      <p>Sem Bibliografias</p>
    @endif
        </ul>
      </div>

      <div class="section-title">Métodos de Ensino</div>
      <div class="section-content">
        <p>{{ $discipline->currentTranslation->teaching_methods }}</p>
      </div>

      <div class="section-title">Estratégia de Avaliação</div>
      <div class="section-content">
        <p>{{ $discipline->currentTranslation->assessment_strategy }}</p>
      </div>

    
      <p class="page-break text-white">olá</p>
 
    @endforeach

    </div>
    </div>
    </main>
      
    


@endsection