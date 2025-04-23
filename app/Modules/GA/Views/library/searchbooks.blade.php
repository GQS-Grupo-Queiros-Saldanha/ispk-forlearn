<!--F4k3-->
@section('title', __('Gerir Itens'))
@extends('layouts.backoffice')

@section('styles')
    @parent


@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script type="javascript/text" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="javascript/text" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"> --}}



<!-- Temas em destaque -->
<style>
    .modal-css .close {
        color: black;
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

    .modal-header .close {
        color: black;
    }


    #temas-destaque .temas .col {
        border: 20px solid transparent;
        border-radius: 7px;
        background-size: cover;
        background-repeat: no-repeat;
        min-width: 458px;
        min-height: 305px;
        display: flex;
        color: white;
        position: relative;
        filter: brightness(.8);
    }

    #temas-destaque .temas .col:hover {
        cursor: pointer;
        filter: brightness(1);
    }

    #temas-destaque .temas .col span {
        text-transform: uppercase;
        font-weight: bold;
        font-size: 15px;
        line-height: 24px;
        position: absolute;
        bottom: 20px;
        left: 30px;
        text-shadow: 0 0 2px #000;
    }

    #search-results {
        color: white;
        display: flex;
        flex-wrap: wrap;
    }

    .result {
        color: #000;
        box-shadow: 0 0 5px rgba(0, 0, 0, .25);
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column-reverse;
        border-radius: 7px !important;
        height: auto;
        width: 18%;
        margin: 10px;
    }

    .result img {
        width: 250px;
        height: 360px;
    }

    .tab-content {
        padding-top: 2% !important;
    }

    .nav-tabs .nav-link .active {
        background-color: #ced4da;
        border-color: #ced4da;
    }

    .nav-tabs .active i {
        color: white !important;
    }

    .nav-tabs a {
        text-transform: uppercase;
        font-weight: bold;
        float: left;
        border: 2px solid #ebedef;
        border-left: none;
        border-bottom: none;
    }



    .nav-tabs .active {
        color: #ffffff !important;
        background-color: #0060af !important;
        border-color: #0060af !important;
        transform: translateY(2px);
    }

    .nav-tabs {
        /* width: 1200px;    */
    }

    /* .active{
        background-color: rgb(54, 54, 210)!important;
    } */

    .tab-content {
        background-color: #f8fafc !important;
    }

    .modal-body span {
        color: black !important;
    }

    .badge {
        font-size: 12px !important;
        margin-right: 30px;
        color: white;
        padding: 5px 10px !important;
        float: right;
        background-color: #1986e7 !important;
    }

    .fundo {
        background-color: #1986e7 !important;
    }

    .tab-Content {
        border-top: 1px solid #0060af7a !important;
    }


    .dataTables_length select {
        width: 60px !important;

    }

    .buttons-colvis {
        margin-left: 3%;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #8181824f;
    }

    table.dataTable thead .sorting,
    table.dataTable thead .sorting_asc,
    table.dataTable thead .sorting_desc {
        background-image: none !important;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 1px solid #d3d5d6;
    }

    table.dataTable thead th,
    table.dataTable thead td {
        padding: 10px 18px;
        border-bottom: 1px solid #d3d5d6;
    }

    table.dataTable tbody th,
    table.dataTable tbody td {
        padding: 4px 10px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 1px !important;
    }

    .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff !important;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }

    .pagination .active {
        background-color: #007bff;
        color: white !important;
    }

    .pagination .active .page-link {

        color: white !important;
    }

    .pagination li:hover,
    .pagination .page-link:hover {
        border: 1px solid #007bff !important;
        background-color: white;
    }

    .estado-finalizado {
        background-color: #28a745;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .estado-operacional {
        background-color: #28a745;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .estado-danificado {
        background-color: #cf1a1a;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .estado-andamento {
        background-color: #d8cb0f;
        padding: 2%;
        text-align: center;
        color: rgb(0, 0, 0) !important;
    }

    #tabela-livros-requisitados td,
    #tabela-livros-requisitados th {
        padding: 0.75rem;
    }

    .novo-item {
        margin-bottom:
    }
