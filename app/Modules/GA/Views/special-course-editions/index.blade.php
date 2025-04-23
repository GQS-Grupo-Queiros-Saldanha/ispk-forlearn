<title>Edições de Curso intensivo | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@php $name = 'Edições de Curso intensivo' .' - '. $course->display_name;@endphp
@section('page-title', $name)
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Edições de Curso intensivo</li>

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
@endsection

@section('body')

   

                                <table id="grau-academico-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Edição nº</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                    </thead>
                                </table>

    

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="form">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Adicionar nova edição</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              
                    <div class="mb-3">
                        <label for="inicio" class="form-label">Início</label>
                        <input type="date" class="form-control" id="inicio" name="inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fim" class="form-label">Fim</label>
                        <input type="date" class="form-control" id="fim" name="fim" required>
                    </div>

                    <div class="form-group col alert alert-success" role="alert" id="div_success">
                        <p id="success_message"></p>
                    </div>
                    <div class="form-group col alert alert-danger" role="alert" id="div_error">
                        <p id="error_message"></p>
                     </div>
               
               
            </div>
                                
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Fechar</button>
                <button type="submit" class="btn btn-primary" id="submit">Salvar</button>
            </div>
        </form>
        </div>
    </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="editForm">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Edição</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                
                <div class="mb-3">
                    <label for="editInicio" class="form-label">Início</label>
                    <input type="date" class="form-control" id="editInicio" name="inicio" required>
                </div>
                <div class="mb-3">
                    <label for="editFim" class="form-label">Fim</label>
                    <input type="date" class="form-control" id="editFim" name="fim" required>
                </div>

                <div class="form-group col alert alert-success" role="alert" id="edit_div_success">
                    <p id="edit_success_message"></p>
                </div>
                <div class="form-group col alert alert-danger" role="alert" id="edit_div_error">
                    <p id="edit_error_message"></p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="editClose">Fechar</button>
                <button type="submit" class="btn btn-primary" id="editSubmit">Salvar</button>
            </div>
        </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            let course_id = @json($course->id);
            let id;

            $('#close').on('click', function(){
                $('#inicio').val('');
                $('#fim').val('');
            })
            loadData()
            $('#lective_year').change(function () { loadData()})
            $('#div_error').hide()
            $('#div_success').hide()

            $('#edit_div_error').hide()
            $('#edit_div_success').hide()

            window.edit = function (element) { 
      
                id = $(element).data('id'); 
              

              
                const tr = element.closest('tr'); // Encontra a <tr> mais próxima do botão


                const tds = tr.querySelectorAll('td');

                // Pega a terceira e a quarta <td> (lembrando que os índices começam em 0)
                const terceiraColuna = tds[2];
                const quartaColuna = tds[3];

                $('#editInicio').val(terceiraColuna.textContent);
                $('#editFim').val(quartaColuna.textContent);

                $('#editModal').modal('show');
                };

         

            function loadData() {
                if ($.fn.DataTable.isDataTable('#grau-academico-table')) {
                    $('#grau-academico-table').DataTable().destroy();
            }
                var url = "{{ route('sce_ajax', ['course' => '__EDITION__', 'lective_year' => '__USER__']) }}"
                .replace('__EDITION__', course_id)
                .replace('__USER__', $('#lective_year').val());

            $('#grau-academico-table').DataTable({
              
                ajax: url,
                buttons:[
                    'colvis',
                        'excel',
                        'pdf',
                {
                    text: '<i class="fas fa-plus-square"></i>',
                    className: 'btn btn-primary ml-1 rounded',
                            action: function () {
                               
                        $('#formModal').modal('show');
                    }
                           }],

                columns: [
                    {
                    data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, 
                    {
                    data: 'number',
                    name: 'number',
                    
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    
                },
                
                 {
                   data: 'created_por',
                    name:'created_por',
                    visible: false
                }, {
                    data: 'updated_por',
                    name: 'updated_por',
                    visible: false
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }, {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
   
             language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
            }

            $('#form').on('submit', function(event) {
                event.preventDefault();
                console.log('test')
                var lective_year = $('#lective_year').val();
                var inicio = $('#inicio').val();
                var fim = $('#fim').val();
                var number = $('#grau-academico-table tr').length;
          
                $.ajax({
                    url: "{{ route('sce_storeEdition') }}",
                    type: "POST",
                    data:{
                    "_token": "{{ csrf_token() }}",
                    number: number,
                    lective_year: lective_year,
                    course_id: course_id,
                    start_date: inicio,
                    end_date: fim
                },
                    success: function(response) {
                        if (response.success) {
                            $('#success_message').text(response.success);
                            $('#div_success').show()
                            setTimeout(function() {
                                $('#div_success').fadeOut('slow');
                            }, 4000);
                           loadData();
                        } else {
                            $('#error_message').text(response.error);
                            $('#div_error').show()
                            setTimeout(function() {
                                $('#div_error').fadeOut('slow');
                            }, 4000);

                        }
                    },

                });

            });


            $('#editForm').on('submit', function(event) {
    event.preventDefault();
    console.log('test')
  
    var inicio = $('#editInicio').val();
    var fim = $('#editFim').val();
 

    $.ajax({
        url: "{{ route('sce_updateEdition','__ID__') }}".replace('__ID__',id),
        type: "POST",
        data:{
        "_token": "{{ csrf_token() }}",
        start_date: inicio,
        end_date: fim
    },
        success: function(response) {
            if (response.success) {
                $('#edit_success_message').text(response.success);
                $('#edit_div_success').show()
                setTimeout(function() {
                    $('#edit_div_success').fadeOut('slow');
                }, 4000);
               loadData();
            } else {
                $('#edit_error_message').text(response.error);
                $('#edit_div_error').show()
                setTimeout(function() {
                    $('#edit_div_error').fadeOut('slow');
                }, 4000);
            }
        },
    });
});



               
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
