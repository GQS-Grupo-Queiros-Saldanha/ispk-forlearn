@extends('layouts.generic_index_new')
@section('title', 'INSCRIÇÃO PARA CURSO PROFISSIONAL')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title')
INSCRIÇÃO PARA CURSO PROFISSIONAL
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('student-special-course.index') }}">Cursos profissionais</a>
    </li>
  
@endsection
@section('selects')
<div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div> 
    <div class="mb-2">
            <label for="student_type">Curso</label>
            <select name="course" id="course" class="selectpicker form-control form-control-sm" data-live-search="true">
                <option value="" selected>Nenhum selecionado</option>
                @foreach($courses as $course)
               <option value="{{$course->id}}">{{$course->display_name}}</option>
                @endforeach
            </select>
    </div>

    <div class="mb-2">
            <label for="edition">Ediçao</label>
            <select name="edition" id="edition" class="selectpicker form-control form-control-sm" data-live-search="true">
                <option value="" selected>Nenhum selecionado</option>
                
            </select>
    </div>
@endsection
@section('body')
<table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nº de inscrição</th>
                <th>@lang('Users::users.name')</th>
                <th>@lang('Users::users.email')</th>
                <th>Curso</th>
                <th>Edição</th>
                <th>Pagamento</th>
                <th>Certificado</th>                  
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>ACÇÕES POSSÍVEIS</th>
            </tr>
        </thead>
    </table>

     <!-- Modal de Confirmação -->
<div class="modal fade" id="modal_confirm" tabindex="-1" aria-labelledby="modal_confirm_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal_confirm_label">Confirmação</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza de que deseja eliminar a inscrição?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <a class="btn btn-danger" id="confirm_delete">Eliminar</a>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts-new')
<script>

   function destroy(element) { 
      
     var url = $(element).data('action'); 
     $('#confirm_delete').prop('href', url);

     $('#modal_confirm').modal('show');
    }

            let lective_year = $("#lective_year").val();
            loadDataUserCandidate()
            $("#lective_year").change(function() {
                lective_year = $("#lective_year").val();
           
                if (lective_year != "") {
                    loadDataUserCandidate();
                }
            })

            $("#course").change(function() {
              
                if (!(lective_year == "" || $("#course").val() == "")) {
                    loadDataUserCandidate($("#course").val());
                }
                getEditions($("#course").val());
            })

            $("#edition").change(function() {
              
              if (!($("#course").val() == "" || lective_year == "" || $("#edition").val() == "")) {
                  loadDataUserCandidate($("#course").val(), $("#edition").val());
              }
            
          })

            function getEditions(course){
              
                $.ajax({
        url: "student-special-course/get-classes/" + $("#course").val()+"/"+lective_year,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
        success: function(dataR) {
            var dataResult = dataR.classes;
            var data = '';
           if(dataResult.length > 0) {
            $.each(dataResult, function(index, item) { 
                data += "<option value='" + item.id + "'>" + item.number +"</option>";
            });
        }
            $('#edition').append(data);
           $('#edition').selectpicker('refresh');
         
          
        },
        error: function(xhr, status, error) { // Corrigido erro na estrutura
            console.log(xhr.responseText);
        }
    });
            }

            function loadDataUserCandidate(course = null,edition = null){
                console.log('oi')
                if ($.fn.DataTable.isDataTable('#users-table')) {
                $('#users-table').DataTable().destroy();
                }
                $('#users-table').DataTable({
                    ajax: "student-special-course/getStudentsBy/"+lective_year+"/"+course+"/"+edition,
                    buttons: ['colvis', 'excel', {
                            text: '<i class="fas fa-plus-square"></i> Criar nova inscrição',
                            className: 'btn-primary main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) {
                             
                                let url =  'student-special-course/create/';
                                window.open(url, "_blank");
                            }
                        }
                    ],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'cand_number',
                        name: 'cand_number',
                        searchable: true
                    }, {
                        data: 'name_name',
                        name: 'name_name',
                        searchable: true
                    }, {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    }, {
                        data: 'cursos',
                        name: 'cursos',
                        searchable: true
                    },
                    {
                        data: 'number',
                        name: 'number',
                        searchable: true
                    }
                    , {
                        data: 'states',
                        name: 'states',
                        searchable: true
                    }, {
                        data: 'diploma',
                        name: 'diploma',
                        searchable: false,
                        orderable: true,
                    }, {
                        data: 'us_created_by',
                        name: 'u1.name',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'us_updated_by',
                        name: 'us_updated_by',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false,
                        searchable: false
                    }, {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }],
                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

                    

</script>@endsection