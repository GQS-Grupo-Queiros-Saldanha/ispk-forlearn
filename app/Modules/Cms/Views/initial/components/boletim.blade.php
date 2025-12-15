<?php
use App\Modules\Cms\Controllers\mainController;
?>

<style>
 .tabela_pauta tbody tr td {
 font-weight: normal !important;
 }

 .tabela_pauta tbody tr .text-bold {
 font-weight: 600 !important;
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
 background-color: #fbe4d5 !important;
 }

 .bg4 {
 background-color: #f4b083 !important;
 }

 .bgmac {
 background-color: #a5c4ff !important;
 }

 .cf1 {
 background-color: #4888ffdb !important;

 }

 .rec {
 background-color: #a5c4ff !important;

 }

 .fn {
 background-color: #1296ff !important;
 }

 .bo1 {
 border: 1px solid white!important;
 }

 table tr .small,
 table tr .small {
 font-size: 11px !important;
 }

 .for-green {
 background-color: #00ff89 !important;
 }

 .for-blue {
 background-color: #cce5ff !important;
 z-index: 1000;
 }

 .for-red {
 background-color: #f5342ec2 !important;
 }

 .for-yellow {
 background-color: #f39c12 !important;

 }

 .boletim_text {

 font-weight: normal !important;
 }

 .barra {
 color: #f39c12 !important;
 font-weight: bold;
 }

 .semestreA,
 .semestre2{
 
 }
 
</style>


@if (isset($articles['dividas']['pending']) && $articles['dividas']['pending'] > 0)
 <div class="alert alert-warning text-dark font-bold">Para visualizar as notas lançadas, dirija-se a Tesouraria para
 regularizar os seus pagamentos!</div>
@else
 @if (is_object($percurso)) 
 @if (count($percurso) > 0)

 @php
 $semestres = ['1','2'];
 $disciplina_count = 0;
 
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
 <td colspan="5" class="text-center bgmac bo1 p-top" style="border-bottom: 1px solid white; ">MAC
 </td>
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

 $par = null;

 if ($disciplina_count % 2 == 0) {
 $par = 'bg-white';
 }


 $disciplina_nome = $index;
 $avalicao_nome = null;
 $avaliacao_nota = 0;
 $code_disc = false;
 $avaliacao_count = 0;
 $pf1_nota = null;
 $pf1_percentagem = 0;
 $pf2_nota = null;
 $pf2_percentagem = 0;
 $oa_nota = null;
 $oa_percentagem = 0;
 $neen_nota = null;
 $oral_nota = null;
 $recurso_nota = null;
 $especial_nota = null;
 $classificacao = 0;
 $aval_mac = null;
 $mac_nota = 0;
 $estado_final = '';
 $count_exame = 0;
 $last_exame = 0;
 $nota_final = '-';
 $color_final = '';
 $mac_percentagem = $config->percentagem_mac / 100;
 $neen_percentagem = $config->percentagem_oral / 100;
 
