<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new', ['painelTitleDiv' => true])
@section('page-title')
    <div class="d-flex align-items-center">
        <h1 id="pauta_titulo">
            Exibir trabalho fim de curso
        </h1>
        <label class="switch">
            <input type="checkbox" id="selector_pauta">
            <span class="slider round"></span>
        </label>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Exibir TFC</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/new_switcher.css') }}">
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
    {!! Form::open(['route' => ['imprimirPDF_Grades'], 'id' => 'printForm', 'target' => '_blank']) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-6">
            <label>Selecione o curso</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="curso_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina"
                tabindex="-98"></select>
        </div>
    </div>
    <input type="hidden" value="0" id="verificarSelector" />
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="" />
    {{-- {{Dados}} --}}
    <input type="hidden" id="data_html" name="data_html" value="" />
    {{-- {{Dados Curso}} --}}
    <input type="hidden" id="curso_id" name="curso_id" value="" />
    {{-- {{Dados Turma}} --}}
    <input type="hidden" id="id_turma" name="id_turma" value="" />
    {{-- {{Dados Disciplina}} --}}
    <input type="hidden" id="id_disciplina" name="id_disciplina" value="" />
    {{-- {{Dados Pauta Code}} --}}
    <input type="hidden" id="pauta_code" name="pauta_code" value="" />
    <input type="hidden" id="pauta_dados" name="pauta_dados" value="" />

    {{-- INCLUI A LISTA DE NOTAS --}}
    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class="table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align: center"></tr>
            </thead>
            <tbody id="lista_tr"></tbody>
        </table>
    </div>
    <div id="div_btn_save" class=" float-right">
        <button class="btn btn-success mb-3 ml-3" id="generate-pdf" data-toggle="modal" data-target="#exampleModal">
            <i class="fas fa-plus-circle"></i>
            Imprimir pauta
        </button>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {
            console.clear();
            var id_anoLectivo = $("#lective_year");
            var anoCurso_id_Select = $("#anoCurso_id_Select")
            var curso = $("#curso_id_Select")
            var Disciplina_Select = $("#Disciplina_id_Select")
            var Turma_id_Select = $("#Turma_id_Select")
            var disciplina_nome;
            var turma_nome;
            var curso_nome;
            var ano_nome;
            var disciplina_regime;
            var selector_pauta = null;

            selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');

            function ouculta_botao() {
                document.getElementById('generate-pdf').style.visibility = 'hidden';
            }

            $("#selector_pauta").click(function() {
                selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');
                change_title_page(selector_pauta);
                getStudentNotasPautaFinal();
            });

            change_title_page($("#selector_pauta").parent().find('input').is(':checked'));

            function change_title_page(estado) {
                if (estado === false) {
                    $("#pauta_titulo").parent().find('h1').html(
                        "EXIBIR TRABALHO FIM DE CURSO - Propinas Liquidadas");
                } else {
                    $("#pauta_titulo").parent().find('h1').html("EXIBIR TRABALHO FIM DE CURSO - Propinas Pedentes");
                }
            }

            ouculta_botao();
            getCurso(id_anoLectivo);
            ano_nome = $("#lective_year")[0].selectedOptions[0].text;
            // DADOS LECTIVO
            $("#id_anoLectivo").val(id_anoLectivo.val());
            //Remoção de caracters;
            ano_str1 = $("#lective_year")[0].selectedOptions[0].text[3];
            ano_str2 = ano_str1.concat($("#lective_year")[0].selectedOptions[0].text[4]);

            var id_curso = ano_nome.replace(ano_str2, '');
            id_curso = id_curso.replace('/', '');
            var ano_str1;
            var ano_str2;
            var ano_valor = "20";

            ano_str2 = ano_valor.concat(id_curso);

            id_anoLectivo.bind('change keypress', function() {
                ouculta_botao();

                $("#lista_tr").empty();
                $("#listaMenu").empty();
                Turma_id_Select.empty();
                Disciplina_Select.empty();
                Disciplina_Select.prop('disabled', true);
                Turma_id_Select.prop('disabled', true);
                Turma_id_Select.selectpicker('refresh');
                Disciplina_Select.selectpicker('refresh');
            })

            function getCurso(id_anoLectivo) {
                $.ajax({
                    url: "/avaliations/getCurso/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    if (data['data'].length > 0) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        curso.empty();

                        curso.append('<option selected="" value="0">Selecione o curso</option>');
                        $.each(data['data'], function(indexInArray, row) {
                            curso.append('<option value="' + row.id + ',' + row.duration_value +
                                ' ,' + row.code + ' ">' + row.nome_curso + '</option>');
                        });
                        curso.prop('disabled', false);
                        curso.selectpicker('refresh');
                    }
                });
            }

            curso.bind('change keypress', function() {
                ouculta_botao();

                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var id_curso = vetorCurso.split(re);
                $.ajax({
                    url: "/avaliations/getTurma/" + id_anoLectivo.val() + "/" + id_curso[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {

                    curso_nome = $("#curso_id_Select")[0].selectedOptions[0].text;

                    if (data == 500) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        alert(
                            "Atenção! este curso não está associada a nenhuma turma no ano lectivo selecionado, verifique a edição de plano de estudo do mesma.");
                    }

                    if (data['data'].length > 0) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        getStudentNotasPautaFinal();
                    }
                });
            })



            function getStudentNotasPautaFinal() {
                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var id_curso = vetorCurso.split(re);

                $.ajax({
                    url: "/avaliations/getStudentGradesTFC/" + id_anoLectivo.val() + "/" + id_curso[0] +
                        "/" + 6,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    if (data['data']['disciplina']) {
                        let idDisciplina = data['data']['disciplina']['discipline_id']
                        $('#id_disciplina').val(idDisciplina);
                    }

                    var resultados_student = data['data']['dados'];

                    if (resultados_student.length != 0) {
                        //GERADOR NO MENU PAUTA
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        tabelatr += "<th>#</th>"
                        tabelatr += "<th>MATRÍCULA</th>"
                        tabelatr += "<th>ESTUDANTE</th>"
                        tabelatr += "<th>Trabalho</th>"
                        tabelatr += "<th>Defesa</th>"
                        tabelatr += "<th  colspan='2'>CLASSIFICAÇÃO TFC</th>"
                        $("#listaMenu").append(tabelatr);
                        document.getElementById('generate-pdf').style.visibility = 'visible';
                    } else {
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        $("#listaMenu").append(
                            "<center>Selecione uma disciplina que tenha notas lançadas.</center>");
                        document.getElementById('generate-pdf').style.visibility = 'hidden';
                    }

                    var tabelatr = "";
                    var aluno_nome = "";
                    var numero_alunos = 0;
                    var count_val = 0;
                    var defesa_nome = "";
                    var defesa = 0;
                    var defesa_percentagem = 0;
                    var trabalho_nome = "";
                    var trabalho = 0;
                    var trabalho_percentagem = 0;
                    var lista_alunos_notas = [];
                    var estatistica_pauta = [];
                    var alunos_masculino = 0;
                    var alunos_femenino = 0;
                    var alunos_masculino_reprovado = 0;
                    var alunos_femenino_reprovado = 0;
                    var alunos_aprovados = 0;
                    var alunos_reprovados = 0;
                    var id_disc = 0;
                    var id_turm = 0;
                    var traba = 0;
                    var defes = 0;
                    var nota_ja_existe = 0;

                    //GERA A LISTA DE ESTUDANTES E SUAS NOTAS
                    $("#lista_tr").empty();
                    // INFORMA O TIPO DE PAUTA A SER SALVA
                    $("#pauta_code").val(50);
                    $("#tipo_pauta").val("Pauta TFC");


                    $.each(resultados_student, function(index, item) {
                        aluno_ja_existe = 0;
                        nota_ja_existe = 0;
                        defesa_nome = "";
                        trabalho_nome = "";

                        $.each(item, function(index_avaliacao, item_avaliacao) {
                            if (aluno_nome != item_avaliacao.fullName_value) {
                                aluno_nome = item_avaliacao.fullName_value;
                                numero_alunos += 1;
                                tabelatr += "<tr><td style='text-align: center'>" +
                                    numero_alunos + "</td>"
                                tabelatr += "<<td style='text-align: center'>" +
                                    item_avaliacao.up_meca_matriculaId + "</td>"
                                tabelatr += "<td style='text-align: left'>" + item_avaliacao
                                    .fullName_value + "</td>"
                                aluno_ja_existe = 1;
                                nota_ja_existe = 1;
                                // MOSTRA AS NOTAS
                                if (item_avaliacao.mt_code_dev === "Trabalho") {
                                    if (item_avaliacao.avlAluno_nota === null) {
                                        tabelatr +=
                                            "<td style='text-align: center'> F </td>";
                                        trabalho = 0;
                                        trabalho_percentagem = 0;
                                    } else {
                                        trabalho = item_avaliacao.avlAluno_nota;
                                        trabalho_percentagem = item_avaliacao
                                        .mt_percentagem;
                                        tabelatr += "<td style='text-align: center'>" +
                                            trabalho + "</td>";
                                    }
                                } else {
                                    tabelatr += "<td style='text-align: center'> - </td>";
                                    trabalho = 0;
                                    trabalho_percentagem = 0;
                                }
                            } else {
                                if (aluno_ja_existe === 1) {
                                    aluno_ja_existe = 2;
                                    nota_ja_existe = 2;
                                    if (item_avaliacao.mt_code_dev === "Defesa") {
                                        if (item_avaliacao.avlAluno_nota === null) {
                                            tabelatr +=
                                                "<td style='text-align: center'> F </td>";
                                            defesa = 0;
                                            defesa_percentagem = 0;
                                        } else {
                                            defesa = item_avaliacao.avlAluno_nota;
                                            defesa_percentagem = item_avaliacao
                                                .mt_percentagem;
                                            tabelatr += "<td style='text-align: center'>" +
                                                defesa + "</td>";
                                        }
                                    } else {
                                        tabelatr +=
                                            "<td style='text-align: center'> - </td>";
                                        defesa = 0;
                                        defesa_percentagem = 0;
                                    }
                                }
                            }

                            // CLASSIFICAÇÃO FINAL
                            if (aluno_ja_existe === 2) {
                                // MOSTRA AS NOTAS
                                calculo_med = ((trabalho * ((trabalho_percentagem) / 100)) +
                                    (defesa * ((defesa_percentagem) / 100)));
                                tabelatr += "<td style='text-align: center'>" +
                                    calculo_med + "</td>"
                                aluno_ja_existe = 3;
                                nota_ja_existe = 3;
                                if (calculo_med >= 10) {
                                    tabelatr +=
                                        "<td class='c_final' style='text-align: center'>" +
                                        'Aprovado(a)' + "</td>"
                                    lista_alunos_notas.push([item_avaliacao
                                        .up_meca_matriculaId, item_avaliacao
                                        .fullName_usersId, calculo_med,
                                        item_avaliacao.sexo, "@"
                                    ])
                                    if (item_avaliacao.sexo == "Feminino") {
                                        alunos_femenino += 1;
                                    }
                                    if (item_avaliacao.sexo == "Masculino") {
                                        alunos_masculino += 1;
                                    }
                                    alunos_aprovados += 1;
                                } else {
                                    tabelatr +=
                                        "<td class='c_final' style='text-align: center'>" +
                                        'Reprovado(a)' + "</td>"
                                    lista_alunos_notas.push([item_avaliacao
                                        .up_meca_matriculaId, item_avaliacao
                                        .fullName_usersId, calculo_med,
                                        item_avaliacao.sexo, "@"
                                    ])
                                    if (item_avaliacao.sexo == "Feminino") {
                                        alunos_femenino_reprovado += 1;
                                    }
                                    if (item_avaliacao.sexo == "Masculino") {
                                        alunos_masculino_reprovado += 1;
                                    }
                                    alunos_reprovados += 1;
                                }
                                id_disc = item_avaliacao.dt_discipId;
                                id_turm = item_avaliacao.avaliacao_aluno_turma;
                            }
                            if (resultados_student = data['data']['Trabalho'] === 0) {
                                tabelatr += "<td style='text-align: center'> - </td>";
                                tabelatr +=
                                    "<td class='c_final' style='text-align: center'>" +
                                    'Reprovado(a)' + "</td>"
                            }
                            if (resultados_student = data['data']['Defesa'] === 0) {
                                tabelatr += "<td style='text-align: center'> - </td>";
                                tabelatr +=
                                    "<td class='c_final' style='text-align: center'>" +
                                    'Reprovado(a)' + "</td>"
                            }
                        });
                    });
                    //Tag que fecha a tabela
                    tabelatr += "</tr>"
                    $("#lista_tr").append(tabelatr);
                    estatistica_pauta.push([numero_alunos, alunos_aprovados, alunos_reprovados,
                        alunos_femenino, alunos_masculino, alunos_femenino_reprovado,
                        alunos_masculino_reprovado
                    ]);
                    $("#pauta_estatistica").val(estatistica_pauta);
                    $("#pauta_dados").val(lista_alunos_notas);
                    $("#discipline_id").val(id_disc);
                    $("#curso_id").val(id_curso[0]);
                    $("#id_turma").val(id_turm);
                    $("#data_html").val($("#pauta_disciplina").html());
                });
            }
        })
    </script>
@endsection
