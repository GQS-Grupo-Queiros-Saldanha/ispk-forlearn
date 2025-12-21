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

@if(2+2 !=4 )
    <div class="alert alert-warning text-dark font-bold">
        Para visualizar as notas lançadas, dirija-se a Tesouraria para regularizar os seus pagamentos!
    </div>
@elseif (auth()->check() && auth()->user()->id != 529)
    @include('Cms::initial.components.manutencao')
@else
    @if(isset($disciplinas) && count($disciplinas))
        @php
            // Separar disciplinas por semestre
            $disciplinas_semestre1 = [];
            $disciplinas_semestre2 = [];
            foreach($disciplinas as $d){
                $sem = intval($d->disciplinas[3]);
                if($sem === 1) $disciplinas_semestre1[] = $d;
                elseif($sem === 2) $disciplinas_semestre2[] = $d;
            }
            $semestres = [1 => $disciplinas_semestre1, 2 => $disciplinas_semestre2];
        @endphp

        @foreach($semestres as $num_semestre => $disciplinas_semestre)
            @if(count($disciplinas_semestre))
                <table class="table tabela_pauta table-striped table-hover">
                    <thead>
                        <tr>
                            <td colspan="3" class="boletim_text">
                                <b>{{ $matricula->nome_curso }}</b>
                                <as class="barra">|</as> Ano: <b>{{ $matricula->ano_curricular }}º</b>
                                <as class="barra">|</as> Semestre: <b>{{ $num_semestre }}º</b>
                                <as class="barra">|</as> Turma: <b>{{ $matricula->nome_turma }}</b>
                            </td>
                            <td colspan="5" class="text-center bgmac bo1 p-top">MAC</td>
                            <td colspan="2" class="text-center bg1 p-top">EXAME</td>
                            <td colspan="2" class="text-center cf1 bo1 p-top">CLASSIFICAÇÃO</td>
                            <td colspan="4" class="rec bo1 text-center p-top">EXAME</td>
                            <td colspan="2" class="fn bo1 text-center p-top">CLASSIFICAÇÃO</td>
                        </tr>
                        <tr style="text-align: center">
                            <th class="bg1 bo1">#</th>
                            <th class="bg1 bo1">CÓDIGO</th>
                            <th class="bg1 bo1">DISCIPLINA</th>
                            <th class="bgmac bo1">PF1</th>
                            <th class="bgmac bo1">PF2</th>
                            <th class="bgmac bo1">OA</th>
                            <th colspan="2" class="bgmac bo1">MÉDIA</th>
                            <th class="bg1 bo1">ESCRITO</th>
                            <th class="bg1 bo1">ORAL</th>
                            <th colspan="2" class="cf1 bo1">MAC + EXAME</th>
                            <th colspan="2" class="rec bo1">RECURSO</th>
                            <th colspan="2" class="rec bo1">ESPECIAL</th>
                            <th colspan="2" class="fn bo1">FINAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($disciplinas_semestre as $index => $disciplina)
                            @php
                                // Inicialização
                                $pf1 = $pf2 = $oa = null;
                                $ex_escrito = $ex_oral = null;
                                $nota_recurso = null;

                                foreach ($dados as $nota) {
                                    if ($nota->disciplina == $disciplina->disciplinas) {
                                        if ($nota->metrica == 'PP1') $pf1 = floatval($nota->nota);
                                        if ($nota->metrica == 'PP2') $pf2 = floatval($nota->nota);
                                        if ($nota->metrica == 'OA') $oa = floatval($nota->nota);
                                        if ($nota->metrica == 'Exame Escrito') $ex_escrito = floatval($nota->nota);
                                        if ($nota->metrica == 'Exame Oral') $ex_oral = floatval($nota->nota);
                                        if ($nota->metrica == 'Recurso') $nota_recurso = floatval($nota->nota);
                                    }
                                }

                                // Média MAC
                                $media = ($pf1 !== null || $pf2 !== null || $oa !== null)
                                    ? round(($pf1 * 0.35) + ($pf2 * 0.35) + ($oa * 0.3), 2)
                                    : null;

                                // Cor e classificação MAC
                                $cor_media = '';
                                $classificacao = '-';
                                if ($media !== null) {
                                    if ($media >= 10.3) {
                                        $classificacao = 'Aprovado(a)';
                                        $cor_media = 'for-green';
                                    } elseif ($media == 10) {
                                        $classificacao = 'Exame';
                                        $cor_media = 'for-yellow';
                                    } else {
                                        $classificacao = 'Recurso';
                                        $cor_media = 'for-red';
                                    }
                                }

                                // Exame normal
                                $exame_total = ($ex_escrito !== null || $ex_oral !== null)
                                    ? round(($ex_escrito ?: 0) + ($ex_oral ?: 0), 2)
                                    : null;

                                $media_exame = ($media !== null && $exame_total !== null)
                                    ? round(($media * 0.7) + ($exame_total * 0.3), 2)
                                    : null;

                                // Se houver recurso, substituir a nota
                                if ($media !== null && $media < 10 && $nota_recurso !== null) {
                                    $media_final = $nota_recurso;
                                } elseif ($media_exame !== null) {
                                    $media_final = $media_exame;
                                } else {
                                    $media_final = $media;
                                }

                                // Classificação final
                                $cor_final = '';
                                $estado_final = '-';
                                if ($media_final !== null) {
                                    if ($media_final >= 10) {
                                        $estado_final = 'Aprovado(a)';
                                        $cor_final = 'for-green';
                                    } else {
                                        $estado_final = 'Reprovado(a)';
                                        $cor_final = 'for-red';
                                    }
                                }
                            @endphp

                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $disciplina->disciplinas }}</td>
                                <td>{{ $disciplina->nome_disciplina }}</td>

                                <td class="text-center">{{ $pf1 !== null ? $pf1 : '-' }}</td>
                                <td class="text-center">{{ $pf2 !== null ? $pf2 : '-' }}</td>
                                <td class="text-center">{{ $oa  !== null ? $oa  : '-' }}</td>

                                <td class="text-center">{{ $media !== null ? $media : '-' }}</td>
                                <td class="text-center {{ $cor_media }}">{{ $classificacao }}</td>

                                <td class="text-center">{{ $ex_escrito !== null ? $ex_escrito : '-' }}</td>
                                <td class="text-center">{{ $ex_oral !== null ? $ex_oral : '-' }}</td>

                                <td class="text-center">{{ $media_exame !== null ? $media_exame : '-' }}</td>
                                <td class="text-center {{ $cor_media }}">{{ $classificacao }}</td>

                                <td colspan="2" class="text-center">{{ $nota_recurso !== null ? $nota_recurso : '-' }}</td>
                                <td colspan="2" class="text-center">-</td>
                                <td colspan="2" class="text-center">{{ $media_final !== null ? $media_final : '-' }}</td>
                                <td colspan="2" class="text-center {{ $cor_final }}">{{ $estado_final }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @else
        <h1>Sem disciplinas associadas à matrícula</h1>
    @endif

@endif