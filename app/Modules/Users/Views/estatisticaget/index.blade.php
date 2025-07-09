<title>Estatísticas de Pagamento | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estatísticas de Pagamento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">Avaliações</a></li>
    <li class="breadcrumb-item active" aria-current="page">Estatísticas de Pagamento</li>
@endsection

@section('styles-new')
    @parent
    <style>
        .stats-table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 0.9em;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .stats-table thead tr {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: center;
        }
        
        .stats-table th,
        .stats-table td {
            padding: 12px 8px;
            border: 1px solid #dddddd;
            text-align: center;
        }
        
        .stats-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }
        
        .stats-table tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }
        
        .stats-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        
        .stats-table th {
            position: sticky;
            top: 0;
        }
        
        .year-header {
            background-color: #34495e !important;
        }
        
        .period-header {
            background-color: #3d566e !important;
        }
        
        .total-cell {
            font-weight: bold;
            background-color: #e9ecef;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .select-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
    </style>
@endsection

@section('selects')
    <div class="select-container">
        <div class="filter-row">
            <div class="filter-group">
                <label for="lective_year" class="form-label">Ano Letivo</label>
                <select name="lective_year" id="lective_year" class="form-select">
                    @foreach ($lectiveYears as $lectiveYear)
                        <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                            {{ $lectiveYear->currentTranslation->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="selectorCurso" class="form-label">Curso</label>
                <select class="form-select" name="curso" id="selectorCurso">
                    <option value="" selected>Selecione o Curso</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->currentTranslation->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="selectorTurma" class="form-label">Turma</label>
                <select class="form-select" name="turma" id="selectorTurma" disabled>
                    <option value="" selected>Selecione primeiro o curso</option>
                </select>
            </div>
        </div>
    </div>
@endsection

@section('body')
    <div class="table-container">
        <table class="stats-table">
            <thead>
                <tr>
                    <th rowspan="2">Curso</th>
                    <th colspan="4" class="year-header">1º Ano</th>
                    <th colspan="4" class="year-header">2º Ano</th>
                    <th colspan="4" class="year-header">3º Ano</th>
                    <th colspan="4" class="year-header">4º Ano</th>
                    <th colspan="4" class="year-header">5º Ano</th>
                </tr>
                <tr>
                    <!-- 1º Ano -->
                    <th class="period-header">Manhã</th>
                    <th class="period-header">Tarde</th>
                    <th class="period-header">Noite</th>
                    <th class="period-header">Protocolo</th>
                    
                    <!-- 2º Ano -->
                    <th class="period-header">Manhã</th>
                    <th class="period-header">Tarde</th>
                    <th class="period-header">Noite</th>
                    <th class="period-header">Protocolo</th>
                    
                    <!-- 3º Ano -->
                    <th class="period-header">Manhã</th>
                    <th class="period-header">Tarde</th>
                    <th class="period-header">Noite</th>
                    <th class="period-header">Protocolo</th>
                    
                    <!-- 4º Ano -->
                    <th class="period-header">Manhã</th>
                    <th class="period-header">Tarde</th>
                    <th class="period-header">Noite</th>
                    <th class="period-header">Protocolo</th>
                    
                    <!-- 5º Ano -->
                    <th class="period-header">Manhã</th>
                    <th class="period-header">Tarde</th>
                    <th class="period-header">Noite</th>
                    <th class="period-header">Protocolo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $c)
                    @php
                        $sigla = strtoupper(substr($c->currentTranslation->display_name, 0, 1) . substr(strstr($c->currentTranslation->display_name, ' '), 1, 1));
                    @endphp
                    <tr>
                        <td><strong>{{ $sigla }}</strong> - {{ $c->currentTranslation->display_name }}</td>
                        
                        <!-- 1º Ano -->
                        <td id="manha_1_{{ $c->id }}">-</td>
                        <td id="tarde_1_{{ $c->id }}">-</td>
                        <td id="noite_1_{{ $c->id }}">-</td>
                        <td id="protocolo_1_{{ $c->id }}">-</td>
                        
                        <!-- 2º Ano -->
                        <td id="manha_2_{{ $c->id }}">-</td>
                        <td id="tarde_2_{{ $c->id }}">-</td>
                        <td id="noite_2_{{ $c->id }}">-</td>
                        <td id="protocolo_2_{{ $c->id }}">-</td>
                        
                        <!-- 3º Ano -->
                        <td id="manha_3_{{ $c->id }}">-</td>
                        <td id="tarde_3_{{ $c->id }}">-</td>
                        <td id="noite_3_{{ $c->id }}">-</td>
                        <td id="protocolo_3_{{ $c->id }}">-</td>
                        
                        <!-- 4º Ano -->
                        <td id="manha_4_{{ $c->id }}">-</td>
                        <td id="tarde_4_{{ $c->id }}">-</td>
                        <td id="noite_4_{{ $c->id }}">-</td>
                        <td id="protocolo_4_{{ $c->id }}">-</td>
                        
                        <!-- 5º Ano -->
                        <td id="manha_5_{{ $c->id }}">-</td>
                        <td id="tarde_5_{{ $c->id }}">-</td>
                        <td id="noite_5_{{ $c->id }}">-</td>
                        <td id="protocolo_5_{{ $c->id }}">-</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    @for ($i = 1; $i <= 5; $i++)
                        <td id="total_manha_{{ $i }}" class="total-cell">0</td>
                        <td id="total_tarde_{{ $i }}" class="total-cell">0</td>
                        <td id="total_noite_{{ $i }}" class="total-cell">0</td>
                        <td id="total_protocolo_{{ $i }}" class="total-cell">0</td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts-new')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lectiveSelector = document.getElementById('lective_year');
    const courseSelector = document.getElementById('selectorCurso');
    const classSelector = document.getElementById('selectorTurma');
    
    // Atualizar turmas quando o curso é alterado
    courseSelector.addEventListener('change', function() {
        const courseId = this.value;
        
        if (!courseId) {
            classSelector.disabled = true;
            classSelector.innerHTML = '<option value="" selected>Selecione primeiro o curso</option>';
            return;
        }
        
        fetch(`/api/courses/${courseId}/classes?lective_year=${lectiveSelector.value}`)
            .then(response => response.json())
            .then(data => {
                classSelector.disabled = false;
                classSelector.innerHTML = '<option value="">Todas as turmas</option>';
                
                data.forEach(classItem => {
                    const option = document.createElement('option');
                    option.value = classItem.id;
                    option.textContent = classItem.name;
                    classSelector.appendChild(option);
                });
            });
    });
    
    // Carregar estatísticas quando os filtros mudam
    function loadStatistics() {
        const lectiveYear = lectiveSelector.value;
        const courseId = courseSelector.value;
        const classId = classSelector.value;
        
        // Limpar a tabela
        document.querySelectorAll('[id^="manha_"], [id^="tarde_"], [id^="noite_"], [id^="protocolo_"]').forEach(el => {
            el.textContent = '-';
        });
        
        // Resetar totais
        for (let i = 1; i <= 5; i++) {
            document.getElementById(`total_manha_${i}`).textContent = '0';
            document.getElementById(`total_tarde_${i}`).textContent = '0';
            document.getElementById(`total_noite_${i}`).textContent = '0';
            document.getElementById(`total_protocolo_${i}`).textContent = '0';
        }
        
        // Se nenhum curso selecionado, carregar todos
        const coursesToLoad = courseId ? [{id: courseId}] : @json($courses);
        
        coursesToLoad.forEach(course => {
            for (let year = 1; year <= 5; year++) {
                fetch(`/api/payment-stats?course_id=${course.id}&year=${year}&lective_year=${lectiveYear}&class_id=${classId || ''}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById(`manha_${year}_${course.id}`).textContent = data.manha || '0';
                        document.getElementById(`tarde_${year}_${course.id}`).textContent = data.tarde || '0';
                        document.getElementById(`noite_${year}_${course.id}`).textContent = data.noite || '0';
                        document.getElementById(`protocolo_${year}_${course.id}`).textContent = data.protocolo || '0';
                        
                        // Atualizar totais
                        updateTotals(year, data);
                    });
            }
        });
    }
    
    function updateTotals(year, data) {
        const totalManha = document.getElementById(`total_manha_${year}`);
        const totalTarde = document.getElementById(`total_tarde_${year}`);
        const totalNoite = document.getElementById(`total_noite_${year}`);
        const totalProtocolo = document.getElementById(`total_protocolo_${year}`);
        
        totalManha.textContent = parseInt(totalManha.textContent) + (parseInt(data.manha) || 0);
        totalTarde.textContent = parseInt(totalTarde.textContent) + (parseInt(data.tarde) || 0);
        totalNoite.textContent = parseInt(totalNoite.textContent) + (parseInt(data.noite) || 0);
        totalProtocolo.textContent = parseInt(totalProtocolo.textContent) + (parseInt(data.protocolo) || 0);
    }
    
    // Event listeners para os selects
    lectiveSelector.addEventListener('change', loadStatistics);
    courseSelector.addEventListener('change', loadStatistics);
    classSelector.addEventListener('change', loadStatistics);
    
    // Carregar dados iniciais
    loadStatistics();
});
</script>
@endsection