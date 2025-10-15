@extends('layouts.generic_index_new')
@section('navbar')
@endsection
@section('page-title', 'MATRÍCULAS ANULADAS FINALISTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('index.matriculation-finalista') }}">Finalista</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Anulados</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
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
                <th id="dado">#</th>
                <th>Confirmação </th>
                <th>Matrícula</th>
                <th>Nome do estudante</th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Ano </th>
                <th>nº BI </th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Estado do pagamento</th>
                <th>Observação</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            let anoLective = $("#lective_years");
            let tableFinalista = $('#users-table');

            getQueryAjax(`anulate_matriculation_finalist_ajax/${anoLective.val()}`);

            function getQueryAjax(url) {
                if (tableFinalista.children('tbody').length > 0)
                    tableFinalista.DataTable().clear().destroy();
                tableFinalista.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: url,
                    buttons: [
                        'colvis',
                        'excel',
                    ],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'code_matricula',
                        name: 'code_matricula'
                    }, {
                        data: 'matricula',
                        name: 'matricula'
                    }, {
                        data: 'name_full',
                        name: 'name_full'
                    }, {
                        data: 'email',
                        name: 'email'
                    }, {
                        data: 'course',
                        name: 'course'
                    }, {
                        data: 'year_curso',
                        name: 'year_curso',
                        visible: false
                    }, {
                        data: 'num_bi',
                        name: 'num_bi'
                    }, {
                        data: 'criado_por',
                        name: 'criado_por',
                        visible: false
                    }, {
                        data: 'criado_em',
                        name: 'criado_em',
                        visible: false
                    }, {
                        data: 'actualizado_por',
                        name: 'actualizado_por'
                    }, {
                        data: 'states',
                        name: 'states',
                        searchable: false
                    }, {
                        data: 'descricao',
                        name: 'descricao'
                    }, {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }],
                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    },
                });
            }

            anoLective.change((e) => {
                getQueryAjax(`anulate_matriculation_finalist_ajax/${anoLective.val()}`);
            })
        })();
    </script>
@endsection