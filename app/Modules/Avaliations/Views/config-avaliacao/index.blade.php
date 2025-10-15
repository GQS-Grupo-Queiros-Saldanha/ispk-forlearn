<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Configurações avaliações')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Configurações</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option>Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if( $lectiveYearSelected->id == $lectiveYear->id ) selected @endif >
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="configuration-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Ano lectivo</th>
                <th>Estratégia</th>
                <th>Nota para dispensar no exame</th>
                <th>Nota mínima para ir a exame </th>
                <th>Nota máxima para ir a exame</th>
                <th>Nota máxima para ir a recurso</th>
                <th>Nota para dispensar em MAC</th>
                <th>Percentagem da MAC</th>
                <th>Percentagem do Exame</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Avaliations::config-avaliacao.form')
@endsection
@section('scripts-new')
    @parent
    <script>
        
    
        (()=>{

            getConfiguration();

            function getConfiguration(){
                $('#configuration-table').DataTable({
                    ajax: '{!! route('avaliacao.config.ajax') !!}',
                    buttons: [
                        {
                            className: 'btn-primary main ml-1 rounded',
                            text: '<i class="fas fa-plus-square"></i> Criar nova configuração',
                            attr:{ "data-toggle": "modal", "data-target":"#exampleModalCenter"},
                            action: function(e, dt, node, config) {
                                document.querySelector("#exampleModalLongTitle").innerHTML = "Criar configuração";
                                document.querySelector("#btn-save").innerHTML = "<i class='fas fa-save'></i><span>Salvar</span>";
                                createLectiveYear();
                                selectorStrategy("");
                                initValueInform(10,7,13,7,14,40,60);
                                formAction('{!! route('avaliacao.config.store') !!}');
                            }
                        }
                    ],
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },{
                            data: 'display_name',
                            name: 'lyt.display_name',
                        }, {
                            data: 'strategy',
                            name: 'ac.strategy',
                        }, {
                            data: 'exame_nota',
                            name: 'ac.exame_nota',
                        }, {
                            data: 'exame_nota_inicial',
                            name: 'ac.exame_nota_inicial',
                        }, {
                            data: 'exame_nota_final',
                            name: 'ac.exame_nota_final',
                        },
                        {
                            data: 'mac_nota_recurso',
                            name: 'ac.mac_nota_recurso',
                        },
                        {
                            data: 'mac_nota_dispensa',
                            name: 'ac.mac_nota_dispensa',
                        },
                        {
                            data: 'percentagem_mac',
                            name: 'ac.percentagem_mac',
                        },
                        {
                            data: 'percentagem_oral',
                            name: 'ac.percentagem_oral',
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                        },                        
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
 
        })();
    </script>
@endsection
