<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forLEARN | App</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer"
    />
    {{-- <link rel="stylesheet" href="{{asset('css/mobile/app.css')}}"> --}}
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
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

body {
    font-family: 'Nunito Sans', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #0A3147;
}

.container{

    margin-top: 40%;
    background-color: white;
    width: 100%;
    height:80vh;
    outline: none;
    position: fixed;
    bottom: 0px;
    border-radius: 40px 40px 0px 0px;
}
.header-body{
    background-color:white;
    width: 100%;
    
}












/*//////////////////////////////////////////////////////////////////
[ FONT ]*/

@font-face {
  font-family: Poppins-Regular;
  src: url('../fonts/poppins/Poppins-Regular.ttf'); 
}

@font-face {
  font-family: Poppins-Bold;
  src: url('../fonts/poppins/Poppins-Bold.ttf'); 
}

@font-face {
  font-family: Poppins-Medium;
  src: url('../fonts/poppins/Poppins-Medium.ttf'); 
}

@font-face {
  font-family: Montserrat-Bold;
  src: url('../fonts/montserrat/Montserrat-Bold.ttf'); 
}

/*//////////////////////////////////////////////////////////////////
[ RESTYLE TAG ]*/

* {
	margin: 0px; 
	padding: 0px; 
	box-sizing: border-box;
}

body, html {
	height: 100%;
	font-family: Poppins-Regular, sans-serif;
}

/*---------------------------------------------*/
a {
	font-family: Poppins-Regular;
	font-size: 14px;
	line-height: 1.7;
	color: #666666;
	margin: 0px;
	transition: all 0.4s;
	-webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
}

a:focus {
	outline: none !important;
}

a:hover {
	text-decoration: none;
  color: #57b846;
}

/*---------------------------------------------*/
input {
	outline: none;
	border: none;
}


input:focus::-webkit-input-placeholder { color:transparent; }
input:focus:-moz-placeholder { color:transparent; }
input:focus::-moz-placeholder { color:transparent; }
input:focus:-ms-input-placeholder { color:transparent; }

