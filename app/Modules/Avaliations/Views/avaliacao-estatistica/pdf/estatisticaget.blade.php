@extends('layouts.print')

@php
    $doc_name = 'Dados Estatisticos Geral';
    $discipline_code = '';
@endphp

@include('Reports::pdf_model.forLEARN_header')

<style>
    /* Estilos Gerais */
    body {
        font-family: 'Arial', sans-serif;
        color: #333;
        /*line-height: 1.4;*/
    }
    
    /* Layout da Tabela */
    .ranking-table {
        width: 100%;
        /*border-collapse: collapse;*/
        margin: 15px 0;
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }
    
    .ranking-table th, 
    .ranking-table td {
        padding: 10px 8px;
        text-align: left;
        /*border-bottom: 1px solid #e0e0e0*/
    }
    
    .ranking-table th {
        background-color: #2c3e50;
        color: white;
        font-weight: 500;
        font-size: 11px;
        text-transform: uppercase;
        /*letter-spacing: 0.5px;*/
    }
    
    .ranking-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .ranking-table tr:hover {
        background-color: #f1f1f1;
    }
    
    /* Cabeçalho do Relatório */
    .report-header {
        text-align: center;
        /*margin-bottom: 20px;*/
        padding-bottom: 15px;
        border-bottom: 2px solid #2c3e50;
    }
    
    .report-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        /*margin-bottom: 5px;*/
        text-transform: uppercase;
    }
    
    .report-subtitle {
        font-size: 14px;
        color: #7f8c8d;
       /* margin-bottom: 10px;*/
    }
    
    /* Destaques para os melhores */
    .top-1 {
        background-color: #ffeaa7 !important;
        font-weight: 600;
    }
    
    .top-2 {
        background-color: #fdcb6e !important;
    }
    
    .top-3 {
        background-color: #fab1a0 !important;
    }
    
    /* Medalhas para os primeiros lugares */
    .rank-cell {
        position: relative;
        font-weight: bold;
    }
    
    .rank-1::before {
        content: "🥇";
        margin-right: 5px;
    }
    
    .rank-2::before {
        content: "🥈";
        margin-right: 5px;
    }
    
    .rank-3::before {
        content: "🥉";
        margin-right: 5px;
    }
    
    /* Notas */
    .grade-cell {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        text-align: right;
        padding-right: 15px !important;
    }
    
    /* Rodapé */
    .report-footer {
       /* margin-top: 20px;*/
        padding-top: 10px;
        border-top: 1px solid #e0e0e0;
        font-size: 10px;
        color: #7f8c8d;
        text-align: right;
    }

    
    /* Responsividade */
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