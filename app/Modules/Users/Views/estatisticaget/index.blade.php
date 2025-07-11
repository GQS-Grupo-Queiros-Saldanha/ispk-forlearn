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
                    @for ($ano = 1; $ano <= 5; $ano++)
                        <td id="manha_{{ $c->id }}_{{ $ano }}">-</td>
                        <td id="tarde_{{ $c->id }}_{{ $ano }}">-</td>
                        <td id="noite_{{ $c->id }}_{{ $ano }}">-</td>
                        <td id="protocolo_{{ $c->id }}_{{ $ano }}">-</td>
                    @endfor
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
@endsection

@section('scripts-new')
@section('scripts-new')
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const lectiveSelector = document.getElementById('lective_year');
    const lectiveYear = lectiveSelector.value;
    const courses = @json($courses);

    for (let ano = 1; ano <= 5; ano++) {
        // Inicializa totais fora do fetch para manter escopo correto
        const totais = {
            1: { M: 0, T: 0, N: 0, PT: 0 },
            2: { M: 0, T: 0, N: 0, PT: 0 },
            3: { M: 0, T: 0, N: 0, PT: 0 },
            4: { M: 0, T: 0, N: 0, PT: 0 },
            5: { M: 0, T: 0, N: 0, PT: 0 },
        };

        courses.forEach(course => {
            fetch(`/pt/grades/teacher_disciplines/${course.id}/${lectiveYear}`)
                .then(res => res.json())
                .then(response => {
                    const turmas = response.turma || [];

                    turmas.forEach(turma => {
                        const codigo = turma.display_name;
                        if (codigo.length < 4) return;

                        // Ano extraído do código (posição 2)
                        // Se quiseres usar o ano do código, podes extrair aqui
                        // mas tu já tens 'ano' no ciclo, verifica se coincidem
                        if (![1, 2, 3, 4, 5].includes(ano)) return;

                        const periodo = codigo.charAt(3);
                        let lista = [];

                        if (ano === 2) lista = ["46", "47"];
                        else if (ano === 3) lista = ["48", "49"];
                        else if (ano === 4) lista = ["50", "50"];
                        else if (ano === 5) lista = ["52", "53"];

                        if (lista.length > 0) {
                            // Fazer fetch para cada turmacod na lista e esperar todos completarem
                            Promise.all(lista.map(turmacod =>
                                fetch(`/pt/estatisticaget/student/${turmacod}/${ano}`)
                                    .then(resp => resp.json())
                            )).then(results => {
                                results.forEach(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const totalProtocolo = json.protocolo ?? 0;

                                    if (periodo === "M") totais[ano].M += totalAlunos;
                                    else if (periodo === "T") totais[ano].T += totalAlunos;
                                    else if (periodo === "N") totais[ano].N += totalAlunos;

                                    totais[ano].PT += totalProtocolo;
                                });

                                // Atualiza a tabela depois de somar todos
                                document.getElementById(`manha_${course.id}_${ano}`).textContent = totais[ano].M;
                                document.getElementById(`tarde_${course.id}_${ano}`).textContent = totais[ano].T;
                                document.getElementById(`noite_${course.id}_${ano}`).textContent = totais[ano].N;
                                document.getElementById(`protocolo_${course.id}_${ano}`).textContent = totais[ano].PT;
                            }).catch(console.error);
                        } else {
                            // Usa turma.id directamente quando não há lista
                            const turmacod = turma.id;

                            fetch(`/pt/estatisticaget/student/${turmacod}/${ano}`)
                                .then(res => res.json())
                                .then(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const totalProtocolo = json.protocolo ?? 0;

                                    if (periodo === "M") totais[ano].M += totalAlunos;
                                    else if (periodo === "T") totais[ano].T += totalAlunos;
                                    else if (periodo === "N") totais[ano].N += totalAlunos;

                                    totais[ano].PT += totalProtocolo;

                                    document.getElementById(`manha_${course.id}_${ano}`).textContent = totais[ano].M;
                                    document.getElementById(`tarde_${course.id}_${ano}`).textContent = totais[ano].T;
                                    document.getElementById(`noite_${course.id}_${ano}`).textContent = totais[ano].N;
                                    document.getElementById(`protocolo_${course.id}_${ano}`).textContent = totais[ano].PT;
                                }).catch(console.error);
                        }
                    });
                }).catch(console.error);
            });
        }
    });

</script>
@endsection

@endsection