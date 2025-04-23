@extends('layouts.print')
@section('content')

<link>

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
            font-size:2em;
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

        .div-top {
            height:99px;
            text-transform: uppercase;
            position: relative;
            /* border-top: 1px solid #000;
            border-bottom: 1px solid #000; */
            margin-bottom: 15px;
            background-color: rgb(240, 240, 240);
            background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');
            /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/
            background-position: 100%;
            background-repeat: no-repeat;
            background-size: 63%;
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



        .pl-1 {
            padding-left: 1rem !important;
        }
          table     { page-break-inside:auto }
               tr    { page-break-inside:avoid; page-break-after:auto }
               thead { display:table-header-group }
               tfoot { display:table-footer-group }

    </style>
    <main>
        <div class="div-top" style="">
            <table class="table m-0 p-0">
                <tr>
                
                 
                    </td>
                     <td rowspan="12" style=" width:12px;">
                     </td>
                    <td class="">
                        <h1 class="h1-title">
                          Estatísticas de candidaturas
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td class="">
                        <span class="" rowspan="1">
                        @foreach ($lectiveYears as $lectiveYear_T)
                           <b>Ano Acadêmico:{{$lectiveYear_T->currentTranslation->display_name}}</b>
                           <br>
                           <b>Fase:  {{isset($lectiveFase->fase) ? $lectiveFase->fase : '.' }}</b>
                            @break
                        @endforeach

                            
                        </span>
                    </td>
                </tr>
               <tr>
                    <td class="">
                        <span class="" rowspan="1" style="color:rgb(240, 240, 240);">
                           <b> <b>-----------------</b></b>
                            <b>

                            </b>
                        </span>
                    </td>
                </tr>
                 <tr>
                    <td class="">
                        <span class="" rowspan="1">
                            Documento gerado a
                            <b>
                                <b>{{ Carbon\Carbon::now()->format('d/m/Y') }}</b>
                            </b>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
            <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
        <div class="">
            <div class="row">
                <div class="col-12 mb-4">
                    <table class="table_te" >
                        <style>
                            .table_te{
                            background-color: #F5F3F3; !important ;width:100%;text-align:right;font-family:calibri light; margin-bottom: 6px; font-size:14pt;}
                            .cor_linha{background-color:#999;color:#000;}
                             .table_te th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;padding: 4px; !important; text-align:center; font-size:18pt;font-weight:bold;}
                            .table_te td{border-left:1px solid #fff;background-color: #F9F2F4;border-bottom: 1px solid white; font-size:14pt;}
                            .tabble_te thead{}
                        </style>
                                   
                             {{-- <tr>    
                                <th class="text-center" >.</th>
                                <th class="text-center" >.</th>
                                <th class="text-center" >.</th>
                            </tr>
                             <tr>
                                 <td class="text-center" style="background-color: #F9F2F4;">.</td>
       
                                 <td class="text-center" style="background-color: #F9F2F4;">.</td>
                               
                                  @php
                                  $count=0;
                                  @endphp

                                 <td class="text-center" style="background-color: #F9F2F4;">.</td>  
                            </tr> --}}
                          
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
                                   $n_cand=[];
                                   $vagas_manha=0;
                                   $vagas_tarde=0;
                                   $vagas_noite=0;
                                   $vagas_tarde;
                                   $vagas_noite;
                                   $manhaT=0;
                                        
                             @endphp
                    
                            <table class="table_te">

                             @foreach ($vagas as $item)
                                @php
                                    $vagas_manha+=$item->manha;
                                    $vagas_tarde+=$item->tarde;
                                    $vagas_noite+=$item->noite;
                                @endphp 

                                <tr>
                                    <th colspan="2"></th>
                                    <th>Manhã</th>
                                    <th>Tarde</th>
                                    <th>Noite</th>
                                    <th>Total</th>
                                  </tr>
                                  <tr>
                                      <td rowspan="7" class="text-center">{{$item->display_name }}</td>
                                      <td class="text-left">Vagas</td>
                                      <td><b>{{$item->manha !=0?$item->manha:"-"}}</b></td> 
                                      <td>{{$item->tarde !=0?$item->tarde:"-" }}</td> 
                                      <td>{{$item->noite !=0?$item->noite:"-" }}</td> 
                                      <td>{{$item->noite+$item->tarde+$item->manha!=0?$item->noite+$item->tarde+$item->manha:"-"}}</td> 
                                  </tr>
                                  
                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id ==$esta->id_curso&& $esta->categoria=="NC") 
                                  <tr>
                                      <td class="text-left"> Nº de candidatos</td>
                                      <td>{{$esta->manha}}</td>
                                      <td>{{$esta->tarde}}</td>
                                      <td>{{$esta->noite}}</td>
                                      <td>{{$esta->manha+$esta->tarde+$esta->noite}}</td>    
                                 </tr>
                                    @endif   
                                    @endforeach


                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id ==$esta->id_curso&& $esta->categoria=="AD") 
                                  <tr>
                                    <td class="text-left">Admitido ao exame</td>
                                      <td>{{$esta->manha}}</td>
                                      <td>{{$esta->tarde}}</td>
                                      <td>{{$esta->noite}}</td>
                                      <td>{{$esta->manha+$esta->tarde+$esta->noite}}</td>    
                                 </tr>
                                    @endif   
                                    @endforeach
                                    
                                
                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id ==$esta->id_curso&& $esta->categoria=="NP") 
                                  <tr>
                                    <td class="text-left">Nº de Provas</td>
                                      <td>{{$esta->manha}}</td>
                                      <td>{{$esta->tarde}}</td>
                                      <td>{{$esta->noite}}</td>
                                      <td>{{$esta->manha+$esta->tarde+$esta->noite}}</td>    
                                 </tr>
                                    @endif   
                                    @endforeach
                                    


                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id ==$esta->id_curso&& $esta->categoria=="ADP") 
                               
                                  <tr>
                                    <td class="text-left">Admitidos</td>
                                      <td>{{$esta->manha>=$item->manha? $item->manha:$esta->manha}}</td>
                                      <td>{{$esta->tarde>=$item->tarde ? $esta->tarde-$item->tarde:$esta->tarde}}</td>
                                      <td>{{$esta->noite>=$item->noite? $esta->noite-$item->noite:$esta->noite }}</td>
                                      <td>{{($esta->manha>=$item->manha? $item->manha:$esta->manha)+($esta->tarde>=$item->tarde ? $esta->tarde-$item->tarde:$esta->tarde)+($esta->noite>=$item->noite? $esta->noite-$item->noite:$esta->noite ) }}</td>    
                                  </tr>
                                    @endif   
                                    @endforeach


                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id==$esta->id_curso && $esta->categoria=="ADP") 
                                  <tr>
                                    <td class="text-left">Suplentes</td>
                                      <td>{{ $esta->manha>=$item->manha? $esta->manha-$item->manha:0 }}</td>
                                      <td>{{ $esta->tarde>=$item->tarde ? $esta->tarde-$item->tarde:0 }}</td>
                                      <td>{{ $esta->noite>=$item->noite? $esta->noite-$item->noite:0 }}</td>
                                      <td>{{($esta->manha>=$item->manha? $esta->manha-$item->manha:0)+($esta->tarde>=$item->tarde ? $esta->tarde-$item->tarde:0)+($esta->noite>=$item->noite? $esta->noite-$item->noite:0) }}</td>    
                                  </tr>
                                  @endif 

                                  @endforeach

                                  @foreach ($estatistica as $esta) 
                                  @if ($item->course_id ==$esta->id_curso&& $esta->categoria=="ND") 
                                  <tr>
                                    <td class="text-left">Não Admitidos</td>
                                      <td>{{$esta->manha}}</td>
                                      <td>{{$esta->tarde}}</td>
                                      <td>{{$esta->noite}}</td>
                                      <td>{{$esta->manha+$esta->tarde+$esta->noite}}</td>    
                                 </tr>
                                    @endif   
                                    @endforeach
                                    
                            
                        
                              
                            
                              @endforeach   

                          





                            @php
                                $NC=0;
                                $NT=0;
                                $NN=0;

                                $AM=0;
                                $AT=0;
                                $AN=0;

                                $NP=0;
                                $NPT=0;
                                $NPN=0;

                                $ADP=0;
                                $ADPT=0;
                                $ADPN=0;


                                $ND=0;
                                $NDT=0;
                                $NDN=0;

                            @endphp

                              @foreach ($estatistica as $esta) 
                              @if ($esta->categoria=="NC" ) 
                                 @php
                                    //Candidados geral
                                     $NC+=$esta->manha;
                                     $NT+=$esta->tarde;
                                     $NN+=$esta->noite;
                                     
                                 @endphp
                              @endif   
                              @if ($esta->categoria=="AD" ) 
                                 @php
                                    //Candidados geral
                                     $AM+=$esta->manha;
                                     $AT+=$esta->tarde;
                                     $AN+=$esta->noite;
                                     
                                 @endphp
                              @endif   
                              @if ($esta->categoria=="NP") 
                                 @php
                                    //Candidados geral
                                     $NP+=$esta->manha;
                                     $NPT+=$esta->tarde;
                                     $NPN+=$esta->noite;
                                     
                                 @endphp
                              @endif   
                              @if ($esta->categoria=="ADP") 
                                 @php
                                    //Candidados geral
                                     $ADP+=$esta->manha;
                                     $ADPT+=$esta->tarde;
                                     $ADPN+=$esta->noite;
                                     
                                 @endphp
                              @endif   
                              @if ($esta->categoria=="ND") 
                                 @php
                                    //Candidados geral
                                     $ND+=$esta->manha;
                                     $NDT+=$esta->tarde;
                                     $NDN+=$esta->noite;
                                     
                                 @endphp
                              @endif   
                              @endforeach
                              
                              <tr>
                                <th colspan="2"></th>
                                <th>Manhã</th>
                                <th>Tarde</th>
                                <th>Noite</th>
                                <th>Total</th>
                              </tr>
                              <tr>
                                  <td rowspan="7" class="text-center">Dados gerais</td>
                                  <td class="text-left">Vagas</td>
                                  <td><b>{{$vagas_manha}}</b></td> 
                                  <td>{{$vagas_tarde }}</td> 
                                  <td>{{$vagas_noite}}</td> 
                                  <td>{{$vagas_tarde+$vagas_noite+$vagas_manha}}</td> 
                              </tr>
                              
                              <tr>
                                  <td class="text-left"> Nº de candidatos</td>
                                  <td>{{$NC}}</td>
                                  <td>{{$NT}}</td>
                                  <td>{{$NN}}</td>
                                  <td>{{$NC+$NT+$NN}}</td>    
                             </tr>


                          
                              <tr>
                                <td class="text-left">Admitido ao exame</td>
                                  <td>{{$AM}}</td>
                                  <td>{{$AT}}</td>
                                  <td>{{$AN}}</td>
                                  <td>{{$AM+$AT+$AN}}</td>    
                             </tr>
                              
                                
                            
                         
                              <tr>
                                <td class="text-left">Nº de Provas</td>
                                  <td>{{$NP}}</td>
                                  <td>{{$NPT}}</td>
                                  <td>{{$NPN}}</td>
                                  <td>{{$NP+$NPT+$NPN}}</td>    
                             </tr>
                          


                           
                              <tr>
                                <td class="text-left">Admitidos</td>
                                  <td>{{$ADP>=$vagas_manha?$vagas_manha:$ADP }}</td>
                                  <td>{{$ADPT>=$vagas_tarde?$vagas_tarde:$ADPT }}</td>
                                  <td>{{$ADPN>=$vagas_noite?$vagas_noite:$ADPN}}</td>
                                  <td>{{($ADP>=$vagas_manha?$vagas_manha:$ADP)+($ADPT>$vagas_tarde?$vagas_tarde:$ADPT)+($ADPN>$vagas_noite?$vagas_noite:$ADPN)}}</td>    
                             </tr>
                               

              
                              <tr>
                                <td class="text-left">Suplentes</td>
                                <td>{{$ADP>=$vagas_manha?$ADP-$vagas_manha:0 }}</td>
                                <td>{{$ADPT>=$vagas_tarde?$ADPT-$vagas_tarde:0 }}</td>
                                <td>{{$ADPN>=$vagas_noite?$ADPN-$vagas_noite:0}}</td>
                                <td>{{($ADP>=$vagas_manha?$ADP-$vagas_manha:0 )+($ADPT>=$vagas_tarde?$ADPT-$vagas_tarde:0 )+($ADPN>=$vagas_noite?$ADPN-$vagas_noite:0)}}</td>   
                              </tr>
                             

                      
                              <tr>
                                  <td class="text-left">Não Admitidos</td>
                                  <td>{{$ND}}</td>
                                  <td>{{$NDT}}</td>
                                  <td>{{$NDN}}</td>
                                  <td>{{$ND+$NDT+$NDN}}</td>    
                               </tr>
                           

                            </table>   
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                                    <div class="">
                                    <br>
                                    <br>
                                    <table class="table-borderless">
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
                                                <td style="font-size: 12pt;color:white; ">A Chefe do DAAC:<br><br> ________________________________________________________________________</td>
                                                <td style="font-size: 12pt; ; color: white;">_____________________
                                                <td style="font-size: 12pt; ">O(a) Vice-Director(a) Acadêmico, : <br><br>____________________________________________________________________<br>
                                                 <p>{{$cordenador}}/ MSc</p>
                                                </td>
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
