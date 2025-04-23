@extends('layouts.print')
@section('content')
    <main>
        @php
            $doc_name = mb_strtoupper("Análise estatística - Graduados", 'UTF-8');
            $discipline_code = '';
        @endphp 
        @include('Reports::pdf_model.forLEARN_header')
        <br> 
        <style>

            #tabela-aprovados{
                margin-bottom: 10px;
                width: 100%;
            }
            #tabela-total{

                width: 22%;
            }

            #tabela-reprovados{
                width: 70%;
            }
            
            #tabela-aprovados th, #tabela-aprovados td, #tabela-aprovados
           {
                text-align: center !important;
                /* background-color: #adadad; */
                
            }
            #tabela-reprovados th, #tabela-reprovados td,#tabela-reprovados
            {
                text-align: center !important;
                /* background-color: #c1c1c1; */
                
            }

            #tabela-total th,#tabela-total td,#tabela-total {
                text-align: center !important;
                /* background-color: #a09e9e; */
                
            }


            #tabela-reprovados {
                float: left;
                margin-right: 8%;
                padding: 2px;
                
            }

         
            .titulo{
                text-transform: uppercase;
                /* background-color: #adadad; */
                width: 250px;
                text-indent: 10px;
                padding: 4px;

            }

            #opaco{
                background-color: transparent;
            }

            .tabela-estatistica thead{
                display: table-row-group;
            }

            .tabela-estatistica td {
                text-align: center;
                white-space: pre-wrap;  
            }
            

            #tabela-aprovados th,#tabela-reprovados  th,#tabela-total th,
            #tabela-aprovados td,#tabela-reprovados td,#tabela-total td 
            {
                padding: 2px 4px 1px 4px;

                border: 1px solid white;
                font-size: 10px;
            }

            #tabela-aprovados  thead,#tabela-reprovados  thead,#tabela-total  thead {
                width: 100%;
            }

            #tabela-aprovados td,#tabela-reprovados td,#tabela-total td {
                font-size: 10px;
                border: 1px solid white;
            }

            .titulo-aprovados {
                background-color: #a8d08d!important;
            }

            .titulo-reprovados {
                background-color: #c55a11!important;
            }

            .titulo-avaliados {
                background-color: #8eaadb!important;

            }

            .escala-135 {
                background-color: #ffe598!important;
            }

            .escala-24 {
                background-color: #f7caac!important;
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

        </style>
        <br>
        <br>


      
        
        {{-- <h4 class="titulo"><b>Análise estatística por disciplina </b></h4> --}}
        <table cellspacing="0" class="table_pauta table-hover dark" id="tabela-aprovados">
            <thead>
                <tr>

                    <th colspan="30" class="titulo-aprovados">APROVADOS</th>

                </tr>
                <tr>

                    <th colspan="6" class="escala-135">( 10-13 )</th>
                    <th colspan="6" class="escala-24">( 14-16 )</th>
                    <th colspan="6" class="escala-135">( 17-19 )</th>
                    <th colspan="6" class="escala-24">( 20 )</th>
                    <th colspan="6" class="escala-135">TOTAL <as style="font-size: 11px;"> APROVADOS</as></th>


                </tr>
                <tr>


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
                </tr>
            </thead>

            <tbody>
                @php
                    {{$total_student=$estatistica_total['Total_m']+$estatistica_total['Total_f'];}} 
                    
                @endphp

                <tr>
                    <td class="titulo-avaliados">{{$estatistica['thirst']['M']}}</td>
                    <th class="titulo-avaliados">{{number_format( ($estatistica['thirst']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['thirst']['F']}}</td>
                    <th class="escala-24">{{ number_format(($estatistica['thirst']['F']*100)/$total_student,2)}} % </th>
                    <th class="t">{{$estatistica['thirst']['F']+$estatistica['thirst']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['thirst']['F']+$estatistica['thirst']['M'])*100)/$total_student,2)}} </th>


                    <td class="titulo-avaliados">{{$estatistica['fourth']['M']}}</td>
                    {{--<td class="titulo-avaliados">{{$estatistica['fourth']['Percent_M']}}%</td>--}}
                    <th class="titulo-avaliados">{{ number_format(($estatistica['fourth']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['fourth']['F']}}</td>
                    {{--<td class="escala-24">{{$estatistica['fourth']['Percent_F']}}%</td>--}}
                    <th class="escala-24">{{number_format( ($estatistica['fourth']['F']*100)/$total_student,2)}} %</th>
                    <th class="t">{{$estatistica['fourth']['F']+$estatistica['fourth']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['fourth']['F']+$estatistica['fourth']['M'])*100)/$total_student,2)}} </th>


                    <td class="titulo-avaliados">{{$estatistica['fiveth']['M']}}</td>
                    {{-- <td class="titulo-avaliados">{{$estatistica['fiveth']['Percent_M']}}%</td> --}}
                    <th class="titulo-avaliados">{{ number_format(($estatistica['fiveth']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['fiveth']['F']}}</td>
                    {{-- <td class="escala-24">{{$estatistica['fiveth']['Percent_F']}}%</td> --}}
                    <th class="escala-24">{{ number_format(($estatistica['fiveth']['F']*100)/$total_student,2)}} %</th>
                    <th class="t">{{$estatistica['fiveth']['F']+$estatistica['fiveth']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['fiveth']['F']+$estatistica['fiveth']['M'])*100)/$total_student,2)}} </th>



                    <td class="titulo-avaliados">{{$estatistica['sixth']['M']}}</td>
                    {{-- <td class="titulo-avaliados">{{$estatistica['sixth']['Percent_M']}}%</td> --}}
                    <th class="titulo-avaliados">{{number_format( ($estatistica['sixth']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['sixth']['F']}}</td>
                    {{-- <td class="escala-24">{{$estatistica['sixth']['Percent_F']}}%</td> --}}
                    <th class="escala-24">{{number_format( ($estatistica['sixth']['F']*100)/$total_student,2)}} %</th>
                    <th class="t">{{$estatistica['sixth']['F']+$estatistica['sixth']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['sixth']['F']+$estatistica['sixth']['M'])*100)/$total_student,2)}} </th>

                    @php
                        $M_aprovados=$estatistica['thirst']['M']+$estatistica['fourth']['M']+$estatistica['fiveth']['M']+$estatistica['sixth']['M'];

                        $F_aprovados=$estatistica['thirst']['F']+$estatistica['fourth']['F']+$estatistica['fiveth']['F']+$estatistica['sixth']['F'];

                        $AP_total=$M_aprovados+$F_aprovados;
                    
                    @endphp

                    <td class="titulo-avaliados">{{$M_aprovados}}</td>
                    <td class="titulo-avaliados">{{number_format($AP_total!=0? ($M_aprovados*100)/$total_student:0,2)}}%</td>
                    <td class="escala-24">{{$F_aprovados}}</td>
                    {{-- <td class="escala-24">{{$AP_total!=0?(int) round(($F_aprovados/$AP_total)*100,0):0,}}%</td> --}}
                    <td class="escala-24">{{number_format($AP_total!=0? ($F_aprovados*100)/$total_student:0,2)}}%</td>
                    <th class="t">{{$AP_total}} </th>
                    <th class="t">{{ number_format(($AP_total*100)/$total_student,2)}} </th>
                    

                </tr>


            </tbody>
        </table>
      
        <table cellspacing="0" id="tabela-reprovados" class="table_pauta table-hover dark">
            <thead>
                <tr>

                    <th colspan="18" class="titulo-reprovados">REPROVADOS</th>

                </tr>
                <tr>

                    <th colspan="6" class="escala-135">( 0-6 )</th>
                    <th colspan="6" class="escala-24">( 7-9 )</th>
                    <th colspan="6" class="escala-135">TOTAL <as style="font-size: 11px;"> REPROVADOS</as></th>



                </tr>
                <tr>



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
                    

                </tr>
            </thead>

            <tbody>
          
                <tr>
                    <td class="titulo-avaliados">{{$estatistica['first']['M']}}</td>
                    {{-- <td class="titulo-avaliados">{{$estatistica['first']['Percent_M']}}%</td> --}}
                    <th class="titulo-avaliados">{{ number_format(($estatistica['first']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['first']['F']}}</td>
                    {{-- <td class="escala-24">{{$estatistica['first']['Percent_F']}}%</td> --}}
                    <th class="escala-24">{{number_format( ($estatistica['first']['F']*100)/$total_student,2)}} %</th>
                    <th class="t">{{$estatistica['first']['F']+$estatistica['first']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['first']['F']+$estatistica['first']['M'])*100)/ $total_student,2)}}</th>


                    <td class="titulo-avaliados">{{$estatistica['second']['M']}}</td>
                    {{-- <td class="titulo-avaliados">{{$estatistica['second']['Percent_M']}}%</td> --}}
                    <th class="titulo-avaliados">{{number_format( ($estatistica['second']['M']*100)/$total_student,2)}} %</th>
                    <td class="escala-24">{{$estatistica['second']['F']}}</td>
                    {{-- <td class="escala-24">{{$estatistica['second']['Percent_F']}}%</td> --}}
                    <th class="escala-24">{{number_format( ($estatistica['second']['F']*100)/$total_student,2)}} %</th>
                    <th class="t">{{$estatistica['second']['F']+$estatistica['second']['M']}}</th>
                    <th class="t">{{ number_format((($estatistica['second']['F']+$estatistica['second']['M'])*100)/ $total_student,2)}}</th>
                    {{-- <th class="t">{{ (($estatistica['second']['F']+$estatistica['second']['M'])*100)/$total_student;}}</th> --}}

                      @php
                      $M_Reprovados=$estatistica['first']['M']+$estatistica['second']['M'];
                      $F_Reprovados=$estatistica['first']['F']+$estatistica['second']['F'];
                      $RP_total=$M_Reprovados+$F_Reprovados;
                  
                    @endphp
                       <td class="titulo-avaliados">{{$M_Reprovados}}</td>
                       <td class="titulo-avaliados">{{number_format($RP_total!=0? ($M_Reprovados*100)/$total_student:0,2)}}%</td>
                       <td class="escala-24">{{$F_Reprovados}}</td>
                       <td class="escala-24">{{number_format($RP_total!=0? ($F_Reprovados*100)/$total_student:0,2)}}%</td>
                       <th class="t">{{$RP_total}}</th>
                       <th class="t">{{number_format( ($RP_total*100) /$total_student,2)}}</th>

                </tr>

            </tbody>
        </table>



        <table cellspacing="0" id="tabela-total" class="table_pauta table-hover dark">
            <thead>
                <tr>
                    <th colspan="6" class="titulo-avaliados">TOTAL <as style="font-size: 11px;">AVALIADOS</as></th>
                </tr>
                <tr>
                    <th colspan="6" style="color: transparent; background-color: white!important"> . </th>
                </tr>
                <tr>


                   <th class="titulo-avaliados">M</th>
                    <th class="titulo-avaliados">%</th>
                    <th class="escala-24">F</th>
                    <th class="escala-24">%</th>
                    <th class="t">T</th>
                    <th class="t">%</th>





                </tr>

            </thead>
         
            <tbody>
                <tr>
                    
                    <th class="titulo-avaliados">{{$estatistica_total["Total_m"]}}</th>
                    <th class="titulo-avaliados">{{number_format(($estatistica_total["Total_m"]+$estatistica_total["Total_f"])!=0? ($estatistica_total["Total_m"]*100)/$total_student:0,2)}} %</th>
                    <th class="escala-24">{{$estatistica_total["Total_f"]}}</th>
                    <th class="escala-24">{{number_format(($estatistica_total["Total_m"]+$estatistica_total["Total_f"])!=0?($estatistica_total["Total_f"]*100)/$total_student:0,2)}} %</th>
                    <th class="t">{{$estatistica_total["Total_m"]+$estatistica_total["Total_f"]}}</th>
                    <th class="t">{{number_format((($estatistica_total["Total_m"]+$estatistica_total["Total_f"])*100)/$total_student,2) }}</th>
                </tr>


            </tbody>
        </table>
        

      

        <br>

        <div class="col-12">







 
           

           
    </main>
    @endsection

<script>
    // window.print();
</script>
