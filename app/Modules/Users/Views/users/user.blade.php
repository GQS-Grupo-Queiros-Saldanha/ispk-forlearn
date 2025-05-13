<title>Perfil do Utilizador</title>  
@extends('layouts.generic_index_new', ['breadcrumb' => true])
@php
    $is_student = $user->hasAnyRole(["student"]);
    $is_teacher = $user->hasAnyRole(["teacher"]);
    $is_staff = !$is_student && !$is_teacher;
    
    $staff_permission = auth()->user()->hasAnyPermission(['gestorRH']) && !auth()->user()->hasRole(['coordenador-curso']);
@endphp
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('styles-new')
    @parent
    @include('Users::script')
    <style>
        .user-profile-image {
            width: 200px !important;
        }
        
        [name="parameters[14][154]"], [for="parameters[14][154]"],
        [name="parameters[12][68]"], [for="parameters[12][68]"],
        [name="parameters[7][68]"], [for="parameters[7][68]"],
        [name="parameters[11][312]"], [for="parameters[11][312]"] {
            display: none !important;
        }

        [name="parameters[12][41]"] {width: 100% !important;}

        [for="parameters[11][311]"]{margin-top: -18px;}

        .collapse .form-group.col {width: 100% !important;}

        .collapse .form-inner:not(:first-child) {margin-top: 2px;}

        #formularioEdit, #form-candidatura{ margin-left: -28px; }

        #myTab{ margin-left: -10px; }

    </style>
    
    @if($is_staff)
        <style>
            .card-body .content .conten-fluid{
                --bs-gutter-x: 1rem!important;
            }
        
            .wrapper .card{
                --bs-card-spacer-y: none!important;
            }
        </style>
    @endif
    
@endsection
@section('page-title')
   @switch($action)
        @case('create')
            @if ($is_student)
                CRIAR ESTUDANTE
            @elseif($is_teacher)  
                CRIAR DOCENTE
            @else
                CRIAR STAFF
            @endif
        @break

        @case('show')
            @if ($is_student)
                PERFIL DO ESTUDANTE
            @elseif($is_teacher)  
                PERFIL DO DOCENTE                
            @else
                PERFIL DO STAFF
            @endif
        @break

        @case('edit')
            @if ($is_student)
                EDITAR PERFIL DO ESTUDANTE
            @elseif($is_teacher)  
                EDITAR PERFIL DO DOCENTE                
            @else
                EDITAR PERFIL DO STAFF 
            @endif
        @break
    @endswitch
@endsection
@section('breadcrumb')
    @if($action == 'create')
        {{ Breadcrumbs::render('users.create') }}
    @endif
     
    @if($action == 'show' || $action  == 'edit')  
        <li class="breadcrumb-item"> <a href="/">Home</a> </li>
        @if($is_staff)
            @if($staff_permission)
                <li class="breadcrumb-item"> <a href="{{ route('add_funcionario') }}">Staff</a> </li>
            @endif
        @endif
        @if($is_student)
            <li class="breadcrumb-item"> <a href="{{ route('users.index') }}">Estudantes</a> </li>
        @elseif($is_teacher)  
            <li class="breadcrumb-item"> <a href="{{ route('user.docents') }}">Docentes</a> </li>
        @endif
        <li class="breadcrumb-item active" aria-current="page"> @if($action == 'edit') EDITAR - @endif {{ $user->name }}</li>
    @endif

