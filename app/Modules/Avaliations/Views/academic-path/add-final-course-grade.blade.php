@section('title',__('Atribuir notas de trabalho de fim de curso'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-10">
                    <h1>Atribuir notas de trabalho de fim de curso</h1>
                    </h1>
                </div>

                    {{-- <div class="float-right mt-4">
                        <a href="{{ route('old_student.index') }}" class="btn btn-secondary btn-sm mb-3">
                            Voltar
                        </a>
                    </div> --}}
                    <div class="col-sm-2 pt-4 pull-right">
                    <form class="form-inline">
                        <div class="form-group mb-2">
                            <label for="staticEmail2" class="">Exibir: </label>
                            <select name="" id="type" class="form-control ml-2">
                                <option value="1">Todos</option>
                                <option value="2">Com notas</option>
                                <option value="3">Sem notas</option>
                            </select>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- INCLUI O MENU DE BOTÕES --}}
    @include('Avaliations::avaliacao.show-panel-avaliation-button')

    {{-- <hr> --}}
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {{--<form action="{{ route('old_student.storeFinalGrade')}}" method="POST">--}}
                        @csrf
                        <table id="students" class="table table-striped table-hover">
                            <thead>
                                <th></th>
                                <th>Código</th>
                                <th>Estudante</th>
                                <th>Email</th>
                                <th>Ação</th>
                            </thead>
                            {{--<tbody>

                                @php $i = 1; @endphp
                                @foreach ($students as $student)
                                    <tr>
                                        <td>{{$loop->iteration}} <input type="text" name="student_id[]" value="{{$student->id}}"></td>
                                        <td>{{$student->n_mecanografico}}</td>
                                        <td>{{$student->name}}</td>
                                        <td>{{$student->code}}</td>
                                        <td>{{$student->display_name}} <input type="text" name="discipline_id[]" value="{{$student->discipline_id}}"></td>
                                        <td><select class='form-control' name="lective_year[]"><option>2020</option></select></td>
                                        <td><input type='number' min='10' max='20' class='form-control' name="grade[]" value=""></td>

                                    </tr>
                                @endforeach
                            </tbody>--}}
                        </table>
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
            "columns": [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'n_mecanografico', name: 'n_mecanografico'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            //{data: 'display_name', name: 'display_name'},
            {data: 'grade', name: 'grade'},
            ],
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "Todos"]],

        });
    }

     var $ = jQuery.noConflict();
    $(document).ready(function() {
        oTable = $('#students').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('old_student.getFinalCourse')}}",
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'n_mecanografico', name: 'n_mecanografico'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            //{data: 'display_name', name: 'display_name'},
            {data: 'grade', name: 'grade'},
            ],
            "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "Todos"]],

        });

   });



        // var $ = jQuery.noConflict();
        // $('#students').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('old_student.getFinalCourse')}}",
        //     columns: [
        //     {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        //     {data: 'n_mecanografico', name: 'n_mecanografico'},
        //     {data: 'name', name: 'name'},
        //     {data: 'email', name: 'email'},
        //     //{data: 'display_name', name: 'display_name'},
        //     {data: 'grade', name: 'grade'},

        // ],
        // "order": [[1, "ASC"]]
        // });

    /* $.ajax({
        url: ""
    }).done(function(response){
        console.log(response);
        if(response.length){
            $("#students").append('<option selected="" value=""></option>');
            response.forEach(function (student) {
                $("#students").append('<option value="' + student.id +'">' + student.display_name +'</option>');
                $("#students").selectpicker('refresh');
            })
        }
    })
    $('#students').change(function(){
        var student_id = $(this).children("option:selected").val();
        console.log(student_id);
        $("#body").empty();
        $.ajax({
          url: "/avaliations/old_student_get_discipline/" + student_id,
          type: "GET",
          data: {
              _token: '{{ csrf_token() }}'
          },
          cache: false,
          dataType: 'json',
          success: function (dataResult) {
              console.log(student_id);
              console.log(dataResult);
              var body = '';
              $.each(dataResult, function (index, row) {
                    body += "<tr>"
                    body += "<td>" + row.code + "</td><td>" + row.display_name + "</td><td><select class='form-control'><option>2019</option></select></td><td><input type='number' min='10' max='20' class='form-control'></td>"
                    body += "</tr>"
                });
                $("#body").append(body);
          }

        })
    });
*/


     $("#type").change(function() {
       let ajaxValue = "old_student_final_get_list";
       if ($("#type").val() == 1) {
           ajaxValue = "old_student_final_get_list";
       }else if($("#type").val() == 2){
            ajaxValue = "old_student_final_get_list_with_grades";
       }else if($("#type").val() == 3) {
           ajaxValue = "old_student_final_get_list_without_grades";
       }
        showStudentsByType(ajaxValue);
    });


    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection
