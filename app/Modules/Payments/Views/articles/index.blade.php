<title>Tesouraria | forLEARN by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', __('Payments::articles.articles'))
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="/">Home</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('requests.index') }}" class="">
        Tesouraria
    </a>
</li>
<li class="breadcrumb-item active" aria-current="page">Emolumentos - Propinas</li>
@endsection
@section('selects')
<div class="mb-2 mt-3">
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
<form action="{{ route('articles.duplicar') }}" method="post">
    @csrf

    <table id="articles-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th><i class="fa fa-check-square" aria-hidden="true"></i></th>
                <th>@lang('common.code')</th>
                <th>@lang('translations.display_name')</th>
                <th>@lang('Payments::articles.base_value')</th>
                <th>@lang('categoria')</th>
                <th>@lang('sigla')</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>@lang('common.actions')</th>
            </tr>
        </thead>
    </table>
    <div class="modal fade" id="modal-copiar-article" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg rounded mt-5" role="document">
            <div class="modal-content rounded" style="background-color: #e9ecef">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLongTitle">Informação</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <p class="lead">Caro utilizador/ {{ auth()->user()->name }} pretende copiar a configuração
                        deste(s) emolumentos/propina.</p>
                    <hr class="my-4">
                    <p>Por favor seleciona o ano lectivo que pretende colar a copia das configurações dos
                        emolumentos/propinas checadas</p>
                    <div class="col-6 mt-2 mb-4 pl-0" style="width:200px; !important">
                        <label> Ano lectivo </label>
                        <select name="lective_years" id="lective_years"
                            class="selectpicker form-control form-control-sm" style="width: 100%; !important">
                            @foreach ($lectiveYears as $lectiveYear)
                                @if ($lectiveYearSelected == $lectiveYear->id)
                                    <option value="{{ $lectiveYear->id }}" selected>
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @else
                                    <option value="{{ $lectiveYear->id }}">
                                        {{ $lectiveYear->currentTranslation->display_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button style="border-radius: 6px; background: #20c7f9" type="submit"
                        class="btn btn-lg text-white mt-2">Gravar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 div-btn-duplicar" hidden>
        <button data-toggle="modal" data-target="#modal-copiar-article" type="button" class="btn btn-primary "><i
                class="fas fa-copy"></i> Duplicar</button>
    </div>
</form>
@endsection
@section('models')
@include('layouts.backoffice.modal_confirm')

<div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Duplicar emolumentos / propinas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="{{ route('duplicate.articles') }}" method="POST">
                    @csrf
                    <div hidden>
                        <input type="text" id="article_id" name="id">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="code">Código</label>
                                <input type="text" id="code" name="code" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="base_value">Valor Base</label>
                                <input type="number" id="base_value" name="base_value" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="Nome">Nome</label>
                                <input type="text" id="name" name="display_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="base_value">Descrição</label>
                                <input type="text" id="description" name="description" class="form-control" required>
                            </div>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts-new')
@parent
<script>
    var lective_year = $("#lective_years");
    var criarEmolumento = document.getElementById("criarEmolumento");
    var getCheckBox = true;
    const table = $('#articles-table');

    loadData('{!! route('articles.ajax') !!}');

    function loadData(url) {
        let tam = table.children('tbody').length;
        if (tam > 0) table.DataTable().clear().destroy();
        table.DataTable({
            "ajax": {
                "url": url,
                "type": "GET",
                "data": {
                    "user_id": 451
                }
            },
            buttons: ['colvis', 'excel', {
                text: '<i class="fas fa-plus"></i> Novo',
                className: 'btn-primary main ml-1 rounded btn-main btn-text',
                action: function (e, dt, node, config) {
                    const url = '/payments/createEmolimento/' + lective_year.val();
                    window.open(url, "_blank");
                }
            },
                @if (auth()->user()->hasAnyPermission(['creat-rule']))
                                    {
                        text: '<i class="fas fa-edit"></i> Implementar regras',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function (e, dt, node, config) {
                            const url = '/payments/implementar_regra/' + lective_year.val();
                            window.open(url, "_blank");
                        }
                    },
                @endif
                    @if (auth()->user()->hasRole(['superadmin']))
                                                {
                            text: '<i class="fas fa-edit"></i> Consulta Dev',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text',
                            action: function (e, dt, node, config) {
                                window.open("{{ route('update.Transion') }}", "_blank");
                            }
                        }, {
                            text: '<i class="fa-solid fa-file-pdf"></i> Tabela de Emolumentos',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text',
                            action: function (e, dt, node, config) {
                                const url = '#' + lective_year.val();
                                window.open("{{ route('articles.gerarPDF') }}", "_blank");
                            }
                        }, {
                            text: '<i class="fa-solid fa-layer-group"></i> Categorias',
                            className: 'btn-primary main ml-1 rounded btn-main btn-text',
                            action: function (e, dt, node, config) {
                                const url = '#' + lective_year.val();
                                window.open("{{ route('articles.categoria') }}", "_blank");
                            }
                        }
                    @endif
            ],
            columns: [{
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false
            },
            {
                data: 'code',
                name: 'code',
                visible: false
            }, {
                data: 'display_name',
                name: 'at.display_name'
            }, {
                data: 'base_value',
                name: 'base_value'
            }, {
                data: 'category_name',
                name: 'category_name',
                searchable: false
            }, {
                data: 'acronym',
                name: 'acronym',
                searchable: false
            },
            {
                data: 'created_by',
                name: 'u1.name',
                visible: false
            }, {
                data: 'updated_by',
                name: 'u2.name',
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
                name: 'action',
                orderable: false,
                searchable: false
            }
            ],
            columnDefs: [{
                targets: [1],
                render: function (data, type, row, meta) {

                    if (meta.col == 1) {
                        var id_article = meta.settings.aoData[meta.row]._aData.id
                        getCheckBox = false
                        return '<input type="checkbox" onClick="checkedBoxArticle(' + id_article +
                            ')" name="articleCopy[]" class="articleCopy" value="' + id_article +
                            '">';
                    }

                }
            }],
            "lengthMenu": [
                [10, 50, 100, 50000],
                [10, 50, 100, "Todos"]
            ],
            language: {
                url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
            }
        });
    }

    lective_year.change(function () {
        loadData("/payments/articles-by-year/" + lective_year.val());
    })


    function checkedBoxArticle(article) {
        $(".articleCopy").change(function (e) {
            var checagem_article = $(".articleCopy").is(':checked');
            if (checagem_article == true) {
                $(".div-btn-duplicar").attr('hidden', false)
            } else {
                $(".div-btn-duplicar").attr('hidden', true)
            }

        });
    }


    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
</script>
@endsection