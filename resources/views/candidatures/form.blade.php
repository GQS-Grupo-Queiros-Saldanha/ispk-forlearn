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




    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @if ($errors->has('email'))
            @endif
        </div>
    @endif




    <div class="d-lg-flex half">
    <!-- <div class="bg order-1 order-md-2" style="background-image: url('images/bg_1.jpg');"></div> -->
    <div class="bg order-1 order-md-2" style="background-image: url('{{asset('img/image-register.jpg')}}');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center" style="color: #1bb5cc;">
          <div class="col-md-7 ">
            </br>
            </br>
            </br>
            </br>
            
            <h2><a href="https://www.ispm.co.ao/" title="Página inicial candidatura ISPM"><img src="{{asset('img/logo.jpg')}}"></a></h2>

            <h1 style="color:#1bb5cc;"><strong >Faça aqui o seu pré-registo</strong></h1>

            <p class="mb-4">Para iniciar sua pré-candidatura no <B>Instituto Superior Politécnico Maravilha</B>, preencha os seguintes campos:</p>
            <form action="{{ route('candidature.submitData') }}" method="POST" target="_blank">
                @csrf
              <div class="form-group first">
                <label for="username">Nome completo</label>
                <input type="text" class="form-control" placeholder="Nome completo como no documento de identificação." title="Nome completo como no documento de identificação." id="fullname" required name="name">
              </div>

              <div class="form-group last mb-3">
                <label for="password">e-mail</label>
                <input type="email" class="form-control" placeholder="Coloque um e-mail válido." id="emailuser" required name="email" title="Coloque e-mail válido.">
              </div>

              <div class="d-flex mb-1 align-items-center">
                <label class="control control--checkbox mb-0">
                </label>
                <!--<span class="ml-auto"><a href="https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login" class="forgot-pass">Acessar candidatura</a></span>-->
              </div>

              <input type="submit" value="Registrar" class="btn btn-block btn_registrar" >

            </form>
          </div>
        </div>
      </div>
    </div>


  </div>
</body>
</html>