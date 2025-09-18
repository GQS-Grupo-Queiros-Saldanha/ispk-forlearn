@extends('layouts.generic_index_new', ['breadcrumb_super'=> true])
@section('title',__('Users::matriculations.matriculation'))
@switch($action)
    @case('create') @section('title',__('Users::matriculations.create_matriculation')) @break
    @case('show') @section('title',__('Users::matriculations.matriculation')) @break
    @case('edit') @section('title',__('Users::matriculations.edit_matriculation')) @break
@endswitch
@section('page-title')
    @switch($action)
        @case('create')
            @lang('Users::matriculations.create_matriculation')
        @break

        @case('show')
            @lang('Users::matriculations.matriculation')
        @break

        @case('edit')
            @lang('Users::matriculations.edit_matriculation')
        @break
    @endswitch
@endsection
@section('breadcrumb')
    @switch($action)
        @case('create')
            {{ Breadcrumbs::render('matriculations.create') }}
        @break

        @case('show')
            {{ Breadcrumbs::render('matriculations.show', $matriculation) }}
        @break

        @case('edit')
            {{ Breadcrumbs::render('matriculations.edit', $matriculation) }}
        @break
    @endswitch
@endsection
@section('body')
    @switch($action)
        @case('create')
            {!! Form::open(['route' => ['matriculations.store']]) !!}
        @break

        @case('show')
            {!! Form::model($matriculation) !!}
        @break

        @case('edit')
            {!! Form::model($matriculation, ['route' => ['matriculations.update', $matriculation->id], 'method' => 'put']) !!}
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
                    <button type="submit" class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>
                        @lang('common.create')
                    </button>
                @break

                @case('edit')
                    @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn']) ||
                            auth()->user()->hasAnyPermission(['editar_matricula']))
                        <button type="submit" class="btn btn-sm btn-success mb-3">
                            <i class="fas fa-save"></i>
                            @lang('common.save')
                        </button>
                    @endif
                @break

                @case('show')
                    <a href="{{ route('matriculations.edit', $matriculation->id) }}" class="btn btn-sm btn-warning mb-3">
                        <i class="fas fa-edit"></i>
                        @lang('common.edit')
                    </a>
                    <a href="{{ route('matriculations.report', $matriculation->id) }}" target="_blank"
                        class="btn btn-sm btn-info mb-3">
                        <i class="fas fa-file-pdf"></i>
                        Gerar Boletim
                    </a>
                @break
            @endswitch

            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            @if ($action === 'create')
                                <label>@lang('Users::matriculations.student')</label>
                                {{ Form::bsLiveSelect('user', $users, old('user') ?: null, ['required', 'placeholder' => '']) }}
                            @else
                                <h5 class="card-title mb-3">@lang('Users::matriculations.student')</h5>
                                {{ $userName }}
                            @endif
                        </div>
                    </div>
                    @if ($action !== 'create')
                        <div class="col-6">
                            <div class="form-group col">
                                <h5 class="card-title mb-3">@lang('Users::matriculations.code')</h5>
                                {{ $matriculation->code }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <hr>

            @php($course = isset($matriculation) && $matriculation->user->courses ? $matriculation->user->courses->first() : null)
            @php($courseName = $course ? $course->currentTranslation->display_name : 'N/A')

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col col-6">
                            <h5 class="card-title mb-3">@lang('Users::matriculations.course')</h5>
                            <span id="course-name">{{ $courseName }}</span>
                        </div>
                        <div class="col col-6">
                            <h5 class="card-title mb-3">@lang('Users::matriculations.years')</h5>
                            {{ Form::bsLiveSelectEmpty('years[]', [], null, ['multiple', 'id' => 'years', 'disabled']) }}
                            @if ($action !== 'show')
                                <div style="/*display: flex; justify-content: right*/">
                                    <button id="load-disciplines-btn" type="submit" class="btn btn-sm btn-primary mt-2">
                                        @icon('fas fa-spinner')
                                        @lang('Users::matriculations.load_disciplines')
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="config-years-container" class="card" hidden>
                <div class="card-body row">
                    <div class="col col-12">
                        <h5 class="card-title">@lang('Users::matriculations.disciplines')</h5>
                    </div>
                </div>
                <div id="discipline-tables-container" class="card-body"></div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    <script>
        (() => {
            let storedYears = null;
            let storedClasses = null;
            let storedDisciplines = null;
            let storedDisciplinesExamOnly = null;

            @if (is_array(old()) && count(old()))
                storedYears = @json(old('years'));
                storedYears = storedYears ? storedYears.map(Number).sort().reverse() : null;
                storedClasses = @json(old('classes'));
                if (storedClasses) {
                    $.each(storedClasses, function(key, value) {
                        storedClasses[key] = parseInt(value);
                    });
                }
                storedDisciplines = @json(old('disciplines'));
                if (storedDisciplines) {
                    storedDisciplines = $.map(storedDisciplines, function(n) {
                        return n;
                    }).map(Number);
                }

                storedDisciplinesExamOnly = @json(old('disciplines_exam_only'));
                if (storedDisciplinesExamOnly) {
                    storedDisciplinesExamOnly = $.map(storedDisciplinesExamOnly, function(n) {
                        return n;
                    }).map(Number);
                }
            @elseif ($action !== 'create') /* load stored data from DB */

                storedYears = @json($stored['years'] ?? null);
                storedClasses = @json($stored['classes'] ?? null);
                storedDisciplines = @json($stored['disciplines'] ?? null);
                storedDisciplinesExamOnly = @json($stored['disciplines_exam_only'] ?? null);
            @endif

            let btnSubmit = $("#submit-btn");
            let selectUser = $('#user');
            let userCourseText = $('#course-name');
            let selectUserYears = $('#years');
            let btnLoadDisciplines = $('#load-disciplines-btn');
            let containerConfigYears = $('#config-years-container');
            let containerDisciplineTables = $('#discipline-tables-container');
            let disciplinesTables = {};

            let userData = null;
            let userCourse = null;

            function resetUserSelect() {
                userData = null;
                userCourse = null;
                userCourseText.text('{!! $courseName !!}');
                selectUserYears.prop('disabled', true);
                selectUserYears.empty();
                containerConfigYears.attr('hidden', true);
            }

            function switchUser(userId) {
                resetUserSelect();
                if (userId) {
                    let route = ("{{ route('matriculations.user.ajax', 'id_user') }}").replace('id_user', userId);
                    $.get(route, function(data) {
                        userData = data;
                        if (userData.courses && userData.courses.length) {
                            userCourse = userData.courses[0];
                            let userCourseName = userCourse.current_translation.display_name;
                            userCourseText.text(userCourseName);
                            if (isUserCandidate()) {
                                storedYears = storedYears ? storedYears : [1];
                                storedClasses = storedClasses ? storedClasses : {
                                    1: userData.classes[0].id
                                }
                                loadDisciplinesTables(storedYears);
                            }
                            loadYearSelect(userCourse.duration_value);
                        }
                    });
                }
            }

            function isUserCandidate() {
                let role = null;

                if (userData && userData.roles.length) {
                    role = userData.roles[0].id;
                }

                return role === 15;
            }

            @if ($action === 'create')

                if (!$.isEmptyObject(selectUser)) {
                    switchUser(selectUser[0].value);
                    selectUser.change(function() {
                        switchUser(this.value);
                    });
                }
            @else
                switchUser({!! $matriculation->user->id !!});
            @endif

            function loadYearSelect(courseDuration) {
                selectUserYears.prop('disabled', true);
                selectUserYears.empty();

                if (courseDuration) {
                    courseYears = [];
                    while (courseDuration) {
                        courseYears.push(courseDuration--)
                    }

                    courseYears.reverse().forEach(function(year) {
                        selectUserYears
                            .append('<option value="' + year + '">' + year + 'º Ano</option>');
                    });

                    @if ($action !== 'show')
                        selectUserYears.prop('disabled', false);
                    @endif
                    selectUserYears.selectpicker('refresh');

                    if (storedYears) {
                        selectUserYears.selectpicker('val', storedYears);

                        if (storedDisciplines) {
                            loadDisciplinesTables(storedYears);
                        }
                    }
                } else {
                    resetUserSelect();
                }
            }

            function loadDisciplinesTables(years) {
                let action = '{{ $action }}';
                containerDisciplineTables.empty();

                let disciplinesByYear = {};
                let classesByYear = {};
                years.forEach(function(year) {
                    let yearDisciplines = userCourse.study_plans.study_plans_has_disciplines.filter(function(
                        d) {
                        return d.years === year;
                    });

                    function compareDisciplines(a, b) {
                        const A = a.discipline.code.toUpperCase();
                        const B = b.discipline.code.toUpperCase();

                        let comparison = 0;
                        if (A > B) {
                            comparison = 1;
                        } else if (A < B) {
                            comparison = -1;
                        }
                        return comparison;
                    }

                    yearDisciplines.sort(compareDisciplines);

                    disciplinesByYear[year] = [];
                    yearDisciplines.forEach(function(d) {
                        disciplinesByYear[year].push({
                            id: d.discipline.id,
                            code: d.discipline.code,
                            name: d.discipline.current_translation.display_name
                        })
                    });

                    // Classes
                    let yearClasses = userCourse.classes.filter(function(c) {
                        return c.year === year;
                    });
                    classesByYear[year] = [];
                    yearClasses.forEach(function(c) {
                        classesByYear[year].push({
                            id: c.id,
                            code: c.code,
                            name: c.display_name
                        })
                    });
                });

                years.forEach(function(year) {
                    let tableId = 'year_' + year;
                    let isMatriculationYear = Math.max(...years) === year;

                    let selectedClass = null;
                    if (storedClasses && storedClasses[year]) {
                        selectedClass = parseInt(storedClasses[year]);
                    }

                    let select = $('<select>', {
                            class: 'mb-2',
                            name: 'classes[' + year + ']',
                            required: true,
                            disabled: action === 'show'
                        })
                        .append($('<option>', {
                            value: '',
                            selected: !selectedClass,
                            disabled: true,
                            hidden: true
                        }).text(''));

                    classesByYear[year].forEach(function(c) {
                        select.append($('<option>', {
                            value: c.id,
                            selected: selectedClass === c.id
                        }).text(c.name));
                    });

                    containerDisciplineTables.append($('<div>', {
                        class: isMatriculationYear ? 'row' : 'row mt-3'
                    }).append(
                        $('<div>', {
                            class: 'col-12'
                        }).append(
                            $('<h5>', {
                                class: 'card-title mb-2'
                            }).text(year + 'º Ano'),
                            $('<span>', {
                                style: 'font-weight: bold; margin-right: 6px'
                            }).text('Turma:'),
                            select,
                            $('<div>', {
                                id: tableId
                            })
                        )
                    ));

                    let table = $('#' + tableId);
                    disciplinesByYear[year].forEach(function(d) {
                        let disciplineIsChecked = isMatriculationYear;
                        if (storedDisciplines) {
                            disciplineIsChecked = $.inArray(d.id, storedDisciplines) !== -1;
                        }

                        table.append(
                            $('<div>', {
                                id: 'container_discipline_' + d.id,
                                class: 'col'
                            }).append(
                                $('<label>', {
                                    class: 'form-check-label',
                                    style: 'color: black !important'
                                }).append(
                                    $('<input>', {
                                        id: 'check_discipline_' + d.id,
                                        value: d.id,
                                        class: 'check-discipline form-check-input-center',
                                        type: 'checkbox',
                                        checked: disciplineIsChecked,
                                        name: 'disciplines[' + year + '][]',
                                        disabled: action === 'show',
                                        'data-id': d.id,
                                    }),
                                    $('<span>', {
                                        style: 'font-size: 13px'
                                    }).text('#' + d.code + ' - ' + d.name)
                                )
                            )
                        );
                        if (!isMatriculationYear) {
                            let examOnlyIsChecked = false;
                            if (storedDisciplinesExamOnly) {
                                examOnlyIsChecked = $.inArray(d.id, storedDisciplinesExamOnly) !== -1;
                            }

                            table.append(
                                $('<div>', {
                                    id: 'container_discipline_regime_' + d.id,
                                    class: 'col',
                                    hidden: !disciplineIsChecked
                                }).append(
                                    $('<label>', {
                                        class: 'form-check-label',
                                        style: 'color: #595959; margin-left: 20px'
                                    }).append(
                                        $('<input>', {
                                            id: 'check_discipline_regime_' + d.id,
                                            value: d.id,
                                            class: 'check-discipline-regime form-check-input-center',
                                            type: 'checkbox',
                                            checked: examOnlyIsChecked,
                                            name: 'disciplines_exam_only[' + year + '][]',
                                            disabled: action === 'show',
                                            'data-id': d.id,
                                        }),
                                        $('<span>').text('Inscrição para exame')
                                    )
                                )
                            );
                        }
                    });
                });

                $('.check-discipline').unbind().change(function() {
                    let isChecked = this.checked;

                    let targetContainer = $('#container_discipline_regime_' + this.dataset.id);
                    let targetInput = $('#check_discipline_regime_' + this.dataset.id);

                    targetInput.prop('checked', false);
                    targetContainer.attr('hidden', !isChecked);
                });

                containerConfigYears.attr('hidden', false);
            }

            function submitMatriculation(id) {
                let thisTab = window;
                let myNewTab = window.open('about:blank', '_blank');
                let route = ("{{ route('matriculations.user.pdf', 'id_user') }}").replace('id_user', id);

                $.ajax({
                    method: "GET",
                    url: route
                }).done(function(url) {
                    myNewTab.location.href = url;
                    thisTab.location.href = thisTab.location.href;
                });
            }

            btnLoadDisciplines.on('click', function(event) {
                event.preventDefault();
                let selectedYears = selectUserYears.val().map(Number).sort().reverse();
                loadDisciplinesTables(selectedYears);
            });

            btnSubmit.on('click', function() {
                submitMatriculation(1);
            })

        })();
    </script>
@endsection