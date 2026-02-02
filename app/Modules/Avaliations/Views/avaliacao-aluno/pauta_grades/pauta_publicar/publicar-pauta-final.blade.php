<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PUBLICAR CLASSIFICAÇÃO MAC+EXAME')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Publicar classificação final</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
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
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-6">
            <label>Selecione a disciplina</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_disciplina"
                tabindex="-98"></select>
        </div>
        <div class="col-6">
            <label>Selecione a turma</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="id_turma"
                tabindex="-98"> </select>
        </div>
    </div>

    <input type="hidden" value="0" id="verificarSelector"/>

    {{-- Recebe os ID das Avaliações para serem trancadas --}}
    {{-- <input type="hidden" id="dados_avalicao" name="dados_avalicao_id" value="3487"> --}}
    <input type="hidden" id="id_anoLectivo" name="id_anoLectivo" value=""/>
    {{-- {{Dados}} --}}
    <input type="hidden" id="data_html" name="data_html" value=""/>
    {{-- {{Dados Curso}} --}}
    <input type="hidden" id="curso_id" name="curso_id" value=""/>
    <input type="hidden" id="pauta_code" name="pauta_code" value=""/>
    <input type="hidden" id="pauta_dados" name="pauta_dados" value=""/>
    <input type="hidden" id="pauta_estatistica" name="pauta_estatistica" value=""/>

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

    <div class="card mb-2" style="float: right;">
        <button type="button" class="btn btn-success" id="togglee">
            <i class="fas fa-lock" id="icone_publish"></i>
            Publicar pauta
        </button>
        <p id="warning" style="color:red">Esta pauta já está publicada, Por favor contacte o coordenador!</p>

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
                                style="font-weight:bold; !important"></label> pauta é de sua inteira
                            responsabilidade
                            <br>
                        </p>
                    </div>
                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important" id="idTExto"></p>
                        <ul>
                            <li style="padding:5px; !important" id="text1"></li>
                            <li style="padding:5px; !important" id="text2"></li>
                            <li style="padding:5px; !important" id="text3"> </li>
                        </ul>
                    </div>

                    <div style="margin-top:10px; !important" id="confirmMessage">
                        <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações
                            acima assinaladas, por favor seleccione: Contactar os gestores forLEARN
                            pessoalmente.
                        </p>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação
                            acima, por favor seleccione: Tenho a certeza.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores
                        forLEARN</button>
                    <nav id="ocultar_btn">
                        <button type="button" class="btn btn-danger" id="btn-PublishSubmit">Tenho a
                            certeza</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script src="{{ asset('js/new_avalicacoes_publicar.js') }}"></script>
    <script>
        $(document).ready(function() {
            var pauta_desbloqueada = false;
            var id_anoLectivo = $("#lective_year");
            var Disciplina_Select = $("#Disciplina_id_Select")
            var Turma_id_Select = $("#Turma_id_Select")

            $("#id_anoLectivo").val(id_anoLectivo.val());

            var disciplina_id;
            var course_id = 0;
            var verctorDiscipline = "";
            var curso_nome;
            var whoIs = "{{ $whoIs }}"
            console.log(whoIs)
            document.getElementById('togglee').style.visibility = 'hidden';
            document.getElementById('warning').style.visibility = 'hidden';
            discipline_get_new(id_anoLectivo)


            $("#lective_year").change(function() {
                id_anoLectivo = $('#lective_year');
                discipline_get_new(id_anoLectivo);

                document.getElementById('togglee').style.visibility = 'hidden';
                document.getElementById('warning').style.visibility = 'hidden';
            });

            function discipline_get_new(anolectivo) {
                $.ajax({
                    url: "/avaliations/getCursoCoordenador/" + id_anoLectivo  + "/" + whoIs,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    beforeSend: function() {}

                }).done(function(data) {
                    course_id = data['data'][0].course_id;
                    $("#lista_tr").empty();
                    $("#listaMenu").empty();

                    Disciplina_Select.empty();
                    Disciplina_Select.append('<option selected="" value="0">Selecione a disciplina</option>');

                    if (data['data'].length > 0) {
                        $.each(data['data'], function(indexInArray, row) {
                            Disciplina_Select.append('<option value="' + row.discipline_id + '" curso="'+row.course_id+'">' +
                                row.code + '  ' + row.dt_display_name + '</option>');
                        });
                    }

                    Disciplina_Select.prop('disabled', false);
                    Disciplina_Select.selectpicker('refresh');

                    Turma_id_Select.empty();
                    Turma_id_Select.prop('disabled', false);
                    // Turma_id_Select.selectpicker('refresh');
                });
            }

            //Evento de mudança na select disciplina
            Disciplina_Select.bind('change keypress', function() {
                $("#lista_tr").empty();
                $("#listaMenu").empty();
                
                var e = document.getElementById("Disciplina_id_Select");
                var cursoSelected = e.options[e.selectedIndex].getAttribute('curso');
                if(cursoSelected != null) course_id = cursoSelected;
                    
                disciplina_id = Disciplina_Select.val();
                $("#curso_id").val(course_id);
                var strUser = e.options[e.selectedIndex].text;
                verctorDiscipline = "";
                
                // PEGA OS PRIMEIROS CARACTERS DA DISCIPLINA
                verctorDiscipline = verctorDiscipline.concat(strUser[0]);
                verctorDiscipline = verctorDiscipline.concat(strUser[1]);
                verctorDiscipline = verctorDiscipline.concat(strUser[2]);
                verctorDiscipline = verctorDiscipline.concat(strUser[3]);

                document.getElementById('togglee').style.visibility = 'hidden';
                document.getElementById('warning').style.visibility = 'hidden';

                get_turmas();
            })

            //Função Que Busca as Turmas
            function get_turmas() {
                $.ajax({
                    url: "/avaliations/getTurma/" + id_anoLectivo.val() + "/" + course_id + "/" + whoIs,
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
                        Turma_id_Select.empty();

                        Turma_id_Select.append('<option selected="" value="0">Selecione a turma</option>');

                        // Disciplina_Select.prop('disabled', true);
                        $.each(data['data'], function(indexInArray, row) {

                            var verctorTurma = "";
                            // PEGA OS PRIMEIROS CARACTERS DA TURMA
                            verctorTurma = verctorTurma.concat(row.display_name[0]);
                            verctorTurma = verctorTurma.concat(row.display_name[1]);
                            verctorTurma = verctorTurma.concat(row.display_name[2]);
                            verctorTurma = verctorTurma.concat(row.display_name[3]);

                            // if (verctorTurma == verctorDiscipline) {
                            Turma_id_Select.append('<option value="' + row.id + '">' + row
                                .display_name + '</option>');
                            // }
                        });

                        Turma_id_Select.prop('disabled', false);
                        Turma_id_Select.selectpicker('refresh');
                    }
                });
            }

            //Função Que Muda de Turma
            Turma_id_Select.bind('change keypress', function() {
                $("#lista_tr").empty();
                $("#listaMenu").empty();

                // Ouculta os botões
                // document.getElementById('generate-pdf').style.visibility = 'hidden';                    

                var vetorTurma = Turma_id_Select.val();

                if (vetorTurma != 0) {
                    document.getElementById('togglee').style.visibility = 'hidden';
                    document.getElementById('warning').style.visibility = 'hidden';
                    getStudentNotasPautaFinal()
                } else {
                    $("#listaMenu").empty();
                    $("#lista_tr").empty();
                    $("#listaMenu").append("<center>Selecione uma turma para que se possa gerar uma pauta.</center>");
                }
            })

            function getStudentNotasPautaFinal() {
                $.ajax({
                    url: "/avaliations/getStudentNotasPautaFinal/" + id_anoLectivo.val() + "/" + course_id +
                        "/" + Turma_id_Select.val() + "/" + disciplina_id + "/" + 30 + "/" + 3,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    // console.log(data);

                    //Chamar o modal de confirmação
                    $("#togglee").click(function() {
                        $("#exampleModal").modal('show');
                    });

                    $("#btn-PublishSubmit").click(function() {
                        $("#id_anoLectivo").val(id_anoLectivo.val());
                        $("#publishForm").submit();
                    });

                    var pdf1_val = 0;
                    var pdf2_val = 0;
                    var ao_val = 0;
                    var resultados_student = data['data']['dados'];
                    var hide_button = false;

                    if (resultados_student.length != 0) {
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
                        tabelatr += "<th colspan='2'>CLASSIFICAÇÃO MAC + EXAME</th>"
                        $("#listaMenu").append(tabelatr);
                    } else {
                        $("#listaMenu").empty();
                        $("#lista_tr").empty();
                        $("#listaMenu").append("<center>Selecione uma disciplina que tenha notas lançadas.</center>");
                    }

                    
                    // Mostra os botões
                    if (data['data']['estado_pauta'] == 1 && data['data']['estado_tipo'] == 30 && whoIs != 'teacher') {
                        $("#togglee").text("Desbloquear pauta");
                        pauta_desbloqueada = true;
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
                    } else if (data['data']['estado_pauta'] == 0 && data['data']['estado_tipo'] == 30 && whoIs != 'teacher') {
                        pauta_desbloqueada = false;
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
                    }
                    else if(data['data']['estado_pauta']==1 && data['data']['estado_tipo'] == 30 && whoIs == 'teacher'){
                        hide_button = true;
                    }
                    else if(data['data']['estado_pauta']==0 && data['data']['estado_tipo'] == 30 && whoIs == 'teacher'){
                        hide_button = false;
                    }
                    else {
                        $("#acaoID").text("Publicar");
                        $("#idTExto").text("Verifique se os dados da pauta estão correctos, nomeadamente: ");
                        $("#text1").text("Todos os alunos pertencem a esta TURMA?");
                        $("#text2").text("Falta algum aluno nesta TURMA?");
                        $("#text3").text("Há alguma anomalia nos cálculos das NOTAS?");
                    }


                    var numero_alunos = 0;
                    var pf1_percentagem = 0.35;
                    var pf2_percentagem = 0.35;
                    var oa_percentagem = 0.30;
                    var mac_percentagem = data['data']['avaliacao_config'].percentagem_mac / 100;
                    var neen_percentagem = data['data']['avaliacao_config'].percentagem_oral / 100;
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
                    var usersMac = [];

                    //GERA A LISTA DE ESTUDANTES E SUAS NOTAS
                    $("#lista_tr").empty();
                    // INFORMA O TIPO DE PAUTA A SER SALVA
                    $("#pauta_code").val(30);
                    $("#tipo_pauta").val("Pauta Final");
                    //Estrutura de repitição que lista os alunos
                    $.each(resultados_student, function(index, item) {

                        numero_alunos += 1;
                        pf1 = 0;
                        pf2 = 0;
                        oa = 0;
                        neen = 0;
                        pf1_nome = "";
                        pf2_nome = "";
                        oa_nome = "";
                        neen_nome = ""
                        exame_pauta = 0;
                        exame_nome = "";
                        j = 0;
                        calculo_mac = 0;

                        pf1_count = 0;
                        pf2_count = 0;
                        oa_count = 0;

                        med_final = 0;

                        oral_count = 0;
                        oral_nota = 0;
                        oral_name = "";
                        oral_percentagem = 0;

                        $.each(item, function(index_exam, item_exam) {
                            if (aluno_nome != index) {
                                // console.log(item_exam)
                                aluno_nome = index;
                                tabelatr += "<tr><td style='text-align: center'>" + numero_alunos + "</td>"
                                tabelatr += "<td style='text-align: center'>" + item_exam.code_matricula + "</td>"
                                tabelatr += "<td style='text-align: left'>" + index +  "</td>"

                                // HABILITA O BOTÃO DE SALVAR                                              
                                if(hide_button)
                                    document.getElementById('warning').style.visibility = 'visible';
                                else
                                    document.getElementById('togglee').style.visibility = 'visible';

                                $.each(item, function(index_avaliacao, item_avaliacao) {
                                    // Estrura de repetição que pega as metricas PF1.
                                    if (item_avaliacao.MT_CodeDV === "PF1") {
                                        if (pf1_nome != item_avaliacao.MT_CodeDV) {
                                            if (item_avaliacao.nota_anluno === null) {
                                                pf1_nome = "PF1";
                                            } else {
                                                pf1_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                pf1 = item_avaliacao.nota_anluno;
                                                pf1_nome = "PF1";
                                            }
                                            j += 1;
                                            pf1_count = 1;
                                        }
                                    }

                                    // Estrura de repetição que pega as metricas PF2.
                                    if (item_avaliacao.MT_CodeDV === "PF2") {
                                        if (pf2_nome != item_avaliacao.MT_CodeDV) {
                                            // console.log("A:",item_avaliacao.nota_anluno)
                                            if (item_avaliacao.nota_anluno === null) {
                                                pf2_nome = "PF2";
                                            } else {
                                                pf2_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                pf2 = item_avaliacao.nota_anluno;
                                                pf2_nome = "PF2";
                                            }
                                            j += 1;
                                            pf2_count = 1;
                                        }
                                    }

                                    // Estrura de repetição que pega as metricas OA..
                                    if (item_avaliacao.MT_CodeDV === "OA") {
                                        if (oa_nome != item_avaliacao.MT_CodeDV) {
                                            if (item_avaliacao.nota_anluno === null) {
                                                oa_nome = "OA";
                                            } else {
                                                oa_percentagem = item_avaliacao.percentagem_metrica / 100;
                                                oa = item_avaliacao.nota_anluno;
                                                oa_nome = "OA";
                                            }
                                            j += 1;
                                            oa_count = 1;
                                        }

                                    }

                                    // Estrura de repetição que pega as metricas OA..
                                    if (item_avaliacao.MT_CodeDV === "Neen") {
                                        if (neen_nome != item_avaliacao.MT_CodeDV) {
                                            if (item_avaliacao.nota_anluno === null) {
                                                neen = -1;
                                                neen_nome = "Neen";
                                            } else {
                                                neen_nome = "Neen";
                                                neen = Math.ceil(item_avaliacao.nota_anluno);
                                                
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
                                                oral_nota = Math.ceil(item_avaliacao.nota_anluno);
                                                oral_name = oral_nota;
                                            }
                                        }
                                        j += 1;
                                        oral_count = 1;
                                    }

                                });

                                if (oral_count === 0) {
                                    oral_percentagem = 0;
                                    oral = 0;
                                }

                                if (pf1_count === 0) {
                                    tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                    pf1 = 0;
                                    pf1_percentagem = 0;
                                } else {
                                    tabelatr += "<td style='text-align: center'>" + pf1 + "</td>"
                                }
                                if (pf2_count === 0) {
                                    tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                    pf2 = 0;
                                    pf2_percentagem = 0;
                                } else {
                                    tabelatr += "<td style='text-align: center'>" + pf2 + "</td>"
                                }
                                if (oa_count === 0) {
                                    tabelatr += "<td style='text-align: center'>" + '-' + "</td>"
                                    oa = 0;
                                    oa_percentagem = 0;
                                } else {
                                    tabelatr += "<td style='text-align: center'>" + oa +  "</td>"
                                }
                                
                               // calculo_mac = macCalculate(data['data']['avaliacao_config'],pf1,pf1_percentagem,pf2,pf2_percentagem,oa,oa_percentagem);
                                calculo_mac = parseInt(Math.ceil(  (pf1 * pf1_percentagem) + (pf2 * pf2_percentagem) + (oa * oa_percentagem) ) );
                          

                                //Verifica se a nota do NEEN é superior a -1
                                if (neen == -1) {
                                       exame_pauta = parseInt(Math.ceil((parseFloat(calculo_mac) * mac_percentagem) + (parseFloat(0) * neen_percentagem)));
       
                                   } else {
                                        exame_pauta = parseInt(Math.ceil((parseFloat(calculo_mac) * mac_percentagem) + (parseFloat(neen) * neen_percentagem)));
                                   }
                                
                                if(calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa){
                                    oral_name = "-";
                                    exame_pauta = calculo_mac;
                                    exame_nome = "Aprovado(a)";
                                }
                                else if(calculo_mac <= data['data']['avaliacao_config'].mac_nota_recurso){
                                    exame_nome = "Recurso";
                                    exame_pauta = calculo_mac;
                                }
                                else{
                                    exame_nome = exame_pauta >= data['data']['avaliacao_config'].exame_nota ? "Aprovado(a)" : "Recurso";
                                }
                                
                                

                                // CASO A NOTA SEJA UM NaN
                                if (Number.isNaN(calculo_mac)) {
                                    tabelatr += "<td style='text-align: center'> - </td>";
                                } else {
                                    tabelatr += "<td style='text-align: center'>" + calculo_mac + "</td>";
                                }

                                if (data['data']['exame'].has_mandatory_exam == 1) {
                                    //Mostra a nota do NEEN 
                                    if (neen == -1) {
                                      
                                        tabelatr +=  "<td style='text-align: center' class='exame_note'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                    }
                                    else if (neen == 0){
                                        tabelatr +=  "<td style='text-align: center' class='exame_note'></td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                    }
                                    else {
                                        tabelatr += "<td style='text-align: center' class='exame_note'>" + neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                    }

                                    // CASO A NOTA SEJA UM NaN
                                    if (Number.isNaN(exame_pauta)) {
                                        tabelatr += "<td class='c_final med_final' style='text-align: right'> - </td>";
                                    } else {
                                       
                                        tabelatr += "<td class='c_final med_final' style='text-align: right'>" + exame_pauta + "</td>";
                                        med_final = exame_pauta
                                    }
                        
                                    if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                        tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                        alunos_aprovados += 1;
                                    } else {
                                        tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                        alunos_recurso += 1;
                                    }
                                } else {
                                    //
                                    if (calculo_mac >= data['data']['avaliacao_config'].mac_nota_dispensa) {
                                        tabelatr += "<td style='text-align: center'>-</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                        tabelatr +="<td class='c_final med_final' style='text-align: right'>" +  calculo_mac + "</td>"
                                        med_final = calculo_mac
                                        tabelatr +="<td class='c_final' style='text-align: center'>" +'Aprovado(a)' + "</td>"
                                        alunos_aprovados += 1;
                                    } 
                                    else if(calculo_mac <= data['data']['avaliacao_config'].mac_nota_recurso){
                                        tabelatr += "<td style='text-align: center'>-</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                        tabelatr +="<td class='c_final med_final' style='text-align: right'>" +  calculo_mac + "</td>"
                                        med_final = calculo_mac
                                        tabelatr +="<td class='c_final' style='text-align: center'>" +'Recurso' + "</td>"
                                        alunos_recurso += 1;
                                    }
                                    else {
                                            if (neen == -1) {
                                                tabelatr += "<td style='text-align: center' class='exame_note'> F </td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                            }
                                            else if (neen == 0){
                                                tabelatr +=  "<td style='text-align: center' class='exame_note'></td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                            }
                                            else {
                                                tabelatr +="<td style='text-align: center' class='exame_note'>" + neen + "</td>"+`<td style='text-align: center'>${oral_name}</td>`;
                                            }
                                            tabelatr +="<td class='c_final med_final' style='text-align: right'>" + exame_pauta + "</td>"

                                            med_final = exame_pauta

                                            if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                                tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                alunos_aprovados += 1;
                                            } else {
                                                tabelatr += "<td class='c_final' style='text-align: center'>" + exame_nome + "</td>"
                                                alunos_recurso += 1;
                                            }
                                      
                                    }
                                }
                                // LISTA DE ALUNOS E SUA MÉDIAS PARA SEREM TRANÇADOS
                                if (data['data']['exame'].has_mandatory_exam == 1) {
                                    if (exame_pauta >= data['data']['avaliacao_config'].exame_nota) {
                                        lista_alunos_notas.push([
                                            item_exam.id_mat, item_exam.user_id, med_final,
                                            item_exam.sexo, 
                                            "@"
                                        ])
                                        if (item_exam.sexo == "Feminino") {
                                            alunos_femenino += 1;
                                        }
                                        if (item_exam.sexo == "Masculino") {
                                            alunos_masculino += 1;
                                        }

                                    } else {
                                        lista_alunos_notas.push([item_exam.id_mat, item_exam
                                            .user_id, med_final,
                                            item_exam.sexo, "@"
                                        ])
                                        if (item_exam.sexo == "Feminino") {
                                            alunos_femenino_reprovado += 1;
                                        }
                                        if (item_exam.sexo == "Masculino") {
                                            alunos_masculino_reprovado += 1;
                                        }
                                    }
                                } else {
                                    if (calculo_mac >= data['data']['avaliacao_config'].exame_nota) {
                                        lista_alunos_notas.push([item_exam.id_mat, item_exam.user_id, med_final,
                                            item_exam.sexo, "@"
                                        ])
                                        // alunos_aprovados += 1;
                                        if (item_exam.sexo == "Feminino") {
                                            alunos_femenino += 1;
                                        }
                                        if (item_exam.sexo == "Masculino") {
                                            alunos_masculino += 1;
                                        }

                                    } else {
                                        lista_alunos_notas.push([item_exam.id_mat, item_exam
                                            .user_id, med_final,
                                            item_exam.sexo, "@"
                                        ])

                                        if (item_exam.sexo == "Feminino") {
                                            alunos_femenino_reprovado += 1;
                                        }
                                        if (item_exam.sexo == "Masculino") {
                                            alunos_masculino_reprovado += 1;
                                        }
                                    }
                                }
                            }
                            
                            if(!usersMac.includes(item_exam.user_id)){
                                tabelatr += `<input type='hidden' name='usersMac[]' value="${item_exam.user_id}-${calculo_mac}">`;
                                usersMac.push(item_exam.user_id);
                            }
                            
                        });
                        
                    });

                    estatistica_pauta.push([numero_alunos, alunos_aprovados, alunos_recurso,
                        alunos_femenino, alunos_masculino, alunos_femenino_reprovado,
                        alunos_masculino_reprovado
                    ]);

                    $("#pauta_dados").val(lista_alunos_notas);
                    $("#pauta_estatistica").val(estatistica_pauta);
                    //Tag que fecha a tabela
                    tabelatr += `</tr>`
                    $("#lista_tr").append(tabelatr);
                    $("#data_html").val($("#pauta_disciplina").html());
                    analiseToggle(data['data']['avaliacao_config'].mac_nota_dispensa, data['data']['avaliacao_config'].mac_nota_recurso);
                });
                
            }
            
            function analiseSeparater(exame_note,med_final,mac_nota_dispensa,mac_nota_recurso,x_d_s,numCase){
                let exames = []; 
                let meds = [];
                let tam = exame_note.length;
                for(let i = 0; i < tam; i++){
                    let ex = exame_note[i].innerHTML.trim()
                    let nota = parseInt(med_final[i].innerHTML.trim())
                    
                    if(numCase == 1 && ex != "F" ){
                        if(nota >= mac_nota_dispensa) meds.push(nota);
                        exames.push(ex);
                    }
                    
                    if(numCase == 2 && ex == "F"){
                        if(nota < mac_nota_recurso) meds.push(nota);
                        if(nota >= mac_nota_dispensa) meds.push(nota);
                        exames.push(ex);
                    }
                }
                return exames.length == meds.length && !x_d_s
            }
            
            function analiseToggle(mac_nota_dispensa, mac_nota_recurso){
                let togglee = document.querySelector("#togglee");
                let med_final = document.querySelectorAll(".med_final");
                let publishForm = document.querySelector("#pauta_disciplina");
                let exame_note = document.querySelectorAll(".exame_note");
                let tag = `<input type="hidden" id="x_d_s" name="x_d" value="ok"/>`
                togglee.addEventListener('mouseover',(e) => {
                    let x_d_s = document.querySelector("#x_d_s");
                    if(!pauta_desbloqueada){
                        if(exame_note.length == med_final.length){
                           if(analiseSeparater(exame_note,med_final,mac_nota_dispensa,mac_nota_recurso,x_d_s,1)){
                               publishForm.innerHTML += tag;
                           }
                           
                           if(analiseSeparater(exame_note,med_final,mac_nota_dispensa,mac_nota_recurso,x_d_s,2)){
                               publishForm.innerHTML += tag;
                           }
                        }
                    }else{
                        if(x_d_s) x_d_s.remove();
                    }
                });
                
            }
            
        })
    </script>
@endsection
