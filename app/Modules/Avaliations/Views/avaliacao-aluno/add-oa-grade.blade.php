<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LANCAR OUTRAS AVALIAÇÕES')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lançar OA'S</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
    <style>
        .red {
            background-color: red !important;
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
    <section id="NextStap">
        <div class="content_s" style="margin-bottom: 10px">
            <form action="{{ route('other_avaliations.store') }}" id="id_form_Nota" method="POST">
                @csrf
                <input type="hidden" name=version id="pauta-version" value="">
                <input type="hidden" name=anoLectivo id="anoLectivo" value="">
                <div class="row">
                    <div class="col-6 p-2">
                        <label>Selecione a disciplina</label>
                        <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                            required="" id="Disciplina_id_Select" data-actions-box="false"
                            data-selected-text-format="values" name="disciplina" tabindex="-98">
                        </select>
                    </div>
                    <div class="col-6 p-2">
                        <label>Selecione a turma</label>
                        <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                            required="" id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values"
                            name="turma" tabindex="-98">
                        </select>
                    </div>
                    <div class="col-6 p-2" id="caixaAvalicao">
                        <label>Selecione a métrica</label>
                        <select data-live-search="true" required class="selectpicker form-control form-control-sm"
                            required="" id="OA_metrica" data-actions-box="false" data-selected-text-format="values"
                            name="oa_number" tabindex="-98">
                            <option value="">Selecione a métrica </option>
                        </select>
                    </div>
                </div>
                <hr>
                <div id="tabela_new" style="display: none;">
                    <div class="row">
                        <div class="col-12">
                            <h2 id="Titulo_Avalicao"></h2>
                            <table class="table table-hover ">
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
                        <div class="col-12">
                            <div class=" float-right">
                                <span class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal"
                                    data-target="#exampleModal">
                                    <i class="fas fa-plus-circle"></i>
                                    Guardar notas
                                </span>
                            </div>
                        </div>

                        <div class="col-12">
                            <a id="btn_pdf" class=" float-right" target="_blank">
                            <span class="btn btn-danger mb-3 ml-3" 
                                >
                                <i class="fas fa-file-pdf"></i>
                                Gerar pdf
                            </span>
                            </a>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@section('models')
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
                    <button type="button" data-dismiss="modal" class="btn btn-primary">Ok,Entendí</button>
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
                    <br>
                    <br>
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
                        <br>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima,
                            por favor seleccione: Tenho a certeza.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores
                        forLEARN</button>
                    <button type="button" class="btn btn-danger" id="btn-callSubmit">Tenho a certeza</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>

        $(document).ready(function() {
            //Inicio do Cláudio JS
            //Variaveis 
            var Disciplina_id_Select = $("#Disciplina_id_Select");
            var Turma_id_Select = $("#Turma_id_Select");
            var avaliacao_id_Select = $("#OA_metrica");
            var metrica_id_Select = $("#metrica_id_Select");
            var lective_year = $("#lective_year");
            let Nota_aluno = '';
            let id_avaliacao = 0;
            let metrica_id = 0;
            let id_planoEstudo = 0;
            let discipline_id = 0;
            let whoIs = "";
            let metrica_id_teacher = "";
            let periodo = 0;
            let avlNome = "";
            let mtrNome = "";
            let version;
            let ano;
            //Carregar               
            ambiente();
            ano = $("#lective_year").val();
            $('#anoLectivo').val(ano);
            //Evento de mudança na select anolectivo
            lective_year.change(function() {
                 ano = $("#lective_year").val();
                $('#anoLectivo').val(ano);
                //chamndo a função de mudança de frames
                ambiente();
            });
            //Evento de mudança na select disciplina
            Disciplina_id_Select.bind('change keypress', function() {
                //chamndo a função de mudança de frames
                $("#avaliacao_id_Select").empty();
                $("#tabela_new").hide();
                $("#students_new").empty();

                var id = Disciplina_id_Select.val();
                Turma(id, lective_year.val());
            });
            //Evento de mudança na select turma e 
            Turma_id_Select.change(function() {
                //chamndo a função de mudança de turma e trazer os estudantes
                var lective_year = $("#lective_year").val();
                var id = Disciplina_id_Select.val();
                //  StudantGrade(discipline_id,metrica_id,id_planoEstudo,id_avaliacao,lective_year);
                if (Turma_id_Select.val() == "") {
                    avaliacao_id_Select.prop('disabled', true);
                } else {
                    avaliacao_id_Select.prop('disabled', false);
                }
            });
            //Mudando a métrica OA---
            avaliacao_id_Select.bind('change', function() {
                var lective_year = $("#lective_year").val();
                var id = Disciplina_id_Select.val();
                var Numero_prova = avaliacao_id_Select.val();

                StudantGrade(discipline_id, metrica_id, id_planoEstudo, id_avaliacao, lective_year,
                    Numero_prova);
            });
            //Função de mudança de frame
            function ambiente() {
                var anoL = lective_year.val();
                Disciplina_id_Select.empty();
                //Passar o parametro de ano lectivo
                //Neste momento colequei o ano anterior pk não há registro desse ano lectivo 
                //Por tanto no final terei de colocar a variavel (anoL)como parametro.
                $("#Titulo_Avalicao").empty();
                $("#tabela_new").hide();
                Turma_id_Select.empty();
                avaliacao_id_Select.empty();
                var anoL = lective_year.val();
                discipline_get_new(anoL);
                // discipline_get_new();
                // $("#NextStap").show();

            }
            //fim da funcção de Mundaça de frame
            //Função de pegar disciplina do professor frame
            // url: "/pt/avaliations/disciplines_teacher",
            function discipline_get_new(anolectivo) {

                $.ajax({
                    url: "/pt/avaliations/disciplines_teacher/" + anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        console.log("Carregando as disciplinas...")
                    }
                }).done(function(data) {
                    if (data['disciplina'].length) {
                        $("#students_new").empty();
                        Disciplina_id_Select.prop('disabled', true);
                        Disciplina_id_Select.empty();

                        $("#Disciplina_id_Select").append(
                            '<option selected="" value="00">Selecione a disciplina</option>');
                        $.each(data['disciplina'], function(index, row) {

                            $("#Disciplina_id_Select").append('<option  value="' + data['whoIs'] +
                                ',' + row.course_id + ',' + row.discipline_id + ' ">#' + row
                                .code + '  ' + row.dt_display_name + '</option>');
                        });
                        Disciplina_id_Select.prop('disabled', false);
                        Disciplina_id_Select.selectpicker('refresh');
                    } else {
                        Disciplina_id_Select.empty();
                        Disciplina_id_Select.prop('disabled', true);
                        console.log("sem dados para este ano lectivo")
                    }
                });
            }
            //fim da funcção de pegar disciplina de frame
            function Turma(id_plano, anolectivo) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                $.ajax({
                    url: "/pt/avaliations/turma_teacher_oa/" + id_plano + "/" + anolectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (id_plano == 00) {
                            return false;
                        }
                    },
                }).done(function(data) {
                    // periodo Da simestre
                    $.each(data['periodo'], function(indexInArray, valueOfElement) {
                        periodo = valueOfElement;
                    });
                    let index = 1;
                    if (data == 500) {
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        avaliacao_id_Select.empty();
                       
                        $("#caixaAvalicao").hide();
                        $("#textoAviso").text("");
                        $("#textoAviso").text(
                            "Atenção! esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma."
                        );
                        $("#modalAviso").modal('show');
                    } else {
                        if (periodo != 0 && periodo == "Anual") {
                            $("#OA_metrica").empty();
                            $("#OA_metrica").append('<option value="">Selecione a métrica</option>');
                            // for (let index = 1; index <= 12; index++) {
                            //     $("#OA_metrica").append('<option value="' + index + '">OA - ' + index +
                            //         '</option>');
                            // }
                            $("#OA_metrica").append('<option value="' + index + '">OA - ' + index +
                            '</option>');
                            $("#OA_metrica").selectpicker('refresh');
                        } else if (periodo != 0 && periodo != "Anual") {
                           
                            $("#OA_metrica").empty();
                            $("#OA_metrica").append('<option value="">Selecione a métrica</option>');
                            // for (let index = 1; index <= 6; index++) {
                            //     $("#OA_metrica").append('<option value="' + index + '">OA - ' + index +
                            //         '</option>');
                            // }

                            $("#OA_metrica").append('<option value="' + index + '">OA - ' + index +
                            '</option>');
                        }
                        $("#OA_metrica").selectpicker('refresh');
                        $("#caixaAvalicao").show();
                          
                        if (data['whoIs'] == "super") {
                            //chama o metodo para trazer o tratamento do loop da turma 
                            TurmaLoop(data, "coordenador")
                            //para trazer outra select na avaliacao de notas
                            $("#caixaAvalicao").show();
                            avaliacao_id_Select.selectpicker('refresh');
                            //Termina as avaliações do coordenador
                            whoIs = '';
                            whoIs = data['whoIs'];
                            data['avaliacao'] == null ? alert(
                                "Atenção! não foi encontrado nenhuma avalicão do tipo OA, verique se a mesma se encontra disponível como métrica nas avaliações."
                            ) : data['avaliacao'];
                        } else {
                            //Automático teacher.
                            whoIs = '';
                            whoIs = data['whoIs'];
                            $("#caixaAvalicao").show();
                            //Prencher variaveis para trazer depois os alunos.
                            id_avaliacao = data['avaliacao'].avl_id;
                            metrica_id = data['avaliacao'].id_metrica != null ? data['avaliacao']
                                .id_metrica : " Sem id métrica no intervalo";
                            //metrica_id_teacher=data['metrica'][0].mtrc_id;
                            console.log("Id:métrica: " + metrica_id);
                            discipline_id = data['disciplina'];
                            id_planoEstudo = data['plano_estudo'];
                            TurmaLoop(data, "teacher")
                            data['avaliacao'] == null ? alert(
                                "Atenção! não foi encontrado nenhuma avalicão do tipo OA, verique se a mesma se encontra disponível nas avaliações, ou contacte o staff forLEARN."
                            ) : data['avaliacao'];
                        }
                    }
                });
            }

            //Metodo para trazer a turma array
            function TurmaLoop(data, titulo) {
                if (data['turma'].length) {
                    id_planoEstudo = data['plano_estudo'];
                    discipline_id = data['disciplina'];
                    metrica_id = data['avaliacao'].id_metrica;
                    id_avaliacao = data['avaliacao'].avl_id;
                    avlNome = data['avaliacao'].avl_nome;
                    mtrNome = data['avaliacao'].metrica;

                    if (titulo == "teacher") {
                        metrica_id = data['avaliacao'].id_metrica != null ? data['avaliacao'].id_metrica :
                            " Sem id da métrica no intervalo ";
                        // console.log(metrica_id+" : id da métrica ou sem métrica ");
                    }
                    //  $("#tabela_new").show();
                    Turma_id_Select.prop('disabled', true);
                    Turma_id_Select.empty();

                    Turma_id_Select.append('<option selected="" value="">Selecione a turma</option>');
                    $.each(data['turma'], function(index, row) {
                        $("#Turma_id_Select").append('<option value="' + row.id + '">' + row.display_name +
                            '</option>');
                    });
                    Turma_id_Select.prop('disabled', false);
                    Turma_id_Select.selectpicker('refresh');
                    //switchRegimes(selectDiscipline[0]);
                } else {
                    Turma_id_Select.empty();
                    Turma_id_Select.prop('disabled', true);
                    avaliacao_id_Select.prop('disabled', true);
                }
            }

            function StudantGrade(discipline_id, metrica_id, id_planoEstudo, id_avaliacao, lective_year,
                Numero_prova) {
                var turma = Turma_id_Select.val();
                $.ajax({

                    url: "/avaliations/student_ajax_oa_new/" + discipline_id + "/" + metrica_id + "/" +
                        id_planoEstudo + "/" + id_avaliacao + "/" + turma + "/" + lective_year + "/" +
                        Numero_prova,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                        $("#students_new").empty();
                        $("#students_new tr").empty();
                        // var resultGrades = dataResult.data;
                        var resultStudents = dataResult.students;
                        var grades = dataResult.grades;
                        // var metricArePlublished = dataResult.metricArePlublished;
                        var bodyData = '';
                        var i = 1;
                        var ative = false;
                        var flag = true;
                        var regime = "";

                        var pauta_path = dataResult.pauta_path;

                        version = dataResult.version;
                      
                        if(pauta_path != null){
                        $('#btn_pdf').attr('href',pauta_path);
                        $('#btn_pdf').find('span').removeClass('btn-danger');
                        $('#btn_pdf').find('span').addClass('btn-primary');
                    }
                        $('#pauta-version').val(version);
                    
                        var nota = [];

                        if (resultStudents.length > 0) {
                            var dd = 0;
                            var j=0;
                            //Filtro quando o estudante entra depois de terem lançado a nota
                            let filtrado = resultStudents.filter(estudante => !  dataResult['grades'].some(nota => estudante.user_id === nota.user_id));
                            if (filtrado.length > 0) {
                                   dataResult['grades'] = dataResult['grades'].concat(filtrado);
                            }
                            resultStudents.forEach(function(student) {
                                regime = student.e_f == 0 ? "Frequência" : "Exame";
                                dataResult['grades'].forEach(function(nota) {
                                    if (student.user_id != nota.user_id){console.log(student.user_name)}
                                    else if (student.user_id == nota.user_id) {
                                        ative = true;
                                        j++;
                                        nota[i++] = nota.grade;
                                        if (nota.presence == null) {
                                            //se estiver presente na prova
                                            linha = "Linha_checado";
                                            bodyData += '<tr id=' + linha + student
                                                .user_id + '>'
                                            bodyData += "<td>" + j +
                                                "</td><td width='120'><input name='inputCheckBox[]'  value='falta_" +
                                                student.user_id + "'  id='checado" +
                                                student.user_id +
                                                "' onclick='verChecagem(this);'  type='checkbox'> <span  id='span_checado" +
                                                student.user_id +
                                                "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td> <td class='regime'>" +
                                                regime + "</td><td width='120'>" +
                                                student.n_student +
                                                "</td> <td style='font-size:0.9pc'>" +
                                                student.user_name +
                                                "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                                student.user_id +
                                                "><input type='number' id='nota_checado" +
                                                student.user_id +
                                                "'  min='0' max='20' name='notas[]' class='form-control' value=" +
                                                nota.grade +
                                                ">  <input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='teachear'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                                metrica_id +
                                                ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                                id_planoEstudo +
                                                "><input type='hidden' name='id_avaliacao' value=" +
                                                id_avaliacao +
                                                "> <input type='hidden' name='id_plano' value=" +
                                                id_planoEstudo + ">  ";
                                            bodyData += '</tr>'
                                        } else {
                                            //se não estiver presente na prova
                                            linha = "Linha_checado";
                                            bodyData += '<tr id=' + linha + student
                                                .user_id + '>'
                                            bodyData += "<td>" + j +
                                                "</td><td width='120'><input name='inputCheckBox[]'  value='falta_" +
                                                student.user_id + "'  id='checado" +
                                                student.user_id +
                                                "' onclick='verChecagem(this);'  type='checkbox' checked> <span  id='span_checado" +
                                                student.user_id +
                                                "' style='background: red; padding: 2px; color: #fff;'>AUSENTE</span></td> <td class='regime'>" +
                                                regime + "</td><td width='120'>" +
                                                student.n_student +
                                                "</td> <td style='font-size:0.9pc'>" +
                                                student.user_name +
                                                "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                                student.user_id +
                                                "><input type='number' id='nota_checado" +
                                                student.user_id +
                                                "'  min='0' max='20' name='notas[]' class='form-control' value=''>  <input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='teachear'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                                metrica_id +
                                                ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                                id_planoEstudo +
                                                "><input type='hidden' name='id_avaliacao' value=" +
                                                id_avaliacao +
                                                "> <input type='hidden' name='id_plano' value=" +
                                                id_planoEstudo + ">  ";
                                            bodyData += '</tr>'
                                            }
                                      }
                                });
                                if (ative == false) {
                                    console.log("sem registo")
                                    linha = "Linha_checado";
                                    bodyData += '<tr id=' + linha + student.user_id + '>'
                                    bodyData += "<td>" + i++ +
                                        "</td><td width='120'><input name='inputCheckBox[]'  value='falta_" +
                                        student.user_id + "'  id='checado" + student.user_id +
                                        "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                        student.user_id +
                                        "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td><td class='regime'>" +
                                        regime + "</td> <td width='120'>" + student.n_student +
                                        "</td> <td style='font-size:0.9pc'>" + student
                                        .user_name +
                                        "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                        student.user_id +
                                        "><input type='number'id='nota_checado" + student
                                        .user_id +
                                        "'  min='0' max='20' name='notas[]' class='form-control' value=''><input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='teachear'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                        metrica_id +
                                        ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                        id_planoEstudo +
                                        "><input type='hidden' name='id_avaliacao' value=" +
                                        id_avaliacao +
                                        "> <input type='hidden' name='id_plano' value=" +
                                        id_planoEstudo + ">  ";
                                    bodyData += '</tr>'
                                }
                            }); 
                        } else {
                            bodyData += '<tr>'
                            bodyData +=
                                "<td class='text-center fs-2'>Nenhum estudante foi encontrado nesta turma, verifique se existe algum matriculado na mesma.</td>";
                            bodyData += '</tr>'
                        }
                        $("#students_new").append(bodyData);
                        $("#Titulo_Avalicao").empty();
                        $("#Titulo_Avalicao").text(mtrNome + " - " + avaliacao_id_Select.val());
                        $("#tabela_new").show();
                        Nota_aluno = "";
                        // $('#metrica_id').prop('disabled', false);
                    },
                    error: function(dataResult) {
                        console.log('error' + dataResult);
                    }
                });
                //Ajax termina aqui
            }
            //Fim do Cláudio JS
            $("#btn-callSubmit").click(function() {
                $("#id_form_Nota").submit();
            });
        });
        //Checar presença na turma
        function verChecagem(element) {

            //------------------------------------//
            var linha1 = $("#Linha_" + element.id);
            var span = $("#span_" + element.id);
            var inputNota = $("#nota_" + element.id);
            //-----------------------------  ------//
            let checkbox = document.getElementById('' + element.id);
            console.log(checkbox)
            if (checkbox.checked) {
                span.css("background-color", "red")
                span.text("AUSENTE");
                inputNota.val("");
             
                 inputNota.attr('readonly', true);
            } else {
                span.css("background-color", "#38C172")
                span.text("PRESENTE");
                inputNota.val("");
               inputNota.attr('readonly', false);
            }
        }
    </script>    
@endsection