$id_turma = $classes->first(function($item) use ($item_DISC) {
    return $item_DISC->turma == $item->display_name; // ou $item->code
})->id;

 $aprovado = false;
 $recurso = false;
 $exame = false;
 $exame_oral = false;
 $exam_only = $item_DISC->e_f;
 
 @endphp

 <tbody>
 <tr class="{{'semestre'.$semestreActual}} {{ $par ?? '' }}">
 
 <td style='text-align: center'>{{ $disciplina_count }}</td>
 <td style='text-align: center'>{{ $disciplina_nome }}</td>
 <td style='text-align: left'>{{ $item_DISC->nome_disciplina }}</td>

 @php
 if(isset($percurso[$index])){
 foreach ($percurso[$index] as $indexNotas => $itemNotas){
 if ($itemNotas->MT_CodeDV == 'PF1') {
 if ($itemNotas->nota_anluno == null) {
 
 } else {
 $pf1_nota = floatval($itemNotas->nota_anluno);
 $pf1_percentagem = $itemNotas->percentagem_metrica / 100;
 }
 }

 if ($itemNotas->MT_CodeDV == 'PF2') {
 if ($itemNotas->nota_anluno == null) {
 } else {
 $pf2_nota = floatval($itemNotas->nota_anluno);
 $pf2_percentagem = $itemNotas->percentagem_metrica / 100;
 }
 }

 if ($itemNotas->MT_CodeDV == 'OA') {
 if ($itemNotas->nota_anluno == null) {
 } else {
 $oa_nota = floatval($itemNotas->nota_anluno);
 $oa_percentagem = $itemNotas->percentagem_metrica / 100;
 }
 }

 if ($itemNotas->MT_CodeDV == 'Neen') {
 // CORREÇÃO: Não atribuir 0 quando for null
 $neen_nota = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
 }
 if ($itemNotas->MT_CodeDV == 'oral') {
 // CORREÇÃO: Não atribuir 0 quando for null
 $oral_nota = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
 }

 if ($itemNotas->MT_CodeDV == 'Recurso') {
 // CORREÇÃO: Não atribuir 0 quando for null
 $recurso_nota = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
 }

 if ($itemNotas->MT_CodeDV == 'Exame_especial') {
 // CORREÇÃO: Não atribuir 0 quando for null
 $especial_nota = ($itemNotas->nota_anluno != null) ? floatval($itemNotas->nota_anluno) : null;
 }
 
 }
 }
 @endphp

 <td class='text-bold text-center'>{{ isset($pf1_nota) ? number_format($pf1_nota, 2) : '-' }}</td>
 <td class='text-bold text-center'>{{ isset($pf2_nota) ? number_format($pf2_nota, 2) : '-' }}</td>
 <td class='text-bold text-center'>{{ isset($oa_nota) ? number_format($oa_nota, 2) : '-' }}</td>

 @php
    
    $p_mac = mainController::verificar_pauta(
        $id_turma,
        $item_DISC->id_disciplina,
        $item_DISC->id_anoLectivo,
        'Pauta Frequência'
    );

    // CORREÇÃO: Cálculo do MAC com verificação de valores null
    if ($p_mac > 0) {
        // Calcular MAC apenas com notas existentes
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
        
        // Ajustar se houver percentagens
        if ($total_percentagem > 0) {
            $mac_nota = $mac_calculo / $total_percentagem;
        } else {
            $mac_nota = 0;
        }
        
        // Arredondar apenas se houver valor
        if ($mac_nota > 0) {
            $mac_nota = round($mac_nota);
        }
        
        $classificacao = $mac_nota;

        if($exam_only == 1){
            $exame = true;
        }
        else{
            if ($classificacao >= 0 && $classificacao <= $config->mac_nota_recurso) {
                $estado_final = 'Recurso';
                $color_final = 'for-red';
                $recurso = true;
            }

            if ($classificacao >= $config->exame_nota_inicial && $classificacao <= $config->exame_nota_final) {
                $estado_final = 'Exame';
                $color_final = 'for-yellow';
                $exame = true;
            }

            if ($classificacao >= $config->mac_nota_dispensa && $classificacao <= 20) {
                $estado_final = 'Aprovado(a)';
                $color_final = 'for-green';
                $aprovado = true;
            }

            $nota_final = $classificacao;
        }
    }
 @endphp

 @if ($p_mac > 0)
    <td class='text-bold text-center'>{{ $nota_final }}</td>
    <td class="{{'text-bold text-center ' .$color_final }}"> {{$estado_final}} </td>
 @else
    <td style='text-align: center'>-</td>
    <td style='text-align: center'>-</td>
 @endif

 @if ($neen_nota === null || $aprovado) 
    <td style='text-align: center'> - </td>
 @else 
 
 @if ($estado_final == 'Aprovado(a)') 
    <td style='text-align: center'>-</td>
 
 @elseif ($estado_final == 'Recurso')
    <td style='text-align: center'>-</td>
 @else
    <td style='text-align: center'>{{ round($neen_nota) }}</td>
 @endif

 @endif

