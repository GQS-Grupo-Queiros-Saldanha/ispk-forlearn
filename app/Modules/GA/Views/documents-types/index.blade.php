<title>Tipos de Documentos | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Tipos de Documentos')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Tipos de Documentos</li>
@endsection

@section('body')

   

                                <table id="tipos-documentos-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Id')</th>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Observação')</th>
                                        <th>@lang('created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                    </thead>
                                </table>

    

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection
@section('scripts')

    @parent
    <script>
        $(function () {
            $('#tipos-documentos-table').DataTable({
                ajax: '{!! route('documents-types.ajax') !!}',
                buttons:[
                    'colvis',
                        'excel',
                        'pdf',
                {
                            text: '<i class="fas fa-plus-square"></i> Criar novo',
                            className: 'btn-primary main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) { 
                                let url =  'documents-types/create/';
                                window.open(url, "_self");
                            }}],

                columns: [
                    {
                    data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, 
                
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name',
                    
                }, 
                {
                    data: 'observation',
                    name: 'observation'
                },
                 {
                    data: 'created_by',
                    name:'created_by',
                    //visible: false
                }, {
                    data: 'updated_by',
                    name: 'updated_by',
                    visible: false
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    //visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    //visible: false
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
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
