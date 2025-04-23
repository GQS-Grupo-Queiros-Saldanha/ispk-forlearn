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
            background-color:#00ff8b93 !important;
        }

        .bg-orange {
            background-color: #f39c12 !important;
        }

        .bg-red {
            background-color: #d9534f !important;
        }

        .bg-mat {
            background-color: #ff7a20;
        }

        .text-mat {
            color: #ff7a20;
        }

        .bg-grey {
            background-color: #8492a7;
        }

        .bg-rb {
            background-color: #281f5f;
        }

        .text-rb {
            color: #281f5f;
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

        .cl-b {
            padding-right: 0.3rem;
        }

        .cl-b span {
            float: right;
        }
        .bg0 {
        background-color: #2f5496 !important;
        color: white;
    }



        .bg1 {
            background-color: #8eaadb !important;
        }

        .bg2 {
            background-color: #d9e2f3 !important;
        }

        .bg3 {
            background-color: #fbe4d5 !important;
        }

        .bg4 {
            background-color: #f4b083 !important;
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
                                                    <div class="row" style="opacity: 0;">
                                                        <div class="col-8" style="padding:0px;">Pendentes</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-t ">
                                                                @if (isset($articles['count']['pending']))
                                                                    {{ $articles['count']['pending'] }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <a href="/payments/requests" target="_blank" class="card-box-footer"
                                                            style="padding:0px">Ver mais <i
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
                                    {{-- <div class="card-box bg-rb" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Candidaturas
                                                        
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Novas</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-rb">
                                                                @if (is_object($percurso))
                                                                    @php
                                                                        $notas_novas = 0;

                                                                        foreach ($percurso as $index => $item) {
                                                                            if ($index != null) {
                                                                                foreach ($item as $indexNotas => $itemNotas) {
                                                                                    if ($itemNotas->nota_anluno != null) {
                                                                                        ++$notas_novas;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        echo $notas_novas;
                                                                    @endphp
                                                                @else
                                                                    0
                                                                @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <a href="/candidaturas" target="_blank" class="card-box-footer"
                                                            style="padding:0px">Ver mais <i
                                                                class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-a" aria-hidden="true"></i>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="col-lg-12 col-sm-3">
                                    {{-- <div class="card-box bg-mat" style="padding: 0px">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-6">

                                                    <p class="">Matrículas
                                                        
                                                    </p>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col-8" style="padding:0px;">Confirmadas</div>
                                                        <div class="col-4 cl-b">
                                                            <span class="badge bg-mat">
                                                                @if (is_object($matriculation))
                                                                    {{ count($matriculation) }}
                                                                @else
                                                                    0
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <a href="/users/matriculations" target="_blank"
                                                            class="card-box-footer" style="padding:0px">Ver mais <i
                                                                class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-h" aria-hidden="true"></i>
                                        </div>
                                    </div> --}}
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
                                            @include('Cms::initial.components.tesouraria_staff')
                                            {{-- <div class="accordion-item">
                                               
                                                <div id="collapseFour" class="accordion-collapse collapse show"
                                                    aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                    </div>
                                                </div>
                                            </div> --}}
                                            {{-- <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                        aria-expanded="false" aria-controls="collapseTwo">
                                                        <h5 class="text-rb"><i class="fa-solid fa-list-check"></i>
                                                            CANDIDATURAS
                                                        </h5>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        @include('Cms::initial.components.candidates')
                                                    </div>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                        aria-expanded="false" aria-controls="collapseThree">
                                                        <h5 class="text-mat"><i class="fa-solid fa-graduation-cap"></i>
                                                            Matrículas
                                                        </h5>

                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse"
                                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        @include('Cms::initial.components.matriculation')
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <br>
                                            <div class="accordion-item mt-3">
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
