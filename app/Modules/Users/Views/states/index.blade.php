<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estados')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Estados</li>
@endsection
@section('body')
    <div hidden>
        <a href="{{ route('states.create') }}" id="estado-create"></a>
    </div>
    <table id="states-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Sigla</th>
                <th>Nome</th>
                <th>Estado</th>
                <th>Criado Por</th>
                <th>Atualizado Por</th>
                <th>Criado a</th>
                <th>Atualizado a</th>
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
    $(function() {
        $("#states-table").DataTable({
            processing: true,
            buttons: ['colvis', 'excel', {
                text: '<i class="fas fa-plus-square"></i> Novo',
                className: 'btn-primary main ml-1 rounded btn-main',
                action: function(e, dt, node, config) {
                    window.open($("#estado-create").attr('href'), "_blank");
                }
            }],
            serverSide: true,
            ajax: "{{ route('states.ajax') }}",
            columns: [{
                    data: 'initials',
                    name: 'initials'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'state_type',
                    name: 'state_type'
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    data: 'updated_by',
                    name: 'updated_by',
                    visible: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                },
                {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
            }
        });
    });
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
</script>
@endsection