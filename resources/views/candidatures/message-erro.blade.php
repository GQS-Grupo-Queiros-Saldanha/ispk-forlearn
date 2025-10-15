<!d<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ISPM-Candidatura</title>
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <!--<link rel="stylesheet" href="css/style.css">-->
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

    <div class="d-lg-flex half">
    <!-- <div class="bg order-1 order-md-2" style="background-image: url('images/bg_1.jpg');"></div> -->
    <!-- <div class="bg order-1 order-md-2" style="background-image: url('{{asset('img/image-neutra.jpg')}}');"></div> -->
    <div class="bg order-1 order-md-2" style=" background-color:#1bb5cc; "></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center" style="color: #1bb5cc;">
          <div class="col-md-7">
          
             <h2>
                 <a href="https://www.ispm.co.ao/" title="Página inicial"><img src="{{asset('img/logo.jpg')}}"></a>
             </h2> 
          
            <h1 style="color:#1bb5cc;"><strong>{{ $code==4 ? 'Concluído' : 'Aviso!' }}</strong></h1>
  
            <br>
            @if($code==1)
                <p class="mb-4 text-justify">O <b>{{$email ?? 'N/A'}}</b> já se encontra registado Verifique se digitou corretamente o endereço de e-mail .
                </p>
                 <form action="{{route('reSend.email')}}" method="post" target="_blank">
                     @csrf
                     @method('POST')
                     <input type="hidden" value="{{ $email ?? 'N/A' }}"   name="email">
                     <input type="hidden" value="Reenviar e-mail" name="submitButton">
                     
                     <button type="submit"  value="" class="btn btn-block" style=" background:#1bb5cc; color: white;">Não recebí o e-mail</button>
                </form>
            @elseif($code==2)
                <p class="mb-4 text-justify">Não foi concedido o acesso ao login ao <b>{{$email ?? 'N/A'}}</b>, o mesmo pode não estar registado ou se encontrar em uso activo ,  Verifique se digitou corretamente o dados de login .</p>
                 <a href="https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login"  class="btn btn-block" style=" background:#1bb5cc; color: white;padding-top:16px;">Tentar novamente</a>

            @elseif($code==3)
                 <p class="mb-4 text-justify">Erro ao encontrar os dados do usuários<b>{{$email ?? 'N/A'}}</b>.</p>
                 <a href="https://forlearn.ispm.co.ao/pt/candidaturas/candidate-login"  class="btn btn-block" style=" background:#1bb5cc; color: white;padding-top:16px;">Tentar novamente</a>  
            @elseif($code==4)
                 <p class="text-justify">Está finalizada a sua pré-candidatura ao <b>Instituto Superior Politécnico Maravilha</b>.</p>
                 <p class="mb-4 text-justify">Aceda à plataforma forLEARN<sup>®</sup> com os novos dados de acesso enviados para o seu e-mail.</p>
                 <a href="https://forlearn.ispm.co.ao/pt/login" target="_blank"  class="btn btn-block" style=" background:#1bb5cc; color: white;padding-top:16px;">Entrar na forLEARN<sup>®</sup></a>
            @endif
            
          
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