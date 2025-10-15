@extends('layouts.generic_index_new')
@section('navbar')
@endsection
@section('page-title')
    Candidatura graduado
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ url()->previous() }}">Candidatura</a>
    </li>
    <li class="breadcrumb-item">
        <a href="#">Graduado</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Criar</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
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
                <th>#</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Matricula</th>
                <th>Nota(TFC)</th>
                <th>Acções</th>
            </tr>
        </thead>
    </table>
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script>
        (() => {
            const table = $('#users-table');
            const lectiveSelect = $('#lective_year');

            loadDataGraduado();

            lectiveSelect.change(function() {
                let lective_year = $("#lective_year").val();
                if (lective_year != "") {
                    loadDataGraduado();
                }
            })

            function urlGraduado() {
                let lective_year = lectiveSelect.val()
                let url = '{!! route('ajax.finalista.graduado') !!}' + `?lective_year=${lective_year}`;
                return url;
            }

            function loadDataGraduado() {
                let tam = table.children('tbody').length;
                let url = urlGraduado();
                if (tam > 0) table.DataTable().clear().destroy();
                table.DataTable({
                    ajax: url,
                    buttons: ['colvis', 'excel'],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'nome_completo',
                        name: 'nome_completo',
                        searchable: true
                    }, {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    }, {
                        data: 'curso',
                        name: 'curso',
                        searchable: true
                    }, {
                        data: 'matricula',
                        name: 'matricula',
                        searchable: true
                    }, {
                        data: 'nota',
                        name: 'nota',
                        searchable: true
                    }, {
                        data: 'actions',
                        name: 'actions',
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
