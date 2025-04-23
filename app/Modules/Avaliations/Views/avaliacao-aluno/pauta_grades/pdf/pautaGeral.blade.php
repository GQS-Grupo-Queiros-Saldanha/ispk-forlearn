@extends('layouts.print')
@section('content')

@php
$logotipo = $logotipo;
$documentoCode_documento = 50;
$doc_name = "Pauta de ".$disciplina;
@endphp
<main>

    @include('Reports::pdf_model.forLEARN_header')
    <table class="table_te">
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

        <thead style="border:none;" id="chega">
            <tr class="bg1">

                <th>DISCIPLINA</th>
                <th>CURSO</th>
                <th>ANO CURRICULAR</th>
                <th>ANO LECTIVO</th>
                <th>REGIME</th>
                <th>TURMA</th>
            </tr>

        </thead>




        <tbody id="corpoTabela">
            <tr class="bg2">
                <td class="text-center bg2">
                    {{$disciplina}}
                </td>

                <td class="text-center bg2">
                    {{$curso}}
                </td>
                {{--<td class="text-center bg2">{{$lectiveYear[0]->display_name}}</td>--}}
                <td class="text-center bg2">
                    {{$ano }}
                </td>
                <td class="text-center bg2">
                    {{$ano_lectivo}}
                </td>
                <td class="text-center bg2">
                    {{$regime}}
                </td>
                <td class="text-center bg2">
                    {{$turma}}
                </td>

            </tr>
        </tbody>
    </table>


    {{-- Começa aqui a tabela --}}
    <table class="table_te">


        <thead style="border:none;" id="chega">
            <tr class="bg1">

                <th>#</th>
                <th>MATRÍCULA</th>
                <th>ESTUDANTE</th>
                <th>PP1</th>
                <th>PP2</th>
                <th>OA</th>
                <th>MAC</th>
                <th>Exame</th>
                <th>CF</th>
                <th>Recurso</th>
                <th>Melhoria</th>
                <th>Especial</th>
                <th>Extraordinário</th>

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
                    {{$student->n_student}}
                </td>
                <td class="text-left bg2">
                    {{$student->user_name}}
                </td>
                @php
                $percurso = $student->percurso;
                $pf1_nota = null;
                $pf1_percentagem = 0;
                $pf2_nota = null;
                $pf2_percentagem = 0;
                $oa_nota = null;
                $oa_percentagem = 0;
                $neen_nota = null;
                $recurso_nota = null;
                $especial_nota = null;
                $classificacao = 0;
                $mac_nota = 0;
                $mac_percentagem = $config->percentagem_mac / 100;
                $neen_percentagem = $config->percentagem_oral / 100;
                $melhoria_nota = null;
                $extra_nota = null;
                $aprovado = false;
                $recurso = false;
                $exame = false;

                if(isset($percurso[$pauta->codigo_disciplina])){
                foreach ($percurso[$pauta->codigo_disciplina] as $indexNotas => $itemNotas){
                if ($itemNotas->MT_CodeDV == 'PF1') {
                if ($itemNotas->nota_anluno == null) {
                $pf1_nota = 0;
                } else {
                $pf1_nota = round($itemNotas->nota_anluno);
                $pf1_percentagem = $itemNotas->percentagem_metrica / 100;
                }
                }

                if ($itemNotas->MT_CodeDV == 'PF2') {
                if ($itemNotas->nota_anluno == null) {
                $pf2_nota = 0;
                } else {
                $pf2_nota = round($itemNotas->nota_anluno);
                $pf2_percentagem = $itemNotas->percentagem_metrica / 100;
                }
                }

                if ($itemNotas->MT_CodeDV == 'OA') {
                if ($itemNotas->nota_anluno == null) {
                $oa_nota = 0;
                } else {
                $oa_nota = round($itemNotas->nota_anluno);
                
                $oa_percentagem = $itemNotas->percentagem_metrica / 100;
                }
                }

                if ($itemNotas->MT_CodeDV == 'Neen') {
                if ($itemNotas->nota_anluno == null) {
                $neen_nota = 0;
                } else {
                $neen_nota = floatval($itemNotas->nota_anluno);
                }
                }

                if ($itemNotas->MT_CodeDV == 'Recurso') {
                if ($itemNotas->nota_anluno == null) {
                $recurso_nota = 0;
                } else {
                $recurso_nota = floatval($itemNotas->nota_anluno);
                $recurso_nota = round($recurso_nota);
                }
                }

                if ($itemNotas->MT_CodeDV == 'Exame_especial') {
                if ($itemNotas->nota_anluno == null) {
                $especial_nota = 0;
                } else {
                $especial_nota = floatval($itemNotas->nota_anluno);
                }
                }

                if ($itemNotas->MT_CodeDV == 'Extraordinario') {
                if ($itemNotas->nota_anluno == null) {
                $extra_nota = 0;
                } else {
                $extra_nota = floatval($itemNotas->nota_anluno);
                }
                }

                }
                $melhoria_notas = get_melhoria_notas($student->user_id, $pauta->lective_year_id, 0);
                if($melhoria_notas->contains('discipline_id',$pauta->id_disciplina)){
                $m = $melhoria_notas->where('discipline_id',$pauta->id_disciplina)->first();

                $melhoria_nota = !is_null($m->new_grade) ? $m->new_grade : null;

                }


                }
                @endphp

                <td class="text-center bg2">{{$pf1_nota ?? '-'}}</td>
                <td class="text-center bg2">{{$pf2_nota ?? '-'}}</td>
                <td class="text-center bg2">{{$oa_nota ?? '-'}}</td>
                @php
                $mac_nota = (($pf1_nota * $pf1_percentagem) + ($pf2_nota * $pf2_percentagem) + ($oa_nota * $oa_percentagem));
                $mac_nota = round($mac_nota);
                $classificacao = $mac_nota;

                if ($classificacao >= 0 && $classificacao <= $config->mac_nota_recurso) {

                    $recurso = true;
                    }
                    if ($classificacao >= $config->exame_nota_inicial && $classificacao <= $config->exame_nota_final) {

                        $exame = true;
                        }
                        if ($classificacao >= $config->mac_nota_dispensa && $classificacao <= 20) {
                            $aprovado=true;


                            }
                            @endphp
                            <td class="text-center bg2">{{$mac_nota}}</td>
                            <td class="text-center bg2">{{$recurso || $aprovado ? '-' : $neen_nota}}</td>
                            @php
                            if($exame){

                            if ($neen_nota == null)
                            $neen_nota = 0;
                            else
                            $neen_nota = round($neen_nota);

                            $last_exame = $neen_nota;

                            $classificacao = ($mac_nota * $mac_percentagem) + ($neen_nota * $neen_percentagem);
                            $classificacao = round($classificacao);
                            if ($classificacao >= 0 && $classificacao < $config->exame_nota) {
                                $aprovado = true;
                                }

                                if ($classificacao >= $config->exame_nota && $classificacao <= 20) {
                                    $recurso=true;
                                    }

                                    }
                                    @endphp
                                    <td class="text-center bg2">{{$classificacao}}</td>
                                    <td class="text-center bg2">{{$recurso_nota ?? ''}}</td>
                                    <td class="text-center bg2">{{$melhoria_nota ?? ''}}</td>
                                    <td class="text-center bg2">{{$especial_nota ?? ''}}</td>
                                    <td class="text-center bg2">{{$extra_nota ?? ''}}</td>

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

                    <td style="font-size: 10pt; ">Docente:<br><br>


                        ________________________________________________________________________
                        {{$teacher->fullname}}<br><br>


                    </td>








                    <td style="font-size: 10pt; ; color: white;">_____________________




                    <td style="font-size: 10pt; "><br>Coordenador do curso:<br><br> ________________________________________________________________________ <br>{{$coordenador->fullname}}<br><br><br>

                </tr>

                </tr>
            </tbody>
        </table>
    </div>

</main>

@endsection

<script>
    // window.print();
</script>