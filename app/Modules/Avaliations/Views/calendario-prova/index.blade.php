<title>Avaliações | forLEARN® by GQS</title>
@php
    $isCreate = auth()
        ->user()
        ->hasAnyRole(['superadmin', 'staff_forlearn', 'staff_candidaturas']);
@endphp
@extends('layouts.generic_index_new')
@section('page-title', 'Calandário de prova')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Calandário de prova</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    @if ($isCreate)
        <a id="criarCalendario" href="" class="btn btn-success mb-3 ml-4 d-none">
            @icon('fas fa-plus-square')
            Criar novo calendário de prova
        </a>
    @endif
    <table id="calendarie-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>@lang('Users::users.name')</th>
                <th>Data do Inicio</th>
                <th>Data do Fim</th>
                <th>Periodo </th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
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
        $(function() {
            urlCriarCalendario();

            function urlCriarCalendario() {
                var id_anoLectivo = $("#lective_year").val();
                var url = "{{ url('avaliations/calendarie/getCreate') . '/' }}" + id_anoLectivo;
                var Calendario = $("#criarCalendario").attr('href', url);
            }

            function arrayButtons() {
                let params = ['colvis', 'excel'];
                @if ($isCreate)
                    params.push({
                        text: '<i class="fas fa-plus-square"></i> Criar novo calendário de prova',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        attr: {
                            id: "btn_create_can"
                        },
                        action: function(e, dt, node, config) {
                            window.open($('#criarCalendario').attr('href'), "_blank");
                        }
                    });
                @endif
                return params;
            }

            $('#calendarie-table').DataTable({
                ajax: '{!! route('calendarie.ajax') !!}',
                buttons: arrayButtons(),
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'cl.code',
                        visible: true,
                        searchable: true
                    }, {
                        data: 'name',
                        name: 'cl.display_name',
                        searchable: true
                    }, {
                        data: 'data_inicio',
                        name: 'cl.date_start',
                        searchable: true
                    }, {
                        data: 'date_fim',
                        name: 'cl.data_end',
                        searchable: true
                    },
                    {
                        data: 'periodo',
                        name: 'dt.display_name',
                        searchable: true
                    },
                    {
                        data: 'us_created_by',
                        name: 'u1.name',
                        visible: true
                    },
                    {
                        data: 'us_updated_by',
                        name: 'u2.name',
                        visible: false
                    },
                    {
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
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });

            $("#lective_year").change(function() {
                var lective_year = $("#lective_year").val();
                var url = "{{ url('avaliations/calendarie/getCreate') . '/' }}" + lective_year;
                var Calendario = $("#criarCalendario").attr('href', url);
                searchCalendarie("/avaliations/calendarie/getSCalendarie/" + lective_year);
            })

            function searchCalendarie(url) {
                $('#calendarie-table').DataTable().clear().destroy();
                $('#calendarie-table').DataTable({
                    "ajax": {
                        "url": url,
                        "type": "GET",
                        "data": {
                            "user_id": 451
                        }
                    },
                    buttons: arrayButtons(),
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'cl.code',
                            visible: true,
                            searchable: true
                        }, {
                            data: 'name',
                            name: 'cl.display_name',
                            searchable: true
                        }, {
                            data: 'data_inicio',
                            name: 'cl.date_start',
                            searchable: true
                        }, {
                            data: 'date_fim',
                            name: 'cl.data_end',
                            searchable: true
                        },
                        {
                            data: 'simestre',
                            name: 'dt.display_name',
                            searchable: true
                        },
                        {
                            data: 'us_created_by',
                            name: 'u1.name',
                            visible: true
                        },
                        {
                            data: 'us_updated_by',
                            name: 'u2.name',
                            visible: false
                        },
                        {
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
                        [10, 50, 100, 50000],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

        });
        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
