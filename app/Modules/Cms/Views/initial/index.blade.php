
<title>Painel inicial | forLEARN® by GQS</title>
@extends('layouts.backoffice_new')
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
      .panel-initial{
            height: 50vh; 
        display: grid;
        align-content: center;
        justify-content: center;
      }
      .logo-forlearn{
        width: 50vw;
      }
      .greet{
        margin-left: 170px;
        font-size: 28px;  
        padding: 1px;
        font-family: 'calibri'!important;
      }

      .as{
        color:#ec7614;
      }
      
         .popup-forlearn1 .swal2-image{
                margin-top: 0px !important;
                margin-bottom: 0px !important;  
                width: 20em!important;
        }
        
        .popup-forlearn1 .swal2-html-container{
            margin-top: 0px !important;
        }
        
        .popup-forlearn1{
            width: 55em!important;
        }
  
    </style>



    <br>
    <div class="content-panel" style="padding:0">
    
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-12">
                        <h1> Painel Inicial</h1>
                     
                            <p class="greet"> Olá,<as class="as"> {{auth()->user()->name}}</as>!</p>
                            <p class="greet" >Estamos a preparar um painel inicial, especial e dedicado, para ti!</p>
                     
                    </div>
                   
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col panel-initial">
                           <center>
                              <img class="logo-forlearn" src="{{asset('img/login/ForLEARN 03.png')}}" alt="logotipo">
                             

                            </center>
                      
                    </div>
                </div>
            </div>
        </div>



        
    </div>






    </div>

@endsection
@section('scripts')

    @parent
    
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      
      {{-- APAGAR ESTE IF DEPOIS DO DIA DOS PAIS  --}}
      @if(date('Y-m-d')=="2024-03-19")
        <script>
               Swal.fire({
                title: "",
                html: "<b style='color:#0485ff;'>Dia do Pai</b><br><br>"+
                "A <b style='color:#249fbd;'>forLEARN®</b> parabeniza todos os pais de nossa comunidade – professores, técnicos-administrativos, estudantes, pais de estudantes e colaboradores."
                +"<br>Ser pai não é só gerar vida, mas cuidar, proteger e, acima de tudo, amar sem limites.<br><br>"
                +"O <b style='color:#0485ff;'>ISPSML</b> acolhe e expressa gratidão a todos os pais, aqueles que são pais-professores,"
                +" pais-técnicos administrativos, pais-estudantes e pais-colaboradores e que estão sempre no dia a dia do <b style='color:#0485ff;'>ISPSML</b>,"
                +" dando o melhor de si, para a construção de uma sociedade mais justa"
                +" e fraterna, com educação de qualidade para todos." 
                +"<br><br> Parabéns a todos os pais.",
                imageUrl: "https://{{$logotipo}}",
                showCancelButton: false,
                confirmButtonColor: "#3085d6", 
                cancelButtonColor: "#d33",
                confirmButtonText: "Feliz dia do Pai !",
                customClass: {
                        popup: 'popup-forlearn1'
                        }
                }).then((result) => { 
                    if (result.isConfirmed) {
                }
                    
                });
        </script>
        @endif
@endsection
