@extends('layouts.print')
@section('title',__('Declaração com notas'))
@section('content')

    <style>

    


        body{
         font-family: 'Calibri Light', sans-serif;
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
    

    
    .table_te td
        {
          font-size:12pt!important;
        }
        
    .body {
        margin-top:-50px;
    }


    .div-top {
         margin-bottom:-40px!important;
    }
    
    .institution-name {
        margin-left: 90px!important;
    }

    </style>
    <main>
        
       
        @include('Reports::declaration.cabecalho.cabecalho_forLEARN')
       
            <!-- aqui termina o cabeçalho do pdf -->
            {{-- background-color:#F5F3F3; rgb(36, 35, 35)--}}

            <style>
                .table_te{
                margin-left: 120px !important;
                margin-right: 120px !important;
                text-align:right;margin-bottom: 6px; font-size:font-size:{{$config->tamanho_fonte}}pt;}
                .cor_linha{background-color:#999;color:#000;}
                .table_te th{  border: 1px solid rgb(238, 233, 233)!important;padding: 4px; !important; text-align:center;}
                .table_te td{  border: 1px solid rgb(238, 233, 233)!important; font-size:14pt;}
                .table_te thead{}
            </style>

        <div class="body">
      
                        @php
                            $efeito == '' ? ' ' : $efeito;
                            $status = $status == 0 ?"concluiu o":"a frequentar";

                            $province = null;
                            $province = matchProvince(substr($studentInfo->bi,-5,2));
                        @endphp
             

                   <p> 
                    {{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b>,{{ $direitor->cargo ?? 'cargo' }}  do <b>{{ $institution->nome }}</b>, declara para
                  os devidos efeitos, que <b>
                        {{ $studentInfo->name }}</b>, filho(a) de {{ $studentInfo->dad }} e de
                    {{ $studentInfo->mam }}, Nascido(a) aos
                    {{ $nascimento }}, Portador(a) do B.I nº {{ $studentInfo->bi }}, passado pelo Arquivo de Identificação de {{ $province }}, aos
                    {{ \Carbon\Carbon::parse($studentInfo->emitido)->format('d/m/Y') }},
                    {{$status}}
                    
                @foreach ($disciplines as $discipline)
                    
                    @foreach ($oldGrades as $year => $oldGradex)
                                                
                                           
                                                @foreach ($oldGradex as $oldGrade)
                                                    @if ($oldGrade->discipline_id == $discipline->id)
                                                        @php 
                                                        $academic_year = $oldGrade->lective_year;
                                                        break;
                                                        @endphp

                                                

                                                    @endif
                                                @endforeach
                    @endforeach
                @endforeach
                    
                    <b>{{ $ano }}º Ano</b>, no ano académico {{ $academic_year ?? 'Ano lectivo' }}, no
                    curso de Licenciatura em <b>{{ $studentInfo->course }}</b>, com a Matrícula nº
                    <b>{{ $studentInfo->number }},</b> tendo obtido as seguintes classificações: 

                </p>
              
            <!-- personalName -->






            <div class="row">
                <div class="col-12">



                           
                            <div class="">
                            <div class="">
                             @php
                                   $i=1;
                             @endphp
                            @php $flag = true; $oFlag = true; $areaGeral = 0; $contaGeral = 0;
                                $areaEspecifia = 0; $contaEspecifica = 0;
                                $areaProfissional = 0; $contaProfissional = 0;
                            @endphp
                            <table class="table_te" style="width: 76%;margin-top:-20px">
                                    <!--<tr>-->
                                    <!--    <th class="bg0 text-white" colspan="5" style="text-align: center; font-size: 15pt;text-transform: uppercase;" ><b><b>{{ $ano }}º Ano</b></b></th>-->
                                    <!--    @if ($var == 1)-->
                                    <!--        @foreach ($studyPlanEditions as $studyPlanEdition)-->
                                    <!--            <th style="text-align: left;">{{ $studyPlanEdition->lective_year}}</th>-->
                                    <!--        @endforeach-->
                                    <!--    @endif-->
                                    <!--</tr>-->
                                    <tr style="font-weight: 900!important">
                                        {{-- <th style="text-align:center;">Ano</th> --}}
                                        <th class="bg1" style="text-align:center; font-size:15pt;"><b>#</b></th>
                                        <th class="bg1" style="text-align:center; font-size:15pt;">CÓDIGO</th>
                                        <th class="bg1" style="text-align:left; font-size:15pt;text-indent:10px;"><b>DISCIPLINAS</b></th>
                                        
                                        <th class="bg1" style="text-align:center; font-size:15pt;"><b>HORAS</b></th>
                                        <th class="bg1" style="text-align:center; font-size:15pt;"><b>UC</b></th>
                                        @foreach ($oldGrades as $year => $oldGrade)
                                             @if($oldGrade!="")
                                            <th class="bg1" style="text-align:center; font-size:15pt; " colspan="{{$i}}"><b>CLASSIFICAÇÃO</b></th>
                                            @break
                                            @endif
                                        @endforeach

                                        @if ($var == 1)
                                            @foreach ($studyPlanEditions as $studyPlanEdition)
                                                  @if($studyPlanEdition!="")
                                                 <th class="bg1" style="text-align:center; font-size:15pt;" colspan="{{$i}}"><b>CLASSIFICAÇÃO</b></th>
                                                 @break
                                                 @endif

                                            @endforeach
                                        @endif
                                    </tr>
                                   
                                        @php $contaDisciplina = 0; @endphp
                                        @php $notas = 0; @endphp
                                        @php $count_notas = 0; @endphp
                                    @foreach ($disciplines as $discipline)
                                        @php
                                            $cor= $i++ % 2 === 0 ? 'cor_linha' : '';
                                         @endphp
                                        <tr class="{{$cor}}">
                                            <td  class="bg2" style="text-align: center;">{{$i-1}} </td>
                                            <td class="bg2" style="text-align: left; text-indent: 5px">{{ $discipline->code }}</td>
                                            <td class="bg2" style="text-align: left; text-indent: 5px">{{ $discipline->name}}
                                                @php $contaDisciplina++; @endphp
                                            </td>
                                          



                                                @foreach($cargaHoraria as $carga)   
                                                     @if($discipline->id===$carga->id_disciplina)
                                                        <td class="bg2" style="text-align: center;">{{ $carga->hora}}</td>
                                                     @endif
                                                 @endforeach

                                                 <td class="bg2" style="text-align: center;">{{ $discipline->uc ?? ''}}</td>

                                            @foreach ($oldGrades as $year => $oldGradex)
                                            @php $nao_tem_nota = true @endphp
                                            @php $flag = true @endphp
                                            @php $oFlag = true; @endphp
                                                @foreach ($oldGradex as $oldGrade)
                                                    @if ($oldGrade->discipline_id == $discipline->id)
                                                    @php $nao_tem_nota = false @endphp
                                                        @php $flag = false @endphp

                                                        @if ($discipline->area_id == 13)
                                                            @php
                                                                $areaGeral += $oldGrade->grade;
                                                                $contaGeral++ ;
                                                            @endphp
                                                            @elseif($discipline->area_id == 14)
                                                            @php
                                                                $areaProfissional += $oldGrade->grade;
                                                                $contaProfissional++;
                                                            @endphp
                                                            @elseif($discipline->area_id == 15)
                                                            @php
                                                                $areaEspecifia += $oldGrade->grade;
                                                                $contaEspecifica++;
                                                            @endphp
                                                        @endif
                                                        <td class="bg2" style="text-align: center;">{{ round($oldGrade->grade) }}</td>
                                                        @php $notas += $oldGrade->grade; $count_notas++; @endphp
                                                    @endif
                                            @endforeach
                                              
                                                    @if ($flag)
                                                        {{-- <td style="background-color: #F9F2F4;"></td> --}}
                                                    @endif
                                                @endforeach
                                                
                                               
                                                
                                                @if ($var == 1)
                                                @foreach ($grades as $grade)
                                                   
                                                    @php $oFlag = true @endphp
                                                    {{-- aqui falta comparar o id do pl ano de edicao de estudo, caso a disciplina acarretar negativa --}}
                                                    @if ($grade->discipline_id == $discipline->id)
                                                            @if ($discipline->area_id == 13)
                                                                @php
                                                                    $areaGeral += round($grade->percentage_mac + $grade->percentage_neen);
                                                                    $contaGeral++ ;
                                                                    @endphp
                                                                @elseif($discipline->area_id == 14)
                                                                @php
                                                                    $areaProfissional += round($grade->percentage_mac + $grade->percentage_neen);
                                                                    $contaProfissional++;
                                                                @endphp
                                                                @elseif($discipline->area_id == 15)
                                                                @php
                                                                    $areaEspecifia += round($grade->percentage_mac + $grade->percentage_neen);
                                                                    $contaEspecifica++;
                                                                @endphp
                                                            @endif
                                                            @php $oFlag = false @endphp
                                                           <td class="bg2" style="text-align: center;">{{ round($grade->percentage_mac + $grade->percentage_neen) }}</td>
                                                       @endif
                                                @endforeach
                                                @endif
                                                

                                        </tr>
                                        
                                        @endforeach
                                        @if ($status_finalist == 1) 
                                        
                                        <tr>
                                            <td class="td bg4" style="text-align: center!important;">{{(count($disciplines)+1)}}</td>
                                            <td class="td bg4" style="text-align: left;">{{$final_note[0]->display_name}}</td>
                                            <td class="td bg4"  style="text-align: center;">64</td>
                                            <td class="td bg4"  style="text-align: center;">{{round($final_note[0]->grade)}}</td>
                                        </tr>
                                        @endif;
                                        @if ($status_finalist == 2) 
                                        
                                        <tr>
                                            <td class="td bg4"  style="text-align: center!important;">{{(count($disciplines)+1)}}</td>
                                            <td class="td bg4"  style="text-align: left;"> Trabalho de Fim de Curso</td>
                                            <td class="td bg4"  style="text-align: center;">64</td>
                                            <td class="td bg4"  style="text-align: center;">F</td>
                                        </tr>
                                        @endif;
                                     <tr>
                                        <td class="td bg4"  style="text-align: center!important;" colspan="5"><b>MÉDIA</b></td>
                                        @php $average = $notas / $count_notas; @endphp
                                        <td class="td bg4"  style="text-align: center;">{{round($average)}}</td>
                                   </tr>
                       
                            </table>
                                    

                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div>
                <p style="font-size: 14pt !important;"> 
                    @if ($status_finalist == 2) 
                            Obs: O estudante ainda não terminou o curso, pois falta-lhe a apresentação e defesa do Trabalho de Fim de Curso.
                            @endif
                    Por ser verdade e me ter sido solicitada, passa-se a presente declaração nº {{$requerimento->code ?? 'código doc'}}, liquidada no CP nº {{$recibo ?? 'recibo'}},
                    assinada e autenticada com o carimbo a óleo em uso no {{$institution->abrev}}.
                </p>
                
                <!--<p style="font-size:12pt !important">Documento gerado automaticamente pela <b style="color:#243f60;margin-top:10px;font-size:inherit !important">forLEARN<sup style="font-size: 11px">®</sup></b></p>-->
               
                 <p style="font-size: 16pt !important;text-align:left;font-weight:bolder !important;margin-bottom:40px !important;margin-top:-10px">
           
                 {{ $institution->provincia }}, aos {{ $dataActual }}
                </p>
                <p>_______________________________________</p>
                          <p style="font-size: 14pt !important;margin-top:-18px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                          <p style="font-size:10pt !important;margin-top:-24px">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                          <p style="font-size:10pt !important;margin-top:-22px">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>

                
            </div>            

            <div class="watermark"></div>

                                 
        </div>
    </main>

@endsection

<script>
</script>
