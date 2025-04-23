<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LANÇAR NOTAS DE EXAME DE ACESSO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lançar notas de exame de acesso</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
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
            <input type="hidden" name="lective_year" id="lective_year_h" />
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group col">
                            <label>@lang('GA::courses.course')</label>
                            {{ Form::bsLiveSelect('course', $courses, null, ['required', 'placeholder' => 'Selecione o curso']) }}
                        </div>
                    </div>
                    <div class="col-6" id="disciplines-container">
                        <div class="form-group col">
                            <label>@lang('GA::disciplines.discipline')</label>
                            {{ Form::bsLiveSelectEmpty('discipline', [], null, ['required', 'disabled', 'placeholder' => 'Selecione a disciplina']) }}
                        </div>
                    </div>
                    <div class="col-6" id="turma-container">
                        <div class="form-group col">
                            <label>@lang('Turma')</label>
                            {{ Form::bsLiveSelectEmpty('classe', [], null, ['required', 'disabled', 'placeholder' => 'Selecione a turma']) }}
                        </div>
                    </div>
                    <div class="col-6" id="fase">
                        <div class="form-group col">
                            <label>@lang('Fase')</label>
                            {{ Form::bsLiveSelectEmpty('fase', [], null, ['required', 'disabled', 'placeholder' => 'Selecione a fase', 'id' => 'fase_candidate_select']) }}
                        </div>
                    </div>
                    <div class="col-6" id="btn_dados-container">
                        <div class="form-group col BotoesGrupo">
                            <label for="" style="color: #fff; ">estatística</label>
                            <div class="btn-group" role="group" aria-label="Exemplo básico">
                                <a href="#" type="button" id="btn-listar" class="btn btn-primary p-1 text-light mr-1"
                                    target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                    Lista de candidatos
                                </a>
                                <a href="#" type="button" id="btn-pdf" class="btn btn-primary p-1 mr-2 text-light"
                                    target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                    Pauta de exame
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="container-pdf" hidden>
                <div class="card"> </div>
            </div>

            <div id="grade-container" hidden>
                <hr>
                <div class="card">
                    <div class="card-body">
                        <table id="grades-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="100">#</th>
                                    <th>Faltou</th>
                                    <th>Nome Completo</th>
                                    <th>e-mail</th>
                                    <th>Nº candidato</th>
                                    <th class="th_nota">Nota</th>
                                </tr>
                            </thead>
                            <tbody id="student-table">

                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer BotoesGrupo">
                        <button type="submit"
                            class="btn btn-sm btn-success pt-2 pb-2 pl-5 pr-5 border border-success rounded-0 float-right"
                            id="btn_lancar_nota">
                            <i class="fas fa-plus-circle"></i>
                            Lançar notas
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        let lective_year = $('#lective_year').val();
        let selectCourse = $('#course');
        let selectClasse = $('#classe');
        let containerDisciplines = $('#disciplines-container');
        let selectDiscipline = $('#discipline');
        let containerGrade = $('#grade-container');
        let containerPDF = $('#container-pdf');
        let fase = "";

        $("#btn-listar").hide();
        $("#btn-pdf").hide();
        $('#lective_year_h').val(lective_year);

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
                            selectClasse.append('<option value="' + turmaId + '">' + turmaName +
                                '</option>');
                        });

                        selectClasse.prop('disabled', false);
                        selectClasse.selectpicker('refresh');

                        selectDiscipline.append('<option value=""></option>');
                        if (response['disciplines'].length) {
                            selectDiscipline.prop('disabled', true);
                            selectDiscipline.empty();

                            //console.log(response)
                            response['disciplines'].forEach(function(discipline) {
                                var discId = discipline.id;
                                var discName = '#' + discipline.code + ' - ' + discipline.display_name;
                                selectDiscipline.append('<option value="' + discId + '">' + discName +
                                    '</option>');
                            });

                            selectDiscipline.prop('disabled', false);
                            selectDiscipline.selectpicker('refresh');
                            // containerDisciplines.prop('hidden', true);
                            //switchStudents(selectDiscipline[0]);
                            let turmaGo = selectClasse.val();
                            switchDataOnDataTable(selectDiscipline[0], courseId, lective_year, turmaGo);
                            //getPostData();




                            lectiveFase(lective_year);
                        } else {
                            resetCourse();
                        }

                    } else {
                        alert("Nenhuma  turma foi encontrada para este curso");
                    }


                });
            }
        }

        // PegaR AS URL PARA PREENCHER OS ELEMENTOS
        //___________________________________________________________________________________
        function getUrl(value, cursoId, anoLectivo, turma) {
            fase = faseCandidateSelect.val();
            let route = "/pt/grades/show_student_grades/" + value + "/" + anoLectivo + "/" + cursoId + "/" + turma +
                "?fase=" + fase;
            document.getElementById("btn-pdf").setAttribute('href', route);

            let route1 = "/pt/grades/show_student_list/" + cursoId + "/" + anoLectivo + "/" + turma + "?fase=" + fase;
            document.getElementById("btn-listar").setAttribute('href', route1);

            // let route2 = "/pt/grades/show_student_estatistica/"+cursoId+"/"+anoLectivo+"/"+turma;
            // document.getElementById("btn-estatística").setAttribute('href', route2);

            // let route3 = "/pt/grades/show_student_excel/"+cursoId+"/"+anoLectivo;
            // document.getElementById("btn-excel").setAttribute('href', route3);
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

        //-----


        //SelectDisciplina

        selectDiscipline.change(function() {

            var id = selectDiscipline.val();
            var Cursoid = selectCourse.val();
            var turma = selectClasse.val();
            switchDataOnDataTable(this, Cursoid, lective_year, turma);
        });
        //
        function switchDataOnDataTable(element, courseId, lective_yearS, turma)

        {
            //console.log("turma entrou é"+turma)
            let disciplineId = element.value;
            let fase = faseCandidateSelect.val();
            ajaxLoad(disciplineId, courseId, lective_yearS, turma);

        }

        //novo codigo sedrac
        function ajaxLoad(disciplineId, courseId, lective_yearS, turma) {
            fase = faseCandidateSelect.val();
            if (fase != null && fase != "")
                $.ajax({
                    url: "/pt/grades/grades_candidate/getStudentsBy/" + lective_yearS + "/" + courseId + "/" +
                        disciplineId + "/" + turma + "?fase=" + (fase != null ? fase : ""),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        let bodyData = '',
                            i = 1;
                        // let resultStudent = dataResult.student;
                        var dados = dataResult;

                        //console.log(dataResult)

                        if (dataResult['studant'].length > 0) {
                            for (let a = 0; a < dataResult['studant'].length; a++) {
                                let cand_number = dataResult['studant'][a].cand_number == null;
                                if (dataResult['studant'][a].cand_number == null) {
                                    cand_number = "N/A";
                                }
                                //console.log(dataResult['studant'][a].value)
                                if (lective_yearS > 6 && dataResult['studant'][a].state != "pending") {

                                    $(".BotoesGrupo").show();
                                    $(".th_nota").show();
                                    $("#btn_lancar_nota").show();
                                    $("#btn-listar").show();
                                    $("#btn-pdf").show();

                                    check_id = `inp_check_presenca_${a}`;
                                    param =
                                        `name="users_falta[]" value="${dataResult['studant'][a].user_id}" id="${check_id}" class="check_inp_grade"`;
                                    isNotaLancada = dataResult['studant'][a].value && dataResult['studant'][a]
                                        .value > -1 && dataResult['studant'][a].value < 21;
                                    checkPresenca =
                                        `<td><input type="checkbox" ${isNotaLancada ? 'disabled' : param }></td>`;

                                    bodyData += `<tr id="line_${check_id}">`
                                    //bodyData += '<td width="100">'+ (i++) +'</td><td>'+ dataResult['studant'][a].name_completo+ '</td><td>'+ dataResult['studant'][a].email +'</td><td>'+dataResult['studant'][a].cand_number+'</td><td width="100"><input type="number" required name="grades[]" min="0.00" max="20.00" class="form-control grades" value='+dataResult['studant'][a].value+' step="any"><input type="hidden" name="students[]" class="form-control" value='+dataResult['studant'][a].id+'> <input type="hidden" name="users[]" class="form-control" value='+dataResult['studant'][a].user_id+'></td>';

                                    bodyData += `
                                <td width="100">${i++}</td>
                                ${checkPresenca}
                                <td>${dataResult['studant'][a].name_completo}</td>
                                <td>${dataResult['studant'][a].email}</td>
                                <td>${dataResult['studant'][a].cand_number}</td>
                                <td width="100">
                                    <input type="hidden" required name="students[]" class="form-control" value="${dataResult['studant'][a].id}">
                                    <input type="number" required name="grades[]" min="0.00" max="20.00" class="form-control grades ${!isNotaLancada ? "nao_tem_nota" : ""}" value="${dataResult['studant'][a].value}" step="any" link="${check_id}">
                                    <input type="hidden" name="users[]" class="form-control" value="${dataResult['studant'][a].user_id}">
                                </td>                                
                            `;

                                    bodyData += '</tr>'

                                } else if (lective_yearS == 6) {
                                    $(".BotoesGrupo").show();
                                    $("#btn_lancar_nota").hide();
                                    $(".th_nota").hide();
                                    bodyData += '<tr>'
                                    bodyData += '<td width="100">' + (i++) + '</td><td>' + dataResult['studant'][a]
                                        .name_completo + '</td><td>' + dataResult['studant'][a].email +
                                        '</td><td>' + cand_number + '</td>';
                                    bodyData += '</tr>'
                                } else {}

                            }
                        } else {
                            $(".BotoesGrupo").hide();
                            $("#btn-listar").hide();
                            $("#btn-pdf").hide();
                            bodyData += '<tr >'
                            bodyData +=
                                '<td colspan="5" style="text-align:center;"> Nenhum registro encontrado na busca</td>';
                            bodyData += '</tr>'
                        }

                        $("#student-table").empty();
                        containerPDF.prop('hidden', false);
                        containerGrade.prop('hidden', false);
                        getUrl(disciplineId, courseId, lective_yearS, turma);
                        $('#student-table').append(bodyData);

                        eventoGradesSemNotas();

                    },
                    error: function(dataResult) {
                        alert('error: ', dataResult);
                    }
                });
        }

        function eventoGradesSemNotas() {
            let inputsCheckSemNota = document.querySelectorAll('.check_inp_grade');
            inputsCheckSemNota.forEach(item => {
                item.addEventListener('click', (e) => {
                    let line = document.querySelector("#line_" + item.id);
                    let users = line.querySelector('[name="users[]"]');
                    let grade = line.querySelector('[name="grades[]"]');
                    let studant = line.querySelector('[name="students[]"]');
                    if (item.hasAttribute('checked')) {
                        item.removeAttribute('checked')
                        if (grade.hasAttribute('disabled')) grade.removeAttribute('disabled');
                        if (users.hasAttribute('disabled')) users.removeAttribute('disabled');
                        if (studant.hasAttribute('disabled')) studant.removeAttribute('disabled');
                    } else {
                        item.setAttribute('checked', true)
                        if (!grade.hasAttribute('disabled')) grade.setAttribute('disabled', true);
                        if (!users.hasAttribute('disabled')) users.setAttribute('disabled', true);
                        if (!studant.hasAttribute('disabled')) studant.setAttribute('disabled', true);
                    }
                })
            })
        }
        // Quando trocamos o ano 
        $("#lective_year").change(function() {
            let lective_year = $('#lective_year').val();
            $('#lective_year_h').val(lective_year);
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
                switchDataOnDataTable(selectDiscipline[0], selectCourse.val(), lective_year, selectClasse[0]);
            } else {
                selectClasse.empty();
            }
            //sedrac
            lectiveFase(lective_year);
        });

        selectClasse.change(function() {
            var turma = selectClasse.val();
            switchDataOnDataTable(selectDiscipline[0], selectCourse.val(), lective_year, turma);
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
                                faseCandidateSelect.append('<option  value="' + value.id + '"> ' + value
                                    .fase + 'ª fase</option>');
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
            fase = faseCandidateSelect.val();
            let discipline = selectDiscipline.val();
            let classe = selectClasse.val();
            let course = selectCourse.val();
            let lective_year = $('#lective_year').val();
            ajaxLoad(discipline, course, lective_year, classe);
        })



        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