@endsection
@section('body')

    <div class="content">
        <div class="container-fluid">
            @switch($action)
                @case('create')
                    {!! Form::open(['route' => 'users.store', 'files' => true, 'id'=>"form-user-for"]) !!}
                @break

                @case('show')
                    {!! Form::model($user) !!}
                @break

                @case('edit')
                    {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put', 'files' => true, 'id'=>"form-user-for"]) !!}
                @break
            @endswitch

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
                    <div class="card">
                        @if ($action !== 'create')
                            @include('Users::users.partials.parameters')
                        @endif
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
@section('models')
    @include('Users::candidate.candidate_modal')
    @include('Users::users.partials.pdf_modal',['hidden_btn' => true])
 
 
 @if (isset($user) && ($user->hasAnyRole(['student']))) 
    @include('Users::candidate.modal.modal_card')
 @endif
@endsection
@section('scripts-new')
    @parent
    @include('Users::script')
    <script src="{{ asset('js/new_tabpane_form.js') }}"></script>
    <script>
        (() => {

            let hasName = false,
                hasFullName = false,
                hasPassword = false;

            replaceDisable();

            function replaceDisable() {
                const formControl = document.querySelectorAll('.form-control');
                formControl.forEach(control => {
                    if (control.hasAttribute('disabled')) {
                        control.removeAttribute('disabled');
                        control.setAttribute('readonly', '');
                    }
                });
            }

            $(document).on('keypress', function(e) {
                if (e.which == 13) {
                    let password = $("#id_number").val();
                    if (password.length == 14) {
                        e.preventDefault();
                        return false;
                    } else {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            let courses = $('#course');
            courses.on('change', function(e) {
                let user_id = "{{ $user->id }}";
                $.ajax({
                    url: "{{ route('user.verify.change.course', $user->id) }}",
                    type: "GET",
                    cache: false,
                    success: function(response) {
                        if (!response.status) {
                            if (courses.val() != response.body.courses_id)
                                $('#modalVerifyChangeCourse').modal('show');
                            courses.children().each(function(index, item) {
                                if (item.value == response.body.courses_id) {
                                    $(item).attr('seleted', true);
                                    $('.filter-option-inner-inner').html(
                                        response.body.display_name);
                                    $('.dropdown-menu .inner .show').children()
                                        .each(function(i, li) {
                                            let text = li.querySelector(
                                                '.text').innerHTML;
                                            let drop = li.querySelector(
                                                '.dropdown-item');

                                            if (text == response.body
                                                .display_name) {
                                                drop.classList.add(
                                                    'active');
                                                drop.classList.add(
                                                    'selected');
                                                drop.setAttribute(
                                                    'aria-selected',
                                                    true);
                                                li.classList.add(
                                                    'selected');
                                                li.classList.add('active');
                                            } else {
                                                drop.classList.remove(
                                                    'active');
                                                drop.classList.remove(
                                                    'selected');
                                                drop.setAttribute(
                                                    'aria-selected',
                                                    false);
                                                li.classList.remove(
                                                    'selected');
                                                li.classList.remove(
                                                    'active');
                                            }
                                        });
                                } else
                                    $(item).attr('seleted', false);
                            });
                        }
                    },
                    error: function(erro) {
                        console.log("ERRO")
                    }
                });
            });

            $('.staffClose').click(function() {
                let status = "{{ $staff_status_studant->status ?? 0 }}";
                $('#scholarship_check_staff').prop("checked", status == "0" ? false : true);
                $("#exampleModalStaff").modal('hide');
            });

            $('.openModalStaff').click(function() {
                $("#exampleModalStaff").modal('show');
            });

            $('#btn-docente-estudante').click(function() {
                $.ajax({
                    url: "{{ route('users.create_docente_student') }}",
                    type: "GET",
                    data: {
                        user_id: "{{ $user->id }}",
                    },
                    dataType: 'json',
                    success: function(response) {
                        window.open("{{ route('users.edit', $user->id) }}", "_self");
                    }

                })
            });

            $('#btn-docente-estudante-eliminar').click(function() {
                $.ajax({
                    url: "{{ route('users.deleta_docente_student') }}",
                    type: "GET",
                    data: {
                        user_id: "{{ $user->id }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        window.open("{{ route('users.edit', $user->id) }}", "_self");
                    }
                })
            });

            $("#name").on('blur', function() {
                let name = $(this).val();
                let nameVerified = name.toString().trim();
                let result = nameVerified.split(" ");

                if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)) {
                    let result = "erro";
                    $("#name").addClass('is-invalid');
                    $("#name").removeClass('is-valid');
                    hasFullName = false;
                } else {
                    if (result.length >= 2) {
                        hasFullName = true;
                        $("#name").removeClass('is-invalid');
                        $("#name").addClass('is-valid');

                        $.ajax({
                            url: "user_email_convert/" + result,
                            type: "GET",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            cache: false,
                            dataType: 'json',

                            success: function(dataResult) {
                                let email = dataResult.email;
                                let name = dataResult.name;
                                $("#email").val(email);
                                $("#full_name").val(name);

                            },
                            error: function(dataResult) {

                            }
                        });


                    } else {
                        hasFullName = false;
                        $("#name").removeClass('is-valid');
                        $("#name").addClass('is-invalid');
                        $("#email").val("");
                        $("#full_name").val("");
                    }
                }
                if (hasFullName && hasPassword) {

                } else {
                    $("#nextBtn").prop('hidden', true);
                    $("#confirmpassword").prop('hidden', true);
                }
            })

            $("#id_number").on('blur', function() {
                let count = $(this).val().length;
                if (count === 14) {
                    let valor = $(this).val();
                    $.ajax({
                        url: "validation/" + valor,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(dataResult) {
                            if (dataResult.status != 1) {
                                $("#confirmpassword").prop('hidden', false);
                                $("#id_number").removeClass('is-invalid');
                                $("#id_number").addClass('is-valid');
                                $("#confirmpassword").prop('hidden', false);
                            } else {
                                $("#nextBtn").prop('hidden', true);
                                $("#id_number").addClass('is-invalid');
                                $("#id_number").removeClass('is-valid');
                                $("#confirmpassword").prop('hidden', true);
                                $("#confirm_password").val('');
                            }
                        },
                        error: function(dataResult) {

                        }
                    });

                    if ($("#id_number").val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                        hasPassword = true;
                        $("#confirmpassword").prop('hidden', false);
                        $("#id_number").removeClass('is-invalid');
                        $("#id_number").addClass('is-valid');

                    } else {
                        hasPassword = false;
                        $("#nextBtn").prop('hidden', true);
                        $("#id_number").addClass('is-invalid');
                        $("#id_number").removeClass('is-valid');
                        $("#confirm_password").val('');
                    }

                } else {
                    hasPassword = false;
                    $("#nextBtn").prop('hidden', true);
                    $("#id_number").addClass('is-invalid');
                    $("#id_number").removeClass('is-valid');
                }

                if (hasFullName && hasPassword) {

                } else {
                    $("#nextBtn").prop('hidden', true);
                    $("#confirmpassword").prop('hidden', true);
                }
            });

            $("#confirm_password").on('blur', function() {
                comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
            })

            $("#nextBtn").on('mouseover', function() {
                comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
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

            let params3_14 = $('input[name="parameters[3][14]"]').val();
            
            if (params3_14 && params3_14.match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                let file = $('input[name="attachment_parameters[1][25]"]').val();
                console.log(file)
                let extension = file.substr((file.lastIndexOf('.') + 1));

                if (!$('input[name="attachment_parameters[1][25]"]').val() == "") {
                    if (extension == "jpg" || extension == "png" || extension == "jpge") {} else {
                        checkExtension = false
                        e.preventDefault();
                    }
                }
            } else {
                $('input[name="parameters[3][14]"]').attr('autofocus', 'true');
            }

            $('input[name="email"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}','{{ $user->id ?? '' }}');
            });

            $('input[name="parameters[3][14]"]').on('blur', function() {
                if ($('input[name="parameters[3][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                    $('input[name="parameters[3][14]"]').removeClass('is-valid');
                    $('input[name="parameters[3][14]"]').removeClass('is-invalid');
                    Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}','{{ $user->id ?? '' }}');
                } else {
                    $('input[name="parameters[3][14]"]').removeClass('is-valid');
                    $('input[name="parameters[3][14]"]').addClass('is-invalid');
                }
            });

            $('input[name="parameters[1][19]"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsMecanNumber') }}',
                    '{{ $user->id ?? '' }}');
            });

            $('#scholarship_check').on('change', function(e) {
                if (e.target.checked) {
                    $('#exampleModal').modal();
                }
            });

            $('#regime_especial_check').on('change', function(e) {
                if (e.target.checked) {
                    $('#modalRegimeEspecial').modal();
                }
            });

            $("#closeModal").click(function() {
                $("#scholarship_check").prop('checked', false);
            });

            $("#closeModalRegimeEspecial").click(function() {
                $("#regime_especial_check").prop('checked', false);
            });

            $("#cancelModalScholarship").click(function() {
                $("#scholarship_check").prop('checked', false);
            });

            $("#cancelModalRegimeEspecial").click(function() {
                $("#regime_especial_check").prop('checked', false);
            });

            @if ($action !== 'create' && $user->hasAnyRole(['teacher', 'student', 'candidado-a-estudante'])) //
                let selectCourses = $('#course');
                let selectDisciplines = $('#disciplines');
                let userSelectedDisciplines = JSON.parse('{!! json_encode($user->disciplines->pluck('id')) !!}');
                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                    let selectClasses = $('#classes');
                    let userSelectedClasses = JSON.parse('{!! json_encode($user->classes->pluck('id')) !!}');
                @endif
                function updateSelectDisciplines() {
                    let courseIds = getCourses();
                    if (courseIds.length) {
                        let data = {
                            courses: courseIds,
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                                url: '{{ route('users.disciplines') }}',
                                method: 'POST',
                                data
                            })
                            .then(function(resp) {
                                console.log(resp.disciplines.length)

                                if (resp.disciplines.length) {
                                    selectDisciplines.empty();

                                    resp.disciplines.forEach(function(discipline) {
                                        selectDisciplines.append('<option value="' + discipline.id + '">' +
                                            "#" + discipline.code + " - " + discipline
                                            .current_translation.display_name + '</option>');
                                    });

                                    selectDisciplines.selectpicker('val', userSelectedDisciplines);
                                    selectDisciplines.prop('disabled', false);
                                    selectDisciplines.selectpicker('refresh');
                                }
                                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                                    if (resp.classes.length) {
                                        selectClasses.empty();

                                        resp.classes.forEach(function(clss) {
                                            selectClasses.append('<option value="' + clss.id + '"># ' + clss
                                                .display_name + ' - "' + clss.anoLective + '"</option>');
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
                    let courseIds = selectCourses.val();
                    return Array.isArray(courseIds) ? courseIds : [courseIds];
                }

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
                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']))
                    if (!$.isEmptyObject(selectClasses)) {
                        selectClasses.change(function() {
                            userSelectedClasses = selectClasses.val();
                        });
                    }
                @endif
                updateSelectDisciplines();
            @endif
        })();
    </script>
    
    <script>
        (() => {
            let counter = 0;
            const bsSearchbox = document.querySelectorAll('.selectpicker[multiple]');

            bsSearchbox.forEach(item => {
                let panel = item.parentElement;
                panel.addEventListener('click', (_) => {
                    let textDigit = panel.querySelector('input');
                    textDigit.addEventListener('keyup', (_) => {
                        let listOptions = panel.querySelectorAll("ul li");
                        listOptions.forEach(itemLi => itemLi.addEventListener('click', (
                            _) => {
                            let isSelected = !itemLi.classList.contains(
                                'selected');
                            if (isSelected) {
                                compareLiOption(panel, listOptions[0], itemLi);
                            }
                        }));
                    })
                })
            })

            function getText(itemLi) {
                return itemLi.querySelector('a').innerHTML.trim();
            }

            function getLi(panel, itemLi) {
                let item;
                let listOptions = panel.querySelectorAll("ul li");
                for (let i = 0; i < listOptions.length; i++)
                    if (getText(listOptions[i]) == getText(itemLi))
                        item = listOptions[i];
                return item;
            }

            function compareLiOption(panel, firstLiOld, clickedLiOld) {

                let firstLi = getLi(panel, firstLiOld);
                let clickedLi = getLi(panel, clickedLiOld);

                let isWork = !firstLi.classList.contains('selected') && !clickedLi.classList.contains('selected');
                if (isWork && counter == 0) {
                    firstLi.classList.remove('selected');
                    let aLink = firstLi.querySelector('a');
                    aLink.classList.remove('selected');
                    aLink.setAttribute('aria-disabled', false);
                    aLink.setAttribute('aria-selected', false);
                    aLink.querySelector('.bs-ok-default.check-mark').remove();
                    counter = 1;
                }
            }

        })();
    </script>
@endsection
