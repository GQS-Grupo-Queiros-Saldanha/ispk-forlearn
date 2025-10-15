@php use App\Modules\Users\Controllers\CardsController; @endphp
<title>Relatório dos cartões</title>
@extends('layouts.print')
@section('content') 
    @php
            $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
            $documentoCode_documento = 50;
            $doc_name = 'Relatório dos cartões';
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
                       
                                   
                             <tr class="bg1">    
                                <th class="text-center" stle="widht:300px!important;">Curso</th>
                                <th class="text-center" >Ano</th>
                                 <th class="text-center" >Ano lectivo</th>
                                <th class="text-center" >Turma</th>
                                <th class="text-center" >Nº de matriculados(s)</th>
                            </tr>
                             <tr class="bg2">
                                 <td class="text-center bg2" >{{$model[0]->course_name}}</td>
                                 <td class="text-center bg2" >{{$ano}}º</td>
                                   <td class="text-center bg2" >{{$lt->display_name}}</td>
                                 <td class="text-center bg2" >{{$model[0]->turma}}</td>
                                 <td class="text-center bg2" >{{count($model)}}</td>  
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
                                   $i=1;
                             @endphp
                 
                            <table class="table_te">
                          
                              <tr class="bg1">
                                <th class="text-center" style="font-size: 14pt; padding: 0px;width: 10px;">#</th>
                                <th class="text-center" style="font-size: 14pt; padding: 0px;">Matrícula</th>
                                <th class="text-center" style="font-size: 14pt; padding: 0px; width:100px;">Nome do(a) estudante</th>
                                <th class="text-center" style="width: 240px;">e-mail</th>
                                <th class="text-center" style="width: 240px;">Fotografia</th>
                                <th class="text-center">Impressão</th>
                                <th class="text-center">Entrega</th>
                            </tr>
                                @php
                                    $i=1;
                                    $t_entrega=0;
                                    $t_impressao=0;
                                    $t_fotografia=0;
                                @endphp
                            @foreach ($model as $item)
                        
                            <tr class="bg2">    
                               <td class="text-center bg2">{{$i++}}</td> 
                               <td class="text-center bg2" style="width:150px;">{{$item->matricula}}</td> 
                               <td class="text-left bg2" style="width:380px;">{{$item->student}}</td> 
                               <td class="text-left bg2">{{$item->email}}</td> 
                               <td class="text-left bg2" style="width: 100px!important;">
                                @if (isset($item->photo))
                                    <center>sim</center>
                                    @php $t_fotografia=$t_fotografia+1; @endphp
                                @else 
                                    <center>-</center>
                                @endif
                                </td> 
                               <td class="text-center bg2" >
                                    @php 
                                        $impressao = CardsController::verificar_cards($item->matriculation_id,$item->id_anoLectivo)  
                                    @endphp
                                    @if(isset($impressao->data_impressao))    
                                    {{$impressao->data_impressao}}
                                    @php $t_impressao=$t_impressao+1; @endphp
                                    @else
                                    -
                                    @endif
                                </td> 
                               <td class="text-center bg2" >
                                    @php 
                                        $entrega = CardsController::verificar_cards($item->matriculation_id,$item->id_anoLectivo)  
                                    @endphp
                                    @if(isset($entrega->data_entrega))    
                                    {{$entrega->data_entrega}}
                                    @php $t_entrega=$t_entrega+1; @endphp
                                    @else
                                    -
                                    
                                    @endif
                                </td>                             
                           
                            
                            </tr>
                        
                            @endforeach
                            </table>   
                            </div>
                        
                           
                            <table class="table_te" style="width: 900px;margin-left:165px;margin-top:15px;">
                          
                                <tr class="bg1">
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;width: 200px;">Resumo</th>
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;width: 200px;">Nº de estudantes </th>
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;">Fotografia</th>
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;">Imprimido</th>
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;">Entrega</th>
                                  @if(count($cartoes_eliminados)>0)
                                  <th class="text-center" style="font-size: 14pt; padding: 0px;">Matrículas anuladas</th>
                                  @endif
                                  
                                </tr>
                            
                          
                              <tr class="bg2">    
                                 <td class="text-center bg2">{{$model[0]->turma}}</td> 
                                 <td class="text-center bg2">{{count($model)}}</td> 
                                 <td class="text-center bg2">{{$t_fotografia}}</td> 
                                 <td class="text-center bg2">{{$t_impressao}}</td> 
                                 <td class="text-center bg2">{{$t_entrega}}</td> 
                                 @if(count($cartoes_eliminados)>0)
                                 <td class="text-center bg2">{{count($cartoes_eliminados)}}</td> 
                                 @endif
                              </tr>
                              </table>
                            @include('Reports::pdf_model.signature')
                            
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