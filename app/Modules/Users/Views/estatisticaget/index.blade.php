<title>Estatistica de pagamento | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estatistica de pagamento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">****</a></li>
    <li class="breadcrumb-item active" aria-current="page">*******</li>
@endsection

@section('styles-new')
    @parent
    <style>
        .table-responsive { border-radius: 0.25rem;}
        .table thead th {white-space: nowrap; vertical-align: middle; }
        .table tbody td { vertical-align: middle;}
    </style>
@endsection

@section('selects')
    <div class="mb-3">
        <label for="lective_year" class="form-label">Ano lectivo</label>
        <select name="lective_year" id="lective_year" class="form-select form-select-sm">
        @foreach ($lectiveYears as $lectiveYear)
            <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                {{ $lectiveYear->currentTranslation->display_name }}
            </option>
        @endforeach
        </select>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="selectorCurso" class="form-label">Curso</label>
            <select class="form-select form-select-sm" name="curso" id="selectorCurso">
                <option selected>Selecione o Curso</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->currentTranslation->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="selectorTurma" class="form-label">Turma</label>
            <select class="form-select form-select-sm" name="turma" id="selectorTurma">
                <option selected>Escolha a turma</option>
                <!-- JS vai preencher -->
            </select>
        </div>
    </div>
@endsection

@section('body')
    <div class="table-responsive border rounded">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="align-middle text-center bg-light">Curso</th>
                    <th colspan="4" class="text-center">1º Ano</th>
                    <th colspan="4" class="text-center">2º Ano</th>
                    <th colspan="4" class="text-center">3º Ano</th>
                    <th colspan="4" class="text-center">4º Ano</th>
                    <th colspan="4" class="text-center">5º Ano</th>
                </tr>
                <tr>
                    <!-- 1ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">PT</th>
                    
                    <!-- 2ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">PT</th>
                    
                    <!-- 3ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">PT</th>
                    
                    <!-- 4ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">PT</th>
                    
                    <!-- 5ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">PT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $c)
                    @php
                        $sigla = strtoupper(substr($c->currentTranslation->display_name, 0, 1) . substr(strstr($c->currentTranslation->display_name, ' '), 1, 1));
                    @endphp
                    <tr>
                        <td class="fw-semibold bg-light">{{ $sigla }}</td>
                        <!-- 1ano -->
                        <td id="manha_{{ $c->id }}">-</td>
                        <td id="tarde_{{ $c->id }}">-</td>
                        <td id="noite_{{ $c->id }}">-</td>
                        <td id="protocolo_{{ $c->id }}">-</td>
                        
                        <!-- 2ano -->
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        
                        <!-- 3ano -->
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        
                        <!-- 4ano -->
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        
                        <!-- 5ano -->
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts-new')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lectiveSelector = document.getElementById('lective_year');
            const lectiveYear = lectiveSelector.value;
            
            const courses = @json($courses);

            courses.forEach(course => {
                let manha = 0, tarde = 0, noite = 0;

                fetch(`/pt/grades/teacher_disciplines/${course.id}/${lectiveYear}`)
                    .then(res => res.json())
                    .then(response => {
                        const turmas = response.turma || [];
                        console.log(turmas);
                        turmas.forEach(turma => {
                            fetch(`/pt/estatisticaget/student/${turma.id}`)
                                .then(res => res.json())
                                .then(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const protocolo = json.protocolo;
                                    const periodo = turma.display_name.charAt(3);

                                    if (periodo === "T") {
                                        tarde += totalAlunos;
                                    } else if (periodo === "M") {
                                        manha += totalAlunos;
                                    } else {
                                        noite += totalAlunos;
                                    }

                                    document.getElementById(`manha_${course.id}`).textContent = manha;
                                    document.getElementById(`tarde_${course.id}`).textContent = tarde;
                                    document.getElementById(`noite_${course.id}`).textContent = noite;
                                    document.getElementById(`protocolo_${course.id}`).textContent = protocolo;
                                });
                        });
                    });
            });
        });
    </script>
@endsection