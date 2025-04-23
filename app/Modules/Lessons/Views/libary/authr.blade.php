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

                            <div class="card-body form-css">

                                <form action="#" method="POST" autocomplete="off"
                                    id="formLivro">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="nome">Nome</label>
                                            <input type="text" class="form-control rounded b-rad " placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="nome" required="" aria-required="Título do livro"
                                                name="nome">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="sobrenome">Sobrenome</label>
                                            <input type="text" class="form-control rounded b-rad" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="sobrenome" required="" aria-required="Título do livro"
                                                name="sobrenome">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <label for="genero">Sexo</label>
                                            <select name="sexo" id="genero" class="form-control rounded b-rad"
                                                required="">
                                                <option value="Feminino">Feminino</option>
                                                <option value="Masculino">Masculino</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="paises">País</label>
                                            @include('GA::library.paises')
                                        </div>
                                    </div>
                                    <div class="mt-3 w-25">
                                        <label for="informacoes">Código do autor</label>
                                        <input type="text" class="form-control rounded b-rad" placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="informacoes" name="informacoes" required="">
                                    </div>
                                    <div class="mt-4">
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
