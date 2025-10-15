@section('title',__('Visualizar Notas'))

{{-- <style>
    table, th, td {
       border: 1px solid black;
       font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
       font-size: 12pt;
   }
</style> --}}
{{-- <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'> --}}

@extends('layouts.backoffice')
@section('content')
   <div class="content-panel">
        <div class="content-header">
           <div class="container-fluid">
               <div class="row mb-2">
                   <div class="col-sm-5">
                       <h1 class="m-0 text-dark">
                       @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn','teacher']))
                           Visualizar notas do estudante 
                       @else
                           Minhas notas
                       @endif
                       
                       </h1>
                   </div>
                   <div class="col-sm-6">
                    {!! Form::open(['route' => ['store_final_grade']]) !!}
                                <div class="form-group col-sm-10 mt-4" style="margin-left: 8rem">
                                    <label for="courses">Selecionar ano lectivo</label>
                                    <select name="anoLectivo" id="anoLectivo" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                                        @foreach ($lectiveYears as $lectiveYear)
                                            @if ($lectiveYearSelected == $lectiveYear->id)
                                                <option style="width: 100%;" value="{{ $lectiveYear->id }}" selected>
                                                    {{ $lectiveYear->currentTranslation->display_name }}
                                                </option>
        
                                            @else
                                                <option style="width: 100%;" value="{{ $lectiveYear->id }}">
                                                    {{ $lectiveYear->currentTranslation->display_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                                
                                    </select>
                                    {{-- {{ Form::bsLiveSelectEmpty('ano_lectivo', [],null, ['id' => 'ano_lectivo', 'class' => 'form-control','disabled']) }} --}}
                                </div>
                            {{-- @elseif(auth()->user()->hasAnyRole(['student'])) --}}
                            {{-- <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>Ano lectivo</label>
                                        {{ Form::bsLiveSelectEmpty('students', ['2020 - 2021'],null, ['id' => 'students', 'class' => 'form-control','disabled']) }}
                                    </div>
                                </div>
                            </div> --}}
                   </div>
               </div>
           </div>
        </div>


        {{-- INCLUI O MENU DE BOTÕES --}}
        @include('Avaliations::avaliacao.show-panel-avaliation-button') 



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
                                   </div>
                               </div>

                           @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn','teacher']))
                               <div class="row">
                                   <div class="col-6">
                                       <div class="form-group col">
                                           <label for="courses">Curso</label>
                                           {{ Form::bsLiveSelect('courses', $courses, null, ['id' => 'courses']) }}

                                       </div>
                                   </div>
                               {{-- </div>
                               <div class="row"> --}}
                                   <div class="col-6">
                                       <div class="form-group col">
                                           <label>Estudante</label>
                                           {{ Form::bsLiveSelectEmpty('students', [],null, ['id' => 'students', 'class' => 'form-control','disabled']) }}
                                           <span style="margin: 5px;color: red" id="alerta"></span>
                                        </div>
                                   </div>
                               </div>
                               
                                @endif
                                </div>
                         
                           <div class="card">
                               <hr>
                            
                                <span class="text-center" id="processar"></span>
                       
                               <div class="row">
                                   
                                   <div class="col" id="container">
                                       @if(auth()->user()->hasAnyRole(['student']))
                                           <table class="table table-bordered">
                                               <thead style="background-color: #F5F3F3; !important">
                                                   <th style="padding: 4px; !important">Disciplinas</th>
                                                   @foreach ($avaliacaos as $avaliacao)
                                                       @foreach ($metricas as $metrica)
                                                           @if ($avaliacao->avaliacaos_id == $metrica->avaliacao_id)
                                                               @if ($metrica->nome != "Exame")
                                                                   <th style="padding: 4px; !important">
                                                                       {{ $metrica->nome }}
                                                                   </th>
                                                               @endif
                                                           @endif
                                                       @endforeach
                                                       <th style="padding: 4px; !important">
                                                           {{$avaliacao->nome}}
                                                       </th>
                                                   @endforeach
                                                   <th  style="padding: 4px; !important">Classificação Final (CF)</th>
                                                   <th style="padding: 4px; !important">Observações</th>
                                               </thead>
                                               <tbody>
                                                  @foreach ($disciplines as $discipline)
                                                  {{-- caso a disciplina NAO tiver exame obrigatorio --}}
                                                       @if($discipline->has_mandatory_exam != 0)
                                                           <tr>
                                                               <td style="padding: 5px; font-weight: bold; !important">{{ $discipline->display_name}}</td>
                                                               @php
                                                                   $flag = true;
                                                                   $discipline_id =  $discipline->discipline_id;
                                                               @endphp
                                                               @foreach ($metricas as $metrica)
                                                                       @php
                                                                           $flag = true;
                                                                           $metrica_id = $metrica->metrica_id;
                                                                       @endphp
                                                                   @foreach ($grades as $grade)
                                                                       @if ($grade->metricas_id == $metrica_id
                                                                           && $grade->disciplines_id == $discipline_id)
                                                                       @if ($grade->metricas_id != 55)
                                                                       @php $flag = false; @endphp
                                                                               <td style="padding: 5px; !important">
                                                                                   {{ $grade->nota ??  "F" }}
                                                                               </td>
                                                                           @endif
                                                                       @endif
                                                                   @endforeach
                                                                   @if ($metrica_id != 55)
                                                                       @if ($flag)
                                                                           <td style="padding: 5px; !important"> - </td>
                                                                       @endif
                                                                   @endif
                                                               @endforeach

                                                               @foreach ($finalGrades  as $finalGrade)
                                                                   @foreach ($avaliacaos as $avaliacao)
                                                                           @php $avaliacao_id = $avaliacao->avaliacaos_id @endphp
                                                                           @if ($finalGrade->avaliacaos_id == $avaliacao_id
                                                                               && $finalGrade->disciplines_id == $discipline_id)
                                                                               <td style="padding: 5px; !important">
                                                                                   {{round($finalGrade->nota_final)}}
                                                                               </td>

                                                                               @if ($avaliacao_id == 21 && $finalGrade->nota_final >= 0
                                                                                    && $finalGrade->nota_final <= 6)
                                                                                       <td style="padding: 5px; !important"> - </td>
                                                                                       <td style="padding: 5px; !important"> {{round($finalGrade->nota_final)}} </td>
                                                                                       <td style="padding: 5px; !important"> Recurso </td>
                                                                               @elseif($avaliacao_id == 21 && $finalGrade->nota_final >= 14
                                                                                    && $finalGrade->nota_final <= 20)
                                                                                       <td style="padding: 5px; !important"> - </td>
                                                                                       <td style="padding: 5px; !important"> {{round($finalGrade->nota_final)}} </td>
                                                                                       <td style="padding: 5px; !important"> Aprovado (a) </td>
                                                                               @elseif($avaliacao_id == 23)
                                                                                       @foreach ($gradesWithPercentage as $result)
                                                                                           @if ($result->discipline_id == $discipline_id)
                                                                                               <td>{{round($result->grade) ?? ''}}</td>

                                                                                               @if ($result->grade >= 10)
                                                                                                   <td style="padding: 5px; !important">Aprovado (a)</td>
                                                                                               @else
                                                                                                   <td style="padding: 5px; !important">Recurso</td>
                                                                                               @endif

                                                                                           @endif
                                                                                       @endforeach
                                                                               @endif
                                                                           @endif
                                                                       @endforeach
                                                               @endforeach
                                                           </tr>

                                                       @else
                                                           {{-- caso for exame obrigatorio --}}
                                                           <tr>
                                                           <td style="padding: 5px; !important ">{{ $discipline->display_name}}</td>
                                                           @php
                                                               $flag = true;
                                                               $discipline_id =  $discipline->discipline_id;
                                                           @endphp
                                                            @foreach ($metricas as $metrica)
                                                                   @php
                                                                       $flag = true;
                                                                       $metrica_id = $metrica->metrica_id;
                                                                   @endphp
                                                               @foreach ($grades as $grade)
                                                                   @if ($grade->metricas_id == $metrica_id
                                                                       && $grade->disciplines_id == $discipline_id)
                                                                   @if ($grade->metricas_id != 55)
                                                                   @php $flag = false; @endphp
                                                                           <td style="padding: 5px; !important">
                                                                               {{ $grade->nota ??  "F" }}
                                                                           </td>
                                                                       @endif
                                                                   @endif
                                                               @endforeach
                                                               @if ($metrica_id != 55)
                                                                   @if ($flag)
                                                                       <td style="padding: 5px; !important"> - </td>
                                                                   @endif
                                                               @endif
                                                            @endforeach

                                                            @foreach ($finalGrades  as $finalGrade)
                                                               @foreach ($avaliacaos as $avaliacao)
                                                                       @php $avaliacao_id = $avaliacao->avaliacaos_id @endphp
                                                                       @if ($finalGrade->avaliacaos_id == $avaliacao_id
                                                                           && $finalGrade->disciplines_id == $discipline_id)
                                                                           <td style="padding: 5px; !important">
                                                                               {{round($finalGrade->nota_final)}}
                                                                           </td>
                                                                           @if ($avaliacao_id == 21 && $finalGrade->nota_final < 6.5)
                                                                               <td style="padding: 5px; !important"> - </td>
                                                                           @endif
                                                                           {{-- Aqui vem um if poque so pode aparecer se tiver o EXAME  --}}
                                                                                   @if ($avaliacao->avaliacaos_id != 21)

                                                                                       @foreach ($gradesWithPercentage as $percentage)
                                                                                           @if ($finalGrade->disciplines_id == $percentage->discipline_id)
                                                                                               @if ($percentage->grade != null)
                                                                                                   <td style="padding: 5px; !important">{{ round($percentage->grade)}}</td>
                                                                                               @endif
                                                                                               @if ($percentage->grade >= 10)
                                                                                                   <td style="padding: 5px; !important">Aprovado (a) </td>
                                                                                               @else
                                                                                                   <td style="padding: 5px; !important">Recurso</td>
                                                                                               @endif

                                                                                           @endif
                                                                                       @endforeach

                                                                                   @endif
                                                                       @endif
                                                                   @endforeach
                                                            @endforeach
                                                       </tr>
                                                       @endif
                                                  @endforeach
                                               </tbody>
                                           </table>

                                       @endif
                                   </div>
                               </div><br>
                               @if(auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn','teacher']))
                               {{-- imprimir dados  --}}
                               <div class="row">
                                   <div class="col-6" id="pdf-button" hidden>
                                       <a class="btn btn-primary" id="generate-pdf" target="_blank">
                                               <i class="fas fa-print"></i>
                                               IMPRIMIR
                                           </a>
                                   </div>
                               </div>
                               @elseif(auth()->user()->hasAnyRole(['student']))
                               <div class="row">
                                   <div class="col-6">
                                       <a href="{{ route('student.generatePDF', auth()->user()->id)}}" class="btn btn-primary" target="_blank">
                                               <i class="fas fa-print"></i>
                                               IMPRIMIR
                                           </a>
                                   </div>
                               </div>
                               @endif
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
       var selectStudent = $("#students");
       var selescDiscipline = $("#disciplines");
       var selescAno_lectivo= $("#anoLectivo");
       callAluno($("#courses").val(), $("#anoLectivo").val());
       
        $("#anoLectivo").bind("click change",function() {
            var id_anolectivo=$(this).children("option:selected").val();
            var course_id =$("#courses").children("option:selected").val();
            var student_id = $("#students").children("option:selected").val();
           
            if(course_id!=null) {
                if(course_id!=null && student_id==null || student_id=="") {   
                    callAluno(course_id,id_anolectivo)
                 } 
                
                else if(student_id!=null && course_id!=null) {   
                    callDataAluno(student_id,course_id,id_anolectivo)
                } 
            }
        });

        function callAluno(course_id,id_anolectivo) {
               $('#container').hide();
               $("#alerta").text(null) 
                   $.ajax({
                   url: "/avaliations/getStudentByCourse/"+ course_id+"/"+id_anolectivo,
                   type: "GET",
                   data: {_token: '{{ csrf_token() }}'},
                   cache: false,
                   dataType: 'json',
               }).done(function (dataResult){
                   if (dataResult.length>0) {
                    $("#processar").text(null)

                       selectStudent.prop('disabled', true);
                       selectStudent.empty();
                       selectStudent.append('<option selected="" value=""></option>');
                       dataResult.forEach(function (student) {
                           selectStudent.append('<option value="' + student.id + '">' + student.name + " #" + student.mecanografico + "(  " + student.email + ")" + '</option>');
                       });
                       selectStudent.prop('disabled', false);
                       selectStudent.selectpicker('refresh');
                   }else{
                        console.log(dataResult)
                        selectStudent.prop('disabled', true);
                        selectStudent.empty();
                        selectStudent.selectpicker('refresh');
                    }
               });
        }
        function callDataAluno(student_id,course_id,id_anolectivo) {
            $('#container').hide();
            $("#pdf-button").prop('hidden', true);
            if (student_id!="") {
                $("#processar").text("A processar ...")
                 $.ajax({
                   url: "/avaliations/getGradesByStudent/"+ student_id+"/"+ id_anolectivo+"/"+ course_id,
                   type: "GET",
                   data: {
                       _token: '{{ csrf_token() }}'
                   },
                   cache: false,
                   dataType: 'json',
                   }).done(
                       function(data)  
                       {
                        console.log(data)
                        // if (data==1) {
                        //     $("#processar").text("Sem dados") 
                        //    }else{
                        //         $("#processar").text(null)
                        //         $('#container').show();
                        //         $("#pdf-button").prop('hidden', false);
                        //         $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
                        //         // getDisciplines(student_id);
                        //     }
                          
                        
                       }
                   )
            }else{
                $("#alerta").text("Selecionar o aluno/a") 
                
            }
           
        }

        $("#courses").change(function(){
                var course_id = $(this).children("option:selected").val();
                var id_anolectivo =$("#anoLectivo").children("option:selected").val();
                // selectStudent.prop('disabled', true);
                // selectStudent.empty();
                $('#container').hide();
                 $("#pdf-button").prop('hidden', true);
                $.ajax({
                url: "/avaliations/getStudentByCourse/"+ course_id+"/"+id_anolectivo,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
                //$('#container').html(data.html);

                }).done(function (dataResult){
                    // console.log(dataResult)
                if (dataResult.length>0) {
                    selectStudent.prop('disabled', true);
                    selectStudent.empty();

                        selectStudent.append('<option selected="" value=""></option>');
                        dataResult.forEach(function (student) {
                            selectStudent
                                .append('<option value="' + student.id + '">' + student.name + " #" + student.mecanografico + "(  " + student.email + ")" + '</option>');
                        });

                        selectStudent.prop('disabled', false);
                        selectStudent.selectpicker('refresh');

                        // //switchRegimes(selectDiscipline[0]);
                    }else{
                        selectStudent.prop('disabled', true);
                        selectStudent.empty();
                        selectStudent.selectpicker('refresh');

                    }
                });
                })






          $("#students").change(function(){
              if ($("#students").val() == "") {
                  $("#pdf-button").prop('hidden', true);
              }
             
              $("#alerta").text(null) 
              var student_id = $(this).children("option:selected").val();
              var id_anolectivo=$("#anoLectivo").children("option:selected").val();
              var course_id = $("#courses").children("option:selected").val();
              console.log("id_aluno "+student_id)

              var element = document.getElementById("generate-pdf");
              element.href = "/avaliations/print-grades-student/"+ student_id;

               $('#container').hide();
               $("#pdf-button").prop('hidden', true);
               $("#processar").text("A processar ...")
               $.ajax({
                   url: "/avaliations/getGradesByStudent/"+ student_id+"/"+ id_anolectivo+"/"+ course_id,
                   type: "GET",
                   data: {
                       _token: '{{ csrf_token() }}'
                   },
                   cache: false,
                   dataType: 'json',
                   }).done(
                       function(data)  
                       {
                        console.log(data)
                        //    if (data==1) {
                        //     $("#processar").text("Sem dados") 
                        //    }else{
                        //     $("#processar").text(null)
                        //         $('#container').show();
                        //         $("#pdf-button").prop('hidden', false);
                        //         $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)   
                        //    }
                       }
                   )
          })
          
        //   function getDisciplines(student_id)
        //   {
        //        //var student_id = $(this).children("option:selected").val();
        //       $.ajax({
        //            //url: "/avaliations/getStudentByDiscipline/"+ discipline + "/" + student_id,
        //            url: "/avaliations/getDisciplinesByStudent/" + student_id,

        //            type: "GET",
        //            data: {
        //                _token: '{{ csrf_token() }}'
        //            },
        //            cache: false,
        //            dataType: 'json',
        //            //$('#container').html(data.html);

        //        }).done(function (dataResult){
        //            if (dataResult.length) {
        //                selescDiscipline.prop('disabled', true);
        //                selescDiscipline.empty();

        //                selescDiscipline.append('<option selected="" value=""></option>');
        //                dataResult.forEach(function (discipline) {
        //                    selescDiscipline
        //                        .append('<option value="' + discipline.discipline_id + '">' + discipline.display_name + '</option>');
        //                });

        //                selescDiscipline.prop('disabled', false);
        //                selescDiscipline.selectpicker('refresh');

        //                //switchRegimes(selectDiscipline[0]);
        //            }
        //        });
        //   }

        //   $("#disciplines").change(function(){
        //       var student_id = $("#students").val();
        //       var discipline_id = $(this).val();
        //       console.log(discipline_id);
        //       $.ajax({
        //            url: "/avaliations/getGradesByDiscipline/"+ student_id + "/"+ discipline_id,
        //            type: "GET",
        //            data: {
        //                _token: '{{ csrf_token() }}'
        //            },
        //            cache: false,
        //            dataType: 'json',
        //            }).done(
        //                function(data)
        //                {
        //                    $("#pdf-button").prop('hidden', false);
        //                    //getDisciplines(student_id);
        //                    $('#container').html(data.html); //chamar outra view dentro da mesma view (substituindo a tabela princiapl)
        //                }
        //            )
        //   })

       });
   </script>
@endsection