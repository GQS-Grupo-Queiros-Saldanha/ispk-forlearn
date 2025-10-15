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
        /* padding: 12px; */
        color: #fff !important;
        margin-left: 1%;
        margin-right: 2%;
    }

    .navbar-logo i {
        margin-right: 8%;
    }


    .navbar {
        background-color: #0060af !important;
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



    .ani-d li,
    .ani-d .dropdown-menu {
        /* width: 200px; */
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

    .ani-d .dropdown-menu .dropdown-item {
        font-size: 14px;
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
        transform: translateY(-1px)
    }

    .inicio-nav,
    .d-menu {
        font-family: Roboto Slab, serif !important;
        font-size: 16px;
        /* padding: 12px; */
        /* border: 1px solid #0060af; */
        border-bottom: none;

    }

    .a-first {
        color: white !important;
        text-transform: uppercase;
        padding-right: 10px !important;
    }





    .d-menu:hover {


        border-radius: 10px 10px 0px 0px;
        transform: translateY(1px)
    }

    .li-nav .nav-link:hover,
    .li-nav .nav-link:active {
        background: white;
        color: #0060af !important;
        transition: all 0.5s;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .navbar .nav-link {
        display: block;
        padding: 0.8rem 3rem;
        padding-left: 0.7rem;
        padding-right: 0.7rem;
        text-decoration: none;
        color: white;

    }

    .navbar .li-nav {
        font-size: 15px;
        text-transform: uppercase;
        font-family: Roboto Slab, serif;
        transition: all 0.5s;
        width: max-content;
        
    }
    
    .navbar .li-nav a{
        color:white;
        padding: 13px;
    }
    
    .navbar .li-nav:hover{
        cursor:pointer;
        background: white;
    }

    .navbar .li-nav:hover a {
        text-decoration:none;
        color:#0060af;
    }
    
    
    .navbar .li-nav .dropdown-menu a {
             text-transform: none;
            color: #1e1e1e;
            width: max-content;
            width: -webkit-fill-available;width: -webkit-fill-available;
             padding: 10px;
    }
    
    .navbar .li-nav .dropdown-menu{
    width: max-content;
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
        font-family: Roboto Slab, serif !important;
    }

    .inicio-nav:hover {
        color: white;
    }

    .nav-opcao {
        margin-right: 10rem !important;
    }

    @media (min-width: 992px) {
        .navbar-expand-lg .navbar-nav .dropdown-menu {}

        .navbar .dropdown-menu {

            width: 200px;
        }

        .a-second {
            width: 265px;
        }

        .a-Third {
            width: 100px;
        }


    }

    .a-first {
        text-align: left;
    }


    @media (max-width: 992px) {
        .navbar-expand-lg .navbar-nav .dropdown-menu {}

        .a-first {
            text-align: left !important;
        }

        .navbar .dropdown-menu {
            border-radius:none!important;
            /* box-shadow: 0 1px 1px #999999; */
            box-shadow: none;
            /* border: 1px solid #0060af; */
            border-top: none;
            width: -webkit-fill-available;
        }

        .nav-opcao {
            margin-right: 0rem !important;
        }

    }

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

    .d-menu:hover {
    background-color: white !important;
    color: #0060af !important;
    /* border-radius: 10px 10px 0px 0px; */
    transform: translateY(1px);
}

</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark " style="margin:0px!important;padding:0!important!">
    <a class="navbar-brand navbar-logo inicio-nav tirar" href="#"><i class="fas fa-retweet"></i>MUDANÇA DE CURSO
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse dropdown ani-d" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">



                        @if (auth()->user()->hasAnyPermission(['mudanca_de_curso']))
                         <li class="nav-item  li-nav">
                            <a class="dropdown-item" target="_blank"
                           href="{{route('change_course.index')}}">
                                 <i class="fas fa-retweet"></i>
                                    listagem de estudantes
                                </a>
                         </li>
                        @endif 
                        
                 
                   
                
                         
    
                    
            
           
            

        </ul>

        <ul class="navbar-nav nav-opcao">
            <li class="nav-item dropdown li-nav">
                <a class="nav-link dropdown-toggle a-first" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-cogs"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                    <a class="dropdown-item" href="{{route('setting-change-course')}}"><i
                                class="fa fa-cogs"></i>Configurar| Mudança de curso</a>

                </div>
            </li>
        </ul>
       

        <a href="/logout" class="text-white float-right font-weight-bold"
            style=" color: #fff;
                                font-family: Roboto Slab,serif;
                                font-size: 20px; padding: 10px;
                                text-shadow: 0 0 10px #000;margin-right: 1em;"><i
                class="fas fa-power-off"></i></a>
    </div>
</nav>
<div>

</div>
<br>
@section('scripts')
    @parent
    <script>
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
    </script>
@endsection
