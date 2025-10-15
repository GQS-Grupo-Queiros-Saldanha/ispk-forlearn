@extends('layouts.generic_index_new')
@section('page-title', 'MATRÍCULAS ANULADAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
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
    @include('Users::candidate.fase_candidatura.message')
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Confirmação</th>
                <th>Matrícula</th>
                <th>Nome do estudante</th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Ano </th>
                <th>Turma </th>
                <th>nº BI </th>
                <th>Estado do pagamento</th>
                <th>Observação</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            let anoLective = $("#lective_years").val();
            ajaxStudant(anoLective);

            $("#lective_years").change(function() {
                $('#users-table').DataTable().clear().destroy();
                ajaxStudant($("#lective_years").val());
            });

            function ajaxStudant(anoLetive) {
                let routa = "/users/matriculations/anulate_matriculation_ajax/" + anoLetive;
                $('#users-table').DataTable({
                    ajax: routa,
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
                            name: 'matriculations.code'
                        }, {
                            data: 'matricula',
                            name: 'up_meca.value'
                        }, {
                            data: 'student',
                            name: 'u_p.value'
                        }, {
                            data: 'email',
                            name: 'u0.email',
                            visible: false
                        }, {
                            data: 'course',
                            name: 'ct.display_name'
                        }, {
                            data: 'course_year',
                            name: 'course_year'
                        }, {
                            data: 'classe',
                            name: 'cl.display_name'
                        }, {

                            data: 'n_bi',
                            name: 'up_bi.value'

                        }, {
                            data: 'states',
                            name: 'state',
                            searchable: false
                        },
                        {
                            data: 'descricao',
                            name: 'anulate_m.description',
                            searchable: false
                        },
                        {
                            data: 'criado_por',
                            name: 'u1.name',
                            visible: false
                        }, {
                            data: 'actualizado_por',
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
                        }
                    ],

                    "lengthMenu": [
                        [10, 100, 50000],
                        [10, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
            }

        })();
    </script>
@endsection
