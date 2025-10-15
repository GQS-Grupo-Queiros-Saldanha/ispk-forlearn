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
    }

    .nav-tabs a:active {
        transform: translateY(1px) !important;
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
        color: white;
        padding: 5px 10px !important;
        float: right;
        background-color: #1986e7 !important;
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

    .tab-Content {
        border-top: 1px solid #0060af7a !important;
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
</style>
@include('GA::library.modal.view-book')
@include('GA::library.modal.final')
@include('GA::library.modal.modal')
@section('content')

    <!-- Pesquisa -->
    <div class="content-panel" style="padding: 0">
        @include('GA::library.modal.layout')

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1 class="m-0 text-dark">@lang('Gerir Requisições')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>
                    <div class="col-12"> <br> <br>
                        @if (auth()->user()->hasAnyPermission(['library_manage_request']))
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
                            <div class="col-12">

                                <nav class="col-4">
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link" id="nav-Requisicao-Computador-tab" data-toggle="tab"
                                            href="#nav-Requisicao-Computador" role="tab" data="Computador"
                                            aria-controls="nav-Requisicao-Computador" aria-selected="true">

                                            <i class="fa fa-desktop"></i>
                                            Computadores

                                            <span class="badge ">
                                                @php
                                                    $total = count($requisicao_computador);
                                                    
                                                    echo $total;
                                                @endphp

                                            </span>

                                        </a>

                                        <a class="nav-item nav-link active" id="nav-Requisicao-Livro-tab" data-toggle="tab"
                                            href="#nav-Requisicao-Livro" role="tab" aria-controls="nav-Requisicao-Livro"
                                            data="Livro" aria-selected="true">

                                            <i class="fa fa-book"></i>
                                            Livros

                                            <span class="badge ">
                                                @php
                                                    $total = count($requisicao_livro);
                                                    
                                                    echo $total;
                                                @endphp

                                            </span>

                                        </a>

                                    </div>
                                </nav>

                                <div class="tab-content" id="nav-tabContent">

                                    <div class="tab-pane fade show" id="nav-Requisicao-Computador" role="tabpanel"
                                        aria-labelledby="nav-Requisicao-Computador-tab">

                                        <div class="col">

                                            <div class="row">

                                                <div class="row">

                                                    <div class="col-4" style="margin-left: 15px">

                                                        <div class="mb-4">
                                                            <label for="dataInicio_computador" class="form-label">Data
                                                                de
                                                                início</label>
                                                            <input type="date" class="form-control"
                                                                id="dataInicio_computador" name="dataInicio_computador"
                                                                style="width: 220px;cursor:pointer;"
                                                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                                                required>
                                                        </div>

                                                    </div>

                                                    <div class="col-4">

                                                        <div class="mb-4">
                                                            <label for="dataFim_computador" class="form-label">Data de
                                                                fim</label>
                                                            <input type="date" class="form-control"
                                                                id="dataFim_computador" name="dataFim_computador"
                                                                style="width: 220px;cursor:pointer;"
                                                                max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                                                required>

                                                        </div>

                                                    </div>

                                                    <div class="col-2">

                                                        <div class="mb-4">
                                                            <label for="dataFim" class="form-label">Filtrar por:</label>
                                                            <select type="date" class="form-control"
                                                                id="F_estado_computador" name="F_estado_computador"
                                                                style="width: 220px;cursor:pointer;" required>

                                                                <option value="Em curso">Em curso</option>>
                                                                <option value="Finalizada">Finalizada</option>
                                                                <option value="Todas" selected>Todas </option>

                                                            </select>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                            {{-- Tabela para a listagem das requisicoes de livros --}}

                                            <table id="tabela-Requisicao-computador"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Recibo</th>
                                                        <th>Requerente</th>
                                                        <th>email</th>
                                                        <th>Computador</th>
                                                        <th>Data requisição</th>
                                                        <th>Hora inicio</th>
                                                        <th>Hora fim</th>
                                                        <th>Estado</th>
                                                        <th>Actividades</th>
                                                    </tr>
                                                </thead>

                                            </table>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade show active" id="nav-Requisicao-Livro" role="tabpanel"
                                        aria-labelledby="nav-Requisicao-Livro-tab">

                                        <div class="col">

                                            <div class="row">

                                                <div class="row">

                                                    <div class="col-4" style="margin-left: 15px">

                                                        <div class="mb-4">
                                                            <label for="dateInicio" class="form-label">Data de
                                                                início</label>
                                                            <input type="date" class="form-control" id="dataInicio"
                                                                name="dataInicio" style="width: 220px;cursor:pointer;"
                                                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}"
                                                                required>
                                                        </div>

                                                    </div>

                                                    <div class="col-4">

                                                        <div class="mb-4">
                                                            <label for="dataFim" class="form-label">Data de fim</label>
                                                            <input type="date" class="form-control" id="dataFim"
                                                                name="dataFim" style="width: 220px;cursor:pointer;"
                                                                max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                                                required>

                                                        </div>

                                                    </div>

                                                    <div class="col-2">

                                                        <div class="mb-4">
                                                            <label for="dataFim" class="form-label">Filtrar por:</label>
                                                            <select type="date" class="form-control"
                                                                id="F_estado_livro" name="F_estado_livro"
                                                                style="width: 220px;cursor:pointer;" required>

                                                                <option value="Em curso">Em curso</option>>
                                                                <option value="Finalizada">Finalizada</option>
                                                                <option value="Todas" selected>Todas </option>

                                                            </select>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>


                                            {{-- Tabela para a listagem das requisicoes de livros --}}

                                            <table id="tabela-Requisicao-Livro"
                                                class="table table-striped table-hover dataTable no-footer dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Recibo</th>
                                                        <th>Requerente</th>
                                                        <th>email</th>
                                                        <th>Data requisição</th>
                                                        <th>Data devolução</th>
                                                        <th>Estado</th>
                                                        <th>Actividades</th>
                                                    </tr>
                                                </thead>

                                            </table>
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


    {{-- modal confirm --}}

    @include('layouts.backoffice.modal_confirm')

@endsection
@section('scripts')
    @parent
    <script>
        // $(".dataTables_length label").text("");

        // Maximizar e minimizar a tela da biblioteca


        var cookies = document.cookie;

        var nova = cookies.split(";");

        if (nova[0] == "tela=cheia") {
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
            $(".btn-logout").removeClass("col-6");
            $(".btn-logout").addClass("col-7");
            $(".content-panel").css({
                marginTop: '0px'
            });
        }

        $(".tirar").click(function() {

            var cookies = document.cookie;

            var nova = cookies.split(";");

            if (nova[0] == "tela=cheia") {

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
                $(".btn-logout").removeClass("col-7");
                $(".btn-logout").addClass("col-6");
                $(".content-panel").css({
                    marginTop: '14px'
                });


                document.cookie = "tela=normal";

            } else if (nova[0] == "tela=normal") {

                $(".left-side-menu,.top-bar").hide();
                $(".btn-logout").show();
                $(".btn-logout").removeClass("col-6");
                $(".btn-logout").addClass("col-7");
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


        // ===================================================================


        // Tabela requisição de livros...

        $(function() {

            let table = $('#tabela-Requisicao-Livro').DataTable({
                destroy: true,
                searching: false,
                serverSide: false,
                processing: false,
                aLengthMenu: [6, 10, 20, 50],
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

        // Tabela requisição de Computadores...

        $(function() {

            let table = $('#tabela-Requisicao-Computador').DataTable({
                destroy: true,
                searching: false,
                serverSide: false,
                processing: false,
                aLengthMenu: [6, 10, 20, 50],
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

        // Criar a datatable para listagem das requisições dos livros requeridos em função do estado 

        function tabela_estado_livros(estado) {

            let tabela = $('#tabela-Requisicao-Livro').DataTable({
                destroy: true,
                searching: true,
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 50, 100, 500],
                orderable: false,
                paging: true,
                buttons: [
                    'colvis','excel' 
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                },
                "ajax": {
                    "url": "library-get_filter-loan/" + estado,
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'leitor_email',
                        name: 'leitor_email'
                    },
                    // {
                    //     data: 'livro_titulo',
                    //     name: 'livro_titulo'
                    // },
                    // {
                    //     data: 'livro_isbn',
                    //     name: 'livro_isbn'
                    // },

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

        // Criar a datatable para listagem das requisições dos computadores em função do estado 

        function tabela_estado_computadores(estado) {

            let tabela = $('#tabela-Requisicao-computador').DataTable({
                destroy: true,
                searching: true,
                serverSide: false,
                processing: false,
                aLengthMenu: [10, 50, 100, 500],
                orderable: false,
                paging: true,
                buttons: [
                    'colvis','excel' 
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                },
                "ajax": {
                    "url": "library-get_filter-loan-computer/" + estado,
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'nome_computador',
                        name: 'nome_computador'
                    },
                    // {
                    //     data: 'livro_isbn',
                    //     name: 'livro_isbn'
                    // },

                    {
                        data: 'data_requisicao',
                        name: 'data_requisicao'
                    },
                    {
                        data: 'hora_requisicao',
                        name: 'hora_requisicao'
                    },
                    {
                        data: 'hora_final',
                        name: 'hora_final'
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
 

        // Quando mudar o estado, listar novamente as requisições dos livros

        $("#F_estado_livro").on('change', function() {

            var estado = $(this).val();
            tabela_estado_livros(estado);

        });

        // Quando mudar o estado, listar novamente as requisições dos computadores

        $("#F_estado_computador").on('change', function() {

            var estado = $(this).val();
            tabela_estado_computadores(estado);

        });


        // Listar todos as requisições dos livros

        tabela_estado_livros("Todas");

        // Listar todos as requisições dos computadores

        tabela_estado_computadores("Todas");

        var codigo_devolucao = 0;

        $(".devolverLivro").click(
            function() {
                $(".modal-confirm h4").text('Finalizar requisição?');
                codigo_devolucao = $(this).children(".id").text();
                $("#modalDevolverLivro .modal-confirm p").show();
                $("#modalDevolverLivro .material-icons").text("info");
                $("#modalDevolverLivro .btn-cancelar-livro").text("Cancelar");
                $("#modalDevolverLivro .btn-eliminar-livro").show();


            }
        );

        // devolucao de Livro

        $(".btn-devolver-livro").click(


            function() {

                var action = "devolucao";


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
                        $("#modalDevolverLivro .modal-confirm h4").text(
                            'Requisicão finalizada com sucesso!!!');
                        $("#modalDevolverLivro .modal-confirm p").hide();
                        $("#modalDevolverLivro .material-icons").text("check_circle");
                        $("#modalDevolverLivro .btn-cancelar-livro").text("Continuar");
                        $("#modalDevolverLivro .btn-devolver-livro").hide();
                        $(".requisicao-" + codigo_devolucao + "").removeClass("estado-andamento");
                        $(".requisicao-" + codigo_devolucao + "").addClass("estado-finalizado");
                        $(".requisicao-" + codigo_devolucao + "").text("Finalizada");
                        btn_devolver.hide();
                    }

                });
            }
        );


        // Finalizar requisiçao de livros

        var codigo_devolucao = 0;
        var btn_devolver = "";
        var btn_devolver_computador = "";

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

                        $("#modalComputadorFinalizar .modal-confirm h4").text(
                            'Requisicão finalizada com sucesso!!!');
                        $("#modalComputadorFinalizar .modal-confirm p").hide();
                        $("#modalComputadorFinalizar .material-icons").text("check_circle");
                        $("#modalComputadorFinalizar .btn-cancelar-livro").text("Continuar");
                        $("#modalComputadorFinalizar .btn-finalizar").hide();
                        $(".requisicao-computador-" + codigo_devolucao + "").removeClass(
                            "estado-andamento");
                        $(".requisicao-computador-" + codigo_devolucao + "").addClass("estado-finalizado");
                        $(".requisicao-computador-" + codigo_devolucao + "").text("Finalizada");
                        btn_devolver_computador.hide();


                    }

                });
            }
        );
    </script>
    <script>
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


        $("#btn-create-item").on('click', function() {

            switch ($(".nav-tabs .active").attr("data")) {
                case "Livro":
                    window.location.href = "{{ route('library-loan') }}";
                    break;
                case "Computador":
                    window.location.href = "{{ route('library-computer-loan') }}";
                    break;
                default:
                    break;
            }

        });


        // ============================ Criar relatórios ===================================

        $("#btn-pdf-item").on('click', function() {
            switch ($(".nav-tabs .active").attr("data")) {

                case "Livro":
                    var inicio = $("#dataInicio").val();
                    var fim = $("#dataFim").val();
                    var estado = $("#F_estado_livro").val();

                    // Se a data inicial for maior que a data final

                    if (inicio > fim) {

                        window.open('library-reports-pdf/' + fim + "/" + inicio + "/" + estado, '_blank');

                    } else {

                        window.open('library-reports-pdf/' + inicio + "/" + fim + "/" + estado, '_blank');

                    }
                    break;

                case "Computador":

                    var inicio = $("#dataInicio_computador").val();
                    var fim = $("#dataFim_computador").val();
                    var estado = $("#F_estado_computador").val();

                    // Se a data inicial for maior que a data final

                    if (inicio > fim) {
                        window.open('library-reports-computer-pdf/' + fim + "/" + inicio + "/" + estado,
                            '_blank');
                    } else {
                        window.open('library-reports-computer-pdf/' + inicio + "/" + fim + "/" + estado,
                            '_blank');
                    }
                    break;

                default:
                    break;
            }
        });

        $(".nav-tabs a").on('click', function() {
            $("#btn-create-item").html("<i class='fa fa-plus'></i> Requerer " + $(this).attr("data"));
        });
    </script>
@endsection
