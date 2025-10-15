 <!-- Modal para o cadastro de Autores -->

 <style>
    .b-rad {
        border-radius: 5px !important;
        border-bottom: 3px solid #076DF2; !important;
        background-color: #eee !important;
    }

    .m-header {
        background-color: #076DF2!important;
        color: white;
        margin-bottom: 3%;
    }

    .b-cont {
        border-bottom: 5px solid #076DF2; !important;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
 
    .modal-body span{
       color: black!important;
   }

</style>
<style>
    .modal-confirm {		
        color: #434e65;
        width: 525px;
        font-family:  Roboto Slab,serif;
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
    .modal-confirm .form-control, .modal-confirm .btn {
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
    
    .modal-confirm .icon-box .material-icons{
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
    .modal-confirm .btn{
        color: #fff!important;
        background: #0FB2F2; !important;
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

    .modal-confirm .btn-demiss{
        color: #fff!important;
        background: #41464e !important;
    }

    .modal-confirm .btn:hover, .modal-confirm .btn:focus {
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
    
    @keyframes mover{
    
        0%{
            transform: rotate(-60deg);
        }
        50%{
            transform: rotate(60deg);
        }
        100%{
            transform: rotate(0deg);
        }
    }

    @keyframes moverbutton{
    
    0%{
        margin-left: -100px;
    }
    50%{
        transform: rotate(60deg);
    }
    100%{
        transform: rotate(0deg);
    }
    }

    .modal-header h6{
        cursor: pointer;
    }
     
    </style>


<!-- Modal para o cadastro de Autores -->

<style>
      .modal-body span{
       color: black!important;
   }

</style>
 
{{-- ======================================================== Modal para cadastros pos item =============================== --}}


{{-- Modal para a requisiçao de Livro --}}

<div id="modalRequisitarLivro" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">done</i> 
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4>Sucesso !!!</h4>	
                <p> Nova requisicão criada!</p> 
               
            </div>
        </div>
    </div>
</div>

{{-- Modal para a devoluçao de Livro --}}

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
                <h4>Finalizar Requisição ?</h4>	
                <p> Os livros presentes nesta requisição serão devolvidos</p>
                <button class="btn btn-success btn-devolver-livro"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-livro" aria-hidden="true" data-dismiss="modal"><span style="color: #fff!important">Continuar</span></button>
                
            </div>
        </div>
    </div>
</div>

{{-- Modal para a requisiçao de Livro --}}

<div id="modalLivro" class="modal fade ">
    <div class="modal-dialog modal-confirm ">
        <div class="modal-content">
             
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">done</i> 
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>  
              
            <div class="modal-body text-center">
                <h4>Sucesso !!!</h4>	
                <p> Novo livro criado!</p> 
            </div>

        </div>
    </div>
</div>

{{-- Modal para a finalizar a requisicão do Computador --}}

<div id="modalComputadorFinalizar" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">help</i> 
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4>Finalizar Requisição?</h4>	
                <p>O computador desta requisicão ficará Disponível</p>
                <button class="btn btn-success btn-finalizar"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-computador" aria-hidden="true" data-dismiss="modal"><span style="color: #fff!important">Continuar</span></button>
                
            </div>
        </div>
    </div>
</div>


{{-- Modal para criar uma requisiçao  Livro --}}
 





<script>

    var codigo_devolucao = 0;

        
$(".devolverLivro").click(
    
    function() {
        codigo_devolucao = $(this).children("#id").text();

        alert(codigo_devolucao);

        $(".modal-confirm h4").text('Finalizar requisição?'+codigo_devolucao);
        $("#modalDevolverLivro .modal-confirm p").show();
        $("#modalDevolverLivro .material-icons").text("info");
        $("#modalDevolverLivro .btn-cancelar-livro").text("Cancelar");
        $("#modalDevolverLivro .btn-eliminar-livro").show();

    }
);

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
</script>