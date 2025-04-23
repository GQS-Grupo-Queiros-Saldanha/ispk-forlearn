@section('title',__('Atribuir notas'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Atribuir notas</h1>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right mt-4">
                        <a href="{{ route('old_student.index') }}" class="btn btn-secondary btn-sm mb-3">
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(['route' => ['old_student.store_past_student']]) !!}
                        @csrf
                        <div class="col-6">
                            <div class="form-group">
                                <label for="students">Estudante:</label>
                                {!! Form::bsLiveSelectEmpty('students',[],old('students') ?: null, ['id' => 'students', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <br>
                        <div class="col-12">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <th>Ano</th>
                                    <th>Código</th>
                                    <th>Disciplina</th>
                                    <th>Ano lectivo</th>
                                    <th>Nota</th>
                                    <th>Ano lectivo</th>
                                    <th>Nota</th>
                                </thead>
                                <tbody id="body">
                                </tbody>
                            </table>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-success">Inserir notas</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
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
     $.ajax({
        url: "{{ route('old_student.students_not_matriculed') }}"
    }).done(function(response){
        if(response.length){
            $("#students").append('<option selected="" value=""></option>');
            response.forEach(function (student) {
                $("#students").append('<option value="' +student.id +'">' + student.display_name +'</option>');
                $("#students").selectpicker('refresh');
            })
        }
    })
    $('#students').change(function(){
        var student_id = $(this).children("option:selected").val();
        $("#body").empty();
        $.ajax({
          url: "/avaliations/past_student_get_discipline/" + student_id,
          type: "GET",
          data: {
              _token: '{{ csrf_token() }}'
          },
          cache: false,
          dataType: 'json',
          success: function (dataResult) {
              console.log(dataResult);
              var body = '';
              $.each(dataResult, function (index, row) {
                    body += "<tr>"
                    body += "<td>#</td><td>" + row.code + "</td><td><input name='discipline_id[]' value="+ row.id +" hidden>" + row.display_name + "</td><td><select name='lective_year[]' class='form-control'><option>2012</option><option>2013</option><option>2014</option><option>2015</option><option>2016</option><option>2017</option><option>2018</option><option>2019</option></select></td><td><input type='number' min='0' max='20' step='0.01' class='form-control' name='negativa[]' value=''><td><select name='lective_year[]' class='form-control'><option>2012</option><option>2013</option><option>2014</option><option>2015</option><option>2016</option><option>2017</option><option>2018</option><option>2019</option></select></td><td><input type='number' min='0' max='20' step='0.01' class='form-control' name='positiva[]' value=''></td>"
                    body += "</tr>"
                });
                $("#body").append(body);
          }

        })
    });


    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection
