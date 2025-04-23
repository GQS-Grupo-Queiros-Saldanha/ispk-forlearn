@switch($action)
@case('create') @section('title','RH - DOCENTE| CRIAR') @break
@case('show') @section('title','RH - DOCENTE') @break
@case('edit') @section('title','RH - DOCENTE| EDITAR') @break
@endswitch
@extends('layouts.backoffice')
@section('styles')
@parent
@endsection
@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
<style>
    .list-group li button {
        border: none;
        background: none;
        outline-style: none;
        transition: all 0.5s;
    }

    .list-group li button:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        font-weight: bold
    }

    .subLink {
        list-style: none;
        transition: all 0.5s;
        border-bottom: none;
    }

    .subLink:hover {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        border-bottom: #dfdfdf 1px solid;
    }


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

{{-- @section('content') --}}

<div class="content-panel" >

    @include('RH::index_menu')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row --mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') GESTÃO DO STAFF @break
                                @case('show') @lang('Users::users.user') @break
                                @case('edit') @lang('Users::users.edit_user') @break
                            @endswitch
                        </h1>
                    </div>
                    {{-- <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('users.create') }} @break
                            @case('show') {{ Breadcrumbs::render('users.show', $user) }} @break
                            @case('edit') {{ Breadcrumbs::render('users.edit', $user) }} @break
                        @endswitch
                    </div> --}}
                </div>
            </div>
        </div>

        
        
        
        <p class="btn-menu col-md-2 ml-3"><i style="font-size: 1.3pc;" class="fa-solid fa-bars"></i></p>
        <div class="content-fluid ml-4 mr-4 mb-5">
            <div class="d-flex align-items-start">
                
                @include('RH::index_menuStaff')
                
                <div style="background-color: #f5fcff" class="tab-content ml-1 mr-0 pl-0 pr-0 col" id="v-pills-tabContent">
                    <div class="associarCodigo">
                        <div class="ml-0 mr-0 pl-0 pr-0  pb-4 row col-12 ">
                            <div style="background: #20c7f9; height: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px " class="col-12 m-0 mb-3"></div>
                            
                                <h5 class="col-md-12 mb-3 text-right text-muted text-uppercase">CRIAR DOCENTE</h5>
                                {{-- formularios --}}
                                <div class="col-12 mb-4 border-bottom">


        
        
        
        
                                    <div class="content">
                                        <div class="container-fluid">
                                            @if($action === 'show')
                                            <div class="row">
                                                <div class="col">
                                                    <div class="float-right">
                                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning mr-3">
                                                        <i class="fas fa-plus-square"></i>
                                                                Editar formulário
                                                        </a>
                                                        @include('Users::users.partials.pdf_modal')
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                    {{-- Main content --}}
                                    <div class="content">

                                        <div class="container-fluid">
                                            @switch($action)
                                                @case('create')
                                                {!! Form::open(array('route' => 'users.store','files' => true)) !!}
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
                                                                
                                                                @if (auth()->user()->hasRole('superadmin'))
                                                                    {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.name')]) }}
                                                                @else
                                                                    {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required','readonly', 'autocomplete' => 'name'], ['label' => __('Users::users.name')]) }}
                                                                @endif 
                                                            
                                                                @if (auth()->user()->hasRole(['superadmin','staff_forlearn']))
                                                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                                                                @else
                                                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'readonly','autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                                                                @endif
                                                                
                                                                @if($action === 'edit')
                                                                    {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if($action === 'create')
                                                            <div class="card-body row pb-0">
                                                                {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.name')]) }}
                                                                {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'readonly','required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                                                            </div>
                                                            <div class="card-body row pt-0">
                                                                {{ Form::bsText('full_name', null, ['placeholder' => "Escreva apenas o primeiro e o último nome", 'disabled' => $action === 'show', 'readonly','required', 'autocomplete' => 'name'], ['label' => "Primeiro e último nome"]) }}
                                                                {{ Form::bsText('id_number', null, ['placeholder' => __('Users::users.id_number'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.id_number')]) }}
                                                            </div>
                                                            <div class="card-body row pt-0" hidden id="confirmpassword">
                                                                <div class="col-6">

                                                                </div>
                                                            {{--<div id="confirmpassword">--}}
                                                                    {{ Form::bsText('confirm_password', null, ['placeholder' => "Confirmar password", 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => "Confirmar password"]) }}
                                                            {{--</div>--}}
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
                                                        
                                                        {{-- @include('Users::users.partials.roles') --}}
                                                        @if($action !== 'create')
                                                            @include('Users::users.partials.courses')
                                                        @endif
                                                        @if ($action !== 'create')
                                                            @include('Users::users.partials.scholarship-holder')
                                                        @endif

                                                        <div class="card-body row pb-0">
                                                            @if ($action !== 'create')
                                                                @include('Users::users.partials.departments')
                                                            @endif

                                                            @if ($action !== 'create')
                                                                @include('Users::users.partials.coordinator')
                                                            @endif
                                                        </div>

                                                        @if($action !== 'create')
                                                            @include('Users::users.partials.parameters')
                                                        @endif

                                                    </div>
                                                    <div class="float-right">
                                                        @switch($action)
                                                            @case('edit')
                                                            <button type="submit" class="btn btn-success mr-3" id="editUser">
                                                                @icon('fas fa-save')
                                                                @lang('common.save')
                                                            </button>
                                                            @break
                                                            @case('create')
                                                            <div hidden id="nextBtn">
                                                                @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn'])  || auth()->user()->hasAnyPermission(['criar_docente']))
                                                                    <button type="submit" class="btn btn-success mb-3 mr-3">
                                                                        @icon('fas fa-plus-circle')
                                                                        @lang('common.create') docente
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            @break
                                                            @endswitch
                                                    </div>
                                                </div>
                                            </div>

                                            {!! Form::close() !!}


                                             <!-- Modal alerta BI existente-->
                                            <div   style="z-index: 9999999;" class="modal fade" id="modal-alerta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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



                                        </div>
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
    <script>
        $(function () {
            
            // $("#editUser").click(function (e) {
                //     if ($('input[name="parameters[3][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                //         var file = $('input[name="attachment_parameters[1][25]"]').val();
                //         var extension = file.substr((file.lastIndexOf('.') +1));
                //         if (!$('input[name="attachment_parameters[1][25]"]').val() == "") {
                //             if (extension == "jpg" || extension == "png" || extension == "jpge") {

                //             }else{
                //                 e.preventDefault();
                //             }
                //         }else{
                            
                //         }

                //     }else{
                //         $('input[name="parameters[3][14]"]').attr('autofocus', 'true');
                //         e.preventDefault();
                //     }

            // })

            //NOVA LINA
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


            $("#name").on('blur', function()
            {
                var name = $(this).val();
                var nameVerified = name.toString().trim();
                var result = nameVerified.split(" ");                

                if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)){
                    //alert("password not valid");
                    var result = "erro";
                    $("#name").addClass('is-invalid');
                    $("#name").removeClass('is-valid');
                     hasFullName = false;
                }else{
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

                        success: function (dataResult) {
                            var email = dataResult.email;
                            var name = dataResult.name;
                            $("#email").val(email);
                            $("#full_name").val(name);

                        },
                        error: function (dataResult) {
                        // alert('error' + result);
                        console.log("ERRO")
                        }
                    });

                    
                 }else{
                     hasFullName = false;
                     $("#name").removeClass('is-valid');
                     $("#name").addClass('is-invalid');
                     $("#email").val("");
                     $("#full_name").val("");
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
                    var valorBi=$(this).val();
                    $.ajax({
                        url: "get_validation_bi/"+ valorBi,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function (dataResult) {
                            console.log(dataResult);
                             if (dataResult==true) {
                                $("#confirmpassword").prop('hidden', true);
                                $("#id_number").removeClass('is-valid');
                                $("#id_number").addClass('is-invalid');
                                $("#modal-alerta").modal('show')
                             }else{
                               
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
                             }
                            
                          
                        },
                        error: function (dataResult) {
                        // alert('error' + result);
                        }
                    });

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
            
            if ($('input[name="parameters[3][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                    var file = $('input[name="attachment_parameters[1][25]"]').val();
                    var extension = file.substr((file.lastIndexOf('.') +1));
                    

                    if (!$('input[name="attachment_parameters[1][25]"]').val() == "") {
                        if (extension == "jpg" || extension == "png" || extension == "jpge") {
                            //e.preventDefault();
                        }else{
                            checkExtension = false
                                                                    
                            e.preventDefault();
                        }
                    }



                }else{
                   
                    $('input[name="parameters[3][14]"]').attr('autofocus', 'true');
                 
                }
            
           
            
            $('input[name="email"]').on('blur', function () {
                //alert("On Blur ...")
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}', '{{ $user->id ?? '' }}');
                console.log( "Forlearn" ) ;
            });

            $('input[name="parameters[3][14]"]').on('blur', function () {
                if ($('input[name="parameters[3][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                    $('input[name="parameters[3][14]"]').removeClass('is-valid');
                    $('input[name="parameters[3][14]"]').removeClass('is-invalid');
                    Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}', '{{ $user->id ?? '' }}');
                }else{
                    $('input[name="parameters[3][14]"]').removeClass('is-valid');
                    $('input[name="parameters[3][14]"]').addClass('is-invalid');
                }
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

            @if($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']) ) 
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
