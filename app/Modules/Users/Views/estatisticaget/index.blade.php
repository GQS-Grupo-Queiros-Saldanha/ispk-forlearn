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
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .table {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-bottom: 0;
        }
        
        .table thead th {
            white-space: nowrap;
            vertical-align: middle;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            vertical-align: middle;
            font-size: 0.9rem;
            padding: 0.75rem;
        }
        
        /* Cabeçalho principal */
        .table thead tr:first-child {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
        }
        
        /* Subcabeçalho */
        .table thead tr:nth-child(2) {
            background-color: #3b82f6;
            color: white;
        }
        
        /* Estilo para as células de total */
        .table tbody td[class*="total_"] {
            font-weight: 600;
            background-color: #e0f2fe;
        }
        
        /* Cores diferentes para cada ano */
        .table thead tr:nth-child(2) th:nth-child(2),
        .table thead tr:nth-child(2) th:nth-child(7),
        .table thead tr:nth-child(2) th:nth-child(12),
        .table thead tr:nth-child(2) th:nth-child(17),
        .table thead tr:nth-child(2) th:nth-child(22) {
            background-color: rgba(59, 130, 246, 0.2);
        }
        
        /* Cores alternadas para linhas */
        .table tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
        
        /* Destaque para o nome do curso */
        .table tbody td.fw-semibold {
            background-color: #eff6ff !important;
            color: #1e40af;
            font-weight: 700 !important;
            border-left: 3px solid #3b82f6;
        }
        
        /* Hover effect */
        .table-hover tbody tr:hover {
            background-color: #f0f9ff;
        }
        
        /* Botão PDF */
        #btnPdf {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        #btnPdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
        }
        
        /* Selects */
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            transition: border-color 0.3s ease;
        }
        
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        
        /* Responsividade */
        @media (max-width: 992px) {
            .table-responsive {
                border: 1px solid #e5e7eb;
            }
        }
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
    <div class="d-flax">
        <button class="btn btn-success" id="btnPdf">
            <i class="bi bi-file-earmark-pdf"></i>
            Gerar PDF
        </button>        

    </div>
@endsection

