<!--F4k3-->
@section('title', __('Novo'))
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

                        <h1 class="m-0 text-dark ">
                            @switch($type)
                            @case("autor")
                            REGISTRAR AUTOR
                            
                            @break
                            @case("editora")
                            REGISTRAR EDITORA
                            
                            @break
                            @case("computador")
                            REGISTRAR COMPUTADOR
                            
                            @break
                            @case("area")
                            REGISTRAR ÁREA
                                    
                                    @break
                                @default
                                    
                            @endswitch
                        </h1>

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
                                @switch($type)
                                    @case('autor')
                                       
                                           
                                            <div class="modal-body">

                                                <div class="col-12">

                                                    {{-- Pegando o tipo de acção --}}

                                                    <div class="row">

                                                        <div class="col-6">

                                                            {{-- ================== Código de identificação ========================== --}}

                                                            <input type="text" class="form-control d-none" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                id="action" required aria-required="Título do livro"
                                                                name="action" value="autor">



                                                            <label class="">Nome</label>

                                                            <div class="input-group mb-3">

                                                                <input type="text" class="form-control b-rad " placeholder=""
                                                                    aria-label="Recipient's username"
                                                                    aria-describedby="button-addon2" id="nome" required
                                                                    aria-required="Título do livro" name="nome">


                                                            </div>
                                                        </div>

                                                        <div class="col-6">

                                                            <label class="">Sobrenome</label>

                                                            <div class="input-group mb-3">

                                                                <input type="text" class="form-control b-rad" placeholder=""
                                                                    aria-label="Recipient's username"
                                                                    aria-describedby="button-addon2" id="sobrenome" required
                                                                    aria-required="Título do livro" name="sobrenome">


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="row">


                                                        <div class="col-6">
                                                            <label class="">Sexo</label>

                                                            <div class="input-group mb-3">

                                                                <select name="sexo" id="genero" name="genero"
                                                                    class="form-control b-rad" required>

                                                                    <option value="Feminino">Feminino</option>
                                                                    <option value="Masculino">Masculino</option>

                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="">País</label>

                                                            <div class="input-group mb-3">

                                                                @include('GA::library.paises')
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="col-12">

                                                    <div class="row">
                                                        <div class="col-2">
                                                            <label class="">Código do autor</label>

                                                            <div class="input-group mb-3">

                                                                <input type="text" class="form-control b-rad" placeholder=""
                                                                    aria-label="Recipient's username"
                                                                    aria-describedby="button-addon2" id="codigo"
                                                                    name="codigo" required>

                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                                <div class="col-12 mt-2">

                                                    <div class="row">

                                                        <div class="col-6">

                                                            <button type="submit" class="btn btn-success col-4 s-a"
                                                                style="border-radius: 5px;"><i class="fa fa-plus"></i>
                                                                Criar</button>
                                                            <button type="reset" class="btn btn-secondary s-e"
                                                                style="border-radius: 5px;">Limpar</button>
                                                        </div>

                                                        <div class="col-3 sms">

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                      
                                    @break

                                    @case('area')
                                           

                                            <input type="text" class="form-control d-none" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2" id="action"
                                                required aria-required="Título do livro" name="action" value="area">

                                            <div class="col-12">
                                                <div class="row">


                                                    <div class="col-2">
                                                        <label class="">CDD / CDU ( ex: 120 )</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad " placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="cdu" id="cdu" required>

                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="">Nome</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="nome" id="nome" required>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-12">

                                                <div class="row">

                                                    <div class="col-6">

                                                        <button type="submit" class="btn btn-success col-4 s-c"
                                                            style="border-radius: 5px;"><i class="fa fa-plus"></i> Criar</button>

                                                    </div>

                                                    <div class="col-3 sms">

                                                    </div>
                                                </div>
                                            </div>

                                    @break

                                    @case('editora')
                                       
                                            <input type="text" class="form-control d-none" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="action" required aria-required="Título do livro" name="action"
                                                value="editora">

                                            <div class="col-12">

                                                <div class="row">

                                                    <div class="col-6">

                                                        <label class="">Nome</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" name="nome"
                                                                id="nome" required>

                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <label class="">Endereço</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="endereco" id="endereco" required>


                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12">
                                                <div class="row">




                                                    <div class="col-6">
                                                        <label class="">Email</label>

                                                        <div class="input-group mb-3">

                                                            <input type="email" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="email" id="email" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="">Cidade</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="cidade" id="cidade" required>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="">País</label>

                                                        <div class="input-group mb-3">

                                                            @include('GA::library.paises')
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="row">

                                                    <div class="col-6">

                                                        <button type="submit" class="btn btn-success col-4 s-e"
                                                            style="border-radius: 5px;"><i class="fa fa-plus"></i> Criar</button>
                                                        <button type="reset" class="btn btn-secondary col-4 s-e"
                                                            style="border-radius: 5px;">Limpar</button>
                                                    </div>
                                                    <div class="col-3 sms">

                                                    </div>
                                                </div>
                                            </div>
                                     
                                    @break

                                    @case('computador')
                                     

                                            <input type="text" class="form-control d-none" placeholder=""
                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                id="action" required name="action" value="computador">

                                            <div class="col-12">

                                                <div class="row">

                                                    <div class="col-6">

                                                        <label class="">Nome</label>

                                                        <div class="input-group mb-3">

                                                            <input type="text" class="form-control b-rad" placeholder=""
                                                                aria-label="Recipient's username" name="nome"
                                                                id="nome" required>

                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <label class="">Marca</label>

                                                        <div class="input-group mb-3">

                                                            <select name="marca" id="marca"
                                                                class="selectpicker form-control" data-actions-box="true"
                                                                data-selected-text-format="count > 3" data-live-search="true">
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
                                                </div>

                                            </div>

                                            <div class="col-12">
                                                <div class="row">




                                                    <div class="col-6">
                                                        <label class="">Processador</label>

                                                        <div class="input-group mb-3">


                                                            <select name="processador" id="processador"
                                                                class="selectpicker form-control" data-actions-box="true"
                                                                data-selected-text-format="count > 3" data-live-search="true">
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
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="">RAM</label>

                                                        <div class="input-group mb-3">

                                                            <input type="number" class="form-control  b-rad"
                                                                style="border-radius: 5px 0px 0px 5px!important;" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="ram" id="ram" required>

                                                            <select class="form-control col-4 b-rad" id="ramUnidade"
                                                                name="ramUnidade"
                                                                style="cursor:pointer;padding-left: 1px;border-radius: 0px 5px 5px 0px !important;"
                                                                required>
                                                                <option value="MB">MB </option>
                                                                <option value="GB">GB </option>

                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="">HD / SSD</label>

                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control b-rad"
                                                                style="border-radius: 5px 0px 0px 5px!important;" placeholder=""
                                                                aria-label="Recipient's username" aria-describedby="button-addon2"
                                                                name="hd" id="hd" required>

                                                            <select class="form-control col-4 b-rad" id="hdUnidade"
                                                                name="hdUnidade"
                                                                style="cursor:pointer;padding-left: 1px;border-radius: 0px 5px 5px 0px !important;"
                                                                required>

                                                                <option value="GB">GB </option>
                                                                <option value="TB">TB </option>

                                                            </select>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="row">

                                                    <div class="col-6">

                                                        <button type="submit" class="btn btn-success col-4 s-e"
                                                            style="border-radius: 5px;"><i class="fa fa-plus"></i> Criar</button>
                                                        <button type="reset" class="btn btn-secondary col-4 s-e"
                                                            style="border-radius: 5px;">Limpar</button>
                                                    </div>
                                                    <div class="col-3 sms">

                                                    </div>
                                                </div>
                                            </div>
                                 
                                    @break

                                    @default
                                @endswitch
                                {!! Form::close() !!}


                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
