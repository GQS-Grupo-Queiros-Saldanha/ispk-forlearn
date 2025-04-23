<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>forLEARN | App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

</head>



<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap');
</style>

<!-- Modal HTML embedded directly into document -->

<body>



    <div class="container animate__animated animate__slideInUp ">
        <div class="header-body">

        </div>
        <div>
            <form class="login100-form validate-form">
                <span class="login100-form-title">
                    <img src="{{ asset('img/mobile/img/ForLEARN_06.png') }}" style="margin-top:15px;padding:3px;"
                        alt="Logotipo da forLEARN" class="fotoLogotipo">
                    <p class="login-subtitle"> Bem vindo à forLEARN! </p>
                    <p class="login-subtitle1"> Entre na sua conta </p>

                </span>

                <div class="wrap-input100 validate-input" data-validate="e-mail válida requerido: ex@abc.xyz">
                    <input class="input100" type="text" name="email" placeholder="Insira seu e-mail institucional"
                        id="email">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                    </span>
                </div>


                <div class="wrap-input100 validate-input" data-validate="senha válida requerida">
                    <input class="input100" type="password" name="pass" placeholder="Insira sua senha!"
                        id="secret">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                </div>

                <div class="text-center p-t-12">

                    <a class="txt2 forgot-pass" style="color:blue;" href="#">
                        {{-- Esqueci-me da senha --}}
                    </a>
                </div>


                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" id="entrarBtn">
                        ENTRAR
                    </button>
                </div>

            </form>
        </div>
    </div>


</body>

</html>

@include('Mobile::css.style');
@include('Mobile::modal.modal');

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    var url = "http://{{ $url }}";
    $(document).ready(function() {
        if ((window.screen.availHeight < 1234) && (window.screen.availWidth < 1234)) {
            verificar_sesstion()
            $("#entrarBtn").click(function(e) {
                e.preventDefault();
                var email, secret;
                email = $("#email").val();
                secret = $("#secret").val();
                // $(this).prop('disabled', true);
                console.log("Teste básico");
                validar_login(email, secret);

            });


        } else {
            $(location).attr("href", url);
        }


        function validar_login(email, secret) {
            //método para validar
            $.ajax({
                type: 'POST',
                url: "{{ route('login-app') }}",
                data: {
                    'secret': secret,
                    'email': email
                },
                dataType: "json",
                beforeSend: function() {
                    if (email == "" || secret == "") {
                        open_modal('type_empty',
                            'Por favor preencha os campos:<br> <strong> email e senha </strong>'
                        );
                        return false;
                    }
                },
                success: function(e) {
                    console.log(e);
                    if (e['status'] == "sucesso") {
                        window.localStorage.setItem('forLearnApp', JSON.stringify(e));
                        const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));

                        window.location.href = "/mobile/menu/" + dados['user_secret'].user_secret;
                    } else if (e['status'] == "negado") {
                        open_modal('type_denied',
                            ' As credenciais inseridas não são válidas no sistema!');
                    }
                }
            });
        }


        function verificar_sesstion() {

            const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
            if (dados != null) {
                window.location.href = "/mobile/menu/" + dados['user_secret'].user_secret;

            }
        }

        function close_modal() {

            $(".jquery-modal,.modal").fadeOut(3000);


            setTimeout(function() {
                $("#entrarBtn").prop('disabled', false);

            }, 3000);

        }

        var type, html;

        function open_modal(type, html) {

            switch (type) {
                case 'type_empty':

                    $(".modal").fadeIn();
                    $(".modal .text-modal").html(html);
                    $(".modal .img-modal").prop('src',
                        'https://static.vecteezy.com/system/resources/previews/004/968/537/original/forgot-password-when-login-concept-illustration-flat-design-eps10-modern-graphic-element-for-landing-page-empty-state-ui-infographic-icon-vector.jpg'
                    );

                    break;

                case 'type_denied':

                    $(".modal").fadeIn();
                    $(".modal .text-modal").html(html);
                    $(".modal .img-modal").prop('src',
                        'https://st4.depositphotos.com/3358145/22120/v/380/depositphotos_221201714-stock-illustration-vector-concept-illustration-of-danger.jpg'
                    );

                    break;

                default:

                    break;
            }

            close_modal();


        }





    })
</script>
