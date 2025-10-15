@section('title', __('Percurso acadêmico'))


@extends('layouts.backoffice')

@section('styles')
@parent
<style>
        html,
        body {}

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

    
@section('content')
<div class="content-panel" style="padding: 0;">
   @include('Avaliations::avaliacao.navbar')
        <div class="content-header">
            <div class="container-fluid">
               <div class="row">
                   <div class="col-sm-12">
                       <div class=" float-right" > 
                           <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                               <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">Avaliações</a></li>
                               <li class="breadcrumb-item active" aria-current="page">Percurso académico</li>
                           </ol>
                       </div>
                   </div>
               </div> 
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            Percurso académico
                        </h1> 
                    </div>
                </div>
            </div>
        </div>

       
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                {!! Form::open(['route' => ['concluir.notas']]) !!}

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
                            <div class="card" style="margin-left: 10px;">
                               {{-- Se for um estudante --}}
                                <div class="row">
                                   @if (isset($student))
                                   <div class="col-6">
                                       <div class="form-group col">
                                           <label>Curso:</label>
                                           {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control', 'disabled']) }}
                                       </div>
                                   </div>

                                   <div class="col-6">
                                       <div class="form-group col">
                                           <label>Estudantes:</label>
                                           {{ Form::bsLiveSelectEmpty('students', $student, 1, ['id' => 'students', 'class' => 'form-control']) }}
                                       </div>
                                   </div>
                                   <div class="row">
                                       <div class="col"  id="btn-print">
                                           <div class="float-right mr-3">
                                               <a class="btn btn-success mb-3 ml-3" href="" id="btn-link" target="_blank" route rel="noopener noreferrer"
                                                   class="btn btn-primary">
                                                   <i class="fas fa-plus-circle"></i>
                                                   Imprimir documento
                                               </a>
                                           </div>
                                       </div>
                                   </div>
                                   @else
                                         <div class="col-6">
                                        <div class="form-group col">
                                            <label>Curso:</label>
                                            {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control', 'disabled']) }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Estudantes:</label>
                                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control', 'disabled']) }}
                                        </div>
                                    </div>

                                    <div class="row">
                                       <div class="col" hidden id="btn-print">
                                           <div class="float-right mr-3">
                                               <a class="btn btn-success mb-3 ml-3" href="" id="btn-link" target="_blank" route rel="noopener noreferrer"
                                                   class="btn btn-primary">
                                                   <i class="fas fa-plus-circle"></i>
                                                   Imprimir documento
                                               </a>
                                           </div>
                                       </div>
                                   </div>
                                   @endif

                                 
                                </div>

                           


                               
                                
                            </div>
                        </div>

                        {!! Form::close() !!}


                    </div>
                </div>
            </div>
<br>
        @endsection

        @section('scripts')
            @parent
            <script>
                // $(function fechar() {
                //     $("form").attr("id","formulario");
                //     // document.forms["formulario"].submit();  
                //     setTimeout(
                //         function() {
                //             // document.forms["formulario"].submit();        ^


                //               document.forms["formulario"].submit();        
                //         },2000
                //     );

                // });



                $(function() {
                    var selectStudyPlan = $("#course_id");
                    var selectStudents = $("#students");
                    var id_aluno = parseFloat('{{ 0 }}')


                    $.ajax({
                        url: "/avaliations/curricular_path_course/" + id_aluno,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        //$('#container').html(data.html);

                    }).done(function(data) {
                        //if (dataResult.length) {
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
                        //}
                    });


                    $("#students").change(function() {
                        var url = "";
                        if ($('#students').val() == "") {
                            $("#btn-print").prop('hidden', true);
                        } else {
                            $("#btn-link").attr('href', "academic-path/" + $('#students').val());
                            $("#btn-print").prop('hidden', false);
                        }


                        //$("#btn-print").prop('hidden', false);
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
                            //$('#container').html(data.html);

                        }).done(function(data) {
                            //if (dataResult.length) {
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


                            //}
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
                            //$('#container').html(data.html);

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

 
                            //}
                        });
                    }

                })
            </script>



        @endsection
