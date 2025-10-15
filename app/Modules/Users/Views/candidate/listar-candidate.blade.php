<title>Candidaturas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Listar candidatos a estudante')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Listar candidatos</li>
@endsection
@section('selects')
    <div class="mb-2">
        {{--   --}}
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
    <div class="">
        <label>Selecione a fase</label>
        <select name="fase" id="fase_candidate_select" class="selectpicker form-control form-control-sm"
            style="width: 100% !important;" disabled>
            <option selected value="">Seleciona a fase</option>
        </select>
    </div>
@endsection
@section('body')
    <div class="row">
        <form action="{{ route('show_student_excel') }}" method="POST" id="FormaExcel" target="_blank">
            @csrf
            @method('post')

            <input type="hidden" name="id_curso" value="" required="" id="id_excel_curso" />
            <input type="hidden" name="anoLectivo" value="" required="" id="id_excelanoLectivo" />
            <input type="hidden" name="Id_disciplina" value="" required="" id="id_exceldisciplina" />
        </form>

        <div class="col">
            {!! Form::open(['route' => ['grade_teacher.store']]) !!}
            @csrf
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>@lang('GA::courses.course')</label>
                            {{-- {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso']) }} --}}
                            <select name="course" id="course" class="selectpicker form-control" disabled>
                                <option selected value="">Seleciona o curso</option>
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->currentTranslation->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6" id="turma-container">
                        <div class="form-group">
                            <label>@lang('Turma')</label>
                            <select name="classe" id="classe" class="selectpicker form-control" disabled>
                                <option selected value="">Seleciona a turma</option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="fase" id="fase_valor" />
                <div class="w-100 position-relative">
                    <a href="#" type="button" id="btn-listar" class="btn btn-primary rounded float-right-abs"
                        target="_blank">
                        <i class="fas fa-file-pdf"></i>
                        Lista de candidatos
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div id="container-pdf" hidden>
        <div class="card">
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section('scripts-new')
    @parent
    <script>
        (() => {
            let lective_year = $('#lective_year').val();
            let selectCourse = $('#course');
            let selectClasse = $('#classe');
            let lectiveYearCourse = $("#lective_year");
            let containerPDF = $('#container-pdf');
            let getUrlCounter = 0;
            let btnListar = $("#btn-listar");
            let faseCandidateSelect = $("#fase_candidate_select");

            $("#btn-pdf").hide();

            if (lective_year != "") {
                if (selectCourse.attr('disabled')) {
                    selectCourse.removeAttr('disabled');
                    selectCourse.selectpicker('refresh');
                }
            }

            selectCourse.change(function() {
                switchDisciplines(this)
            });

            function switchDisciplines(element) {
                var courseId = element.value;
                let lective_year = $('#lective_year').val();
                if (courseId != "" && lective_year != "") {
                    $.ajax({
                        url: '/pt/grades/teacher_disciplines/' + courseId + '/' + lective_year
                    }).done(function(response) {
                        selectClasse.empty();
                        selectClasse.append('<option value="" selected>Seleciona a turma</option>');
                        if (response['turma'].length > 0) {
                            selectClasse.prop('disabled', true);
                            selectClasse.empty();
                            selectClasse.append('<option value="" selected>Seleciona a turma</option>');
                            response['turma'].forEach(function(turma) {
                                var turmaId = turma.id;
                                var turmaName = turma.display_name;
                                selectClasse.append('<option value="' + turmaId + '">' + turmaName +
                                    '</option>');
                            });
                            selectClasse.prop('disabled', false);
                            selectClasse.selectpicker('refresh');

                            let turmaGo = selectClasse.val();
                            getUrl();
                        }

                    });
                }
            }

            function getUrl() {
                var anoLectivo = $("#lective_year").val();
                var cursoId = selectCourse.val();
                var turma = selectClasse.val();
                if (anoLectivo != "" && cursoId != "" && turma != "") {
                    let route1 = "/pt/grades/show_student_list/" + cursoId + "/" + anoLectivo + "/" + turma + "?fase=" +
                        faseCandidateSelect.val();
                    document.getElementById("btn-listar").setAttribute('href', route1);
                } else {
                    document.getElementById("btn-listar").setAttribute('href', '#');
                }
            }

            function emptyAllSelector(disabled = false) {
                if (disabled) {
                    selectCourse.attr('disabled', true);
                    selectCourse.val("");
                    selectCourse.selectpicker('refresh');
                    selectClasse.attr('disabled', true);
                } else {
                    selectCourse.val("");
                    selectCourse.selectpicker('render');
                }
                selectClasse.empty();
                selectClasse.append('<option selected value="">Selecione a turma</option>')
                selectClasse.selectpicker('refresh');

                faseCandidateSelect.empty();
                faseCandidateSelect.attr('disabled', '');
                faseCandidateSelect.append('<option selected value="">Selecione a fase</option>')
                faseCandidateSelect.selectpicker('refresh');
            }

            lectiveYearCourse.change(function() {
                let lective_year = lectiveYearCourse.val();
                if (lective_year != "") {
                    lectiveFase(lective_year);
                    switchDisciplines(lectiveYearCourse);

                    if (selectCourse.attr('disabled')) {
                        selectCourse.removeAttr('disabled');
                        selectCourse.selectpicker('refresh');
                    } else {
                        emptyAllSelector();
                    }

                    if (getUrlCounter != 0) getUrl();
                } else {
                    if (!selectCourse.attr('disabled')) {
                        emptyAllSelector(true);
                    }
                }
            });

            $("#btn-listar").click(function() {
                getUrl();
            });

            selectClasse.click(function() {
                getUrl();
            });

            lectiveFase(lective_year);

            faseCandidateSelect.on('change', (e) => {
                $("#fase_valor").val(faseCandidateSelect.val());
            })

            function lectiveFase(lective_year) {
                if (lective_year != "") {
                    $.ajax({
                        url: "/users/candidatura_fases_ajax_lective/" + lective_year,
                        type: 'GET',
                        success: function(data) {
                            $("#fase_candidate_select").empty();
                            if (data.length) {
                                faseCandidateSelect.append(
                                    '<option selected="" value="">Selecione a fase</option>');
                                $.each(data, function(indexInArray, value) {
                                    faseCandidateSelect.append(
                                        `<option  value="${value.id}">${value . fase} ª fase</option>`
                                    );
                                });
                                //faseCandidateSelect.prop('disabled', false);
                                faseCandidateSelect.removeAttr('disabled');
                                faseCandidateSelect.selectpicker('refresh');
                            }
                        }
                    });
                }
            }

            btnListar.on('click', (e) => {
                e.preventDefault();
                if (lectiveYearCourse.val() == "") {
                    warning("Seleciona o ano lectivo")
                } else if (selectCourse.val() == "") {
                    warning("Seleciona o curso")
                } else if (selectClasse.val() == "") {
                    warning("Seleciona a turma")
                } else if (faseCandidateSelect.val() == "") {
                    warning("Seleciona a fase")
                } else {
                    window.open(btnListar.attr('href'), "_blank");
                }
            })

        })();
    </script>
@endsection
