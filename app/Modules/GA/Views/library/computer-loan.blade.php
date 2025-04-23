<!--F4k3-->
@section('title', __('Biblioteca'))
@extends('layouts.backoffice')

@section('styles')
    @parent
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection
 
<!-- Temas em destaque -->
<style>
    h1 {
        font-family: Roboto Slab, serif;
        font-style: normal;
        font-weight: 700;
        line-height: 35px;
        font-size: 22px;
    }


    span .title-large {
        color: #1e1e1e !important;
    }


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

    .form-css {
        margin-bottom: 2%;
        background-color: #1e1d1d0a !important;
        padding-bottom: 2%;
        padding-left: 1%;
    }

    .form-css .input {
        font-size: 14px;
        padding: 14px 16px;
        border-radius: 6px;
    }

    .dados-loan {
        font-family: Roboto Slab, Roboto, serif;
        font-style: normal;
        font-size: 14px;
        text-transform: uppercase;
        background-color: #0060af;
        padding: 10px;
        color: white;
        margin-bottom: 0px !important;
    }

    .button-req {
        
        border-radius: 5px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        border: 2px solid #0060af !important;
        background-color: #0060af !important;
        display: none;
    }

    .button-cls {
        width: 220px !important;
        border-radius: 5px !important;
        color: #white !important;
        font-weight: 700 !important;
        border: 2px solid #4389e3 !important;

    }

    .button-req:hover {
        background-color: white !important;
        color: #4389e3 !important;
        border: 2px solid #6c757d !important;

    }

    .button-cls:hover {

        color: #6c757d !important;
        border-color: #6c757d !important;
        background-color: white !important;
        outline: #0b66dbc4;
    }

    .inf-content {
        border: 1px solid #DDDDDD;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        box-shadow: 7px 7px 7px rgba(0, 0, 0, 0.3);
    }

    .img-profile {

        width: 150px;
        height: 153px;
        border-radius: 100px !important;
    }

    .inf-content {
        box-shadow: 0px 0px 10px rgb(0 0 0 / 30%) !important;
    }

    .custom-scrollbar-js,
    .custom-scrollbar-css {
        height: 200px;
    }


    /* Custom Scrollbar using CSS */
    .custom-scrollbar-css {
        overflow-y: scroll;
    }

    /* scrollbar width */
    .custom-scrollbar-css::-webkit-scrollbar {
        width: 5px;
    }

    /* scrollbar track */
    .custom-scrollbar-css::-webkit-scrollbar-track {
        background: #eee;
    }

    /* scrollbar handle */
    .custom-scrollbar-css::-webkit-scrollbar-thumb {
        border-radius: 1rem;
        background-color: #00d2ff;
        background-image: linear-gradient(to top, #00d2ff 0%, #3a7bd5 100%);
    }

    .panel-livro-selecionados {
        height: 700px !important;
    }

    .panel-livro-selecionados img {
        width: 120px;
        height: 150px;
        border-radius: 10px;

    }

    .lista {
        list-style: none;
        padding-top: 3%;

    }

    .lista li {
        padding: 1px;
        width: 100%;

    }

    .linha {
        box-shadow: 0px 0px 5px rgb(119, 119, 119);
        background-color: white;
        margin-left: 0px !important;
        width: 99%;
        margin-left: 2%;
        border-radius: 10px;
        padding-left: 0.4px;
    }

    .linha .col-3,
    .linha .col-8 {
        padding-left: 0px;
        padding-right: 0px;
    }

    .col-1 i {
        margin-left: 6%;
        margin-top: 10px;
    }

    .fa-close {
        cursor: pointer;
    }

    .area-requisicoes {
        height: 360px;
    }

    .estado-finalizado {
        background-color: #28a745;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .estado-andamento {
        background-color: #d8cb0f;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .dataTables_filter {

        float: right !important;

    }

    .nav-link {
        color: #1e1e1e !important;
    }

    .nav-link:hover {
        color: white !important;
    }

    .badge {
        font-size: 13px !important;
        color: white;
        float: right;
        padding: 7px 15px !important;
        background-color: #1a446e !important;
        /* margin-top: -5px; */
    }
    .badge i{
        margin-right: 2px;
    }

    .badge:hover{
        opacity: 0.8;
        cursor: pointer;
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
        color: white;
        background: rgba(0, 0, 0, 0.4);
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

    .color-green {
        color: #00a65a !important;
    }

    .bg-orange {
        background-color: #f39c12 !important;
    }

    .bg-red {
        background-color: #d9534f !important;
    }

    .color-red {
        color: #d9534f !important;
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

    .navbar .dropdown,.inicio-nav {
        width: auto!important;    
  
    }
    .navbar .dropdown{
        padding-left: 0px;
        padding-right: 0px; 
    }
    .navbar .dropdown li{
        text-align: left!important;
    }

    .card-box{
        border-radius: 12px; 
    }

</style>




@section('content') 

    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0 ">

        @include('GA::library.modal.layout')
        @include('GA::library.modal.final')
        @include('GA::library.modal.modalVisitante')
        @include('GA::library.modal.view-book')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6 mt-n1">

                        <h1 class="text-dark" style="    font-family: Roboto Slab,Roboto,serif;
                                                        font-style: normal;
                                                        font-weight: 700;
                                                        line-height: 28px;
                                                        font-size: 28px;
                                                        padding: 20px;
                                                        text-transform: uppercase;">
                            @lang('requisição de computador')</h1>

                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content" style="margin-left: 15px;">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-5">
                        <h5 class="dados-loan" style="border-top-left-radius: 5px; border-top-right-radius: 5px">Requerente</h5>
                        <form action="/action_page.php" class="was-validated form-css" style="height: 360px;">

                            <div class="row">

                                <div class="col-10 mt-2 mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
                                        class="rounded img-profile d-block" alt="Foto do leitor" id="imgLeitor">
                                </div>

                                <div class="col-2">
                                    <div class="form-check form-check-inline" >
                                        <input class="form-check-input" type="checkbox" id="check-visitante"
                                            value="visitante" data-toggle="modal"
                                    data-target="#modalVisitante">
                                        <label class="form-check-label" for="check-visitante">Visitante</label>
                                    </div>
                                </div>

                            </div>

                            <div class="row d-none">
                                <div class="mb-1 mt-3 col-6">
                                    <label for="codigoLeitor" class="form-label">Codigo do leior</label>
                                    <input type="text" class="form-control input mt-1" id="codigoLeitor"
                                        placeholder="Codigo do leitor" name="codigoLeitor" disabled required>

                                </div>
                            </div>

                            <div class="col-12">



                                <div class="row">
                                    <div class="mt-3 col-5">
                                        <label for="nomeLeitor" class="form-label">Nome completo</label>
                                        <input type="text" class="form-control input mt-1" id="nomeLeitor"
                                             name="nomeLeitor" disabled required>
                                        <div class="invalid-feedback invalid-leitor">Leitor não encontrado</div>
                                    </div>

                                    <div class="mt-3 col-5">
                                        <label for="emailLeitor" class="form-label documento-lb">email</label>
                                        <input type="email" class="form-control input mt-1 emailLeitor" id="emailLeitor"
                                            name="emailLeitor" required disabled>

                                    </div>

                                    <div class="mt-3 col-10 div-internos">
                                        <div class="col-12">
                                            <label for="select-user-visitor" class="form-label">Interno</label>

                                        </div>
                                        <select name="select-user" id="select-user"
                                            class="selectpicker form-control select-user" data-actions-box="true"
                                            data-selected-text-format="count > 3" data-live-search="true" required>
                                            <option value=""></option>

                                            @foreach ($usuarios as $item)
                                                <option value="{{ $item->leitor_codigo }}">{{ $item->leitor_nome }} |
                                                    {{ $item->leitor_email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- <div class="input-group mt-3 col-10 div-visitantes" style="display: none">
                                        <div class="col-12">
                                            <label for="select-user-visitor" class="form-label">Visitante</label>

                                        </div>
                                        <select name="select-user-visitor" id="select-user-visitor"
                                            class="selectpicker form-control select-user-visitor" data-actions-box="true"
                                            data-selected-text-format="count > 3" data-live-search="true" required>
                                            <option value=""></option>

                                            @foreach ($visitante as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }} |
                                                    {{ $item->bi }} | {{ $item->code }}
                                                </option>
                                            @endforeach

                                        </select>

                                        <button type="button" class="btn btn-success btn-add-editora" data-toggle="modal"
                                            data-target="#modalVisitante"
                                            style="border-radius: 0px 5px 5px 0px; letter-spacing: 1px;"
                                            title="Criar Editora"><i class="fas fa-plus"></i></button>
                                    </div> --}}
                                </div>
                            </div>

                        </form>

                        <h5 class="dados-loan" style="border-top-left-radius: 5px; border-top-right-radius: 5px">Computadores Disponíveis</h5>

                        <div class="form-css">

                            <div class="col-10  mt-3">

                                <label for="">Computador </label>
                                <div class="input-group mb-3">

                                    <select name="select-computador" id="select-computador"
                                        class="selectpicker form-control input computador" data-actions-box="true"
                                        data-selected-text-format="count > 3" data-live-search="true" required>
                                        <option value=""></option>
                                        @foreach ($computadores as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name . ' | ' . $item->brand }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="invalid-feedback invalid-livro">Computador não selecionado</div>
                            </div>

                        </div>

                        <h5 class="dados-loan" style="border-top-left-radius: 5px; border-top-right-radius: 5px">Tempo da requisição </h5>
                        <form class="was-validated form-css" id="form-requisitar">

                            <div class="col-12">



                                <div class="row">

                                    <div class="mb-1 mt-2 col-6">
                                        <label for="uname" class="form-label" >Duração da requisição</label>
                                        {{-- <input type="time" class="form-control input mt-1" id="temporequisicao"
                                            name="temporequisicao" @php
                                                echo 'value=' . date('H:i') . ' ';
                                            @endphp required> --}}
                                        <select name="temporequisicao" id="temporequisicao" class="form-control" required>
                                           
                                            <option value="1h">1h</option>
                                            <option value="1h30">1h30</option>
                                            <option value="2h">2h</option>
                                            <option value="2h30">2h30</option>
                                            <option value="3h">3h</option> 
                                            
    
                                        </select>

                                    </div>

                                </div>
                            </div>
                            <br><br>
                            <div class="col-12">

                                <div class="row">
                                    
                                    <div class="col-10">
                                    </div>
                                    <div class="col-2">
                                        <button class="btn button-req" data-target="#modalRequisitarLivro" data-toggle="modal">
                                            <i class="fa fa-plus"></i> Criar</a>
                                        </button>
                                        
                                        
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>

                    <div class="col-sm-7 "> 
                        
                        <h5 class="dados-loan" style="border-top-left-radius: 5px; border-top-right-radius: 5px">Computadores
                            
                            <span  class="badge" style="background-color: #f39c12 !important;border-radius: 0px 5px 0px 0px;" title="Computadores ocupados"><i class="fa fa-tv" aria-hidden="true"></i> {{$estados["disponivel"]}}</span>
                            <span class="badge bg-red" title="Computadores esgotados" style="border-radius: 0px;" id="esgotado"><i class="fa fa-tv" aria-hidden="true"></i> {{$estados["esgotado"]}}</span>
                            <span class="badge" style="background-color: #00a65a !important;border-radius: 5px 0px 0px 0px;" title="Computadores ocupados"><i class="fa fa-tv" aria-hidden="true"></i> {{$estados["ocupado"]}}</span></h5>

                        <div class="form-css" style="height: 700px;">


                            <div class="container py-3 custom-scrollbar-css panel-livro-selecionados livros_selecionados"
                                style="padding-left:4px;">
                                <div class="col-12">


                                    <div class="row">

                                        @foreach ($computadores_requisitados as $item)
                                            
                                        @switch($item->estado_uso)

                                                @case('Disponível')
                                                    <div class="col-lg-4 col-sm-6">


                                                        <div class="card-box bg-orange">

                                                            <div class="inner">
                                                                <h3> {{ $item->nome_computador }} </h3>
                                                                <p class="" style="transform: scale(0,0)">
                                                                    0</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fa fa-desktop" aria-hidden="true"></i>
                                                            </div>

                                                        </div>
                                                        <h6>

                                                            <p style="float: left;" class="color-green">
                                                                <i class="fa fa-tv" aria-hidden="true"></i>
                                                                {{ $item->estado_uso }}
                                                            </p>


                                                        </h6>
                                                    @break

                                                @case('Ocupado')
                                                        <div class="col-lg-4 col-sm-6">


                                                            <div class="card-box bg-green">

                                                                <div class="inner">
                                                                    <h3> {{ $item->nome_computador }} </h3>
                                                                    <p style="transform: scale(0,0)"> {{ $item->codigo }} </p>
                                                                </div>
                                                                <div class="icon">
                                                                    <i class="fa fa-desktop" aria-hidden="true"></i>
                                                                </div>
                                                                <button class="card-box-footer btn btn-success btn-sm gerar-pdf"
                                                                    style="width:30px;height: 30px;">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                    <p class="d-none name"></p>
                                                                    <p class="d-none id">{{ $item->codigo }}</p>
                                                                </button>
                                                                <button data-target="#modalComputadorFinalizar" data-toggle="modal"
                                                                    class="card-box-footer btn btn-success btn-sm finalizar-computador"
                                                                    style="width:30px;height: 30px;margin-left:30px;">
                                                                    <i class="fas fa-handshake"></i>
                                                                    <p class="d-none name"></p>
                                                                    <p class="d-none id">{{ $item->codigo }}</p>
                                                                </button>
                                                            </div>
                                                            <h6>
                                                                <p>
                                                                    
                                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                                    
                                                                    @if ($item->requerente=="Visitante")
                                                                    
                                                                        {{ $item->nome_visitante }}

                                                                    @else
                                                                        
                                                                        {{ $item->requerente }}

                                                                    @endif
                                                                    
                                                                </p>


                                                                {{-- Se o tempo estiver esgotado --}}

                                                                @if ($item->hora_final > date('H:i:s') && $item->data_requisicao == date('Y-m-d'))
                                                                    <p class="color-green">
                                                                        <i class="fa fa-check-square" aria-hidden="true"></i>
                                                                        {{ $item->estado_uso }}

                                                                    </p>
                                                                    <p class="color-green" style="float: right;">
                                                                        <i class="fa fa-clock" aria-hidden="true"></i> Fim:
                                                                        {{ $item->hora_final }}
                                                                    </p>
                                                                    <p style="" class="color-green">
                                                                        <i class="fa fa-clock" aria-hidden="true"></i> Início:
                                                                        {{ $item->hora_requisicao }}
                                                                    </p>
                                                                @else
                                                                    <p class="color-red esgotado">
                                                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                                                        Tempo esgotado
                                                                    </p>
                                                                    <p class="color-red" style="float: right;">
                                                                        <i class="fa fa-clock" aria-hidden="true"></i> Fim:
                                                                        {{ $item->hora_final }}
                                                                    </p>
                                                                    <p style="" class="color-red">
                                                                        <i class="fa fa-clock" aria-hidden="true"></i> Início:
                                                                        {{ $item->hora_requisicao }}
                                                                    </p>
                                                                @endif

                                                            </h6>
                                                        @break

                                                        @default
                                                    @endswitch

                                                </div>
                                        @endforeach

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>


    </div>
    </div>

    <div id="temas-destaque" class="container-fluid">
    </div>
    </div>


@endsection

@section('scripts')

    @parent
    <script>
        // ========================================== Ocultar a barra de navega... ===================================

        var cookies = document.cookie;

        var nova = cookies.split(";");

        if (nova[0] == "tela=cheia") {
 
            $(".btn-logout").removeClass("col-6");
            $(".btn-logout").addClass("col-7");
            $(".left-side-menu,.top-bar").hide();
            $(".btn-logout").show();

            $(".content-wrapper").css({
                margin: '0 auto',
                marginTop: '0px',
                position: 'absolute',
                left: '0',
                top: '0',
                padding: '0',
                width: '100%'
            });

            $(".content-panel").css({
                marginTop: '0px'
            });
        }


        

        $(".tirar").click(function() {

            var cookies = document.cookie;

            var nova = cookies.split(";");

            if (nova[0] == "tela=cheia") {

                $(".btn-logout").removeClass("col-7");
                $(".btn-logout").addClass("col-6");
                $(".left-side-menu,.top-bar").show();
                $(".btn-logout").hide();
                $(".content-wrapper").css({
                    // margin: '0 auto',
                    // marginTop: '0px',  
                    position: 'absolute',
                    left: '370px',
                    top: '84px',
                    padding: '20px',
                    width: 'calc(100% - 370px)'
                });

                $(".content-panel").css({
                    marginTop: '14px'
                });


                document.cookie = "tela=normal";

            } else if (nova[0] == "tela=normal") {
                $(".btn-logout").removeClass("col-6");
                $(".btn-logout").addClass("col-7");
                $(".btn-logout").show();
                $(".left-side-menu,.top-bar").hide();

                $(".content-wrapper").css({
                    margin: '0 auto',
                    marginTop: '0px',
                    position: 'absolute',
                    left: '0',
                    top: '0',
                    padding: '0',
                    width: '100%'
                });

                $(".content-panel").css({
                    marginTop: '0px'
                });
                document.cookie = "tela=cheia";

            } else {
                document.cookie = "tela=cheia";
            }

        });

        // Se os dados do leitor nao forem encontrados
 
        $(".emailLeitor").on('change', function(){
 
            $('#codigoLeitor').val("");
            $('#nomeLeitor').val("");
            $("#imgLeitor").attr("src", "https://cdn-icons-png.flaticon.com/512/149/149071.png");

        });


        // Metodo para pegar os dados do leitor

        function tabela(id) {

            let tabela = $('#tabela-requisicoes').DataTable({
                destroy: true,
                searching: false,
                serverSide: false,
                processing: false,
                aLengthMenu: [6, 10, 20, 50],
                orderable: false,
                paging: true,
                buttons: [
                    // 'colvis'
                    //   ,{
                    //       text: "Todos",
                    //       className:"btn"
                    //     }

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                },
                "ajax": {
                    "url": "library-get-loan/" + id,
                    "type": "GET"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false
                    },
                    {
                        data: 'referencia',
                        name: 'referencia'
                    },
                    {
                        data: 'data_inicio',
                        name: 'data_inicio'
                    },
                    {
                        data: 'data_fim',
                        name: 'data_fim'
                    },
                    {
                        data: 'states',
                        name: 'states'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    }
                ]

            });


        }

        $(".select-user").on('change', function() {

            var codigo_leitor = $(this).val();

            // Pegando os dados do leitor (Nome, Email, Foto de Perfil)


            action = "leitor";

            var array = [action, codigo_leitor];

            $.ajax({
                url: 'library-get-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }
                },
                success: function(response) {

                    // limpar_tabela();



                    var leitor = response;

                    // Converte os dados num array

                    leitor = (leitor + "").split(',');

                    // Preenchendo os campos obtidos para posteriormente serem alterados

                    $('#codigoLeitor').val(leitor[0]);
                    $('#nomeLeitor').val(leitor[1]);
                    $('#emailLeitor').val(leitor[2]);
                    $("#em-curso,#finalizada").text("0");
                    $("#em-curso").text(leitor[4]);
                    $("#finalizada").text(leitor[5]);

                    if (leitor[3] == null || leitor[3] == "") {
                        $("#imgLeitor").attr("src",
                            "https://cdn-icons-png.flaticon.com/512/149/149071.png");
                    } else {

                        $("#imgLeitor").attr("src", "//forlearn.ao/storage/attachment/" + leitor[3]);
                    }

                    tabela(codigo_leitor);

                }

            });

        });

        $(".select-user-visitor").on('change', function() {

            var codigo_leitor = $(this).val();

            // Pegando os dados do leitor (Nome, Email, Foto de Perfil)


            action = "visitante";

            var array = [action, codigo_leitor];

            $.ajax({
                url: 'library-get-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }
                },
                success: function(response) {

                    // limpar_tabela();

                    var visitante = response;

                    // Converte os dados num array

                    visitante = (visitante + "").split(',');

                    // Preenchendo os campos obtidos para posteriormente serem alterados

                    $('#codigoLeitor').val(visitante[0]);
                    $('#nomeLeitor').val(visitante[1]);
                    $('#emailLeitor').val(visitante[2]);
            
                }

            });

        });

        // Pegar o id da requisição a ser finalizada

        var codigo_devolucao = 0;
        var btn_devolver = "";

        $(".finalizar-computador").click(

            function() {
                codigo_devolucao = $(this).children(".id").text();

                $(".modal-confirm h4").text('Finalizar requisição?');
                $("#modalComputadorFinalizar .modal-confirm p").show();
                $("#modalComputadorFinalizar .material-icons").text("info");
                $("#modalComputadorFinalizar .btn-cancelar-computador").text("Cancelar");
                $("#modalComputadorFinalizar .btn-finalizar").show();

            }
        );

        // finalizar requisicao

        $(".btn-finalizar").click(

            function() {

                var action = "requisicao-computador";

                var array = [action, codigo_devolucao];

                $.ajax({
                    url: 'library-delete-item/' + array,
                    type: "post",
                    data: $(this).serialize(),
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }
                    },
                    success: function(response) {

                        // Quando a requisição for finalizada com sucesso, muda o estado do modal para sucesso!

                        $("#modalComputadorFinalizar .modal-confirm h4").text(
                            'Requisicão finalizada com sucesso!!!');
                        $("#modalComputadorFinalizar .modal-confirm p").hide();
                        $("#modalComputadorFinalizar .material-icons").text("check_circle");
                        $("#modalComputadorFinalizar .btn-cancelar-livro").text("Continuar");
                        $("#modalComputadorFinalizar .btn-finalizar").hide();


                        // Carregar a página

                        window.location.href = 'library-computer-loan';


                    }

                });
            }
        );

        // Requisitar um computador 

        $("#form-requisitar").submit(function(event) {

            
            var tipo = "interno";
            var action = "requisitar-computador";
            var leitor = $("#codigoLeitor").val();
            var computador = $("#select-computador").val() + "";
            var datarequisicao = $("#temporequisicao").val();

            var array = [action, leitor, computador, datarequisicao,tipo];  

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

                    if (resultado[0] == "Requisição existente") {

                        alert("Requisição existente");
                    }

                }

            });
        });

        $(".form-css").hover(
            function() {

                var leitor = $("#codigoLeitor").val();
                var computador = $("#select-computador").val();
                var horarequisicao = $("#temporequisicao").val();
                

                if (leitor == "" || computador == "" || horarequisicao == "") {

                    $(".button-req").hide();

                } else {
                    $(".button-req").show();
                }



            });


        // Gerar individual pdf

        $(".gerar-pdf").on('click', function() {

            codigo_requisicao = $(this).children(".id").text();
            window.open('library-computer-pdf/' + codigo_requisicao, '_blank');

        });


        // Se a Instituição não existir abre o botão para adiciona-la

        $("#instituicaoVisitante").on('change',
            function() {


                var nome = $(this).val();

                if (nome == "Adicionar") {

                    $(".painel-instituicao").show();

                } else {

                    $(".painel-instituicao").hide();


                }
            }
        );

        // Verificando se o nome da Instituiçao foi preenchido


        $("#nomeInstituicao").on('keyup', function() {


            if ($("#nomeInstituicao").val() != "") {

                $("#nomeInstituicao").removeClass('is-invalid');
                $("#nomeInstituicao").addClass('is-valid');
                $(".add-Instituicao").show();


            } else {

                $("#nomeInstituicao").removeClass('is-valid');
                $("#nomeInstituicao").addClass('is-invalid');
                $(".add-Instituicao").hide();
            }
        });

        // Cadastrar uma novo instituiçao 

        $(".add-Instituicao").click(function() {

            event.preventDefault();

            var nome = $('#nomeInstituicao').val();
            var array = ["instituicao", nome];
            var instituicao = $("#instituicaoVisitante");
            var resultado = "";

            $.ajax({
                url: 'library-create-item/' + array,
                type: "get",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }

                },
                success: function(response) {


                    $(".painel-instituicao").hide();


                    // Recupera presentes no objecto response

                    resultado = response;

                    // Converte os dados num array

                    resultado = (resultado + "").split(',');



                    // Se a Instituição já existe na base de dados

                    if (resultado[0] == "Instituicao existente") {


                        $("#instituicaoVisitante option[value='" + resultado[1] + "']").attr("selected",
                            "true");
                        $('.instituicaoVisitante .filter-option-inner-inner').text(resultado[2]);

                    }

                    // Se a Instituição não existir, cadastra Instituição
                    else if (resultado[0] == "sucesso") {

                        $('.instituicaoVisitante .filter-option-inner-inner').text(resultado[2]);
                        instituicao.append("<option value='" + resultado[2] + "'selected> " +
                            resultado[2] + " </option>");

                    }


                }

            });

        });

        // Formulário para efectuar a requisição de computador dos visitantes Visitantes

        $('#formVisitante').submit(function(event) {

            event.preventDefault();

            var action = "requisitar-computador";
            var leitor = $("#codigoLeitor").val();
            var nome = $("#nomeVisitante").val();
            var telefone = $("#telefoneVisitante").val();
            var instituicao = $("#instituicaoVisitante").val();
            var computador = $("#select-computador-v").val() + "";
            var tempo = $("#select-hora").val();
            var tipo = "visitante";

            var array = [action,leitor,computador,tempo,tipo,nome,telefone,instituicao];  

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

                    window.location.href="library-computer-loan";

                } 

            });

        });

        // Escolher visitante ou usuario 

        $("#check-visitante").on('click', function() {

            if ($('#check-visitante').is(':checked')) {
                
                // Limpa os campos
                $('#codigoLeitor,#nomeLeitor,#emailLeitor').val("");
                $(this).attr("data-target","#modalVisitante"); 
                               
                
            } else {

                // Limpa os campos 
                $('#codigoLeitor,#nomeLeitor,#emailLeitor').val("");
                $(this).removeAttr("data-target"); 
            }

        });


        setTimeout(function(){
            $("#esgotado").html('<i class="fa fa-tv" aria-hidden="true"></i> '+$(".esgotado").length);
        }, 1000);


        $("#esgotado").click(function() {
            $(".esgotado").animate({opacity:'0'},500);
            $(".esgotado").animate({opacity:'1'},500);
        });
        

    </script>

@endsection
