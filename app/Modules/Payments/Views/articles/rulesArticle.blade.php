@extends('layouts.generic_index_new')
@section('title', __('Payments::articles.articles'))
@section('page-title', 'Implementar Regra')
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
        <a href="{{ route('articles.index') }}" class="">
            Emolumentos - Propinas
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Implementar regra</li>
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
    <table class="table table-striped" id="table-rules">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">EMOLUMENTO/PROPINA</th>
                <th scope="col">MÊS</th>
                <th scope="col">Periódo</th>
                <th scope="col">Ano curricular</th>
                <th scope="col">VALOR</th>
                <th scope="col">ESTADO</th>
                <th scope="col">CRIADO AO</th>
                <th scope="col">ACÇÃO</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @php
        $anoCurriculares = [
            '1' => 'Primeiro ano',
            '2' => 'Segundo ano',
            '3' => 'Terceiro ano',
            '4' => 'Quarto ano',
            '5' => 'Quinto ano',
        ];
    @endphp
    
    @include('layouts.backoffice.modal_confirm')

    <div class="modal fade" id="modalRuleArticle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="#" class="modal-content" method="POST">
                <div class="modal-header">
                    @csrf
                    @method('POST')
                    <h3 class="modal-title" id="staticBackdropLabel" id="">Formulário de regra</h3>
                </div>
                <div class="modal-body row">
                    <div class="col-6 p-1">
                        <label>@lang('Payments::articles.article') <span class="text-danger">*</span> </label>
                        <select name="emolument[]" id="emolument" multiple class="selectpicker form-control form-control-sm"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($model as $arti)
                                <option value="{{ $arti['id'] }}"> {{ $arti['display_name'] }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 p-1">
                        <label for="month">Mês <span class="text-danger">*</span></label>
                        <select name="month[]" id="month" multiple class="selectpicker form-control"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($ordem_Month as $month)
                                <option value="{{ $month['id'] }}">{{ $month['display_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 p-1">
                        <label for="schedule_type">Periódo</label>
                        <select name="schedule_type[]" id="schedule_type" multiple class="selectpicker form-control"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($scheduleTypes as $scheduleType)
                                <option value="{{ $scheduleType->id }}">{{ $scheduleType->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 p-1">
                        <label for="ano_curricular">Ano curricular</label>
                        <select name="ano_curricular[]" id="ano_curricular" multiple class="selectpicker form-control"
                            data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true">
                            @foreach ($anoCurriculares as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 p-1">
                        <label for="inputState">Valor <span class="text-danger">*</span></label>
                        <input type="number" required class="form-control" id="valorPercentual" name="valorPercentual" placeholder="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="btn-submit" name="user_id">Guardar</button>
                    <button type="button" class="btn btn-primary" id="close_modal_create"
                        data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
        const table = $('table#table-rules');
        const selectArticle = $('select#emolument');
        const lectiveYear = $('select#lective_years');
        const modalRuleArticle = $('.modal#modalRuleArticle');
        const formRuleArticle = document.querySelector('.modal#modalRuleArticle form');

        loadArticleRules();

        function loadArticleRules() {

            let lective_year = lectiveYear.val();
            if (lective_year == "") return;

            let tam = table.children('tbody').length;
            if (tam > 0) table.DataTable().clear().destroy();

            table.DataTable({
                ajax: "/payments/getImplemtRulesAjax/" + lective_year,
                buttons: ['colvis', 'excel', {
                    text: '<i class="fas fa-plus"></i> Criar regra',
                    className: 'btn-primary main ml-1 rounded btn-main btn-text',
                    action: function(e, dt, node, config) {
                        modalRuleArticle.modal('show');
                        formRuleArticle.action = "{{ route('createRegraNew.emolumento', ['id' => $id_anolectivo]) }}";
                        formRuleArticle.method = "POST";
                        getAlunoAnoLectivo(lective_year);
                    }
                }],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'display_name',
                    name: 'display_name',
                }, {
                    data: 'mes',
                    name: 'mes',
                    render: function(data, type, row) { return getMes(data); }
                }, {
                    data: 'periodo_name',
                    name: 'periodo_name',
                }, {
                    data: 'ano_curricular',
                    name: 'ano_curricular',
                    searchable: false,
                    render: function(data, type, row) { return getAnoCurricular(data); }
                }, {
                    data: 'valor',
                    name: 'valor',
                    searchable: false,
                }, {
                    data: 'estado',
                    name: 'art_rule.estado',
                    visible: false,
                }, {
                    data: 'created_at',
                    name: 'art_rule.created_at',
                    visible: false
                }, {
                    data: 'actions',
                    name: 'actions',
                }],
            });

        }

        function getAlunoAnoLectivo(ano) {
            $.ajax({
                url: "/payments/getEmoluAnoletivo/" + ano,
            }).done(function(data) {
                selectArticle.empty();
                if (data.length > 0) {
                    $.each(data, function(index, item) {
                        selectArticle.append(`<option value="${item.id}">${item.current_translation.display_name}</option>`);
                    })
                }
                selectArticle.selectpicker('refresh');
            })
        }

        function getMes(mes) {
            switch (mes) {
                case "1":
                    return "Janeiro";
                case "2":
                    return "Fevereiro";
                case "3":
                    return "Março";
                case "4":
                    return "Abril";
                case "5":
                    return "Maio";
                case "6":
                    return "Junho";
                case "7":
                    return "Julho";
                case "8":
                    return "Agosto";
                case "9":
                    return "Setembro";
                case "10":
                    return "Outubro";
                case "11":
                    return "Novembro";
                default:
                    return "Dezembro";
            }
        }

        function getAnoCurricular(ano){
            switch (ano) {
                case "1":
                    return "Primeiro ano";
                case "2":
                    return "Segundo ano";
                case "3":
                    return "Terceiro ano";                                        
                case "4":
                    return "Quarto ano";                    
                default:
                    return "Quinto ano";
            }
        }

        lectiveYear.on('change', (e) => loadArticleRules());

        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
