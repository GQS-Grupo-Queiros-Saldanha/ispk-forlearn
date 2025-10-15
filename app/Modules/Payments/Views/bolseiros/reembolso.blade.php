<title>Reembolsos | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title')
    @lang('Reembolsos')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Reembolsos</li>
@endsection
@section('styles-new')
    @parent
    <style>
        .red {
            background-color: red !important;
        }

        .dt-buttons {
            float: left;
            margin-bottom: 20px;
        }

        .dataTables_filter label {
            float: right;
        }


        .dataTables_length label {
            margin-left: 10px;
        }

        .casa-inicio {}

        .div-anolectivo {
            width: 300px;

            padding-right: 0px;
            margin-right: 15px;
        }

        #table-individual form,
        #table-all form {
            margin: 0px;
            display: inline;
        }

        #Modalconfirmar form input,
        #user_id {
            transform: scale(0);
        }

        table form {
            margin-bottom: 0px;
            display: contents;
        }

        #ModalWord form {
            display: flex;
        }

        #table-all {
            display: none;
        }
    </style>
@endsection
@section('selects')
    <div class="mb-2">
        <select name="lective_year" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="null" data-terminado="null">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->start_date . ',' . date('Y-m-d') . ',' . $lectiveYear->id }}" selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->start_date . ',' . $lectiveYear->end_date . ',' . $lectiveYear->id }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
@endsection
@section('buttons')
    <div class="form-group mb-2 pb-3 w-50">
        <label>Estudantes</label>
        <select name="student" id="student" class="selectpicker form-control form-control-sm" data-live-search="true"
            style="width: 100%; !important">
            <option value="1"></option>
            @foreach ($students as $item)
                <option value="{{ $item->id }}">{{ isset($item->full_name) ? $item->full_name : $item->name }}
                    #{{ $item->matriculation }} {{ $item->email }} </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="table-individual" class="table table-striped table-hover pt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Recibo nº</th>
                <th>Valor reembolsado</th>
                <th>Data</th>
                <th>Método de devolução</th>
                <th>Banco</th>
                <th>Nº da conta / IBAN</th>
                <th>Saldo inicial</th>
                <th>Saldo final</th>
                <th>Criado a</th>
                <th>Criado por</th>
                <th>Actividades</th>
            </tr>
        </thead>
    </table>

    <table id="table-all" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Recibo nº</th>
                <th>Estudante</th>
                <th>email</th>
                <th>Valor reembolsado</th>
                <th>Data</th>
                <th>Método de devolução</th>
                <th>Banco</th>
                <th>Nº da conta / IBAN</th>
                <th>Criado a</th>
                <th>Criado por</th>
                <th>Actividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('scripts-new')
    <script>
        (() => {
            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

            let urlRedirect = "";

            function isset(variable) {
                return typeof variable !== 'undefined' && variable !== null;
            }

            function openNewReembolso() {
                console.log(urlRedirect)
                if(urlRedirect != ""){
                    window.open(urlRedirect);
                }else{
                    warning('Seleciona um estudante');
                }
            }

            function get_reembolsos(id) {
                if (id == null) {
                    $('#table-all').DataTable({
                        ajax: {
                            "url": "/payments/bolseiros/ajax_reembolso_all/" + $("#lective_years").val().split(
                                ",")[2],
                            "type": "GET"
                        },
                        destroy: true,
                        buttons: [{
                            text: '<i class="fas fa-plus-square"></i> Novo',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text btn-new',
                            action: function(e, dt, node, config) {
                                openNewReembolso();
                            }
                        }, {
                            text: '<i class="fas fa-file"></i> Folha',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text',
                            action: function(e, dt, node, config) {
                                window.open('{!! route('bolseiros.report_reembolsos') !!}', "_blank");
                            }
                        }],
                        columns: [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'code',
                                name: 'code',
                                searchable: true
                            },
                            {
                                data: 'student',
                                name: 'student',
                                searchable: true
                            },
                            {
                                data: 'email',
                                name: 'email',
                                searchable: true
                            },
                            {
                                data: 'value',
                                name: 'value',
                                searchable: true
                            },
                            {
                                data: 'date',
                                name: 'date',
                                searchable: true
                            },
                            {
                                data: 'mode',
                                name: 'mode',
                                searchable: true
                            },
                            {
                                data: 'bank',
                                name: 'bank',
                                searchable: true
                            },
                            {
                                data: 'iban',
                                name: 'iban',
                                searchable: true
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                                searchable: true
                            },
                            {
                                data: 'created_by',
                                name: 'created_by',
                                searchable: true
                            },
                            {
                                data: 'actions',
                                name: 'actions',
                                searchable: true
                            }
                        ],
                        "lengthMenu": [
                            [10, 50, 100, 50000],
                            [10, 50, 100, "Todos"]
                        ],
                        language: {
                            url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                        }
                    });

                    $('#table-individual_wrapper,#table-individual').hide();
                    $("#table-all_wrapper,#table-all").show();

                } else {
                    var AnoDataTable = $('#table-individual').DataTable({
                        ajax: {
                            "url": "/payments/bolseiros/ajax_reembolso/" + id,
                            "type": "GET"
                        },
                        destroy: true,
                        buttons: [{
                            text: '<i class="fas fa-plus-square"></i> Novo',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text btn-new',
                            action: function(e, dt, node, config) {
                                openNewReembolso();
                            }
                        }, {
                            text: '<i class="fas fa-file"></i> Folha',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text',
                            action: function(e, dt, node, config) {
                                window.open('{!! route('bolseiros.report_reembolsos') !!}', "_blank");
                            }
                        }],
                        columns: [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'code',
                                name: 'code',
                                searchable: true
                            },
                            {
                                data: 'value',
                                name: 'value',
                                searchable: true
                            },
                            {
                                data: 'date',
                                name: 'date',
                                searchable: true
                            },
                            {
                                data: 'mode',
                                name: 'mode',
                                searchable: true
                            },
                            {
                                data: 'bank',
                                name: 'bank',
                                searchable: true
                            },
                            {
                                data: 'iban',
                                name: 'iban',
                                searchable: true
                            },
                            {
                                data: 'credit_balance',
                                name: 'credit_balance',
                                searchable: true
                            },
                            {
                                data: 'credit_balance_final',
                                name: 'credit_balance_final',
                                searchable: true
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                                searchable: true
                            },
                            {
                                data: 'created_by',
                                name: 'created_by',
                                searchable: true
                            },
                            {
                                data: 'actions',
                                name: 'actions',
                                searchable: true
                            }
                        ],
                        "lengthMenu": [
                            [10, 50, 100, 50000],
                            [10, 50, 100, "Todos"]
                        ],
                        language: {
                            url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                        }
                    });

                    $('#table-individual_wrapper,#table-individual').show();
                    $("#table-all_wrapper,#table-all").hide();

                }
            }


            $("#student").change(function() {
                get_reembolsos($(this).val());
                urlRedirect = "/payments/bolseiros/reembolsos/create/" + $(this).val();
                $(".btn-new").attr("value", urlRedirect);
            });

            $("#lective_years").change(function() {
                get_reembolsos(null);
            });

            get_reembolsos(null);
        })();
    </script>
@endsection
