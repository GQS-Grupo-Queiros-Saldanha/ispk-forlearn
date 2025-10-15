<!--F4k3-->
@section('title', __('Biblioteca - ForLibrary'))
@extends('layouts.backoffice')

@section('styles')
    @parent
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .form-css {
            margin-bottom: 2%;
            border-top: 4px solid #076DF2;
            background-color: #1e1d1d0a;
        }
    </style>
@endsection


@section('content')
    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0;">
        @include('GA::library.modal.layout')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">

                        <h1 class="m-0 text-dark ">REGISTRA AUTOR</h1>

                    </div>

                </div>
            </div>
        </div>

        {{-- Main content --}}

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

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

                        <div class="card-body form-css">
                            <a href="{{ route('lessons.teste') }}"
                                class="btn btn-danger active text-white rounded">Voltar</a>

                            <form action="{{ route('lessons.edtStore') }}" method="POST" autocomplete="off" id="formLivro">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="nomeAutor">Nome</label>
                                        <input type="text" class="form-control rounded b-rad " placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="nomeAutor" required="" aria-required="Nome do Autor" name="nomeAutor">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sobrenomeAutor">Sobrenome</label>
                                        <input type="text" class="form-control rounded b-rad " placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="sobrenomeAutor" required="" aria-required="sobrenome do Autor"
                                            name="sobrenomeAutor">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="generoAutor">Genero</label>
                                        <select class="form-control rounded b-rad " name="generoAutor" id="generoAutor">
                                            @foreach ($masculinos as $masculino)
                                                <option selected value="{{ $masculino->id }}">Masculino</option>
                                            @endforeach
                                            @foreach ($femininos as $feminino)
                                                <option value="{{ $feminino->id }}">Feminino</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="paises">País</label>
                                        @include('GA::library.paises')
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label for="codigoAutor">Codigo do Autor</label>
                                        <input type="text" class="form-control rounded b-rad " placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="codigoAutor" required="" aria-required="Codigo do Autor"
                                            name="codigoAutor">
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <button type="submit" {{-- data-toggle="modal" data-target="#modalLivro" --}} class="btn btn-success s-e criarlivro"
                                        style="border-radius: 5px;; letter-spacing: 1px;"><i class="fas fa-plus"
                                            style="font-size: 12px;margin-right:5px;"> </i> Criar</button>

                                    <button type="reset" data-toggle="modal" data-target="#modalExemplo"
                                        class="btn btn-secondary"
                                        style="border-radius: 5px;; letter-spacing: 1px;margin-left: 10px;">Limpar</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        function show(id) {
            document.getElementById(id).style.display = 'none';
        }
    </script>

@endsection
