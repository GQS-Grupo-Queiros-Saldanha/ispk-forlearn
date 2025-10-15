<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>



@section('title', __('Certificado de Mérito'))
@extends('layouts.backoffice')
@section('styles')
    @parent
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

        .casa-inicio {}

        .div-anolectivo {
            width: 300px;

            padding-right: 0px;
            margin-right: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="content-panel" style="padding: 0;">
        @include('Avaliations::requerimento.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Certificado de Mérito</li>
                            </ol>
                        </div>
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Certificado de Mérito')</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-right div-anolectivo">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm"
                                style="width: 100%; !important">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected == $lectiveYear->id)
                                        <option value="{{ $lectiveYear->id }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @else
                                        <option value="{{ $lectiveYear->id }}">
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">

                        <div class="row">

                            <div class="col-6">
                                <div class="form-group col">
                                    <label for="roles">Cargos</label>
                                    <select class="selectpicker form-control form-control-sm" name="roles" id="roles"
                                        data-actions-box="true" data-live-search="true">
                                        <option></option>
                                        @foreach ($roles as $item)
                                            <option value="{{ $item->role_id }}">
                                                {{ $item->display_name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>

                            <div class="col-6 d_funcionario">
                                <div class="form-group col">
                                    <label for="funcionario">Funcionários</label>
                                    <select class=" selectpicker form-control form-control-sm" name="funcionario"
                                        id="funcionario" data-actions-box="true" data-live-search="true" disabled>

                                    </select>

                                </div>
                            </div>
                            <div class="col-6 d_estudante">
                                <div class="form-group col">
                                    <label for="student">Estudantes</label>
                                    <select class="selectpicker form-control " name="student" id="student"
                                        data-actions-box="true" data-live-search="true">

                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6 d_ano">
                                <div class="form-group col anos_estudantes">
                                    <label for="year">Ano curricular</label>
                                    <select class="selectpicker form-control " name="year" id="year"
                                        data-actions-box="true" data-live-search="true">
                                    </select>
                                </div>

                            </div>
                            <div class="col-6 d_seccao">
                                <div class="form-group col seccao">
                                    <label for="seccao">Secção:</label>
                                    <input type="text" class="form-control" id="seccao" name="seccao">
                                </div>
                            </div>
                            <div class="col-6 d_departamento">
                                <div class="form-group col">
                                    <label for="departamento">Departamentos</label>
                                    <select class="selectpicker form-control form-control-sm" name="departamento"
                                        id="departamento" data-actions-box="true" data-live-search="true">

                                        @foreach ($departamento as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->display_name }}</option>
                                        @endforeach

                                    </select>

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="float-right">
                            <button type="submit" class="btn btn-success mb-3" id="requerer">
                                <i class="fas fa-plus-circle"></i>
                                Requerer documento
                            </button>

                        </div>

                        <div class="row">
                            <div class="col-4 mr-3">
                                <div class="form-group">
                                    <div class="alert alert-warning alert-dismissible fade show alert_request"
                                        role="alert">
                                        <strong><i class="fas fa-exclamation-triangle"
                                                style="margin-right: 10px;"></i></strong><strong class="title_requerimento"
                                            style="font-size: 20px;"></strong> <br>
                                        <p id="sms" style="font-size: 20px;"></p>
                                    </div>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        var tipo_requerimento = $("#req_type");
        var emolumentos_doc = $("#emolumentos_doc");
        var student = $("#student");
        var emo = $(".emo");
        var student_list = $(".student_list");
        var ano_lectivo = $("#lective_year");
        var student_year = $("#year");
        var efeito = $("#efeito");
        var sms = $("#sms");
        var btn_requerer = $("#requerer");
        var alert = $(".alert_request");
        var title = $(".title_requerimento");
        emo.hide();
        student_list.hide();
        btn_requerer.hide();
        alert.hide();
        var valores = "";
        var utilizador = $("#funcionario");
        var cargos = $("#roles");
        $(".d_estudante,.d_ano,.d_departamento,.d_seccao,.d_funcionario").hide();

        $("#anos_estudantes").hide();


        function get_user(id_roles) {

            if (id_roles == "") {
                utilizador.empty();
                utilizador.attr('disabled', true);
                cargos.selectpicker('refresh');
                utilizador.selectpicker('refresh');

            } else {

                if (id_roles == 1) {
                    $(".d_departamento,.d_funcionario").show();
                    $(".d_ano,d_estudante,.d_seccao").hide();
                    $.ajax({
                        url: '/gestao-academica/ajax_users/' + id_roles,
                        type: "get",
                        data: $(this).serialize(),
                        dataType: 'json',
                        statusCode: {
                            404: function() {
                                alert("Página não encontrada");
                            }
                        },
                        success: function(response) {
                            utilizador.empty();
                            response.forEach(response => {
                                utilizador.append("<option value='" + response["id_usuario"] + "'>" +
                                    response["nome_usuario"] + " ( " + response["email_usuario"] +
                                    " )</option>");
                            });
                            utilizador.prop('disabled', false);
                            utilizador.selectpicker('refresh');
                        }
                    });

                } else if (id_roles == 6) {
                    getUser();
                    $(".d_ano,.d_estudante").show();
                    $(".d_departamento,.d_seccao,.d_funcionario").hide();

                } else {

                    $(".d_seccao,.d_funcionario").show();
                    $(".d_ano,.d_estudante,.d_departamento").hide();

                    $.ajax({
                        url: '/gestao-academica/ajax_users/' + id_roles,
                        type: "get",
                        data: $(this).serialize(),
                        dataType: 'json',
                        statusCode: {
                            404: function() {
                                alert("Página não encontrada");
                            }
                        },
                        success: function(response) {
                            utilizador.empty();
                            response.forEach(response => {
                                utilizador.append("<option value='" + response["id_usuario"] + "'>" +
                                    response["nome_usuario"] + " ( " + response["email_usuario"] +
                                    " )</option>");
                            });
                            utilizador.prop('disabled', false);
                            utilizador.selectpicker('refresh');
                        }
                    });
                }


            }
        }

        $("#roles").on('change', function() {
            get_user($(this).val());
        });



        $("#req_type,#emolumentos_doc,#student,#lective_year").on("change", function() {
            validar();
        });

        $("#req_type").on('change', function() {

            getUserArticle();
            getUser();
            validar();

        });

        $("#lective_year").on('change', function() {


            getUser();
            validar();

        });

        $("#emolumentos_doc").change(function() {


            var tipo = $('#emolumentos_doc').val();
            tipo = tipo.split(",");

            console.log(tipo);

            if (tipo[2] == "2" || tipo[2] == "3") {
                $(".anos_estudantes").show();
            } else {
                $(".anos_estudantes").hide();
            }

            if (tipo[2] == "6" || tipo[2] == "1" || tipo[2] == "2") {
                $(".efeito").show();
            } else {
                $(".efeito").hide();
            }


        });

        $("#req_type").change(function() {


            var tipo = $('#req_type').val();
            tipo = tipo.split(",");


            if (tipo == "1") {
                $(".anos_estudantes,.efeito").show();
            } else if (tipo == "2") {
                console.log("alertou");
            } else {
                $(".anos_estudantes,.efeito").hide();
            }

        });


        // Verificar o estado de mensalidade do estudante

        student.change(function() {
            mensalidades();
        });

        function mensalidades() {

            $.ajax({
                url: "/avaliations/requerimento_ajax/" + student.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',


            }).done(function(data) {




                if (data["anos"].length == 0) {
                    student_year.empty();
                    student_year.selectpicker('refresh');
                } else {
                    student_year.empty();
                    data["anos"].forEach(function(ano_matriculado) {
                        student_year.append('<option value="' + ano_matriculado.ano + '">' +
                            ano_matriculado.ano +
                            '</option>');
                    });

                    student_year.selectpicker('refresh');
                }



            });

        }

        function getUser() {

            $.ajax({
                url: "/avaliations/matriculation_requerimento/" + ano_lectivo.val(),
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {

                student.empty();
                data["matriculation"].forEach(function(user) {
                    student.append('<option value="' + user.codigo + '">' + user.name + ' ( ' + user.email +
                        ' )</option>');
                });
                student.selectpicker('refresh');
            });
        }

        function store_doc() {

            tipo = 0;
            ano_curricula = $("#year").val();
            estudante = $("#student").val();
            departameno = $("#departamento").val();
            seccao = $("#seccao").val();
            funcionario = $("#funcionario").val();

            if ($(".d_estudante").is(":visible")) {
                tipo = 1;
                caminho = "/avaliations/store_doc_merito/" + [tipo, estudante, ano_curricula, ano_lectivo.val()];

            } else if ($(".d_departamento").is(":visible")) {
                tipo = 2;
                caminho = "/avaliations/store_doc_merito/" + [tipo, funcionario, departameno, ano_lectivo.val()];

            } else if ($(".d_seccao").is(":visible")) {
                tipo = 3;
                caminho = "/avaliations/store_doc_merito/" + [tipo, funcionario, seccao, ano_lectivo.val()];

            }
            $.ajax({
                url: caminho,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {


                if (data["code"] == "exist") {
                    alert.show();
                    title.text(" Atenção!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-danger");
                    alert.removeClass("alert-success");
                    alert.addClass("alert-warning");
                }
                if (data["code"] == "success") {
                    alert.show();
                    title.text(" Sucesso!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-danger");
                    alert.removeClass("alert-warning");
                    alert.addClass("alert-success");
                }
                if (data["code"] == "empty") {
                    alert.show();
                    title.text(" Atenção!");
                    sms.html(data["dados"]);

                    alert.removeClass("alert-warning");
                    alert.removeClass("alert-success");
                    alert.addClass("alert-danger");
                }
                setTimeout(function() {
                    alert.hide();
                }, 3000);
            });
        }

        // Requerer documento

        btn_requerer.click(function() {

            store_doc();
        });
        $("div").hover(function() {

            validar();
        });




        function validar() {
            if ($(".d_estudante").is(":visible")) {

                if (($("#year").val() == "") || ($("#student").val() == "")) {
                    btn_requerer.hide();
                } else {
                    btn_requerer.show();
                }

            } else if ($(".d_departamento").is(":visible")) {

                if (($("#funcionario").val() == "") || ($("#departamento").val() == "")) {

                    btn_requerer.hide();
                } else {
                    btn_requerer.show();
                }
            } else if ($(".d_seccao").is(":visible")) {

                if (($("#funcionario").val() == "") || ($("#seccao").val() == "")) {

                    btn_requerer.hide();
                } else {

                    btn_requerer.show();
                }
            } else {
            }

            if ($("#roles").val() == "") {

                btn_requerer.hide();
            }

        }
    </script>
@endsection
