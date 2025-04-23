<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    @include('layouts.backoffice.head')
    @include('layouts.backoffice.styles')
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<style>
    .div-mensagem{
        display: flex;
        justify-content: center;
        align-items: center
    }
</style>
<body class="div-mensagem  hold-transition sidebar-mini">
     <div class="container">
        <div class="col-md-12 ">
            <div class="card">
                <div style="box-shadow: #e5dbdb 1px 10px 20px 1px;" class="card-header rounded">
                    <br>
                    <h4 style="font-size:1.9pc"> Lamentamos informar, a <b>forLEARN</b><sup style="font-size:1.5pc">®</sup> está temporariamente indisponível!</h4>
                    <hr>
                    <h5 class="pl-3"> Por favor, tente mais tarde  ! <i  class="fa fa-spinner fa-spin"></i></h3>
                    <center>
                        <img style="width: 60%;" src="//{{$_SERVER['HTTP_HOST']}}/storage/fonts/servico.webp" >
                    </center>
                    <br><br><br><br><br><br><br><br>
                </div>
            </div>
        </div>
    </div>
</body>
</html>