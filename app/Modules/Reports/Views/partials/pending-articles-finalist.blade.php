@php use App\Modules\Reports\Controllers\DocsReportsController; @endphp
<div class="container-fluid" style="padding:0;">
    <div class="row">
        <div class="col-md-12">
                        <style>
                                .tabela_principal{ height: 2px; padding: 0; margin: 0; margin-bottom: 1px; }
                                    .tabela_principal td{ background-color:transparent;font-family: calibri light; }
                                    .tabela_principal thead{border-top: none; border:1px solid  #2c2c2c; padding: 0; color: #fff;}
                                    .tabela_principal thead th{background-color: #2c2c2c; border:1px solid #fff; width: 800px; text-align: center; padding:0px; padding-left:1px;}
                         
                             .tfoot {
                              border-bottom: 1px solid #BCBCBC !important;
                              text-align: right;
                          }
                      </style>
            @php $total = 0; $linha=0; @endphp
            <table class="table  tabela_principal" style="width:100%;">
                <thead  class="">
                    <th  style="font-size:1pc; text-align: center;width:4pc;">#</th>
                    <th  style="font-size:1pc; text-align: center;width: 200px!important;">Nº Matricula</th>
                    <th  style="font-size:1pc; text-align: center;">Estudante </th>
                    <th  style="font-size:1pc; text-align: center;">Curso </th> 
                    {{-- <th  style="font-size:1pc; text-align: center;width: 100px!important;">Turma </th> --}}
                    <th  style="font-size:1pc; text-align: center;">Emolumento / Propina</th>
                    <th style="font-size:1pc; text-align: center;width: 210px!important;">Valor</th>
                    {{-- <th style="font-size:1pc;width:20pc;">Estado do Pagamento</th> --}}
                    <th style="font-size:1pc; text-align: center;">Valor a pagar</th>
                </thead>   <tbody class="tbody-parameter-group">
            @foreach ($emoluments as $matriculation_number => $emolument)
                @foreach ($emolument as $course_name => $emolumen)
                    @foreach ($emolumen as $user_name => $emolumentos)
                        @php $subtotal = 0; $linha++; @endphp
                        {{-- <table class="tabela_emolumento">
                            <style>
                                .tabela_emolumento{margin-bottom: 0px;border-bottom: 1px solid #fff;width: 735px; font-family: calibri light; margin-top:7px;}
                                .tabela_emolumento td{padding-right:0px;}
                                .tabela_emolumento .ce{background-color:#2c2c2c;color:#fff;text-align:left; font-weight: bold; }
                                .cd{background-color:rgb(231, 231, 231);color:#444;text-align:left;font-weight: bold;}
                                .cor_linha{background-color:#f2f2f3;color:#000;}
                            </style>
                            
                            <tr class="thead-parameter-group" style="border:1px solid  #2c2c2c; padding: 0; color: #fff; font-size:0.9pc;">
                                <td class="mr-3" style="background: white;border:none;color: #000">{{$linha}}</td>
                                <td class="ce pl-1 border-right"  colspan="1">Nº Matricula </td>
                                <td class="ce border-right pl-1"colspan="1">Estudante</td>
                                <td class="ce border-right pl-1" colspan="1">Curso</td>
                            </tr>
                            <tr style="font-size: 1.2pc">
                                <td colspan="1" class="cd pl-1" ></td>
                                <td colspan="1" class="cd pl-1" >{{ $matriculation_number }}</td>
                                <td colspan="1" class="cd pl-1" >{{$user_name}}</td>
                                <td colspan="1" class="cd pl-1" >{{ $course_name }}</td>
                                
                            </tr>
                        </table> --}} 

                                      
                          
                                @php $k=1; $i=0; $user=null; $valorApagar=null; @endphp
                                @foreach ($emolumentos as $item)
                                        @foreach ($item as  $emolument)
                                            @php $cor= $k++ % 2 === 0 ? 'cor_linha' : ''; @endphp
                                            @if ($user==null)
                                                @php $i++; $user=$emolument->user_id; @endphp
                                            @elseif($user==$emolument->user_id)
                                                @php $i++; $user=$emolument->user_id; @endphp
                                            @else
                                                @php $i=1;
                                                 $i++; $user=$emolument->user_id; @endphp

                                            @endif
                                            <tr class="{{$cor}}">
                                                <td style="text-align: center;width: 4pc;">{{$i}}</td>
                                                {{-- <td class="td-parameter-column" style="text-align: center;"> {{date('d-m-Y', strtotime($emolument->created_at))}} </td> --}}
                                                <td>{{ $matriculation_number }}</td> 
                                                <td>{{$user_name}}</td> 
                                                <td>{{ $course_name }}</td> 
                                                {{-- <td>{{DocsReportsController::getTurma($emolument->id,$emolument->lective_year,$emolument->year)}}</td>  --}}
                                                <td class="td-parameter-column">{{ $emolument->article_name}} - {{ $emolument->discplina_display_name}} 
                                                    @if($emolument->article_month == 1)
                                                        ( Janeiro {{ $emolument->article_year}} )
                                                        @elseif($emolument->article_month == 2)
                                                            ( Fevereiro {{ $emolument->article_year}} )
                                                        @elseif ( $emolument->article_month == 3)
                                                            ( Março {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 4)
                                                            ( Abril {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 5)
                                                            ( Maio {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 6)
                                                            ( Junho {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 7)
                                                            ( Julho {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 8)
                                                            ( Agosto {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 9)
                                                            ( Setembro {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 10)
                                                            ( Outubro {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 11)
                                                            ( Novembro {{ $emolument->article_year}} )
                                                        @elseif ($emolument->article_month == 12)
                                                            ( Dezembro {{ $emolument->article_year}} )
                                                    @endif
                                                </td>
                                                <td style="text-align: right" class="td-parameter-column"> {{ number_format($emolument->value, 2, ",", ".") }} Kz</td>
                                                {{-- <td style="text-align: right; width: 15pc" class="td-parameter-column"> 
                                                    @if ($emolument->status == "total")
                                                    <span class='bg-success p-1 text-white'>PAGO</span>
                                                    @elseif($emolument->status == "pending")
                                                        <span class='bg-info p-1'>ESPERA</span>
                                                    @elseif($emolument->status == "partial")
                                                        <span class='bg-warning p-1'>PARCIAL</span>
                                                    @elseif($emolument->status ==null)
                                                        <span class='bg-info p-1'>ESPERA</span>
                                                    @endif
                                                    
                                                </td> --}}
                                                <td style="text-align: right" class="td-parameter-column"> 
                                                    @if ($emolument->balance<0)
                                                    @php $valorApagar=-1*($emolument->balance) @endphp
                                                        {{ number_format($valorApagar, 2, ",", ".") }} Kz
                                                    @else
                                                    @php $valorApagar=$emolument->balance @endphp
                                                        {{ number_format($valorApagar, 2, ",", ".") }} Kz
                                                    @endif

                                                    @php $subtotal += $valorApagar; $total += $valorApagar;  @endphp 
                                                </td>
                                                
                                            </tr>
                                       @endforeach
                                @endforeach
                            </tbody>
                            
                                <tr>
                                    <td class="tfoot"></td>
                                    <td class="tfoot"></td>
                                    <td class="tfoot"></td>
                                    <td class="tfoot"></td>
                                    {{-- <td class="tfoot"></td> --}}
                                    <td class="tfoot"></td>
                                    <td class="tfoot"><b></b></td>
                                    <td class="tfoot"><b>Subtotal: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    {{ number_format($subtotal, 2, ",", ".") }} </b>Kz</td>
                                </tr>
                            
                      
                    @endforeach
                @endforeach
            @endforeach  </table>
            <div style="width: 300px; float: right;">
                  </br>  
                <table class="table table-parameter-group" style="width:250px;" width="10px" cellspacing="2">
                    <thead class="">
                        <th style="text-align: center;background-color:#2c2c2c; color:#fff;" class="th-parameter-group">Total</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-size:13pt !important; text-align: right; border-bottom:1px solid !important;"> <b> {{ number_format($total, 2, ",", ".") }}</b> Kz</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
