<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@php
$title = 'LANÇAR NOTAS';
if($segunda_chamada)
    $title .= ' - 2ª CHAMADA';
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
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
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

        .devedor{
            background-color:red !important;
            color:white
        }
        .recurso{
            background-color:orange;
            color:white;
        }

        .dispensado{
            background-color:blue;
            color:white
        }

        .neen{
            background-color:green;
            color:white
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

                                <input type='hidden'  id="pauta" value="" name="pauta" class="form-control">
                                @if($segunda_chamada)
                                <input type='hidden'  value="{{$segunda_chamada}}" name="segunda_chamada" class="form-control">
                                @endif

                                <div class="col-6" id="caixaAvalicao" style="display: none">
                                    <div class="form-group col">
                                        <label>Selecione a avaliacão</label>
                                        <select data-live-search="true" required
                                            class="selectpicker form-control form-control-sm" required=""
                                            id="avaliacao_id_Select" data-actions-box="false"
                                            data-selected-text-format="values" name="avaliacao" tabindex="-98">
                                            <option value="">Selecione a avaliação </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6" id="caixaMatrica" style="display: none">
                                    <div class="form-group col">
                                        <label>Selecione a métrica</label>
                                        <select data-live-search="true" required
                                            class="selectpicker form-control form-control-sm" required=""
                                            id="metrica_id_Select" data-actions-box="false"
                                            data-selected-text-format="values" name="metrica" tabindex="-98">
                                            <option value="">Selecione a métrica </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row"></div>
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
                            <span class="btn btn-primary mb-3 ml-3" 
                                >
                                <i class="fas fa-file-pdf"></i>
                                Gerar pdf
                            </span>
                        </a>
                    </div>
                  
                </div>
            </div>
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
            //Inicio do Cláudio JS
            //Variaveis
            var Disciplina_id_Select = $("#Disciplina_id_Select");
            var Turma_id_Select = $("#Turma_id_Select");
            var avaliacao_id_Select = $("#avaliacao_id_Select");
            var metrica_id_Select = $("#metrica_id_Select");
            var lective_year = $("#lective_year");
            let Nota_aluno = '';
            let estado_presença = '';
            let presencaClass = '';
            let regime = '';
            let id_avaliacao = 0;
            let metrica_id = 0;
            let id_planoEstudo = 0;
            let discipline_id = 0;
            let whoIs = "";
            let metrica_id_teacher = "";
            //botões a serem ocultos
            let ElementoBTN_salvar = "";
            let callSumit = "";
            let cargo = "";
            var selectedLective = $('#lective_year').val();
            var metric = null;
            var metrica_code_dev;

                            console.log(selectedLective)
                            var lective = "<input type='hidden' name='selectedLective' id='selectedLective' class='form-control' value=" +
                            selectedLective + "> ";
                             $('#id_form_Nota').append(lective);



    function setar_pauta(whoIs) {        

             console.log(whoIs)

             const  pauta_status=  {
  'PF1': '40',
  'PF2': '40',
  'OA': '40',
  'Recurso': '10',
  'Neen': '20',
  'oral': '25',
  'Exame_especial': '35',
  'Trabalho': '50',
  'Defesa': '50',
  'TESP': '60'
};

            var pauta_tipo = "";
            var tipo = 0;

            if(whoIs == "teacher"){
                metric = metrica_code_dev;
            }
            if(whoIs == "super"){
                metric =  $("#metrica_id_Select").find('option:selected').data('metric');
            }
            
           
            
            $.each(pauta_status, function(chave, valor) {
                if(chave == metric){
                    pauta_tipo = metric;
                    tipo = valor;
                }
            });
            var paut = pauta_tipo + "," + tipo;
            $('#pauta').val(paut);
            console.log($('#pauta').val())
        }
            //Carregar              
            ambiente();
            //Evento de mudança na select anolectivo
            lective_year.change(function() {
                //chamndo a função de mudança de frames
                ambiente();
                selectedLective = $('#lective_year').val();
                $('#selectedLective').val(selectedLective);
            });
            //Evento de mudança na select disciplina
            Disciplina_id_Select.change(function() {
                //chamndo a função de mudança de frames
                $("#avaliacao_id_Select").empty();
                // $("#tabela_new").empty();
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
                StudantGrade(discipline_id, metrica_id, id_planoEstudo, id_avaliacao, lective_year);
                if (Turma_id_Select.val() == "") {
                    console.log('a')
                    avaliacao_id_Select.prop('disabled', true);
                } else {
                   
                    avaliacao_id_Select.prop('disabled', false);
                    $("#tabela_new").show();
                }
            });
            //Função de mudança de frame
            function ambiente() {
                var anoL = lective_year.val();
                if (anoL == 6) {
                    $("#NextStap").hide();
                    $("#tabela_new").hide();
                    $("#ConteudoMain").show();
                    Turma_id_Select.empty();
                } else {
                    //Passar o parametro de ano lectivo
                    //Neste momento colequei o ano anterior pk não há registro desse ano lectivo
                    //Por tando no final terei de colocar a variavel (anoL)como parametro.
                    Turma_id_Select.empty();
                    var anoL = lective_year.val();
                    discipline_get_new(anoL);
                    // discipline_get_new();
                    $("#ConteudoMain").hide();
                    $("#NextStap").show();
                }
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
                    }
                });
            }

            function Turma(id_plano, anolectivo) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                let url= "/pt/avaliations/turma_teacher/" + id_plano + "/" + anolectivo;
                
                @if($segunda_chamada)
                url += "?segunda_chamada=true";
                console.log(url);
                @endif
                
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if (id_plano == 00) return false;
                    },
                }).done(function(data) {
                   
                    if (data == 500) {
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        avaliacao_id_Select.empty();
                        avaliacao_id_Select.hide();
                        $("#textoAviso").text("");
                        $("#textoAviso").text(
                            "Atenção! esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma."
                        );
                        $("#modalAviso").modal('show');
                    } else {
                        if (data['whoIs'] == "super") {
                            //chama o metodo para trazer o tratamento do loop da turma  
                            TurmaLoop(data, "coordenador")
                            //para trazer outra select na avaliacao de notas
                            $("#caixaAvalicao").show();
                            //avaliacao_id_Select.prop('disabled', true);
                            $("#avaliacao_id_Select").append(
                                '<option value="">Selecione a avaliação</option>')
                            $.each(data['avaliacao'], function(index, row) {
                                $("#avaliacao_id_Select").append('<option value="' + row.avl_id +
                                    '">' + row.avl_nome + '</option>');
                            });
                            avaliacao_id_Select.selectpicker('refresh');
                            //Termina as avaliações do coordenador
                            whoIs = '';
                            whoIs = data['whoIs'];
                        } else {
                            //Validar existência de calendario geral
                            if (data['avaliacao'] == null) {
                                $("#textoAviso").text("");
                                $("#textoAviso").text(
                                    "Caro docente, verificou-se que não há calendário de prova disponível, razão pela qual não pode fazer o lançamento de notas. Contacte o superior hierárquico para habilitar ou extender a data do calendáro de prova."
                                );
                                $("#modalAviso").modal('show');
                                $("#btn-Enviar").hide();
                            } else {
                                $("#btn-Enviar").show();
                            }
                            //Automático teacher.
                            whoIs = '';
                            var FIla = '';
                            whoIs = data['whoIs'];
                            $("#caixaAvalicao").hide();
                            avaliacao_id_Select.empty();
                            //Prencher variaveis para trazer depois os alunos.
                            id_avaliacao = data['avaliacao'].avl_id;
                            metrica_id_teacher = data['metrica'].length > 0 ? data['metrica'][0].mtrc_id :
                                "Sem métrica no intervalo";
                            //metrica_id_teacher=data['metrica'][0].mtrc_id;
                           metrica_code_dev = data['metrica'][0].code_dev;
                            
                            discipline_id = data['disciplina'];
                            id_planoEstudo = data['plano_estudo'];
                            TurmaLoop(data, "teacher")
                            setar_pauta("teacher");
                            $("#tabela_new").hide();
                        }
                    }

                   

                });
            }
            //Selecionar_avaliacao_pega_metrica
            avaliacao_id_Select.change(function() {
                if (avaliacao_id_Select.val() != "") {
                    $("#caixaMatrica").show();
                    // $("#metrica_id_Select").hide();
                    var id = avaliacao_id_Select.val();
                    metricasCoordenador(id)
                } else {
                    $("#caixaMatrica").hide();
                    $("#metrica_id_Select").empty();
                }
            });
            //Selecionar_avaliacao_pega_metrica
            metrica_id_Select.change(function() {
                if (metrica_id_Select.val() != "") {
                     setar_pauta("super");
                    studentCourse_coordenador(id_planoEstudo);
                   
                } else {
                    $("#students_new").hide();
                }
            });
            //Metodo para trazer a turma array
            function TurmaLoop(data, titulo) {
                if (data['turma'].length) {
                    id_planoEstudo = data['plano_estudo'];
                    discipline_id = data['disciplina'];
                    if (titulo == "teacher") {

                        $("#Titulo_Avalicao").empty();
                        metrica_id = data['metrica'].length > 0 ? data['metrica'][0].mtrc_id :
                            "Sem métrica no intervalo";
                        metrica_name = data['metrica'].length > 0 ? data['metrica'][0].mtrc_nome :
                            "Sem nome da métrica no intervalo";
                        validar_metrica = data['metrica'].length > 0 ? data['metrica'][0].mtrc_nome : "";
                        //console.log(data['metrica'][0].mtrc_nome+" : id da métrica ou sem métrica ");
                        if (validar_metrica == "") {
                            //alert("Sem métrica")    
                            $("#textoAviso").text("");
                            $("#textoAviso").text(
                                "Caro docente, verificou-se que não há calendário de prova disponível, razão pela qual não pode fazer o lançamento de notas. Contacte o superior hierárquico para habilitar ou extender a data do calendáro de prova."
                            );
                            $("#modalAviso").modal('show');
                            $("#btn-Enviar").hide();
                        } else {
                            $("#btn-Enviar").show();
                        }
                        $("#Titulo_Avalicao").text(metrica_name);
                    }
                    $("#tabela_new").show();
                    Turma_id_Select.prop('disabled', true);
                    Turma_id_Select.empty();

                    Turma_id_Select.append('<option selected="" value="">Selecione a turma</option>');
                    $.each(data['turma'], function(index, row) {
                        $("#Turma_id_Select").append('<option value="' + row.id + '">' + row.display_name +
                            '</option>');
                    });
                    Turma_id_Select.prop('disabled', false);
                    Turma_id_Select.selectpicker('refresh');
                } else {
                    Turma_id_Select.empty();
                    Turma_id_Select.prop('disabled', true);
                    avaliacao_id_Select.prop('disabled', true);
                }

            }
            //Metrodo de trazer as metricas --de forma manual.
            function metricasCoordenador(id_avaliacao) {
                $.ajax({
                    url: "/pt/avaliations/metrica_ajax_coordenador/" + id_avaliacao,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {},
                }).done(function(data) {
                    if (data['metricas'].length) {
                        $("#metrica_id_Select").empty();
                        metrica_id_Select.append('<option selected="" value="">Selecione a métrica</option>');
                        $.each(data['metricas'], function(index, row) {
                            $("#metrica_id_Select").append('<option value="' + row.id + '" data-metric ="'+ row.code_dev+'">' + row
                                .nome + '</option>');
                        });
                        metrica_id_Select.prop('disabled', false);
                        metrica_id_Select.selectpicker('refresh');
                    } else {
                        console.log(data['metricas'].length)
                        metrica_id_Select.prop('disabled', true);
                        metrica_id_Select.empty();
                    }
                });
            }
            //Fim do método

            //Pegar os alunos de forma manual coordenador
            function studentCourse_coordenador() {
                var turma = Turma_id_Select.val();
                var lective_year_select = $("#lective_year").val();
                cargo = Disciplina_id_Select.val().split(",")[0];
                console.log(cargo);
                let url = "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id_Select.val() +
                        "/" + id_planoEstudo + "/" + avaliacao_id_Select.val() + "/" + turma + "/" +
                        lective_year_select + "?whoIs=" + cargo;

                @if($segunda_chamada)
                url += "&segunda_chamada=true";
                @endif
                console.log(url);
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {},
                }).done(function(data) {
                    //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                    var url =   "/avaliations/mac_pdf/" + discipline_id + "/" + metrica_id_Select.val() +
                        "/" + id_planoEstudo + "/" + avaliacao_id_Select.val() + "/" + turma + "/" +
                        lective_year_select;

                    @if($segunda_chamada)
                        url += "?segunda_chamada=true";
                    @endif
                    
                            $('#btn_pdf').attr('href',url)
                    $("#students_new").empty();
                   
                    // var resultGrades = dataResult.data;
                    var resultStudents = data.students;
                    var students_segunda_chamada = data.students_segunda_chamada;
                    var metricas = data.metricas;
                    var config = data.config;
                    // var metricArePlublished = dataResult.metricArePlublished;
                    var bodyData = '';
                    var i = 1;
                    var flag = true;
                    if (resultStudents.length > 0) {
                        var dd = 0;
                        resultStudents.forEach(function(student) {
                            data['grades'].forEach(function(nota) {
                                if (student.user_id == nota.user_id) {
                                    Nota_aluno = nota.aanota;
                                    presencaClass = nota.aanota;
                                }
                            });
                            regime = student.e_f == 0 ? "Frequência" : "Exame";
                            //validação se faltou ou não na prova
                            if (presencaClass == null) {
                                //essa linhas é dos alunos que faltaram
                                linha = "Linha_checado";
                                bodyData += '<tr style="background-color:#f4f4f4;" id=' + linha +
                                    student.user_id + '>'
                                bodyData += "<td>" + i++ +
                                    "</td><td width='120'><input name='inputCheckBox[]' checked value='falta," +
                                    student.user_id + "'  id='" + student.user_id +
                                    "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                    student.user_id +
                                    "' style='background: red; padding: 2px; color: #fff;'>AUSENTE</span></td> <td class='regime'>" +
                                    regime + "</td><td width='120'>" + student.n_student +
                                    "</td> <td style='font-size:0.9pc'>" + student.user_name +
                                    "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                    student.user_id + "><input type='number' readonly id='nota_checado" +
                                    student.user_id +
                                    "'  min='0' max='20' name='notas[]' class='form-control' value=" +
                                    Nota_aluno +
                                    "><input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='super'><input type='hidden' min='0' max='20' name='metrica_teacher'  class='form-control' value=" +
                                    metrica_id_teacher +
                                    " >  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                    id_planoEstudo + "> ";
                                bodyData += '</tr>'
                            } else {
                                linha = "Linha_checado";
                                bodyData += '<tr id=' + linha + student.user_id + '>'
                                bodyData += "<td>" + i++ +
                                    "</td><td width='120'><input name='inputCheckBox[]' value='falta," +
                                    student.user_id + "'  id='" + student.user_id +
                                    "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                    student.user_id +
                                    "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td> <td class='regime'>" +
                                    regime + "</td><td width='120'>" + student.n_student +
                                    "</td> <td style='font-size:0.9pc'>" + student.user_name +
                                    "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                    student.user_id + "><input type='number' id='nota_checado" +
                                    student.user_id +
                                    "'  min='0' max='20' name='notas[]' class='form-control' value=" +
                                    Nota_aluno +
                                    "><input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='super'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                    metrica_id_teacher +
                                    ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                    id_planoEstudo + "> ";
                                bodyData += '</tr>'
                            }
                        });
                        Nota_aluno = '';
                    } else {
                        bodyData += '<tr>'
                        bodyData +=
                            "<td class='text-center fs-2'>Nenhum estudante foi encontrado nesta turma.</td>";
                        bodyData += '</tr>'
                    }
                    $("#students_new").append(bodyData);

                   
                    var pauta_lancada = data['pauta_lancada'];
                    console.log("pauta_lancada"+data);
                        if(metric == 'Neen'){
                           


                            resultStudents.forEach(function(student) {

                             var mac =  data['grades'].find(grade => grade.user_id == student.user_id).aanota;
                           
                                if(mac <= config.mac_nota_recurso){
                                   
                                    $("#Linha_checado"+student.user_id).addClass('recurso');
                                    $("#nota_checado"+student.user_id).attr('readonly','true');
                                    $("#nota_checado"+student.user_id).addClass('text-white');
                                    $("#" + student.user_id).prop('disabled','true');
                                }
                                if(mac >= config.mac_nota_dispensa){
                                    $("#Linha_checado"+student.user_id).addClass('dispensado');
                                    $("#nota_checado"+student.user_id).attr('readonly','true');
                                    $("#nota_checado"+student.user_id).addClass('text-white');
                                    $("#" + student.user_id).prop('disabled','true');
                                }
                                if(mac >= config.exame_nota_inicial && mac <= config.exame_nota_final){
                                    $("#Linha_checado"+student.user_id).addClass('neen');
                                    if(!pauta_lancada){
                                        $("#nota_checado"+student.user_id).attr('value','');
                                    }
                                  
                                    $("#nota_checado"+student.user_id).addClass('text-white');
                                   
                                }


                            });

                        }

                       
                        resultStudents.forEach(function(student) {
                         
                            if(students_segunda_chamada.length > 0 && students_segunda_chamada.includes(student.user_id)) {   
                                $("#nota_checado"+student.user_id).prop('readonly', true);
                                $("#" + student.user_id).prop('readonly', true);
                            }
                        });

                        

                });
            }

            function StudantGrade(discipline_id, metrica_id, id_planoEstudo, id_avaliacao, lective_year) {
                cargo = Disciplina_id_Select.val().split(",")[0];
                console.log(cargo);
                var turma = Turma_id_Select.val();
                let url ="/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" +
                        id_planoEstudo + "/" + avaliacao_id + "/" + turma + "/" + lective_year+ "?whoIs=" + cargo;
                @if($segunda_chamada)
                url += "&segunda_chamada=true";
                @endif
                console.log(url)
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        var url =  "/avaliations/mac_pdf/" + discipline_id + "/" + metrica_id + "/" +
                            id_planoEstudo + "/" + avaliacao_id + "/" + turma + "/" + lective_year +"/";
                    @if($segunda_chamada)
                        url += "?segunda_chamada=true";
                    @endif
                            $('#btn_pdf').attr('href',url)

                   
                        //Estado da publish
                        var estado_pauta = dataResult.estado_pauta;
                       var estado_pauta_lancar = dataResult.estado_pauta_lancar;
                    
                        if (estado_pauta == 1) {
                            $("#textoAviso").text("");
                            $("#textoAviso").text(
                                "Atenção! detetamos que a pauta desta disciplina já se encontra publicada, com base nesta situação não lhe é permitido fazer o lançamento de nota. Em caso de dúvida, cantacte a coordenação."
                            );
                            $("#modalAviso").modal('show');
                            ElementoBTN_salvar = $("#div_btn_save").html();
                            callSumit = $("#ocultar_btn").html();
                            $("#btn-Enviar").remove();
                            $("#btn-callSubmit").remove();
                            $(".notaCampo").attr("disabled", true);
                        } else if (estado_pauta == 0) {
                            if (ElementoBTN_salvar.length) {
                                $("#div_btn_save").html(ElementoBTN_salvar);
                                $("#ocultar_btn").html(callSumit);
                            }
                        }

                        // Estado lançar pauta
                        console.log('pauta:'+estado_pauta_lancar)
                        if (estado_pauta_lancar == 1) {
                            $("#textoAviso").text("");
                            $("#textoAviso").text(
                                "Atenção! detetamos que a pauta desta disciplina já se encontra lançada, com base nesta situação não lhe é permitido fazer o lançamento de nota. Em caso de dúvida, cantacte a coordenação."
                            );
                            $("#modalAviso").modal('show');
                            ElementoBTN_salvar = $("#div_btn_save").html();
                            callSumit = $("#ocultar_btn").html();
                            $("#btn-Enviar").remove();
                            $("#btn-callSubmit").remove();
                            $(".notaCampo").attr("disabled", true);
                        } else if (estado_pauta_lancar == 0) {
                            if (ElementoBTN_salvar.length) {
                                $("#div_btn_save").html(ElementoBTN_salvar);
                                $("#ocultar_btn").html(callSumit);
                            }
                        }



                        $("#students_new tr").empty();

                        // var resultGrades = dataResult.data;
                        var resultStudents = dataResult.students;
                        var grades = dataResult.grades;
                        // var metricArePlublished = dataResult.metricArePlublished;
                        var bodyData = '';
                        var i = 1;
                        var flag = true;
                        var students_segunda_chamada = dataResult.students_segunda_chamada;
                        //Compara o utilizador e tras automático ou manual
                        if (whoIs == "teacher") {
                            console.log(resultStudents.length > 0)
                            //validar notas
                            if (resultStudents.length > 0) {
                                resultStudents.forEach(function(student) {
                                    var count = 0;
                                    dataResult['grades'].forEach(function(nota) {
                                        if (student.user_id == nota.user_id) {
                                            Nota_aluno = nota.aanota;
                                            presencaClass = nota.aanota;
                                        }
                                    });
                                    regime = "";
                                    regime = student.e_f == 0 ? "Frequência" : "Exame";
                                    //validação se faltou ou não na prova
                                   
                                    if (presencaClass == null) {
                                       
                                        //essa linhas é dos alunos que faltaram
                                        linha = "Linha_checado";
                                        bodyData +=
                                            '<tr  style="background-color:#f4f4f4;" id=' +
                                            linha + student.user_id + '>'
                                        bodyData += "<td>" + i++ +
                                            "</td><td width='120'><input name='inputCheckBox[]' checked value='falta," +
                                            student.user_id + "'  id='" + student.user_id +
                                            "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                            student.user_id +
                                            "' style='background: red; padding: 2px; color: #fff;'>AUSENTE</span></td>  <td class='regime'>" +
                                            regime + "</td><td width='120'>" + student
                                            .n_student + "</td> <td style='font-size:0.9pc'>" +
                                            student.user_name +
                                            "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                            student.user_id +
                                            "><input type='number'  readonly id='nota_checado" + student
                                            .user_id +
                                            "'  min='0' max='20' name='notas[]' class='form-control notaCampo' value=" +
                                            Nota_aluno +
                                            "><input type='hidden' min='0' max='20' name='whoIs' class='form-control' value='teachear'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                            metrica_id_teacher +
                                            ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                            id_planoEstudo + "> ";
                                        bodyData += '</tr>'
                                    } else {
                                        linha = "Linha_checado";
                                        bodyData += '<tr id=' + linha + student.user_id + '>'
                                        bodyData += "<td>" + i++ +
                                            "</td><td width='120'><input name='inputCheckBox[]' value='falta," +
                                            student.user_id + "'  id='" + student.user_id +
                                            "' onclick='verChecagem(this);'  type='checkbox'> <span id='span_checado" +
                                            student.user_id +
                                            "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span> </td> <td class='regime' >" +
                                            regime + "</td><td width='120'>" + student
                                            .n_student + "</td> <td style='font-size:0.9pc'>" +
                                            student.user_name +
                                            "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                            student.user_id +
                                            "><input type='number' id='nota_checado" + student
                                            .user_id +
                                            "'  min='0' max='20' name='notas[]' class='form-control notaCampo' value=" +
                                            Nota_aluno +

                                            "><input  id='whoIs' type='hidden' min='0' max='20' name='whoIs' class='form-control' value='teachear'><input type='hidden' min='0' max='20' name='metrica_teacher' class='form-control' value=" +
                                            metrica_id_teacher +
                                            ">  <input type='hidden' name='id_plano_estudo' class='form-control' value=" +
                                            id_planoEstudo + "> ";
                                        bodyData += '</tr>'
                                    }
                                });
                            } else {
                                bodyData += '<tr>'
                                bodyData +=
                                    "<td class='text-center fs-2'>Nenhum estudante foi encontrado nesta turma.</td>";
                                bodyData += '</tr>'
                            }

                        } else {

                        }
                        $("#students_new").append(bodyData);
                      
                        resultStudents.forEach(function(student) {
                            if(students_segunda_chamada.length > 0 && students_segunda_chamada.includes(student.user_id)) {   
                                $("#nota_checado"+student.user_id).prop('readonly', true);
                                $("#" + student.user_id).prop('readonly', true);
                            }
                        });

                         
                    },
                    error: function(dataResult) {
                        console.log('error' + result);
                    }
                });
            }
            //Fim do Cláudio JS


            $("#btn-callSubmit").click(function() {
                $("#id_form_Nota").submit();
            });
            var selectStudyPlan = $("#course_id");
            var selectDiscipline = $("#discipline_id");
            var selectAvaliation = $("#avaliacao_id");
            var selectClass = $("#class_id");
            var selectMetrica = $("#metrica_id");

            getAllStudyPlanEdition();

            function getAllStudyPlanEdition() {
                $.ajax({
                    url: "/avaliations/plano_estudo_ajax/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    selectStudyPlan.prop('disabled', true);
                    selectStudyPlan.empty();
                    selectStudyPlan.append('<option selected="" value="" style="color:black;"></option>');
                    $.each(data, function(index, row) {
                        selectStudyPlan.append('<option value="' + row.spea_id + '">' + row
                            .spea_nome + '</option>');
                    });
                    selectStudyPlan.prop('disabled', false);
                    selectStudyPlan.selectpicker('refresh');
                });
            }

            $("#class_id").prop('disabled', true);
            //Buscar Disciplinas apartir do curso associados ao Plano estudo Avaliacao
            $('#course_id').change(function() {
                var course_id = $(this).children("option:selected").val();
                console.log(course_id);
                $("#class_id").empty();
                $("#avaliacao_id").empty();
                $("#metrica_id").empty();
                $("#students tr").empty();
                $('#avaliacao_id').prop('disabled', true);
                $('#class_id').prop('disabled', true);
                $('#metrica_id').prop('disabled', true);

                if (course_id == "") {
                    console.log("Empty");
                    $('#discipline_id').prop('disabled', true);
                    $("#discipline_id").empty();
                    $('#avaliacao_id').prop('disabled', true);
                    $("#class_id").prop('disabled', true);
                    $("#avaliacao_id").empty();
                    $('#metrica_id').prop('disabled', true);
                    $('#class_id').empty("");
                    $("#metrica_id").empty();
                    $("#students tr").empty();
                } else {
                    console.log(course_id)
                    $.ajax({
                        url: "/avaliations/disciplines_ajax/" + course_id,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                            $("#discipline_id").empty();
                            var resultData = dataResult.data;
                            var resultClasses = dataResult.classes;
                            var bodyData = '';
                            var bodyClassData = '';
                            var i = 1;
                            console.log(dataResult.data);
                            selectDiscipline.prop('disabled', true);
                            selectDiscipline.empty();
                            selectClass.empty();
                            selectDiscipline.append('<option selected="" value=""></option>');
                            selectClass.append('<option selected="" value=""></option>');
                            $.each(resultData, function(index, row) {
                                selectDiscipline.append('<option value="' + row
                                    .discipline_id + '">' + row.dt_display_name +
                                    '</option>');
                            });
                            $.each(resultClasses, function(index, row) {
                                selectClass.append('<option value="' + row.id + '">' +
                                    row.display_name + '</option>');
                            });
                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                            selectClass.prop('disabled', false);
                            selectClass.selectpicker('refresh');

                        },
                        error: function(dataResult) {

                        }
                    });
                }
            });
            //Buscar Avaliações apartir do curso e disciplina associados ao Plano estudo Avaliacao
            $('#discipline_id').change(function() {
                var discipline_id = $(this).children("option:selected").val();
                $("#students tr").empty();
                $('#metrica_id').prop('disabled', true);
                $("#metrica_id").empty();
                $("#avaliacao_id").empty();
                $('#class_id').val("");
                $('#class_id').prop('disabled', true);

                if (discipline_id == "") {
                    console.log("Empty");
                    $('#avaliacao_id').prop('disabled', true);
                    $("#class_id").prop('disabled', true);
                    $("#avaliacao_id").empty();
                    $('#metrica_id').prop('disabled', true);
                    $('#class_id').val("");
                    $("#metrica_id").empty();
                    $("#students tr").empty();
                } else {
                    let url= "/avaliations/avaliacao_ajax/" + discipline_id;
                    @if($segunda_chamada)
                url += "?segunda_chamada=true";
                @endif
                    $.ajax({
                        url: url,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                            $("#avaliacao_id").empty();
                            var resultData = dataResult.data;
                            var bodyData = '';
                            var i = 1;
                            console.log(dataResult.data);
                            selectAvaliation.prop('disabled', true);
                            selectAvaliation.empty();
                            selectAvaliation.append('<option selected="" value=""></option>');
                            $.each(resultData, function(index, row) {
                                selectAvaliation.append('<option value="' + row.avl_id +
                                    '">' + row.avl_nome + '</option>');
                            });
                            selectAvaliation.prop('disabled', false);
                            selectAvaliation.selectpicker('refresh');
                        },
                        error: function(dataResult) {

                        }
                    });
                }
            });
            //Buscar Metricas apartir do curso da disciplina e da avaliacao associados ao Plano estudo Avaliacao
            $('#avaliacao_id').change(function() {
                var avaliacao_id = $(this).children("option:selected").val();
                var discipline_id = $('#discipline_id').val();
                var course_id = $('#course_id').val();
                $("#students tr").empty();
                $("#metrica_id").empty();
                $('#class_id').val("");
                if (avaliacao_id == "") {
                    console.log("Empty");
                    $('#metrica_id').prop('disabled', true);
                    $("#class_id").prop('disabled', true);
                    $("#class_id").val("");
                    $("#metrica_id").empty();
                    $("#students tr").empty();
                } else {
                    $.ajax({
                        url: "/avaliations/metrica_ajax/" + avaliacao_id + "/" + discipline_id +
                            "/" + course_id,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'

                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                            $("#metrica_id").empty();
                            console.log(" Teste básico " + dataResult);
                            var resultData = dataResult.data;
                            var bodyData = '';
                            var i = 1;
                            console.log(dataResult.data);
                            selectMetrica.prop('disabled', true);
                            selectMetrica.empty();
                            selectMetrica.append('<option selected="" value=""></option>');
                            //Limpar a tabela sempre que for
                            $.each(resultData, function(index, row) {
                                selectMetrica.append('<option value="' + row.mtrc_id +
                                    '">' + row.mtrc_nome + '</option>');
                            });
                            selectMetrica.prop('disabled', false);
                            selectMetrica.selectpicker('refresh');
                            //$("#class_id").prop('disabled', false)//

                            selectClass.prop('disabled', false);
                        },
                        error: function(dataResult) {

                        }
                    });
                }
            });

            $("#class_id").change(function() {
                if ($("#class_id").val() == "") {
                    $("#metrica_id").val("");
                    $("#students tr").empty();
                    $('#metrica_id').prop('disabled', true);
                } else {
                    var avaliacao_id = $("#avaliacao_id").val();
                    var discipline_id = $('#discipline_id').val();
                    var course_id = $('#course_id').val();
                    $.ajax({

                        url: "/avaliations/metrica_ajax/" + avaliacao_id + "/" + discipline_id +
                            "/" + course_id,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                            var resultData = dataResult.data;
                            var bodyData = '';
                            var i = 1;
                            selectMetrica.prop('disabled', true);
                            selectMetrica.empty();

                            selectMetrica.append('<option selected="" value=""></option>');
                            $.each(resultData, function(index, row) {
                                selectMetrica.append('<option value="' + row.mtrc_id +
                                    '">' + row.mtrc_nome + '</option>');
                            });
                            selectMetrica.prop('disabled', false);
                            selectMetrica.selectpicker('refresh');
                            selectClass.prop('disabled', false);
                        },
                        error: function(dataResult) {}
                    });
                    $("#students tr").empty();
                }
            });
            //Listar estudante que tem a determinada disciplina e determinada turma.
            $("#metrica_id").change(function() {
                console.log('iu')
                if ($("#metrica_id").val() == "") {
                    $("#students tr").empty();
                } else {
                    console.log('tu')
                    var discipline_id = $('#discipline_id').val();
                    var metrica_id = $('#metrica_id').val();
                    var course_id = $('#course_id').val();
                    var avaliacao_id = $('#avaliacao_id').val();
                    var class_id = $('#class_id').val();
                    console.log('course_id'+$course_id)
                    cargo = Disciplina_id_Select.val().split(",")[0];
                    console.log(cargo);
                    $.ajax({
                        url: "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" +
                            course_id + "/" + avaliacao_id + "/" + class_id + "?whoIs=" + cargo,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                         
                         
                            $("#students tr").empty();
                            var resultGrades = dataResult.data;
                            var resultStudents = dataResult.students;
                            var metricArePlublished = dataResult.metricArePlublished;
                            var bodyData = '';
                            var i = 1;
                            var flag = true;
                            var a;
                            var students_segunda_chamada = dataResult.students_segunda_chamada;
                            
                            for (a = 0; a < resultStudents.length; a++) {
                                var dd = a;
                                flag = true;
                                //Verifica se o Array das notas está vazio
                                if (resultGrades == '') {
                                    checkInputEmpty(dd);
                                    bodyData += '<tr>'
                                    bodyData += "<td>" + i++ +
                                        "</td><td width='120'><input type='checkbox' id='check" +
                                        dd + "' onclick='disbleInput(" + dd +
                                        ");' checked> <input id='input" + dd +
                                        "' value='true' name='inputCheckBox[]' hidden> <span id='span" +
                                        dd +
                                        "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td><td width='120'>" +
                                        resultStudents[a].n_student + "</td><td>" +
                                        resultStudents[a].user_name +
                                        "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                        resultStudents[a].user_id + ">";
                                    if (metrica_id == 53 ||
                                        metricArePlublished
                                    ) //se a metrica for igual a OA ou a metrica ja for publicada bloquear o campo (disabled)
                                    {
                                        bodyData +=
                                            "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id=" +
                                            dd + " readOnly></td>"
                                    } else {
                                        bodyData +=
                                            "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' step='0.01' id=" +
                                            dd + "></td>"
                                    }
                                    bodyData += '</tr>'
                                } else {
                                    checkInputEmpty(dd);
                                    bodyData += '<tr>'
                                    bodyData += "<td>" + i++ + "</td><td width='120'>"
                                    bodyData += "<input type='checkbox' id='check" + dd +
                                        "' onclick='disbleInput(" + dd +
                                        ");' checked> <input id='input" + dd +
                                        "' value='true' name='inputCheckBox[]' hidden> <span id='span" +
                                        dd +
                                        "' style='background: #38C172; padding: 2px; color: #fff;'>PRESENTE</span></td>"
                                    bodyData += "<td width='120'>" + resultStudents[a]
                                        .n_student + "<td>" + resultStudents[a].user_name +
                                        "</td><td width='100'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                        resultStudents[a].user_id + ">";

                                    if (metrica_id == 53 ||
                                        metricArePlublished
                                    ) //se a metrica for igual a OA bloquear o campo (disabled)
                                    {

                                        for (var b = 0; b < resultGrades.length; b++) {
                                            if (resultGrades[b].user_id == resultStudents[a]
                                                .user_id) {
                                                flag = false;
                                                bodyData +=
                                                    "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value=" +
                                                    resultGrades[b].aanota +
                                                    " step='0.01' id=" + dd + " readOnly></td>"
                                            }
                                        }
                                        if (flag) {
                                            bodyData +=
                                                "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id=" +
                                                dd + " readOnly></td>"
                                        }
                                    } else {
                                        for (var b = 0; b < resultGrades.length; b++) {
                                            if (resultGrades[b].user_id == resultStudents[a]
                                                .user_id) {
                                                flag = false;
                                                if (avaliacao_id ==
                                                    22) //caso for recurso input (max = 12)
                                                {
                                                    bodyData +=
                                                        "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value=" +
                                                        resultGrades[b].aanota +
                                                        " step='0.01' id=" + dd + "></td>"
                                                } else {
                                                    bodyData +=
                                                        "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value=" +
                                                        resultGrades[b].aanota +
                                                        " step='0.01' id=" + dd + "></td>"
                                                }
                                            }
                                        }
                                        if (flag) {
                                            if (avaliacao_id ==
                                                22) //caso for recurso input (max = 12)
                                            {
                                                bodyData +=
                                                    "<input type='number' name='notas[]' min='0' max='12' class='form-control notas' value='' step='0.01' id=" +
                                                    dd + "></td>"
                                            } else {
                                                bodyData +=
                                                    "<input type='number' name='notas[]' min='0' max='20' class='form-control notas' value='' step='0.01' id=" +
                                                    dd + "></td>"
                                            }
                                        }
                                    }

                                    // }
                                    bodyData += '</tr>'
                                }
                            }
                            $("#students").append(bodyData);
                      

                              resultStudents.forEach(function(student) {
                            if(students_segunda_chamada.length > 0 && students_segunda_chamada.includes(student.user_id)) {   
                                $("#nota_checado"+student.user_id).prop('readonly', true);
                                $("#" + student.user_id).prop('readonly', true);
                            }
                        });

                        },
                        error: function(dataResult) {}
                    });
                }
            });
        });

        function disbleInput(dd) {
            var checkStatus = document.getElementById("check" + dd + "").checked;
            var inputGrade = document.getElementById(dd);
            var span = document.getElementById("span" + dd + "");

            if (checkStatus == true) {
                inputGrade.readOnly = false;
                document.getElementById("input" + dd + "").value = "";
                document.getElementById("input" + dd + "").value = true;
                span.style.backgroundColor = "#38C172";
                span.innerHTML = "PRESENTE";
            } else {
                inputGrade.readOnly = true
                document.getElementById("input" + dd + "").value = "";
                document.getElementById("input" + dd + "").value = false;
                span.style.backgroundColor = "red";
                span.innerHTML = "AUSENTE";
            }
        }

        function checkInputEmpty(dd) {
            var inputGrade = document.getElementById(dd);
            Console.log("Realidade Virtual");
            console.log(inputGrade);
        }

        function verChecagem(element) {
            console.log(element);
            var linha1 = $("#Linha_checado" + element.id);
            var span = $("#span_checado" + element.id);
            var inputNota = $("#nota_checado" + element.id);
            let checkbox = document.getElementById('' + element.id);
            console.log(checkbox.checked)
            if (checkbox.checked) {
                linha1.css("background-color", "#f4f4f4");
                span.css("background-color", "red")
                span.text("AUSENTE");
                inputNota.val("");
                inputNota.prop("readonly",true);
            } else {
                linha1.css("background-color", "#fff");
                span.css("background-color", "#38C172")
                span.text("PRESENTE");
                inputNota.val("");
                inputNota.prop('disabled', false);
                inputNota.prop("readonly",false);
            }
        }
    </script>
@endsection