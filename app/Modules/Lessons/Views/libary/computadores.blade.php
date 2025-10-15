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

                        <h1 class="m-0 text-dark ">REGISTRA COMPUTADORES</h1>

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
                                            <label for="sobrenome">Marca</label>
                                            <select name="marcaComputador" id="marcaComputador" class="selectpicker form-control" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" tabindex="-98">
                                                <option></option>
                                                <option value="ACER">ACER</option>
                                                <option value="ASUS">ASUS</option>
                                                <option value="APPLE">APPLE</option>
                                                <option value="DELL">DELL</option>
                                                <option value="IBM">IBM</option>
                                                <option value="HP">HP</option>
                                                <option value="LENOVO">LENOVO</option>
                                                <option value="LG">LG</option>
                                                <option value="SAMSUNG">SAMSUNG</option>
                                                <option value="POSITIVO">POSITIVO</option>
                                                <option value="TOSHIBA">TOSHIBA</option>
                                                <option value="OUTRA">OUTRA</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="processador">Processador</label>
                                            <select name="processador" id="processador" class="selectpicker form-control" data-actions-box="true" data-selected-text-format="count > 3" data-live-search="true" tabindex="-98">
                                                <option value=""></option>
                                                <optgroup label="Apple">
                                                    <option value="Apple">Apple</option>
                                                </optgroup>
                                                <optgroup label="INTEL" style="font-weight: bold;">
                                                    <option value="Intel Pentium">Intel Pentium</option>
                                                    <option value="Intel Celeron">Intel Celeron</option>
                                                    <option value="Intel inside">Intel inside</option>
                                                    <option value="Intel Core 2">Intel Core 2</option>
                                                    <option value="Intel Core i3">Intel Core i3</option>
                                                    <option value="Intel Core i5">Intel Core i5</option>
                                                    <option value="Intel Core i7">Intel Core i7</option>
                                                    <option value="Intel Core i9">Intel Core i9</option>
                                                    <option value="Intel Xeon">Intel Xeon</option>
                                                </optgroup>
                                                <optgroup label="AMD" style="font-weight: bold;">
                                                    <option value="Athion">Athion</option>
                                                    <option value="Ryzen">Ryzen</option>
                                                    <option value="Phenom">Phenom</option>
                                                    <option value="Threadripper">Threadripper</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="">RAM</label>
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control  b-rad" style="border-radius: 5px 0px 0px 5px!important;" placeholder="" aria-label="Recipient's username" aria-describedby="button-addon2" name="ramComputador" id="ramComputador" required="">
                                                <select class="form-control col-4 b-rad" id="ram" name="ram" style="cursor:pointer;padding-left: 1px;border-radius: 0px 5px 5px 0px !important;" required="">
                                                    <option value="MB">MB </option>
                                                    <option value="GB">GB </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="">HD/SSD</label>
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control b-rad" style="border-radius: 5px 0px 0px 5px!important;" placeholder="" aria-label="Recipient's username" aria-describedby="button-addon2" name="hdComputador" id="hdComputador" required="">
                                                <select class="form-control col-4 b-rad" id="hd" name="hd" style="cursor:pointer;padding-left: 1px;border-radius: 0px 5px 5px 0px !important;" required="">
                                                    <option value="GB">GB </option>
                                                    <option value="TB">TB </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" mt-3">
                                        <button type="submit" class="btn btn-success s-e criarlivro"
                                            style="border-radius: 5px; letter-spacing: 1px;"><i class="fas fa-plus"
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
