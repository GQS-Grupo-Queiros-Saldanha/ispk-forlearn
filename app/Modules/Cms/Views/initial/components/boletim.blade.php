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
    @if(isset($disciplinas))
        @php
            $semestres = [1, 2]; // ou calcula dinamicamente
        @endphp

        @foreach($semestres as $sem)
            @php
                $disc_semestre = $disciplinas->filter(function($d) use ($sem) {
                    return (int)$d->disciplinas[3] === $sem;
                });
            @endphp

            @if($disc_semestre->isNotEmpty())
                <table class="table tabela_pauta table-striped table-hover tabela_pauta">
                    <thead>
                        <tr>
                            <td colspan="3" class="boletim_text">
                                <b>{{ $matricula->nome_curso }}</b>
                                <as class="barra">|</as> Ano: <b>{{ $matricula->ano_curricular }}º</b>
                                <as class="barra">|</as> Semestre: <b>{{ $sem }}º</b>
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
                        @foreach($disc_semestre as $index => $disc)
                            @php
                                $notas = $dados->where('disciplina', $disc->disciplinas);
                                $pf1 = $notas->firstWhere('metrica', 'PP1')->nota ?? '-';
                                $pf2 = $notas->firstWhere('metrica', 'PP2')->nota ?? '-';
                                $oa  = $notas->firstWhere('metrica', 'OA')->nota ?? '-';
                                $exame = $notas->firstWhere('metrica', 'Exame Escrito')->nota ?? '-';
                                $recurso = $notas->firstWhere('metrica', 'Recurso')->nota ?? '-';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td class="text-center">{{ $disc->disciplinas }}</td>
                                <td>{{ $disc->nome_disciplina }}</td>
                                <td class="text-center">{{ $pf1 }}</td>
                                <td class="text-center">{{ $pf2 }}</td>
                                <td class="text-center">{{ $oa }}</td>
                                <td colspan="2" class="text-center">Média</td>
                                <td class="text-center">{{ $exame }}</td>
                                <td class="text-center">-</td>
                                <td colspan="2" class="text-center">MAC + Exame</td>
                                <td colspan="2" class="text-center">{{ $recurso }}</td>
                                <td colspan="2" class="text-center">Especial</td>
                                <td colspan="2" class="text-center">Final</td>
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