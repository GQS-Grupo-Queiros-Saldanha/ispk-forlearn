@section('title', __('Faltas'))

@extends('layouts.backoffice')

@section('content')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

    <div class="content-panel" style="padding: 0px;">
        @include('Lessons::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                {{-- <li class="breadcrumb-item"><a href="{{ route('lessons.index') }}">Aulas</a></li> --}}
                                <li class="breadcrumb-item active" aria-current="page">Faltas</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            Faltas
                        </h1>
                    </div>
                    <div class="col-sm-6"  style="padding-right: 30px">
                        <div class="float-right div-anolectivo"  style="width: 45%; !important">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
                                @foreach ($lectiveYears as $lectiveYear)
                                    @if ($lectiveYearSelected == $lectiveYear->id)
                                        <option value="{{ $lectiveYear->id }}" selected>
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @else
                                        <option value="{{ $lectiveYear->id }}">
                                            {{ $lectiveYear->currentTranslation->display_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

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
                                    <div class="form-group col">
                                        <label for="course">Curso: </label>
                                        {{ Form::bsLiveSelectEmpty('courses', [], ['class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group col">

                                        <label for="discipline">Disciplina: </label>
                                        {{ Form::bsLiveSelectEmpty('disciplines', [], null, ['id' => 'disciplines', 'class' => 'form-control', 'disabled']) }}

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label for="students">Estudante: </label>
                                        {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control', 'disabled']) }}

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <th>Disciplina</th>
                                            <th width="200" class='text-center'>Nº máximo de faltas</th>
                                            <th class='text-center'>Aulas</th>
                                            <th class='text-center'>Presenças</th>
                                            <th class='text-center'>Faltas</th>
                                        </thead>

                                        <tbody id="presence">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <br>

            </div>
        </div>


        <div class="modal fade bd-example-modal-lg" id="lista_estudantes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-light">
                        <h5 class="modal-title" id="exampleModalLabel">ALERTA | Gestão de faltas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="center">
                            <h4>
                                <p class="text-danger" style="font-weight:bold; !important" id="estudantes"> 
                                    {{-- Não existem disciplinas associadas ao docente ({{auth()->user()->name}}) --}}
                                <br>
                                </p>
                            </h4>
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

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            lective_year = $("#lective_year").val();

            $("#lective_year").change(function() {                
                lective_year = $("#lective_year").val();   ;
            });

            function loadCourses() {
                $.ajax({
                    url: "/courses_ajax"
                }).done(function(response) {
                    if (response.length) {
                        $("#courses").append('<option selected="" value=""></option>');
                        response.forEach(function(course) {
                            $("#courses").append('<option value="' + course.id + '">' + course
                                .current_translation.display_name + '</option>');
                            $("#courses").selectpicker('refresh');
                        })
                    }
                })
            }
            $('#courses').change(function() {
                course_id = $("#courses").children("option:selected").val();
                if (course_id == "") {
                    disabledFields();
                } else {
                    $("#disciplines").empty();
                    $("#students").empty();
                    //$("#presence").empty();
                    getDisciplines(course_id);
                }
            })

            $('#disciplines').change(function() {
                let discipline_id = $(this).children("option:selected").val();
                if (discipline_id == "") {
                    $("#students").prop('disabled', true);
                    $("#students").selectpicker('refresh');
                    $("#students").empty();
                    $("#presence").empty();
                    alert("Lista de estudantes vazia.");
                } else {
                    $("#students").empty();
                    $("#presence").empty();
                    getStudents(discipline_id);
                }
            })

            $("#students").change(function() {
                let student_id = $(this).children("option:selected").val();
                let discipline_id = $("#disciplines").children("option:selected").val();
                if (student_id = "") {
                    $("#presence").empty();
                } else {
                    $("#presence").empty();
                    getAttedance($(this).children("option:selected").val(), discipline_id);
                }
            })

            function getDisciplines(course_id) {
                $.ajax({
                    url: "/disciplines_ajax/" + course_id,
                }).done(function(response) {
                    if (response.length) {
                        $("#disciplines").append('<option selected="" value=""></option>');
                        response.forEach(function(discipline) {
                            $("#disciplines").append('<option value="' + discipline.id + '">' +
                                discipline.display_name + '</option>');
                            $("#disciplines").selectpicker('refresh');
                        })
                        $("#disciplines").prop('disabled', false);
                        $("#disciplines").selectpicker('refresh');
                    }
                })
            }

            function getStudents(discipline_id) {
                $.ajax({
                    url: "/students_ajax/" + discipline_id + "/" + lective_year,
                }).done(function(response) {
                    if (response.length) {
                        $("#students").empty();
                        $("#students").append('<option selected="" value=""></option>');
                        response.forEach(function(student) {
                            $("#students").append('<option value="' + student.id + '">' + student
                                .display_name + '</option>');
                            $("#students").selectpicker('refresh');
                        })
                        $("#students").prop('disabled', false);
                        $("#students").selectpicker('refresh');
                    }
                    else {
                        $("#estudantes").text("Não existe estudantes na lista para este curso, disciplina e ano lectivo.");                
                        $("#lista_estudantes").modal('show');
                    }
                })
            }

            function getAttedance(student_id, discipline_id) {
                $.ajax({
                    url: "/attendance_ajax/" + student_id + "/" + discipline_id + "/"+ lective_year,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        let totalLessons = data.totalLessons;
                        let totalPresences = data.totalPresences;
                        let totalByRegimes = data.totalByRegimes;
                        let body = '';
                        let i = 1;
                        flag = true;
                        if (totalLessons.length < 1) {
                            body += "<tr>"
                            body += "<td colspan='5' class='text-center'>Sem dados</td>"
                            body += "</tr>"
                            $("#presence").append(body);
                        } else {
                            for (let a = 0; a < totalLessons.length; a++) {
                                body += "<tr>"
                                let discipline_id = totalLessons[a].discipline_id;
                                flag = true;
                                body += "<td>" + totalLessons[a].name +
                                    "</td><td class='text-center'>" + totalLessons[a].maximum_absence +
                                    "</td><td class='text-center'>" + totalLessons[a].total + "</td>"
                                for (let b = 0; b < totalPresences.length; b++) {
                                    if (discipline_id == totalPresences[b].discipline_id) {
                                        flag = false;
                                        body += "<td class='text-center'>" + totalPresences[b].total +
                                            "</td>"
                                        body += "<td class='text-center'>" + (totalLessons[b].total -
                                            totalPresences[b].total) + "</td>"
                                    }
                                }
                                if (flag) {
                                    body += "<td class='text-center'> 0 </td>";
                                    body += "<td class='text-center'>" + (totalLessons[a].total - 0) +
                                        "</td>"
                                }
                                body += "</tr>"
                            }
                            $("#presence").append(body);
                        }
                    },
                    error: function(error) {
                        alert(error);
                    }
                })
            }

            function disabledFields() {
                $("#disciplines").prop('disabled', true);
                $("#disciplines").selectpicker('refresh');
                $("#students").prop('disabled', true);
                $("#students").selectpicker('refresh');
                $("#presence").empty();
            }

            loadCourses();
        })
    </script>
@endsection
