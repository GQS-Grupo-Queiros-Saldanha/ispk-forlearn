<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PUBLICAR EXAME ESCRITO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Publicar Exame Escrito</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
    <style>
    .devedor {
    background-color: red;
    color: white;
}
</style>
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
    {!! Form::open(['route' => ['publisher_final_grade'], 'id' => 'publishForm']) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
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
        <div class="col-6 p-2">
            <label>Selecione a turma</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="i_turma"
                tabindex="-98"></select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a disciplina</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="i_disciplina"
                tabindex="-98"></select>
        </div>
    </div>

    <input type="hidden" value="0" id="verificarSelector">
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value="">
    {{-- {{Dados}} --}}
    <input type="hidden" id="data_html" name="data_html" value="">
    {{-- {{Dados Curso}} --}}
    <input type="hidden" id="curso_id" name="curso_id" value="">
    {{-- {{Dados Turma}} --}}
    <input type="hidden" id="id_turma" name="id_turma" value="">
    {{-- {{Dados Disciplina}} --}}
    <input type="hidden" id="id_disciplina" name="id_disciplina" value="">
    {{-- {{Dados Pauta Code}} --}}
    <input type="hidden" id="pauta_code" name="pauta_code" value="">
    <input type="hidden" id="pauta_dados" name="pauta_dados" value="">
    <input type="hidden" id="pauta_estatistica" name="pauta_estatistica" value="">
    <hr>

    {{-- INCLUI A LISTA DE NOTAS --}}
    <div class="card mr-1" id="pauta_disciplina">
        <h4 id="titulo_semestre"></h4>
        <table class="table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr id="listaMenu" style="text-align: center"> </tr>
            </thead>
            <tbody id="lista_tr"></tbody>
        </table>
    </div>
    <div class="card" style="float: right;">
        <button type="button" class="btn btn-success" style="left: 10.5em;" id="togglee">
            <i class="fas fa-lock" id="icone_publish"></i>
            Publicar pauta
        </button>
    </div>
    {!! Form::close() !!}
@endsection
@section('models')
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
                        <p class="text-danger" style="font-weight:bold; !important"> Caro Coordenador(a)
                            ({{ auth()->user()->name }}), a acção de <label id="acaoID" class="text-danger"
                                style="font-weight:bold; !important"></label> pauta é de sua inteira responsabilidade
                            <br>
                        </p>
                    </div>
                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important" id="idTExto"></p>
                        <ul>
                            <li style="padding:5px; !important" id="text1">
                            </li>
                            <li style="padding:5px; !important" id="text2">
                            </li>

                            <li style="padding:5px; !important" id="text3">
                            </li>
                        </ul>
                    </div>
                    <div style="margin-top:10px; !important" id="confirmMessage">
                        <p>No caso de <span class="text-success"><b>HAVER</b></span> alguma das situações acima
                            assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.</p>
                        <p>No caso de <span class="text-danger"><b>NÃO HAVER</b></span> nenhuma situação acima, por favor
                            seleccione: Tenho a certeza.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores
                        forLEARN</button>
                    <nav id="ocultar_btn">
                        <button type="button" class="btn btn-success" id="btn-PublishSubmit">Tenho a certeza</button>
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

            document.getElementById('togglee').style.visibility = 'hidden';
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

            $("#lective_year").change(function() {
                id_anoLectivo = $('#lective_year');
                getCurso(id_anoLectivo);
                document.getElementById('togglee').style.visibility = 'hidden';
            });

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
                    document.getElementById('togglee').style.visibility = 'hidden';
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
                document.getElementById('togglee').style.visibility = 'hidden';
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
                    Disciplina_Select.append(
                        '<option selected="" value="0">Selecione a disciplina</option>');

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

                // DADOS CURSO                
                $("#curso_id").val(id_curso[0]);
                $("#id_disciplina").val(id_disciplinaVetor[0]);
                $("#id_turma").val(Turma_id_Select.val());

                document.getElementById('togglee').style.visibility = 'hidden';

                if (vetorDisciplina != 0) {
                    getStudentNotasPautaFinal()
                } else {
                    $("#listaMenu").empty();
                    $("#lista_tr").empty();
                    $("#listaMenu").append(
                        "<center>Selecione uma disciplina para que se possa gerar uma pauta.</center>");
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
                    url: "/avaliations/getStudentMatriculation/" + id_anoLectivo.val() + "/" + id_curso[0] + "/" + Turma_id_Select.val() + "/" + id_disciplinaVetor[0] + "/" + 5 +"/"+20,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    //Chamar o modal de confirmação
                    $("#togglee").click(function() {
                        $("#exampleModal").modal('show');
                    });

                    $("#btn-PublishSubmit").click(function() {
                        $("#id_anoLectivo").val(id_anoLectivo.val());
                        $("#publishForm").submit();
                    });

                    var resultados_student = data['data']['dados'];
                    var devedores = data['data']['devedores'];

                    if (resultados_student.length != 0) {
                        //GERADOR NO MENU PAUTA
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        tabelatr += "<th>#</th>"
                        tabelatr += "<th>MATRÍCULA</th>"
                        tabelatr += "<th>ESTUDANTE</th>"
                        tabelatr += "<th>CLASSIFICAÇÃO DO EXAME ESCRITO</th>"
                        $("#listaMenu").append(tabelatr);
                    } else {
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        $("#listaMenu").append("<center>Selecione uma disciplina que tenha notas lançadas.</center>");
                    }
                    // Mostra os botões
                    if (data['data']['estado_pauta'] == 1 && data['data']['estado_tipo'] == 20) {
                        $("#togglee").text("Desbloquear pauta");

                        //no modal de alerta de publicação de notas
                        $("#acaoID").text("Desbloquear");
                        $("#idTExto").text(
                            "Após desbloquear a pauta, algumas acções podem ser realizadas, nomeadamente:"
                        );
                        $("#text1").text("As notas poderão ser EDITADAS");
                        $("#text2").text("A pauta gerada anteriormente será DESCARTADA");
                        $("#text3").text("A pauta deixará de estar DISPONÍVEL");
                        $("#confirmMessage").hide();
                        //fim modal

                        $("#togglee").removeClass("btn-success");
                        $("#togglee").addClass("btn-warning text-dark");

                        $("#icone_publish").removeClass("fas fa-lock");
                        $("#icone_publish").addClass("fas fa-unlock");
                    } else if (data['data']['estado_pauta'] == 0 && data['data']['estado_tipo'] == 20) {
                        $("#togglee").text("");
                        $("#togglee").text("Publicar pauta");

                        //no modal de alerta de publicação de notas
                        $("#acaoID").text("Publicar");

                        $("#idTExto").text(
                            "Verifique se os dados da pauta estão correctos, nomeadamente: ");
                        $("#text1").text("Todos os alunos pertencem a esta TURMA?");
                        $("#text2").text("Falta algum aluno nesta TURMA?");
                        $("#text3").text("Há alguma anomalia nos cálculos das NOTAS?");
                        $("#confirmMessage").show();
                        //fim modal

                        $("#icone_publish").removeClass("fas fa-unlock ");
                        $("#icone_publish").addClass("fas fa-lock");

                        $("#togglee").addClass("btn-success");
                        $("#togglee").removeClass(" btn-warning");
                    } else {
                        $("#acaoID").text("Publicar");
                        $("#idTExto").text(
                            "Verifique se os dados da pauta estão correctos, nomeadamente: ");
                        $("#text1").text("Todos os alunos pertencem a esta TURMA?");
                        $("#text2").text("Falta algum aluno nesta TURMA?");
                        $("#text3").text("Há alguma anomalia nos cálculos das NOTAS?");
                    }

                    var numero_alunos = 0;
                    var neen_percentagem = 0.4;
                    var lista_alunos_notas = [];
                    var estatistica_pauta = [];

                    var tabelatr = "";
                    var aluno_nome = "";
                    var alunos_aprovados = 0;
                    var alunos_reprovados = 0;
                    var alunos_exame = 0;
                    var alunos_recurso = 0;
                    var alunos_masculino = 0;
                    var alunos_femenino = 0;
                    var alunos_masculino_reprovado = 0;
                    var alunos_femenino_reprovado = 0;
                    

                    //GERA A LISTA DE ESTUDANTES E SUAS NOTAS
                    $("#lista_tr").empty();
                    // INFORMA O TIPO DE PAUTA A SER SALVA
                    $("#pauta_code").val(20);
                    $("#tipo_pauta").val("Pauta Exame");

                    //Estrutura de repitição que lista os alunos
                    $.each(resultados_student, function(index, item) {

                        numero_alunos += 1;
                        neen_nome = "";
                        exame_pauta = 0;

                        $.each(item, function(index_exam, item_exam) {
                            if (aluno_nome != index) {

                                aluno_nome = index;
                                
                                tabelatr += "<tr id='user_"+ item_exam.user_id + "' class='oi'><td style='text-align: center'>" + numero_alunos + "</td>"
                                tabelatr += "<td style='text-align: center'>" + item_exam.code_matricula + "</td>"
                                tabelatr += "<td style='text-align: left'>" + index +"</td>"

                              

                               
                                  
               

                                // HABILITA O BOTÃO DE SALVAR                                            
                                document.getElementById('togglee').style.visibility = 'visible';

                                $.each(item, function(index_avaliacao, item_avaliacao) {
                                    if (item_avaliacao.MT_CodeDV === "Neen") {
                                        if (neen_nome != item_avaliacao.MT_CodeDV) {
                                            if (item_avaliacao.nota_anluno === null) {
                                                neen = -1;
                                                neen_nome = "Neen";
                                            } else {
                                                neen = item_avaliacao.nota_anluno;
                                                neen_nome = "Neen";
                                            }
                                        }
                                    }
                                });

                                

                                //Verifica se a nota do NEEN é superior a -1
                                if (neen == -1) {
                                    exame_pauta = parseInt(0);
                                } else {
                                   exame_pauta =parseInt(Math.round(neen));
                                }

                                //Validação da nota do NEEN                                   
                                if (Math.round(exame_pauta) >= data['data']['avaliacao_config'].exame_nota) {
                                  
                                    if (neen == -1) {
                                        tabelatr += "<td style='text-align: right'> F </td>"
                                    
                                    } else {
                                        tabelatr += "<td style='text-align: right'>" + exame_pauta + "</td>"
                                        
                                    }
                                } else {
                                    //Mostra a nota do NEEN
                                    if (neen == -1) {
                                        tabelatr += "<td style='text-align: right'> F </td>"
                                    }
                                    else{
                                        tabelatr +="<td class='c_final' style='text-align: center'>Recurso</td>"
                                    } 
                                     
                                     
                                }

                                // LISTA DE ALUNOS E SUA MÉDIAS PARA SEREM TRANÇADOS
                                if (Math.round(exame_pauta) >=data['data']['avaliacao_config'].exame_nota) {
                                    lista_alunos_notas.push([item_exam.id_mat, item_exam.user_id, exame_pauta, item_exam.sexo, "@"])
                                    alunos_aprovados += 1;

                                    if (item_exam.sexo == "Feminino") {
                                        alunos_femenino += 1;
                                    }
                                    if (item_exam.sexo == "Masculino") {
                                        alunos_masculino += 1;
                                    }
                                } else {
                                    lista_alunos_notas.push([item_exam.id_mat, item_exam
                                        .user_id, Math.round(exame_pauta), item_exam
                                        .sexo, "@"
                                    ])
                                    alunos_recurso += 1;

                                    if (item_exam.sexo == "Feminino") {
                                        alunos_femenino_reprovado += 1;
                                    }
                                    if (item_exam.sexo == "Masculino") {
                                        alunos_masculino_reprovado += 1;
                                    }
                                }

                            }
                        });
                   
                    });
                  

                    //Tag que fecha a tabela
                    tabelatr += "</tr>"
                    $("#lista_tr").append(tabelatr);

                    estatistica_pauta.push([numero_alunos, alunos_aprovados, alunos_recurso,
                        alunos_femenino, alunos_masculino, alunos_femenino_reprovado,
                        alunos_masculino_reprovado
                    ]);

                    $("#pauta_estatistica").val(estatistica_pauta);
                    $("#pauta_dados").val(lista_alunos_notas);

                    $("#data_html").val($("#pauta_disciplina").html());                                             
                });
            }
        })
    </script>
@endsection
