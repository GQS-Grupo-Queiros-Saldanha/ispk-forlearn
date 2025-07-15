@extends('layouts.print')

@php
    $doc_name = 'Dados Estatisticos Geral';
    $discipline_code = '';
@endphp

@include('Reports::pdf_model.forLEARN_header')

<style>
    /* Estilos Gerais Modernizados */
    
    body {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: #2d3748;
        line-height: 1.5;
        background-color: #f8fafc;
    }
    
    /* Layout da Tabela Moderno */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 20px 0;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
    }
    
   
    
    .table th {
        background-color: #4f46e5;
        color: white;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-color: #4f46e5;
    }
    
    .table tr:nth-child(even) {
        background-color: #f8fafc;
    }
    
    .table tr:hover {
        background-color: #f1f5f9;
    }
    
    .table thead tr:first-child th:first-child {
        border-top-left-radius: 8px;
    }
    
    .table thead tr:first-child th:last-child {
        border-top-right-radius: 8px;
    }
    
    .table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 8px;
    }
    
    .table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 8px;
    }
    
    /* Cabeçalho Moderno */
    .report-header {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .report-title {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }
    
    .report-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 400;
    }
    
    /* Células de Destaque */
    .bg-light-tb {
        background-color: #f1f5f9 !important;
        font-weight: 600;
        color: #1e293b;
    }
    
    .fw-semibold {
        font-weight: 600;
    }
    
    /* Efeitos de Gradiente para Cabeçalhos */
    .table-light th {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    
    /* Rodapé Moderno */
    .report-footer {
        margin-top: 24px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #64748b;
        text-align: right;
        font-family: 'Courier New', monospace;
    }
    
    /* Efeitos de Hover mais suaves */
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    /* Responsividade para Impressão */
    @media print {
        body {
            padding: 0.5cm;
            background-color: white;
        }
        
        .table {
            box-shadow: none;
            border-radius: 0;
        }
        
        .table th {
            background-color: #4f46e5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .bg-light-tb {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
    
    /* Melhorias na legibilidade */
    .text-center {
        text-align: center !important;
    }
    
    .align-middle {
        vertical-align: middle !important;
    }
    
    .mb-0 {
        margin-bottom: 0 !important;
    }
</style>

@section('content')
<main>
    @include('Reports::pdf_model.pdf_header')
    
    <div class="report-header">
        <h1 class="report-title">Dados Estatísticos Gerais</h1>
        <div class="report-subtitle">Resumo por Curso e Ano Letivo</div>
    </div>
    
    <table class="table table-bordered table-hover table-sm mb-0">
        <thead class="table-light">
            <tr>
                <th rowspan="2" class="align-middle text-center bg-light-tb">Curso</th>
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
                    <td class="fw-semibold bg-light-tb">{{ $codTurma }}</td>
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