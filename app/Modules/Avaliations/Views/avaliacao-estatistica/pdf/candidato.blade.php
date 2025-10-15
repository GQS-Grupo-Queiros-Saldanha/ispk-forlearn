@extends('layouts.print')
@section('content')
    <main>

        @include('Reports::pdf_model.pdf_header')


        @php
            $x = 0;
            $i = 1;
            $total_geral = [
                ' M_Aprovado' => 0,
                'F_Aprovado' => 0,
                'Total_Aprovado' => 0,
                'M_Reprovado' => 0,
                'F_Reprovado' => 0,
                'Total_Reprovado' => 0,
            ];
            
            $g = 0;
        @endphp


        <style>
            .tabela-estatistica thead {
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
                font-weight:bold;
            }

            .t-descricao {
                background-color: #ffd965;
            }

            .bg0,.div-top{
                background-color: #2f5496!important;
                color: white!important;
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
            .h1-title{
                color: white;
            }

            .div-top table tr:last-child{
                color: white;
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
                   
                    
                    <th class="bnone bg1" style="text-align:left;font-size: 10pt;" colspan="70">

                        Curso:

                        {{ $cursos }}

                    </th>
                </tr>
                <tr>
                    <th colspan="50" class="bnone" style="color:rgba(0,0,0,0)">.</th>
                </tr>

                <tr>
                    <th colspan="2" class="bnone"></th>
                    {{-- <th colspan="10" class="bnone"></th> --}}
                    <th colspan="6" class="bnone"></th>


                    <th colspan="30" class="titulo-aprovados">APROVADOS</th>
                    <th colspan="1" class="bnone"></th>



                    <th colspan="18" class="titulo-reprovados">REPROVADOS</th>
                    <th colspan="1" class="bnone"></th>




                    <th colspan="6" class="titulo-avaliados">TOTAL AVALIADOS</th>

                </tr>
                <tr>
                    <th colspan="2" class="bnone"></th>
                    {{-- <th colspan="10" class="bnone"></th> --}}
                    <th colspan="6" class="bnone"></th>
                    <th colspan="6" class="escala-135">( 10-13 )</th>
                    <th colspan="6" class="escala-24">( 14-16 )</th>
                    <th colspan="6" class="escala-135">( 17-19 )</th>
                    <th colspan="6" class="escala-24">( 20 )</th>
                    <th colspan="6" class="escala-135">TOTAL APROVADOS</th>
                    <th colspan="1" class="bnone"></th>
                    <th colspan="6" class="escala-135">( 0-6 )</th>
                    <th colspan="6" class="escala-24">( 7-9 )</th>
                    <th colspan="6" class="escala-135">TOTAL REPROVADOS</th>
                    <th colspan="1" class="bnone"></th>
                    <th colspan="6"></th>

                </tr>
                <tr>


                    <th colspan="2" class="anodisc">Ano</th>
                    {{-- <th colspan="10" class="anodisc">Disciplina</th> --}}
                    <th colspan="6" class="titulo-reprovados">Turma</th>



                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>
                    <th colspan="1" class="bnone"></th>


                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>

                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>




                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>
                    <th colspan="1" class="bnone"></th>






                    <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>


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


                @foreach ($turma as $item_turma)
                 
               
                    @if (isset($turmas[$item_turma->id_turma]))
                        
                   


                    <tr>
                        <td colspan="2" class="t-descricao">1</td>
                        <td colspan="6" class="t-descricao">{{ $item_turma->nome_turma }}</td>


                        
                        @php
                            
                            $total_thirst = $turmas[$item_turma->id_turma][1]["thirst"] + $turmas[$item_turma->id_turma][0]["thirst"];
                            $total_fourth = $turmas[$item_turma->id_turma][1]["fourth"] + $turmas[$item_turma->id_turma][0]["fourth"];
                            $total_fiveth = $turmas[$item_turma->id_turma][1]["fiveth"] + $turmas[$item_turma->id_turma][0]["fiveth"];
                            $total_sixth = $turmas[$item_turma->id_turma][1]["sixth"] + $turmas[$item_turma->id_turma][0]["sixth"];

                            $total_first = $turmas[$item_turma->id_turma][1]["first"] + $turmas[$item_turma->id_turma][0]["first"];
                            $total_second = $turmas[$item_turma->id_turma][1]["second"] + $turmas[$item_turma->id_turma][0]["second"];

                            // Total Masculino aprovado

                            $total_M_A = $turmas[$item_turma->id_turma][1]["thirst"]+$turmas[$item_turma->id_turma][1]["fourth"]+$turmas[$item_turma->id_turma][1]["fiveth"]+$turmas[$item_turma->id_turma][1]["sixth"];
                            $total_F_A = $turmas[$item_turma->id_turma][0]["thirst"]+$turmas[$item_turma->id_turma][0]["fourth"]+$turmas[$item_turma->id_turma][0]["fiveth"]+$turmas[$item_turma->id_turma][0]["sixth"];

                            $total_A =  $total_M_A +  $total_F_A;

                            // Total Masculino aprovado

                            $total_M_R = $turmas[$item_turma->id_turma][1]["first"]+$turmas[$item_turma->id_turma][1]["second"];
                            $total_F_R = $turmas[$item_turma->id_turma][0]["first"]+$turmas[$item_turma->id_turma][0]["second"];

                            $total_R =  $total_M_R +  $total_F_R;
                            
                            $total_AV = $total_R+$total_A;

                            $mediaF['first'] = $mediaF['first'] + $turmas[$item_turma->id_turma][0]["first"];
                            $mediaF['second'] = $mediaF['second'] + $turmas[$item_turma->id_turma][0]["second"];
                            $mediaF['thirst'] = $mediaF['thirst'] + $turmas[$item_turma->id_turma][0]["thirst"];
                            $mediaF['fourth'] = $mediaF['fourth'] + $turmas[$item_turma->id_turma][0]["fourth"];
                            $mediaF['fiveth'] = $mediaF['fiveth'] + $turmas[$item_turma->id_turma][0]["fiveth"];
                            $mediaF['sixth'] = $mediaF['sixth'] + $turmas[$item_turma->id_turma][0]["sixth"];
                           
                             
                            $mediaM['first'] = $mediaM['first'] + $turmas[$item_turma->id_turma][1]["first"];
                            $mediaM['second'] = $mediaM['second'] + $turmas[$item_turma->id_turma][1]["second"];
                            $mediaM['thirst'] = $mediaM['thirst'] + $turmas[$item_turma->id_turma][1]["thirst"];
                            $mediaM['fourth'] = $mediaM['fourth'] + $turmas[$item_turma->id_turma][1]["fourth"];
                            $mediaM['fiveth'] = $mediaM['fiveth'] + $turmas[$item_turma->id_turma][1]["fiveth"];
                            $mediaM['sixth'] = $mediaM['sixth'] + $turmas[$item_turma->id_turma][1]["sixth"];
                           
                           
                            $ountt=[
                                'first'=>0,
                                'second'=>0,
                                'thirst'=>0,
                                'fourth'=>0,
                                'fiveth'=>0,
                                'sixth'=>0
                            ];

                        @endphp
                       
                        {{-- Terceira Escala  --}}

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["thirst"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["thirst"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["thirst"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["thirst"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_thirst}}</th>
                        <th class="t">{{($total_AV)!=0? round((($total_thirst)/( $total_AV))*100,2):0,}}%</th>
                      
                        {{-- Quarta Escala  --}}

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["fourth"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["fourth"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["fourth"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["fourth"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_fourth}}</th>
                        <th class="t">{{($total_AV)!=0? round((($total_fourth)/( $total_AV))*100,2):0,}}%</th>
                        
                        {{-- Quinta Escala  --}}

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["fiveth"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["fiveth"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["fiveth"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["fiveth"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_fiveth}}</th>
                        <th class="t">{{($total_AV)!=0? round((($total_fiveth)/( $total_AV))*100,2):0,}}%</th>
                        
                        {{-- Sexta Escala  --}}

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["sixth"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["sixth"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["sixth"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["sixth"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_sixth}}</th>
                        <th class="t">{{($total_AV)!=0? round((($total_sixth)/( $total_AV))*100,2):0,}}%</th>
                        
                        {{-- Total Aprovados  --}}

                        <td class="titulo-avaliados">{{$total_M_A}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_A)/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$total_F_A}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($total_F_A)/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_A}}</th>
                        <td class="t">{{($total_AV)!=0? round((($total_A)/( $total_AV))*100,2):0,}}%</td>
                        
                        
                        
                        <th colspan="1" class="bnone"></th>                                                              
                        
                        {{-- Primeira Escala  --}}

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["first"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["first"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["first"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["first"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_first}}</th>
                        <td class="t">{{($total_AV)!=0? round((($total_first)/( $total_AV))*100,2):0,}}%</td>
                      

                        {{-- Segunda Escala  --}} 

                        <td class="titulo-avaliados">{{$turmas[$item_turma->id_turma][1]["second"]}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][1]["second"])/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$turmas[$item_turma->id_turma][0]["second"]}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($turmas[$item_turma->id_turma][0]["second"])/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_second}}</th>
                        <td class="t">{{($total_AV)!=0? round((($total_second)/( $total_AV))*100,2):0,}}%</td>
                        
                        {{-- Total Reprovados  --}}

                        <td class="titulo-avaliados">{{$total_M_R}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_R)/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$total_F_R}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($total_F_R)/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_R}}</th>       
                        <td class="t">{{($total_AV)!=0? round((($total_R)/( $total_AV))*100,2):0,}}%</td>
                        <th colspan="1" class="bnone"></th>  

                        <td class="titulo-avaliados">{{$total_M_R+$total_M_A}}</td>
                        <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_R+$total_M_A)/( $total_AV))*100,2):0,}}%</td>
                        <td class="escala-24">{{$total_F_R+$total_F_A}}</td>
                        <td class="escala-24">{{($total_AV)!=0? round((($total_F_R+$total_F_A)/( $total_AV))*100,2):0,}}%</td>
                        <th class="t">{{$total_R+$total_A}}</th>   
                        <td class="t">{{($total_AV)!=0? round((($total_R+$total_A)/( $total_AV))*100,2):0,}}%</td>
                        
                                                                      

                                                                               
                    
                    </tr> 
                    @else
                   
                    @endif        
                @endforeach








                {{-- ===================================== TOTAL ========================================= --}}

                <tr>

                    <th colspan="2" class="bnone"> </th>
                    <th colspan="10" class="bnone"></th>
                    <th colspan="6" class="bnone"> </th>


                </tr>

                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>


                    
                    @php
                        
                        $mediaF['first'];
                        $mediaF['second'];
                        $mediaF['thirst'];
                        $mediaF['fourth'];
                        $mediaF['fiveth'];
                        $mediaF['sixth'];
                       
                         
                        $mediaM['first'];
                        $mediaM['second'];
                        $mediaM['thirst'];
                        $mediaM['fourth'];
                        $mediaM['fiveth'];
                        $mediaM['sixth'];
                       
                       
                        $ountt=[
                            'first'=>0,
                            'second'=>0,
                            'thirst'=>0,
                            'fourth'=>0,
                            'fiveth'=>0,
                            'sixth'=>0
                        ];






                        $total_thirst =  $mediaF['thirst'] + $mediaM['thirst'];
                        $total_fourth =  $mediaF['fourth'] + $mediaM['fourth'];
                        $total_fiveth =  $mediaF['fiveth'] + $mediaM['fiveth'];
                        $total_sixth  =   $mediaF['sixth'] + $mediaM['sixth'];

                        $total_first =  $mediaF['first'] +  $mediaM['first'];
                        $total_second =  $mediaF['second'] +  $mediaM['second'];

                        // Total Masculino aprovado

                        $total_M_A = $mediaM['thirst']+$mediaM['fourth']+$mediaM['fiveth']+$mediaM['sixth'];

                        $total_F_A = $mediaF['thirst']+$mediaF['fourth']+$mediaF['fiveth']+$mediaF['sixth'];

                        $total_A =  $total_M_A +  $total_F_A;

                        // Total Masculino aprovado

                        $total_M_R = $mediaM['first']+$mediaM["second"];
                        $total_F_R = $mediaF['first']+$mediaF["second"];
                        

                        $total_R =  $total_M_R +  $total_F_R;
                        

                        $total_AV = $total_R+$total_A;

                    @endphp
                   
                    {{-- Terceira Escala  --}}

                    <td class="titulo-avaliados">{{ $mediaM['thirst']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['thirst'])/($total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['thirst']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['thirst'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_thirst}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_thirst)/( $total_AV))*100,2):0,}}%</td>
                  
                    {{-- Quarta Escala  --}}
 
                    <td class="titulo-avaliados">{{ $mediaM['fourth']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['fourth'])/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['fourth']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['fourth'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_fourth}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_fourth)/( $total_AV))*100,2):0,}}%</td>
                    
                    {{-- Quinta Escala  --}} 

                    <td class="titulo-avaliados">{{ $mediaM['fiveth']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['fiveth'])/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['fiveth']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['fiveth'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_fiveth}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_fiveth)/( $total_AV))*100,2):0,}}%</td>
                    
                    {{-- Sexta Escala  --}} 
           
                    <td class="titulo-avaliados">{{ $mediaM['sixth']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['sixth'])/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['sixth']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['sixth'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_sixth}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_sixth)/( $total_AV))*100,2):0,}}%</td>
           
                    {{-- Total Aprovados  --}}

                    <td class="titulo-avaliados">{{$total_M_A}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_A)/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$total_F_A}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($total_F_A)/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_A}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_A)/( $total_AV))*100,2):0,}}%</td>
                    
                    
                    <th colspan="1" class="bnone"></th>                                                       
                    
                    {{-- Primeira Escala  --}}

     
                    <td class="titulo-avaliados">{{ $mediaM['first']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['first'])/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['first']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['first'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_first}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_first)/( $total_AV))*100,2):0,}}%</td>

                    {{-- Segunda Escala  --}} 
                    
                    <td class="titulo-avaliados">{{ $mediaM['second']}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($mediaM['second'])/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$mediaF['second']}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($mediaF['second'])/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_second}}</th>
                    <td class="t">{{($total_AV)!=0? round((($total_second)/( $total_AV))*100,2):0,}}%</td>

                    
                    {{-- Total Reprovados  --}}

                  <td class="titulo-avaliados">{{$total_M_R}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_R)/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$total_F_R}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($total_F_R)/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_R}}</th>       
                    <td class="t">{{($total_AV)!=0? round((($total_R)/( $total_AV))*100,2):0,}}%</td>
                    <th colspan="1" class="bnone"></th>  

                    <td class="titulo-avaliados">{{$total_M_R+$total_M_A}}</td>
                    <td class="titulo-avaliados">{{($total_AV)!=0? round((($total_M_R+$total_M_A)/( $total_AV))*100,2):0,}}%</td>
                    <td class="escala-24">{{$total_F_R+$total_F_A}}</td>
                    <td class="escala-24">{{($total_AV)!=0? round((($total_F_R+$total_F_A)/( $total_AV))*100,2):0,}}%</td>
                    <th class="t">{{$total_R+$total_A}}</th>  
                    <td class="t">{{($total_AV)!=0? round((($total_R+$total_A)/( $total_AV))*100,2):0,}}%</td>
                    <th colspan="1" class="bnone"></th> 
                                                                  


                
                </tr> 
                <tr>
                    <th colspan="2" class="bnone"> </th>
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>                    
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>                    
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>                    
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>                    
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>
                </tr>
                <tr>
                    <th colspan="2" class="bnone"> </th>                    
                </tr>
                <!--<tr>-->
                <!--    <td colspan="2" class="bnone"></td>-->
                <!--    <td colspan="5" class="bnone"></td>-->
                <!--    <th colspan="5" class="titulo-avaliados" style="text-align: left!important;">TOTAL VAGAS</th>-->
                <!--    <th colspan="2" class="titulo-avaliados" style="text-align: left!important;">{{$vaga}}</th>-->
                <!--</tr>-->
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="t-descricao bg1" style="text-align: left!important;">TOTAL VAGAS</th>
                    <th colspan="2" class="t-descricao bg1" style="text-align: left!important;">{{$vaga}}</th>
                </tr>
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="t-descricao bg2" style="text-align: left!important;">TOTAL CANDIDATOS</th>
                    <th colspan="2" class="t-descricao bg2" style="text-align: left!important;">{{$total_candidatos}}</th>
                </tr>
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="t-descricao" style="text-align: left!important;">TOTAL PROVAS</th>
                    <th colspan="2" class="t-descricao" style="text-align: left!important;">{{$total_R+$total_A}}</th>
                </tr>
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="titulo-aprovados" style="text-align: left!important;">TOTAL ADMITIDOS</th>
                    <th colspan="2" class="titulo-aprovados" style="text-align: left!important;">{{$total_A}}</th>
                </tr>
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="t-descricao bg3" style="text-align: left!important;">TOTAL SUPLENTES</th>
                    <th colspan="2" class="t-descricao bg3" style="text-align: left!important;">
                        @if(!isset($vaga)||$vaga==0)
                            0
                        @else
                        @if ($vaga<$total_A)
                            
                            {{$total_A-$vaga}}
                                
                            @else
                                0
                            @endif
                        @endif    
                    </th>
                </tr>
                <tr>
                    <td colspan="2" class="bnone"></td>
                    <td colspan="6" class="bnone"></td>
                    <th colspan="6" class="titulo-reprovados" style="text-align: left!important;">TOTAL N/ ADMITIDOS</th>
                    <th colspan="2" class="titulo-reprovados" style="text-align: left!important;">{{$total_R}}</th>
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
