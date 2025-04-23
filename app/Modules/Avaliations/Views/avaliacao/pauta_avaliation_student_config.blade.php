<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LIMITE DE PROPINA')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a href="{{ route('pauta_student.config') }}">Limite de Propina</a>
    </li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
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
    @include('Avaliations::avaliacao.show-panel-avaliation-button')
    <table id="tipo-metrica-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Quat. Mês</th>
                <th>Quat. Dia(s)</th>
                <th>Ano Lectivo</th>
                <th>Criado por</th>
                <th>@lang('common.actions')</th>
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
        $(document).ready(function (){
            var lective_year = $("#lective_year").val();
            var table = $('#tipo-metrica-table');

            show_data("/avaliations/pauta_student_ajax/"+lective_year);

            $("#lective_year").bind('change keypress ', function (e){ 
                lective_year = $("#lective_year").val();
                table.DataTable().clear().destroy();
                show_data("/avaliations/pauta_student_ajax/"+lective_year);
            });

            function show_data(url) {
                table.DataTable({
                    "ajax": {
                        "url": url,
                        "type": "GET",
                        "data": { }
                    },
                    buttons: ['colvis', 'excel', {
                        text: '<i class="fas fa-plus-square"></i> Criar novo',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            window.open('{!! route('pauta_student_config.create') !!}', "_blank");
                        }
                    }],
                    columns: [                   
                        {
                            data: 'DT_RowIndex',
                            orderable: false, 
                            searchable: false            
                        },{
                            data: 'quantidade_mes',
                            name: 'quantidade_mes'
                        }, {
                            data: 'quatidade_day',
                            name: 'quatidade_day'
                        }, {
                            data: 'lective_year_id',
                            name: 'lective_year_id'
                        }, {
                            data: 'created_by',
                            name: 'created_by',
                        }, 
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                    }
                });        
            }
            
            // Delete confirmation modal
            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
        });    
    </script>
@endsection
