<title>LISTA DE MATRICULADOS - TURMA</title>
@extends('layouts.print')
@section('content')
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/storage/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'LISTA DE MATRICULADOS';
        $discipline_code = '';
    @endphp

    <style>
        /* =========================================================
           ESTILOS GERAIS DA TABELA
        ========================================================= */
        .table_te {
            border: 2px solid #000 !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .table_te th {
            border: 2px solid #333 !important;
            background-color: #f0f0f0 !important;
            padding: 8px 5px !important;
            font-weight: bold !important;
        }

        .table_te td {
            border: 1.5px solid #666 !important;
            padding: 6px 4px !important;
        }

        .bg1 {
            background-color: #e0e0e0 !important;
            border: 2px solid #333 !important;
        }

        .bg2 {
            background-color: #ffffff !important;
            border: 1.5px solid #666 !important;
        }

        /* =========================================================
           MODO DE IMPRESSÃO (PRETO E BRANCO)
        ========================================================= */
        @media print {
            .table_te,
            .table_te th,
            .table_te td,
            .bg1,
            .bg2 {
                border-color: #000 !important;
                border-width: 2px !important;
            }

            .table_te th,
            .bg1 {
                background-color: #f5f5f5 !important;
            }

            .table_te td,
            .bg2 {
                background-color: #ffffff !important;
            }
        }

        /* =========================================================
           ALINHAMENTOS E ESPAÇAMENTO
        ========================================================= */
        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
            padding-left: 8px !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        /* =========================================================
           OUTROS
        ========================================================= */
        .cadeirante {
            background-color: yellow !important;
        }
    </style>

    <main>
        @include('Reports::pdf_model.forLEARN_header')
        <!-- aqui termina o cabeçalho do pdf -->
        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te">
                            <tr class="bg1">
                                <th class="text-center">Curso</th>
                                <th class="text-center">Ano</th>
                                <th class="text-center">Ano lectivo</th>
                                <th class="text-center">Disciplina</th>
                                <th class="text-center">Turma</th>
                                <th class="text-center">Regime</th>
                                <th class="text-center">Nº de matriculados(s)</th>
                            </tr>

                            <tr class="bg2">
                                <td class="text-center bg2">{{$curso}}</td>
                                <td class="text-center bg2">{{$ano}}º</td>
                                <td class="text-center bg2">
                                    @foreach ($lectiveYears as $anoLectivo)
                                        {{$anoLectivo->currentTranslation->display_name}}
                                        @break
                                    @endforeach
                                </td>
                                <td class="text-center bg2">{{$nome_disciplina}}</td>
                                <td class="text-center bg2">{{$turmaC}}</td>

                                @php
                                    $count = 0;
                                @endphp

                                @foreach ($model as $curso)
                                    @php
                                        $count++;
                                    @endphp
                                @endforeach

                                <td class="text-center bg2">{{$regime==0 ? "Frequência" : "Exame"}}</td>
                                <td class="text-center bg2">{{$count}}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- LISTA DE ESTUDANTES -->
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                    $cadeirantes = collect();
                                @endphp

                                <table class="table_te">
                                    <tr class="bg1">
                                        <th class="text-center" style="font-size: 14pt;">#</th>
                                        <th class="text-center" style="font-size: 14pt;">Matrícula</th>
                                        <th class="text-center" style="font-size: 14pt;">Nome do(a) estudante</th>
                                        <th class="text-center" style="width:210px;">E-mail</th>
                                        <th class="text-center">Assinatura</th>
                                        <th class="text-center">Nota</th>
                                    </tr>

                                    @foreach ($model as $item)
                                        @php
                                            if($item->cadeirante) {
                                                $cadeirantes->push($item);
                                                continue;
                                            }
                                        @endphp

                                        @if(isset($item->email))
                                            <tr class="bg2">
                                                <td class="text-center bg2" style="font-size: 14pt;">{{$i++}}</td>
                                                <td class="text-center bg2" style="width:150px;font-size: 14pt;">{{$item->matricula}}</td>
                                                <td id={{$i}} class="text-left bg2" style="width:390px;font-size: 14pt;">{{$item->student}}</td>
                                                <td class="text-left bg2" style="width:270px;font-size: 14pt;">{{$item->email}}</td>
                                                <td class="text-left bg2" style="font-size: 14pt;width:400px;"></td>
                                                <td class="text-left bg2" style="width:70px;font-size: 14pt;"></td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @if(!$cadeirantes->isEmpty())
                                        <tr class="bg2">
                                            <td class="text-center" style="font-size: 14pt;" colspan="6"><b>Cadeiras em atraso</b></td>
                                        </tr>

                                        @foreach ($cadeirantes as $item)
                                            <tr class="bg2 cadeirante">
                                                <td class="text-center bg2" style="font-size: 14pt;">{{$i++}}</td>
                                                <td class="text-center bg2" style="width:150px;font-size: 14pt;">{{$item->matricula}}</td>
                                                <td id={{$i}} class="text-left bg2" style="width:390px;font-size: 14pt;">{{$item->student}}</td>
                                                <td class="text-left bg2" style="width:270px;font-size: 14pt;">{{$item->email}}</td>
                                                <td class="text-left bg2" style="font-size: 14pt;width:400px;"></td>
                                                <td class="text-left bg2" style="width:70px;font-size: 14pt;"></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>

                            @include('Reports::pdf_model.signature')
                        </div>
                    </div>
                </div>
            </div>
            <div class="signature">
                Docente(s):<br>
                __________________________________
               <br>
                ({{ $metrica }})
            </div>
        </div>
    </main>
@endsection

<script></script>
