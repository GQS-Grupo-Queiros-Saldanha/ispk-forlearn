<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forLEARN | App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- <link rel="stylesheet" href="{{asset('css/mobile/app.css')}}"> --}}

</head>


<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap');


    .container {

        height: 40vh !important;

    }

    .grupoBtn button{
        background-color: #666666;
        color: white; 
        /* background-color: #0590cb; */
    }
    .grupoBtn button i{
        color: #0590cb; 
    }
</style>

<body>


    <div class="header dispose_fade">


        <div class="col-12">
            <div class="row">

                <div class="col-2">
                    <img src="{{ asset('img/mobile/img/chevron_left_96px.png') }}" alt="back" style="height:70px;"
                        id="back_menu">
                </div>
                <div class="col-8">
                    <small id="Titulo">Perfil</small>

                </div>
                <div class="col-2">
                    <i class="fas fa-bell text-red float-right animate__animated   animate__swing " id="bell">

                    </i>
                </div>
            </div>

        </div>

    </div><br><br><br>
    <div class="col-12 dispose_fade">
        <center>
            <div class="painel_img">

                <div id="fotoPerfil">

                </div>
                {{-- <img src="" alt="perfil" border-radius:4px;"
                        id="fotoPerfil"> --}}
            </div>
        </center>
        <h1 id="TItulo"></h1>
    </div>
    <center>
    <div class="grupoBtn">
            <button class="btn_menu" style="background-color: white">
                <i class="fa-solid fa-id-card" style="color: #0590cb;"></i>     
            </button>
            <button class="btn_menu" id="matricula_data">
                <i class="fa-solid fa-graduation-cap"></i>
            </button>
            <button class="btn_menu" id="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </center>
    <div class="container  animate__animated animate__slideInUp dispose_fade" style="padding:4%;">

        <div class="perfil_estudante">
            <Label class="text_perfil">Nome Completo:</Label><br>
            <Label class="text_perfil_data"id="u_fullname"></Label>
            <br>
            <Label class="text_perfil">e-mail:</Label><br>
            <Label class="text_perfil_data"id="u_email"></Label> 
            <br>
            <Label class="text_perfil">Curso:</Label><br>
            <Label class="text_perfil_data"id="u_curso"></Label>
            <br>
            <Label class="text_perfil">Matrícula:</Label><br>
            <Label class="text_perfil_data"id="u_matricula"></Label>

            
        </div>
    </div>


</body>

</html>
@include('Mobile::css.backoffice')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    var url = "http://{{ $url }}";
    var url_back = "";
    $(document).ready(function() {
        if ((window.screen.availHeight < 1234) && (window.screen.availWidth < 1234)) {
            verificar_sesstion()


        } else {
            $(location).attr("href", url);
        }




        function verificar_sesstion() {

            const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
            if (dados == null) {
                window.location.href = "{{ route('app.index') }}";

            } else {
                
                const img = "{{ asset('storage/attachment') }}/" + dados['user'].image;
                url_back = "/mobile/menu/" + dados['user_secret'].user_secret;

                $("#TItulo").text(dados['user'].name);
                $("#u_fullname").text(dados['user'].fullname);
                $("#u_email").text(dados['user'].email);
                $("#u_curso").text(dados['user'].curso);
                $("#u_ano").text(dados['user'].curso);
                $("#u_matricula").text(dados['user'].matricula);
                $("#u_turma").text(dados['user'].matricula);
                $("#fotoPerfil").attr('src', img);
                $("#fotoPerfil").css("background-image", "url('" + img + "')");
            }
        }


        $("#back_menu").click(function(e) {

            window.location.href = url_back;
        });

        $("#logout").click(function(e) {

            localStorage.removeItem('forLearnApp');
            window.location.href = "";

        });


        $(".btn_menu").click(function () {
            $(".btn_menu").css({backgroundColor: '#666666'});
            $(".btn_menu").children('i').css({color: '#0590cb'});
            $(this).css({backgroundColor: '#0590cb'});
            $(this).children('i').css({color: 'white'});
        });


        $("#matricula_data").click(function(e) {
            const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
            window.location.href = "/mobile/matricula/" + dados['user_secret'].user_secret;

        });


    })
</script>
