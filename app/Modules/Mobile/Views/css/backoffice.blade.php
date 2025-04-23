<script src="https://kit.fontawesome.com/13b512e6f8.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Nunito Sans', sans-serif;
        background: #0A3147;
    }

    .container {

        margin-top: 40%;
        background-color: white;
        width: 100%;
        height: 40vh;
        outline: none;
        position: fixed;
        bottom: 0px;
        border-radius: 40px 40px 0px 0px;
        overflow-y: scroll;
    }

    .header-body {
        background-color: white;
        width: 100%;
    }

    .btn_menu i {
        font-size: 26px;
        color: #F88C3F;
    }


    #ConteudoGeral div ul {
        list-style: none;
    }

    #ConteudoGeral .row .col-2 {
        padding-top: 0.2em;
        border-radius: 146px;
    }

    #ConteudoGeral .row .col-2 i {
        font-size: 2em;
        padding-top: 0.2em;
    }

    #ConteudoGeral .row {
        background: #95B7D6;
        padding: 0.2em;
        border-radius: 16px;
        margin-bottom: 6px;
        font-weight: 400;

    }

    .saldo_m {
        color: #0A3147;
        font-weight: 700;
        font-size: 64px;
    }

    .saldo_d {
        color: #4F4C4C;
        font-weight: 700;
        font-size: 40px;
    }

    .back_m {
        background-color: #95B7D6;
        padding: 0.5em;
        border-radius: 10px;
    }

    .back_di {
        background-color: #ff000033;
        padding: 0.5em;
        border-radius: 10px;
    }

    .back_nd {
        background-color: #ffcc0052;
        padding: 0.5em;
        border-radius: 10px;
    }

    .modal center .img-modal {
        width: 160px;
    }

    .modal div {
        width: 100%;
        margin-bottom: 1%;
    }

    .modal .modal-close:hover {
        background-color: white;
        color: #0A3147;
        border: 2px solid #0A3147;

    }

    .modal .modal-close {
        text-align: center;
        font-weight: bold;
        color: white;
        background: #0A3147;
        padding: 10px;
        margin-bottom: 5%;
        text-align: center;
        margin-top: 3%;
        width: 80%;
        border-radius: 10px;
        margin-left: 10%;

    }

    .modal .text-modal {
        text-align: center;
    }


    /*//////////////////////////////////////////////////////////////////
[ RESTYLE TAG ]*/

    * {
        margin: 0px;
        padding: 0px;
        box-sizing: border-box;
    }

    body,
    html {
        height: 100%;
        font-family: Poppins-Regular, sans-serif;
    }

    button {
        outline: none !important;
        border: none;
        background: transparent;
    }

    button:hover {
        cursor: pointer;
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

    #bell {
        font-size: 2em;
        padding-top: 0.2em;

    }

    #bell sub {
        font-size: 16px;
        color: red;
        font-weight: 700;
    }

    .header .col-12 .col-8 {
        padding-top: 0.6em;
    }

    .header .col-12 .col-2:nth-child(3) {
        padding-top: 0.6em;
    }

    .painel_img {
        padding: 0.3em;
        margin-top: 2%;
        background: white;
        width: 116px;
        border-radius: 15px;
    }

    #fotoPerfil {

        background-position: center;
        background-repeat: no-repeat;
        background-color: #0590CB;
        border-radius: 5px;
        background-size: cover;
        background-position-x: 40%;
        width: 100px;
        height: 100px;
    }

    .perfil_estudante {
        padding: 1em;
    }


    .text_perfil {
        font-size: 18px;
        font-weight: 500;
        color: #0590CB;
    }

    .text_perfil_data {
        font-size: 1.1em;
        border-bottom: 1px solid #9999993b;
        width: 100%;
        padding: 0.2em;
        margin-bottom: 1%;

    }

    .grupoBtn {
        justify-content: center;
        display: inline-block;
    }

    .grupoBtn button {

        padding: 10px 20px;
        background-color: #666666;
        width: 75.89px;
        height: 65px;
        left: 20px;
        top: 212px;
        /* background: #F88C3F; */
        border-radius: 16px;
    }


    .accordion-body li {
        list-style: none;
        font-weight: 400;
        border-top: 1px solid #999;
        padding-bottom: 0.4em;
        padding-top: 0.4em;
    }

    #lective_year {
        background-color: #95B7D6 !important;
        width: 100%;
        margin-top: 5%;
    }

    #TItulo {
        color: white !important;
        text-align: center;
        font-weight: bold;
        margin-top: 2%;
        margin-bottom: 2%;
    }

    #Titulo {
        color: #0590CB;
        font-size: 32px;
        font-weight: bold;

    }

    .titulo_e {
        color: #0590CB;
        font-size: 32px;
        font-weight: bold;

    }



    .btn_perfil_user {

        background-color: #0590CB;
        padding: 10px 30px;
        width: 100%;

    }


    /*                        Detalhes dos recibos                          */

    .card-detail {

        padding: 0px;
        padding-bottom: var(--bs-card-spacer-y) var(--bs-card-spacer-x);
    }

    .card-detail .badge {
        margin-bottom: 0px;
        float: right;
        border-radius: ;
        border-radius: 0px var(--bs-card-border-radius) 0px var(--bs-card-border-radius);
    }

    .card-detail .badge span {
        margin-bottom: 0px;
    }

    .card-detail p {
        margin-top: 0.1em;
        margin-bottom: 0.3em;
        margin-left: 0.7em;
        margin-right: 0.7em;
    }

    .card-detail a {
        margin-bottom: 0.5em;
        margin-top: 0.5em;
    }

    /*                  Notificações                     */

    .item-notificacao {
        padding: 1em;
        border-radius: 5px;
    }

    .item-notificacao div p {
        padding: 0.4em;
    }

    .item-notificacao div h5 {
        
        padding-bottom: 0.4em;
        border-bottom: 1px solid #ffffff63;
        width: 100%;
    }

    .sms-notification{
        padding: 0.3em;
        padding-top: 0.5em;
    }




    @media (min-width: 500px) {

        .section_main,
        .dispose_fade {
            display: none;
        }

        body {
            width: 100%;
            background-color: white;
        }

        .section_painel_erro {
            display: block !important;
        }

        .section_painel_erro center img {
            width: 500px;
        }

    }
</style>


<section class="section_painel_erro animate__animated animate__bounceInLeft" style="display: none;">

    <center><img src="{{ asset('img/mobile/img/ForLEARN_06@3x.png') }}" style="margin-top:15px;padding:3px;"><br><img
            src="https://codenex.in/wp-content/uploads/2019/01/appdevelopment.png" alt="">
        <h1>Está versão está disponível apenas para dispositivos móveis </h1>
    </center>
</section>
