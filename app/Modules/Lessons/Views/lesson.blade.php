@switch($action)
    @case('show') @section('title',__('Lessons::lessons.lesson')) @break
@case('edit') @section('title',__('Lessons::lessons.edit_lesson')) @break
@endswitch

@extends('layouts.backoffice')

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
                                @case('show') @lang('Lessons::lessons.lesson') @break
                                @case('edit') @lang('Lessons::lessons.edit_lesson') @break
                            @endswitch
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        @switch($action)
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
                            @case('edit')
                            <button type="submit" class="btn btn-sm btn-success mb-3">
                                <i class="fas fa-save"></i>
                                @lang('common.save')
                            </button>
                            @break
                            @case('show')                            
                            @if (auth()->user()->hasAnyPermission(['editar_aulas']))
                                <a href="{{ route('lessons.edit', $lesson->id) }}"
                                class="btn btn-sm btn-warning mb-3">
                                    <i class="fas fa-edit"></i>
                                    @lang('common.edit')
                                </a>                            
                            @endif
                            @if (auth()->user()->hasAnyPermission(['imprimir_aulas']))
                                <a href=" {{ route('lessons.pdf', $lesson->id)}}" class="btn btn-sm btn-info mb-3">
                                    <i class="fas fa-pdf"></i>
                                    Gerar PDF
                                </a>
                            @endif
                            @break
                        @endswitch

                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.teachers')</label>
                                        {{ $lesson->teacher->name }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.date')</label>
                                        {{ $lesson->occured_at }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="card">
                            <div class="row" id="disciplines-container">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.discipline_class')</label>
                                        {{ $lesson->discipline->currentTranslation->display_name . ' - ' . $lesson->class->display_name }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Lessons::lessons.regime')</label>
                                        {{ $lesson->regime->currentTranslation->display_name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="summary-container">
                            <hr>
                            <div class="row">
                                
                                <input id="summary" name="summary" hidden aria-hidden="true" value="{{$lesson->summary->id}}">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <label>Sumário</label>
                                        <div
                                            id="summary-name"> {{ $lesson->summary->currentTranslation->display_name }} </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control" id="summary-ckeditor" name="text" disabled>
                                                {{ $lesson->summary->content }}
                                        </textarea>
                                    <!--<div class="form-group col">-->
                                    <!--    <div id="summary-description"> {{ $lesson->summary->content }} </div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>


                        <div class="card" id="observation-container">
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group col">
                                        <label for="observation-description">Observação</label>
                                        <div id="observation-description"> {{ $lesson->observations }} </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="student-container">
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('summary-ckeditor');
        var studentContainer = $('#student-container');
        var studentTable = $('#student-table');
        var presentStudents = @json($attendance)

        function mountSelectedDiscipline() {
            var disciplineId = parseInt({{ $lesson->discipline_id }});
            var classId = parseInt({{ $lesson->class_id }});

            if (disciplineId && classId) {
                let route = ("{{ route('lessons.discipline-class') }}") + '?discipline=' + disciplineId + '&class=' + classId;
                $.get(route, function (data) {
                    eventData = data;
                    buildStudentsTable();
                });
            }
        }

        function buildStudentsTable() {
            var table = $('<table>', {style: 'width:100%'});

            var headerRow = $('<tr>').append(
                @if($action === 'edit')
                $('<th>', {width: '25px'}).text('#'),
                $('<th>', {width: '70px'}).text('Presente'),
                @endif
                $('<th>').text('Aluno'),
                $('<th>', {width: '30%'}).text('Estado'),
            );
            table.append(headerRow);

            var count = 1;
            $.each(eventData.students, function (k, v) {
                if (v != null) {
                    var name = v.parameters[1].pivot.value ? v.parameters[1].pivot.value : v.name;
                    var displayName = name + ' ( #' + v.parameters[0].pivot.value + ' )';

                    var wasStudentPresent = $.inArray(v.id, presentStudents) !== -1;
                    var stateBadge = wasStudentPresent ? 'badge-success' : 'badge-danger';
                    var stateText = wasStudentPresent ? 'presente' : 'falta';

                    var row = $('<tr>').append(
                        @if($action === 'edit')
                        $('<td>').text(count++),
                        $('<td>').append(
                            $('<input>', {
                                type: 'checkbox',
                                name: 'attendance[]',
                                value: v.id,
                                onclick: 'handleCheckBoxOnStudent(this, ' + v.id + ')',
                                checked: wasStudentPresent
                            })
                        ),
                        @endif
                        $('<td>').text(displayName),
                        $('<td>').append(
                            $('<span>', {
                                class: 'badge text-uppercase ' + stateBadge,
                                id: 'student-' + v.id + '-state'
                            }).text(stateText)
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

        $(function () {
            mountSelectedDiscipline();
        });
    </script>
@endsection
