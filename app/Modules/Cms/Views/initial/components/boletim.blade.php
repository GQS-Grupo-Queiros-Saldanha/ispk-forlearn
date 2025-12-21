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

            foreach ($disciplinas as $d) {
                $sem = intval($d->disciplinas[3]);
                if ($sem === 1) {
                    $disciplinas_semestre1[] = $d;
                } elseif ($sem === 2) {
                    $disciplinas_semestre2[] = $d;
                }
            }

            $semestres = [
                1 => $disciplinas_semestre1,
                2 => $disciplinas_semestre2
            ];
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
                            <td colspan="5" class="text-center bgmac">MAC</td>
                            <td colspan="2" class="text-center bg1">EXAME</td>
                            <td colspan="2" class="text-center cf1">CLASSIFICAÇÃO</td>
                            <td colspan="2" class="text-center rec">RECURSO</td>
                            <td colspan="2" class="text-center fn">FINAL</td>
                        </tr>

                        <tr class="text-center">
                            <th>#</th>
                            <th>CÓDIGO</th>
                            <th>DISCIPLINA</th>
                            <th>PF1</th>
                            <th>PF2</th>
                            <th>OA</th>
                            <th>MÉDIA</th>
                            <th>ESTADO</th>
                            <th>ESCRITO</th>
                            <th>ORAL</th>
                            <th>MÉDIA + EXAME</th>
                            <th>ESTADO</th>
                            <th>NOTA</th>
                            <th>ESTADO</th>
                            <th>FINAL</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($disciplinas_semestre as $index => $disciplina)

                            @php
                                // Inicialização segura
                                $pf1 = $pf2 = $oa = 0;
                                $ex_escrito = $ex_oral = null;
                                $nota_recurso = null;

                                foreach ($dados as $nota) {
                                    if ($nota->disciplina == $disciplina->disciplinas) {
                                        if ($nota->metrica == 'PP1') $pf1 = floatval($nota->nota);
                                        if ($nota->metrica == 'PP2') $pf2 = floatval($nota->nota);
                                        if ($nota->metrica == 'OA')  $oa  = floatval($nota->nota);
                                        if ($nota->metrica == 'Exame Escrito') $ex_escrito = floatval($nota->nota);
                                        if ($nota->metrica == 'Exame Oral')    $ex_oral    = floatval($nota->nota);
                                        if ($nota->metrica == 'Recurso')        $nota_recurso = floatval($nota->nota);
                                    }
                                }

                                // Média MAC
                                $media = round(
                                    ($pf1 * 0.35) +
                                    ($pf2 * 0.35) +
                                    ($oa  * 0.30),
                                    2
                                );

                                // Exame
                                $exame_total = ($ex_escrito !== null || $ex_oral !== null)
                                    ? round((float)$ex_escrito + (float)$ex_oral, 2)
                                    : null;

                                $media_exame = $exame_total !== null
                                    ? round(($media * 0.7) + ($exame_total * 0.3), 2)
                                    : $media;

                                // Classificação FINAL (regra absoluta)
                                if ($nota_recurso !== null) {

                                    if ($nota_recurso >= 12) {
                                        $cor = 'for-green';
                                        $estado = 'Aprovado(a)';
                                        $nota_final = $nota_recurso;
                                    } else {
                                        $cor = 'for-red';
                                        $estado = 'Reprovado(a)';
                                        $nota_final = $nota_recurso;
                                    }

                                } else {

                                    if ($media >= 12) {
                                        $cor = 'for-green';
                                        $estado = 'Aprovado(a)';
                                        $nota_final = $media;
                                    } elseif ($media == 10) {
                                        $cor = 'for-yellow';
                                        $estado = 'Exame';
                                        $nota_final = $media_exame;
                                    } else {
                                        $cor = 'for-red';
                                        $estado = 'Recurso';
                                        $nota_final = '-';
                                    }

                                }
                            @endphp

                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $disciplina->disciplinas }}</td>
                                <td>{{ $disciplina->nome_disciplina }}</td>

                                <td class="text-center">{{ $pf1 }}</td>
                                <td class="text-center">{{ $pf2 }}</td>
                                <td class="text-center">{{ $oa }}</td>

                                <td class="text-center">{{ $media }}</td>
                                <td class="text-center {{ $cor }}">{{ $estado }}</td>

                                <td class="text-center">{{ $ex_escrito !== null ? $ex_escrito : '-' }}</td>
                                <td class="text-center">{{ $ex_oral !== null ? $ex_oral : '-' }}</td>

                                <td class="text-center">{{ $media_exame }}</td>
                                <td class="text-center {{ $cor }}">{{ $estado }}</td>

                                <td class="text-center">{{ $nota_recurso !== null ? $nota_recurso : '-' }}</td>
                                <td class="text-center {{ $cor }}">{{ $nota_recurso !== null ? $estado : '-' }}</td>

                                <td class="text-center">{{ $nota_final }}</td>
                                <td class="text-center {{ $cor }}">{{ $estado }}</td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>

            @endif

        @endforeach

    @else
        <h3>Sem disciplinas associadas à matrícula</h3>
    @endif

@endif