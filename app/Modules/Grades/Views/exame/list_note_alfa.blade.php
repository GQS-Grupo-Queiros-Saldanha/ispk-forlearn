<title>Pauta de exame de acesso | forLEARN</title>
@extends('layouts.print')
@section('content')



@php
$logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
$documentoCode_documento = 50;
$doc_name = 'PAUTA DE EXAME DE ACESSO';
$discipline_code = '';
@endphp
    
    <main>
        
        @include('Reports::pdf_model.forLEARN_header')

        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te" >
                        <style>
                          
                        </style>
                                   
                             <tr class="bg1">    
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Curso</th>
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Ano lectivo</th>
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Fase</th>
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Turma</th>
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Total candidato(s)</th>
                                <th class="text-center" style="font-size:15pt;font-weight:bold;">Total vagas(s)</th>
                            </tr>
                             <tr class="bg2">
                          
                                 <td class="text-center bg2" style="">{{$curso}}</td>
                                 <td class="text-center bg2" style="">
                                    @foreach ($model as $anoLectivo)
                                    <b> {{$anoLectivo->lective_year_code}}</b>
                                    @break
                                    @endforeach
                                 </td>
                                 <td class="text-center bg2" style="">
                                    <b> {{isset($lectiveCandidate->fase) ? $lectiveCandidate->fase : '.' }}</b>
                                 </td>
                                 <td class="text-center bg2" style="">{{$turmaC}}</td>
                                
                                
                                  @php
                                  $count=0;
                                  @endphp

                                 @foreach ($estudantes as $curso)
                                
                                        @php
                                        $count++;
                                        @endphp
                                 
                                  @endforeach  
                                        <td class="text-center bg2" style="">{{$count}}</td>  
                                        <td class="text-center bg2" style="">{{$vagas_number}}</td>
                            </tr>
                          
                    </table>
                </div>
            </div>
            <!-- personalName -->

            <div class="row">
                <div class="col-12">
                    <div class="">
                        <div class="">
                             @php
                                   $tamanho=0;
                                   $d=0;   
                                   $i=1;
                             @endphp
                           
                            <table class="table_te">
                          
                              <tr class="bg1">
                                <th class="text-center" style="font-size: 15pt; padding: 0px; ">#</th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;">Nº do candidato </th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;">Nome completo</th>
                                <th class="text-center">e-mail</th>
                                
                                @php
                                $count_d=0;
                                $count_d_a=0;
                                $count=0;
                                
                                $flag=false;
                                @endphp

                                
                         
                                @foreach ($estudantes as $dis)
                                @php
                                    $count_d_a++;
                                @endphp
                                @if ($count_d_a==1)
                                @foreach ($disciplines as $item)
                                    @if ($dis['id_disciplina_a']==$item->id)
                                        <th class="text-center" style="font-size: 15pt; padding: 0px;">{{$item->disciplina}}</th>   
                                    @endif
                                @endforeach
                                @foreach ($disciplines as $item)
                                    @if ($dis['id_disciplina_b']==$item->id)
                                        <th class="text-center" style="font-size: 15pt; padding: 0px;">{{$item->disciplina ?? '-' }}</th>   
                                    @endif
                                @endforeach

                                @break
                                @endif

                                @endforeach
                             
                                   
                                <th class="text-center" style="font-size: 15pt;">Média</th>
                              
                                <th class="text-center" style="font-size: 15pt;">Estado</th>
                                
                            </tr>
                           
                            @foreach ($estudantes as $item)
                                 @php
                                     $count++;
                                 @endphp
                                <tr class="">    
                                <td class="text-center bg2">{{$i++}}     </td>
                                <td class="text-left bg2">  {{$item['cand']==null? "N/A": $item['cand']}}</td>  
                                <td class="text-left bg2">  {{$item['nome']}}</td> 
                                <td class="text-left bg2">  {{$item['email']}}</td> 
                                
                                <td class="text-center bg2">{{$item['nota_a'] ?? '-' }}</td>
                                
                                <td class="text-center bg2">{{$item['nota_b'] ?? '-' }}</td>
                                @isset($item['nota_b'])
                                    <td class="text-center bg2">{{ !isset($item['resultado']) ? '-' : $item['resultado'] }}</td> 
                                @else
                                    <td class="text-center bg2">-</td>                     
                                @endisset
                                @if ($count>$vagas_number&& $item['resultado']>9)
                                @php
                                  $count =$vagas_number+1;
                                @endphp        
                                <td class="text-center bg2">Suplente</td> 
                                @else
                                <td class="text-center bg2">{{$item['estado']}}</td> 
                                @endif

                                </tr>
                            @endforeach
                            </table>   
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="data" style="text-align: left; font-size: 12pt;">

                                <as style="text-transform: capitalize;"> {{ $institution->municipio }}</as>,
                                aos
                                @php
                                    $m = date('m');
                                    $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                                    echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
                                @endphp



                                <br>
                                <titles class="t-color">Powered by</titles> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
                            </div>
                               <div class="">
                               </br>
                               </br>

                               <table class="table-borderless" style="opacity: 0;">
                                   <thead style="text-align:left:">
                                        <th colspan="2" style="font-size: 12pt;">
                                        </th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                            </tr>
                                            <tr>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <tr>
                                                <td style="font-size: 12pt;color:white; ">A Chefe do DAAC:<br><br> ________________________________________________________________________   </td>
                                                <td style="font-size: 12pt; ; color: white;">_____________________
                                                <td style="font-size: 12pt; ">A comissão de acesso: <br><br>____________________________________________________________________</td>
                                            </tr>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         </div>
    </main>

@endsection

<script>
</script>
