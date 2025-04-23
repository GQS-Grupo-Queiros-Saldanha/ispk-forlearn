<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'EXIBIR AVALIAÇÃO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Exibir avaliação</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
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
    {!! Form::open(['route' => ['store_final_grade']]) !!}
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
            <label>Selecione a disciplina</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Disciplina_id_Select" data-actions-box="false" data-selected-text-format="values" name="disciplina"
                tabindex="-98">
            </select>
        </div>
        <div class="col-6 p-2">
            <label>Selecione a turma</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Turma_id_Select" data-actions-box="false" data-selected-text-format="values" name="turma"
                tabindex="-98">
            </select>
        </div>
        <div class="col-6 p-2" id="caixaAvalicao">
            <label>Selecione a métrica</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="selector_metricas" data-actions-box="false" data-selected-text-format="values" name="selector_metricas"
                tabindex="-98">
            </select>
        </div>
    </div>
    <input type="hidden" value="0" id="verificarSelector">
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover table-bordered">
                <thead id="head"></thead>
                <tbody id="body"></tbody>
            </table>
        </div>
    </div>
    <h4 id="titulo_semestre"></h4>
    <div class="col-12" id="lista">
        <table class="table table-hover" id="lista_tabela" style="visibility: hidden;width: 100%">
            <thead id="tagTabela">
                <th>#</th>
                <th>MATRÍCULA</th>
                <th>ESTUDANTE</th>
                <th>NOTA</th>
                <th>CRIADO POR</th>
                <th>CRIADO A</th>
                <th>ACTUALIZADO POR</th>
                <th>ACTUALIZADO A</th>
                <th id="metricasOA" style="visibility: hidden">OA</th>
            </thead>
            <tbody id="Tabale_geral_pauta"></tbody>
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
            var id_anoLectivo = $("#lective_year").val();
            //###funcao que pegar o ano lectivo selecionado                       
            $("#lective_year").bind('change keypress ', function(e) {
                // console.log(id_anoLectivo);
                id_anoLectivo = $("#lective_year").val();
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
                    if (data['disciplina'].length) {
                        //LIMPA O SELECTER
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append(
                            '<option selected="" value="0">Selecione a disciplina</option>');
                        Disciplina_id_Select.selectpicker('refresh');
                        Turma_id_Select.empty();
                        Turma_id_Select.append(
                            '<option selected="" value="">Nenhum selecionado</option>');
                        Turma_id_Select.selectpicker('refresh');
                        $("#selector_metricas").empty();
                        metricas_Select.append(
                            '<option selected="" value="">Nenhum selecionado</option>');
                        metricas_Select.selectpicker('refresh');
                        $('#lista').css("visibility", "hidden")
                        $('#lista_tabela').css("visibility", "hidden")
                        getDocenteDisciplina(id_anoLectivo);
                    } else {
                        //LIMPA O SELECTOR
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append(
                            '<option selected="" value="0">Selecione a disciplina</option>');
                        Disciplina_id_Select.selectpicker('refresh');

                        Turma_id_Select.empty();
                        Turma_id_Select.append(
                            '<option selected="" value="">Nenhum selecionado</option>');
                        Turma_id_Select.selectpicker('refresh');

                        $("#selector_metricas").empty();
                        metricas_Select.append(
                            '<option selected="" value="">Nenhum selecionado</option>');
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
                $.ajax({
                    url: "/avaliations/getDocenteDisciplina/" + id_anoLectivo,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    if (data['disciplina'].length) {
                        $("#selector_metricas").empty();
                        Disciplina_id_Select.prop('disabled', true);
                        Disciplina_id_Select.empty();
                        $("#Disciplina_id_Select").append(
                            '<option selected="" value="0">Selecione a disciplina</option>');
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
                        if (id_plano == 00) {
                            return false;

                        }
                    },

                }).done(function(data) {
                    // console.log(data);
                    $.each(data['periodo'], function(indexInArray, valueOfElement) {
                        periodo = valueOfElement;
                    });

                    if (data == 500) {
                        Turma_id_Select.empty();
                        Turma_id_Select.prop('disabled', true);
                        metricas_Select.prop('disabled', true);
                        $("#selector_metricas").empty();
                        // avaliacao_id_Select.empty();
                        // $("#caixaAvalicao").hide();

                        alert(
                            "Atenção! esta disciplina não está associada a nenhuma avaliação no ano lectivo selecionado, verifique a edição de plano de estudo da mesma."
                            );
                    } else {
                        if (data['whoIs'] == "super") {

                            if (data['turma'].length) {
                                Turma_id_Select.empty();
                                $("#selector_metricas").empty();
                                Turma_id_Select.append(
                                    '<option selected="" value="">Selecione a turma</option>');

                                $.each(data['turma'], function(index, row) {
                                    $("#Turma_id_Select").append('<option value="' + row.id + '">' +
                                        row.display_name + '</option>');
                                });
                                Turma_id_Select.prop('disabled', false);
                                Turma_id_Select.selectpicker('refresh');
                                var periodo = data['periodo'];
                                // verificar se a oa e anual ou simestral
                                if (periodo[0] == "1_simestre" || periodo[0] == "2_simestre") {
                                    selector_ao.empty();
                                    $("#selector_ao").append(
                                        '<option selected="" value="0">Selecione a OA</option>');
                                    for (var i = 1; i <= 6; i++) {

                                        $("#selector_ao").append('<option value="' + i + ' ">OA ' + i +
                                            '</option>');

                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                } else {
                                    selector_ao.empty();
                                    $("#selector_ao").append(
                                        '<option selected="" value="0">Selecione a OA</option>');
                                    for (var index = 1; index <= 12; index++) {

                                        $("#selector_ao").append('<option value="' + index + ' ">Oa ' +
                                            index + '</option>');
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
                                    $("#Turma_id_Select").append('<option value="' + row.id + '">' +
                                        row.display_name + '</option>');
                                });
                                Turma_id_Select.prop('disabled', false);
                                Turma_id_Select.selectpicker('refresh');
                                $("#students").empty()
                                var periodo = data['periodo'];
                                // verificar se a oa e anual ou simestral
                                if (periodo[0] == "1_simestre" || periodo[0] == "2_simestre") {
                                    selector_ao.empty();
                                    $("#selector_ao").append(
                                        '<option selected="" value="0">Selecione a OA</option>');
                                    for (var i = 1; i <= 6; i++) {
                                        $("#selector_ao").append('<option value="' + i + ' ">OA ' + i +
                                            '</option>');

                                    }
                                    selector_ao.prop('disabled', false);
                                    selector_ao.selectpicker('refresh');
                                } else {
                                    selector_ao.empty();
                                    $("#selector_ao").append(
                                        '<option selected="" value="0">Selecione a OA</option>');
                                    for (var index = 1; index <= 12; index++) {

                                        $("#selector_ao").append('<option value="' + index + ' ">Oa ' +
                                            index + '</option>');
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

            Turma_id_Select.bind('change keypress', function() {
                var id = Disciplina_id_Select.val();
                getMetricas(id, lective_year.val());
                // selector_ao.empty();
                $('#metrica_Oa').css("visibility", "hidden")
                // $("#metricasOA").remove();
                $("#generate-pdf").removeAttr("href");
                $('#lista').css("visibility", "hidden")
                $('#lista_tabela').css("visibility", "hidden")

            });

            function getMetricas(id_plano, anolectivo) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                $.ajax({
                    url: "/pt/avaliations/pautaTurma_teacher_metricas/" + id_plano + "/" + anolectivo,
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
                    if (data['data'].length > 0) {
                        $("#selector_metricas").empty();
                        metricas_Select.append('<option selected="" value="">Nenhum seleccionado</option>');
                        $.each(data['data'], function(indexInArray, row) {
                            if (row.nome_metrica == null) {
                                console.log('vazio');
                            } else {
                                $("#selector_metricas").append('<option value="' + row.id_metrica +
                                    ', ' + row.calendario_mt + ' ">' + row.nome_metrica +
                                    '</option>');
                            }
                        });
                        var id_turma = $("#Turma_id_Select").val();
                        metricas_Select.prop('disabled', false);
                        metricas_Select.selectpicker('refresh');
                    }
                });
            }
            //  metodo que executa a select metrica 
            metricas_Select.bind('change keypress', function() {
                var id = Disciplina_id_Select.val();
                var id_turma = Turma_id_Select.val();
                var metrica = metricas_Select.val();
                var verificar = verificarSelector.val();
                var reverte = /\s*,\s*/;
                var reverte_turma = metrica.split(reverte);
                var id_metrica = reverte_turma[0];
                var metrica_Oa = reverte_turma[1];

                if (verificar == 0) {
                    console.log(1);
                    verificarSelector.val(1);
                    $('#lista').css("visibility", "visible")
                    $('#lista_tabela').css("visibility", "visible")
                    if (metrica_Oa == 1) {
                        $("#metrica_Oa").css('visibility', 'visible')
                        getStudentNotas(id, lective_year.val(), id_turma, metrica);
                    } else {
                        $("#metrica_Oa").css('visibility', 'hidden')
                        getStudentNotas(id, lective_year.val(), id_turma, metrica);

                    }
                } else {
                    $('#lista').css("visibility", "visible")
                    $('#lista_tabela').css("visibility", "visible")
                    if (metrica_Oa == 1) {
                        $("#metrica_Oa").css('visibility', 'visible')
                        getStudentNotas(id, lective_year.val(), id_turma, metrica);
                    } else {
                        $("#metrica_Oa").css('visibility', 'hidden')
                        getStudentNotas(id, lective_year.val(), id_turma, metrica);
                    }
                }
            });

            selector_ao.bind('change keypress', function() {
                var valor_oa = selector_ao.val();
                var id_plano = Disciplina_id_Select.val();
                var id_turma = Turma_id_Select.val();
                var metrica = metricas_Select.val();
                $('#lista_tabela').DataTable().clear().destroy();
                // getStudentNotas_oa(id_plano, id_turma, metrica, valor_oa, lective_year.val())
            });
            // funcao que pega todas as notas e os estudante de acordo a metrica
            function getStudentNotas(id_plano, anolectivo, id_turma, metrica) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                $("#metricasOA").remove();
                var reverte = /\s*,\s*/;
                var reverte_turma = metrica.split(reverte);
                var id_metrica = reverte_turma[0];
                var metrica_Oa = reverte_turma[1];

                $.ajax({
                    url: "/pt/avaliations/pautaTurma_teacher_getStudent/" + id_plano + "/" + id_turma +
                        "/" + anolectivo + "/" + id_metrica,
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

                    if (data['data'].length > 0) {
                        var body = "";
                        $("#Tabale_geral_pauta").empty();
                        console.log(data['data']);
                        var i = 0;
                        $.each(data['data'], function(indexInArray, item) {
                            console.log(item.full_name);
                            i++;
                            var nota = item.nota_anluno != null ? item.nota_anluno : "F";
                            body += '<tr><td>' + i + '</td><td style="text-align:center">' + item
                                .code_matricula + '</td><td>' + item.full_name +
                                '</td><td style="text-align:center">' + nota + '</td><td>' + item
                                .teacher_create + '</td><td>' + item.criado_a + '</td><td>' + item
                                .teacher_update + '</td><td>' + item.actualizado_a + '</td></tr>'
                        });
                        $("#Tabale_geral_pauta").append(body);
                        var element = document.getElementById("generate-pdf");
                        element.href = "/avaliations/show_final_grades_ajax_pdf/" + Planno_disciplina[1] +
                            "/" + id_turma + "/" + Planno_disciplina[2] + "/" + id_metrica + "/" +
                            anolectivo;
                    } else {
                        $("#generate-pdf").removeAttr("href");
                    }
                });
            }

            // funcao que pega as OAs da metrica OA
            function getStudentNotas_oa(id_plano, id_turma, metrica, valor_oa, anolectivo) {
                var re = /\s*,\s*/;
                var Planno_disciplina = id_plano.split(re);
                $("#tagTabela").append("<th id='metricasOA' style='visibility: hidden'>OA</th>");
                $('#metricasOA').css("visibility", "visible")
                var reverte = /\s*,\s*/;
                var reverte_turma = metrica.split(reverte);
                var id_metrica = reverte_turma[0];
                $.ajax({
                    url: "/avaliations/pautaTurma_getAvaliacaoAO/" + id_metrica + "/" + id_turma + "/" +
                        id_plano + "/" + anolectivo + "/" + valor_oa,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    if (data['data'].length) {
                        var body = "";
                        $("#Tabale_geral_pauta").empty();
                        // alert(dados);                            
                        console.log(data['data']);
                        var i = 0;
                        $.each(data['data'], function(indexInArray, item) {
                            console.log(item.full_name);
                            return false;
                            i++;
                            var nota = item.nota_anluno != null ? item.nota_anluno : "F";
                            body += '<tr><td>' + i + '</td><td>' + item.code_matricula +
                                '</td><td>' + item.full_name + '</td><td>' + nota + '</td><td>' +
                                item.teacher_create + '</td><td>' + item.criado_a + '</td><td>' +
                                item.teacher_update + '</td><td>' + item.actualizado_a +
                                '</td></tr>'

                        });
                        $("#Tabale_geral_pauta").append(body);

                        var element = document.getElementById("generate-pdf");
                        element.href = "/avaliations/show_final_grades_ajax_pdf_OA/" + Planno_disciplina[
                                1] + "/" + id_turma + "/" + Planno_disciplina[2] + "/" + id_metrica + "/" +
                            anolectivo + "/" + valor_oa;
                    } else {
                        $("#generate-pdf").removeAttr("href");
                    }
                });
            }
        });
    </script>
@endsection
