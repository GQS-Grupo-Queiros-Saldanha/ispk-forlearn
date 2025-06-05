@section('title',__('Relatórios de confirmação de matrículas '))
<style>
    @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');

    .cabecalho {
        font-family: 'Tinos' , serif;
        text-transform: uppercase;
        margin-top: 15px;
    }

    .cabecalho>*,
    .titulo>* {
        padding: 0;
        margin: 0;
        padding-top: 3px;
    }

    .cabecalho .instituition,
    .cabecalho .area,
    .titulo p {
        font-size: 1rem;
        font-weight: 700;
    }

    .cabecalho .instituition {
        font-size: 20px!important;
        letter-spacing: 1px;
        padding-bottom: 0px;
        margin-bottom: 0px; 
        
    }

    .cabecalho .area {
        padding-top:0px;
    }
    .cabecalho .decreto {
        font-size: 0.5rem;
        text-align: left;
        text-indent: 210px;
        padding-top:0px;
        top:-10;
        
        position: relative;
    }

    .cabecalho .logotipo {
        width: 76px;
        height: 76px;
    }

    .titulo p {
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .titulo .a {
        padding-top: 30px;
        padding-bottom: 5px;
    }

    .table_te {
        
     
        width: 76%;
        margin-left:  12%;
        text-align: right;
        font-family: calibri light;
        margin-bottom: 6px;
        
    }
    .cor_linha {
        background-color: #999;
        color: #000;
    }


  
    .table_te th,.table_te td {
        border: 1px solid #fff;
        background-color: #F9F2F4;
              
        padding: 3px!important;
    }

    .last-line td{
        background-color: #cecece; 
    }
    .line td{
        background-color: #ebebeb; 
    }

    .tabble_te thead {}

    .logotipo img{
        width: 140px!important;
        height: 140px!important;
        margin-top: 20px;
    }
    .assinaturas p,.data{ 
        font-size: 17px;
    }
    .data,.assinaturas{
        margin-left: 12%;
    }

    .bg0{
        background-color: #2f5496!important;
    }
    .bg1{
        background-color: #8eaadb!important;
    }
    .bg2{
        background-color: #d9e2f3!important;
    }
    .bg3{
        background-color:#fbe4d5!important;
    }
    .bg4{
        background-color:#f4b083!important;
    } 

    
    .f1{
        font-size: 14pt!important;    
    }
    .f2{
        font-size: 13pt!important;
    }
    .f3{
        font-size: 12pt!important;
    }
    .f4{
        font-size: 11pt!important;
    }
    .pd{
        width: 60px;
        
    }
    .pd1{
        width: 70px;    
    }

    .strange{
        color:#1c65e5; 
    }
 
</style>
<center>
   @php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/app/public/' . $institution->logotipo;
    @endphp
    <div class="logotipo" style="background-image: url()">
        <img src="{{$logotipo}}" alt="" srcset="">
    </div>
    <div class="cabecalho">
        <p class="instituition">{{ $institution->nome }}</p>
        <p class="decreto">Criado pelo {{$institution->decreto_instituicao}}</p>
        <p class="area">{{isset($area) ? ''.$area : '' }}</p>
    </div>  
    <div class="titulo">
        <p class="a">{{isset($title1) ? ''.$title1 : '' }}</p>
        <p class="y">{{isset($title2) ? ''.$title2 : '' }}</p>
    </div>
</center>
<br>
<br>