@php
    if($exame){
        ++$count_exame;
        
        // CORREÇÃO: Verificar se $neen_nota não é null antes de usar
        if($neen_nota === null) {
            $neen_nota_valor = 0;
        } else {
            $neen_nota_valor = round($neen_nota);
        }
        
        // CORREÇÃO: Usar variável temporária para cálculo
        $neen_calc = $neen_nota_valor;

        if(!is_null($config->exame_oral_final) && ($neen_calc > $config->mac_nota_recurso && $neen_calc <= round($config->exame_oral_final)))
        {
            $exame_oral = true;
        }
        else{
            if($exam_only == 1){
                $classificacao = $neen_calc;
            }
            else
            {
                // CORREÇÃO: Verificar se $mac_nota é válido antes de calcular
                if($mac_nota > 0) {
                    $classificacao = ($mac_nota * $mac_percentagem) + ($neen_calc * $neen_percentagem);
                } else {
                    $classificacao = $neen_calc;
                }
            }
            
            $classificacao = round($classificacao);
            
            // CORREÇÃO: Resetar variáveis antes de nova avaliação
            $estado_final_temp = $estado_final;
            $color_final_temp = $color_final;
            
            if ($classificacao >= 0 && $classificacao < $config->exame_nota) {
                $estado_final = 'Recurso';
                $color_final = 'for-red';
            }

            if ($classificacao >= $config->exame_nota && $classificacao <= 20) {
                $estado_final = 'Aprovado(a)';
                $color_final = 'for-green';
            }
            $nota_final = round($classificacao);
        }
    }
 @endphp
 
 @if ($oral_nota === null)
 <td style='text-align: center'> - </td>
 @else
 @php
 $p_exame_oral = mainController::verificar_pauta(
 $id_turma,
 $item_DISC->id_disciplina,
 $item_DISC->id_anoLectivo,
 'Pauta de Exame Oral',
 ); 
 @endphp
 
 @if ($p_exame_oral > 0) 
 @if ($estado_final == 'Aprovado(a)') 
 <td style='text-align: center'>---</td>
 
 @elseif ($estado_final == 'Recurso')
 <td style='text-align: center'>--</td>
 @else
 <td style='text-align: center'>{{$oral_nota}}</td>
 @endif
 @else
 <td style='text-align: center'>-</td> 
 @endif


 @endif

 @php
 if($exame_oral){
    ++$count_exame;
    
    // CORREÇÃO: Verificar se $oral_nota não é null
    if($oral_nota === null) {
        $oral_nota_valor = 0;
    } else {
        $oral_nota_valor = round($oral_nota);
    }
    
    // CORREÇÃO: Usar variável temporária
    $oral_calc = $oral_nota_valor;

    if($exam_only == 1){
        $classificacao = $neen_nota_valor;
    }
    else{
        // CORREÇÃO: Verificar se $mac_nota é válido
        if($mac_nota > 0) {
            $classificacao = ($mac_nota * $mac_percentagem) + ($oral_calc * $neen_percentagem);
        } else {
            $classificacao = $oral_calc;
        }
        
        $classificacao = round($classificacao);
    }
    
    // CORREÇÃO: Resetar variáveis de estado
    $estado_final = '';
    $color_final = '';
    
    if ($classificacao >= 0 && $classificacao < 10) {
        $estado_final = 'Recurso';
        $color_final = 'for-red';
    }
    if ($classificacao >= 10 && $classificacao <= 20) {
        $estado_final = 'Aprovado(a)';
        $color_final = 'for-green';
    }
    
    $nota_final = $classificacao;
 }
 @endphp


 @php

 $p_final = mainController::verificar_pauta(
 $id_turma,
 $item_DISC->id_disciplina,
 $item_DISC->id_anoLectivo,
 'Pauta Final',
 );

 @endphp

 @if($p_final > 0)
 @if($aprovado || $recurso) 
 <td class='text-bold text-center'>{{ $classificacao }}</td>
 <td class="{{'text-bold text-center ' . $color_final}}">{{ $estado_final }}</td>
 @else
 
 <td class='text-bold text-center'>{{ $classificacao }}</td>
 <td class="{{'text-bold text-center ' . $color_final}}">{{ $estado_final }}</td>
 @endif
 @else
 <td style='text-align: center'>-</td>
 <td style='text-align: center'>-</td>
 @endif

 @if ($recurso_nota === null) 
 <td style='text-align: center'> - </td>
 <td style='text-align: center'> - </td> 
 @else
 @php
 $p_recurso = mainController::verificar_pauta(
 $id_turma,
 $item_DISC->id_disciplina,
 $item_DISC->id_anoLectivo,
 'Pauta de Recurso',
 );
 @endphp
 
 @if ($p_recurso > 0)
 @if ($estado_final == 'Aprovado(a)') 
 <td style='text-align: center'>-</td>
 <td style='text-align: center'>-</td>
 @else 
 @php
 // CORREÇÃO: Verificar se $recurso_nota não é null
 if($recurso_nota !== null) {
    $classificacao = round($recurso_nota);
    
    // CORREÇÃO: Resetar estado para recurso
    $estado_final = '';
    $color_final = '';
    
    if ($classificacao >= 0 && $classificacao < 10) {
        $estado_final = 'Especial';
        $color_final = 'for-red';
    }
    if ($classificacao >= 10 && $classificacao <= 20) {
        $estado_final = 'Aprovado(a)';
        $color_final = 'for-green';
    }
    $nota_final = $classificacao;
 }
 @endphp
 <td style='text-align: center'>{{ $recurso_nota }}</td>
 <td class="{{'text-bold text-center ' . $color_final}}">{{ $estado_final }}</td>
 @endif
 @else
 <td style='text-align: center'>-</td>
 <td style='text-align: center'>-</td>
 @endif
 @endif

 @if ($especial_nota === null)
 <td style='text-align: center'> - </td>
 <td style='text-align: center'> - </td>
 @else
 @php

 $p_especial = mainController::verificar_pauta(
 $id_turma,
 $item_DISC->id_disciplina,
 $item_DISC->id_anoLectivo,
 'Pauta Exame Especial',
 );

 @endphp

 @if ($p_especial > 0)
 @if ($estado_final == 'Aprovado(a)') 
 <td style='text-align: center'>-</td>
 <td style='text-align: center'>-</td>
 @else
 @php
 // CORREÇÃO: Verificar se $especial_nota não é null
 if($especial_nota !== null) {
    $classificacao = round($especial_nota);
    
    // CORREÇÃO: Resetar estado para especial
    $estado_final = '';
    $color_final = '';
    
    if ($classificacao >= 0 && $classificacao < 10) {
        $estado_final = 'Reprovado(a)';
        $color_final = 'for-red';
    }

    if ($classificacao >= 10 && $classificacao <= 20) {
        $estado_final = 'Aprovado(a)';
        $color_final = 'for-green';
    }
    $nota_final = $classificacao;
 }
 @endphp

 <td style='text-align: center'>{{ $especial_nota }}</td>
 <td class="{{'text-bold text-center ' . $color_final}}">{{ $estado_final }}</td>
 @endif

 @else
 <td style='text-align: center'>-</td>
 @endif
 @endif

 @if ($p_final > 0)
 @if ($estado_final == 'Aprovado(a)')
 <td class='text-bold text-center '>{{$nota_final}}</td>
 <td class="{{'text-bold text-center ' .$color_final }}"> {{$estado_final}} </td>
 @else
 <td class='text-bold text-center '>{{ $nota_final }}</td>
 <td class="{{'text-bold text-center ' .$color_final }}">{{$estado_final}}</td>
 @endif

 @else
 <td class='text-bold text-center'>-</td>
 <td class='text-bold text-center'>-</td>
 @endif

 </tr>

 </tbody>

 @endif
 

 @endforeach
 </table>
 @endforeach
 
 @if(!isset($institution)) 
 <div class="row float-right btn-pdf-boletim" style="margin-right: 0.1!important;">
 <a class="btn " style="background-color:#0082f2;" target="_blank" href="{{ route("main.boletim_pdf", $matriculations->id) }}">
 <i class="fa fa-file-pdf"></i> Boletim de notas</a> 
 </div>
 @endif
 
 @else
 <div class="alert alert-warning text-dark font-bold">Nenhuma nota foi lançada neste ano lectivo! </div>
 @endif
 @else
 <div class="alert alert-warning text-dark font-bold"> {{ $percurso }}!</div>
 @endif
@endif