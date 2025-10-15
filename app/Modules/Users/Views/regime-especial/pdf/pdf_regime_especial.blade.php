@extends('layouts.print')
@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<title>Estudantes em Regime Especial | forLEARN</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

    .td-parameter-column {
        padding-left: 5px !important;
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
        display: table-footer-group;
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

    th,
    td {
        padding: 4px;
        text-align: left;
        border: 10px solid white;
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

    td.number-cell {
        background-color: #2f5496;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 1.5em;
        font-family: 'Bebas Neue', sans-serif;
        padding: 2px;
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
        padding: 10px;
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

    table td:nth-child(1),
    table th:nth-child(1) {
        width: 20px;
    }

    table td:nth-child(2),
    table th:nth-child(2) {
        width: 40px;
    }

    table td:nth-child(3),
    table th:nth-child(3) {
        width: 40px;
    }

    table td:nth-child(4),
    table th:nth-child(4) {
        width: 10px;
    }

    table td:nth-child(5),
    table th:nth-child(5) {
        width: 40px;
    }

    table td:nth-child(6),
    table th:nth-child(6) {
        width: 40px;
    }

    table td:nth-child(7),
    table th:nth-child(7) {
        width: 40px;
    }

    table td:nth-child(8),
    table th:nth-child(8) {
        width: 40px;
    }

    table td:nth-child(9),
    table th:nth-child(9) {
        width: 40px;
    }

    table td:nth-child(10),
    table th:nth-child(10) {
        width: 40px;
    }

    table td:nth-child(11),
    table th:nth-child(11) {
        width: 40px;
    }
</style>

@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
    $documentoCode_documento = 50;
    $doc_name = 'Estudantes em Regime Especial';
@endphp

@include('Reports::pdf_model.forLEARN_header')


    <main id="content">
        <div class="corpo-element">
            <div class="table-wrapper">
               
                <table class="article-table bg-table">
                    <thead>
                        <tr>
                            <th class="invisible">#</th>
                            <th>Estudantes</th>
                            <th>N.º Matrícula</th>
                            <th>E-mail</th>
                            <th>Curso</th>
                            <th>Rotação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php    $i = 0; @endphp
                        @foreach($regime_especial as $item)
                                        @php        $i++; @endphp
                                        <tr class="tr">
                                            <td class="number-cell">{{ $i }}</td>
                                            <td class="td">{{ $item->name }}</td>
                                            <td class="td">{{ $item->matricula }}</td>
                                            <td class="td">{{ $item->email }}</td>
                                            <td class="td">{{ $item->display_name }}</td>
                                            <td class="td">{{ $item->rotacao ?? 'Não definido'}}</td>
                                          
                                           
                                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
       
    </main>
