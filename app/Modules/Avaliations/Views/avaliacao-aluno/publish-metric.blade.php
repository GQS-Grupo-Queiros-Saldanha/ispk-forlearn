@section('title',__('Avaliações | Publicar notas'))


@extends('layouts.backoffice')
     <style>
      table,
      th,
      td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
      }
    </style>
@section('content')
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                           Publicar notas
                        </h1>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-bottom: 10px">
            <div class="container-fluid">

                {!! Form::open(['route' => ['publish.metric']]) !!}

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

                        <button type="submit" class="btn btn-success mb-3">
                            <i class="fas fa-plus-circle"></i>
                              Salvar
                        </button>

                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Edição de Plano de Estudo</label>
                                                {{ Form::bsLiveSelectEmpty('study_plan_edition_id', [], null, ['id' => 'study_plan_edition_id', 'class' => 'form-control','disabled','required'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Disciplina</label>
                                            {{ Form::bsLiveSelectEmpty('discipline_id',[],null,['id' => 'discipline_id', 'class' => 'form-control', 'disabled','required'])}}
                                        </div>
                                    </div>

                                </div>

                                 <div class="row" id="discipline-group">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Avaliação</label>
                                            {{ Form::bsLiveSelectEmpty('avaliation_id',[],null,['id' => 'avaliation_id', 'class' => 'form-control', 'disabled','required'])}}
                                        </div>
                                    </div>
                                </div>

                                 <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Turma</label>
                                            {{ Form::bsLiveSelectEmpty('class_id',[],null,['id' => 'class_id', 'class' => 'form-control', 'disabled','required'])}}
                                        </div>
                                    </div>
                                </div>

                                 <div class="row" id="discipline-group">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Métrica</label>
                                            {{--<select name="metric_id" id="metric_id" class="form-control" disabled required>
                                                <option value=""></option>

                                            </select>--}}
                                            {{ Form::bsLiveSelectEmpty('metric_id',[],null,['id' => 'metric_id', 'class' => 'form-control', 'disabled','required'])}}

                                        </div>
                                    </div>
                                </div>



                            </div>
                            <hr>

                            <div class="card">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-hover">
                                            <thead>
                                                <th>#</th>
                                                <th>Nº Estudante</th>
                                                <th>Nome</th>
                                                <th>Nota</th>
                                            </thead>
                                            <tbody id="students">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                {!! Form::close() !!}


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function (){
            var selectStudyPlan = $("#study_plan_edition_id");
            var selectDiscipline = $("#discipline_id");
            var selectAvaliation = $("#avaliation_id");
            var selectClass = $("#class_id");
            var selectMetrica = $("#metric_id");

            getAllStudyPlanEdition();

            function getAllStudyPlanEdition(){
                $.ajax({
                    url: "/avaliations/plano_estudo_ajax/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function (data){
                    //if (dataResult.length) {
                        selectStudyPlan.prop('disabled', true);
                        selectStudyPlan.empty();


                        selectStudyPlan.append('<option selected="" value=""></option>');
                        $.each(data, function (index, row) {
                            selectStudyPlan.append('<option value="' + row.spea_id + '">' + row.spea_nome + '</option>');
                        });

                        selectStudyPlan.prop('disabled', false);
                        selectStudyPlan.selectpicker('refresh');

                        //switchRegimes(selectDiscipline[0]);
                    //}
                });
            }
            //$('#discipline_id').prop('disabled', true);
            $("#class_id").prop('disabled', true);

            //Buscar Disciplinas apartir do curso associados ao Plano estudo Avaliacao
           $('#study_plan_edition_id').change(function(){
               var study_plan_edition_id = $(this).children("option:selected").val();
               console.log(study_plan_edition_id);
               $("#class_id").empty();
               $("#avaliation_id").empty();
               $("#metric_id").empty();
               $("#students tr").empty();

               $('#avaliation_id').prop('disabled', true);
               $('#class_id').prop('disabled', true);
               $('#metric_id').prop('disabled', true);

            if(study_plan_edition_id == ""){
                console.log("Empty");
                $('#discipline_id').prop('disabled', true);
                $("#discipline_id").empty();
                $('#avaliation_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#avaliation_id").empty();
                $('#metric_id').prop('disabled', true);
                $('#class_id').empty("");
                $("#metric_id").empty();
                $("#students tr").empty();

            }else{

            $.ajax({

            url: "/avaliations/disciplines_ajax/" + study_plan_edition_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#discipline_id").empty();
                console.log(dataResult);
                var resultData = dataResult.data;
                var resultClasses = dataResult.classes;
                var bodyData = '';
                var bodyClassData = '';
                var i = 1;
                console.log(dataResult.data);
                 selectDiscipline.prop('disabled', true);
                    selectDiscipline.empty();
                    selectClass.empty();

                    selectDiscipline.append('<option selected="" value=""></option>');
                    selectClass.append('<option selected="" value=""></option>');

                    $.each(resultData, function (index, row) {
                        selectDiscipline.append('<option value="' + row.discipline_id + '">' + row.dt_display_name + '</option>');
                    });

                    $.each(resultClasses, function (index, row) {
                        selectClass.append('<option value="' + row.id + '">' + row.display_name + '</option>');
                    });

                    selectDiscipline.prop('disabled', false);
                    selectDiscipline.selectpicker('refresh');

                    selectClass.prop('disabled', false);
                    selectClass.selectpicker('refresh');


            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
   }
        });


        //Buscar Avaliações apartir do curso e disciplina associados ao Plano estudo Avaliacao
           $('#discipline_id').change(function(){
               var discipline_id = $(this).children("option:selected").val();
               console.log(discipline_id);

                $("#students tr").empty();
                $('#metric_id').prop('disabled', true);
                $("#metric_id").empty();
                 $("#avaliation_id").empty();
                 $('#class_id').val("");
                 $('#class_id').prop('disabled', true);

            if(discipline_id == ""){
                console.log("Empty");
                $('#avaliation_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#avaliation_id").empty();
                $('#metric_id').prop('disabled', true);
                $('#class_id').val("");
                $("#metric_id").empty();
                $("#students tr").empty();

            }else{

            $.ajax({

            url: "/avaliations/avaliacao_ajax/" + discipline_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                 $("#avaliation_id").empty();
                console.log(dataResult);
                var resultData = dataResult.data;
                var bodyData = '';
                var i = 1;
                console.log(dataResult.data);
                selectAvaliation.prop('disabled', true);
                selectAvaliation.empty();


                selectAvaliation.append('<option selected="" value=""></option>');
                $.each(resultData, function (index, row) {
                    selectAvaliation.append('<option value="' + row.avl_id + '">' + row.avl_nome + '</option>');
                });

                selectAvaliation.prop('disabled', false);
                selectAvaliation.selectpicker('refresh');


            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
    }
        });

        //Buscar Metricas apartir do curso da disciplina e da avaliacao associados ao Plano estudo Avaliacao
           $('#avaliation_id').change(function(){
               var avaliation_id = $(this).children("option:selected").val();
               console.log(avaliation_id);
                $("#students tr").empty();
                $("#metric_id").empty();
                $('#class_id').val("");

            if(avaliation_id == ""){
                console.log("Empty");
                $('#metric_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#class_id").val("");
                $("#metric_id").empty();
                $("#students tr").empty();

            }else{

            $.ajax({

            url: "/avaliations/metrica_ajax/" + avaliation_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#metric_id").empty();
                console.log(dataResult);
                var resultData = dataResult.data;
                var bodyData = '';
                var i = 1;
                console.log(dataResult.data);
                /*bodyData += "<option value=''></option>";
                $.each(resultData, function (index, row) {

                    bodyData += "<option value=" + row.mtrc_id + ">" + row.mtrc_nome + "</option>";

                })
                $("#metric_id").append(bodyData);

                $('#metric_id').prop('disabled', false);*/
                selectMetrica.prop('disabled', true);
                selectMetrica.empty();


                selectMetrica.append('<option selected="" value=""></option>');
                $.each(resultData, function (index, row) {
                    selectMetrica.append('<option value="' + row.mtrc_id + '">' + row.mtrc_nome + '</option>');
                });

                selectMetrica.prop('disabled', false);
                selectMetrica.selectpicker('refresh');

                //$("#class_id").prop('disabled', false);
                selectClass.prop('disabled', false);
            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
    }
});


    $("#class_id").change(function(){
        if($("#class_id").val() == "")
        {
            $("#metric_id").val("");
            $("#students tr").empty();
            $('#metric_id').prop('disabled', true);
        }else{
            $("#metric_id").val("");
            $('#metric_id').prop('disabled', false);
            $("#students tr").empty();
        }
    });

    //Listar estudante que tem a determinada disciplina e determinada turma.
        $("#metric_id").change(function(){

            if($("#metric_id").val() == "")
            {
                $("#students tr").empty();

            } else {
            var discipline_id = $('#discipline_id').val();
            var metric_id = $('#metric_id').val();
            var study_plan_edition_id = $('#study_plan_edition_id').val();
            var avaliation_id = $('#avaliation_id').val();
            var class_id = $('#class_id').val();

            $.ajax({

            url: "/avaliations/student_ajax/" + discipline_id + "/" + metric_id + "/" + study_plan_edition_id + "/" + avaliation_id + "/" + class_id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {
                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                $("#students tr").empty();
                console.log(dataResult);

                var resultGrades = dataResult.data;
                var resultStudents = dataResult.students;
                var bodyData = '';
                var i = 1;


                var a; for (a = 0; a < resultStudents.length; a++)
                 {
                     var dd = a;

                    //Verifica se o Array das notas está vazio
                    if (resultGrades == '') {
                        checkInputEmpty(dd);
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td>"+ resultStudents[a].n_student +"</td><td>"+ resultStudents[a].user_name +"</td><td></td>"
                        bodyData += '</tr>'
                    }else{
                        checkInputEmpty(dd);
                        bodyData += '<tr>'
                        bodyData += "<td>"+  i++ +"</td><td>"+ resultStudents[a].n_student +"</td><td>"+ resultStudents[a].user_name +"</td>"
                        if (resultGrades[a].aanota != null) {
                           bodyData += "<td>"+ resultGrades[a].aanota +"</td>"
                        }else{
                            bodyData += "<td> 0 </td>"
                        }
                        bodyData += '</tr>'
                     }
                }
                $("#students").append(bodyData);
            },
            error: function (dataResult) {
               // alert('error' + result);
            }

        });
        }
        });
        });

        function disbleInput(dd)
        {
            var checkStatus = document.getElementById("check"+dd+"").checked;
            var inputGrade = document.getElementById(dd);
            var span = document.getElementById("span"+dd+"")

            if (checkStatus == true) {
                inputGrade.readOnly = false;
                inputGrade.value = "";
                //document.getElementById("check"+dd+"").disabled = true;
                span.style.backgroundColor = "#38C172";
                span.innerHTML = "PRESENTE";
            }else{
                 inputGrade.readOnly = true
                 inputGrade.value = "";
                //document.getElementById("check"+dd+"").disabled = true;
                span.style.backgroundColor = "red";
                span.innerHTML = "AUSENTE";
            }

        }

        function checkInputEmpty(dd) {
            var inputGrade = document.getElementById(dd);

                //var dpe = document.getElementById(dd).value;
                //console.log(inputGrade);


        }
    </script>
@endsection
