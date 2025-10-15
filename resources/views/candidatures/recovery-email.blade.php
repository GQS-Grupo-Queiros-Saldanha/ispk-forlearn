<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ISPM-Candidatura</title>
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}">


    <!-- Links -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png')}}">
    <link rel="manifest" href="forlearn.ispm.co.ao/manifest.webmanifest">
    <link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg')}}" color="#5bbad5">


    <style >

    .ld{
      font-weight: bold;
      color: #17a2b85e;
      cursor: pointer;
      font-family:30pt;
    }
    .ld:hover{
      transition: .5s;
      cursor: pointer;
      color: #1bb5cc;
    }
    
   .btn_registrar{
      background-color: #1bb5cc;
      color:white;
   }
   .btn_registrar: hover{
      background-color: red;
      color: white;
   }
  </style>
</head>
<body>
    
    <!-- Messenger Plug-in de chat Code -->
    <div id="fb-root"></div>

    <!-- Your Plug-in de chat code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "101010008951594");
      chatbox.setAttribute("attribution", "biz_inbox");

      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v11.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/pt_PT/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>

    <div class="d-lg-flex half">
    <!-- <div class="bg order-1 order-md-2" style="background-image: url('images/bg_1.jpg');"></div> -->
    <div class="bg order-1 order-md-2" style="background-image: url('{{asset('img/background_students.jpg')}}');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center" style="color: #1bb5cc;">
          <div class="col-md-7">
            <h2><img src="{{asset('img/logo.jpg')}}"></h2>

           
            <h1 style="color:#1bb5cc;"><strong >Recuperação de acesso</strong></h1>
            <br>
            <p class="mb-4 text-justify">Enviamos um e-mail para <b>{{$candidateEmail ?? 'N/A'}}</b> com os dados de acesso para validar sua candidatura; Por favor clique no <b>botão</b> da messengem enviada para confirmar seu e-mail.</p>
            
             

            <form action="#" method="post" target="_blank">
          <!--     <div class="form-group ">
                <label for="username">Nome Completo</label>
                <input type="text" class="caixa" placeholder="Informe aqui seu nome completo" id="fullname">
              </div>

              <div class="form-group  mb-3">
                <label for="password">Seu e-mail</label>
                <input type="email" class="caixa" placeholder="Informe aqui seu e-mail válido" id="emailuser">
              </div>

              <div class="d-flex mb-1 align-items-center">
                <label class="control control--checkbox mb-0"><!-- span class="caption">Não Recebí o e-ma</span>
                  <input type="checkbox" checked="checked"/> -->
                  <!-- <div class="control__indicator"></div> -->
                <!-- </label> -->
                <!-- <span class="ml-auto"><a href="#" class="forgot-pass">Não recebí o e-mail</a></span>  -->
              <!-- </div> -->



                  <input type="submit" value="Mudar e-mail" class="btn btn " style=" background:#1bb5cc; color: white;">
                  
                  <input type="submit" value="Reenviar e-mail" class="btn btn btn-primary">


            </form>
            <p class="mb-4 text-justify" style="color:transparent;"> <br><br><br></p></p>
            <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-sm">Reenviar e-mail</button>-->
            
            <!--<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">-->
            <!--  <div class="modal-dialog modal-sm p-5">-->
            <!--    <div class="modal-content">-->
            <!--     O Seu e-mail foi reenviado com sucesso, verifique a sua caixa de entrada -->
            <!--    </div>-->
            <!--  </div>-->
            <!--</div>-->
                        
            
                
                
                
          </div>
        </div>
      </div>
    </div>


  </div>
  
 <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>