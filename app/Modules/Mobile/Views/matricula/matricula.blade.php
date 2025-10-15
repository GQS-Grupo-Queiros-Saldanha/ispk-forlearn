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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
    <title>Matrícula</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</head>


<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Nunito Sans', sans-serif;


    }

    .container {
        height: 50vh !important;
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

    .saldo_d {
        font-size: 28px !important;
    }

    .grupoBtn button {
        background-color: #666666;
        color: white;
        /* background-color: #0590cb; */
    }

    .grupoBtn button i {
        color: #0590cb;
    }
</style>


<body>


    <section class="section_main">



        <div class="header">


            <div class="col-12">
                <div class="row">

                    <div class="col-2">
                        <img src="{{ asset('img/mobile/img/chevron_left_96px.png') }}" alt="back"
                            style="height:70px;" id="back_menu">
                    </div>
                    <div class="col-8">
                        <small id="Titulo">Matrícula</small>

                    </div>
                    <div class="col-2">
                        <i class="fas fa-bell text-red float-right animate__animated   animate__swing " id="bell">

                        </i>
                    </div>
                </div>

            </div>

        </div>

        <br>
        <br>
        <br>

        <div class="col-12">

            <center>
                <div class="col-11">



                    <select name="lective_year" id="lective_year" style="width: 100%; !important"
                        class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                        @foreach ($lectiveYears as $lectiveYear)
                            @if ($lectiveYearSelected == $lectiveYear->id)
                                <option value="{{ $lectiveYear->id }}" selected>
                                    {{ $lectiveYear->currentTranslation->display_name }}
                                </option>
                            @else
                                <option value="{{ $lectiveYear->id }}">
                                    {{ $lectiveYear->currentTranslation->display_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>



                </div>

                <div class="grupoBtn">
                    <button class="btn_menu" id="perfil_data">
                        <i class="fa-solid fa-id-card"></i>
                    </button>
                    <button class="btn_menu" id="matricula_data" style="background-color: white">
                        <i class="fa-solid fa-graduation-cap" style="color: #0590cb;"></i>
                    </button>

                </div>

            </center>
        </div>
        <h1 id="TItulo">Dados da Matrícula</h1>
        <div class="container  animate__animated animate__slideInUp " style="padding:4%;">

            <center>

                <p class="fs-1 titulo_e" id="codigoMat"></p>
            </center>


            <div id="ConteudoGeral">


            </div>
        </div>

    </section>


</body>

</html>
@include('Mobile::css.backoffice')
{{-- @include('Mobile::modal.modal'); --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

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
                url_back = "{{ route('perfil-app') }}";
                frame();
            }
        }
        $("#back_menu").click(function(e) {
            window.location.href = url_back;
        });
    })

    $("#lective_year").change(function() {
        frame()
    })



    function frame() {

        const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
        var anoLectivo = $("#lective_year").val();
        $.ajax({
            type: "GET",
            url: "/mobile/matricula-dados/" + anoLectivo + "/" + dados['user_secret'].user_secret,
            dataType: "json",
            beforeSend: function() {

            },
            success: function(e) {

                if (e['Disciplinas_matricula'].length > 0) {
                    dadosMatricula_get(e);
                } else {
                    $("#ConteudoGeral,#codigoMat").empty();
                    $("#ConteudoGeral").append(
                        '<div class=" back_nd col-12 animate__animated   animate__bounceInLeft back_m"><center><h2 class="saldo_d"><i class="fa-solid fa-triangle-exclamation"></i></h2><br><p class="animate__animated   animate__backInLeft">Nenhuma matrícula foi encontrada no ano lectivo selecionado!</p></center></div>'
                    )
                }

            }
        });
    }


    $(".btn_menu").click(function() {
        $(".btn_menu").css({
            backgroundColor: '#666666'
        });
        $(".btn_menu").children('i').css({
            color: '#0590cb'
        });
        $(this).css({
            backgroundColor: '#0590cb'
        });
        $(this).children('i').css({
            color: 'white'
        });
    });

    $("#perfil_data").click(function(e) {

        window.location.href = url_back;
    });

    var painel;



    //Monta o frame da propina
    function dadosMatricula_get(e) {
        $("#ConteudoGeral").empty();
        var money = "{{ asset('img/mobile/img/money_96px.png') }}";
        console.log(e)
        if (e['studant_class'].length) {


            var acordion = '';

            //Destruidora
            $("#codigoMat").text(e['codigo_mat']);
            $.each(e['studant_class'], function(index, Value) {


                acordion +=
                    '<div class="accordion" id="accordionExample animate__animated   animate__backInLeft"> <div class="accordion-item"><h2 class="accordion-header" id="headingOne"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne_' +
                    Value.display_name + '" aria-expanded="false" aria-controls="collapseOne">' + Value.ano +
                    'º Ano - ' + Value.display_name + ' </button></h2><div id="collapseOne_' + Value
                    .display_name +
                    '" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style=""><div class="accordion-body">';
                $.each(e['Disciplinas_matricula'], function(index, item) {
                    if (Value.ano == item.ano_edicao) {
                        acordion += '<li>' + item.display_name + ' [' + item.codigo_discipline +
                            ']</li>'
                    }
                });
                acordion += '</div></div></div>'
            });
            acordion += '</div>';
            $("#ConteudoGeral").append(acordion);
        } else {

            $("#ConteudoGeral").append(
                '<p class="animate__animated   animate__backInLeft">Sem nenhum emulumento de propina encontrado no ano lectivo selecionado!</p>'
            );
        }
    }
</script>
