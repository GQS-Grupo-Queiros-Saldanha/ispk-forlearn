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
            <a class="navbar-brand navbar-logo inicio-nav tirar" href="#"><i class="fas fa-bookmark"></i>AVALIAÇÃO
            </a>

            <a class="navbar-brand navbar-logo inicio-nav casa-inicio" style="margin: 0%; transform: scale(0,0);"
                href="{{ route('panel_avaliation') }}"><i class="fa fa-home"></i>
            </a>



            {{-- Lançar notas --}}



            <div class="dropdown ani-d">
                <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i>Lançar
                </li>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 240px;">
                    @if (auth()->user()->hasAnyRole(['teacher', 'coordenador_curso', 'coordenador', 'superadmin']))
                        <a class="dropdown-item" target="_blank" href="/avaliations/avaliacao_aluno/create">
                            <i class="fa fa-pencil-square-o"></i>
                            Notas
                        </a>
                        <a class="dropdown-item" target="_blank" href="/avaliations/other_avaliations">
                            <i class="fa fa-pencil-square-o"></i>
                            OA's</a>
                        {{-- <a class="dropdown-item fa-solid fa-arrow-right" style="padding-bottom: 5%" target="_blank" href="/avaliations/old_student_final_grade"> Atribuir Notas TFC</a> --}}
                        <hr class="m-0">
                    @endif
                    <a class="dropdown-item" target="_blank" href="/grades/teacher">
                        <i class="fa fa-pencil-square-o"></i>
                        Notas de exame de acesso</a>
                    <hr class="m-0">

                    <a class="dropdown-item" target="_blank" href="/avaliations/old_student">
                        <i class="fa fa-pencil-square-o"></i>
                        Notas por transição</a>
                </div>

            </div>

            {{-- Exibir notas --}}

            <div class="dropdown ani-d">
                <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-eye"></i>Exibir
                </li>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 250px;">

                    <a class="dropdown-item" target="_blank" href="/avaliations/show_final_grades"><i
                            class="fa fa-eye"></i> Avaliação</a>



                    <a class="dropdown-item" target="_blank" href="/avaliations/discipline_grades_mac/10"><i
                            class="fa fa-eye"></i> MAC</a>

                    <a class="dropdown-item" target="_blank" href="/avaliations/discipline_grades_st">
                        <i class="fa fa-eye"></i> Classificação final</a>

                    <a class="dropdown-item" target="_blank" href="/avaliations/discipline_exame_grades/20"> <i
                            class="fa fa-eye"></i> Exame</a>

                    <a class="dropdown-item" target="_blank" href="/avaliations/discipline_recurso_grades/0"><i
                            class="fa fa-eye"></i> Exame de recurso</a>
                    <a class="dropdown-item" href="/avaliations/discipline_exame_especial_grades/40">
                        <i class="fa fa-eye"></i> Exame especial</a>
                        
                    <a class="dropdown-item" href="/avaliations/discipline_tfc_grades/6">
                            <i class="fa fa-eye"></i> Trabalho de fim de curso
                    </a>
                    
                        <hr class="m-0">

                    <a class="dropdown-item" target="_blank" href="/grades/student">
                        <i class="fa fa-eye"></i>
                        Notas de exame de acesso</a>
                    @if (auth()->user()->hasAnyRole(['superadmin', 'staff_forlearn', 'teacher']))
                        <a class="dropdown-item" target="_blank" href="/avaliations/grade">
                            <i class="fa fa-eye"></i> Notas do estudante</a>
                    @endif

                    {{-- <a class="dropdown-item" target="_blank" href="/avaliations/show_summary_grades"><i
                            class="fa fa-file-powerpoint-o"></i> Sumário de notas</a> --}}
                </div>


            </div>

            {{-- Publicar notas --}}

            @if (auth()->user()->hasAnyRole(['coordenador-curso', 'superadmin']))
                <div class="dropdown ani-d">
                    <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-play"></i>Publicar
                    </li>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">


                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank"
                            href="/avaliations/discipline_grades_mac/15">
                            <i class="fa fa-play"></i> MAC
                        </a>

                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank"
                            href="/avaliations/discipline_grades_coordenador">
                            <i class="fa fa-play"></i> Classificação final
                        </a>

                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank"
                            href="/avaliations/discipline_exame_grades/25">
                            <i class="fa fa-play"></i> Exame
                        </a>

                        <a class="dropdown-item" style="padding-bottom: 5%" target="_blank"
                            href="/avaliations/discipline_recurso_grades/1">
                            <i class="fa fa-play"></i> Exame de recurso
                        </a>

                        <a class="dropdown-item" href="/avaliations/discipline_exame_especial_grades/35">
                            <i class="fa fa-play"></i> Exame especial
                        </a>
                        <a class="dropdown-item" href="/avaliations/discipline_tfc_grades/1">
                            <i class="fa fa-play"></i> Trabalho de fim de curso
                        </a>


                    </div>
                </div>
            @endif

            {{-- CANSULTAS --}}

            <div class="dropdown ani-d">
                <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style=" text-align: left!important;">
                    <i class="fa fa-search"></i> CONSULTAR
                </li>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 250px;">


                    <a class="dropdown-item" target="_blank" href="/avaliations/curricular_path">
                        <i class="fa fa-search"></i>Percurso académico </a>

                </div>

            </div>

            {{-- REQUERER --}}

            <div class="dropdown ani-d">
                <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style=" text-align: left!important;">
                    <i class="fa fa-file-text"></i> REQUERER
                </li>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 250px;">

                    <a class="dropdown-item id_studentPercursoAcademico element" target="_blank"
                        href="/avaliations/schedule_exam"><i class="fa fa-file-text"></i> Marcação de exame </a>

                </div>

            </div>

            {{-- REQUERER --}}

            <div class="dropdown ani-d">
                <li class=" dropdown-toggle text-white d-menu" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style=" text-align: left!important;">
                    <i class="fa fa-bar-chart"></i> ESTATÍSTICAS
                </li>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 250px;">

                    <a class="dropdown-item id_studentPercursoAcademico element" target="_blank" href="#">
                        <i class="fa fa-bar-chart"></i> Documentos</a>

                </div>

            </div>

            {{-- Configuraçoes --}}

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
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 260px;">


                            <a class="dropdown-item" target="_blank" href="/avaliations/avaliacao">
                                <i class="fa fa-cogs"></i> Avaliação</a>

                            <a class="dropdown-item" target="_blank" href="/avaliations/school-exam-calendar">
                                <i class="fa fa-cogs"></i> Calendário
                                de
                                provas</a>
                            <a class="dropdown-item" target="_blank" href="/avaliations/pauta_student_config">
                                <i class="fa fa-cogs"></i> Limite de pagamento de propina
                            </a>

                            <a class="dropdown-item" target="_blank" href="/avaliations/plano_estudo_avaliacao">
                                <i class="fa fa-cogs"></i>
                                Plano de
                                estudos e
                                avaliação</a>
                            <a class="dropdown-item" target="_blank" href="/avaliations/tipo_avaliacao">
                                <i class="fa fa-cogs"></i>
                                Tipos de avaliações</a>
                            <a class="dropdown-item" target="_blank" href="/avaliations/tipo_metrica"><i
                                    class="fa fa-cogs"></i> Tipos de métrica</a>


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
