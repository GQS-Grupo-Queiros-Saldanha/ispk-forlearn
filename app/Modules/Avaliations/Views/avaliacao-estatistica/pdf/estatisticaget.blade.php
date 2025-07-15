@extends('layouts.print')

@php
    $doc_name = 'Dados Estatisticos Geral';
    $discipline_code = '';
@endphp

@include('Reports::pdf_model.forLEARN_header')

<style>
    /* Fonte e Cores Gerais */
    body {
        font-family: 'Arial', sans-serif;
        color: #2c3e50;
    }

    /* Cabeçalho do Relatório */
    .report-header {
        text-align: center;
        padding-bottom: 15px;
        border-bottom: 2px solid #007bff;
    }

    .report-title {
        font-size: 18px;
        font-weight: 600;
        color: #007bff;
        text-transform: uppercase;
    }

    .report-subtitle {
        font-size: 14px;
        color: #6c757d;
    }

    /* Tabela Principal */
    .ranking-table, .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        font-size: 11px;
    }

    .ranking-table th, 
    .ranking-table td,
    .table th,
    .table td {
        padding: 8px 6px;
        text-align: center;
        border: 1px solid #dee2e6;
    }

    /* Cabeçalho da Tabela */
    .ranking-table th,
    .table thead th {
        background-color: #007bff;
        color: #ffffff;
        font-weight: bold;
        text-transform: uppercase;
    }

    .table-light th {
        background-color: #cce5ff !important;
        color: #003366;
    }

    /* Linhas Alternadas */
    .ranking-table tr:nth-child(even),
    .table tbody tr:nth-child(even) {
        background-color: #f1f8ff;
    }

    /* Destaques para melhores posições (se aplicável) */
    .top-1 {
        background-color: #d1ecf1 !important;
        font-weight: 600;
    }

    .top-2 {
        background-color: #bee5eb !important;
    }

    .top-3 {
        background-color: #abdde5 !important;
    }

    /* Células de nota */
    .grade-cell {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        text-align: right;
        padding-right: 12px !important;
    }

    /* Coluna de Curso */
    .fw-semibold.bg-light {
        background-color: #e3f2fd !important;
        font-weight: bold;
        color: #003366;
    }

    /* Rodapé */
    .report-footer {
        padding-top: 12px;
        border-top: 1px solid #ced4da;
        font-size: 10px;
        color: #6c757d;
        text-align: right;
    }

    /* Impressão */
    @media print {
        .ranking-table {
            page-break-inside: avoid;
        }
        body {
            padding: 1cm;
        }
    }
</style>

@section('content')
<main>
    @include('Reports::pdf_model.pdf_header')
    <table class="table table-bordered table-hover table-sm mb-0">
        <thead class="table-light">
            <tr>
                <th rowspan="2" class="align-middle text-center bg-light">Curso</th>
                <th colspan="5" class="text-center">1.º Ano</th>
                <th colspan="5" class="text-center">2.º Ano</th>
                <th colspan="5" class="text-center">3.º Ano</th>
                <th colspan="4" class="text-center">4.º Ano</th>
                <th colspan="5" class="text-center">5.º Ano</th>
            </tr>
            <tr>
                @for ($ano = 1; $ano <= 5; $ano++)
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    @if (!($ano === 4))
                        <th class="text-center">N</th>
                    @endif
                    <th class="text-center">Prot.</th>
                    <th class="text-center">Total</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $c)
                @php
                    $codTurma = $c->code;
                @endphp
                <tr>
                    <td class="fw-semibold bg-light">{{ $codTurma }}</td>
                    @for ($ano = 1; $ano <= 5; $ano++)
                        <td class="text-center">
                            {{ $estatisticas[$codTurma][$ano]['M'] ?? 0 }}
                        </td>
                        <td class="text-center">
                            {{ $estatisticas[$codTurma][$ano]['T'] ?? 0 }}
                        </td>
                        @if (!($ano === 4))
                            <td class="text-center">
                                {{ $estatisticas[$codTurma][$ano]['N'] ?? 0 }}
                            </td>
                        @endif
                        <td class="text-center">
                            {{ $estatisticas[$codTurma][$ano]['PT'] ?? 0 }}
                        </td>
                        <td class="text-center">
                            {{ $estatisticas[$codTurma][$ano]['TOTAL'] ?? 0 }}
                        </td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
    

    <div class="report-footer">
        Relatório gerado em {{ date('d/m/Y H:i') }} | Sistema Learn
    </div>
</main>
@endsection