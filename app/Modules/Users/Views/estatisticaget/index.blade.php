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

    <table>
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
                
                <!-- 4ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
                
                <!-- 5ano -->
                <th>M</th>
                <th>T</th>
                <th>N</th>
                <th>PT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>EC</td>
                <td>23</td>
                <td>54</td>
                <td>87</td>
                <td>09</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>ENT</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>HID</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4</td>
                <td>GEO</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>5</td>
                <td>PET</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>6</td>
                <td>QUI</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>7</td>
                <td>REC</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
@endsection

@section('scripts-new')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const courseSelector = document.getElementById('selectorCurso');
        const lectiveSelector = document.getElementById('lective_year');
        const turmaSelector = document.getElementById('selectorTurma');
        const table = document.getElementById('students-table');
        const alertBox = document.getElementById('alert');
    
        // Função que carrega as turmas com base no curso e ano letivo
        function loadTurmas(courseId, lectiveYear) {
            if (!courseId || !lectiveYear) return;
    
            fetch(`/pt/grades/teacher_disciplines/${courseId}/${lectiveYear}`)
                .then(res => res.json())
                .then(response => {
                    const turmas = response.turma || [];
                    turmaSelector.innerHTML = '';
    
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.selected = true;
                    defaultOption.textContent = 'Seleciona a turma';
                    turmaSelector.appendChild(defaultOption);
    
                    if (turmas.length > 0) {
                        turmas.forEach(turma => {
                            const option = document.createElement('option');
                            option.value = turma.id;
                            option.textContent = turma.display_name;
                            turmaSelector.appendChild(option);
                        });
    
                        turmaSelector.disabled = false;
                        $('.selectpicker').selectpicker('refresh');
                        // getUrl(); // se realmente precisares disto
                    } else {
                        turmaSelector.disabled = true;
                    }
                })
                .catch(error => console.error('Erro ao carregar turmas:', error));
        }
    
        // Quando o utilizador muda o curso, carregar as turmas
        courseSelector.addEventListener('change', function () {
            const courseId = this.value;
            const lectiveYear = lectiveSelector.value;
            loadTurmas(courseId, lectiveYear);
        });

        turmaSelector.addEventListener('change', function () {
            const turma = this.value;
            if (!turma) return;
    
            fetch(`/pt/estatisticaget/student/${turma}`)
                .then(res => res.json())
                .then(json => {
                    const totalAlunos = json.alunos ?? 0; 
                    console.log('Total de alunos:', totalAlunos);
                })
                .catch(error => console.error('Erro na entrega dos dados:', error));

        });
    });
    
</script>
    
@endsection