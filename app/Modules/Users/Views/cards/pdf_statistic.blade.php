 <title>{{'Estatística dos cartões de estudantes_'.$lt->display_name}}</title>
 @extends('layouts.print')
@section('content')

    <head>
       
        <style>
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .titulo{
                margin-bottom:10px;
            }
            .decreto{
                font-size: 0.7rem!important;
                padding-left: 70px!important;

            }
            .instituition{
                font-size: 30px!important;
                
            }

            .
        </style>
    </head>
    <main>
        @php
            $title1 = 'Análise estatística dos cartões de estudantes';
            $title2 = 'Ano lectivo ( ' . $lt->display_name . ' )';
            $area = '';
        @endphp
        @include('Reports::declaration.cabecalho.others')
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
                                <center>
                                <table class="table_te" style="width: 780px!important;">


                                    @php
                                        $t1 = 0;
                                        $t2 = 0;
                                        $t3 = 0;
                                        $t4 = 0;
                                    @endphp


                                    @php
                                        $i = 1;
                                        $m_p = 0;
                                        $t_p = 0;
                                        $n_p = 0;
                                        $count_break = 0
                                    @endphp
                                    @foreach ($courses as $key => $item)
                                        @php
                                            $sub_t1 = 0;
                                            $sub_t2 = 0;
                                            $sub_t3 = 0;
                                            $sub_t4 = 0;
                                            $count_break = $count_break+1;
                                        @endphp
                                        <tr class="line">
                                            <td colspan="5"
                                                class="text-left text-white bg0 text-uppercase font-weight-bold f1">
                                                {{ $key }}</td>
                                        </tr>

                                        <tr>
                                            <th class="text-center bg1 font-weight-bold f2">Turmas</th>
                                            <th class="text-center bg1 font-weight-bold f2">Nº de estudantes</th>
                                            <th class="text-center bg1 font-weight-bold f2">Fotografia</th>
                                            <th class="text-center bg1 font-weight-bold f2">Imprimido</th>
                                            <th class="text-center bg1 font-weight-bold f2">Entregas</th>

                                        </tr>
                                        @foreach ($item as $turma)
                                            <tr>
                                                <th class="text-center bg2 font-weight-bold f3 pd">
                                                    {{ $turma['turma'] }}</th>
                                                <th class="text-center bg2 font-weight-bold f3 pd">
                                                    {{ $turma['total'] }}</th>
                                                <th class="text-center bg2 font-weight-bold f3 pd">
                                                    {{ $turma['fotografia'] }}</th>
                                                <th class="text-center bg2 font-weight-bold f3 pd">
                                                    {{ $turma['imprimido'] }}</th>
                                                <th class="text-center bg2 font-weight-bold f3 pd">
                                                    {{ $turma['entrega'] }}</th>

                                            </tr>
                                            @php
                                                $sub_t1 += $turma['total'];
                                                $sub_t2 += $turma['fotografia'];
                                                $sub_t3 += $turma['imprimido'];
                                                $sub_t4 += $turma['entrega'];

                                                $t1 += $turma['total'];
                                                $t2 += $turma['fotografia'];
                                                $t3 += $turma['imprimido'];
                                                $t4 += $turma['entrega'];

                                            @endphp
                                        @endforeach


                                        <tr class="last-line font-weight-bold">
                                            <td class="bg-white f4"><b>SUB-TOTAL</b></td>
                                            <td class="text-center bg3 f3">{{ $sub_t1 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t2 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t3 }}</td>
                                            <td class="text-center bg3 f3">{{ $sub_t4 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>
                                        @if ($count_break==5)
                                        
                                        <tr> 
                                            <td class="bg-white"><br><p style="color:white;padding-top:12px;">_</p></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>
                                        <tr> 
                                            <td class="bg-white"><br><p style="color:white;padding-top:12px;">_</p></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>

                                        <tr> 
                                            <td class="bg-white"><br><p style="color:white;padding-top:12px;">_</p></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>
                                        <tr> 
                                            <td class="bg-white"><br><p style="color:white;padding-top:12px;">_</p></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-white"></td>
                                        </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td class="bg-white"></td>
                                    </tr>



                                    <tr>
                                        <td class="bg-white"></td>
                                    </tr>
                                    <tr class="last-line">
                                        <td class="bg-white f1" ><b>TOTAL</b></td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t1 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t2 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t3 }}</td>
                                        <td class="text-center bg4 f3 font-weight-bold">{{ $t4 }}</td>
                                    </tr>
                                </table>
                                </center>
                                <br>
                                @if(($card_total-$t3)>0)
                                  <div style="font-size:17px;margin-left:108px;"><b>Nota:</b> A forLEARN detectou ({{(+$card_total-$t3)}}) cartões imprimidos associados a matrículas anuladas.</div>
                                @endif
                                
                            </div>
                            <br>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        
        .signature-forlearn{
            margin-left:92px;
            width: 810px!important;
        }
        
        .sign-date p{
            font-size: 17px!important;
        }
        
    </style>
    @include('Reports::pdf_model.signature')
    
@endsection
