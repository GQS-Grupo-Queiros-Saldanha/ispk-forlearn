@switch($action)
    @case('create') @section('title',__('Lessons::lessons.create_lesson')) @break
@case('show') @section('title',__('Lessons::lessons.lesson')) @break
@case('edit') @section('title',__('Lessons::lessons.edit_lesson')) @break
@endswitch

@extends('layouts.backoffice')

@php($multiPermission = auth()->user()->can('manage-lessons-others'))


@section('content')
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('Lessons::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            @switch($action)
                                @case('create') @lang('Lessons::lessons.create_lesson') @break
                                @case('show') @lang('Lessons::lessons.lesson') @break
                                @case('edit') @lang('Lessons::lessons.edit_lesson') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
                            @case('create') {{ Breadcrumbs::render('lessons.create') }} @break
                            @case('show') {{ Breadcrumbs::render('lessons.show', $lesson) }} @break
                            @case('edit') {{ Breadcrumbs::render('lessons.edit', $lesson) }} @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                @switch($action)
                    @case('create')
                    {!! Form::open(['route' => ['lessons.save']]) !!}
                    @break
                    @case('show')
                    {!! Form::model($lesson) !!}
                    @break
                    @case('edit')
                    {!! Form::model($lesson, ['route' => ['lessons.update', $lesson->id], 'method' => 'put']) !!}
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
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                <i class="fas fa-save"></i>
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')
                            <a href="{{ route('lessons.edit', $lesson->id) }}"
                               class="btn btn-sm btn-warning mb-3">
                                <i class="fas fa-edit"></i>
                                @lang('common.edit')
                            </a>
                            @break
                        @endswitch

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.teachers')</label>
                                        @if (auth()->user()->hasRole('teacher'))
                                            <select name="teacher" id="teacher" class="form-control">
                                                <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                                            </select>
                                        @else
                                            @if($multiPermission)
                                                {{ Form::bsLiveSelect('teacher', $teachers, null, ['required', 'placeholder' => '']) }}
                                            @else
                                                <input id="teacher" name="teacher" hidden aria-hidden="true" value="{{auth()->id()}}">
                                                {{ auth()->user()->name }}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    @if($multiPermission)
                                        <div class="form-group col p-0 mb-0">
                                            {{ Form::bsCustom('occured_at', Carbon\Carbon::now()->toDatetimelocalString('minute'), ['id' => 'occured_at', 'type' => 'datetime-local', 'required'], ['label' => __('Lessons::lessons.date')]) }}
                                        </div>
                                    @else
                                        <div class="form-group col">
                                            <input id="occured_at" name="occured_at" hidden aria-hidden="true" value="{{Carbon\Carbon::now()->toDatetimelocalString('minute')}}">
                                            <label>@lang('Lessons::lessons.date')</label>
                                            {{ Carbon\Carbon::now() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="card">
                            @if($multiPermission)
                                <div class="row" id="load-disciplines">
                                    <div class="col-6">
                                        <div class="form-group col p-0">
                                            <button id="load-disciplines-btn"
                                                    type="submit"
                                                    class="btn btn-sm btn-primary"
                                                    style="width: 150px; margin-left: 15px"
                                                    disabled>
                                                @icon('fas fa-spinner')
                                                Carregar Disciplinas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="row" id="disciplines-container" hidden>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.discipline_class')</label>
                                        {{ Form::bsLiveSelectEmpty('discipline', [], null, ['required', 'placeholder' => '']) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.regime')</label>
                                        {{ Form::bsLiveSelectEmpty('regime', [], null, ['id' => 'regime', 'required', 'placeholder' => '']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="summary-container" hidden>
                            <hr>
                            <div class="row">
                                <input id="summary" name="summary" hidden aria-hidden="true">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <div id="summary-name">...</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group col">
                                        <div id="summary-description">...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="observation-container" hidden>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <label for="observation">Observação</label>
                                        <textarea name="observation" id="observation-box" cols="50" rows="5" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="student-container" hidden>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <div id="student-table"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {!! Form::close() !!}



                <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-light">
                                <h5 class="modal-title" id="exampleModalLabel">ALERTA | Docente disciplina</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="float-right">
                                    <p class="text-danger" style="font-weight:bold; !important" id="docenteNome">
                                        {{-- Não existem disciplinas associadas ao docente ({{auth()->user()->name}}) --}}
                                        Está mensagem de erro está a ser apresentada, por uma das razões listadas abaixo:
                                    <br></p>
                                </div>
                                <br>
                                <br>
                                <div style="margin-top:25px; !important">
                                    <p style="padding:5px; !important" id="idTExto"></p>
        
                                    <ul>
                                        <li style="padding:5px; !important"  id="text1"> 
                                            Verifica se o docente tem disciplinas por lecionar hoje;                                   
                                        </li>
                                        <li style="padding:5px; !important"  id="text1"> 
                                            Verifica se o docente tem disciplinas associadas;                                   
                                        </li>
                                        <li style="padding:5px; !important" id="text2">  
                                            Caso o docente tenha disciplinas associadas e ainda assim elas não estão sendo carregadas, por favor contacta o gestor forLEARN ...
                                        </li>
                                    </ul>
                                </div>
        
                            </div>
                            <div class="modal-footer" >
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-lg" id="disciplina_regime" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-light">
                                <h5 class="modal-title" id="exampleModalLabel">ALERTA | Docente disciplina regime</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="float-right">
                                    <p class="text-danger" style="font-weight:bold; !important" id="docenteRegime"> 
                                        {{-- Não existem disciplinas associadas ao docente ({{auth()->user()->name}}) --}}
                                    <br></p>
                                </div>
                                <br>
        
                            </div>
                            <div class="modal-footer" >
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>                                
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
                <div class="modal fade bd-example-modal-lg" id="disciplina_summary" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-light">
                                <h5 class="modal-title" id="exampleModalLabel">ALERTA | Docente disciplina sumário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
        
                            <div class="modal-body">
                                <div class="float-right">
                                    <p class="text-danger" style="font-weight:bold; !important" id="docenteSummary"> 
                                        {{-- Não existem disciplinas associadas ao docente ({{auth()->user()->name}}) --}}
                                    <br></p>
                                </div>
                                <br>
        
                            </div>
                            <div class="modal-footer" >
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Contactar gestores forLEARN</button>                                
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
        var selectedTeacher = null;
        var inputedOccuredAt = null;
        @if($multiPermission) //
            var selectTeacher = $('#teacher');
            var occuredAtInput = $('#occured_at');
            var loadDisciplinesBtn = $('#load-disciplines-btn');
        @else //
            selectedTeacher = parseInt({{auth()->user()->id}});
            inputedOccuredAt = new Date('{{Carbon\Carbon::now()}}');
        @endif //
        var disciplineContainer = $('#disciplines-container');
        var selectDiscipline = $('#discipline');
        var selectedDiscipline = null;
        var selectLessonRegime = $('#regime');
        var disciplineData = null;
        var eventData = null;
        var summaryContainer = $('#summary-container');
        var summaryInput = $('#summary');
        var summaryNameText = $('#summary-name');
        var summaryDescriptionText = $('#summary-description');
        var studentContainer = $('#student-container');
        var studentTable = $('#student-table');

        var observationContainer = $("#observation-container");

        var disciplina_nome = null;
        var nome_discipline = null;

        function resetDisciplines() {
            if (typeof loadDisciplinesBtn !== 'undefined' && loadDisciplinesBtn) {
                loadDisciplinesBtn.attr('disabled', true);
            }

            disciplineContainer.attr('hidden', true);
            selectDiscipline.prop('disabled', true);
            selectDiscipline.empty();

            resetSelectDiscipline();
        }

        function resetSelectDiscipline() {
            selectedDiscipline = null;

            setDisabledStateRegimes(true);

            summaryContainer.attr('hidden', true);
            studentContainer.attr('hidden', true);
            observationContainer.attr('hidden', true);
        }

        function setDisabledStateRegimes(state) {
            var selectLessonRegimeBtn = $('button[data-id="regime"]');

            selectLessonRegime.prop('disabled', state);
            selectLessonRegimeBtn.prop('disabled', state);

            if (state) {
                selectLessonRegime.empty();
                selectLessonRegimeBtn.addClass('disabled');
                selectLessonRegime.selectpicker('val', "");
                            

            } else {
                selectLessonRegimeBtn.removeClass('disabled');
                selectLessonRegime.selectpicker('refresh');
                
            }
        }

        function isValidDate(d) {
            return d instanceof Date && !isNaN(d);
        }

        function teacherAndDateCheck() {
            var date = new Date(inputedOccuredAt);

            if (selectedTeacher && inputedOccuredAt && isValidDate(date)) {
                loadDisciplinesBtn.attr('disabled', false);
            } else {
                resetDisciplines();
            }
        }

        // CARREGA AS DISCIPLINAS NO SELECTOR
        function loadDisciplineData() {
            resetDisciplines();

            var date = new Date(inputedOccuredAt);
            if (selectedTeacher && inputedOccuredAt && isValidDate(date)) {

                if (typeof loadDisciplinesBtn !== 'undefined' && loadDisciplinesBtn) {
                    loadDisciplinesBtn.attr('disabled', false);
                }

                var dateUnix = parseInt((date.getTime() / 1000).toFixed(0))
                let route = ("{{ route('lessons.disciplines') }}") + '?teacher=' + selectedTeacher + '&date=' + dateUnix;
                $.get(route, function (data) {
                    disciplineData = data;

                    if (disciplineData.disciplines.length) {
                        selectDiscipline.append('<option class="disabled" value=""></option>');

                        disciplineData.disciplines.forEach(function (discipline) {
                            selectDiscipline
                                .append('<option value="' + discipline.id + '">' + discipline.display_name + '</option>');

                            disciplina_nome = discipline.display_name;
                        });                        

                        selectDiscipline.prop('disabled', false);
                        selectDiscipline.selectpicker('refresh');
                        disciplineContainer.attr('hidden', false);
                    }
                    else {
                        $("#exampleModal").modal('show');
                    }
                });
            }
        }

        function mountSelectedDiscipline(uid) {
            
            if (uid) {
                var split = uid.split('-');
                var disciplineId = parseInt(split[0]);
                var classId = parseInt(split[1]);

                if (disciplineId && classId) {
                    let route = ("{{ route('lessons.discipline-class') }}") + '?discipline=' + disciplineId + '&class=' + classId;                    
                    $.get(route, function (data) {
                        eventData = data;
                        loadEventData();                        
                    });

                } else {
                    resetSelectDiscipline();
                }
            } else {
                resetSelectDiscipline();
            }

        }

        // DISCIPLINA REGIME
        function loadEventData() {
            setDisabledStateRegimes(true);

            if (eventData.regimes.length) {
                selectLessonRegime.append('<option class="disabled" value=""></option>');
                
                eventData.regimes.forEach(function (regime) {
                    selectLessonRegime
                        .append('<option value="' + regime.discipline_regime.id + '">' + regime.discipline_regime.code + '</option>');
                    
                    //console.log(2328, regime.discipline_regime);
                });

                setDisabledStateRegimes(false);

            }
            else {
                $("#docenteRegime").text("A disciplina ("+nome_discipline+") selecionada não possui um regime, por favor selecione uma disciplina que tenha regime académico");                
                $("#disciplina_regime").modal('show');
            }

        }


        function buildStudentsTable() {
            var table = $('<table>', {style: 'width:100%'});

            var headerRow = $('<tr>').append(
                $('<th>', {width: '25px'}).text('#'),
                $('<th>', {width: '70px'}).text('Presente'),
                $('<th>').text('Aluno'),
                $('<th>', {width: '30%'}).text('Estado'),
            );
            table.append(headerRow);

            var count = 1;
            
            
            // Gera a lista de alunos
            $.each(eventData.students, function (k, v) {
                
                // Caso venha um estudante com problemas na matrícula
                if (v != null) {
                
                    var name = v.parameters[1].pivot.value ? v.parameters[1].pivot.value : v.name;
                    var displayName = name + ' ( #' + v.parameters[0].pivot.value + ' )';

                    var row = $('<tr>').append(
                        $('<td>').text(count++),
                        $('<td>').append(
                            $('<input>', {
                                type: 'checkbox',
                                name: 'attendance[]',
                                value: v.id,
                                onclick: 'handleCheckBoxOnStudent(this, ' + v.id + ')'
                            })
                        ),
                        $('<td>').text(displayName),
                        $('<td>').append(
                            $('<span>', {
                                class: 'badge badge-danger text-uppercase',
                                id: 'student-' + v.id + '-state'
                            }).text('falta')
                        )
                    )
                    table.append(row);
                }

            });

            studentTable.empty().append(table);
            studentContainer.attr('hidden', false);
        }


        function handleCheckBoxOnStudent(checkbox, studentId) {
            var isChecked = checkbox.checked;
            var stateSpan = $('#student-' + studentId + '-state');

            if (isChecked) {
                stateSpan.removeClass('badge-danger').addClass('badge-success').text('presente');
            } else {
                stateSpan.removeClass('badge-success').addClass('badge-danger').text('falta');
            }

        }


        @if($multiPermission) //
            if (!$.isEmptyObject(selectTeacher)) {
                selectedTeacher = selectTeacher[0] && selectTeacher[0].value ? selectTeacher[0].value : null;
                teacherAndDateCheck();
            }

            if (!$.isEmptyObject(occuredAtInput)) {
                inputedOccuredAt = occuredAtInput ? occuredAtInput.val() : null;
                teacherAndDateCheck();
            }
        @endif //


        if (!$.isEmptyObject(selectDiscipline)) {
            selectedDiscipline = selectDiscipline[0] && selectDiscipline[0].value ? selectDiscipline[0].value : null;
            mountSelectedDiscipline(selectedDiscipline)
        }

        $(function () {
            @if($multiPermission) //
                selectTeacher.change(function () {
                    selectedTeacher = this.value ? this.value : null;
                    resetDisciplines();
                    teacherAndDateCheck();
                    $("#docenteNome").text("Não existem disciplinas associadas ao docente ("+this.options[this.selectedIndex].text+")");
                });

                occuredAtInput.change(function () {
                    inputedOccuredAt = this.value ? this.value : null;
                    resetDisciplines();
                    teacherAndDateCheck();
                })

                loadDisciplinesBtn.click(function (event) {
                    event.preventDefault();
                    loadDisciplineData();  
                })                
            @else //
                resetDisciplines();
                loadDisciplineData();
            @endif //

            // AO SELECIONAR A DISCIPLINA
            selectDiscipline.change(function () {
                selectedDiscipline = this.value ? this.value : null;

                var split_discipline = this.options[this.selectedIndex].text.split('-');
                nome_discipline = split_discipline[1]
                mountSelectedDiscipline(selectedDiscipline);
            });

            selectLessonRegime.change(function () {
                var regime = this.value;

                if (regime) {
                    
                    var summary = $.grep(eventData.summaries, function (e) {
                        return parseInt(e.regime_id) === parseInt(regime);
                    });

                    summary = summary.length ? summary[0] : null;
                    
                    if (summary) {

                    summaryInput.val(summary.id);
                    summaryNameText.text(summary.current_translation.display_name);
                    summaryDescriptionText.text(summary.content);
                    buildStudentsTable();
                    summaryContainer.attr('hidden', false);
                    observationContainer.attr('hidden', false)
                    
                    }
                    else {
                        $("#docenteSummary").text("A disciplina ("+nome_discipline+") selecionada não possui um sumário, por favor selecione uma disciplina que tenha pelomenos um sumário criado.");                
                        $("#disciplina_summary").modal('show');
                    }

                } else {
                    summaryContainer.attr('hidden', true);
                    studentContainer.attr('hidden', true);
                    observationContainer.attr('hidden', true);
                }
            });

        });

    </script>
@endsection
