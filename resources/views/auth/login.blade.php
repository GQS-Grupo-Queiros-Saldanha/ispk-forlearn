<!DOCTYPE html>
<html lang="pt">
<?php  
    if(PHP_SESSION_ACTIVE != session_status())
        session_start(); 
?>
<head>
    <title>Login | forLEARN® by GQS</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/faviicon2.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="forLEARN© by GQS" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="all">

    <meta name="generator" content="HTML">
    <meta name="language" content="pt">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="theme-color" content="#303A4D" />
    <link href="" rel="canonical" />
    <meta name="theme-color" content="#0082F2" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
     <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
     <link rel="stylesheet" href="{{ asset('css/login.css') }}">
     <link rel="stylesheet" href="{{ asset('css/login_templates.css') }}"> 
     <style>
        .login__logo img{
            height: 30px;
            width: 150px;
        }

        .login__wrapper{
            display: flex;
            gap: 0.5rem;
            align-items: center;
            /*justify-content: center;*/
        }

        input{
            background: white;
            padding: 0.2rem;
            border: 1px solid #cdcdcd;
        } 
        
        .form-label{
            font-size: 10pt;
        }

        .login, .login__wrapper{
            width: 100% !important;
        }

     </style>
</head>

<body class="login">
    
    <div class="login">
        <div class="row  pb-3" id="Topbar" style="background-color:#3ec4f7;"></div> 
        <div class="login__wrapper">
            <div class="login__container">
                <div class="login__logo"><a href="#" title="forLEARN"><img src="{{ asset('img/login/ForLEARN 03.png') }}"
                            title="Logo forLEARN" alt="Logo forLEARN"></a>

                </div>
                <div class="login__form">
                    <h1 class="login__form-title pt-2" style="color:#249fbd;">Bem-vindo!</h1>
                    <h1 class="login__form-title text-black" style="font-size: 1.5em;margin-top:0.5em;">Inicie sessão na
                        sua conta</h1>
        

                    <form id="loginForm" class="login__form-input mt-3" action="{{ route('login') }}"
                              method="POST">
                        @csrf
                        

                        <div id="input_wrapper_username" class="input__wrapper">
                            
                            <label for="username"  aria-label="E-mail" class="form-label">E-mail</label>
                            <div class="input ">
                                <input id="email" type="email"
                                             class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                             placeholder="Insira aqui seu e-mail !" required
                                             autofocus @isset($_SESSION['forlean_email']) value="{{ $_SESSION['forlean_email'] }}" @else value="{{ old('email') }}" @endisset>
                                       
                            </div> @if ($errors->has('email'))
                                        <span class="form-control invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                        </div>

                        <div id="input_wrapper_password" class="input__wrapper pt-2">
                            <label for="password" aria-label="Palavra-Passe" class="form-label">Palavra-Passe</label>
                            <div class="input">
                                
                                <input id="password" type="password"
                                             class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                             placeholder="Insira aqui sua senha !" name="password" required
                                             @isset($_SESSION['forlean_password']) value="{{ $_SESSION['forlean_password'] }}" @endisset>
                                       
                                </div> @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                        </div>

                        <div class="login__form-group" style="color:white!important;transform: scale(0,0);">
                            <label class="checkbox checkbox-green mt-3 mr-8">
                                <span class="checkbox__input  checkbox__input--default">


                                </span>
                                <span class="checkbox__label ">
                                    
                                </span>
                            </label>

                            <a href="#" onclick="javascript:openPasswordRecoveryOverlay()" class="mt-3">
                                
                            </a>
                        </div>



                        <button id="signupInit" type="submit" class="btn  btn-primary" aria-label="Iniciar sessão">
                            <span class="btn__label">Iniciar sessão</span>
                        </button>
                    </form>
                    <div class="login__links"><a></a>
                        <p><a target="_blank" title="Apoio a Clientes"></a></p>
                    </div>
                </div>
            </div>
            <div class="col-6 area-img mt-7" style="margin-top:3em!important">
                    <div class="owl-carousel text-center mt-4" style="width:480px;margin-left:190px!important;">
                            
                         <div class="item">
                              <img src="{{ asset('img/login/Login 2.png') }}" class="img-fluid" alt="..." alt="Image 1">
                              <p>A plataforma forLEARN® é uma ferramenta inovadora que visa promover a democratização no acesso à informação. </p>
                         </div>
                         <div class="item">
                              <img src="{{ asset('img/login/Login 3.png') }}" class="img-fluid" alt="..." alt="Image 1">
                              <p>A plataforma forLEARN® destaca-se por oferecer informações permanentemente atualizadas.
                              </p>
                         </div>
                         <div class="item" style="width:490px; padding:2px; ">
                              <img src="{{ asset('img/login/Login 4.png') }}" class="mw-auto " alt="..." alt="Image 1">
                              <p> Para a plataforma forLEARN® é importante a transparência na informação.</p>
                         </div>
                         <div class="item" style="width:490px;padding:2px; ">
                              <img src="{{ asset('img/login/Login 5.png') }}" class="mw-auto " alt="..." alt="Image 1">
                              <p>A plataforma forLEARN® permite que os seus utilizadores,<br>de qualquer lugar, e a qualquer hora, possam aceder à informação disponibilizada pela sua instituição <br>de ensino.</p>
                         </div>

                    </div>
               </div>

        </div>
        <div class="signup-trusted-companies">
            <div class="trusted-companies-label"></div>
            <div id="companiesSlider" style="height: 1px!important" class="trusted-companies-slider">
                <div class="trusted-companies-item">
                </div>

            </div>
        </div>
        
        <div class="container-footer">
            <div class="d1">
              <p><img class="img-footer" src="//ispk.forlearn.ao/img/login/ForLEARN 03.png" title="Logo forLEARN" alt="Logo forLEARN"> <sub>by GQS</sub> <sub class="sub2"> Versão: 1.3.43</sub> <br> © @php echo date("Y");@endphp Todos os direitos reservados</p>
            </div>
           <div class="d3">
               
               @if(isset($name))
                    <p><br>{{$name}}</p>
               @else
                     
               @endif
               
            </div>
        </div>
    </div>
</body>
<style>
.container-footer div {
        width: 10%;
        font-size: 13px;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .container-footer .d1 {
        width: 38%;
        margin-left: 150px!important;
        
    }

    .container-footer .d3 {
        width: 500px;
        margin-left: 50px;
        text-align: right;
    }

    .container-footer {
        display: flex;
        background-color: #cdcdcd;
        border-top: 1px solid #c1c1c1;
    }
    .owl-stage-outer{
        margin-top:1em;
    }    
    
    .img-footer{
        width:100px!important;
        display:inline;
    }
    .login__container{
        margin-left:150px!important;
    }
    
    sub {
    bottom: -0.25em;
    top: 4px;
    font-size: 10px;
    }
    .sub2{
            margin-left: 26px;
    }
    .invalid-feedback{
            color: red;
    font-size: 13px;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.min.js"
     integrity="sha512-1/RvZTcCDEUjY/CypiMz+iqqtaoQfAITmNSJY17Myp4Ms5mdxPS5UV7iOfdZoxcGhzFbOm6sntTKJppjvuhg4g=="
     crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script src="{{ asset('js/login.js') }}"></script>
<!--JavaScript end-->

</html>
@isset($_SESSION['forlean_password'])
    <script>
        (()=>{
            const formLogin = document.querySelector("#loginForm");
            formLogin.submit();
        })();
    </script>
@endisset
<?php session_destroy();?>

