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
     
    </style>




<script>
    $(
        function() {



            // Validação do formulários para o cadastro de autores

            $("#nomeAutor").on('blur', function() {
                var name = $(this).val();
                var nameVerified = name.toString().trim();
                var result = nameVerified.split(" ");

                if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)) {
                   //alert("password not valid");
                   var result = "erro";
                   $("#nomeAutor").addClass('is-invalid');
                   $("#nomeAutor").removeClass('is-valid');
                   hasFullName = false;
               } else {

                   if (result.length >= 0 && nameVerified.length > 2) {
                       hasFullName = true;
                       $("#nomeAutor").removeClass('is-invalid');
                       $("#nomeAutor").addClass('is-valid');
                       $(".s-a").removeAttr("disabled");

                   } else {
                       hasFullName = false;
                       $("#nomeAutor").removeClass('is-valid');
                       $("#nomeAutor").addClass('is-invalid');
                       $(".s-a").attr("disabled", true);


                   }

               }

           });

           $("#sobrenomeAutor").on('blur', function() {
               var name = $(this).val();
               var nameVerified = name.toString().trim();
               var result = nameVerified.split(" ");

               if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)) {
                    //alert("password not valid");
                    var result = "erro";
                    $("#sobrenomeAutor").addClass('is-invalid');
                    $("#sobrenomeAutor").removeClass('is-valid');
                    hasFullName = false;
                } else {

                    if (result.length >= 0 && nameVerified.length > 2) {
                        hasFullName = true;
                        $("#sobrenomeAutor").removeClass('is-invalid');
                        $("#sobrenomeAutor").addClass('is-valid');
                        $(".s-a").removeAttr("disabled");

                    } else {
                        hasFullName = false;
                        $("#sobrenomeAutor").removeClass('is-valid');
                        $("#sobrenomeAutor").addClass('is-invalid');
                        $(".s-a").attr("disabled", true);


                    }

                }

            });



            $("#descricaoCategoria,#nomeCategoria").on('blur', function() {
                var name = $(this).val();
                var nameVerified = name.toString().trim();
                var result = nameVerified.split(" ");

                if (name.match(/[»«&%#!?*+^ºª$`~,.<>;':"\/\[\]\|{}()-=_+@]/)) {
                    //alert("password not valid");
                    var result = "erro";
                    $("#descricaoCategoria,#nomeCategoria").addClass('is-invalid');
                    $("#descricaoCategoria,#nomeCategoria").removeClass('is-valid');
                    hasFullName = false;
                } else {

                    if (result.length >= 0 && nameVerified.length > 2) {
                        hasFullName = true;
                        $("#descricaoCategoria,#nomeCategoria").removeClass('is-invalid');
                        $("#descricaoCategoria,#nomeCategoria").addClass('is-valid');
                        $(".s-c").removeAttr("disabled");

                    } else {
                        hasFullName = false;
                        $("#descricaoCategoria,#nomeCategoria").removeClass('is-valid');
                        $("#descricaoCategoria,#nomeCategoria").addClass('is-invalid');
                        $(".s-c").attr("disabled", true);


                    }

                }

            });


            // Formulário cadastrar Autor


            //  $('#formAutor').submit(function(event) {

            //      event.preventDefault();

            //      var nome = $(this).find('input#nomeAutor').val() + " " + $(this).find(
            //          'input#sobrenomeAutor').val();
            //      var sexo = $(this).find('select#generoAutor').val();
            //      var pais = $(this).find('select#paises').val();
            //      var outras = $(this).find('input#informacoesAutor').val();
            //      var action = $(this).find('input#actionAutor').val();
            //      var array = [action, nome, sexo, pais, outras];
            //      var sms = $("#formAutor .sms");
            //      var book = $("#select-a-book");
            //      var resultado = "";


            //      $.ajax({
            //          url: 'library-create-item/' + array,
            //          type: "get",
            //          data: $(this).serialize(),
            //          dataType: 'json',
            //          statusCode: {
            //              404: function() {
            //                  alert("Página não encontrada");
            //              }

            //          },
            //          success: function(response) {


            //              // Recupera presentes no objecto response

            //              resultado = response;

            //              // Converte os dados num array

            //              resultado = (resultado + "").split(',');


            //              // Se o autor já existe na base de dados

            //              if (resultado[0] == "Autor existente") {


            //                  // Verifica se na caixa de selecção existe o valor que pretendemos pegar

            //                  if (document.querySelector("option[value='" + resultado[1] +
            //                          "']") == null) {

            //                      // Adiciona o elemento se não existi

            //                      book.append("<option value='" + resultado[1] + "'selected> " +
            //                          resultado[2] + " </option>");
            //                      $(".btn-add-autor").hide();

            //                  }

            //                  // Informa que o autor existe

            //                  sms.html(
            //                      '<div class="alert d-flex align-items-center" style="padding:0px;color:#d10000;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>  </svg>  <div style="width:100%;text-align:right;">Autor existente</div></div>'
            //                  );

            //                  setTimeout(function() {
            //                      sms.html("");
            //                  }, 2000);
            //              }

            //              // Se o autor não existir, cadastra o autor
            //              else if (resultado[0] == "sucesso") {


            //                  if (document.querySelector("option[value='" + resultado[1] +
            //                          "']") == null) {
            //                      // Adiciona o elemento se não existi

            //                      book.append("<option value='" + resultado[1] + "'selected> " +
            //                          resultado[2] + " </option>");
            //                      $(".btn-add-autor").hide();


            //                  }

            //                  sms.html(
            //                      '<div class="alert d-flex align-items-center" style="padding:0px;color:#11a308;letter-spacing:0.1;margin-bottom: 0px;" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Success:">    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>  </svg>  <div style="width:100%;text-align:right;">Autor registrado</div></div>'
            //                  );

            //                  setTimeout(function() {
            //                      sms.html("");
            //                  }, 2000);

            //              }


            //          }

            //      });

            //  });


 




        }
    );
