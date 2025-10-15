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


@include('GA::library.modal.final')

@section('content')
    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0 ">
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

                            <div class="card-body form-css">


                                <form action="" method="post" autocomplete="off" id="formLivro" class="row">

                                    @csrf
                                    <div class="col-md-6">
                                        <label for="nomeAutor">Nome</label>
                                        <input type="text" class="form-control b-rad " placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="nomeAutor" required="" aria-required="Título do livro" name="nomeAutor">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sobrenomeAutor">Sobrenome</label>
                                        <input type="text" class="form-control b-rad" placeholder=""
                                            aria-label="Recipient's username" aria-describedby="button-addon2"
                                            id="sobrenomeAutor" required="" aria-required="Título do livro"
                                            name="sobrenomeAutor">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="generoAutor">Sexo</label>
                                        <select name="sexo" id="generoAutor" class="form-control b-rad" required="">
                                            <option value="Feminino">Feminino</option>
                                            <option value="Masculino">Masculino</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">País</label>
                                        <select name="" id="">
                                            @include('GA::library.paises')
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
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

    {{-- modal confirm --}}

    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.12.0/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js "></script>
    <script>
        function show(id) {
            document.getElementById(id).style.display = 'none';
        }

        // ========================================== Ocultar a barra de navega... ===================================
        // Validação do formulários para o cadastro de Editoras

        $('#formLivro').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#action').val();
            var titulo = $(this).find('input#f-titulo').val();
            var subtitlo = $(this).find('input#f-subtitulo').val();
            var autor = $(this).find('select#select-a-book').val();
            var editora = $(this).find('select#select-e-book').val();
            var categoria = $(this).find('select#select-c-book').val();
            var isbn = $(this).find('input#f-isbn').val();
            var ano = $(this).find('input#f-ano').val();
            var edicao = $(this).find('input#f-edicao').val();
            var local = $(this).find('input#f-local').val();
            var pagina = $(this).find('input#f-pagina').val();
            var idioma = $(this).find('input#f-idioma').val();
            var quantidade = $(this).find('input#f-quantidade').val();

            var autores = autor + "";
            var novo = autores.replace(/,/gi, "-");


            var array = [action, titulo, subtitlo, novo, editora, categoria, isbn, ano, edicao, local,
                pagina, quantidade, idioma
            ];


            var resultado = "";


            $.ajax({
                url: 'library-create-item/' + array,
                type: "get",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    },
                    500: function() {
                        alert("Contacta o apoio a forlearn");
                    }

                },
                success: function(response) {

                    // Recupera presentes no objecto response

                    resultado = response;

                    // Converte os dados num array

                    resultado = (resultado + "").split(',');

                    // Se a editora existe na base de dados

                    if (resultado[0] == "Livro existente") {

                        alert("Livro existente");
                    }

                    // Se o autor não existir, cadastra o autor
                    else if (resultado[0] == "sucesso") {

                        // alert("Livro cadastrado com sucesso" + resultado[1]);
                        window.location.href = "library-searchBooks";

                    }


                }

            });

        });
    </script>
@endsection
