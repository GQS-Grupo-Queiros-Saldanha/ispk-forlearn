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
                             <div class="col-3">
                                 <div class="form-group col">
                                     <label for="email">E-mail</label>
                                     <span style="font-size: 9.5pt">{{$user->email}}</span>
                                 </div>
                             </div>

                             <div class="col-3">
                                 <div class="form-group col">
                                     <label for="class">Turma</label>
                                     @foreach ($user->classes as $classes)
                                     @if ($loop->last)
                                     <span style="font-size: 9.5pt">{{$classes->display_name}}</span>
                                     @endif
                                     @endforeach
                                 </div>
                             </div>

                             <div class="col-6">
                                 <div class="float-right">
                                     <!-- <a href="#" class="btn btn-info">Gerar PDF</a> -->
                                 </div>
                             </div>
                         </div>
                     </div>
                     <br>
                     <div class="card">
                         <div class="row">
                             <div class="col-12">
                                 <table class="table table-striped table-hover">
                                     <thead>
                                         <th>#</th>
                                         <th>Disciplina</th>
                                         <th>Aulas</th>
                                         <th>Presenças</th>
                                         <th>Faltas</th>
                                     </thead>

                                     <tbody id="presence">

                                     </tbody>
                                 </table>
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
             let totalLessons = {!!$totalLessons!!};
             let totalPresences = {!!$totalPresences!!};

             $(function () {
                 let body = '';
                 let i = 1;
                 flag = true;
                 if (totalLessons.length < 1) {
                     body += "<tr>"
                     body += "<td colspan='5' class='text-center'>Sem dados</td>"
                     body += "</tr>"
                     $("#presence").append(body);
                 } else {
                     for (let a = 0; a < totalLessons.length; a++) {
                         body += "<tr>"
                         let discipline_id = totalLessons[a].discipline_id;
                         flag = true;
                         body += "<td>" + i++ + "</td><td>" + totalLessons[a].name + "</td><td>" + totalLessons[
                             a].total + "</td>"
                         for (let b = 0; b < totalPresences.length; b++) {
                             if (discipline_id == totalPresences[b].discipline_id) {
                                 flag = false;
                                 body += "<td>" + totalPresences[b].total + "</td>"
                                 body += "<td>" + (totalLessons[b].total - totalPresences[b].total) + "</td>"
                             }
                         }
                         if (flag) {
                             body += "<td> 0 </td>";
                             body += "<td>" + (totalLessons[a].total - 0) + "</td>"
                         }
                         body += "</tr>"
                     }
                 }
                 $("#presence").append(body);
             });

         </script>
         @endsection
