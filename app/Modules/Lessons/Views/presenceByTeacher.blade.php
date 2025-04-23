 @section('title',__('Faltas'))


 @extends('layouts.backoffice')

 @section('content')
 <div class="content-panel" style="padding: 0px;">
    @include('Lessons::navbar.navbar')
     <div class="content-header">
         <div class="container-fluid">
             <div class="row mb-2">
                 <div class="col-sm-6">
                     <h1 class="m-0 text-dark">
                         Faltas
                     </h1>
                 </div>
                 <div class="col-sm-6">
                    <div class=" float-right">
                        <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="{{ route('lessons.index') }}">Aulas</a></li> --}}
                            <li class="breadcrumb-item active" aria-current="page">Faltas</li>
                        </ol>
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
                                     <label for="discipline">Disciplina: </label>
                                     {{ Form::bsLiveSelectEmpty('disciplines', [], null,['id' => 'disciplines', 'class' => 'form-control'])}}
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="card">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group col">
                                     <label for="classes">Turma: </label>
                                     {{ Form::bsLiveSelectEmpty('classes', [], null,['id' => 'classes', 'class' => 'form-control', 'disabled'])}}
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="card">
                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group col" id="aulas-container" hidden>
                                     Aulas
                                     <span id="aulas"></span>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="form-group col" id="maximum-container" hidden>
                                     Número máximo de faltas
                                     <span id="maximum"></span>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="card">
                         <div class="row">
                             <div class="col-12">
                                 <div class="form-group col" id="student-container" hidden>
                                     <table class="table table-hover table-striped">
                                         <thead>
                                             <th>#</th>
                                             <th>Estudante</th>
                                             <th>Faltas</th>
                                         </thead>
                                         <tbody id="students">

                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>


                 </div>
             </div>
         </div>
         @endsection

         @section('scripts')
         @parent
         <script>
             $(function () {

                let disciplines = {!! $disciplines !!};
                $("#disciplines").append('<option value=""></option>');
                disciplines.forEach(function (discipline) {
                    $("#disciplines").append('<option value="' +discipline.id +'">' + discipline.display_name +'</option>');
                    $("#disciplines").selectpicker('refresh');
                })

                 $('#disciplines').change(function () {
                     let discipline = $("#disciplines").children("option:selected").val();
                     
                     $("#student-container").prop('hidden', true)
                     $("#students").empty();
                     $("#maximum").empty();
                     $("#aulas").empty();
                     $("#aulas-container").prop('hidden', true)
                     $("#maximum-container").prop('hidden', true)
                     bodyData = '';
                     $.ajax({
                         url: "/classes/" + discipline
                     }).done(function(response){
                         if(response.length){
                             $("#classes").empty();
                             console.log(response);
                            $("#classes").append('<option value=""></option>');
                            response.forEach(function(classes) {
                                $("#classes").append('<option value="' +classes.id +'">' + classes.display_name +'</option>');
                                $("#classes").selectpicker('refresh');
                            });
                            $("#classes").prop('disabled', false);
                            $("#classes").selectpicker('refresh');
                         }
                     })
                 })

                 $('#classes').change(function () {
                     let classes = $("#classes").val();
                     let discipline = $("#disciplines").children("option:selected").val();
                     $("#student-container").prop('hidden', true)
                     $("#students").empty();
                     $("#maximum").empty();
                     $("#aulas").empty();
                     $("#aulas-container").prop('hidden', true)
                     $("#maximum-container").prop('hidden', true)
                     let maximum_ab = '';
                     let aulas_number = '';
                     $.ajax({
                         url: "/students/" + discipline + "/" + classes,
                         type: "GET",
                         data: {
                             _token: '{{ csrf_token() }}'
                         },
                         cache: false,
                         dataType: 'json',
                         success: function(data){
                             let discipline = data.discipline;
                             let student = data.students;
                             let totalLessons = data.totalLessons;
                             let totalAbsence = data.totalAbsence;
                             let i = 1;
                             flag = true;
                             let dataBody = '';
                             console.log(data);
                             maximum_ab += discipline.maximum_absence,
                             aulas_number += totalLessons.total;
                             if (student.length < 1) {
                                dataBody += "<tr>"
                                dataBody += "<td colspan='3' class='text-center'>Sem dados</td>"
                                dataBody += "</tr>"
                                $("#students").append(dataBody);
                             } else {
                                for (let a = 0; a < student.length; a++) {
                                    dataBody += "<tr>"
                                    let student_id = student[a].id;
                                    flag = true;
                                    dataBody += "<td>"+ i++ +"</td><td>"+ student[a].name +"</td>"
                                    for (let b = 0; b < totalAbsence.length; b++) {
                                        if(student_id == totalAbsence[b].student_id)
                                        {
                                            flag = false;
                                            dataBody += "<td>"+ (aulas_number - student[a].total) +"</td>"
                                        }
                                    }
                                    if(flag){
                                        dataBody += "<td>"+ (aulas_number - 0) +"</td>"
                                    }
                                    dataBody += "</tr>"   
                                }
                             }
                             
                                
                             $("#maximum").append(maximum_ab)
                             $("#aulas").append(aulas_number)
                             $("#students").append(dataBody)
                             $("#student-container").prop('hidden', false)
                             $("#aulas-container").prop('hidden', false)
                             $("#maximum-container").prop('hidden', false)
                         }
                     })
                 })
             })

         </script>
         @endsection
