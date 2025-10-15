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
    <li class="breadcrumb-item active" aria-current="page">Regra - Bloqueio</li>
@endsection
@section('body')
    <div class=" p-3 mb-2 bg-body rounded text-end" style="box-shadow: #00000021 0px 2px 6px 0px;">
        <h1 style="font-size: 1pc;text-align: right" class="col text-end p-0 m-0">Regra de Bloqueio</h1>
    </div>
    <div class=" p-3 pt-4 mb-4 bg-body rounded" style="box-shadow: #00000026 0px 2px 6px 0px;">
        <div class="row">
            <div class="col-6  mt-2 m-0 mb-5">
                <form method="POST" action="{{ route('config.divida.create') }}">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">Quantidade de meses em atraso</label>
                        <input required type="number" min="0" name="qdt_divida" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Quantidade de dias de excepção em atraso </label>
                        <input type="number" min="0" name="qdt_dias" class="form-control">
                    </div>
                    <button type="submit" style="background: #1290f8" class="btn text-white rounded">Gravar regra</button>
                </form>
            </div>
            <div class="col-6">
                <header class="pb-1 mb-1 border-bottom">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none">
                        <span class="fs-4">Informação</span>
                    </a>
                </header>
                <div class="p-1 bg-light rounded">
                    <div class="container-fluid py-2 pb-4">
                        <h1 style="font-size: 0.8pc; font-weight: normal" class="display-5 ">Caro Utilizador
                            <b>{{ auth()->user()->name }}</b></h1>
                        <p style="fon" class="col-md fs-4">Neste formulário pode determinar a quantidade de meses e dias
                            em atraso (Propina) que os estudantes podem ter.
                            <br>
                            Esta configuração determina o acesso/bloqueio à instituição.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-0 m-0 mt-4 mb-3">
            <table id="list-configDivida-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Quantidade de meses e</th>
                        <th>Dias de excepção</th>
                        <th>@lang('common.created_by')</th>
                        <th>@lang('common.updated_by')</th>
                        <th>@lang('common.created_at')</th>
                        <th>@lang('common.updated_at')</th>
                        <th>@lang('common.actions')</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    <div class="modal fade" id="delete-configuracao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Informação!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Caro utilizador deseja eliminar esta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="formRoute-delete-confiDivida" method="POST" action="">
                        @csrf
                        <input type="hidden" name="getId" id="getId">
                        <button type="submit" class="btn btn-primary">Ok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            $('#list-configDivida-table').DataTable({
                ajax: '{!! route('config_divida.ajax') !!}',
                buttons: [
                    'colvis'
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'qtd_divida',
                    name: 'qtd_divida',
                }, {
                    data: 'dias_exececao',
                    name: 'dias_exececao'
                }, {
                    data: 'created_by',
                    name: 'created_by'
                }, {
                    data: 'updated_by',
                    name: 'updated_by'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'updated_at',
                    name: 'updated_at'
                }, {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',

                }
            });
        });
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
