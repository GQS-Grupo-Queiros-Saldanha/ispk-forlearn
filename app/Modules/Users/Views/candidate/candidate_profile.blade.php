@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('styles-new')
    @parent
    <style>
        .user-profile-image { width: 200px !important;}

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
    @endsection
    @section('page-title')
        @switch($action)
            @case('create')
                @lang('Users::users.create_user')
            @break
    
            @case('show')
                Candidato a estudante
            @break
    
            @case('edit')
                Formulário de candidatura
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
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection
@section('body')
    <div class="content">
        <div class="container-fluid">
            @if ($action === 'show')
                <div class="row">
                    <div class="col">
                        <div class="float-right">
                            @include('Users::users.partials.pdf_modal',["hidden_btn" => true])
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="content">
        <div class="container-fluid form-inline-slc">
            @switch($action)
                @case('create')
                    {!! Form::open(['route' => 'candidates.store', 'files' => true]) !!}
                @break

                @case('show')
                    {!! Form::model($user) !!}
                @break

                @case('edit')
                    {!! Form::model($user, [
                        'route' => ['candidates.update', $user->id],
                        'method' => 'put',
                        'files' => true,
                        'id' => 'formularioEdit',
                    ]) !!}
                @break
            @endswitch
            <div class="row" @if($action == "show") id="form-candidatura" @endif>
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
                    @switch($action)
                        @case('create')
                            @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']))
                                <button type="submit" class="btn btn-sm btn-success mb-3">
                                    @icon('fas fa-plus-circle')
                                    @lang('common.create')
                                </button>
                            @endif
                        @break
                    @endswitch

                    @if ($action !== 'create')
                        @include('Users::users.partials.candidate_parameters')
                    @endif
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>


    @include('Users::candidate.candidate_modal')
