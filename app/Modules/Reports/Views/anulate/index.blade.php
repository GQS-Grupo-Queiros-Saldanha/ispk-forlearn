 <title>Eliminar | forLEARN® by GQS</title>
 @extends('layouts.backoffice')
 @section('styles')
     @parent
 @endsection
 @section('content')
     <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
     <style>
         .list-group li button {
             border: none;
             background: none;
             outline-style: none;
             transition: all 0.5s;
         }

         .list-group li button:hover {
             cursor: pointer;
             font-size: 15px;
             transition: all 0.5s;
             font-weight: bold
         }

         .subLink {
             list-style: none;
             transition: all 0.5s;
             border-bottom: none;
         }

         .subLink:hover {
             cursor: pointer;
             font-size: 15px;
             transition: all 0.5s;
             border-bottom: #dfdfdf 1px solid;
         }
     </style>




     <div class="content-panel" style="padding:0">
         @include('GA::events.navbar.navbar')
         <div class="content-header">
             <div class="container-fluid">
                 <div class="row mb-1">
                     <div class="col-sm-6">
                         <h1> @lang('GA::events.events')</h1>
                     </div>
                     <div class="col-sm-6">
                         <div class=" float-right">
                             <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                 <li class="breadcrumb-item">
                                     <a href="{{ route('events.index') }}">Eliminar</a>
                                 </li>
                                 <li class="breadcrumb-item active" aria-current="page">
                                     Listar
                                 </li>
                             </ol>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

         <div class="content">
             <div class="container-fluid">
                 <div class="row">
                     <div class="col">

                         <div class="row">
                             <div class="col-6">
                                 <div class="form-group col">
                                     <label for="student">Estudantes</label>
                                     <select class="selectpicker form-control " name="student" id="student"
                                         data-actions-box="true" data-live-search="true">

                                     </select>
                                 </div>
                             </div>
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

     console.log("Mateus Massaqui Jungo");
     <script>
        
        
         let student = $("#student");
         function getUser() {

             $.ajax({
                 url: "/avaliations/matriculation_requerimento/" +7,
                 type: "GET",
                 data: {
                     _token: '{{ csrf_token() }}'
                 },
                 cache: false,
                 dataType: 'json',
             }).done(function(data) {

                 student.empty();
                 data["matriculation"].forEach(function(user) {
                     student.append('<option value="' + user.codigo + '">' + user.name + ' #' + user
                         .matricula + ' ( ' + user.email +
                         ' )</option>');
                 });
                 student.selectpicker('refresh');
             });
         }
         getUser(): 
        
     </script>
 @endsection
