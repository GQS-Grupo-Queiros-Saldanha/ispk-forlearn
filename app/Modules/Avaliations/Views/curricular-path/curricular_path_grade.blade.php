@section('title', __('Localizar percurso académico alterados'))


@extends('layouts.backoffice')

@section('styles')
@parent
<style>
        html,
        body {}

        body {
            font-family: "sans-serif";
        }

        #table_pauta, th, td,thead {
            border: none 0.9px solid;        
        }
        th{
        background-color:#999;
        color:white;
        padding:5px;
        font-size: 18pt;    
        border-bottom:1px solid white;
        border-right:1px solid white;
        font-weight:bold;
        }
        tr:nth-child(even) {background: #FFF}
        tr:nth-child(odd) {background: #EEE}
        td{
        padding:5px;
        font-size: 18pt;  
        border-bottom:1px solid white;
        border-right:1px solid white;

        } tr:hover{
            cursor:pointer;
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
                            <li class="breadcrumb-item active" aria-current="page">Localizar percurso académico alterados</li>
                        </ol>
                    </div>
                </div>
            </div> 
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Localizar percurso académico alterados - TFC
                    </h1>
                </div>
            </div>
        </div>
    </div>

        {{-- INCLUI O MENU DE BOTÕES --}}
        {{-- @include('Avaliations::avaliacao.show-panel-avaliation-button')  --}}

        {{-- Main content --}}
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
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Curso:</label>
                                        {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control', 'disabled']) }}
                                    </div>
                                </div>

                                <div class="col-6" hidden>
                                    <div class="form-group col">
                                        <label>Estudantes:</label>
                                        {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control', 'disabled']) }}
                                    </div>
                                </div>
                            </div>

                            {{-- <hr>
                            <div class="col-12">

                                <div id="div_btn_save" class=" float-right">
                                    <a id="generate-pdf" target="_blank" style="color: white">
                                        
                                        <span class="btn btn-success mb-3 ml-3" id="btn-Enviar" data-toggle="modal"
                                            data-target="#exampleModal">
                                            <i class="fas fa-plus-circle"></i>
                                            Imprimir pauta
                                        </span>
                                    </a>
                                </div>
                            </div> --}}

                            
                            <div class="card mr-1" id="table_dados">
                                {{-- <h4 id="titulo_semestre"></h4> --}}
                                <table class="table_pauta table-hover dark">
                                    <thead class="table_pauta">
                                        
                                        <tr id="listaMenu" style="text-align: center">
                                            
                                        </tr>
                                        
                                    </thead>
                                    <tbody id="lista_tr">
                                        
                                    </tbody>
                                </table>  
                            </div> 

                            <div class="row">
                                
                                <br><br><br>

                                <div class="col" hidden id="btn-print">
                                    <div class="float-right mr-3" hidden>
                                        <a class="btn btn-success mb-3 ml-3" href="" id="btn-link" target="_blank" route rel="noopener noreferrer"
                                            class="btn btn-primary">
                                            <i class="fas fa-plus-circle"></i>
                                            Atualizar Percurso
                                        </a>
                                    </div>
                                </div>
                            </div>   



                        </div>
                    </div>

                    {!! Form::close() !!}


                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>

        $(function() {
            var selectStudyPlan = $("#course_id");
            var selectStudents = $("#students");
            var id_aluno = parseFloat('{{ $id_aluno }}')


            $.ajax({
                url: "/avaliations/curricular_path_pauta",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                //$('#container').html(data.html);

            }).done(function(data) {
                //if (dataResult.length) {
                //console.log(data);
                selectStudyPlan.prop('disabled', true);
                selectStudyPlan.empty();

                selectStudyPlan.append('<option selected="" value=""></option>');
                $.each(data['course'], function(index, row) {
                    
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
                    $("#btn-link").attr('href', "academic-path/" + $('#students').val());
                    $("#btn-print").prop('hidden', false);

                    getStudenteAcademicPach($('#students').val());
                }


                //$("#btn-print").prop('hidden', false);
            });




            $("#course_id").change(function() {
                $.ajax({
                    url: "/avaliations/curricular_path_pauta_students/" + $("#course_id").val(),
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function(data) {

                    $("#listaMenu").empty();
                    $("#listaAno").empty();
                    $("#lista_tr").empty();
                    //$("#table_dados").empty();
                    
                    var tabelaMenu="";
                    var tabelalist="";

                    var tabelatrAno="";
                    var tabelaDados="";
                    var anoLectivo_count = 0;
                    var contaDisciplina = 0;
                    var user_id = 0;     
                    var aluno_id = "";
                    var alunon = 0;


                    //console.log("Deu certo", data['data']['pauta_tfc']);


                            
                    tabelaMenu+="<th style='text-align:center;'>Nº</th>"
                    tabelaMenu+="<th style='text-align:center;'>Estudante</th>"
                    tabelaMenu+="<th style='text-align:center;'>Email</th>"
                    tabelaMenu+="<th style='text-align:center;'>Código</th>"
                    tabelaMenu+="<th style='text-align:center;'>Disciplina</th>"
                    tabelaMenu+="<th style='text-align:center;'>Ano</th>"
                    tabelaMenu+="<th style='text-align:center;'>Nota</th>"

                    $("#listaMenu").append(tabelaMenu);
                    //tabelaMenu = ""; 
                    
                    $.each(data['data']['pauta_tfc'], function (index, item) {
                        console.log(index, item);
                        
                        //if (user_id != index)
                        //{
                        //    user_id = index

                            //tabelaMenu+="<th colspan='6' style='text-align: center; font-size: 15pt;'><b>UNIDADES CURRICULARES</b></th>"
                                                                                    
                            $.each(item, function (index_grade, item_grade) {

                                if (aluno_id != item_grade.id) {
                                
                                    aluno_id = item_grade.id;
                                    alunon = alunon + 1;
                                    //console.log(item)
    
                                    tabelalist+="<tr>"
                                    tabelalist+="<th style='text-align:left;'>"+ alunon+"</th>"
                                    tabelalist+="<th style='text-align:left;'>"+ item_grade.name+"</th>"
                                    tabelalist+="<th style='text-align:left;'>"+ item_grade.email+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.code+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.discipline_name+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.lective_year+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.grade+"</th>"
                                    tabelalist+="</tr>"

                                } 
                                else {
                                    tabelalist+="<tr>"
                                    tabelalist+="<th style='text-align:left; background-color:white;'></th>"
                                    tabelalist+="<th style='text-align:left; background-color:white;'></th>"
                                    tabelalist+="<th style='text-align:left; background-color:white;'></th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.code+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.discipline_name+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.lective_year+"</th>"
                                    tabelalist+="<th style='text-align:center;'>"+ item_grade.grade+"</th>"
                                    tabelalist+="</tr>"
                                }

                            })

                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            tabelalist+="<tr> <th style='background-color:white;></th>  </tr>"
                            
                            
                            $("#lista_tr").append(tabelalist);
                            tabelalist = "";                            
                        
                        //}                       

                    })

                    
                    

                });
            })


            function getalunocurso(id_curso) {
                $.ajax({
                    url: "/avaliations/curricular_path_pauta_students/" + id_curso,
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
                    //console.log("ujjgj", data);
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
                        

                        //selectStudents.append('<option value="' + row.id + '">' + row.pauta_tipo + '</option>');
                    });

                    selectStudents.prop('disabled', false);
                    selectStudents.selectpicker('refresh');


                    //}
                });
            }

            function getStudenteAcademicPach(id_student){

                $.ajax({
                    url: "/avaliations/student_academic_path/"+id_student,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done( function (data)
                {

                    console.log("Deu certo", data['data'])
                    $("#listaMenu").empty();
                    $("#listaAno").empty();
                    $("#lista_tr").empty();
                    
                    var tabelatr="";
                    var tabelalist="";
                    var tabelatrAno="";
                    var anoLectivo_count = 0;
                    var contaDisciplina = 0;
                    
                    tabelatrAno+="<th style='text-align:center;'>Código</th>"
                    tabelatrAno+="<th style='text-align:center;'>Nome</th>"
                    tabelatrAno+="<th style='text-align:center;'>Ano</th>"
                    tabelatrAno+="<th style='text-align:center;'>Nota</th>"

                    $("#listaAno").append(tabelatrAno);


                    $.each(data['data']['pauta_tfc'], function (index, item) {
                        
                        tabelalist+="<tr>"
                        tabelalist+="<th style='text-align:center;'>"+ item.code+"</th>"
                        tabelalist+="<th style='text-align:center;'>"+ item.discipline_name+"</th>"
                        tabelalist+="<th style='text-align:center;'>"+ item.lective_year+"</th>"
                        tabelalist+="<th style='text-align:center;'>"+ item.grade+"</th>"
                        //tabelalist+="<th><input type='number' name='grade[]'' class='form-control' style='width:100px; height:30px;' min='0' max='20' value='"+item.grade+"'></th>"
                        tabelalist+="</tr>"

                        //atualiza_dados.push(item.student_grades_id);
                        
                    })

                    $("#lista_tr").append(tabelalist);
                    
                    
                })
            }

        })
    </script>



@endsection
