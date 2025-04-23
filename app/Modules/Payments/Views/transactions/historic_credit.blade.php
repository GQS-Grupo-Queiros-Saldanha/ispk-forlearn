<title>Históricos | forLEARN® by GQS</title>
@extends('layouts.backoffice')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .list-group li button {
            border: none;
            background: none;
            outline-style: none;
            transition: all 0.5s;
        }

        .list-group li button:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            font-weight: bold
        }

        .subLink {
            list-style: none;
            transition: all 0.5s;
            border-bottom: none;
        }

        .subLink:hover {
            cursor: pointer;
            font-size: 15px;
            transition: all 0.5s;
            border-bottom: #dfdfdf 1px solid;
        }

        .fa-arrow-up {
            background-color: #21e821;
            padding: 4px;
            font-size: 10px;
            margin-right: 3px;
            border-radius: 2px;
        }

        .fa-arrow-down {
            background-color: #ff20108c;
            padding: 4px;
            font-size: 10px;
            margin-right: 3px;
            border-radius: 2px;
        }
    </style>


    <div class="content-panel" style="padding:0">
        @include('Payments::requests.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="https://inspunyl.forlearn.ao/pt/payments/requests">
                                        Tesouraria
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Histórico saldo em carteira
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-9">
                        <h1> Histórico [ Saldo em carteira ]</h1>
                    </div>
                    <div class="col-sm-3">

                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="col-12">

                    <div class="row">

                        <div class="col-6 div-anolectivo">
                            <div class="form-group col">
                                <label>Estudante</label>

                                <select name="requerimento_tipo" id="requerimento_tipo"
                                    class="selectpicker form-control form-control-sm" data-search="true"
                                    style="width: 100%; !important" disabled>
                                    <option value="{{ $estudante->id }}">{{ $estudante->nome_usuario }}
                                        #{{ $estudante->numb_mecanografico }} ({{ $estudante->email }}) </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 div-anolectivo">
                            <div class="form-group col">
                                <label>Tipo</label>

                                <select name="tipo" id="tipo" class="selectpicker form-control form-control-sm"
                                    data-search="true" style="width: 100%; !important">
                                    <option value="credito">Crédito</option>
                                    <option value="debito">Débito</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="form-group col">

                            <div class="credit-table">
                                <table id="credit-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>FACTURA/RECIBO nº</th>
                                            <th>Emolumento</th>
                                            <th>Saldo</th>
                                            <th>Obs</th>
                                            <th>FACTURA/RECIBO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $ic = 1;
                                        @endphp
                                        @foreach ($credito as $item)
                                            <tr>

                                                <td>{{ $ic++ }}</td>
                                                <td>{{ $item['recibo_n'] }}</td>
                                                <td>{{ $item['emolumento'] }}</td>
                                                <td>@icon('fas fa-arrow-up')
                                                    {{ number_format($item['valor'], 2, ',', '.') . ' kzs' }}
                                                </td>
                                                <td>
                                                    @if ($item['data_from'] == 'estorno')
                                                        <p class="text-danger">Estornado</p>
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $reciboNome = basename($item['recibo']);
                                                    @endphp
                                                    <a href="https://ispk.forlearn.ao/pt/payments/view-file/historic_credit/{{ $reciboNome }}"
                                                        class="btn btn-info btn-sm" target="_blank">
                                                        @icon('fas fa-file-pdf')
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="debit-table" style="display: none">
                                <table id="debit-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>FACTURA/RECIBO nº</th>
                                            <th>Emolumento</th>
                                            <th>Saldo</th>
                                            <th>Obs</th>
                                            <th>FACTURA/RECIBO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $ic = 1;
                                        @endphp
                                        @foreach ($debito as $item)
                                            <tr>
                                                <td>{{ $ic++ }}</td>
                                                <td>{{ $item['recibo_n'] }}</td>
                                                <td>{{ $item['emolumento'] }}</td>
                                                <td>@icon('fas fa-arrow-down')
                                                    {{ number_format($item['valor'], 2, ',', '.') . ' kzs' }}</td>
                                                <td>
                                                    @if ($item['data_from'] == 'estorno')
                                                        <p class="text-danger">Estornado</p>
                                                    @else
                                                        ---
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="https://inspunyl.forlearn.ao{{ $item['recibo'] }}"
                                                        class="btn btn-info btn-sm" target="blank">
                                                        @icon('fas fa-file-pdf')
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>

    </div>


    </div>
@endsection
@section('scripts')
    @parent
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> --}}

    <script>
        $(function() {
            // $('#debit-table').DataTable();

            let credit_table = $('#credit-table').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis', 'excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
            let debit_table = $('#debit-table').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis', 'excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
        });
    </script>

    <script>
        $(".debit-table").hide();
        $("#tipo").change(
            function() {
                if ($(this).val() == "credito") {
                    $(".credit-table").show();
                    $(".debit-table").hide();
                } else {
                    $(".credit-table").hide();
                    $(".debit-table").show();
                }
            }
        );
    </script>
@endsection
