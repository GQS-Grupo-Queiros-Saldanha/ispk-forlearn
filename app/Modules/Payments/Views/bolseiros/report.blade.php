<title>Reembolsos | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Folha de caixa [ Reembolsos ]')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>    
    <li class="breadcrumb-item">
        <a href="{{ route('bolseiros.reembolsos') }}" class="">
            Reembolsos
        </a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Folha de caixa [ Reembolsos ]</li>
@endsection 
@section('styles-new')
    @parent
    <style>
        success:focus,.btn-success:hover {
            color: rgb(255, 255, 255);
            background-color: rgb(47, 163, 96) !important;
            border-color: rgb(45, 153, 91) !important;
            font-style: normal;

        }
    </style>
@endsection
@section('selects')
    <div class="">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->start_date . ',' . date('Y-m-d') . ',' . $lectiveYear->id }}" data-id="{{$lectiveYear->id}}" selected>
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
@section('body')
        <form method="POST" action="{{ route('bolseiros.report_pdf') }}" class="m-3 d-flex gap-1 items-center" target="_blank" style="align-items: center !important;">
            @csrf
            <input name="lective_years" id="lective_years_aux" type="hidden"/>
            <div class="">
                <label for="dateInicio" class="form-label">Data de início</label>
                <input type="date" class="form-control" id="dataInicio_id" value="{{ date('Y-m-d') }}" name="dataInicio"
                    style="width: 220px;" required>
            </div>

            <div class="">
                <label for="dataFim" class="form-label">Data de fim</label>
                <input type="date" class="form-control" id="dataFim_id" name="dataFim" style="width: 220px;" required>
            </div>
            <div class="mt-4">
                <button name="submitButton" value="pdf" type="submit" class="btn btn-dark"
                    style="">
                    <i class="fas fa-plus-square" ></i> Criar folha de caixa
                </button>
            </div>                
        </form>
        
        <div class="" id="content-table" style="">
            <table id="recibo-table" class="table table-striped table-hover">
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
                        <th>Tesoureiro</th>
                        <th>Actividades</th>
                    </tr>
                </thead>
            </table>
        </div>
        
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')

    <script>
        $(function() {
            let valor = "";
            let table = $('#recibo-table');

            table.DataTable({
                    ajax: {
                        "url": "/payments/bolseiros/ajax_reembolso_all/"+$("#lective_years").val().split(",")[2],
                        "type": "GET" 
                    },
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],

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

            // Função para pegar os val
            //Quando a página carregar   
            calendario();
            $("#lective_years").change(function() {
                calendario();
                change_payments();
            });
            
            //Usado para automatização das datas no calendário
            /*Mateus & Cláudio*/
            function change_payments() {
                

                table.DataTable({
                    ajax: {
                        "url": "/payments/bolseiros/ajax_reembolso_all/"+$("#lective_years").val().split(",")[2],
                        "type": "GET" 
                    },
                    destroy: true,
                    buttons: [
                        'colvis',
                        'excel'
                    ],

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
            }
            function calendario() {
                data = $("#lective_years").val().split(",");
                $("#dataInicio_id,#dataFim_id").attr("min", '' + data[0] + '');
                $("#dataInicio_id").val(data[1]);
                $("#dataFim_id").val(data[1]);
                $("#dataInicio_id,#dataFim_id").attr("max", '' + data[1] + '');
                $('#lective_years_aux').val(data[2]);
            }
            
        });
    </script>

    @include('layouts.backofficeScriptQueryBuilder')
    
@endsection