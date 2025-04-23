<!DOCTYPE html>
<html lang="pt">

<head>
    <title>Manutenção | forLEARN® by GQS</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
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
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://www.moloni.pt/_include/min/?g=css_login_registo_20220809153000" />
    <script charset="utf-8" type="text/javascript" src="https://www.moloni.pt/_include/min/?g=js_ac_20230516172000">
    </script>
    <script charset="utf-8" type="text/javascript" src="https://www.moloni.pt/_include/js/external/slick/slick.min.js">
    </script>
    <link rel="stylesheet" type="text/css" href="https://www.moloni.pt/_include/js/external/slick/slick.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
    <style>

 
        .login__container {
            width: 700px !important;
        }

        .img {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn {
            color: #249fbd;
            margin-top: 2em;
            border: 1px solid #249fbd;
            padding: 4px 8px;
            border-radius: 8px;
            transition: all 0.5s;
        }

        .btn:hover{
            
            color: #545454; 
            border: 1px solid #545454;
            transform: translateY(-5px)
        }

        @media screen and (min-width: 1280px) {
            .login__form {
                margin-top: inherit !important;
                margin-bottom: auto;
            }
        }
    </style>
</head>


<body class="login">
    <div class="row  pb-3" id="Topbar" style="background-color:#3ec4f7;"></div>
    <div class="login">
        <div class="login__wrapper">
            <div class="login__container">
                <div class="login__logo"><a href="#" title="forLEARN"><img
                            src="{{ asset('img/login/ForLEARN 03.png') }}" title="Logo forLEARN"
                            alt="Logo forLEARN"></a>

                </div>
                <div class="login__form">
                    <h1 class="login__form-title" style="color:#249fbd;">PÁGINA EM MANUTENÇÃO <i
                            class="fa fa-exclamation-triangle" aria-hidden="true"></i></h1>
                    <br>
                    <h1 class="login__form-title text-black" style="font-size: 1.5em;margin-top:0.5em;color:#545454">
                        Estamos a actualizar a nossa página para oferecer-lhe uma melhor experiência...</h1>

                    <a href="{{route('profile.index')}}" class="btn btn-success">Volto mais tarde</a>
                </div>
            </div>
            <div class="img">
                <img src="{{ asset('img/manutence.png') }}" title="Logo forLEARN" alt="Logo forLEARN">
            </div>

        </div>

    </div>
</body>



</html>
