@section('title', __('Documentação do estudante'))


@extends('layouts.backoffice')

@section('content')
    <style>
        html,
        body {}

        body {
            font-family: Montserrat, sans-serif;
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
    <div class="content-panel" style="padding: 0;">
        @include('Avaliations::requerimento.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Documentos do estudante</li>
                            </ol>
                        </div>
                    </div>
                </div>


                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('DOCUMENTAÇÃO DO ESTUDANTE')</h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>

            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">
                {!! Form::open(['route' => ['document.generate-documentation'], 'method' => 'post', 'target' => '_blank']) !!}

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

                        <div id="showSelect">
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Curso:</label>
                                            {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'required' => '', 'class' => 'form-control', 'disabled']) }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Estudantes:</label>
                                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'required' => '', 'class' => 'form-control', 'disabled']) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label for="type_document">Documento</label>
                                            <select data-live-search="true" required
                                                class="selectpicker form-control form-control-sm" required=""
                                                id="type_document" data-actions-box="false"
                                                data-selected-text-format="values" name="type_document" tabindex="-98">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group col anos_estudantes">
                                            <label for="student_year">Ano</label>
                                            <select class="selectpicker form-control form-control-sm" name="student_year"
                                                id="student_year" data-actions-box="true" data-live-search="true">
                                            </select>
                                        </div>
                                        <div class="form-group col efeito" style="display: none;"> 
                                            <label for="efeito">Para efeito de:</label>
                                            <input type="text" class="form-control" id="efeito_type" name="efeito_type">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-6">

                                    </div>
                                    <div class="col-6" hidden id="btn-print">
                                        <div class="float-right mr-3">
                                            <button type="submit" id="btn-link" target="_blank" rel="noopener noreferrer"
                                                class="btn btn-primary">
                                                <i class="fas fa-plus-square"></i>
                                                Gerar documento
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::close() !!}


                    </div>
                </div>
            </div>
            <br>
            <br>

        @endsection

        @section('scripts')
            @parent
            <script>
                $(function() {
                    var selectStudyPlan = $("#course_id");
                    var type_document = $("#type_document");
                    var student_year = $("#student_year");
                    var selectStudents = $("#students");
                    $(".anos_estudantes").hide();
                    $.ajax({
                        url: "/reports/documentation_course/",
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        //$('#container').html(data.html);

                    }).done(function(data) {
                        //if (dataResult.length) {
                        selectStudyPlan.prop('disabled', true);
                        selectStudyPlan.empty();

                        selectStudyPlan.append('<option selected="" value=""></option>');
                        $.each(data, function(index, row) {
                            selectStudyPlan.append('<option value="' + row.id + '">' + row
                                .current_translation.display_name + '</option>');
                        });

                        selectStudyPlan.prop('disabled', false);
                        selectStudyPlan.selectpicker('refresh');
                        //}
                    });


                    $("#students").change(function() {
                        var url = "";
                        if ($('#students').val() == "") {
                            $("#btn-print").prop('hidden', true);
                        } else {
                            // $("#btn-link").attr('href', "document.generate-documentation/"+$('#students').val());
                            $("#btn-print").prop('hidden', false);
                        }
                        //$("#btn-print").prop('hidden', false);
                    });

                    $("#type_document").change(function() {

                        if ($('#type_document').val() == "2") {
                            $(".anos_estudantes").show();
                        } else {
                            $(".anos_estudantes").hide();
                        }
                        
                        if ($('#type_document').val() == "6" || $('#type_document').val() == "1" || $('#type_document').val() == "2") {
                            $(".efeito").show(); 
                        } else {
                            $(".efeito").hide();  
                        }


                    });

                    $("#course_id").change(function() {
                        $.ajax({
                            url: "/reports/documentation_students/" + $("#course_id").val(),
                            type: "GET",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            cache: false,
                            dataType: 'json',
                            //$('#container').html(data.html);

                        }).done(function(data) {
                            //if (dataResult.length) {
                            selectStudents.prop('disabled', true);
                            selectStudents.empty();

                            selectStudents.append('<option selected="" value=""></option>');
                            $.each(data, function(index, row) {
                                selectStudents.append('<option value="' + row.id + '">' + row.name +
                                    " #" + row.mecanografico + " (" + row.email + ")" +
                                    '</option>');
                            });

                            selectStudents.prop('disabled', false);
                            selectStudents.selectpicker('refresh');

                            //}
                        });
                    })




                    var selectSType = $("#type_document");
                    $.ajax({
                        url: "/gestao-academica/document_type/",
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        //$('#container').html(data.html);

                    }).done(function(data) {
                        //if (dataResult.length) {
                        selectSType.prop('disabled', true);
                        selectSType.empty();

                        selectSType.append('<option value="">Selecione o tipo de documento</option>');
                        $.each(data, function(index, row) {
                            selectSType.append('<option value="' + row.id + '">' + row.name + '</option>');
                        });
                        selectSType.prop('disabled', false);
                        selectSType.selectpicker('refresh');
                        //}
                    });


                    selectStudents.change(function() {
                        mensalidades();
                    });

                    function mensalidades() {

                        $.ajax({
                            url: "/avaliations/requerimento_ajax/" + selectStudents.val(),
                            type: "GET",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            cache: false,
                            dataType: 'json',


                        }).done(function(data) {

                            student_year.empty();


                            data["anos"].forEach(function(ano_matriculado) {
                                student_year.append('<option value="' + ano_matriculado.ano + '">' +
                                    ano_matriculado.ano +
                                    '</option>');
                            });

                            student_year.selectpicker('refresh');

                        });

                    }




                })
            </script>

        @endsection
