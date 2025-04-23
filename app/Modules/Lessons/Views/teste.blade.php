@section('title', __('Lessons::lessons.lessons'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('Lessons::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Formulário Inserir</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('lessons') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{-- <a href="{{ route('lessons.create') }}" class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-plus-square"></i>
                            @lang('common.new')
                        </a> --}}

                        <div class="card">
                            <div id="btn-close" class="card-body">
                                @if (session('valido'))
                                    <div class="alert alert-success">
                                        {{ session('valido') }}
                                        <button type="button" class="close" onclick="show('btn-close');"
                                            aria-label="close">
                                            <span aria-hidden="true">x</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('invalido'))
                                    <div class="alert alert-danger">
                                        {{ session('invalido') }}
                                        <button type="button" class="close" onclick="show('btn-close');"
                                            aria-label="close">
                                            <span aria-hidden="true">x</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <a href="{{ route('lessons.index-teste') }}" class="btn btn-outline-success">Listar</a>
                                <a href="{{ route('lessons.editora') }}" class="btn btn-outline-warning">Editora</a>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('lessons.store') }}" class="row" method="POST">
                                    @csrf
                                    <div class="col-md-3">
                                        <label for="ano">Ano de Frequência:</label>
                                        <input class="form-control" type="number" name="ano"
                                            placeholder="Ano de frequência" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cadeira">Cadeira:</label>
                                        <input class="form-control" type="text" name="cadeira"
                                            placeholder="Nome da Cadeira" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="professor">Docente:</label>
                                        <input class="form-control" type="text" name="professor"
                                            placeholder="Nome do Docente" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="horario">Horário:</label>
                                        <input class="form-control" type="text" name="horario"
                                            placeholder="Digite os Horários" required>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <button type="submit" class="btn btn-primary">Inserir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent

    <script>
        function show(id) {
            document.getElementById(id).style.display = 'none';
        }

        $(function() {
            $('#disciplina-table').DataTable({
                ajax: '{!! route('lessons.index-ajax') !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'ano',
                    name: 'ano',
                }, {
                    data: 'cadeira',
                    name: 'cadeira'
                }, {
                    data: 'professor',
                    name: 'professor'
                }, {
                    data: 'horario',
                    name: 'horario'
                }, {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>


@endsection
