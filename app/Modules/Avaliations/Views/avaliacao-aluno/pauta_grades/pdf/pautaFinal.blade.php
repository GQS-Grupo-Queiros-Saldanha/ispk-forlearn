@extends('layouts.print')
@section('content')

         @php
            $logotipo = $logotipo;
            $documentoCode_documento = 50;
            $doc_name = "Pauta de ".$discipline_name;
        @endphp
    <main>
        
                @include('Reports::pdf_model.forLEARN_header')
        <table class="table_te">
            <style>
                .table_te,.table_pauta {background-color: #F5F3F3; !important ;width:100%;text-align:right;font-family:calibri light; margin-bottom: 6px; }
                    
                .table_pauta_estatistica{background-color: #F5F3F3; !important ;width:100%;text-align:right;font-family:calibri light; margin-bottom: 6px;border:none; border-left:1px solid #fff;border-bottom: 1px solid #fff;}
                .table_te th, .table_pauta th {border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center;}
                .table_pauta_estatistica  th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center;}
                .table_te td, .table_pauta td{border-left:1px solid #fff;background-color:#F9F2F4; } 
                .table_pauta_estatistica td{border-left:1px solid #fff;background-color:#F9F2F4; }
                .table_pauta_estatistica tr{border-bottom:1px solid #fff; } 
                .table_pauta_estatistica  thead{}
                .tabble  thead{ }
                #corpoTabela tr td{ font-size: 12pt; }
                #chega th{ font-size: 13pt; font-weight: bold;}
                .c_final{ font-size: 13pt; font-weight:bold;  }
                .table_pauta thead tr{
                    background-color: #8eaadb!important;
                    padding-top: 3px;
                    padding-bottom: 3px;
                }
                .table_pauta td{
                    padding-top: 3px;
                    padding-bottom: 3px;
                    font-size: 16px!important;
                    border: 1px solid white;
                    background-color: #d9e2f3 !important;
                }
                .table_pauta tr td:nth-child(4){
                    text-align: center!important;
                }

            </style>
            
            <thead style="border:none;" id="chega">
                <tr class="bg1">
    
                    <th >DISCIPLINA</th>
                    <th >CURSO</th>
                    <th >ANO CURRICULAR</th>
                    <th >ANO LECTIVO</th>
                    <th >REGIME</th>
                    <th >TURMA</th> 
                    @isset($prova)
                    <th>PROVA</th> 
                </tr>
                @endisset   
            </thead>
    
    
    
    
            <tbody id="corpoTabela"> 
                <tr class="bg2">
                    <td  class="text-center bg2">
                        {{$discipline_code}}
                    </td>
    
                    <td  class="text-center bg2">
                        {{$curso}} 
                    </td>
                    {{--<td  class="text-center bg2">{{$lectiveYear[0]->display_name}}</td>--}}
                    <td  class="text-center bg2">
                        {{$ano_curricular }} 
                    </td>
                    <td  class="text-center bg2">
                        {{$lectiveYear}}   
                    </td> 
                    <td  class="text-center bg2">
                        {{$regimeFinal}}
                    </td>
                    <td  class="text-center bg2">
                        {{$turma}}
                    </td>
                      @isset($prova)
                    <td  class="text-center bg2">
                        {{$prova}}
                    </td>
                    @endisset
                </tr>                    
            </tbody>
        </table>

       
        @php $x = 0; $i = 1; @endphp
        
        {{-- Começa aqui a tabela --}}
        <?php
            $tabela = storage_path('app/public/pautas-final/tabela.blade.php');
            include $tabela;
        ?>
        {{-- termina aqui --}}
         <style>

            #tabela-aprovados{
                margin-bottom: 10px;
            }
            #tabela-total{

                width: 28%;
            }

            #tabela-reprovados{
                width: 70%;
            }
            
            #tabela-aprovados th, #tabela-aprovados td, #tabela-aprovados
           {
                text-align: center !important;
                background-color: #adadad;
                
            }
            #tabela-reprovados th, #tabela-reprovados td,#tabela-reprovados
            {
                text-align: center !important;
                background-color: #c1c1c1;
                
            }

            #tabela-total th,#tabela-total td,#tabela-total {
                text-align: center !important;
                background-color: #a09e9e;
                
            }


            #tabela-reprovados {
                float: left;
                margin-right: 2%;
                padding: 2px;
                
            }

            th h6{
                font-size: 10px;
            }

            .titulo{
                text-transform: uppercase;
                background-color: #adadad;
                width: 250px;
                text-indent: 10px;
                padding: 4px;

            }

            #opaco{
                background-color: transparent;
            }
        </style>
        <br>
        <br> 
          
          
            </br>
            </br>
            
           
        @if (count($estatistica_tabela)>0)
        
        <h4 class="titulo" style="width: 350px!important"><b>Análise estatística</b></h4>
        <table cellspacing="0" class="table_pauta_estatistica table-hover dark" id="tabela-aprovados" style="width: 100%;">
            <thead>
                <tr>

                    <th colspan="30">APROVADOS</th>

                </tr>
                <tr>

                    <th colspan="5">( 10-13 )</th>
                    <th colspan="5">( 14-16 )</th>
                    <th colspan="5">( 17-19 )</th>
                    <th colspan="5">( 20 )</th>
                    <th colspan="5" >TOTAL <as style="font-size: 11px;"> APROVADOS</as></th>


                </tr>
                <tr>


                    <th>M</th>
                    <td>%</td>
                    <th>F</th>
                    <td>%</td>
                    <th>T</th>

                    <th>M</th>
                    <td>%</td>
                    <th>F</th>
                    <td>%</td>
                    <th>T</th>


                    <th>M</th>
                    <td>%</td>
                    <th>F</th>
                    <td>%</td>
                    <th>T</th>


                    <th>M</th>
                    <td>%</td>
                    <th>F</th>
                    <td>%</td>
                    <th>T</th>


                    <th>M</th>
                    <td>%</td>
                    <th>F</th>
                    <td>%</td>
                    <th>T</th>

                </tr>
            </thead>

            <tbody>

                <tr>

                    <td>{{ $estatistica_tabela['escala']['thirst']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['thirst']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['thirst']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['thirst']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['thirst']['T'] }}</td>


                    <td>{{ $estatistica_tabela['escala']['fourth']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['fourth']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['fourth']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['fourth']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['fourth']['T'] }}</td>


                    <td>{{ $estatistica_tabela['escala']['fiveth']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['fiveth']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['fiveth']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['fiveth']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['fiveth']['T'] }}</td>


                    <td>{{ $estatistica_tabela['escala']['sixth']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['sixth']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['sixth']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['sixth']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['sixth']['T'] }}</td>



                    <td>{{ $estatistica_tabela['total']['aprovados_masculino'] }}</td>
                    <td>{{ $estatistica_tabela['total']['aprovados'] != 0 ? (int) round(($estatistica_tabela['total']['aprovados_masculino'] / $estatistica_tabela['total']['aprovados']) * 100, 0) : 0 }}
                        %</td>
                    <td>{{ $estatistica_tabela['total']['aprovados_femenino'] }}</td>

                    <td>{{ $estatistica_tabela['total']['aprovados'] != 0 ? (int) round(($estatistica_tabela['total']['aprovados_femenino'] / $estatistica_tabela['total']['aprovados']) * 100, 0) : 0 }}
                        %</td>
                    <td>{{ $estatistica_tabela['total']['aprovados'] }}</td>



                </tr>


            </tbody>
        </table>
      
        <table cellspacing="0" id="tabela-reprovados" class="table_pauta_estatistica table-hover dark">
            <thead>
                <tr>

                    <th colspan="15">REPROVADOS</th>

                </tr>
                <tr>

                    <th colspan="5">( 0-6 )</th>
                    <th colspan="5">( 7-9 )</th>
                    <th colspan="5">TOTAL <as style="font-size: 11px;"> REPROVADOS</as></th>



                </tr>
                <tr>



                    <th>M</th>
                    <th>%</th>
                    <th>F</th>
                    <th>%</th>
                    <th>T</th>

                    <th>M</th>
                    <th>%</th>
                    <th>F</th>
                    <th>%</th>
                    <th>T</th>

                    <th>M</th>
                    <th>%</th>
                    <th>F</th>
                    <th>%</th>
                    <th>T</th>

                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>{{ $estatistica_tabela['escala']['first']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['first']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['first']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['first']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['first']['T'] }}</td>


                    <td>{{ $estatistica_tabela['escala']['second']['M'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['second']['Percent_M'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['second']['F'] }}</td>
                    <td>{{ $estatistica_tabela['escala']['second']['Percent_F'] }}%</td>
                    <td>{{ $estatistica_tabela['escala']['second']['T'] }}</td>

                    <td>{{ $estatistica_tabela['total']['reprovados_masculino'] }}</td>
                    <td>{{ $estatistica_tabela['total']['reprovados'] != 0 ? (int) round(($estatistica_tabela['total']['reprovados_masculino'] / $estatistica_tabela['total']['reprovados']) * 100, 0) : 0 }}
                        %</td>
                    <td>{{ $estatistica_tabela['total']['reprovados_femenino'] }}</td>

                    <td>{{ $estatistica_tabela['total']['reprovados'] != 0 ? (int) round(($estatistica_tabela['total']['reprovados_femenino'] / $estatistica_tabela['total']['reprovados']) * 100, 0) : 0 }}
                        %</td>
                    <td>{{ $estatistica_tabela['total']['reprovados'] }}</td>

                </tr>


            </tbody>
        </table>



        <table cellspacing="0" id="tabela-total" class="table_pauta_estatistica table-hover dark">
            <thead>
                <tr>
                    <th colspan="5">TOTAL <as style="font-size: 11px;">AVALIADOS</as></th>
                </tr>
                <tr>
                    <th colspan="5" style="color: transparent; background-color: white!important"> . </th>
                </tr>
                <tr>



                    <th>M</th>
                    <th>%</th>
                    <th>F</th>
                    <th>%</th>
                    <th>T</th>





                </tr>

            </thead>

            <tbody>

                <tr>
                    <td>{{ $H = $estatistica_tabela['total']['aprovados_masculino'] + $estatistica_tabela['total']['reprovados_masculino'] }}
                    </td>
                    <td>{{ $estatistica_tabela['total']['total'] != 0 ? (int) round(($H / $estatistica_tabela['total']['total']) * 100, 0) : 0 }}%
                    </td>
                    <td>{{ $F = $estatistica_tabela['total']['aprovados_femenino'] + $estatistica_tabela['total']['reprovados_femenino'] }}
                    </td>
                    <td>{{ $estatistica_tabela['total']['total'] != 0 ? (int) round(($F / $estatistica_tabela['total']['total']) * 100, 0) : 0 }}%
                    </td>
                    <td>{{ $estatistica_tabela['total']['total'] }}</td>
                </tr>


            </tbody>
        </table>
        @endif

      

        <br>
        
        
            
            <div class="col-12">
            </br>
            </br>
            
            <table class="table-borderless">
                <thead style="text-align:left:">
                    <th colspan="2" style="font-size: 9pt;">
                        
                    </th> 
                 
                </thead>
                <tbody>                   
                    <tr>
                         <td style="font-size: 15pt; font-weight:bold;  padding-bottom:17px; "><b></b>Assinaturas:</b></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                       <tr>
                     
                        <td style="font-size: 10pt; ">Docente(s):<br><br>
                        
                          @foreach($utilizadores as $criador)
                          ________________________________________________________________________ 
                         {{$criador->criador_fullname}} - ({{$criador->metricas=="Neen"?"Exame":$criador->metricas}})<br><br>
                         
                          @endforeach
                        </td> 
                       
                      
                        
                        
                        
                       


                        <td style="font-size: 10pt; ; color: white;">_____________________


                 
                        
                        <td style="font-size: 10pt; "><br>Coordenador do curso:<br><br> ________________________________________________________________________ <br>{{$coordenador_publicou}}<br><br><br>
                       
                    </tr>
                
                    </tr>
                </tbody>
            </table>
        </div>
       
        {{-- @include('Avaliations::avaliacao-aluno.reports.pdf_partials') --}}
    </main>

@endsection

<script>
    // window.print();
</script>
