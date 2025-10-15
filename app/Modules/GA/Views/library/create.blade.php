<!--F4k3-->
@section('title', __('Biblioteca - ForLibrary'))
@extends('layouts.backoffice')

@section('styles')
    @parent
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

<!-- Temas em destaque -->
<style>
    .form-css {
        margin-bottom: 2%;
        border-top: 4px solid #076DF2;
        background-color: #1e1d1d0a;
    }

    .modal-css .row {
        width: 100%;
    }


    .editora .dropdown-menu,
    .categoria .dropdown-menu {
        min-width: none !important;
    }

    .modal-css .modal-header {
        background: aliceblue;
        border-bottom: none;
        position: relative;
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 1px;
    }

    .modal-css .modal-header h5 {
        margin-left: 15px;
        font-weight: bold;
    }

    .modal-css .modal-content {
        padding: 20px;
        border-radius: 10px;

    }

    .modal-css .modal-dialog {
        margin-top: 80px;
        min-width: 1000px;
    }

    .modal-css .b-rad {
        height: 40px !important;
        font-size: 16px !important;
    }

    .modal-css .close {
        color: black;
    }
</style>


{{-- @include('GA::library.modal.final') --}}

@section('content')
    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0 ">
        @include('GA::library.modal.layout')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">

                        <h1 class="m-0 text-dark ">@lang('REGISTRAR LIVRO')</h1>

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


                                {!! Form::open(['route' => ['library-create-item']]) !!}

                                @csrf

                                <input type="text" class="form-control d-none" placeholder=""
                                    aria-label="Recipient's username" aria-describedby="button-addon2" id="action"
                                    required aria-required="Título do livro" name="action" value="livro">

                                <div class="row">

                                    <div class="col-5">
                                        <label class="">Título</label>

                                        <div class="input-group mb-3">

                                            <input type="text" class="form-control" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="titulo" name="titulo" required aria-required="Título do livro">

                                            {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <label class="">Subtítulo</label>

                                        <div class="input-group mb-3">

                                            <input type="text" class="form-control" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="subtitulo" name="subtitulo" aria-required="Título do livro">

                                            {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                        </div>
                                    </div>

                                </div>

                                <div class="row">


                                    <div class="col-5">

                                        <label for="">Autor </label>
                                        <div class="input-group mb-3 ">

                                            {{-- <select name="" id="select-a-book" class="form-control autor"> --}}
                                            <select name="autor[]" id="autor" multiple
                                                class="selectpicker form-control autor" data-actions-box="true"
                                                data-selected-text-format="count > 3" data-live-search="true" required>

                                                @foreach ($autores as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name . ' ' . $item->surname }}
                                                    </option>
                                                @endforeach


                                            </select>
                                            <button type="button" class="btn btn-success btn-add-autor ocultar"
                                                data-toggle="modal" data-target="#modalAutor"
                                                style="border-radius: 0px 5px 5px 0px; letter-spacing: 1px;display: none;"
                                                title="Criar Autor"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>

                                    <div class="col-5">

                                        <label for="">Editora</label>
                                        <div class="input-group mb-3">
                                            <select name="editora" id="editora" class="selectpicker form-control editora"
                                                data-actions-box="true" data-selected-text-format="count > 3"
                                                data-live-search="true" required>
                                                <option value=""></option>
                                                @foreach ($editoras as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-5">

                                        <label for="">Área </label>
                                        <div class="input-group mb-3">

                                            <select name="area" id="area"
                                                class="selectpicker form-control categoria" data-actions-box="true"
                                                data-selected-text-format="count > 3" data-live-search="true" required>
                                                <option value=""></option>

                                                @foreach ($categorias as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-success btn-add-categoria "
                                                data-toggle="modal" data-target="#modalCategoria"
                                                style="border-radius: 0px 5px 5px 0px; letter-spacing: 1px;display: none;"
                                                title="Criar categoria"><i class="fas fa-plus"></i></button>
                                        </div>

                                    </div>

                                    <div class="col-5">



                                        <div class="row">

                                            <div class="col-6">
                                                <label class="">ISBN</label>

                                                <div class="input-group mb-3">

                                                    <input type="text" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="isbn" id="isbn" min="1">

                                                    {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                                </div>

                                            </div>

                                            <div class="col-3">
                                                <label class="">Ano lançamento</label>

                                                <div class="input-group mb-3">

                                                    <input type="number" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="ano" id="ano" min="1300"
                                                        max="@php echo date('Y'); @endphp">

                                                    {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                                </div>

                                            </div>


                                            <div class="col-3">
                                                <label class="">Edição</label>

                                                <div class="input-group mb-3">

                                                    <input type="number" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="edicao" id="edicao" min="1">

                                                    {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                                </div>

                                            </div>


                                        </div>
                                    </div>

                                </div>


                                <div class="row">

                                    <div class="col-5">

                                        <label for="">Local de Lançamento</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                name="local" id="local" required aria-required="Título do livro">
                                        </div>

                                    </div>

                                    <div class="col-5">



                                        <div class="row">

                                            <div class="col-6">
                                                <label class="">Número de chamada</label>

                                                <div class="input-group mb-3">

                                                    <input type="text" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="idioma" id="idioma">

                                                    {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                                </div>

                                            </div>



                                            <div class="col-3">
                                                <label class="">Páginas</label>

                                                <div class="input-group mb-3">

                                                    <input type="number" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="pagina" id="pagina" min="10" max="3000">

                                                    {{-- <button class="btn btn-outline-primary" type="button" id="pesquisa">Pesquisar</button> --}}
                                                </div>

                                            </div>

                                            <div class="col-3">
                                                <label class="">Quantidade</label>

                                                <div class="input-group mb-3">

                                                    <input type="number" class="form-control" placeholder=""
                                                        aria-label="Recipient's username" aria-describedby="button-addon2"
                                                        name="quantidade" id="quantidade" min="1">


                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>


                                <div class="row">
                                    <br>
                                </div>

                                <div class="row">

                                    <div class="col-6">
                                        <button type="submit" class="btn btn-success s-e criarlivro"
                                            style="border-radius: 5px;; letter-spacing: 1px;"><i class="fas fa-plus"
                                                style="font-size: 12px;margin-right:5px;"> </i> Criar</button>

                                        <button type="reset" data-toggle="modal" data-target="#modalExemplo"
                                            class="btn btn-secondary"
                                            style="border-radius: 5px;; letter-spacing: 1px;margin-left: 10px;">Limpar</button>
                                    </div>
                                </div>

                                {!! Form::close() !!}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.12.0/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js "></script>
    <script>
        // ========================================== Ocultar a barra de navega... ===================================

    </script>
@endsection
