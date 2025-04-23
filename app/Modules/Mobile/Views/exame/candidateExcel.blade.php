

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
                                   
                             <tr>    
                                <th class="text-center"style="font-size: 15pt; padding: 0px;font-weight:bold;">Curso</th>
                                <th class="text-center"style="font-size: 15pt; padding: 0px;font-weight:bold;">Total candidato(s)</th>
                            </tr>
                             <tr>
                                 <td class="text-center" >{{$curso}}</td>

                                  @php
                                  $count=0;
                                  @endphp

                                 @foreach ($model as $curso)
                                 @if ($curso->state=="total")
                                        @php
                                        $count++;
                                        @endphp
                                 @endif
                                  @endforeach  
                                        <td class="text-center" >{{$count}}</td>  
                            </tr>
                          
                    </table>
                </div>
            </div>
  
                             @php
                                   $i=1;
                             @endphp
                 
                            <table>
                          
                              <tr>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;font-weight:bold;">#</th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;font-weight:bold;">Nome completo</th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;font-weight:bold;">e-mail</th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;font-weight:bold;">Nº do candidato</th>
                                <th class="text-center"style="font-size: 15pt; padding: 0px;font-weight:bold;">Nota</th>
                                <th class="text-center" style="font-size: 15pt; padding: 0px;font-weight:bold;">Estado</th>
                             </tr>

                            @php
                                $i=1;
                                 $flag=false;
                            @endphp
                            @foreach ($model as $item)
                            @if($item->state=="total")
                            <tr>    
                               <td class="text-left">{{$i++}}</td> 
                               <td class="text-left">{{$item->name_completo}}</td> 
                               <td class="text-left">{{$item->email}}</td> 
                               <td class="text-left">{{$item->cand_number==null? "N/A": $item->cand_number}}</td> 
                            
                             @foreach($notas as $nota)
                               @if($item->id==$nota->student_id)
                               <td class="text-center">{{$nota->value}}</td>

                               <td class="text-center">{{$nota->value>9? "Admitido(a)":"Não admitido(a)"}}</td> 
                               @else
                                  @php
                                    $flag=false;
                                   @endphp
                               @endif
                            @endforeach
                            
                            </tr>


                            @endif
                            @endforeach
                            </table>   
                        
                           
                     
    
  