@endsection
@section('scripts-new')
    @parent
    @include('Users::script')
    <script src="{{ asset('js/new_tabpane_form.js')}} ">
        
    </script>
    <script>
        $(function() {

            $('[name="email"]').keyup(function(e){
                $('input[name="parameters[11][312]"]').val($(this).val());
            })

            $('input[name="parameters[3][14]"]').keyup(function(e) {
                $('input[name="parameters[3][49]"]').val($(this).val());
            })

            $('input[name="parameters[13][14]"]').keyup(function(e) {
                $('input[name="parameters[13][49]"]').val($(this).val());
            })

            $("#editUser").click(function(e) {
                if ($('input[name="parameters[13][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {

                } else {
                    $('input[name="parameters[13][14]"]').attr('autofocus', 'true');
                    e.preventDefault();
                }
            })

            $('input[name="parameters[13][14]"]').on('blur', function() {
                if ($('input[name="parameters[13][14]"]').val().match(/^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {
                    $('input[name="parameters[13][14]"]').removeClass('is-valid');
                    $('input[name="parameters[13][14]"]').removeClass('is-invalid');
                    Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}',
                        '{{ $user->id ?? '' }}');
                } else {
                    $('input[name="parameters[13][14]"]').removeClass('is-valid');
                    $('input[name="parameters[13][14]"]').addClass('is-invalid');
                }
            });

            @if (auth()->user()->hasAnyRole(['candidado-a-estudante']))
                $("#formularioEdit").submit(function() {
                    $('#exampleModalCenter').modal('show')
                });
            @endif

            //alert($("#attachment_parameters[3][56]").val());

            //Codigo de habilitar as caixas de textos.

            @if (auth()->user()->hasAnyRole(['superadmin', 'staff_candidaturas', 'staff_forlearn']))
                $('input[name="parameters[2][1]"]').prop('readonly', false);
            @else
                $('input[name="parameters[2][1]"]').prop('readonly', true);
            @endif

            @if (auth()->user()->hasAnyRole(['superadmin']))
                $('input[name="email"]').prop('readonly', false);
            @else
                $('input[name="email"]').prop('readonly', true);
            @endif
            //fim do código


            $('input[name="parameters[1][19]"]').prop('required', true);
            $('input[name="parameters[1][19]"]').prop('readonly', true);

            $('input[name="parameters[1][311]"]').prop('readonly', true);

            // $('input[name="parameters[3][55]"]').prop('required', true);

            //$('input[name="parameters[13][49]"]').prop('readonly', true);
            $('input[name="parameters[13][49]"]').prop('required', true);

            // $('input[name="parameters[13][55]"]').prop('required', true);


            $('input[name="parameters[13][16]"]').prop('required', true);
            $('input[name="parameters[6][37]"]').prop('required', true);
            // $('input[name="parameters[6][34]"]').prop('required', true);
            //$('input[name="parameters[6][34]"]').prop('readonly', true);
            // $('input[name="parameters[2][48]"]').prop('required', true);
            // $('input[name="parameters[2][46]"]').prop('required', true); ->Rua
            // $('input[name="parameters[2][47]"]').prop('required', true);
            // $('input[name="parameters[2][45]"]').prop('required', true); ->Bairro
            $('input[name="parameters[2][24]"]').prop('required', true);
            $('input[name="parameters[2][23]"]').prop('required', true);
            $('input[name="parameters[13][15]"]').prop('required', true);


            $('input[name="parameters[11][312]"]').prop('readonly', true);
            $('input[name="parameters[11][311]"]').prop('readonly', true);


            $('input[name="parameters[6][37]"]').attr('maxlength', '9');

            // $('input[name="attachment_parameters[3][17]"]').on('blur', function(){
            //     alert("Ola!");
            // })

            //$('input[name="attachment_parameters[3][56]"]').prop('required', true);

            $("#course").prop('required', true);

            $("#disciplines").prop('required', true);
            $("#classes").prop('required', true);

            //Validar os campos --Estado civil
            $('select[name="parameters[2][4]"]').prop('required', true);
            //Escola do ensino médio
            $('input[name="parameters[12][41]"]').prop('required', true);

            //Percurso do ensino médio
            $('select[name="parameters[12][68]"]').on('change', function() {
                //Escola do ensino médio
            })

            // $('select[title="Nacionalidade"]').on('change', function(){
            //     select[title="Nacionalidade"]
            // })

            $('input[name="email"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}',
                    '{{ $user->id ?? '' }}');
            });

            $('input[name="parameters[3][14]"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}',
                    '{{ $user->id ?? '' }}');
            });
            $('input[name="parameters[0][1]"]').on('click', function() {
                // Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}', '{{ $user->id ?? '' }}');
                alert("nome")
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

            $("#email").attr('readonly',true);
        });

        //@if (
            $action !== 'create' &&
                $user->hasAnyRole(['teacher', 'student', 'candidado-a-estudante', 'staff_gestor_forlearn', 'superadmin'])) //

        var selectCourses = $('#course');
        var selectDisciplines = $('#disciplines');
        var userSelectedDisciplines = JSON.parse('{!! json_encode($user->disciplines->pluck('id')) !!}');
        @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher']))
            var selectClasses = $('#classes');
            var userSelectedClasses = JSON.parse('{!! json_encode($user->classes->pluck('id')) !!}');
        @endif

        function updateSelectDisciplines() {
            var courseIds = getCourses();
            if (courseIds.length) {
                var data = {
                    courses: courseIds,
                    user: {!! $user->id !!},
                    _token: "{{ csrf_token() }}"
                };
                $.ajax({
                        url: '{{ route('candidates.disciplines') }}',
                        method: 'POST',
                        data
                    })
                    .then(function(resp) {
                        if (resp.disciplines.length > 0) {
                            selectDisciplines.empty();
                            resp.disciplines.forEach(function(discipline) {
                                selectDisciplines.append('<option value="' + discipline.id + '">' + "#" +
                                    discipline.code + " - " + discipline.current_translation.display_name +
                                    '</option>');
                            });

                            selectDisciplines.selectpicker('refresh');
                            selectDisciplines.selectpicker('val', userSelectedDisciplines);
                            selectDisciplines.prop('disabled', false);
                            selectDisciplines.selectpicker('selectAll');
                        }
                        @if ($action != 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                            
                            if (resp.classes.length > 0) {
                                
                                selectClasses.empty();
                                
                                resp.classes.forEach(function(clss) {
                                    selectClasses.append('<option value="' + clss.id + '">' + clss.display_name + '</option>');
                                     console.log(selectClasses);
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
                // selectDisciplines.selectpicker('refresh');
                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                    selectClasses.selectpicker('deselectAll');
                    selectClasses.prop('disabled', true);
                    // selectClasses.selectpicker('refresh');
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
                    if ($("#course option:selected").length > 2) {
                        selectCourses[0].selectedOptions[2].selected = false;
                        selectCourses.selectpicker('refresh');
                        console.log($("#course option:selected").length);
                    }
                });
            }

            if (!$.isEmptyObject(selectDisciplines)) {
                selectDisciplines.change(function() {
                    userSelectedDisciplines = selectDisciplines.val();
                });
             }

            @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher','superadmin'])) 
              
                 if (!$.isEmptyObject(selectClasses)) {
                            selectClasses.change(function() {
                            userSelectedClasses = selectClasses.val();
                      });
                      
                      
                      
                  }
                    
                  
            @endif

            updateSelectDisciplines();
        });
        @endif

        deleteCol();

        function deleteCol() {
            const arrayForm = ["parameters[7][41]", "parameters[12][41]"];
            arrayForm.forEach(item => {
                const schoolMiddle = $(`[name="${item}"]`);
                if (schoolMiddle && schoolMiddle.length == 1) {
                    divForm = schoolMiddle[0].parentElement;
                    if (divForm.classList.contains('col')) {
                        divForm.classList.remove('col');
                    }
                }
            });
        }

    </script>
@endsection
