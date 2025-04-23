@extends('layouts.print')
@section('title',__('Anúncio de vagas'))
@section('content')
   <head>
  
     <style>

    
    .logotipo img {
                width: 400px!important;
                height: 400px!important;
                margin-top: 90px!important;
                 margin-bottom: 90px!important;
            }
    .titulo {
        margin-bottom: 300px;
        margin-top: 200px!important;
        text-align: left;
        text-indent: 20px;
        
    }
    
    .titulo .a {
        color: #243f60!important;
        
    }
    
    .logotipo {
        margin: 500px 0 90px 170px;
    }
    .cabecalho .instituition:first-child {
        font-size: 48px!important;

            
    }
    .cabecalho .decreto {
        font-size: 1rem!important;
        text-indent: 20px!important; 
    }
    .cabecalho .area {
        padding-top:0px;
        display: none;
    }
    .titulo h1 {
        font-size: 32px!important;
        color: #243f60!important;   
    }
    
    .titulo .fase,
    .titulo .ano {
        font-weight: normal!important;
        color:#fc8a17!important;
        /* text-indent: 100px!important; */
    }
    .titulo p:not(:first-child){
        font-size: 27px!important;
        font-weight: 700!important;
        color: #243f60!important;
        text-transform: initial!important;  
    }
    .titulo p:first-child{
        font-size: 60px!important;
      
    }
    
    .row{
        margin-top:1500px !important;
         width:1175px !important;
    }
    
    .table_te{
       
        /*margin-left:-25px !important;*/
    }
    
        .instituition {
      text-align:left !important;
      /*font-size: 1000px!important;*/
     
    }
    
    .decreto {
        text-align:left; margin-left:-17px !important; 
    }
       </style>
   </head> 
   


        <main style="width:900px!important;">

        {{-- @include('Reports::pdf_model.pdf_header') --}}
        @include('Reports::declaration.cabecalho.geral')
        <!-- aqui termina o cabeçalho do pdf -->
        
        
        <div class="">
            <div class="">

                <!-- personalName -->

                <div class="row">
                    <div class="col-12">
                        
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                @endphp

                                <table class="table_te">

                                 
                                    @php
                                      
                                        $m = 0;
                                        $t = 0;
                                        $n = 0;
                                    @endphp
                                    @foreach ($vagas as $key => $item)
                                    @php
                                        $i = 1;
                                        $m_p = 0;
                                        $t_p = 0;
                                        $n_p = 0;
                                    @endphp
                                     
                                    <tr class="line">
                                        <td colspan="6" class="text-left text-white bg0 text-uppercase font-weight-bold f1"> {{ $key }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-center bg-white " style="vertical-align:bottom;" rowspan="2"></th>
                                        <th class="text-center bg-white " style="vertical-align:bottom;"></th>
                                        <th class="text-center bg1 font-weight-bold f2" colspan="4">Vagas</th>

                                    </tr>
                                    <tr>

                                        <th class="text-center bg1 font-weight-bold f2" style="vertical-align:bottom;"  >CURSOS</th>
                                        <th class="text-center bg1 font-weight-bold f3 pd" style="">M</th>
                                        <th class="text-center bg1 font-weight-bold f3 pd" style="">T</th>
                                        <th class="text-center bg1 font-weight-bold f3 pd" style="">N</th>
                                        <th class="text-center bg1 font-weight-bold f3 pd1" style="">Total</th>
 
                                    </tr>
                                        @foreach ($item as $item_vagas)
                                            <tr class="f2">  
                                                <td class="text-right bg2 ">{{ $i++ }}</td>
                                                <td class="text-left bg2 text-uppercase ">{{ $item_vagas->display_name }}</td>
                                                <td class="text-center bg2 ">{{ $item_vagas->manha != 0 ? $item_vagas->manha : '-' }}</td>
                                                <td class="text-center bg2 ">{{ $item_vagas->tarde != 0 ? $item_vagas->tarde : '-' }}</td>
                                                <td class="text-center bg2 ">{{ $item_vagas->noite != 0 ? $item_vagas->noite : '-' }}</td>
                                                <td class="text-center bg2 ">
                                                    {{ $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha != 0 ? $item_vagas->noite + $item_vagas->tarde + $item_vagas->manha : '-' }}
                                                </td>
                                            </tr> 
                                            @php
                                                $m += $item_vagas->manha;
                                                $t += $item_vagas->tarde;
                                                $n += $item_vagas->noite;
                                                $m_p += $item_vagas->manha;
                                                $t_p += $item_vagas->tarde;
                                                $n_p += $item_vagas->noite;
                                            @endphp
                                        @endforeach
                                        <tr class="last-line font-weight-bold">
                                            <td class="bg-white f4" colspan="2"><b>SUB-TOTAL</b></td>
                                            <td class="text-center bg3 f3">{{ $m_p }}</td>
                                            <td class="text-center bg3 f3">{{ $t_p }}</td>
                                            <td class="text-center bg3 f3">{{ $n_p }}</td>
                                            <td class="text-center bg3 f3">{{ $m_p + $t_p + $n_p }}</td>
                                        </tr> 
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>
                                       
                                        
                                    @endforeach
                                    <tr>
                                        <td class="bg-white"></td>
                                    </tr>
                                    <tr class="last-line">
                                        <td class="bg-white f1" colspan="2"><b>TOTAL</b></td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $m }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $n }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold strange">{{ $m + $t + $n }}</td>
                                    </tr> 
                                </table>
                            </div>
                            <br>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
   
    <div class="data" style="margin-top: 50px; !important">
        <b>
            <as style="text-transform: initial;"> {{ $institution->municipio }}</as>,
            aos
            @php
                $m = date('m');
                $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
            @endphp.
            <div>
                {{-- <titles class="t-color">Powered by</titles> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b> --}}

            </div>
        </b>
    </div>
    <br><br>

    
        <div class="assinaturas">
            <p style="margin-bottom: 70px; !important">O/A {{ $vd_academica_role_name }} do <b>{{ $institution->abrev }}</b></p>
            <p>______________________________</p>
            <p  style="margin-top: -15px; !important"><b>{{ $cordenador->nome ?? 'Nome'}}</b> / {{ $cordenador->abreviacao ?? 'Abrev Grau Acad.'}}</p>
            <p style="margin-top: -15px; !important">{{ $cordenador->categoria ?? 'Categoria Profissional' }}</p>
        </div>
        </div>
    
@endsection
