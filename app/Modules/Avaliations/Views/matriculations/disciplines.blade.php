<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LISTA DE MATRICULADOS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lista de matriculados</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/new_switcher.css') }}">
    <style>
        .div-anolectivo {
            width: 300px;
            padding-top: 16px;
            padding-right: 0px;
            margin-right: 15px;
        }

        #lista_tabela,
        th,
        td,
        thead {
            border: none;
        }

        th {
            background-color: #999;
            color: white;
            text-align: center;
            padding: 5px;
            font-size: 18pt;
            border-bottom: 1px solid white;
            border-right: 1px solid white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #FFF
        }

        tr:nth-child(odd) {
            background: #EEE
        }

        td {
            padding: 5px;
            font-size: 15pt;
            border-bottom: 1px solid white;
            border-right: 1px solid white;
        }

        tr:hover {
            cursor: pointer;
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
    {!! Form::open(['route' => ['ajax_dados'], 'method' => 'post', 'required' => 'required', 'target' => '_blank']) !!}

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
        <div class="col-6 p-2">
            <label for="Disciplina_id_Select">Selecione a disciplina</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina"
                tabindex="-98" required> </select>
        </div>
        <div class="col-6 p-2">
            <label for="Turma_id_Select">Selecione a turma</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="classe"
                tabindex="-98"> </select>
        </div>
        <div class="col-6 p-2">
            <label for="status_id_select">Listar estudantes devedores?</label>
            <select required class="selectpicker form-control form-control-sm" required="" id="status"
                data-actions-box="false" data-selected-text-format="values" id="status_id_select" name="status" tabindex="-98">
                <option value=""></option>
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
        </div>
    </div>

    <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">

    <input type="hidden" value="0" id="verificarSelector">
    <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank" style="width:180px;">
        <i class="fas fa-file-pdf"></i>
        <span>Gerar PDF</span>
    </button>

    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {
            //### Criar as variavel de cada selector
            var Disciplina_id_Select = $("#Disciplina_id_Select");
            var lective_year = $("#lective_year");
            var Turma_id_Select = $("#Turma_id_Select");
            var metricas_Select = $("#selector_metricas");
            var selector_ao = $("#selector_ao");
            var verificarSelector = $("#verificarSelector");
            var i = 1;
            $("#Ano_lectivo_foi").val($("#lective_year").val());

            var id_anoLectivo = $("#lective_year").val();
            //###funcao que pegar o ano lectivo selecionado                       
            $("#lective_year").bind('change keypress ', function(e) {
                id_anoLectivo = $("#lective_year").val();
                $("#Ano_lectivo_foi").val(id_anoLectivo);
                // console.log(id_anoLectivo);
                selector_ao.empty();
                $('#metrica_Oa').css("visibility", "hidden")
                selector_ao.prop('disabled', true);
                $("#metricasOA").remove();

                $("#generate-pdf").removeAttr("href");

                $.ajax({
                    url: "/avaliations/getDocenteDisciplina/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    // console.log(data);
                    if (data['disciplina'].length) {

                        //LIMPA O SELECTER
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append('<option selected="" value="0">Selecione a disciplina</option>');
                        Disciplina_id_Select.selectpicker('refresh');

                        Turma_id_Select.empty();
                        Turma_id_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                        Turma_id_Select.selectpicker('refresh');



                        $("#selector_metricas").empty();
                        metricas_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                        metricas_Select.selectpicker('refresh');
                        $('#lista').css("visibility", "hidden")
                        $('#lista_tabela').css("visibility", "hidden")
                        getDocenteDisciplina(id_anoLectivo);

                    } else {
                        //LIMPA O SELECTOR
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append('<option selected="" value="0">Selecione a disciplina</option>');
                        Disciplina_id_Select.selectpicker('refresh');

                        Turma_id_Select.empty();
                        Turma_id_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                        Turma_id_Select.selectpicker('refresh');

                        $("#selector_metricas").empty();
                        metricas_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                        metricas_Select.selectpicker('refresh');
                        // $('#lista').css("visibility","hidden")
                        $('#lista_tabela').css("visibility", "hidden")
                        $('#metrica_Oa').css("visibility", "hidden")
                        selector_ao.prop('disabled', true);
                    }
                })
            });

            //###funcao que pegar as disciplinas logo que a pagina e carregada.
            getDocenteDisciplina(id_anoLectivo);

            function getDocenteDisciplina(id_anoLectivo) {
                //var id_anoLectivo=$("#lective_year").val();
                $.ajax({
                    url: "/avaliations/getDocenteDisciplina/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    //  console.log(data)
                    if (data['disciplina'].length) {
                        $("#selector_metricas").empty();
                        // 
                        Disciplina_id_Select.prop('disabled', true);
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append('<option selected="" value="0">Selecione a disciplina</option>');
                        $.each(data['disciplina'], function(index, row) {
                            $("#Disciplina_id_Select").append('<option  value="' + data['whoIs'] +
                                ',' + row.course_id + ',' + row.discipline_id + ' ">#' + row
                                .code + '  ' + row.dt_display_name + '</option>');
                        });
                        $("#students").empty()
                        Disciplina_id_Select.prop('disabled', false);
                        Disciplina_id_Select.selectpicker('refresh');
                    } else {
                        Disciplina_id_Select.empty();
                        Disciplina_id_Select.prop('disabled', true);
                        console.log("sem dados para este ano lectivo")
                    }
                });
            }
            //Evento de mudança na select disciplina
            Disciplina_id_Select.bind('change keypress', function() {
                $("#generate-pdf").removeAttr("href");
                var id = Disciplina_id_Select.val();

                if (id == 0) {
                    console.log(id);
                    Turma_id_Select.empty();
                    Turma_id_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                    Turma_id_Select.selectpicker('refresh');
                    $('#lista_tabela').css("visibility", "hidden")
                }
                Turma(id, lective_year.val());
                $("#selector_metricas").empty();
                // metricas_Select.prop('disabled', true);
                metricas_Select.append('<option selected="" value="">Nenhum selecionado</option>');
                metricas_Select.selectpicker('refresh');
                $('#lista').css("visibility", "hidden")
                $('#lista_tabela').css("visibility", "hidden")

                $('#metrica_Oa').css("visibility", "hidden")
                $("#metricasOA").remove();
                // selector_ao.prop('disabled', true);
                // selector_ao.selectpicker('refresh');
            });
            // Funcao que pega a turma em que professor leciona uma terminda disciplina.
            function Turma(id_plano, anolectivo) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                $.ajax({
                    url: "/pt/avaliations/pautaTurma_teacher/" + id_plano + "/" + anolectivo,
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
                    $.each(data['periodo'], function(indexInArray, valueOfElement) {
                        periodo = valueOfElement;
                    });

                    if (data == 500) {
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        metricas_Select.prop('disabled', true);
                        $("#selector_metricas").empty();

                        alert("Atenção! esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma.");
                    } else {
                        if (data['whoIs'] == "super") {

                            if (data['turma'].length) {
                                Turma_id_Select.empty();
                                $("#selector_metricas").empty();
                                Turma_id_Select.append('<option selected="" value="">Selecione a turma</option>');
                                $.each(data['turma'], function(index, row) {
                                    $("#Turma_id_Select").append('<option value="' + row.id + '">' + row.display_name + '</option>');
                                });
                                Turma_id_Select.prop('disabled', false);
                                Turma_id_Select.selectpicker('refresh');
                                var periodo = data['periodo'];
                                // verificar se a oa e anual ou simestral
                                if (periodo[0] == "1_simestre" || periodo[0] == "2_simestre") {
                                    selector_ao.empty();
                                    $("#selector_ao").append( '<option selected="" value="0">Selecione a OA</option>');
                                    for (var i = 1; i <= 6; i++) {
                                        $("#selector_ao").append('<option value="' + i + ' ">OA ' + i + '</option>');
                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                } else {
                                    selector_ao.empty();
                                    $("#selector_ao").append( '<option selected="" value="0">Selecione a OA</option>');
                                    for (var index = 1; index <= 12; index++) {
                                        $("#selector_ao").append('<option value="' + index + ' ">Oa ' +index + '</option>');
                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                }
                            }
                            //chama o metodo para trazer o tratamento do loop da turma 
                        } else {
                            if (data['turma'].length) {
                                Turma_id_Select.empty();
                                $("#selector_metricas").empty();
                                Turma_id_Select.append(
                                    '<option selected="" value="">Selecione a turma</option>');
                                $.each(data['turma'], function(index, row) {
                                    $("#Turma_id_Select").append('<option value="' + row.id + '">' + row.display_name + '</option>');
                                });
                                Turma_id_Select.prop('disabled', false);
                                Turma_id_Select.selectpicker('refresh');
                                $("#students").empty()
                                var periodo = data['periodo'];

                                // verificar se a oa e anual ou simestral
                                if (periodo[0] == "1_simestre" || periodo[0] == "2_simestre") {
                                    selector_ao.empty();
                                    $("#selector_ao").append('<option selected="" value="0">Selecione a OA</option>');
                                    for (var i = 1; i <= 6; i++) {
                                        $("#selector_ao").append('<option value="' + i + ' ">OA ' + i +'</option>');
                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                } else {
                                    selector_ao.empty();
                                    $("#selector_ao").append('<option selected="" value="0">Selecione a OA</option>');
                                    for (var index = 1; index <= 12; index++) {
                                        $("#selector_ao").append('<option value="' + index + ' ">Oa ' +index + '</option>');
                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                }
                            } else {
                                Turma_id_Select.empty();
                                Turma_id_Select.prop('disabled', true);
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
