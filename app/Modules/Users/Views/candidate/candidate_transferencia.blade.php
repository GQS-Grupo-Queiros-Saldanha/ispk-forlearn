@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('styles-new')
    @parent
    <style>
        .user-profile-image {
            width: 200px !important;
        }
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
            Formulário de Transferência
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
    <li class="breadcrumb-item disabled">
        <a href="#">Transferência</a>
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
                            @if ($action === 'show')
                                @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_transferências', 'staff_gestor_forlearn']))
                                    <a href="{{ route('candidates.edit', $user->id) }}"
                                        class="btn btn-warning mt-3 mb-3 mr-3">
                                        <i class="fas fa-plus-square"></i>
                                        Editar formulário
                                    </a>

                                    @include('Users::users.partials.pdf_modal')
                                @else
                                    <a href="{{ route('candidates.edit', $user->id) }}"
                                        class="btn btn-warning mb-3 mr-3 mt-3">
                                        <i class="fas fa-plus-square"></i>
                                        Editar formulário
                                    </a>

                                    @include('Users::users.partials.pdf_modal')

                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="content">
        <div class="container-fluid form-inline-slc">
            @php $action = $options->action; @endphp
            @switch($action)
                @case('create')
                    {!! Form::open(['route' => ['transferencia.up', $options->userCandidate->user_id], 'files' => true]) !!}
                @break

                @case('show')
                    {!! Form::model($user) !!}
                @break

                @case('edit')
                    {!! Form::model($user, [
                        'route' => ['transferencia.up', $options->userCandidate->user_id],
                        'method' => 'post',
                        'files' => true,
                        'id' => 'formularioEdit',
                    ]) !!}
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

                    <div class="card">
                        <input type="hidden" name="transferencia" value="{{ $options->nextCode }}">
                        @if (in_array($action, ['show', 'edit']))
                            <div class="card-body row">
                                @if (auth()->user()->hasRole('candidado-a-estudante'))
                                    {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'readonly', 'required', 'autocomplete' => 'name', 'readonly'], ['label' => 'Primeiro e o último nome']) }}
                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'readonly', 'required', 'autocomplete' => 'email', 'readonly'], ['label' => __('Users::users.email')]) }}
                                @else
                                    {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'readonly', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e o último nome']) }}
                                    {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'readonly', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                                @endif

                                @if ($action === 'edit')
                                    {{ Form::bsPassword('password', ['placeholder' => __('Users::users.password'), 'disabled' => $action === 'show', 'required' => $action === 'create', 'autocomplete' => 'new-password'], ['label' => __('Users::users.password')]) }}
                                @endif
                            </div>
                        @endif
                        @if ($action === 'create')
                            <div class="card-body row pb-0">
                                {{ Form::bsText('name', null, ['placeholder' => __('Users::users.name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => 'Primeiro e o último nome']) }}
                                {{ Form::bsEmail('email', null, ['placeholder' => __('Users::users.email'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.email')]) }}
                            </div>
                            <div class="card-body row pt-0">
                                {{ Form::bsText('full_name', null, ['placeholder' => __('Users::users.full_name'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'name'], ['label' => __('Users::users.full_name')]) }}
                                {{ Form::bsText('id_number', null, ['placeholder' => __('Users::users.id_number'), 'disabled' => $action === 'show', 'required', 'autocomplete' => 'email'], ['label' => __('Users::users.id_number')]) }}
                            </div>
                        @endif

                        @if ($action !== 'create')
                            @if (auth()->user()->hasAnyRole(['candidado-a-estudante']))
                                @include('Users::users.partials.candidate_courses',[
                                    "hidden" => true
                                ])
                            @else
                                @php
                                    $currentUserIsAuthorized = auth()
                                        ->user()
                                        ->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_transferências', 'staff_matriculas', 'staff_gestor_forlearn']);
                                @endphp
                                <div class="card">
                                    <div class="card-body row">

                                        <div class="col col-4">
                                            <h5 class="card-title mb-3">@lang('Users::users.courses')</h5>
                                            @if ($action !== 'show' && $currentUserIsAuthorized)
                                                {{ Form::bsLiveSelect('course[]', $courses, $action === 'create' ? old('courses') : $user->courses->pluck('id'), ['multiple', 'id' => 'course']) }}
                                            @else
                                                {!! implode(', ', $user->courses->pluck('currentTranslation.display_name')->toArray()) !!}
                                            @endcan
                                    </div>

                                    <div class="col col-4" hidden>
                                        <h5 class="card-title mb-3">@lang('Users::users.disciplines')</h5>
                                        @if ($action !== 'show' && $currentUserIsAuthorized)
                                            {{ Form::bsLiveSelectEmpty('disciplines[]', [], null, ['multiple', 'id' => 'disciplines', 'disabled']) }}
                                        @else
                                            {!! implode(', ', $user->disciplines->pluck('currentTranslation.display_name')->toArray()) !!}
                                        @endif
                                    </div>

                                    <div class="col col-4">
                                        <h5 class="card-title mb-3">@lang('GA::classes.class')</h5>
                                        @if ($action !== 'show' && $currentUserIsAuthorized)
                                            {{ Form::bsLiveSelectEmpty('classes[]', [], null, ['multiple', 'id' => 'classes', 'disabled']) }}
                                        @else
                                            {!! implode(', ', $user->classes->pluck('display_name')->toArray()) !!}
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endif


                    @if ($action !== 'create')
                        @include('Users::users.partials.candidate_parameters',[
                            "candidatura_hidden" => true
                        ])
                    @endif

                </div>
                <div class="float-right">
                    @switch($action)
                        @case('edit')
                            <button type="submit" class="btn btn-success mb-3 mr-3" id="editUser">
                                @icon('fas fa-save')
                                @lang('common.save')
                            </button>
                        @break
                    @endswitch
                </div>
            </div>
        </div>
        {!! Form::close() !!}

    </div>

    <input type="hidden" id="code" value="{{ $options->nextCode }}" />
    @include('Users::candidate.candidate_modal')
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {

            $("#editUser").click(function(e) {
                if ($('input[name="parameters[13][14]"]').val().match(
                        /^[0-9]{9}[A-Z]{2}[0-9]{3}$/)) {} else {
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

            @if (auth()->user()->hasAnyRole(['superadmin', 'staff_transferências', 'staff_forlearn']))
                $('input[name="parameters[2][1]"]').prop('readonly', false);
            @else
                $('input[name="parameters[2][1]"]').prop('readonly', true);
            @endif

            @if (auth()->user()->hasAnyRole(['superadmin']))
                $('input[name="email"]').prop('readonly', false);
            @else
                $('input[name="email"]').prop('readonly', true);
            @endif

            $('input[name="parameters[1][19]"]').prop('required', true);
            $('input[name="parameters[1][19]"]').prop('readonly', true);

            $('input[name="parameters[1][311]"]').prop('readonly', true);

            $('input[name="parameters[13][49]"]').prop('required', true);

            $('input[name="parameters[13][16]"]').prop('required', true);
            $('input[name="parameters[6][37]"]').prop('required', true);
            $('input[name="parameters[2][24]"]').prop('required', true);
            $('input[name="parameters[2][23]"]').prop('required', true);
            $('input[name="parameters[13][15]"]').prop('required', true);

            $('input[name="parameters[11][312]"]').prop('readonly', true);
            $('input[name="parameters[11][311]"]').prop('readonly', true);

            $('input[name="parameters[6][37]"]').attr('maxlength', '9');

            $("#course").prop('required', true);

            $("#disciplines").prop('required', true);
            $("#classes").prop('required', true);

            $('select[name="parameters[2][4]"]').prop('required', true);
            $('input[name="parameters[12][41]"]').prop('required', true);


            $('input[name="email"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.exists') }}',
                    '{{ $user->id ?? '' }}');
            });

            $('input[name="parameters[3][14]"]').on('blur', function() {
                Forlearn.checkIfModelFieldExists(this, '{{ route('users.existsParameter') }}',
                    '{{ $user->id ?? '' }}');
            });
            $('input[name="parameters[0][1]"]').on('click', function() {
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

        });

        $('input[name="parameters[11][311]"]').val($('#code').val());

        //@if (
            $action !== 'create' &&
                $user->hasAnyRole(['teacher', 'student', 'candidado-a-estudante', 'staff_gestor_forlearn', 'superadmin'])) //

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
                @if ($action !== 'create' && $user->hasAnyRole(['candidado-a-estudante', 'teacher'])) //
                    selectClasses.selectpicker('deselectAll');
                    selectClasses.prop('disabled', true);
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
                    }
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
