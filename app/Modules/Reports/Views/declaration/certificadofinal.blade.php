@extends('layouts.print')
<title>Certificado | forLEARN</title>
@section('content')
   
    <script src="https://c.webfontfree.com/c.js?f=OldEnglishTextMT" type="text/javascript"></script>
    
    <style>
           
        body {
            font-family: 'Tinos', serif;
            /* page-break-inside:auto ; */
        }

        html,
        body {
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

       

        .h1-title_Com {

            padding: 0;
            margin-bottom: 0;
            font-size: 4.3em;
            font-weight: bold;
            margin-left: 130px;
            margin-top: 100px;

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

        /*.div-top {*/
        /*    height: 99px;*/
        /*    text-transform: uppercase;*/
        /*    position: relative;*/
            /* border-top: 1px solid #000;
                border-bottom: 1px solid #000; */
            /*margin-bottom: 15px;*/
            /*background-color: rgb(240, 240, 240);*/
            /*background-image: url('https://forlearn.ao/storage/attachment/{{ $institution->logotipo }}');*/
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
        /*    background-position: 100%;*/
        /*    background-repeat: no-repeat;*/
        /*    background-size: 10%;*/
        /*}*/

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: justify;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
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

        input,
        textarea,
        select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width: 85px;
            height: 100%;
            margin-bottom: 5px;

            background-position: 50%;
            margin-right: 8px;
        }

        .mediaClass td {
            border: 1px solid rgb(36, 35, 35) !important;
        }

        p {
            font-size: 20px;
            margin-left: 120px;
            margin-right: 120px;
            color: black;
            text-align: justify;
            line-height: 22px;
        }

        .dados_pessoais {
            margin-bottom: -5;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        table {
            page-break-inside: avoid;
            page-break-after: auto
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
        
        .bg6{
            background-color: #F5F3F3!important;
        }
        
        .institution-name {
            margin-left:95px!important;
        }
        
        
        .div-top_f {
            margin-bottom: -30px!important;
        }
    

        /* tr    { page-break-inside:avoid; page-break-after:auto } */
        /* thead { display:table-header-group } */
        /* tfoot { display:table-footer-group } */

        
    </style>
    
    <main>

        <!--@include('Reports::declaration.cabecalho.cabecalhoCertificado')-->
        @include('Reports::declaration.cabecalho.cabecalho_forLEARN')
        <!-- aqui termina o cabeçalho do pdf -->

        <style>
            @page {
                
            }

            .table_te {margin-left: 120px !important; margin-right: 120px !important; background-color: #F5F3F3 !important; width: 75%; text-align: right; margin-bottom: 10px; font-size:{{ $config->tamanho_fonte }}pt !important; }
            .cor_linha {background-color: #999; color: #000; }
            .table_te th {padding: 4px !important; text-align: center;}
            .table_te td {background-color: #F5F3F3; font-size: 12pt!important; }
            .table_te th{border: 1px solid rgb(238, 233, 233)!important; padding: 4px; !important; text-align:center;}
            .table_te td{border: 1px solid rgb(238, 233, 233)!important; font-size:12pt!important;}
            .tabble_te thead {}

            /* GERAL: Permitir que as tabelas se partam de forma natural */
            .table_te {
                page-break-inside: auto !important;
                break-inside: auto !important; /* Para navegadores modernos */
                margin-bottom: 10px !important;
            }

            /* EVITAR QUE UMA LINHA SEJA DIVIDIDA AO MEIO */
            .table_te tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Garante que o cabeçalho aparece no início da página impressa */
            .table_te thead {
                display: table-header-group !important;
            }

        </style>

        <div class="">
            
            @php
            
                $numeros = [
                    0  => 'Zero',
                    1  => 'Um',
                    2  => 'Dois',
                    3  => 'Três',
                    4  => 'Quatro',
                    5  => 'Cinco',
                    6  => 'Seis',
                    7  => 'Sete',
                    8  => 'Oito',
                    9  => 'Nove',
                    10 => 'Dez',
                    11 => 'Onze',
                    12 => 'Doze',
                    13 => 'Treze',
                    14 => 'Catorze',
                    15 => 'Quinze',
                    16 => 'Dezasseis',
                    17 => 'Dezassete',
                    18 => 'Dezoito',
                    19 => 'Dezanove',
                    20 => 'Vinte'

                ];

        
                $province = null;
                $province = matchProvince(substr($studentInfo->bi,-5,2));
                     
            @endphp

            
            <p> 
                {{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>,&nbsp;{{ $direitor->cargo ?? 'cargo' }}  <span>do</span> <b>{{ $institution->nome }}</b>, certifica
                em face dos respectivos livros que <b style="">
                    {{ $studentInfo->name }}</b>, filho(a) de {{ $studentInfo->dad }} e de
                {{ $studentInfo->mam }}, natural da Província de {{ $studentInfo->province }}, nascido(a) aos
                {{ $nascimento }}, portador(a) do B.I nº {{ $studentInfo->bi }}, passado pelo Arquivo de Identificação de {{ $province }},
                    concluiu  a Licenciatura em <b>{{ $studentInfo->course }}</b>, com a matrícula nº
                <b>{{ $studentInfo->number }},</b> tendo obtido as seguintes classificações: 

            </p>
              


            <div class="row">

                <div class="col-12 my-0">
                        @foreach ($allDiscipline as $year => $disciplines)     
                        <table class="table_te">
                            <thead>
                                <tr>
                                    <th class="bg2" style="text-align:center; font-size:15pt;">#</th>
                                    <th class="bg2" style="text-align:center; font-size:15pt;">Ano</th>
                                    <th class="bg2" style="text-align:left; font-size:15pt;text-indent:10px;"><b>DISCIPLINA</b></th>
                                    <th class="bg2" style="text-align:center; font-size:15pt;"><b>U.C</b></th>
                                    <th class="bg2" style="text-align:center; font-size:15pt;" colspan="2"><b>CLASSIFICAÇÃO</b></th>     
                                </tr>
                            </thead>
                            <tbody>

                                  @php $index=1 @endphp 
                                  @foreach ($disciplines as $discipline)
                                    <tr>
                                        <td class="bg6" style="text-align: center;background-color: #F9F2F4;">{{$index}}</td>
                                        @php $index++ @endphp
                                        <td class="bg6" style="text-align: center;background-color: #F9F2F4;">{{$year}}º</td>
                                        <td class="bg6" style="text-align: left;text-indent: 5px;">{{ $discipline->disciplina }}</td>
                                        <td class="bg6" style="text-align: center;background-color: #F9F2F4;">{{ $discipline->uc }}</td>
                                        @foreach ($grades as $nota)
                                            @if ($discipline->discipline_id == $nota->discipline_id)
                                            @php
                                                $note =  round($nota->grade)
                                            @endphp
                                            <td class="bg6" style="text-align: center;background-color: #F9F2F4;">
                                                   {{ round($nota->grade) }}</td>
                                                <td class="bg6" style="text-align: center;background-color: #F9F2F4;">
                                                    {{ $numeros[$note] }}</td>
                                            @endif 
                                        @endforeach
                                    </tr>
                            </tbody>  
                         
                        @endforeach
                        </table>
                   
                        @if($loop->iteration == 3)
                       
                        @endif
                      @endforeach

                    <br>

                </div>
            </div>
        </div>
        </div>
        
        <p style="font-size:12pt;margin-top:-10px">
            Escala de avaliação: 0-20 
        </p>

        <p style="font-size:12pt;margin-top:-10px">
           Aprovação: nota >= 10 valores 
        </p>

        <p style="font-size:14pt">
            @php $media = round($media) @endphp
            Média final do curso: <b> {{ round($media) }} ( {{$numeros[$media]}} )</b> valores.
        </p>

       
            <div>
                <p> 
                   
                    Por ser verdade e me ter sido solicitado, passa-se o presente certificado nº {{$requerimento->code ?? 'código doc'}}, liquidado no CP nº {{$recibo ?? 'recibo'}},
                    assinado e autenticado com o selo em branco em uso no <b>{{$institution->nome}}</b>.
                </p>
                <br>
             
                 <p style="font-size: 16pt !important;text-align:left;font-weight:bolder !important;margin-bottom:35px !important;margin-top:-12px">
           
                 {{ $institution->provincia }}, aos {{ $dataActual }}
                </p>
                
                <p style="font-size: 14pt !important;margin-bottom:-5px!important;">_________________________________________________________</p>
                <p style="font-size: 14pt !important;margin-top:-3px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                <p style="font-size:11pt  !important;margin-top:-20px!important;">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                <p style="font-size:11pt; !important;margin-top:-20px!important;">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>

                
            </div>            
                <div class="watermark" style="top: 1400px;"></div>
                <div class="watermark"></div>

        </div>
        </div>

    </main>
@endsection

<script></script>
