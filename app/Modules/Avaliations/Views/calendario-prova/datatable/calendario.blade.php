@switch($action)
    @case('create') @section('title',"CRIAR CALENDÁRIO DE PROVA") @break
@case('show') @section('title',"CRIAR CALENDÁRIO DE PROVA") @break
@case('edit') @section('title',"CRIAR CALENDÁRIO DE PROVA") @break
@endswitch


@extends('layouts.backoffice')
<style>
    .user-profile-image {
       width: 200px !important;
    }
    input#name::placeholder {
        color: red;
    }
    input#full_name::placeholder{
        color: red;
    }
    input#id_number::placeholder{
        color: red;
    }
</style>
@section('content')


    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row -- mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create')CRIAR CALENDÁRIO DE PROVA @break
                                @case('show') CRIAR CALENDÁRIO DE PROVA @break
                                @case('edit') CRIAR CALENDÁRIO DE PROVA @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                           {{-- @case('create') {{ Breadcrumbs::render('users.create') }} @break --}}
                            @case('show') {{ Breadcrumbs::render('users.show', $user) }} @break
                            @case('edit') {{ Breadcrumbs::render('users.edit', $user) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @if($action === 'show')
                    @include('Users::users.partials.pdf_modal')
                @endif

                @switch($action)
                    @case('create')
                    {!! Form::open(array('route' => 'candidates.store','files' => true)) !!}
                    @break
                    @case('show')
                    {!! Form::model($user) !!}
                    @break
                    @case('edit')
                    {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'put','files' => true]) !!}
                    @break
                @endswitch

                <div class="row">
                    <div class="col">

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

                        <div class="card">
                            @if(in_array($action, ['show','edit']))
                                <div class="card-body row">
                                    {{ Form::bsText('name', null, ['placeholder' => "Primeiro e último nome", 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.name')]) }}
                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}

                                    @if($action === 'edit')
                                        {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
                                    @endif
                                </div>
                            @endif
                            @if($action === 'create')
                                <div class="card-body row pb-0">
                                    {{ Form::bsText('full_name', null, ['placeholder' => "Escreva o nome completo", 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.full_name')]) }}
                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show','readonly', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                                </div>
                                <div class="card-body row pt-0">
                                    {{ Form::bsText('name', null, ['placeholder' => "Escreva apenas o primeiro e o último nome", 'disabled' => $action === 'show','readonly', 'required', 'autocomplete' => 'name'], ['label' => "Primeiro e último nome"]) }}
                                    {{ Form::bsText('id_number', null, ['placeholder' => "Número de Bilhete de identidade (usado para gerar password)", 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => "Número de Bilhete de identidade (usado para gerar password)"]) }}
                                </div>
                                <div class="card-body row pt-0" hidden id="confirmpassword">
                                    <div class="col-6">

                                    </div>
                                    {{-- <div id="confirmpassword"> --}}
                                        {{ Form::bsText('confirm_password', null, ['placeholder' => "Confirmar password", 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => "Confirmar password"]) }}
                                    {{-- </div> --}}

                                </div>

                            @endif
                            {{-- {{ Form::bsUpload('fotografia', $user->image, null, 'Fotografia', ['disabled' => $action === 'show'], ['label' => 'fotografia']) }} --}}

                            <div class="card" hidden>
                                <div class="card-body row">
                                    <div class="col">
                                        <h5 class="card-title mb-3">@lang('Users::roles.roles')</h5>
                                         <select name="roles" id="">
                                                <option value="{{$roles->id}}" selected>
                                                        {{$roles->currentTranslation->display_name}}
                                                </option>
                                         </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            @switch($action)
                            @case('create')
                            <div hidden id="nextBtn">
                                @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']))
                                <button type="submit" class="btn btn-lg btn-success mb-3 float-right" >
                                    @icon('fas fa-plus-circle')
                                    Seguinte
                                </button>
                            </div>
                                @endif
                            @break
                        @endswitch
                        </div>

                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>


        $(function () {
            var hasName = false, hasFullName = false, hasPassword = false;

            $(document).on('keypress',function(e) {
                if(e.which == 13) {
                    var password = $("#id_number").val();
                    if (password.length == 14) {
                        e.preventDefault();
                        return false;
                    }else{
                        e.preventDefault();
                        return false;
                    }
                }
            });

            //^[0-9]{9}[A-Z]{2}[0-9]{3}$

            $("#full_name").on('blur', function()
            {
                var name = $(this).val();
                var nameVerified = name.toString().trim();
                var result = nameVerified.split(" ");

                if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)){
                    //alert("password not valid");
                    var result = "erro";
                    $("#full_name").addClass('is-invalid');
                    $("#full_name").removeClass('is-valid');
                     hasFullName = false;
                }else{
                    if (result.length >= 2) {
                      hasFullName = true;
                    $("#full_name").removeClass('is-invalid');
                    $("#full_name").addClass('is-valid');
                    $.ajax({
                        url: "email_convert/" + result,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',

                        success: function (dataResult) {
                            var email = dataResult.email;
                            var name = dataResult.name;
                            $("#email").val(email);
                            $("#name").val(name);

                        },
                        error: function (dataResult) {
                        // alert('error' + result);
                        }
                    });
                 }else{
                     hasFullName = false;
                     $("#full_name").removeClass('is-valid');
                     $("#full_name").addClass('is-invalid');
                     $("#email").val("");
                     $("#name").val("");
                 }
                }
                    if (hasFullName && hasPassword) {

                    }else{
                        $("#nextBtn").prop('hidden', true);
                        $("#confirmpassword").prop('hidden', true);
                    }
            })

            $("#id_number").on('blur', function(){
                var count = $(this).val().length;
                if(count === 14){

                    if ($("#id_number").val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                        hasPassword = true; //so exibir esse botao se a confirmacao e a caixa conscedirem
                        $("#confirmpassword").prop('hidden', false);
                        $("#id_number").removeClass('is-invalid');
                        $("#id_number").addClass('is-valid');
                    }else{
                        hasPassword = false; //so exibir esse botao se a confirmacao e a caixa conscedirem
                        $("#nextBtn").prop('hidden', true);
                        $("#id_number").addClass('is-invalid');
                        $("#id_number").removeClass('is-valid');
                        $("#confirm_password").val('');
                    }

                }else{
                    hasPassword = false;
                    $("#nextBtn").prop('hidden', true);
                    $("#id_number").addClass('is-invalid');
                    $("#id_number").removeClass('is-valid');
                }

                if (hasFullName && hasPassword) {

                    }else{
                        $("#nextBtn").prop('hidden', true);
                        $("#confirmpassword").prop('hidden', true);
                    }
            });

            $("#confirm_password").on('blur', function(){
                comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
            })

            $("#nextBtn").on('mouseover', function(){
                comparePasswordValues($("#id_number").val(), $("#confirm_password").val());
            })

            function comparePasswordValues(passwordInputValue, confirmInputValue) {
                if (passwordInputValue == confirmInputValue) {
                    $("#nextBtn").prop('hidden', false);
                    $("#confirm_password").addClass('is-valid');
                    $("#confirm_password").removeClass('is-invalid');
                }else{
                    $("#confirm_password").addClass('is-invalid');
                    $("#confirm_password").removeClass('is-valid');
                    $("#nextBtn").prop('hidden', true);
                }
            }

            $('input[name="email"]').on('blur', function () {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}', '{{ $user->id ?? '' }}');
            });

            $('input[name="parameters[3][14]"]').on('blur', function () {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}', '{{ $user->id ?? '' }}');
            });
            $('input[name="parameters[1][19]"]').on('blur', function () {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsMecanNumber') }}', '{{ $user->id ?? '' }}');
            });

            $('input[type="checkbox"]').on('change', function(e){
            if(e.target.checked){
                $('#exampleModal').modal();
            }
            });

            $("#closeModal").click(function()
            {
                $("#scholarship_check").prop('checked', false);
            });
            $("#cancelModalScholarship").click(function()
            {
                $("#scholarship_check").prop('checked', false);
            });

        });

        @if($action !== 'create' && $user->hasAnyRole(['teacher', 'student', 'candidado-a-estudante']) ) //

        var selectCourses = $('#course');
        var selectDisciplines = $('#disciplines');
        var userSelectedDisciplines = JSON.parse('{!! json_encode($user->disciplines->pluck('id')) !!}');
        @if($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']) ) //
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
                $.ajax({url: '{{ route('users.disciplines') }}', method: 'POST', data})
                    .then(function (resp) {

                        if (resp.disciplines.length) {
                            selectDisciplines.empty();

                            resp.disciplines.forEach(function (discipline) {
                                selectDisciplines.append('<option value="' + discipline.id + '">' + "#" + discipline.code + " - " + discipline.current_translation.display_name + '</option>');
                            });

                            selectDisciplines.selectpicker('val', userSelectedDisciplines);
                            selectDisciplines.prop('disabled', false);
                            selectDisciplines.selectpicker('refresh');
                        }
                        @if($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']) ) //
                        if (resp.classes.length) {
                            selectClasses.empty();

                            resp.classes.forEach(function (clss) {
                                selectClasses.append('<option value="' + clss.id + '">' + clss.display_name + '</option>');
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
                @if($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']) ) //
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

        $(function () {
            if (!$.isEmptyObject(selectCourses)) {
                selectCourses.change(function () {
                    updateSelectDisciplines();
                });
            }

            if (!$.isEmptyObject(selectDisciplines)) {
                selectDisciplines.change(function () {
                    userSelectedDisciplines = selectDisciplines.val();
                });
            }

            @if($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']) ) //
            if (!$.isEmptyObject(selectClasses)) {
                selectClasses.change(function () {
                    userSelectedClasses = selectClasses.val();
                });
            }
            @endif

            updateSelectDisciplines();
        });

        @endif
    </script>
@endsection
