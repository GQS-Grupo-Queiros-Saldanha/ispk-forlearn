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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .container {

        height: 80vh !important;

    }

    .header-body {
        background-color: white;
        width: 100%;

    }


    /*---------------------------------------------*/
    button {
        outline: none !important;
        border: none;
        background: transparent;
    }

    button:hover {
        cursor: pointer;
    }

    /*//////////////////////////////////////////////////////////////////
            [ Utility ]*/
    .txt1 {
        font-family: Poppins-Regular;
        font-size: 13px;
        line-height: 1.5;
        color: #999999;
    }

    .txt2 {
        font-family: Poppins-Regular;
        font-size: 13px;
        line-height: 1.5;
        color: #666666;
    }


    .header {
        background-color: white;
        width: 100%;
        height: 65px;
        position: fixed;
        top: 0px;
        color: #0A3147;
        box-shadow: 0 0 4px rgba(0, 0, 0, .14), 0 4px 8px rgba(0, 0, 0, .28);
    }

    .grupoBtn {
        justify-content: center;
        display: inline-block;
    }

    .grupoBtn button {

        padding: 10px 20px;
        background-color: #666666;
    }

    .container {
        height: 70vh !important;
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
                    <small id="Titulo">Notificações</small>

                </div>
                <div class="col-2">


                    </i>
                </div>
            </div>

        </div>

    </div>

    <br>
    <br>
    <br>
    <center>
        <h1 id="TItulo">Mensagem</h1>
    </center>

    <div class="container animate__animated animate__slideInUp" style="padding:4%;">


        @php
            $meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
            $Pagamento = ['total' => 'PAGO', 'pending' => 'ESPERA PAGAMENTO', 'partial' => 'PARCIALMENTE PAGO'];
            $color = ['total' => 'success', 'pending' => 'info', 'partial' => 'warning'];
            $i = 0;
        @endphp
        <div id="ConteudoGeral">






            <div class="col-12">
                <div class="row">
                    <div class="col-4 mt-4" id="col_perfil" style="    margin-top: 2.2rem!important;">
                        <div style="width:100%; heigth:120px; border-radius:50px;" id="fotoPerfil"
                            alt="foto do remitente">

                        </div>

                    </div>
                    <div class="col-8">

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><small>Assunto: <b>{{ $notification->subject }}</b></small></li>
                            <li class="list-group-item"><i  class="fas fa-user"></i> <small>{{ $notification->fullname }} <<
                                    {{ $notification->email }}>></small></li>
                            <li class="list-group-item"><small><i  class="fas fa-calendar"></i> <small>{{ $notification->date_sent }}</small></li>

                        </ul>


                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="Mensagem" class="sms-notification">
                            {{ $notification->body_messenge }}
                        </div>
                            @if ($notification->file != null)
                                <p>
                                    <a target="_blank" href="{{ asset($notification->file) }}" class="btn btn-primary"
                                        style="border-radius:4px; color:white;">
                                        <i class="fas fa-paperclip">
                                            Abrir Anexo
                                        </i>
                                    </a>
                                </p>
                            @endif

                    </div>
                </div>














            </div>

        </div>
</body>
@include('Mobile::css.backoffice')

</html>
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
                var photo = "{{ $notification->image }}";
                const img = "{{ asset('storage/attachment') }}/" + photo;
                url_back = "/mobile/notification/" + dados['user_secret'].user_secret;
                $("#fotoPerfil").css("background-image", "url('" + img + "')");
            }
        }
        $("#back_menu").click(function(e) {
            window.location.href = url_back;
        });

        //Metodos das notificações



        var texto = $("#Mensagem").text();
        $("#Mensagem").text("");
        $("#Mensagem").html(texto)



    });
</script>
