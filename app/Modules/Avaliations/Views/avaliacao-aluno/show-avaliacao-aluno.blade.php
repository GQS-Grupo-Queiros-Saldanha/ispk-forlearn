 @section('title',__('Visualizar Plano de Estudos Avaliação'))


@extends('layouts.backoffice')

@section('content')
<style>
        html, body {

        }

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
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                         Exibir Avaliações
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('home') }}" style="margin-left: 345px;">Home</a> 
                        / 
                        <a href="/avaliations/panel_avaliation">Avaliação</a> 
                        / 
                        <span style="opacity: 0.5">Exibir avaliações</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- INCLUI O MENU DE BOTÕES --}}
        @include('Avaliations::avaliacao.show-panel-avaliation-button') 

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
                             <div class="card alert alert-warning" id="message">
                                 <div class="row">
                                     <div class="col-6">
                                         <h4>
                                             Não há avaliações para processar
                                         </h4>
                                     </div>
                                 </div>
                                </div>

                        <div id="showSelect">
                            <div class="card">
                                <div class="row">
                                    <div class="col-6">
                                         <div class="" style="margin-left: 15px;">
                                             <button type="submit" class="btn btn-success mb-6" id="submit" disabled hidden>
                                                <i class="fas fa-plus-circle"></i>
                                                Concluir Avaliação
                                            </button>

                                            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal">
                                                <i class="fas fa-plus-circle"></i>
                                                Salvar
                                            </button>
                                         </div>
                                    </div>
                                </div>

                                <br>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Edição de Plano de Estudo</label>
                                            {{-- <select name="course_id" id="course_id" class="form-control" required>
                                                <option value=""></option>
                                                 @foreach ($pea as $item)
                                                    <option value="{{ $item->spea_id }}">
                                                        {{ $item->spea_nome }}
                                                    </option>
                                                   @endforeach
                                                </select> --}}
                                                {{ Form::bsLiveSelectEmpty('course_id', [], null, ['id' => 'course_id', 'class' => 'form-control','disabled'])}}

                                        </div>
                                    </div>
                                {{-- </div>
                                <div class="row"> --}}
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label>Disciplina</label>
                                            {{-- <select name="discipline_id" id="discipline_id" class="form-control" disabled required>
                                                <option value=""> </option>

                                            </select> --}}
                                            {{ Form::bsLiveSelectEmpty('discipline_id',[],null,['id' => 'discipline_id', 'class' => 'form-control', 'disabled'])}}

                                        </div>
                                    </div>

                                {{-- </div>

                                <div class="row"> --}}
                                    <div class="col-6">
                                        <div class="form-group col">
                                            <label for="classes">Turma</label>
                                            {{-- <select name="classes" id="classes" class="form-control" disabled required>
                                                <option value=""></option>
                                            </select> --}}
                                            {{ Form::bsLiveSelectEmpty('classes',[],null,['id' => 'classes', 'class' => 'form-control', 'disabled'])}}

                                        </div>
                                    </div>
                                {{-- </div>


                                 <div class="row" id="discipline-group"> --}}
                                    <div class="col-6" id="discipline-group">
                                        <div class="form-group col">
                                            <label>Avaliação</label>
                                            {{-- <select name="avaliacao_id" id="avaliacao_id" class="form-control" disabled required>

                                            </select> --}}
                                            {{ Form::bsLiveSelectEmpty('avaliacao_id',[],null,['id' => 'avaliacao_id', 'class' => 'form-control', 'disabled'])}}

                                        </div>
                                    </div>
                                </div>

                            </div>

                            </div>
                            <hr>

                        <div id="mainPauta">
                            <div id="tableHeader" hidden>
                            <table class="table m-0 p-0">
                                    <tr>
                                        <td class="pl-1">
                                            <h1 class="h1-title">
                                                Pauta de
                                                <span id="pautaName">

                                                </span>
                                            </h1>
                                        </td>
                                        <td class="td-institution-name" rowspan="2">
                                            Instituto Universitário<br>Politécnico Maravilha
                                        </td>
                                        <td class="td-institution-logo" rowspan="2">
                                            <img class="img-institution-logo" src="{{ asset('img/logo.jpg') }}" alt="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-1">

                                            <b>
                                                {{-- $discipline->course->currentTranslation->display_name--}}
                                            </b>
                                        </td>
                                    </tr>
                            </table>

                            <br>
                            <table class="table table-bordered">
                                <thead>
                                    <th style="font-size: 8pt;" class="text-center">Curso</th>
                                    <th style="font-size: 8pt;" class="text-center">Código</th>
                                    <th style="font-size: 8pt;" class="text-center">Ano Lectivo</th>
                                    <th style="font-size: 8pt;" class="text-center">Regime</th>
                                    <th style="font-size: 8pt;" class="text-center">Turma</th>
                                    <th style="font-size: 8pt;" class="text-center">Avaliação</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="25%" style="font-size: 8pt;" width="25%" style="font-size: 8pt;" class="text-center">
                                            <span id="courseName"></span>
                                            {{-- {{ $discipline->course->currentTranslation->display_name }} --}}
                                        </td>
                                        <td width="25%" style="font-size: 8pt;" class="text-center">
                                            <span id="disciplineName"></span>
                                            {{-- {{ $discipline->code }} - {{ $discipline->currentTranslation->display_name }} --}}
                                        </td>
                                        <td width="25%" style="font-size: 8pt;" class="text-center"></td>
                                        <td width="25%" style="font-size: 8pt;" class="text-center">
                                            <span id="periodName"></span>
                                            {{-- {{ $discipline->study_plans_has_disciplines->first()->discipline_period->currentTranslation->display_name }} --}}
                                        </td>
                                        <td width="25%" style="font-size: 8pt;" class="text-center">
                                            <span id="className"></span>
                                            {{-- {{ $class->code }} --}}
                                        </td>
                                        <td width="25%" style="font-size: 8pt;" class="text-center">
                                            <span id="avaliationName"></span>
                                            {{-- {{ $avaliacao[0]->nome }} --}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>



                            <div class="card">
                                <div class="row">
                                    <div class="col-12">
                                        <div id="table-to-print">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead id="head">

                                                </thead>

                                                <tbody id="body">

                                                </tbody>
                                            </table>

                                            <div hidden id="tableFooter">

                                                <div class="col-12">
                                                    <table class="table-borderless">
                                                        <thead>
                                                            <th colspan="2" style="font-size: 9pt;">
                                                                Assinaturas
                                                            </th>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td style="font-size: 9pt;">Docente: ________________________________________________________________________.</td>
                                                                <td style="font-size: 9pt;">Pelo gabinete de termos: ____________________________________________________________________.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="print-button" hidden>
                                {{-- <a class="btn btn-primary" id="generate-pdf" target="_blank">
                                    IMPRIMIR
                                </a> --}}
                                <button id="make-print" type="button" class="btn btn-primary">
                                    IMPRIMIR
                                </button>
                            </div>


                {!! Form::close() !!}


            </div>
        </div>
    </div>




    <!-- Modal -->
        <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="exampleModalLabel">ALERTA | Confirmação de dados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="float-right">
                        <p class="text-danger" style="font-weight:bold; !important"> Caro DOCENTE ({{auth()->user()->name}}), as informações inseridas
                        neste<br> formulário CONCLUIR AVALIAÇÃO, são da sua inteira responsabilidade.
                        <br> Por favor seja rigoroso na informação prestada.</p>
                    </div>

                        <br>
                        <br>

                    <div style="margin-top:50px; !important">
                        <p style="padding:5px; !important">Verifique se os dados estão correctos, nomeadamente: </p>

                        <ul>
                            <li style="padding:5px; !important">
                                Todos os alunos pertencem a esta TURMA?
                            </li>
                            <li style="padding:5px; !important">
                                Falta algum aluno nesta TURMA?
                            </li>
                        </ul>
                    </div>

                   <div style="margin-top:10px; !important">
                        <p>
                            No caso de <span class="text-danger"><b>HAVER</b></span> alguma das situações acima assinaladas, por favor seleccione: Contactar os gestores forLEARN pessoalmente.
                        </p>
                            <br>
                        <p>
                            No caso de <span class="text-success"><b>NÃO HAVER</b></span> nenhuma situação acima, por favor seleccione: Tenho a certeza.
                        </p>
                   </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Contactar gestores forLEARN</button>
                    <button type="button" class="btn btn-danger" id="btn-callSubmit">Tenho a certeza</button>
                </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
    @parent

    <script>
        document.getElementById('make-print').addEventListener("click", function(){
            var restorepage = document.body.innerHTML;
            var printcontent = document.getElementById('mainPauta').innerHTML;
            document.body.innerHTML = printcontent;
            window.print();
            document.body.innerHTML = restorepage;
        })

        window.addEventListener('afterprint', (event) => {
            document.location.reload(true);
        });

        $(document).ready(function (){
            var selectStudyPlan = $("#course_id");
            var selectDiscipline = $("#discipline_id");
            var selectClass = $("#classes");
            var selectAvaliation = $("#avaliacao_id");
            $("#message").hide();

            $("#btn-callSubmit").click(function(){
                $("#submit").click();
            })

            getAllStudyPlanEdition();

            function getAllStudyPlanEdition(){
                $.ajax({
                    url: "/avaliations/study_plan_edition_closed/",
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
                    //}
                });
            }

            //Buscar Disciplinas apartir do curso associados ao Plano estudo Avaliacao
           $('#course_id').change(function(){
               var course_id = $(this).children("option:selected").val();
               console.log(course_id);
            //    $("#class_id").empty();
               $("#avaliacao_id").empty();
               $("#metrica_id").empty();
               $("#students tr").empty();

               $('#avaliacao_id').prop('disabled', true);
               $('#class_id').prop('disabled', true);
               $('#metrica_id').prop('disabled', true);

                if(course_id == ""){
                    console.log("Empty");
                    $('#discipline_id').prop('disabled', true);
                    $("#discipline_id").empty();
                    $('#avaliacao_id').prop('disabled', true);
                    $("#class_id").prop('disabled', true);
                    $("#avaliacao_id").empty();
                    $('#metrica_id').prop('disabled', true);
                    // $('#class_id').empty("");
                    $("#metrica_id").empty();
                    $("#students tr").empty();

                }else{

                $.ajax({

                url: "/avaliations/disciplines_ajax/" + course_id,
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

                    console.log(resultData);

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

        //$('#discipline_id').prop('disabled', true);

        //Buscar Avaliações apartir do curso e disciplina associados ao Plano estudo Avaliacao
           $('#discipline_id').change(function(){
               var discipline_id = $(this).children("option:selected").val();
               console.log(discipline_id);

                $("#students tr").empty();
                $('#metrica_id').prop('disabled', true);
                $("#metrica_id").empty();
                 $("#avaliacao_id").empty();
                //  $('#class_id').val("");
                 $('#class_id').prop('disabled', true);

            if(discipline_id == ""){
                console.log("Empty");
                $('#avaliacao_id').prop('disabled', true);
                $("#class_id").prop('disabled', true);
                $("#avaliacao_id").empty();
                $('#metrica_id').prop('disabled', true);
                // $('#class_id').val("");
                $("#metrica_id").empty();
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
                 $("#avaliacao_id").empty();
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

        $("#classes").change(function(){
            var classes = $(this).children("option:selected").val();
              $("#head").empty();
              $("#body").empty();
              $("#avaliacao_id").val("");
                if(classes == "")
                {
                    $("#students tr").empty();
                    $('#avaliacao_id').prop('disabled', true);
                    $("#avaliacao_id").val("");
                }else{
                    $('#avaliacao_id').prop('disabled', false);
                }
        });

        //Buscar Metricas apartir do curso da disciplina e da avaliacao associados ao Plano estudo Avaliacao
           $('#avaliacao_id').change(function(){
               var avaliacao_id = $(this).children("option:selected").val();
               var discipline_id = $("#discipline_id").val();
               var stdplanedition = $("#course_id").val();
               var classes = $("#classes").val();

               $("#head").empty();
               $("#body").empty();

            if(avaliacao_id == ""){
                $("#print-button").prop('hidden', true);
                $("#students tr").empty();

            }else{

            $.ajax({

            url: "/avaliations/show_grades_ajax/" + avaliacao_id + "/" + discipline_id + "/" + stdplanedition + "/" + classes,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',

            success: function (dataResult) {

                // var element = document.getElementById("generate-pdf");
                // element.href = "/avaliations/show_partial_grades_ajax_pdf/" + avaliacao_id + "/" + discipline_id + "/" + stdplanedition + "/" + classes;

                //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                var resultMetricas = dataResult.metricas;
                var resultStudents = dataResult.students;
                var resultGrades = dataResult.grades;
                var resultAvaliacao = dataResult.avaliacao;
                var disciplineHasMandatoryExam = dataResult.disciplineHasMandatoryExam;
                var head = '';
                var body = '';
                var i = 1;

                printCenas(avaliacao_id, discipline_id,stdplanedition,classes)

                /*$.each(resultData, function (index, row) {
                    bodyData += "<option value=" + row.mtrc_id + ">" + row.mtrc_nome + "</option>";
                })*/
                head += "<th> # </th>"
                head += "<th> Estudantes </th>"

                var a; for (a = 0; a < resultMetricas.length; a++)
                        {
                            //Para Classificacao Final nao exibir a metrica
                            if (resultMetricas[a].metrica_id != 56) {
                                head += "<th>"+ resultMetricas[a].nome + "</th>"
                            }
                        }

                 //head += "<th> Nota Final</th>"
                 var z; for(z = 0; z < resultAvaliacao.length; z++)
                        {
                            head += "<th>"+ resultAvaliacao[z].nome +"</th>"
                        }

                        head += "<th>Observações</th>"

                 var a; for (a = 0; a < resultStudents.length; a++)
                 {
                    body += "<tr>"
                    body += "<td>" + i++ + "</td><td>"+resultStudents[a].user_name+"<input type='hidden' value="+resultStudents[a].user_id+" name='user_id[]'></td>";

                     var id_user = resultStudents[a].user_id;
                     var nota_final = 0;
                     var flag = true;

                     var b; for (b = 0; b < resultMetricas.length; b++)
                        {
                           var metrica_id = resultMetricas[b].metrica_id;
                           flag = true;

                           var c; for (c = 0; c < resultGrades.length; c++)
                                {
                                    if (resultGrades[c].users_id == id_user && resultGrades[c].metricas_id == metrica_id)
                                    {

                                      flag = false;

                                      //se o id da metrica for o classificacao final nao exibe.
                                      if (resultGrades[c].metricas_id != 56) {

                                      //SE A NOTA FOR NULL ESCREVER FALTOU NA TABELA
                                        if(resultGrades[c].nota == null)
                                        {
                                            body += "<td>F</td>";
                                        }else{
                                            body += "<td>"+ parseFloat(resultGrades[c].nota).toFixed(2) +"</td>";
                                        }
                                     }
                                          nota_final += (resultGrades[c].nota * resultMetricas[b].percentagem) / 100 ;

                                          //nota_final = nota_final;

                                    }
                                }

                                //caso a metrica for diferente de classificacao final ele trata
                                if (metrica_id != 56) {
                                    if(flag)
                                    {
                                        body += "<td> - </td>";
                                    }
                                }
                        }
                    nota_final = Math.round(nota_final);
                    body += "<td>"+ nota_final +"<input type='hidden' value="+nota_final+" name='nota_final[]'></td>"

                    if (disciplineHasMandatoryExam.exam == 1) {
                            var x; for(x = 0; x < resultAvaliacao.length; x++)
                                {
                                    if (resultAvaliacao[x].id == 21 && nota_final >= 6.5) {
                                        body += "<td> Exame </td>"
                                    }else if(resultAvaliacao[x].id == 21 && nota_final <= 6) {
                                        body += "<td> Recurso </td>"
                                    }else if(resultAvaliacao[x].id == 23 && nota_final <= 9) {
                                        body += "<td> Recurso </td>"
                                    }else if(resultAvaliacao[x].id == 23 && nota_final >= 10) {
                                        body += "<td> Aprovado </td>"
                                    }else if(resultAvaliacao[x].id == 24 && nota_final >= 10){
                                        body += "<td> Aprovado </td>"
                                    }else if(resultAvaliacao[x].id == 24 && nota_final < 10){
                                        body += "<td> Reprovado </td>"
                                    }else if(resultAvaliacao[x].id == 22 && nota_final < 10){
                                        body += "<td> Reprovado </td>"
                                    }else if(resultAvaliacao[x].id == 22 && nota_final > 9){
                                        body += "<td> Aprovado </td>"
                                    }
                                }
                        }else{
                            var x; for(x = 0; x < resultAvaliacao.length; x++)
                                {
                                    if (resultAvaliacao[x].id == 21 && nota_final >= 6.5 && nota_final <= 13) {
                                        body += "<td> Exame </td>"
                                    }else if(resultAvaliacao[x].id == 21 && nota_final >= 13.5 && nota_final <= 20) {
                                        body += "<td> Aprovado </td>"
                                    }else if(resultAvaliacao[x].id == 21 && nota_final >= 0 && nota_final <= 6) {
                                        body += "<td> Recurso </td>"
                                    }else if(resultAvaliacao[x].id == 23 && nota_final >= 10) {
                                        body += "<td> Aprovado </td>"
                                    }else if(resultAvaliacao[x].id == 23 && nota_final < 10) {
                                        body += "<td> Recurso </td>"
                                    }else if(resultAvaliacao[x].id == 24 && nota_final >= 10){
                                        body += "<td> Aprovado </td>"
                                    }else if(resultAvaliacao[x].id == 24 && nota_final < 10)
                                    {
                                        body += "<td> Reprovado </td>"
                                    }else if(resultAvaliacao[x].id == 22 && nota_final < 10){
                                        body += "<td> Reprovado </td>"
                                    }else if(resultAvaliacao[x].id == 22 && nota_final > 9){
                                        body += "<td> Aprovado </td>"
                                    }
                                }
                        }
                    body += "</tr>"
                 }

            $("#head").append(head);
            $("#body").append(body);

            $("#tableHeader").attr('hidden', false);
            $("#tableFooter").attr('hidden', false);

            //Desabilitar o botado se ainda tiver nota por ser lancada
            //verificar se existe " - " nos elementos da tabela e desabilitar
            //botao

            ///VERIFICAR
            /*var td = $("td:contains('-')");
            console.log(td.length - 3);
            if(td.length - 3 > 0)
            {
                $('#submit').prop('disabled', true);
            }else{
                $('#submit').prop('disabled', false);
            }*/
            $('#submit').prop('disabled', false);

            //END VERIFICAR
            },
            error: function (dataResult) {
               // alert('error' + result);
            }
        });
    }


});

    //Listar estudante que tem a determinada disciplina.
        $("#metrica_id").change(function(){
            if( $("#metrica_id").val() == "")
            {
                $("#students tr").empty();

            } else {
            var discipline_id = $('#discipline_id').val();
            var metrica_id = $('#metrica_id').val();
            var course_id = $('#course_id').val();

            $.ajax({

            url: "/avaliations/student_ajax/" + discipline_id + "/" + metrica_id + "/" + course_id,
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
                    //Verifica se o Array das notas está vazio
                    if (resultGrades == '') {
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='number' name='notas[]' min='0' max='20' class='form-control notas'><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+"></td>";
                        bodyData += '</tr>'
                    }else{
                        bodyData += '<tr>'
                        bodyData += "<td>"+ i++ +"</td><td>"+ resultStudents[a].user_name + "</td><td width='100'><input type='number' name='notas[]' min='0' max='20' class='form-control notas' value="+resultGrades[a].aanota +"><input type='hidden' name='estudantes[]' class='form-control' value="+resultStudents[a].user_id+"></td>";
                        bodyData += '</tr>'
                     }

                }
                /*$.each(resultGrades , function (index, row) {
                    bodyData += '<tr>'
                    bodyData += "<td>"+ i++ +"</td><td>"+ row.user_name + "</td><input type='hidden' name='estudantes[]' class='form-control' value="+row.user_id+"></td>";
                    bodyData += '</tr>'
                })*/
                $("#students").append(bodyData);

               // $('#metrica_id').prop('disabled', false);
            },
            error: function (dataResult) {
               // alert('error' + result);
            }

        });

        }

        });


        function printCenas(avaliacao_id, discipline_id,stdplanedition,classes)
        {
             $.ajax({

                url: "/avaliations/show_partial_grades_ajax_pdf/" + avaliacao_id + "/" + discipline_id + "/" + stdplanedition + "/" + classes,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',


                success: function (dataResult) {
                    console.log(dataResult);
                    var avaliation = dataResult.avaliacao;
                    // var class = dataResult.class;
                    var discipline = dataResult.discipline;
                    $("#avaliationName").text("");
                    $("#avaliationName").text(avaliation[0].nome);

                    $("#pautaName").text("");
                    $("#pautaName").text(discipline.current_translation.display_name);

                    $("#className").text("");
                    $("#className").text($("#classes option:selected").text());

                    $("#disciplineName").text("");
                    $("#disciplineName").text( discipline.code +" - " +discipline.current_translation.display_name);

                    $("#courseName").text("");
                    $("#courseName").text( discipline.course.current_translation.display_name);

                    $("#periodName").text("");
                    $("#periodName").text( discipline.study_plans_has_disciplines[0].discipline_period.current_translation.display_name);

                    $("#print-button").prop('hidden', false);
                },
                error: function (dataResult) {
                    console.log(dataResult);

                }

        });

        }
        });
    </script>
@endsection
