@extends('layouts.generic_index_new')
@section('title',__('Payments::banks.banks'))
@section('page-title', 'Bancos')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Bancos</li>
@endsection
@section('body')
    <table id="banks-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>@lang('common.code')</th>
                <th>@lang('translations.display_name')</th>
                <th>@lang('Payments::banks.account_number')</th>
                <th>{{ 'IBAN' }}</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>@lang('common.actions')</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            $('#banks-table').DataTable({
                ajax: '{!! route('banks.ajax') !!}',
                buttons: ['colvis', 'excel',{
                        text: '<i class="fas fa-plus"></i> Novo Banco',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            window.open("{{ route('banks.create') }}", "_blank");
                        }
                    }, {
                    text: '<i class="fas fa-file-pdf"></i> Lista de Bancos',
                    className: 'btn-secondary main ml-1 rounded btn-main btn-text',
                    action: function (e, dt, node, config) {
                        window.open("{{ route('banks.pdf') }}", "_blank");
                    }
                }
                ],                
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'display_name'
                }, {
                    data: 'account_number',
                    name: 'account_number'
                }, {
                    data: 'iban',
                    name: 'iban'
                }, {
                    data: 'created_by',
                    name: 'u1.name',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'u2.name',
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
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        });

        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
