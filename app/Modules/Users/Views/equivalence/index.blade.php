<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Equivalência')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Equivalência</li>
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
    <table id="matriculations-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th id="dado">#</th>
                <th>Nome completo </th>
                <th>E-mail</th>
                <th>Curso</th>
                <th>Instituição proveniênte</th>
                <th>Tipo de transferência</th>
                <th>Estado do pagamento</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.created_at')</th>
                <th>Atividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::equivalence.datatables.modalDelete')
@endsection
@section('scripts-new')
    <script>
        (() => {
            let curso = $("#curso");
            let curso_years = $("#curso_years");
            let id_curso = $("#curso");
            let id_anoLective = $("#lective_years");


            id_anoLective.bind('change keypress', function() {
                id_anoLective = $("#lective_years").val();
            });
        
            getCurso(id_anoLective.val());
                $("#lective_years").change(function() {
                    let lective_year = $("#lective_years").val();
                    $('#matriculations-table').DataTable().clear().destroy();
                    getCurso(lective_year);
                })
            
            function getCurso(id_lective) {
                $('#matriculations-table').DataTable({
                    "ajax": {
                        "url": "/users/ajaxTransferenceStudant/"+id_lective,
                        "type": "GET",
                    },
                    buttons: [
                        'colvis',
                        'excel',
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
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
                        },
                        {
                            data: 'school_name',
                            name: 'school_name'
                        },
                        {
                            data: 'in_out',
                            name: 'in_out'
                        }, {
                            data: 'states',
                            name: 'state',
                            searchable: false
                        }, {
                            data: 'criado_por',
                            name: 'u1.name',
                            visible: false
                        }, {
                            data: 'created_at',
                            name: 'created_at',
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



