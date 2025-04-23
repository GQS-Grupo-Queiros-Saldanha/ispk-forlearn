<style>
    a {
        color: #1e1e1e;
    }

    * {
        /* font-family: "Roboto Slab"; */
    }

    .ani-d li i {
        margin-right: 5%;
    }

    .navbar .dropdown-menu a i {
        margin-right: 10% !important;
    }

    .navbar .dropdown-menu a {
        text-align: left;
    }

    .navbar-logo {
        padding: 12px;
        color: #fff;
        margin-left: 1%;
        margin-right: 2%;
    }

    .navbar-logo i {
        margin-right: 8%;
    }

    .navbar-mainbg {
        background-color: #0060af;
        padding: 0;
    }

    .nav-tabs .nav-item {
        text-align: center;
        box-shadow: 1px 0px 1px 1px #ebedef;

    }

    .nav-tabs .nav-item i {
        font-size: 16px;
        color: #0060af;
        float: left;

    }


    .ani-d li {
        text-transform: uppercase;
    }

    .ani-d li,
    .ani-d .dropdown-menu {
        width: 200px;
        text-align: center !important;
    }

    .ani-d li {
        width: auto;
        font-size: 16px;
    }


    .ani-d .dropdown-menu .dropdown-item:hover {
        background-color: #0060af;
        color: white;
        animation: animar 1s;
    }

    .ani-d .dropdown-menu .dropdown-item:active {
        animation: animar-click 1s;
        animation: baixar 2s;
    }

    .navbar .dropdown-menu {
        border-radius: 0px 0px 10px 10px;
        /* box-shadow: 0 1px 1px #999999; */
        box-shadow: 0 6px 14px #999;
        /* border: 1px solid #0060af; */
        border-top: none;
        transform: translateY(0px)
    }

    .inicio-nav,
    .d-menu {
        font-family: Roboto Slab, serif;
        font-size: 16px;
        padding: 12px;
        border: 1px solid #0060af;
        border-bottom: none;

    }

    .d-menu:hover {
        background-color: white !important;
        color: #0060af !important;

        border-radius: 10px 10px 0px 0px;
        transform: translateY(1px)
    }

    @keyframes animar {
        0% {
            color: black;
            padding-left: 0%;
        }

        100% {
            color: white;
        }
    }

    @keyframes animar-click {
        0% {

            color: black;

        }

        100% {
            color: white;
            padding-left: 50%;
        }
    }

    @keyframes baixar {
        0% {}

        100% {

            transform: translate(1px, 10px);
        }
    }

    .navbar {
        /* position: sticky;  */
    }

    .inicio-nav:hover {
        color: white;
    }

    .painel-botoes {}

    .dt-buttons {
        float: left;
        margin-bottom: 20px;
    }

    .dataTables_filter label {
        float: right;
    }


    .dataTables_length label {
        margin-left: 10px;
    }

    .texto-verde {
        color: #38c172 !important
    }

    .texto-vermelho {
        color: #e3342f !important
    }

    .texto-preto {
        color: #555657 !important
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #38c172;
        -webkit-transition: .4s;
        transition: .4s;
        transform: scale(0.7, 0.7);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #e3342f;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #e3342f;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .circulo {
        border-radius: 0.5rem !important;
    }
</style>

{{-- Barra de navegação --}}

<nav class="navbar navbar-expand-lg navbar-mainbg col-12" style="margin:0px!important;padding:0!important">

    {{-- Botão Minimizar/Maximizar --}}
    <div class="col-10">

        <div class="row">
            
    <a class="navbar-brand navbar-logo inicio-nav" style="margin-left:10px;margin-right:10px;" href="{{route("main.index")}}"><i class="fas fa-home"></i>PAINEL INICIAL
            </a>
            
            <a class="navbar-brand navbar-logo inicio-nav tirar" href="#"><i class="fas fa-bookmark"></i>EVENTOS
            </a>

            <a class="navbar-brand navbar-logo inicio-nav casa-inicio" style="margin: 0%; transform: scale(0,0);"
                href="{{ route('panel_avaliation') }}"><i class="fa fa-home"></i>
            </a>




        </div>

    </div>

    <div class="col-2">

        {{-- Configuraçoes --}}
        <div class="row">
            <div class="col-6">
                @if (auth()->user()->hasAnyRole(['superadmin']))
                    <div class="dropdown ani-d">
                        <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="text-align: left!important;width: 60px;">
                            <i class="fas fa-cogs"></i>
                        </li>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 200px;">
                            <a class="dropdown-item" href="/gestao-academica/event-types">
                                <i class="fa-solid fa-calendar-days"></i>Tipos de Eventosa</a>
                            
                        </div>
  
                    </div>
                @endif
            </div>
            <div class="col-6">


                <div class="dropdown btn-logout " style="display: none" title="Terminar a sessão">
                    <a href="/logout" class="text-white float-right font-weight-bold"
                        style="    color: #fff;
                                font-family: Roboto Slab,serif;
                                font-size: 20px; padding: 10px;
                                text-shadow: 0 0 10px #000;"><i
                            class="fas fa-power-off"></i></a>

                </div>
            </div>
        </div>


    </div>
</nav>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@include('GA::events.navbar.popup')
<script>
    setTimeout(function() {
        var cookies = document.cookie;

        var nova = cookies.split(";");

        if (nova[0] == "tela=cheia") {



            $(".left-side-menu,.top-bar").hide();
            $(".btn-logout").show();

            $(".content-wrapper").css({
                margin: '0 auto',
                marginTop: '0px',
                position: 'absolute',
                left: '0',
                top: '0',
                padding: '0',
                width: '100%'
            });

            $(".content-panel").css({
                marginTop: '0px'
            });
        }

        $(".tirar").click(function() {

            var cookies = document.cookie;

            var nova = cookies.split(";");

            if (nova[0] == "tela=cheia") {


                $(".left-side-menu,.top-bar").show();
                $(".btn-logout").hide();
                $(".content-wrapper").css({
                    // margin: '0 auto',
                    // marginTop: '0px',  
                    position: 'absolute',
                    left: '370px',
                    top: '84px',
                    padding: '20px',
                    width: 'calc(100% - 370px)'
                });

                $(".content-panel").css({
                    marginTop: '14px'
                });


                document.cookie = "tela=normal";

            } else if (nova[0] == "tela=normal") {

                $(".btn-logout").show();
                $(".left-side-menu,.top-bar").hide();

                $(".content-wrapper").css({
                    margin: '0 auto',
                    marginTop: '0px',
                    position: 'absolute',
                    left: '0',
                    top: '0',
                    padding: '0',
                    width: '100%'
                });

                $(".content-panel").css({
                    marginTop: '0px'
                });
                document.cookie = "tela=cheia";

            } else {
                document.cookie = "tela=cheia";
            }

        });
    }, 500);
</script>
