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

<div class="alert alert-warning text-dark font-bold">
    Para visualizar as notas lançadas, dirija-se à Tesouraria para regularizar os seus pagamentos!
</div>

@elseif (is_object($percurso) && count($percurso) > 0)

@php
    $semestres = ['1','2'];
@endphp

@foreach ($semestres as $semestreActual)

<table class="table tabela_pauta table-striped table-hover" id="tabela_pauta_student{{ $semestreActual }}">

<thead>
<tr>
    <td colspan="3" class="boletim_text">
       @if (isset($matriculations->course))
            <b>{{ $matriculations->course }}</b>
        @endif

        @if (isset($matriculations->course_year))
            <span class="barra">|</span> Ano: <b>{{ $matriculations->course_year }}º</b>
        @endif

        <span class="barra">|</span> Semestre: <b>{{ $semestreActual }}º</b>

        @if (isset($matriculations->classe))
            <span class="barra">|</span> Turma: <b>{{ $matriculations->classe }}</b>
        @endif
    </td>
    <td colspan="5" class="text-center bgmac">MAC</td>
    <td colspan="2" class="text-center bg1">EXAME</td>
    <td colspan="2" class="text-center cf1">CLASSIFICAÇÃO</td>
    <td colspan="4" class="text-center rec">EXAME</td>
    <td colspan="2" class="text-center fn">FINAL</td>
</tr>

<tr class="text-center">
    <th>#</th>
    <th>CÓDIGO</th>
    <th>DISCIPLINA</th>
    <th>PF1</th>
    <th>PF2</th>
    <th>OA</th>
    <th colspan="2">MÉDIA</th>
    <th>ESCRITO</th>
    <th>ORAL</th>
    <th colspan="2">MAC + EXAME</th>
    <th colspan="2">RECURSO</th>
    <th colspan="2">ESPECIAL</th>
    <th colspan="2">FINAL</th>
</tr>
</thead>

<tbody>

@php $disciplina_count = 0; @endphp

@foreach ($disciplines as $codigo => $disciplina)

@if ($codigo[3] != $semestreActual)
    @continue
@endif

@php
    $disciplina_count++;

    /* ===== NOTAS ===== */
    $pf1 = $pf2 = $oa = $exame = $oral = $recurso = $especial = null;

    if (isset($percurso[$codigo])) {
        foreach ($percurso[$codigo] as $nota) {
            if ($nota->nota_anluno === null) continue;

            $valor = round($nota->nota_anluno, 2);

            switch ($nota->MT_CodeDV) {
                case 'PF1':
                    $pf1 = $valor;
                    break;

                case 'PF2':
                    $pf2 = $valor;
                    break;

                case 'OA':
                    $oa = $valor;
                    break;

                case 'Neen':
                    $exame = $valor;
                    break;

                case 'oral':
                    $oral = $valor;
                    break;

                case 'Recurso':
                    $recurso = $valor;
                    break;

                case 'Exame_especial':
                    $especial = $valor;
                    break;
            }

        }
    }

    /* ===== ESTADO ===== */
    $mac = $classificacao_final = null;
    $estado = $cor = null;

    /* ===== MAC ===== */
    if ($pf1 !== null && $pf2 !== null && $oa !== null) {
        $mac = round(
            ($pf1 * ($config->pf1_percentagem / 100)) +
            ($pf2 * ($config->pf2_percentagem / 100)) +
            ($oa  * ($config->oa_percentagem  / 100))
        );

        if ($mac >= $config->mac_nota_dispensa) {
            $classificacao_final = $mac;
            $estado = 'Aprovado(a)';
            $cor = 'for-green';
        }
    }

    /* ===== EXAME ===== */
    if ($classificacao_final === null && $mac !== null && $exame !== null) {
        $nota = round(
            ($mac * ($config->percentagem_mac / 100)) +
            ($exame * ($config->percentagem_oral / 100))
        );

        if ($nota >= 10) {
            $classificacao_final = $nota;
            $estado = 'Aprovado(a)';
            $cor = 'for-green';
        } else {
            $estado = 'Recurso';
            $cor = 'for-red';
        }
    }

    /* ===== ORAL ===== */
    if ($estado === 'Recurso' && $oral !== null && $mac !== null) {
        $nota = round(
            ($mac * ($config->percentagem_mac / 100)) +
            ($oral * ($config->percentagem_oral / 100))
        );

        if ($nota >= 10) {
            $classificacao_final = $nota;
            $estado = 'Aprovado(a)';
            $cor = 'for-green';
        }
    }

    /* ===== RECURSO ===== */
    if ($estado === 'Recurso' && $recurso !== null) {
        if ($recurso >= 10) {
            $classificacao_final = $recurso;
            $estado = 'Aprovado(a)';
            $cor = 'for-green';
        } else {
            $estado = 'Especial';
            $cor = 'for-red';
        }
    }

    /* ===== ESPECIAL ===== */
    if ($especial !== null) {
        $classificacao_final = $especial;
        $estado = $especial >= 10 ? 'Aprovado(a)' : 'Reprovado(a)';
        $cor = $especial >= 10 ? 'for-green' : 'for-red';
    }
@endphp

<tr>
    <td>{{ $disciplina_count }}</td>
    <td>{{ $codigo }}</td>
    <td>{{ $disciplina->nome_disciplina }}</td>

    <td>{{ $pf1 ?? '-' }}</td>
    <td>{{ $pf2 ?? '-' }}</td>
    <td>{{ $oa ?? '-' }}</td>

    <td colspan="2">{{ $mac ?? '-' }}</td>

    <td>{{ $exame ?? '-' }}</td>
    <td>{{ $oral ?? '-' }}</td>

    <td colspan="2">{{ $classificacao_final ?? '-' }}</td>

    <td colspan="2">{{ $recurso ?? '-' }}</td>
    <td colspan="2">{{ $especial ?? '-' }}</td>

    <td colspan="2" class="{{ $cor }}">{{ $estado ?? '-' }}</td>
</tr>

@endforeach
</tbody>
</table>

@endforeach

@else
<div class="alert alert-warning text-dark font-bold">
    Nenhuma nota foi lançada neste ano lectivo!
</div>
@endif
