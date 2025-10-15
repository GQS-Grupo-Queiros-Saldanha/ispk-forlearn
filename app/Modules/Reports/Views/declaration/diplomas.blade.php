<title>Diploma | forLEARN</title>
@extends('layouts.print')
@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Junge&display=swap" rel="stylesheet">
 <script src="https://c.webfontfree.com/c.js?f=OldEnglishTextMT" type="text/javascript"></script>

    <style>
       
        


        html
         {
            padding: 0;
           
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;

        }

        .form-group,
        .card,
        label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

        .h1-title_Com{
            
            padding: 0;
            margin-bottom: 0;
            
            font-weight: bold;
            margin-left:130px;
            margin-top:100px;
            
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }

        .img-parameter {
            max-height: 100px;
            max-width: 50px;
        }

        .table-parameter-group {
            page-break-inside: avoid;
        }

        .table-parameter-group td,
        .table-parameter-group th {
            vertical-align: unset;
        }

        .tbody-parameter-group {
            border-top: 0;
            /* border-left: 1px solid #BCBCBC;
                        border-right: 1px solid #BCBCBC; */
            /* border-bottom: 1px solid #BCBCBC; */
            padding: 0;
            margin: 0;
        }

        .thead-parameter-group {
            color: white;
            background-color: #3D3C3C;
        }

        .th-parameter-group {
            padding: 2px 5px !important;
            font-size: .625rem;
        }



        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: justify;
        }

        .td-institution-logo {
            vertical-align: middle !important;

        }

        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        p {
            /*margin-top:50px;*/
            font-size:22px!important;
            font-size: {{ $config->tamanho_fonte }}pt;
            margin-left: 80px;
            margin-right: 80px;
            color: black;
            text-align: justify;
            
        }



        .dados_pessoais {
            margin-bottom: -5;
        }
        
        .conteudo {
            font-family: "Junge", cursive;
  font-weight: 400;
  font-style: normal;
        padding-bottom:40px;
        }
        
                .h1-title {
            font-family: "Junge", cursive;
  font-weight: 400;
  font-style: normal; 
    
        }
        
         body {
           
            border: 4px solid black;
        }
        
        .b1 {
            border: 4px solid black;
            margin: 20px;
            margin-bottom:10px;
           border-radius: 30px; 
        }
        
        .b0 {
            border: 2px solid black;
            margin: 5px;
           border-radius: 30px; 
        }
  
    </style>
    <main class="b1 mgb">
        <div class="b0">
            
            

        @include('Reports::declaration.cabecalho.CabecalhoDiploma')

        <!-- aqui termina o cabeçalho do pdf -->
        <div class="b2">

            <div class="row">
                <div class="col-12 ">


            <div class="conteudo">

                <div class="row">
                    <div>


                        <p>
                            O {{ $direitor->cargo ?? 'cargo' }} do {{ $institution->nome ?? ' nome da instituição'}}, {{ $direitor->grau_academico ?? 'Grau Académico' }}
                             <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>, no uso das suas atribuições faz saber que <b>{{ $studentInfo->name }}</b>, 
                            filho(a) de {{ $studentInfo->dad }} e de {{ $studentInfo->mam }},  concluiu com aproveitamento, aos {{$data_conclusao}}
                            o curso de <b>{{ $studentInfo->course }}</b> ministrado por esta Instituição, e que lhe confere o grau de  <b>Licenciado(a)</b>.
                        </p>

                        <p style="margin-bottom: 40px">E para que conste, mandou passar o presente Diploma que outorga os direitos e prerrogativas de acordo com o referido grau
                             académico, em conformidade com a lei vigente e que vai assinado e autenticado com o selo branco em uso nesta Instituição, e registado
                             sob o número {{$n_registro}} na folha {{$codigo_diploma}} do respectivo livro.
                        </p>

                       
                        <p style="text-align: center;margin-bottom:70px;">
                            {{ $institution->provincia }}, aos {{$data_diploma}}
                        </p>


                     <div style="margin-left: 550px; padding-left:0px">
                        <p>_________________________________________________________</p>

        
                        <p style="font-size: 14pt !important;margin-top:0px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                        <p style="font-size:11pt !important;margin-top:-22px">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                        <p style="font-size:11pt !important;margin-top:-22px">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>
                     </div>
                     
                     <div class="watermark"></div>

                    </div>

                   

                </div>


            </div>
        </div>
        </div>
        </div>
        
        
        </div>
    
            
        
        </main>
@endsection

<script></script>