@section('body')
    <div class="table-responsive border rounded">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="align-middle text-center bg-gradient-primary text-white">Curso</th>
                    <th colspan="5" class="text-center bg-blue-50">1º Ano</th>
                    <th colspan="5" class="text-center bg-blue-100">2º Ano</th>
                    <th colspan="5" class="text-center bg-blue-150">3º Ano</th>
                    <th colspan="4" class="text-center bg-blue-200">4º Ano</th>
                    <th colspan="5" class="text-center bg-blue-250">5º Ano</th>
                </tr>
                <tr>
                    <!-- 1ano -->
                    <th class="text-center bg-blue-50">M</th>
                    <th class="text-center bg-blue-50">T</th>
                    <th class="text-center bg-blue-50">N</th>
                    <th class="text-center bg-blue-50">Prot.</th>
                    <th class="text-center bg-blue-50">Total</th>
                    
                    <!-- 2ano -->
                    <th class="text-center bg-blue-100">M</th>
                    <th class="text-center bg-blue-100">T</th>
                    <th class="text-center bg-blue-100">N</th>
                    <th class="text-center bg-blue-100">Prot.</th>
                    <th class="text-center bg-blue-100">Total</th>
                    
                    <!-- 3ano -->
                    <th class="text-center bg-blue-150">M</th>
                    <th class="text-center bg-blue-150">T</th>
                    <th class="text-center bg-blue-150">N</th>
                    <th class="text-center bg-blue-150">Prot.</th>
                    <th class="text-center bg-blue-150">Total</th>
                    
                    <!-- 4ano -->
                    <th class="text-center bg-blue-200">M</th>
                    <th class="text-center bg-blue-200">T</th>
                    <th class="text-center bg-blue-200">Prot.</th>
                    <th class="text-center bg-blue-200">Total</th>
                    
                    <!-- 5ano -->
                    <th class="text-center bg-blue-250">M</th>
                    <th class="text-center bg-blue-250">T</th>
                    <th class="text-center bg-blue-250">N</th>
                    <th class="text-center bg-blue-250">Prot.</th>
                    <th class="text-center bg-blue-250">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $c)
                @php
                    $sigla = strtoupper(substr($c->currentTranslation->display_name, 0, 1) . substr(strstr($c->currentTranslation->display_name, ' '), 1, 1));
                    $codTurma = $c->code;
                    // Gerar uma cor baseada no código do curso para consistência
                    $colorHash = crc32($codTurma);
                    $r = ($colorHash & 0xFF0000) >> 16;
                    $g = ($colorHash & 0x00FF00) >> 8;
                    $b = $colorHash & 0x0000FF;
                    $baseColor = sprintf("rgba(%d, %d, %d, 0.1)", min($r + 100, 255), min($g + 100, 255), min($b + 150, 255));
                @endphp
                <tr style="--course-color: {{ $baseColor }};">
                    <td class="fw-semibold" style="background-color: var(--course-color); border-left: 4px solid #3b82f6;">{{ $codTurma }}</td>
                    @for ($ano = 1; $ano <= 5; $ano++)
                        <td id="manha_{{ $c->id }}_{{ $ano }}" class="text-center" style="background-color: rgba(147, 197, 253, 0.2);">-</td>
                        <td id="tarde_{{ $c->id }}_{{ $ano }}" class="text-center" style="background-color: rgba(147, 197, 253, 0.3);">-</td>
                        <td id="noite_{{ $c->id }}_{{ $ano }}" class="text-center" style="background-color: rgba(147, 197, 253, 0.4);">-</td>
                        <td id="protocolo_{{ $c->id }}_{{ $ano }}" class="text-center" style="background-color: rgba(147, 197, 253, 0.5);">-</td>
                        <td id="total_{{ $c->id }}_{{ $ano }}" style="background-color: rgba(59, 130, 246, 0.1); font-weight: 600;">-</td>
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
    
    /*Acção do Botão de PDF*/
    const btn = document.getElementById('btnPdf');
    btn.addEventListener('click', function() {
        window.location.href = "/pt/estatisticaget/pdf/";
    });

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

    for (let a = 1; a <= 5; a++) {
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
                                    document.getElementById(`total_${course.id}_${ano}`).textContent = totais[ano].M + totais[ano].T + totais[ano].N + totais[ano].PT;
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
                                    document.getElementById(`total_${course.id}_${ano}`).textContent = totais[ano].M + totais[ano].T + totais[ano].N + totais[ano].PT;
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
                                    document.getElementById(`total_${course.id}_${ano}`).textContent = totais[ano].M + totais[ano].T + totais[ano].N + totais[ano].PT;
                                });
                        });

                    }  
                    else if (ano === 5) {
                        const codlista = [
                            { curso: "EC", id: "52", periodo: "M" },
                            { curso: "EC", id: "53", periodo: "T" },
                            { curso: "EG", id: "0", periodo: "M" },
                            { curso: "EG", id: "0", periodo: "T" },
                            { curso: "EH", id: "0", periodo: "M" },
                            { curso: "EH", id: "0", periodo: "T" },
                            { curso: "EI", id: "18", periodo: "M" },
                            { curso: "EI", id: "0", periodo: "T" },
                            { curso: "EM", id: "10", periodo: "M" },
                            { curso: "EM", id: "0", periodo: "T" },
                            { curso: "EP", id: "4", periodo: "M" },
                            { curso: "EP", id: "0", periodo: "T" },
                            { curso: "EQ", id: "42", periodo: "M" },
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
                                    document.getElementById(`total_${course.id}_${ano}`).textContent = totais[ano].M + totais[ano].T + totais[ano].N + totais[ano].PT;
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
                                        const total = totalAlunos + totalProtocolo;
                                        
                                        console.log(total)
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
                                        document.getElementById(`total_${course.id}_${ano}`).textContent = totais[ano].M + totais[ano].T + totais[ano].N + totais[ano].PT;
                                        

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