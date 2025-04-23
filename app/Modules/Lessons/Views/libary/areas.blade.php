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

                        <h1 class="m-0 text-dark ">REGISTRA AREAS</h1>

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
                                        <div class="col-md-3">
                                            <label for="descricao">CDD / CDU ( ex: 120 )</label>
                                            <input type="text" class="form-control b-rad " placeholder="" aria-label="Recipient's username" aria-describedby="button-addon2" name="descricao" id="descricao" required="">
                                        </div>
                                        <div class="col-md-9">
                                            <label for="nome">Nome</label>
                                            <input type="text" class="form-control b-rad" placeholder="" aria-label="Recipient's username" aria-describedby="button-addon2" name="nome" id="nome" required="">
                                        </div>
                                    </div>
                                   
                                    <div class="mt-4">
                                        <button type="submit" {{-- data-toggle="modal" data-target="#modalLivro" --}} class="btn btn-success s-e criarlivro"
                                            style="border-radius: 5px;; letter-spacing: 1px;"><i class="fas fa-plus"
                                                style="font-size: 12px;margin-right:5px;"> </i> Criar</button>
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
