<?php 
use App\Modules\Cms\Controllers\mainController;
?>

<style> 
    .tabela_pauta tbody tr td { font-weight: normal !important; } 
    .tabela_pauta tbody tr .text-bold { font-weight: 600 !important; } 
    .bg0 { background-color: #2f5496 !important; color: white; } 
    .bg1 { background-color: #8eaadb !important; } 
    .bg2 { background-color: #d9e2f3 !important; } 
    .bg3 { background-color: #fbe4d5 !important; } 
    .bg4 { background-color: #f4b083 !important; } 
    .bgmac { background-color: #a5c4ff !important; } 
    .cf1 { background-color: #4888ffdb !important; } 
    .rec { background-color: #a5c4ff !important; } 
    .fn { background-color: #1296ff !important; } 
    .bo1 { border: 1px solid white!important; } 
    table tr .small, table tr .small { font-size: 11px !important; } 
    .for-green { background-color: #00ff89 !important; } 
    .for-blue { background-color: #cce5ff !important; z-index: 1000; } 
    .for-red { background-color: #f5342ec2 !important; } 
    .for-yellow { background-color: #f39c12 !important; } 
    .boletim_text { font-weight: normal !important; } 
    .barra { color: #f39c12 !important; font-weight: bold; } 
    .semestreA, .semestre2{ } 
</style>

@if (isset($articles['dividas']['pending']) && $articles['dividas']['pending'] > 0)
    <div class="alert alert-warning text-dark font-bold">
        Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar os seus pagamentos!
    </div>
@else
    @if (is_object($percurso) && count($percurso) > 0)
        @php
            $semestres = ['1', '2'];
            $disciplina_count = 0;

            function calcularNotaMAC($pf1_nota, $pf1_percentagem, $pf2_nota, $pf2_percentagem, $oa_nota, $oa_percentagem) {
                $mac_calculo = 0;
                $total_percentagem = 0;

                if ($pf1_nota !== null && $pf1_nota !== '') {
                    $mac_calculo += $pf1_nota * $pf1_percentagem;
                    $total_percentagem += $pf1_percentagem;
                }
                if ($pf2_nota !== null && $pf2_nota !== '') {
                    $mac_calculo += $pf2_nota * $pf2_percentagem;
                    $total_percentagem += $pf2_percentagem;
                }
                if ($oa_nota !== null && $oa_nota !== '') {
                    $mac_calculo += $oa_nota * $oa_percentagem;
                    $total_percentagem += $oa_percentagem;
                }

                if ($total_percentagem > 0) {
                    return round($mac_calculo / $total_percentagem);
                }
                return 0;
            }

            function determinarEstado($nota, $config, $tipo = 'mac') {
                if ($tipo === 'mac') {
                    if ($nota >= $config->mac_nota_dispensa && $nota <= 20) return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    if ($nota >= $config->exame_nota_inicial && $nota <= $config->exame_nota_final) return ['estado' => 'Exame', 'cor' => 'for-yellow'];
                    if ($nota >= 0 && $nota <= $config->mac_nota_recurso) return ['estado' => 'Recurso', 'cor' => 'for-red'];
                }
                if ($tipo === 'exame') {
                    if ($nota >= $config->exame_nota && $nota <= 20) return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    if ($nota >= 0 && $nota < $config->exame_nota) return ['estado' => 'Recurso', 'cor' => 'for-red'];
                }
                if ($tipo === 'final') {
                    if ($nota >= 10 && $nota <= 20) return ['estado' => 'Aprovado(a)', 'cor' => 'for-green'];
                    if ($nota >= 0 && $nota < 10) return ['estado' => 'Reprovado(a)', 'cor' => 'for-red'];
                }
                return ['estado' => '', 'cor' => ''];
            }
        @endphp

        @foreach($semestres as $semestreActual)
            <table class="table tabela_pauta table-striped table-hover tabela_pauta" id="{{ 'tabela_pauta_student' . $semestreActual }}">
                <thead>
                    <tr>
                        <td colspan="3" class="boletim_text">
                            @if (isset($matriculations->course))
                                <b>{{ $matriculations->course }}</b>
                                <as class="barra">|</as> Ano: <b>{{ $matriculations->course_year }}º</b>
                                <as class="barra">|</as> Semestre: <b>{{ $semestreActual . 'º'}}</b>
                                <as class="barra">|</as> Turma: <b>{{ $matriculations->classe }}</b>
                            @endif
                        </td>
                        <td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>
                        <td colspan="2" class="text-center bg1 p-top">EXAME</td>
                        <td class="text-center cf1 bo1 p-top" colspan="2">CLASSIFICAÇÃO</td>
                        <td class="rec bo1 text-center p-top" colspan="4">EXAME</td>
                        <td class="fn bo1 text-center p-top" colspan="2">CLASSIFICAÇÃO</td>
                    </tr>
                    <tr style="text-align: center">
                        <th class="bg1 bo1">#</th>
                        <th class="text-center small bg1 bo1">CÓDIGO</th>
                        <th class="bg1 bo1">DISCIPLINA</th>
                        <th class="bgmac bo1">PF1</th>
                        <th class="bgmac bo1">PF2</th>
                        <th class="bgmac bo1">OA</th>
                        <th colspan="2" class="bgmac bo1">MÉDIA</th>
                        <th class="bg1 bo1">ESCRITO</th>
                        <th class="bg1 bo1">ORAL</th>
                        <th class="cf1 bo1" colspan="2">MAC + EXAME</th>
                        <th class="rec bo1" colspan="2">RECURSO</th>
                        <th class="rec bo1" colspan="2">ESPECIAL</th>
                        <th class="fn bo1" colspan="2">FINAL</th>
                    </tr>
                </thead>

                @foreach ($disciplines as $index => $item_DISC)
                    @if($index[3] == $semestreActual)
                        @php
                            $disciplina_count++;
                            $par = ($disciplina_count % 2 == 0) ? 'bg-white' : null;

                            $pf1_nota = $pf2_nota = $oa_nota = null;
                            $pf1_percentagem = $pf2_percentagem = $oa_percentagem = 0;
                            $neen_nota = $oral_nota = $recurso_nota = $especial_nota = null;

                            if(isset($percurso[$index])) {
                                foreach ($percurso[$index] as $itemNotas) {
                                    $nota_aluno = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
                                    switch ($itemNotas->MT_CodeDV) {
                                        case 'PF1': $pf1_nota=$nota_aluno; $pf1_percentagem=$itemNotas->percentagem_metrica/100; break;
                                        case 'PF2': $pf2_nota=$nota_aluno; $pf2_percentagem=$itemNotas->percentagem_metrica/100; break;
                                        case 'OA': $oa_nota=$nota_aluno; $oa_percentagem=$itemNotas->percentagem_metrica/100; break;
                                        case 'Neen': $neen_nota=$nota_aluno; break;
                                        case 'oral': $oral_nota=$nota_aluno; break;
                                        case 'Recurso': $recurso_nota=$nota_aluno; break;
                                        case 'Exame_especial': $especial_nota=$nota_aluno; break;
                                    }
                                }
                            }

                            $id_turma = ($classes && method_exists($classes,'first')) ? ($classes->first(fn($item)=>$item_DISC->turma==$item->display_name)->id ?? null) : null;

                            $mac_nota = ($id_turma && isset($item_DISC->id_disciplina) && isset($item_DISC->id_anoLectivo)) ? calcularNotaMAC($pf1_nota,$pf1_percentagem,$pf2_nota,$pf2_percentagem,$oa_nota,$oa_percentagem) : 0;
                            $classificacao = $mac_nota;
                            $nota_final = $mac_nota;
                            $estado_final = $color_final = '';
                            $aprovado = $recurso = $exame = $exame_oral = false;
                            $exam_only = $item_DISC->e_f ?? 0;
                            $mac_percentagem = $config->percentagem_mac/100 ?? 0.7;
                            $neen_percentagem = $config->percentagem_oral/100 ?? 0.3;

                            $p_mac = mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo,'Pauta Frequência');
                            $p_exame_oral = mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo,'Pauta de Exame Oral');
                            $p_recurso = mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo,'Pauta de Recurso');
                            $p_especial = mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo,'Pauta Exame Especial');
                            $p_final = mainController::verificar_pauta($id_turma, $item_DISC->id_disciplina, $item_DISC->id_anoLectivo,'Pauta Final');

                            $estado_final = determinarEstado($nota_final, $config,'final')['estado'];
                            $color_final = determinarEstado($nota_final, $config,'final')['cor'];
                        @endphp

                        <tbody>
                            <tr class="semestre{{ $semestreActual }} {{ $par ?? '' }}">
                                <td style='text-align: center'>{{ $disciplina_count }}</td>
                                <td style='text-align: center'>{{ $index }}</td>
                                <td style='text-align: left'>{{ $item_DISC->nome_disciplina ?? '' }}</td>

                                <td class='text-bold text-center'>{{ $pf1_nota!==null ? number_format($pf1_nota,2) : '-' }}</td>
                                <td class='text-bold text-center'>{{ $pf2_nota!==null ? number_format($pf2_nota,2) : '-' }}</td>
                                <td class='text-bold text-center'>{{ $oa_nota!==null ? number_format($oa_nota,2) : '-' }}</td>

                                <td class='text-bold text-center'>{{ $nota_final }}</td>
                                <td class='text-bold text-center {{ $color_final }}'>{{ $estado_final }}</td>

                                <td style='text-align: center'>{{ $neen_nota!==null ? round($neen_nota) : '-' }}</td>
                                <td style='text-align: center'>{{ $oral_nota!==null ? round($oral_nota) : '-' }}</td>

                                <td class='text-bold text-center'>{{ $nota_final }}</td>
                                <td class='text-bold text-center {{ $color_final }}'>{{ $estado_final }}</td>

                                <td style='text-align: center'>{{ $recurso_nota!==null ? round($recurso_nota) : '-' }}</td>
                                <td style='text-align: center'>{{ $especial_nota!==null ? round($especial_nota) : '-' }}</td>

                                <td class='text-bold text-center'>{{ $nota_final }}</td>
                                <td class='text-bold text-center {{ $color_final }}'>{{ $estado_final }}</td>
                            </tr>
                        </tbody>
                    @endif
                @endforeach
            </table>
        @endforeach

        @if(!isset($institution))
            <div class="row float-right btn-pdf-boletim" style="margin-right: 0.1!important;">
                <a class="btn" style="background-color:#0082f2;" target="_blank" href="{{ route('main.boletim_pdf', $matriculations->id) }}">
                    <i class="fa fa-file-pdf"></i> Boletim de notas
                </a>
            </div>
        @endif
    @elseif (is_object($percurso))
        <div class="alert alert-warning text-dark font-bold">Nenhuma nota foi lançada neste ano lectivo!</div>
    @else
        <div class="alert alert-warning text-dark font-bold">{{ $percurso }}!</div>
    @endif
@endif
