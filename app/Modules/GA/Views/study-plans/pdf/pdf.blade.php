{{-- @include('GA::budget.pdf.cabecalho') --}}



@extends('layouts.print')
<title>Plano de Estudo | forLEARN</title>
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tinos:ital,wght@0,400;0,700;1,400;1,700&display=swap');


        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }


        table {
            page-break-inside: auto;

        }

        #table-study thead{
            display: table-row-group;
        }
        
        thead {
            display: table-header-group;
            
            border: 1px solid rgba(0,0,0,0);
        }

        tfoot {
            display: table-footer-group
        }

        .corpo-element {
            margin-left: 15px;
            margin-right: 15px;
        }

        p{
            width: 100%;
            margin: 0px;
            padding: 0px;
            border-bottom: 1px solid #999;
            
        }

        tbody tr td{
            vertical-align: middle!important;
            font-weight: 700;
        }
        #table-study tbody tr:nth-child(2n+1){
            background-color: rgb(241, 241, 241);
        }

        .f_td{
            text-align: center;
            width: 80px;
        }
    </style>
    <main>
        @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'Plano de estudo';
        $discipline_code = '';
        @endphp

        @include('Reports::pdf_model.forLEARN_header')
        <!-- aqui termina o cabeçalho do pdf -->
        <div class="corpo-element">


            <h3 style="margin-left: 15px;">
                <b>

                    Curso: {{ $plano[0]->nome_plano }}
                </b>
            </h3>
            <br>
            <div class="row">
                        <table id="table-study" class="table">
                            <thead>
                            <tr class="bg1">
                                <th class="f_td">#</th>
                                <th>Disciplinas</th>
                                <th>Período</th>
                                <th class="f_td">Ano</th>
                                <th>Carga horária</th>
                                <th>Regimes</th>
                                <th>Horas</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($plano as $item)
                                {{-- <tr style="background-color: transparent;color:transparent; font-size:5px;">
                                    <td colspan="100">a</td>
                                </tr> --}}
                                    <tr>
                                        <td class="f_td">{{$i++}}</td>
                                        <td>#{{$item->code_disci}} - {{$item->nome_disciplina}}</td>
                                        <td>{{$item->period_nome}}</td>
                                        <td class="f_td">{{$item->ano}}</td>
                                        <td class="f_td">{{$item->total}}</td>
                                        <td>
                                            @foreach ($plano_regime as $pr)
                                                    @if($pr->id==$item->st_has_d_id)
                                                        <p>{{$pr->codigo}}</p>
                                                    @endif
                                            @endforeach                                            
                                        </td> 
                                        <td class="f_td">
                                            @foreach ($plano_regime as $pr)
                                            @if($pr->id==$item->st_has_d_id)
                                            <p>{{$pr->horas}}</p>
                                            @endif
                                            @endforeach 
                                        </td> 
                                    </tr>
                                   
                                @endforeach 
                            </tbody>
                        </table>

                       
            </div>
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

        </div>


        </div>

    </main>
@endsection
