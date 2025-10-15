 <!-- Modal para o cadastro de Autores -->

 <style>
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


     .modal-view {
         color: #434e65;
         width: 525px;
         font-family: Roboto Slab, serif;
     }

     .modal-view .modal-content {
         padding: 20px;
         font-size: 16px;
         border-radius: 10px;
         border: none;
     }

     .modal-view .modal-header {
         background: #fff;
         border-bottom: none;
         position: relative;
         text-align: center;
         margin: -20px -20px 0;
         border-radius: 10px 10px 0 0;
         padding: 35px;
         padding-bottom: 1px;
     }

     .modal-view .modal-header h5 {
        background-color: aliceblue;
        color: black;
        padding: 10px;
        padding-left: 0px;
        padding-right: 0px;
        text-align: left;
     }

     .modal-view .form-control,
     .modal-view .btn {
         min-height: 40px;
         border-radius: 3px;
     }

     .modal-view .close {
         position: absolute;
         top: 15px;
         right: 15px;
         color: #d41a1ab4;
         text-shadow: none;
         opacity: 1;
     }

     .modal-view .close:hover {
         opacity: 0.8;
     }

     .modal-view .icon-box {
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

     .modal-view .icon-box .material-icons {
         font-size: 50px;
         animation: mover 1s;
     }

     .modal-view .icon-box i {
         font-size: 64px;
         margin: -4px 0 0 -4px;
     }

     .modal-view.modal-dialog {
         margin-top: 80px;
     }

     .modal-view .btn {
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

     .modal-view .btn-demiss {
         color: #fff !important;
         background: #41464e !important;
     }

     .modal-view .row{
         width: 100%!important;
     }

     .modal-view .btn:hover,
     .modal-view .btn:focus {
         opacity: 0.9;
         outline: none;
     }

     .modal-view .btn span {
         margin: 1px 3px 0;
         float: left;
     }

     .modal-view .btn i {
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


     /* Custom Scrollbar using CSS */
     .custom-scrollbar-css {
         /* overflow-y: scroll; */
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
         height: 350px;
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

     .img-profile {

         width: 150px;
         height: 153px;
         border-radius: 100px !important;
     }


     .modal-body span {
         color: black !important;
     }

     .fundo-alice{
        background-color: aliceblue;
     }

 </style>


 {{-- Modal ver os detalhes da requisiçao dos Livros --}}

 <div id="modalLivroRequisitados" class="modal fade ">
     <div class="modal-dialog modal-view" style="min-width: 1000px!important;">
         <div class="modal-content" style="height: 530px;">
             <div class="modal-header ">

                <div class="row fundo-alice">
                    
                    
                       
                        <div class="col-6">
                            
                            <h5 class="R-referencia" style="text-transform: uppercase;"> 
                              
                            </h5>

                        </div>

                        <div class="col-6">

                            <h5 class="text-right leitor-nome"> 
                              
                            </h5>

                        </div>

                
                </div>
              
             </div>
             <div class="modal-body">


                 <div class="row">
                     <div class="container py-3 custom-scrollbar-css panel-livro-selecionados" style="padding-left:4px;">

                                <table id="tabela-livros-requisitados"
                                    class="table table-striped table-hover dataTable no-footer dtr-inline"
                                    style="width:100%">
                                    <thead>
                                        <tr>    
                                            <th>#</th>
                                            <th>Codigo Livro</th>
                                            <th>Título</th>
                                            <th>Subtítulo</th>
                                            <th>ISBN</th>
                                            

                                        </tr>
                                    </thead>
                                  
                                </table>


                     </div>
                 </div>


                 <div class="row fundo-alice"> 
                    
                    <h5 class="text-left mt-2 lidos"></h5>  

                 </div>
             </div>
         </div>
     </div>
 </div>
