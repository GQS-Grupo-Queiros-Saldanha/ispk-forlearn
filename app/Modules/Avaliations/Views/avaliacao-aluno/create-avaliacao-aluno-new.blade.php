<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@php
    $title = 'LANÇAR NOTAS';
    if ($segunda_chamada) {
        $title .= ' - 2ª CHAMADA';
    }
@endphp
@section('page-title', $title)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page" style="text-transform:capitalize">{{ $title }}</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <style>
        .red {
            background-color: red !important;
        }

        #ConteudoMain {
            display: none;
        }

        .regime {
            width: 5%;
        }

        .dt-buttons {
            float: left;
            margin-bottom: 20px;
        }

        .dataTables_filter label {
            float: right;
        }

        .dataTables_length label {
            margin-left: 10px;
        }

        .devedor {
            background-color: red !important;
            color: white
        }

        .recurso {
            background-color: orange;
            color: white;
        }

        .dispensado {
            background-color: blue;
            color: white
        }

        .neen {
            background-color: green;
            color: white
        }
    </style>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
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
    <section id="ConteudoMain">
        {!! Form::open(['route' => ['avaliacao_aluno.store']]) !!}
        <div class="row">
            <div class="col">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <h5>@choice('common.error', $errors->count())</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal"
                    id="btnSalvar">
                    <i class="fas fa-plus-circle"></i>
                    <span>Salvar</span>
                </button>
                <div class="row">
                    <div class="col-6">
                        <label>Edição de Plano de Estudo</label>
                        {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control', 'disabled']) }}
                    </div>
                    <div class="col-6">
                        <label>Disciplina</label>
                        {{ Form::bsLiveSelectEmpty('discipline_id', [], null, ['id' => 'discipline_id', 'class' => 'form-control', 'disabled']) }}
                    </div>
                    <div class="col-6" id="discipline-group">
                        <label>Avaliação</label>
                        {{ Form::bsLiveSelectEmpty('avaliacao_id', [], null, ['id' => 'avaliacao_id', 'class' => 'form-control', 'disabled']) }}
                    </div>
                    <div class="col-6">
                        <label>Turma</label>
                        {{ Form::bsLiveSelectEmpty('class_id', [], null, ['id' => 'class_id', 'class' => 'form-control', 'disabled']) }}
                    </div>
                    <div class="col-6" id="discipline-group">
                        <label>Métrica</label>
                        {{ Form::bsLiveSelectEmpty('metrica_id', [], null, ['id' => 'metrica_id', 'class' => 'form-control', 'disabled']) }}
                    </div>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-hover">
                            <thead class="table-primary">
                                <th>#</th>
                                <th class="text-center">Estado</th>
                                <th>Nº Estudante</th>
                                <th>Nome</th>
                                <th>Nota</th>
                            </thead>
                            <tbody id="students"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
    <section id="NextStap">
        <div>
            <div class="content_s" style="margin-bottom: 10px">
                <div class="">
                    <form action="{{ route('avaliacao_aluno_new.store') }}" id="id_form_Nota" method="POST"
                        target="_blank">
                        @csrf
                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione a disciplina</label>
                                        <select data-live-search="true" required
                                            class="selectpicker form-control form-control-sm" required=""
                                            id="Disciplina_id_Select" data-actions-box="false"
                                            data-selected-text-format="values" name="disciplina" tabindex="-98"></select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Selecione a turma</label>
                                        <select data-live-search="true" required
                                            class="selectpicker form-control form-control-sm" required=""
                                            id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values"
                                            name="turma" tabindex="-98">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <input type='hidden' id="pauta" value="" name="pauta" class="form-control">
                                <input type='hidden' id="version" value="" name="version" class="form-control">

                                @if ($segunda_chamada)
                                    <input type='hidden' value="{{ $segunda_chamada }}" name="segunda_chamada" class="form-control">
                                @endif

                                <div class="col-6" id="caixaAvalicao" style="display: none">
                                    <div class="form-group col">
                                        <label>Selecione a avaliacão</label>
                                        <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="avaliacao_id_Select" data-actions-box="false" data-selected-text-format="values" name="avaliacao" tabindex="-98">
                                            <option value="" selected>Selecione a avaliação </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6" id="caixaMatrica" style="display: none">
                                    <div class="form-group col">
                                        <label>Selecione a métrica</label>
                                        <select data-live-search="true" required class="selectpicker form-control form-control-sm" required="" id="metrica_id_Select" data-actions-box="false" data-selected-text-format="values" name="metrica" tabindex="-98">
                                            <option value="" selected>Selecione a métrica </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6" id="caixaDesc" style="display: none">
                                        <div class="form-group col">
                                            <label>Adicione uma descrição da pauta</label>
                                            <textarea class="form-control" name="description" id="description">

                                            </textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <hr>
                        <div id="tabela_new" style="display: none;">
                            <div class="card  mr-2">
                                <div class="row">
                                    <div class="col-12">
                                        <h2 id="Titulo_Avalicao"></h2>
                                        <table class="table table-hover dark">
                                            <thead>
                                                <th class="text-center" style="width:40px;">#</th>
                                                <th class="text-center">PRESENÇA</th>
                                                <th class="text-center">REGIME</th>
                                                <th class="text-center">MATRÍCULA</th>
                                                <th>ESTUDANTE</th>
                                                <th class="text-center">NOTA</th>
                                            </thead>
                                            <tbody id="students_new"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-12">
                        <div id="div_btn_save" class=" float-right">
                            <span class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal"
                                data-target="#exampleModal">
                                <i class="fas fa-plus-circle"></i>
                                Guardar notas
                            </span>
                        </div>
                    </div>

                    <div class="col-12">
                        <a id="btn_pdf" class=" float-right">
                            <span class="btn btn-danger mb-3 ml-3">
                                <i class="fas fa-file-pdf"></i>
                                Gerar pdf
                            </span>
                        </a>
                    </div>

                    @if (auth()->user()->hasRole('coordenador-curso'))
                        <div class="col-12">
                            <a id="btn_historic" class=" float-right">
                                <span class="btn btn-primary mb-3 ml-3" data-toggle="modal" data-target="#historicModal">
                                    <i class="fa fa-history"></i>
                                    Histórico de alterações
                                </span>
                            </a>
                        </div>
                    @endif
                    <div class="col-12" id="nav-lock-pauta">
                        <a class="float-right" id="btn_lock_pauta">
                            <span class="btn btn-warning mb-3 ml-3" data-toggle="modal" data-target="#lockModal">
                                <i class="fa fa-lock"></i>
                                Fechar pauta
                            </span>
                        </a>
                    </div>
                    
                    <div class="col-12" id="nav-open-pauta">
                        <a id="btn_open_pauta" class=" float-right" href="">
                            <span class="btn btn-warning mb-3 ml-3">
                                <i class="fa fa-unlock"></i>
                                Abrir pauta
                            </span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('models')
    <div class="modal fade" id="lockModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Aviso! </h5>
                </div>
                <div class="modal-body">
                    <p>Após o fecho da pauta, já não será possível alterá-la.<br>
                        Tem certeza que deseja continuar?</p>
                </div>
                <form action="{{ route('lock-pauta') }}" method="POST" id="id_form_lock">
                    @csrf
                    <input type="hidden" value="" id="lock" name="pauta_id">
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-callLock">Sim, tenho certeza</button>
                </div>

            </div>

        </div>
    </div>
    <div class="modal fade" id="historicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b> Histórico de actualizações</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <table id="historic-table" class="table">
                    <thead>

                        <th># </th>
                        <th>Descrição</th>
                        <th>Ver</th>
                        <th>Publicado por</th>
                        <th>Publicado a</th>

                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAviso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel"><b> AVISO</b> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="textoAviso"></div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary">Ok, Entendi</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="exampleModalLabel">ALERTA | Confirmação de dados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="float-right">
                        <p class="text-danger" style="font-weight:bold; !important"> Caro DOCENTE
                            ({{ auth()->user()->name }}), as informações inseridas
                            neste<br> formulário ATRIBUIR NOTAS, são da sua inteira responsabilidade.
                            <br> Por favor seja rigoroso na informação prestada.
                        </p>
                    </div>



                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important">Verifique se os dados estão correctos, nomeadamente: </p>
                        <ul>
                            <li style="padding:5px; !important">
                                Todos os alunos pertencem a esta TURMA?
                            </li>
                            <li style="padding:5px; !important">
                                Falta algum aluno nesta TURMA?
                            </li>
                        </ul>
                    </div>
                    <div style="margin-top:10px; !important">
                        <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações acima
                            assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.
                        </p>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima, por
                            favor seleccione: Tenho a certeza.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores
                        forLEARN</button>
                    <nav id="ocultar_btn">

                        <button type="button" class="btn btn-danger" id="btn-callSubmit">Tenho a certeza</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
$(document).ready(function() {

    // =======================
    // ELEMENTOS DOM
    // =======================
    const elements = {
        disciplinaSelect: $("#Disciplina_id_Select"),
        turmaSelect: $("#Turma_id_Select"),
        avaliacaoSelect: $("#avaliacao_id_Select"),
        metricaSelect: $("#metrica_id_Select"),
        lectiveYear: $("#lective_year"),
        formNota: $("#id_form_Nota"),
        tabelaNew: $("#tabela_new"),
        studentsNew: $("#students_new"),
        description: $("#description"),
        caixaAvalicao: $("#caixaAvalicao"),
        caixaMatrica: $("#caixaMatrica"),
        caixaDesc: $("#caixaDesc"),
        tituloAvalicao: $("#Titulo_Avalicao"),
        btnEnviar: $("#btn-Enviar"),
        btnCallSubmit: $("#btn-callSubmit"),
        modalAviso: $("#modalAviso"),
        textoAviso: $("#textoAviso"),
        navLockPauta: $("#nav-lock-pauta"),
        navOpenPauta: $("#nav-open-pauta"),
        btnPdf: $("#btn_pdf")
    };

    // =======================
    // ESTADO GLOBAL
    // =======================
    const state = {
        selectedLective: elements.lectiveYear.val() || '',
        whoIs: "",
        metricaCodeDev: null,
        idAvaliacao: 0,
        metricaId: 0,
        idPlanoEstudo: 0,
        disciplineId: 0,
        metricaIdTeacher: null,
        elementoBtnSalvar: "",
        callSubmit: "",
        cargo: "",
        version: "",
        metric: null,
        estadoPautaLancar: null,
        pautaId: null,
        pautaPath: null,
        elementLockPauta: elements.navLockPauta.html() || '',
        elementOpenPauta: elements.navOpenPauta.html() || ''
    };

    // =======================
    // INICIALIZAÇÃO
    // =======================
    elements.formNota.append(`<input type="hidden" name="selectedLective" id="selectedLective" value="${state.selectedLective}">`);
    $('#btn_lock_pauta, #btn_open_pauta').remove();  // Remove botões temporariamente

    ambiente(); // Configura ambiente inicial

    // =======================
    // FUNÇÕES AUXILIARES
    // =======================
    function log(msg, obj=null) {
        if (obj) console.log(msg, obj);
        else console.log(msg);
    }

    function showModalAviso(message) {
        elements.textoAviso.text(message);
        elements.modalAviso.modal('show');
    }

    function isValidMetrica(metricaId) {
        return metricaId !== null && metricaId !== 0 && !isNaN(metricaId);
    }

    // =======================
    // AMBIENTE
    // =======================
    function ambiente() {
        const anoL = elements.lectiveYear.val();
        if (anoL == 6) {
            $("#NextStap").hide();
            elements.tabelaNew.hide();
            $("#ConteudoMain").show();
            elements.turmaSelect.empty();
        } else {
            elements.turmaSelect.empty();
            disciplineGetNew(anoL);
            $("#ConteudoMain").hide();
            $("#NextStap").show();
        }
    }

    // =======================
    // DISCIPLINAS
    // =======================
    function disciplineGetNew(anolectivo) {
        log("Carregando disciplinas para ano:", anolectivo);

        $.ajax({
            url: `/pt/avaliations/disciplines_teacher/${anolectivo}`,
            type: "GET",
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            cache: false
        }).done(function(data) {
            elements.disciplinaSelect.empty().prop('disabled', true);
            elements.studentsNew.empty();

            if (data.disciplina && data.disciplina.length > 0) {
                elements.disciplinaSelect.append('<option selected value="00">Selecione a disciplina</option>');
                data.disciplina.forEach(row => {
                    elements.disciplinaSelect.append(
                        `<option value="${data.whoIs},${row.course_id},${row.discipline_id}">
                            #${row.code} ${row.dt_display_name}
                        </option>`
                    );
                });
                elements.disciplinaSelect.prop('disabled', false).selectpicker('refresh');
            } else {
                showModalAviso("Nenhuma disciplina encontrada.");
            }
        }).fail(function(err) {
            console.error("Erro ao carregar disciplinas:", err);
        });
    }

    // =======================
    // TURMAS
    // =======================
    function Turma(idPlano, anolectivo) {
        if (!idPlano || idPlano === "00") return;

        let url = `/pt/avaliations/turma_teacher/${idPlano}/${anolectivo}`;
        @if ($segunda_chamada)
            url += "?segunda_chamada=true";
        @endif

        log("URL da turma:", url);

        $.ajax({
            url: url,
            type: "GET",
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            cache: false
        }).done(handleTurmaSuccess).fail(handleTurmaError);
    }

    function handleTurmaSuccess(data) {
        if (!data || !data.turma) return handleTurmaError();

        // Configura turmas
        turmaLoop(data, data.whoIs === "super" ? "coordenador" : "teacher");

        if (data.whoIs === "super") {
            // Coordenador: popula avaliação e métricas
            elements.caixaAvalicao.show();
            elements.avaliacaoSelect.empty().append('<option value="">Selecione a avaliação</option>');
            data.avaliacao.forEach(av => elements.avaliacaoSelect.append(`<option value="${av.avl_id}">${av.avl_nome}</option>`));
            elements.avaliacaoSelect.selectpicker('refresh');
            state.whoIs = data.whoIs;
        } else {
            // Teacher
            handleTeacherTurma(data);
        }
    }

    function handleTurmaError() {
        elements.turmaSelect.empty().prop('disabled', true);
        elements.avaliacaoSelect.empty().hide();
        showModalAviso("Atenção! Esta disciplina não possui avaliação no ano lectivo selecionado.");
    }

    function handleTeacherTurma(data) {
        state.idPlanoEstudo = data.plano_estudo;
        state.disciplineId = data.disciplina;

        if (!data.metrica || data.metrica.length === 0) {
            showModalAviso("Nenhuma métrica disponível. Não é possível lançar notas.");
            elements.btnEnviar.hide();
        } else {
            state.metricaId = data.metrica[0].mtrc_id;
            state.metricaCodeDev = data.metrica[0].code_dev;
            state.idAvaliacao = data.avaliacao.avl_id;
            elements.tituloAvalicao.text(data.metrica[0].mtrc_nome);
            elements.btnEnviar.show();
        }

        elements.caixaAvalicao.hide();
        elements.tabelaNew.show();
    }

    function turmaLoop(data, tipo) {
        elements.turmaSelect.empty().prop('disabled', true).append('<option selected value="">Selecione a turma</option>');
        data.turma.forEach(t => elements.turmaSelect.append(`<option value="${t.id}">${t.display_name}</option>`));
        elements.turmaSelect.prop('disabled', false).selectpicker('refresh');
    }

    // =======================
    // MÉTRICAS
    // =======================
    function metricasCoordenador(idAvaliacao) {
        $.ajax({
            url: `/pt/avaliations/metrica_ajax_coordenador/${idAvaliacao}`,
            type: "GET",
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            cache: false
        }).done(function(data) {
            elements.metricaSelect.empty();
            if (data.metricas && data.metricas.length > 0) {
                elements.metricaSelect.append('<option selected value="">Selecione a métrica</option>');
                data.metricas.forEach(row => {
                    elements.metricaSelect.append(`<option value="${row.id}" data-metric="${row.code_dev}">${row.nome}</option>`);
                });
                elements.metricaSelect.prop('disabled', false).selectpicker('refresh');
            } else {
                showModalAviso("Nenhuma métrica encontrada.");
                elements.metricaSelect.prop('disabled', true);
            }
        }).fail(function(err) {
            console.error("Erro ao carregar métricas:", err);
        });
    }

    // =======================
    // ESTUDANTES
    // =======================
    function StudantGrade(disciplineId, metricaId, idPlanoEstudo, idAvaliacao, lectiveYear) {
        if (!isValidMetrica(metricaId)) {
            console.warn("Métrica inválida, não será feita chamada AJAX:", metricaId);
            return;
        }

        const turma = elements.turmaSelect.val();
        const cargo = elements.disciplinaSelect.val().split(",")[0];

        let url = `/avaliations/student_ajax/${disciplineId}/${metricaId}/${idPlanoEstudo}/${idAvaliacao}/${turma}/${lectiveYear}?whoIs=${cargo}`;

        @if ($segunda_chamada)
            url += "&segunda_chamada=true";
        @endif

        log("URL notas:", url);

        $.ajax({
            url: url,
            type: "GET",
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            cache: false,
            success: function(dataResult) {
                renderStudentsTable(dataResult, "teacher");
            },
            error: function(err) {
                console.error("Erro ao carregar notas:", err);
            }
        });
    }

    function renderStudentsTable(data, tipo) {
        elements.studentsNew.empty();
        const students = data.students || [];
        const grades = data.grades || [];

        if (students.length === 0) {
            elements.studentsNew.append('<tr><td class="text-center fs-2">Nenhum estudante foi encontrado.</td></tr>');
            return;
        }

        let html = '';
        students.forEach((student, i) => {
            const grade = grades.find(g => g.user_id == student.user_id) || {};
            const notaAluno = grade.aanota || '';
            const ausente = grade.presence;
            const isAusente = (notaAluno === null && ausente == 1) || (notaAluno === null && ausente === null && state.pautaPath !== null);
            const linhaId = `Linha_checado${student.user_id}`;
            const spanId = `span_checado${student.user_id}`;
            const notaId = `nota_checado${student.user_id}`;
            const bgColor = isAusente ? 'style="background-color:#f4f4f4;"' : '';
            const spanStyle = isAusente ? 'style="background: red; padding: 2px; color: #fff;"' : 'style="background: #38C172; padding: 2px; color: #fff;"';
            const spanText = isAusente ? 'AUSENTE' : 'PRESENTE';
            const readonly = isAusente ? 'readonly' : '';

            html += `
                <tr ${bgColor} id="${linhaId}">
                    <td>${i+1}</td>
                    <td width="120">
                        <input name="inputCheckBox[]" value="falta,${student.user_id}" id="${student.user_id}" type="checkbox" onclick="verChecagem(this);" ${isAusente ? 'checked' : ''}>
                        <span id="${spanId}" ${spanStyle}>${spanText}</span>
                    </td>
                    <td class="regime">${student.e_f == 0 ? "Frequência" : "Exame"}</td>
                    <td width="120">${student.n_student}</td>
                    <td style="font-size:0.9pc">${student.user_name}</td>
                    <td width="100">
                        <input type="hidden" name="estudantes[]" value="${student.user_id}">
                        <input type="number" id="${notaId}" min="0" max="20" name="notas[]" class="form-control notaCampo" value="${notaAluno}" ${readonly}>
                        <input type="hidden" name="whoIs" value="${tipo === 'teacher' ? 'teacher' : 'super'}">
                        <input type="hidden" name="metrica_teacher" value="${state.metricaIdTeacher}">
                        <input type="hidden" name="id_plano_estudo" value="${state.idPlanoEstudo}">
                    </td>
                </tr>`;
        });

        elements.studentsNew.append(html);
    }

    // =======================
    // EVENTOS
    // =======================
    elements.lectiveYear.change(function() {
        state.selectedLective = $(this).val();
        $('#selectedLective').val(state.selectedLective);
        ambiente();
    });

    elements.disciplinaSelect.change(function() {
        elements.avaliacaoSelect.empty();
        elements.description.val('');
        elements.tabelaNew.hide();
        elements.studentsNew.empty();

        const id = $(this).val();
        if (id) Turma(id, elements.lectiveYear.val());
    });

    elements.turmaSelect.change(function() {
        if ($(this).val() === "") {
            elements.avaliacaoSelect.prop('disabled', true);
            return;
        }
        StudantGrade(state.disciplineId, state.metricaId, state.idPlanoEstudo, state.idAvaliacao, state.selectedLective);
        elements.avaliacaoSelect.prop('disabled', false);
        elements.tabelaNew.show();
    });

    elements.avaliacaoSelect.change(function() {
        if ($(this).val() !== "") {
            elements.caixaMatrica.show();
            metricasCoordenador($(this).val());
        } else {
            elements.caixaMatrica.hide();
            elements.metricaSelect.empty();
        }
    });

    elements.metricaSelect.change(function() {
        if ($(this).val() !== "" && isValidMetrica($(this).val())) {
            state.metricaId = parseInt($(this).val());
            state.metricaCodeDev = $(this).find(':selected').data('metric');
            StudantGrade(state.disciplineId, state.metricaId, state.idPlanoEstudo, state.idAvaliacao, state.selectedLective);
        } else {
            elements.studentsNew.hide();
        }
    });

    elements.btnCallSubmit.click(() => elements.formNota.submit());

    $("#btn-callLock").click(() => $("#id_form_lock").submit());

});

// =======================
// FUNÇÃO GLOBAL: CHECAGEM DE AUSÊNCIA
// =======================
function verChecagem(element) {
    const linha = $("#Linha_checado" + element.id);
    const span = $("#span_checado" + element.id);
    const inputNota = $("#nota_checado" + element.id);

    if (!linha.length || !span.length || !inputNota.length) return;

    if (element.checked) {
        linha.css("background-color", "#f4f4f4");
        span.css("background-color", "red").text("AUSENTE");
        inputNota.val("").prop("readonly", true);
    } else {
        linha.css("background-color", "#fff");
        span.css("background-color", "#38C172").text("PRESENTE");
        inputNota.prop("readonly", false);
    }
}
</script>
@endsection
