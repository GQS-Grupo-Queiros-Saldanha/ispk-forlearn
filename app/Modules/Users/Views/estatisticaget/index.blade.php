<title> Estatistica de pagamento | forLEARN® by GQS</title>
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
        table {border-collapse: collapse; width: 100%; margin: 20px 0;}
        th, td {border: 1px solid #ddd; padding: 8px; text-align: center;}
        th {background-color: #f2f2f2;}
    </style>
@endsection

@section('selects')
    <div class="mb-2">
        <label for="lective_year">Ano lectivo</label>
        <select name="lective_year" id="lective_year" class="form-control form-control-sm">
        @foreach ($lectiveYears as $lectiveYear)
            <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                {{ $lectiveYear->currentTranslation->display_name }}
            </option>
        @endforeach
        </select>
    </div>
    <div class="row">
        <div class="d-flex justify-content-between w-100">
            <!-- Curso -->
            <div class="d-flex flex-column me-3" style="flex: 1;">
                <label for="selectorCurso">Curso</label>
                <select class="form-control form-control-sm" name="curso" id="selectorCurso">
                    <option selected>Selecione o Curso</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->currentTranslation->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Turma -->
            <div class="d-flex flex-column ms-3" style="flex: 1;">
                <label for="selectorTurma">Turma</label>
                <select class="form-control form-control-sm" name="turma" id="selectorTurma">
                    <option selected>Escolha a turma</option>
                    <!-- JS vai preencher -->
                </select>
            </div>
        </div>
    </div>
@endsection

@section('body')

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2"></th>
                <th colspan="4">1ano</th>
                <th colspan="4">2ano</th>
                <th colspan="4">3ano</th>
                <th colspan="4">4ano</th>
                <th colspan="4">5ano</th>
            </tr>
            <tr>
                <!-- 1ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                <th></th>
                
                <!-- 2ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                
                <!-- 3ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                <th></th>
                
                <!-- 4ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                <th></th>
                
                <!-- 5ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $c)
                @php
                    $sigla = strtoupper(substr($c->currentTranslation->display_name, 0, 1) . substr(strstr($c->currentTranslation->display_name, ' '), 1, 1));
                @endphp
                <tr>
                    <td>{{ $sigla }}</td>
                    <td id="manha_{{ $c->id }}"></td>
                    <td id="tarde_{{ $c->id }}"></td>
                    <td id="noite_{{ $c->id }}"></td>
                    <td id="protocolo_{{ $c->id }}"></td>
                </tr>
            @endforeach
        </tbody>
    </table>
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

                turmas.forEach(turma => {
                    fetch(`/pt/estatisticaget/student/${turma.id}`)
                        .then(res => res.json())
                        .then(json => {

                            const totalAlunos = json.total ?? 0;
                            const protocolo = json.protocolo;
                            const valorOriginal = json.valororiginal;

                            const periodo = turma.display_name.charAt(3);
                            console.log('real:',valorOriginal 'total: ', totalAlunos 'protocolo:', protocolo );

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