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
                        @isset($aulas)
                            <h1>Formulário Editar</h1>
                        @endisset
                        @isset($view)
                            <h1>Formulário Visualizar</h1>
                        @endisset
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
                        </div>
                        <div class="card-body">
                            @isset($aulas)
                                <a href="{{ route('lessons.index-teste') }}" class="btn btn-outline-primary mb-3">Voltar</a>


                                <form action="{{ route('lessons.teste.update', $aulas->id) }}" class="row" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-3">
                                        <label for="ano">Ano de Frequência:</label>
                                        <input class="form-control" type="number" name="ano" value="{{ $aulas->ano }}"
                                            placeholder="Ano de frequência">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cadeira">Cadeira:</label>
                                        <input class="form-control" type="text" name="cadeira" value="{{ $aulas->cadeira }}"
                                            placeholder="Nome da Cadeira">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="professor">Docente:</label>
                                        <input class="form-control" type="text" name="professor"
                                            value="{{ $aulas->professor }}" placeholder="Nome do Docente">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="horario">Horário:</label>
                                        <input class="form-control" type="text" name="horario" value="{{ $aulas->horario }}"
                                            placeholder="Digite os Horários">
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <button type="submit" class="btn btn-primary">Atualizar</button>
                                    </div>
                                </form>
                            @endisset

                            @isset($view)
                                <a href="{{ route('lessons.index-teste') }}" class="btn btn-outline-primary mb-3">Voltar</a>


                                <form action="{{ route('lessons.teste-show', $view->id) }}" class="row" method="POST">
                                    @csrf
                                    <div class="col-md-3">
                                        <label for="ano">Ano de Frequência:</label>
                                        <input class="form-control" type="number" name="ano" value="{{ $view->ano }}"
                                            disabled placeholder="Ano de frequência">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cadeira">Cadeira:</label>
                                        <input class="form-control" type="text" name="cadeira" value="{{ $view->cadeira }}"
                                            disabled placeholder="Nome da Cadeira">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="professor">Docente:</label>
                                        <input class="form-control" type="text" name="professor"
                                            value="{{ $view->professor }}" disabled placeholder="Nome do Docente">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="horario">Horário:</label>
                                        <input class="form-control" type="text" name="horario" value="{{ $view->horario }}"
                                            disabled placeholder="Digite os Horários">
                                    </div>
                                </form>
                            @endisset

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
    </script>

@endsection
