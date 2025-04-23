<title>{{ 'Boletim | '.$student_info->matricula .
        ' | ' .
        $student_info->turma
        .' | ' .
        $student_info->lective_year }}
</title>
@extends('layouts.print')
@section('content')
    @php
        $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
        $documentoCode_documento = 50;
        $doc_name = 'Boletim do estudante';
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
                           
                           
<?php
    use App\Modules\Cms\Controllers\mainController;
?>

@if(isset($articles['dividas']["pending"]) && ($articles['dividas']["pending"]>0))
<div class="alert alert-warning text-dark font-bold">Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar 
os seus pagamentos!</div>
@else
        @if(is_object($percurso))
            @if(count($percurso)>0)
                <table class="table_te">
                    <thead class="table_pauta">
                        <tr class="bg1" style="text-align: center">
                            <th>#</th>
                            <th>DISCIPLINA</th>
                            <th>PF1</th>
                            <th>PF2</th>
                            <th>OA</th>
                            <th>MAC</th>
                            <th>EXAME</th> 
                            <th>RECURSO</th>
                            <th>CLASSIFICAÇÃO</th>
                        </tr>
                    </thead>
                    <tbody id="lista_tr">
                        @php

                            $disciplina_count = 0;
                            $tabelaCorpo = '';

                            $discipline = [];

                            foreach ($percurso as $index => $item) {
                                if ($index != null) {
                                    $disciplina_nome = $index;
                                    $disciplina_count += 1;

                                    echo "<tr classs='bg2'><td style='text-align: center'>".$disciplina_count."</td>"; 
                                    echo "<td style='text-align: left'>".$disciplina_nome."</td>"; 
                                    $tabelaCorpo = $disciplina_count;
                                    $tabelaCorpo = $disciplina_nome;

                                    $avalicao_nome = null;
                                    $avaliacao_nota = 0;
                                    $avaliacao_count = 0;
                                    $pf1 = 0;
                                    $pf2 = 0;
                                    $oa = 0;
                                    $nenn = 0;
                                    $recurso = 0;
                                    $tesp = 0;
                                    $pf1_nota = null;
                                    $pf2_nota = null;
                                    $oa_nota = null;
                                    $neen_nota = null;
                                    $recurso_nota = null;
                                    $classificacao = 0;
                                    $tesp_nota = null;

                                    $aval_mac = null;
                                    $mac_nota = 0;

                                    foreach ($item as $indexNotas => $itemNotas) {
                                        if ($itemNotas->MT_CodeDV == 'PF1') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $pf1_nota = 0;
                                            } else {
                                                $pf1_nota = floatval($itemNotas->nota_anluno);
                                                $pf1 = 1;
                                            }
                                        }

                                        if ($itemNotas->MT_CodeDV == 'PF2') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $pf2_nota = 0;
                                            } else {
                                                $pf2_nota = floatval($itemNotas->nota_anluno);
                                                $pf2 = 1;
                                            }
                                        }

                                        if ($itemNotas->MT_CodeDV == 'OA') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $oa_nota = 0;
                                            } else {
                                                $oa_nota = floatval($itemNotas->nota_anluno);
                                                $oa = 1;
                                            }
                                        }

                                        if ($itemNotas->MT_CodeDV == 'Neen') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $neen_nota = 0;
                                            } else {
                                                $neen_nota = floatval($itemNotas->nota_anluno);
                                                $nenn = 1;
                                            }
                                        }

                                        if ($itemNotas->MT_CodeDV == 'Recurso') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $recurso_nota = 0;
                                            } else {
                                                $recurso_nota = floatval($itemNotas->nota_anluno);
                                                $recurso = 1;
                                            }
                                        }

                                        if ($itemNotas->MT_CodeDV == 'TESP') {
                                            if ($itemNotas->nota_anluno == null) {
                                                $tesp_nota = 0;
                                            } else {
                                                $tesp_nota = floatval($itemNotas->nota_anluno);
                                                $tesp = 1;
                                            }
                                        }
                                    }

                                    $aval_mac = $pf1 + $pf2 + $oa;

                                    if ($aval_mac == 0) {
                                        echo "<td style='text-align: center'> - </td>";
                                        echo "<td style='text-align: center'> - </td>";
                                        echo "<td style='text-align: center'> - </td>";

                                        if ($neen_nota == null) {
                                            echo  "<td style='text-align: center'> - </td>";
                                        } else {
                                            echo "<td style='text-align: center'> - </td>";
                                            $p_exame = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Exame");
                                                
                                                if($p_exame>0){
                                                    echo "<td style='text-align: center'>".$neen_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                        }
                                        
                                        if ($recurso_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            if ($recurso_nota == 0) {

                                                $p_recurso = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Recurso");
                                                
                                                if($p_recurso>0){
                                                    echo "<td style='text-align: center'>".$recurso_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }

                                                $classificacao = $recurso_nota;
                                            } else {
                                                echo "<td style='text-align: center'> - </td>";
                                            }
                                        }
                                        
                                        if ($recurso_nota == null && $neen_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        }

                                        if ($tesp_nota != null) {
                                            echo "<td style='text-align: center'>" . number_format($tesp_nota, 2) . "</td>";
                                        } else {
                                            echo "<td style='text-align: center'>" . number_format($classificacao, 2) . "</td>";
                                        }
                                    }

                                    // NO CADO DE HOUVER SOMENTE UMA NOTA LANÇADA
                                    if ($aval_mac == 1) {
                                        if ($pf1_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        }else{
                                            echo "<td style='text-align: center'>".$pf1_nota."</td>";
                                        }
                                        echo "<td style='text-align: center'> - </td>";
                                        echo "<td style='text-align: center'> - </td>";
                                        echo "<td style='text-align: center'> - </td>";

                                        if ($neen_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            $p_exame = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Exame");
                                                
                                                if($p_exame>0){
                                                    echo "<td style='text-align: center'>".$neen_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            $classificacao = ($mac_nota * 0.6 + $neen_nota * 0.4) / (0.6 + 0.4);
                                        }

                                        if ($recurso_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            $p_recurso = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Recurso");
                                                
                                                if($p_recurso>0){
                                                    echo "<td style='text-align: center'>".$recurso_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            $classificacao = $recurso_nota;
                                        }
                                        $classificacao = round($classificacao);
                                        echo "<td style='text-align: center'>".number_format($classificacao, 2)."</td>";
                                    }

                                    // NO CASO DE HOUVER SOMENTE DUAS NOTAS LANÇADAS
                                    if ($aval_mac == 2) {
                                        if ($pf1_nota == null) {
                                            echo  "<td style='text-align: center'> - </td>";
                                        } else {
                                            echo "<td style='text-align: center'>".$pf1_nota."</td>";
                                        }
                                        if ($pf2_nota == null) {
                                        echo "<td style='text-align: center'> - </td>";
                                        } else {
                                        echo "<td style='text-align: center'>".$pf2_nota."</td>";
                                        }
                                        if ($oa_nota == null) {
                                        echo "<td style='text-align: center'> - </td>";
                                        } else {
                                        echo "<td style='text-align: center'>".$oa_nota."</td>";
                                        }

                                        $mac_nota = $pf1_nota * 0.33 + $pf2_nota * 0.33 + $oa_nota * 0.34;

                                        $p_mac = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta Frequência");
                                                
                                                if($p_mac>0){
                                                    echo "<td style='text-align: center'>".number_format($mac_nota, 2)."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                } 
                                        
                
                                        if ($neen_nota == null) {
                                            $classificacao = $mac_nota;
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            $p_exame = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Exame");
                                                
                                                if($p_exame>0){
                                                    echo "<td style='text-align: center'>".$neen_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            $classificacao = ($mac_nota * 0.6 + $neen_nota * 0.4) / (0.6 + 0.4);
                                        }

                                        if ($recurso_nota == null) {
                                            $classificacao = $mac_nota;
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            $p_recurso = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Recurso");
                                                
                                                if($p_recurso>0){
                                                    echo "<td style='text-align: center'>".$recurso_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            $classificacao = $recurso_nota;
                                        }
                                        $classificacao = round($classificacao);
                                        echo "<td style='text-align: center'>".number_format($classificacao, 2)."</td>";
                                    }

                                    // NO CASO DE TODAS AS NOTAS FOREM LANÇADAS
                                    
                                    if ($aval_mac == 3){
                                        //
                                        $mac_nota = $pf1_nota * 0.33 + $pf2_nota * 0.33 + $oa_nota * 0.34;
                                        $classificacao = $mac_nota;

                                        echo "<td style='text-align: center'>".$pf1_nota."</td>";
                                        echo "<td style='text-align: center'>".$pf2_nota."</td>";
                                        echo "<td style='text-align: center'>".$oa_nota."</td>";
                                        
                                        
                                        $p_mac = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta Frequência");
                                                
                                                if($p_mac>0){
                                                    echo "<td style='text-align: center'>".number_format($mac_nota, 2)."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                        

                                        if ($neen_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                            
                                        } else {
                                            $p_exame = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Exame");
                                                
                                                if($p_exame>0){
                                                    echo "<td style='text-align: center'>".$neen_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            
                                            $classificacao = ($mac_nota * 0.6 + $neen_nota * 0.4) / (0.6 + 0.4);
                                        }

                                        if ($recurso_nota == null) {
                                            echo "<td style='text-align: center'> - </td>";
                                        } else {
                                            $p_recurso = mainController::verificar_pauta($itemNotas->class_id,$itemNotas->Disciplia_id,$itemNotas->lective_years_id,"Pauta de Recurso");
                                                
                                                if($p_recurso>0){
                                                    echo "<td style='text-align: center'>".$recurso_nota."</td>";
                                                }else{
                                                    echo "<td style='text-align: center'>-</td>";
                                                }
                                            $classificacao = $recurso_nota;
                                        }
                                        $classificacao = round($classificacao);
                                        echo "<td style='text-align: center'>".number_format($classificacao,2)."</td>";
                                        
                                    }


                                    echo'</tr>';
                                }
                            }

                        @endphp
                    </tbody>
                </table>
             @else
             <div class="alert alert-warning text-dark font-bold">Nenhuma nota foi lançada neste ano lectivo!  </div>
             @endif
        @else

        <div class="alert alert-warning text-dark font-bold"> {{$percurso}}!</div>
        @endif
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