</script>

<!-- Modal para o cadastro de Autores -->

<style>
      .modal-body span{
       color: black!important;
   }

</style>

 
{{-- ========================================================== Modal para restaurar todos item =============================== --}}


{{-- Modal para restaurar categoria --}}

<div id="modalRestaurarCategoria" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">&#xE88E;</i>
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4></h4>	
                <p> Se restaurar esta categoría voltará a vê-la</p>
                <button class="btn btn-success btn-restaurar-categoria"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-categoria" data-dismiss="modal" aria-hidden="true"><span style="color: #fff!important">Cancelar</span></button>
            </div>
        </div>
    </div>
</div>
 
{{-- Modal para restaurar editora --}}

<div id="modalRestaurarEditora" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">&#xE88E;</i>
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4></h4>	
                <p> Se restaurar esta editora voltará a vê-la</p>
                <button class="btn btn-success btn-restaurar-editora"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-editora" data-dismiss="modal" aria-hidden="true"><span style="color: #fff!important">Cancelar </span></button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para restaurar Autor --}}

<div id="modalRestaurarAutor" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">&#xE88E;</i>
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4></h4>	
                <p> Se restaurar este Autor voltará a vê-lo</p>
                <button class="btn btn-success btn-restaurar-autor"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-autor" data-dismiss="modal" aria-hidden="true" ><span style="color: #fff!important">Não </span></button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para restaurar Livro --}}
     
<div id="modalRestaurarLivro" class="modal fade ">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <div class="icon-box"> 
                    <i class="material-icons">&#xE88E;</i>
                </div>
                <h6 type="" class="close" data-dismiss="modal" aria-hidden="true">&times;</h6>
            </div>
            <div class="modal-body text-center">
                <h4></h4>	
                <p> Se restaurar este Livro voltará a vê-lo</p>
                <button class="btn btn-success btn-restaurar-livro"  ><span style="color: #fff!important">Sim</span></button>
                <button class="btn btn-demiss btn-cancelar-livro" data-dismiss="modal" aria-hidden="true" ><span style="color: #fff!important">Cancelar</span></button>
            </div>
        </div>
    </div>
</div>


   
<script>
  
</script>
   