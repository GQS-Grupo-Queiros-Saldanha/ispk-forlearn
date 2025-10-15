@extends('layouts.print')
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
                                    font-size: 14pt;
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
                                    font-size: 14pt;
                                }

                                .tabble_te thead {}
                            </style>
                            <tr class="bg1">
                                <th class="text-left" style="font-size: 15pt; padding: 0px;">ESTUDANTE</th>
                                <th class="text-center">Nº de matrícula</th>
                                <th class="text-center">e-mail</th>
                                <th class="text-center">Curso</th>
                            </tr>
                            <tr class="bg2">
                                <td  class="bg2 text-left">{{ $studentInfo->name }}
                                </td>
                                <td  class="bg2 text-center">{{ $studentInfo->number }}</td>
                                <td  class="bg2 text-center">{{ $studentInfo->email }}</td>
                                <td  class="bg2 text-center">@if (isset($student_course) && ($student_course!="")){{   $student_course }}@else{{ $studentInfo->course}}@endif</td>

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
                                                                @endphp 
                                <table class="table_te">
                                    <tr class="bg1">
                                        <th colspan="6" style="text-align: center; font-size: 15pt;"><b>UNIDADES
                                                CURRICULARES</b></th>
                                        @php
                                            $i = 0;
                                        @endphp
                                        @foreach ($oldGrades as $year => $oldGrade)
                                            @php
                                                $i++;
                                            @endphp
                                            <th colspan="1">{{ $year }}</th>
                                        @endforeach
                                        @if ($var == 1)
                                            @foreach ($studyPlanEditions as $studyPlanEdition)
                                                <th style="text-align: left;">{{ $studyPlanEdition->lective_year }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                    <tr class="bg2">
                                        <th style="text-align:center;">Ano</th>
                                        <th style="text-align:center;">Regime</th>
                                        <th style="text-align:center;">Código</th>
                                        <th style="text-align:center;">Nome</th>
                                        <th style="text-align:center;">Área</th>
                                        <th style="text-align:center;">Carga Horária</th>
                                        @foreach ($oldGrades as $year => $oldGrade)
                                            @if ($oldGrade != '')
                                                <th style="width: 70px; text-align:center; font-size:15pt; "
                                                    colspan="{{ $i }}">CLASSIFICAÇÃO</th>
                                            @break
                                        @endif
                                    @endforeach

                                    @if ($var == 1)
                                        @foreach ($studyPlanEditions as $studyPlanEdition)
                                            @if ($studyPlanEdition != '')
                                                <th style="width: 70px; text-align:center; font-size:15pt;"
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
                            @php $contaDisciplina = 0;@endphp
                            @isset($outDisciplines) 
                                @foreach ($disciplines as $discipline)
                                    @if (!in_array($discipline->id, $outDisciplines, true))
                                        @php
                                            $cor = $i++ % 2 === 0 ? 'cor_linha' : '';
                                        @endphp
                                        <tr class="{{ $cor }}">
                                            <td style="text-align:center;">{{ $discipline->course_year }} º</td>
                                            @if (substr($discipline->code, -3, 1) == 'A')
                                                <td style="text-align: center;">A</td>
                                            @elseif(substr($discipline->code, -3, 1) == '1')
                                                <td style="text-align: center;">1</td>
                                            @elseif(substr($discipline->code, -3, 1) == '2')
                                                <td style="text-align: center;">2</td>
                                            @endif
                                            <td style="text-align: center;">{{ $discipline->code }}</td>
                                            <td style="text-align: left;">{{ $discipline->name }}
                                                @php $contaDisciplina++; @endphp
                                            </td>
                                            <td style="text-align: left;">{{ $discipline->area }} </td>


                                            @foreach ($cargaHoraria as $carga)
                                                @if ($carga->id_disciplina == $discipline->id)
                                                    <td style="text-align: center;">{{ $carga->hora }} </td>
                                                @endif
                                            @endforeach

                                            @foreach ($oldGrades as $year => $oldGradex)
                                                @php $flag = true @endphp
                                                @php $oFlag = true; @endphp
                                                @foreach ($oldGradex as $oldGrade)
                                                    @if ($oldGrade->discipline_id == $discipline->id)
                                                        @php $flag = false @endphp

                                                        @if ($discipline->area_id == 13)
                                                            @php
                                                                $areaGeral += $oldGrade->grade;
                                                                $contaGeral++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 14)
                                                            @php
                                                                $areaProfissional += $oldGrade->grade;
                                                                $contaProfissional++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 15)
                                                            @php
                                                                $areaEspecifia += $oldGrade->grade;
                                                                $contaEspecifica++;
                                                            @endphp
                                                        @endif
                                                        <td style="text-align: center;background-color: #F9F2F4;">
                                                            {{ round($oldGrade->grade) }}</td>
                                                    @endif
                                                @endforeach
                                                @if ($flag)
                                                    <td style="background-color: #F9F2F4;"> </td>
                                                @endif
                                            @endforeach
                                            @if ($var == 1)
                                                @foreach ($grades as $grade)
                                                    @php $oFlag = true @endphp
                                                    {{-- aqui falta comparar o id do pl ano de edicao de estudo, caso a disciplina acarretar negativa --}}
                                                    @if ($grade->discipline_id == $discipline->id)
                                                        @if ($discipline->area_id == 13)
                                                            @php
                                                                $areaGeral += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaGeral++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 14)
                                                            @php
                                                                $areaProfissional += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaProfissional++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 15)
                                                            @php
                                                                $areaEspecifia += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaEspecifica++;
                                                            @endphp
                                                        @endif
                                                        @php $oFlag = false @endphp
                                                        <td style="text-align: center; background-color: #F9F2F4;">
                                                            {{ round($grade->percentage_mac + $grade->percentage_neen) }}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach ($disciplines as $discipline)
                                    @if (in_array($discipline->id, $outDisciplines, true))
                                        @php
                                            $cor = $i++ % 2 === 0 ? 'cor_linha' : '';
                                        @endphp
                                        <tr class="{{ $cor }}">
                                            <td style="text-align:center;">{{ $discipline->course_year }} º</td>
                                            @if (substr($discipline->code, -3, 1) == 'A')
                                                <td style="text-align: center;">A</td>
                                            @elseif(substr($discipline->code, -3, 1) == '1')
                                                <td style="text-align: center;">1</td>
                                            @elseif(substr($discipline->code, -3, 1) == '2')
                                                <td style="text-align: center;">2</td>
                                            @endif
                                            <td style="text-align: center;">{{ $discipline->code }}</td>
                                            <td style="text-align: left;">{{ $discipline->name }}
                                                @php $contaDisciplina++; @endphp
                                            </td>
                                            <td style="text-align: left;">{{ $discipline->area }} </td>


                                            @foreach ($cargaHoraria as $carga)
                                                @if ($carga->id_disciplina == $discipline->id)
                                                    <td style="text-align: center;">{{ $carga->hora }} </td>
                                                @endif
                                            @endforeach

                                            @foreach ($oldGrades as $year => $oldGradex)
                                                @php $flag = true @endphp
                                                @php $oFlag = true; @endphp
                                                @foreach ($oldGradex as $oldGrade)
                                                    @if ($oldGrade->discipline_id == $discipline->id)
                                                        @php $flag = false @endphp

                                                        @if ($discipline->area_id == 13)
                                                            @php
                                                                $areaGeral += $oldGrade->grade;
                                                                $contaGeral++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 14)
                                                            @php
                                                                $areaProfissional += $oldGrade->grade;
                                                                $contaProfissional++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 15)
                                                            @php
                                                                $areaEspecifia += $oldGrade->grade;
                                                                $contaEspecifica++;
                                                            @endphp
                                                        @endif
                                                        <td style="text-align: center;background-color: #F9F2F4;">
                                                            {{ round($oldGrade->grade) }}</td>
                                                    @endif
                                                @endforeach
                                                @if ($flag)
                                                    <td style="background-color: #F9F2F4;"> </td>
                                                @endif
                                            @endforeach
                                            @if ($var == 1)
                                                @foreach ($grades as $grade)
                                                    @php $oFlag = true @endphp
                                                    {{-- aqui falta comparar o id do pl ano de edicao de estudo, caso a disciplina acarretar negativa --}}
                                                    @if ($grade->discipline_id == $discipline->id)
                                                        @if ($discipline->area_id == 13)
                                                            @php
                                                                $areaGeral += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaGeral++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 14)
                                                            @php
                                                                $areaProfissional += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaProfissional++;
                                                            @endphp
                                                        @elseif($discipline->area_id == 15)
                                                            @php
                                                                $areaEspecifia += round($grade->percentage_mac + $grade->percentage_neen);
                                                                $contaEspecifica++;
                                                            @endphp
                                                        @endif
                                                        @php $oFlag = false @endphp
                                                        <td style="text-align: center; background-color: #F9F2F4;">
                                                            {{ round($grade->percentage_mac + $grade->percentage_neen) }}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            @endisset
                        </table>
                        <div class="col-4 float-right mt-4 p-0 mb-8">
                            <table class="mediaClass"
                                style=" background-color: #F5F3F3; !important ;width:100%;font-family:calibri light; margin-bottom: 6px; font-size:14pt; padding-left: 2px; ">
                                <tr class="bg1">
                                    <td colspan="2" style="font-size:13pt;text-align: center; font-size: 15pt;">
                                        <b>MÉDIAS</b>
                                    </td>
                                </tr>
                                @foreach ($disciplinesAreas as $disciplinesArea)
                                    <tr style="text-align: right;">
                                        <td style="text-align:left;"> {{ $disciplinesArea->display_name }}</td>
                                        @if ($disciplinesArea->discipline_areas_id == 13)
                                            @php $areaGeral != 0 ? $geral = round( $areaGeral / $contaGeral) : $geral = $areaGeral  @endphp
                                            <td style="text-align:center;">
                                                {{ number_format($areaGeral != 0 ? round($areaGeral / $contaGeral) : $areaGeral, 1, ',', '') }}
                                            </td>
                                        @elseif($disciplinesArea->discipline_areas_id == 14)
                                            @php $areaProfissional != 0 ? $profissional = round($areaProfissional / $contaProfissional) : $profissional = $areaProfissional @endphp
                                            <td style="text-align:center;">
                                                {{ number_format($areaProfissional != 0 ? round($areaProfissional / $contaProfissional) : $areaProfissional, 1, ',', '') }}
                                            </td>
                                        @elseif($disciplinesArea->discipline_areas_id == 15)
                                            @php $contaEspecifica != 0 ? $especifica = round($areaEspecifia / $contaEspecifica) : $especifica = $areaEspecifia @endphp
                                            <td style="text-align:center;">
                                                {{ number_format($contaEspecifica != 0 ? round($areaEspecifia / $contaEspecifica) : $areaEspecifia, 1, ',', '') }}
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                                <!--   <tr>
                                <td style="text-align:right ;"><b>Média do curso</b></td>
                                <td style="text-align:center ;" >
                                    @php
                                        $mucg = $geral;
                                        $muce = $especifica;
                                        $muce = $profissional;
                                    @endphp
                                    {{ round(($mucg + $muce + $muce) / 3) }}
                             </td>
                           </tr>  -->

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
                                {{-- <tr style="height: 1px;">
                                        <td style="text-align: right;"><b>Trabalho de fim de curso</b></td>
                                        <td style="text-align: center;"> --}}
                                @if ($var == 2)
                                    @php $contaDisciplina++ @endphp
                                    @php $final = round($finalDisciplineGrade[0]->grade) @endphp

                                    @if ($final == 0)
                                        @php $final = 0 @endphp
                                    @else
                                         {{-- {{ number_format($final, 1, ',', ' ') }} --}}
                                    @endif
                                @else
                                    @php $final = 0 @endphp
                                @endif
                                {{-- </td>
                                    </tr> --}}
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
                                    <td style="text-align: right; font-size: 17pt;"><b>MÉDIA FINAL</b></td>
                                    <td style="text-align: center ;  font-size: 17pt;">
                                        <b>
                                            @php
                                                $mucg = ($geral * 0.10);
                                                $muce = ($especifica * 0.30);
                                                $muce_1 = ($profissional * 0.35);
                                                $mtfc = ($final * 0.25);
                                            @endphp


                                            @if ($contaDisciplina == $countAllDisciplines)
                                                @if ($final == 0)
                                                    -
                                                @else
                                                    {{ round($mucg + $muce_1 + $muce + $mtfc) }}
                                                @endif
                                            @else
                                                @if ($final == 0)
                                                    -
                                                @else
                                                    {{ round($mucg + $muce_1 + $muce + $mtfc) }}
                                                @endif
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
                        <div class="">
                            </br>
                            </br>
                            <table class="table-borderless">
                                <thead style="text-align:left:">
                                    <th colspan="2" style="font-size: 12pt;">
                                    </th>
                                </thead>
                                <tbody>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <tr>
                                    <tr>
                                        <td style="font-size: 12pt; ">Gabinete de Termos:<br><br>
                                            ________________________________________________________________________
                                            {{ isset($chefe_gab_termos->full_name)?$chefe_gab_termos->full_name:"" }}
                                        </td>
                                        <td style="font-size: 12pt; ; color: white;">_____________________
                                        <td style="font-size: 12pt; ">DAAC:
                                            <br><br>____________________________________________________________________
                                            {{ isset($chefe_daac->full_name)?$chefe_daac->full_name:"" }}
                                        </td>
                                    </tr>
                                    </tr>
                                </tbody>
                            </table>

                            <h5 class="text-center" style="text-align: center;">
                                <br><br>
                                <td style="text-align: center; font-size: 12pt;">Vice-Presidente para assuntos academicos:<br><br>
                                    ________________________________________________________________________
                                    <br>{{ isset($vice_director_academica->full_name)?$vice_director_academica->full_name:"" }}
                                </td>
                            </h5>
                            
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
