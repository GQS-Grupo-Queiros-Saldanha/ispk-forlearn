<title>LISTA DE MATRICULADOS - TURMA</title>
@extends('layouts.print')
@section('content')
    @php
            $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
            $documentoCode_documento = 50;
            $doc_name = 'LISTA DE MATRICULADOS';
            $discipline_code = '';
    @endphp

    <style>
        /* Versão a cores - linhas mais visíveis */
        .table_te {
            border: 2px solid #000 !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }
        
        .table_te th {
            border: 2px solid #333 !important;
            background-color: #f0f0f0 !important;
            padding: 8px 5px !important;
            font-weight: bold !important;
        }
        
        .table_te td {
            border: 1.5px solid #666 !important;
            padding: 6px 4px !important;
        }
        
        .bg1 {
            background-color: #e0e0e0 !important;
            border: 2px solid #333 !important;
        }
        
        .bg2 {
            background-color: #ffffff !important;
            border: 1.5px solid #666 !important;
        }
        
        /* Versão preto e branco para impressão */
        @media print {
            .table_te,
            .table_te th,
            .table_te td,
            .bg1,
            .bg2 {
                border-color: #000 !important;
                border-width: 2px !important;
            }
            
            .table_te th,
            .bg1 {
                background-color: #f5f5f5 !important;
            }
            
            .table_te td,
            .bg2 {
                background-color: #ffffff !important;
            }
        }
        
        /* Melhorias de espaçamento e alinhamento */
        .text-center {
            text-align: center !important;
        }
        
        .text-left {
            text-align: left !important;
            padding-left: 8px !important;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
    </style>

    <main>
        @include('Reports::pdf_model.forLEARN_header_MT')
            <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te" >
                       
                                   
                             <tr class="bg1">    
                                <th class="text-center" >Curso</th>
                                <th class="text-center" >Ano</th>
                                 <th class="text-center" >Ano lectivo</th>
                                <th class="text-center" >Turma</th>
                                <th class="text-center" >Regime</th>
                                <th class="text-center" >Nº de matriculados(s)</th>
                            </tr>
                             <tr class="bg2">
                                 <td class="text-center bg2" >{{$curso}}</td>
                                 <td class="text-center bg2" >{{$ano}}º</td>
                                   <td class="text-center bg2" >
                                          @foreach ($lectiveYears as $anoLectivo)
                                            {{$anoLectivo->currentTranslation->display_name}}
                                            @break
                                          @endforeach
                                  </td>
                                 <td class="text-center bg2" >{{$turmaC}}</td>
                               
                                  @php
                                  $count=0;
                                  @endphp

                                 @foreach ($model as $curso)
                                        @php
                                        $count++;
                                        @endphp
                                 @endforeach  
                                 <td class="text-center bg2" >{{$regime==0?"Frequência":"Exame"}}</td>  
                                 <td class="text-center bg2" >{{$count}}</td>  
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
                                <th class="text-center" style="width:20px!important;font-size: 14pt; padding: 8px 5px;">#</th>
                                <th class="text-center" style="font-size: 14pt; padding: 8px 5px;">Matrícula</th>
                                <th class="text-center" style="font-size: 14pt; padding: 8px 5px;">Nome do(a) estudante</th>
                                <th class="text-center" style="font-size: 14pt; padding: 8px 5px;">e-mail</th>
                                <th class="text-center" style="font-size: 14pt; padding: 8px 5px;">Assinatura</th>
                                <th class="text-center" style="font-size: 14pt; padding: 8px 5px;">Nota</th>
                               
                                
                            </tr>
                            @php
                                $i=1;
                            @endphp
                            @foreach ($model as $item)
                                @if(isset($item->email))
                                    <tr class="bg2">    
                                       <td class="text-center bg2" style="width:20px!important;font-size: 14pt; padding: 6px 4px;">{{$i++}}</td> 
                                       <td class="text-center bg2" style="width:120px;font-size: 14pt; padding: 6px 4px;">{{$item->matricula}}</td> 
                                       <td class="text-left bg2" style="font-size: 14pt; width:425px!important; padding: 6px 8px;">{{$item->student}}</td> 
                                       <td class="text-left bg2" style="width:240px;font-size: 14pt; padding: 6px 8px;">{{$item->email}}</td>  
                                        <td class="text-center bg2" style="font-size: 14pt; padding: 6px 4px;">
                                            <!--<div style="height: 20px; border-bottom: 1px solid #000; margin: 0 10px;"></div>-->
                                        </td>
                                        <td class="text-center bg2" style="width:70px;font-size: 14pt; padding: 6px 4px;"></td>
                                    
                                    </tr>                        
                                @endif

                            @endforeach
                            </table>   
                            </div>
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