<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new', ['painelTitleDiv' => true])
@section('page-title')
    <div class="d-flex align-items-center">
        <h1 id="pauta_titulo">
            EXIBIR CLASSIFICAÇÃO FINAL
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
    <li class="breadcrumb-item active" aria-current="page">Exibir classificação final</li>
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
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                ×
            </button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-6 p-2">
            <label>Selecione o curso</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="curso_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina"
                tabindex="-98"></select>
        </div>
        <div class="col-6 p-2" id="disciplines-container">
            <label>Selecione a turma</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_turma"
                tabindex="-98"></select>
        </div>
        <div class="col-6 p-2" id="disciplines-container">
            <label>Selecione a disciplina</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_disciplina"
                tabindex="-98"> </select>
        </div>
    </div>

    <input type="hidden" value="0" id="verificarSelector">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    {{-- {{Dados}} --}}
    <input type="hidden" id="data_html" name="data_html" value="">
    {{-- {{Dados Curso}} --}}
    <input type="hidden" id="curso_id" name="curso_id" value="">{{-- {{Dados Pauta Code}} --}}
    <input type="hidden" id="pauta_code" name="pauta_code" value="">
    <input type="hidden" id="pauta_dados" name="pauta_dados" value="">
    <input type="hidden" id="pauta_estatistica" name="pauta_estatistica" value="">

    {{-- INCLUI A LISTA DE NOTAS --}}
    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class="table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align: center"></tr>
            </thead>
            <tbody id="lista_tr"> </tbody>
        </table>
    </div>
    <div id="div_btn_save" class=" float-right">
        <a id="generate-pdf" target="_blank" style="color: white">
            <span class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal" data-target="#exampleModal">
                <i class="fas fa-plus-circle"></i>
                Imprimir pauta
            </span>
        </a>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script src="{{ asset('js/new_avalicacoes_publicar.js') }}"></script>
    <script>
        $(document).ready(function() {
            console.clear();
            console.log("Pauta Final Docente");

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

            $("#selector_pauta").click(function() {
                // $("#selector_pauta").submit();
                selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');
                change_title_page(selector_pauta);
                getStudentNotasPautaFinal();
            });

            change_title_page($("#selector_pauta").parent().find('input').is(':checked'));

            function change_title_page(estado) {
                if (estado === false) {
                    $("#pauta_titulo").parent().find('h1').html("Exibir Classificação Final - Propinas Liquidadas");
                } else {
                    $("#pauta_titulo").parent().find('h1').html("Exibir Classificação Final - Propinas Pedentes");
                }

                document.getElementById('generate-pdf').style.visibility = 'hidden';
            }

            document.getElementById('generate-pdf').style.visibility = 'hidden';

            getCurso(id_anoLectivo);
            ano_nome = $("#lective_year")[0].selectedOptions[0].text;

            //Remoção de caracters;
            ano_str1 = $("#lective_year")[0].selectedOptions[0].text[3];
            ano_str2 = ano_str1.concat($("#lective_year")[0].selectedOptions[0].text[4]);

            var id_curso = ano_nome.replace(ano_str2, '');
            id_curso = id_curso.replace('/', '');
            var ano_str1;
            var ano_str2;
            var ano_valor = "20";

            ano_str2 = ano_valor.concat(id_curso);

            // id_anoLectivo.bind('change keypress', function() {
            $("#lective_year").change(function() {
                $("#lista_tr").empty();
                $("#listaMenu").empty();

                getCurso($("#lective_year").val());

                document.getElementById('generate-pdf').style.visibility = 'hidden';
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
                    document.getElementById('generate-pdf').style.visibility = 'hidden';

                    if (data == 500) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        alert(
                            "Atenção! este curso não está associada a nenhuma turma no ano lectivo selecionado, verifique a edição de plano de estudo do mesma."
                            );
                    }

                    if (data['data'].length > 0) {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        Turma_id_Select.empty();
                        Turma_id_Select.append(
                            '<option selected="" value="0">Selecione a turma</option>');

                        Disciplina_Select.prop('disabled', true);
                        $.each(data['data'], function(indexInArray, row) {
                            Turma_id_Select.append('<option value="' + row.id + ' ,' + row
                                .year + '">' + row.display_name + '</option>');
                        });

                        Turma_id_Select.prop('disabled', false);
                        Turma_id_Select.selectpicker('refresh');
                    }
                });
            })

            Turma_id_Select.bind('change keypress', function() {
                turma_nome = $("#Turma_id_Select")[0].selectedOptions[0].text;
                document.getElementById('generate-pdf').style.visibility = 'hidden';

                getDiscipline()
            })

            function getDiscipline() {
                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var arrayCurso = vetorCurso.split(re);

                var vetorTurma = Turma_id_Select.val();
                var reTurma = /\s*,\s*/;
                var anoCursoturma = vetorTurma.split(reTurma);

                $.ajax({
                    url: "/avaliations/getDiscipline/" + id_anoLectivo.val() + "/" + anoCursoturma[1] +
                        "/" + arrayCurso[0],
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    $("#lista_tr").empty();
                    $("#listaMenu").empty();

                    Disciplina_Select.empty();
                    Disciplina_Select.append('<option selected="" value="0">Selecione a disciplina</option>');

                    if (data['data'].length > 0) {
                        $.each(data['data'], function(indexInArray, row) {
                            Disciplina_Select.append('<option value="' + row.id_disciplina + ',' +
                                row.periodo_disciplina + '">' + row.code + '  ' + row
                                .dt_display_name + '</option>');
                        });
                    }

                    Disciplina_Select.prop('disabled', false);
                    Disciplina_Select.selectpicker('refresh');
                });
            }

            Disciplina_Select.bind('change keypress', function() {
                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var id_curso = vetorCurso.split(re);

                var vetorDisciplina = Disciplina_Select.val();
                var vetor = /\s*,\s*/;
                var id_disciplinaVetor = vetorDisciplina.split(vetor);

                disciplina_nome = $("#Disciplina_id_Select")[0].selectedOptions[0].text;

                if (vetorDisciplina != 0) {
                    $("#lista_tr").empty();
                    document.getElementById('generate-pdf').style.visibility = 'hidden';
                    getStudentNotasPautaFinal()
                } else {
                    $("#listaMenu").empty();
                    $("#listaMenu").append(
                        "<center>Selecione uma disciplina para que se possa gerar uma pauta.</center>");
                    $("#lista_tr").empty();
                    document.getElementById('generate-pdf').style.visibility = 'hidden';
                }
            })

            function getStudentNotasPautaFinal() {
                var vetorCurso = curso.val();
                var re = /\s*,\s*/;
                var id_curso = vetorCurso.split(re);

                var vetorDisciplina = Disciplina_Select.val();
                var vetor = /\s*,\s*/;
                var id_disciplinaVetor = vetorDisciplina.split(vetor);

                $.ajax({
                    url: "/avaliations/getStudentNotasPautaFinal/" + id_anoLectivo.val() + "/" + id_curso[0] + "/" + Turma_id_Select.val() + "/" + id_disciplinaVetor[0] + "/" + 30 +"/" + 0,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    var dados_pauta = [];
                    var pdf1_val = 0;
                    var pdf2_val = 0;
                    var ao_val = 0;
                    var tabelatr = "";
                    var mensagem_erro_propina = null;
                    var propina_nome = "";
                    var exame_nome = "";
                    var a = 0;
                    var erro = 0;

                    var selector_pauta = $("#selector_pauta").parent().find('input').is(':checked');

                    // VERIFICA SE A PAUTA ESTÁ PUBLICADA OU NÃO
                    if (data['data']['estado_pauta'] == 1 && data['data']['estado_tipo'] == 30) {

                        // MOSTRA OS ELEMENTOS DA PÁGINA
                        $("#generate-pdf").show();
                        $("#pauta_disciplina").show();

                        // VERIFICA SE TODAS AS AVALIAÇÕES FORAM LANÇADAS
                        disciplina_regime = data['data']['periodo_disc'][0].value_disc;

                        //GERADOR NO MENU PAUTA
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        tabelatr += "<th>#</th>"
                        tabelatr += "<th>MATRÍCULA</th>"
                        tabelatr += "<th>ESTUDANTE</th>"
                        tabelatr += "<th>PF1</th>"
                        tabelatr += "<th>PF2</th>"
                        tabelatr += "<th>OA</th>"
                        tabelatr += "<th>MAC</th>"
                        tabelatr += "<th colspan='2'>EXAME</th>"
                        tabelatr += "<th colspan='2'>CLASSIFICAÇÃO FINAL</th>"
                        // tabelatr+="<th>Observações</th>"
                        $("#listaMenu").append(tabelatr);

                        var numero_alunos = 0;
                        var numero_alunos_n = 0;
                        var pf1_percentagem = 0.33;
                        var pf2_percentagem = 0.33;
                        var oa_percentagem = 0.34;
                        var mac_percentagem = 0.60;
                        var neen_percentagem = 0.40;
                        var lista_alunos_notas = [];
                        var estatistica_pauta = [];

                        var tabelatr = "";
                        var resultados_student = data['data']['dados'];
                        var aluno_nome = "";
                        var alunos_aprovados = 0;
                        var alunos_reprovados = 0;
                        var alunos_exame = 0;
                        var alunos_recurso = 0;
                        var alunos_masculino = 0;
                        var alunos_femenino = 0;
                        var alunos_masculino_reprovado = 0;
                        var alunos_femenino_reprovado = 0;

                        $("#lista_tr").empty();
                        // INFORMA O TIPO DE PAUTA A SER SALVA
                        $("#pauta_code").val(30);
                        $("#tipo_pauta").val("Pauta Final");

                        //Estrutura de repitição que lista os alunos
                        $.each(resultados_student, function(index, item) {
                            pf1 = 0;
                            pf2 = 0;
                            oa = 0;
                            neen = -1;
                            pf1_nome = "";
                            pf2_nome = "";
                            oa_nome = "";
                            neen_nome = ""
                            exame_pauta = 0;
                            j = 0;
                            calculo_mac = 0;

                            oral_count = 0;
                            oral_nota = 0;
                            oral_name = "-";
                            oral_percentagem = 0                            

                            // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                            if (selector_pauta === false) {
                                if (a === 0) {
                                    mensagem_erro_propina = 0;
                                    a = 1;
                                }

                                $.each(item, function(index_exam, item_exam) {

                                    mensagem_erro_propina = 1;

                                    // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                                    if (item_exam.estado_do_mes === "total") {

                                        if (aluno_nome != index) {
                                            aluno_nome = index;
                                            numero_alunos_n += 1;

                                            erro = 1;

                                            tabelatr += "<tr><td style='text-align: center'>" +numero_alunos_n + "</td>"
                                            tabelatr += "<td style='text-align: center'>" +item_exam.code_matricula + "</td>"
                                            tabelatr += "<td style='text-align: left'>" +index + "</td>"

                                            // HABILITA O BOTÃO DE SALVAR                                                    
                                            document.getElementById('generate-pdf').style.visibility = 'visible';

                                            $.each(item, function(index_avaliacao, item_avaliacao) {

                                                // Estrura de repetição que pega as metricas PF1.
                                                if (item_avaliacao.MT_CodeDV === "PF1") {
                                                    if (pf1_nome != item_avaliacao.MT_CodeDV) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr += "<td style='text-align: center'>" +'F' + "</td>"
                                                            pf1_nome = "PF1";
                                                        } else {
                                                            pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                            pf1 = item_avaliacao.nota_anluno;

                                                            tabelatr += "<td style='text-align: center'>" + item_avaliacao.nota_anluno +"</td>"
                                                            pf1_nome = "PF1";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas PF2.
                                                if (item_avaliacao.MT_CodeDV === "PF2") {
                                                    if (pf2_nome != item_avaliacao.MT_CodeDV) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr += "<td style='text-align: center'>" + 'F' + "</td>"
                                                            pf2_nome = "PF2";
                                                        } else {
                                                            pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                            pf2 = item_avaliacao.nota_anluno;
                                                            tabelatr += "<td style='text-align: center'>" +item_avaliacao.nota_anluno +"</td>"
                                                            pf2_nome = "PF2";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.MT_CodeDV === "OA") {
                                                    if (oa_nome != item_avaliacao.MT_CodeDV) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr += "<td style='text-align: center'>" +'F' + "</td>"
                                                            oa_nome = "OA";
                                                        } else {
                                                            oa_percentagem = item_avaliacao.percentagem_metrica /100;
                                                            oa = item_avaliacao.nota_anluno;
                                                            tabelatr += "<td style='text-align: center'>" + item_avaliacao.nota_anluno +"</td>"
                                                            oa_nome = "OA";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.MT_CodeDV === "Escrita") {
                                                    if (oa_nome != item_avaliacao.MT_CodeDV) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            neen = -1;
                                                            neen_nome = "Neen";
                                                        } else {
                                                            neen = item_avaliacao.nota_anluno;
                                                            neen_nome = "Neen";
                                                        }
                                                    }
                                                }

                                                if(item_avaliacao.MT_CodeDV == "oral"){
                                                    if (oral_name != item_avaliacao.MT_CodeDV) {
                                            if(item_avaliacao.nota_anluno === null) {
                                                oral_nota = 0;
                                                oral_name = "F";
                                                oral_percentagem = 0;
                                            }else{
                                                oral_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                oral_nota = item_avaliacao.nota_anluno;
                                                oral_name = oral_nota;
                                            }
                                        }
                                        j += 1;
                                        oral_count = 1;
                                    }

                                            });

                                            // O ESTUDANTE NÃO TÊM NOTAS LANÇADAS
                                            if (j == 0) {
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                            }
                                            // O ESTUDANTE SÓ TÊM UMA NOTA LANÇADA
                                            if (j == 1) {
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                            }
                                            // O ESTUDANTE SÓ TÊM DUAS NOTAS LANÇADAS
                                            if (j == 2) {
                                                tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                            }

                                            // O ESTUDANTE TÊM TODAS AS NOTAS LANÇADAS
                                            if (j >= 2) {
                                                //Calculo da MAC
                                                calculo_mac = macCalculate(data['data']['avaliacao_config'],pf1,pf1_percentagem,pf2,pf2_percentagem,oa,oa_percentagem);
                                                //Verifica se a nota do NEEN é superior a -1
                                                if(oral_nota == oral_name){
                                                    exame_pauta = mfCalculate(data['data']['avaliacao_config'],calculo_mac,oral_nota,oral_percentagem);
                                                }else{
                                                    if (neen == -1) {
                                                        exame_pauta = examePautaCalculate(data['data']['avaliacao_config'],calculo_mac,mac_percentagem,neen_percentagem,0);
                                                    } else {
                                                        exame_pauta = examePautaCalculate(data['data']['avaliacao_config'],calculo_mac,mac_percentagem,neen_percentagem,neen);
                                                    }
                                                }
                                            
                                                if(calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa){
                                                    oral_name = calculo_mac;
                                                    exame_pauta = calculo_mac;
                                                    exame_nome = "Aprovado(a)";
                                                }else{
                                                    exame_nome = exame_pauta >= data['data']['avaliacao_config'].exame_nota ? "Aprovado(a)" : "Recurso";
                                                }                                            

                                                tabelatr +="<td style='text-align: center'>" + calculo_mac.toFixed(2) + "</td>"


                                                if (data['data']['exame'].has_mandatory_exam == 1) {
                                                    //Mostra a nota do NEEN 
                                                    if (neen == -1) { 
                                                        tabelatr +="<td style='text-align: center'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                    } else {
                                                        tabelatr +="<td style='text-align: center'>" +neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                    }

                                                    tabelatr += "<td class='c_final' style='text-align: right'>" + Math.round(exame_pauta) + "</td>"
                                                    if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                        tabelatr +="<td class='c_final' style='text-align: center'>" +exame_nome+ "</td>"
                                                        // CONTA ALUNOS APROVADOS
                                                        alunos_aprovados += 1;
                                                    } else {
                                                        tabelatr +="<td class='c_final' style='text-align: center'>" +exame_nome+ "</td>"
                                                        // CONTA ALUNOS REPROVADOS
                                                        alunos_recurso += 1;
                                                    }
                                                } else {
                                                    //
                                                    if (calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa) {
                                                        //Mostra a nota do NEEN 
                                                        tabelatr +="<td style='text-align: center'>-</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                        tabelatr += "<td class='c_final' style='text-align: right'>" +calculo_mac+ "</td>"
                                                        tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                        // CONTA ALUNOS APROVADOS
                                                        alunos_aprovados += 1;
                                                    } else {
                                                        if (calculo_mac >= data['data']['avaliacao_config'].exame_nota_inicial && calculo_mac < data['data']['avaliacao_config'].exame_nota_final ) {
                                                            //Mostra a nota do NEEN 
                                                            if (neen == -1) {
                                                                tabelatr +="<td style='text-align: center'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            } else {
                                                                tabelatr +="<td style='text-align: center'>" + neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            }
                                                            tabelatr +="<td class='c_final' style='text-align: right'>" +Math.round(exame_pauta) +"</td>"
                                                            if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" +exame_nome + "</td>"
                                                                // CONTA ALUNOS APROVADOS
                                                                alunos_aprovados += 1;
                                                            } else {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                                // CONTA ALUNOS REPROVADOS
                                                                alunos_recurso += 1;
                                                            }
                                                        } else {
                                                            //Mostra a nota do NEEN 
                                                            if (neen == -1) {
                                                                tabelatr +="<td style='text-align: center'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            } else {
                                                                tabelatr += "<td style='text-align: center'>" +neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            }

                                                            tabelatr += "<td class='c_final' style='text-align: right'>" + Math.round(exame_pauta) + "</td>"
                                                            if (Math.round(exame_pauta) >= data['data']['avaliacao_config'].exame_nota) {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" +'Aprovado(a)' + "</td>"
                                                                // CONTA ALUNOS APROVADOS
                                                                alunos_aprovados += 1;
                                                            } else {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" +'Recurso' + "</td>"
                                                                // CONTA ALUNOS REPROVADOS
                                                                alunos_recurso += 1;
                                                            }

                                                        }
                                                    }
                                                }

                                                // LISTA DE ALUNOS E SUA MÉDIAS PARA SEREM TRANÇADOS
                                                if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                    lista_alunos_notas.push([
                                                        item_exam.id_mat, 
                                                        item_exam.user_id, 
                                                        Math.round(calculo_mac), 
                                                        item_exam.sexo, 
                                                        "@"
                                                    ])
                                                    // alunos_aprovados += 1;
                                                    if (item_exam.sexo == "Feminino") {
                                                        alunos_femenino += 1;
                                                    }
                                                    if (item_exam.sexo == "Masculino") {
                                                        alunos_masculino += 1;
                                                    }

                                                } else {
                                                    lista_alunos_notas.push([
                                                        item_exam.id_mat, 
                                                        item_exam.user_id, 
                                                        Math.round(exame_pauta), 
                                                        item_exam.sexo, 
                                                        "@"
                                                    ])
                                                    // alunos_recurso += 1;

                                                    if (item_exam.sexo == "Feminino") {
                                                        alunos_femenino_reprovado += 1;
                                                    }
                                                    if (item_exam.sexo == "Masculino") {
                                                        alunos_masculino_reprovado += 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                                numero_alunos += 1;
                                console.log("not")
                            } // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                            else {
                                // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                                if (a === 0) {
                                    mensagem_erro_propina = 0;
                                    a = 1;
                                }

                                $.each(item, function(index_exam, item_exam) {

                                    mensagem_erro_propina = 1;

                                    // MOSTRA OS ESTUDANTES COM BASE NA PROPINA PAGA
                                    // VERIFICA SE A PROPINA ESTÁ PAGA
                                    if (item_exam.estado_do_mes === "pending") {

                                        if (aluno_nome != index) {
                                            aluno_nome = index;
                                            numero_alunos_n += 1;

                                            erro = 1;

                                            tabelatr += "<tr><td style='text-align: center'>" + numero_alunos_n + "</td>"
                                            tabelatr += "<td style='text-align: center'>" + item_exam.code_matricula + "</td>"
                                            tabelatr += "<td  style='text-align: left'>" + index + "</td>"

                                            // HABILITA O BOTÃO DE SALVAR                                                    
                                            document.getElementById('generate-pdf').style.visibility = 'visible';

                                            $.each(item, function(index_avaliacao, item_avaliacao) {

                                                // Estrura de repetição que pega as metricas PF1.
                                                if (item_avaliacao.Metrica_nome === "PF1") {
                                                    if (pf1_nome != item_avaliacao.Metrica_nome) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr +="<td style='text-align: center'>" +'F' + "</td>"
                                                            pf1_nome = "PF1";
                                                        } else {
                                                            pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                            pf1 = item_avaliacao.nota_anluno;
                                                            tabelatr += "<td style='text-align: center'>" +item_avaliacao.nota_anluno +"</td>"
                                                            pf1_nome = "PF1";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas PF2.
                                                if (item_avaliacao.Metrica_nome ===  "PF2") {
                                                    if (pf2_nome != item_avaliacao .Metrica_nome) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr +="<td style='text-align: center'>" +'F' + "</td>"
                                                            pf2_nome = "PF2";
                                                        } else {
                                                            pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                            pf2 = item_avaliacao.nota_anluno;
                                                            tabelatr +="<td style='text-align: center'>" +item_avaliacao.nota_anluno +"</td>"
                                                            pf2_nome = "PF2";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome ==="OA") {
                                                    if (oa_nome != item_avaliacao.Metrica_nome) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            tabelatr += "<td style='text-align: center'>" +'F' + "</td>"
                                                            oa_nome = "OA";
                                                        } else {
                                                            oa_percentagem = item_avaliacao.percentagem_metrica /100;
                                                            oa = item_avaliacao.nota_anluno;
                                                            tabelatr += "<td style='text-align: center'>" + item_avaliacao.nota_anluno +"</td>"
                                                            oa_nome = "OA";
                                                        }
                                                    }
                                                    j += 1;
                                                }

                                                // Estrura de repetição que pega as metricas OA..
                                                if (item_avaliacao.Metrica_nome === "Escrita") {
                                                    if (oa_nome != item_avaliacao.Metrica_nome) {
                                                        if (item_avaliacao.nota_anluno === null) {
                                                            // tabelatr+="<td>"+'F'+"</td>"
                                                            neen = -1;
                                                            neen_nome = "Neen";
                                                        } else {
                                                            neen = item_avaliacao.nota_anluno;
                                                            neen_nome = "Neen";
                                                        }
                                                    }
                                                }
                                            });

                                            // O ESTUDANTE NÃO TÊM NOTAS LANÇADAS
                                            if (j == 0) {
                                                tabelatr +="<td style='text-align: center'>" + '-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" + '-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" +'-' + "</td>"
                                            }
                                            // O ESTUDANTE SÓ TÊM UMA NOTA LANÇADA
                                            if (j == 1) {
                                                tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                                tabelatr +="<td style='text-align: center'>" + '-' + "</td>"
                                            }
                                            // O ESTUDANTE SÓ TÊM DUAS NOTAS LANÇADAS
                                            if (j == 2) {
                                                tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                            }

                                            //Fim das avaliações PF1, PF2, OA
                                            if (j >= 2) {
                                                //Calculo da MAC
                                                calculo_mac = macCalculate(data['data']['avaliacao_config'],pf1,pf1_percentagem,pf2,pf2_percentagem,oa,oa_percentagem);
                                                //Verifica se a nota do NEEN é superior a -1
                                                if(oral_nota == oral_name){
                                                    exame_pauta = mfCalculate(data['data']['avaliacao_config'],calculo_mac,oral_nota);
                                                }else{
                                                    if (neen == -1) {
                                                        exame_pauta = examePautaCalculate(data['data']['avaliacao_config'],calculo_mac,mac_percentagem,neen_percentagem,0);
                                                    } else {
                                                        exame_pauta = examePautaCalculate(data['data']['avaliacao_config'],calculo_mac,mac_percentagem,neen_percentagem,neen);
                                                    }
                                                }
                                                
                                                if(calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa){
                                                    oral_name = calculo_mac;
                                                    exame_pauta = calculo_mac;
                                                    exame_nome = "Aprovado(a)";
                                                }else{
                                                    exame_nome = exame_pauta >= data['data']['avaliacao_config'].exame_nota ? "Aprovado(a)" : "Recurso";
                                                }                                                  
                                                
                                                tabelatr += "<td style='text-align: center'>" + calculo_mac.toFixed(2) + "</td>"


                                                if (data['data']['exame'].has_mandatory_exam == 1) {
                                                    //Mostra a nota do NEEN 
                                                    if (neen == -1) {
                                                        tabelatr +="<td style='text-align: center'> F1 </td>"
                                                    } else {
                                                        tabelatr += "<td style='text-align: center'>" +neen + "</td>"

                                                    }

                                                    tabelatr +="<td class='c_final' style='text-align: right'>" +exame_pauta + "</td>"

                                                    if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                        tabelatr += "<td class='c_final' style='text-align: center'>" +exame_nome + "</td>"
                                                        // CONTA ALUNOS APROVADOS
                                                        alunos_aprovados += 1;
                                                    } else {
                                                        tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                        // CONTA ALUNOS REPROVADOS
                                                        alunos_recurso += 1;
                                                    }
                                                } else {
                                                    //
                                                    if (calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa) {
                                                        //Mostra a nota do NEEN 
                                                        tabelatr += "<td style='text-align: center'> </td>"
                                                        tabelatr += "<td class='c_final' style='text-align: right'>" +calculo_mac +"</td>"

                                                        tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                        // CONTA ALUNOS APROVADOS
                                                        alunos_aprovados += 1;

                                                    } else {
                                                        if (calculo_mac >= data['data']['avaliacao_config'].exame_nota_inicial && calculo_mac < data['data']['avaliacao_config'].exame_nota_final) {
                                                            //Mostra a nota do NEEN 
                                                            if (neen == -1) {
                                                                tabelatr += "<td style='text-align: center'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            } else {
                                                                tabelatr += "<td style='text-align: center'>" + neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            }
                                                            tabelatr +="<td class='c_final' style='text-align: right'>" + exame_pauta +"</td>"
                                                            if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                                tabelatr +="<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                                // CONTA ALUNOS APROVADOS
                                                                alunos_aprovados += 1;
                                                            } else {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                                // CONTA ALUNOS REPROVADOS
                                                                alunos_recurso += 1;
                                                            }
                                                        } else {
                                                            //Mostra a nota do NEEN 
                                                            if (neen == -1) {
                                                                tabelatr +="<td style='text-align: center'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            } else {
                                                                tabelatr += "<td style='text-align: center'>" + neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                                            }

                                                            tabelatr += "<td class='c_final' style='text-align: right'>" +Math.round(exame_pauta) +"</td>"

                                                            if (Math.round(exame_pauta) >= data['data']['avaliacao_config'].exame_nota) {
                                                                tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                                // CONTA ALUNOS APROVADOS
                                                                alunos_aprovados += 1;
                                                            } else {
                                                                tabelatr +="<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                                // CONTA ALUNOS REPROVADOS
                                                                alunos_recurso += 1;
                                                            }

                                                        }
                                                    }
                                                }

                                                // LISTA DE ALUNOS E SUA MÉDIAS PARA SEREM TRANÇADOS
                                                if (Math.round(exame_pauta) >= 10) {
                                                    lista_alunos_notas.push([
                                                        item_exam.id_mat, 
                                                        item_exam.user_id, 
                                                        Math.round(calculo_mac), 
                                                        item_exam.sexo, 
                                                        "@"
                                                    ])
                                                    // alunos_aprovados += 1;
                                                    if (item_exam.sexo == "Feminino") {
                                                        alunos_femenino += 1;
                                                    }
                                                    if (item_exam.sexo == "Masculino") {
                                                        alunos_masculino += 1;
                                                    }

                                                } else {
                                                    lista_alunos_notas.push([item_exam
                                                        .id_mat, item_exam
                                                        .user_id, Math.round(
                                                            exame_pauta), item_exam
                                                        .sexo, "@"
                                                    ])
                                                    // alunos_recurso += 1;

                                                    if (item_exam.sexo == "Feminino") {
                                                        alunos_femenino_reprovado += 1;
                                                    }
                                                    if (item_exam.sexo == "Masculino") {
                                                        alunos_masculino_reprovado += 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                                numero_alunos += 1;
                                console.log("yes")
                            }
                        });
                        if (mensagem_erro_propina === 0) {
                            $("#listaMenu").empty();
                            $("#listaMenu").append(
                                "<center>Infelizmente não há estudantes com o emolumento de propina pago, por favor verifica o seletor de escolha.</center>"
                                );
                        }
                        if (mensagem_erro_propina === 2 || erro === 0) {
                            $("#listaMenu").empty();
                            $("#listaMenu").append(
                                "<center>Felizmente não há estudantes com o emolumento de propina não pago, por favor verifica o seletor de escolha para visualizar os estudantes com pagamento realizados.</center>"
                            );
                        }
                        //Tag que fecha a tabela
                        tabelatr += "</tr>"
                        $("#lista_tr").append(tabelatr);

                        estatistica_pauta.push([numero_alunos_n, alunos_aprovados, alunos_recurso,
                            alunos_femenino, alunos_masculino, alunos_femenino_reprovado,
                            alunos_masculino_reprovado
                        ]);
                        console.log(estatistica_pauta, lista_alunos_notas);

                        $("#pauta_estatistica").val(estatistica_pauta);
                        $("#pauta_dados").val(lista_alunos_notas);

                        $("#id_anoLectivo").val(id_anoLectivo.val());
                        $("#data_html").val($("#pauta_disciplina").html());
                        $("#curso_id").val(id_curso[0]);
                        $("#id_disciplina").val(id_disciplinaVetor[0]);
                        $("#id_turma").val(Turma_id_Select.val());
                        $("#pauta_code").val(30);

                        $("#generate-pdf").click(function() {
                            $("#printForm").submit();
                        });
                    } else {
                        $("#lista_tr").empty();
                        $("#listaMenu").empty();
                        tabelatr +=
                            "<td class='text-center'>A pauta ainda não foi publicada, em caso de duvidas contactar um superior.</td>"
                        $("#listaMenu").append(tabelatr);

                        // alert('Infelizmente a pauta ainda não foi publicada.');

                        // OUCULTA OS ELEMENTOS DA PÁGINA
                        $("#generate-pdf").hide();
                        // $("#pauta_disciplina").hide();
                    }
                });
            }
        })
    </script>
@endsection
