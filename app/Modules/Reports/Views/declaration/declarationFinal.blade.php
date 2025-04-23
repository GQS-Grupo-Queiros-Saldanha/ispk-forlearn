@extends('layouts.print')
@section('content')

<link href="http://fonts.cdnfonts.com/css/calibri-light" rel="stylesheet">

    <style>

       @import url('https://fonts.cdnfonts.com/css/times-new-roman');


        body{
         font-family: 'Times New Roman';
        }

         html, body {
            padding:0;
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;
        }

        .form-group, .card, label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

  .h1-title {

            padding: 0;
            margin-bottom: 0;
            font-size:4.3em;
   
   
        
           
        }
        .h1-title_Com{
            
            padding: 0;
            margin-bottom: 0;
            font-size:4.3em;
           
          
             
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

        .div-top {
            height:99px;
            position: relative;
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://forlearn.ao/storage/attachment/{{$institution->logotipo}}');
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 10%;
        }

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

        input, textarea, select {
            display: none;
        }

        .td-fotografia {
            background-size: cover;
            padding-left: 10px !important;
            padding-right: 10px !important;
            width:85px;
            height:100%;
            margin-bottom: 5px;

            background-position:50%;
            margin-right:8px;
        }
        .mediaClass td{
            border: 1px solid #fff;


        }
        .watermark1{
            margin-top: 1200px;
        } 
        p{
         /*margin-top:50px;*/
        @if($config->tamanho_fonte!="")
            font-size:{{$config->tamanho_fonte}}pt;
        @else
            font-size:1.5rem;
        @endif
                margin-left: 120px !important;
                margin-right: 120px !important;
         color:black;
         text-align:justify;
     }
      .dados_pessoais{ margin-bottom:-5;}

        .pl-1 {
            padding-left: 1rem !important;
        }
          table     { page-break-inside:auto }
               tr    { page-break-inside:avoid; page-break-after:auto }
               thead { display:table-header-group }
               tfoot { display:table-footer-group }
               @media screen and (min-height: 08px) {
                    body {      
                       display: none!important;   
                    }   
                    }     
    </style> 
    <main>
        
       
            @include('Reports::declaration.cabecalho.cabecalhoDeclaration')
       
            <!-- aqui termina o cabeçalho do pdf -->


            <style>
                .table_te{
                margin-left: 120px !important;
                margin-right: 120px !important;
                background-color: #F5F3F3; !important ;text-align:right;margin-bottom: 6px; font-size:font-size:{{$config->tamanho_fonte}}pt;}
                .cor_linha{background-color:#999;color:#000;}
                .table_te th{  border: 1px solid rgb(36, 35, 35)!important;padding: 4px; !important; text-align:center;}
                .table_te td{background-color:#F5F3F3;  border: 1px solid rgb(36, 35, 35)!important; font-size:14pt;}
                .tabble_te thead{}
            </style>

        <div class="">
                        
                        @php
                            $efeito == '' ? ' ' : $efeito;
                        @endphp
                        @php
                        if (isset($finalDisciplineGrade[0]->lective_year)) {
                                                                        
                        $ano_final = $finalDisciplineGrade[0]->lective_year;

                        switch($ano_final){
                            case '20/21':
                            $ano_final ="2020/2021";
                            break;
                        case '21/22':
                            $ano_final ="2021/2022";
                            break;
                        case '22/23':
                            $ano_final ="2022/2023";
                            break;
                        case '23/24':
                            $ano_final ="2023/2024";
                            break;
                        case '24/25':
                            $ano_final ="2024/2025";
                            break;
                        }
                        } else {
                            $ano_final = "N/D";
                        }
                        
                        if (isset($lectivo->ano)) {
                                                                        
                        $ano_lectivo = $lectivo->ano;

                        switch($ano_lectivo){
                            case '20/21':
                            $ano_lectivo ="2020/2021";
                            break;
                        case '21/22':
                            $ano_lectivo ="2021/2022";
                            break;
                        case '22/23':
                            $ano_lectivo ="2022/2023";
                            break;
                        case '23/24':
                            $ano_lectivo ="2023/2024";
                            break;
                        case '24/25':
                            $ano_lectivo ="2024/2025";
                            break;
                        }
                        } else {
                            $ano_lectivo = "N/A";
                        }
                    @endphp
             

                   <p style="text-indent: 60px;"> 
                   <b> Prof. Doutor {{ $direitor->value == '' ? $direitor->name : $direitor->value }}</b>,<b> Director Geral do {{ $institution->nome }}</b>, declara para
                   @if($efeito=="Diversos")efeitos <b>{{$efeito}}</b>@else efeito de <b>{{$efeito}}</b>@endif, que <b style="color:red;">
                        {{ $studentInfo->name }}</b>, filho(a) de {{ $studentInfo->dad }} e de
                    {{ $studentInfo->mam }}, Natural de {{str_replace('Município de ', '',$studentInfo->municipio)}}, Província de {{ $studentInfo->province }}, Nascido(a) aos
                    {{ $nascimento }}, Portador(a) do B.I nº {{ $studentInfo->bi }}, passado pelo Arquivo de Identificação de {{ $studentInfo->province }}, aos
                    {{ \Carbon\Carbon::parse($studentInfo->emitido)->format('d/m/Y') }}, 
                    Concluiu o Plano Curricular do curso, no ano académico {{ $ano_lectivo }} e defendeu o Trabalho de Fim de curso no ano lectivo
                    de {{$ano_final}}, no
                    curso de Licenciatura em <b>{{ $studentInfo->course }}</b>
                    (<b>Aprovado pelo Decreto Executivo nº 195/16</b>), com o Processo nº
                    <b>{{ $studentInfo->number }},</b> tendo obtido as seguintes classificações:

                </p>
              
            <!-- personalName -->
            <div class="row">

                <div class="col-12">


             
                    @foreach ($allDiscipline as $year => $disciplines)
                        {{-- <div style="height: 48px !important;"></div> --}}
                       
                        <table class="table_te" style="width: 76%!important">
                            <thead>
                                <tr>
                                    <th style="text-align: center; font-size:16pt;background-color:white;"
                                        colspan="3"><b> {{$year}}.º ANO<b></th>
                                </tr>
                                <tr  style="font-size:16pt;font-weight: 900;">
                                    <th style="width:65%"> <b>UNIDADES CURRICULARES</b></th>
                                    <th style="width:10%"><b>HORAS</b></th>
                                    <th><b>CLASSIFICAÇÃO</b></th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach ($disciplines as $discipline)
                                    <tr>
                                        <td style="text-align: left;text-indent: 5px;">{{ $discipline->disciplina }}</td>
                                        @foreach ($cargaHoraria as $carga)
                                            @if ($discipline->discipline_id == $carga->id_disciplina)
                                                <td style="text-align: center;background-color: #F9F2F4;">
                                                    {{ $carga->hora }}</td>
                                            @endif
                                        @endforeach
                                        @foreach ($grades as $nota)
                                            @if ($discipline->discipline_id == $nota->discipline_id)
                                                <td style="text-align: center;background-color: #F9F2F4;">
                                                    {{ round($nota->grade) }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>  

                        {{-- <div style="height: 5px !important;"></div> --}}
                    </table>
                    <br>
                    
                    @endforeach

                   
                    <table class="table_te" style="width: 76%;background-color: white!important ;">
                        <thead style="opacity:0!important;height: 1px!important;">
                            <tr  style="font-size:16pt;font-weight: 900;height: 1px!important;opacity: 0!important;background-color: white!important;">
                                <th style="width:75%;height: 1px!important;opacity: 0!important;border:none!important"> <b>UNIDADES CURRICULARES</b></th>
                                <th style="width:10%;height: 1px!important;opacity: 0!important;border:none!important"><b>CLASSIFICAÇÃO</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr  style="font-size:16pt;font-weight: 900;">
                               
                                <td style="padding-right:30px"> <b>MÉDIA DO CURSO</b></td>
                                <td style="text-align: center;" ><b>{{round($media)}}</b></td>
                            </tr>
                            <tr  style="font-size:16pt;font-weight: 900;">
                                
                                <td style="padding-right:30px"> <b>MÉDIA DO TRABALHO DE FIM DE CURSO</b></td>
                                
                                    @if (isset($finalDisciplineGrade[0]->grade))
                                <td style="text-align: center;" >{{round($finalDisciplineGrade[0]->grade)}}</td>
                                @else
                                <td style="text-align: center;" >0</td>
                                @endif
                                
                            </tr>
                            <tr></tr>
                            <tr></tr>
                            <tr  style="font-size:16pt;font-weight: 900;">
                                
                                <td style="padding-right:30px"> <b>MÉDIA FINAL</b></td>
                                
                                    @if (isset($finalDisciplineGrade[0]->grade))
                                <td style="text-align: center;" >{{(round($finalDisciplineGrade[0]->grade+$media)/2)}}</td>
                                @else
                                <td style="text-align: center;" >0</td> 
                                @endif
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



                        <br>

                        <p>

                        Por ser verdade e ter sido solicitado, passou-se a presente declaração que será
                            assinada e autenticada com o carimbo a óleo e o selo branco em uso neste Instituto.

                        </p>
                 

                        <p style="17pt;text-align:center;">
                            <b>{{ $institution->nome }}, Benguela {{ $dataActual }}</b>
                            <!--Benguela,{{ Carbon\Carbon::now()->format('d  M  Y') }}-->
                        </p>
                        
               


                        <div style="width: 500px;">
                            <b>
                                <p style="font-size:16pt;text-align: center;margin-left: 30px;">
                                    A Chefe da Secretaria
                                </p>
                                <p style="font-size:16pt;text-align: center;margin-left: 30px;">
                                    ________________________
                                    <br>
                                    @if (isset($secretario->value))
                                        {{ $secretario->value }}
                                    @endif

                                </p>
                            </b>

                        </div>
                        <div class="float-right">
                            <b>
                                <p style="font-size:16pt;text-align: center;">
                                    DIRECTOR GERAL
                                </p>
                                <p style="font-size:16pt;text-align: center;">
                                    
                                    <br>

                                    
                                    <strong style="text-transform: uppercase;">
                                        @if (isset($direitor->value))
                                        {{ $direitor->value }}
                                        @endif, 
                                    </strong>
                                   
                                    PhD.

                                </p>

                            </b>

                        </div>
         </div>
         <div class="watermark">
                                 
        </div>
        <br>
        <div class="watermark watermark1">  
                                  
        </div>
    </main>

@endsection

<script>
</script>
