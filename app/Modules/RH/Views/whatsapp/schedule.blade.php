<title>
    {{ 'Horário | '.$student_info->turma .
        ' | ' .
        $student_info->lective_year }}
</title>
@extends('layouts.print')
@section('content')
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'Horário do estudante';
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
                    .table_emolumentos tbody td {
                        padding-top: 5px !important;
                        padding-bottom: 5px !important;
                    }

                    .table_emolumentos thead th {
                        padding-top: 5px !important;
                        padding-bottom: 5px !important;
                    }
                </style>
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <style>
                                .cell-forlearn {
                                    padding: 5px;
                                    padding-left: 10px;
                                    padding-right: 10px;
                                    margin: 0px;
                                    border-radius: 2px;
                                    border: 10px solid #8eaadb;
                                    border-radius: 7px;
                                    font-size: 18px;
                                    /* font-weight: bold; */
                                    text-align: left;
                                    vertical-align: top;
                            
                                }
                            
                                .cell-forlearn-top {
                                    font-size: 18px;
                                }
                            
                                .bg0 {
                                    background-color: #2f5496 !important;
                                    color: white;
                                }
                            
                                .bg1 {
                                    background-color: #8eaadb !important;
                                }
                            
                                .bg2 {
                                    background-color: #d9e2f3 !important;
                                }
                            
                                .bg3 {
                                    background-color: #fc8a17 !important;
                                    font-weight: bold;
                                }
                            
                                .bg4 {
                                    /* background-color: #00c0ef !important; */
                                    background-color: #d9e2f3 !important;
                                    padding: 5px;
                                    /* border-left: 6px solid #d9e2f3;
                                    border-right: 6px solid #d9e2f3; */
                            
                                }
                            
                                .table-forlearn {
                                    font-size: 18px;
                                    width: 90%;
                                    margin-left: 5%;
                                }
                            
                                .img-forlearn {
                                    height: 28px;
                                    width: 95px;
                                }
                            
                                .span-forlearn{
                                    position: absolute;
                                    bottom: 0;
                                    right: 0;
                                    background-color: #2f5496;
                                    color: white;
                                    padding: 2px 5px;
                                }
                            </style>
                            
                            @if (is_object($horario))
                                @foreach ($horario as $key => $item)
                                    <table class="table-forlearn">
                            
                                        <thead>
                                            @if (is_object($horario))
                                                @foreach ($horario as $key => $item)
                                                    @if (isset($item['classes']))
                                                        <tr>
                                                            <td class="cell-forlearn-top" colspan="6"><img class="img-forlearn"
                                                                    src="https://dev.forlearn.ao/img/login/ForLEARN 03.png" title="Logo forLEARN"
                                                                    alt="Logo forLEARN"></td>
                            
                                                        </tr>
                                                    @else
                                                    @endif
                                                @endforeach
                                            @else
                                            @endif
                                            <tr>
                                                <th class="cell-forlearn bg3" style="text-align: center;">Hora</th>
                                                <th class="cell-forlearn bg0" style="text-align: center;">Segunda<br>Feira</th>
                                                <th class="cell-forlearn bg0" style="text-align: center;">Terça<br>Feira</th>
                                                <th class="cell-forlearn bg0" style="text-align: center;">Quarta<br>Feira</th>
                                                <th class="cell-forlearn bg0" style="text-align: center;">Quinta<br>Feira</th>
                                                <th class="cell-forlearn bg0" style="text-align: center;">Sexta<br>Feira</th>
                                            </tr>
                                        </thead>
                            
                            
                            
                                        @php
                                            $i = 0;
                                            $count = [0, 1, 2, 3, 4, 5];
                                        @endphp
                            
                                        @foreach ($count as $index)
                                            <tr>
                                                {{-- <td class="cell-forlearn bg0" >{{ $index + 1 }}</td> --}}
                                                <td class="cell-forlearn bg3" style="text-align: center;width: 300px!important;">
                                                    @if (isset($tempo[$index]->start))
                                                        {{ $tempo[$index]->start }}
                                                    @endif
                                                    <br>
                                                    @if (isset($tempo[$index]->end))
                                                        {{ $tempo[$index]->end }}
                                                    @endif
                                                </td>
                                                <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                                                    @if (isset($item['segunda'][$index]))
                                                        {{ $item['segunda'][$index] }}
                                                    @endif
                                                    @if (isset($item['segunda_room'][$index]))
                                                        <span class="span-forlearn">{{ $item['segunda_room'][$index] }}</span>
                                                    @endif
                                                </td>
                                                <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                                                    @if (isset($item['terca'][$index]))
                                                        {{ $item['terca'][$index] }}
                                                    @endif
                                                    @if (isset($item['terca_room'][$index]))
                                                        <span class="span-forlearn">{{ $item['terca_room'][$index] }}</span>
                                                    @endif
                                                </td>
                                                <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                                                    @if (isset($item['quarta'][$index]))
                                                        {{ $item['quarta'][$index] }}
                                                    @endif
                                                    @if (isset($item['quarta_room'][$index]))
                                                        <span class="span-forlearn">{{ $item['quarta_room'][$index] }}</span>
                                                    @endif
                                                </td>
                                                <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                                                    @if (isset($item['quinta'][$index]))
                                                        {{ $item['quinta'][$index] }}
                                                    @endif
                                                    @if (isset($item['quinta_room'][$index]))
                                                        <span class="span-forlearn">{{ $item['quinta_room'][$index] }}</span>
                                                    @endif
                                                </td>
                                                <td class="cell-forlearn bg2" style="width: 300px!important; position: relative;">
                                                    @if (isset($item['sexta'][$index]))
                                                        {{ $item['sexta'][$index] }}
                                                    @endif
                                                    @if (isset($item['sexta_room'][$index]))
                                                        <span class="span-forlearn">{{ $item['sexta_room'][$index] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                            
                                        <tbody>
                            
                                        </tbody>
                                    </table>
                                    <br><br>
                                @endforeach
                            @endif
                            @if (!is_object($horario))
                                <div class="alert alert-warning text-dark font-bold">
                                    {{ $horario }}</div>
                            @endif
                             


                            <br>
                            <br>

                            @include('Reports::pdf_model.signature')

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
