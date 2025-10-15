<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Exibir exame especial')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Exibir exame especial</li>
@endsection
@section('form-start')
{!! Form::open(['route' => ['generate_estatistic_candidato']]) !!}
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @csrf
    <div class="row">
        <div class="col-6 p-2">
            <label>@lang('GA::courses.course')</label>
            {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required']) }}
        </div>
        <div class="col-6 p-2" id="fase">
            <label>@lang('Fase')</label>
            {{ Form::bsLiveSelectEmpty('fase', [], null, ['disabled', 'required', 'placeholder' => 'Selecione a fase', 'id' => 'fase_candidate_select']) }}
        </div>
        <div class="col-6 p-2" id="disciplines-container" hidden>
            <label>@lang('GA::disciplines.discipline')</label>
            {{-- {{ Form::bsLiveSelectEmpty('discipline', [], null, ['disabled','required','placeholder' => 'Selecione a disciplina']) }} --}}
            <select class="form-control-sm" name="discipline[]" id="discipline" disabled required multiple></select>
        </div>
        <div class="col-6" id="turma-container" hidden>
            <label>@lang('Turma')</label>
            <select class="form-control-sm" name="classe[]" id="classe" disabled required multiple></select>
        </div>
    </div>

    <div id="grade-container">
        <div class="card-footer BotoesGrupo">
            <button type="submit" target="blank" class="btn btn-sm btn-success pt-2 pb-2 pl-5 pr-5 border border-success rounded-0 float-right" id="btn_lancar_nota">
                <i class="fas fa-file-pdf"></i>
                Imprimir
            </button>
        </div>
    </div>
@endsection
@section('form-end')
{!! Form::close() !!}
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts')
    @parent
    <script>
        let lective_year = $('#lective_year').val();
        let selectCourse = $('#course');
        let selectClasse = $('#classe');
        let containerDisciplines = $('#disciplines-container');
        let selectDiscipline = $('#discipline');
        let containerGrade = $('#grade-container');
        let containerPDF = $('#container-pdf');
        let id_fase = "";
        $("#btn-listar").hide();
        $("#btn-pdf,#grade-container").hide();

        function switchDisciplines(element) {
            //resetCourse();
            var courseId = element.value;
            if (courseId) {
                let lective_year = $('#lective_year').val();

                $.ajax({
                    url: '/pt/grades/teacher_disciplines/' + courseId + '/' + lective_year
                }).done(function(response) {
                    selectClasse.empty();
                    //Começa o if principal das turmas 
                    //Ou seja se não tiver turma não mosta
                    selectClasse.append('<option value=""></option>');
                    if (response['turma'].length > 0) {
                        selectClasse.prop('disabled', true);
                        selectClasse.empty();

                        response['turma'].forEach(function(turma) {
                            var turmaId = turma.id;
                            var turmaName = turma.display_name;
                            selectClasse.append('<option value="' + turmaId + '" selected>' + turmaName +
                                '</option>');
                        });

                        selectClasse.prop('disabled', false);
                        selectClasse.selectpicker('refresh');

                        selectDiscipline.append('<option value=""></option>');
                        if (response['disciplines'].length) {
                            selectDiscipline.prop('disabled', true);
                            selectDiscipline.empty();
                            response['disciplines'].forEach(function(discipline) {
                                var discId = discipline.id;
                                var discName = '#' + discipline.code + ' - ' + discipline.display_name;
                                selectDiscipline.append('<option value="' + discId + '" selected>' +
                                    discName + '</option>');
                            });

                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                            let turmaGo = selectClasse.val();

                            lectiveFase(lective_year);
                        } else {
                            resetCourse();
                        }

                    } else {
                        alert("Nenhuma  turma foi encontrada para este curso");
                    }
                    containerGrade.hide();
                });
            }
        }
        // PegaR AS URL PARA PREENCHER OS ELEMENTOS
        function getUrl(value, cursoId, anoLectivo, turma) {
            id_fase = faseCandidateSelect.val();
            let route = "/pt/grades/show_student_grades/" + value + "/" + anoLectivo + "/" + cursoId + "/" + turma +
                "?id_fase=" + id_fase;
            document.getElementById("btn-pdf").setAttribute('href', route);

            let route1 = "/pt/grades/show_student_list/" + cursoId + "/" + anoLectivo + "/" + turma + "?id_fase=" + id_fase;
            document.getElementById("btn-listar").setAttribute('href', route1);

            $('#id_excel_curso').val(cursoId);
            $('#id_excelanoLectivo').val(anoLectivo);
            $('#id_exceldisciplina').val(value);
        }

        //submeter o formulario para gerar pdf
        $('#btn-excel').click(function() {
            var id = $('#id_excel_curso').val();
            if (id != "") {
                $("#FormaExcel").submit();
            }
        });

        selectDiscipline.change(function() {
            var id = selectDiscipline.val();
            var Cursoid = selectCourse.val();
            var turma = selectClasse.val();
        });
        // Quando trocamos o ano 
        $("#lective_year").change(function() {
            let lective_year = $('#lective_year').val();
            //switchStudents(selectDiscipline[0]);
            if (selectCourse.val() != "") {
                selectClasse.empty();
                //ocultar o selector de disciplina quando for ano passado
                if (lective_year <= 6) {
                    selectClasse.empty();
                    $("#disciplines-container").hide();
                } else {
                    $("#disciplines-container").show();
                }
            } else {
                selectClasse.empty();
            }
            //sedrac
            lectiveFase(lective_year);
        });

        selectClasse.change(function() {
            var turma = selectClasse.val();
        });

        if (!$.isEmptyObject(selectCourse)) {
            switchDisciplines(selectCourse[0]);
            selectCourse.change(function() {
                switchDisciplines(this);
                if (this.value == "") {
                    $("#student-table").empty();
                    //    containerDisciplines.prop('hidden', true);
                    containerPDF.prop('hidden', true);
                    containerGrade.prop('hidden', true);
                }
            });
        }
        // novo codigo sedrac
        let faseCandidateSelect = $("#fase_candidate_select");
        // Método para validar os campos de ano lectivo
        faseCandidateSelect.change(
            function() {
                if ($(this).val() == "") {
                    containerGrade.hide();
                } else {
                    containerGrade.show();
                }
            }
        );

        function lectiveFase(lective_year) {
            if (lectiveFase != null && lective_year != "") {
                $.ajax({
                    url: "/users/candidatura_fases_ajax_lective/" + lective_year,
                    type: 'GET',
                    success: function(data) {
                        $("#fase_candidate_select").empty();
                        if (data.length) {
                            faseCandidateSelect.append(
                                '<option selected="" value="">Selecione a fase</option>');
                            $.each(data, function(indexInArray, value) {
                                faseCandidateSelect.append('<option  value="' + value.id + ',' + value
                                    .fase + '"> ' + value.fase + 'ª fase</option>');
                            });
                            faseCandidateSelect.prop('disabled', false);
                            faseCandidateSelect.selectpicker('refresh');
                        }
                    }
                });
            }
        }
        faseCandidateSelect.change(function(e) {
            verificar = true;
            id_fase = faseCandidateSelect.val();
            let lective_year = $('#lective_year').val();
        })
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
