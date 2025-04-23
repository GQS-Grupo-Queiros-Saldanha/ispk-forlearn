@extends('layouts.print')
@section('content')

@php
$logotipo = $logotipo;
$documentoCode_documento = 50;
$doc_name = "Pauta de ".$discipline_name;
@endphp


<style>
    .table_te,
    .table_pauta {
        background-color: #F5F3F3;
        !important;
        width: 100%;
        text-align: right;
        font-family: calibri light;
        margin-bottom: 6px;
    }

    .table_pauta_estatistica {
        background-color: #F5F3F3;
        !important;
        width: 100%;
        text-align: right;
        font-family: calibri light;
        margin-bottom: 6px;
        border: none;
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
    }

    .table_te th,
    .table_pauta th {
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
        padding: 4px;
        !important;
        text-align: center;
    }

    .table_pauta_estatistica th {
        border-left: 1px solid #fff;
        border-bottom: 1px solid #fff;
        padding: 4px;
        !important;
        text-align: center;
    }

    .table_te td,
    .table_pauta td {
        border-left: 1px solid #fff;
        background-color: #F9F2F4;
    }

    .table_pauta_estatistica td {
        border-left: 1px solid #fff;
        background-color: #F9F2F4;
    }

    .table_pauta_estatistica tr {
        border-bottom: 1px solid #fff;
    }

    .table_pauta_estatistica thead {}

    .tabble thead {}

    #corpoTabela tr td {
        font-size: 12pt;
    }

    #chega th {
        font-size: 13pt;
        font-weight: bold;
    }

    .c_final {
        font-size: 13pt;
        font-weight: bold;
    }

    .table_pauta thead tr {
        background-color: #8eaadb !important;
        padding-top: 3px;
        padding-bottom: 3px;
    }

    .table_pauta td {
        padding-top: 3px;
        padding-bottom: 3px;
        font-size: 16px !important;
        border: 1px solid white;
        background-color: #d9e2f3 !important;
    }

    .table_pauta tr td:nth-child(4) {
        text-align: center !important;
    }
</style>
<main>

    @include('Reports::pdf_model.forLEARN_header')
    <table class="table_te">

        <thead style="border:none;" id="chega">
            <tr class="bg1">

                <th>DISCIPLINA</th>
                <th>CURSO</th>
                <th>ANO CURRICULAR</th>
                <th>ANO LECTIVO</th>
                <th>REGIME</th>
                <th>TURMA</th>

        </thead>




        <tbody id="corpoTabela">
            <tr class="bg2">
                <td class="text-center bg2">
                    {{$discipline_code}}
                </td>

                <td class="text-center bg2">
                    {{$curso}}
                </td>
                {{--<td class="text-center bg2">{{$lectiveYear[0]->display_name}}</td>--}}
                <td class="text-center bg2">
                    {{$ano_curricular }}
                </td>
                <td class="text-center bg2">
                    {{$lectiveYear}}
                </td>
                <td class="text-center bg2">
                    {{$regimeFinal}}
                </td>
                <td class="text-center bg2">
                    {{$turma}}
                </td>
                @isset($prova)
                <td class="text-center bg2">
                    {{$prova}}
                </td>
                @endisset
            </tr>
        </tbody>
    </table>


    {{-- Começa aqui a tabela --}}
    <table class="table_te">
        @php

        $sc = '';
        if($segunda_chamada)
        $sc = 'SEGUNDA CHAMADA';

        @endphp

        <thead style="border:none;" id="chega">
            <tr class="bg1">

                <th>#</th>
                <th>MATRÍCULA</th>
                <th>ESTUDANTE</th>

                @if($code_dev == "PF1")
                <th>CLASSIFICAÇÃO PF1 <br> {{ $sc }}</th>

                @elseif($code_dev == "PF2")
                <th>CLASSIFICAÇÃO PF2 <br> {{ $sc }}</th>

                @elseif($code_dev == "OA")
                <th>CLASSIFICAÇÃO OA</th>
                @elseif($code_dev == "Neen")
                <th>CLASSIFICAÇÃO DO EXAME ESCRITO <br> {{ $sc }}</th>

                @elseif($code_dev == "Recurso")
                <th>CLASSIFICAÇÃO DO RECURSO</th>

                @elseif($code_dev == "Exame_especial")
                <th>CLASSIFICAÇÃO DO EXAME ESPECIAL</th>

                @elseif($code_dev == "Extraordinario")
                <th>CLASSIFICAÇÃO DO EXAME EXTRAORDINÁRIO</th>
                @endif
            </tr>

        </thead>



        @php $index = 1;@endphp
        @foreach($students as $student)
        <tbody id="corpoTabela">
            <tr class="bg2">
                <td class="text-center bg2">
                    {{$index}}
                    @php $index++ @endphp
                </td>
                <td class="text-center bg2">
                    {{$student->mat}}
                </td>
                <td class="text-left bg2">
                    {{$student->nome}}
                </td>

                <td class="text-center bg2">
                    {{$student->grade ?? 'F'}}
                </td>

            </tr>
        </tbody>
        @endforeach

    </table>
    {{-- termina aqui --}}

    <br>


    <div class="col-12">
        </br>
        </br>

        <table class="table-borderless">
            <thead style="text-align:left:">
                <th colspan="2" style="font-size: 9pt;">

                </th>

            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 15pt; font-weight:bold;  padding-bottom:17px; "><b></b>Assinaturas:</b></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                <tr>

                    <td style="font-size: 10pt; ">Docente(s):<br><br>

                        @foreach($utilizadores as $criador)
                        @php
                        $prof = $criador->criado_por;
                        @endphp
                        ________________________________________________________________________
                        <br>
                        {{$criador->criador_fullname}} - ({{$criador->metricas=="Neen"?"Exame":$criador->metricas}})<br><br>
                        Criado a: {{$criador->criado_a}}
                        @endforeach
                    </td>

                    @php
                    $lancou_coordenador = false;
                    $exists = false;
                    if(count($coordenadores) > 1){
                    $exists = true;
                    $lancou_coordenador = $coordenadores[0]->actualizado_por == $prof;
                    }
                    @endphp
                    <br>
                    @if($exists & !$lancou_coordenador)
                    <td style="font-size: 10pt; ">Coordenador(es):<br><br>

                        @foreach($coordenadores as $coordenador)
                        ________________________________________________________________________
                        <br>
                        {{$coordenador->actualizador_fullname}}<br><br>
                        Actualizado a: {{$coordenador->actualizado_a}}
                        @endforeach
                    </td>
                    @endif

                </tr>

                </tr>
            </tbody>
        </table>
    </div>

</main>

@endsection