<!DOCTYPE html>
<html>

<head>
    <title>forLEARN APP</title>
    <meta name="viewport" content="initial-scale=1 viewport-fit=cover" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.2/assets/css/docs.css" rel="stylesheet">
    <style>

    </style>

</head>

<body>


    <div class="header">
        <div class="col-12">

            <div class="row">
                
                <div class="col-2" id="col_perfil">

                    {{-- <img src="" alt="imagem_de_perfil" 
                    class="perfil"> --}}
                    <div style="width:100%; heigth:120px; border-radius:50px;" id="fotoPerfil" >

                    </div>

                </div>
                <div class="col-8">
                    <small id="studant_name" class="perfil"></small><br>
                    <small id="studant_curso" class="perfil"></small>

                </div>

                <div class="col-2" >
                    <i class="fas fa-bell text-red float-right animate__animated   animate__swing " id="bell" >
                   
                    </i>
                </div>

            </div>
        
        
        
            
        </div>

    </div>

    <br>
    <br>
    <br>
 
<center>
    <div class="col-11">


       
            <h2 class="title-Menu">Menu</h2>
       
            <div class="alert alert-primary animate__animated animate__bounceInUp " role="alert" style="display: none">
                Este menu estará disponível brevemente 😉
              </div>
           
        
        <ul id="menu_list" class="col-12">
            
            
            <div class="row">
                
    

            <div class="col-6">
                <li class="btn-tesouraria" onclick="navegate(this)" data-route="{{ route('propina-app') }}" >
                    <div></div>
                    Tesouraria
                </li>

                {{-- <li class="btn-avaliacao menu-indisponivel" onclick="navegate(this)" data-route="{{ route('avaliacao-app') }}" > --}}
                {{-- <li class="btn-avaliacao menu-indisponivel">
                    <div></div>
                    Avaliação
                </li>
                <li class="btn-exames menu-indisponivel" >
                    <div></div>
                    Exames
                </li> --}}
            </div>
            {{-- <div class="col-6">
                <li class="btn-eventos menu-indisponivel ">
                    <div></div>
                    Eventos
                </li>
                <li class="btn-sumarios menu-indisponivel">
                    <div></div>
                    Sumários
                </li>
                <li class="btn-biblioteca menu-indisponivel">
                    <div></div>
                    <span>Biblioteca</span>
                </li>
            </div> --}}
        </div>
   
    </ul>

    </div> </center>
    <div class="footer"></div>

</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
</html>
@include('Mobile::css.m_css');
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>

    var url = "http://{{ $url }}";
    var perfil_foto;
    $(document).ready(function() {
        if ((window.screen.availHeight < 1234) && (window.screen.availWidth <
                1234)) { //Verificar sessão sempre!
            verificar_sesstion();


        } else {
            $(location).attr("href", url);
        }

        //Verifique a sessão 
       

        function verificar_sesstion() {
            const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
            if (dados == null) {
                window.location.href = "{{ route('app.index') }}";

            } else {
                const img = "{{ asset('storage/attachment') }}/" + dados['user'].image;
                perfil_foto = img;
                $("#studant_name").text("");
                $("#studant_curso").text("");
                $("#studant_name").text(dados['user'].name);
                $("#studant_curso").text(dados['user'].curso);
                $("#fotoPerfil").css("background-image","url('"+perfil_foto+"')");
               

            }


        }

        $(".perfil").click(function() {
            window.location.href = "{{ route('perfil-app') }}";
        });
        $(".new-li").click(function() {
            $(this).animate({
                borderRadius: '10px'
            }, 2000);
        });



    })
    //navegação entre menu
    function navegate(element) {
        var url_nav = element.getAttribute("data-route");
        $(location).attr("href", url_nav);
    }

    
        height_perfil = $(".header").height();
        width_perfil = ($("#col_perfil").width());
        $("#fotoPerfil").css({height:height_perfil,width:height_perfil});
    
  

    var noty="{{$notify}}";
    if(noty>9){noty="+9"}
    $("#bell").html("<sub>"+noty+"</sub>");

    $("#bell").click(function(){
        const dados = JSON.parse(window.localStorage.getItem('forLearnApp'));
        window.location.href = "/mobile/notification/" + dados['user_secret'].user_secret;
    });

    $(".menu-indisponivel").click(function(){
        $(".alert").show();
        
        
        $(".alert").removeClass("animate__bounceInUp");
        setTimeout(function(){
            
            
            
            $(".alert").addClass("animate__bounceOutUp");
            // $(".alert").css({display:"none"});
        }, 3000);
        $(".alert").removeClass("animate__bounceOutUp");
        $(".alert").addClass("animate__bounceInUp");

    });


</script>
