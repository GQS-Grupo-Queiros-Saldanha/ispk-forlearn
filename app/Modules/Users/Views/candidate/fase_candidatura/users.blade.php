@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title') 
 Candidatos  {{$lectiveYear->display_name}} na fase {{$lectiveCandidate->fase}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidate.list_candidatura') }}">Calendário</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('fase.anolectivo', $lectiveYear->lective_years_id) }}">Fases</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Usuários</li>
@endsection
@section('body')
    @if (!$lectiveCandidate->is_termina)
        <p class="mb-2 text-danger">
            <i class="fas fa-info"></i>
            <span>A fase ({{ $lectiveCandidate->fase }}) se encontra aberto [{{ $lectiveCandidate->data_inicio }} -
                {{ $lectiveCandidate->data_fim }}], não será permitido que os candidatos passam para próxima fase.</span>
        </p>
    @endif
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Nº de Candidato</th>

                <th>Fase</th>

                <th>@lang('Users::users.name')</th>
                <th>@lang('Users::users.email')</th>
                <th>Curso</th>
                <th>Estado do pagamento</th>


                {{-- <th>Ano lectivo</th> --}}
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>

                <th>Ações</th>
            </tr>
        </thead>
    </table>

    @include('Users::candidate.modal.modal_gerar_pdf')
    @include('Users::candidate.modal.modal_escolher_curso')
    @include('Users::candidate.modal.modal_users_historico')
    @include('Users::candidate.modal.modal_fase_transferencia')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            reloadDatas();

            const ident = $('#user');
            const form = $('#form');
            const message = $('#message');
            const faseNova = $('#fase_nova');
            const modalFase = $('#modalFase');
            const modalEscolher = $('#modalEscolher');
            const modalHistorico = $('#modalHistorico');
            const formMethod = $("[name='_method']");

            function reloadDatas() {
                let url = $('#url');
                console.log(url.val())
                $('#users-table').DataTable({
                    ajax: '' + url.val(),
                    buttons: ['colvis', 'excel'],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false

                        }, {
                            data: 'cand_number',
                            name: 'candidate.value',
                            searchable: true
                        },

                        {
                            data: 'fase',
                            name: 'lc.fase',
                            searchable: true
                        },
                        {
                            data: 'name_name',
                            name: 'full_name.value',
                            visible: true,
                            searchable: true
                        }, {
                            data: 'email',
                            name: 'email',
                            searchable: true
                        }, {
                            data: 'cursos',
                            name: 'cursos',
                            visible: true,
                            searchable: true
                        }, {
                            data: 'states',
                            name: 'state',
                            searchable: false
                        },
                        {
                            data: 'us_created_by',
                            name: 'u1.name',
                            visible: true,
                            searchable: false
                        },
                        {
                            data: 'us_updated_by',
                            name: 'u2.name',
                            visible: false,
                            searchable: false
                        },
                        {
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
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "lengthMenu": [
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

        })();
    </script>
@endsection
