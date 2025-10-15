<title>Candidaturas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('navbar')
    @include('Users::candidate.navbar.navbar')
@endsection
@section('page-title', 'Calendário das Candidaturas')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('candidates.index') }}">Candidaturas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Calendários</li>
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
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>ano lectivo</th>
                <th>data_inicio</th>
                <th>data_fim</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
@endsection
@section('hiddens')
    <a class="btn btn-primary rounded" href="{{ route('candidate.ano_lectivo') }}" id="calendario-create"></a>
@endsection
@section('scripts-new')
    <script>
        (() => {
            const calendarioCreate = $('#calendario-create');
            const lectiveYearSelect = $('#lective_year');
            const table = $('#users-table');

            ajaxReload(lectiveYearSelect.val());

            lectiveYearSelect.change((e) => {
                ajaxReload(lectiveYearSelect.val());
            })

            function ajaxReload(lective_year = "") {
                let params = lective_year != "" ? `?year=${lective_year}` : '';
                let url = '{!! route('candidates.ajax.list') !!}' + params;
                let tam = table.children('tbody').length;
                if (tam > 0) table.DataTable().clear().destroy();
                table.DataTable({
                    ajax: url,
                    buttons: ['colvis', 'excel', {
                        text: '<i class="fas fa-plus-square"></i> Criar novo calendário',
                        className: 'btn-primary main ml-1 rounded',
                        action: function(e, dt, node, config) {
                            window.open(calendarioCreate.attr('href'), "_blank");
                        }
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'display_name',
                        name: 'display_name',
                        searchable: true
                    }, {
                        data: 'data_inicio',
                        name: 'data_inicio',
                        searchable: true
                    }, {
                        data: 'data_fim',
                        name: 'data_fim',
                        visible: true,
                        searchable: true
                    }, {
                        data: 'actions',
                        name: 'action',
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

        })();
    </script>
@endsection
