 @section('title',__('Visualizar Plano de Estudos Avaliação'))


 @extends('layouts.backoffice')

 @section('content')
 <div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Exibir Sumário de Notas
                    </h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('home') }}" style="margin-left: 350px;">Home</a> 
                    / 
                    <a href="/avaliations/panel_avaliation">Avaliação</a> 
                    / 
                    <span style="opacity: 0.5">Sumário de Notas</span>
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

                    <div class="card">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Edição de Plano de Estudo</label>
                                    <select name="course_id" id="course_id" class="form-control" required>
                                        <option value=""></option>
                                        @foreach ($pea as $item)
                                        <option value="{{ $item->spea_id }}">
                                            {{ $item->spea_nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        {{-- </div>
                        <div class="row"> --}}
                            <div class="col-6">
                                <div class="form-group col">
                                    <label>Disciplina</label>
                                    <select name="discipline_id" id="discipline_id" class="form-control" disabled
                                        required>
                                        <option value=""> </option>

                                    </select>
                                </div>
                            </div>

                        </div>



                    </div>
                    <hr>

                    <div class="card">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead id="head">

                                    </thead>

                                    <tbody id="body">

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
             $(document).ready(function () {
                 //$('#discipline_id').prop('disabled', true);

                 //Buscar Disciplinas apartir do curso associados ao Plano estudo Avaliacao
                 $('#course_id').change(function () {
                     var course_id = $(this).children("option:selected").val();
                     console.log(course_id);
                     $("#discipline_id").empty();
                     $("#avaliacao_id").empty();
                     $("#head").empty();
                     $("#body").empty();

                     if (course_id == "") {
                         console.log("Empty");
                         $('#discipline_id').prop('disabled', true);
                         $("#discipline_id").empty();

                     } else {

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
                                 $("#avaliacao_id").empty();
                                 console.log(dataResult);
                                 var resultData = dataResult.data;
                                 var bodyData = '';
                                 var i = 1;
                                 console.log(dataResult.data);

                                 bodyData += "<option value=''></option>";
                                 $.each(resultData, function (index, row) {

                                     bodyData += "<option value=" + row
                                         .discipline_id + ">" + row
                                         .dt_display_name + "</option>";

                                 })
                                 $("#discipline_id").append(bodyData);

                                 $('#discipline_id').prop('disabled', false);
                             },
                             error: function (dataResult) {
                                 // alert('error' + result);
                             }
                         });
                     }
                 });


                 //Buscar Avaliações apartir do curso e disciplina associados ao Plano estudo Avaliacao
                 $('#discipline_id').change(function () {
                     var discipline_id = $(this).children("option:selected").val();
                     console.log(discipline_id);
                     $("#head").empty();
                     $("#body").empty();

                     $("#students tr").empty();
                     $("#avaliacao_id").empty();

                     if (discipline_id == "") {
                         console.log("Empty");
                         $('#avaliacao_id').prop('disabled', true);
                         $("#avaliacao_id").empty();
                         $("#students tr").empty();

                     } else {

                         $.ajax({

                             url: "/avaliations/show_summary_grades_ajax/" + course_id + "/" +
                                 discipline_id,
                             type: "GET",
                             data: {
                                 _token: '{{ csrf_token() }}'
                             },
                             cache: false,
                             dataType: 'json',

                             success: function (dataResult) {
                                 //Limpar a tabela sempre que for inicializada (Aberto o Modal)
                                 console.log(dataResult);
                                 var resultAvaliacaos = dataResult.avaliacaos;
                                 var resultStudents = dataResult.students;
                                 var resultFinalGrades = dataResult.finalGrades;
                                 var head = '';
                                 var body = '';
                                 var i = 1;
                                 console.log(resultFinalGrades);

                                 head += "<th> # </th>"
                                 head += "<th> Estudantes </th>"
                                 head += "<th> Período da Avaliação </th>"
                                 head += "<th> Nota </th>"
                                 head += "<th> Estado </th>"

                                 for (var a = 0; a < resultStudents.length; a++) {
                                     body += "<tr>"
                                     body += "<td>" + i++ + "</td>";
                                     body += "<td>" + resultStudents[a].user_name +
                                         "<input type='hidden' value=" + resultStudents[a]
                                         .user_id + " name='user_id[]'></td>";

                                     var id_user = resultStudents[a].user_id;
                                     var nota_final = 0.0;
                                     var flag = true;
                                     var nota_anterior = 0.0;
                                     var periodo_avaliacao = "";
                                     for (var c = 0; c < resultFinalGrades.length; c++) {
                                         if (resultFinalGrades[c].users_id == id_user) {
                                             nota_final = parseFloat(resultFinalGrades[c].nota_final);
                                             
                                             if (nota_anterior < nota_final) {
                                                 nota_anterior = nota_final;
                                                for (var index = 0; index < resultAvaliacaos.length; index++) {
                                                    
                                                    if (resultFinalGrades[c].avaliacaos_id == resultAvaliacaos[index].avaliacaos_id) {
                                                        
                                                        periodo_avaliacao = resultAvaliacaos[index].nome; 
                                                    }

                                                }
                                                 
                                             }
                                         }
                                     }
                                     body += "<td>" + periodo_avaliacao + "</td>";
                                     body += "<td>" + nota_anterior + "</td>";
                                     console.log(typeof(nota_anterior));
                                     
                                     body += "<td>"+(nota_anterior < 10.0 ? "N/Apto" : "Apto" )+"</td>";
                                     periodo_avaliacao = '';
                                     nota_anterior = 0;

                                     body += "</tr>"
                                 }


                                 $("#head").append(head);
                                 $("#body").append(body);
                             },
                             error: function (dataResult) {
                                 // alert('error' + result);
                             }
                         });
                     }


                 });

                 //Buscar Metricas apartir do curso da disciplina e da avaliacao associados ao Plano estudo Avaliacao
                 $('#avaliacao_id').change(function () {
                     var avaliacao_id = $(this).children("option:selected").val();
                     var discipline_id = $("#discipline_id").val();
                     var stdplanedition = $("#course_id").val();

                     $("#head").empty();
                     $("#body").empty();

                     if (avaliacao_id == "") {
                         console.log("Empty");
                         $("#students tr").empty();

                     } else {

                         $.ajax({

                             url: "/avaliations/show_final_grades_ajax/" + avaliacao_id + "/" +
                                 discipline_id + "/" + stdplanedition,
                             type: "GET",
                             data: {
                                 _token: '{{ csrf_token() }}'
                             },
                             cache: false,
                             dataType: 'json',

                             success: function (dataResult) {
                                 //Limpar a tabela sempre que for inicializada (Aberto o Modal)

                                 console.log(dataResult);
                                 var resultMetricas = dataResult.metricas;
                                 var resultStudents = dataResult.students;
                                 var resultGrades = dataResult.grades;
                                 var head = '';
                                 var body = '';
                                 var i = 1;
                                 console.log(resultGrades);
                                 /*$.each(resultData, function (index, row) {
                                     bodyData += "<option value=" + row.mtrc_id + ">" + row.mtrc_nome + "</option>";
                                 })*/
                                 head += "<th> # </th>"
                                 head += "<th> Estudantes </th>"

                                 var a;
                                 for (a = 0; a < resultMetricas.length; a++) {
                                     head += "<th>" + resultMetricas[a].nome + "</th>"
                                 }

                                 head += "<th> Nota Final</th>"

                                 var a;
                                 for (a = 0; a < resultStudents.length; a++) {
                                     body += "<tr>"
                                     body += "<td>" + i++ + "</td><td>" + resultStudents[a]
                                         .user_name + "<input type='hidden' value=" +
                                         resultStudents[a].user_id +
                                         " name='user_id[]'></td>";

                                     var id_user = resultStudents[a].user_id;
                                     var nota_final = 0;
                                     var flag = true;

                                     var b;
                                     for (b = 0; b < resultMetricas.length; b++) {
                                         var metrica_id = resultMetricas[b].metrica_id;
                                         flag = true;

                                         var c;
                                         for (c = 0; c < resultGrades.length; c++) {
                                             if (resultGrades[c].users_id == id_user &&
                                                 resultGrades[c].metricas_id == metrica_id
                                                 ) {

                                                 flag = false;
                                                 body += "<td>" + resultGrades[c].nota +
                                                     "</td>";

                                                 nota_final += (resultGrades[c].nota *
                                                         resultMetricas[b].percentagem) /
                                                     100;
                                             }
                                         }
                                         if (flag) {
                                             body += "<td> - </td>";
                                         }
                                     }

                                     body += "<td>" + nota_final +
                                         "<input type='hidden' value=" + nota_final +
                                         " name='nota_final[]'></td>"
                                     body += "</tr>"
                                 }

                                 $("#head").append(head);
                                 $("#body").append(body);

                                 //Desabilitar o botado se ainda tiver nota por ser lancada
                                 //verificar se existe " - " nos elementos da tabela e desabilitar
                                 //botao
                                 var td = $("td:contains('-')");
                                 console.log(td.length - 3);
                                 if (td.length - 3 > 0) {
                                     $('#submit').prop('disabled', true);
                                 } else {
                                     $('#submit').prop('disabled', false);
                                 }

                             },
                             error: function (dataResult) {
                                 // alert('error' + result);
                             }
                         });
                     }


                 });

                 //Listar estudante que tem a determinada disciplina.
                 $("#metrica_id").change(function () {
                     if ($("#metrica_id").val() == "") {
                         $("#students tr").empty();

                     } else {
                         var discipline_id = $('#discipline_id').val();
                         var metrica_id = $('#metrica_id').val();
                         var course_id = $('#course_id').val();

                         $.ajax({

                             url: "/avaliations/student_ajax/" + discipline_id + "/" +
                                 metrica_id + "/" + course_id,
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


                                 var a;
                                 for (a = 0; a < resultStudents.length; a++) {
                                     //Verifica se o Array das notas está vazio
                                     if (resultGrades == '') {
                                         bodyData += '<tr>'
                                         bodyData += "<td>" + i++ + "</td><td>" +
                                             resultStudents[a].user_name +
                                             "</td><td width='100'><input type='number' name='notas[]' min='0' max='20' class='form-control notas'><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                             resultStudents[a].user_id + "></td>";
                                         bodyData += '</tr>'
                                     } else {
                                         bodyData += '<tr>'
                                         bodyData += "<td>" + i++ + "</td><td>" +
                                             resultStudents[a].user_name +
                                             "</td><td width='100'><input type='number' name='notas[]' min='0' max='20' class='form-control notas' value=" +
                                             resultGrades[a].aanota +
                                             "><input type='hidden' name='estudantes[]' class='form-control' value=" +
                                             resultStudents[a].user_id + "></td>";
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
             });

         </script>
         @endsection
