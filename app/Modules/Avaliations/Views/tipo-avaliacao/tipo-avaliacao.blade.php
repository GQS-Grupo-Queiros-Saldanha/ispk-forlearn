<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'TIPOS DE AVALIAÇÃO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Tipos de avaliação</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
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
    <table id="tipo-avaliacao-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Abreviatura</th>
                <th>Criado por</th>
                <th>Atualizado por</th>
                <th>Criado a</th>
                <th>Atualizado a</th>
                <th>@lang('common.actions')</th>
            </tr>
        </thead>
    </table>
    <a class="d-none" id="id_novo-tipo" href="#"></a>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {

            var lective_year = $("#lective_year").val();
            tabela(lective_year);
            let route = "/pt/avaliations/create-type_avaliation/" + lective_year;
            document.getElementById("id_novo-tipo").setAttribute('href', route);

            //Começar a trocar baseado no ano lectivo
            $("#lective_year").change(function() {
                var lective_year = $("#lective_year").val();
                if (lective_year == 6) {
                    $("#id_novo-tipo").hide();
                } else {

                    let route = "/pt/avaliations/create-type/" + lective_year;
                    document.getElementById("id_novo-tipo").setAttribute('href', route);
                    $("#id_novo-tipo").show();
                }

                $('#tipo-avaliacao-table').DataTable().clear().destroy();
                tabela(lective_year);
            });


            function tabela(lective_year) {
                $('#tipo-avaliacao-table').DataTable({
                    "ajax": {
                        "url": "/avaliations/tipo_avaliacao_ajax_anoLestivo/" + lective_year,
                        "type": "GET",
                        "data": {
                            "user_id": 451
                        }
                    },
                    buttons: ['colvis', 'excel', {
                        text: '<i class="fas fa-plus-square"></i> Criar novo',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            window.open($('#id_novo-tipo').attr('href'), "_blank");
                        }
                    }],
                    columns: [{
                            data: 'codigo',
                            name: 'codigo'
                        },
                        {
                            data: 'nome',
                            name: 'nome'
                        },
                        {
                            data: 'descricao',
                            name: 'descricao'
                        },
                        {
                            data: 'abreviatura',
                            name: 'abreviatura'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            visible: false
                        }, {
                            data: 'updated_by',
                            name: 'updated_by',
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
                            name: 'actions',
                        }
                    ],
                    "lengthMenu": [
                        [10, 50, 100, -1],
                        [10, 50, 100, "Todos"]
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });

            }
            // Delete confirmation modal
            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
        });
    </script>
@endsection
