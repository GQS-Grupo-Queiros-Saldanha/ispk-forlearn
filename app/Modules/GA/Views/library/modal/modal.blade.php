 <style>
     .b-rad {
         border-radius: 5px !important;
         /* border-bottom: 3px solid #076DF2;
          !important;
         background-color: #eee !important; */
     }

     .m-header {
         background-color: #076DF2 !important;
         color: white;
         margin-bottom: 3%;
     }

     .b-cont {
         border-bottom: 5px solid #076DF2;
          !important;
         border-bottom-left-radius: 10px;
         border-bottom-right-radius: 10px;
     }

     .modal-body span {
         color: black !important;
     }

     .modal-header span {
         color: black !important;
     }
 </style>
 <style>
     .modal-confirm {
         color: #434e65;
         width: 525px;
         font-family: Roboto Slab, serif;
     }




     .modal-confirm .modal-content {
         padding: 20px;
         font-size: 16px;
         border-radius: 10px;
         border: none;
     }

     .modal-confirm .modal-header {
         background: #fff;
         border-bottom: none;
         position: relative;
         text-align: center;
         margin: -20px -20px 0;
         border-radius: 10px 10px 0 0;
         padding: 35px;
         padding-bottom: 1px;
     }

     .modal-confirm h4 {
         text-align: center;
         font-size: 28px;
         margin: 10px 0;
     }

     .modal-confirm .form-control,
     .modal-confirm .btn {
         min-height: 40px;
         border-radius: 3px;
     }

     .modal-confirm .close {
         position: absolute;
         top: 15px;
         right: 15px;
         color: #d41a1ab4;
         text-shadow: none;
         opacity: 1;
     }

     .modal-confirm .close:hover {
         opacity: 0.8;
     }

     .modal-confirm .icon-box {
         color: #0FB2F2;
         width: 95px;
         height: 95px;
         display: inline-block;
         border-radius: 50%;
         z-index: 9;
         border: 2px solid #0FB2F2;
         padding: 25px;
         text-align: center;
     }

     .modal-confirm .icon-box .material-icons {
         font-size: 50px;
         animation: mover 1s;
     }

     .modal-confirm .icon-box i {
         font-size: 64px;
         margin: -4px 0 0 -4px;
     }

     .modal-confirm.modal-dialog {
         margin-top: 80px;
     }

     .modal-confirm .btn {
         color: #fff !important;
         background: #0FB2F2;
          !important;
         text-decoration: none;
         transition: all 0.4s;
         line-height: normal;
         border-radius: 5px;
         padding: 6px 20px;
         border: none;
         margin-top: 5%;
         margin-right: 2%;
         animation: moverbutton 1s;
     }

     .modal-confirm .btn-demiss {
         color: #fff !important;
         background: #41464e !important;
     }

     .modal-confirm .btn:hover,
     .modal-confirm .btn:focus {
         opacity: 0.9;
         outline: none;
     }

     .modal-confirm .btn span {
         margin: 1px 3px 0;
         float: left;
     }

     .modal-confirm .btn i {
         margin-left: 1px;
         font-size: 20px;
         float: right;
     }

     .trigger-btn {
         display: inline-block;
         margin: 100px auto;
     }

     @keyframes mover {

         0% {
             transform: rotate(-60deg);
         }

         50% {
             transform: rotate(60deg);
         }

         100% {
             transform: rotate(0deg);
         }
     }

     @keyframes moverbutton {

         0% {
             margin-left: -100px;
         }

         50% {
             transform: rotate(60deg);
         }

         100% {
             transform: rotate(0deg);
         }
     }

     .modal-header h6 {
         cursor: pointer;
     }
 </style>


 {{-- ======================================================== Modal para cadastros pos item =============================== --}}

 {{-- Modal para o cadastro de autores --}}

 <div class="modal fade modal-css" id="modalAutor" tabindex="-1" role="dialog" aria-labelledby="modalAutor"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">

                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">REGISTRAR AUTOR</h5>

                 </div>

                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>



             </div>

             <form method="post" autocomplete="off" id="formAutor">
                 @csrf
                 <div class="modal-body">

                     <div class="col-12">

                         {{-- Pegando o tipo de acção --}}

                         <div class="row">

                             <div class="col-6">

                                 {{-- ================== Código de identificação ========================== --}}

                                 <input type="text" class="form-control d-none" placeholder=""
                                     aria-label="Recipient's username" aria-describedby="button-addon2" id="actionAutor"
                                     required aria-required="Título do livro" name="actionAutor" value="autor">



                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad " placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="nomeAutor" required aria-required="Título do livro" name="nomeAutor">


                                 </div>
                             </div>

                             <div class="col-6">

                                 <label class="">Sobrenome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="sobrenomeAutor" required aria-required="Título do livro"
                                         name="sobrenomeAutor">


                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-12">
                         <div class="row">


                             <div class="col-6">
                                 <label class="">Sexo</label>

                                 <div class="input-group mb-3">

                                     <select name="sexo" id="generoAutor" name="generoAutor"
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
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="informacoesAutor" name="informacoesAutor" required>

                                 </div>
                             </div>

                         </div>

                     </div>
                     <div class="col-12 mt-2">

                         <div class="row">

                             <div class="col-6">

                                 <button type="submit" class="btn btn-success col-4 s-a" style="border-radius: 5px;"><i
                                         class="fa fa-plus"></i> Criar</button>
                                 <button type="reset" class="btn btn-secondary s-e"
                                     style="border-radius: 5px;">Limpar</button>
                             </div>

                             <div class="col-3 sms">

                             </div>
                         </div>
                     </div>

                 </div>
             </form>
         </div>
     </div>
 </div>



 <!-- Modal para o cadastro de Editoras -->

 <div class="modal fade modal-css" id="modalEditora" tabindex="-1" role="dialog" aria-labelledby="modalEditora"
     aria-hidden="true">
     <div class="modal-dialog " role="document">
         <div class="modal-content b-cont">

             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">REGISTRAR EDITORA</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>

             <div class="modal-body">

                 <form method="post" autocomplete="off" id="formEditora">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionEditora"
                         required aria-required="Título do livro" name="actionEditora" value="editora">

                     <div class="col-12">

                         <div class="row">

                             <div class="col-6">

                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" name="nomeEditora" id="nomeEditora"
                                         required>

                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Endereço</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="enderecoEditora" id="enderecoEditora" required>


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
                                         name="emailEditora" id="emailEditora" required>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <label class="">Cidade</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="cidadeEditora" id="cidadeEditora" required>

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
                 </form>
             </div>
         </div>
     </div>
 </div>

 <!-- Modal para o cadastro de Categorias -->

 <div class="modal fade modal-css" id="modalCategoria" tabindex="-1" role="dialog"
     aria-labelledby="modalCategoria" aria-hidden="true">

     <div class="modal-dialog  " role="document">

         <div class="modal-content">

             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">REGISTRAR Área</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>
             <div class="modal-body">

                 <form method="post" autocomplete="off" id="formCategoria">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="action" required
                         aria-required="Título do livro" name="action" value="categoria">

                     <div class="col-12">
                         <div class="row">


                             <div class="col-2">
                                 <label class="">CDD / CDU ( ex: 120 )</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad " placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="descricaoCategoria" id="descricaoCategoria" required>

                                 </div>
                             </div>
                             <div class="col-6">
                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="nomeCategoria" id="nomeCategoria" required>

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

                 </form>


             </div>

         </div>
     </div>
 </div>

 <!-- Modal para o cadastro de  Computadores -->

 <div class="modal fade modal-css" id="modalComputador" tabindex="-1" role="dialog"
     aria-labelledby="modalComputador" aria-hidden="true">
     <div class="modal-dialog " role="document">
         <div class="modal-content b-cont">

             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">REGISTRAR Computador</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>

             <div class="modal-body">

                 <form method="post" autocomplete="off" id="formComputador">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionComputador"
                         required name="actionComputador" value="computador">

                     <div class="col-12">

                         <div class="row">

                             <div class="col-6">

                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" name="nomeComputador" id="nomeComputador"
                                         required>

                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Marca</label>

                                 <div class="input-group mb-3">

                                     <select name="marcaComputador" id="marcaComputador"
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


                                     <select name="processadorComputador" id="processadorComputador"
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
                                         name="ramComputador" id="ramComputador" required>

                                     <select class="form-control col-4 b-rad" id="ramUnidade" name="ramUnidade"
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
                                         name="hdComputador" id="hdComputador" required>

                                     <select class="form-control col-4 b-rad" id="hdUnidade" name="hdUnidade"
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
                 </form>
             </div>
         </div>
     </div>
 </div>

 {{-- ========================================================== Modal para editar todos item =============================== --}}

 {{-- Modal pra a editar os dados dos livros --}}

 <div class="modal  fade modal-css" id="modalAlterarLivro" tabindex="-1" role="dialog"
     aria-labelledby="modalAlterarLivro" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content b-cont" style="min-width: 1050px;">
             <div class="modal-header">

                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">Alterar Livro</h5>

                 </div>

                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>
             <div class="modal-body">

                 <form action="" method="post" autocomplete="off" id="formAlterarLivro">

                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionAlterar"
                         required aria-required="Título do livro" name="actionAlterar" value="livro">


                     <div class="col-12">
                         <div class="row">
                             <div class="col-5 d-none">
                                 <label class="">Codigo</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="codigoLivro-A" id="codigoLivro-A" disabled required>


                                 </div>
                             </div>


                             <div class="col-6">
                                 <label class="">Título</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="titulo-A" name="titulo-A" required aria-required="Título do livro">

                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Subtítulo</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="subtitulo-A" name="subtitulo-A" required
                                         aria-required="Título do livro">

                                 </div>
                             </div>

                         </div>
                     </div>

                     <div class="col-12">
                         <div class="row">

                             <div class="col-6">

                                 <label for="">Autor </label>
                                 <div class="input-group mb-3 ">

                                     <select name="select-a-book-A" id="select-a-book-A" multiple
                                         class="selectpicker form-control autor select-a-book-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true">

                                         @foreach ($autores as $item)
                                             <option value="{{ $item->id }}">
                                                 {{ $item->name . ' ' . $item->surname }}
                                             </option>
                                         @endforeach

                                     </select>

                                 </div>
                             </div>

                             <div class="col-6">

                                 <label for="">Editora</label>
                                 <div class="input-group mb-3">

                                     <select name="select-e-book-A" id="select-e-book-A"
                                         class="selectpicker form-control editora select-e-book-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true" required>
                                         <option selected>Editora actual</option>
                                         @foreach ($editoras as $item)
                                             <option value="{{ $item->id }}">{{ $item->name }}</option>
                                         @endforeach

                                     </select>

                                 </div>
                             </div>
                         </div>
                     </div>


                     <div class="col-12">

                         <div class="row">

                             <div class="col-6">

                                 <label for="">Área </label>
                                 <div class="input-group mb-3">

                                     <select name="select-c-book-A" id="select-c-book-A"
                                         class="selectpicker form-control categoria select-c-book-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true" required>
                                         <option selected>Área actual</option>
                                         @foreach ($categorias as $item)
                                             <option value="{{ $item->id }}">{{ $item->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                             </div>

                             <div class="col-6">

                                 <div class="row">

                                     <div class="col-6">
                                         <label class="">ISBN</label>

                                         <div class="input-group mb-3">

                                             <input type="text" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 id="isbn-A" name="isbn-A" min="1">


                                         </div>

                                     </div>

                                     <div class="col-3">
                                         <label class="">Ano lançamento</label>

                                         <div class="input-group mb-3">

                                             <input type="number" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 name="ano-A" id="ano-A" min="1300"
                                                 max="@php
                                                     echo date('Y');
                                                 @endphp">

                                         </div>

                                     </div>


                                     <div class="col-3">
                                         <label class="">Edição</label>

                                         <div class="input-group mb-3">

                                             <input type="number" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 name="edicao-A" id="edicao-A" min="1">

                                         </div>

                                     </div>

                                 </div>
                             </div>

                         </div>

                     </div>

                     <div class="col-12">

                         <div class="row">

                             <div class="col-6">

                                 <label for="">Local de Lançamento</label>
                                 <div class="input-group mb-3">
                                     <input type="text" class="form-control" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="local-A" id="local-A" required aria-required="Título do livro">
                                 </div>

                             </div>

                             <div class="col-6">

                                 <div class="row">

                                     <div class="col-6">
                                         <label class="">Número de chamada</label>

                                         <div class="input-group mb-3">

                                             <input type="text" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 name="idioma-A" id="idioma-A">

                                         </div>

                                     </div>



                                     <div class="col-3">
                                         <label class="">Páginas</label>

                                         <div class="input-group mb-3">

                                             <input type="number" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 name="pagina-A" id="pagina-A" min="10" max="3000">

                                         </div>

                                     </div>

                                     <div class="col-3">
                                         <label class="">Quantidade</label>

                                         <div class="input-group mb-3">

                                             <input type="number" class="form-control" placeholder=""
                                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                                 name="quantidade-A" id="quantidade-A" min="1" readonly>

                                         </div>

                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-12">
                         <div class="row">
                             <div class="col-6">
                                 <button type="submit" class="btn btn-success col-4"
                                     style="border-radius: 5px; letter-spacing: 1px;">Salvar</button>

                                 <button type="reset" data-toggle="modal" data-target="#modalExemplo"
                                     class="btn btn-secondary col-4 "
                                     style="border-radius: 5px;; letter-spacing: 1px;margin-left: 10px;">Limpar</button>
                             </div>
                         </div>
                     </div>
                 </form>

             </div>

         </div>
     </div>
 </div>

 {{-- Modal pra a editar os dados das categorias --}}

 <div class="modal fade modal-css" id="modalAlterarCategoria" tabindex="-1" role="dialog"
     aria-labelledby="modalAlterarCategoria" aria-hidden="true">

     <div class="modal-dialog" role="document">

         <div class="modal-content">

             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">Alterar Área</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>
             <div class="modal-body">


                 <form method="post" autocomplete="off" id="formAlterarCategoria">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionAlterar"
                         required name="actionAlterar" value="categoria">

                     <div class="col-12">
                         <div class="row">
                             <div class="col-2">
                                 <label class="">CDD / CDU ( ex: 120 )</label>
                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="descricaoCategoria-A" id="descricaoCategoria-A" required>
                                 </div>

                                 <div class="input-group mb-3 d-none">
                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="codigoCategoria-A" id="codigoCategoria-A" disabled required>
                                 </div>

                             </div>
                             <div class="col-6">
                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="nomeCategoria-A" id="nomeCategoria-A" required>


                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-12 mt-2">

                         <div class="row">

                             <div class="col-6">

                                 <button type="submit" class="btn btn-success  col-4  s-c"
                                     style="border-radius: 5px;">Salvar
                                 </button>

                             </div>

                             <div class="col-3 sms">

                             </div>
                         </div>
                     </div>

                 </form>


             </div>

         </div>
     </div>
 </div>

 {{-- Modal para editar os dados da editora --}}

 <div class="modal fade modal-css" id="modalAlterarEditora" tabindex="-1" role="dialog"
     aria-labelledby="modalAlterarEditora" aria-hidden="true">
     <div class="modal-dialog" style="border-radius: 10px;" role="document">
         <div class="modal-content b-cont">
             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">Alterar Editora</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>
             <div class="modal-body">

                 <form method="post" autocomplete="off" id="formAlterarEditora">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionAlterar"
                         required aria-required="Título da Editora" name="actionAlterar" value="editora">

                     <div class="col-12">
                         <div class="row">
                             <div class="col-12 d-none">
                                 <label class="">Codigo</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="codigoEditora-A" id="codigoEditora-A" disabled required>
                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" name="nomeEditora-A" id="nomeEditora-A"
                                         required>

                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Endereço</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="enderecoEditora-A" id="enderecoEditora-A" required>


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
                                         name="emailEditora-A" id="emailEditora-A" required>


                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Cidade</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="cidadeEditora-A" id="cidadeEditora-A" required>


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

                                 <button type="submit" class="btn col-4 btn-success s-e"
                                     style="border-radius: 5px;">Salvar</button>

                             </div>

                             <div class="col-3 sms">

                             </div>



                         </div>

                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 </div>
 </div>

 {{-- Modal para editar os dados do Computador --}}

 <div class="modal fade modal-css" id="modalAlterarComputador" tabindex="-1" role="dialog"
     aria-labelledby="modalAlterarComputador" aria-hidden="true">
     <div class="modal-dialog " role="document">
         <div class="modal-content b-cont">

             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">Alterar Computador</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>

             <div class="modal-body">

                 <form method="post" autocomplete="off" id="formAlterarComputador">
                     @csrf

                     <input type="text" class="form-control d-none" placeholder=""
                         aria-label="Recipient's username" aria-describedby="button-addon2" id="actionAlterar"
                         required name="actionAlterar" value="computador">

                     <div class="col-12">

                         <div class="row">

                             <div class="col-12 d-none">
                                 <label class="">Codigo</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="codigoComputador-A" id="codigoComputador-A" disabled required>
                                 </div>
                             </div>

                             <div class="col-6">

                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" name="nomeComputador-A"
                                         id="nomeComputador-A" required>

                                 </div>
                             </div>

                             <div class="col-6">
                                 <label class="">Marca</label>

                                 <div class="input-group mb-3">

                                     <select name="marcaComputador-A" id="marcaComputador-A"
                                         class="selectpicker form-control computador select-marca-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true">
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


                                     <select name="processadorComputador-A" id="processadorComputador-A"
                                         class="selectpicker form-control autor select-processador-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true">
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
                                         name="ramComputador" id="ramComputador-A" required>

                                     <select class="form-control col-4 b-rad" id="ramUnidade-A" name="ramUnidade-A"
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
                                         name="hdComputador-A" id="hdComputador-A" required>

                                     <select class="form-control col-4 b-rad" id="hdUnidade-A" name="hdUnidade-A"
                                         style="cursor:pointer;padding-left: 1px;border-radius: 0px 5px 5px 0px !important;"
                                         required>

                                         <option value="GB">GB </option>
                                         <option value="TB">TB </option>

                                     </select>

                                 </div>

                             </div>

                         </div>
                     </div>

                     <div class="col-12">
                         <div class="row">
                             <div class="col-6">
                                 <label class="">Estado</label>

                                 <div class="input-group mb-3">

                                     <select name="estadoComputador-A" id="estadoComputador-A"
                                         class="selectpicker form-control computador select-estado-A"
                                         data-actions-box="true" data-selected-text-format="count > 3"
                                         data-live-search="true">
                                         <option value="Operacional">Operacional</option>
                                         <option value="Danificado">Danificado</option>
                                     </select>

                                 </div>
                             </div>

                         </div>
                     </div>


                     <div class="col-12 mt-2">
                         <div class="row">

                             <div class="col-6">

                                 <button type="submit" class="btn btn-success col-4 s-e"
                                     style="border-radius: 5px;"></i>Salvar</button>

                             </div>
                             <div class="col-3 sms">

                             </div>
                         </div>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>


 {{-- Modal para editar dados do autor --}}

 <div class="modal fade modal-css" id="modalAlterarAutor" tabindex="-1" role="dialog"
     aria-labelledby="modalAlterarAutor" aria-hidden="true">
     <div class="modal-dialog " role="document">
         <div class="modal-content">
             <div class="modal-header">



                 <div class="row fundo-alice">

                     <h5 style="text-transform: uppercase;">Alterar Autor</h5>

                 </div>


                 <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                     <span aria-hidden="true">&times;</span>
                 </button>

             </div>

             <div class="modal-body">
                 <form method="post" autocomplete="off" id="formAlterarAutor">
                     @csrf

                     <div class="col-12">
                         <div class="row">

                             <input type="text" class="form-control d-none" placeholder=""
                                 aria-label="Recipient's username" aria-describedby="button-addon2"
                                 id="actionAlterar" required aria-required="Título da Editora" name="actionAlterar"
                                 value="autor">


                             <div class="col-6 d-none">
                                 <label class="">Codigo</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         name="codigoAutor-A" id="codigoAutor-A" disabled required>


                                 </div>
                             </div>

                             <div class="col-6">


                                 <label class="">Nome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad " placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="nomeAutor-A" required aria-required="Título do livro"
                                         name="nomeAutor-A">

                                 </div>
                             </div>

                             <div class="col-6">

                                 <label class="">Sobrenome</label>

                                 <div class="input-group mb-3">

                                     <input type="text" class="form-control b-rad" placeholder=""
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="sobrenomeAutor-A" required aria-required="Título do livro"
                                         name="sobrenomeAutor-A">

                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-12">
                         <div class="row">


                             <div class=" col-6">
                                 <label class="">Sexo</label>

                                 <div class="input-group mb-3">

                                     <select name="sexo" id="generoAutor-A" name="generoAutor-A"
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
                                         aria-label="Recipient's username" aria-describedby="button-addon2"
                                         id="informacoesAutor-A" name="informacoesAutor" required
                                         aria-required="Título do livro">

                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-12">

                         <div class="row">

                             <div class="col-6">

                                 <button type="submit" class="btn btn-success col-4 mt-2 s-a"
                                     style="border-radius: 5px;">Salvar</button>
                             </div>

                             <div class="col-3 sms">

                             </div>
                         </div>
                     </div>
                 </form>
             </div>

         </div>
     </div>
 </div>
 </div>

 <style>
     .modal-body span {
         color: black !important;
     }
 </style>


 {{-- Modal para eliminar categoria --}}

 <div id="modalEliminarCategoria" class="modal fade ">
     <div class="modal-dialog modal-confirm">
         <div class="modal-content">
             <div class="modal-header justify-content-center">
                 <div class="icon-box">
                     <i class="material-icons">&#xE872;</i>
                 </div>
                 <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
             </div>
             <div class="modal-body text-center">
                 <h4></h4>
                 <p> Se eliminar esta Área não voltará a vê-la</p>
                 <button class="btn btn-success btn-eliminar-categoria"><span
                         style="color: #fff!important">Sim</span></button>
                 <button class="btn btn-demiss btn-cancelar-categoria" data-dismiss="modal"
                     aria-hidden="true"><span style="color: #fff!important">Cancelar</span></button>
             </div>
         </div>
     </div>
 </div>

 {{-- Modal para eliminar editora --}}

 <div id="modalEliminarEditora" class="modal fade ">
     <div class="modal-dialog modal-confirm">
         <div class="modal-content">
             <div class="modal-header justify-content-center">
                 <div class="icon-box">
                     <i class="material-icons">&#xE872;</i>
                 </div>
                 <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
             </div>
             <div class="modal-body text-center">
                 <h4></h4>
                 <p> Se eliminar esta editora não voltará a vê-la</p>
                 <button class="btn btn-success btn-eliminar-editora"><span
                         style="color: #fff!important">Sim</span></button>
                 <button class="btn btn-demiss btn-cancelar-editora" data-dismiss="modal" aria-hidden="true"><span
                         style="color: #fff!important">Cancelar </span></button>
             </div>
         </div>
     </div>
 </div>

 {{-- Modal para eliminar Autor --}}

 <div id="modalEliminarAutor" class="modal fade ">
     <div class="modal-dialog modal-confirm">
         <div class="modal-content">
             <div class="modal-header justify-content-center">
                 <div class="icon-box">
                     <i class="material-icons">&#xE872;</i>
                 </div>
                 <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
             </div>
             <div class="modal-body text-center">
                 <h4></h4>
                 <p> Se eliminar este Autor não voltará a vê-lo</p>
                 <button class="btn btn-success btn-eliminar-autor"><span
                         style="color: #fff!important">Sim</span></button>
                 <button class="btn btn-demiss btn-cancelar-autor" data-dismiss="modal" aria-hidden="true"><span
                         style="color: #fff!important">Não </span></button>
             </div>
         </div>
     </div>
 </div>

 {{-- Modal para eliminar Livro --}}

 <div id="modalEliminarLivro" class="modal fade ">
     <div class="modal-dialog modal-confirm">
         <div class="modal-content">
             <div class="modal-header justify-content-center">
                 <div class="icon-box">
                     <i class="material-icons">&#xE872;</i>
                 </div>
                 <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
             </div>
             <div class="modal-body text-center">
                 <h4></h4>
                 <p> Se eliminar este Livro não voltará a vê-lo</p>
                 <button class="btn btn-success btn-eliminar-livro"><span
                         style="color: #fff!important">Sim</span></button>
                 <button class="btn btn-demiss btn-cancelar-livro" data-dismiss="modal" aria-hidden="true"><span
                         style="color: #fff!important">Cancelar</span></button>
             </div>
         </div>
     </div>
 </div>

 {{-- Modal finalizar o requisiçao do Livro --}}

 <div id="modalDevolverLivro" class="modal fade ">
     <div class="modal-dialog modal-confirm">
         <div class="modal-content">
             <div class="modal-header justify-content-center">
                 <div class="icon-box">
                     <i class="material-icons">help</i>
                 </div>
                 <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
             </div>
             <div class="modal-body text-center">
                 <h4>Finalizar Requisição</h4>
                 <p> Os livros presentes nesta requisição serão devolvidos</p>
                 <button class="btn btn-success btn-devolver-livro"><span
                         style="color: #fff!important">Sim</span></button>
                 <button class="btn btn-demiss btn-cancelar-livro" data-dismiss="modal" aria-hidden="true"><span
                         style="color: #fff!important">Cancelar</span></button>
             </div>
         </div>
     </div>
 </div>


