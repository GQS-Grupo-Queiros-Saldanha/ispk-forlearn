<title>Tesouraria | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Folha de caixa [ Resumo ]')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Folha de caixa [ resumo ]</li>
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
@section('body')
        <form method="POST" action="{{ route('listasRecibos') }}" class="m-3 d-flex gap-1 items-center" target="_blank" style="align-items: center !important;">
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
            <div class="mt-4">
                <button name="submitButton" value="excel" type="submit" class="btn btn-success"
                    style=" ">
                    <i class="fas fa-file-excel" ></i> Gerar excel
                </button>
            </div>
        </form>
        
        <div class="" id="content-table" style="">
            <table id="recibo-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matrícula</th>
                        <th>Nome do estudante</th>
                        <th>Recibo Nº</th>
                        <th>Data</th>
                        <th>Tesoureiro</th>
                        <th>Valor</th>
                        <th>Documentos</th>

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
                ajax: '{!! route('tabelaRecibo') !!}',
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
                        data: 'matricula',
                        name: 'u_m.value'
                    },
                    {
                        data: 'estudante',
                        name: 'u_p.value'
                    },
                    {
                        data: 'code',
                        name: 'trans.code'
                    },
                    {

                        data: 'data',
                        name: 'trans.created_at'
                    },

                    {
                        data: 'criador',
                        name: 'users.name',
                        searchable: true,
                    },
                    {
                        data: "valor",
                        name: 'tr.value'
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

            // Função para pegar os val
            //Quando a página carregar   
            calendario();
            $("#lective_years").change(function() {
                calendario()
            });
            
            //Usado para automatização das datas no calendário
            /*Mateus & Cláudio*/
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