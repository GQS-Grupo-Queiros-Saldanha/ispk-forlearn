@section('title',__('Lançar NPT'))
@extends('layouts.backoffice')

@section('styles')
@parent


<style>
    table,
    th,
    td {
        padding: 1px!important;
        
    }

        .div-anolectivo{
            width:300px; 
            padding-top:16px; 
            padding-right:0px;
            margin-right: 15px;   
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
                    <div class=" float-right">  
                        <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                            <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">Avaliações</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lançar notas por transição</li>
                            
                        </ol>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Lançar notas por transição </h1>
                </div>
               
                <div class="col-sm-6">
                    <div class="float-right div-anolectivo">
                        <label>Estudantes</label>  
                        <br>
                        <select name="type" id="type" class="selectpicker form-control form-control-sm">
                            <option value="1">Todos</option>
                            <option value="2">Com notas</option>
                            <option value="3">Sem notas</option>                                           
                        </select> 
                                                   
                    </div>                         
                </div>
            </div>
        </div>
    </div>

    {{-- INCLUI O MENU DE BOTÕES --}}
    {{-- @include('Avaliations::avaliacao.show-panel-avaliation-button')  --}}

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">

                    <div class="card">
                        <div class="card-body">
                           <table id="students" class="table table-striped table-hover display">
                               <thead>
                                   <tr>
                                       <th></th>
                                       <th>Nº de matrícula</th>
                                       <th>Código</th>
                                       <th>Estudante</th>
                                       <th>Email</th>
                                       <th>Curso</th>
                                       <th>Ano curricular</th>
                                       <th>Ação</th>
                                   </tr>
                               </thead>
                           </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal confirm --}}
@include('layouts.backoffice.modal_confirm')


@endsection



@section('scripts')
@parent
<script>
    function showStudentsByType(ajaxValue){
        $("#students").empty();
        $("#students").dataTable().fnDestroy();
         oTable = $('#students').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": ajaxValue,
            buttons: [
                    'colvis',
                    'excel'
                ],
            "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'matriculation', name:'matriculation'},
                {data: 'code', name: 'code'},
                {data: 'student', name: 'student'},
                {data: 'email', name: 'email'},
                {data: 'course', name: 'course'},
                {data: 'year', name: 'year'},
                {data: 'actions', name:'actions', orderable: false, searchable: false}
            ],
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "Todos"]],
            language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
              
        });
    }
   var $ = jQuery.noConflict();
   $(document).ready(function() {
        oTable = $('#students').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('old_student.list') }}",
            "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'matriculation', name:'matriculation'},
                {data: 'code', name: 'code'},
                {data: 'student', name: 'student'},
                {data: 'email', name: 'email'},
                {data: 'course', name: 'course'},
                {data: 'year', name: 'year'},
                {data: 'actions', name:'actions', orderable: false, searchable: false}
            ],
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "Todos"]],
            buttons: [
                    'colvis',
                    'excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                } 
             
        });
 
   }); 

   $("#type").change(function() {
       let ajaxValue = "old_student_get_list";
       if ($("#type").val() == 1) {
           ajaxValue = "old_student_get_list";
       }else if($("#type").val() == 2){
            ajaxValue = "old_student_get_list_with_grades";
       }else if($("#type").val() == 3) {
           ajaxValue = "old_student_get_list_without_grades";
       }   
        showStudentsByType(ajaxValue);
   });
    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection



