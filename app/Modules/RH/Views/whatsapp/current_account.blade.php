<title>{{'Conta corrente | '.$student_info->matricula.' | '.$student_info->full_name.' | '
    .$student_info->lective_year}}
    </title>
@extends('layouts.print')
@section('content')
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'Conta corrente';
        $discipline_code = '';
    @endphp
    <main>
        @include('Reports::pdf_model.forLEARN_header')
        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te">


                            <tr class="bg1">
                                <th class="text-center">Estudante</th>
                                <th class="text-center">Matrícula</th>
                                <th class="text-center">E-mail</th>
                                <th class="text-center">Turma</th>
                                <th class="text-center">Curso</th>
                                <th class="text-center">Ano Lectivo</th>
                            </tr>
                            <tr class="bg2">
                                <td class="text-center bg2">{{ $student_info->full_name }}</td>
                                <td class="text-center bg2">{{ $student_info->matricula }}</td>
                                <td class="text-center bg2">{{ $student_info->email }}</td>
                                <td class="text-center bg2">{{ $student_info->turma }}</td>
                                <td class="text-center bg2">{{ $student_info->course_name }}</td>
                                <td class="text-center bg2">{{ $student_info->lective_year }}</td>
                            </tr>

                        </table>
                    </div>
                </div>
                <!-- personalName -->
                <style>
                    .table_emolumentos tbody td{
                        padding-top: 5px!important;
                        padding-bottom: 5px!important;
                    }
                    .table_emolumentos thead th{
                        padding-top: 5px!important;
                        padding-bottom: 5px!important;
                    }
                  

                  
                </style>
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <table class="table_te table_emolumentos">

                                <thead>
                                    
                                    <tr class="bg1">
                                        <th class="text-center">#</th>
                                        <th class="text-left">Emolumento/Propina</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center" style="width: 200px;">Factura/Recibo nº</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                    $count_articles = 1;
                                @endphp
                                @if(isset($articles))
                                    @foreach ($articles as $item)
                                        <tr class="bg3">
                                            <td class="text-center bg2">{{$count_articles++}}</td>
                                            <td class="text-left bg2">{{ $item->display_name }} {{ isset($item->mes) ? $item->mes : '' }}</td>
                                            <td class="text-center bg2">{{ number_format($item->base_value, 2, ',', '.') }} kz</td>
                                            
                                            <td class="text-center bg2" style="font-size:14px;">
                                                <center>
                                                    @if ($item->status == 'total')
                                                        <span
                                                            class='bg-success p-1 text-white'>PAGO</span>
                                                    @elseif($item->status == 'pending')
                                                        <span class='bg-info p-1'>ESPERA</span>
                                                    @elseif($item->status == 'partial')
                                                        <span class='bg-warning p-1'>PARCIAL</span>
                                                    @endif
                                                </center>
                                            </td>
                                            <td class="text-center bg2">
                                               @if(isset($item->recibo))
                                                {{$item->recibo}}
                                               @else
                                               --
                                               @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                  
                                </tbody>
    
                            </table>
                         

                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                           
<style>
   
    .t-color{
            color:#fc8a17;
        }
</style>
<div class="row">
    
        <div class="col-6 text-left">
            <as style="text-transform: capitalize;"> {{ $institution->municipio }}</as>,
    @php
        $m = date('m');
        $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
        echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
    @endphp 
        </div>
        <div class="col-6 text-right"><span class="t-color"> Powered by</span> <b style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
        </div>
    
</div>

</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
@endsection

<script></script>
