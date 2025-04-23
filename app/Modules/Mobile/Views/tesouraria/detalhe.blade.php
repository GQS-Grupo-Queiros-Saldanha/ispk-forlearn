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

        height: 80vh!important;

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
                    <small id="Titulo">Tesouraria</small>

                </div>
                <div class="col-2">
                    <i class="fas fa-bell text-red float-right animate__animated   animate__swing " id="bell">

                    </i>
                </div>
            </div>

        </div>

    </div>


    <div class="container animate__animated animate__slideInUp" style="padding:4%;">


        <br>
        <br>
        <center>
            <h1 id="TItulo"></h1>
        </center>
          @php
          $meses=[1=>"Janeiro",2=>"Fevereiro",3=>"Março",4=>"Abril",5=>"Maio",6=>"Junho",7=>"Julho",8=>"Agosto",9=>"Setembro",10=>"Outubro",11=>"Novembro",12=>"Dezembro"];
          $Pagamento=["total"=>"PAGO","pending"=>"ESPERA PAGAMENTO","partial"=>"PARCIALMENTE PAGO"];
          $color=["total"=>"success","pending"=>"info","partial"=>"warning"];
          $mes_c =isset($meses[$detail->mes])?"(".$meses[$detail->mes]."-".$detail->year.")":"";
          @endphp
        <div id="ConteudoGeral">
       
         

     
            <div class="card" style="width: 100%;">
                <div class="card-body card-detail">
                    <div class="badge  animate__animated animate__rotateInUpLeft bg-{{$color[$detail->status]}} text-wrap p-2 ">
                        <span style="fcolor:white;">{{$Pagamento[$detail->status]}}</h2>
                     </div>
                     
                     <br>
                     
                     @if (isset($detail->numero_recibo))
                     
                     <p>Nº recibo: <b> {{$detail->numero_recibo??"Erro ao pegar o número do recibo"}}</b></p>
                     @else
                         
                     @endif

                     <p class="card-title">{{ $detail->emolumento}} {{$detail->disciplina!=null? "(".$detail->disciplina."-"."[".$detail->codigo."])" :"" }} {{$mes_c }}
                     </p>
                     
                     @if (($detail->status =="total" || $detail->status=="partial"))

                    <p class="card-text">Valor base: <b style="color:#198754 ">{{number_format ($detail->valor_base,2, ".", ",")}} kz</b></p>
                    
                    @else 

                    <p class="card-text">Valor base: <b style="color:#990707 ">{{number_format ($detail->valor_base,2, ".", ",")}} kz</b></p>
                         
                    @endif

                    @if($detail->status=="total" || $detail->status=="partial")
                    <hr>
                    <p class="card-text">Valor pago: <b style="color: #198754">{{number_format ($detail->valor_pago,2, ".", ",")}} kz</b></p>
                    
                    <p>Data de pagamento: <b>{{$detail->data_pagamento??"Erro ao pegar a data"}}</b></p>
                    <a href="{{ $detail->path }}" download class="btn btn-dark w-100 p-3 f-12"> <i class="fas fa-file-pdf"></i> Descarregar recibo</a>
                        </div>
                    @endif 
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
    var  url_back = "";
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
                url_back = "{{ route('propina-app') }}";
            }
        }
        $("#back_menu").click(function(e) {
            window.location.href = url_back;
        });

        var noty=window.localStorage.getItem('notify');
        $("#bell").html("<sub>"+noty+"</sub>");
      
    })
    // frame(2);
    function layout(element) {
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
            var lista = '<div class="animate__animated   animate__bounceInLeft"><ul>';
            $.each(e['propina'], function(index, Value) {
                lista += '<li>' + Value.emolumento + ' (' + Value.display_month + ') <img src=' + money +
                    ' style="background-color:' + Value.color + '; width:50px;"></li>'
            });
            lista += '</ul></div>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append(
                '<p class="animate__animated   animate__backInLeft">Sem nenhum emulumento de propina encontrado no ano lectivo selecionado!</p>'
                );
        }
    }

    //Monta o frame da saldo em carteira
    function saldo(e) {
        $("#ConteudoGeral").empty();

        if (e['saldo'] != null) {
            var saldo = '<div class="animate__animated   animate__bounceInLeft"><h2>Disponível</h2><br><h3>' + e[
                'saldo'] + '</h3></div>';
            $("#ConteudoGeral").append(saldo);
        } else {
            $("#ConteudoGeral").append(
                '<p class="animate__animated   animate__backInLeft">Sem saldo em carteira encontrado!</p>');
        }
    }



    //Monta o frame do emolumentoExtra
    function emolumentoExtra(e) {
        $("#ConteudoGeral").empty();
        var money = "{{ asset('img/mobile/img/money_96px.png') }}";
        console.log(e)

        if (e['emolumentoExtra'].length) {
            var lista = '<div class="animate__animated   animate__bounceInLeft"><ul>';
            $.each(e['emolumentoExtra'], function(index, Value) {
                var disciplina = Value.discipline_name != null ? '(' + Value.discipline_name + "[" + Value
                    .discipline_code + "]" + ')' : "";
                var mes_ano = Value.display_month != null ? '(' + Value.display_month + ')' : "";
                lista += '<li>' + Value.emolumento + ' ' + disciplina + ' ' + mes_ano + ' <img src=' + money +
                    ' style="background-color:' + Value.color + '; width:50px;"></li>'
            });
            lista += '</ul></div>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append(
                '<p class="animate__animated   animate__backInLeft">Sem nenhum emulumento encontrado no ano lectivo selecionado!</p>'
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
            var lista = '<div class="animate__animated animate__bounceInLeft"><div><h1>TOTAL <br>' + e['total'] +
                '</h1></div><br><h2>Referente</h2><br><ul>';
            $.each(e['divida'], function(index, Value) {
                lista += '<li>' + Value.emolumento + ' (' + Value.display_month + ') <img src=' + money +
                    ' style="background-color:' + Value.color + '; width:50px;"></li>'
            });
            lista += '</ul></div>';
            $("#ConteudoGeral").append(lista);
        } else {

            $("#ConteudoGeral").append('<p class="animate__animated   animate__backInLeft">Sem dívida encontrada!</p>');
        }


    }
</script>