input::-webkit-input-placeholder { color: #0A3147; }
input:-moz-placeholder { color: #0A3147; }
input::-moz-placeholder { color: #0A3147; }
input:-ms-input-placeholder { color: #0A3147; }

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


/*//////////////////////////////////////////////////////////////////
[ login ]*/

.limiter {
  width: 100%;
  margin: 0 auto;
}

.container-login100 {
  width: 100%;  
  min-height: 100vh;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  padding: 15px;

}

.wrap-login100 {
  width: 960px;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 177px 130px 33px 95px;
}

/*------------------------------------------------------------------
[  ]*/
.login100-pic {
  width: 316px;
}

.login100-pic img {
  max-width: 100%;
}


/*------------------------------------------------------------------
[  ]*/
.login100-form {
  width: 290px;
}

.login100-form-title {
  font-family: Poppins-Bold;
  font-size: 24px;
  color: #333333;
  line-height: 1.2;
  text-align: center;

  width: 100%;
  display: block;
  padding-bottom: 50px;
}



/*---------------------------------------------*/
.wrap-input100 {
  position: relative;
  width:100%;
  z-index: 1;
  margin-bottom: 10px;
  padding: 5%;
}



.input100 {
  font-family: Poppins-Medium;
  font-size: 15px;
  line-height: 1.5;
  color: #666666;
  display: block;
  width: 100%;
  background: #e6e6e6;
  height: 50px;
  border-radius: 10px;
  padding: 0 30px 0 68px;
}



/*------------------------------------------------------------------
[ Focus ]*/
.focus-input100 {
  display: block;
  position: absolute;
  border-radius: 10px;
  bottom: 0;
  left: 0;
  z-index: -1;
  width: 100%;
  height: 100%;
  box-shadow: 0px 0px 0px 0px;
  color: ;
}



.input100:focus + .focus-input100 {
  -webkit-animation: anim-shadow 0.5s ease-in-out forwards;
  animation: anim-shadow 0.5s ease-in-out forwards;
}


@-webkit-keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}



@keyframes anim-shadow {
  to {
    box-shadow: 0px 0px 70px 25px;
    opacity: 0;
  }
}


.symbol-input100 {
  font-size: 15px;

  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  align-items: center;
  position: absolute;
  border-radius: 10px;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding-left: 35px;
  pointer-events: none;
  color: #666666;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}


.input100:focus + .focus-input100 + .symbol-input100 {
  color: #0A3147;
  padding-left: 28px;
}
/*------------------------------------------------------------------
[ Button ]*/

.container-login100-form-btn {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding-top: 20px;
}

.login100-form-btn {
  font-family: Montserrat-Bold;
  font-size: 15px;
  line-height: 1.5;
  color: #fff;
  text-transform: uppercase;

  width: 100%;
  height: 50px;
  border-radius: 10px;
  background: #0A3147;
  display: -webkit-box;
  display: -webkit-flex;
  display: -moz-box;
  display: -ms-flexbox;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0 25px;

  -webkit-transition: all 0.4s;
  -o-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
}

.login100-form-btn:hover {
  background: #0590CB;
}



/*------------------------------------------------------------------
[ Responsive ]*/



@media (max-width: 992px) {
  .wrap-login100 {
    padding: 177px 90px 33px 85px;
  }


  .login100-form {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .wrap-login100 {
    padding: 100px 80px 33px 80px;
  }

  .login100-pic {
    display: none;
  }

  .login100-form {
    width: 100%;
  }
}

@media (max-width: 576px) {
  .wrap-login100 {
    padding: 100px 15px 33px 15px;
  }
}


/*------------------------------------------------------------------
[ Alert validate ]*/

.validate-input {
  position: relative;
}

.alert-validate::before {
  content: attr(data-validate);
  position: absolute;
  max-width: 70%;
  background-color: white;
  border: 1px solid #c80000;
  border-radius: 13px;
  padding: 4px 25px 4px 10px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  right: 8px;
  pointer-events: none;

  font-family: Poppins-Medium;
  color: #c80000;
  font-size: 13px;
  line-height: 1.4;
  text-align: left;

  visibility: hidden;
  opacity: 0;

  -webkit-transition: opacity 0.4s;
  -o-transition: opacity 0.4s;
  -moz-transition: opacity 0.4s;
  transition: opacity 0.4s;
}

.alert-validate::after {
  content: "\f06a";
  font-family: FontAwesome;
  display: block;
  position: absolute;
  color: #c80000;
  font-size: 15px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  transform: translateY(-50%);
  right: 13px;
}

.alert-validate:hover:before {
  visibility: visible;
  opacity: 1;
}

@media (max-width: 992px) {
  .alert-validate::before {
    visibility: visible;
    opacity: 1;
  }
}
.header {
            background-color:white;
            width: 100%;
            height: 65px;
            position: fixed;
            top: 0px;
            color:#0A3147;
            box-shadow: 0 0 4px rgba(0, 0, 0, .14), 0 4px 8px rgba(0, 0, 0, .28);
        }
    .grupoBtn{
        justify-content: center;
        display: inline-block;
    }
    .grupoBtn button{
      
       padding: 10px 20px;
       background-color: #666666;
    }
</style>


<body>
    
    
        <div class="header">
            <img src="{{asset('img/mobile/img/chevron_left_96px.png')}}" alt="back" style="height:70px;" id="back_menu" >
            
            <small id="Titulo">Avaliação</small><br>
            sino
        </div>
 

    <div class="container" style="padding:4%;">
      
      
            <select name="lective_year" id="lective_year" style="width: 100%; !important" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
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
            
            {{-- grupo de botoes --}}
            <div class="grupoBtn">
                <button class="btn_menu" onclick="layout(this)" id="1" data-id="Notas finais">1</button>
                <button class="btn_menu" onclick="layout(this)" id="2" data-id="Recurso">2</button>
                <button class="btn_menu" onclick="layout(this)" id="3" data-id="Exame">3</button>
                <button class="btn_menu" onclick="layout(this)" id="4" data-id="Calendário">4</button>
            </div>
            <br>
            <br>
            <center>
                <h1 id="TItulo">Notas finais</h1>
            </center>
            <div id="Disciplina">
                <select id="selectDisciplina" style="width: 100%;"  class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value="1">História</option>
                    <option value="2">História</option>
                    <option value="3">História</option>
                </select>
            </div>
            <div id="ConteudoGeral">

            </div>
    </div>
</body>

</html>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script>

    


    var url="http://{{$url}}";
    var url_back="";
    $(document).ready(function () {
       if ((window.screen.availHeight < 1234) && (window.screen.availWidth < 1234))
         {verificar_sesstion()} else {$(location).attr("href", url);}

       function verificar_sesstion(){
          const dados=JSON.parse(window.localStorage.getItem('forLearnApp'));
          if(dados==null){window.location.href = "{{route('app.index')}}";}
             else{const img="{{asset('storage/attachment')}}/"+dados['user'].image;url_back="/mobile/menu/"+dados['user_secret'].user_secret;}
            }
          $("#back_menu").click(function (e) { 
            window.location.href = url_back;
          });
    })

    function layout(element){
        var item_menu=element.getAttribute("id");
        var titulo=element.getAttribute("data-id");
        $("#TItulo").text("");
        $("#TItulo").text(titulo);
        frame(item_menu)
    }

    function frame(type){

      
        const dados=JSON.parse(window.localStorage.getItem('forLearnApp'));
        var anoLectivo=$("#lective_year").val();
        $.ajax({
                type: "GET",
                url: "/mobile/finance/"+type+"/"+anoLectivo+"/"+dados['user_secret'].user_secret,
                dataType: "json",
                beforeSend:function(){
                },
                success: function (e) {
                    if(e['Type']==1){propina(e)}
                    else if(e['Type']==2){saldo(e)}
                    else if(e['Type']==3){emolumentoExtra(e) }
                    else if(e['Type']==4){divida(e) }
                 }
              });
       }



    //Monta o frame da propina
    function propina(e){
        $("#ConteudoGeral").empty(); 
        var money="{{asset('img/mobile/img/money_96px.png')}}";
        console.log(e)
        if(e['propina'].length){
            var lista='<div class="animate__animated   animate__bounceInLeft"><ul>';
                $.each(e['propina'], function (index, Value) { 
                    lista+='<li>'+Value.emolumento+' ('+Value.display_month+') <img src='+money+' style="background-color:'+Value.color+'; width:50px;"></li>'
                });
                lista+='</ul></div>';
                $("#ConteudoGeral").append(lista); 
            }
            else{
                
                $("#ConteudoGeral").append('<p class="animate__animated   animate__backInLeft">Sem nenhum emulumento de propina encontrado no ano lectivo selecionado!</p>'); 
             } 
    }

    //Monta o frame da saldo em carteira
    function saldo(e){
        $("#ConteudoGeral").empty(); 
       
        if(e['saldo']!=null){
           var saldo='<div class="animate__animated   animate__bounceInLeft"><h2>Disponível</h2><br><h3>'+e['saldo']+'</h3></div>';
           $("#ConteudoGeral").append(saldo);
        }else{
           $("#ConteudoGeral").append('<p class="animate__animated   animate__backInLeft">Sem saldo em carteira encontrado!</p>'); 
        }
    }



    //Monta o frame do emolumentoExtra
    function emolumentoExtra(e){  
        $("#ConteudoGeral").empty(); 
        var money="{{asset('img/mobile/img/money_96px.png')}}";
        console.log(e)
        
            if(e['emolumentoExtra'].length){
            var lista='<div class="animate__animated   animate__bounceInLeft"><ul>';
                $.each(e['emolumentoExtra'], function (index, Value) { 
                    var disciplina =Value.discipline_name!=null?'('+Value.discipline_name+"["+Value.discipline_code+"]"+')':"";
                    var mes_ano =Value.display_month!=null?'('+Value.display_month+')':"";
   lista+='<li>'+Value.emolumento+' '+disciplina+' '+mes_ano+' <img src='+money+' style="background-color:'+Value.color+'; width:50px;"></li>'
                });
                lista+='</ul></div>';
                $("#ConteudoGeral").append(lista); 
            }
            else{
                
                $("#ConteudoGeral").append('<p class="animate__animated   animate__backInLeft">Sem nenhum emulumento encontrado no ano lectivo selecionado!</p>'); 

             } 
       
        

    }


    //Monta o frame do emolumentoExtra
    function divida(e){
        $("#ConteudoGeral").empty(); 
        console.log(e)
        var money="{{asset('img/mobile/img/money_96px.png')}}";
        console.log(e)
        if(e['divida'].length){
            var lista='<div class="animate__animated animate__bounceInLeft"><div><h1>TOTAL <br>'+e['total']+'</h1></div><br><h2>Referente</h2><br><ul>';
                $.each(e['divida'], function (index, Value) { 
                    lista+='<li>'+Value.emolumento+' ('+Value.display_month+') <img src='+money+' style="background-color:'+Value.color+'; width:50px;"></li>'
                });
                lista+='</ul></div>';
                $("#ConteudoGeral").append(lista); 
            }
            else{
                
                $("#ConteudoGeral").append('<p class="animate__animated   animate__backInLeft">Sem dívida encontrada!</p>'); 
             } 


    }

</script>