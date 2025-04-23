<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script src="https://kit.fontawesome.com/13b512e6f8.js" crossorigin="anonymous"></script>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #0A3147;
        background-image: url(icon.png);
        background-position: center;
        background-size: auto;
        background-repeat: no-repeat;
        background-attachment: fixed;
        margin: 0px;
        overflow: visible;

        text-align: center;
        font-family: sans-serif;
    }

    .header {
        background-color: white;
        width: 100%;
        height: 65px;
        position: fixed;
        top: 0px;
        color: #0A3147;
        box-shadow: 0 0 4px rgba(0, 0, 0, .14), 0 4px 8px rgba(0, 0, 0, .28);
        text-align: left;
        letter-spacing: 0.2px;
        padding: 5px;
    }


    #studant_name {
        color: #0590CB;
        font-weight: 700;
        font-size: 18px;
        vertical-align: sub !important;

    }

    #studant_curso {
        color: #A8A8A8;
        vertical-align: top !important;


        font-weight: 700;
        font-size: 13px;

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

    #fotoPerfil {
        background-position: center;
        background-repeat: no-repeat;
        background-color: #0590CB;
        background-size: cover; 
        background-position-x: 40%;
    }


    #menu_list {
        background-color: transparent;
        list-style: none !important;
        display: inline;
        text-decoration: none !important;
    }

    #menu_list div li {
        color: white;
        display: block;
        list-style: none !important;
        background-color: white;
        border: 1px solid white;
        height: 150px !important;
        /* border: 5px solid #0A3147; */
        margin-bottom: 10px;
        border-radius: 20px;
        color: #0A3147;
        font-weight: bold;

    }


    .new-li {
        background: url("{{ asset('img/mobile/img/undraw_Bibliophile_re_xarc 1.png') }}") center;
        background-repeat: no-repeat;

    }

    .btn-exames {
        background: url("{{ asset('img/mobile/img/undraw_Sharing_articles_re_jnkp 1.png') }}") center;
        background-repeat: no-repeat;

    }

    .btn-avaliacao {
        background: url("{{ asset('img/mobile/img/undraw_exams_g4ow 1.png') }}") center;
        background-repeat: no-repeat;

    }

    .btn-tesouraria {
        background: url("{{ asset('img/mobile/img/undraw_Investing_re_bov7_1.png') }}") center;
        background-repeat: no-repeat;

    }

    .btn-eventos {
        background: url("{{ asset('img/mobile/img/undraw_Events_re_98ue 1.png') }}") center;
        background-repeat: no-repeat;

    }

    .btn-biblioteca {
        background: url("{{ asset('img/mobile/img/undraw_Bibliophile_re_xarc 1.png') }}") center;
        background-repeat: no-repeat;
        background-position-y: 0px;
    }

    .btn-sumarios {
        background: url("{{ asset('img/mobile/img/undraw_chore_list_re_2lq8 1.png') }}") center;
        background-repeat: no-repeat;

    }


    #menu_list li div {
        height: 130px;
    }

    .title-Menu {
        color: white;
        margin-top: 5px;
        margin-bottom: 5px;
    }



    @keyframes animar {
        0% {
            transform: scale(0.9, 0.9);
        }

        100% {
            transform: scale(1, 1);

        }

    }

    @media (min-width: 500px) {
        body{
            display: none;
        }
    }

    
.modal center .img-modal{
    width: 160px; 
}
.modal div{
    width: 100%; 
    margin-bottom: 1%;
}
 
.modal .modal-close:hover{
    background-color: white;
    color: #0A3147;
    border: 2px solid #0A3147; 

}

.modal .modal-close{
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

.modal .text-modal{
    text-align: center;
}

</style>

@include('Mobile::modal.modal')


<script>
    
</script>