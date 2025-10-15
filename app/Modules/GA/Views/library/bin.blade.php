<!--F4k3-->
@section('title', __('Biblioteca - ForLibrary'))
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



<!-- Temas em destaque -->
{{-- <style>
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
        padding-top: 3% !important;
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
        margin-left: 5%;
        color: white;
        padding: 5px 10px!important;   
        background-color: #dc3545 !important;   
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

</style> --}}

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
    }

    .nav-tabs .active {
        color: #ffffff !important;
        background-color: #0060af !important;
        border-color: #0060af !important;
        transform: translateY(2px);
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
        margin-right: 80px;
        color: white;
        padding: 5px 10px !important;
        float: right;
        background-color: #e7193b !important;
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
@section('content')

    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0">

        @include('GA::library.modal.layout') 
        @include('GA::library.modal.recycle') 

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('RESTAURAR ITENS')</h1>   
                    </div>
                    <div class="col-sm-6">
                        
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">

 
                        <div class="row">
                            <div class="col-md-12">
                                <nav class="col-md-8">
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link" id="nav-Autor-tab" data-toggle="tab" href="#nav-Autor"
                                        role="tab" aria-controls="nav-Autor" aria-selected="false">Autores
                                        <i class="fa fa-user"></i>
                                        <span class="badge">

                                            @php
                                                $total = count($autores);
                                                
                                                echo $total;
                                            @endphp

                                        </span></a>

                                    <a class="nav-item nav-link" id="nav-Categoria-tab" data-toggle="tab"
                                        href="#nav-Categoria" role="tab" aria-controls="nav-Categoria"
                                        aria-selected="false">Áreas
                                        <i class="fa fa-sort-alpha-asc"></i>
                                        <span class="badge ">
                                            @php
                                                $total = count($categorias);
                                                
                                                echo $total;
                                            @endphp
                                        </span>

                                    </a>

                                    <a class="nav-item nav-link" id="nav-Editora-tab" data-toggle="tab"
                                        href="#nav-Editora" role="tab" aria-controls="nav-Editora"
                                        aria-selected="false">Editoras
                                        <i class="fa fa-edit"></i>
                                        <span class="badge ">
                                            @php
                                                $total = count($editoras);
                                                
                                                echo $total;
                                            @endphp
                                        </span>

                                    </a>

                                    <a class="nav-item nav-link active" id="nav-Livro-tab" data-toggle="tab"
                                        href="#nav-Livro" role="tab" aria-controls="nav-Livro"
                                        aria-selected="true">Livros
                                        <i class="fa fa-book"></i>
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

                                            <table id="tabela-Autor" class="table  table-striped ">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Código</th>
                                                        <th>Nome Completo</th>
                                                        <th>Sobrenome</th>
                                                        <th>Gênero</th>
                                                        <th>País</th>
                                                        <th>Eiminado por</th>
                                                        <th>Eliminado a</th>
                                                        <th>Reciclar</th>

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
                                                            <th> {{ $item->deleted_by }} </th>
                                                            <th> {{ $item->deleted_at }} </th>



                                                            <th>
                                                                
                                                                <button data-target="#modalRestaurarAutor"
                                                                    data-toggle="modal"
                                                                    class="btn btn-danger btn-sm restaurarAutor"
                                                                    style="width:30px;">
                                                                    <i class="fas fas fa-recycle"></i>
                                                                    <p class="d-none name">{{ $item->name }}</p>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
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


                                            <table id="tabela-Categoria" class="table  table-striped ">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>Código</th> --}}
                                                        <th>CDD / CDU</th> 
                                                        <th>Nome</th>
                                                        <th>Eiminado por</th>
                                                        <th>Eliminado a</th>
                                                        <th>Reciclar</th>
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
                                                            <th> {{ $item->deleted_by }} </th>
                                                            <th> {{ $item->deleted_at }} </th>
                                                            <th>
                                                               
                                                                <button data-target="#modalRestaurarCategoria"
                                                                    data-toggle="modal"
                                                                    class="btn btn-danger btn-sm restaurarCategoria"
                                                                    style="width:30px;">
                                                                    <i class="fas fas fa-recycle"></i>
                                                                    <p class="d-none name">{{ $item->name }}</p>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
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


                                            {{-- Tabela para a listagem dos Editora --}}

                                            <table id="tabela-Editora" class="table  table-striped ">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>código</th> --}}
                                                        <th>Nome</th>
                                                        <th>email</th>
                                                        <th>Endereço</th>
                                                        <th>Emai</th>
                                                        <th>Cidade</th>
                                                        <th>País</th>
                                                        <th>Eiminado por</th>
                                                        <th>Eliminado a</th>
                                                        <th>Reciclar</th>
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
                                                            <th> {{ $item->email }} </th>
                                                            <th> {{ $item->city }} </th>
                                                            <th> {{ $item->country }} </th>
                                                            <th> {{ $item->deleted_by }} </th>
                                                            <th> {{ $item->deleted_at }} </th>
                                                            <th>
                                                                
                                                                <button data-target="#modalRestaurarEditora"
                                                                    data-toggle="modal"
                                                                    class="btn btn-danger btn-sm restaurarEditora"
                                                                    style="width:30px;">
                                                                    <i class="fas fas fa-recycle"></i>
                                                                    <p class="d-none name">{{ $item->name }}</p>
                                                                    <p class="d-none id">{{ $item->id }}</p>
                                                                </button>
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
                                                        <th>Idioma</th>
                                                        <th>Número de chamada</th>
                                                        <th>Total</th>
                                                        <th>Eiminado por</th>
                                                        <th>Eliminado a</th>
                                                        <th>Reciclar</th>
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
                                                            <th>
                                                                 
                                                                <button data-target="#modalRestaurarLivro"
                                                                    data-toggle="modal"
                                                                    class="btn btn-danger btn-sm restaurarLivro"
                                                                    style="width:30px;">
                                                                    <i class="fas fas fa-recycle"></i>
                                                                    <p class="d-none name">{{ $item[0] }}</p>
                                                                    <p class="d-none id">{{ $item[11] }}</p>
                                                                </button>
                                                            </th>

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>


                                    </div>

                                </div>

                                {{-- </div>
                                        </div>
                                    </div>
                                </div> --}}
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
                    'colvis'
                    //   ,{
                    //       text: "Todos",
                    //       className:"btn"
                    //     }

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });

        });

 
        // Tabela categoria

        $(function() {


            let table = $('#tabela-Categoria').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis'
                    //   ,{
                    //       text: "Todos",
                    //       className:"btn"
                    //     }

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });

        });

 

        // Tabela Editoras

        $(function() {


            let table = $('#tabela-Editora').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis'
                    //   ,{
                    //       text: "Todos",
                    //       className:"btn"
                    //     }

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });

        });


        // Tabela Autor

        $(function() {


            let table = $('#tabela-Autor').DataTable({
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 25, 50, 100],
                orderable: false,
                paging: true,

                buttons: [
                    'colvis'
                    //   ,{
                    //       text: "Todos",
                    //       className:"btn"
                    //     }

                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                }

            });

        });


        // ========================================== Restaurar Item =====================================================

        var codigo_restaurar = 0;

        // Pegar o id da categoria a ser restaurada

        $(".restaurarCategoria").click(
            function() {
                $(".modal-confirm h4").text('Restaurar " ' + $(this).children(".name").text() + ' " ?');
                codigo_restaurar = $(this).children(".id").text();
                $("#modalRestaurarCategoria .modal-confirm p").show();
                $("#modalRestaurarCategoria .material-icons").text("info");
                $("#modalRestaurarCategoria .btn-cancelar-categoria").text("Cancelar");
                $("#modalRestaurarCategoria .btn-restaurar-categoria").show();
            }
        );

        // restaurar categoria

        $(".btn-restaurar-categoria").click(


            function() {

                var action = "categoria";


                var array = [action, codigo_restaurar];

                $.ajax({
                    url: 'library-recycle-item/' + array,
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

                        $("#nav-Categoria-tab .badge").text(parseInt($("#nav-Categoria-tab .badge").text())-1);

                        $("#modalRestaurarCategoria .modal-confirm h4").text(
                            'Categoria eliminada com sucesso!!!');
                        $("#modalRestaurarCategoria .modal-confirm p").hide();
                        $("#modalRestaurarCategoria .material-icons").text("check_circle");
                        $("#modalRestaurarCategoria .btn-cancelar-categoria").text("Continuar");
                        $("#modalRestaurarCategoria .btn-restaurar-categoria").hide();

                    }

                });
            }
        );

        // Pegar o id do autor a ser restaurado

        $(".restaurarAutor").click(
            function() {
                $(".modal-confirm h4").text('Restaurar " ' + $(this).children(".name").text() + ' " ?');
                codigo_restaurar = $(this).children(".id").text();
                $("#modalRestaurarAutor .modal-confirm p").show();
                $("#modalRestaurarAutor .material-icons").text("info");
                $("#modalRestaurarAutor .btn-cancelar-autor").text("Cancelar");
                $("#modalRestaurarAutor .btn-restaurar-autor").show();
            }
        );

        // restaurar autor

        $(".btn-restaurar-autor").click(


            function() {

                var action = "autor";


                var array = [action, codigo_restaurar];

                $.ajax({
                    url: 'library-recycle-item/' + array,
                    type: "post",
                    data: $(this).serialize(),
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }

                    },
                    success: function(response) {
                        $("#nav-Autor-tab .badge").text(parseInt($("#nav-Autor-tab .badge").text())-1);
                        $("#modalRestaurarAutor .modal-confirm h4").text('Autor restaurado com sucesso!!!');
                        $("#modalRestaurarAutor .modal-confirm p").hide();
                        $("#modalRestaurarAutor .material-icons").text("check_circle");
                        $("#modalRestaurarAutor .btn-cancelar-autor").text("Continuar");
                        $("#modalRestaurarAutor .btn-restaurar-autor").hide();
                    }

                });
            }
        );

        // Pegar o id da editora a ser restaurado
        $(".restaurarEditora").click(
            function() {
                $(".modal-confirm h4").text('Restaurar " ' + $(this).children(".name").text() + ' " ?');
                codigo_restaurar = $(this).children(".id").text();
                $("#modalRestaurarEditora .modal-confirm p").show();
                $("#modalRestaurarEditora .material-icons").text("info");
                $("#modalRestaurarEditora .btn-cancelar-editora").text("Cancelar");
                $("#modalRestaurarEditora .btn-restaurar-editora").show();

            }
        );

        // restaurar editora

        $(".btn-restaurar-editora").click(


            function() {

                var action = "editora";
   
                var array = [action, codigo_restaurar];

                $.ajax({
                    url: 'library-recycle-item/' + array,
                    type: "post",
                    data: $(this).serialize(),
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }

                    },
                    success: function(response) {
                        $("#nav-Editora-tab .badge").text(parseInt($("#nav-Editora-tab .badge").text())-1);
                        $("#modalRestaurarEditora .modal-confirm h4").text(
                            'Editora eliminada com sucesso!!!');
                        $("#modalRestaurarEditora .modal-confirm p").hide();
                        $("#modalRestaurarEditora .material-icons").text("check_circle");
                        $("#modalRestaurarEditora .btn-cancelar-editora").text("Continuar");
                        $("#modalRestaurarEditora .btn-restaurar-editora").hide();
                    }

                });
            }
        );

        // Pegar o id do livro a ser restaurado

        $(".restaurarLivro").click(
            function() {
                $(".modal-confirm h4").text('Restaurar " ' + $(this).children(".name").text() + ' " ?');
                codigo_restaurar = $(this).children(".id").text();
                $("#modalRestaurarLivro .modal-confirm p").show();
                $("#modalRestaurarLivro .material-icons").text("info");
                $("#modalRestaurarLivro .btn-cancelar-livro").text("Cancelar");
                $("#modalRestaurarLivro .btn-restaurar-livro").show();
            }
        );

        // restaurar Livro

        $(".btn-restaurar-livro").click(


            function() {

                var action = "livro";


                var array = [action, codigo_restaurar];

                $.ajax({
                    url: 'library-recycle-item/' + array,
                    type: "post",
                    data: $(this).serialize(),
                    dataType: 'json',
                    statusCode: {
                        404: function() {
                            alert("Página não encontrada");
                        }
                    }, 
                    success: function(response) {
                        $("#nav-Livro-tab .badge").text(parseInt($("#nav-Livro-tab .badge").text())-1);
                        $("#modalRestaurarLivro .modal-confirm h4").text('Livro restaurado com sucesso!!!');
                        $("#modalRestaurarLivro .modal-confirm p").hide();
                        $("#modalRestaurarLivro .material-icons").text("check_circle");
                        $("#modalRestaurarLivro .btn-cancelar-livro").text("Continuar");
                        $("#modalRestaurarLivro .btn-restaurar-livro").hide();
                    }

                });
            }
        );

        // Criando uma cookie para manipular a expançao da tela
    </script>
@endsection
