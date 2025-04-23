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

    .container{
        height: 70vh!important;  
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
            <h1 id="TItulo">Todas</h1>
        </center>

    <div class="container animate__animated animate__slideInUp " style="padding:4%;">

        <br>
       
          @php
          $meses=[1=>"Janeiro",2=>"Fevereiro",3=>"Março",4=>"Abril",5=>"Maio",6=>"Junho",7=>"Julho",8=>"Agosto",9=>"Setembro",10=>"Outubro",11=>"Novembro",12=>"Dezembro"];
          $Pagamento=["total"=>"PAGO","pending"=>"ESPERA PAGAMENTO","partial"=>"PARCIALMENTE PAGO"];
          $color=["total"=>"success","pending"=>"info","partial"=>"warning"];
            $i=0;
            $conut_not =0;
          @endphp
        <div id="ConteudoGeral">
       
            

            
            <div class="list-group list-notificacao">
             
                    
                @forelse ($notification as $item)
                {{-- @php
                    $conut_not++;
                @endphp

                @if ()
                    
                @else
                    
                @endif --}}

                <a href="#" class="animate__animated animate__zoomInDown list-group-item list-group-item-action mb-2 item-notificacao" style="background-color: {{$item->state_read==null?"#95B7D6":"#A8A8A8"}};" aria-current="true">
                  <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><i class="{{$item->icon}}"></i> <b>{{$item->subject}}</b></h5>
                  
                  </div>
                  <p class="mb-1 mailbox sms_{{$i++}}" onclick="abrir(this)" data-id="{{$item->id}}">  {{nl2br (mb_strimwidth($item->body_messenge, 0, 65, '...'))}}.</p>
                  <b><small><i  class="fas fa-clock"></i>  {{$item->date_sent}}</small></b>
                </a>

                @empty
                <center>Nenhuma notificacão encontrada!</center>
                @endforelse
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
                const img = "{{ asset('storage/attachment') }}/" + dados['user'].image;
                url_back = "{{ route('propina-app') }}";
            }
        }
        $("#back_menu").click(function(e) {
            window.location.href = url_back;
        });

   //Metodos das notificações
   var count='{{$i}}';
                       
for (let index = 0; index < count; index++) {
        var texto=$(".sms_"+index).text();
        $(".sms_"+index).text("");
        $(".sms_"+index).html(texto)
        //depois tirar o espaço e incluir
        var format= $(".sms_"+index).text().replace(/\s/g,' ');
        $(".sms_"+index).html(format)
    }


    });



    function abrir(element){
        const user = JSON.parse(window.localStorage.getItem('forLearnApp'));
         var n_id=element.getAttribute("data-id");
            window.location.href = "/mobile/single_notification/"+ user['user_secret'].user_secret+"/"+n_id;
    }
    



</script>
