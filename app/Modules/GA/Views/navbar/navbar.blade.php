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
        /* padding-top: 4px;
        padding-bottom: 4px; */
        font-size: 15px;
        text-transform: uppercase;
        font-family: Roboto Slab, serif;
        transition: all 0.5s;

    }

    .navbar .li-nav .dropdown-menu a {
        text-transform: none;
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

            width: 220px;
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
            border-radius: none !important;
            /* box-shadow: 0 1px 1px #999999; */
            box-shadow: none;
            /* border: 1px solid #0060af; */
            border-top: none;

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

    .hr-nav {
        margin-bottom: 3px;
        margin-top: 3px;
    }
</style>
<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark " style="margin:0px!important;padding:0!important!">
    <a class="navbar-brand navbar-logo inicio-nav" style="margin-left:10px;margin-right:10px;" href="{{route("main.index")}}"><i class="fas fa-home"></i>PAINEL INICIAL</a>
    <a class="navbar-brand navbar-logo inicio-nav tirar" href="#"><i class="fas fa-bookmark"></i>GESTÃO ACADÉMICA
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse dropdown ani-d" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">


            {{-- Cursos --}}

            @if (auth()->user()->hasAnyPermission([
                    'superadmin',
                    'manage-courses',
                    'manage-course-cycles',
                    'manage-course-regimes',
                    'manage-degrees',
                    'manage-duration-types',
                ]))
                <li class="nav-item dropdown li-nav">
                    <a class="nav-link dropdown-toggle a-first" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-graduation-cap"></i>Cursos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @if (auth()->user()->hasAnyPermission(['manage-courses', 'superadmin']))
                            <a class="dropdown-item" href="{{ route('courses.create') }}">
                                <i class="fa-solid fa-graduation-cap"></i>Novo</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-courses', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/courses/courses">
                                <i class="fa-solid fa-graduation-cap"></i>Gerir cursos</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-course-cycles', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/courses/course-cycles">
                                <i class="fa-solid fa-graduation-cap"></i>Ciclos de curso</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-course-regimes', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/courses/course-regimes">
                                <i class="fa-solid fa-graduation-cap"></i>Regimes de curso</a>
                        @endif
                     

                    </div>
                </li>
            @endif

            {{-- Disciplinas --}}

            @if (auth()->user()->hasAnyPermission(['manage-disciplines','manage-discipline-profiles','manage-discipline-areas',
            'manage-discipline-periods','manage-discipline-regimes','manage-optional-groups','superadmin']))
                <li class="nav-item dropdown li-nav">
                    <a class="nav-link dropdown-toggle a-first" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-flask"></i>Disciplinas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                        @if (auth()->user()->hasAnyPermission(['manage-disciplines']))
                            <a class="dropdown-item" href="{{ route('disciplines.create') }}">
                                <i class="fa-solid fa-flask"></i>Novo</a>
                        @endif

                        @if (auth()->user()->hasAnyPermission(['manage-disciplines', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/disciplines/disciplines">
                                <i class="fa-solid fa-flask"></i>Gerir disciplinas</a>
                        @endif

                        @if (auth()->user()->hasAnyPermission(['manage-discipline-profiles', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/disciplines/discipline-profiles">
                                <i class="fa-solid fa-flask"></i>Perfis de disciplinas</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-discipline-areas', 'superadmin']))
                            <hr class="hr-nav">
                            <a class="dropdown-item" href="/gestao-academica/disciplines/discipline-areas">
                                <i class="fa-solid fa-flask"></i>Áreas de disciplinas</a>
                        @endif

                        @if (auth()->user()->hasAnyPermission(['manage-discipline-periods', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/disciplines/discipline-periods">
                                <i class="fa-solid fa-flask"></i>Periodo de disciplinas</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-discipline-regimes', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/disciplines/discipline-regimes">
                                <i class="fa-solid fa-flask"></i>Regime de disciplinas</a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-optional-groups', 'superadmin']))
                            <hr class="hr-nav">
                            <a class="dropdown-item" href="/gestao-academica/disciplines/optional-groups">
                                <i class="fa-solid fa-flask"></i>Grupos opcionais</a>
                        @endif



                    </div>
                </li>
            @endif

            {{-- Planos curriculares de curso --}}
            @if (auth()->user()->hasAnyPermission(['manage-study-plans', 'manage-summaries', 'manage-study-plan-editions', 'superadmin']))

                <li class="nav-item dropdown li-nav">
                    <a class="nav-link dropdown-toggle a-first" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-list-check"></i>Planos curriculares
                    </a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                        @if (auth()->user()->hasAnyPermission(['manage-study-plans', 'superadmin']))

                            <a class="dropdown-item" href="{{ route('study-plans.create') }}">
                                <i class="fa-solid fa-list-check"></i>Novo</a>
                            <a class="dropdown-item" href="/gestao-academica/study-plans">
                                <i class="fa-solid fa-list-check"></i>Gerir Plano de Estudo</a>
                            @if (auth()->user()->hasAnyPermission(['manage-study-plan-editions', 'gerir_horários', 'superadmin']))
                                <a class="dropdown-item"
                                    href="/gestao-academica/study-plan-editions/study-plan-editions">
                                    <i class="fa-solid fa-list-check"></i>Gerir edições de PE</a>
                            @endif
                        @endif
                        {{--
                        @if (auth()->user()->hasAnyPermission(['manage-summaries', 'superadmin']))
                            <hr class="hr-nav">
                            <a class="dropdown-item" href="/gestao-academica/summaries">
                                <i class="fa-solid fa-list-check"></i>Sumários</a>
                        @endif
                        --}}

                    </div>
                </li>
            @endif



          



            {{-- <li class="nav-item  li-nav ">
                <a class="nav-link a-first a-second" href="/check_user">
                    <i class="fas fa-list-check"></i> Gestão de faltas
                </a>
            </li> --}}




        </ul>
        @if (auth()->user()->hasAnyPermission([
                'configuracoes_gest_academ',
                'manage-discipline-curricula',
                'manage-rooms',
                'manage-degree-levels',
                'manage-classes',
                'manage-discipline-classes',
                'manage-discipline-absence-configuration',
                'manage-period-types',
                'manage-access-types',
                'manage-average-calculation-rules',
                'gerir_edificios',
                'manage-year-transition-rules',
                'superadmin',
            ]))

            <ul class="navbar-nav nav-opcao">
                <li class="nav-item dropdown li-nav">
                    <a class="nav-link dropdown-toggle a-first" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cogs"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <a class="dropdown-item" href="/gestao-academica/study-plan-editions/lective-years"><i
                                    class="fa fa-cogs"></i>Ano lectivo</a>
                        <a class="dropdown-item" href="/gestao-academica/study-plan-editions/lective-years-course-curricular-block"><i
                                    class="fa fa-cogs"></i>Ano curricular bloqueado</a>
                        @if (auth()->user()->hasAnyPermission(['manage-classes', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/rooms"><i
                                    class="fa fa-cogs"></i>Salas
                            </a>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['manage-classes', 'superadmin']))
                            <a class="dropdown-item" href="/gestao-academica/study-plan-editions/classes"><i
                                    class="fa fa-cogs"></i>Turmas</a>
                        @endif

                    </div>
                </li>
            </ul>
        @endif

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
