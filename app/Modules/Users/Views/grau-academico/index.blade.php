<title>Graus académicos | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Graus académicos')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Graus académicos</li>
@endsection

@section('body')

   

                                <table id="grau-academico-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('Abreviação')</th>
                                        <th>@lang('Descrição')</th>
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

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#grau-academico-table').DataTable({
                ajax: '{!! route('grau-academico.ajax') !!}',
                buttons:[
                    'colvis',
                        'excel',
                        'pdf',
                {
                            text: '<i class="fas fa-plus-square"></i> Criar novo',
                            className: 'btn-primary main ml-1 rounded btn-main new_matricula',
                            action: function(e, dt, node, config) { 
                                let url =  'grau-academico/create/';
                                window.open(url, "_self");
                            }}],

                columns: [
                    {
                    data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, 
                    {
                    data: 'codigo',
                    name: 'codigo',
                    
                }, 
                {
                    data: 'nome',
                    name: 'nome'
                },
                {
                    data: 'abreviacao',
                    name: 'abreviacao',
                    visible: false
                }, 
                {
                    data: 'descricao',
                    name: 'descricao'
                },
                 {
                   data: 'created_by',
                    name:'created_by',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'updated_by',
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
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
