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
        // Inicialização de variáveis com valores padrão
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

        // Estado da aplicação
        const state = {
            selectedLective: elements.lectiveYear.val() || '',
            whoIs: "",
            metricaCodeDev: "",
            idAvaliacao: 0,
            metricaId: 0,
            idPlanoEstudo: 0,
            disciplineId: 0,
            metricaIdTeacher: "",
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

        // Remover botões de lock/open pauta inicialmente
        $('#btn_lock_pauta').remove();
        $('#btn_open_pauta').remove();

        console.log("Ano lectivo selecionado:", state.selectedLective);

        // Adicionar campo hidden para ano lectivo
        const lectiveInput = `<input type="hidden" name="selectedLective" id="selectedLective" class="form-control" value="${state.selectedLective}">`;
        elements.formNota.append(lectiveInput);

        // ========== FUNÇÕES PRINCIPAIS ==========

        /**
         * Carrega o histórico da pauta
         */
        function getHistoric() {
            if (!state.pautaId) {
                console.error("Erro: pauta_id não definido!");
                return;
            }

            const url = `/avaliations/historico-pauta-ajax/${state.pautaId}`;
            console.log("Carregando histórico:", url);

            if ($.fn.DataTable.isDataTable('#historic-table')) {
                $('#historic-table').DataTable().clear().destroy();
            }

            $('#historic-table').DataTable({
                ajax: url,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'description', name: 'description' },
                    { data: 'file', name: 'file' },
                    { data: 'name', name: 'posted_by' },
                    { data: 'updated_at', name: 'posted_at' }
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        }

        /**
         * Define o tipo de pauta baseado na métrica
         */
        function setarPauta(whoIs) {
            console.log("Setando pauta para:", whoIs);

            const pautaStatus = {
                'PF1': '40', 'PF2': '40', 'OA': '40', 'Recurso': '10',
                'Neen': '20', 'oral': '25', 'Exame_especial': '35',
                'Extraordinario': '45', 'Trabalho': '50', 'Defesa': '50', 'TESP': '60'
            };

            let pautaTipo = "";
            let tipo = 0;

            if (whoIs === "teacher") {
                state.metric = state.metricaCodeDev;
            } else if (whoIs === "super") {
                const selectedMetric = elements.metricaSelect.find('option:selected');
                state.metric = selectedMetric.data('metric') || null;
            }

            if (state.metric && pautaStatus.hasOwnProperty(state.metric)) {
                pautaTipo = state.metric;
                tipo = pautaStatus[state.metric];
            }

            const pautaValue = `${pautaTipo},${tipo}`;
            $('#pauta').val(pautaValue);
            console.log("Pauta definida:", $('#pauta').val());
        }

        /**
         * Controla a exibição dos elementos baseado no ambiente
         */
        function ambiente() {
            const anoL = elements.lectiveYear.val();
            
            if (anoL == 6) {
                $("#NextStap").hide();
                elements.tabelaNew.hide();
                $("#ConteudoMain").show();
                elements.turmaSelect.empty();
            } else {
                elements.turmaSelect.empty();
                const anoL = elements.lectiveYear.val();
                disciplineGetNew(anoL);
                $("#ConteudoMain").hide();
                $("#NextStap").show();
            }
        }

        /**
         * Carrega disciplinas do professor
         */
        function disciplineGetNew(anolectivo) {
            console.log("Carregando disciplinas para ano:", anolectivo);

            $.ajax({
                url: `/pt/avaliations/disciplines_teacher/${anolectivo}`,
                type: "GET",
                data: { _token: '{{ csrf_token() }}' },
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    console.log("Carregando as disciplinas...");
                }
            }).done(function(data) {
                if (data.disciplina && data.disciplina.length) {
                    elements.studentsNew.empty();
                    elements.disciplinaSelect.prop('disabled', true).empty();
                    
                    elements.disciplinaSelect.append('<option selected value="00">Selecione a disciplina</option>');
                    
                    $.each(data.disciplina, function(index, row) {
                        elements.disciplinaSelect.append(
                            `<option value="${data.whoIs},${row.course_id},${row.discipline_id}">
                                #${row.code}  ${row.dt_display_name}
                            </option>`
                        );
                    });

                    elements.disciplinaSelect.prop('disabled', false).selectpicker('refresh');
                } else {
                    elements.disciplinaSelect.empty().prop('disabled', true);
                    console.warn("Nenhuma disciplina encontrada");
                }
            }).fail(function(error) {
                console.error("Erro ao carregar disciplinas:", error);
            });
        }

        /**
         * Carrega turmas baseado na disciplina selecionada
         */
        function Turma(idPlano, anolectivo) {
            console.log("Carregando turmas para plano:", idPlano, "ano:", anolectivo);
            
            if (!idPlano || idPlano === "00") {
                console.warn("ID do plano inválido");
                return false;
            }

            const planoDisciplina = idPlano.split(/\s*,\s*/);
            let url = `/pt/avaliations/turma_teacher/${idPlano}/${anolectivo}`;

            @if ($segunda_chamada)
                url += "?segunda_chamada=true";
            @endif

            console.log("URL da turma:", url);

            $.ajax({
                url: url,
                type: "GET",
                data: { _token: '{{ csrf_token() }}' },
                cache: false,
                dataType: 'json'
            }).done(function(data) {
                if (data === 500) {
                    handleTurmaError();
                } else {
                    handleTurmaSuccess(data);
                }
            }).fail(function(error) {
                console.error("Erro ao carregar turmas:", error);
                handleTurmaError();
            });
        }

        function handleTurmaError() {
            elements.turmaSelect.empty().prop('disabled', true);
            elements.avaliacaoSelect.empty().hide();
            elements.textoAviso.text(
                "Atenção! Esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma."
            );
            elements.modalAviso.modal('show');
        }

        function handleTurmaSuccess(data) {
            if (data.whoIs === "super") {
                turmaLoop(data, "coordenador");
                elements.caixaAvalicao.show();
                
                elements.avaliacaoSelect.append('<option value="">Selecione a avaliação</option>');
                $.each(data.avaliacao, function(index, row) {
                    elements.avaliacaoSelect.append(`<option value="${row.avl_id}">${row.avl_nome}</option>`);
                });
                elements.avaliacaoSelect.selectpicker('refresh');
                
                state.whoIs = data.whoIs;
            } else {
                console.log(data);
                handleTeacherTurma(data);
            }
        }

        function handleTeacherTurma(data) {
            if (!data.avaliacao) {
                elements.textoAviso.text(
                    "Caro docente, verificou-se que não há calendário de prova disponível, razão pela qual não pode fazer o lançamento de notas. Contacte o superior hierárquico para habilitar ou extender a data do calendáro de prova."
                );
                elements.modalAviso.modal('show');
                elements.btnEnviar.hide();
            } else {
                elements.btnEnviar.show();
            }

            state.whoIs = data.whoIs;
            elements.caixaAvalicao.hide();
            elements.avaliacaoSelect.empty();

            state.idAvaliacao = data.avaliacao.avl_id;
            state.metricaIdTeacher = data.metrica && data.metrica.length > 0 ? data.metrica[0].mtrc_id : "Sem métrica no intervalo";
            state.metricaCodeDev = data.metrica && data.metrica.length > 0 ? data.metrica[0].code_dev : null;
            state.disciplineId = data.disciplina;
            state.idPlanoEstudo = data.plano_estudo;

            turmaLoop(data, "teacher");
            setarPauta("teacher");
            elements.tabelaNew.hide();
        }

        /**
         * Preenche o select de turmas
         */
        function turmaLoop(data, tipo) {
            if (data.turma && data.turma.length) {
                state.idPlanoEstudo = data.plano_estudo;
                state.disciplineId = data.disciplina;

                if (tipo === "teacher") {
                    console.log("Turmas do professor:", data.turma);
                    elements.tituloAvalicao.empty();
                    
                    state.metricaId = data.metrica && data.metrica.length > 0 ? data.metrica[0].mtrc_id : "Sem métrica no intervalo";
                    const metricaName = data.metrica && data.metrica.length > 0 ? data.metrica[0].mtrc_nome : "Sem nome da métrica no intervalo";
                    const validarMetrica = data.metrica && data.metrica.length > 0 ? data.metrica[0].mtrc_nome : "";

                    if (!validarMetrica) {
                        elements.textoAviso.text(
                            "Caro docente, verificou-se que não há calendário de prova disponível, razão pela qual não pode fazer o lançamento de notas. Contacte o superior hierárquico para habilitar ou extender a data do calendáro de prova."
                        );
                        elements.modalAviso.modal('show');
                        elements.btnEnviar.hide();
                    } else {
                        elements.btnEnviar.show();
                    }
                    
                    elements.tituloAvalicao.text(metricaName);
                }

                elements.tabelaNew.show();
                elements.turmaSelect.prop('disabled', true).empty();
                elements.turmaSelect.append('<option selected value="">Selecione a turma</option>');
                
                $.each(data.turma, function(index, row) {
                    elements.turmaSelect.append(`<option value="${row.id}">${row.display_name}</option>`);
                });
                
                elements.turmaSelect.prop('disabled', false).selectpicker('refresh');
            } else {
                elements.turmaSelect.empty().prop('disabled', true);
                elements.avaliacaoSelect.prop('disabled', true);
                console.warn("Nenhuma turma encontrada");
            }
        }

        /**
         * Carrega métricas para coordenador
         */
        function metricasCoordenador(idAvaliacao) {
            console.log("Carregando métricas para avaliação:", idAvaliacao);

            $.ajax({
                url: `/pt/avaliations/metrica_ajax_coordenador/${idAvaliacao}`,
                type: "GET",
                data: { _token: '{{ csrf_token() }}' },
                cache: false,
                dataType: 'json'
            }).done(function(data) {
                if (data.metricas && data.metricas.length) {
                    elements.metricaSelect.empty();
                    elements.metricaSelect.append('<option selected value="">Selecione a métrica</option>');
                    
                    $.each(data.metricas, function(index, row) {
                        elements.metricaSelect.append(
                            `<option value="${row.id}" data-metric="${row.code_dev}">${row.nome}</option>`
                        );
                    });
                    
                    elements.metricaSelect.prop('disabled', false).selectpicker('refresh');
                } else {
                    console.warn("Nenhuma métrica encontrada");
                    elements.metricaSelect.prop('disabled', true).empty();
                }
            }).fail(function(error) {
                console.error("Erro ao carregar métricas:", error);
            });
        }

        /**
         * Carrega estudantes para coordenador
         */
        function studentCourseCoordenador() {
            const turma = elements.turmaSelect.val();
            const lectiveYearSelect = elements.lectiveYear.val();
            state.cargo = elements.disciplinaSelect.val().split(",")[0];
            
            console.log("Carregando estudantes para coordenador, cargo:", state.cargo);

            let url = `/avaliations/student_ajax/${state.disciplineId}/${elements.metricaSelect.val()}/${state.idPlanoEstudo}/${elements.avaliacaoSelect.val()}/${turma}/${lectiveYearSelect}?whoIs=${state.cargo}`;

            @if ($segunda_chamada)
                url += "&segunda_chamada=true";
            @endif

            console.log("URL estudantes coordenador:", url);

            $.ajax({
                url: url,
                type: "GET",
                data: { _token: '{{ csrf_token() }}' },
                cache: false,
                dataType: 'json'
            }).done(function(data) {
                renderStudentsTable(data, "coordenador");
            }).fail(function(error) {
                console.error("Erro ao carregar estudantes coordenador:", error);
            });
        }

        /**
         * Carrega notas dos estudantes
         */
        function StudantGrade(disciplineId, metricaId, idPlanoEstudo, idAvaliacao, lectiveYear) {
            state.cargo = elements.disciplinaSelect.val().split(",")[0];
            const turma = elements.turmaSelect.val();
            
            console.log("Carregando notas, cargo:", state.cargo);

            let url = `/avaliations/student_ajax/${disciplineId}/${metricaId}/${idPlanoEstudo}/${idAvaliacao}/${turma}/${lectiveYear}?whoIs=${state.cargo}`;

            @if ($segunda_chamada)
                url += "&segunda_chamada=true";
            @endif

            console.log("URL notas:", url);

            $.ajax({
                url: url,
                type: "GET",
                data: { _token: '{{ csrf_token() }}' },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    renderStudentsTable(dataResult, "teacher");
                },
                error: function(error) {
                    console.error('Erro ao carregar notas:', error);
                }
            });
        }

        /**
         * Renderiza a tabela de estudantes
         */
        function renderStudentsTable(data, tipo) {
            console.log(`Renderizando tabela para: ${tipo}`, data);

            // Atualizar estado da pauta
            updatePautaState(data);

            // Limpar tabela
            elements.studentsNew.empty();

            const resultStudents = data.students || [];
            const studentsSegundaChamada = data.students_segunda_chamada || [];
            const grades = data.grades || [];
            let bodyData = '';
            let i = 1;

            state.version = data.version || '';
            $('#version').val(state.version);

            if (resultStudents.length > 0) {
                resultStudents.forEach(function(student) {
                    const studentGrade = findStudentGrade(grades, student.user_id);
                    const regime = student.e_f == 0 ? "Frequência" : "Exame";
                    
                    bodyData += createStudentRow(student, studentGrade, i++, regime, tipo);
                });
            } else {
                bodyData = '<tr><td class="text-center fs-2">Nenhum estudante foi encontrado nesta turma.</td></tr>';
            }

            elements.studentsNew.append(bodyData);

            // Aplicar restrições para segunda chamada
            applySegundaChamadaRestrictions(resultStudents, studentsSegundaChamada);
        }

        function updatePautaState(data) {
            state.estadoPautaLancar = data.estado_pauta_lancar;
            state.pautaId = data.pauta_id;
            state.pautaPath = data.pauta_path;

            getHistoric();

            // Atualizar botão PDF
            if (state.pautaPath) {
                elements.btnPdf.attr('href', state.pautaPath)
                    .find('span').removeClass('btn-danger').addClass('btn-primary');
            }

            $('#lock').val(state.pautaId);
            const openUrl = `/pt/avaliations/open-pauta/${state.pautaId}`;

            // Controlar visibilidade dos botões de pauta
            if (state.estadoPautaLancar == 1) {
                showOpenPauta();
                $('#btn_open_pauta').attr('href', openUrl);
                
                state.elementoBtnSalvar = $("#div_btn_save").html();
                state.callSubmit = $("#ocultar_btn").html();
                elements.btnEnviar.remove();
                elements.btnCallSubmit.remove();
                $(".notaCampo").attr("disabled", true);
            } else {
                hideOpenPauta();
                if (state.elementoBtnSalvar) {
                    $("#div_btn_save").html(state.elementoBtnSalvar);
                    $("#ocultar_btn").html(state.callSubmit);
                }
            }

            if (state.estadoPautaLancar != 1 && data.grades && data.grades.length > 0) {
                showLockPauta();
            } else {
                hideLockPauta();
            }
        }

        function findStudentGrade(grades, userId) {
            return grades.find(nota => nota.user_id == userId) || {};
        }

        function createStudentRow(student, grade, index, regime, tipo) {
            const notaAluno = grade.aanota || '';
            const ausente = grade.presence;
            const isAusente = (notaAluno === null && ausente == 1) || (notaAluno === null && ausente === null && state.pautaPath !== null);
            
            const linhaId = `Linha_checado${student.user_id}`;
            const spanId = `span_checado${student.user_id}`;
            const notaId = `nota_checado${student.user_id}`;
            
            const bgColor = isAusente ? 'style="background-color:#f4f4f4;"' : '';
            const spanStyle = isAusente ? 
                'style="background: red; padding: 2px; color: #fff;"' : 
                'style="background: #38C172; padding: 2px; color: #fff;"';
            const spanText = isAusente ? 'AUSENTE' : 'PRESENTE';
            const checked = isAusente ? 'checked' : '';
            const readonly = isAusente ? 'readonly' : '';

            return `
                <tr ${bgColor} id="${linhaId}">
                    <td>${index}</td>
                    <td width="120">
                        <input name="inputCheckBox[]" value="falta,${student.user_id}" 
                               id="${student.user_id}" onclick="verChecagem(this);" 
                               type="checkbox" ${checked}>
                        <span id="${spanId}" ${spanStyle}>${spanText}</span>
                    </td>
                    <td class="regime">${regime}</td>
                    <td width="120">${student.n_student}</td>
                    <td style="font-size:0.9pc">${student.user_name}</td>
                    <td width="100">
                        <input type="hidden" name="estudantes[]" value="${student.user_id}">
                        <input type="number" id="${notaId}" min="0" max="20" 
                               name="notas[]" class="form-control ${tipo === 'teacher' ? 'notaCampo' : ''}" 
                               value="${notaAluno}" ${readonly}>
                        <input type="hidden" name="whoIs" value="${tipo === 'teacher' ? 'teachear' : 'super'}">
                        <input type="hidden" name="metrica_teacher" value="${state.metricaIdTeacher}">
                        <input type="hidden" name="id_plano_estudo" value="${state.idPlanoEstudo}">
                    </td>
                </tr>`;
        }

        function applySegundaChamadaRestrictions(students, studentsSegundaChamada) {
            students.forEach(function(student) {
                if (studentsSegundaChamada.includes(student.user_id)) {
                    $(`#nota_checado${student.user_id}`).prop('readonly', true);
                    $(`#${student.user_id}`).prop('readonly', true);
                }
            });
        }

        // ========== FUNÇÕES DE CONTROLE DE PAUTA ==========

        function showLockPauta() {
            elements.navLockPauta.html(state.elementLockPauta);
        }

        function hideLockPauta() {
            state.elementLockPauta = elements.navLockPauta.html();
            $("#btn_lock_pauta").remove();
        }

        function showOpenPauta() {
            elements.navOpenPauta.html(state.elementOpenPauta);
        }

        function hideOpenPauta() {
            state.elementOpenPauta = elements.navOpenPauta.html();
            $("#btn_open_pauta").remove();
        }

        function setarDesc() {
            elements.description.text('');
            elements.caixaDesc.attr('style', '');
        }

        // ========== EVENT HANDLERS ==========

        // Evento de mudança no ano lectivo
        elements.lectiveYear.change(function() {
            ambiente();
            state.selectedLective = elements.lectiveYear.val();
            $('#selectedLective').val(state.selectedLective);
        });

        // Evento de mudança na disciplina
        elements.disciplinaSelect.change(function() {
            elements.avaliacaoSelect.empty();
            elements.description.val('');
            elements.tabelaNew.hide();
            elements.studentsNew.empty();
            
            const id = elements.disciplinaSelect.val();
            if (id) {
                Turma(id, elements.lectiveYear.val());
            }
        });

        // Evento de mudança na turma
        elements.turmaSelect.change(function() {
            elements.description.val('');
            const lectiveYear = elements.lectiveYear.val();
            const id = elements.disciplinaSelect.val();
            
            if (elements.turmaSelect.val() === "") {
                console.log('Turma não selecionada');
                elements.avaliacaoSelect.prop('disabled', true);
            } else {
                StudantGrade(state.disciplineId, state.metricaId, state.idPlanoEstudo, state.idAvaliacao, lectiveYear);
                elements.avaliacaoSelect.prop('disabled', false);
                elements.tabelaNew.show();
            }
        });

        // Evento de mudança na avaliação (coordenador)
        elements.avaliacaoSelect.change(function() {
            elements.description.val('');
            if (elements.avaliacaoSelect.val() !== "") {
                elements.caixaMatrica.show();
                const id = elements.avaliacaoSelect.val();
                metricasCoordenador(id);
            } else {
                elements.caixaMatrica.hide();
                elements.metricaSelect.empty();
            }
        });

        // Evento de mudança na métrica (coordenador)
        elements.metricaSelect.change(function() {
            elements.description.val('');
            if (elements.metricaSelect.val() !== "") {
                setarPauta("super");
                studentCourseCoordenador();
            } else {
                elements.description.val('');
                elements.studentsNew.hide();
            }
        });

        // Eventos de submit
        elements.btnCallSubmit.click(function() {
         
                elements.formNota.submit();
            
        });

        $("#btn-callLock").click(function() {
            $("#id_form_lock").submit();
        });

        // ========== INICIALIZAÇÃO ==========
        ambiente();
    });

    // ========== FUNÇÕES GLOBAIS ==========

    /**
     * Controla o estado de desabilitação dos inputs
     */
    function disbleInput(dd) {
        console.log('Alternando estado do input:', dd);
        
        const checkElement = document.getElementById("check" + dd);
        const inputGrade = document.getElementById(dd);
        const span = document.getElementById("span" + dd);
        const hiddenInput = document.getElementById("input" + dd);

        if (!checkElement || !inputGrade || !span || !hiddenInput) {
            console.error("Elementos não encontrados para:", dd);
            return;
        }

        const checkStatus = checkElement.checked;

        if (checkStatus) {
            inputGrade.readOnly = false;
            hiddenInput.value = "true";
            span.style.backgroundColor = "#38C172";
            span.innerHTML = "PRESENTE";
        } else {
            inputGrade.readOnly = true;
            hiddenInput.value = "false";
            span.style.backgroundColor = "red";
            span.innerHTML = "AUSENTE";
        }
    }

    /**
     * Verifica se input está vazio (função de utilidade)
     */
    function checkInputEmpty(dd) {
        const inputGrade = document.getElementById(dd);
        console.log("Verificando input:", dd, "Valor:", inputGrade ? inputGrade.value : "Elemento não encontrado");
    }

    /**
     * Controla a checagem de presença/ausência
     */
    function verChecagem(element) {
        console.log("Verificando checagem:", element);
        
        if (!element || !element.id) {
            console.error("Elemento inválido");
            return;
        }

        const linha = $("#Linha_checado" + element.id);
        const span = $("#span_checado" + element.id);
        const inputNota = $("#nota_checado" + element.id);
        const checkbox = document.getElementById(element.id);

        if (!linha.length || !span.length || !inputNota.length || !checkbox) {
            console.error("Elementos da linha não encontrados para ID:", element.id);
            return;
        }

        if (checkbox.checked) {
            // Marcar como AUSENTE
            linha.css("background-color", "#f4f4f4");
            span.css("background-color", "red").text("AUSENTE");
            inputNota.val("").prop("readonly", true);
        } else {
            // Marcar como PRESENTE
            linha.css("background-color", "#fff");
            span.css("background-color", "#38C172").text("PRESENTE");
            inputNota.val("").prop("readonly", false);
            checkbox.value = "";
        }
    }
</script>
@endsection
