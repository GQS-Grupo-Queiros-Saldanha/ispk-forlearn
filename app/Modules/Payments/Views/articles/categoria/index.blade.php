<title>Tesouraria | forLEARN® by GQS</title>

@extends('layouts.generic_index_new')
@section('page-title', 'Categorias Dos Emolumentos')
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
    <li class="breadcrumb-item active" aria-current="page">Categorias</li>
@endsection
@section('body')
    <table id="users-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Nome da categoria</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_at')</th>
                <th>Ações</th>

            </tr>
        </thead>
    </table>

    <div class="modal" id="modalCategoria" tabindex="-1" role="dialog">
        <form class="modal-dialog" role="document" id="form" action="{{ route('articles.categoria.store') }}"
            method="POST">
            @csrf
            @method('POST')
            <input type="hidden" name="chave" id="chave" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Categoria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning alert-dismissible fade show d-none" role="alert" id="alert">
                        <span id="alert-message"></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="name">Nome da Categoria</label>
                        <input type="text" name="name" id="categoria_name" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-categoria-action">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('hiddens')
    <input type="hidden" name="" value="{{ route('articles.categoria.ajax_list') }}" id="ajax_categoria_list">
@endsection

@section('scripts-new')
    @parent
    <script>
        const form = $('#form');
        const formMethod = $("[name='_method']");
        const btnCategoria = $('#btn-categoria');
        const modalCategoria = $('#modalCategoria');
        const categoria = $('#categoria_name');
        const btnCategoriaAction = $('#btn-categoria-action');
        const alert = $('#alert');
        const alertMessage = $('#alert-message');

        btnCategoria.click((e) => {
            form.trigger("reset");
            modalCategoria.modal('show');
            form.attr('action', '#');
            formMethod.val('POST');
            console.log("action");
        });

        $('#modalCategoria').on('hidden.bs.modal', function() {
            form.trigger("reset");
            alert.addClass('d-none');
        });

        function reloadDatas() {
            let table = $('#users-table');
            let tam = table.children('tbody').length;
            if (tam > 0) table.DataTable().clear().destroy();
            table.DataTable({
                ajax: `${$('#ajax_categoria_list').val()}`,
                buttons: ['colvis', 'excel', {
                    text: '<i class="fas fa-plus-square"></i> Criar nova Categoria',
                    className: 'btn-primary main ml-1 rounded',
                    action: function(e, dt, node, config) {
                        modalCategoria.modal('show');
                    }
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        searchable: true
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by',
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        searchable: true
                    },
                    {
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
        reloadDatas();
    </script>
@endsection