</style>
@include('GA::library.modal.view-book')
@include('GA::library.modal.modal')
@include('GA::library.modal.time')
@section('content')

    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0">
        @include('GA::library.modal.layout')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1 class="m-0 text-dark" style="padding-left: 0px;">@lang('gestão de itens') </h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>

                    <div class="col-12"> <br> <br>
                         @if (auth()->user()->hasAnyPermission(['library_manage_item']))                            
                        <button class="btn btn-success ml-2 btn-create-item" style="border-radius: 5px;"
                            id="btn-create-item">
                            <i class='fa fa-plus'></i> Criar Livro
                        </button>
                          @endif                        
                        <button class="btn btn-primary ml-2 btn-pdf-item" style="border-radius: 5px;" id="btn-pdf-item">
                            <i class='fa fa-file-pdf'></i> Gerar Relátório
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Pegar o total de cada elemento --}}





        {{-- Main content --}}

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <nav class="col-8">
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link" id="nav-Autor-tab" data-toggle="tab" href="#nav-Autor"
                                            role="tab" aria-controls="nav-Autor" data="Autor" aria-selected="false">

                                            <i class="fa fa-user"></i>
                                            Autores
                                            <span class="badge">

                                                @php
                                                    $total = count($autores);
                                                    
                                                    echo $total;
                                                @endphp

                                            </span></a>

                                        <a class="nav-item nav-link" id="nav-Categoria-tab" data-toggle="tab"
                                            href="#nav-Categoria" role="tab" data="Área"
                                            aria-controls="nav-Categoria" aria-selected="false">
                                            <i class="fa fa-sort-alpha-asc"></i>
                                            Áreas

                                            <span class="badge ">
                                                @php
                                                    $total = count($categorias);
                                                    
                                                    echo $total;
                                                @endphp
                                            </span>

                                        </a>

                                        <a class="nav-item nav-link" id="nav-computador-tab" data-toggle="tab"
                                            href="#nav-computador" role="tab" data="Computador"
                                            aria-controls="nav-Computador" aria-selected="false">
                                            <i class="fa fa-desktop"></i>
                                            Computadores

                                            <span class="badge ">
                                                @php
                                                    $total = count($computadores);
                                                    
                                                    echo $total;
                                                @endphp
                                            </span>

                                        </a>

                                        <a class="nav-item nav-link" id="nav-Editora-tab" data-toggle="tab"
                                            href="#nav-Editora" role="tab" data="Editora" aria-controls="nav-Editora"
                                            aria-selected="false">
                                            <i class="fa fa-edit"></i>

                                            Editoras

                                            <span class="badge ">
                                                @php
                                                    $total = count($editoras);
                                                    
                                                    echo $total;
                                                @endphp
                                            </span>

                                        </a>

                                        <a class="nav-item nav-link active" id="nav-Livro-tab" data-toggle="tab"
                                            href="#nav-Livro" role="tab" data="Livro" aria-controls="nav-Livro"
                                            aria-selected="true">

                                            <i class="fa fa-book"></i>
                                            Livros

                                            <span class="badge ">
                                                @php
                                                    $total = count($livros);
                                                    
                                                    echo $total;
                                                @endphp
                                            </span>

                                        </a>


                                    </div>
                                </nav>

                                <div class="tab-content" id="nav-tabContent">

                                    <div class="tab-pane fade" id="nav-Autor" role="tabpanel"
                                        aria-labelledby="nav-Autor-tab">
                                        <div class="col">

                                            {{-- Tabela para a listagem dos Autores --}}

                                            <table id="tabela-Autor"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline collapsed">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>Código</th> --}}
                                                        <th>Código</th>
                                                        <th>Nome Completo</th>
                                                        <th>Sobrenome</th>
                                                        <th>Gênero</th>
                                                        <th>País</th>
                                                        <th>Criado por</th>
                                                        <th>Criado a</th>
                                                        <th>Actualizado por</th>
                                                        <th>Actualizado a</th>
                                                        <th>Actividades</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $contador = 1;
                                                    @endphp


                                                    @foreach ($autores as $item)
                                                        <tr>
                                                            <th> {{ $contador++ }} </th>

                                                            {{-- <th> {{ $item->id }} </th> --}}
                                                            <th> {{ $item->others_information }} </th>
                                                            <th> {{ $item->name }} </th>
                                                            <th> {{ $item->surname }} </th>
                                                            <th> {{ $item->genre }} </th>
                                                            <th> {{ $item->country }} </th>
                                                            <th> {{ $item->created_by }} </th>
                                                            <th> {{ $item->created_at }} </th>
                                                            <th> {{ $item->updated_by }} </th>
                                                            <th> {{ $item->updated_at }} </th>


                                                            <th>
                                                                {{-- <a href="{{ route('library-create') }}"
                                                                    class="btn btn-info btn-sm" style="width:30px;">
                                                                    <i class="far fa-eye"></i>
                                                                </a> --}}
                                                                 @if (auth()->user()->hasAnyPermission(['library_manage_item']))
                                                                <button href="#"
                                                                    class="btn btn-warning btn-sm editarAutor"
                                                                    data-toggle="modal" data-target="#modalAlterarAutor"
                                                                    style="width:30px;">
                                                                    <i class="fas fa-edit"></i>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
                                                                <button data-target="#modalEliminarAutor"
                                                                    data-toggle="modal" data-name="{{ $item->name }}"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm eliminarAutor"
                                                                    style="width:30px;" onclick="eliminar_autor(this)">
                                                                    <i class="fas fas fa-trash-alt"></i>
                                                                </button>
                                                                  @endif
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade " id="nav-Categoria" role="tabpanel"
                                        aria-labelledby="nav-Categoria-tab">
                                        <div class="col">

                                            {{-- Tabela para a listagem de Categoria --}}


                                            <table id="tabela-Categoria"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>Código</th> --}}
                                                        <th>CDU / CDD</th>
                                                        <th>Nome</th>
                                                        <th>Criado por</th>
                                                        <th>Criado a</th>
                                                        <th>Actualizado por</th>
                                                        <th>Actualizado a</th>
                                                        <th>Actividades</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $contador = 1;
                                                    @endphp


                                                    @foreach ($categorias as $item)
                                                        <tr>
                                                            <th> {{ $contador++ }} </th>
                                                            {{-- <th> {{ $item->id }} </th> --}}
                                                            <th> {{ $item->description }} </th>
                                                            <th> {{ $item->name }} </th>
                                                            <th> {{ $item->created_by }} </th>
                                                            <th> {{ $item->created_at }} </th>
                                                            <th> {{ $item->updated_by }} </th>
                                                            <th> {{ $item->updated_at }} </th>
                                                            <th>
                                                                {{-- <a href="{{ route('library-create') }}"
                                                                    class="btn btn-info btn-sm" style="width:30px;">
                                                                    <i class="far fa-eye"></i>
                                                                </a> --}}
                                                                 @if (auth()->user()->hasAnyPermission(['library_manage_item']))
                                                                <button class="btn btn-warning btn-sm editarCategoria"
                                                                    data-toggle="modal"
                                                                    data-target="#modalAlterarCategoria"
                                                                    style="width:30px;">
                                                                    <i class="fas fa-edit"></i>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
                                                                <button data-target="#modalEliminarCategoria"
                                                                    data-toggle="modal" data-name="{{ $item->name }}"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm eliminarCategoria"
                                                                    style="width:30px;" onclick="eliminar_area(this)">
                                                                    <i class="fas fas fa-trash-alt"></i>
                                                                </button>
                                                                  @endif
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="nav-Computador" role="tabpanel"
                                        aria-labelledby="nav-Computador-tab">
                                        <div class="col">

                                            {{-- Tabela para a listagem de Categoria --}}


                                            <table id="tabela-Computador"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>Código</th> --}}
                                                        <th>Nome</th>
                                                        <th>Marca</th>
                                                        <th>Processador</th>
                                                        <th>RAM</th>
                                                        <th>HD / SSD</th>
                                                        <th>Estado</th>
                                                        <th>Criado por</th>
                                                        <th>Criado a</th>
                                                        <th>Actualizado por</th>
                                                        <th>Actualizado a</th>
                                                        <th>Actividades</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $contador = 1;
                                                    @endphp


                                                    @foreach ($computadores as $item)
                                                        <tr>
                                                            <th> {{ $contador++ }} </th>
                                                            {{-- <th> {{ $item->id }} </th> --}}
                                                            <th> {{ $item->name }} </th>
                                                            <th> {{ $item->brand }} </th>
                                                            <th> {{ $item->processor }} </th>
                                                            <th> {{ $item->ram }} </th>
                                                            <th> {{ $item->hd_ssd }} </th>
                                                            <th>
                                                                @switch($item->status)
                                                                    @case('Operacional')
                                                                        <p class="estado-operacional">{{ $item->status }}</p>
                                                                    @break

                                                                    @case('Danificado')
                                                                        <p class="estado-danificado">{{ $item->status }}</p>
                                                                    @break

                                                                    @default
                                                                @endswitch
                                                            </th>
                                                            <th> {{ $item->created_by }} </th>
                                                            <th> {{ $item->created_at }} </th>
                                                            <th> {{ $item->updated_by }} </th>
                                                            <th> {{ $item->updated_at }} </th>

                                                            <th>
                                                                {{-- <a href="{{ route('library-create') }}"
                                                                    class="btn btn-info btn-sm" style="width:30px;">
                                                                    <i class="far fa-eye"></i>
                                                                </a> --}}
                                                                 @if (auth()->user()->hasAnyPermission(['library_manage_item']))
                                                                <button class="btn btn-warning btn-sm editarComputador"
                                                                    data-toggle="modal"
                                                                    data-target="#modalAlterarComputador"
                                                                    style="width:30px;">
                                                                    <i class="fas fa-edit"></i>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
                                                                  @endif
                                                                {{-- <button data-target="#modalEliminarComputador"
                                                                    data-toggle="modal"
                                                                    class="btn btn-danger btn-sm eliminarComputador"
                                                                    style="width:30px;">
                                                                    <i class="fas fas fa-trash-alt"></i>
                                                                    <p class="d-none name">{{ $item->brand }}</p>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button> --}}
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade " id="nav-Editora" role="tabpanel"
                                        aria-labelledby="nav-Editora-tab">
                                        <div class="col">

                                            {{-- <div class="row">
                                                <div class="col-12">
                                                    <button class="btn btn-success novo-item"> <i class="fa fa-plus"></i> Criar nova editora </button>
                                                    
                                                </div>
                                            </div> --}}


                                            {{-- Tabela para a listagem dos Editora --}}

                                            <table id="tabela-Editora"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>código</th> --}}
                                                        <th>Nome</th>
                                                        <th>email</th>
                                                        <th>Endereço</th>
                                                        <th>Cidade</th>
                                                        <th>País</th>
                                                        <th>Criado por</th>
                                                        <th>Criado a</th>
                                                        <th>Actualizado por</th>
                                                        <th>Actualizado a</th>
                                                        <th>Actividades</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $contador = 1;
                                                    @endphp


                                                    @foreach ($editoras as $item)
                                                        <tr>
                                                            <th> {{ $contador++ }} </th>
                                                            {{-- <th> {{ $item->id }} </th> --}}
                                                            <th> {{ $item->name }} </th>
                                                            <th> {{ $item->email }} </th>
                                                            <th> {{ $item->address }} </th>
                                                            <th> {{ $item->city }} </th>
                                                            <th> {{ $item->country }} </th>
                                                            <th> {{ $item->created_by }} </th>
                                                            <th> {{ $item->created_at }} </th>
                                                            <th> {{ $item->updated_by }} </th>
                                                            <th> {{ $item->updated_at }} </th>
                                                            <th>
                                                                {{-- <a href="{{ route('library-create') }}"
                                                                    class="btn btn-info btn-sm" style="width:30px;">
                                                                    <i class="far fa-eye"></i>
                                                                </a> --}}
                                                                 @if (auth()->user()->hasAnyPermission(['library_manage_item']))
                                                                <button href="#"
                                                                    class="editarEditora btn btn-warning btn-sm"
                                                                    data-toggle="modal" data-target="#modalAlterarEditora"
                                                                    style="width:30px;">
                                                                    <i class="fas fa-edit"></i>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
                                                                <button data-target="#modalEliminarEditora"
                                                                    data-toggle="modal" data-name="{{ $item->name }}"
                                                                    data-id="{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm eliminarEditora"
                                                                    style="width:30px;" onclick="eliminar_editora(this)">
                                                                    <i class="fas fas fa-trash-alt"></i>
                                                                </button>
                                                                  @endif
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>

                                        </div>

                                    </div>

                                    <div class="tab-pane fade show active" id="nav-Livro" role="tabpanel"
                                        aria-labelledby="nav-Livro-tab">
                                        <div class="col">
                                            {{-- Tabela para a listagem dos Editoras --}}

                                            <table id="tabela-livro" class="table  table-striped ">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>código</th> --}}
                                                        <th>Título</th>
                                                        <th>Subtítulo</th>
                                                        <th>Autor</th>
                                                        <th>Editora</th>
                                                        <th>Área</th>
                                                        <th>ISBN</th>
                                                        <th>Local</th>
                                                        <th>Ano lançamento </th>
                                                        <th>Edição </th>
                                                        <th>Número de chamada</th>
                                                        <th>Páginas</th>
                                                        <th>Total</th>
                                                        <th>Disponível</th>
                                                        <th>Criado por</th>
                                                        <th>Criado a</th>
                                                        <th>Actualizado por</th>
                                                        <th>Actualizado a</th>
                                                        <th>Actividades</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $contador = 1;
                                                    @endphp


                                                    @foreach ($livros as $item)
                                                        <tr>
                                                            <th> {{ $contador++ }} </th>
                                                            {{-- <th> {{ $item[11] }} </th> --}}
                                                            <th> {{ $item[0] }} </th>
                                                            <th>{{ $item[1] }} </th>
                                                            <th> {{ $item[2] }} </th>
                                                            <th> {{ $item[3] }} </th>
                                                            <th> {{ $item[4] }} </th>
                                                            <th> {{ $item[5] }} </th>
                                                            <th> {{ $item[12] }} </th>
                                                            <th> {{ $item[6] }} </th>
                                                            <th> {{ $item[7] }} </th>
                                                            <th> {{ $item[8] }} </th>
                                                            <th> {{ $item[9] }} </th>
                                                            <th> {{ $item[10] }} </th>
                                                            <th> {{ $item[13] }} </th>
                                                            <th> {{ $item[14] }} </th>
                                                            <th> {{ $item[15] }} </th>
                                                            <th> {{ $item[16] }} </th>
                                                            <th> {{ $item[17] }} </th>
                                                            <th>
                                                                {{-- <a href="{{ route('library-create') }}"
                                                                    class="btn btn-info btn-sm" style="width:30px;">
                                                                    <i class="far fa-eye"></i>
                                                                </a> --}}
                                                                 @if (auth()->user()->hasAnyPermission(['library_manage_item']))
                                                                <button class="btn btn-success btn-sm addLivro"
                                                                    data-toggle="modal" data-target="#modalTempo"
                                                                    data-name="{{ $item[0] }}"
                                                                    data-id="{{ $item[11] }}" style="width:30px;"
                                                                    onclick="adicionar_livro(this)">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                                <button class="btn btn-warning btn-sm editarLivro"
                                                                    data-toggle="modal" data-target="#modalAlterarLivro"
                                                                    data-name="{{ $item[0] }}"
                                                                    data-id="{{ $item[11] }}" style="width:30px;"
                                                                    onclick="editar_livro(this)">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button data-target="#modalEliminarLivro"
                                                                    data-toggle="modal" data-name="{{ $item[0] }}"
                                                                    data-id="{{ $item[11] }}"
                                                                    class="btn btn-danger btn-sm eliminarLivro"
                                                                    style="width:30px;" onclick="eliminar_livro(this)">
                                                                    <i class="fas fas fa-trash-alt"></i>
                                                                </button>
                                                                  @endif
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>


                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    </section>

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
        $(".dataTables_length label").text("");

        // Maximizar e minimizar a tela da biblioteca


        // ===================================================================


        // Tabela Livros

        $(function() {



            let table = $('#tabela-livro').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis','excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
        });

        // Datatable - Tabela categoria

        $(function() {


            let table = $('#tabela-Categoria').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,
                buttons: [
                    'colvis','excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
        });

        // Datatable -Tabela Editoras

        $(function() {


            let table = $('#tabela-Editora').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis','excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
        });


        // Datatable - Tabela Autor

        $(function() {


            let table = $('#tabela-Autor').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis','excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
            
        });

        // Datatable - Tabela Computador

        $(function() {


            let table = $('#tabela-Computador').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis','excel'
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });
           
        });






        // ==================================== Formulario para editar as categorias =============================================

        //  Pegando os dados no modal para o alterar os dados

        $(".editarCategoria").click(function() {

            var id = $(this).children(".id").text();
            var action = "categoria";
            var array = [action, id];

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

                    var categoria = response;

                    // Converte os dados num array

                    categoria = (categoria + "").split(',');

                    // Preenchendo os campos obtidos para posteriormente serem alterados

                    $('#codigoCategoria-A').val(categoria[0]);
                    $('#nomeCategoria-A').val(categoria[1]);
                    $('#descricaoCategoria-A').val(categoria[2]);


                }

            });
        });

        // Editar os  dados da categoria

        $('#formAlterarCategoria').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionAlterar').val();
            var codigo = $(this).find('input#codigoCategoria-A').val();
            var nome = $(this).find('input#nomeCategoria-A').val();
            var descricao = $(this).find('input#descricaoCategoria-A').val();
            var sms = $("#formAlterarCategoria .sms");

            var resultado = "";

            var array = [action, codigo, nome, descricao];
            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }

                },
                success: function(response) {

                    var categoria = response;

                    // Converte os dados num array

                    categoria = (categoria + "").split(',');

                    if (categoria[0] == "sucesso") {

                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Área Altera com sucesso</div></div>'
                        );

                        window.location.href = "library-searchBooks";

                    } else if (categoria[0] == "nome existente") {
                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#d10000;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">O Código ou Nome já existe</div></div>'
                        );

                    }


                    setTimeout(function() {
                        sms.html("");
                    }, 2000);
                }

            });

        });



        // Editar a qualidade de livros 

        $('#formQuantidade').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionQuantidade').val();
            var codigo = $(this).find('input#id_livro').val();
            var type = $(this).find('select#type_operation').val();
            var quantidade = $(this).find('input#quantidade').val();
            var sms = $("#formQuantidade .sms");

            var resultado = "";

            var array = [action, codigo, type, quantidade];


            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }

                },
                success: function(response) {

                    var categoria = response;

                    // Converte os dados num array

                    categoria = (categoria + "").split(',');

                    console.log(categoria);


                    if (categoria[0] == "sucesso") {

                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Quantidade com sucesso</div></div>'
                        );

                        window.location.href = "library-searchBooks";

                    } else if (categoria[0] == "Erro") {
                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#d10000;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Quantidade inválida</div></div>'
                        );

                    }


                    setTimeout(function() {
                        sms.html("");
                    }, 2000);
                }

            });

        });

        //  Pegando os dados no modal para o alterar os dados da editora

        $(".editarEditora").click(function() {

            var id = $(this).children(".id").text();
            var action = "editora";
            var array = [action, id];

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

                    var editora = response;

                    // Converte os dados num array

                    editora = (editora + "").split(',');

                    // Preenchendo os campos obtidos para posteriormente serem alterados

                    $('#codigoEditora-A').val(editora[0]);
                    $('#nomeEditora-A').val(editora[1]);
                    $('#emailEditora-A').val(editora[2]);
                    $('#enderecoEditora-A').val(editora[3]);
                    $('#bairroEditora-A').val(editora[4]);
                    $('#cidadeEditora-A').val(editora[5]);
                    $('.paises').append('<option value="' + editora[6] + '"selected>' + editora[6] +
                        '</option>');

                }

            });
        });

        //  Editar os dados da editora

        $('#formAlterarEditora').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionAlterar').val();
            var codigo = $(this).find('input#codigoEditora-A').val();
            var nome = $(this).find('input#nomeEditora-A').val();
            var email = $(this).find('input#emailEditora-A').val();
            var endereco = $(this).find('input#enderecoEditora-A').val();
            var cidade = $(this).find('input#cidadeEditora-A').val();
            var pais = $(this).find('select#paises').val();

            var sms = $("#formAlterarEditora .sms");

            var resultado = "";

            var array = [action, codigo, nome, email, endereco,cidade, pais];

            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }

                },
                success: function(response) {

                    sms.html(
                        '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Editora alterada com sucesso</div></div>'
                    );

                    window.location.href = "library-searchBooks";

                    setTimeout(function() {
                        sms.html("");
                    }, 2000);
                }

            });

        });

        //  Pegando os dados no modal para o alterar os dados do Computador

        $(".editarComputador").click(function() {

            var id = $(this).children(".id").text();
            var action = "computador";
            var array = [action, id];

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

                    var computador = response;

                    // Converte os dados num array

                    computador = (computador + "").split(',');


                    // Preenchendo os campos obtidos para posteriormente serem alterados


                    // Separando os armazenamento das unidades Ex: (4 GB) para 4 e GB

                    var ram = computador[4];
                    var hd = computador[5];

                    ram = (ram + "").split(' ');
                    hd = (hd + "").split(' ');

                    $('#codigoComputador-A').val(computador[0]);
                    $('#nomeComputador-A').val(computador[1]);
                    $('#ramComputador-A').val(ram[0]);
                    $('#ramUnidade-A').val(ram[1]);
                    $('#hdComputador-A').val(hd[0]);
                    $('#hdUnidade-A').val(hd[1]);

                    $('.select-marca-A option[value="' + computador[2] + '"]').attr('selected', 'true');
                    $('.select-marca-A .filter-option-inner-inner').text(computador[2]);

                    $('.select-processador-A option[value="' + computador[3] + '"]').attr('selected',
                        'true');
                    $('.select-processador-A .filter-option-inner-inner').text(computador[3]);

                    $('.select-estado-A option[value="' + computador[6] + '"]').attr('selected',
                        'true');
                    $('.select-estado-A .filter-option-inner-inner').text(computador[6]);
                }

            });
        });

        //  Editar os dados do Computador

        $('#formAlterarComputador').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionAlterar').val();
            var codigo = $(this).find('input#codigoComputador-A').val();
            var nome = $(this).find('input#nomeComputador-A').val();
            var marca = $(this).find('select#marcaComputador-A').val();
            var processador = $(this).find('select#processadorComputador-A').val();
            var ram = $(this).find('input#ramComputador-A').val() + " " + $(this).find('select#ramUnidade-A').val();
            var hd = $(this).find('input#hdComputador-A').val() + " " + $(this).find('select#hdUnidade-A').val();
            var estado = $(this).find('select.select-estado-A').val();

            var sms = $("#formAlterarComputador .sms");

            var resultado = "";

            var array = [action, codigo, nome, marca, processador, ram, hd, estado];

            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }

                },
                success: function(response) {

                    var computador = response;

                    // Converte os dados num array

                    computador = (computador + "").split(',');

                    if (computador[0] == "sucesso") {

                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Sucesso</div></div>'
                        );

                        window.location.href = "library-searchBooks";

                    } else if (computador[0] == "nome existente") {
                        sms.html(
                            '<div class="alert d-flex align-items-center" style="padding:0px;color:#d10000;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Este nome já existe</div></div>'
                        );

                    }

                    setTimeout(function() {
                        sms.html("");
                    }, 2000);
                }

            });

        });

        //  Pegando os dados na modal para o alterar os dados do autor

        $(".editarAutor").click(function() {

            var id = $(this).children(".id").text();
            var action = "autor";
            var array = [action, id];

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

                    var autor = response;

                    // Converte os dados num array

                    autor = (autor + "").split(',');

                    // Preenchendo os campos obtidos para posteriormente serem alterados

                    $('#codigoAutor-A').val(autor[0]);
                    $('#nomeAutor-A').val(autor[1]);
                    $('#sobrenomeAutor-A').val(autor[2]);
                    $('#generoAutor-A').append('<option value="' + autor[3] + '"selected>' + autor[3] +
                        '</option>');
                    $('.paises').append('<option value="' + autor[4] + '"selected>' + autor[4] +
                        '</option>');
                    $('#informacoesAutor-A').val(autor[5]);

                }

            });
        });

        //  Editar os dados do Autor

        $('#formAlterarAutor').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionAlterar').val();
            var codigo = $(this).find('input#codigoAutor-A').val();
            var nome = $(this).find('input#nomeAutor-A').val();
            var sobrenome = $(this).find('input#sobrenomeAutor-A').val();
            var genero = $(this).find('select#generoAutor-A').val();
            var informacoes = $(this).find('input#informacoesAutor-A').val();
            var pais = $(this).find('select#paises').val();

            var sms = $("#formAlterarAutor .sms");

            var resultado = "";

            var array = [action, codigo, nome, sobrenome, genero, informacoes, pais];

            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }
                },
                success: function(response) {
                    sms.html(
                        '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Sucesso!</div></div>'
                    );

                    setTimeout(function() {
                        sms.html("");
                    }, 2000);

                    window.location.href = "library-searchBooks";
                }

            });

        });

        // Pegando os dados para editar os autores


        var codigo_editar_livro;

        function editar_livro(element) {

            codigo_editar_livro = $(element).attr("data-id");


            var action = "livro";

            var array = [action, codigo_editar_livro];

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

                    var livro = response;

                    // Converte os dados num array

                    livro = (livro + "").split(',');
                    substr = livro[14].substring(1, 1000);
                    autores = (substr + "").split('-');


                    $("#codigoLivro-A").val(livro[0]);
                    $("#titulo-A").val(livro[1]);
                    $("#subtitulo-A").val(livro[2]);
                    $("#isbn-A").val(livro[3]);
                    $("#ano-A").val(livro[4]);
                    $("#edicao-A").val(livro[5]);
                    $("#local-A").val(livro[6]);
                    $("#idioma-A").val(livro[7]);
                    $("#pagina-A").val(livro[8]);
                    $("#quantidade-A").val(livro[9]);

                    $('#select-e-book-A option:first-child').val(livro[10]);
                    $('#select-e-book-A option:first-child').text(livro[11]);
                    $('.select-e-book-A .filter-option-inner-inner').text(livro[11]);

                    $('#select-c-book-A option:first-child').val(livro[12]);
                    $('#select-c-book-A option:first-child').text(livro[13]);
                    $('.select-c-book-A .filter-option-inner-inner').text(livro[13]);
                    $('.select-c-book-A .text').text(livro[13]);


                    var nome = "";
                    for (let index = 0; index < autores.length; index++) {

                        $(".select-a-book-A option[value='" + autores[index] + "']").attr("selected",
                            "true");

                        if (autores.length == 2) {

                            nome = autores[1];

                            break;
                        } else {
                            if (index % 2 != 0) {
                                nome = autores[index] + " , " + nome;
                            }
                        }
                    }
                    $('.select-a-book-A .filter-option-inner-inner').text(nome);
                }

            });

        }


        $(".editarLivro").click(function() {

            var id = codigo_editar_livro;
            var action = "livro";

            var array = [action, id];

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

                    var livro = response;

                    // Converte os dados num array

                    livro = (livro + "").split(',');
                    substr = livro[14].substring(1, 1000);
                    autores = (substr + "").split('-');


                    $("#codigoLivro-A").val(livro[0]);
                    $("#titulo-A").val(livro[1]);
                    $("#subtitulo-A").val(livro[2]);
                    $("#isbn-A").val(livro[3]);
                    $("#ano-A").val(livro[4]);
                    $("#edicao-A").val(livro[5]);
                    $("#local-A").val(livro[6]);
                    $("#idioma-A").val(livro[7]);
                    $("#pagina-A").val(livro[8]);
                    $("#quantidade-A").val(livro[9]);

                    $('#select-e-book-A option:first-child').val(livro[10]);
                    $('#select-e-book-A option:first-child').text(livro[11]);
                    $('.select-e-book-A .filter-option-inner-inner').text(livro[11]);

                    $('#select-c-book-A option:first-child').val(livro[12]);
                    $('#select-c-book-A option:first-child').text(livro[13]);
                    $('.select-c-book-A .filter-option-inner-inner').text(livro[13]);
                    $('.select-c-book-A .text').text(livro[13]);


                    var nome = "";
                    for (let index = 0; index < autores.length; index++) {

                        $(".select-a-book-A option[value='" + autores[index] + "']").attr("selected",
                            "true");

                        if (autores.length == 2) {

                            nome = autores[1];

                            break;
                        } else {
                            if (index % 2 != 0) {
                                nome = autores[index] + " , " + nome;
                            }
                        }
                    }
                    $('.select-a-book-A .filter-option-inner-inner').text(nome);
                }

            });
        });

        // Editar os dados do livro

        $('#formAlterarLivro').submit(function(event) {

            event.preventDefault();

            var action = $(this).find('input#actionAlterar').val();
            var codigo = $(this).find('input#codigoLivro-A').val();
            var titulo = $(this).find('input#titulo-A').val();
            var sobretitulo = $(this).find('input#subtitulo-A').val();
            var editora = $(this).find('#select-e-book-A').val();
            var autor = $(this).find('#select-a-book-A').val();
            var categoria = $(this).find('#select-c-book-A').val();
            var isbn = $(this).find('input#isbn-A').val();
            var ano = $(this).find('input#ano-A').val();
            var edicao = $(this).find('input#edicao-A').val();
            var lancamento = $(this).find('input#local-A').val();
            var idioma = $(this).find('input#idioma-A').val();
            var pagina = $(this).find('input#pagina-A').val();
            var quantidade = $(this).find('input#quantidade-A').val();


            var autores = autor + "";
            var novo = autores.replace(/,/gi, "-");


            var array = [action, codigo, titulo, sobretitulo, editora, categoria, isbn, ano, edicao, lancamento,
                idioma, pagina, quantidade, novo
            ];

            $.ajax({
                url: 'library-edit-item/' + array,
                type: "post",
                data: $(this).serialize(),
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert("Página não encontrada");
                    }
                },
                success: function(response) {
                    window.location.href = "/gestao-academica/library-searchBooks";
                }

            });


            setTimeout(function() {

            }, 1000);


        });

        // ========================================== Eliminar Item =====================================================

        var codigo_eliminar_area = 0;

        // Pegar o id da categoria a ser eliminada

        function eliminar_area(element) {
            $(".modal-confirm h4").text('Eliminar " ' + $(element).attr("data-name") + ' " ?');
            codigo_eliminar_area = $(element).attr("data-id");
            $("#modalEliminarCategoria .modal-confirm p").show();
            $("#modalEliminarCategoria .material-icons").text("delete");
            $("#modalEliminarCategoria .btn-cancelar-categoria").text("Cancelar");
            $("#modalEliminarCategoria .btn-eliminar-categoria").show();

        }

        // eliminar categoria

        $(".btn-eliminar-categoria").click(


            function() {

                var action = "categoria";


                var array = [action, codigo_eliminar_area];

                $.ajax({
                    url: 'library-delete-item/' + array,
                    type: "post",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }

                    },
                    success: function(response) {

                        if (response[0] == "Eliminado") {

                            $("#nav-Categoria-tab .badge").text(parseInt($("#nav-Categoria-tab .badge")
                                    .text()) -
                                1);
                            $("#modalEliminarCategoria .modal-confirm h4").text(
                                'Área eliminada com sucesso!!!');
                            $("#modalEliminarCategoria .modal-confirm p").hide();
                            $("#modalEliminarCategoria .material-icons").text("check_circle");
                            $("#modalEliminarCategoria .btn-cancelar-categoria").text("Continuar");
                            $("#modalEliminarCategoria .btn-eliminar-categoria").hide();
                            window.location.href = "library-searchBooks";
                        } else {}
                    }

                });
            }
        );

        // Pegar o id do autor a ser eliminado

        var codigo_eliminar_autor;

        function eliminar_autor(element) {
            $(".modal-confirm h4").text('Eliminar " ' + $(element).attr("data-name") + ' " ?');
            codigo_eliminar_autor = $(element).attr("data-id");
            $("#modalEliminarAutor .modal-confirm p").show();
            $("#modalEliminarAutor .material-icons").text("delete");
            $("#modalEliminarAutor .btn-cancelar-autor").text("Cancelar");
            $("#modalEliminarAutor .btn-eliminar-autor").show();

        }

        // eliminar autor

        $(".btn-eliminar-autor").click(


            function() {

                var action = "autor";


                var array = [action, codigo_eliminar_autor];

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

                        if (response[0] == "Eliminado") {

                            $("#nav-Autor-tab .badge").text(parseInt($("#nav-Autor-tab .badge").text()) -
                                1);
                            $("#modalEliminarAutor .modal-confirm h4").text(
                                'Autor eliminado com sucesso!!!');
                            $("#modalEliminarAutor .modal-confirm p").hide();
                            $("#modalEliminarAutor .material-icons").text("check_circle");
                            $("#modalEliminarAutor .btn-cancelar-autor").text("Continuar");
                            $("#modalEliminarAutor .btn-eliminar-autor").hide();
                            window.location.href = "library-searchBooks";

                        } else {

                        }
                    }

                });
            }
        );

        var codigo_eliminar_editora;

        function eliminar_editora(element) {
            $(".modal-confirm h4").text('Eliminar " ' + $(element).attr("data-name") + ' " ?');
            codigo_eliminar_editora = $(element).attr("data-id");
            $("#modalEliminarEditora .modal-confirm p").show();
            $("#modalEliminarEditora .material-icons").text("delete");
            $("#modalEliminarEditora .btn-cancelar-editora").text("Cancelar");
            $("#modalEliminarEditora .btn-eliminar-editora").show();

        }


        // eliminar editora

        $(".btn-eliminar-editora").click(


            function() {

                var action = "editora";


                var array = [action, codigo_eliminar_editora];

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

                        if (response[0] == "Eliminado") {

                            $("#modalEliminarEditora .modal-confirm h4").text(
                                'Editora eliminada com sucesso!!!');
                            $("#modalEliminarEditora .modal-confirm p").hide();
                            $("#modalEliminarEditora .material-icons").text("check_circle");
                            $("#modalEliminarEditora .btn-cancelar-editora").text("Continuar");
                            $("#modalEliminarEditora .btn-eliminar-editora").hide();
                            window.location.href = "library-searchBooks";

                        } else {

                        }
                    }

                });
            }
        );

        // Pegar o id do autor a ser eliminado

        var codigo_eliminar_livro;

        function eliminar_livro(element) {
            $(".modal-confirm h4").text('Eliminar " ' + $(element).attr("data-name") + ' " ?');
            codigo_eliminar_livro = $(element).attr("data-id");

            $("#modalEliminarLivro .modal-confirm p").show();
            $("#modalEliminarLivro .material-icons").text("delete");
            $("#modalEliminarLivro .btn-cancelar-livro").text("Cancelar");
            $("#modalEliminarLivro .btn-eliminar-livro").show();
        }

        $(".btn-eliminar-livro").click(


            function() {

                var action = "livro";


                var array = [action, codigo_eliminar_livro];

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

                        console.log(response[0]);

                        if (response[0] == "Eliminado") {
                            $("#nav-Livro-tab .badge").text(parseInt($("#nav-Livro-tab .badge").text()) -
                                1);
                            $("#modalEliminarLivro .modal-confirm h4").text(
                                'Livro eliminado com sucesso!!!');
                            $("#modalEliminarLivro .modal-confirm p").hide();
                            $("#modalEliminarLivro .material-icons").text("check_circle");
                            $("#modalEliminarLivro .btn-cancelar-livro").text("Continuar");
                            $("#modalEliminarLivro .btn-eliminar-livro").hide();
                            window.location.href = "library-searchBooks";
                        } else {

                        }
                    }

                });
            }
        );

        // Adicionar livr ´

        // Pegar o id do autor a ser eliminado

        var codigo_adicionar_livro;

        function adicionar_livro(element) {

            codigo_adicionar_livro = $(element).attr("data-id");
            $("#formQuantidade #id_livro").val(parseInt(codigo_adicionar_livro));

        }



        $("#btn-create-item").on('click', function() {
         
            switch ($(".nav-tabs .active").attr("data")) {

                case "Livro":
                    window.open('library/new-item/livro', 'b_lank');
                    break;
                case "Área":
                    window.open('library/new-item/area', 'b_lank');
                    break;

                case "Autor":
                    window.open('library/new-item/autor', 'b_lank');
                    break;

                case "Computador":
                    window.open('library/new-item/computador', 'b_lank');
                    break;
                case "Editora":
                    window.open('library/new-item/editora', 'b_lank');
                    break;

                default:
                    break;
                }
        });


        // ============================ Criar relatórios ===================================

        $("#btn-pdf-item").on('click', function() {
            switch ($(".nav-tabs .active").attr("data")) {

                case "Livro":
                window.open('library-report-item-pdf/' + "livro", 'b_lank');
                    break;

                case "Área":
                window.open('library-report-item-pdf/' + "categoria", 'b_lank');
                    break;

                case "Autor":
                window.open('library-report-item-pdf/' + "autor", 'b_lank');
                    break;

                case "Computador":
                window.open('library-report-item-pdf/' + "computador", 'b_lank');
                    break;
                case "Editora":
                window.open('library-report-item-pdf/' + "editora", 'b_lank');
                    break;

                default:
                    break;
            }
        });

        $(".nav-tabs a").on('click', function() {
            $("#btn-create-item").html("<i class='fa fa-plus'></i> Criar " + $(this).attr("data"));

           
        });
    </script>
@endsection
