<title>Gerir regime especial | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Gerir regime especial')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('requests.index') }}" class="">
            Tesouraria
        </a>        
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Gerir regime especial</li>
@endsection
@section('body')
    <table id="table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Estudante</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Rotacao</th>
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
        $(function() {
            $('#table').DataTable({
                ajax: '{!! route('regime_especial.ajax') !!}',
                buttons: ['colvis', 'excel',{
                        text: '<i class="fas fa-file-pdf"></i> Lista de Estudantes em Regime Especial',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            let url = "pdf_regime_especial"
                            window.open(url, "_blank");
                        }
                    }
                    ,{
                        text: '<i class="fas fa-plus"></i> Adicionar nova rotação',
                        className: 'btn-primary main ml-1 rounded btn-main btn-text',
                        action: function(e, dt, node, config) {
                            let url = "rotacao-regime-especial"
                            window.open(url, "_blank");
                        }
                    }
                ],
                columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'value',
                    name: 'u_p.value',

                }, {
                    data: 'name',
                    name: 'users.name'
                }, {
                    data: 'email',
                    name: 'users.email',

                }, {
                    data: 'display_name',
                    name: 'ct.display_name',

                }, {
                    data: 'nome',
                    name: 'rotacao.nome',

                }],

                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }
            });
        });
    </script>
@endsection
