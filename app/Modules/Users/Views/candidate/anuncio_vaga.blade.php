<title>Candidaturas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Anúncio de vagas')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Anúncio de Vagas</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected>Nenhum registro</option>
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
            style="width: 100% !important;">
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
            {!! Form::open(['route' => ['anuncio-vagas.store']]) !!}
            @csrf
            <input type="hidden" name="year" id="year" value="{{ $lectiveYearSelected }}" />
            <input type="hidden" name="fase" id="fase_change" value="" />
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>@lang('GA::courses.course')</label>
                            {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso']) }}
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mt-3">
                            <a href="" type="button" id="btn-pdf" class="btn btn-primary p-1 text-light mr-1"
                                target="_blank">
                                <i class="fas fa-file-pdf"></i>
                                Anúncio de vagas
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6"></div>
                    <div class="col-6" hidden>
                        <div class="form-group mt-3">
                            <a href="" type="button" id="btn-pdf-estatistica"
                                class="btn btn-primary p-1 mr-2 text-light" target="_blank">
                                <i class="fas fa-chart-bar"></i>
                                Estatística
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="container-pdf" hidden>
                <div class="card"></div>
            </div>
            <div id="grade-container" hidden>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <table id="grades-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fase</th>
                                    <th>Curso</th>
                                    <th>Manhã</th>
                                    <th>Tarde</th>
                                    <th>Noite</th>
                                </tr>
                            </thead>
                            <tbody id="student-table"></tbody>
                        </table>
                    </div>
                    <div class="BotoesGrupo">
                        <div class="w-100 position-relative">
                            <button type="submit" class="btn btn-primary float-right-abs" id="btn_lancar_nota">
                                <i class="fas fa-plus-circle"></i>
                                <span>Gravar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
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

        getUrl(lective_year);
        lectiveFase(lective_year);

        function switchDisciplines(element, ...args) {
            var courseId = element.value;
            let fasePesquisa = args.length > 0 ? '?fase=' + faseCandidateSelect.val() : '';

            if (fasePesquisa == '' || fasePesquisa == '?fase=') {
                $(".grades").val("");
                return false;
            }
            if (courseId) {
                let lective_year = $('#lective_year').val();
                let bodyData = '',
                    i = 1;
                $.ajax({
                    url: '/pt/users/vagas/' + courseId + '/' + lective_year + fasePesquisa
                }).done(function(vaga) {
                    selectClasse.empty();
                    selectClasse.append('<option value=""></option>');
                    if (vaga['course_vagas'].length > 0) {
                        $(".BotoesGrupo").show();
                        $(".th_nota").show();
                        $("#btn_lancar_nota").show();


                        vaga['course_vagas'].forEach(function(vaga) {
                            let fase = vaga.fase_num != null ? vaga.fase_num : "-";
                            bodyData += '<tr>'
                            bodyData += '<td width="100">' + (i++) + '</td><td>' + fase + '</td><td>' + vaga
                                .display_name +
                                '</td><td><input type="number" required name="manha[]" min="0.00"  class="form-control grades" value=' +
                                vaga.manha +
                                ' step="any">   <input type="hidden" name="id_vaga[]" class="form-control" value=' +
                                vaga.id_vaga +
                                '></td><td> <input type="number" required name="tarde[]" min="0.00"  class="form-control grades" value=' +
                                vaga.tarde +
                                ' step="any"></td> <td><input type="number" required name="noite[]" min="0.00"  class="form-control grades" value=' +
                                vaga.noite + ' step="any"></td>';
                            bodyData += '</tr>'
                        });

                        $("#student-table").empty();
                        containerPDF.prop('hidden', false);
                        containerGrade.prop('hidden', false);
                        getUrl(lective_year);
                        $('#student-table').append(bodyData);
                        selectDiscipline.append('<option value=""></option>');
                    }
                });
            }
        }

        function getUrl(anoLectivo) {
            let fase = $("#fase_candidate_select").val();
            let route = "/pt/users/anuncio-vagas/pdf/" + anoLectivo + "?fase=" + fase;
            document.getElementById("btn-pdf").setAttribute('href', route);

            let route1 = "/pt/users/anuncio-vagas/estatistica/" + anoLectivo + "?fase=" + fase;
            document.getElementById("btn-pdf-estatistica").setAttribute('href', route1);
        }

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
            switchDataOnDataTable(this, Cursoid, lective_year, turma);
        });

        function switchDataOnDataTable(element, courseId, lective_yearS, turma) {
            let disciplineId = element.value;
            $.ajax({
                url: "/pt/grades/grades_candidate/getStudentsBy/" + lective_yearS + "/" + courseId + "/" +
                    disciplineId + "/" + turma,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                success: function(dataResult) {
                    let bodyData = '',
                        i = 1;
                    var dados = dataResult;
                    if (dataResult['studant'].length > 0) {
                        for (let a = 0; a < dataResult['studant'].length; a++) {
                            let cand_number = dataResult['studant'][a].cand_number == null;
                            if (dataResult['studant'][a].cand_number == null) {
                                cand_number = "N/A";
                            }

                            if (lective_yearS > 6 && dataResult['studant'][a].state != "pending") {

                                $(".BotoesGrupo").show();
                                $(".th_nota").show();
                                $("#btn_lancar_nota").show();

                                bodyData += '<tr>'
                                bodyData += '<td width="100">' + i++ + '</td><td>' + dataResult['studant'][a]
                                    .name_completo + '</td><td>' + dataResult['studant'][a].email +
                                    '</td><td>' + dataResult['studant'][a].cand_number +
                                    '</td><td width="100"><input type="number" required name="grades[]" min="0.00" max="20.00" class="form-control grades" value=' +
                                    dataResult['studant'][a].value +
                                    ' step="any"><input type="hidden" name="students[]" class="form-control" value=' +
                                    dataResult['studant'][a].id + '></td>';
                                bodyData += '</tr>'

                            } else if (lective_yearS == 6) {
                                $(".BotoesGrupo").show();
                                $("#btn_lancar_nota").hide();
                                $(".th_nota").hide();
                                bodyData += '<tr>'
                                bodyData += '<td width="100">' + i++ + '</td><td>' + dataResult['studant'][a]
                                    .name_completo + '</td><td>' + dataResult['studant'][a].email +
                                    '</td><td>' + cand_number + '</td>';
                                bodyData += '</tr>'
                            }
                        }

                    } else {
                        $(".BotoesGrupo").hide();
                        bodyData += '<tr >'
                        bodyData +=
                            '<td colspan="5" style="text-align:center;"> Nenhum registro encontrado na busca</td>';
                        bodyData += '</tr>'
                    }

                    $("#student-table").empty();
                    containerPDF.prop('hidden', false);
                    containerGrade.prop('hidden', false);
                    $('#student-table').append(bodyData);

                },
                error: function(dataResult) {
                    alert('error: ', dataResult);
                }
            });

        }

        $("#lective_year").change(function() {
            let lective_year = $('#lective_year').val();
            getUrl(lective_year);
            if (selectCourse.val() != "") {
                selectClasse.empty();
                if (lective_year <= 6) {
                    selectClasse.empty();
                    $("#disciplines-container").hide();

                } else {
                    $("#disciplines-container").show();
                }
                if (!$.isEmptyObject(selectCourse)) {
                    switchDisciplines(selectCourse);
                }
            } else {
                selectClasse.empty();
            }

            lectiveFase(lective_year);
            $('#year').val(lective_year);
        });

        selectClasse.change(function() {
            let turma = selectClasse.val();
            let lective_year = $('#lective_year').val();
            switchDataOnDataTable(selectDiscipline[0], selectCourse.val(), lective_year, turma);
        });


        if (!$.isEmptyObject(selectCourse)) {
            switchDisciplines(selectCourse[0]);
        }

        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        let faseCandidateSelect = $("#fase_candidate_select").first();

        function lectiveFase(lective_year) {
            if (lectiveFase != null && lective_year != "") {
                $.ajax({
                    url: "/users/candidatura_fases_ajax_lective/" + lective_year,
                    type: 'GET',
                    success: function(data) {
                        faseCandidateSelect.empty();
                        faseCandidateSelect.append('<option selected="" value="">Selecione a fase</option>');
                        if (data.length > 0) {
                            $.each(data, function(indexInArray, value) {
                                faseCandidateSelect.append('<option  value="' + value.id + '"> ' + value
                                    .fase + 'ª fase</option>');
                            });
                            faseCandidateSelect.prop('disabled', false);
                            faseCandidateSelect.selectpicker('refresh');
                        }else{
                            faseCandidateSelect.selectpicker('refresh');
                        }
                    }
                });
            }
        }

        faseCandidateSelect.change(function(e) {
            id_fase = faseCandidateSelect.val();
            let lective_year = $('#lective_year').val();
            if (!$.isEmptyObject(selectCourse)) {
                switchDisciplines(selectCourse[0], true);
                $('#fase_change').val(faseCandidateSelect.val())
                selectCourse.change(function() {
                    switchDisciplines(this, true);
                    if (this.value == "") {
                        $("#student-table").empty();
                        containerPDF.prop('hidden', true);
                        containerGrade.prop('hidden', true);
                    }
                });
            }
        })
    </script>

@endsection
