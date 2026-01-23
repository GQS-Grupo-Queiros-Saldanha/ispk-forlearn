<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@endsection
@switch($action)
    @case('create')
        @section('page-title', __('Users::matriculations.create_matriculation') . ' - ' .
            $lective_year->TRANSLATIONS[0]['display_name'])
        @break

        @case('show')
            @section('page-title', __('Users::matriculations.matriculation'))
        @break

        @case('edit')
            @section('page-title', __('Users::matriculations.edit_matriculation'))
        @break
    @endswitch
    @section('breadcrumb')
        <li class="breadcrumb-item">
            <a href="/">Home</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('matriculations.index') }}">Matrículas</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Criar</li>
    @endsection
    @section('body')
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                        {!! Form::open(['route' => ['confirmation_matriculation.store']]) !!}
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

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        @if ($action === 'create')
                                            
                                            <label>@lang('Users::matriculations.student')</label>
                                            {{ Form::bsLiveSelect('user', $users, old('user') ?: null, ['required', 'placeholder' => '']) }}
                                        @else
                                            <h5 class="card-title mb-3">@lang('Users::matriculations.student')</h5>
                                            {{ $userName }} 
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        {{-- O botão estava aqui --}}
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

                        <div id="config-years-containerx" class="card" hidden  style="margin-top: -15px;">
                            <div class="card-body row">
                                <div class="col col-12">
                                    <h5 class="card-title">@lang('Users::matriculations.disciplines')</h5>
                                </div>
                            </div>
                            <div id="discipline-tables-containerx" class="card-body">
                            </div>
                        </div>
                        
                        @switch($action)
                           @case('create')
                                <div hidden>
                                    <input type="hidden" value="{{ $lective_year->id }}" name="anoLective" required id="flag_id_lective">
                                </div>
                                <div id="groupBTNconf" class="mt-3 d-none">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-square"></i>
                                        Criar confirmação de matrícula
                                    </button>
                                </div>
                                @break
                        @endswitch                        
                        
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('scripts-new')
        @parent
        
        <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
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

                window.showSelect = function (id) {
                    if ($("#check_discipline_" + id).is(':checked')) {
                        $("#checkbox_group_" + id).prop('hidden', false);
                        $("#checkbox_item_" + id).prop('disabled', false);
                    } else {
                        $("#checkbox_group_" + id).prop('hidden', true);
                        $("#checkbox_item_" + id).prop('disabled', true);
                    }
                };
                $("#groupBTNconf").removeClass("d-none");
                function toggleSubmitButton() {
                  

                    if ($('.check-discipline:checked').length > 0) {
                        $('#groupBTNconf').removeClass('d-none');
                    } else {
                        $('#groupBTNconf').addClass('d-none');
                    }
                }



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
                    
                    let groupBTNconf = $("#groupBTNconf");

                    if (userId) {
                        let lective_year = $("#flag_id_lective").val();
                        let IDUSER_LECTIVE_YEAR = userId + "," + lective_year;

                        let route = ("{{ route('confirmations.user.ajax', 'id_user') }}").replace('id_user',
                            IDUSER_LECTIVE_YEAR);
                        $.get(route, function(data) {
                            userData = data;
                            $('#discipline-tables-containerx').html(userData.html);
                            toggleSubmitButton();

                        });
                        
                        // if(groupBTNconf.hasClass('d-none')){
                        //     groupBTNconf.removeClass('d-none');
                        // }                        
                        
                    }else{
                        
                        if(!groupBTNconf.hasClass('d-none')){
                            groupBTNconf.addClass('d-none');
                        }                        
                        
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
                            if (this.value != "") {
                                $("#config-years-containerx").prop('hidden', false);
                            } else {
                                $("#config-years-containerx").prop('hidden', true);
                            }

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
                        // Disciplines
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

                        toggleSubmitButton();

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
                        // thisTab.location.reload(true);
                        thisTab.location.href = thisTab.location.href;
                    });
                }

                $(function() {
                    btnLoadDisciplines.on('click', function(event) {
                        event.preventDefault();
                        let selectedYears = selectUserYears.val().map(Number).sort().reverse();
                        loadDisciplinesTables(selectedYears);
                    });

                    btnSubmit.on('click', function() {
                        submitMatriculation(1);
                    })
                });
            })();
        </script>
    @endsection
