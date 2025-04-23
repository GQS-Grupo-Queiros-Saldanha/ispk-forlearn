<title>
    Painel inicial | forLEARN® by GQS</title>
@extends('layouts.backoffice_new')

<head>
    <!-- CSS do Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JS do Bootstrap 5 (início do corpo do documento) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
@section('styles')
    @parent
@endsection
@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <style>
        .panel-initial {
            height: 50vh;
            display: grid;
            align-content: center;
            justify-content: center;
        }

        .logo-forlearn {
            width: 50vw;
        }

        .greet {
            margin-left: 170px;
            font-size: 28px;
            padding: 1px;
            font-family: 'calibri' !important;
        }

        .as {
            color: #ec7614;
        }

        .accordion-collapse {
            max-height: 50vh;
            overflow: auto;
        }

        .accordion-collapse::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            background-color: #F5F5F5;
        }

        .accordion-collapse::-webkit-scrollbar {
            width: 2px;
            background-color: #F5F5F5;

        }

        .accordion-collapse::-webkit-scrollbar-thumb {
            background-color: #000000;
            border: 2px solid #555555;
        }

        h5 {
            font-weight: bold;
            margin-left: 10px;
            font-size: 20px;
            font-weight: 600;
        }

        .card-box {
            position: relative;
            color: #fff;
            padding: 20px 10px 40px;
            margin: 20px 0px;
            border-radius: 10px;
        }

        .card-box:hover {
            text-decoration: none;
            color: #f1f1f1;
        }

        .card-box:hover .icon i {
            font-size: 100px;
            transition: 1s;
            -webkit-transition: 1s;
        }

        .card-box .inner {
            padding: 5px 10px 0 10px;
        }

        .card-box h3 {
            font-size: 27px;
            font-weight: bold;
            margin: 0 0 8px 0;
            white-space: nowrap;
            padding: 0;
            text-align: left;
        }

        .card-box p {
            font-size: 15px;
            margin: 0px;
        }

        .card-box .icon {
            position: absolute;
            top: auto;
            bottom: 5px;
            right: 5px;
            z-index: 0;
            font-size: 33px;
            color: rgba(0, 0, 0, 0.15);
        }

        .card-box .card-box-footer {
            
           
            text-align: left;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(0, 0, 0, 0.1);
            width: 100%;
            text-decoration: none;
            font-size: 16px !important;
            font-size: 12px;
        }

        .card-box:hover .card-box-footer {
            background: rgba(0, 0, 0, 0.3);
        }

        .bg-blue {
            background-color: #00c0ef !important;
        }

        .bg-green {
            background-color: #00a65a !important;
        }

        .bg-orange {
            background-color: #f39c12 !important;
        }

        .bg-red {
            background-color: #d9534f !important;
        }

        .bg-violtet {
            background-color: #4000ff;
        }

        .bg-grey {
            background-color: #8492a7;
        }

        .bg-rb {
            background-color: #281f5f;
        }

        .bg-v {
            background-color: #743a8f;
        }

        .card-box .inner p {
            font-size: 20px;
        }

        .bg-t {
            background-color: #024b29;
        }

        .bg-p {
            background-color: #0086a7;
            border-color: #0086a7;
        }

        .bg-a {
            background-color: #7d0000;
            border-color: #7d4b00;
        }

        .bg-c {
            background-color: #7d4b00;
            border-color: #7d0000;
        }

        table td,
        table th {
            font-size: 14px !important;
        }

        .cl-b{
            padding-right: 0.3rem; 
        }
        .cl-b span{
            float: right;
        }
        .popup-forlearn{
            width: 47em!important;
        }
        .popup-forlearn-image .swal2-image{
                margin-top: 0px !important;
                margin-bottom: 0px !important;  
                width: 20em!important;
        }
        .popup-forlearn1{
            width: 55em!important;
        }
        
        .popup-forlearn .swal2-html-container{
            margin-top: 0px !important;
        }
        .boletim_text {
          font-size: 12px !important;
          font-weight: normal !important;
        } 
       .btn-pdf-boletim{
           display:none;
       }
    </style>



    <br>
    <div class="content-panel" style="padding:0">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-12">
                        <h1> Painel Inicial</h1>
                        <div class="row">
                            <div class="col-md-3" style="1px 0px 14px 6px #d2d2d2">
                                <div class="col-lg-12 col-sm-3">
                                    <div class="card-box bg-success" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Tesouraria
                                                        {{-- {{ count($notification['notification']) }} --}}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Pendentes</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-t ">
                                                                @if(isset($articles['count']['pending']))
                                                                    {{ $articles['count']['pending'] }}
                                                                @endif
                                                            </span>    
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                      <a href="/payments/requests" target="_blank"
                                                        class="card-box-footer" style="padding:0px">Ver mais <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-t" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-3">
                                    <div class="card-box bg-orange" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Avaliações
                                                        {{-- {{ count($notification['notification']) }} --}}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Novas</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-a">
                                                                @if(is_object($percurso))
                                                                    @php 
                                                                        $notas_novas = 0;
                                                                   
                                                                        foreach ($percurso as $index => $item){
                                                                            if($index!=null){
                                                                            
                                                                                foreach($item as $indexNotas => $itemNotas){
                                                                                    if ($itemNotas->nota_anluno != null) {
                                                                                        ++$notas_novas;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        echo $notas_novas;
                                                                     @endphp
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                      <a href="avaliations/discipline_boletimNotas" target="_blank"
                                                        class="card-box-footer" style="padding:0px">Ver mais <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-a" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                          
                                <div class="col-lg-12 col-sm-3">
                                    <div class="card-box bg-red" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Horários</p>
                                                       
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Novas</div>
                                                    </div>
                                                    <div class="row">
                                                      <a href="{{ route('schedules.index') }}" target="_blank"
                                                        class="card-box-footer" style="padding:0px">Ver mais <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-d" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                    
                                <div class="col-lg-12 col-sm-3">
                                    <div class="card-box bg-blue" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Notificações
                                                        {{-- {{ count($notification['notification']) }} --}}
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Novas</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-p">{{ $notification['count'] }}</span>    
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                      <a href="/central-notification" target="_blank"
                                                        class="card-box-footer" style="padding:0px">Ver mais <i
                                                            class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-d" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            

                            </div>
                            <div class="col-md-9 ">
                                <div class="accordion" id="accordionExample" style="margin-top:1%;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingFour">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                        aria-expanded="false" aria-controls="collapseFour">
                                                        <h5 class="text-success"><i class="fa fa-money-bill"></i>
                                                            TESOURARIA
                                                        </h5>

                                                    </button>
                                                </h2>
                                                <div id="collapseFour" class="accordion-collapse collapse"
                                                    aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        @include('Cms::initial.components.tesouraria')
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                        aria-expanded="false" aria-controls="collapseTwo">
                                                        <h5 class="text-warning"><i class="fa-solid fa-list-check"></i>
                                                            AVALIAÇÕES
                                                        </h5>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        @include('Cms::initial.components.boletim')
                                                    </div>
                                                </div>
                                            </div>
                                     
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                        aria-expanded="false" aria-controls="collapseThree">
                                                        <h5 class="text-danger"><i class="fa fa-calendar-alt"></i>
                                                            Horários
                                                        </h5>

                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse"
                                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                    @if(isset($schedule_id))
                                                    @include('GA::schedules.partials.schedule_student')
                                                    @else
                                                    <div class="alert alert-warning text-dark font-bold">Horário indisponível!</div>
                                                    @endif
                                                    </div>
                                                </div>
                                            </div>
                                           
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">

                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        <h5 class="text-info"><i class="fa fa-bell"></i> NOTIFICAÇÕES
                                                        </h5>
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body" id="table_notification">
                                                        @include('Cms::initial.components.notification')
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>







    </div>






    </div>
@endsection
@section('scripts')
    @parent

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    
    @if(isset($articles['dividas']['pending']) && $articles['dividas']['pending'] > 0)
    
    <script>
         Swal.fire({
            title: "",
            html: "Caro(a) estudante, <as class='as'><b>{{auth()->user()->name}}</b></as>, a <b style='color:#249fbd;'>forLEARN®</b> informa-te da necessidade de regularizares as propinas na tesouraria da instituição.<br><br>"
            +"O <b style='color:#0485ff;'>{{$institution->abrev}}</b> agradece.",
            imageUrl: "https://{{$logotipo}}",
            showCancelButton: false,
            confirmButtonColor: "#3085d6", 
            cancelButtonColor: "#d33",
            confirmButtonText: "Tomei conhecimento!",
            customClass: {
                    popup: 'popup-forlearn popup-forlearn-image'
                    }
            }).then((result) => { 
        if (result.isConfirmed) {
            Swal.fire({
                          title: "",
                          html: "Caro(a) estudante, <b class='as'> {{auth()->user()->name}}</b>, solicitamos que entre em contacto com o seu coordenador de curso caso encontre alguma dúvida e/ou desconformidade nas suas avaliações.<br><br>Obrigado.",
                          imageUrl: "https://{{$logotipo}}",
                          showCancelButton: false,
                          confirmButtonColor: "#3085d6",
                          cancelButtonColor: "#d33",
                          confirmButtonText: "Tomei conhecimento.",
                           customClass: {
                            popup: 'popup-forlearn popup-forlearn-image'
                            }
                            }).then((result) => { 
                          if (result.isConfirmed) {
                               
                
                          }
                        });   
            }
        });
   
    </script>
    
    @else
    
         <script>
             Swal.fire({
                          title: "",
                          html: "Caro(a) estudante, <b class='as'> {{auth()->user()->name}}</b>, solicitamos que entre em contacto com o seu coordenador de curso caso encontre alguma dúvida e/ou desconformidade nas suas avaliações.<br><br>Obrigado.",
                          imageUrl: "https://{{$logotipo}}",
                          showCancelButton: false,
                          confirmButtonColor: "#3085d6",
                          cancelButtonColor: "#d33",
                          confirmButtonText: "Tomei conhecimento.",
                           customClass: {
                            popup: 'popup-forlearn popup-forlearn-image'
                            }
                            })
        
        </script>
    
    @endif
    
    {{--NÃO APAGAR ESTE CÓDIGO --}}
    
    <script>
       @if($semestre == 1)
    $('#tabela_pauta_student2').remove();                              
@elseif($semestre == 2)
$('#tabela_pauta_student1').remove();
@else
@endif
    </script>

@endsection
