<!--F4k3-->
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

<title>Biblioteca | forLEARN® by GQS</title>
<!-- Temas em destaque -->

<style>
    .left-side-menu a {
        color: #1e1e1e;
    }

    .card-box {
        position: relative;
        color: #fff;
        padding: 20px 10px 40px;
        margin: 20px 0px;
    }

    .card-box:hover {
        text-decoration: none;
        color: #f1f1f1;
    }

    .card-box:hover .icon i {
        font-size: 100px;
        transition: 1s;
        -webkit-transition: 1s;
    }

    .card-box .inner {
        padding: 5px 10px 0 10px;
    }

    .card-box h3 {
        font-size: 27px;
        font-weight: bold;
        margin: 0 0 8px 0;
        white-space: nowrap;
        padding: 0;
        text-align: left;
    }

    .card-box p {
        font-size: 15px;
    }

    .card-box .icon {
        position: absolute;
        top: auto;
        bottom: 5px;
        right: 5px;
        z-index: 0;
        font-size: 72px;
        color: rgba(0, 0, 0, 0.15);
    }

    .card-box .card-box-footer {
        position: absolute;
        left: 0px;
        bottom: 0px;
        text-align: center;
        padding: 3px 0;
        color: rgba(255, 255, 255, 0.8);
        background: rgba(0, 0, 0, 0.1);
        width: 100%;
        text-decoration: none;
        font-size: 16px !important;
    }

    .card-box:hover .card-box-footer {
        background: rgba(0, 0, 0, 0.3);
    }

    .bg-blue {
        background-color: #00c0ef !important;
    }

    .bg-green {
        background-color: #00a65a !important;
    }

    .bg-orange {
        background-color: #f39c12 !important;
    }

    .bg-red {
        background-color: #d9534f !important;
    }

    .bg-violtet {
        background-color: #4000ff;
    }

    .bg-grey {
        background-color: #8492a7;
    }

    .bg-rb {
        background-color: #281f5f;
    }

    .bg-v {
        background-color: #743a8f;
    }

    .card-box .inner p {
        font-size: 20px;
    }

    .countdown {
        text-transform: uppercase;
        font-weight: bold;
    }

    .countdown span {
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        font-size: 3rem;
        margin-left: 0.8rem;
    }

    .countdown span:first-of-type {
        margin-left: 0;
    }

    .countdown-circles {
        text-transform: uppercase;
        font-weight: bold;
    }

    .countdown-circles span {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    }

    .countdown-circles span:first-of-type {
        margin-left: 0;
    }


    /*
*
* ==================================================
* FOR DEMO PURPOSES
* ==================================================
*
*/


    .bg-gradient-1 {
        background: #7f7fd5;
        background: -webkit-linear-gradient(to right, #7f7fd5, #86a8e7, #91eae4);
        background: linear-gradient(to right, #7f7fd5, #86a8e7, #91eae4);
    }

    .bg-gradient-2 {
        background: #654ea3;
        background: -webkit-linear-gradient(to right, #654ea3, #eaafc8);
        background: linear-gradient(to right, #654ea3, #eaafc8);
    }

    .bg-gradient-3 {
        background: #ff416c;
        background: -webkit-linear-gradient(to right, #ff416c, #ff4b2b);
        background: linear-gradient(to right, #ff416c, #ff4b2b);
    }

    .bg-gradient-4 {
        background: #007991;
        background: -webkit-linear-gradient(to right, #007991, #78ffd6);
        background: linear-gradient(to right, #007991, #78ffd6);
    }

    .rounded {
        border-radius: 1rem !important;
    }

    .btn-demo {
        padding: 10px !important;
        border-radius: 30rem !important;
        background: rgba(255, 255, 255, 0.3);
        color: #fff;
        text-transform: uppercase;
        font-weight: bold !important;
    }

    .btn-demo:hover,
    .btn-demo:focus {
        color: #fff;
        background: rgba(255, 255, 255, 0.5);
    }
</style>

<style>
    /* Isso foi adicionado depois da criação do vídeo apenas para repetir vários containers */
    .wrappers {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        grid-gap: 50px;
        padding: 50px;
    }

    /* Início do código descrito no vídeo */
    .containers {
        width: 100%;
        height: 300px;
        background: #dadada;
        font-family: sans-serif;
        font-size: 16px;
        position: relative;
        box-shadow: -2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .labels {
        position: absolute;
        top: 20px;
        right: -16px;
        padding: 5px 20px;
        color: #fafafa;
        background: teal;
        font-weight: 700;
        max-width: 70%;
        /* Ajustado para textos muito grandes */
        box-shadow: -2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .labels::after {
        position: absolute;
        content: '';
        bottom: -6px;
        right: 0;
        border-style: solid;
        border-top-width: 3px;
        border-left-width: 8px;
        border-bottom-width: 3px;
        border-right-width: 8px;
        border-top-color: teal;
        border-left-color: teal;
        border-bottom-color: transparent;
        border-right-color: transparent;
        filter: brightness(50%);
    }

    /* Fim do código descrito no vídeo */

    /* Se quiser mudar a cor (essa classe depende de .label) */
    .label-1 {
        background: rgb(2, 58, 58);
        color: #ffffff;
    }

    .label-1::after {
        border-top-color: rgb(2, 58, 58);
        border-left-color: rgb(2, 58, 58);
        filter: brightness(80%);
    }

    .label-2 {
        background: #076DF2;
        color: #ffffff;
    }

    .label-2::after {
        border-top-color: rgb(2, 58, 58);
        border-left-color: rgb(2, 58, 58);
        filter: brightness(80%);
    }

    /* Se quiser mudar a cor (essa classe depende de .label) */
    .label-3 {
        background: rgb(2, 10, 83);
        color: #ffffff;
    }

    .label-3::after {
        border-top-color: rgb(2, 10, 83);
        border-left-color: rgb(2, 10, 83);
        filter: brightness(50%);
    }

    /* Com conteúdo no container */
    .container-contents {
        overflow: hidden;
        width: 100%;
        height: 100%;
    }

    .container-contents img {
        max-width: 100%;
        height: auto;
    }

    .container-contents:hover .container-subtitles {
        opacity: 1;
    }

    .container-subtitles {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
        padding: 20px;
        color: #f9f9f9;
        font-size: 14px;
        opacity: 0;
        transition: opacity 600ms ease-in-out;
    }

    .container-subtitles h2 {
        margin: 0 0 10px;
        font-size: 16px;
    }

    .container-subtitles p {
        margin: 0;
        font-size: 14px;
    }

    /* Na esquerda (essa classe depende de .label) */
    .labels-left {
        top: auto;
        right: auto;
        left: -16px;
        bottom: 50px;
    }

    .label-lefts::after {
        right: auto;
        left: 0;
        border-top-color: teal;
        border-left-color: transparent;
        border-bottom-color: transparent;
        border-right-color: teal;
    }

    /* Na esquerda e direita (essa classe NÃO DEPENDE de .label) */
    .label-boths {
        position: absolute;
        font-weight: bold;
        padding: 10px 20px;
        text-align: center;
        top: 20px;
        right: -16px;
        left: -16px;
        background: chartreuse;
        color: rgba(0, 0, 0, 0.7);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .label-boths::before,
    .label-boths::after {
        position: absolute;
        content: '';
        bottom: -6px;
        border-style: solid;
        border-top-width: 3px;
        border-left-width: 8px;
        border-bottom-width: 3px;
        border-right-width: 8px;
        filter: brightness(80%);
    }

    .label-boths::after {
        right: 0;
        border-top-color: chartreuse;
        border-left-color: chartreuse;
        border-bottom-color: transparent;
        border-right-color: transparent;
    }

    .label-boths::before {
        left: 0;
        border-top-color: chartreuse;
        border-left-color: transparent;
        border-bottom-color: transparent;
        border-right-color: chartreuse;
    }

    .carousel-item {
        min-height: 280px;
    }
</style>

@section('content')

    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0;height: 99vh;">

        @include('GA::library.modal.layout')
        {{-- @include('GA::library.modal.modal') --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <br>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-3">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark" style="text-align: center;font-weight: 700;">@lang('Biblioteca')</h1>
                    </div>
                    <div class="col-sm-3">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>
                </div>
                {{-- <div class="row">

                    <div class="col-1">

                    </div>

                    <div class="col-10">
                        <img src="https://en.unesco.org/sites/default/files/styles/img_688x358/public/courier/photos/shutterstock_book-day-.jpg?itok=awCCawBl"
                            alt="..." class="img-thumbnail rounded mx-auto d-block">

                    </div>

                    <div class="col-1">

                    </div>
                </div> --}}
            </div>
        </div>



        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">




                    </div>
                </div>
            </div>
        </div>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        </head>


        <div class="col-12">


            <div class="row">

                <div class="col-lg-2">

                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-blue">
                        <div class="inner">
                            <h3> {{ $autores }} </h3>
                            <p> Autores </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                        auth()->user()->hasRole(['superadmin']))
                        <a href="{{ route('library-searchBooks') }}" class="card-box-footer">Ver mais <i
                                class="fa fa-arrow-circle-right"></i></a>@endif
                    </div>
                </div>


                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-green">
                        <div class="inner">
                            <h3> {{ $categorias }} </h3>
                            <p> Áreas </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                        auth()->user()->hasRole(['superadmin']))
                        <a href="{{ route('library-searchBooks') }}" class="card-box-footer">Ver mais <i
                                class="fa fa-arrow-circle-right"></i></a>@endif
                    </div>
                </div>
                <div class="col-lg-2">

                </div>
                <div class="col-lg-2">

                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-orange">
                        <div class="inner">
                            <h3> {{ $computadores }} </h3>
                            <p> Computadores </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-desktop" aria-hidden="true"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                        auth()->user()->hasRole(['superadmin']))
                        <a href="{{ route('library-searchBooks') }}" class="card-box-footer">Ver mais <i
                                class="fa fa-arrow-circle-right"></i></a>
                                @endif
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-red">
                        <div class="inner">
                            <h3> {{ $editoras }} </h3>
                            <p> Editoras </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-edit"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                            auth()->user()->hasRole(['superadmin']))
                            <a href="{{ route('library-searchBooks') }}" class="card-box-footer">Ver mais <i
                                    class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2">

                </div>
                <div class="col-lg-2">

                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-violtet">
                        <div class="inner">
                            <h3> {{ $livros }} </h3>
                            <p> Livros </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-book"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                            auth()->user()->hasRole(['superadmin']))
                            <a href="{{ route('library-searchBooks') }}" class="card-box-footer">Ver mais <i
                                    class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6">
                    <div class="card-box bg-v">
                        <div class="inner">
                            <h3> {{ $requisicao }} </h3>
                            <p> Requisições </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-handshake-o"></i>
                        </div>
                        @if (auth()->user()->hasAnyPermission(['library_manage_item']) ||
                            auth()->user()->hasRole(['superadmin']))
                            <a href="{{ route('library-searchLoan') }}" class="card-box-footer">Ver mais <i
                                    class="fa fa-arrow-circle-right"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2">

                </div>
                <div class="col-lg-2">

                </div>


                {{-- <div class="col-lg-4 col-sm-6">
                <div class="card-box bg-grey">
                    <div class="inner">
                        <h3> 723 </h3>
                        <p> Reciclagem </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-trash"></i>
                    </div>
                    <a href="{{ route('library-bin') }}" class="card-box-footer">Ver mais <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div> --}}

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
        $(".navbar").removeClass("bg-dark");
    </script>


@endsection


<style>
    .navbar {
        padding: 0px !important;
    }
</style>
