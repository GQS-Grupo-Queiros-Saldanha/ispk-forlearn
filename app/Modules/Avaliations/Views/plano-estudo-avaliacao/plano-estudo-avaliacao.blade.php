<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PLANO DE ESTUDO E AVALIAÇÃO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Plano de estudo e avaliação</li>
@endsection
@section('body')
    @php $i = 1; @endphp
    <table id="plano_estudo_avaliacao_tables" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Avaliação</th>
                <th>Edição de Plano de Estudos</th>
                <th>Disciplina</th>
                <th>Criado Por</th>
                <th>Editado Por</th>
                <th>Criado a</th>
                <th>Editado a</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            $('#plano_estudo_avaliacao_tables').DataTable({
                ajax: '{!! route('plano_estudo_avaliacao.ajax') !!}',
                buttons: ['colvis','excel',{
                    text: '<i class="fas fa-plus-square"></i> Criar novo',
                    className: 'btn-primary main ml-1 rounded btn-main btn-text',
                    action: function(e, dt, node, config) {
                        window.open("{{ route('plano_estudo_avaliacao.create') }}", "_blank");
                    }
                } ],
                columns: [{
                        name: 'avaliacaos.nome',
                        data: 'nome',
                        searchable: true
                    },
                    {
                        name: 'spet.display_name',
                        data: 'spet_nome',
                        searchable: true
                    },
                    {
                        name: 'dt.display_name',
                        data: 'discipline_nome',
                        searchable: true
                    },
                    {
                        name: 'created_by',
                        data: 'created_by',
                        searchable: false
                    },
                    {
                        name: 'updated_by',
                        data: 'updated_by',
                        searchable: false
                    },
                    {
                        name: 'created_at',
                        data: 'created_at',
                        searchable: false
                    },
                    {
                        name: 'updated_at',
                        data: 'updated_at',
                        searchable: false
                    },
                    {
                        name: 'actions',
                        data: 'actions',
                        searchable: false
                    }
                ],
                language: { url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}' }
            })
        });
        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>    
@endsection
