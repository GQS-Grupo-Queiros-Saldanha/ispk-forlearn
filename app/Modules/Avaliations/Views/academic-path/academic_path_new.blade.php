@extends('layouts.print')
@section('title',__('Percurso académico'))
@section('content')



    <main>
        @php
            $doc_name = 'PERCURSO ACADÉMICO';
            $discipline_code = '';
            $logotipo = "https://".$_SERVER['HTTP_HOST']."/instituicao-arquivo/".$institution->logotipo;
        @endphp
        @include('Reports::pdf_model.forLEARN_header')
        <!-- aqui termina o cabeçalho do pdf -->

        <div class="">
            <div class="">
                <div class="row">
                    <div class="col-12 mb-4">
                        <table class="table_te">
                            <style>
                                .table_te {
                                    background-color: #F5F3F3;
                                     !important;
                                    width: 100%;
                                    text-align: right;
                                    font-family: calibri light;
                                    margin-bottom: 6px;
                                    font-size: 12pt;
                                }

                                .cor_linha {
                                    background-color: #999;
                                    color: #000;
                                }

                                .table_te th {
                                    border-left: 1px solid #fff;
                                    border-bottom: 1px solid #fff;
                                    padding: 4px;
                                     !important;
                                    text-align: center;
                                }

                                .table_te td {
                                    border-left: 1px solid #fff;
                                    background-color: #F5F3F3;
                                    border-bottom: 1px solid white;
                                    font-size: 12pt;
                                }

                                .tabble_te thead {}
                            </style>
                            <tr class="bg1">

                                <th class="text-left" style="font-size: 12pt; padding: 0px;">ESTUDANTE</th>
                                <th class="text-center">Nº de matrícula</th>
                                <th class="text-center">BI</th>
                                <th class="text-center">Curso</th>
                            </tr>
                            <tr class="bg2">
                                <td  class="bg2 text-left">{{ $studentInfo->name }}
                                </td>
                                <td  class="bg2 text-center">{{ $studentInfo->number }}</td>
                                <td  class="bg2 text-center">{{ $studentInfo->bi }}</td>
                                <td  class="bg2 text-center">@if (isset($student_course) && ($student_course!="")){{   $student_course }}@else{{ $studentInfo->course}}@endif</td>
                            </tr>
                            <tr>

                                <td class="bg1" style="font-size: 12pt;text-align:center">Filiação</th>
                                <td  class="bg2 text-center" colspan="3">{{ $studentInfo->dad }} e {{ $studentInfo->mam }}</td>

                            </tr>

                        </table>
                    </div>
                </div>
                <!-- personalName -->
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="">
                                @php
                                    $i = 1;
                                @endphp
                                @php $flag = true;
                                                                        $oFlag = true;
                                                                        $areaGeral = 0;
                                                                        $contaGeral = 0;
                                                                        $areaEspecifia = 0;
                                                                        $contaEspecifica = 0;
                                                                        $areaProfissional = 0;
                                                                        $contaProfissional = 0;
                                                                        $index = 1;
                                                                        $soma1 = null;
                                                                        $soma2 = null;
                                                                        $soma3 = null;
                                                                        $soma4 = null;
                                                                        $soma5 = null;
                                                                        $soma6 = null;

                                                                        $count1 = 0;
                                                                        $count2 = 0;
                                                                        $count3 = 0;
                                                                        $count4 = 0;
                                                                        $count5 = 0;
                                                                        $count6 = 0;

                                                                @endphp
                                <table class="table_te">
                                    <tr class="bg1">
                                        <th colspan="7" style="text-align: center; font-size: 12pt;">
                                            <b>UNIDADES CURRICULARES</b>
                                        </th>
                                        @php
                                            $i = 0;

                                       $oldGradesOrder = collect($oldGrades)->sortBy(function ($value, $key) {
                                         if (is_numeric($key)) {
                                             return '0' . $key; // Prioriza chaves numéricas
                                         }
                                        return '1' . $key; // Depois chaves no formato "XX / XX"
                                    })->all();
                                   @endphp


                                        @foreach ($oldGradesOrder as $year => $oldGrade)
                                            @php $i++; @endphp
                                            <th colspan="1">{{ $year }} </th>
                                        @endforeach
                                        @if ($var == 1)
                                            @foreach ($studyPlanEditions as $studyPlanEdition)
                                                <th style="text-align: left;">{{ $studyPlanEdition->lective_year }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                    <tr class="bg2">
                                        <th style="text-align:center;">#</th>
                                        <th style="text-align:center;">Ano</th>
                                        <th style="text-align:center;">S</th>
                                        <th style="text-align:center;">Código</th>
                                        <th style="text-align:center;">Nome</th>
                                        <th style="text-align:center;">CH</th>
                                        <th style="text-align:center;">UC</th>
                                        @foreach ($oldGradesOrder as $year => $oldGrade)
                                            @if ($oldGrade != '')
                                                <th style="width: 70px; text-align:center; font-size:12pt; " colspan="{{ $i }}">
                                                    CLASSIFICAÇÃO
                                                </th>
                                            @break
                                        @endif
                                    @endforeach

                                    @if ($var == 1)
                                        @foreach ($studyPlanEditions as $studyPlanEdition)
                                            @if ($studyPlanEdition != '')
                                                <th style="width: 70px; text-align:center; font-size:12pt;"
                                                    colspan="{{ $i }}">CLASSIFICAÇÃO</th>
                                            @break
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                            <tr class="bg2" style="background-color:white; padding: 3px;color: white;">
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                            </tr>
                            <tr style="background-color:white; padding: 3px;color: white;">
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                                <td style="background-color: white;"></td>
                            </tr>
                            @php
                            $contaDisciplina = 0;
                            $somatorio = 0;
                            $countGrade = 0;
                            @endphp

                               <!-- Dados das disciplinas -->
@foreach ($disciplines as $discipline)
    @php
        $cor = $loop->iteration % 2 === 0 ? 'cor_linha' : '';
    @endphp

    <tr class="{{ $cor }}">
        <!-- Colunas fixas -->
        <td style="text-align: center;">{{ $index }}</td>
        @php $index++; @endphp

        <td style="text-align: center;">{{ $discipline->course_year }}º</td>

        <!-- Semestre -->
        @if (substr($discipline->code, -3, 1) == 'A')
            <td style="text-align: center;">A</td>
        @elseif(substr($discipline->code, -3, 1) == '1')
            <td style="text-align: center;">1</td>
        @elseif(substr($discipline->code, -3, 1) == '2')
            <td style="text-align: center;">2</td>
        @else
            <td style="text-align: center;">-</td>
        @endif

        <td style="text-align: center;">{{ $discipline->code }}</td>
        <td style="text-align: left;">{{ $discipline->name }}</td>

        <!-- Carga horária -->
        @php $cargaEncontrada = false; @endphp
        @foreach ($cargaHoraria as $carga)
            @if ($carga->id_disciplina == $discipline->id)
                <td style="text-align: center;">{{ $carga->hora }}</td>
                @php $cargaEncontrada = true; @endphp
                @break
            @endif
        @endforeach
        @if (!$cargaEncontrada)
            <td style="text-align: center;">-</td>
        @endif

        <!-- UC -->
        <td style="text-align: center;">{{ $discipline->uc ?? '-' }}</td>

        <!-- Notas por ano lectivo -->
        @foreach ($oldGradesOrder as $year => $oldGradex)
            @php $notaEncontrada = false; @endphp
            @foreach ($oldGradex as $oldGrade)
                @if ($oldGrade->discipline_id == $discipline->id)
                    <td class="nota-cell">
                        {{ round($oldGrade->grade) }}
                    </td>
                    @php
                        $notaEncontrada = true;
                        $somatorio += $oldGrade->grade;
                        $countGrade++;

                        // Cálculo das somas por ano
                        switch($discipline->course_year) {
                            case 1: $soma1 += $oldGrade->grade; $count1++; break;
                            case 2: $soma2 += $oldGrade->grade; $count2++; break;
                            case 3: $soma3 += $oldGrade->grade; $count3++; break;
                            case 4: $soma4 += $oldGrade->grade; $count4++; break;
                            case 5: $soma5 += $oldGrade->grade; $count5++; break;
                            case 6: $soma6 += $oldGrade->grade; $count6++; break;
                        }
                    @endphp
                    @break
                @endif
            @endforeach
            @if (!$notaEncontrada)
                <td class="nota-cell">-</td>
            @endif
        @endforeach

        <!-- Notas para studyPlanEditions se aplicável -->
        @if ($var == 1)
            @foreach ($studyPlanEditions as $studyPlanEdition)
                <td class="nota-cell">-</td>
            @endforeach
        @endif
    </tr>
@endforeach


                        </table>
                        <div class="col-4 float-right mt-4 p-0 mb-8">
                            <table class="mediaClass"
                                style=" background-color: #F5F3F3; !important ;width:100%;font-family:calibri light; margin-bottom: 6px; font-size:12pt; padding-left: 2px; ">
                                <tr class="bg1">
                                    <td colspan="6" style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        <b>MÉDIAS POR ANO</b>
                                    </td>
                                </tr>
                                @if(isset($soma1))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        1º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">

                                         {{ number_format($soma1 != 0 ? $soma1 / $count1 : $soma1, 2, ',', '') }}
                                    </td>
                                </tr>
                                @endif
                                 @if(isset($soma2))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        2º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                         {{ number_format($soma2 != 0 ? $soma2 / $count2: $soma2, 2, ',', '') }}
                                    </td>
                                </tr>
                                @endif
                                 @if(isset($soma3))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        3º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        {{ number_format($soma3 != 0 ? $soma3 / $count3 : $soma3, 2, ',', '') }}
                                    </td>
                                </tr>
                                 @endif
                                @if(isset($soma4))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        4º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        {{ number_format($soma4 != 0 ? $soma4 / $count4 : $soma4, 2, ',', '') }}
                                    </td>
                                </tr>
                                 @endif
                                  @if(isset($soma5))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        5º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        {{ number_format($soma5 != 0 ? $soma5 / $count5 : $soma5, 2, ',', '') }}
                                    </td>
                                </tr>
                               @endif
                                  @if(isset($soma6))
                                <tr style="background-color: #F9F2F4;">
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                       6º Ano
                                    </td>
                                    <td style="font-size:12pt;text-align: center; font-size: 12pt;">
                                        {{ number_format($soma6 != 0 ? $soma6 / $count6 : $soma6, 2, ',', '') }}
                                    </td>
                                </tr>
                                 @endif



                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: left;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>

                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="bg2">
                                    <td style="text-align: right; font-size: 12pt;"><b>MÉDIA FINAL DE CURSO</b></td>
                                    <td style="text-align: center ;  font-size: 12pt;">
                                        <b>
                                            @php
                                                $final =  round($somatorio / $countGrade)
                                            @endphp

                                                @if ($final == 0)
                                                    -
                                                @else
                                                    {{ $final }}
                                                @endif
                                        </b>
                                    </td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color:white; padding: 3px;color: white;text-align: center;">
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <br>
                        <br>
                        <br>

                        <div style="margin-left:30px;margin-top:150px;">
                    @if(isset($requerimento))
                            <p>

                    Documento nº {{$requerimento->code ?? 'código doc'}}, liquidado no CP nº {{$recibo ?? 'recibo'}},
                    assinado e autenticado com o carimbo a óleo em uso no {{$institution->abrev}}.
                </p><br><br><br><br>

                @endif

            <div>


                 <p style="font-size: 12pt !important;text-align:left;font-weight:bolder !important;margin-bottom:100px !important;">

                 {{ $institution->provincia }}, aos {{ $dataActual }}
                </p>
                <p>_________________________________________________</p>
                          <p style="font-size: 12pt !important;margin-top:-18px!important;">{{ $direitor->grau_academico ?? 'Grau Académico' }}, <b>{{ $direitor->nome_completo ?? 'Nome completo' }}</b></p>
                          <p style="font-size:11pt !important;margin-top:-24.5px">{{ $direitor->categoria_profissional ?? 'Categoria Profissional' }}</p>
                          <p style="font-size:11pt !important;margin-top:-24px">{{ $direitor->cargo  ?? 'Cargo' }} do {{$institution->abrev}}</p>


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
