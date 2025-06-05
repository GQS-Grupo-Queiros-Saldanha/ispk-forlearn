{{-- @include('GA::budget.pdf.cabecalho') --}}



@extends('layouts.print')
@section('title', 'Plano de horário do docente')
@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">


<style>
  @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

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
    font-size: 1.1em;
    text-align: center;
    font-weight: bold;
    border-radius: 5px solid white;
    text-transform: uppercase;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: #fff;
    box-shadow: 0 2px 4px #ffffff;
  }

  th,
  td {
    padding: 8px;
    /* Diminui o espaçamento */
    text-align: left;
    border: 10px solid #ffffff;
    /* Ajusta a borda das células */
    font-size: 0.9em;
    /* Diminui o tamanho da fonte */
  }

  th {
    background-color: #2f5496;
    color: white;
    font-size: 1em;
    /* Ajusta o tamanho da fonte do cabeçalho */
    text-transform: uppercase;
    letter-spacing: 0.01em;
  }

  .table-docente td {
    background-color: #e1f0ff;
    font-size: 0.85em;
    /* Ajusta o tamanho da fonte das células */
    color: #333;
  }

  .header {
    font-weight: bold;
    background-color: #e1f0ff;
    color: #2f5496;
    font-size: 0.85em;
    /* Ajusta o tamanho da fonte da classe header */
  }

  .section-title {
    background-color: #2f5496;
    color: white;
    font-size: 1.2em;
    /* Ajusta o tamanho da fonte do título da seção */
    text-align: center;
    padding: 0px;
    /* Ajusta o padding */
    margin-top: 20px;
    border-radius: 5px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .course {
    border: 0px solid #ffffff;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 4px 8px #ffffff;
    border-radius: 8px;
  }

  .course,
  th {
    /* Define uma largura fixa para as células */
    overflow: hidden;
    /* Esconde conteúdo que exceder o tamanho */
    text-overflow: ellipsis;
    /* Adiciona reticências (...) caso o texto seja maior que a célula */
    white-space: nowrap;
    /* Evita quebra de linha no conteúdo */
  }

  .course td {
    width: 100%;
  }


  .section-content {
    padding: 0px;
    /* Ajusta o padding */
    background-color: #f4f7fa;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 0.85em;
    /* Ajusta o tamanho da fonte */
  }

  ol,
  ul {
    margin: 0;
    padding-left: 18px;
    /* Ajusta o espaçamento */
  }

  p {
    margin: 0 0 8px;
    /* Ajusta o espaçamento */
  }

  li {
    list-style-type: none;
    margin: 0 0 1px;
    font-size: 1em;
    color: #333;
    position: relative;
    padding-left: 25px;
  }

  li:before {
    display: flex;
    content: "•";
    /* Substitua por qualquer símbolo desejado, como "•", "→", "★", etc. */
    position: absolute;
    left: 0;
    color: #2f5496;
    /* Cor do símbolo */
    /* Ajuste o tamanho conforme necessário */
  }
</style>
@php
  $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
  $documentoCode_documento = 50;
  $doc_name = 'Plano de Horário do docente';
  $discipline_code = '';
@endphp

@include('Reports::pdf_model.forLEARN_header')
<!-- aqui termina o cabeçalho do pdf -->
<main id="content">
  <div class="corpo-element">
    <div class="table-wrapper">
      <table class="table-docente">
        <thead>
          <t>
            <th class="text-center">
              Docente
            </th>
            <td><b>{{ $user->name }}</b></td>
            <th class="text-center">
              Email
            </th>
            <td><b>{{ $user->email }}</b></td>
            <th class="text-center">
              B.I.
            </th>
            @foreach ($user->user_parameters->where('users_id', $user->id)->where('parameters_id', 14) as $parameters)
        <td>{{ $parameters->value }}</td>
      @endforeach
            <th class="text-center">
              Telemóvel
            </th>
            @foreach ($user->user_parameters->where('users_id', $user->id)->where('parameters_id', 39) as $parameters)
        <td>{{ $parameters->value }}</td>
      @endforeach
            </tr>
        </thead>
      </table>
      <table class="course table-docente">
        <tbody>
          <tr>
            <th class="text-center">Departamento: </th>
            <td> @forelse ($userDepartment as $item)
          {{ $item->display_name }}
        @empty
        N/A
      @endforelse</td>
          </tr>
        </tbody>
      </table>
      @foreach($user->courses as $curso)
      <table class="course table-docente">
      <div>
        <tr>
        <th class="text-center">Curso</th>
        <td>
          <ul class="styled-list">
          <li>{{   $curso->currentTranslation->display_name  }}</li>
          </ul>
        </td>
        </tr>
        <tr>
        <th class="text-center">Disciplinas</th>
        <td>
          <ul class="styled-list">
          @foreach($user->disciplines->where('courses_id', $curso->id) as $disciplina)
        <li>{{ $disciplina->currentTranslation->display_name }}</li>
      @endforeach
          </ul>
        </td>
        </tr>
        <tr>
        <th class="text-center">Turmas</th>
        <td>
          <ul class="styled-list">
          @foreach($user->classes->where('courses_id', $curso->id) as $turma)
        <li>{{ $turma->display_name }}</li>
      @endforeach
          </ul>
        </td>
        </tr>
      </div>
      </table>
    @endforeach
    </div>
  </div>
</main>
@endsection