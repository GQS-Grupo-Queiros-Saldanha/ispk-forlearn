@extends('layouts.generic_index_new')
@section('title', __('Gerir bolsas de estudo'))
@section('page-title', 'Gerir entidades')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('requests.index') }}" class="">
        Tesouraria
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">Gerir entidades</li>
@endsection
@section('body')
<table id="table" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Código</th>
            <th>Nome da Empresa</th>
            <th>Tipo</th>
            <th>NIF</th>
            <th>Telefone</th>
            <th>Criado Por</th>
            <th>Actualizado Por</th>
            <th>@lang('common.created_at')</th>
            <th>@lang('common.updated_at')</th>
            <th>Ações</th>
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
    $(function () {
        $('#table').DataTable({
            ajax: '{!! route('scholarship.ajax_entity') !!}',
            buttons: ['colvis','excel', {
                    text: '<i class="fas fa-plus"></i> Nova entidade',
                    className: 'btn-primary main ml-1 rounded btn-main btn-text',
                    action: function (e, dt, node, config) {
                        window.open("{{ route('create.entity') }}", "_blank");
                    }
                }, {
                    text: '<i class="fas fa-file-pdf"></i> Lista de entidades',
                    className: 'btn-primary main ml-1 rounded btn-main btn-text',
                    action: function (e, dt, node, config) {
                        window.open("{{ route('pdf.scholarship_entity') }}", "_blank");
                    } 
                }, {
                    text: '<i class="fas fa-plus"></i> Implementar regra',
                    className: 'btn-primary main ml-1 rounded btn-main btn-text',
                    action: function (e, dt, node, config) {
                        window.open("{{ route('scholarship.rules') }}", "_blank");
                    }
                }
            ],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'code', name: 'code' },
                { data: 'company', name: 'company' },
                { data: 'type', name: 'type' },
                { data: 'NIF', name: 'NIF' },
                { data: 'telf', name: 'telf' },
                { data: 'created_by', name: 'created_by', visible: false }, // Alterado para 'created_by_name'
                { data: 'updated_by', name: 'updated_by', visible: false }, // Alterado para 'updated_by_name'
                { data: 'created_at', name: 'created_at', visible: false },
                { data: 'updated_at', name: 'updated_at', visible: false },
                { data: 'actions', name: 'actions' }
            ],
            language: {
                url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
            }
        });
    });
</script>



@endsection

