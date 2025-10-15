@extends('layouts.print')
@section('content')
    <main>
 
        @php
            $doc_name = mb_strtoupper("Análise estatística - ".$Pauta_Name??"", 'UTF-8');
            $discipline_code = '';
        @endphp
        @include('Reports::pdf_model.forLEARN_header')
       

        @php
            $x = 0;
            $i = 1;
            $total_geral=[
                     " M_Aprovado"=>0,
                      "F_Aprovado"=> 0,
                      "Total_Aprovado"=>0,
                      "M_Reprovado"=>0,
                      "F_Reprovado"=> 0,
                      "Total_Reprovado"=>0
              ];

            $g=0;
        @endphp

       
        <style>
              .tabela-estatistica thead{
                display: table-row-group;
            }

            .tabela-estatistica td {
                text-align: center;
                white-space: pre-wrap;  
            }

            th,
            td {
                padding: 2px 4px 1px 4px;

                border: 1px solid white;
                font-size: 10px;
            }

            thead {
                width: 100%;
            }

            td {
                font-size: 10px;
                border: 1px solid white;
            }

            #tabela-aprovados {
                float: left;
            }

            #tabela-reprovados {
                float: left;
                margin-right: 34px
            }

            #tabela-total {}

            .bnone {
                border: none !important;
            }


            h4 {
                text-transform: uppercase;
            }

            .titulo-aprovados {
                background-color: #a8d08d;
            }

            .titulo-reprovados {
                background-color: #c55a11;
            }

            .titulo-avaliados {
                background-color: #8eaadb;

            }

            .escala-135 {
                background-color: #ffe598;
            }

            .escala-24 {
                background-color: #f7caac;
            }

            .anodisc {
                background-color: #b4c6e7;
            }

            .t {
                background-color: #aeabab;
            }

            .t-descricao {
                background-color: #ffd965;
            }
             .page-break {
        page-break-inside: avoid;
    }
        </style>

        <br>
        <br>

        {{-- <h4 class="titulo titulo-avaliados" style="padding: 5px 10px; color:white;min-width:25%; max-width:45%; align-content:center;" >  
        <b>Análise estatística - {{$Pauta_Name??""}}</b>
        </h4> --}}
  

        
            
        
            
       


        <table class="tabela-estatistica" cellspacing="0" style="width: 100% !important">
            <thead>
                <tr>
                    <th colspan="2" class="bnone"></th>
                        <th colspan="10" class="bnone"></th>
                        <th colspan="5" class="bnone"></th>
                    <th class="bnone" style="text-align:left;font-size: 10pt;" colspan="50">
                        
                        Curso(s):
                           
                            {{$cursos}}

                    </th>
                </tr>
                <tr>
                    <th colspan="50" class="bnone" style="color:rgba(0,0,0,0)">.</th>
                </tr>

                <tr>
                    <th colspan="2" class="bnone"></th>
                    <th colspan="10" class="bnone"></th>
                    <th colspan="5" class="bnone"></th>

                    @if ($scalaAprovado > 0)
                        <th colspan="{{ ($scalaAprovado * 6) + 6 }}" class="titulo-aprovados">APROVADOS</th>
                        <th colspan="1" class="bnone"></th>
                    @endif

                    @if ($scalaReprovado > 0)
                        <th colspan="{{ ($scalaReprovado * 6) + 6 }}" class="titulo-reprovados">REPROVADOS</th>
                        <th colspan="1" class="bnone"></th>
                    @endif

                    @if (isset($scalaReprovado) && isset($scalaAprovado))
                        @if(($scalaReprovado>0) && ($scalaAprovado>0))
                            <th colspan="6" class="titulo-avaliados">TOTAL AVALIADOS</th>
                        @endif
                    @endif

                </tr>
                
                <tr>
                    <th colspan="2" class="bnone"></th>
                    <th colspan="10" class="bnone"></th>
                    <th colspan="5" class="bnone"></th>

                    @if ($scalaAprovado > 0)
                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'thirst')
                                <th colspan="6" class="escala-135">( 10-13 )</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'fourth')
                                <th colspan="6" class="escala-24">( 14-16 )</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'fiveth')
                                <th colspan="6" class="escala-135">( 17-19 )</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'sixth')
                                <th colspan="6" class="escala-24">( 20 )</th>
                            @endif
                        @endforeach


                        <th colspan="6" class="escala-135">TOTAL APROVADOS</th>
                        <th colspan="1" class="bnone"></th>
                    @endif


                    @if ($scalaReprovado > 0)
                        @foreach ($scalaReprovadoName as $item)
                            @if ($item == 'first')
                                <th colspan="6" class="escala-135">( 0-6 )</th>
                            @endif
                        @endforeach


                        @foreach ($scalaReprovadoName as $item)
                            @if ($item == 'second')
                                <th colspan="6" class="escala-24">( 7-9 )</th>
                            @endif
                        @endforeach


                        <th colspan="6" class="escala-135">TOTAL REPROVADOS</th>
                        <th colspan="1" class="bnone"></th>
                    @endif

                    <th colspan="6"></th>

                </tr>
                <tr>


                    <th colspan="2" class="anodisc">Ano</th>
                    <th colspan="10" class="anodisc">Disciplina</th>
                    <th colspan="5" class="titulo-reprovados">Turma</th>


                    @if ($scalaAprovado > 0)
                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'thirst')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'fourth')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'fiveth')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach

                        @foreach ($scalaAprovadoName as $item)
                            @if ($item == 'sixth')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach


                        <th class="titulo-avaliados">M</th>
                        <th class="titulo-avaliados">%</th>
                        <th class="escala-24">F</th>
                        <th class="escala-24">%</th>
                        <th class="t">T</th>
                        <th class="t">%</th>
                        <th colspan="1" class="bnone"></th>
                    @endif

                    @if ($scalaReprovado > 0)
                        @foreach ($scalaReprovadoName as $item)
                            @if ($item == 'first')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach


                        @foreach ($scalaReprovadoName as $item)
                            @if ($item == 'second')
                                <th class="titulo-avaliados">M</th>
                                <th class="titulo-avaliados">%</th>
                                <th class="escala-24">F</th>
                                <th class="escala-24">%</th>
                                <th class="t">T</th>
                                <th class="t">%</th>
                            @endif
                        @endforeach



                        <th class="titulo-avaliados">M</th>
                        <th class="titulo-avaliados">%</th>
                        <th class="escala-24">F</th>
                        <th class="escala-24">%</th>
                        <th class="t">T</th>
                        <th class="t">%</th>
                        <th colspan="1" class="bnone"></th>
                    @endif




                    @if (isset($scalaReprovado) && isset($scalaAprovado))
                     @if(($scalaReprovado>0) && ($scalaAprovado>0))
                        <th class="titulo-avaliados">M</th>
                        <th class="titulo-avaliados">%</th>
                        <th class="escala-24">F</th>
                        <th class="escala-24">%</th>
                        <th class="t">T</th>
                        <th class="t">%</th>
                        <th colspan="1" class="bnone"></th>
                        @endif
                    @endif

                </tr>
                <tr style="height: 5px;">
                 
                </tr>

            </thead>

            <tbody>
                @php
                     $ountt=[
                         'first'=>0,
                         'second'=>0,
                         'thirst'=>0,
                         'fourth'=>0,
                         'fiveth'=>0,
                         'sixth'=>0
                       ];

                     $mediaM=[
                         'first'=>0,
                         'second'=>0,
                         'thirst'=>0,
                         'fourth'=>0,
                         'fiveth'=>0,
                         'sixth'=>0
                       ];

                     $mediaF=[
                         'first'=>0,
                         'second'=>0,
                         'thirst'=>0,
                         'fourth'=>0,
                         'fiveth'=>0,
                         'sixth'=>0
                       ];

                     $FINAL_M=[];
                     $FINAL_F=[];
                    
                  
                @endphp
                @php
                    
                    $total_student=$total['F']+$total['M'];
                    @endphp

                @foreach ($dados_ano_turma_disc as $lado)
                 <tr>

                        <td colspan="2" class="t-descricao">  {{ $lado->anoCurricular }}</td>
                        <td colspan="10" class="t-descricao"> {{ $lado->disciplina_name }}</td>
                        <td colspan="5" class="t-descricao">  {{ $lado->turma }} </td>


                        @if ($scalaAprovado > 0)

                            

                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'thirst')

                                    @php
                              
                                        $ountt['thirst']++;
                                        $mediaM['thirst']=$mediaM['thirst']+$item->masculine;
                                        $mediaF['thirst']=$mediaF['thirst']+$item->feminine;
                                        $FINAL_M['thirst']= $mediaM['thirst'];
                                        $FINAL_F['thirst']=$mediaF['thirst'];
                                
                                    @endphp   
                                    
                                   
                                        <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                        {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                        <td class="titulo-avaliados">{{ number_format(($item->masculine*100)/$total_student,2) }}%</td>
                                        <td class="escala-24">{{ $item->feminine }}</td>
                                        {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                        <td class="escala-24">{{ number_format(($item->feminine*100)/$total_student,2) }}%</td>
                                        <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                        <th class="t">{{ number_format((($item->masculine + $item->feminine)*100)/$total_student,2) }}%</th>
                                    @endif
                                @endif
                            @endforeach

                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'fourth')

                                    @php
                                        $ountt['fourth']++;
                                        $mediaM['fourth']=$mediaM['fourth']+$item->masculine;
                                        $mediaF['fourth']=$mediaF['fourth']+$item->feminine;
                                        $FINAL_M['fourth']=$mediaM['fourth'];
                                        $FINAL_F['fourth']=$mediaF['fourth'];
                                   @endphp  
                             

                                        <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                        {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                        <td class="titulo-avaliados">{{number_format(($item->masculine*100)/$total_student ,2) }}%</td>
                                        <td class="escala-24">{{ $item->feminine }}</td>
                                        {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                        <td class="escala-24">{{ number_format(($item->feminine*100)/$total_student ,2) }}%</td>
                                        <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                        <th class="t">{{ number_format((($item->masculine + $item->feminine)*100)/$total_student ,2)}}%</th>
                                    @endif
                                @endif
                            @endforeach

                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'fiveth')

                                    @php
                                        $ountt['fiveth']++;
                                        $mediaM['fiveth']=$mediaM['fiveth']+$item->masculine;
                                        $mediaF['fiveth']=$mediaF['fiveth']+$item->feminine;
                                        $FINAL_M['fiveth']=$mediaM['fiveth'];
                                        $FINAL_F['fiveth']=$mediaF['fiveth'];
                                    @endphp  
                                        <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                        {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                        <td class="titulo-avaliados">{{number_format( ($item->masculine*100)/$total_student,2) }}%</td>
                                        <td class="escala-24">{{ $item->feminine }}</td>
                                        {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                        <td class="escala-24">{{ number_format(($item->feminine*100)/$total_student ,2)}}%</td>
                                        <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                        <th class="t">{{ number_format((($item->masculine + $item->feminine)*100)/$total_student,2) }}%</th>
                                    @endif
                                @endif
                            @endforeach

                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'sixth')

                                        @php
                                            $ountt['sixth']++;
                                            $mediaM['sixth']=$mediaM['sixth']+$item->masculine;
                                            $mediaF['sixth']=$mediaF['sixth']+$item->feminine;
                                            $FINAL_M['sixth']=$mediaM['sixth'];
                                            $FINAL_F['sixth']=$mediaF['sixth'];
                                        @endphp  
                                          <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                          {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                          <td class="titulo-avaliados">{{ number_format(($item->masculine*100)/$total_student,2) }}%</td>
                                          <td class="escala-24">{{ $item->feminine }}</td>
                                          {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                          <td class="escala-24">{{ number_format(($item->feminine*100)/$total_student,2) }}%</td>
                                          <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                          <th class="t">{{ number_format((($item->masculine + $item->feminine)*100)/$total_student,2) }}%</th>
                                    @endif
                                @endif
                            @endforeach

                            @php 
                            $total_countA=0;
                            $total_MA=0;
                            $total_FA=0;
                            $total_Aprovados_MF=0;

                            @endphp
                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                        @if ($item->scale == 'thirst' || $item->scale == 'fourth'||$item->scale == 'fiveth' || $item->scale == 'sixth')
                                            @php 
                                            $total_MA+=$item->masculine;
                                            $total_FA+=$item->feminine;
                                            $total_countA++;
                                            @endphp
                                      
                                        @endif
                                        
                                @endif


                            @endforeach
                            @php
                                  $total_Aprovados_MF = $total_MA+$total_FA;
                            @endphp

                        @if ($total_countA>0)
                        <td class="titulo-avaliados">{{$total_MA}}</td>
                        <td class="titulo-avaliados">{{number_format(($total_Aprovados_MF)!=0?($total_MA*100)/$total_student:0,2)}}%</td>
                        <td class="escala-24">{{$total_FA}}</td>
                        <td class="escala-24">{{number_format(($total_Aprovados_MF)!=0?($total_FA*100)/$total_student:0,2)}}%</td>
                        <th class="t">{{$total_Aprovados_MF}}</th>
                        <td class="t">{{number_format(($total_Aprovados_MF)!=0?($total_Aprovados_MF*100)/$total_student:0,2)}}%</td>
                        <th colspan="1" class="bnone"></th>
                            
                        @endif
                        @endif

                       
                        @if ($scalaReprovado > 0)
                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'first')
                                    @php
                              
                                    $ountt['first']++;
                                    $mediaM['first']=$mediaM['first']+$item->masculine;
                                    $mediaF['first']=$mediaF['first']+$item->feminine;
                                    $FINAL_M['first']= $mediaM['first'];
                                    $FINAL_F['first']=$mediaF['first'];
                            
                                @endphp   
                                
                               
                                <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                <td class="titulo-avaliados">{{  number_format(($item->masculine*100)/$total_student,2) }}%</td>
                                <td class="escala-24">{{ $item->feminine }}</td>
                                {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                <td class="escala-24">{{  number_format(($item->feminine*100)/$total_student,2) }}%</td>
                                <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                <th class="t">{{ number_format( (($item->masculine + $item->feminine)*100)/$total_student,2) }}%</th>

                                    @endif
                                @endif

                            @endforeach
                            @foreach ($dados as $item)
                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'second')
                                    @php
                              
                                    $ountt['second']++;
                                    $mediaM['second']=$mediaM['second']+$item->masculine;
                                    $mediaF['second']=$mediaF['second']+$item->feminine;

                                    $FINAL_M['second']= $mediaM['second'];
                                    $FINAL_F['second']=$mediaF['second'];
                            
                                @endphp   
                                
                               
                                <td class="titulo-avaliados">{{ $item->masculine }}</td>
                                        {{-- <td class="titulo-avaliados">{{ $item->percent_masculine }}%</td> --}}
                                        <td class="titulo-avaliados">{{ number_format(($item->masculine*100)/$total_student,2) }}%</td>
                                        <td class="escala-24">{{ $item->feminine }}</td>
                                        {{-- <td class="escala-24">{{ $item->percent_feminine }}%</td> --}}
                                        <td class="escala-24">{{ number_format(($item->feminine*100)/$total_student,2) }}%</td>
                                        <th class="t">{{ $item->masculine + $item->feminine }}</th>
                                        <th class="t">{{ number_format((($item->masculine + $item->feminine)*100)/$total_student,2) }}%</th>
                                    @endif
                                @endif

                            @endforeach
                            @php 
                            $total_count=0;
                            $total_M=0;
                            $total_F=0;
                            $total_Reprovados_MF=0;

                            @endphp
                            @foreach ($dados as $item)

                                @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                        @if ($item->scale == 'first' || $item->scale == 'second')
                                            @php 
                                            $total_M+=$item->masculine;
                                            $total_F+=$item->feminine;
                                            $total_count++;
                                            @endphp
                                      
                                        @endif
                                        
                                @endif



                            @endforeach

                            @php
                                $total_Reprovados_MF = $total_M+$total_F;
                            @endphp

                            @if ($total_count>0)
                            <td class="titulo-avaliados">{{$total_M}}</td>
                            <td class="titulo-avaliados">{{number_format(($total_Reprovados_MF)!=0?($total_M*100)/$total_student:0,2)}}%</td>
                            <td class="escala-24">{{$total_F}}</td>
                            <td class="escala-24">{{number_format(($total_Reprovados_MF)!=0?($total_F*100)/$total_student:0,2)}}%</td>
                            <th class="t">{{$total_Reprovados_MF}}</th>
                            <td class="t">{{number_format(($total_Reprovados_MF)!=0?($total_Reprovados_MF*100)/$total_student:0,2)}}%</td>
                            <th colspan="1" class="bnone"></th>
                                
                            @endif

                            

                    @endif
                      @php 
                            $total_Aprovados_MF=isset($total_Aprovados_MF)?$total_Aprovados_MF:0;
                            $total_MA=isset($total_MA)?$total_MA:0;
                            $total_FA=isset($total_FA)?$total_FA:0;

                            $total_Reprovados_MF=isset($total_Reprovados_MF)?$total_Reprovados_MF:0;
                            $total_M=isset($total_M)?$total_M:0;
                            $total_F=isset($total_F)?$total_F:0;
                           $i=$total_Reprovados_MF +$total_Aprovados_MF;
                      
                      @endphp
                   
                   @if (isset($scalaReprovado) && isset($scalaAprovado))
                   @if(($scalaReprovado>0) && ($scalaAprovado>0))
                    <td class="titulo-avaliados">{{ $total_M  + $total_MA   }}</td>
                    <td class="titulo-avaliados"> {{ number_format($i!=0?(($total_M  + $total_MA)*100)/$total_student:0,2)}}%</td>
                    <td class="escala-24">{{ $total_F +$total_FA }}</td>
                    <td class="escala-24">{{number_format( $i!=0?(($total_F  + $total_FA)*100)/$total_student:0,2)}}%</td>
                    <th class="t">{{$total_Reprovados_MF+$total_Aprovados_MF}}</th> 
                    <td class="t">{{ number_format($i!=0?(($total_Reprovados_MF+$total_Aprovados_MF)*100)/$total_student:0,2)}}%</td>
                    @endif
                    @endif
                     </tr>
                @endforeach

           
                <tr>
                    <th colspan="50" class="bnone" style="color:rgba(0,0,0,0)">.</th>
                </tr>

                {{-- ===================================== TOTAL ========================================= --}}

                <tr class="page-break">

                    <th colspan="2" class="bnone"> </th>
                    <th colspan="10" class="bnone"></th>
                    <th colspan="5" class="bnone"> </th>


                    @if ($scalaAprovado > 0)
                        
                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                              @if ($item->scale == 'thirst')
                                @php
                                  $media['thirst']=($FINAL_F['thirst']+$FINAL_M['thirst']);
                                @endphp
                        

                                <td class="titulo-avaliados">{{$FINAL_M['thirst']}}</td>
                                <td class="titulo-avaliados">{{  number_format($media['thirst']!=0?($FINAL_M['thirst']*100)/$total_student:0,2)}}%</td>
                                <td class="escala-24">{{$FINAL_F['thirst']}}</td>
                                <td class="escala-24">{{ number_format($media['thirst']!=0?($FINAL_F['thirst']*100)/$total_student:0,2)}}%</td>
                                <th class="t">{{ $media['thirst']}}</th>
                                <td class="t">{{ number_format($media['thirst']!=0?($media['thirst'] *100)/$total_student:0,2)}}%</td>
                             @endif
                            @endif
                        @endforeach

                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                @if ($item->scale == 'fourth')
                                   @php
                                     $media['fourth']=($FINAL_F['fourth']+$FINAL_M['fourth']);
                                   @endphp
                              
                                
                              <td class="titulo-avaliados">{{$FINAL_M['fourth']}}</td>
                              <td class="titulo-avaliados">{{number_format($media['fourth']!=0?($FINAL_M['fourth']*100)/$total_student:0,2)}}%</td>
                              <td class="escala-24">{{$FINAL_F['fourth']}}</td>
                              <td class="escala-24">{{number_format($media['fourth']!=0?($FINAL_F['fourth']*100)/$total_student:0,2)}}%</td>
                              <th class="t">{{ $media['fourth']}}</th>
                              <td class="t">{{number_format($media['fourth']!=0?($media['fourth'] *100)/$total_student:0,2)}}%</td>
                                @endif
                            @endif
                        @endforeach

                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                @if ($item->scale == 'fiveth')
                                @php
                                $media['fiveth']=($FINAL_F['fiveth']+$FINAL_M['fiveth']);
                              @endphp
                         
                                <td class="titulo-avaliados">{{$FINAL_M['fiveth']}}</td>
                                <td class="titulo-avaliados">{{number_format($media['fiveth']!=0?($FINAL_M['fiveth']*100)/$total_student:0,2)}}%</td>
                                <td class="escala-24">{{$FINAL_F['fiveth']}}</td>
                                <td class="escala-24">{{number_format($media['fiveth']!=0?($FINAL_F['fiveth']*100)/$total_student:0,2)}}%</td>
                                <th class="t">{{ $media['fiveth']}}</th>
                                <td class="t">{{number_format($media['fiveth']!=0?($media['fiveth'] *100)/$total_student:0,2)}}%</td>
                                @endif
                            @endif
                        @endforeach

                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                @if ($item->scale == 'sixth')
                                @php
                                     $media['sixth']=($FINAL_F['sixth']+$FINAL_M['sixth']);
                                @endphp

                                <td class="titulo-avaliados">{{$FINAL_M['sixth']}}</td>
                                <td class="titulo-avaliados">{{number_format($media['sixth']!=0?($FINAL_M['sixth']*100)/$total_student:0,2)}}%</td>
                                <td class="escala-24">{{$FINAL_F['sixth']}}</td>
                                <td class="escala-24">{{number_format($media['sixth']!=0?($FINAL_F['sixth']*100)/$total_student:0,2)}}%</td>
                                <th class="t">{{ $media['sixth']}}</th>
                                <td class="t">{{number_format($media['sixth']!=0?($media['sixth'] *100)/$total_student:0,2)}}%</td>
                                @endif
                            @endif
                        @endforeach

                        @php 
                        $total_countA=0;
                        $total_MA=0;
                        $total_FA=0;
                        $total_Aprovados_MF=0;

                     
                        @endphp
                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'thirst' || $item->scale == 'fourth'||$item->scale == 'fiveth' || $item->scale == 'sixth')
                                        @php 
                                        $total_MA+=$item->masculine;
                                        $total_FA+=$item->feminine;
                                        $total_countA++;
                                        @endphp
                                  
                                    @endif
                                    
                            @endif


                        @endforeach
                        @php

                            $M_totalA =0; $F_totalA =0;
                            foreach ($mediaM as $key => $value) {
                                if ($key!="first" && $key!="second") {
                                    $M_totalA = $M_totalA + $t=isset($FINAL_M[$key])?$FINAL_M[$key]:0; 
                                    $F_totalA = $F_totalA + $t=isset($FINAL_F[$key])?$FINAL_F[$key]:0; 
                                 }
                            }
                            $total_Aprovado_Media= $M_totalA +$F_totalA ;

                            $total_geral["M_Aprovado"]=$M_totalA;
                            $total_geral["F_Aprovado"]=$F_totalA;
                            $total_geral["Total_Aprovado"]=$F_totalA+$M_totalA;
                       
                         @endphp
                    @if ($total_countA>0)
                           

                    <td class="titulo-avaliados">{{$M_totalA}}</td>
                    <td class="titulo-avaliados">{{number_format(($total_Aprovado_Media)!=0?($M_totalA*100)/$total_student:0,2)}}%</td>
                    <td class="escala-24">{{$F_totalA}}</td>
                    <td class="escala-24">{{number_format(($total_Aprovado_Media)!=0?($F_totalA*100)/$total_student:0,2)}}%</td>
                    <th class="t">{{$total_Aprovado_Media}}</th>
                    <td class="t">{{number_format(($total_Aprovado_Media)!=0?($total_Aprovado_Media*100)/$total_student:0,2)}}%</td>
                    <th colspan="1" class="bnone"></th>
                        
                    @endif
                    @endif

                   
                    @if ($scalaReprovado > 0)
                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                @if ($item->scale == 'first')
                                @php
                                $media['first']=($FINAL_F['first']+$FINAL_M['first']);
                              @endphp
                      

                            <td class="titulo-avaliados">{{$FINAL_M['first']}}</td>
                            <td class="titulo-avaliados">{{number_format($media['first']!=0?($FINAL_M['first']*100)/$total_student:0,2)}}%</td>
                            <td class="escala-24">{{$FINAL_F['first']}}</td>
                            <td class="escala-24">{{number_format($media['first']!=0?($FINAL_F['first']*100)/$total_student:0,2)}}%</td>
                            <th class="t">{{ $media['first']}}</th>
                            <td class="t">{{number_format($media['first']!=0?($media['first'] *100)/$total_student:0,2)}}%</td>

                                @endif
                            @endif

                        @endforeach
                        @foreach ($dados as $item)
                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                @if ($item->scale == 'second')
                                @php
                                $media['second']=($FINAL_F['second']+$FINAL_M['second']);
                              @endphp
                      

                            <td class="titulo-avaliados">{{$FINAL_M['second']}}</td>
                            <td class="titulo-avaliados">{{number_format($media['second']!=0?($FINAL_M['second']*100)/$total_student:0,2)}}%</td>
                            <td class="escala-24">{{$FINAL_F['second']}}</td>
                            <td class="escala-24">{{number_format($media['second']!=0?($FINAL_F['second']*100)/$total_student:0,2)}}%</td>
                            <th class="t">{{ $media['second']}}</th>
                            <td class="t">{{number_format($media['second']!=0?($media['second'] *100)/$total_student:0,2)}}%</td>
                                @endif
                            @endif

                        @endforeach
                        @php 
                        $total_count=0;
                        $total_M=0;
                        $total_F=0;
                        $total_Reprovados_MF=0;


                        @endphp
                        @foreach ($dados as $item)

                            @if ($lado->anoCurricular == $item->anoCurricular && $lado->turma == $item->turma && $lado->disciplina_name == $item->disciplina_name)
                                    @if ($item->scale == 'first' || $item->scale == 'second')
                                        @php 
                                        $total_M+=$item->masculine;
                                        $total_F+=$item->feminine;
                                        $total_count++;
                                        @endphp
                                  
                                    @endif
                                    
                            @endif

                        @endforeach

                        @php
                            $M_totalRE =0; $F_totalRE =0;
                            foreach ($mediaM as $key => $value) {
                                if ($key=="first" || $key=="second") {
                                    $M_totalRE = $M_totalRE + $t=isset($FINAL_M[$key])?$FINAL_M[$key]:0; 
                                    $F_totalRE = $F_totalRE + $t=isset($FINAL_F[$key])?$FINAL_F[$key]:0; 
                                }
                            }

                            $total_Reprovados_MF_media = $M_totalRE+$F_totalRE;

                            $total_geral["M_Reprovado"]=$M_totalRE;
                            $total_geral["F_Reprovado"]=$F_totalRE;
                            $total_geral["Total_Reprovado"]=$total_Reprovados_MF_media;
                            
                            // $g= $total_geral["Total_Reprovado"] + $total_geral["Total_Aprovado"];
                         
                            
                        @endphp

                            

                        @if ($total_count>0)
                            <td class="titulo-avaliados">{{$M_totalRE}}</td>
                            <td class="titulo-avaliados">{{number_format(($total_Reprovados_MF_media)!=0?($M_totalRE*100)/$total_student:0,2)}}%</td>
                            <td class="escala-24">{{$F_totalRE}}</td>
                            <td class="escala-24">{{number_format(($total_Reprovados_MF_media)!=0?($F_totalRE*100)/$total_student:0,2)}}%</td>
                            <th class="t">{{$total_Reprovados_MF_media}}</th>
                            <td class="t">{{number_format(($total_Reprovados_MF_media)!=0?($total_Reprovados_MF_media*100)/$total_student:0,2)}}%</td>
                            <th colspan="1" class="bnone"></th>
                        @endif

                        

                @endif
                {{-- @foreach ($total_geral as $key=> $item)    
                        {{$key}}- {{$item}}
                        <br>
                @endforeach --}}
                @php
                    
                @endphp

                @php
                    $M_GeralA=$total_geral['M_Aprovado'] ??0 ; 
                    $M_GeralR=$total_geral['M_Reprovado'] ?? 0 ;
                    $M_Geral=$M_GeralR+$M_GeralA;
                    
                    $F_GeralA=$total_geral['F_Aprovado'] ?? 0 ;
                    $F_GeralR=$total_geral['F_Reprovado'] ?? 0;
                   
                    $F_Geral= $F_GeralA+ $F_GeralR;

                    $g=$M_Geral+$F_Geral;
                @endphp


            @if (isset($total_count) && isset($total_countA))
                <td class="titulo-avaliados">{{  $M_Geral}}</td>
                <td class="titulo-avaliados">{{number_format(($g)!=0?(int) ($M_Geral*100)/$total_student:0,2)}}%</td>
                <td class="escala-24">{{ $F_Geral }}</td>
                <td class="escala-24">{{number_format(($g)!=0?(int) ($F_Geral*100)/$total_student:0,2)}}%</td>
                <th class="t">{{$g}}</th> 
                <td class="t">{{number_format(($g)!=0?(int) ($g*100)/$total_student:0,2)}}%</td>
            @endif

            </tr>

            </tbody>
        </table>
        
         
        <br>
    </main>
    @endsection
    {{-- @include('Reports::pdf_model.pdf_footer') --}}

<script>
    // window.print();
</script>
