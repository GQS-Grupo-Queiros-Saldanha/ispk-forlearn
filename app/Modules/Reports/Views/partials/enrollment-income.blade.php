<div class="container-fluid" style="padding:0;">
    <div class="row">
        <div class="col-md-12">
            @php $subtotal = 0; $ano=date('Y'); @endphp

            @php $total = 0; @endphp

            @foreach ($emoluments as $course => $emoluments)
            @foreach ($emoluments as $course_id => $emoluments)
            @php $subtotal = 0;  @endphp

            <table class="tabela_emolumento" >
                  <style>
                        table { page-break-inside:auto }
                         tr    { page-break-inside:avoid; page-break-after:auto }
                        thead { display:table-header-group }
                        tfoot { display:table-footer-group ;color:#000;}
                        
                        .tabela_emolumento{margin-bottom: 1px;border-bottom: 1px solid #fff;width: 620px; font-family: calibri light;}
                        .tabela_emolumento td{padding-right:0px;}
                        .tabela_emolumento .ce{background-color:#2c2c2c;color:#fff;text-align:left; font-weight: bold; }
                        .cd{background-color:rgb(231, 231, 231);color:#444;text-align:left;font-weight: bold;}
                   </style>

                  <tr>
                      <td class="ce">Emolumento</td>
                      <td class="ce">Curso</td>
                     
                  </tr>
                  <tr>
                      <td  class="cd" style=" ">{{ $course }}</td>
                      <td  class="cd" style="">{{ $course_id }}</td>
                  </tr>
                  </table>
            <table class="table table-parameter-group  tabela_principal" >
                <style>.tabela_principal{ height: 30px; padding: 0; margin: 0; margin-bottom: 1px; }
                       .tabela_principal td{ background-color:transparent;font-family: calibri light; }
                       .tabela_principal .thead{border:1px solid  #2c2c2c; padding: 0; color: #fff;}
                       .tabela_principal .thead th{background-color: #2c2c2c; 
                        border:1px solid #fff; width: 1000px; text-align: center; padding:0px; padding-left:1px;}
                </style>

                <tr class="thead" style="font-family: calibri light;">
                    <th style="font-size:10pt; width:300px;">#</th>
                    <th style="font-size:10pt;">Matricula</th>
                    <th style="font-size:10pt; width:1800px;">Nome do aluno</th>
                    <th style="font-size:10pt; width:300px;">Ano</th>
                    <th style="font-size:10pt;">Banco</th>
                    <th style="font-size:10pt;">Referência</th>
                    <th style="font-size:10pt;">Data</th>
                    <th style="font-size:10pt;">Utilizador</th>
                    <th style="font-size:10pt;">Nº de recibo</th>
                    <th style="font-size:10pt;">Valor</th>
                </tr>
                <tbody class="corpo_td">
            <style>.corpo_td td{font-family: calibri light;font-size:80%; color:#444; border:none; padding: 0; background-color:transparent;}
                   .cor_linha{background-color:#f2f2f3;color:#000;}
            </style>
           
           @php
           $i=1;
           $count=1;
           @endphp

            @foreach ($emoluments as $emolument)
                    @php
                    $cor = $i++ % 2 === 0 ? 'cor_linha':''; 
                    @endphp    
                    <tr class="{{$cor}}">
                        <td style="text-align: center;" class="td-parameter-column"> {{ $count++}} </td>
                        <td class=""> {{ $emolument->matriculation_number }} </td>
                        <td class="" >{{ $emolument->full_name }}</td>
                        <td style="text-align: center;" class="td-parameter-column"> {{ $emolument->course_year }} º </td>
                        <td class="column"> {{ $emolument->bank_name }} </td>
                        <td class=""> {{ $emolument->reference }}</td>
                        <td class="" style="text-align: center;"> {{date('d-m-Y', strtotime($emolument->fulfilled_at))}}</td>
                        <td class=""> {{ $emolument->created_by_user }}</td>
                        <td class="" style="text-align: center;"> {{ $emolument->recibo }}</td>
                        <td style="text-align: right" class="td-parameter-column"> {{ number_format($emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia, 2, ".", ",") }} @php $subtotal += $emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia  ; $total += $emolument->valorreferencia >= $emolument->price ? $emolument->price : $emolument->valorreferencia;  @endphp </td>
                    </tr>
             @endforeach


                 <tr style="border:none;">
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td style="border-bottom: 1px solid#fff;"></td>
                     <td></td>
                     <td style="border-bottom: 1px solid#fff;"></td>
                     <td class="tfoot"><b>Sub-total: </b></td>
                     <td class="tfoot"><b>{{ number_format($subtotal, 2, ".", ",") }}</b></td>
                 </tr>




                </tbody>
               
        
       </table>
       
       @endforeach
       @endforeach
       
       <br>
            <div style="width: 300px; float: right; font-family: calibri light;">
                <table class="table table-parameter-group" style="width:200px;" width="10px" cellspacing="2">
                    <thead class="thead-parameter-group">
                        <th style="text-align: center" class="th-parameter-group">Total</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-size:13pt !important; text-align: right; border-bottom:1px solid !important;"> <b> {{ number_format($total, 2, ".", ",") }} </b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>