@extends('layouts.generic_index_new')
@switch($action)
    @case('create')
        @section('title', 'CRIAR CANDIDATO A ESTUDANTE')
    @break

    @case('show')
        @section('title', 'CRIAR CANDIDATO A ESTUDANTE')
    @break

    @case('edit')
        @section('title', 'CRIAR CANDIDATO A ESTUDANTE')
    @break
@endswitch
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title')
    @switch($action)
        @case('create')
            CRIAR CANDIDATO A ESTUDANTE
        @break

        @case('show')
            CRIAR CANDIDATO A ESTUDANTE
        @break

        @case('edit')
            CRIAR CANDIDATO A ESTUDANTE
        @break
    @endswitch
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @if ($action === 'show')
        @include('Users::users.partials.pdf_modal')
    @endif
    @switch($action)
        @case('create')
            {!! Form::open(['route' => 'candidates.store', 'files' => true]) !!}
        @break

        @case('show')
            {!! Form::model($user) !!}
        @break

        @case('edit')
            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'post', 'files' => true]) !!}
        @break
    @endswitch
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
    <div class="container-fluid">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>
    @if (in_array($action, ['show', 'edit']))
        <div class="card-body row">
            {{ Form::bsText('name', null, ['placeholder' => 'Primeiro e último nome', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.name')]) }}
            {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}

            @if ($action === 'edit')
                {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
            @endif
        </div>
    @endif
    @if ($action === 'create')
        <div class="card-body row pb-0">
            {{ Form::bsText('full_name', null, ['placeholder' => 'Escreva o nome completo', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.full_name')]) }}
            {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'readonly', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
        </div>
        <div class="card-body row pt-0">
            {{ Form::bsText('name', null, ['placeholder' => 'Escreva apenas o primeiro e o último nome', 'disabled' => $action === 'show', 'readonly', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e último nome']) }}
            {{ Form::bsText('id_number', null, ['placeholder' => 'Número de Bilhete de identidade (usado para gerar password)', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => 'Número de Bilhete de identidade (usado para gerar password)']) }}
        </div>
        <div class="card-body row pt-0" hidden id="confirmpassword">
            <div class="col-6"></div>
            {{ Form::bsText('confirm_password', null, ['placeholder' => 'Confirmar password', 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => 'Confirmar password']) }}
        </div>
    @endif
    <div class="card" hidden>
        <div class="card-body row">
            <div class="col">
                <h5 class="card-title mb-3">@lang('Users::roles.roles')</h5>
                <select name="roles" id="">
                    <option value="{{ $roles->id }}" selected>
                        {{ $roles->currentTranslation->display_name }}
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div class="">
        @switch($action)
            @case('create')
                <div hidden id="nextBtn">
                    @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']) || auth()->user()->hasAnyPermission(['create_candidate']) )
                        <button type="submit" class="btn btn-lg btn-primary mb-3 float-right">
                            @icon('fas fa-plus-circle')
                            Seguinte
                        </button>
                </div>
                @endif
            @break

        @endswitch
    </div>
    {!! Form::close() !!}

    <div style="z-index: 9999999;" class="modal fade" id="modal-alerta" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div style="z-index: 9999999;background: #002d3a" class="modal-content rounded">
                <div class="modal-header">
                    <h4 style="color: #ededed" class="" id="exampleModalLabel">Informação</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 style="font-size: 1.1pc;color: #ededed">
                        Houve um conflito de dado para prosseguir o processo de candidatura,
                        a forLEARN detectou que já existe um utilizador com o mesmo número do bilhete inserido.
                    </h2>
                </div>
                <div class="modal-footer border-0">
                    <button style="border-radius: 6px; background:#01c93e" type="button" class="btn btn-lg text-white"
                        data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div style="z-index: 9999999;" class="modal fade" id="modal-staff" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div style="z-index: 9999999;background: #002d3a" class="modal-content rounded">
                <div class="modal-header">
                    <h4 style="color: #ededed" class="" id="exampleModalLabel">Atenção!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 style="font-size: 1.1pc;color: #ededed">
                        Estamos diante de um utilizador [Staff-estudante], será preenchido automaticamente os campos para
                        poder prosseguir com a candidatura deste utilizador.
                    </h2>
                </div>
                <div class="modal-footer border-0">
                    <button style="border-radius: 6px;" type="button" class="btn btn-lg btn-warning active text-dark"
                        data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div style="z-index: 9999999;" class="modal fade" id="modal-ano-lectivo" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div style="z-index: 9999999;background: #002d3a" class="modal-content rounded">
                <div class="modal-header">
                    <h4 style="color: #ededed" class="" id="exampleModalLabel">Atenção!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 style="font-size: 1.1pc;color: #ededed">
                        As candidaturas encontram-se encerradas para o ano lectivo selecionado . Por favor verique o
                        calendário de abertura e fechamento da mesma para validar esta informação.
                    </h2>
                </div>
                <div class="modal-footer border-0">
                    <button style="border-radius: 6px;" type="button" class="btn btn-lg btn-warning active text-dark"
                        data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            var hasName = false,
                hasFullName = false,
                hasPassword = false,
                status_sit = -1,
                nomeLongo = "",
                nextBtnCounter = 0;

            const lective = $('#lective_year');

            lective.change(function() {

                $.ajax({
                    url: "/users/candidates/validation_ano/" + lective.val(),
                    type: "GET",
                    success: function(data) {
                        if (data.status == 1) {
                            if (data.body.is_termina == 0) {
                                if ($("#id_number").val() != "" && $("#confirm_password")
                                    .val() != "") {
                                    if ($("#id_number").val() != $("#confirm_password").val())
                                        $("#nextBtn").prop('hidden', true);
                                    else $("#nextBtn").prop('hidden', false);
                                } else $("#nextBtn").prop('hidden', true);
                            } else {
                                $('#modal-ano-lectivo').modal('show');
                                $("#nextBtn").prop('hidden', true);
                            }
                        }
                    }
                });
            });


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

            $("#id_number").on('blur', function() {
                var count = $(this).val().length;

                if (count === 14) {
                    var valorBi = $(this).val();

                    $.ajax({
                        url: "/users/candidates/get_validation_bi/" + valorBi,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                        // BI já existe (usuário comum) - BLOQUEIA
                            if (dataResult.status == 2) {
                                $("#confirmpassword").prop('hidden', true);
                                $("#id_number").removeClass('is-valid');
                                $("#id_number").addClass('is-invalid');
                                $("#modal-alerta").modal('show');
                                status_sit = dataResult.status;
                                nomeLongo = "";
                            } else {
                                // É STAFF - Permite continuar
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
                                // BI disponível - PERMITE CADASTRO
                                if (dataResult.status == 3) {
                                    $('#full_name').prop('readonly', false);
                                    nomeLongo = "";
                                }

                                if ($("#id_number").val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                                    $.ajax({
                                        url: "/users/candidates/validation_ano/" +
                                            lective.val(),
                                        type: 'GET',
                                        success: function(data) {
                                            if (data.status == 1) {
                                                if (data.body.is_termina == 0) {
                                                    hasPassword = true;
                                                    $("#confirmpassword").prop(
                                                        'hidden', false);
                                                    $("#id_number").removeClass(
                                                        'is-invalid');
                                                    $("#id_number").addClass(
                                                        'is-valid');
                                                } else {
                                                    $('#modal-ano-lectivo').modal(
                                                        'show');
                                                }
                                            }
                                        }
                                    });
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
                    $.ajax({
                        url: "/users/candidates/validation_ano/" + lective.val(),
                        type: 'GET',
                        success: function(data) {
                            if (data.status == 1) {
                                if (data.body.is_termina == 0) {
                                    $("#nextBtn").prop('hidden', false);
                                    $("#confirm_password").addClass('is-valid');
                                    $("#confirm_password").removeClass('is-invalid');
                                } else {
                                    $('#modal-ano-lectivo').modal('show');
                                }
                            }
                        }
                    });
                } else {
                    $("#confirm_password").addClass('is-invalid');
                    $("#confirm_password").removeClass('is-valid');
                    $("#nextBtn").prop('hidden', true);
                }
            }

            $('input[name="email"]').on('blur', function() {
                console.log(this)
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}',
                    '{{ $user->id ?? '' }}');
            });

            $('input[name="parameters[3][14]"]').on('blur', function() {
                console.log("vida dos outros")
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}',
                    '{{ $user->id ?? '' }}');
            });
            $('input[name="parameters[1][19]"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsMecanNumber') }}',
                    '{{ $user->id ?? '' }}');
            });

            $('input[type="checkbox"]').on('change', function(e) {
                if (e.target.checked) {
                    $('#exampleModal').modal();
                }
            });

            $("#closeModal").click(function() {
                $("#scholarship_check").prop('checked', false);
            });
            $("#cancelModalScholarship").click(function() {
                $("#scholarship_check").prop('checked', false);
            });

        });

        @if ($action !== 'create' && $user->hasAnyRole(['teacher', 'student', 'candidado-a-estudante'])) //

            var selectCourses = $('#course');
            var selectDisciplines = $('#disciplines');
            var userSelectedDisciplines = JSON.parse('{!! json_encode($user->disciplines->pluck('id')) !!}');
            @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                var selectClasses = $('#classes');
                var userSelectedClasses = JSON.parse('{!! json_encode($user->classes->pluck('id')) !!}');
            @endif

            function updateSelectDisciplines() {
                var courseIds = getCourses();
                if (courseIds.length) {
                    var data = {
                        courses: courseIds,
                        _token: "{{ csrf_token() }}"
                    };
                    $.ajax({
                            url: '{{ route('users.disciplines') }}',
                            method: 'POST',
                            data
                        })
                        .then(function(resp) {

                            if (resp.disciplines.length) {
                                selectDisciplines.empty();

                                resp.disciplines.forEach(function(discipline) {
                                    selectDisciplines.append('<option value="' + discipline.id + '">' + "#" +
                                        discipline.code + " - " + discipline.current_translation
                                        .display_name + '</option>');
                                });

                                selectDisciplines.selectpicker('val', userSelectedDisciplines);
                                selectDisciplines.prop('disabled', false);
                                selectDisciplines.selectpicker('refresh');
                            }
                            @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                                if (resp.classes.length) {
                                    selectClasses.empty();

                                    resp.classes.forEach(function(clss) {
                                        selectClasses.append('<option value="' + clss.id + '">' + clss
                                            .display_name + '</option>');
                                    });

                                    selectClasses.selectpicker('val', userSelectedClasses);
                                    selectClasses.prop('disabled', false);
                                    selectClasses.selectpicker('refresh');
                                }
                            @endif
                        })
                } else {
                    selectDisciplines.selectpicker('deselectAll');
                    selectDisciplines.prop('disabled', true);
                    selectDisciplines.selectpicker('refresh');
                    @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                        selectClasses.selectpicker('deselectAll');
                        selectClasses.prop('disabled', true);
                        selectClasses.selectpicker('refresh');
                    @endif
                }
            }

            function getCourses() {
                var courseIds = selectCourses.val();
                return Array.isArray(courseIds) ? courseIds : [courseIds];
            }

            $(function() {
                if (!$.isEmptyObject(selectCourses)) {
                    selectCourses.change(function() {
                        updateSelectDisciplines();
                    });
                }

                if (!$.isEmptyObject(selectDisciplines)) {
                    selectDisciplines.change(function() {
                        userSelectedDisciplines = selectDisciplines.val();
                    });
                }

                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                    if (!$.isEmptyObject(selectClasses)) {
                        selectClasses.change(function() {
                            userSelectedClasses = selectClasses.val();
                        });
                    }
                @endif

                updateSelectDisciplines();
            });
        @endif
    </script>
@endsection
