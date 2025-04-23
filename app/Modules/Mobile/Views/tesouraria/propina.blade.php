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
        <script src="https://kit.fontawesome.com/13b512e6f8.js" crossorigin="anonymous"></script>
    {{-- <link rel="stylesheet" href="{{asset('css/mobile/app.css')}}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap');
</style>


<body>

    <section class="section_main">

    
    <div class="header">


        <div class="col-12">
            <div class="row">

                <div class="col-2">
                    <img src="{{ asset('img/mobile/img/chevron_left_96px.png') }}" alt="back" style="height:70px;"
                        id="back_menu">
                </div>
                <div class="col-8">
                    <small id="Titulo">Tesouraria</small>

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



                <select name="lective_year" id="lective_year" class="form-select form-select-lg mb-3"
                    aria-label=".form-select-lg example">
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
                
                <div class="grupoBtn">
                    <button class="btn_menu" onclick="layout(this)" id="4"
                        data-id="Emolumento(s) por pagar" style="background: #F88C3F;">
                        <i class="fas fa-money-check-dollar" style="color: white"></i>
                    </button>
                    <button class="btn_menu" onclick="layout(this)" id="1" data-id="Mensalidade">
                        <i class="fas fa-receipt"></i>
                    </button>
                    <button class="btn_menu" onclick="layout(this)" id="3"
                    data-id="Outro(s) emolumento(s)">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                        
                    </button>
                    <button class="btn_menu" onclick="layout(this)" id="2"
                        data-id="Saldo em carteira"><i class="fas fa-wallet"></i></button>
                </div>
                <h1 id="TItulo" >Emolumento(s) por pagar</h1>
            </div>
        </center>
    </div>
    <div class="container  animate__animated animate__slideInUp " style="padding:4%;">



        {{-- grupo de botoes --}}
        <br>
        <br>
        <center>
            <h1 id="TItulo"></h1>
        </center>
        <div id="ConteudoGeral">
                <div class="col-12">
                    
                </div>
        </div>

    </div>

</section>


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
            }
        }


        $("#back_menu").click(function(e) {

            window.location.href = url_back;
        });

        var noty=window.localStorage.getItem('notify');
        $("#bell").html("<sub>"+noty+"</sub>");
      

    })
    frame(4);

    function layout(element) {

        // $(".btn_menu").css('background-color: ','#666666;');
        $(".btn_menu").css({backgroundColor: '#666666'});
        $(".btn_menu").children('i').css({color: '#F88C3F'});
        
        $(element).css({backgroundColor: '#F88C3F'});
        $(element).children('i').css({color: 'white'});

        var item_menu = element.getAttribute("id");
        var titulo = element.getAttribute("data-id");
        $("#TItulo").text("");
        $("#TItulo").text(titulo);
        frame(item_menu)
    }

    function frame(type) {

        const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
        var anoLectivo = $("#lective_year").val();
        $.ajax({
            type: "GET",
            url: "/mobile/finance/" + type + "/" + anoLectivo + "/" + dados['user_secret'].user_secret,
            dataType: "json",
            beforeSend: function() {},
            success: function(e) {
                if (e['Type'] == 1) {
                    propina(e)
                } else if (e['Type'] == 2) {
                    saldo(e)
                } else if (e['Type'] == 3) {
                    emolumentoExtra(e)
                } else if (e['Type'] == 4) {
                    divida(e)
                }
            }
        });
    }




    //Monta o frame da propina
    function propina(e) {
        $("#ConteudoGeral").empty();
        var money = "{{ asset('img/mobile/img/money_96px.png') }}";
        console.log(e)
        if (e['propina'].length) {
            var lista = '<center><div class="col-11 animate__animated   animate__bounceInLeft">';
            $.each(e['propina'], function(index, Value) {
                lista += '<div class="row" onclick="route_detail(this)" data-route=' + Value.id_artiRequest + '><div class="col-10" >' + Value
                    .emolumento + ' (' + Value.display_month + ') </div><div class="col-2" style="background-color:' + Value.color +'"><i class="'+Value.icon+'" style="color:white"></i></div></div>'
            });
            lista += '</div></center>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append('<div class=" back_nd col-12 animate__animated   animate__bounceInLeft back_m"><center><h2 class="saldo_d"><i class="fa-solid fa-triangle-exclamation"></i></h2><br><p class="animate__animated   animate__backInLeft">Nenhum emolumento de propina foi encontrado no ano lectivo selecionado!</p></center></div>'
            );
        }
    }

    //Monta o frame da saldo em carteira
    function saldo(e) {
        $("#ConteudoGeral").empty();

        if (e['saldo'] != null) {
            var saldo = '<div class="col-12 animate__animated   animate__bounceInLeft back_m"><h2 class="saldo_d">Disponível</h2><br><h3 class="saldo_m">' + e[
                'saldo'] + ' kz</h3></div>';
            $("#ConteudoGeral").append(saldo);
        } else {
            $("#ConteudoGeral").append(
                '<div class=" back_nd col-12 animate__animated   animate__bounceInLeft back_m"><center><h2 class="saldo_d"><i class="fa-solid fa-triangle-exclamation"></i></h2><br><p class="animate__animated   animate__backInLeft">Sem saldo em carteira!</p></center></div>');
        }
    }



    //Monta o frame do emolumentoExtra
    function emolumentoExtra(e) {
        $("#ConteudoGeral").empty();
        var money = "{{ asset('img/mobile/img/money_96px.png') }}";
        console.log(e)

        if (e['emolumentoExtra'].length) {


            var lista = '<center><div class="col-11 animate__animated   animate__bounceInLeft">';
            $.each(e['emolumentoExtra'], function(index, Value) {
              
                var disciplina = Value.discipline_name != null ? '(' + Value.discipline_name + "[" + Value
                    .discipline_code + "]" + ')' : "";
                var mes_ano = Value.display_month != null ? '(' + Value.display_month + ')' : "";

                lista += '<div class="row" onclick="route_detail(this)" data-route=' + Value.id_artiRequest + '><div class="col-10" >' + Value
                    .emolumento + ' ' + disciplina + ' ' + mes_ano + '</div><div class="col-2" style="background-color:' + Value.color +'"><i class="'+Value.icon+'" style="color:white"></i></div></div>'
            });
            lista += '</div></center>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append('<div class=" back_nd col-12 animate__animated   animate__bounceInLeft back_m"><center><h2 class="saldo_d"><i class="fa-solid fa-triangle-exclamation"></i></h2><br><p class="animate__animated   animate__backInLeft">Nenhum emolumento foi encontrado no ano lectivo selecionado!</p></center></div>'
            );

        }



    }


    //Monta o frame do emolumentoExtra
    function divida(e) {
        $("#ConteudoGeral").empty();
        console.log(e)
        var money = "{{ asset('img/mobile/img/money_96px.png') }}";
        console.log(e)
        if (e['divida'].length) {

            var lista  = '<div class=" back_di col-12 animate__animated   animate__bounceInLeft back_m"><h2 class="saldo_d">Total</h2><br><h3 class="saldo_m">' + e[
                'total'] + ' kz</h3></div><br><h2>Referente</h2><br><center><div class="col-11 animate__animated   animate__bounceInLeft">';
            // var lista = '<div class="animate__animated animate__bounceInLeft"><div><h1>TOTAL <br>' + e['total'] +
            //     '</h1></div><br><h2>Referente</h2><br><ul>';
            $.each(e['divida'], function(index, Value) {
                lista += '<div class="row" onclick="route_detail(this)" data-route=' + Value.id_artiRequest + '><div class="col-10" >' + Value
                    .emolumento + '(' + Value.display_month + ') </div><div class="col-2" style="background-color:' + Value.color +'"><i class="'+Value.icon+'" style="color:white"></i></div></div>'
                    
            });
            lista += '</div></center>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append('<div class=" back_nd col-12 animate__animated   animate__bounceInLeft back_m"><center><h2 class="saldo_d"><i class="fa-solid fa-triangle-exclamation"></i></h2><br><p class="animate__animated   animate__backInLeft">Nenhum emolumento por pagar foi encontrado no ano lectivo selecionado!</p></center></div>');
            
        }


    }


    //routa detalhe
    function route_detail(element) {
        var item = element.getAttribute("data-route");
        url_datail = "/mobile/detail/" + item;
        window.location.href = url_datail;
    }
</script>
