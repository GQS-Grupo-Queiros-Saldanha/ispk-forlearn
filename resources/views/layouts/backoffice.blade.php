<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    @include('layouts.backoffice.head')
    @include('layouts.backoffice.styles')
    <title>@yield('title') | {{ config('app.name','ForLearn') }}</title>
</head>
<style>
    .div-mensagem{
        /*display: flex;*/
        /*justify-content: center;*/
        /*align-items: center*/
    }
</style>
<body class="div-mensagem hold-transition sidebar-mini">
   
  
                @auth
                    @include('layouts.backoffice.background')
                
                    <div class="wrapper">
                        {{--@include('layouts.backoffice.header')--}}
                
                        <div class="content-wrapper">
                            @section('content')
                            @show
                        </div>
                
                        @include('layouts.backoffice.footer')
                    </div>
                    
                    
                    <!-- <div class="container">-->
                    <!--    <div class="col-md-12 ">-->
                    <!--        <div class="card">-->
                    <!--            <div style="box-shadow: #e5dbdb 1px 10px 20px 1px;" class="card-header rounded">-->
                    <!--                <br>-->
                    <!--                <h4 style="font-size:1.9pc"> Lamentamos informar, a <b>forLEARN</b><sup style="font-size:1.5pc">®</sup> está temporariamente indisponível!</h4>-->
                    <!--                <hr>-->
                    <!--                <h5 class="pl-3"> Por favor, tente mais tarde  ! <i  class="fa fa-spinner fa-spin"></i></h3>-->
                    <!--                <center>-->
                    <!--                    <img style="width: 60%;" src="//{{$_SERVER['HTTP_HOST']}}/storage/fonts/servico.webp" >-->
                    <!--                </center>-->
                    <!--                <br><br><br><br><br><br><br><br>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                @endauth
                
                @guest
                    <div class="wrapper">
                        @include('layouts.backoffice.header')
                        @section('guest-content')
                        @show
                    </div>
                @endguest
                
                @include('layouts.backoffice.scripts')
                @include('layouts.backoffice.footer')
  
</body>
</html>
