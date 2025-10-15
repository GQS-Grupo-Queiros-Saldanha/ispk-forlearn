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
    <div class="bg order-1 order-md-2" style="background-image: url('{{asset('img/image-neutra.jpg')}}');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center" style="color: #1bb5cc;">
          <div class="col-md-7">
            <h2><a href="https://www.ispm.co.ao/"><img src="{{asset('img/logo.jpg')}}"></a></h2>


            <h1 style="color:#1bb5cc;"><strong >Verifique o seu e-mail</strong></h1>
            <br>
            <p class="mb-4 text-justify">Enviamos para <b>{{$candidateEmail ?? 'N/A'}}</b> os dados de acesso para validar sua candidatura.</p>
            <p class=" text-justify">Por favor aceda ao seu e-mail.</p>
            <p class="mb-4 text-justify">Pode ser necessário verificar sua pasta de <b>Spam</b>.</p>


            <form action="{{ route('reSend.email') }}" method="post" target="_blank">
              @csrf
              @method('POST')
                  <input type="hidden" value="{{ $candidateEmail ?? 'N/A' }}" name="email">

                  <!--<input type="submit" value="Mudar e-mail" class="btn btn " style=" background:#1bb5cc; color: white;" name="submitButton">-->
                   <a href="https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login"  class="btn btn-block" target="_blank" style="background:#1bb5cc;  color: white;  padding-top: 12px;">
                       Login
                  </a>

                   <input type="submit" style=" background:#1bb5cc; color: white;"  value="Reenviar e-mail" class="btn btn-block" name="submitButton">

                   <a href="https://forlearn.ispm.co.ao/pt/candidaturas" target="_blank" class="btn btn-block" style=" background:#1bb5cc; color: white;  padding-top: 12px;">
                   Mudar e-mail</a>

      
            </form>
            <p class="mb-4 text-justify" style="color:transparent;"> <br><br><br></p></p>


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