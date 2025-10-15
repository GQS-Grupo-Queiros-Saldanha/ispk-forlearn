<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PERCURSO ACADÉMICO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Percurso académico</li>
@endsection
@section('styles-new')
    @parent
    <style>
        body {
            font-family: "sans-serif";
        }

        .table td,
        .table th {
            padding: 2px;
        }

        .h1-title {
            padding: 0;
            margin-bottom: 0;
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
        }

        .div-top {
            text-transform: uppercase;
            position: relative;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            margin-bottom: 25px;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 70px;
            height: 100%;
            margin-bottom: 5px;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }
    </style>
@endsection
@section('body')
    {!! Form::open(['route' => ['concluir.notas']]) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div id="showSelect" class="row">
        <div class="col-6 p-2">
            <label>Curso:</label>
            {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control', 'disabled']) }}
        </div>
        <div class="col-6 p-2">
            <label>Estudantes:</label>
            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control', 'disabled']) }}
        </div>
        <div class="col" hidden id="btn-print">
            <div class="float-right mr-3">
                <a class="btn btn-success mb-3 ml-3" href="" id="btn-link" target="_blank" route
                    rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    Imprimir documento
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            var selectStudyPlan = $("#course_id");
            var selectStudents = $("#students");
            var id_aluno = parseFloat('{{ $id_aluno }}')
            $.ajax({
                url: "/avaliations/curricular_path_course/" + id_aluno,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data) {
                console.log(data);
                selectStudyPlan.prop('disabled', true);
                selectStudyPlan.empty();

                selectStudyPlan.append('<option selected="" value=""></option>');
                $.each(data['course'], function(index, row) {
                    if (data['aluno'].length > 0) {
                        console.log("Angola");
                        if (data['aluno'][0].courses_id == row.id) {
                            getalunocurso(row.id)
                            selectStudyPlan.append('<option selected value="' + row.id + '">' + row
                                .current_translation.display_name + '</option>');
                        } else {
                            selectStudyPlan.append('<option value="' + row.id + '">' + row
                                .current_translation.display_name + '</option>');
                        }
                    } else {
                        selectStudyPlan.append('<option value="' + row.id + '">' + row
                            .current_translation.display_name + '</option>');
                    }

                });

                selectStudyPlan.prop('disabled', false);
                selectStudyPlan.selectpicker('refresh');
            });

            $("#students").change(function() {
                var url = "";
                if ($('#students').val() == "") {
                    $("#btn-print").prop('hidden', true);
                } else {
                    $("#btn-link").attr('href', "academic-path/" + $('#students').val());
                    $("#btn-print").prop('hidden', false);
                }
            });


            $("#course_id").change(function() {
                $.ajax({
                    url: "/avaliations/curricular_path_students/" + $("#course_id").val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {
                    selectStudents.prop('disabled', true);
                    selectStudents.empty();
                    console.log("ujjgj", data);
                    selectStudents.append('<option selected="" value=""></option>');
                    $.each(data, function(index, row) {
                        selectStudents.append('<option value="' + row.id + '">' + row.name +
                            " #" + row.mecanografico + " (" + row.email + ")" +
                            '</option>');
                    });

                    selectStudents.prop('disabled', false);
                    selectStudents.selectpicker('refresh');
                });
            })

            function getalunocurso(id_curso) {
                $.ajax({
                    url: "/avaliations/curricular_path_students/" + id_curso,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                }).done(function(data) {
                    //if (dataResult.length) {
                    selectStudents.prop('disabled', true);
                    selectStudents.empty();
                    console.log("ujjgj", data);
                    selectStudents.append('<option selected="" value=""></option>');
                    $.each(data, function(index, row) {
                        if (id_aluno == row.id) {
                            selectStudents.append('<option selected value="' + row.id + '">' + row
                                .name + " #" + row.mecanografico + " (" + row.email + ")" +
                                '</option>');

                            // tarefa para o Marcos tem, criar a url para o PDF.
                            var url = "";
                            if ($('#students').val() == "") {
                                $("#btn-print").prop('hidden', true);
                            } else {
                                $("#btn-link").attr('href', "academic-path/" + $('#students')
                                    .val());
                                $("#btn-print").prop('hidden', false);
                            }
                        } else {
                            selectStudents.append('<option value="' + row.id + '">' + row.name +
                                " #" + row.mecanografico + " (" + row.email + ")" + '</option>');
                        }
                    });
                    selectStudents.prop('disabled', false);
                    selectStudents.selectpicker('refresh');
                });
            }
        })
    </script>
@endsection
