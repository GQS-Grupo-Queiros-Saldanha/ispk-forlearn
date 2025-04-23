<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Avaliação')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Avaliação</li>
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
    <table id="avaliacao-tables" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo de Avaliação</th>
                <th>Criado Por</th>
                <th>Editado Por</th>
                <th>Criado a</th>
                <th>Editado a</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
    <a href="" id="nova_avalicao" class="d-none" hidden></a>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    {{-- Modal para associacao de uma métrica a uma avalicao --}}
    <div class="modal fade" id="insertMetrica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div id="modal_max" class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title " id="exampleModalLabel">Associar Métrica</h5>
                    <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="linha_row" class="row ">
                        <div id="associarMetrica" class="col">
                            <form id="metricaForm">
                                <input type="hidden" name="avaliacao_id" id="avaliacao_id_x" value="">
                                <div class="form-group col">
                                    <label>Nome da Métrica</label>
                                    <input required type="text" name="nome_metrica" class="form-control"
                                        id="nome_metrica">
                                </div>
                                <div class="form-group col">
                                    <label>Percentagem da Métrica</label>
                                    <input required type="number" min="0" max="100" name="percentagem"
                                        id="percentagem" class="form-control">
                                </div>
                                <div class="form-group col">
                                    <label>Tipo de Métrica</label>
                                    <select name="tipo_avaliacao" id="tipo_metrica" class="form-control">
                                        <option value=""></option>

                                    </select>
                                </div>
                                <div class="form-group col">
                                    <div class=" custom-control custom-switch" id="semcaledario" style="display: none">
                                        <input type="checkbox" class="custom-control-input" id="outrasAvaliacao"
                                            name="outrasAvaliacao">
                                        <label class="custom-control-label" for="customSwitch1">Não tem calendário</label>
                                    </div>
                                </div>
                                <div class="form-group col alert alert-success" role="alert" id="div_success">
                                    <p id="success_message"></p>
                                </div>
                                <div class="form-group col alert alert-danger" role="alert" id="div_error">
                                    <p id="error_message"></p>
                                </div>
                                <div class="modal-footer">
                                    <button id="submit" name="adc_metrica" class="btn btn-success">Adicionar
                                        Métrica</button>
                                </div>
                            </form>

                            <input type="hidden" name="avaliacao_id" id="avaliacao_id_y" value="">
                            <div class="form-group col">
                                <label>Métrica</label>
                                <table id="" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Percentagem</th>
                                            <th>Avaliação</th>
                                            <th>Tipo de Métrica</th>
                                            <th colspan="2">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyData"></tbody>
                                </table>
                            </div>
                        </div>
                        <div style="display: none; box-shadow:#eef0f3 -3px -1px 6px 0px" class="bg-light"
                            id="metricaSemestral"><br><br>
                            <form id="inserirDataMetrica">
                                <input type="hidden" name="metrica_id" id="metrica_id">
                                <div class="d-flex flex-row bd-highlight mb-2 ">
                                    <div class="pr-0 pl-0 p-2 bd-highlight">
                                        <h2 id="tilulo_avaliacao"></h2>
                                    </div>
                                    <div class="p-0 mt-3">
                                        <h5 id="tilulo_metrica"></h5>
                                    </div>
                                </div>
                                <label>Data-inicio Calêndario de Prova:</label> <samp id="data_star"></samp><br>
                                <label>Data-fim Calêndario de Prova: </label> <samp id="data_end"></samp>
                                <hr>
                                <div class="form-group">
                                    <label>Selecionar periodo</label>
                                    <select name="semestreMetrica" id="semestreMetrica" class="form-control">
                                        @isset($semestre)
                                            @foreach ($semestre as $item_semestre)
                                                <option value="{{ $item_semestre->id }}">{{ $item_semestre->display_name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-sm-6">
                                        <label>Data início</label>
                                        <input type="date" name="data_inicio" class="form-control" id="data_inicio">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Data fim</label>
                                        <input type="date" name="data_fim" class="form-control" id="data_fim">
                                    </div>
                                </div>
                                <div class="form-group col alert alert-success" role="alert" id="div_sucesso">
                                    <p id="success_message"></p>
                                </div>
                                <div class="form-group col alert alert-warning" role="alert" id="div_erro">
                                    <p id="error_message"></p>
                                </div>
                                <div class="modal-footer">
                                    <button id="submit" class="btn btn-success  addmetricaCalendario">Adicionar
                                        Data</button>
                                </div>
                            </form>
                            <div class="form-group mt-2">
                                <label>Métricas calêndarizada</label>
                                <table id="" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Data inico</th>
                                            <th>Data fim</th>
                                            <th>Semestre</th>
                                            <th colspan="2">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyMetrica_calendario"></tbody>
                                </table>
                            </div>
                        </div>

                      
                    
                   

                        <div style="display: none; box-shadow:#eef0f3 -3px -1px 6px 0px" class="bg-light"
                            id="metricaSemestralEdit"><br><br>
                            {!! Form::open(['route' => ['metrica_actualizar']]) !!}
                            
                            <input type="hidden" name="metrica_id_edit" id="metrica_id_edit" value="">
                                <div class="form-group col">
                                    <label>Nome da Métrica</label>
                                    <input required type="text" name="nome_metrica_edit" class="form-control"
                                        id="nome_metrica_edit">
                                </div>
                                <div class="form-group col">
                                    <label>Percentagem da Métrica</label>
                                    <input required type="number" min="0" max="100" name="percentagem_edit"
                                        id="percentagem_edit" class="form-control">
                                </div>
                                <div class="form-group col">
                                    <label>Tipo de Métrica</label>
                                    <select name="tipo_metrica_edit" id="tipo_metrica_edit" class="form-control">
                                        <option value=""></option>

                                    </select>
                                </div>
                              
                              
                               
                                    <button type="submit" name="adc_metrica" class="btn btn-success">Editar
                                        Métrica</button>
                                        {!! Form::close() !!}
                                        <button id="close_editar_dois" class="btn btn-secondary">Cancelar</button>
                                        
                         
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancelarMetrica" class="btn btn-secondary"
                        data-dismiss="modal">Cancelar</button>
                    {!! Form::open(['route' => ['avaliacao.concluir_avaliacao']]) !!}
                    <input type="hidden" name="idAvaliacao" id="idAvaliacao" value="">
                    <button type="submit" class="btn btn-success">Concluir Avaliação</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {{-- Modal para visualizar as metricas de uma avaliacao --}}
    <div class="modal fade " id="showMetrica" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloVerMetricas"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid row">
                    <div class="col">
                        <label>Data-inicio Calêndario:</label> <samp id="dataPrava_inicio"></samp><br>
                        <label>Data-fim Calêndario: </label> <samp id="dataPrava_fim"></samp>
                    </div>
                    <div class="col"></div>
                    <div class="float-end col-4 mb-3">
                        <label>Selecione o periodo</label>
                        <select name="semestre" id="semestre" class="selectpicker form-control form-control-sm"
                            style="width: 100%; !important">
                            @isset($semestre)
                                @foreach ($semestre as $item_semestre)
                                    <option value="{{ $item_semestre->id }}">{{ $item_semestre->display_name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                </div>
                <div class="modal-body">
                    <input type="hidden" name="avaliacao_id" id="avaliacao_id_z" value="">
                    <div class="form-group col table-responsive">
                        <table id="" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Percentagem</th>
                                    <th>Tipo de Métrica</th>
                                    <th>Data Inicio</th>
                                    <th>Data Final</th>
                                    <th colspan="2">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="showData"></tbody>
                        </table>
                        <div>
                            <table id="" class="table table-striped table-hover">
                                <tbody id="show_oa"> </tbody>
                            </table>
                        </div>
                        <br><br>
                        <div class="form-group col alert alert-success" role="alert" id="divSucesso_metrica">
                            <p id="p_success_message"></p>
                        </div>
                        <div class="form-group col alert alert-danger" role="alert" id="divError_metrica">
                            <p id="p_error_message"></p>
                        </div>
                        <div style="" class="container-fluid bg-light m-0 p-3" id="formularioEditaMetrica">
                            <form id="avaliacao_editarMetrica">
                           
                                <input type="hidden" name="id_avaliacao" id="id_avaliacao" value="">
                                <input type="hidden" name="id_metrica" id="id_metrica" value="">
                                <input type="hidden" name="id_semestre" id="id_semestre" value="">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="exampleInputEmail1">Nome da métrica</label>
                                        <input required type="text" class="form-control" name="nomeMetrica"
                                            id="nomeMetrica" aria-describedby="emailHelp" value="" placeholder="">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="exampleInputEmail1">Percentagem</label>
                                        <input required type="number" min="0" max="100"
                                            class="form-control" name="percentagem_metrica" id="percentagem_metrica"
                                            aria-describedby="emailHelp" value="" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de métrica</label>
                                    <select name="tipos_metricas" name="" id="tipos_metricas"
                                        class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label id="labeldataInicio_metrica" for="exampleInputEmail1">Data inicio</label>
                                        <input type="date" class="form-control" name="dataInicio_metrica"
                                            id="dataInicio_metrica" aria-describedby="emailHelp" value=""
                                            placeholder="">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label id="labeldataFim_metrica" for="exampleInputEmail1">Data fim</label>
                                        <input type="date" class="form-control" name="dataFim_metrica"
                                            id="dataFim_metrica" aria-describedby="emailHelp" value=""
                                            placeholder="">
                                    </div>
                                </div>
                                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                                        <button id="submit" class="btn btn-success">Editar</button>
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Third group">
                                        <a id="close_editar" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div style="" class="container-fluid bg-light m-0 p-3" id="formularioEditaMetricaSC">
                            <form id="avaliacao_editarMetricaSC">
                            <h2 id="sc">Segunda Chamada<h2>
                          
                                <input type="hidden" name="id_metrica_sc" id="id_metrica_sc" value="">
                
                                <input type="hidden" name="segunda_chamada" id="segunda_chamada" value="">
                               
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label id="labeldataInicio_metrica" for="exampleInputEmail1">Data inicio</label>
                                        <input type="date" class="form-control" name="dataInicio_metrica"
                                            id="dataInicio_metrica_sc" aria-describedby="emailHelp" value=""
                                            placeholder="">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label id="labeldataFim_metrica" for="exampleInputEmail1">Data fim</label>
                                        <input type="date" class="form-control" name="dataFim_metrica"
                                            id="dataFim_metrica_sc" aria-describedby="emailHelp" value=""
                                            placeholder="">
                                    </div>
                                </div>
                                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                                        <button id="submit_sc" class="btn btn-success">Guardar</button>
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Third group">
                                        <a id="close_editar_sc" class="btn btn-secondary">Cancelar</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    {{-- Modal para editar uma determinada avalicao --}}
    <div class="modal fade" id="editAvaliacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Avaliação</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    {!! Form::open(['route' => ['avaliacao.atualizar']]) !!}
                    <input type="hidden" name="avaliacao_id" id="avaliacao_id_w">
                    <div class="form-group col">
                        <label>Nome da Avaliação</label>
                        <input type="text" name="nome_avaliacao" id="nome_avaliacao" class="form-control">
                    </div>
                    <div class="form-group col">
                        <label>Tipo de Avaliação</label>
                        <select name="tipo_avaliacao" id="tipo_avaliacao" class="form-control" required>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Atualizar
                            Avaliação</button>
                    </div>
                    <hr>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    @include('Avaliations::avaliacao.modal_duplicar')
@endsection
@section('scripts-new')
    @parent
    <script>
        function duplicar(element) {
            let id_avaliacao = element.getAttribute("data-user_id");
            if (id_avaliacao != "") {
                $("#id_input_avaliation").val("");
                $("#id_input_avaliation").val(id_avaliacao);
                $("#modal-copiar-avaliation").modal();
            } else {

                alert("Clique novamente no botão para poder duplicar a avaliação!")
            }
        }

        $(function() {

            // FECHAR FORMULARIO DE ABTRIBUICAO DE DATA PARA A CADA METRICA
            $("#close").click(closeFormMetrica)
            $("#cancelarMetrica").click(closeFormMetrica)

            function closeFormMetrica() {
                var nome = $(this).attr('data-id')
                $("#metricaSemestralEdit").css('display', 'none')
                $("#metricaSemestral").css('display', 'none')
                $("#linha_row").attr('class', 'row ')
                $("#associarMetrica").attr('class', 'col')
                $("#metricaSemestral").attr('class', 'bg-light col')
                $("#metricaSemestralEdit").attr('class', 'bg-light col')
                $("#modal_max").attr('class', 'modal-dialog')
            }

            $("#formularioEditaMetrica").hide()
            $("#formularioEditaMetricaSC").hide()
            // $("#metricaSemestral").hide()
            var lective_year = $("#lective_year").val();
            tabela(lective_year);
            new_btn(lective_year);

            $("#lective_year").change(function() {
                var lective_year = $("#lective_year").val();
                new_btn(lective_year);
                $('#avaliacao-tables').DataTable().clear().destroy();
                tabela(lective_year);
            });

            //LIMPAR OS ALERTS DE ERRO E SUCESSO
            $('#div_success').hide();
            $('#div_error').hide();
            $('#div_sucesso').hide();
            $('#div_erro').hide();
            $('#div_success_metrica').hide();
            $('#div_error_metrica').hide();

            $('#divSucesso_metrica').hide();
            $('#divError_metrica').hide();

            function new_btn(year) {
                if (year == 6) {
                    $("#nova_avalicao").hide();
                } else {
                    let route = "/pt/avaliations/create-type/" + year;
                    document.getElementById("nova_avalicao").setAttribute('href', route);
                    $("#nova_avalicao").show();
                }
                $("#tipo_metrica").empty();
                message($("#tipo_metrica"), year);

            }

            function tabela(anoLectivo) {
                $('#avaliacao-tables').DataTable({
                    ajax: "/avaliations/avaliacao_av_ajax/" + anoLectivo,
                    buttons: ['colvis', 'excel',{
                        text: '<i class="fas fa-plus-square"></i> Criar nova avaliação',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        //attr: { id: 'nova_avalicao'},
                        action: function(e, dt, node, config) {
                          window.open($('#nova_avalicao').attr('href'), "_blank");
                        }
                    },],
                    columns: [{
                            name: 'nome',
                            data: 'nome'
                        },
                        {
                            name: 'ta_nome',
                            data: 'ta_nome',
                            searchable: false
                        },
                        {
                            name: 'created_by',
                            data: 'created_by'
                        },
                        {
                            name: 'updated_by',
                            data: 'updated_by'
                        },
                        {
                            name: 'created_at',
                            data: 'created_at'
                        },
                        {
                            name: 'updated_at',
                            data: 'updated_at'
                        },
                        {
                            name: 'actions',
                            data: 'actions'
                        }
                    ],
                    "lengthMenu": [
                        [10, 50, 100, -1],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                })

            }

            //Funcao para carregar as Metricas No modal (ONDE SE CRIA METRICA)
            function call(id) {
                //Limpar a tabela sempre que for chamada
                var id_anoLectivo = $("#lective_year").val();

                $("#bodyData tr").empty();
                var url = "{{ URL('avaliacao_metrica.fetch') }}";
                $.ajax({
                    url: "/avaliations/avaliacao_metrica_fetch/" + id + "/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function(dataResult) {
                        var resultData = dataResult.data;
                        var bodyData = '';
                        var resultModel = dataResult.model;
                        var i = 1;

                        if (resultData.length > 0) {
                            $("#tilulo_avaliacao").text(dataResult.data[0].avalicao_nome + " - ")
                        }

                        if (resultModel.length > 0) {
                            $("#semcaledario").css('display', 'none')
                        } else {
                            $("#semcaledario").css('display', 'block')
                        }
                        $.each(resultData, function(index, row) {
                            var editUrl = url + '/' + row.id + "/edit";
                            if (row.calendario == 1) {
                                bodyData += "<tr>"
                                bodyData += "<td>" + row.metrica_nome + "</td><td>" + row
                                    .metrica_percentagem + "% </td><td>" + row.avalicao_nome +
                                    "</td><td>" + row.tipo_metricas_nome +
                                    "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group mr-2' role='group' aria-label='Second group'><p data-id=" +
                                    row.metrica_id +
                                    " class='btn btn-danger deleteMetricaInsert' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></p></div> </div></td>"
                                bodyData += "</tr>"
                            } else {
                                bodyData += "<tr>"
                                bodyData += "<td>" + row.metrica_nome + "</td><td>" + row
                                    .metrica_percentagem + "% </td><td>" + row.avalicao_nome +
                                    "</td><td>" + row.tipo_metricas_nome +
                                    "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group mr-2' role='group' aria-label='Second group'><p data-id=" +
                                    row.metrica_id +
                                    " class='btn btn-danger deleteMetricaInsert' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></p></div> <div class='btn-group' role='group' aria-label='Third group'><p onClick='associarSemestre(" +
                                    row.metrica_id + ")' data-id=" + row.avalicao_nome +
                                    " class='btn btn-dark' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-calendar-alt'></i></p></div></div></td>"
                                bodyData += "</tr>"
                            }

                        })
                        $("#bodyData").append(bodyData);

                    },
                    error: function(dataResult) {

                    }
                });
            }
            //Funcao para carregar as Metricas No modal (ONDE SE VISUALIZA METRICA)
            function callShow(id) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#showData tr").remove();
                var id_anoLectivo = $("#lective_year").val();
                var url = "{{ URL('avaliacao_metrica.fetch') }}";
                $.ajax({
                    url: "/avaliations/avaliacao_metrica_fetch/" + id + "/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function(dataResult) {
                        var resultData = dataResult.data;
                        var showData = '';
                        var i = 1;
                        $.each(resultData, function(index, row) {

                            var editUrl = url + '/' + row.id + "/edit";
                            showData += "<tr>"
                            showData += "<td>" + row.metrica_nome +
                                "</td><td>" + row.metrica_percentagem +
                                "% </td><td>" + row.avalicao_nome +
                                "</td><td>" + row.tipo_metricas_nome +
                                "</td><td>" + row.data_inicio +
                                "</td><td>" + row.data_fim +
                                "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group me-2 mr-2' role='group' aria-label='Second group'><button data-id=" +
                                row
                                .metrica_id +
                                " class='btn btn-sm btn-danger deleteMetrica' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></button></div><div class='btn-group' role='group' aria-label='Third  group'><button data-id=" +
                                row
                                .metrica_id +
                                " class='btn btn-sm btn-warning editarMetrica' data-token='{{ csrf_token() }}'><i class='fas  fa-edit'></i></button></div> </div></td>"
                            //showData+="<td>"+row.metrica_nome+"</td><td>"+row.metrica_percentagem+"% </td><td>"+row.avalicao_nome+"</td><td>"+row.tipo_metricas_nome+"</td><td><a href='/avaliations/delete_metrica/"+row.metrica_id+"' class='btn btn-sm btn-danger deleteMetrica' id='deleteMetrica'><i class='fas fa-trash-alt'></i></a></td>"

                            showData += "</tr>"

                        })
                        $("#showData").append(showData);

                    },
                    error: function(dataResult) {
                        // alert('error' + result);
                    }
                });
            }

            //ENVIAR DADOS DO FORMULARIO PARA CONTROLLER VIA AJAX
            $('#metricaForm').on('submit', function(event) {
                event.preventDefault();
                nome_metrica = $('#nome_metrica').val();
                percentagem = $('#percentagem').val();
                tipo_avaliacao = $('#tipo_metrica').val();
                avaliacao_id = $('#avaliacao_id_x').val();
                let checkbox = document.getElementById('outrasAvaliacao')
                if (checkbox.checked) {
                    outrasAvaliacao = 1
                } else {
                    outrasAvaliacao = 0
                }
                $.ajax({
                    url: "{{ route('metrica.store') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        avaliacao_id: avaliacao_id,
                        nome_metrica: nome_metrica,
                        percentagem: percentagem,
                        tipo_avaliacao: tipo_avaliacao,
                        outrasAvaliacao: outrasAvaliacao,
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#div_success').show();
                            $('#div_error').hide();
                            $('#div_success').text(response.success);
                            call(avaliacao_id);
                            setTimeout(function() {
                                $('#div_success').fadeOut('slow');
                            }, 4000);
                        } else {
                            $('#div_error').show();
                            $('#div_success').hide();
                            $('#div_error').text(response.error);
                            call(avaliacao_id);
                            setTimeout(function() {
                                $('#div_error').fadeOut('slow');
                            }, 4000);

                        }
                    },

                });

            });

            //METDO QUE REGISTA METRICA CALENDARIZADA.
            $("#inserirDataMetrica").on('submit', function(e) {
                e.preventDefault();
                data_inicio = $('#data_inicio').val();
                data_fim = $('#data_fim').val();
                metrica_id = $('#metrica_id').val();
                semestre_id = $('#semestreMetrica').val();
                id_avaliacao = $('#avaliacao_id_x').val();

                $.ajax({
                    url: "{{ route('add_metricaCalendario.registo') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        data_inicio: data_inicio,
                        data_fim: data_fim,
                        metrica_id: metrica_id,
                        semestre_id: semestre_id,
                        id_avaliacao: id_avaliacao
                    },
                    success: function(data) {
                        if (data.success) {
                            $('#div_sucesso').show();
                            $('#div_erro').hide();
                            $('#div_sucesso').text(data.success);
                            setTimeout(function() {
                                $('#div_sucesso').fadeOut('slow');
                            }, 4000);
                            associarSemestre(metrica_id)
                        } else {
                            $('#div_erro').show();
                            $('#div_sucesso').hide();
                            $('#div_erro').text(data.error);

                            setTimeout(function() {
                                $('#div_erro').fadeOut('slow');
                            }, 4000);

                        };

                    },

                });
            });

            //Passar o ID da avaliacao no campo de texto
            //Enviar o ID da avaliação como parametro da rota
            //listar metricas associadas a uma determinada avaliacao
            //TODO: criar uma funcção nova e adicionar no botao que vai permitir apagar a metrica,
            //essa função vai usar requesiçao ajax
            $('#insertMetrica').on('show.bs.modal', function(e) {

                var user_id = $(e.relatedTarget).data('user_id');
                var url = "{{ URL('avaliacao_metrica.fetch') }}";
                var id_anoLectivo = $("#lective_year").val();


                $('#avaliacao_id_x').val(user_id);
                $('#avaliacao_id_y').val(user_id);

                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#bodyData tr").remove();
                $.ajax({
                    url: "/avaliations/avaliacao_metrica_fetch/" + user_id + "/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {

                        $('#idAvaliacao').val(dataResult.avaliacao_id);

                        var resultData = dataResult.data;
                        var resultModel = dataResult.model;
                        var bodyData = '';
                        var i = 1;
                        if (resultData.length > 0) {
                            $("#tilulo_avaliacao").text(dataResult.data[0].avalicao_nome +
                                " - ")
                        }
                        if (resultModel.length > 0) {
                            $("#semcaledario").css('display', 'none')
                        } else {
                            $("#semcaledario").css('display', 'block')
                        }
                        $.each(resultData, function(index, row) {

                            var editUrl = url + '/' + row.id + "/edit";
                            if (row.calendario == 1) {
                                bodyData += "<tr>"
                                bodyData += "<td>" + row.metrica_nome + "</td><td>" +
                                    row.metrica_percentagem + "% </td><td>" + row
                                    .avalicao_nome + "</td><td>" + row
                                    .tipo_metricas_nome +
                                    "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group mr-2' role='group' aria-label='Second group'><p data-id=" +
                                    row.metrica_id +
                                    " class='btn btn-danger deleteMetricaInsert' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></p></div> </div></td>"
                                bodyData += "</tr>"
                            } else {
                                bodyData += "<tr>"
                                bodyData += "<td>" + row.metrica_nome + "</td><td>" +
                                    row.metrica_percentagem + "% </td><td>" + row
                                    .avalicao_nome + "</td><td>" + row
                                    .tipo_metricas_nome +
                                    "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group mr-2' role='group' aria-label='Second group'><p  onClick='editMetrica(" +
                                    row.metrica_id + ")' data-id=" +
                                    row.metrica_id +
                                    " class='btn btn-warning btn-sm' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-edit'></i></p></div> <div class='btn-group mr-2' role='group' aria-label='Second group'><p data-id=" +
                                    row.metrica_id +
                                    " class='btn btn-danger deleteMetricaInsert' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></p></div> <div class='btn-group' role='group' aria-label='Third group'><p onClick='associarSemestre(" +
                                    row.metrica_id + ")' data-id=" + row.avalicao_nome +
                                    " class='btn btn-dark' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-calendar-alt'></i></p></div></div></td>"
                                bodyData += "</tr>"
                            }

                        })
                        $("#bodyData").append(bodyData);

                    },
                    error: function(dataResult) {
                        // alert('error' + result);
                    }
                });
            });

            //APAGAR METRICA VIA AJAX (SHOW)
            $(document).on("click", ".deleteMetrica", function() {
                avaliacao_id = $('#avaliacao_id_z').val();
                var id = $(this).data("id");
                var token = $(this).data("token");
                if (confirm("Deseja excluir esta métrica")) {
                    $.ajax({
                        url: "/avaliations/delete_metrica/" + id,
                        type: 'delete',
                        dataType: "JSON",
                        data: {
                            "id": id,
                            "_method": 'DELETE',
                            "_token": token,
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#div_success_metrica').show();
                                $('#div_error_metrica').hide();
                                $('#div_success_metrica').text(response.success);
                                callShow(avaliacao_id);
                                setTimeout(function() {
                                    $('#div_success_metrica').fadeOut('slow');
                                }, 4000);
                            } else {
                                $('#div_error_metrica').show();
                                $('#div_success_metrica').hide();
                                $('#div_error_metrica').text(response.error);
                                callShow(avaliacao_id);
                                setTimeout(function() {
                                    $('#div_error_metrica').fadeOut('slow');
                                }, 4000);
                            }
                        }
                    });
                }
            });

            //APAGAR METRICA VIA AJAX (INSERIR METRICA) 
            //DUPLIQUEI PORQUE O AVALIACAO_ID É DIFERENTE
            $(document).on("click", ".deleteMetricaInsert", function() {
                avaliacao_id = $('#avaliacao_id_x').val();
                var id = $(this).data("id");
                var token = $(this).data("token");
                if (confirm("Deseja excluir esta métrica")) {
                    $.ajax({
                        url: "/avaliations/delete_metrica/" + id,
                        type: 'delete',
                        dataType: "JSON",
                        data: {
                            "id": id,
                            "_method": 'DELETE',
                            "_token": token,
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#div_success').show();
                                $('#div_error').hide();
                                $('#div_success').text(response.success);
                                call(avaliacao_id);
                                $("input[name='adc_metrica']").attr("disabled", true);
                                setTimeout(function() {
                                    $('#div_success').fadeOut('slow');
                                }, 4000);
                            } else {
                                $('#div_error').show();
                                $('#div_success').hide();
                                $('#div_error').text(response.error);
                                call(avaliacao_id);
                                setTimeout(function() {
                                    $('#div_error').fadeOut('slow');
                                }, 4000);
                            }
                        }
                    });
                }
            });

            // FUNCAO QUE LISTA AS MESTRICA CALENDARIZADA POR UM DETERMINADO SEMESTRE
            $('#showMetrica').on('show.bs.modal', function(e) {
                var id_avaliacao = $(e.relatedTarget).data('user_id');
                var url = "{{ URL('avaliacao_metrica.fetch_metricaSemestre') }}";
                var id_semestre = $("#semestre").val()
                var id_anoLectivo = $("#lective_year").val();
                $("#avaliacao_id_y").val(id_avaliacao);
                $("#formularioEditaMetrica").slideUp(990)
                $("#formularioEditaMetricaSC").slideUp(990)
                $("#showData tr").remove();
                $.ajax({
                    url: "/avaliations/avaliacao_fetch_metricaSemestre/" + id_avaliacao + "/" +
                        id_semestre,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function(dataResult) {

                        var resultData = dataResult.model;
                        var resultAvaliacao_OA = dataResult.avaliavao_OA;
                        var resultcalendario = dataResult.data;
                        var showData = '';
                        var show_OA = '';
                        var i = 1;
                        var metrica_oa = 0;
                        var data_OA = "Sem data"

                        if (resultData.length > 0) {
                            $("#tituloVerMetricas").text("Métricas da Avaliação - " +
                                resultData[0].nome_avaliacao)
                            $("#dataPrava_inicio").text(resultData[0].data_starProva)
                            $("#dataPrava_fim").text(resultData[0].data_endProva)
                            $.each(resultData, function(index, row) {
                                var editUrl = url + '/' + row.id + "/edit";
                                showData += "<tr>"
                                showData += "<td>" + row.nome_metrica +
                                    "</td><td>" + row.percentagem_metrica +
                                    "%</td><td>" + row.nome_tipoMetrica +
                                    "</td><td>" + row.data_starMetrica +
                                    "</td><td>" + row.data_endMetrica +
                                    "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group' role='group' aria-label='Third  group'><button data-id=" +
                                    row
                                    .calendario +
                                    "  onClick='editarMetrica(" + row.id_calendMetrica +
                                    ")' id='editarMetrica' class='btn btn-sm btn-warning ' data-token='{{ csrf_token() }}'><i class='fas  fa-edit'></i></button> <button data-id=" +
                                    row
                                    .calendario +
                                      "  onClick='editarMetricaSegundaChamada(" + row.id_calendMetrica +
                                     ")'  id='editarMetricaSegundaChamada' class='btn btn-sm btn-success ' data-token='{{ csrf_token() }}'><i class='fas  fa-solid fa-2'></i></button>  </div> </div></td>"
                                  
                                  
                                showData += "</tr>"
                            })

                        } else {
                            if (resultcalendario.length > 0) {
                                $("#tituloVerMetricas").text("Métricas da Avaliação - " +
                                    resultcalendario[0].nome_avaliacao)
                                $("#dataPrava_inicio").text(resultcalendario[0].data_starProva)
                                $("#dataPrava_fim").text(resultcalendario[0].data_endProva)
                                showData += "<tr>"
                                showData += "<td class='text-center'> Nenhum Registo </td>"
                                showData += "</tr>"
                            } else {
                                $("#tituloVerMetricas").text("Métricas da Avaliação")
                                $("#dataPrava_inicio").text("Nunhum registo")
                                $("#dataPrava_fim").text("Nunhum registo")
                                showData += "<tr>"
                                showData += "<td class='text-center'> Nenhum Registo </td>"
                                showData += "</tr>"
                            }

                        }
                        $("#showData").append(showData);
                        if (resultAvaliacao_OA.length > 0) {
                            show_OA += "<tr>"
                            show_OA += "<td>" + resultAvaliacao_OA[0].nome + "</td><td>" +
                                resultAvaliacao_OA[0].percentagem + "%</td><td>" +
                                resultAvaliacao_OA[0].abreviatura + "</td><td>" + data_OA +
                                "</td><td>" + data_OA +
                                "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group' role='group' aria-label='Third  group'><button data-id=" +
                                resultAvaliacao_OA[0].calendario +
                                " onClick='editarMetrica(" + metrica_oa +
                                ")' id='editarMetrica' class='btn btn-sm btn-warning ' data-token='{{ csrf_token() }}'><i class='fas  fa-edit'></i></button></div> </div></td>";

                            show_OA += "</td>"
                            $("#showData").append(show_OA);
                        }
                    },
                    error: function(dataResult) {
                        // alert('error' + result);
                    }
                });
            });

            //função para retornar o nome da avaliação selecionada
            $('#editAvaliacao').on('show.bs.modal', function(e) {

                var user_id = $(e.relatedTarget).data('user_id');
                var url = "{{ URL('avaliacao.single_fetch') }}";

                $("#nome_avaliacao").val('');
                $('#avaliacao_id_w').val(user_id);

                $.ajax({
                    url: "/avaliations/fetch_single_avaliacao/" + user_id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        var resultData = dataResult.data;
                        var nome_avaliacao = '';
                        var i = 1;
                        $.each(resultData, function(index, row) {
                            var editUrl = url + '/' + row.id + "/edit";
                            // nome_avaliacao+="value="+2+">";
                            $("#nome_avaliacao").val(row.avaliacao_nome);
                        })
                        $("#nome_avaliacao").append(nome_avaliacao);
                    },
                    error: function(dataResult) {
                        // alert('error' + result);
                    }
                });
            });

        });

        //Funcao para listar todos os tipos de metrica no select
        function message($param, anoLectivo) {
            var url = "{{ URL('tipo_metrica.fetch') }}";
            $.ajax({
                url: "/avaliations/tipo_metrica_fetch/" + anoLectivo,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    var resultData = dataResult.data;
                    var bodyData = '';
                    var i = 1;
                    $.each(resultData, function(index, row) {
                        var editUrl = url + '/' + row.id + "/edit";
                        bodyData += "<option value=" + row.id + ">" + row.nome + "</option>";
                    })
                    $("#tipo_metrica").append(bodyData);
                    $("#tipos_metricas").append(bodyData);
                }
            });
        }

        // FUNCAO QUE MOSTRAS AS MESTRICA DE ACORDO A SEMESTRE, NO VER METRICA DE UMA AVALIACAO
        $("#semestre").change(function() {
            $("#formularioEditaMetrica").slideUp(200)
            $("#formularioEditaMetricaSC").slideUp(200)
            var id_semestre = $("#semestre").val()
            var url = "{{ URL('metricaSemestre_calendario.fetch') }}";
            var id_avaliacao = $('#avaliacao_id_y').val();
            $.ajax({
                url: "/avaliations/avaliacao_metricaSemestre_calendario/" + id_avaliacao + "/" +
                    id_semestre,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    $("#showData").empty();

                    var resultData = dataResult.model;
                    var resultcalendario = dataResult.data;
                    var showData = '';
                    var i = 1;
                    if (resultData.length > 0) {
                        $("#tituloVerMetricas").text("Métricas da Avaliação - " + resultData[0]
                            .nome_avaliacao)
                        $("#dataPrava_inicio").text(resultData[0].data_starProva)
                        $("#dataPrava_fim").text(resultData[0].data_endProva)
                        $.each(resultData, function(index, row) {
                            var editUrl = url + '/' + row.id + "/edit";
                            showData += "<tr>"
                            showData += "<td>" + row.nome_metrica +
                                "</td><td>" + row.percentagem_metrica +
                                "%</td><td>" + row.nome_tipoMetrica +
                                "</td><td>" + row.data_starMetrica +
                                "</td><td>" + row.data_endMetrica +
                                "</td><td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group' role='group' aria-label='Third  group'><button data-id=" +
                                row.id_calendMetrica +
                                "  onClick='editarMetrica(" + row.id_calendMetrica +
                                ")' id='editarMetrica' class='btn btn-sm btn-warning ' data-token='{{ csrf_token() }}'><i class='fas  fa-edit'></i></button></div> </div></td>"
                            showData += "</tr>"
                        })

                    } else {
                        if (resultcalendario.length > 0) {
                            $("#tituloVerMetricas").text("Métricas da Avaliação - " + resultcalendario[
                                0].nome_avaliacao)
                            $("#dataPrava_inicio").text(resultcalendario[0].data_starProva)
                            $("#dataPrava_fim").text(resultcalendario[0].data_endProva)
                            showData += "<tr>"
                            showData += "<td class='text-center'> Nenhum Registo </td>"
                            showData += "</tr>"
                        } else {
                            $("#tituloVerMetricas").text("Métricas da Avaliação")
                            $("#dataPrava_inicio").text("Nunhum registo")
                            $("#dataPrava_fim").text("Nunhum registo")
                        }
                    }
                    $("#showData").empty()
                    $("#showData").append(showData);
                },
                error: function(dataResult) {}
            });
        })

        // FUNCÃO PARA EDITAR A METRICA 
        $("#close_editar").click(function() {
            $("#formularioEditaMetrica").slideUp(1000);
           
        })
        $("#close_editar_sc").click(function() {
          
            $("#formularioEditaMetricaSC").slideUp(1000);
        })

        $("#avaliacao_editarMetrica").on('submit', function(e) {
            e.preventDefault();
            nomeMetrica = $("#nomeMetrica").val()
            percentagem_metrica = $("#percentagem_metrica").val()
            tipos_metricas = $("#tipos_metricas").val()
            dataInicio_metrica = $("#dataInicio_metrica").val()
            dataFim_metrica = $("#dataFim_metrica").val()
            id_avaliacao = $("#avaliacao_id_y").val()
            id_metricaCalendario = $("#id_metrica").val();
            semestre = $("#semestre").val();
            if (tipos_metricas == "") {
                $('#divError_metrica').show();
                $('#divSucesso_metrica').hide();
                $('#divError_metrica').text("Selecionar tipo de métrica!");
                setTimeout(function() {
                    $('#divError_metrica').fadeOut('slow');
                }, 4000);
            } else {
                $('#divSucesso_metrica').hide();
                $('#divError_metrica').hide();
              
                $.ajax({
                    url: "{{ route('avaliacao.editarMetrica') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        nomeMetrica: nomeMetrica,
                        percentagem_metrica: percentagem_metrica,
                        tipos_metricas: tipos_metricas,
                        dataInicio_metrica: dataInicio_metrica,
                        dataFim_metrica: dataFim_metrica,
                        id_metricaCalendario: id_metricaCalendario,
                        id_avaliacao: id_avaliacao,
                        semestre: semestre          
                    },
                    success: function(data) {

                        if (data.success) {
                            $('#divSucesso_metrica').show();
                            $('#divError_metrica').hide();
                            $('#divSucesso_metrica').text(data.success);
                            setTimeout(function() {
                                $('#divSucesso_metrica').fadeOut('slow');
                                editarMetrica(id_metricaCalendario)
                            }, 4000);

                        } else {
                            $('#divError_metrica').show();
                            $('#divSucesso_metrica').hide();
                            $('#divError_metrica').text(data.error);
                            setTimeout(function() {
                                $('#divError_metrica').fadeOut('slow');
                            }, 4000);

                        };

                    },
                });
            }
        })

        $("#avaliacao_editarMetricaSC").on('submit', function(e) {
            e.preventDefault();
          
            var dataInicio_metrica_sc = $("#dataInicio_metrica_sc").val()
            var dataFim_metrica_sc = $("#dataFim_metrica_sc").val()
            var segunda_chamada =  $("#segunda_chamada").val();
            var id_metricaCalendario_sc = $("#id_metrica_sc").val();
           
         console.log('divayh:',id_metricaCalendario_sc)
                $('#divSucesso_metrica').hide();
                $('#divError_metrica').hide();
              
                $.ajax({
                    url: "{{ route('avaliacao.storeCalendMetricaSC') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                       
                        dataInicio_metrica: dataInicio_metrica_sc,
                        dataFim_metrica: dataFim_metrica_sc,
                        id_metricaCalendario: id_metricaCalendario_sc,
                        segunda_chamada: segunda_chamada   
                    },
                    success: function(data) {

                        if (data.success) {
                            $('#divSucesso_metrica').show();
                            $('#divError_metrica').hide();
                            $('#divSucesso_metrica').text(data.success);
                            setTimeout(function() {
                                $('#divSucesso_metrica').fadeOut('slow');
                                editarMetricaSegundaChamada(id_metricaCalendario)
                            }, 2000);

                        } else {
                            $('#divError_metrica').show();
                            $('#divSucesso_metrica').hide();
                            $('#divError_metrica').text(data.error);
                            setTimeout(function() {
                                $('#divError_metrica').fadeOut('slow');
                            }, 4000);

                        };

                    },
                });
         
        })

        function editarMetrica(id_metrica) {
            $("#formularioEditaMetricaSC").slideUp(1000);
            $("#formularioEditaMetrica").slideUp(990)
            $("#formularioEditaMetrica").slideDown(990)
            var id_avaliacao = $('#avaliacao_id_y').val();
            var semestre = $('#semestre').val();
            var url = "{{ URL('editarMetrica_calendarizada.edit') }}";
            $.ajax({
                url: "/avaliations/editarMetrica_calendarizada/" + id_avaliacao + "/" + id_metrica + "/" + semestre,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',

                success: function(dataResult) {
                   
                    
                    // console.log(dataResult);
                    if (dataResult.data.length > 0) {
                        var resultData = dataResult.data
                       
                       
    
                            $.each(resultData, function(index, row) {
                            $("#dataInicio_metrica").show();
                            $("#dataFim_metrica").show();
                            $("#labeldataInicio_metrica").show();
                            $("#labeldataFim_metrica").show();
                            $("#metrica").val(row.id_metrica);
                            $("#id_metrica").val(row.id_calendMetrica);
                            $("#nomeMetrica").val(row.nome_metrica);
                          
                            $("#percentagem_metrica").val(row.percentagem_metrica);
                          
                            $("#dataInicio_metrica").val(row.data_starMetrica);
                            $("#dataFim_metrica").val(row.data_endMetrica);
                        })
                    }
                       
                    

                    if (dataResult.data_oa.length > 0) {
                        var resultData_oa = dataResult.data_oa
                        $.each(resultData_oa, function(index, row) {
                            $("#id_metrica").val(0);
                            $("#nomeMetrica").val(row.nome);
                            $("#percentagem_metrica").val(row.percentagem);
                            $("#dataInicio_metrica").hide();
                           
                            $("#dataFim_metrica").hide();
                            $("#labeldataInicio_metrica").hide();
                            $("#labeldataFim_metrica").hide();
                        })
                    }
                },
                error: function(dataResult) {}
            });
        }

        function editarMetricaSegundaChamada(id_metrica) {
            $("#formularioEditaMetrica").slideUp(1000);
                $("#formularioEditaMetricaSC").slideUp(990)
                $("#formularioEditaMetricaSC").slideDown(990)

                var semestre = $('#semestre').val();

                console.log('oiee'+semestre)
            
                $.ajax({
                    url: "/avaliations/get_metrica_calendarizada_segunda_chamada/" + id_metrica+ "/"+semestre ,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
    
                    success: function(dataResult) {
    
                        var data = dataResult.model;
                        var metrica = dataResult.metrica;
                        var segunda_chamada = null;
                        var id_metrica_calend = metrica.id_calendMetrica;
                        $("#id_metrica_sc").val(id_metrica_calend)
                        
                    if(metrica != null){

                        if (data != null) {
                            var row = dataResult.data
                           
                                $("#sc").text('Segunda Chamada da ' + metrica.nome_metrica)
    
                                segunda_chamada = data.id;
        
                                $("#dataInicio_metrica_sc").show();
                                $("#dataFim_metrica_sc").show();
    
                                $("#dataFim_metrica_sc").val(data.data_fim);
                                $("#dataInicio_metrica_sc").val(data.data_inicio);
    
                                $("#labeldataInicio_metrica_sc").show();
                                $("#labeldataFim_metrica_sc").show();  

                              
                        }
                        else{
                             $("#sc").text('Segunda Chamada da ' + metrica.nome_metrica)
    
                            $("#dataInicio_metrica_sc").show();
                                $("#dataFim_metrica_sc").show();
    
                                $("#labeldataInicio_metrica_sc").show();
                                $("#labeldataFim_metrica_sc").show();  
                        }
    
                        $("#segunda_chamada").val(segunda_chamada);
                     
                      
                    }
                    else{

                        $('#divError_metrica').show();
                        $('#divError_metrica').text("Nenhum calendário associado a esta métrica encontrado!");

                    setTimeout(function() {
                    $('#divError_metrica').fadeOut('slow');
                }, 4000);
                    }

                    },
                    error: function(dataResult) {}
                });
            }

        // FUNCAO QUE MOSTRAS AS MESTRICA DE ACORDO A SEMESTRE
        $("#semestreMetrica").change(function() {
            var id_semestre = $("#semestreMetrica").val()
            var id_metrica = $("#metrica_id").val();
            var url = "{{ URL('metricaSemestre_calendario.fetch') }}";
            $("#bodyMetrica_calendario").empty();
            $("#data_star").text("")
            $("#data_end").text("")
            associarSemestre(id_metrica);

        })

        // FUNÇÃO PARA  UMA METRICA COM A DATA GLOBAL DA SUA AVALIAÇÃO COM O SEU SEMESTRE 
        function associarSemestre(id_metrica) {
            var id_avaliacao = $("#avaliacao_id_x").val();
            var url = "{{ URL('metrica_calendario.fetch') }}";
            $("#bodyMetrica_calendario").empty();
            $("#data_star").text("")
            $("#data_end").text("")
            var id_semestre = $("#semestreMetrica").val()
            $.ajax({
                url: "/avaliations/Avaliaca_metricaCalendario/" + id_metrica + "/" + id_semestre + "/" +
                    id_avaliacao,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    var resultaMetrica_calendario = dataResult.metrica_calendario;
                    var i = 1;
                    var bodyData = '';
                    $("#tilulo_metrica").text(dataResult.metrica[0].nome)

                    if (resultaMetrica_calendario.length > 0) {
                        $(".addmetricaCalendario").css('display', 'none')
                        $("#data_star").text(resultaMetrica_calendario[0].data_starProva)
                        $("#data_end").text(resultaMetrica_calendario[0].data_endProva)
                        $.each(resultaMetrica_calendario, function(index, row) {
                            var editUrl = url + '/' + row.id + "/edit";
                            bodyData += "<tr>"
                            bodyData += "<td>" + i++ + "</td> <td>" + row.data_starMetrica +
                                "</td><td>" + row.data_endMetrica + "</td><td>" + dataResult.semestre[0]
                                .nome_semestre +
                                "</td>  <td><div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'><div class='btn-group mr-2' role='group' aria-label='Second group'><bottum onClick='deleteMetricaCalendarizada(" +
                                row.id_calendMetrica + ")' data-id=" + row.id_calendMetrica +
                                " class='btn btn-danger ' style='cursor:hand;' data-token='{{ csrf_token() }}'><i class='fas fa-trash-alt'></i></bottum></div></div></td>"
                            bodyData += "</tr>"
                        })
                    } else {
                        if (dataResult.calendario_avaliacao.length > 0) {
                            $("#data_star").text(dataResult.calendario_avaliacao[0].data_starProva)
                            $("#data_end").text(dataResult.calendario_avaliacao[0].data_endProva)
                            $(".addmetricaCalendario").css('display', 'block')

                        } else {
                            $("#data_star").text("Nenhum registo")
                            $("#data_end").text("Nenhum registo")
                            $(".addmetricaCalendario").css('display', 'none')
                        }
                        bodyData += "<tr>"
                        bodyData += "<td class='text-center'>Nenhum registo</td>"
                        bodyData += "</tr>"
                    }
                    $("#bodyMetrica_calendario").append(bodyData);
                }
            });

            $("#metrica_id").val(id_metrica)
            $("#linha_row").attr('class', 'row mr-1')
            $("#associarMetrica").attr('class', 'col-sm-6')
            $("#metricaSemestral").attr('class', 'bg-light col-sm-6')
            $("#modal_max").attr('class', 'modal-dialog modal-xl')
            $("#metricaSemestral").slideUp(390)
            $("#metricaSemestral").slideDown(799)
        }






        // FUNCAO QUE DELETA METRICA CALÊNDARIZADA
        function deleteMetricaCalendarizada(id_calendMetrica) {
            var url = "{{ URL('calendMetrica.delete') }}";
            var id_metrica = $("#metrica_id").val();
           
            $.ajax({
                url: "/avaliations/delete_calendMetrica/" + id_calendMetrica,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        $('#div_sucesso').show();
                        $('#div_erro').hide();
                        $('#div_sucesso').text(data.success);
                        setTimeout(function() {
                            $('#div_sucesso').fadeOut('slow');
                        }, 4000);
                        associarSemestre(id_metrica)
                    } else {
                        $('#div_erro').show();
                        $('#div_sucesso').hide();
                        $('#div_erro').text(data.error);
                        setTimeout(function() {
                            $('#div_erro').fadeOut('slow');
                        }, 4000);
                    };
                }
            });
        }
        //Funcao para listar todos os tipos de AVALIAÇÃO no select
        function showTipoAvaliacao($param) {
            var url = "{{ URL('tipo_avaliacao.fetch') }}";
            $.ajax({
                url: "/avaliations/tipo_avaliacao_fetch",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    // console.log(dataResult);
                    var resultData = dataResult.data;
                    var bodyData = '';
                    var i = 1;
                    // console.log(dataResult.data);

                    $.each(resultData, function(index, row) {

                        var editUrl = url + '/' + row.id + "/edit";
                        bodyData += "<option value=" + row.id + ">" + row.nome +
                            "</option>";
                    })
                    $("#tipo_avaliacao").append(bodyData);
                }
            });
        }
        showTipoAvaliacao($("tipo_avaliacao"));


        //Márcia 

        function editMetrica(id_metrica) {
           console.log(id_metrica)
            $.ajax({
                url: "/avaliations/Avaliaca_metricaCalendario_edit/" + id_metrica,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                   


               var metrica = dataResult['metrica'];
                var testes = dataResult['tipo_metricas'];

                $("#metrica_id_edit").val(metrica.id)
                console.log( $("#metrica_id_edit").val());
                    $("#nome_metrica_edit").val(metrica.nome)
                     $("#percentagem_edit").val(metrica.percentagem)


                  
                     

                      testes.forEach(function(opcao) {
    $('#tipo_metrica_edit').append(
      $('<option>', {
        value: opcao.id,
        text: opcao.nome
      })
    );
  });


     $("#tipo_metrica_edit").val(metrica.tipo_metricas_id)


                    
        }

        });

       
            $("#linha_row").attr('class', 'row mr-1')
            $("#associarMetrica").attr('class', 'col-sm-6')
            $("#metricaSemestralEdit").attr('class', 'bg-light col-sm-6')
            $("#modal_max").attr('class', 'modal-dialog modal-xl')
            $("#metricaSemestralEdit").slideUp(390)
            $("#metricaSemestralEdit").slideDown(799)

 }

 $("#close_editar_dois").click(function() {
           $("#metricaSemestralEdit").css('display', 'none')
            $("#metricaSemestralEdit").attr('class', 'bg-light col')
            $("#linha_row").attr('class', 'row')
            $("#modal_max").attr('class', 'modal-dialog')
        })

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection