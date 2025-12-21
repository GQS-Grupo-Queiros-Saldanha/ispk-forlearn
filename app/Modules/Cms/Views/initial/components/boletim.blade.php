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
        $disciplinas_semestre1 = [];
        $disciplinas_semestre2 = [];

        foreach($disciplinas as $d){
            $sem = intval($d->disciplinas[3]);
            if($sem === 1) $disciplinas_semestre1[] = $d;
            if($sem === 2) $disciplinas_semestre2[] = $d;
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
                    <span class="barra">|</span> Ano: <b>{{ $matricula->ano_curricular }}º</b>
                    <span class="barra">|</span> Semestre: <b>{{ $num_semestre }}º</b>
                    <span class="barra">|</span> Turma: <b>{{ $matricula->nome_turma }}</b>
                </td>
                <td colspan="5" class="text-center bgmac bo1">MAC</td>
                <td colspan="2" class="text-center bg1">EXAME</td>
                <td colspan="2" class="text-center cf1 bo1">CLASSIFICAÇÃO</td>
            </tr>

            <tr class="text-center">
                <th class="bg1 bo1">#</th>
                <th class="bg1 bo1">CÓDIGO</th>
                <th class="bg1 bo1">DISCIPLINA</th>
                <th class="bgmac bo1">PF1</th>
                <th class="bgmac bo1">PF2</th>
                <th class="bgmac bo1">OA</th>
                <th class="bgmac bo1">MÉDIA</th>
                <th class="bg1 bo1">ESCRITO</th>
                <th class="bg1 bo1">ORAL</th>
                <th class="cf1 bo1">FINAL</th>
                <th class="cf1 bo1">ESTADO</th>
            </tr>
            </thead>

            <tbody>

            @foreach($disciplinas_semestre as $index => $disciplina)

            @php
                $pf1 = $pf2 = $oa = $ex_escrito = $ex_oral = null;

                foreach($dados as $nota){
                    if($nota->disciplina == $disciplina->disciplinas){
                        if($nota->metrica == 'PP1') $pf1 = $nota->nota;
                        if($nota->metrica == 'PP2') $pf2 = $nota->nota;
                        if($nota->metrica == 'OA')  $oa  = $nota->nota;
                        if($nota->metrica == 'Exame Escrito') $ex_escrito = $nota->nota;
                        if($nota->metrica == 'Exame Oral')    $ex_oral    = $nota->nota;
                    }
                }

                // MAC
                $media = round(
                    ($pf1 * 0.35) +
                    ($pf2 * 0.35) +
                    ($oa  * 0.30)
                , 2);

                // Exame
                $exame_total = 0;
                if($ex_escrito !== null) $exame_total += $ex_escrito;
                if($ex_oral !== null)    $exame_total += $ex_oral;

                // Nota final REAL
                if($exame_total > 0){
                    $nota_final = round(($media * 0.7) + ($exame_total * 0.3), 2);
                } else {
                    $nota_final = $media;
                }

                // Classificação FINAL
                if($nota_final >= 10.30){
                    $cor = 'for-green';
                    $estado = 'Aprovado(a)';
                } elseif($nota_final >= 10 && $nota_final < 10.30){
                    $cor = 'for-yellow';
                    $estado = 'Exame';
                } else {
                    $cor = 'for-red';
                    $estado = 'Recurso';
                }
            @endphp

            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $disciplina->disciplinas }}</td>
                <td>{{ $disciplina->nome_disciplina }}</td>

                <td class="text-center">{{ $pf1 ?? '-' }}</td>
                <td class="text-center">{{ $pf2 ?? '-' }}</td>
                <td class="text-center">{{ $oa  ?? '-' }}</td>

                <td class="text-center">{{ $media }}</td>

                <td class="text-center">{{ $ex_escrito ?? '-' }}</td>
                <td class="text-center">{{ $ex_oral ?? '-' }}</td>

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