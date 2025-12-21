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
        @foreach($disciplinas as disciplina)
            @php
                $semestre = $disciplina->disciplinas[3]; // 3º índice da string
            @endphp
            @if($semestre == 1)
                <table class="table tabela_pauta table-striped table-hover tabela_pauta">
                    <thead>
                        <tr>
                            <td colspan="3" class="boletim_text">
                                <b>Engenharia Informática</b>
                                <as class="barra">|</as> Ano: <b>{{ $matricula->ano_curricular }}º</b>
                                <as class="barra">|</as> Semestre: <b>1º</b>
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
                        <tr>
                            <td class="text-center">1</td>
                            <td class="text-center">{{ $disciplina->disciplinas }}</td>
                            <td>{{ $disciplina->nome_disciplina }}</td>

                            <td class="text-center">14</td>
                            <td class="text-center">16</td>
                            <td class="text-center">15</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>
                        </tr>

                        <tr>
                            <td class="text-center">2</td>
                            <td class="text-center">MAT102</td>
                            <td>Matemática</td>

                            <td class="text-center">8</td>
                            <td class="text-center">9</td>
                            <td class="text-center">-</td>

                            <td class="text-center">9</td>
                            <td class="text-center for-red">Recurso</td>

                            <td class="text-center">10</td>
                            <td class="text-center">-</td>

                            <td class="text-center">9</td>
                            <td class="text-center for-red">Reprovado(a)</td>

                            <td class="text-center">11</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">11</td>
                            <td class="text-center for-green">Aprovado(a)</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <table class="table tabela_pauta table-striped table-hover tabela_pauta">
                    <thead>
                        <tr>
                            <td colspan="3" class="boletim_text">
                                <b>Engenharia Informática</b>
                                <as class="barra">|</as> Ano: <b>1º</b>
                                <as class="barra">|</as> Semestre: <b>2º</b>
                                <as class="barra">|</as> Turma: <b>A</b>
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
                        <tr>
                            <td class="text-center">1</td>
                            <td class="text-center">INF101</td>
                            <td>Programação I</td>

                            <td class="text-center">14</td>
                            <td class="text-center">16</td>
                            <td class="text-center">15</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">15</td>
                            <td class="text-center for-green">Aprovado(a)</td>
                        </tr>

                        <tr>
                            <td class="text-center">2</td>
                            <td class="text-center">MAT102</td>
                            <td>Matemática</td>

                            <td class="text-center">8</td>
                            <td class="text-center">9</td>
                            <td class="text-center">-</td>

                            <td class="text-center">9</td>
                            <td class="text-center for-red">Recurso</td>

                            <td class="text-center">10</td>
                            <td class="text-center">-</td>

                            <td class="text-center">9</td>
                            <td class="text-center for-red">Reprovado(a)</td>

                            <td class="text-center">11</td>
                            <td class="text-center for-green">Aprovado(a)</td>

                            <td class="text-center">-</td>
                            <td class="text-center">-</td>

                            <td class="text-center">11</td>
                            <td class="text-center for-green">Aprovado(a)</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        @endforeach
    @else
        <h1>Sem disciplinas associados a matricula</h1>
    @endif

@endif