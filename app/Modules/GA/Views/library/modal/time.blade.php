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


    .b-rad {
        border-radius: 5px !important;
        background-color: #eee !important;
    }

    .m-header {
        background-color: #076DF2 !important;
        color: white;
        margin-bottom: 3%;
    }

    .b-cont {
        border-bottom: 5px solid #076DF2;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .modal-body span {
        color: black !important;
    }

    .modal-header span {
        color: black !important;
    }

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

    #formVisitante label {
        margin-bottom: 0px;
    }

</style>



{{-- Modal para criar um visitante --}}

<div class="modal fade modal-css" id="modalTempo" tabindex="-1" role="dialog" aria-labelledby="modalTempo"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="min-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">

                <div class="row fundo-alice">

                    <h5 style="text-transform: uppercase;">Quantidade de livros</h5>

                </div>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>



            </div>

            <form method="post" autocomplete="off" id="formQuantidade">
                @csrf
                <div class="modal-body">

                    <div class="col-12">

                        {{-- Pegando o tipo de acção --}}

                        <div class="row">

                            <div class="col-6">

                                {{-- ================== Código de identificação ========================== --}}

                                <input type="text" class="form-control d-none" placeholder=""
                                    aria-label="Recipient's username" aria-describedby="button-addon2"
                                    id="actionQuantidade" required aria-required="Título do livro" name="actionQuantidade"
                                    value="quantidade"> 
                                <input type="number" class="form-control d-none"  placeholder=""
                                    aria-label="Recipient's username" aria-describedby="button-addon2"
                                    id="id_livro" name="id_livro">


                                <label class="">Tipo de operação</label>

                                <div class="input-group mb-3">


                                    <select name="type_operation" id="type_operation" class="form-control selectpicker ">
                                        
                                        <option  value="add">Adicionar</option>
                                        <option  value="delete">Subtrair</option>
                                    </select>

                                </div>
                            </div>

                            <div class="col-6">



                                <label class="">Quantidade</label>

                                <div class="input-group mb-3">

                                    <input type="number" class="form-control "
                                        aria-label="Recipient's username" placeholder="0"
                                        aria-describedby="button-addon2" min="1"  max="100" id="quantidade" required
                                        name="quantidade">


                                </div>
                            </div>



                        </div>
                    </div>
                
                    <div class="col-12 mt-2">

                        <div class="row">

                            <div class="col-6">

                                <button class="btn btn-success col-3 s-a" style="border-radius: 5px;">
                                    <i class="fa fa-save"></i> Guardar</button>
                               
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
