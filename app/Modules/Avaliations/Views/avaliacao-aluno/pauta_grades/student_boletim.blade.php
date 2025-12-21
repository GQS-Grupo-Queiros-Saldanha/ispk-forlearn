<title>BOLETIM DE NOTAS | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'BOLETIM DE NOTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Boletim de notas</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach 
        </select>
    </div>
@endsection
@section('body')
    <style>
        .boletim_text{
            font-weight: normal !important;
            font-size: 14px !important; 
        }
        .table{
            margin-bottom: 1px;
            padding-bottom: 1px;

        }
        /* Mantenha seus estilos existentes */
        .tabela_pauta tbody tr td { font-weight: normal !important; } 
        .tabela_pauta tbody tr .text-bold { font-weight: 600 !important; } 
        .bg0 { background-color: #2f5496 !important; color: white; } 
        .bg1 { background-color: #8eaadb !important; } 
        .bg2 { background-color: #d9e2f3 !important; } 
        .bg3 { background-color: #fbe4d5 !important; } 
        .bg4 { background-color: #f4b083 !important; } 
        .bgmac { background-color: #a5c4ff !important; } 
        .cf1 { background-color: #4888ffdb !important; } 
        .rec { background-color: #a5c4ff !important; } 
        .fn { background-color: #1296ff !important; } 
        .bo1 { border: 1px solid white!important; } 
        table tr .small, table tr .small { font-size: 11px !important; } 
        .for-green { background-color: #00ff89 !important; } 
        .for-blue { background-color: #cce5ff !important; z-index: 1000; } 
        .for-red { background-color: #f5342ec2 !important; } 
        .for-yellow { background-color: #f39c12 !important; } 
        .boletim_text { font-weight: normal !important; } 
        .barra { color: #f39c12 !important; font-weight: bold; } 
        .semestreA, .semestre2{ } 
        
        /* Estilos adicionais para modernização */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .table thead {
            background-color: #2c3e50;
            color: white;
        }
        
        .table thead th {
            border-bottom: 2px solid #1a252f;
            font-weight: 600;
            padding: 10px 8px;
        }
        
        .table tbody tr {
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody td {
            padding: 8px;
            vertical-align: middle;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2f5496 0%, #4a6fa5 100%);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #254478 0%, #3d5c8a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(47, 84, 150, 0.3);
        }
        
        /* Melhorias para as cores de status */
        .for-green {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        .for-red {
            background-color: #dc3545 !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        .for-yellow {
            background-color: #ffc107 !important;
            color: #212529 !important;
            font-weight: 600 !important;
            border-radius: 4px;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .table {
                font-size: 0.75rem;
            }
            
            .table th, .table td {
                padding: 6px 4px;
            }
            
            .boletim_text {
                font-size: 12px !important;
            }
        }
    </style>
    
   <div id="table_student" class="mt-2">
       @include('Cms::initial.components.manutencao')
   </div>
@endsection

@section('scripts-new')
    @parent

@endsection
