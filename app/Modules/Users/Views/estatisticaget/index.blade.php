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
                    <th class="text-center">Protocolo</th>
                    
                    <!-- 2ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">Protocolo</th>
                    
                    <!-- 3ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">Protocolo</th>
                    
                    <!-- 4ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">Protocolo</th>
                    
                    <!-- 5ano -->
                    <th class="text-center">M</th>
                    <th class="text-center">T</th>
                    <th class="text-center">N</th>
                    <th class="text-center">Protocolo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $c)
                @php
                    $sigla = strtoupper(substr($c->currentTranslation->display_name, 0, 1) . substr(strstr($c->currentTranslation->display_name, ' '), 1, 1));
                    $codTurma = $c->code;
                @endphp
                <tr>
                    <td class="fw-semibold bg-light">{{ $codTurma }}</td>
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

    // Inicializa todas as células da tabela com 0
    for (let ano = 1; ano <= 5; ano++) {
        courses.forEach(course => {
            const courseId = course.id;

            document.getElementById(`manha_${courseId}_${ano}`).textContent = 0;
            document.getElementById(`tarde_${courseId}_${ano}`).textContent = 0;
            document.getElementById(`noite_${courseId}_${ano}`).textContent = 0;
            document.getElementById(`protocolo_${courseId}_${ano}`).textContent = 0;
        });
    }

    // Mapa para evitar fetch duplicado
    const requisicoesFeitas = new Set();

    // Totais por curso e ano
    const totaisPorCurso = {};

    for (let a = 1; a <= 4; a++) {
        const ano = a;

        courses.forEach(course => {
            const courseId = course.id;
            const courseCode = course.code; // este é o que vem de $c->code

            if (!totaisPorCurso[courseId]) {
                totaisPorCurso[courseId] = {
                    1: { M: 0, T: 0, N: 0, PT: 0 },
                    2: { M: 0, T: 0, N: 0, PT: 0 },
                    3: { M: 0, T: 0, N: 0, PT: 0 },
                    4: { M: 0, T: 0, N: 0, PT: 0 },
                    5: { M: 0, T: 0, N: 0, PT: 0 },
                };
            }

            const totais = totaisPorCurso[courseId];

            fetch(`/pt/grades/teacher_disciplines/${courseId}/${lectiveYear}`)
                .then(res => res.json())
                .then(response => {
                    const turmas = response.turma || [];

                    if (ano === 2) {
                        const codlista = [
                            { curso: "EC", id: "46", periodo: "M" },
                            { curso: "EC", id: "47", periodo: "T" },
                            { curso: "EG", id: "48", periodo: "M" },
                            { curso: "EG", id: "49", periodo: "T" },
                            { curso: "EH", id: "50", periodo: "M" },
                            { curso: "EH", id: "51", periodo: "T" },
                            { curso: "EI", id: "15", periodo: "M" },
                            { curso: "EI", id: "20", periodo: "T" },
                            { curso: "EM", id: "7", periodo: "M" },
                            { curso: "EM", id: "12", periodo: "T" },
                            { curso: "EP", id: "29", periodo: "M" },
                            { curso: "EP", id: "31", periodo: "T" },
                            { curso: "EQ", id: "37", periodo: "M" },
                            { curso: "EQ", id: "38", periodo: "T" }



                        ];

                        codlista.forEach(item => {
                            // Verifica se o item é do curso atual
                            if (item.curso !== courseCode) return;

                            const codturma = item.id;
                            const periodo = item.periodo;
                            const chaveRequisicao = `${codturma}_${ano}_${courseId}`;

                            if (requisicoesFeitas.has(chaveRequisicao)) return;
                            requisicoesFeitas.add(chaveRequisicao);

                            fetch(`/pt/estatisticaget/student/${codturma}/${ano}`)
                                .then(res => res.json())
                                .then(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const totalProtocolo = json.protocolo ?? 0;

                                    if (periodo === "M") totais[ano].M += totalAlunos;
                                    else if (periodo === "T") totais[ano].T += totalAlunos;
                                    else if (periodo === "N") totais[ano].N += totalAlunos;

                                    totais[ano].PT += totalProtocolo;

                                    document.getElementById(`manha_${courseId}_${ano}`).textContent = totais[ano].M;
                                    document.getElementById(`tarde_${courseId}_${ano}`).textContent = totais[ano].T;
                                    document.getElementById(`noite_${courseId}_${ano}`).textContent = totais[ano].N;
                                    document.getElementById(`protocolo_${courseId}_${ano}`).textContent = totais[ano].PT;
                                });
                        });

                    }
                    else if (ano === 3) {
                        const codlista = [
                            { curso: "EC", id: "48", periodo: "M" },
                            { curso: "EC", id: "49", periodo: "T" },
                            { curso: "EG", id: "0", periodo: "M" },
                            { curso: "EG", id: "0", periodo: "T" },
                            { curso: "EH", id: "0", periodo: "M" },
                            { curso: "EH", id: "0", periodo: "T" },
                            { curso: "EI", id: "16", periodo: "M" },
                            { curso: "EI", id: "21", periodo: "T" },
                            { curso: "EM", id: "8", periodo: "M" },
                            { curso: "EM", id: "13", periodo: "T" },
                            { curso: "EP", id: "2", periodo: "M" },
                            { curso: "EP", id: "32", periodo: "T" },
                            { curso: "EQ", id: "39", periodo: "M" },
                            { curso: "EQ", id: "40", periodo: "T" }



                        ];

                        codlista.forEach(item => {
                            // Verifica se o item é do curso atual
                            if (item.curso !== courseCode) return;

                            const codturma = item.id;
                            const periodo = item.periodo;
                            const chaveRequisicao = `${codturma}_${ano}_${courseId}`;

                            if (requisicoesFeitas.has(chaveRequisicao)) return;
                            requisicoesFeitas.add(chaveRequisicao);

                            fetch(`/pt/estatisticaget/student/${codturma}/${ano}`)
                                .then(res => res.json())
                                .then(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const totalProtocolo = json.protocolo ?? 0;

                                    if (periodo === "M") totais[ano].M += totalAlunos;
                                    else if (periodo === "T") totais[ano].T += totalAlunos;
                                    else if (periodo === "N") totais[ano].N += totalAlunos;

                                    totais[ano].PT += totalProtocolo;

                                    document.getElementById(`manha_${courseId}_${ano}`).textContent = totais[ano].M;
                                    document.getElementById(`tarde_${courseId}_${ano}`).textContent = totais[ano].T;
                                    document.getElementById(`noite_${courseId}_${ano}`).textContent = totais[ano].N;
                                    document.getElementById(`protocolo_${courseId}_${ano}`).textContent = totais[ano].PT;
                                });
                        });

                    }
                    else if (ano === 4) {
                        const codlista = [
                            { curso: "EC", id: "50", periodo: "M" },
                            { curso: "EC", id: "51", periodo: "T" },
                            { curso: "EG", id: "0", periodo: "M" },
                            { curso: "EG", id: "0", periodo: "T" },
                            { curso: "EH", id: "0", periodo: "M" },
                            { curso: "EH", id: "0", periodo: "T" },
                            { curso: "EI", id: "17", periodo: "M" },
                            { curso: "EI", id: "0", periodo: "T" },
                            { curso: "EM", id: "9", periodo: "M" },
                            { curso: "EM", id: "0", periodo: "T" },
                            { curso: "EP", id: "1", periodo: "M" },
                            { curso: "EP", id: "0", periodo: "T" },
                            { curso: "EQ", id: "41", periodo: "M" },
                            { curso: "EQ", id: "0", periodo: "T" }



                        ];

                        codlista.forEach(item => {
                            // Verifica se o item é do curso atual
                            if (item.curso !== courseCode) return;

                            const codturma = item.id;
                            const periodo = item.periodo;
                            const chaveRequisicao = `${codturma}_${ano}_${courseId}`;

                            if (requisicoesFeitas.has(chaveRequisicao)) return;
                            requisicoesFeitas.add(chaveRequisicao);

                            fetch(`/pt/estatisticaget/student/${codturma}/${ano}`)
                                .then(res => res.json())
                                .then(json => {
                                    const totalAlunos = json.total ?? 0;
                                    const totalProtocolo = json.protocolo ?? 0;

                                    if (periodo === "M") totais[ano].M += totalAlunos;
                                    else if (periodo === "T") totais[ano].T += totalAlunos;
                                    else if (periodo === "N") totais[ano].N += totalAlunos;

                                    totais[ano].PT += totalProtocolo;

                                    document.getElementById(`manha_${courseId}_${ano}`).textContent = totais[ano].M;
                                    document.getElementById(`tarde_${courseId}_${ano}`).textContent = totais[ano].T;
                                    document.getElementById(`noite_${courseId}_${ano}`).textContent = totais[ano].N;
                                    document.getElementById(`protocolo_${courseId}_${ano}`).textContent = totais[ano].PT;
                                });
                        });

                    }              
                    else{
                        // Percorro cada turma devolvida
                        turmas.forEach(turma => {
                            const codigo = turma.display_name;

                            // Verifico se o código da turma tem pelo menos 4 caracteres
                            if (codigo.length < 4) return;

                            // Extraio o ano do curso a partir do 3.º carácter (posição 2 do array)

                            // Verifico se o ano está dentro do intervalo permitido (1.º ao 5.º)
                            if (![1, 2, 3, 4, 5].includes(ano)) return;

                            // Extraio o período da turma a partir do 4.º carácter (posição 3)
                            const periodo = codigo.charAt(3);

                                codturma = turma.id;
                                // Faço fetch dos dados estatísticos desta turma específica
                                fetch(`/pt/estatisticaget/student/${codturma}/${ano}`)
                                    .then(res => res.json())
                                    .then(json => {
                                        const totalAlunos = json.total ?? 0;
                                        const totalProtocolo = json.protocolo ?? 0;
                                        //console.log(turma.id);
                                        // Verifico o período (M: manhã, T: tarde, N: noite) e somo ao total do ano correspondente
                                        if (periodo === "M") {
                                            totais[ano].M += totalAlunos;
                                        } else if (periodo === "T") {
                                            totais[ano].T += totalAlunos;
                                        } else if (periodo === "N") {
                                            totais[ano].N += totalAlunos;
                                        }

                                        // Protocolo é independente do período, mas é somado ao ano correto
                                        totais[ano].PT += totalProtocolo;

                                        // Após cada fetch individual, atualizo os elementos da tabela
                                        document.getElementById(`manha_${course.id}_${ano}`).textContent = totais[ano].M;
                                        document.getElementById(`tarde_${course.id}_${ano}`).textContent = totais[ano].T;
                                        document.getElementById(`noite_${course.id}_${ano}`).textContent = totais[ano].N;
                                        document.getElementById(`protocolo_${course.id}_${ano}`).textContent = totais[ano].PT;
                                    });
                                });
                        }
                            
                });
            });
        }
    });
</script>
@endsection

@endsection