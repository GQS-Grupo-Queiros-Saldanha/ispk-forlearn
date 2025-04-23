@section('title',__('Marcação de Exame | Outros'))


@extends('layouts.backoffice')
@section('styles')
@parent
<style>
    .red {
        background-color: red !important;
    }

    .dt-buttons{
        float: left;
        margin-bottom: 20px; 
    }

    .dataTables_filter label{
        float: right;  
    }

    
    .dataTables_length label{
        margin-left: 10px; 
    }
    .casa-inicio{
        
    }

    .div-anolectivo{
        width:300px; 
        padding-top:16px;
        padding-right:0px;
        margin-right: 15px;  
    }

    table,
    th,
    td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
    }
    </style>
@endsection
@section('content')
<div class="content-panel" style="padding: 0;">
    @include('Avaliations::requerimento.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right" > 
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="/avaliations/requerimento">Requerimentos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Marcação de exame</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Marcação de Exame | Outros')</h1>
                    </div>
                  
                    <div class="col-sm-6">
                        <div class="float-right div-anolectivo">
                            <label>Selecione o ano lectivo</label>
                            <br>
                            <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
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

        {{-- INCLUI O MENU DE BOTÕES --}}
        {{-- @include('Avaliations::avaliacao.show-panel-avaliation-button') --}}

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                <form action="{{ route('schedule_exam.store') }}" method="POST">
                    @method('POST')
                    @csrf


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
                                            <label>Selecionar curso</label>
                                                {{ Form::bsLiveSelectEmpty('courses', [], null, ['id' => 'courses', 'class' => 'form-control'])}}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Selecionar exame</label>
                                                <select name="exam" id="exam" required class="selectpicker form-control form-control-sm" data-live-search="true" data-actions-box="false" data-selected-text-format="values" tabindex="-98">
                                                @foreach ($exams as $exam)
                                                        <option value="{{ $exam['id'] }}">
                                                                {{$exam['display_name']}}
                                                        </option>
                                                    @endforeach
                                                </select>

                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="lectiveY"  value="" name="anoLectivo">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Estudante</label>
                                            {{ Form::bsLiveSelectEmpty('students', [], null, ['id' => 'students', 'class' => 'form-control'])}}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group col" id="group">

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <div class="float-right">
                                <button  id="btnExame" type="submit" class="btn btn-success mb-3">
                                    <i class="fas fa-plus-circle"></i>
                                    Marcar exame 
                                </button>
                                
                            </div>
   
                </form>


            </div>
        </div>
    </div>
@endsection


@section('scripts')
    @parent
    <script>
        $(function(){
  // Oculta o botão no início
  $('#btnExame').hide();


            function verificarCampos() {
        var students = $.trim($('#students').val());
        var courses = $.trim($('#courses').val());
        var exam = $('#exam').val(); // Não precisa usar $.trim() aqui
        var turmaExists = $('#turma').is(':visible'); // Verifica se o elemento com ID 'turma' existe e está visível

       

        // Verifica se students e courses não estão vazios, se exam é diferente de 0, e se turma existe
        if ((students !== "" && courses !== "" && exam !== "0" && turmaExists) || (students !== "" && courses !== "" && (exam === "1" || exam === "5" || exam === "6"))) {
            $('#btnExame').show();  // Exibe o botão
        } else {
            $('#btnExame').hide();  // Oculta o botão
        }
    }

    // Executa a verificação ao mudar qualquer um dos campos
    $('#students, #courses, #exam').on('change keyup', verificarCampos);

  
            //Input lective year 
            $("#lectiveY").val($("#lective_year").val());
            $("#lective_year").change(function(){

                $("#lectiveY").val($("#lective_year").val());
            });


            listCourses();
            $("#exam").change(function(){
                var exam = $("#exam").val();
                var course_id = $("#courses").val();
                var lective_year_matriculation = $("#lective_year").val();
                $("#group").empty();

                $.ajax({
                    url: "/avaliations/get_students_where_has/" + exam + "/" + course_id +"/"+lective_year_matriculation,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',

                    success: function (result) {
                        
                        $("#students").prop('disabled', true);
                        $("#students").empty();

                        $("#students").append('<option selected="" value=""></option>');
                        $.each(result, function (index, row) {
                            $("#students").append('<option value="' + row.user_id + '">' + row.name + " #"+ row.student_number + " ("+ row.email +")"+ '</option>');
                        });

                        $("#students").prop('disabled', false);
                        $("#students").selectpicker('refresh');
                    },
                    error: function (dataResult) {
                        //alert('error' + result);
                    }

                });
            })

            
            $("#courses").change(function(){
               
                // $("#exam").empty();
                $("#students").empty();
            });


            $("#students").change(function() {
    var student_id = $("#students").val();
    var exam = $("#exam").val();
    var lectiveYear = $("#lective_year").val();
    $("#group").empty();

    $.ajax({
        url: "/avaliations/get_exam_info_by/" + exam + "/" + student_id + "/" + lectiveYear,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',

        success: function(result) {
            var bodyData = '';
            
            if (result == 501) {
                bodyData = "<label>O Sistema não detectou nenhum recurso para este(a) estudante no presente ano lectivo. Em caso de dúvida, verifique o percurso acadêmico do mesmo.</label>";
            } else if (result == 502) {
                bodyData = "<label>Não foi encontrada nenhuma matrícula no ano lectivo selecionado deste aluno. Verifique se o mesmo encontra-se matriculado no presente ano para marcar um exame de recurso.</label>";
            } else if (result == 505) {
                bodyData = "<label>Aviso! A marcação de <b>EXAME ESPECIAL</b> encontra-se indisponível. Por favor, contacte o apoio da <b>forLEARN</b> para liberar esta funcionalidade.</label>";
            }
            else if (result == 506) {
                bodyData = "<label>Não foi encontrada nenhuma nota negativa no ano lectivo selecionado deste aluno.</label>";
            }
            else if (result['ExameExpecial'] == 0) {
               console.log('oiee');
                // Processa as turmas e disciplinas
                $.each(result['Turma'], function(index, item) {
                    // Verifica se item[0].length é maior que 0, caso contrário, não carrega disciplinas
                    if (item[0].length === 0) {
                        alert("Nenhuma turma foi encontrada para o(a) estudante(a) selecionado(a).");
                    } else {
                        let ano = index.split("_");
                        bodyData += "<label>" + ano[0] + "º Ano</label><select id='turma' class='form-control' name='turma[]'>";

                        // Monta o select com as turmas
                        $.each(item, function(indexz, gaz) {
                            $.each(gaz, function(indexx, value) {
                                bodyData += "<option value=" + index + ',' + value.id + ">" + value.display_name + "</option>";
                            });
                        });

                        bodyData += "</select>";

                        // Monta as disciplinas correspondentes à turma
                        $.each(result['Disciplina'][index], function(index_d, item_disc) {
                            bodyData += "<div class='form-check'><input class='form-check-input' type='checkbox' value=" + index + ',' + item_disc.discipline_id + " id=check_" + item_disc.discipline_id + " name='disciplines[]'><label class='form-check-label' for='defaultCheck1'>#" + item_disc.discipline_code + '-' + item_disc.discipline_name + "</label></div>";
                        });
                    }
                });
            } else {
                // Caso o exame especial não seja igual a 0, carrega as disciplinas normalmente
                $.each(result, function(index, item) {
                    bodyData += "<div class='form-check'><input class='form-check-input' type='checkbox' value=" + item.discipline_id + " id=check_" + item.discipline_id + " name='disciplines[]'><label class='form-check-label' for='defaultCheck1'>#" + item.discipline_code + '-' + item.discipline_name + "</label></div>";
                });
            }

            // Insere o conteúdo gerado no elemento #group
            $("#group").append(bodyData);
            console.log('ei:'+$('turma').val())
            verificarCampos();
        },
        error: function(dataResult) {
            alert('Erro na busca das disciplinas em exame de recurso, tente novamente: código do erro ' + dataResult);
        }
    });
});

        })

        function listCourses()
        {
            var selectCourse = $("#courses");
            $.ajax({
                url: "/avaliations/list_courses/",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',

                success: function (result) {
                    selectCourse.prop('disabled', true);
                    selectCourse.empty();


                    selectCourse.append('<option   selected="" value=""></option>');
                    $.each(result, function (index, row) {
                        selectCourse.append('<option value="' + row.id + '">' + row.current_translation.display_name + '</option>');
                    });

                    selectCourse.prop('disabled', false);
                    selectCourse.selectpicker('refresh');
                },
                error: function (result) {
                // alert('error' + result);
                }

            });
        }
    </script>
@endsection


