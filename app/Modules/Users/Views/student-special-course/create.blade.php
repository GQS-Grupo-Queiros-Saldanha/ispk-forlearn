@extends('layouts.generic_index_new')
@section('title', 'INSCRIÇÃO PARA CURSO PROFISSIONAL')
@section('navbar')
@include('Users::candidate.navbar.navbar')
@endsection
@section('page-title')
INSCRIÇÃO PARA CURSO PROFISSIONAL
@endsection
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('student-special-course.index') }}">Cursos profissionais</a>
</li>
<li class="breadcrumb-item active" aria-current="page">inscrição</li>
@endsection
@section('selects')

@endsection
@section('body')
{!! Form::open(['route' => 'student-special-course.store', 'files' => true]) !!}
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
<div class="w-25">
    <label for="student_type">Tipo de estudante</label>
    <select name="student_type" id="student_type" class="selectpicker form-control form-control-sm">
        <option value="" selected>Nenhum selecionado</option>
        <option value="0">Interno</option>
        <option value="1">Externo</option>
    </select>
</div>

<div id="content">
    <div class="card-body row pb-0">
        <div class="w-50" id="internos">
            <label for="student_type">Estudantes</label>
            <select id="students" name="student" class="selectpicker form-control form-control-sm" data-live-search="true">
                <option value="" selected>Nenhum selecionado</option>
            </select>
        </div>
        <div class="w-25">
            <label for="student_type">Curso</label>
            <select name="course" id="course" class="selectpicker form-control form-control-sm" data-live-search="true">
                <option value="" selected>Nenhum selecionado</option>
                @foreach($courses as $course)
                <option value="{{$course->id}}">{{$course->display_name}}</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="special_course_edition" id="special_course_edition">
    </div>
    <div id="externos">
        <div class="card-body row pb-0">
            {{ Form::bsText('full_name', null, ['placeholder' => 'Escreva o nome completo', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.full_name')]) }}
            {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'readonly', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
        </div>
        <div class="card-body row pt-0">
            {{ Form::bsText('name', null, ['placeholder' => 'Escreva apenas o primeiro e o último nome', 'readonly', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e último nome']) }}
            {{ Form::bsText('id_number', null, ['placeholder' => 'Número de Bilhete de identidade (usado para gerar password)', 'required', 'autocomplete' => 'email'], ['label' => 'Número de Bilhete de identidade (usado para gerar password)']) }}
        </div>
        <div class="card-body row pt-0" hidden id="confirmpassword">
            <div class="col-6"></div>
            {{ Form::bsText('confirm_password', null, ['placeholder' => 'Confirmar password', 'required', 'autocomplete' => 'email'], ['label' => 'Confirmar password']) }}
        </div>

    </div>

</div>
<div hidden id="nextBtn">

    <button type="submit" class="btn btn-lg btn-primary mb-3 float-right">
        @icon('fas fa-plus-circle')
        Submeter
    </button>
</div>
{!! Form::close() !!}
<!-- Modal alerta BI existente-->
<div style="z-index: 9999999;" class="modal fade" id="modal-alerta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div style="z-index: 9999999;background: #002d3a" class="modal-content rounded">
            <div class="modal-header">
                <h4 style="color: #ededed" class="" id="exampleModalLabel">Informação</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h2 style="font-size: 1.1pc;color: #ededed">Já existe um utilizador com este número do bilhete</h2>
            </div>
            <div class="modal-footer border-0">
                <button style="border-radius: 6px; background:#01c93e" type="button" class="btn btn-lg text-white" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div style="z-index: 9999999;" class="modal fade" id="modal-alerta2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div style="z-index: 9999999;background: #002d3a" class="modal-content rounded">
            <div class="modal-header">
                <h4 style="color: #ededed" class="" id="exampleModalLabel">Informação</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h2 style="font-size: 1.1pc;color: #ededed">Não encontrámos nenhuma edição deste curso aberta</h2>
            </div>
            <div class="modal-footer border-0">
                <button style="border-radius: 6px; background:#01c93e" type="button" class="btn btn-lg text-white" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>






@endsection
@section('scripts-new')
@parent
<script>
    $(function() {
        let student_type;
        let externos;
        let internos;
        $('#content').hide();
        $('#class').prop('disabled', true)

        var hasName = false,
            hasFullName = false,
            hasPassword = false,
            status_sit = -1,
            nomeLongo = "",
            nextBtnCounter = 0;



        $(document).on('keypress', function(e) {
            if (e.which == 13) {
                var password = $("#id_number").val();
                if (password.length == 14) {
                    e.preventDefault();
                    return false;
                } else if (password.length < 14) {
                    $('#full_name').prop('disabled', false);
                } else {
                    e.preventDefault();
                    return false;
                }
            }
        });

        $('#id_number').keyup(function() {
            var passwordl = $("#id_number").val();
            if (status_sit == 1) {
                if (passwordl.length < 14) {
                    let full = $('#full_name');
                    if (full.val() == nomeLongo) {
                        full.val("").prop('readonly', false);
                        $("#email").val("");
                        $("#name").val("");
                    }
                }
            }
        });

        //^[0-9]{9}[A-Z]{2}[0-9]{3}$

        $("#full_name").on('blur', function() {
            var name = $(this).val();
            var nameVerified = name.toString().trim();
            var result = nameVerified.split(" ");

            if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)) {
                //alert("password not valid");
                var result = "erro";
                $("#full_name").addClass('is-invalid');
                $("#full_name").removeClass('is-valid');
                hasFullName = false;
            } else {
                if (result.length >= 2) {
                    hasFullName = true;
                    $("#full_name").removeClass('is-invalid');
                    $("#full_name").addClass('is-valid');
                    $.ajax({
                        url: "/users/user_email_convert/" + result,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',

                        success: function(dataResult) {
                            var email = dataResult.email;
                            var name = dataResult.name;
                            $("#email").val(email);
                            $("#name").val(name);

                        },
                        error: function(dataResult) {
                            // alert('error' + result);
                        }
                    });
                } else {
                    hasFullName = false;
                    $("#full_name").removeClass('is-valid');
                    $("#full_name").addClass('is-invalid');
                    $("#email").val("");
                    $("#name").val("");
                }
            }
            if (hasFullName && hasPassword) {

            } else {
                $("#nextBtn").prop('hidden', true);
                $("#confirmpassword").prop('hidden', true);
            }
        })


        $("#course").change(function() {
            const curso = $(this).val();~
            console.debug("id do curso",curso);
            var url = "/users/student-special-course/get-classes/" + curso + "/" + null;

            $.ajax({
                url: url,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataR) {
                    var dataResult = dataR.classes;
                    var data = '';
                    if (dataResult.length > 0) {
                        $.each(dataResult, function(index, item) {
                            $('#special_course_edition').val(item.id);
                        });
                        if (isHidden($('#externos')))
                            $("#nextBtn").prop('hidden', false);
                    } else {
                        $("#nextBtn").prop('hidden', true);
                        $("#modal-alerta2").modal('show');
                    }


                },
                error: function(xhr, status, error) { // Corrigido erro na estrutura
                    console.log(xhr.responseText);
                }
            });
        });


        function getStudents() {
            $.ajax({
                url: "/users/student-special-course/get-students",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataR) {
                    var dataResult = dataR.students;
                    var data = '';
                    if (dataResult.length > 0) {
                        $.each(dataResult, function(index, item) {
                            data += "<option value='" + item.id + "'>" + item.name + " #" + item.matricula + " (" + item.email + ")</option>";
                        });
                    }

                    $('#students').append(data);
                    $('#students').selectpicker('refresh');

                },
                error: function(xhr, status, error) { // Corrigido erro na estrutura
                    console.log(xhr.responseText);
                }
            });
        }



        $("#id_number").on('blur', function() {
            var count = $(this).val().length;

            if (count === 14) {
                var valorBi = $(this).val();
                console.log('boa')
                $.ajax({
                    url: "/users/candidates/get_validation_bi/" + valorBi,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {

                        if (dataResult.status == 2) {
                            $("#confirmpassword").prop('hidden', true);
                            $("#id_number").removeClass('is-valid');
                            $("#id_number").addClass('is-invalid');
                            $("#modal-alerta").modal('show');
                            status_sit = dataResult.status;
                            nomeLongo = "";
                        } else {

                            if (dataResult.status == 1) {
                                $('#modal-staff').modal('show');
                                $('#name').val(dataResult.object.nome_curto).prop(
                                    'readonly', true);
                                $('#full_name').val(dataResult.object.nome_longo).prop(
                                    'readonly', true);
                                nomeLongo = dataResult.object.nome_longo;
                                let send = dataResult.object.nome_longo.trim().split(" ");
                                $.ajax({
                                    url: "email_convert/" + send,
                                    type: "GET",
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    success: function(dataResult) {
                                        var email = dataResult.email;
                                        $("#email").val(email).prop('readonly',
                                            true);
                                    },
                                    error: function(dataResult) {}
                                });
                                $("#confirmpassword").prop('hidden', false);
                            }

                            status_sit = dataResult.status;

                            if (dataResult.status == 3) {
                                $('#full_name').prop('readonly', false);
                                nomeLongo = "";
                            }

                            if ($("#id_number").val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                                $("#confirmpassword").prop(
                                    'hidden', false);
                                $("#id_number").removeClass(
                                    'is-invalid');
                                $("#id_number").addClass(
                                    'is-valid');
                            } else {
                                hasPassword =
                                    false; //so exibir esse botao se a confirmacao e a caixa conscedirem
                                $("#nextBtn").prop('hidden', true);
                                $("#id_number").addClass('is-invalid');
                                $("#id_number").removeClass('is-valid');
                                $("#confirm_password").val('');

                            }
                        }


                    },
                    error: function(dataResult) {
                        console.log(dataResult);
                    }
                });

            } else {
                hasPassword = false;
                $("#nextBtn").prop('hidden', true);
                $("#id_number").addClass('is-invalid');
                $("#id_number").removeClass('is-valid');

            }

            if (hasFullName && hasPassword) {} else {
                $("#nextBtn").prop('hidden', true);
                $("#confirmpassword").prop('hidden', true);
            }
        });


        $("#confirm_password").on('blur', function() {
            comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
        })

        $("#nextBtn").on('mouseover', function() {
            if (nextBtnCounter == 0) {
                comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
            }
            nextBtnCounter = 1;
        })

        function comparePasswordValues(passwordInputValue, confirmInputValue) {
            if (passwordInputValue == confirmInputValue) {
                $("#nextBtn").prop('hidden', false);
                $("#confirm_password").addClass('is-valid');
                $("#confirm_password").removeClass('is-invalid');
            } else {
                $("#confirm_password").addClass('is-invalid');
                $("#confirm_password").removeClass('is-valid');
                $("#nextBtn").prop('hidden', true);
            }
        }


        $('#student_type').change(function() {
            student_type = $(this).val();
            $('#content').show();
            if (student_type == 0 && !isHidden($('#internos'))) {

                $('#externos').hide();

                getStudents();

            }
            if (student_type == 0 && isHidden($('#internos'))) {

                $('#internos').show();
                $('#externos').hide();
                getStudents();

            }

            if (student_type == 1 && !isHidden($('#externos'))) {

                $('#internos').hide();

            }
            if (student_type == 1 && isHidden($('#externos'))) {
                $('#internos').hide();
                $('#externos').show();

            }

        })

        function isHidden(element) {
            return $(element).css("display") === "none";
        }

        $("#nextBtn").on('click', function() {
            if (isHidden($('#externos'))) {
                $('#externos').remove();
            }
        })

    });
</script>
@endsection